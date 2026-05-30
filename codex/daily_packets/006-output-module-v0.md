# 006 - Output Module v0

## 0. Metadata

packet_id: 006-output-module-v0
stage: Stage 2-1
task_name: Output Module v0
task_type: module_split
execution_mode: guided_practice
human_must_execute: true
codex_can_modify_files: false
codex_can_only_review: true

guided_practice_guard:
  no_complete_copyable_code: true
  only_hints_checkpoints_pseudocode_fill_in: true
  first_two_rounds_no_file_modification: true
  no_execution_report: true
  daily_requires_human_authorization: true
  bug_lab_requires_human_authorization: true
  execution_report_invalidates_loop: true

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
  - codex/daily_packets/003-control-module-v0.md
  - codex/daily_packets/004-csv-store-module-v0.md
  - codex/daily_packets/005-csv-header-once-v0.md
  - docs/daily/2026-05-23-judge-module-v0.md
  - docs/daily/2026-05-24-test-judge-build-v0.md
  - docs/daily/2026-05-25-control-module-v0.md
  - docs/daily/2026-05-26-csv-store-module-v0.md
  - docs/daily/2026-05-27-csv-header-once-v0.md
  - bug_lab/2026-05-23-judge-parameter-contract.md
  - bug_lab/2026-05-24-judge-build-link-error.md
  - bug_lab/2026-05-25-control-interface-contract.md
  - bug_lab/2026-05-26-csv-store-file-mode.md
  - bug_lab/2026-05-27-csv-header-duplication.md
  - notes/current.md
  - Makefile
  - include/record.h
  - include/judge.h
  - include/control.h
  - include/csv_store.h
  - src/judge.c
  - src/control.c
  - src/csv_store.c
  - tests/test_judge.c
  - tests/test_control.c
  - tests/test_csv_store.c
  - exercises/06-projects/1.c

target_files_user_may_edit:
  - include/output.h
  - src/output.c
  - tests/test_output.c
  - Makefile
  - docs/daily/2026-05-29-output-module-v0.md
  - bug_lab/2026-05-29-output-buffer-truncation.md

readonly_files_for_codex:
  - include/output.h
  - src/output.c
  - tests/test_output.c
  - Makefile
  - docs/daily/2026-05-29-output-module-v0.md
  - bug_lab/2026-05-29-output-buffer-truncation.md
  - include/record.h
  - include/judge.h
  - include/control.h
  - include/csv_store.h
  - src/judge.c
  - src/control.c
  - src/csv_store.c
  - tests/test_judge.c
  - tests/test_control.c
  - tests/test_csv_store.c
  - exercises/06-projects/1.c

forbidden_files:
  - domain_snapshot/
  - domain_snapshot/cards/
  - domain_snapshot/source_index.md
  - domain_snapshot/gap_list.md
  - src/input_cli.c
  - include/input_cli.h
  - main.c
  - hardware/
  - drivers/
  - frontend/
  - database/
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
  - docs/daily/2026-05-27-csv-header-once-v0.md
  - bug_lab/2026-05-27-csv-header-duplication.md
  - current_main_task
  - current_bug_lab

git_commit_required: true

## 1. 今日主线

今日只解决一个核心目标：

```text
用户本人手动拆出 output 模块 v0，用于把 PondRecord 和控制建议格式化为一行可读文本。
```

今日不是接 `main`，不是打印完整 UI，不是做前端，不是接硬件，不是做日志文件，不是做传感器输入。

当前系统已有：

```text
record -> judge
record -> judge -> control
record -> csv_store
```

今日新增：

```text
record + control_decision -> output_format_line
```

output v0 的职责：

```text
把已有数据格式化为人能读懂的一行文本。
```

推荐接口方向：

```text
int output_format_record_line(char *buffer, size_t buffer_size, PondRecord record, int should_aerate);
```

注意：

```text
这只是接口方向，不是完整实现。
用户必须本人手写 include/output.h、src/output.c、tests/test_output.c。
Codex 不允许给完整可复制代码。
```

今日完整闭环：

