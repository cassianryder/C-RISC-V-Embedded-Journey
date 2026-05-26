# 003 - Control Module v0

## 0. Metadata

packet_id: 003-control-module-v0
stage: Stage 2-1
task_name: Control Module v0
task_type: module_split
execution_mode: guided_practice
human_must_execute: true
codex_can_modify_files: false
codex_can_only_review: true

reference_files:
  - AGENTS.md
  - learning/c_embedded_skill_graph.yml
  - learning/aquaculture_cycle_map.yml
  - learning/daily_planning_policy.md
  - domain_snapshot/cards/
  - domain_snapshot/source_index.md
  - domain_snapshot/gap_list.md
  - codex/daily_packets/001-judge-module-v0.md
  - codex/daily_packets/002-test-judge-build-v0.md
  - docs/daily/2026-05-23-judge-module-v0.md
  - docs/daily/2026-05-24-test-judge-build-v0.md
  - bug_lab/2026-05-23-judge-parameter-contract.md
  - bug_lab/2026-05-24-judge-build-link-error.md
  - notes/current.md
  - Makefile
  - include/record.h
  - include/judge.h
  - src/judge.c
  - tests/test_judge.c
  - exercises/06-projects/1.c

target_files_user_may_edit:
  - include/control.h
  - src/control.c
  - tests/test_control.c
  - Makefile
  - docs/daily/2026-05-25-control-module-v0.md
  - bug_lab/2026-05-25-control-interface-contract.md

readonly_files_for_codex:
  - include/control.h
  - src/control.c
  - tests/test_control.c
  - Makefile
  - docs/daily/2026-05-25-control-module-v0.md
  - bug_lab/2026-05-25-control-interface-contract.md
  - include/record.h
  - include/judge.h
  - src/judge.c
  - tests/test_judge.c
  - exercises/06-projects/1.c

forbidden_files:
  - domain_snapshot/
  - domain_snapshot/cards/
  - domain_snapshot/source_index.md
  - domain_snapshot/gap_list.md
  - src/csv_store.c
  - include/csv_store.h
  - src/input_cli.c
  - src/output.c
  - main.c
  - hardware/
  - drivers/
  - .gitignore
  - build/
  - tmux-*.log

domain_cards:
  - WQ-DO-GROWOUT-001

related_gaps:
  - GAP-001: Growout temperature status thresholds

bug_lab_required: true
bug_lab_mode: controlled_injection
bug_lab_design_by: codex
bug_lab_injection_by: human
bug_lab_repair_by: human
bug_lab_review_by: codex

srs_required: true
srs_source:
  - docs/daily/2026-05-24-test-judge-build-v0.md
  - bug_lab/2026-05-24-judge-build-link-error.md
  - current_main_task
  - current_bug_lab

git_commit_required: true

## 1. 今日主线

今日只解决一个核心目标：

```text
用户本人手动拆出最小 control 模块 v0，只封装“是否建议增氧”的控制决策。
```

今日主线不是写自动控制系统，不是接继电器，不是接 GPIO，不是新增领域规则。

今日完整闭环是：

```text
上一个闭环 SRS 抽卡
→ 用户手动阅读 record / judge / Makefile
→ Codex 解释 control 模块边界和建议接口
→ 用户手动创建 include/control.h
→ 用户手动创建 src/control.c
→ 用户手动创建 tests/test_control.c
→ 用户手动修改 Makefile 增加 test_control
→ 用户手动运行 make test_control
→ 用户手动运行 make test
→ 用户手动做 Clean Baseline
→ Codex 设计 Bug Lab 受控注入
→ 用户手动注入 bug
→ 用户手动触发失败
→ 用户手动修复
→ 用户手动写 bug_lab
→ 用户手动写 docs/daily
→ Codex 审查
→ 用户手动 Git 提交
```

今日目标从：

```text
record → judge
```

升级为：

```text
record → judge → control
```

control 模块 v0 的唯一职责：

```text
调用 needs_aeration(record)，返回是否建议增氧。
```

推荐接口：

```c
int control_should_aerate(PondRecord record);
```

推荐语义：

```text
如果 needs_aeration(record) 为真，则 control_should_aerate(record) 返回 1。
否则返回 0。
```

