# AquaLog-RV 学习笔记

这份文档用于记录当前版本的逐文件讲解、底层理解和练习任务。  
建议你一边打开代码，一边对照这里阅读和修改。

---

## 当前步骤

这一步不加新功能，先把你已经有的版本“拆开看懂”。

目标不是背代码，而是理解这条完整数据链：

`main()` 启动  
-> 调用采样中心  
-> 每个传感器写入结构体  
-> 告警模块分析结构体  
-> 日志模块把结构体写成 CSV  
-> 主循环继续下一轮

你现在最该练的不是“会不会写更多语法”，而是：

- 一个程序为什么要拆模块
- 数据为什么要集中放进结构体
- 指针传参到底是在干什么
- 文件写入为什么要分成“写表头”和“追加数据”

---

## 逐文件讲解

### 1. `sensor_types.h`：项目的数据总线

看这里：

- `src/sensor_types.h`

```c
typedef struct {
	double temperature;
	double ph;
	double do_value;
	double turbidity;
	double water_level;
	time_t timestamp;
	int temperature_ok;
	int ph_ok;
	int do_ok;
	int turbidity_ok;
	int water_level_ok;
} SensorData;
```

这是整个项目最核心的设计。

你可以把 `SensorData` 理解成“一次采样的快照”。  
某个时刻所有传感器的值、时间、是否读取成功，都被打包进这一份结构体。

### 底层理解

- `struct` 本质上是一块连续内存，里面按字段顺序存放数据。
- `data.temperature`、`data.ph` 这些字段都属于同一个变量 `data`。
- 如果不用结构体，你就得在函数之间传很多个独立变量，主程序会很乱。

为什么还要有 `temperature_ok` 这种字段？

- 因为“读失败”和“读到 0.0”不是一回事。
- 如果只靠数值字段本身，你没法区分“真的测到 0”还是“没测到”。

这其实就是很典型的工程思维：  
数据值 和 数据状态 分开存。

---

### 2. `config.h`：把魔法数字收口

看这里：

- `src/config.h`

```c
#define SAMPLE_INTERVAL_SECONDS 5
#define TEMP_MIN_VALUE 15.0
#define TEMP_MAX_VALUE 35.0
...
#define DO_ALERT_LOW 4.0
#define SENSOR_FAIL_PERCENT 8
#define LOG_FILE_NAME "aqualog.csv"
```

这些 `#define` 是预处理器宏。

### 底层理解

- 编译前，预处理器会把 `SAMPLE_INTERVAL_SECONDS` 直接替换成 `5`。
- 它不是变量，不占运行时内存。
- 好处是统一修改，避免代码里到处写裸数字。

为什么这一步很重要？

因为如果你把 `32.0`、`4.0`、`70.0` 散落在很多 `.c` 文件里：

- 以后改规则很痛苦
- 容易漏改
- 可读性差

你现在可以先把它理解成：  
`config.h` 是程序的参数面板。

---

### 3. 单个传感器模块：指针传参的第一现场

看这里：

- `src/sensor_temp.c`

```c
int read_temperature(double *value)
{
	if (value == NULL)
		return 0;

	if ((rand() % 100) < SENSOR_FAIL_PERCENT)
		return 0;

	*value = random_double(TEMP_MIN_VALUE, TEMP_MAX_VALUE);
	return 1;
}
```

这是你现在最应该吃透的地方。

### 先看函数签名

```c
int read_temperature(double *value)
```

意思是：

- 传入一个 `double` 的地址
- 函数内部通过这个地址把结果写回去
- 返回值 `int` 用来表示成功还是失败

这就是“输出参数 + 状态返回值”的经典写法。

### 为什么不能写成这样？

```c
double read_temperature(void)
```

因为这样只能返回一个东西。  
但你这里其实想返回两个信息：

- 读到的温度值
- 这次读数是否成功

所以当前写法更合理：

- 返回值 `1/0` 表示成功/失败
- `*value` 存实际结果

### `*value = ...` 到底干了什么？

如果主调函数里有：

```c
double t;
read_temperature(&t);
```

那么：

- `&t` 是变量 `t` 的地址
- `value` 接收到这个地址
- `*value = 28.5;` 的效果就是把 `t` 改成 `28.5`

