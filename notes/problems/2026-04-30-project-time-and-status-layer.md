# 2026-04-30 Problems: Status Text, Timestamp, and Project Storage Prep

## 今日主线
- 围绕 `exercises/06-projects/1.c` 继续把池塘记录 CLI 从“终端输出”推进到“可保存记录”的前置层。
- 今天没有完成 CSV，而是先吃透两个关键前置能力：
  - 状态判断从“直接打印”拆成“返回状态文本”。
  - 一条记录开始准备加入 `sampled_at` 时间戳字段。

## 1. 每日问题

### 问题 1
- `const char *temp_status(float temperature)` 到底是什么返回值？为什么它比 `void print_temp_status(...)` 更适合后续 CSV 和告警？

### 解答
- `const char *` 可以理解成“只读字符串的起始地址”。
- `temp_status()` 的职责是返回 `"low"`、`"normal"`、`"high"` 这种状态文本。
- `print_temp_status()` 的职责是直接向终端输出。
- 前者返回结果，后者执行动作。
- 当状态文本可以作为结果返回时，它就能被终端输出、CSV 保存、告警判断、前端标签复用。

### 闭环
- 练习：把 `print_temp_status()` 改成只负责格式化打印：
  - `printf("temp:%.1f(%s)", temperature, temp_status(temperature));`
- 项目：后续 `save_pond_record_csv()` 应该调用 `temp_status(record.temp)`，而不是调用 `print_temp_status()`。

### 使用场景和启发思考
- 场景：状态标签、CSV 字段、告警条件、前端显示。
- 启发：项目函数不要只会“喊结果”，更要能“交还结果”。

---

### 问题 2
- 为什么 `v2` 里用 `temp_status(temperature) == "low"` 判断状态不是好的方向？

### 解答
- C 里的字符串不是高级语言里的字符串对象。
- `==` 比较的是地址，不是字符串内容。
- 即使某些字符串字面量地址看起来可能相同，也不能把它当作稳定的内容比较方式。
- 更重要的是，`v2` 已经拿到了状态文本，又重新写一套 `if / else` 去决定怎么打印，导致判断逻辑重复。
- 更好的方向是 `v3`：
  - `printf("temp:%.1f(%s)", temperature, temp_status(temperature));`

### 闭环
- 练习：保留 `v1 / v2 / v3` 的思路对比，但最终代码采用 `v3`。
- 项目：让状态函数成为判断层，打印函数成为输出层。

### 使用场景和启发思考
- 场景：字符串状态、规则判断、后续 CSV 保存。
- 启发：拿到可复用结果后，不要再把它绕回重复分支；能直接组合输出就直接组合。

---

### 问题 3
- `time_t`、`struct tm`、`strftime` 为什么要分成三层？为什么不能直接用 `strftime` 拿当前时间？

### 解答
- `time_t` 是机器可计算的原始时间值，适合算时间差和采样间隔。
- `struct tm` 把原始时间拆成年、月、日、时、分、秒，适合人理解。
- `strftime` 只负责把 `struct tm` 按指定格式写成字符串，它不负责获取当前时间。
- 三者分离，是因为“计算、理解、保存”是三种不同需求。

### 闭环
- 练习：口头复述这条链：
  - `time(NULL)` -> `localtime(&now)` -> `strftime(...)`
- 项目：先用字符串时间戳 `sampled_at` 作为 CSV 字段，后续如果要做采样间隔，再引入 `time_t` 原始时间值。

### 使用场景和启发思考
- 场景：采样时间、日志、CSV、定时采样。
- 启发：时间不是单一概念；机器算时间、人看时间、文件存时间，需要不同表示。

---

### 问题 4
- `strftime(record->sampled_at, TIMESTAMP_SIZE, ..., local)` 中，为什么第一个参数是 `record->sampled_at`，不是 `&record->sampled_at`？

### 解答
- `sampled_at` 是字符数组：
  - `char sampled_at[TIMESTAMP_SIZE];`
