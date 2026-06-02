# 007 - Input CLI Module v0

## 0. Metadata

packet_id: 007-input-cli-module-v0
stage: Stage 2-1
task_name: Input CLI Module v0
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
  - codex/daily_packets/006-output-module-v0.md
  - docs/daily/2026-05-29-output-module-v0.md
  - bug_lab/2026-05-29-output-buffer-truncation.md
  - notes/current.md
  - Makefile
  - include/record.h
  - include/judge.h
  - include/control.h
  - include/csv_store.h
  - include/output.h
  - src/judge.c
  - src/control.c
  - src/csv_store.c
  - src/output.c
  - tests/test_judge.c
  - tests/test_control.c
  - tests/test_csv_store.c
  - tests/test_output.c
  - exercises/06-projects/1.c

target_files_user_may_edit:
  - include/input_cli.h
  - src/input_cli.c
  - tests/test_input_cli.c
  - Makefile
  - docs/daily/2026-05-30-input-cli-module-v0.md
  - bug_lab/2026-05-30-input-cli-parse-contract.md

readonly_files_for_codex:
  - include/input_cli.h
  - src/input_cli.c
  - tests/test_input_cli.c
  - Makefile
  - docs/daily/2026-05-30-input-cli-module-v0.md
  - bug_lab/2026-05-30-input-cli-parse-contract.md
  - include/record.h
  - include/judge.h
  - include/control.h
  - include/csv_store.h
  - include/output.h
  - src/judge.c
  - src/control.c
  - src/csv_store.c
  - src/output.c
  - tests/test_judge.c
  - tests/test_control.c
  - tests/test_csv_store.c
  - tests/test_output.c
  - exercises/06-projects/1.c

forbidden_files:
  - domain_snapshot/
  - domain_snapshot/cards/
  - domain_snapshot/source_index.md
  - domain_snapshot/gap_list.md
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
  - docs/daily/2026-05-29-output-module-v0.md
  - bug_lab/2026-05-29-output-buffer-truncation.md
  - current_main_task
  - current_bug_lab

git_commit_required: true

## 1. 今日主线

今日只解决一个核心目标：

```text
用户本人手动拆出 input_cli 模块 v0，把一行文本解析为 PondRecord。
```

今日不是接 `main`，不是交互式完整菜单，不是读取真实传感器，不是硬件输入，不是网络输入。

当前系统已有：

```text
record -> judge
record -> judge -> control
record -> csv_store
record + decision -> output
```

今日新增：

```text
text line -> input_cli -> PondRecord
```

input_cli v0 的职责：

```text
把一行固定格式的文本解析成 PondRecord。
```

推荐接口方向：

```text
int input_parse_record_line(const char *line, PondRecord *out_record);
```

推荐输入格式方向：

```text
pond_id,temp,oxygen
```

示例输入方向：

```text
A,23.60,4.50
```

注意：

```text
这是接口方向，不是完整实现。
用户必须本人手写 include/input_cli.h、src/input_cli.c、tests/test_input_cli.c。
Codex 不允许给完整可复制代码。
```

今日完整闭环：

```text
上一个闭环 SRS 抽卡
→ C Foundation Gate
→ 用户手动阅读 record/output/csv_store/Makefile
→ Codex 解释 input_cli 模块边界和解析训练点
→ 用户手动创建 include/input_cli.h
→ 用户手动创建 src/input_cli.c
→ 用户手动创建 tests/test_input_cli.c
→ 用户手动修改 Makefile 增加 test_input_cli
→ 用户手动运行 make test_input_cli
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
1. const char *line
2. PondRecord *out_record
3. 指针输出参数
4. sscanf 的返回值
5. 输入字段数量校验
6. char 数组字段解析
7. invalid input 返回错误
8. input 层和判断/控制/存储/输出层分离
9. Bug Lab 解析字段数量错误注入
10. 王道 408 数据表示与输入解析映射
11. Git 最小提交
```

