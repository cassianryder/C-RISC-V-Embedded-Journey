# 002 - Test Judge Build v0

## 0. Metadata

packet_id: 002-test-judge-build-v0
stage: Stage 2-1
task_name: Test Judge Build v0
task_type: test_addition
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
  - docs/daily/2026-05-23-judge-module-v0.md
  - bug_lab/2026-05-23-judge-parameter-contract.md
  - notes/current.md
  - Makefile
  - include/record.h
  - include/judge.h
  - src/judge.c
  - tests/test_judge.c

target_files_user_may_edit:
  - Makefile
  - docs/daily/2026-05-24-test-judge-build-v0.md
  - bug_lab/2026-05-24-judge-build-link-error.md

readonly_files_for_codex:
  - Makefile
  - docs/daily/2026-05-24-test-judge-build-v0.md
  - bug_lab/2026-05-24-judge-build-link-error.md
  - tests/test_judge.c
  - include/record.h
  - include/judge.h
  - src/judge.c

forbidden_files:
  - domain_snapshot/
  - domain_snapshot/cards/
  - domain_snapshot/source_index.md
  - domain_snapshot/gap_list.md
  - exercises/06-projects/1.c
  - src/control.c
  - include/control.h
  - src/csv_store.c
  - include/csv_store.h
  - src/input_cli.c
  - src/output.c
  - main.c
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
  - docs/daily/2026-05-23-judge-module-v0.md
  - bug_lab/2026-05-23-judge-parameter-contract.md
  - current_main_task
  - current_bug_lab

git_commit_required: true

## 1. 今日主线

今日只解决一个核心目标：

```text
用户本人手动把 judge 模块测试命令固化为 Makefile 测试入口。
```

今日主线不是让 Codex 代写 Makefile，不是让 Codex 自动生成 daily，不是让 Codex 自动修 bug。

今日完整闭环是：

```text
上一个闭环 SRS 抽卡
→ 用户手动阅读 Makefile / judge 模块文件
→ Codex 解释建议 patch
→ 用户手动修改 Makefile
→ 用户手动运行 make test_judge
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

今日主线目标从：

```text
手动 gcc 临时测试 judge 模块
```

升级为：

```text
make test_judge
make test
```

今日重点训练能力：

```text
1. Makefile 最小目标编写
2. C 多文件编译与链接
3. 测试入口固化
4. Clean Baseline 意识
5. Bug Lab 受控故障注入
6. 链接错误定位
7. SRS 抽卡
8. 王道 408 小节映射
9. Git 最小提交
```

Codex 在今日主线中的角色：

```text
带练、解释、审查、提问、设计 Bug Lab，不代工。
```

## 2. 今日不做

今日明确不做：

```text
1. 不拆 control 模块。
2. 不拆 csv_store 模块。
3. 不拆 input_cli 模块。
4. 不拆 output 模块。
5. 不重构 main。
6. 不接硬件。
7. 不接 Milk-V Duo S。
8. 不新增传感器逻辑。
9. 不新增前端。
10. 不新增数据库。
11. 不做 AI 预测。
12. 不做新的领域资料检索。
13. 不做证据审核。
14. 不修改 domain_snapshot。
15. 不修改 domain_snapshot/cards/。
16. 不修改 domain_snapshot/source_index.md。
17. 不修改 domain_snapshot/gap_list.md。
18. 不新增日本沼虾温度阈值。
19. 不把 temp_status() 的 legacy 行为解释成领域规则。
20. 不关闭 GAP-001。
21. 不破坏 exercises/06-projects/1.c 单文件参考版。
22. 不修改 tests/test_judge.c。
23. 不修改 include/record.h。
24. 不修改 include/judge.h。
25. 不修改 src/judge.c。
26. 不整理缩进。
27. 不格式化代码。
28. 不补充无关注释。
29. 不做顺手优化。
30. 不重命名变量。
31. 不移动文件。
32. 不生成新的主线模块文件。
33. 不让 Codex 自动修改文件。
34. 不让 Codex 自动生成 docs/daily 成品。
35. 不让 Codex 自动生成 bug_lab 成品。
```

今日所有动作必须服务于：

```text
judge 模块测试入口固化 + 闭环末端 Bug Lab 训练
```

## 3. 证据与 gap 约束 + 上一个闭环 SRS 抽卡

### 3.1 领域证据约束

Codex 和用户必须先确认：

```text
1. 当前任务是否涉及领域判断：只涉及已有 judge 行为回归，不新增领域判断。
2. 是否涉及养殖阈值：不新增阈值。
3. 是否涉及病害判断：不涉及。
4. 是否涉及投喂比例：不涉及。
5. 是否涉及增氧建议：只保留已有 DO 判断引用，不新增建议。
6. 是否涉及 open gap：涉及 GAP-001，但只作为禁止约束。
```

当前允许继续引用：

```yaml
card_id: WQ-DO-GROWOUT-001
source_id: SRC-001
usage: oxygen_status() / needs_aeration()
status: allowed
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
3. 不得新增温度阈值。
4. 不得关闭 GAP-001。
5. 不得在 docs/daily 中写“温度规则已证据化”。
6. 不得让 Codex 把测试中的 24.0 / 25.0 / 29.0 解释为日本沼虾 growout 阶段正式阈值。
7. 今日不是 domain_rule_update 类型任务。
```

### 3.3 上一个闭环 SRS 抽卡要求

每日任务开始前，必须从上一个闭环抽取 SRS。

SRS 来源：

```text
1. docs/daily/2026-05-23-judge-module-v0.md
2. bug_lab/2026-05-23-judge-parameter-contract.md
3. 上一次参数合同错误
4. 上一次模块拆分经验
5. 上一次 408 映射薄弱点
```

Codex 第一轮必须抽取 3-5 张 SRS 候选卡，但只提问，不代替用户答。

本次至少抽问：

```text
Q1: 为什么 oxygen_status(record.oxygen) 正确，而 oxygen_status(record) 错误？
A1: 用户回答后 Codex 校准。