```text
上一个闭环 SRS 抽卡
→ C Foundation Gate
→ 用户手动阅读 record/control/csv_store/Makefile
→ Codex 解释 output 模块边界和字符串格式化训练点
→ 用户手动创建 include/output.h
→ 用户手动创建 src/output.c
→ 用户手动创建 tests/test_output.c
→ 用户手动修改 Makefile 增加 test_output
→ 用户手动运行 make test_output
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

今日重点训练能力：

```text
1. char buffer[] 字符数组
2. size_t buffer_size
3. snprintf 的返回值语义
4. 字符串格式化
5. 字符串内容比较
6. 输出层和判断/控制/存储层分离
7. 测试固定输出格式
8. Bug Lab 缓冲区过小/截断行为
9. 王道 408 中数据表示、文件/进程弱映射
10. Git 最小提交
```

Codex 在今日主线中的角色：

```text
带练、解释、审查、提问、设计 Bug Lab，不代工。
```

## 2. 今日不做

今日明确不做：

```text
1. 不接 main。
2. 不拆 input_cli。
3. 不接硬件。
4. 不接 Milk-V Duo S。
5. 不写 GPIO。
6. 不写 relay 控制。
7. 不写真实执行器驱动。
8. 不做前端 UI。
9. 不做数据库。
10. 不生成时间戳。
11. 不写 CSV 文件。
12. 不修改 csv_store。
13. 不重构 judge。
14. 不重构 control。
15. 不重构 record。
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
27. 不修改 tests/test_control.c。
28. 不修改 tests/test_csv_store.c。
29. 不修改 include/record.h。
30. 不修改 include/judge.h。
31. 不修改 include/control.h。
32. 不修改 include/csv_store.h。
33. 不修改 src/judge.c。
34. 不修改 src/control.c。
35. 不修改 src/csv_store.c。
36. 不整理无关缩进。
37. 不格式化无关代码。
38. 不补充无关注释。
39. 不做顺手优化。
40. 不移动已有文件。
41. 不让 Codex 自动修改文件。
42. 不让 Codex 自动生成 docs/daily 成品。
43. 不让 Codex 自动生成 bug_lab 成品。
```

今日所有动作必须服务于：

```text
最小 output 格式化模块 + C Foundation Gate + 闭环末端 Bug Lab 训练
```

## 3. 证据与 gap 约束 + 上一个闭环 SRS 抽卡

### 3.1 领域证据约束

Codex 和用户必须先确认：

```text
1. 当前任务是否涉及领域判断：不新增领域判断。
2. 是否涉及养殖阈值：不涉及新增阈值。
3. 是否涉及病害判断：不涉及。
4. 是否涉及投喂比例：不涉及。
5. 是否涉及增氧建议：只显示已有 control 结果，不生成新建议。
6. 是否涉及 open gap：涉及 GAP-001，但只作为禁止约束。
```

当前允许继续存在：

```yaml
card_id: WQ-DO-GROWOUT-001
source_id: SRC-001
usage: oxygen_status() / needs_aeration()
status: allowed
```

output 模块允许做：

```text
1. 接收 PondRecord。
2. 接收一个已经计算好的 should_aerate 标志。
3. 格式化为一行文本。
```

output 模块禁止做：

```text
1. 不调用 temp_status()。
2. 不调用 oxygen_status()。
3. 不调用 needs_aeration()。
4. 不调用 control_should_aerate()。
5. 不直接写 oxygen < 5.0。
6. 不直接写任何领域阈值。
7. 不新增控制建议。
8. 不生成领域解释。
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
3. output 模块不得新增任何温度状态解释。
4. output 模块不得新增任何 DO 阈值解释。
5. output 模块不得新增控制规则。
6. 不得新增温度阈值。
7. 不得关闭 GAP-001。
8. 不得在 docs/daily 中写“温度规则已证据化”。
9. 今日不是 domain_rule_update 类型任务。
```

### 3.3 上一个闭环 SRS 抽卡要求

每日任务开始前，必须从上一个闭环抽取 SRS。

SRS 来源：

```text
1. docs/daily/2026-05-27-csv-header-once-v0.md
2. bug_lab/2026-05-27-csv-header-duplication.md
3. fseek / ftell
4. fgets / strcmp
5. CSV header once
```

Codex 第一轮必须抽取 3-5 张 SRS 候选卡，但只提问，不代替用户答。

本次至少抽问：

```text
Q1: 为什么 CSV header 不能每次 append 都写？
A1: 用户回答后 Codex 校准。

Q2: fseek(fp, 0, SEEK_END) 和 ftell(fp) 在 header once 中分别做什么？
A2: 用户回答后 Codex 校准。

