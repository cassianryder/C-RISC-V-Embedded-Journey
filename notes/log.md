## 学习进度
- 2026-03-29：Day 1 - 01-basics:掌握关键字、标识符、数据类型（int/float/char/unsigned）、常量（数值/字符/字符串 + 转义）、变量、const、算术运算符。代码在 exercises/01-basics/1.c
- 2026-03-30：Day 2 - 01-basics:算术运算符，自增，自减（前置与后置，分别在printf中先计算后赋值，先赋值后计算），关系运算符，逻辑运算符，位运算符，补码（模运算）。代码在 exercises/01-basics/2.c 
- 2026-04-01: Day 3 -01-basics:赋值运算符，其他运算符（sizeof，三目运算符，逗号运算符），运算符的优先级和结合性，使用括号括起来的优先级最高，结合性只有三类是右结合性，分别位单目运算符号、赋值运算符号和三目运算符，表达式，语句（空语句，流程控制语句，表达式语句，复合语句，函数调用语句），流程控制语句（if语句）。代码在exercises/01-basics/3.c exercises/01-basics/4.c exercises/01-basics/5.c
- 2026-04-02: Day 4 -01-basics:if语句（if else语句，if else if语句），switch语句，if else if语句与switch语句的区别，for 语句，while语句，do while语句，跳转语句。代码在exercises/01-basics/6.c exercises/01-basics/7.c
- 2026-04-03: Day 5 -01-basics:跳转语句（break,continue,goto),数组，内存地址。代码在exercises/01-basics/7.c exercises/01-basics/8.c
- 2026-04-04: Day 6 -01-basics:int类型与进制输出，char类型常量变量补充。代码在exercises/01-basics/1.c exercis/01-basics/2.c
- 2026-04-06: Day 7 -01-basics:将char常量与变量补充部分完结，二维数组（初始化的三种方式，给出所有元素的值，给出部分元素的值，剩余的为0值，给出所有元素，可缺省最高维），makefile添加功能（make g，make asm，make mem）。代码在exercises/01-basics/2.c exercis/01-basics/8.c
- 2026-04-07: Day 8 -01-basics:字符数组(赋值方式，字符串使用 \0表示结束)。代码在exercises/01-basics/8.c
- 2026-04-08: Day 9 -01-basics:函数（函数的定义和声明），函数的基本格式（函数返回值类型，函数名，参数列表，函数体），函数的调用（库函数先导入头文件，自定义函数先定义或者先声明），声明格式（函数名+形参列表）。代码在在exercises/01-basics/1.c exercis/01-basics/9.c
- 2026-04-08: Day 10 -01-basics:函数形参与实参，值传递与址传递（在函数内部通过地址的方式修改实参），指针，数组作为函数参数时退化成指针。代码在在exercises/01-basics/1.c exercis/01-basics/10.c
- 2026-04-16: Day 11 -01-basics:函数形参与实参，函数的嵌套调用，指针，寻址，水产闭环小项目池塘水质监控系统（溶氧，温度，函数嵌调用）。代码在在exercises/01-basics/11.c 
- 2026-04-17: Day 12 -01-basics:函数形参与实参，函数的嵌套调用，水产闭环小项目池塘水质监控系统（溶氧，温度，函数嵌调用）优化两轮，c primer配套练习，getchar与putchar。代码在在exercises/01-basics/11.c
- 2026-04-18: Day 12 -01-basics:全局变量 静态变量（在静态内存空间随着程序的结束而终止）局部变量（随机，水调用结束二终止），函数的嵌套调用，c primer配套练习优化打印个人信息（for后面不加括号下一行，单人座），putchar实现（缓冲区buffer，文件io，系统调用（用户态内核态），write（）函数用法）。代码在在exercises/01-basics/11.c 
- 2026-04-20: Day 13 -01-basics:从11.c推进到12.c，完成自定义字符输出系统最小闭环：`my_putchar`、`my_flush`、`my_puts`、`my_putstr`、`my_ponds_status`。重点理解了 `getchar/putchar` 风格接口、`const char *s` 作为字符串入口、`'\0'` 作为字符串结束标记、`return c` 与 `return count` 的返回值链路、局部长度 `count` 与整行长度 `total` 的关系、`+=` 用于分段累计总长度、单字符输出与字符串输出的分层设计，以及 `printf` 和 `write` 混用时的缓冲与换行现象。水产耦合：输出单行池塘状态记录并统计记录长度。代码在 exercises/01-basics/12.c 
- 2026-04-21: Day 14 -01-basics:在 exercises/01-basics/12.c 中继续推进自定义输出系统，从固定字符串输出推进到基于温度与溶氧数值的状态判断输出。完成 `print_temp_status()` 与 `print_oxygen_status()` 两个判断函数，并在 `main` 中实现最小池塘状态记录输出：`pond A temp:Normal oxygen:Low`。重点理解了判断层与记录层的区别、字符串输出函数与单字符输出函数的分工、函数调用与字符串字面量的区别、输入格式 `%f %f` 与 `%f/%f` 的差异，以及旧测试代码与当前主线代码混存时如何收敛主线。水产耦合：根据水温和溶氧自动输出池塘状态文本。代码在 exercises/01-basics/12.c


