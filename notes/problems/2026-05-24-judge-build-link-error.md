# 2026-05-24 Judge Build Link Error

## 今日主线

今天完成 `002 - Test Judge Build v0`：把 judge 模块测试从手动 `gcc` 命令升级为 `make test_judge` / `make test` 的稳定入口。

本次只做构建与测试闭环：固化 `Makefile` 入口、验证 clean baseline、注入并修复一次链接错误。不拆 `control / csv_store / input_cli / output / main`，不修改 `domain_snapshot`，不破坏 `exercises/06-projects/1.c` 单文件参考版。

## 1. 每日问题

### 问题 1：为什么 `test_judge.c` 已经 `#include "judge.h"`，仍然需要 `src/judge.c`？

费曼解释：

`judge.h` 像菜单，只告诉别人“这里有这些函数可以点”；`src/judge.c` 才像厨房，真正做出函数的结果。

在 C 多文件工程里，头文件主要提供类型定义和函数声明：

```c
const char *temp_status(float temp);
const char *oxygen_status(float oxygen);
int needs_aeration(PondRecord record);
```

这些声明让编译器知道函数名、参数类型和返回值类型，所以 `tests/test_judge.c` 能通过语法和类型检查。

但链接阶段需要找到函数体，也就是函数定义：

```text
src/judge.c
```

如果构建命令只编译 `tests/test_judge.c`，没有把 `src/judge.c` 放进来，链接器就只能看到“有人调用这些函数”，却找不到“这些函数在哪里实现”。

闭环规则：

```text
test_judge.c 负责调用。
judge.h 负责声明。
src/judge.c 负责实现。
Makefile 负责把调用者和实现者一起交给编译/链接流程。
```

### 问题 2：为什么 `Undefined symbols for architecture arm64` 是链接错误，而不是编译错误？

判断线索：

```text
ld: symbol(s) not found
```

`ld` 是链接器。报错来自 `ld`，说明程序已经走过了编译阶段，失败发生在链接阶段。

这次 bug 注入后的命令类似：

```makefile
$(CC) $(CFLAGS) -Wextra -Iinclude tests/test_judge.c -o build/test_judge
```

编译器能通过 `-Iinclude` 找到 `judge.h`，所以它知道 `temp_status()`、`oxygen_status()`、`needs_aeration()` 的声明。

但是链接器找不到这些符号的定义：

```text
_temp_status
_oxygen_status
_needs_aeration
```

因此这不是“函数没声明”的编译错误，而是“函数声明存在，但实现文件没有参与链接”的链接错误。

### 问题 3：`-Iinclude` 的作用是什么？它为什么不能替代 `src/judge.c`？

`-Iinclude` 的作用是告诉编译器：

```text
当代码写 #include "judge.h" 时，可以去 include/ 目录查找头文件。
```

它解决的是“去哪里找声明”的问题，不解决“去哪里找实现”的问题。

所以：

```text
-Iinclude 可以让编译器找到 judge.h。
src/judge.c 才能把 judge 函数实现交给链接器。
```

第一性原理拆解：

```text
声明：让编译器相信这个函数存在。
定义：真正给出这个函数的机器码来源。
链接：把调用点和实现点接起来。
```

### 问题 4：为什么 Bug Lab 前要先做 Clean Baseline？

Clean Baseline 的作用是先证明系统原本是干净的：

```bash
make test_judge
make test
git diff --check
git diff -- tests/test_judge.c
git diff -- include/record.h include/judge.h src/judge.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
```

这些命令通过后，再临时删除 `src/judge.c`，就可以确认后面的错误只来自这一个受控注入动作。

如果没有 baseline，报错可能来自测试文件、头文件、领域快照、旧代码或格式问题，bug 归因就会变浑。

闭环规则：

```text
先证明干净，再注入问题，再观察错误，再修复，再回归。
```

### 问题 5：为什么今天禁止修改 `tests/test_judge.c` 和 `src/judge.c`？

因为今天的目标不是改 judge 语义，而是固化构建入口。

如果同时修改测试、实现、领域阈值和 Makefile，一旦出错就无法判断原因：

```text
是测试写错？
是实现变了？
是领域阈值变了？
还是构建命令漏文件？
```

今天把变量压到最少，只让 `Makefile` 成为主角。这就是工程上的“单变量实验”。

## 2. 解答总结

今天真正吃透的是 C 多文件工程的四层关系：

```text
.h 文件：接口合同，给声明。
.c 文件：实现文件，给函数体。
Makefile：构建规则，决定哪些文件参与编译和链接。
ld 链接器：把函数调用和函数实现接起来。
```

`Undefined symbols` 的本质不是“头文件没 include”，而是：

```text
调用点已经存在，声明也能看见，但实现对应的目标代码没有参与链接。
```

这一步是从“写一个能跑的 C 文件”走向“管理一个 C 工程”的关键门槛。

## 3. 闭环

已完成闭环：

- `make test_judge` 通过。
- `make test` 通过。
- `git diff --check` 通过。
- 临时删除 `src/judge.c` 后成功触发 `Undefined symbols`。
- 恢复 `src/judge.c` 后回归通过。
- `domain_snapshot` 和 `exercises/06-projects/1.c` 保持不变。

最小复练：

```bash
make test_judge
make test
git diff --check
```

反向理解练习：

```text
如果看到 Undefined symbols：
1. 先看是不是 ld 报错。
2. 找缺失的符号名。
3. 找这些函数定义在哪个 .c 文件。
4. 检查 Makefile 是否把该 .c 文件加入构建命令。
```

## 4. 使用场景和启发思考

真实项目里，模块越拆越多后，链接错误会越来越常见。

例如后续拆出：

```text
control.c
csv_store.c
input_cli.c
output.c
```

每个模块都会有自己的 `.h` 和 `.c`。测试某个模块时，必须把测试文件和被测模块实现一起构建；如果模块之间有依赖，还要把依赖模块也一起加入链接。

这对水产项目的意义是：

```text
judge 模块可以独立测试。
control 模块可以独立测试。
csv_store 模块可以独立测试。
main 只负责组装流程。
```

这也是工程鲁棒性的来源：不是把所有逻辑塞进一个文件，而是让每个模块都有清晰接口、独立验证入口和可复现构建命令。

## 5. 408 映射

数据结构：

- `PondRecord` 是池塘采样记录的数据对象，字段把温度、溶氧和塘口编号组织成结构化数据。

计算机组成原理：

- 高级语言程序需要经过预处理、编译、汇编、链接，最终生成可执行文件。
- `test_judge.c` 中存在符号引用，`src/judge.c` 提供符号定义。
- 删除 `src/judge.c` 后，链接器无法解析 `temp_status`、`oxygen_status`、`needs_aeration`。

操作系统：

- shell 执行 `make`，`make` 启动 `gcc` / `ld` 进程完成构建。
- `include/`、`src/`、`tests/`、`build/` 体现了文件系统路径和目录组织。

计算机网络：

- 今日不涉及网络通信。

## 6. 明日自然衔接

明天先复盘三件事：

- `judge.h` 为什么只是接口合同。
- `src/judge.c` 为什么必须参与链接。
- `Makefile` 如何把多文件 C 工程变成可重复构建系统。

复盘稳定后，再选择是否进入 `control` 模块拆分；不要同时拆多个模块。