Q3: 为什么检查 header 时要用 strcmp，而不是 line == "header"？
A3: 用户回答后 Codex 校准。

Q4: fgets 和 fgetc 的区别是什么？
A4: 用户回答后 Codex 校准。

Q5: filename 和 FILE *fp 的区别是什么？
A5: 用户回答后 Codex 校准。
```

SRS 卡片分类：

```text
C 文件操作
C 字符串
char buffer
snprintf
CSV 持久化
模块边界
Bug 归因
领域证据
408 映射
```

后期这些 SRS 可以整理为类似 Anki 的抽卡系统：

```text
daily_srs/cards/
daily_srs/review_log/
daily_srs/tag_index.yml
```

但今日不强制生成 Anki 文件，只要求：

```text
1. 今日开始前抽问。
2. 今日结束后新增 SRS 候选。
3. docs/daily 中记录。
```

## 4. C Foundation Gate

今天进入 output 模块前，必须先过 C Foundation Gate。

Codex 只能提问和校准，不得给完整代码实现。

### 4.1 必会概念

用户需要能解释：

```text
1. char buffer[128] 是什么。
2. buffer 和 &buffer[0] 的关系。
3. size_t buffer_size 表示什么。
4. 字符串以 '\0' 结尾是什么意思。
5. snprintf 和 printf 的区别。
6. snprintf 的返回值表示什么。
7. 为什么不能无限制 sprintf。
8. strcmp 比较的是字符串内容，不是地址。
9. buffer_size 太小时可能发生什么。
10. output 模块为什么不应该直接 printf 到终端。
```

### 4.2 今日推荐使用方向

output v0 推荐优先做：

```text
格式化到用户提供的 buffer 中。
```

而不是直接：

```text
printf 到终端。
```

原因：

```text
1. 写入 buffer 更容易测试。
2. 不需要捕获 stdout。
3. 便于后续 main 决定打印到终端、写入日志或发送到板子。
4. output 模块只负责格式化，不负责程序流程。
```

### 4.3 用户必须自己补全的逻辑空位

Codex 只能让用户自己补全：

```text
1. output.h 中需要包含哪些头文件？
2. 函数形参中为什么需要 char *buffer？
3. 为什么需要 buffer_size？
4. 输出格式中应包含哪些字段？
5. should_aerate 为 1 时输出什么词？
6. should_aerate 为 0 时输出什么词？
7. snprintf 返回值小于 0 时如何处理？
8. snprintf 返回值大于等于 buffer_size 时如何处理？
9. 测试如何验证输出字符串内容？
10. 测试如何验证小 buffer 时能检测截断？
```

### 4.4 禁止完整实现

在 `guided_practice` 下，Codex 禁止给出完整可复制的：

```text
include/output.h
src/output.c
tests/test_output.c
Makefile patch
```

Codex 只能给：

```text
1. 接口方向。
2. 检查点。
3. 伪代码。
4. 填空提示。
5. 错误类型解释。
```

如果 Codex 输出：

```text
已完成 / 已实现 / 已修改 / 我已经改好
```

本轮自动判定为无效闭环。

## 5. 用户手动执行步骤

Codex 不允许替用户执行闭环。

用户必须亲自完成：

```text
1. 打开 nvim。
2. 阅读 Makefile。
3. 阅读 include/record.h。
4. 阅读 include/control.h。
5. 阅读 include/csv_store.h。
6. 阅读 tests/test_csv_store.c 中 fgets / strcmp 的用法。
7. 手动创建 include/output.h。
8. 手动创建 src/output.c。
9. 手动创建 tests/test_output.c。
10. 手动修改 Makefile。
11. 手动运行 make test_output。
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

# 2. 阅读相关文件
sed -n '1,160p' include/record.h
sed -n '1,160p' include/control.h
sed -n '1,160p' include/csv_store.h
sed -n '1,260p' tests/test_csv_store.c
sed -n '1,260p' Makefile

# 3. 手动创建 output 接口
nvim include/output.h

# 4. 手动创建 output 实现
nvim src/output.c

# 5. 手动创建 output 测试
nvim tests/test_output.c

# 6. 手动修改 Makefile，增加 test_output
nvim Makefile

# 7. 运行测试
make test_output
make test

# 8. 检查 diff
git diff
git diff --check