你可以把它理解成：  
函数拿到了调用者家里变量的门牌号，然后直接去那个地址写值。

### `rand()` 和范围映射

看这里：

- `src/sensor_temp.c`

```c
scale = (double) rand() / (double) RAND_MAX;
return min + (max - min) * scale;
```

原理是：

1. `rand()` 先产生一个 `0 ~ RAND_MAX` 的整数
2. 除以 `RAND_MAX` 后，得到 `0.0 ~ 1.0` 之间的小数
3. 再映射到 `min ~ max`

这是很典型的“归一化再缩放”。

---

### 4. `sensor_hub.c`：把多个模块串起来

看这里：

- `src/sensor_hub.c`

```c
void collect_sensor_data(SensorData *data)
{
	if (data == NULL)
		return;

	data->timestamp = time(NULL);

	data->temperature_ok = read_temperature(&data->temperature);
	data->ph_ok = read_ph(&data->ph);
	data->do_ok = read_do(&data->do_value);
	data->turbidity_ok = read_turbidity(&data->turbidity);
	data->water_level_ok = read_water_level(&data->water_level);
}
```

这文件的作用是“采样调度中心”。

### 底层理解两个点

#### `data->temperature` 是什么？

`data` 是 `SensorData *`，也就是“指向结构体的指针”。

所以：

- `data->temperature`
- 等价于 `(*data).temperature`

箭头 `->` 就是“通过指针访问结构体字段”。

#### 为什么这里不把逻辑写进 `main.c`？

因为 `main.c` 应该只负责流程，不应该知道每个传感器怎么读。

如果以后你要：

- 把模拟传感器换成真实硬件驱动
- 增加盐度传感器
- 改读取策略

你只动采样模块，不动主循环。

这就是模块化真正的价值：  
隔离变化。

---

### 5. `alert.c`：把数据翻译成告警语义

看这里：

- `src/alert.c`

`build_alert_message()` 的职责是：

- 看结构体里的值
- 判断是不是超阈值
- 把结果拼成字符串，例如：
  - `normal`
  - `do_low`
  - `temperature_error;do_low`

这一步很关键，因为它把“原始数值”变成了“业务含义”。

### 为什么先判断 `xxx_ok`

比如：

```c
if (!data->temperature_ok)
	append_message(..., "temperature_error");
else if (data->temperature > TEMP_ALERT_HIGH)
	append_message(..., "temperature_high");
```

这是个很重要的工程顺序。

如果读取失败了，就不要再拿这个值做阈值比较。  
因为失败时数值字段可能是旧值、未定义值或者无意义值。

所以顺序必须是：

1. 先看这次读数是否成功
2. 成功了再看数值是否越界

### `append_message()` 在干什么

它本质上是在做字符串拼接，并在已有内容时补一个 `;`。

这让最终 `alert` 字段可以同时表示多个异常。  
这是在练字符数组和字符串函数，不是为了炫技。

### `print_console_status()`

这个函数只是负责显示，不负责判断。  
这也是职责分离：

- `build_alert_message()` 负责判
- `print_console_status()` 负责显

---

### 6. `logger.c`：文件 I/O 的主战场

这部分你要重点练。

### 先看时间格式化

看这里：

- `src/logger.c`

```c
local_time_info = localtime(&raw_time);
strftime(buffer, (size_t) buffer_size, "%Y-%m-%d %H:%M:%S", local_time_info);
```

### 底层理解

- `time(NULL)` 返回当前时间，类型是 `time_t`
- `localtime()` 把它拆成“年/月/日/时/分/秒”
- `strftime()` 再格式化成字符串

也就是说：

`time_t`  
-> `struct tm`  
-> `"2026-04-10 21:29:47"`

### 为什么单独写 `ensure_log_header()`

这个函数是为了保证 CSV 第一行存在：

```text
timestamp,temperature,ph,do,turbidity,water_level,alert
```

如果你每次追加都顺手写表头，就会重复很多次。  
所以这里把逻辑拆成两步：

1. 程序启动时检查日志文件是否存在或是否为空
2. 只在必要时写一次表头

这就是一个很标准的 I/O 设计习惯。

### 为什么用 `"a"` 模式

```c
fp = fopen(file_name, "a");
```

`"a"` 是 append，追加模式。

含义是：

- 文件不存在就创建
- 文件存在就在末尾继续写
- 不覆盖历史记录