今日重点训练能力：

```text
1. 模块职责拆分
2. .h 接口合同设计
3. .c 实现与头文件一致性
4. control 对 judge 的依赖边界
5. Makefile 多模块测试目标
6. C 函数声明与定义一致性
7. Bug Lab 受控接口错误注入
8. SRS 抽卡
9. 王道 408 小节映射
10. Git 最小提交
```

Codex 在今日主线中的角色：

```text
带练、解释、审查、提问、设计 Bug Lab，不代工。
```

## 2. 今日不做

今日明确不做：

```text
1. 不拆 csv_store 模块。
2. 不拆 input_cli 模块。
3. 不拆 output 模块。
4. 不重构 main。
5. 不接硬件。
6. 不接 Milk-V Duo S。
7. 不写 GPIO。
8. 不写 relay 控制。
9. 不写 open_aerator()。
10. 不写 turn_on_motor()。
11. 不写真实执行器驱动。
12. 不新增传感器逻辑。
13. 不新增前端。
14. 不新增数据库。
15. 不做 AI 预测。
16. 不做新的领域资料检索。
17. 不做证据审核。
18. 不修改 domain_snapshot。
19. 不修改 domain_snapshot/cards/。
20. 不修改 domain_snapshot/source_index.md。
21. 不修改 domain_snapshot/gap_list.md。
22. 不新增日本沼虾温度阈值。
23. 不把 temp_status() 的 legacy 行为解释成领域规则。
24. 不关闭 GAP-001。
25. 不破坏 exercises/06-projects/1.c 单文件参考版。
26. 不修改 tests/test_judge.c。
27. 不修改 include/record.h。
28. 不修改 include/judge.h。
29. 不修改 src/judge.c。
30. 不整理无关缩进。
31. 不格式化无关代码。
32. 不补充无关注释。
33. 不做顺手优化。
34. 不重命名 judge 既有函数。
35. 不移动已有文件。
36. 不让 Codex 自动修改文件。
37. 不让 Codex 自动生成 docs/daily 成品。
38. 不让 Codex 自动生成 bug_lab 成品。
```

今日所有动作必须服务于：

```text
最小 control 模块拆分 + 闭环末端 Bug Lab 训练
```

## 3. 证据与 gap 约束 + 上一个闭环 SRS 抽卡

### 3.1 领域证据约束

Codex 和用户必须先确认：

```text
1. 当前任务是否涉及领域判断：涉及已有 needs_aeration(record) 的使用，但不新增领域判断。
2. 是否涉及养殖阈值：不新增阈值。
3. 是否涉及病害判断：不涉及。
4. 是否涉及投喂比例：不涉及。
5. 是否涉及增氧建议：只封装已有 needs_aeration(record) 的结果，不新增增氧规则。
6. 是否涉及 open gap：涉及 GAP-001，但只作为禁止约束。
```

当前允许继续引用：

```yaml
card_id: WQ-DO-GROWOUT-001
source_id: SRC-001
usage: oxygen_status() / needs_aeration()
status: allowed
```

control 模块允许做：

```text
调用 needs_aeration(record)
```

control 模块禁止做：

```text
1. 直接写 oxygen < 5.0。
2. 直接写任何 DO 阈值。
3. 直接写 temp_status 控制逻辑。
4. 直接根据温度控制。
5. 直接生成新领域建议。
```

当前必须保持：

```yaml
function: temp_status()
status: legacy_behavior
verification: needs_human_verification
gap: GAP-001 open
```

没有领域卡支撑的内容只能标记为：

```text
needs_human_verification
```

或：

```text
hypothesis
```

不得进入正式代码逻辑。

### 3.2 gap_list 约束

当前相关 gap：

```text
GAP-001: Growout temperature status thresholds
```

约束如下：

```text
1. GAP-001 仍为 open。
2. temp_status() 不得作为 evidence-based domain rule。
3. control 模块不得使用 temp_status() 作为正式控制依据。
4. control 模块不得新增温度控制规则。
5. 不得新增温度阈值。
6. 不得关闭 GAP-001。
7. 不得在 docs/daily 中写“温度规则已证据化”。
8. 今日不是 domain_rule_update 类型任务。
```