# 9. Clean Baseline 检查
git diff -- tests/test_judge.c tests/test_control.c tests/test_csv_store.c
git diff -- include/record.h include/judge.h include/control.h include/csv_store.h
git diff -- src/judge.c src/control.c src/csv_store.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c

# 10. 将输出贴给 Codex 审查
```

### 5.1 建议接口方向

只给方向，不给完整实现：

```text
函数名方向：
output_format_record_line

输入方向：
- char *buffer
- size_t buffer_size
- PondRecord record
- int should_aerate

返回值方向：
- 0 表示成功
- 非 0 表示失败或输出被截断
```

### 5.2 建议测试方向

只给测试目标，不给完整测试代码：

```text
1. 用一个足够大的 char buffer。
2. 构造一个 PondRecord。
3. 传入 should_aerate = 1。
4. 检查返回值为成功。
5. 检查输出字符串包含 pond_id/temp/oxygen/aerate 信息。
6. 再测 should_aerate = 0。
7. 用一个很小的 buffer 触发截断检测。
8. 不调用 judge/control。
9. 不写 CSV。
10. 不接 main。
```

Codex 只能在用户每一步之后做：

```text
解释
检查
纠偏
提问
建议下一步用户亲自做什么
```

## 6. Codex 带练规则

Codex 只能作为带练。

允许：

```text
1. 读取任务包。
2. 读取文件。
3. 解释当前状态。
4. 给出用户手写 patch 的思路。
5. 给出伪代码和填空提示。
6. 解释 char buffer / size_t / snprintf / strcmp。
7. 提问上一个闭环 SRS。
8. 设计受控 Bug Lab。
9. 审查用户贴出的 diff。
10. 判断错误类型。
11. 审查用户写的 docs/daily 草稿。
12. 审查用户写的 bug_lab 草稿。
13. 给 git add 白名单。
14. 给 commit message 建议。
```

禁止：

```text
1. 自动修改文件。
2. 给完整可复制实现。
3. 自动生成最终 docs/daily。
4. 自动生成最终 bug_lab。
5. 自动修复 bug。
6. 自动 git add。
7. 自动 git commit。
8. 自动修改 domain_snapshot。
9. 代替用户完成闭环。
```

Codex 每次输出都必须包含：

```text
1. 下一步用户亲自做什么。
2. 为什么做。
3. 做完后把什么结果贴回来。
```

## 7. 禁止代工规则

本任务包默认：

```yaml
codex_can_modify_files: false
codex_can_output_full_solution: false
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
12. 直接创建 include/output.h。
13. 直接创建 src/output.c。
14. 直接创建 tests/test_output.c。
15. 给出完整 output.h / output.c / test_output.c。
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

## 8. 主线验收标准

今日主线完成必须同时满足：

```text
1. 用户本人完成 include/output.h。
2. 用户本人完成 src/output.c。
3. 用户本人完成 tests/test_output.c。
4. 用户本人完成 Makefile 修改。
5. 用户本人运行 make test_output。
6. 用户本人运行 make test。
7. 测试通过。
8. output 不调用 judge。
9. output 不调用 control。
10. output 不调用 csv_store。
11. output 不写领域阈值。
12. output 不处理 temp_status。
13. output 不直接 printf 到终端，除非用户明确在 docs 中说明为何暂时这么做。
14. output 能格式化 PondRecord 和 should_aerate。
15. output 能检测小 buffer 截断或返回失败。
16. git diff 只包含目标文件。
17. tests/test_judge.c 无 diff。
18. tests/test_control.c 无 diff。
19. tests/test_csv_store.c 无 diff。
20. include/record.h 无 diff。
21. include/judge.h 无 diff。
22. include/control.h 无 diff。
23. include/csv_store.h 无 diff。
24. src/judge.c 无 diff。
25. src/control.c 无 diff。
26. src/csv_store.c 无 diff。
27. domain_snapshot 无 diff。
28. exercises/06-projects/1.c 无 diff。
29. 没有无关格式化。
30. 没有 Codex 代工。
31. docs/daily 有真实操作记录。
32. SRS 有新增候选。
33. Bug Lab 完成受控注入和修复。
34. 408 映射完成，并尽量细到王道小节。
```

主线验收命令：

