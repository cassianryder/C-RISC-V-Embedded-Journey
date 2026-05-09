# Skill 名称

系统动力学到 C 项目闭环建模法

## 适用阶段

- 从“会写一个函数”过渡到“能设计一个小系统”时
- 当前主线：`exercises/06-projects/1.c`
- 后续适用于：池塘记录 CLI、多文件模块化、缓存/队列、传感器数据模拟、板子接入、预测告警系统

## 它解决什么问题

学习 C 时容易把知识点看成孤立语法：

```text
结构体
函数
循环
文件 I/O
指针
测试
```

但真实项目不是语法堆叠，而是一个系统：

```text
有哪些状态？
状态如何变化？
变化由什么关系驱动？
哪些数据需要被观察、保存、反馈和调控？
```

这个 skill 用系统动力学的“存量-流量-反馈”思路，把真实系统先建模，再映射到 C 程序结构中。

## 第一性原理拆解

系统动力学关注的是：

```text
系统边界
-> 存量
-> 流量
-> 关系
-> 反馈
-> 时间推进
-> 观察与调控
```

C 项目关注的是：

```text
数据类型
-> 结构体
-> 函数
-> 主循环
-> 输入输出
-> 存储
-> 测试与调优
```

两者可以直接映射：

| 系统动力学概念 | C 项目映射 | 例子 |
|---|---|---|
| 系统边界 | 项目模块 / 文件范围 | 只模拟一个池塘的水质记录 |
| 存量 stock | 结构体字段 / 状态变量 | `oxygen`、`temp`、`water_level` |
| 流量 flow | 改变存量的函数 | `update_oxygen()`、`add_water()` |
| 辅助变量 | 派生状态 / 判断函数 | `oxygen_status()` |
| 参数 | `#define` / `const` / 配置结构体 | `OXYGEN_LOW_LIMIT` |
| 反馈回路 | `if` 判断 + 控制函数 | 低氧 -> 开增氧机 |
| 时间步 | `while` 循环 / 采样周期 | 每次输入一条记录 |
| 观测 | 打印 / CSV / 日志 | `print_pond_record()` |
| 记忆 | 文件 / 缓存 / 队列 | `save_pond_record_csv()` |
| 调优 | 测试 + 阈值调整 | 修改低氧阈值并验证 |

## 核心方法

以后推进项目时，优先使用这条路线：

```text
1. 先定系统边界
2. 找存量，也就是系统中会被保留或随时间变化的状态
3. 找流量，也就是让存量增加、减少或改变的过程
4. 找关系，也就是状态之间如何互相影响
5. 把存量映射成基本数据类型或结构体字段
6. 把关系映射成函数
7. 先在 main 写逻辑骨架
8. 再在 main 外写函数骨架
9. 再补函数实现
10. 再测试、调优、记录问题、闭环
```

## C 项目模板

### 1. 先写系统语言

```text
系统边界：一个池塘的一条采样记录
存量：温度、溶氧、塘口编号、采样时间
流量：暂时不模拟连续变化，只记录每次采样
关系：温度和溶氧决定状态文本
反馈：低氧时提示告警，后续可接增氧机控制
观察：终端输出
记忆：CSV 保存
```

### 2. 映射成 C 数据模型

```c
typedef struct
{
    char sampled_at[TIMESTAMP_SIZE];
    float temp;
    float oxygen;
    char pond_id;
} PondRecord;
```

### 3. 先写 `main` 逻辑骨架

```c
int main(void)
{
    PondRecord record;

    while (read_pond_record(&record))
    {
        print_pond_record(record);

        if (!save_pond_record_csv(record))
        {
            printf("Error: failed to save.\n");
        }
    }

    printf("Done\n");
    return 0;
}
```

### 4. 再写函数骨架

```c
int read_pond_record(PondRecord *record);
int fill_record_timestamp(PondRecord *record);
const char *temp_status(float temperature);
const char *oxygen_status(float oxygen);
void print_pond_record(PondRecord record);
int save_pond_record_csv(PondRecord record);
```

### 5. 最后补函数实现

每个函数只负责一个系统动作：

```text
read_pond_record       输入一条采样
fill_record_timestamp  给记录补时间
temp_status            温度关系判断
oxygen_status          溶氧关系判断
print_pond_record      观察输出
save_pond_record_csv   长期记忆
```

## 启发带练流程

以后遇到一个新功能，不直接问“代码怎么写”，先问：

```text
1. 这个功能属于系统里的存量、流量、关系、反馈、观察，还是记忆？
2. 它会改变系统状态吗？
3. 它需要被保存吗？
4. 它应该是结构体字段，还是一个函数？
5. 它应该在 main 的流程骨架里出现，还是被封装到函数里？
6. 它的输入、输出、返回值、副作用分别是什么？
```

再进入 C 代码：

```text
先写 main 骨架
-> 再写函数声明
-> 再写空函数体
-> 再补关键条件和返回值
-> 再编译运行
-> 再记录问题
```

## 主逻辑挖空模板

用于启发带练：

```c
int main(void)
{
    SystemState state;

    while (__________)
    {
        __________;  // 输入或采样
        __________;  // 更新状态或判断关系
        __________;  // 输出观察
        __________;  // 保存记忆
        __________;  // 必要时反馈控制
    }

    return 0;
}
```

函数骨架：

```c
int read_state(SystemState *state)
{
    // 输入层
}

void update_state(SystemState *state)
{
    // 流量层
}

const char *judge_state(SystemState state)
{
    // 关系层
}

int save_state(SystemState state)
{
    // 记忆层
}
```

## 常见误区

- 一上来就写代码，没有先定系统边界。
- 把所有变量都放进 `main`，没有区分存量和临时变量。
- 把打印函数当成保存函数，混淆观察层和存储层。
- 把判断逻辑散落在多个地方，没有收成关系函数。
- 在没有测试单步闭环前就扩展很多功能。
- 把系统动力学理解成复杂数学，而忘了它首先是“状态如何变化”的建模方式。

## 和当前主线的映射

当前项目处在：

```text
CLI 池塘记录系统
-> 单条记录建模
-> CSV 存储层
```

当前系统映射：

```text
存量：PondRecord 中的 temp / oxygen / pond_id / sampled_at
关系：temp_status() / oxygen_status()
观察：print_pond_record()
记忆：save_pond_record_csv()
反馈：低氧告警与设备控制，后续再接
时间推进：while (read_pond_record(&record))
```

下一步自然衔接：

```text
1. 先为 CSV 保存层写最小测试
2. 再把 record / judge / csv_store / input_cli / main 拆成多文件
3. 再加入查询统计
4. 再模拟连续时间采样
5. 再接传感器和板子
```

## 最小练习

每次新增功能都做这个练习：

```text
1. 用一句话定义系统边界
2. 列出 3 个存量
3. 列出 2 个关系函数
4. 写 main 骨架
5. 写函数声明
6. 只实现一个函数
7. 编译运行
8. 记录一个问题
```

## 收口标准

- 能把真实问题画成存量-流量-反馈语言。
- 能把存量映射成结构体字段。
- 能把关系映射成函数。
- 能让 `main` 只保留流程骨架。
- 能说明每个函数属于输入、关系、观察、记忆还是反馈。
- 能编译运行一个最小闭环。
