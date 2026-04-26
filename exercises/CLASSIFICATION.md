# Exercises Classification

## 原则

- `01-basics/` 保留原始学习轨迹，不直接搬走旧编号文件。
- 其他目录用于放“按主题整理后的干净版本”或“后续应迁入该主题的稳定文件”。
- 也就是说：
  - `01-basics` 是学习现场
  - `02/03/04/05/06/07` 是主题线

## 当前归类

| 原始文件 | 当前主题判断 | 说明 |
|---|---|---|
| `01-basics/1.c` | `01-basics` | 数据类型、进制、`sizeof`、地址输出 |
| `01-basics/2.c` | `01-basics` | 运算符、字符输入输出 |
| `01-basics/3.c` | `01-basics` | 赋值、三目、逗号运算符 |
| `01-basics/4.c` | `01-basics` | 表达式、类型转换、基本输入 |
| `01-basics/5.c` | `01-basics` | `if / else / switch` |
| `01-basics/6.c` | `01-basics` | `for / while / do while` |
| `01-basics/7.c` | `01-basics` | `break / continue / goto` |
| `01-basics/8.c` | `01-basics` | 数组、二维数组、字符串基础 |
| `01-basics/9.c` | `01-basics` | 函数定义、调用、数组作参数 |
| `01-basics/10.c` | `02-pointers` | 指针、址传递、数组参数退化 |
| `01-basics/11.c` | `05-embedded` / `07-basics-add` | 自定义字符输出、`write()`、缓冲区、递归/嵌套调用混合桥接文件 |
| `01-basics/12.c` | `06-projects` / `05-embedded` | 池塘记录原型 + 自定义输出系统混合演化文件 |
| `01-basics/13.c` | `07-basics-add` | 递归、栈帧地址、输入缓冲、字符串长度补强 |
| `01-basics/14.c` | `07-basics-add` | C Primer / K&R 过渡、递归二进制输出、函数声明与类型边界 |
| `01-basics/15.c` | `03-structs` | 结构体主线的预留编号位 |

## 当前恢复后的主题入口

- `02-pointers/1.c`：从 `10.c` 抽出的指针主题
- `03-structs/1.c`：结构体版池塘记录主线
- `05-embedded/1.c`：从 `11.c` 抽出的自定义字符输出桥接版
- `06-projects/1.c`：项目种子版本
- `07-basics-add/1.c`：从 `13.c` 抽出的递归栈帧演示

## 暂时只建目录，不急着放稳定代码的主题

- `04-data-structures/`
  - 目前 `01-basics` 里还没有真正稳定的数据结构文件
  - 现在只把它恢复为独立目录，等后续 ring queue、stack/queue、DFS 等能力回流时再放正式代码
