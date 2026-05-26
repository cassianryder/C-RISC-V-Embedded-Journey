# 2026-05-25 Control Interface Contract Bug Lab

> 实际执行时间：2026-05-26  
> 任务包路径：`codex/daily_packets/003-control-module-v0.md`

## 1. Bug 注入目标

训练 C 多文件模块中的接口合同一致性：

```text
include/control.h 负责声明 control 模块对外接口。
src/control.c 必须严格实现这个接口。
tests/test_control.c 只能通过公开接口调用 control 模块。
```

## 2. 注入位置

```text
src/control.c
```

目标函数：

```c
int control_should_aerate(PondRecord record)
```

## 3. 注入前 Clean Baseline

```yaml
make_test_control: pass
make_test: pass
git_diff_check: pass
tests_test_judge_diff: none
record_judge_source_diff: none
domain_snapshot_diff: none
exercise_reference_diff: none
```

## 4. 注入动作

临时把：

```c
int control_should_aerate(PondRecord record)
```

改成：

```c
int control_should_aerate(float oxygen)
```

函数体仍然保留：

```c
return needs_aeration(record);
```

## 5. 触发命令

```bash
make test
```

## 6. 报错信息

```text
src/control.c:5:5: error: conflicting types for 'control_should_aerate'
    5 | int control_should_aerate(float oxygen)
      |     ^
include/control.h:6:5: note: previous declaration is here
    6 | int control_should_aerate(PondRecord record);
      |     ^
src/control.c:7:24: error: use of undeclared identifier 'record'
    7 |  return needs_aeration(record);
      |                        ^~~~~~
2 errors generated.
make: *** [test_control] Error 1
```

## 7. 错误类型判断

```text
compile error / interface contract error
```

这不是链接错误。编译器在生成 `src/control.c` 对应目标文件之前，就已经发现 `control.h` 的声明和 `control.c` 的定义不一致。

## 8. 根因分析

`control.h` 中的接口合同是：

```c
int control_should_aerate(PondRecord record);
```

但 `src/control.c` 中临时改成了：

```c
int control_should_aerate(float oxygen)
```

这会导致同一个函数名对应两套不同参数类型。编译器无法确认调用方到底应该按哪一种函数签名来检查类型，所以直接报 `conflicting types`。

第二个错误 `use of undeclared identifier 'record'` 是连带错误：函数参数已经被改成 `oxygen`，函数体里却仍然使用 `record`。

## 9. 修复动作

恢复为：

```c
int control_should_aerate(PondRecord record)
{
 return needs_aeration(record);
}
```

## 10. 回归测试

```yaml
make_test_control: pass
make_test: pass
git_diff_check: pass
```

## 11. 防复发规则

```text
每新增一个模块，先写 .h 接口合同，再让 .c 严格实现该合同，测试只通过公开接口调用。
```

本次 control 模块的边界：

```text
judge: 判断是否需要增氧。
control: 调用 judge 的 needs_aeration(record)，生成是否建议增氧的控制决策。
hardware: 今日不涉及。
```

## 12. SRS 卡片

Q: 为什么 `control.h` 和 `control.c` 的函数签名必须完全一致？

A:

```text
因为 .h 是模块对外的接口合同，.c 是接口合同的实现。如果声明和定义的返回值或参数类型不一致，编译器会报 conflicting types。
```

Q: 为什么这次错误不是链接错误？

A:

```text
因为编译器在 src/control.c 内部已经发现函数声明和定义冲突，目标文件还没有生成，链接器还没有开始做符号解析。
```

Q: 为什么 control 模块不应该自己写 `oxygen < 5.0f`？

A:

```text
因为溶氧阈值判断属于 judge 层和领域证据层。control 层只复用 needs_aeration(record) 的结果，避免领域规则分散。
```

Q: `control_should_aerate(record)` 为什么接收 `PondRecord`，而不是 `float oxygen`？

A:

```text
因为当前 control 层依赖 needs_aeration(record)，输入保持整条记录更符合模块边界，也便于未来扩展人工模式、定时模式和设备状态。
```

## 13. 408 映射

```yaml
data_structure:
  - chapter: "第1章 绪论"
    section: "抽象数据类型"
    point: "数据对象、数据关系、基本操作"
    project_mapping: "PondRecord 是池塘记录对象，control_should_aerate(record) 是对该对象执行控制决策操作。"
    task_evidence: "include/control.h / src/control.c"

computer_organization:
  - chapter: "第1章 计算机系统概述"
    section: "高级语言程序到可执行程序的转换"
    point: "编译、汇编、链接"
    project_mapping: "函数签名冲突发生在编译阶段，目标文件尚未生成。"
    task_evidence: "conflicting types for 'control_should_aerate'"

  - chapter: "第1章 计算机系统概述"
    section: "needs_manual_alignment_with_wangdao"
    point: "符号引用、函数声明、函数定义"
    project_mapping: "control.h 提供声明，control.c 提供定义；二者必须一致。"
    task_evidence: "include/control.h:6 / src/control.c:5"

operating_system:
  - chapter: "第1章 计算机系统概述"
    section: "操作系统作为用户和硬件之间的接口"
    point: "shell 启动 make/gcc 进程"
    project_mapping: "用户执行 make test，make 调用 gcc 构建并运行测试程序。"
    task_evidence: "make test"

computer_network:
  - chapter: "不涉及"
    section: "不涉及"
    point: "今日无网络通信内容"
    project_mapping: "无"
    task_evidence: "无"

note:
  - "王道小节名称需后续结合手头教材版本做 needs_manual_alignment_with_wangdao。"
```

## 14. 是否进入长期问题库

```text
是
```