### 3.3 上一个闭环 SRS 抽卡要求

每日任务开始前，必须从上一个闭环抽取 SRS。

SRS 来源：

```text
1. docs/daily/2026-05-24-test-judge-build-v0.md
2. bug_lab/2026-05-24-judge-build-link-error.md
3. 上一次链接错误训练
4. 上一次 Makefile 测试入口固化
5. 上一次 408 映射薄弱点
```

Codex 第一轮必须抽取 3-5 张 SRS 候选卡，但只提问，不代替用户答。

本次至少抽问：

```text
Q1: 为什么 test_judge.c 包含 judge.h 仍然需要 src/judge.c 参与编译？
A1: 用户回答后 Codex 校准。

Q2: Undefined symbols 属于编译错误还是链接错误？为什么？
A2: 用户回答后 Codex 校准。

Q3: -Iinclude 的作用是什么？
A3: 用户回答后 Codex 校准。

Q4: 为什么 control 模块不应该重复写 oxygen < 5.0？
A4: 用户回答后 Codex 校准。

Q5: judge 和 control 的职责边界是什么？
A5: 用户回答后 Codex 校准。
```

SRS 卡片分类：

```text
C 语言接口类
Makefile / 构建类
GCC 编译类
链接类
模块边界类
Bug 归因类
领域证据类
408 映射类
```

后期这些 SRS 可以整理为类似 Anki 的抽卡系统：

```text
daily_srs/cards/
daily_srs/review_log/
daily_srs/tag_index.yml
```

但今日不强制生成 Anki 文件，只要求：

```text
1. 今日开始前抽问
2. 今日结束后新增 SRS 候选
3. docs/daily 中记录
```

## 4. 用户手动执行步骤

Codex 不允许替用户执行闭环。

用户必须亲自完成：

```text
1. 打开 nvim。
2. 阅读 Makefile。
3. 阅读 include/record.h。
4. 阅读 include/judge.h。
5. 阅读 src/judge.c。
6. 阅读 tests/test_judge.c。
7. 手动创建 include/control.h。
8. 手动创建 src/control.c。
9. 手动创建 tests/test_control.c。
10. 手动修改 Makefile。
11. 手动运行 make test_control。
12. 手动运行 make test。
13. 手动运行 git diff --check。
14. 手动运行 Clean Baseline 检查。
15. 手动注入 Bug Lab bug。
16. 手动触发 bug。
17. 手动修复 bug。
18. 手动写 bug_lab。
19. 手动写 docs/daily。
20. 手动 git add / commit。
```

今日主线建议手动步骤：

```bash
# 1. 查看当前状态
git status

# 2. 阅读现有 judge / record / Makefile
sed -n '1,160p' include/record.h
sed -n '1,160p' include/judge.h
sed -n '1,200p' src/judge.c
sed -n '1,200p' tests/test_judge.c
sed -n '1,200p' Makefile

# 3. 手动创建 control 接口
nvim include/control.h

# 4. 手动创建 control 实现
nvim src/control.c

# 5. 手动创建 control 测试
nvim tests/test_control.c

# 6. 手动修改 Makefile，增加 test_control
nvim Makefile

# 7. 运行测试
make test_control
make test

# 8. 检查 diff
git diff
git diff --check

# 9. Clean Baseline 检查
git diff -- tests/test_judge.c
git diff -- include/record.h include/judge.h src/judge.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c

# 10. 将输出贴给 Codex 审查
```

建议 control.h 最小结构：

```c
#ifndef CONTROL_H
#define CONTROL_H

#include "record.h"

int control_should_aerate(PondRecord record);

#endif
```

建议 control.c 最小结构：

```c
#include "control.h"
#include "judge.h"

int control_should_aerate(PondRecord record)
{
    return needs_aeration(record);
}
```

建议 test_control.c 最小测试方向：

```text
1. 构造 low oxygen record，control_should_aerate(record) 应返回 1。
2. 构造 normal oxygen record，control_should_aerate(record) 应返回 0。
3. 测试只通过 control.h 调用 control 模块公开接口。
4. 不直接测试 oxygen < 5.0。
5. 不测试 temp_status 控制逻辑。
```