```bash
make test_output
make test
git diff
git diff --check
git diff -- tests/test_judge.c tests/test_control.c tests/test_csv_store.c
git diff -- include/record.h include/judge.h include/control.h include/csv_store.h
git diff -- src/judge.c src/control.c src/csv_store.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
```

预期：

```text
make test_output: 通过
make test: 通过
git diff --check: 通过
tests/test_judge.c/tests_test_control.c/tests_test_csv_store.c: no diff
include/record.h/include/judge.h/include/control.h/include/csv_store.h: no diff
src/judge.c/src/control.c/src/csv_store.c: no diff
domain_snapshot: no diff
exercises/06-projects/1.c: no diff
```

## 9. Clean Baseline 要求

Bug Lab 前必须先建立干净基线。

Clean Baseline 必须满足：

```text
1. 主线测试通过。
2. git diff 可解释。
3. 无无关文件改动。
4. 无 domain_snapshot 误改。
5. 无 tests/test_judge.c 误改。
6. 无 tests/test_control.c 误改。
7. 无 tests/test_csv_store.c 误改。
8. 无 record / judge / control / csv_store 既有文件误改。
9. 无 build/、tmux log 等杂项进入暂存区。
```

建议命令：

```bash
make test_output
make test
git diff --check
git diff -- tests/test_judge.c tests/test_control.c tests/test_csv_store.c
git diff -- include/record.h include/judge.h include/control.h include/csv_store.h
git diff -- src/judge.c src/control.c src/csv_store.c
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
  make_test_output: pass
  make_test: pass
  diff_check: pass
  existing_tests_diff: none
  existing_headers_diff: none
  existing_sources_diff: none
  domain_snapshot_diff: none
  exercise_reference_diff: none
```

## 10. Bug Lab 注入要求

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
8. 能训练 C 字符串 / buffer / Makefile / 408 中至少一个知识点。
9. 用户亲自注入。
10. 用户亲自修复。
```

Codex 必须给出 2-3 个候选 bug：

```text
A. 小 buffer 截断未检测 bug
B. strcmp 比较方向错误 bug
C. snprintf 返回值判断错误 bug
```

每个候选必须包含：

```text
1. 注入位置
2. 用户手动注入方式
3. 预期错误或异常行为
4. 触发命令
5. 恢复方式
6. 对应知识点
7. 风险等级
```

本次推荐 bug：

```yaml
bug_lab_recommendation:
  type: buffer_truncation_error
  name: output_small_buffer_not_detected
  injection_point: src/output.c 的 snprintf 返回值判断
  injection_action: 用户手动临时去掉“小 buffer 截断”判断，使小 buffer 测试误判成功
  trigger_command: make test_output
  expected_error: small buffer 测试失败，或者测试发现输出被截断但函数未返回错误
  repair_action: 用户手动恢复 snprintf 返回值与 buffer_size 的比较
  risk_level: L3
  domain_risk: none
```

训练目标：

```text
1. 理解 snprintf 会限制写入长度。
2. 理解 snprintf 返回值不是实际写入长度，而是本来需要写入的长度。
3. 理解返回值 >= buffer_size 表示输出被截断。
4. 理解小 buffer 测试如何捕获截断。
5. 理解输出层需要保护调用方传入的 buffer。
```

禁止 Bug Lab 注入：

```text
1. temp_status 阈值类 bug。
2. oxygen_status 领域阈值类 bug。
3. needs_aeration 领域建议类 bug。
4. control_should_aerate 领域决策类 bug。
5. domain_snapshot 类 bug。
6. gap_list 类 bug。
7. 多文件混合 bug。
8. 删除文件类 bug。
9. 硬件控制类 bug。
10. 数据库类 bug。
```

## 11. Bug Lab 修复要求

用户必须亲自修复 bug。

修复流程：

```text
1. 用户手动注入 bug。
2. 用户运行 make test_output。
3. 用户复制报错或异常输出。
4. 用户先自己判断错误类型。
5. Codex 只做提示，不直接给最终答案。
6. 用户手动修复。
7. 用户重新运行 make test_output。
8. 用户重新运行 make test。
9. 用户写 bug_lab。
10. Codex 审查 bug_lab。
```

本次 Bug Lab 文件路径：

```text
bug_lab/2026-05-29-output-buffer-truncation.md
```

Bug Lab 模板：

```markdown
# 2026-05-29 Output Buffer Truncation Bug Lab

