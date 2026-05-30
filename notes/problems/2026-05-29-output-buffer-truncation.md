# 2026-05-29 Output Buffer Truncation

> 实际完成时间：2026-05-30

## 今日主线

今天完成 `Output Module v0`：把一条池塘记录和控制决策格式化为一行人类可读文本。

核心链路：

```text
PondRecord + should_aerate -> output_format_record_line -> char buffer[]
```

output 模块只负责格式化，不直接 `printf`，不接 `main`，不调用 judge/control/csv_store，不新增领域规则。

## 1. 每日问题

### 问题 1：为什么 output 不直接 `printf` 到终端？

直接 `printf` 会把格式化逻辑和输出出口绑定在一起。

更稳的设计是先写入调用方提供的 `char buffer[]`：

```text
output 负责构造文本。
main 决定文本去哪里：终端、日志、串口、网络或测试断言。
```

这样 output 模块更容易测试，也更适合后续嵌入式和通信输出。

### 问题 2：`char buffer[]` 和 `size_t buffer_size` 分别解决什么问题？

`char buffer[]` 是调用方提供的一段连续字符空间。

`size_t buffer_size` 告诉函数这段空间有多大。

如果只传 buffer，不传容量，函数无法知道最多能写多少字符，容易越界。

因此写 buffer 的函数必须同时知道：

```text
写到哪里：buffer
最多写多少：buffer_size
```

### 问题 3：`snprintf` 为什么仍然需要检查返回值？

`snprintf` 的价值是限制最大写入范围，避免越界写入。

但它不保证输出一定完整。

它的返回值表示：

```text
如果空间足够，本来需要写入的字符数。
```

所以：

```text
written < 0：格式化失败。
written >= buffer_size：输出被截断。
```

结论：

```text
snprintf 负责内存安全。
返回值检查负责语义完整性。
```

### 问题 4：为什么 `written >= buffer_size` 表示 buffer 太小？

C 字符串需要以 `'\0'` 结束。

如果 `snprintf` 返回的 `written` 大于等于 `buffer_size`，说明“完整文本需要的字符数”已经不小于可用容量。

此时 buffer 中虽然不会越界，但只保存了半截字符串。

output 模块不能把半截字符串当成成功结果，所以必须返回 `BUFFER_TOO_SMALL` 这类错误。

### 问题 5：small buffer 测试保护了什么？

small buffer 测试不是为了测试“能不能写一点东西”，而是为了测试：

```text
当 buffer 不够大时，函数能不能正确拒绝半截输出。
```

如果缺少这个测试，函数可能在输出被截断时仍返回成功，调用方会误以为拿到了完整消息。

### 问题 6：为什么 output 不应该调用 judge/control？

output 是展示/格式化层。

它只应该使用已经传入的数据和决策结果：

```text
record 提供数据。
control 提供 should_aerate。
output 只负责格式化。
```

如果 output 自己调用 judge/control，就会把判断、控制和展示耦合在一起，破坏模块边界。

## 2. 解答总结

今天的核心是从“能输出字符串”升级为“安全、完整、可测试地构造字符串”。

关键模型：

```text
buffer 防止输出逻辑直接绑定终端。
buffer_size 防止越界写入。
snprintf 防止写爆内存。
返回值检查防止半截文本被当成完整输出。
```

这一步让 output 模块具备后续复用能力：同一条格式化文本可以去终端、CSV、串口、socket、MQTT 或单元测试。

## 3. 闭环

已完成闭环：

- 创建 `include/output.h`。
- 创建 `src/output.c`。
- 创建 `tests/test_output.c`。
- `Makefile` 增加 `test_output` 并纳入 `make test`。
- 正常 buffer 场景通过。
- small buffer 场景能识别截断。
- `make test_output` 通过。
- `make test` 通过。
- `git diff --check` 通过。

Bug Lab：

```text
临时破坏 written >= buffer_size -> BUFFER_TOO_SMALL 的判断。
small buffer 场景不再正确报错。
make test_output 失败。
恢复截断检测后回归通过。
```

错误类型：

```text
string_buffer / behavior_regression
```

这不是编译错误，也不是链接错误；程序能构建并运行，但模块行为不符合 buffer 合同。

## 4. 使用场景和启发思考

真实项目里，output 模块会成为“输出出口”的前置层。

同一条格式化文本未来可能被送到：

```text
终端 CLI
日志文件
串口 UART
网络 socket
MQTT 消息
前端接口
```

因此 output 不应该急着 `printf`，而应该先把文本构造在 buffer 中。

small buffer 测试也很关键：嵌入式系统里内存空间有限，不能只追求“不崩溃”，还要保证“输出语义完整”。

## 5. 408 映射

数据结构：

- `char buffer[]` 是连续存储空间。
- `PondRecord` 的结构体字段被格式化为线性字符串。

计算机组成原理：

- 字符以字节形式存储。
- C 字符串以 `'\0'` 结束。
- buffer 边界保护体现了内存访问安全。

操作系统：

- 用户态程序需要自己管理缓冲区边界。
- 终端、文件、串口和网络都可以视为不同 I/O 出口，output 先生成用户态 buffer。

计算机网络：

- 应用层协议消息通常先在 buffer 中构造，再发往 socket、UART 或 MQTT。

## 6. 明日衔接

下一步先复盘：

- `char buffer[]`
- `size_t buffer_size`
- `snprintf` 返回值
- `written >= buffer_size`
- small buffer 截断检测

稳定后再进入 `input_cli` 或 `main` 串联，不提前接硬件。
