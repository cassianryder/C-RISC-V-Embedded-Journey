# 副线搭配主线表

> 用途：把 Desktop/cassian/projects 中的副线自学资源，按“如何服务主线”重新组织。
> 原则：一次只保留 `1 个主线 + 1 个副线模块`；回流到主仓的是能力、函数、模块思路，而不是整仓复制。

## 当前主线

- 结构体主线：`exercises/03-structs/1.c`
- 项目主线：`exercises/06-projects/1.c`

当前阶段关键词：

- `PondRecord`
- 结构体作为函数参数
- `main` 只做流程控制
- 从单条记录走向多条记录、日志和采样

## 4 周搭配表

| 周次 | 主线推进什么 | 副线学哪个模块 | 哪个能力回流到 `C-RISC-V-Embedded-Journey` | 哪些不并入，只做理解加固 |
|---|---|---|---|---|
| 第 1 周 | 在 `exercises/03-structs/1.c` 中完成 `read_pond_record(PondRecord *record)`，吃透 `.` 和 `->`，把输入层从 `main` 中拆出来，再把阈值提成常量 | `aquaculture-c-mac-labs` 的 `pond_sample_clock` | `PondRecord` 加时间字段、周期采样循环、时间格式化、输出一条带时间戳记录的思路 | `coa_cli_lab` 的大小端、内存表示、栈帧实验先不并代码，只帮助理解结构体和调用栈 |
| 第 2 周 | 主线切到 `exercises/06-projects/1.c`，做“多条记录”版本：最近 N 条、查看记录、保存 CSV | `aquaculture-github-c-labs` 的 `pond_ring_queue`，配合 `ds_cli_lab` 的 `02_stack_queue.c` | 环形缓冲、队列接口、最近 N 条记录缓存 | `ds_cli_lab` 中数组/链表、哈希表先不并，只做数据结构理解 |
| 第 3 周 | 主线继续在 `exercises/06-projects/1.c` 做“判断更稳”：原始值、平滑值、告警值分层；同时加事件日志 | `sensor_ema_filter` + `pond_event_logger` | EMA 平滑函数、事件结构体、日志追加、日志等级 | `os_cli_lab` 先不并，`fork/pipe/mmap` 只做系统直觉，不进主线 |
| 第 4 周 | 主线做“控制逻辑收口”：不再让 `if` 四处散，开始准备增氧机/告警器控制版本；如果前 3 周稳，再开始网络准备 | `aerator_fsm`，如果顺利再摸 `pond_udp_telemetry` | `enum`、状态机转移、事件驱动控制；如果够稳，再回流 UDP 文本消息格式 | `cn_cli_lab` 的 TCP echo、DNS，`ssh-latency-lab` 全部先不并，只做网络链路理解 |

## 最推荐的回流顺序

按对主线收益排序，建议这样回流：

1. `read_pond_record(PondRecord *record)`
2. 时间字段与采样时钟
3. 环形缓冲保存最近 N 条记录
4. CSV 追加保存
5. EMA 平滑
6. 事件日志
7. 状态机
8. UDP 遥测

## 适合并入主项目的副线能力

这些能力最值得回流到主仓：

- `pond_sample_clock`
- `pond_ring_queue`
- `sensor_ema_filter`
- `pond_event_logger`
- `aerator_fsm`
- `pond_udp_telemetry`

建议的回流方式不是整仓复制，而是：

- 抽一个函数
- 抽一个结构体
- 抽一个模块思路
- 放进 `exercises/03-structs`、`04-data-structures`、`05-embedded`、`06-projects`

## 不适合并入，只做理解加固

这些副线不建议整仓并入 `C-RISC-V-Embedded-Journey`：

- `coa_cli_lab`
- `os_cli_lab`
- `cn_cli_lab`
- `ssh-latency-lab`
- `c_side_quests`

它们的角色分别是：

- `coa_cli_lab`：帮助理解字节、内存、栈、调用
- `os_cli_lab`：帮助理解系统调用、进程、文件描述符
- `cn_cli_lab`：帮助理解网络字节序、socket 基础
- `ssh-latency-lab`：帮助理解遥测链路、延迟来源和 C/S 系统映射
- `c_side_quests`：帮助建立 Unix / C 的系统感

这些副线更适合：

- 做理论和系统直觉补强
- 解决“为什么”层面的不稳
- 不直接混进你的水产主线代码

## 并入主仓的正确方式

主仓的成长方式应该是：

- `exercises/03-structs`
  - 放结构体记录、输入封装、结构体指针

- `exercises/04-data-structures`
  - 放环形缓冲、DFS、哈希、数组/链表对照

- `exercises/05-embedded`
  - 放滤波、状态机、控制逻辑、设备侧思维

- `exercises/06-projects`
  - 放 CLI、记录器、日志系统、遥测版本

也就是说：

- 主仓保留自己的主线叙事
- 副线仓库保留独立实验场角色
- 主仓只吸收真正成熟的能力模块

## 一句话总策略

最优解不是“多学很多副线”，而是：

- 主线在 `exercises/03-structs/1.c` 和 `exercises/06-projects/1.c` 稳步推进
- 每周只抽 `1` 个最贴近主线的副线模块
- 只回流能力，不回流整仓