Q2: 为什么 needs_aeration(record) 正确，而 needs_aeration(record.oxygen) 错误？
A2: 用户回答后 Codex 校准。

Q3: .h 文件和 .c 文件的职责边界是什么？
A3: 用户回答后 Codex 校准。

Q4: 为什么测试 judge 模块时需要把 src/judge.c 和 tests/test_judge.c 一起编译？
A4: 用户回答后 Codex 校准。

Q5: 编译错误和链接错误的区别是什么？
A5: 用户回答后 Codex 校准。
```

SRS 卡片分类：

```text
C 语言接口类
Makefile / 构建类
GCC 编译类
链接类
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
1. 打开 nvim
2. 阅读 Makefile
3. 阅读 include/record.h
4. 阅读 include/judge.h
5. 阅读 src/judge.c
6. 阅读 tests/test_judge.c
7. 手动修改 Makefile
8. 手动运行 make test_judge
9. 手动运行 make test
10. 手动运行 git diff --check
11. 手动运行 Clean Baseline 检查
12. 手动注入 Bug Lab bug
13. 手动触发 bug
14. 手动修复 bug
15. 手动写 bug_lab
16. 手动写 docs/daily
17. 手动 git add / commit
```

今日主线建议手动步骤：

```bash
# 1. 查看当前状态
git status

# 2. 打开 Makefile
nvim Makefile

# 3. 手动添加 test_judge / test 目标
# Codex 只能解释建议 patch，不能直接修改

# 4. 运行测试
make test_judge
make test

# 5. 检查 diff
git diff
git diff --check

# 6. Clean Baseline 检查
git diff -- tests/test_judge.c
git diff -- include/record.h include/judge.h src/judge.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c

# 7. 将输出贴给 Codex 审查
```

建议 Makefile 最小 patch 形态：

```makefile
.PHONY: clean list help test test_judge

test: test_judge

test_judge:
	@mkdir -p build
	$(CC) $(CFLAGS) -Wextra -Iinclude src/judge.c tests/test_judge.c -o build/test_judge
	./build/test_judge