建议 Makefile 增加：

```makefile
test_control:
	@mkdir -p build
	$(CC) $(CFLAGS) -Wextra -Iinclude src/judge.c src/control.c tests/test_control.c -o build/test_control
	./build/test_control
```

建议 make test 更新为：

```makefile
test: test_judge test_control
```

Codex 只能在用户每一步之后做：

```text
解释
检查
纠偏
提问
建议下一步用户亲自做什么
```

## 5. Codex 带练规则

Codex 只能作为带练。

允许：

```text
1. 读取任务包。
2. 读取文件。
3. 解释当前状态。
4. 给出用户手写 patch 的建议。
5. 解释每一行 Makefile / C 代码含义。
6. 提问上一个闭环 SRS。
7. 设计受控 Bug Lab。
8. 审查用户贴出的 diff。
9. 判断错误类型。
10. 审查用户写的 docs/daily 草稿。
11. 审查用户写的 bug_lab 草稿。
12. 给 git add 白名单。
13. 给 commit message 建议。
```

禁止：

```text
1. 自动修改文件。
2. 自动生成最终 docs/daily。
3. 自动生成最终 bug_lab。
4. 自动修复 bug。
5. 自动 git add。
6. 自动 git commit。
7. 自动修改 domain_snapshot。
8. 代替用户完成闭环。
```

Codex 每次输出都必须包含：

```text
1. 下一步用户亲自做什么。
2. 为什么做。
3. 做完后把什么结果贴回来。
```

## 6. 禁止代工规则

本任务包默认：

```yaml
codex_can_modify_files: false
```

Codex 不允许执行：

```text
1. 直接改 Makefile。
2. 直接改 C 文件。
3. 直接改 tests。
4. 直接写 docs/daily 成品。
5. 直接写 bug_lab 成品。
6. 直接运行破坏性命令。
7. 直接 git add / commit。
8. 直接关闭 gap。
9. 直接生成领域事实。
10. 直接注入 bug。
11. 直接修复 bug。
12. 直接创建 include/control.h。
13. 直接创建 src/control.c。
14. 直接创建 tests/test_control.c。
```

如果用户明确要求 Codex 代工，Codex 必须先提醒：

```text
这会削弱训练闭环。当前系统默认要求用户亲自执行。
```

只有用户再次确认：

```text
允许 Codex 代工
```

并且任务不涉及领域规则、open gap、Bug Lab 训练，才可进入 `engineering_execution`。

本任务包不允许切换为 engineering_execution。

## 7. 主线验收标准

今日主线完成必须同时满足：

```text
1. 用户本人完成 include/control.h。
2. 用户本人完成 src/control.c。
3. 用户本人完成 tests/test_control.c。
4. 用户本人完成 Makefile 修改。
5. 用户本人运行 make test_control。
6. 用户本人运行 make test。
7. 测试通过。
8. git diff 只包含目标文件。
9. tests/test_judge.c 无 diff。
10. include/record.h 无 diff。
11. include/judge.h 无 diff。
12. src/judge.c 无 diff。
13. domain_snapshot 无 diff。
14. exercises/06-projects/1.c 无 diff。
15. control 模块没有重复写 oxygen < 5.0。
16. control 模块没有使用 temp_status 作为控制依据。
17. control 模块没有接硬件。
18. 没有无关格式化。
19. 没有 Codex 代工。
20. docs/daily 有真实操作记录。
21. SRS 有新增候选。
22. Bug Lab 完成受控注入和修复。
23. 408 映射完成，并尽量细到王道小节。
```

主线验收命令：

```bash
make test_control
make test
git diff
git diff --check
git diff -- tests/test_judge.c
git diff -- include/record.h include/judge.h src/judge.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
```

预期：

```text
make test_control: 通过
make test: 通过
git diff --check: 通过
tests/test_judge.c: no diff
include/record.h/include/judge.h/src/judge.c: no diff
domain_snapshot: no diff
exercises/06-projects/1.c: no diff
```

## 8. Clean Baseline 要求

Bug Lab 前必须先建立干净基线。

Clean Baseline 必须满足：