Codex 在今日主线中的角色：

```text
带练、解释、审查、提问、设计 Bug Lab，不代工。
```

## 2. 今日不做

今日明确不做：

```text
1. 不接 main。
2. 不做交互式菜单。
3. 不循环读取 stdin。
4. 不接真实传感器。
5. 不接 Milk-V Duo S。
6. 不接硬件。
7. 不写 GPIO。
8. 不写 relay 控制。
9. 不写串口。
10. 不写网络输入。
11. 不写数据库。
12. 不写前端。
13. 不生成时间戳。
14. 不修改 csv_store。
15. 不修改 output。
16. 不修改 judge。
17. 不修改 control。
18. 不修改 record。
19. 不做新的领域资料检索。
20. 不做证据审核。
21. 不修改 domain_snapshot。
22. 不修改 domain_snapshot/cards/。
23. 不修改 domain_snapshot/source_index.md。
24. 不修改 domain_snapshot/gap_list.md。
25. 不新增日本沼虾温度阈值。
26. 不把 temp_status() 的 legacy 行为解释成领域规则。
27. 不关闭 GAP-001。
28. 不破坏 exercises/06-projects/1.c 单文件参考版。
29. 不修改 tests/test_judge.c。
30. 不修改 tests/test_control.c。
31. 不修改 tests/test_csv_store.c。
32. 不修改 tests/test_output.c。
33. 不修改 include/record.h。
34. 不修改 include/judge.h。
35. 不修改 include/control.h。
36. 不修改 include/csv_store.h。
37. 不修改 include/output.h。
38. 不修改 src/judge.c。
39. 不修改 src/control.c。
40. 不修改 src/csv_store.c。
41. 不修改 src/output.c。
42. 不整理无关缩进。
43. 不格式化无关代码。
44. 不补充无关注释。
45. 不做顺手优化。
46. 不移动已有文件。
47. 不让 Codex 自动修改文件。
48. 不让 Codex 自动生成 docs/daily 成品。
49. 不让 Codex 自动生成 bug_lab 成品。
```

今日所有动作必须服务于：

```text
最小 input_cli 文本解析模块 + C Foundation Gate + 闭环末端 Bug Lab 训练
```

## 3. 证据与 gap 约束 + 上一个闭环 SRS 抽卡

### 3.1 领域证据约束

Codex 和用户必须先确认：

```text
1. 当前任务是否涉及领域判断：不新增领域判断。
2. 是否涉及养殖阈值：不涉及新增阈值。
3. 是否涉及病害判断：不涉及。
4. 是否涉及投喂比例：不涉及。
5. 是否涉及增氧建议：不涉及新的增氧建议。
6. 是否涉及 open gap：涉及 GAP-001，但只作为禁止约束。
```

input_cli 模块允许做：

```text
1. 把文本字段解析为 PondRecord。
2. 检查字段数量是否足够。
3. 对明显解析失败的输入返回错误。
```

input_cli 模块禁止做：

```text
1. 不调用 temp_status()。
2. 不调用 oxygen_status()。
3. 不调用 needs_aeration()。
4. 不调用 control_should_aerate()。
5. 不调用 csv_store_append_record()。
6. 不调用 output_format_record_line()。
7. 不直接写 oxygen < 5.0。
8. 不直接写任何领域阈值。
9. 不判断病害。
10. 不生成控制建议。
11. 不生成领域解释。
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
3. input_cli 不得新增任何温度状态解释。
4. input_cli 不得新增任何 DO 阈值解释。
5. input_cli 不得新增控制规则。
6. 不得新增温度阈值。
7. 不得关闭 GAP-001。
8. 不得在 docs/daily 中写“温度规则已证据化”。
9. 今日不是 domain_rule_update 类型任务。
```

### 3.3 上一个闭环 SRS 抽卡要求

每日任务开始前，必须从上一个闭环抽取 SRS。