```

如果仓库已有：

```makefile
CC = gcc
CFLAGS = -Wall -Wextra
```

则沿用已有变量，不重写整个 Makefile。

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
1. 读取任务包
2. 读取文件
3. 解释当前状态
4. 给出用户手写 patch 的建议
5. 解释每一行 Makefile / C 代码含义
6. 提问上一个闭环 SRS
7. 设计受控 Bug Lab
8. 审查用户贴出的 diff
9. 判断错误类型
10. 审查用户写的 docs/daily 草稿
11. 审查用户写的 bug_lab 草稿
12. 给 git add 白名单
13. 给 commit message 建议
```

禁止：

```text
1. 自动修改文件
2. 自动生成最终 docs/daily
3. 自动生成最终 bug_lab
4. 自动修复 bug
5. 自动 git add
6. 自动 git commit
7. 自动修改 domain_snapshot
8. 代替用户完成闭环
```

Codex 每次输出都必须包含：

```text
1. 下一步用户亲自做什么
2. 为什么做
3. 做完后把什么结果贴回来
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
1. 用户本人完成 Makefile 修改。
2. 用户本人运行 make test_judge。
3. 用户本人运行 make test。
4. 测试通过。
5. git diff 只包含 Makefile、docs/daily、bug_lab、daily packet 等目标文件。
6. tests/test_judge.c 无 diff。
7. include/record.h 无 diff。
8. include/judge.h 无 diff。
9. src/judge.c 无 diff。
10. domain_snapshot 无 diff。
11. exercises/06-projects/1.c 无 diff。
12. 没有无关格式化。
13. 没有 Codex 代工。
14. docs/daily 有真实操作记录。
15. SRS 有新增候选。
16. Bug Lab 完成受控注入和修复。
17. 408 映射完成，并尽量细到王道小节。
```

主线验收命令：

```bash
make test_judge
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
make test_judge: 通过
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
6. 无 C 文件误改。
7. 无 build/、tmux log 等杂项进入暂存区。
```

建议命令：

```bash
make test_judge
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
  make_test_judge: pass
  make_test: pass
  diff_check: pass
  tests_test_judge_diff: none
  c_source_diff: none
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
A. Makefile include 路径 bug
B. Makefile 链接实现文件缺失 bug
C. Makefile target 名称 bug
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
  type: link_error
  name: judge_missing_implementation_link_error
  injection_point: Makefile 的 test_judge 编译命令
  injection_action: 用户手动临时删除 src/judge.c
  trigger_command: make test_judge
  expected_error: undefined reference to temp_status / oxygen_status / needs_aeration
  repair_action: 用户手动加回 src/judge.c
  risk_level: L3
  domain_risk: none
```

训练目标：

```text
1. 理解 test_judge.c 只有函数调用。
2. 理解 src/judge.c 提供函数实现。
3. 理解只包含头文件不等于链接到实现。
4. 理解编译错误和链接错误的区别。
5. 理解 Makefile 中多 .c 文件参与最终可执行文件构建。
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
```

## 10. Bug Lab 修复要求

用户必须亲自修复 bug。

修复流程：

```text
1. 用户手动注入 bug。
2. 用户运行 make test_judge。
3. 用户复制报错。
4. 用户先自己判断错误类型。
5. Codex 只做提示，不直接给最终答案。
6. 用户手动修复。
7. 用户重新运行 make test_judge。
8. 用户重新运行 make test。
9. 用户写 bug_lab。
10. Codex 审查 bug_lab。
```

本次 Bug Lab 文件路径：

```text
bug_lab/2026-05-24-judge-build-link-error.md
```

Bug Lab 模板：

```markdown
# 2026-05-24 Judge Build Link Error Bug Lab

## 1. Bug 注入目标

训练 C 多文件工程中的链接错误识别能力。

## 2. 注入位置

Makefile 的 test_judge 编译命令。

## 3. 注入前 Clean Baseline

记录：
- make test_judge:
- make test:
- git diff --check:
- git diff -- tests/test_judge.c:
- git diff -- domain_snapshot:

## 4. 注入动作

临时删除 test_judge 编译命令中的 src/judge.c。

## 5. 触发命令

```bash
make test_judge
```

## 6. 报错信息

粘贴真实报错。

## 7. 错误类型判断

compile / link / runtime / make / git / domain_policy

本次预期：link

## 8. 根因分析

解释为什么 test_judge.c 找到了声明，但链接阶段找不到实现。

## 9. 修复动作

加回 src/judge.c。

## 10. 回归测试

```bash
make test_judge
make test
git diff --check
```

## 11. 防复发规则

例如：多文件 C 测试必须同时编译测试文件和实现文件。

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
docs/daily/2026-05-24-test-judge-build-v0.md
```

