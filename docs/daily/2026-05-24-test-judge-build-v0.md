# 2026-05-24 Test Judge Build v0

## 1. 今日主线

用户本人手动将 judge 模块测试入口固化到 Makefile。

目标从：

```text
手动 gcc 命令测试 judge 模块
```

升级为：

```text
make test_judge
make test
```

今日只做 judge 模块构建与测试闭环，不拆 `control`、`csv_store`、`input_cli`、`output` 或 `main`。

## 2. 上一个闭环 SRS 回顾

Q1: 为什么 `oxygen_status(record.oxygen)` 正确，而 `oxygen_status(record)` 错误？

A1: record是结构体对象本体，这里需要传入的是结构体成员oxygen

```text
因为 oxygen_status() 的参数是 float，需要传入结构体成员 record.oxygen，而不是整条 PondRecord 结构体。
```

Q2: 为什么 `needs_aeration(record)` 正确，而 `needs_aeration(record.oxygen)` 错误？

A2: needs函数的参数列表是PondRecord record，所以直接使用record

```text
因为 needs_aeration() 的参数是 PondRecord record，需要传入整条结构体记录。函数内部会读取 record.oxygen。
```

Q3: `.h` 文件和 `.c` 文件的职责边界是什么？

A3: .h负责接口层面，.c 负责主要逻辑模块

```text
.h 文件负责接口合同，放类型定义和函数声明。
.c 文件负责实现，放函数体和主要逻辑。
```

Q4: 为什么测试 judge 模块时需要把 `src/judge.c` 和 `tests/test_judge.c` 一起编译？

A4: 因为需要将judge和test进行链接

```text
tests/test_judge.c 负责调用 judge 函数，src/judge.c 负责提供 judge 函数实现。只有测试文件没有实现文件，会在链接阶段找不到函数定义。
```

Q5: 编译错误和链接错误的区别是什么？

A5: 编译错误是.c文件内部逻辑或其他问题，链接错误是.h接口设计问题，include guard

```text
编译错误通常发生在语法、类型、头文件、声明等阶段。
链接错误通常发生在函数声明存在，但链接器找不到函数实现，或目标文件没有参与链接时。
```

## 3. 领域证据与 gap 边界

```text
WQ-DO-GROWOUT-001 / SRC-001: oxygen_status() / needs_aeration() 可以继续引用。
GAP-001: 仍然 open。
temp_status: legacy behavior + needs_human_verification。
domain_snapshot: 未修改。
```

今日不新增温度阈值，不关闭 GAP-001，不把 `temp_status()` 的 legacy 行为解释为证据化领域规则。

## 4. 用户手动执行步骤

```bash
git status
nvim Makefile
make test_judge
make test
git diff --check
git diff -- tests/test_judge.c
git diff -- include/record.h include/judge.h src/judge.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
```

## 5. 主线测试结果

执行命令：

```bash
make test_judge
```

结果：

```text
gcc -Wall -g -Wextra -Iinclude src/judge.c tests/test_judge.c -o build/test_judge
./build/test_judge
测试通过！
```

执行命令：

```bash
make test
```

结果：

```text
gcc -Wall -g -Wextra -Iinclude src/judge.c tests/test_judge.c -o build/test_judge
./build/test_judge
测试通过！
```

执行命令：

```bash
git diff --check
```

结果：

```text
无输出，表示通过。
```

## 6. Clean Baseline

执行命令：

```bash
git diff -- tests/test_judge.c
git diff -- include/record.h include/judge.h src/judge.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
```

结果：

```text
均无输出。
```

含义：

```text
tests/test_judge.c 未修改。
include/record.h 未修改。
include/judge.h 未修改。
src/judge.c 未修改。
domain_snapshot 未修改。
exercises/06-projects/1.c 未修改。
```

## 7. Bug Lab 受控注入

### 7.1 注入目标

训练 C 多文件工程中的链接错误识别能力。

### 7.2 注入位置

```text
Makefile 的 test_judge 编译命令。
```

### 7.3 注入动作

临时删除 `src/judge.c`。

注入前：

```makefile
$(CC) $(CFLAGS) -Wextra -Iinclude src/judge.c tests/test_judge.c -o build/test_judge
```

注入后：

```makefile
$(CC) $(CFLAGS) -Wextra -Iinclude tests/test_judge.c -o build/test_judge
```

### 7.4 触发命令

```bash
make test_judge
```

### 7.5 报错信息

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

### 7.6 错误类型判断

```text
link error
```

原因：

```text
test_judge.c 能通过 judge.h 看到函数声明，但 src/judge.c 没有参与构建，链接器找不到 temp_status、oxygen_status、needs_aeration 的实现。
```

### 7.7 修复动作

把 `src/judge.c` 加回 Makefile 的 `test_judge` 编译命令。

修复后：