SRS 来源：

```text
1. docs/daily/2026-05-29-output-module-v0.md
2. bug_lab/2026-05-29-output-buffer-truncation.md
3. char buffer
4. snprintf 返回值
5. small buffer 截断检测
```

Codex 第一轮必须抽取 3-5 张 SRS 候选卡，但只提问，不代替用户答。

本次至少抽问：

```text
Q1: snprintf 为什么仍然需要检查返回值？
A1: 用户回答后 Codex 校准。

Q2: written >= buffer_size 表示什么？
A2: 用户回答后 Codex 校准。

Q3: 为什么 output_format_record_line 不应该直接 printf 到终端？
A3: 用户回答后 Codex 校准。

Q4: char buffer[] 和 char *buffer 有什么关系？
A4: 用户回答后 Codex 校准。

Q5: input_cli 为什么不应该调用 judge/control/csv_store/output？
A5: 用户回答后 Codex 校准。
```

SRS 卡片分类：

```text
C 字符串
char buffer
指针输出参数
sscanf
输入解析
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

今天进入 input_cli 模块前，必须先过 C Foundation Gate。

Codex 只能提问和校准，不得给完整代码实现。

### 4.1 必会概念

用户需要能解释：

```text
1. const char *line 是什么。
2. PondRecord *out_record 是什么。
3. 为什么用指针输出参数。
4. out_record == NULL 时为什么要返回错误。
5. sscanf 的返回值表示什么。
6. 为什么不能只看 sscanf 是否运行，而要看成功解析字段数量。
7. char pond_id buffer 如何避免溢出。
8. 文本中的 23.60 如何进入 float 字段。
9. 解析失败和领域非法不是同一层问题。
10. input_cli 为什么不直接判断 low oxygen。
```

### 4.2 今日推荐解析方向

input_cli v0 推荐做：

```text
解析一行固定格式文本到 PondRecord。
```

推荐格式方向：

```text
pond_id,temp,oxygen
```

示例：

```text
A,23.60,4.50
```

注意：

```text
字段格式必须以当前 include/record.h 为准。
如果 pond_id 在 record.h 中不是字符串，而是 int，则用户必须根据真实字段调整解析格式。
不得编造 record.h 中不存在的字段。
```

### 4.3 用户必须自己补全的逻辑空位

Codex 只能让用户自己补全：

```text
1. input_cli.h 中需要包含哪些头文件？
2. 函数为什么接收 const char *line？
3. 函数为什么接收 PondRecord *out_record？
4. 如何检查 line 是否为 NULL？
5. 如何检查 out_record 是否为 NULL？
6. 如何调用 sscanf 或等价解析函数？
7. 如何判断解析字段数量是否正确？
8. 解析成功后如何写入 out_record 的字段？
9. 测试如何验证有效输入？
10. 测试如何验证无效输入？
```

### 4.4 禁止完整实现

在 `guided_practice` 下，Codex 禁止给出完整可复制的：

```text
include/input_cli.h
src/input_cli.c
tests/test_input_cli.c
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
4. 阅读 include/output.h。
5. 阅读 tests/test_output.c。
6. 手动创建 include/input_cli.h。
7. 手动创建 src/input_cli.c。
8. 手动创建 tests/test_input_cli.c。
9. 手动修改 Makefile。
10. 手动运行 make test_input_cli。
11. 手动运行 make test。
12. 手动运行 git diff --check。
13. 手动运行 Clean Baseline 检查。
14. 手动注入 Bug Lab bug。
15. 手动触发 bug。
16. 手动修复 bug。
17. 手动写 bug_lab。
18. 手动写 docs/daily。
19. 手动 git add / commit。
```

今日主线建议手动步骤：

```bash
# 1. 查看当前状态
git status

# 2. 阅读相关文件
sed -n '1,160p' include/record.h
sed -n '1,160p' include/output.h
sed -n '1,220p' tests/test_output.c
sed -n '1,320p' Makefile