模板：

```markdown
# 2026-05-24 Test Judge Build v0

## 1. 今日主线

用户本人手动将 judge 模块测试入口固化到 Makefile。

## 2. 上一个闭环 SRS 回顾

记录今日开始前抽问的 3-5 张 SRS。

## 3. 领域证据与 gap 边界

- WQ-DO-GROWOUT-001 / SRC-001:
- GAP-001:
- temp_status:
- domain_snapshot 是否修改:

## 4. 用户手动执行步骤

记录实际执行步骤。

## 5. 主线测试结果

```bash
make test_judge
make test
git diff --check
```

记录真实结果。

## 6. Clean Baseline

记录以下检查：

```bash
git diff -- tests/test_judge.c
git diff -- include/record.h include/judge.h src/judge.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
```

## 7. Bug Lab 受控注入

### 7.1 注入目标

### 7.2 注入位置

### 7.3 注入动作

### 7.4 触发命令

### 7.5 报错信息

### 7.6 错误类型判断

### 7.7 修复动作

### 7.8 回归测试

## 8. 今日新增 SRS

至少 3 张。

## 9. 408 映射

细到王道章节 / 小节 / 知识点。

## 10. Git 提交

记录 git add 白名单和 commit message。

## 11. 明日建议
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
1. 一个 Makefile 问题。
2. 一个链接错误问题。
3. 一个 bug 归因问题。
4. 一个 408 映射问题。
5. 如果涉及领域规则，再加一个领域证据问题。
```

本次建议新增 SRS：

```text
Q1: 为什么 test_judge.c 包含 judge.h 仍然需要 src/judge.c 参与编译？
A1: 因为头文件只提供声明，函数实现位于 src/judge.c，链接阶段需要实现文件生成的目标代码。

Q2: undefined reference 通常属于编译错误还是链接错误？
A2: 链接错误。说明声明可能存在，但链接器找不到函数定义。

Q3: Makefile 中 -Iinclude 的作用是什么？
A3: 告诉编译器去 include 目录搜索头文件。

Q4: 为什么本任务禁止修改 tests/test_judge.c？
A4: 因为今日目标是构建入口固化，修改测试文件会制造无关 diff，破坏 bug 归因。

Q5: GAP-001 open 时，temp_status() 为什么不能被解释为证据化领域规则？
A5: 因为缺少已审核 growout 阶段温度状态阈值卡，只能标记 legacy behavior + needs_human_verification。
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
      section: "1.1 数据结构的基本概念"
      point: "数据元素、数据对象、数据结构"
      project_mapping: "PondRecord 是池塘记录这一数据对象的结构化表示"
      task_evidence: "include/record.h 中定义 PondRecord"

    - chapter: "第1章 绪论"
      section: "1.2 算法和算法评价"
      point: "算法的正确性、可读性、健壮性"
      project_mapping: "judge 模块通过 test_judge 回归验证判断函数行为"
      task_evidence: "tests/test_judge.c 与 make test_judge"

  computer_organization:
    - chapter: "第1章 计算机系统概述"
      section: "1.2 计算机系统层次结构"
      point: "高级语言程序、汇编语言程序、机器语言程序之间的转换"
      project_mapping: "Makefile 调用 gcc 将 src/judge.c 和 tests/test_judge.c 构建为可执行文件"
      task_evidence: "Makefile 中 test_judge 目标"

    - chapter: "第1章 计算机系统概述"
      section: "1.3 计算机性能指标"
      point: "程序执行前的编译、链接与可执行文件生成"
      project_mapping: "Bug Lab 中删除 src/judge.c 后出现 undefined reference，体现链接阶段符号解析失败"
      task_evidence: "bug_lab/2026-05-24-judge-build-link-error.md"

    - chapter: "第2章 数据的表示和运算"
      section: "2.2 浮点数的表示与运算"
      point: "浮点数表示与比较"
      project_mapping: "oxygen_status(float) / temp_status(float) 使用 float 参数参与状态判断"
      task_evidence: "include/judge.h 与 src/judge.c"
      note: "今日不修改这些函数，只做映射"

  operating_system:
    - chapter: "第1章 计算机系统概述"
      section: "1.1 操作系统的概念、功能和目标"
      point: "操作系统作为用户与计算机硬件之间的接口"
      project_mapping: "用户通过 shell 执行 make，make 调用 gcc 进程完成构建"
      task_evidence: "make test_judge"

    - chapter: "第4章 文件管理"
      section: "4.1 文件系统基础"
      point: "文件、目录、路径名"
      project_mapping: "include/、src/、tests/、build/ 构成工程文件组织"
      task_evidence: "Makefile 中 -Iinclude、src/judge.c、tests/test_judge.c、build/test_judge"

    - chapter: "第4章 文件管理"
      section: "4.2 文件系统实现"
      point: "目录结构与文件定位"
      project_mapping: "mkdir -p build 保证输出目录存在，避免构建产物路径错误"
      task_evidence: "Makefile 中 @mkdir -p build"

  computer_network:
    - chapter: "不涉及"
      section: "不涉及"
      point: "今日任务无网络通信内容"
      project_mapping: "无"
      task_evidence: "无"
```