这就是日志文件最常见的打开方式。

### 为什么有 `write_value_or_na()`

这个函数是个小抽象：

- 成功就写具体数值
- 失败就写 `NA`

所以日志里不会误写 `0.00` 来假装“成功读取到 0”。

这点非常重要，因为日志是给后续分析看的。  
一旦语义不清，后面统计就全错了。

---

### 7. `main.c`：真正的主控流程

看这里：

- `src/main.c`

```c
int main(void)
{
	SensorData data;
	char alert_message[128];

	srand((unsigned int) time(NULL));

	if (!ensure_log_header(LOG_FILE_NAME)) {
		...
	}

	print_banner();

	for (;;) {
		collect_sensor_data(&data);
		build_alert_message(&data, alert_message, (int) sizeof(alert_message));

		if (!append_log_entry(LOG_FILE_NAME, &data, alert_message)) {
			...
		}

		print_console_status(&data, alert_message);
		...
	}
}
```

你可以把这段看成 6 步：

1. 准备一份 `SensorData`
2. 准备一个告警字符串缓冲区
3. 初始化随机数种子
4. 确保日志文件表头存在
5. 进入无限循环
6. 每轮做：采样 -> 生成告警 -> 写日志 -> 打印 -> 决定是否退出

### `srand()` 为什么只调用一次？

`rand()` 生成的是伪随机数序列。  
如果你不设置种子，每次程序启动可能都重复同一串结果。

所以：

```c
srand((unsigned int) time(NULL));
```

作用是用当前时间作为种子，让每次启动的随机序列不同。

注意：

- `srand()` 调一次就够了
- 不要每次采样都重新 `srand()`
- 否则随机效果反而变差

### `char alert_message[128]` 是什么思路？

这是一块字符数组，用来保存字符串。

你现在要慢慢建立一个感觉：

- C 里“字符串”不是特殊对象
- 本质上就是 `char` 数组
- 结尾用 `'\0'` 表示字符串结束

所以 `build_alert_message()` 不是“返回字符串对象”，而是往你给的数组里写字符。

### `wait_seconds()` 的底层问题

```c
while (time(NULL) - start_time < seconds)
	;
```

这是忙等（busy wait）。

意思是程序在这几秒里一直空转检查时间。  
它简单、纯标准 C、容易理解，但不高效。

这正适合学习阶段，因为你能先看懂控制流程。  
以后再升级成平台相关的 `sleep()` 或更精细的定时器。

---

## 从底层角度串一次完整调用链

假设主循环进入一次采样：

### 第一步：`collect_sensor_data(&data)`

传进去的是 `data` 的地址。  
所以采样函数能直接修改主函数里的那份结构体。

### 第二步：`read_temperature(&data.temperature)`

又把结构体里某个字段的地址传下去。  
所以传感器函数能直接把结果写进该字段。

### 第三步：返回 `temperature_ok`

表示“这次是否成功”。  
这个状态被放进结构体里，供后面的告警和日志使用。

### 第四步：`build_alert_message(&data, alert_message, ...)`

这里同时传了两块东西：

- 结构体地址：读取所有传感器数据
- 字符数组地址：把告警字符串写进去

### 第五步：`append_log_entry(...)`

日志模块再读取结构体，把每个字段展开写入 CSV。

所以这个项目虽然不大，但已经包含了 C 程序非常核心的几类“底层动作”：

- 传变量地址
- 通过地址修改外部数据
- 使用结构体组织内存
- 用字符数组保存字符串
- 用文件指针做 I/O

---

## 你必须自己改的练习

下面这组练习建议你自己动手，不要先看别人替你改。

### 练习 1：把采样间隔改成 2 秒

改这里：

- `src/config.h`

把：

```c
#define SAMPLE_INTERVAL_SECONDS 5
```

改成：

```c
#define SAMPLE_INTERVAL_SECONDS 2
```

你要观察：

- 控制台等待提示是否变成 2 秒
- CSV 里相邻两行时间差是否接近 2 秒

你要理解的点：

- 宏改变后，主程序里的所有相关逻辑都会自动受影响
- 这就是“集中配置”的价值

建议 commit：

