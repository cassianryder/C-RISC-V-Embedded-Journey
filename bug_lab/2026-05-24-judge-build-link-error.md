# 2026-05-24 Judge Build Link Error Bug Lab

## 1. Bug 注入目标

训练 C 多文件工程中的链接错误识别能力。

本次目标是理解：

```text
头文件只提供函数声明，不能替代 .c 文件中的函数实现。
```

## 2. 注入位置

Makefile 的 `test_judge` 编译命令。

## 3. 注入前 Clean Baseline

执行命令：

```bash
make test_judge
make test
git diff --check
git diff -- tests/test_judge.c
git diff -- include/record.h include/judge.h src/judge.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
```

结果：

```text
make test_judge: pass
make test: pass
git diff --check: pass
tests/test_judge.c: no diff
include/record.h include/judge.h src/judge.c: no diff
domain_snapshot: no diff
exercises/06-projects/1.c: no diff
```

## 4. 注入动作

临时删除 `test_judge` 编译命令中的 `src/judge.c`。

注入前：

```makefile
$(CC) $(CFLAGS) -Wextra -Iinclude src/judge.c tests/test_judge.c -o build/test_judge
```

注入后：

```makefile
$(CC) $(CFLAGS) -Wextra -Iinclude tests/test_judge.c -o build/test_judge
```

## 5. 触发命令

```bash
make test_judge
```

## 6. 报错信息

```text
gcc -Wall -g -Wextra -Iinclude tests/test_judge.c -o build/test_judge
Undefined symbols for architecture arm64:
  "_needs_aeration", referenced from:
      _main in test_judge-85b4cd.o
      _main in test_judge-85b4cd.o
  "_oxygen_status", referenced from:
      _main in test_judge-85b4cd.o
      _main in test_judge-85b4cd.o
  "_temp_status", referenced from:
      _main in test_judge-85b4cd.o
      _main in test_judge-85b4cd.o
      _main in test_judge-85b4cd.o
ld: symbol(s) not found
```

## 7. 错误类型判断

```text
link error
```

判断依据：

```text
报错来自 ld，说明失败发生在链接阶段。
test_judge.c 已经能看到函数声明，但链接器找不到函数实现。
```

## 8. 根因分析

`tests/test_judge.c` 中调用了：

```c
temp_status(...)
oxygen_status(...)
needs_aeration(...)
```

这些函数的声明来自：

```c
#include "judge.h"
```

但是函数实现位于：

```text
src/judge.c
```

当 Makefile 的编译命令中删除 `src/judge.c` 后，编译器仍然可以通过 `judge.h` 看到函数声明，所以编译阶段可以继续。

但链接阶段需要找到这些函数的真实实现。由于 `src/judge.c` 没有参与构建，链接器找不到 `temp_status`、`oxygen_status`、`needs_aeration` 的函数定义，于是报出 `Undefined symbols`。

## 9. 修复动作

把 `src/judge.c` 加回 Makefile 的 `test_judge` 编译命令。

修复后：

```makefile
$(CC) $(CFLAGS) -Wextra -Iinclude src/judge.c tests/test_judge.c -o build/test_judge
```

## 10. 回归测试

执行命令：

```bash
make test_judge
make test
git diff --check
```

结果：

```text
make test_judge: pass
make test: pass
git diff --check: pass
```

## 11. 防复发规则

```text
多文件 C 测试必须同时编译测试文件和实现文件。
```

具体规则：

```text
1. test_judge.c 负责调用 judge 模块函数。
2. judge.h 负责提供函数声明。
3. src/judge.c 负责提供函数实现。
4. 只有声明没有实现，会导致链接错误。
```

## 12. SRS 卡片

Q: 为什么 `test_judge.c` 包含了 `judge.h`，仍然需要 `src/judge.c` 参与编译？

A: judge.h是接口层，实现层在src/judge.c中

```text
因为 judge.h 只提供函数声明，src/judge.c 才提供函数实现。编译阶段看到声明即可继续，但链接阶段必须找到函数定义，否则会出现 undefined symbols。
```

Q: `Undefined symbols` 通常属于什么错误？

A: 属于链接错误，未定义的符号

```text
链接错误。它表示链接器找不到某些函数或变量的定义。

Q: 为什么这次不是编译错误？

A:

因为 tests/test_judge.c 能通过 judge.h 看到函数声明，语法和类型检查可以继续。
失败发生在链接阶段，链接器找不到 src/judge.c 中的函数实现，所以这是 link error。


## 13. 408 映射

data_structure:
  - chapter: "第1章 绪论"
    section: "数据结构的基本概念"
    point: "数据对象与结构化表示"
    project_mapping: "PondRecord 把一条池塘记录组织为结构体对象，供 needs_aeration(record) 使用。"
    task_evidence: "include/record.h"

computer_organization:
  - chapter: "第1章 计算机系统概述"
    section: "计算机系统层次结构"
    point: "高级语言程序到机器可执行程序的转换"
    project_mapping: "test_judge.c 和 judge.c 需要一起参与构建，最终链接成 build/test_judge。"
    task_evidence: "Makefile test_judge 目标"

  - chapter: "第1章 计算机系统概述"
    section: "程序的编译与链接"
    point: "符号引用和符号定义"
    project_mapping: "test_judge.c 中存在对 temp_status、oxygen_status、needs_aeration 的符号引用；src/judge.c 提供符号定义。删除 src/judge.c 后链接器无法解析这些符号。"
    task_evidence: "Undefined symbols for architecture arm64"

operating_system:
  - chapter: "第1章 计算机系统概述"
    section: "操作系统作为用户和硬件之间的接口"
    point: "进程执行"
    project_mapping: "shell 执行 make，make 启动 gcc/ld，最终运行 build/test_judge。"
    task_evidence: "make test_judge"

  - chapter: "第4章 文件管理"
    section: "文件和目录"
    point: "路径名和目录组织"
    project_mapping: "src/judge.c、tests/test_judge.c、include/judge.h、build/test_judge 都依赖正确路径。"
    task_evidence: "Bug Lab 中 Makefile 编译命令"

computer_network:
  - chapter: "不涉及"
    section: "不涉及"
    point: "今日无网络通信"
    project_mapping: "无"
    task_evidence: "无"

note:
  - "王道小节名称需后续按手头教材版本做 needs_manual_alignment_with_wangdao。"
```


## 14. 是否进入长期问题库

```text
是
```