```text
1. 主线测试通过。
2. git diff 可解释。
3. 无无关文件改动。
4. 无 domain_snapshot 误改。
5. 无 tests/test_judge.c 误改。
6. 无 judge / record 既有文件误改。
7. 无 build/、tmux log 等杂项进入暂存区。
```

建议命令：

```bash
make test_control
make test
git diff --check
git diff -- tests/test_judge.c
git diff -- include/record.h include/judge.h src/judge.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
git status
```

如果 Clean Baseline 不通过：

```text
禁止进入 Bug Lab。
先修主线。
```

Clean Baseline 通过后，在 docs/daily 中记录：

```yaml
clean_baseline:
  make_test_control: pass
  make_test: pass
  diff_check: pass
  tests_test_judge_diff: none
  record_judge_source_diff: none
  domain_snapshot_diff: none
  exercise_reference_diff: none
```

## 9. Bug Lab 注入要求

Bug Lab 是每次闭环结尾的主动训练模块。

Codex 负责设计受控 bug，用户负责亲自注入。

Bug 注入必须满足：

```text
1. 单点注入。
2. 可逆。
3. 可测试触发。
4. 不涉及 domain_snapshot。
5. 不涉及未关闭 gap 的领域规则。
6. 不破坏 Git 历史。
7. 不扩大主线范围。
8. 能训练 C / Makefile / Git / 408 中至少一个知识点。
9. 用户亲自注入。
10. 用户亲自修复。
```

Codex 必须给出 2-3 个候选 bug：

```text
A. control.h 与 control.c 函数声明/定义不一致
B. Makefile 少链接 src/control.c
C. tests/test_control.c 传参类型错误
```

每个候选必须包含：

```text
1. 注入位置
2. 用户手动注入方式
3. 预期错误
4. 触发命令
5. 恢复方式
6. 对应知识点
7. 风险等级
```

本次推荐 bug：

```yaml
bug_lab_recommendation:
  type: interface_contract_error
  name: control_signature_mismatch
  injection_point: src/control.c 的函数定义
  injection_action: 用户手动临时把 control_should_aerate(PondRecord record) 改成 control_should_aerate(float oxygen)
  trigger_command: make test_control
  expected_error: conflicting types for 'control_should_aerate' 或 incompatible declaration/definition
  repair_action: 用户手动恢复为 control_should_aerate(PondRecord record)
  risk_level: L4
  domain_risk: low
```

训练目标：

```text
1. 理解 .h 是接口合同。
2. 理解 .c 必须遵守头文件声明。
3. 理解 PondRecord vs float 的参数边界。
4. 理解 control 不应该绕过 judge 直接判断 oxygen。
5. 理解函数接口设计影响模块边界。
```

禁止 Bug Lab 注入：

```text
1. temp_status 阈值类 bug。
2. oxygen_status 领域阈值类 bug。
3. needs_aeration 领域建议类 bug。
4. domain_snapshot 类 bug。
5. gap_list 类 bug。
6. 多文件混合 bug。
7. 删除文件类 bug。
8. 硬件控制类 bug。
```

## 10. Bug Lab 修复要求

用户必须亲自修复 bug。

修复流程：

```text
1. 用户手动注入 bug。
2. 用户运行 make test_control。
3. 用户复制报错。
4. 用户先自己判断错误类型。
5. Codex 只做提示，不直接给最终答案。
6. 用户手动修复。
7. 用户重新运行 make test_control。
8. 用户重新运行 make test。
9. 用户写 bug_lab。
10. Codex 审查 bug_lab。
```

本次 Bug Lab 文件路径：

```text
bug_lab/2026-05-25-control-interface-contract.md
```

Bug Lab 模板：