## 1. Bug 注入目标

训练 C 字符串缓冲区和 snprintf 截断检测能力。

## 2. 注入位置

src/output.c 的 snprintf 返回值判断。

## 3. 注入前 Clean Baseline

记录：
- make test_output:
- make test:
- git diff --check:
- git diff -- tests/test_judge.c tests/test_control.c tests/test_csv_store.c:
- git diff -- include/record.h include/judge.h include/control.h include/csv_store.h:
- git diff -- src/judge.c src/control.c src/csv_store.c:
- git diff -- domain_snapshot:

## 4. 注入动作

临时去掉或改错“小 buffer 截断检测”逻辑。

## 5. 触发命令

```bash
make test_output
```

## 6. 报错信息或异常行为

粘贴真实报错或测试失败输出。

## 7. 错误类型判断

compile / link / runtime / string_buffer / behavior_regression / git / domain_policy

本次预期：string_buffer / behavior_regression

## 8. 根因分析

解释为什么 snprintf 返回值需要和 buffer_size 比较。

## 9. 修复动作

恢复正确的截断判断。

## 10. 回归测试

```bash
make test_output
make test
git diff --check
```

## 11. 防复发规则

例如：凡是写入调用方 buffer 的函数，都必须检查 NULL、buffer_size 和 snprintf 返回值。

## 12. SRS 卡片

Q:
A:

## 13. 408 映射

写到王道 408 小节。

## 14. 是否进入长期问题库

是 / 否
```

## 12. docs/daily 要求

docs/daily 必须由用户本人写。

Codex 可以在用户明确授权后整理，但不能未经授权生成最终成品。

路径：

```text
docs/daily/2026-05-29-output-module-v0.md
```

模板：

```markdown
# 2026-05-29 Output Module v0

## 0. 闭环身份确认

本次 output 模块创建、测试命令执行、Bug Lab 注入、Bug 修复、docs/daily 与 bug_lab 初稿均由用户本人完成。Codex 仅提供带练、审查和纠偏。若 Codex 参与整理文档，须在此处明确写明“用户已授权 Codex 整理”。

## 1. 今日主线

用户本人手动拆出最小 output 模块 v0。

## 2. C Foundation Gate

记录今日补齐的：
- char buffer[]
- size_t
- '\0'
- snprintf
- strcmp
- small buffer truncation
- output 为什么不直接 printf

## 3. 上一个闭环 SRS 回顾

记录今日开始前抽问的 3-5 张 SRS。

## 4. 领域证据与 gap 边界

- WQ-DO-GROWOUT-001 / SRC-001:
- GAP-001:
- temp_status:
- output 是否新增领域规则:
- output 是否调用 judge/control/csv_store:
- domain_snapshot 是否修改:

## 5. 用户手动执行步骤

记录实际执行步骤。

## 6. output 模块职责边界

- record:
- judge:
- control:
- csv_store:
- output:
- main:
- 今日不做:

## 7. 主线测试结果

```bash
make test_output
make test
git diff --check
```

记录真实结果。

## 8. Clean Baseline

记录以下检查：

```bash
git diff -- tests/test_judge.c tests/test_control.c tests/test_csv_store.c
git diff -- include/record.h include/judge.h include/control.h include/csv_store.h
git diff -- src/judge.c src/control.c src/csv_store.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
```

## 9. Bug Lab 受控注入

### 9.1 注入目标

### 9.2 注入位置

### 9.3 注入动作

### 9.4 触发命令

### 9.5 报错信息或异常行为

### 9.6 错误类型判断

### 9.7 修复动作

### 9.8 回归测试

## 10. 今日新增 SRS

至少 3-5 张。

## 11. 408 映射

细到王道章节 / 小节 / 知识点。

## 12. Git 提交

记录 git add 白名单和 commit message。

## 13. 明日建议
```

Codex 审查 docs/daily 时只判断：

```text
1. 是否真实记录用户操作。
2. 是否有 C Foundation Gate。
3. 是否有测试命令和结果。
4. 是否有 Clean Baseline。
5. 是否有 Bug Lab。
6. 是否有 SRS。
7. 是否有 408 映射。
8. 是否没有领域越权。
9. 是否没有把 Codex 代工写成用户闭环。
10. 是否明确 record / judge / control / csv_store / output / main 的职责边界。
```

## 13. SRS 要求

SRS 分两类：

```text
1. 开始前：上一个闭环抽卡。
2. 结束后：本次闭环新增卡。
```

### 13.1 开始前 SRS

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
C 字符串
char buffer
snprintf
FILE*
CSV 持久化
模块边界
Git
Bug 归因
领域证据
408 数据结构
408 计组
408 操作系统
408 计网
```