# 3. 手动创建 input_cli 接口
nvim include/input_cli.h

# 4. 手动创建 input_cli 实现
nvim src/input_cli.c

# 5. 手动创建 input_cli 测试
nvim tests/test_input_cli.c

# 6. 手动修改 Makefile，增加 test_input_cli
nvim Makefile

# 7. 运行测试
make test_input_cli
make test

# 8. 检查 diff
git diff
git diff --check

# 9. Clean Baseline 检查
git diff -- tests/test_judge.c tests/test_control.c tests/test_csv_store.c tests/test_output.c
git diff -- include/record.h include/judge.h include/control.h include/csv_store.h include/output.h
git diff -- src/judge.c src/control.c src/csv_store.c src/output.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c

# 10. 将输出贴给 Codex 审查
```

### 5.1 建议接口方向

只给方向，不给完整实现：

```text
函数名方向：
input_parse_record_line

输入方向：
- const char *line
- PondRecord *out_record

返回值方向：
- 0 表示解析成功
- 非 0 表示解析失败
```

### 5.2 建议测试方向

只给测试目标，不给完整测试代码：

```text
1. 有效输入：能解析 pond_id、temp、oxygen。
2. 无效输入：字段数量不足时返回失败。
3. NULL line：返回失败。
4. NULL out_record：返回失败。
5. 解析后字段值与预期一致。
6. 不调用 judge/control/csv_store/output。
7. 不接 main。
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
6. 解释 const char * / PondRecord * / sscanf / 返回值。
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
12. 直接创建 include/input_cli.h。
13. 直接创建 src/input_cli.c。
14. 直接创建 tests/test_input_cli.c。
15. 给出完整 input_cli.h / input_cli.c / test_input_cli.c。
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
1. 用户本人完成 include/input_cli.h。
2. 用户本人完成 src/input_cli.c。
3. 用户本人完成 tests/test_input_cli.c。
4. 用户本人完成 Makefile 修改。
5. 用户本人运行 make test_input_cli。
6. 用户本人运行 make test。
7. 测试通过。
8. input_cli 不调用 judge。
9. input_cli 不调用 control。
10. input_cli 不调用 csv_store。
11. input_cli 不调用 output。
12. input_cli 不写领域阈值。
13. input_cli 不处理 temp_status。
14. input_cli 能解析有效文本行为。
15. input_cli 能拒绝字段数量不足的输入。
16. input_cli 能处理 NULL line。
17. input_cli 能处理 NULL out_record。
18. git diff 只包含目标文件。
19. tests/test_judge.c 无 diff。
20. tests/test_control.c 无 diff。
21. tests/test_csv_store.c 无 diff。
22. tests/test_output.c 无 diff。
23. include/record.h 无 diff。
24. include/judge.h 无 diff。
25. include/control.h 无 diff。
26. include/csv_store.h 无 diff。
27. include/output.h 无 diff。
28. src/judge.c 无 diff。
29. src/control.c 无 diff。
30. src/csv_store.c 无 diff。
31. src/output.c 无 diff。
32. domain_snapshot 无 diff。
33. exercises/06-projects/1.c 无 diff。
34. 没有无关格式化。
35. 没有 Codex 代工。
36. docs/daily 有真实操作记录。
37. SRS 有新增候选。
38. Bug Lab 完成受控注入和修复。
39. 408 映射完成，并尽量细到王道小节。
```

主线验收命令：

```bash
make test_input_cli
make test
git diff
git diff --check
git diff -- tests/test_judge.c tests/test_control.c tests/test_csv_store.c tests/test_output.c
git diff -- include/record.h include/judge.h include/control.h include/csv_store.h include/output.h
git diff -- src/judge.c src/control.c src/csv_store.c src/output.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
```

预期：

```text
make test_input_cli: 通过
make test: 通过
git diff --check: 通过
existing tests: no diff
existing headers: no diff
existing sources: no diff
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
8. 无 tests/test_output.c 误改。
9. 无 record / judge / control / csv_store / output 既有文件误改。
10. 无 build/、tmux log 等杂项进入暂存区。
```

建议命令：

```bash
make test_input_cli
make test
git diff --check
git diff -- tests/test_judge.c tests/test_control.c tests/test_csv_store.c tests/test_output.c
git diff -- include/record.h include/judge.h include/control.h include/csv_store.h include/output.h
git diff -- src/judge.c src/control.c src/csv_store.c src/output.c
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
  make_test_input_cli: pass
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
8. 能训练 C 输入解析 / sscanf / 指针输出参数 / 408 中至少一个知识点。
9. 用户亲自注入。
10. 用户亲自修复。
```

Codex 必须给出 2-3 个候选 bug：

```text
A. sscanf 返回值未检查 bug
B. NULL out_record 未检查 bug
C. 字段顺序解析错误 bug
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
  type: parse_contract_error
  name: input_sscanf_field_count_not_checked
  injection_point: src/input_cli.c 的字段数量判断
  injection_action: 用户手动临时去掉 sscanf 返回值数量检查，使字段不足的输入也可能被当作成功
  trigger_command: make test_input_cli
  expected_error: invalid input 测试失败，字段不足的输入未被拒绝
  repair_action: 用户手动恢复 sscanf 返回值数量检查
  risk_level: L3
  domain_risk: none
