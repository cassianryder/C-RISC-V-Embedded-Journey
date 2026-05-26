# 2026-05-25 Control Module v0

> 实际执行时间：2026-05-26  
> 任务包：`codex/daily_packets/003-control-module-v0.md`  
> 说明：control 模块代码、测试、Bug 注入与修复由用户亲手完成；本 daily 在用户明确准许后由 Codex 整理写入。

## 0. 闭环身份确认

本次闭环主线是从 `record -> judge` 推进到：

```text
record -> judge -> control
```

用户亲手完成：

```text
include/control.h
src/control.c
tests/test_control.c
Makefile test_control 入口
Bug Lab 注入与修复
```

Codex 负责：

```text
SRS 校准
接口合同解释
代码审查
Makefile / 编译 / 链接解释
Bug Lab 归因
文档整理
```

## 1. 今日主线

今日最小目标：

```text
拆出 control 模块 v0，只封装“是否建议增氧”的控制决策。
```

实现链路：

```text
PondRecord record
-> needs_aeration(record)
-> control_should_aerate(record)
```

control v0 不新增领域阈值，不直接写 `oxygen < 5.0f`，不使用 `temp_status()` 做控制依据，不接硬件。

## 2. 上一个闭环 SRS 回顾

本次开始前抽问并校准：

```text
1. test_judge.c 包含 judge.h 仍然需要 src/judge.c，因为 .h 只有声明，.c 才有实现。
2. Undefined symbols 属于链接错误，表示链接器找不到函数定义。
3. -Iinclude 用于告诉编译器去 include/ 目录查找头文件。
4. control 不重复写 oxygen < 5.0f，因为领域判断应集中在 judge 层。
5. judge 负责判断，control 使用 judge 的结果形成控制建议。
```

## 3. 领域证据与 gap 边界

```yaml
WQ-DO-GROWOUT-001:
  status: allowed
  usage: "oxygen_status() / needs_aeration()"

GAP-001:
  status: open
  meaning: "growout temperature status thresholds 未证据化"

temp_status:
  status: legacy_behavior
  verification: needs_human_verification

control:
  new_domain_rule: false
  use_temp_status_for_control: false
  duplicate_oxygen_threshold: false

domain_snapshot:
  modified: false
```

## 4. 用户手动执行步骤

用户完成：

```text
1. 创建 include/control.h，声明 control_should_aerate(PondRecord record)。
2. 创建 src/control.c，调用 needs_aeration(record)。
3. 创建 tests/test_control.c，测试低氧、边界、正常三个场景。
4. 修改 Makefile，新增 test_control，并让 test 依赖 test_judge test_control。
5. 运行 make test_control。
6. 运行 make test。
7. 运行 git diff --check。
8. 完成 Clean Baseline。
9. 注入并修复 control 接口合同 Bug。
```

## 5. control 模块职责边界

```text
record:
  负责表达一条池塘记录的数据结构。

judge:
  负责根据领域证据判断水质状态，例如 needs_aeration(record)。

control:
  负责把 judge 的判断结果转成控制建议。

hardware:
  今日不涉及。未来才可能连接继电器、GPIO、增氧机。
```

当前 control v0：

```c
int control_should_aerate(PondRecord record)
{
 return needs_aeration(record);
}
```

## 6. 主线测试结果

```yaml
make_test_control: pass
make_test: pass
git_diff_check: pass
```

测试输出摘要：

```text
make test_control:
  control_should_aerate函数测试通过！

make test:
  测试通过！
  control_should_aerate函数测试通过！
```

## 7. Clean Baseline

```yaml
tests_test_judge_diff: none
record_judge_source_diff: none
domain_snapshot_diff: none
exercise_reference_diff: none
```

确认未修改：

```text
tests/test_judge.c
include/record.h
include/judge.h
src/judge.c
domain_snapshot/
exercises/06-projects/1.c
```

## 8. Bug Lab 受控注入

Bug Lab 文件：

```text
bug_lab/2026-05-25-control-interface-contract.md
```

### 8.1 注入目标

训练 `.h` 接口合同与 `.c` 实现必须一致。

### 8.2 注入位置

```text
src/control.c
```

### 8.3 注入动作

临时把：

```c
int control_should_aerate(PondRecord record)
```

改为：

```c
int control_should_aerate(float oxygen)
```

### 8.4 触发命令

```bash
make test
```

### 8.5 报错信息

核心报错：

```text
error: conflicting types for 'control_should_aerate'
note: previous declaration is here
error: use of undeclared identifier 'record'
```

### 8.6 错误类型判断

```text
compile error / interface contract error
```

不是链接错误。因为编译器在生成目标文件之前已经发现声明和定义冲突。

### 8.7 修复动作

恢复为：

```c
int control_should_aerate(PondRecord record)
{
 return needs_aeration(record);
}
```

### 8.8 回归测试