### 13.2 结束后 SRS

必须从今日主线和 Bug Lab 抽取。

至少包括：

```text
1. 一个 char buffer 问题。
2. 一个 snprintf 返回值问题。
3. 一个 small buffer 截断问题。
4. 一个 output 模块职责问题。
5. 一个 bug 归因问题。
6. 一个 408 映射问题。
7. 一个领域证据边界问题。
```

本次建议新增 SRS：

```text
Q1: 为什么 output_format_record_line 不应该直接 printf 到终端？
A1: 因为写入 buffer 更容易测试，也让 main 决定最终输出到终端、日志或其他目标。

Q2: snprintf 的返回值表示什么？
A2: 它返回本来需要写入的字符数；如果返回值大于等于 buffer_size，说明输出被截断。

Q3: 为什么写入 char buffer 的函数必须接收 buffer_size？
A3: 因为函数需要知道可写空间上限，避免越界写入。

Q4: 为什么不能用 == 比较两个字符串内容？
A4: == 比较的是地址；字符串内容比较应使用 strcmp。

Q5: output 为什么不应该调用 judge/control？
A5: output 是展示/格式化层，只展示已经传入的数据和决策结果，不拥有判断或控制逻辑。
```

后期可以整理为：

```text
srs/cards/YYYY-MM-DD.yml
```

但当前阶段只要求写进 docs/daily 和 bug_lab。

## 14. 408 映射（详细到王道408的小章节和知识点）

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
      project_mapping: "PondRecord 是内存中的结构化数据对象，output 将其字段格式化为展示字符串。"
      task_evidence: "include/record.h + src/output.c"

    - chapter: "第1章 绪论"
      section: "抽象数据类型"
      point: "基本操作"
      project_mapping: "output_format_record_line(buffer, size, record, decision) 可视作对 PondRecord 的展示操作。"
      task_evidence: "include/output.h"

  computer_organization:
    - chapter: "第1章 计算机系统概述"
      section: "计算机系统层次结构"
      point: "高级语言程序到可执行程序的转换"
      project_mapping: "Makefile 调用 gcc，把 src/output.c 和 tests/test_output.c 构建成 build/test_output。"
      task_evidence: "Makefile 中 test_output 目标"

    - chapter: "第2章 数据的表示和运算"
      section: "字符与字符串表示"
      point: "字符数组、字符串结束符、文本表示"
      project_mapping: "char buffer 保存格式化后的输出行，字符串以 \\0 结尾。"
      task_evidence: "src/output.c / tests/test_output.c"
      note: "王道版本小节需手动校准。"

    - chapter: "needs_manual_alignment_with_wangdao"
      section: "缓冲区与数据表示"
      point: "固定大小缓冲区与截断"
      project_mapping: "small buffer 测试用于训练 snprintf 截断检测。"
      task_evidence: "bug_lab/2026-05-29-output-buffer-truncation.md"

  operating_system:
    - chapter: "第1章 计算机系统概述"
      section: "操作系统的概念、功能和目标"
      point: "程序执行"
      project_mapping: "用户执行 make test_output，make 启动 gcc 和测试程序进程。"
      task_evidence: "make test_output"

    - chapter: "第4章 文件管理"
      section: "不强相关"
      point: "本任务不直接写文件"
      project_mapping: "output v0 只格式化到内存 buffer，不直接进行文件 I/O。"
      task_evidence: "src/output.c"

  computer_network:
    - chapter: "不涉及"
      section: "不涉及"
      point: "今日任务无网络通信内容"
      project_mapping: "无"
      task_evidence: "无"

note:
  - "王道版本小节名称可能存在差异，后续需要结合手头教材做 needs_manual_alignment_with_wangdao。"