- 数组名作为函数参数时通常退化成首元素地址。
- 所以 `record->sampled_at` 等价于写入缓冲区的起始位置，也就是 `&record->sampled_at[0]`。
- `&record->sampled_at` 是整个数组对象的地址，类型更接近 `char (*)[TIMESTAMP_SIZE]`，不是 `strftime` 需要的 `char *`。

### 闭环
- 练习：解释：
  - `record->sampled_at`
  - `&record->sampled_at[0]`
  - `&record->sampled_at`
- 项目：`strftime` 需要的是“从哪个字符位置开始写”，不是整个数组对象的地址。

### 使用场景和启发思考
- 场景：字符串缓冲区、文件路径、传感器文本帧、CSV 行缓冲。
- 启发：数组名退化和取整个数组地址是不同概念，地址数值可能一样，但类型含义不同。

---

### 问题 5
- `TIMESTAMP_SIZE` 的作用是什么？为什么时间戳字符串要给 `'\0'` 留空间？

### 解答
- `TIMESTAMP_SIZE` 是目标字符数组的最大容量。
- `strftime` 第二个参数告诉函数最多能写多少字符，防止写越界。
- `"2026-04-30 17:10:00"` 有 19 个可见字符。
- C 字符串末尾还需要 `'\0'`，所以最小需要 20 个字符空间。

### 闭环
- 练习：数出 `YYYY-MM-DD HH:MM:SS` 的 19 个可见字符，再补上 `'\0'`。
- 项目：`sampled_at` 当前可以先定义为：
  - `char sampled_at[TIMESTAMP_SIZE];`

### 使用场景和启发思考
- 场景：字符串数组、缓冲区大小、CSV 字段。
- 启发：C 字符串容量永远要考虑结尾的 `'\0'`。

---

### 问题 6
- 当前 `fill_record_timestamp(PondRecord *record)` 还没完全收口，主要误区在哪里？

### 解答
- 当前思路是对的：传入结构体指针，把时间戳写进原记录。
- 但代码层还需要明天收口：
  - `time[NULL]` 应该是函数调用 `time(NULL)`。
  - `read_pond_record()` 中读取成功后应该继续填时间戳，而不是成功时直接 `return 0`。
  - 输出时字段名应统一为 `sampled_at`，不要写成不存在的 `timestamp`。
- 这些问题的本质不是“时间戳难”，而是新字段加入后，结构体字段名、函数返回值、流程条件要一起对齐。

### 闭环
- 练习：明天先只修 `fill_record_timestamp()` 和 `read_pond_record()`，让带时间戳的终端输出重新编译通过。
- 项目：CSV 之前先完成“带时间戳的一条记录”。

### 使用场景和启发思考
- 场景：结构体扩字段、输入封装、时间戳写入。
- 启发：每次给结构体加字段，都要检查“读入、填充、输出、保存”这四条链是否同时更新。

## 2. 今日闭环总结
- 今天完成了状态判断层的抽离思路：从 `print_xxx_status()` 直接打印，推进到 `xxx_status()` 返回状态文本。
- 时间戳链路已经完成概念拆解，但代码仍处在半成品状态，需要明天先收编译闭环。
- 今天最大的理解误区集中在：
  - 把字符串内容比较误认为可以用 `==`。
  - 把 `time_t` 当成“打印工具”，而不是机器可计算的时间值。
  - 把 `record->sampled_at` 理解成“值”，但它在函数参数里更关键的是缓冲区起始地址。

## 3. 明日自然衔接
- 先修通 `fill_record_timestamp(PondRecord *record)`。
- 让 `read_pond_record()` 在读取成功后填入时间戳。
- 让终端输出变成：
  - `[2026-04-30 17:10:00] Pond A temp:27.5(normal) oxygen:4.2(low)`
- 时间戳终端输出稳定后，再进入 CSV 保存。