```

训练目标：

```text
1. 理解 sscanf 返回成功匹配字段数量。
2. 理解解析函数不能只看是否运行。
3. 理解无效输入必须被拒绝。
4. 理解 PondRecord *out_record 是输出参数。
5. 理解输入层只负责语法解析，不负责领域判断。
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
2. 用户运行 make test_input_cli。
3. 用户复制报错或异常输出。
4. 用户先自己判断错误类型。
5. Codex 只做提示，不直接给最终答案。
6. 用户手动修复。
7. 用户重新运行 make test_input_cli。
8. 用户重新运行 make test。
9. 用户写 bug_lab。
10. Codex 审查 bug_lab。
```

本次 Bug Lab 文件路径：

```text
bug_lab/2026-05-30-input-cli-parse-contract.md
```

Bug Lab 模板：

```markdown
# 2026-05-30 Input CLI Parse Contract Bug Lab

## 1. Bug 注入目标

训练 sscanf 字段数量检查和输入解析合同。

## 2. 注入位置

src/input_cli.c 的 sscanf 返回值判断。

## 3. 注入前 Clean Baseline

记录：
- make test_input_cli:
- make test:
- git diff --check:
- git diff -- existing tests:
- git diff -- existing headers:
- git diff -- existing sources:
- git diff -- domain_snapshot:

## 4. 注入动作

临时去掉或破坏 sscanf 返回值数量检查。

## 5. 触发命令

```bash
make test_input_cli
```

## 6. 报错信息或异常行为

粘贴真实报错或测试失败输出。

## 7. 错误类型判断

compile / link / runtime / parse_contract / behavior_regression / git / domain_policy

本次预期：parse_contract / behavior_regression

## 8. 根因分析

解释为什么解析函数必须检查成功解析字段数量。

## 9. 修复动作

恢复 sscanf 返回值数量检查。

## 10. 回归测试

```bash
make test_input_cli
make test
git diff --check
```

## 11. 防复发规则

例如：所有输入解析函数必须检查 NULL 参数和字段数量，不得把解析失败当成成功。

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
docs/daily/2026-05-30-input-cli-module-v0.md
```

模板：

```markdown
# 2026-05-30 Input CLI Module v0

## 0. 闭环身份确认