```

## 15. Git 要求

Git 必须由用户本人执行。

Codex 只能给白名单。

提交前必须执行：

```bash
git status
git diff
git diff --check
make test_output
make test
```

如果有暂存：

```bash
git diff --cached
```

允许 add 的文件：

```bash
git add codex/daily_packets/006-output-module-v0.md
git add include/output.h
git add src/output.c
git add tests/test_output.c
git add Makefile
git add docs/daily/2026-05-29-output-module-v0.md
git add bug_lab/2026-05-29-output-buffer-truncation.md
```

禁止 add：

```text
build/
tmux-*.log
domain_snapshot/
exercises/06-projects/1.c
tests/test_judge.c
tests/test_control.c
tests/test_csv_store.c
include/record.h
include/judge.h
include/control.h
include/csv_store.h
src/judge.c
src/control.c
src/csv_store.c
input_cli / main / hardware / database / frontend 相关文件
```

推荐 commit message：

```bash
git commit -m "feat: complete guided output module loop"
```

或分两个 commit：

```bash
git commit -m "feat: add output module v0"
git commit -m "docs: record output buffer truncation bug lab"
```

若保持每日闭环单 commit，使用：

```bash
git commit -m "feat: complete guided output module loop"
```

## 16. Codex 第一轮输出要求

Codex 第一轮只允许输出计划，不允许修改文件。

Codex 第一轮和第二轮都不得修改文件。

Codex 第一轮不得给完整实现代码。

必须按以下格式：

```markdown
# Codex 第一轮：006-output-module-v0 执行计划

## 1. 我已读取的文件

列出已读取文件。

## 2. 上一个闭环 SRS 抽卡

### Q1

### Q2

### Q3

### Q4

### Q5

等待用户回答，或允许用户选择跳过到主线。

## 3. C Foundation Gate

提问：
- char buffer[] 是什么？
- size_t 是什么？
- snprintf 和 printf 的区别是什么？
- snprintf 返回值表示什么？
- 为什么需要 small buffer 测试？

## 4. 当前任务状态判断

判断 output 模块是否已存在。
判断 Makefile 是否已有 test_output。
判断当前是否具备 record / control / csv_store 稳定基础。
判断领域边界。

## 5. 今日主线最小目标

说明今日只做 output 格式化模块。

## 6. 用户需要亲自执行的步骤

给出用户需要手动执行的 nvim / make / git diff 命令。

## 7. Codex 只负责带练的内容

说明 Codex 不改文件，只解释和审查。
说明 Codex 不给完整可复制代码，只给思路、检查点、伪代码和填空提示。

## 8. 明确禁止 Codex 代工的内容

明确不允许 Codex 创建 output 文件、修改 Makefile、写 docs、写 bug_lab、修改 C 文件、修改 domain_snapshot。

## 9. Clean Baseline 要求

列出用户主线完成后要执行的检查。

## 10. 今日 Bug Lab 候选

A. 小 buffer 截断未检测 bug
B. strcmp 比较方向错误 bug
C. snprintf 返回值判断错误 bug

推荐：A

## 11. 等待用户确认

请用户回复：
A. 开始主线手动闭环
B. 只做 SRS + C Foundation Gate
C. 只做 Bug Lab 设计
```

用户确认 A 后，Codex 仍然不改文件，只给用户手动操作指导。

## 17. Codex 只能审查的内容

Codex 只能审查：

```text
1. 用户贴出的 git diff。
2. 用户贴出的 make/gcc 输出。
3. 用户写的 docs/daily 草稿。
4. 用户写的 bug_lab 草稿。
5. 用户写的 SRS 卡片。
6. 用户给出的 408 映射。
7. 用户准备 git add 的文件列表。
8. 用户对 C Foundation Gate 的回答。
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
9. output 是否只做格式化，没有调用 judge/control/csv_store。
10. C Foundation 是否过关。
11. 下一步用户亲自做什么。
```

Codex 不允许：

```text
1. 直接修。
2. 直接写。
3. 直接提交。
4. 替用户完成闭环。
5. 替用户注入 bug。
6. 替用户修 bug。
7. 替用户修改 output 模块。
8. 给完整可复制实现。
```

最终完成标准：

```text
用户本人完成 output 模块创建。
用户本人完成 C Foundation Gate。
用户本人完成 Makefile 修改。
用户本人完成主线测试。
用户本人完成 Clean Baseline。
用户本人完成 Bug Lab 注入与修复。
用户本人写 docs/daily。
用户本人写 bug_lab。
Codex 只完成审查。
Git 提交只包含白名单文件。
```