```markdown
# 2026-05-25 Control Interface Contract Bug Lab

## 1. Bug 注入目标

训练 C 多文件模块中的函数声明与定义一致性。

## 2. 注入位置

src/control.c 的 control_should_aerate 函数定义。

## 3. 注入前 Clean Baseline

记录：
- make test_control:
- make test:
- git diff --check:
- git diff -- tests/test_judge.c:
- git diff -- include/record.h include/judge.h src/judge.c:
- git diff -- domain_snapshot:

## 4. 注入动作

临时把：

```c
int control_should_aerate(PondRecord record)
```

改成：

```c
int control_should_aerate(float oxygen)
```

## 5. 触发命令

```bash
make test_control
```

## 6. 报错信息

粘贴真实报错。

## 7. 错误类型判断

compile / link / runtime / make / git / domain_policy

本次预期：compile 或 interface contract error

## 8. 根因分析

解释为什么 control.h 中的声明必须和 control.c 中的定义一致。

## 9. 修复动作

恢复为：

```c
int control_should_aerate(PondRecord record)
```

## 10. 回归测试

```bash
make test_control
make test
git diff --check
```

## 11. 防复发规则

例如：每新增一个模块，先写 .h 接口合同，再让 .c 严格实现该合同，测试只通过公开接口调用。

## 12. SRS 卡片

Q:
A:

## 13. 408 映射

写到王道 408 小节。

## 14. 是否进入长期问题库

是 / 否
```

## 11. docs/daily 要求

docs/daily 必须由用户本人写。

Codex 可以提供模板，但不能直接生成最终成品。

路径：

```text
docs/daily/2026-05-25-control-module-v0.md
```

模板：

```markdown
# 2026-05-25 Control Module v0

## 0. 闭环身份确认

本次 control 模块创建、Makefile 修改、测试命令执行、Bug Lab 注入、Bug 修复、docs/daily 与 bug_lab 初稿均由用户本人完成。Codex 仅提供带练、审查和纠偏。

## 1. 今日主线

用户本人手动拆出最小 control 模块 v0。

## 2. 上一个闭环 SRS 回顾

记录今日开始前抽问的 3-5 张 SRS。

## 3. 领域证据与 gap 边界

- WQ-DO-GROWOUT-001 / SRC-001:
- GAP-001:
- temp_status:
- control 是否新增领域规则:
- domain_snapshot 是否修改:

## 4. 用户手动执行步骤

记录实际执行步骤。

## 5. control 模块职责边界

- judge:
- control:
- hardware:
- 今日不做:

## 6. 主线测试结果

```bash
make test_control
make test
git diff --check
```

记录真实结果。

## 7. Clean Baseline

记录以下检查：

```bash
git diff -- tests/test_judge.c
git diff -- include/record.h include/judge.h src/judge.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
```

## 8. Bug Lab 受控注入

### 8.1 注入目标

### 8.2 注入位置

### 8.3 注入动作

### 8.4 触发命令

### 8.5 报错信息

### 8.6 错误类型判断

### 8.7 修复动作

### 8.8 回归测试

## 9. 今日新增 SRS

至少 3-5 张。

## 10. 408 映射

细到王道章节 / 小节 / 知识点。

## 11. Git 提交

记录 git add 白名单和 commit message。

## 12. 明日建议
```

Codex 审查 docs/daily 时只判断：

```text
1. 是否真实记录用户操作。
2. 是否有测试命令和结果。
3. 是否有 Clean Baseline。
4. 是否有 Bug Lab。
5. 是否有 SRS。
6. 是否有 408 映射。
7. 是否没有领域越权。
8. 是否没有把 Codex 代工写成用户闭环。
9. 是否明确 judge / control / hardware 的职责边界。
```

## 12. SRS 要求

SRS 分两类：

```text
1. 开始前：上一个闭环抽卡。
2. 结束后：本次闭环新增卡。
```

### 12.1 开始前 SRS

必须从上一轮抽 3-5 张。

格式：

```yaml
- question:
  answer:
  source:
  category:
  review_date:
```

分类：

```text
C 接口
Makefile
GCC 编译
链接
模块边界
Git
Bug 归因
领域证据
408 数据结构
408 计组
408 操作系统
408 计网
```

### 12.2 结束后 SRS

必须从今日主线和 Bug Lab 抽取。

至少包括：

```text
1. 一个 control 模块职责问题。
2. 一个函数声明与定义一致性问题。
3. 一个 Makefile 多模块链接问题。
4. 一个 bug 归因问题。
5. 一个 408 映射问题。
6. 一个领域证据边界问题。
```

本次建议新增 SRS：