```yaml
make_test_control: pass
make_test: pass
git_diff_check: pass
```

## 9. 今日新增 SRS

Q1: 为什么 control 模块调用 `needs_aeration(record)`，而不是自己写 `oxygen < 5.0f`？

A1:

```text
因为溶氧阈值判断属于 judge 层和领域证据层。control 层只复用判断结果，避免领域规则分散。
```

Q2: `control_should_aerate` 为什么接收 `PondRecord`，而不是 `float oxygen`？

A2:

```text
因为当前 control 层依赖 needs_aeration(record)，接收整条记录能保持接口一致，也便于未来扩展人工模式、定时模式和设备状态。
```

Q3: `control.h` 和 `control.c` 的函数签名不一致会发生什么？

A3:

```text
会破坏接口合同，编译阶段可能出现 conflicting types。
```

Q4: `test_control` 为什么需要同时编译 `src/control.c` 和 `src/judge.c`？

A4:

```text
test_control.c 调用 control_should_aerate，control.c 实现该函数；control.c 又调用 needs_aeration，needs_aeration 的实现位于 judge.c。
```

Q5: GAP-001 open 时，control 能不能使用 `temp_status()` 作为正式控制依据？

A5:

```text
不能。温度状态阈值缺少 growout 阶段已审核领域卡，temp_status 只能保持 legacy behavior + needs_human_verification。
```

## 10. 408 映射

```yaml
data_structure:
  - chapter: "第1章 绪论"
    section: "数据结构的基本概念"
    point: "数据对象、数据元素、数据结构"
    project_mapping: "PondRecord 是池塘记录对象，作为 control_should_aerate 的输入。"
    task_evidence: "include/record.h + include/control.h"

  - chapter: "第1章 绪论"
    section: "抽象数据类型"
    point: "数据对象、数据关系、基本操作"
    project_mapping: "control_should_aerate(record) 是对 PondRecord 执行控制决策操作。"
    task_evidence: "include/control.h / src/control.c"

  - chapter: "第1章 绪论"
    section: "算法和算法评价"
    point: "算法正确性、可读性、健壮性"
    project_mapping: "tests/test_control.c 用低氧、边界、正常三个样例验证 control 模块。"
    task_evidence: "tests/test_control.c"

computer_organization:
  - chapter: "第1章 计算机系统概述"
    section: "高级语言程序到可执行程序的转换"
    point: "预处理、编译、汇编、链接"
    project_mapping: "Makefile 调用 gcc，把 src/judge.c、src/control.c、tests/test_control.c 构建成 build/test_control。"
    task_evidence: "Makefile test_control"

  - chapter: "第1章 计算机系统概述"
    section: "needs_manual_alignment_with_wangdao"
    point: "目标文件链接与符号解析"
    project_mapping: "control.c 调用 judge.c 中的 needs_aeration，构建时必须同时链接 judge.c。"
    task_evidence: "make test_control"

operating_system:
  - chapter: "第1章 计算机系统概述"
    section: "操作系统的概念、功能和目标"
    point: "操作系统作为用户与计算机硬件之间的接口"
    project_mapping: "用户在 shell 中执行 make，make 启动 gcc 和测试程序进程。"
    task_evidence: "make test"

  - chapter: "第4章 文件管理"
    section: "文件、目录与路径名"
    point: "目录结构和文件定位"
    project_mapping: "include/、src/、tests/、build/ 形成当前 C 工程组织。"
    task_evidence: "-Iinclude / src/control.c / tests/test_control.c / build/test_control"

computer_network:
  - chapter: "不涉及"
    section: "不涉及"
    point: "今日无网络通信内容"
    project_mapping: "无"
    task_evidence: "无"

note:
  - "王道小节名称后续需结合手头教材版本做 needs_manual_alignment_with_wangdao。"
```

## 11. Git 提交

允许 add：

```bash
git add codex/daily_packets/003-control-module-v0.md
git add include/control.h
git add src/control.c
git add tests/test_control.c
git add Makefile
git add docs/daily/2026-05-25-control-module-v0.md
git add bug_lab/2026-05-25-control-interface-contract.md
```

禁止 add：

```text
build/
tmux-*.log
domain_snapshot/
exercises/06-projects/1.c
tests/test_judge.c
include/record.h
include/judge.h
src/judge.c
csv_store / input_cli / output / main / hardware 相关文件
```

推荐 commit message：

```bash
git commit -m "feat: complete guided control module loop"
```

## 12. 明日建议

下一步建议不要立刻接硬件，先继续 Stage 2 模块化：

```text
1. 复习 control.h / control.c / test_control.c 的接口合同。
2. 若时间短，做 SRS + Bug Lab 回顾。
3. 若时间充足，进入 csv_store 或 input_cli 前先读下一份 daily packet。
4. 保持领域规则不分散：judge 管判断，control 管建议，hardware 管真实动作。
```