```bash
git -C /Users/a15951407904/projects/C-RISC-V-Embedded-Journey add Cassian-RV/aqualog-rv/simulator/src/config.h
git -C /Users/a15951407904/projects/C-RISC-V-Embedded-Journey commit -m "chore(simulator): reduce sample interval to 2 seconds"
```

---

### 练习 2：临时取消 water level 告警

改这里：

- `src/alert.c`

目标：

- 水位继续采样
- 水位继续写入日志
- 但不参与告警判断

你应该删掉的逻辑是 `build_alert_message()` 里关于 `water_level` 的那一段，不要动 `print_console_status()`。

你要理解的点：

- “采样”和“告警”是两个不同职责
- 一个字段可以继续存在，但暂时不参与业务判断
- 模块化的意义就是你能精准改一层，不动另一层

建议 commit：

```bash
git -C /Users/a15951407904/projects/C-RISC-V-Embedded-Journey add Cassian-RV/aqualog-rv/simulator/src/alert.c
git -C /Users/a15951407904/projects/C-RISC-V-Embedded-Journey commit -m "refactor(simulator): disable water level alert rule"
```

---

### 练习 3：提高失败率，观察错误处理

改这里：

- `src/config.h`

把：

```c
#define SENSOR_FAIL_PERCENT 8
```

改成：

```c
#define SENSOR_FAIL_PERCENT 30
```

运行后观察：

- 控制台里 `read_failed` 是否明显增多
- CSV 是否出现更多 `NA`
- 程序是否仍然稳定，不崩溃

你要理解的点：

- 工程代码不能只处理“理想情况”
- 失败是常态，稳定比完美重要
- “状态位 + NA 写日志”是处理失败的一种方式

建议 commit：

```bash
git -C /Users/a15951407904/projects/C-RISC-V-Embedded-Journey add Cassian-RV/aqualog-rv/simulator/src/config.h
git -C /Users/a15951407904/projects/C-RISC-V-Embedded-Journey commit -m "test(simulator): increase sensor failure rate for error handling practice"
```

---

### 练习 4：自己加一个 `sample_id`

这是最值得你做的一个。

目标：

- 给 `SensorData` 新增一个 `int sample_id`
- 每轮采样时让它递增
- 写进 CSV 第一列或第二列

你需要改的文件至少有：

- `src/sensor_types.h`
- `src/main.c`
- `src/logger.c`

你要自己想的关键问题：

- `sample_id` 应该由谁维护？
- 是放在 `main()` 里维护再写入结构体，还是在 `sensor_hub.c` 里维护？
- 为什么？

工程建议：

- 先在 `main()` 里维护，因为主循环天然知道“第几轮”
- `sensor_hub` 负责采样，不负责计数

这能帮你建立“职责边界”的意识。

建议 commit：

```bash
git -C /Users/a15951407904/projects/C-RISC-V-Embedded-Journey add Cassian-RV/aqualog-rv/simulator/src
git -C /Users/a15951407904/projects/C-RISC-V-Embedded-Journey commit -m "feat(simulator): add sample id to sensor records"
```

---

## 编译和测试方法

每改完一步都这样测：

```bash
cd /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/Cassian-RV/aqualog-rv/simulator
make clean
make
./aqualog
```

重点检查 4 件事：

1. 能不能正常编译
2. 控制台输出是否符合预期
3. `aqualog.csv` 是否格式正确
4. 失败情况下程序是否仍然能继续跑

如果你想直接查看日志前几行：

```bash
sed -n '1,10p' aqualog.csv
```

---

## 这一轮建议记录的学习收获

建议你在笔记里写这 5 句，自己填完整：

- `struct` 解决了什么问题？
- 为什么 `read_temperature(double *value)` 比直接返回 `double` 更适合这里？
- 为什么告警判断不能直接写进主循环？
- 为什么日志里失败要写 `NA`，而不是 `0`？
- 为什么 `main()` 更适合维护 `sample_id`？

---

## 下一步建议

你先自己完成上面 4 个改动中的前 2 个，至少先做：

1. 改采样间隔
2. 取消 water level 告警

改完后把你改过的代码贴给我，或者直接让我检查你当前工作区的改动。

下一步我会做两件事：

- 逐行 review 你的改动，指出哪里体现了结构体、指针和模块边界
- 带你继续往下理解“字符串缓冲区”“文件指针”“为什么 `char[]` 容易出错”
