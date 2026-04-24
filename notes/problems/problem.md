# Problems Index

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


## Pending Deep Questions
- 在内存中数据和信号如何区分？
- 在 `char` 中数值 `-1` 与 `255` 的补码关系是什么？
- 补码为什么要取反加 1，背后的数学结构是什么？
- 在补码没发明之前，计算机如何实现减法？
- 在电路中如何识别最高位？