注意：

```text
王道章节名称可作为当前映射草案。
如果用户手头王道版本小节标题不同，应在 docs/daily 中标注 needs_manual_alignment_with_wangdao。
```

## 14. Git 要求

Git 必须由用户本人执行。

Codex 只能给白名单。

提交前必须执行：

```bash
git status
git diff
git diff --check
make test_judge
make test
```

如果有暂存：

```bash
git diff --cached
```

允许 add 的文件：

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
exercises/06-projects/1.c
tests/test_judge.c
include/record.h
include/judge.h
src/judge.c
control / csv_store / input_cli / output / main 相关文件
```

推荐 commit message：

```bash
git commit -m "build: add guided judge test loop"
```

或分两个 commit：

```bash
git commit -m "build: add judge test target"
git commit -m "docs: record judge build bug lab"
```

若保持每日闭环单 commit，使用：

```bash
git commit -m "build: complete guided judge test loop"
```

## 15. Codex 第一轮输出要求

Codex 第一轮只允许输出计划，不允许修改文件。

必须按以下格式：

```markdown
# Codex 第一轮：002-test-judge-build-v0 执行计划

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

判断 Makefile 是否已有 test_judge / test。
判断 judge 模块是否已具备源文件、头文件、测试文件。
判断领域边界。

## 4. 今日主线最小目标

说明今日只做 Makefile 测试入口固化。

## 5. 用户需要亲自执行的步骤

给出用户需要手动执行的 nvim / make / git diff 命令。

## 6. Codex 只负责带练的内容

说明 Codex 不改文件，只解释和审查。

## 7. 明确禁止 Codex 代工的内容

明确不允许 Codex 修改 Makefile、docs、bug_lab、C 文件、domain_snapshot。

## 8. Clean Baseline 要求

列出用户主线完成后要执行的检查。

## 9. 今日 Bug Lab 候选

A. include 路径错误
B. 缺少 src/judge.c 链接错误
C. Makefile target 名称错误

推荐：B

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
9. 下一步用户亲自做什么。
```

Codex 不允许：

```text
1. 直接修。
2. 直接写。
3. 直接提交。
4. 替用户完成闭环。
5. 替用户注入 bug。
6. 替用户修 bug。
```

最终完成标准：

```text
用户本人完成主线修改。
用户本人完成 Clean Baseline。
用户本人完成 Bug Lab 注入与修复。
用户本人写 docs/daily。
用户本人写 bug_lab。
Codex 只完成审查。
Git 提交只包含白名单文件。
```