```makefile
$(CC) $(CFLAGS) -Wextra -Iinclude src/judge.c tests/test_judge.c -o build/test_judge
```

### 7.8 回归测试

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

## 8. 今日新增 SRS

Q1: 为什么 `test_judge.c` 包含 `judge.h` 仍然需要 `src/judge.c`？

A1:

```text
judge.h 只提供函数声明，src/judge.c 提供函数实现。链接阶段需要实现文件生成的目标代码。
```

Q2: `undefined symbols` 属于什么错误？

A2:

```text
链接错误。
```

Q3: `-Iinclude` 的作用是什么？

A3:

```text
告诉编译器到 include 目录查找头文件。
```

Q4: 为什么本任务禁止修改 `tests/test_judge.c`？

A4:

```text
因为今日目标是固化构建入口，不是修改测试语义。修改测试文件会制造无关 diff，破坏 bug 归因。
```

Q5: GAP-001 open 时，`temp_status()` 为什么不能被解释为证据化领域规则？

A5:

```text
因为当前没有已审核的 growout 阶段温度状态阈值卡，temp_status() 只能保持 legacy behavior + needs_human_verification。
```

## 9. 408 映射

```yaml
data_structure:
  - chapter: "第1章 绪论"
    section: "数据结构的基本概念"
    point: "数据对象、数据元素、数据结构"
    project_mapping: "PondRecord 是池塘记录这一数据对象的结构化表示，字段包括 temp、oxygen、pond_id。"
    task_evidence: "include/record.h"

  - chapter: "第1章 绪论"
    section: "算法和算法评价"
    point: "算法正确性、可读性、健壮性"
    project_mapping: "test_judge.c 用固定样例验证 temp_status、oxygen_status、needs_aeration 的回归行为。"
    task_evidence: "tests/test_judge.c + make test_judge"

computer_organization:
  - chapter: "第1章 计算机系统概述"
    section: "计算机系统层次结构"
    point: "高级语言程序到可执行程序的转换"
    project_mapping: "Makefile 调用 gcc，把 src/judge.c 和 tests/test_judge.c 构建为 build/test_judge。"
    task_evidence: "Makefile 中 test_judge 目标"

  - chapter: "第1章 计算机系统概述"
    section: "程序的编译、链接与执行"
    point: "目标文件链接与符号解析"
    project_mapping: "删除 src/judge.c 后出现 undefined symbols，说明链接阶段找不到 temp_status、oxygen_status、needs_aeration 的实现。"
    task_evidence: "Bug Lab 链接错误"

  - chapter: "第2章 数据的表示和运算"
    section: "浮点数的表示与运算"
    point: "float 数据参与比较"
    project_mapping: "oxygen_status(float) 和 temp_status(float) 使用 float 参数进行阈值判断。"
    task_evidence: "include/judge.h / src/judge.c"
    note: "今日不修改领域阈值，只做构建入口和链接错误训练。"

operating_system:
  - chapter: "第1章 计算机系统概述"
    section: "操作系统的概念、功能和目标"
    point: "操作系统作为用户与计算机硬件之间的接口"
    project_mapping: "用户在 shell 中执行 make，make 调用 gcc 和 ld 进程完成构建。"
    task_evidence: "make test_judge"

  - chapter: "第4章 文件管理"
    section: "文件、目录与路径名"
    point: "目录结构和文件路径"
    project_mapping: "include/、src/、tests/、build/ 共同构成当前 C 工程的文件组织。"
    task_evidence: "-Iinclude、src/judge.c、tests/test_judge.c、build/test_judge"

  - chapter: "第4章 文件管理"
    section: "目录结构与文件定位"
    point: "路径查找和输出目录"
    project_mapping: "mkdir -p build 保证 build/ 目录存在，-o build/test_judge 指定可执行文件输出位置。"
    task_evidence: "Makefile 中 test_judge 目标"

computer_network:
  - chapter: "不涉及"
    section: "不涉及"
    point: "今日任务无网络通信内容"
    project_mapping: "无"
    task_evidence: "无"

note:
  - "王道版本小节名称可能存在差异，后续需要结合手头教材做 needs_manual_alignment_with_wangdao。"
```

## 10. Git 提交

允许 add：

```bash
git add codex/daily_packets/002-test-judge-build-v0.md
git add Makefile
git add docs/daily/2026-05-24-test-judge-build-v0.md
git add bug_lab/2026-05-24-judge-build-link-error.md
```

禁止 add：

```text
build/
tmux-*.log
domain_snapshot/
tests/test_judge.c
include/record.h
include/judge.h
src/judge.c
exercises/06-projects/1.c
control / csv_store / input_cli / output / main 相关文件
```

推荐 commit：

```bash
git commit -m "build: complete guided judge test loop"
```

## 11. 明日建议

```text
复习 Makefile、gcc 编译、链接错误、Clean Baseline，再决定是否进入 control 模块拆分。
```