```text
Q1: control 模块为什么调用 needs_aeration(record)，而不是自己写 oxygen < 5.0？
A1: 因为 DO 阈值属于 judge/领域证据层，control 只负责把 judge 的判断结果转成控制建议，避免领域规则分散。

Q2: control_should_aerate 为什么接收 PondRecord，而不是 float oxygen？
A2: 因为 control 的输入应保持与 judge 的 needs_aeration(record) 一致，接收整条池塘记录，便于后续扩展更多控制依据。

Q3: control.h 和 control.c 的函数签名不一致会导致什么问题？
A3: 会破坏接口合同，通常在编译阶段出现 conflicting types 或声明/定义不一致错误。

Q4: test_control 为什么需要同时编译 src/control.c、src/judge.c 和 tests/test_control.c？
A4: test_control.c 调用 control 函数，control.c 实现 control 函数，control.c 又调用 judge.c 中的 needs_aeration，所以三者都要参与构建。

Q5: GAP-001 open 时，control 能不能使用 temp_status 作为正式控制依据？
A5: 不能。温度状态阈值缺少 growout 阶段已审核领域卡，temp_status 只能 legacy behavior + needs_human_verification。
```

后期可以整理为：

```text
srs/cards/YYYY-MM-DD.yml
```

但当前阶段只要求写进 docs/daily 和 bug_lab。

## 13. 408 映射（详细到王道408的小章节和知识点）

408 映射不能只写大类，必须尽量细到王道小节和具体知识点。

如果无法确定王道精确小节，必须标注：

```text
needs_manual_alignment_with_wangdao
```

不能编造小节号。

本次建议映射：

```yaml
408_mapping:
  data_structure:
    - chapter: "第1章 绪论"
      section: "数据结构的基本概念"
      point: "数据对象、数据元素、数据结构"
      project_mapping: "PondRecord 是池塘记录这一数据对象的结构化表示，作为 control_should_aerate 的输入。"
      task_evidence: "include/record.h + include/control.h"

    - chapter: "第1章 绪论"
      section: "抽象数据类型"
      point: "数据对象、数据关系、基本操作"
      project_mapping: "control_should_aerate(record) 是对池塘记录执行的控制决策操作，可视作控制模块的抽象操作。"
      task_evidence: "include/control.h"

    - chapter: "第1章 绪论"
      section: "算法和算法评价"
      point: "算法正确性、可读性、健壮性"
      project_mapping: "tests/test_control.c 用固定样例验证 control 模块的决策行为。"
      task_evidence: "tests/test_control.c + make test_control"

  computer_organization:
    - chapter: "第1章 计算机系统概述"
      section: "计算机系统层次结构"
      point: "高级语言程序到可执行程序的转换"
      project_mapping: "Makefile 调用 gcc，把 src/judge.c、src/control.c、tests/test_control.c 构建为 build/test_control。"
      task_evidence: "Makefile 中 test_control 目标"

    - chapter: "第1章 计算机系统概述"
      section: "needs_manual_alignment_with_wangdao"
      point: "目标文件链接与符号解析"
      project_mapping: "control.c 调用 judge.c 中的 needs_aeration；若链接缺少 judge.c，会出现符号解析问题。"
      task_evidence: "Makefile test_control 目标"

    - chapter: "第2章 数据的表示和运算"
      section: "浮点数的表示与运算"
      point: "float 数据参与比较"
      project_mapping: "control 不直接比较 float oxygen，而是调用 judge 层 needs_aeration，保持领域规则单一来源。"
      task_evidence: "src/control.c"

  operating_system:
    - chapter: "第1章 计算机系统概述"
      section: "操作系统的概念、功能和目标"
      point: "操作系统作为用户与计算机硬件之间的接口"
      project_mapping: "用户在 shell 中执行 make，make 调用 gcc 和 ld 进程完成 control 测试构建。"
      task_evidence: "make test_control"

    - chapter: "第4章 文件管理"
      section: "文件、目录与路径名"
      point: "目录结构和文件路径"
      project_mapping: "include/、src/、tests/、build/ 共同构成当前 C 工程的文件组织。"
      task_evidence: "-Iinclude、src/control.c、src/judge.c、tests/test_control.c、build/test_control"

    - chapter: "第4章 文件管理"
      section: "目录结构与文件定位"
      point: "路径查找和输出目录"
      project_mapping: "mkdir -p build 保证 build/ 目录存在，-o build/test_control 指定可执行文件输出位置。"
      task_evidence: "Makefile 中 test_control 目标"

  computer_network:
    - chapter: "不涉及"
      section: "不涉及"
      point: "今日任务无网络通信内容"
      project_mapping: "无"
      task_evidence: "无"

note:
  - "王道版本小节名称可能存在差异，后续需要结合手头教材做 needs_manual_alignment_with_wangdao。"
```