本次 input_cli 模块创建、测试命令执行、Bug Lab 注入、Bug 修复、docs/daily 与 bug_lab 初稿均由用户本人完成。Codex 仅提供带练、审查和纠偏。若 Codex 参与整理文档，须在此处明确写明“用户已授权 Codex 整理”。

## 1. 今日主线

用户本人手动拆出最小 input_cli 模块 v0。

## 2. C Foundation Gate

记录今日补齐的：
- const char *line
- PondRecord *out_record
- 指针输出参数
- NULL 参数检查
- sscanf 返回值
- 字段数量校验
- 解析失败 vs 领域非法
- input_cli 为什么不调用 judge/control

## 3. 上一个闭环 SRS 回顾

记录今日开始前抽问的 3-5 张 SRS。

## 4. 领域证据与 gap 边界

- WQ-DO-GROWOUT-001 / SRC-001:
- GAP-001:
- temp_status:
- input_cli 是否新增领域规则:
- input_cli 是否调用 judge/control/csv_store/output:
- domain_snapshot 是否修改:

## 5. 用户手动执行步骤

记录实际执行步骤。

## 6. input_cli 模块职责边界

- record:
- input_cli:
- judge:
- control:
- csv_store:
- output:
- main:
- 今日不做:

## 7. 主线测试结果

```bash
make test_input_cli
make test
git diff --check
```

记录真实结果。

## 8. Clean Baseline

记录以下检查：

```bash
git diff -- tests/test_judge.c tests/test_control.c tests/test_csv_store.c tests/test_output.c
git diff -- include/record.h include/judge.h include/control.h include/csv_store.h include/output.h
git diff -- src/judge.c src/control.c src/csv_store.c src/output.c
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
10. 是否明确 record / input_cli / judge / control / csv_store / output / main 的职责边界。
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
指针
sscanf
输入解析
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
1. 一个 const char *line 问题。
2. 一个 PondRecord *out_record 问题。
3. 一个 sscanf 返回值问题。
4. 一个输入解析合同问题。
5. 一个模块边界问题。
6. 一个 bug 归因问题。
7. 一个 408 映射问题。
8. 一个领域证据边界问题。
```

本次建议新增 SRS：

```text
Q1: 为什么 input_parse_record_line 需要 PondRecord *out_record？
A1: 因为函数需要把解析结果写回调用方提供的结构体对象，指针输出参数可以让函数修改调用方的数据。

Q2: sscanf 的返回值表示什么？
A2: 表示成功匹配并赋值的字段数量，必须检查它是否等于预期字段数。

Q3: 为什么字段数量不足的输入必须返回失败？
A3: 因为缺字段会导致 PondRecord 不完整，后续 judge/control/csv_store/output 都不能安全使用。

Q4: 为什么 input_cli 不应该调用 judge/control？
A4: input_cli 是输入解析层，只负责把文本变成 PondRecord，不拥有判断或控制逻辑。

Q5: 解析失败和领域非法有什么区别？
A5: 解析失败是文本格式无法转成结构体；领域非法是数值虽然能解析，但是否合理需要领域规则或输入校验模块判断。
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
      project_mapping: "input_cli 将文本字段解析为 PondRecord 这一结构化数据对象。"
      task_evidence: "include/record.h + src/input_cli.c"

    - chapter: "第1章 绪论"
      section: "抽象数据类型"
      point: "基本操作"
      project_mapping: "input_parse_record_line(line, out_record) 可视作对 PondRecord 的构造/赋值操作。"
      task_evidence: "include/input_cli.h"

  computer_organization:
    - chapter: "第1章 计算机系统概述"
      section: "计算机系统层次结构"
      point: "高级语言程序到可执行程序的转换"
      project_mapping: "Makefile 调用 gcc，把 src/input_cli.c 和 tests/test_input_cli.c 构建成 build/test_input_cli。"
      task_evidence: "Makefile 中 test_input_cli 目标"

    - chapter: "第2章 数据的表示和运算"
      section: "字符与字符串表示"
      point: "文本数字到二进制数值表示"
      project_mapping: "输入文本中的 temp/oxygen 被解析为 PondRecord 中的 float 字段。"
      task_evidence: "src/input_cli.c"
      note: "王道版本小节需手动校准。"

    - chapter: "needs_manual_alignment_with_wangdao"
      section: "指针与内存访问"
      point: "通过指针输出参数修改调用方对象"
      project_mapping: "PondRecord *out_record 用于把解析结果写入调用方结构体。"
      task_evidence: "include/input_cli.h"

  operating_system:
    - chapter: "第1章 计算机系统概述"
      section: "操作系统的概念、功能和目标"
      point: "程序执行"
      project_mapping: "用户执行 make test_input_cli，make 启动 gcc 和测试程序进程。"
      task_evidence: "make test_input_cli"

    - chapter: "needs_manual_alignment_with_wangdao"
      section: "输入输出基础"
      point: "标准输入的后续扩展"
      project_mapping: "input_cli v0 先解析字符串，后续 main 可接入 stdin/fgets。"
      task_evidence: "src/input_cli.c"

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
make test_input_cli
make test
```

