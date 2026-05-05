# Problems Index

## 2026-04-19
- 从 `Pending Deep Questions` 中拆出的内存、信号、补码与最高位专题问题单，见：
- [2026-04-19-deep-questions-memory-twos-complement.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/notes/problems/2026-04-19-deep-questions-memory-twos-complement.md)

## 2026-04-18
- C 标准库 `FILE` 缓冲区和内核缓冲区的区别？
- 内核页缓存（page cache）是什么？`write()` 之后数据立刻到磁盘吗？
- 隐式刷新是什么？隐式刷新会触发系统调用吗？和 `scanf()` 有什么关系？
- 栈指针的功能是什么？栈指针寄存器是什么结构？
- `11.c` 中 `return c` 的作用是什么？数值和字符的区别是什么？为何 `char buf[]` 改成 `int buf[]` 后输出会异常？

## 2026-04-20
- `11.c` 和 `12.c` 中围绕 `my_putchar`、`getchar`、`my_puts`、`my_putstr`、`my_ponds_status` 的问题整理，见：
- [2026-04-20-buffered-output-11-12.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/notes/problems/2026-04-20-buffered-output-11-12.md)
## 2026-04-21
- `12.c` 中从固定字符串输出推进到“温度/溶氧自动判断输出”的问题整理，见：
- [2026-04-21-status-judging-output.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/notes/problems/2026-04-21-status-judging-output.md)

## 2026-04-22
- `13.c` 中结合 C Primer 练习时围绕递归、栈帧地址、输入缓冲、字符串/数组、`strlen` 与 `sizeof` 的问题整理，见：
- [2026-04-22-cprimer-recursion-strings.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/notes/problems/2026-04-22-cprimer-recursion-strings.md)

## 2026-04-24
- `14.c` 中结合 C Primer 习题时围绕函数声明、隐式类型转换、递归二进制输出、三目运算符与格式说明符匹配的问题整理，见：
- [2026-04-24-cprimer-functions-binary.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/notes/problems/2026-04-24-cprimer-functions-binary.md)

## 2026-04-25
- 结构体主线切换、结构体成员访问、函数职责边界、Makefile 映射整理与副线并入策略的问题整理，见：
- [2026-04-25-structs-mainline-architecture.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/notes/problems/2026-04-25-structs-mainline-architecture.md)

## 2026-04-27
- `03-structs/1.c` 中围绕结构体类型/对象/地址分层、`.` 与 `->`、值传递与指针传递、以及 `read_pond_record(PondRecord *record)` 输入封装的问题整理，见：
- [2026-04-27-struct-object-pointer-semantics.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/notes/problems/2026-04-27-struct-object-pointer-semantics.md)

## 2026-04-29
- `03-structs/1.c` 的结构体样板定位、`scanf` 返回值语义、`read_pond_record(&record)` / `print_pond_record(record)` 的接口边界，以及 `15.c` 向多主题目录拆分与项目种子线迁移的问题整理，见：
- [2026-04-29-structs-project-migration-and-scanf.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/notes/problems/2026-04-29-structs-project-migration-and-scanf.md)

## 2026-04-30
- `06-projects/1.c` 中状态文本返回、`const char *`、时间戳链路、`strftime` 缓冲区参数与 CSV 前置设计的问题整理，见：
- [2026-04-30-project-time-and-status-layer.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/notes/problems/2026-04-30-project-time-and-status-layer.md)

## 2026-05-02
- `06-projects/1.c` 中 CSV 保存函数、文件 I/O、返回值合同、保存位置与运行产物边界的问题整理，见：
- [2026-05-02-project-csv-return-contract.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/notes/problems/2026-05-02-project-csv-return-contract.md)

## 2026-05-05
- SRS 时间段动态加权、理解校准门槛、结构体指针层级、`const char *`、指针大小/二级指针、宏与字符串字面量、CSV 运行产物边界的问题整理，见：
- [2026-05-05-srs-pointer-csv-macro.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/notes/problems/2026-05-05-srs-pointer-csv-macro.md)


## Pending Deep Questions
- 已拆分到 `2026-04-19` 的专题问题单中，后续按 `COA / CSAPP / 位运算 / 结构体内存布局` 继续闭环。