## 14. Git 要求

Git 必须由用户本人执行。

Codex 只能给白名单。

提交前必须执行：

```bash
git status
git diff
git diff --check
make test_control
make test
```

如果有暂存：

```bash
git diff --cached
```

允许 add 的文件：

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
git commit -m "feat: add guided control module loop"
```

或分两个 commit：

```bash
git commit -m "feat: add control module v0"
git commit -m "docs: record control interface bug lab"
```

若保持每日闭环单 commit，使用：

```bash
git commit -m "feat: complete guided control module loop"
```

## 15. Codex 第一轮输出要求

Codex 第一轮只允许输出计划，不允许修改文件。

必须按以下格式：

```markdown
# Codex 第一轮：003-control-module-v0 执行计划

## 1. 我已读取的文件

列出已读取文件。

## 2. 上一个闭环 SRS 抽卡

### Q1

### Q2

### Q3

### Q4

### Q5

等待用户回答，或允许用户选择跳过到主线。

## 3. 当前任务状态判断

判断 judge 模块是否稳定。
判断 Makefile 是否已有 test_control。
判断 control 模块是否已存在。
判断领域边界。

## 4. 今日主线最小目标

说明今日只做最小 control 模块拆分。

## 5. 用户需要亲自执行的步骤

给出用户需要手动执行的 nvim / make / git diff 命令。

## 6. Codex 只负责带练的内容

说明 Codex 不改文件，只解释和审查。

## 7. 明确禁止 Codex 代工的内容

明确不允许 Codex 创建 control 文件、修改 Makefile、写 docs、写 bug_lab、修改 C 文件、修改 domain_snapshot。

## 8. Clean Baseline 要求

列出用户主线完成后要执行的检查。

## 9. 今日 Bug Lab 候选

A. control.h 与 control.c 函数声明/定义不一致
B. Makefile 少链接 src/control.c
C. tests/test_control.c 传参类型错误

推荐：A

## 10. 等待用户确认

请用户回复：
A. 开始主线手动闭环
B. 只做 SRS 抽卡
C. 只做 Bug Lab 设计
```

用户确认 A 后，Codex 仍然不改文件，只给用户手动操作指导。

## 16. Codex 只能审查的内容

Codex 只能审查：

```text
1. 用户贴出的 git diff。
2. 用户贴出的 make/gcc 输出。
3. 用户写的 docs/daily 草稿。
4. 用户写的 bug_lab 草稿。
5. 用户写的 SRS 卡片。
6. 用户给出的 408 映射。
7. 用户准备 git add 的文件列表。
```

Codex 审查时必须输出：

```text
1. 是否合格。
2. 不合格点。
3. 是否违反领域证据规则。
4. 是否违反最小闭环规则。
5. 是否存在 Codex 代工痕迹。
6. Bug Lab 是否完成受控注入、触发、修复、回归。
7. SRS 是否来自真实问题。
8. 408 映射是否细到王道小节。
9. control 是否只包装 needs_aeration(record)，没有复制领域阈值。
10. 下一步用户亲自做什么。
```

Codex 不允许：

```text
1. 直接修。
2. 直接写。
3. 直接提交。
4. 替用户完成闭环。
5. 替用户注入 bug。
6. 替用户修 bug。
7. 替用户创建 control 模块。
```

最终完成标准：

```text
用户本人完成 control 模块创建。
用户本人完成 Makefile 修改。
用户本人完成主线测试。
用户本人完成 Clean Baseline。
用户本人完成 Bug Lab 注入与修复。
用户本人写 docs/daily。
用户本人写 bug_lab。
Codex 只完成审查。
Git 提交只包含白名单文件。
```