如果有暂存：

```bash
git diff --cached
```

允许 add 的文件：

```bash
git add codex/daily_packets/007-input-cli-module-v0.md
git add include/input_cli.h
git add src/input_cli.c
git add tests/test_input_cli.c
git add Makefile
git add docs/daily/2026-05-30-input-cli-module-v0.md
git add bug_lab/2026-05-30-input-cli-parse-contract.md
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
tests/test_output.c
include/record.h
include/judge.h
include/control.h
include/csv_store.h
include/output.h
src/judge.c
src/control.c
src/csv_store.c
src/output.c
main / hardware / database / frontend 相关文件
```

推荐 commit message：

```bash
git commit -m "feat: complete guided input cli loop"
```

或分两个 commit：

```bash
git commit -m "feat: add input cli module v0"
git commit -m "docs: record input cli parse bug lab"
```

若保持每日闭环单 commit，使用：

```bash
git commit -m "feat: complete guided input cli loop"
```

## 16. Codex 第一轮输出要求

Codex 第一轮只允许输出计划，不允许修改文件。

Codex 第一轮和第二轮都不得修改文件。

Codex 第一轮不得给完整实现代码。

必须按以下格式：

```markdown
# Codex 第一轮：007-input-cli-module-v0 执行计划

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
- const char *line 是什么？
- PondRecord *out_record 是什么？
- sscanf 返回值表示什么？
- 为什么要检查 NULL？
- 为什么要检查字段数量？

## 4. 当前任务状态判断

判断 input_cli 模块是否已存在。
判断 Makefile 是否已有 test_input_cli。
判断 record / output / csv_store 稳定基础。
判断领域边界。

## 5. 今日主线最小目标

说明今日只做 input_cli 文本解析模块。

## 6. 用户需要亲自执行的步骤

给出用户需要手动执行的 nvim / make / git diff 命令。

## 7. Codex 只负责带练的内容

说明 Codex 不改文件，只解释和审查。
说明 Codex 不给完整可复制代码，只给思路、检查点、伪代码和填空提示。

## 8. 明确禁止 Codex 代工的内容

明确不允许 Codex 创建 input_cli 文件、修改 Makefile、写 docs、写 bug_lab、修改 C 文件、修改 domain_snapshot。

## 9. Clean Baseline 要求

列出用户主线完成后要执行的检查。

## 10. 今日 Bug Lab 候选

A. sscanf 返回值未检查 bug
B. NULL out_record 未检查 bug
C. 字段顺序解析错误 bug

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
9. input_cli 是否只做文本解析，没有调用 judge/control/csv_store/output。
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
7. 替用户修改 input_cli 模块。
8. 给完整可复制实现。
```

最终完成标准：

```text
用户本人完成 input_cli 模块创建。
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
