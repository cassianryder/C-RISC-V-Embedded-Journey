# 002 - Test Judge Build v0

## 0. Metadata

packet_id: 002-test-judge-build-v0
stage: Stage 2-1
task_name: Test Judge Build v0
task_type: test_addition
recommended_mode: Codex guided / heuristic mode / no direct overwrite without explanation

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
  - include/record.h
  - include/judge.h
  - src/judge.c
  - tests/test_judge.c
  - Makefile

target_files:
  - Makefile
  - tests/test_judge.c
  - docs/daily/2026-05-24-test-judge-build-v0.md
  - bug_lab/2026-05-24-*.md if a real bug appears

domain_cards:
  - WQ-DO-GROWOUT-001

related_gaps:
  - GAP-001: Growout temperature status thresholds

packet_status:
  - created

## 1. 今日主线

今日只做一件事：

把昨日已经拆出的 `judge` 模块固化成稳定工程资产。

具体目标：

1. 检查 `include/record.h`、`include/judge.h`、`src/judge.c`、`tests/test_judge.c` 的依赖关系。
2. 检查当前是否已有 Makefile。
3. 如果已有 Makefile，则在不破坏原有目标的前提下，补充最小 `test_judge` 或 `test` 目标。
4. 如果没有 Makefile，则可以创建一个最小 Makefile，只服务于 `judge` 模块测试。
5. 固化一个可重复运行的测试命令。
6. 运行 `tests/test_judge.c`，确认 judge 模块行为稳定。
7. 把测试命令、测试结果、遇到的问题写入 `docs/daily/2026-05-24-test-judge-build-v0.md`。
8. 如果出现真实 bug，写入 `bug_lab/2026-05-24-*.md`。
9. 从昨日 Bug Lab 中抽取 SRS 卡片候选，不需要生成 Anki 文件，只写入今日复盘文档。

今日判断标准：

从：

```text
judge 模块能单独跑测试
```

升级为：

```text
judge 模块拥有稳定、可重复、可追踪的构建与测试入口
```

## 2. 今日不做

今日明确不做：

1. 不拆 `control` 模块。
2. 不拆 `csv_store` 模块。
3. 不拆 `input_cli` 模块。
4. 不拆 `output` 模块。
5. 不重构 `main`。
6. 不接硬件。
7. 不接 Milk-V Duo S。
8. 不新增传感器逻辑。
9. 不新增前端。
10. 不新增数据库。
11. 不做 AI 预测。
12. 不做新的领域资料检索。
13. 不修改 `domain_snapshot`。
14. 不修改 `domain_snapshot/cards/`。
15. 不修改 `domain_snapshot/source_index.md`。
16. 不修改 `domain_snapshot/gap_list.md`。
17. 不新增日本沼虾温度阈值。
18. 不把 `temp_status()` 的 legacy 行为解释成领域规则。
19. 不关闭 GAP-001。
20. 不破坏 `exercises/06-projects/1.c` 单文件参考版。

## 3. SRS 回顾要求

Codex 在动手前，必须先输出以下 SRS 回顾，确认用户理解后再进入修改阶段。

### 3.1 昨日核心 SRS

Q1: 为什么 `oxygen_status(record.oxygen)` 正确，而 `oxygen_status(record)` 错误？

A1: 因为 `oxygen_status()` 的函数声明要求参数类型是 `float`，所以应该传入结构体中的 `oxygen` 字段，而不是整条 `PondRecord`。

Q2: 为什么 `needs_aeration(record)` 正确，而 `needs_aeration(record.oxygen)` 错误？

A2: 因为 `needs_aeration()` 的函数声明要求参数类型是 `PondRecord`，它需要读取整条池塘记录，而不是单独的氧气字段。

Q3: `.h` 文件和 `.c` 文件的职责边界是什么？

A3: `.h` 文件是接口合同，放类型定义和函数声明；`.c` 文件是实现，放函数体和内部实现细节。

Q4: `include guard` 的作用是什么？

A4: 防止同一个头文件在同一个 `.c` 文件的预处理过程中被重复展开，避免重复定义。

Q5: 为什么测试文件应该包含 `judge.h`，而不是直接复制函数声明？

A5: 因为测试应该依赖公开接口。如果测试复制声明，接口变化时测试可能和真实接口脱节，无法发现头文件合同问题。

### 3.2 今日新增 SRS

Codex 今日需要引导用户理解：

Q1: 为什么要把测试命令写进 Makefile？

A1: 因为 Makefile 把一次性的编译命令固化为可重复执行的工程入口，减少手动输入错误，让测试变成稳定流程。

Q2: `gcc -Iinclude src/judge.c tests/test_judge.c -o build/test_judge` 中 `-Iinclude` 的作用是什么？

A2: `-Iinclude` 告诉编译器去 `include/` 目录中查找头文件，例如 `record.h` 和 `judge.h`。

Q3: 为什么 `src/judge.c` 和 `tests/test_judge.c` 要一起参与编译？

A3: `tests/test_judge.c` 调用了 judge 函数，但函数体在 `src/judge.c` 中。两者必须一起编译并链接，最终才能生成可执行测试程序。

Q4: 编译错误、链接错误、运行期断言失败分别说明什么？

A4:
- 编译错误：语法、类型、头文件、声明等问题。
- 链接错误：函数声明存在，但找不到函数实现，或目标文件没有参与链接。
- 运行期断言失败：程序能编译链接，但行为结果不符合预期。

## 4. 领域卡读取要求

Codex 必须读取领域快照，但只能作为约束，不允许修改。

必须读取：

```text
domain_snapshot/cards/
domain_snapshot/source_index.md
domain_snapshot/gap_list.md
```

至少确认以下规则：

1. `oxygen_status()` / `needs_aeration()` 可以继续引用 `WQ-DO-GROWOUT-001 / SRC-001`。
2. `temp_status()` 当前只能作为 legacy behavior。
3. `temp_status()` 仍然是 `needs_human_verification`。
4. GAP-001 不能关闭。
5. 不允许新增 growout 阶段温度状态判断阈值。
6. 不允许把测试中的 `24.0 / 25.0 / 29.0` 解释为领域证据。

如果仓库中找不到这些文件，Codex 必须停止并报告缺失文件，不得自行补领域事实。

## 5. gap_list 约束

当前已知 gap：

```text
GAP-001: Growout temperature status thresholds
```

约束如下：

1. 如果 GAP-001 是 open，则 `temp_status()` 只能保持 legacy behavior。
2. 不能新增温度阈值。
3. 不能把 legacy behavior 写成 evidence-based domain rule。
4. 不能在 docs/daily 中写“温度规则已证据化”。
5. 不能修改 `domain_snapshot/gap_list.md`。
6. 不能生成任何关闭 GAP-001 的 commit。
7. 如果测试中保留 `temp 24.0 -> low`、`temp 25.0 -> normal`、`temp 29.0 -> high`，必须明确标注：

```text
legacy behavior only; needs_human_verification; not an approved growout temperature threshold
```

## 6. 启发式带练规则

Codex 必须采用启发式带练，不允许直接一口气代写完整改动。

第一轮只允许输出：

1. 当前文件树观察。
2. 需要读取的文件清单。
3. 对昨日闭环的理解。
4. 今日最小目标。
5. 预计会修改哪些文件。
6. 预计不会修改哪些文件。
7. 让用户确认 A / B / C 模式。

Codex 第一轮必须给用户三个模式：

### A 模式：只检查，不改文件

用于确认当前状态：

```text
读文件
检查 Makefile
检查 test_judge.c
输出建议
不修改任何文件
```

### B 模式：补最小构建入口

用于今天推荐执行：

```text
检查 Makefile
补 test_judge 目标
运行测试
写 docs/daily
有 bug 才写 bug_lab
```

### C 模式：只生成诊断报告

用于用户暂时不想改仓库：

```text
不改代码
不改 Makefile
只输出 judge 模块当前构建闭环缺口
```

默认推荐：

```text
B 模式
```

但必须等用户确认后才能改文件。

## 7. 禁止直接代写规则

Codex 禁止：

1. 未经用户确认 A/B/C 模式就修改文件。
2. 未读取现有 Makefile 就覆盖 Makefile。
3. 未检查现有测试结构就重写 `tests/test_judge.c`。
4. 为了测试方便修改业务逻辑。
5. 为了测试方便修改领域阈值。
6. 为了通过测试删除 legacy temp 测试。
7. 直接生成 control 模块。
8. 直接生成 csv_store 模块。
9. 直接生成 main 集成。
10. 直接修改 `domain_snapshot`。
11. 直接关闭 GAP-001。
12. 把自己当作领域事实来源。
13. 把 Codex 的推测写入 domain card。
14. 把 docs/daily 写成泛泛总结，而不是工程闭环记录。

## 8. Codex 权限边界

### 8.1 允许修改

Codex 今日允许修改：

```text
Makefile
tests/test_judge.c
docs/daily/2026-05-24-test-judge-build-v0.md
bug_lab/2026-05-24-*.md if a real bug appears
```

### 8.2 谨慎修改

Codex 只有在测试无法构建，并且能明确说明原因时，才允许小范围修改：

```text
include/record.h
include/judge.h
src/judge.c
```

修改条件：

1. 必须先说明问题。
2. 必须说明为什么不是测试文件的问题。
3. 必须说明修改不会改变领域规则。
4. 必须保持昨日 judge 模块接口合同稳定。
5. 必须得到用户确认。

### 8.3 禁止修改

Codex 禁止修改：

```text
domain_snapshot/
domain_snapshot/cards/
domain_snapshot/source_index.md
domain_snapshot/gap_list.md
exercises/06-projects/1.c
src/control.c
include/control.h
src/csv_store.c
include/csv_store.h
src/input_cli.c
src/output.c
main.c
```

如果这些文件不存在，也不允许今天创建 control / csv_store / input_cli / output / main 相关文件。

### 8.4 权限矩阵

| 项目 | 今日权限 |
|---|---|
| 修改 C 代码 | 原则上不改；只有构建错误且用户确认后可小修 judge 相关文件 |
| 修改 domain_snapshot | 禁止 |
| 新增测试 | 允许 |
| 修改 Makefile | 允许 |
| 生成 docs/daily | 必须 |
| 生成 bug_lab | 有真实 bug 时必须 |
| 生成 mapping | 只允许写入 docs/daily 的 408 映射，不单独生成 mapping 文件 |
| 关闭 gap | 禁止 |
| 新增领域阈值 | 禁止 |
| 拆 control 模块 | 禁止 |

## 9. 验收标准

今日任务通过标准：

### 9.1 构建入口

必须满足以下二选一：

A. Makefile 中存在可重复运行的测试目标，例如：

```bash
make test_judge
```

或：

```bash
make test
```

B. 如果暂时不修改 Makefile，必须在 docs/daily 中明确记录可重复运行的 gcc 命令，例如：

```bash
mkdir -p build
gcc -Wall -Wextra -Iinclude src/judge.c tests/test_judge.c -o build/test_judge
./build/test_judge
```

推荐优先选择 A。

### 9.2 测试通过

`tests/test_judge.c` 必须通过，至少覆盖：

1. `oxygen 4.0 -> low`
2. `oxygen 5.0 -> normal`
3. `oxygen 6.0 -> normal`
4. `needs_aeration(record)` 的 true / false 行为
5. `temp_status()` legacy behavior
6. temp legacy 测试必须标注 `needs_human_verification`

### 9.3 头文件依赖清楚

必须确认：

1. `record.h` 定义 `PondRecord`。
2. `judge.h` 引用 `record.h`。
3. `judge.h` 声明 `temp_status()`、`oxygen_status()`、`needs_aeration()`。
4. `judge.c` 实现 `judge.h` 中声明的函数。
5. `test_judge.c` 通过 `judge.h` 使用公开接口。

### 9.4 文档沉淀完成

必须生成：

```text
docs/daily/2026-05-24-test-judge-build-v0.md
```

内容必须包括：

1. 今日主线。
2. 读取文件。
3. 修改文件。
4. 构建命令。
5. 测试结果。
6. 是否出现 bug。
7. Bug Lab 文件路径，如果有。
8. SRS 卡片候选。
9. 408 映射。
10. 明日建议。

### 9.5 Bug Lab

如果出现以下任一问题，必须写入 Bug Lab：

1. 头文件找不到。
2. include 路径错误。
3. 函数声明与定义不一致。
4. 链接时 undefined reference。
5. `float` 和 `PondRecord` 参数再次混用。
6. Makefile 目标写错。
7. 测试命令只能手动跑，无法固化。

如果没有出现真实 bug，则 docs/daily 中写：

```text
今日未产生新的 Bug Lab；昨日参数合同 bug 已完成回归检查。
```

## 10. 测试要求

Codex 至少需要执行：

```bash
make test_judge
```

如果没有该目标，则先查看 Makefile，再决定是否新增。

如果新增目标，建议目标形态为：

```makefile
test_judge:
	mkdir -p build
	gcc -Wall -Wextra -Iinclude src/judge.c tests/test_judge.c -o build/test_judge
	./build/test_judge
```

如果仓库已有变量风格，例如：

```makefile
CC = gcc
CFLAGS = -Wall -Wextra -Iinclude
```

则优先沿用已有风格，不要强行重写。

测试后必须记录：

1. 实际执行命令。
2. 是否通过。
3. 如失败，失败阶段是 compile / link / runtime。
4. 如何修复。
5. 是否影响领域规则。

## 11. Bug Lab 要求

今日重点回归昨日 Bug：

```text
bug_lab/2026-05-23-judge-parameter-contract.md
```

必须检查：

1. `oxygen_status(record.oxygen)` 是否仍然正确。
2. `needs_aeration(record)` 是否仍然正确。
3. 测试中是否再次出现参数合同混用。
4. Makefile 固化后是否仍然能暴露这种类型错误。

如果出现新 bug，Bug Lab 文件命名：

```text
bug_lab/2026-05-24-judge-build-*.md
```

Bug Lab 格式：

```markdown
# 2026-05-24 Judge Build Bug

## 1. Bug 现象

## 2. 触发命令

## 3. 报错信息

## 4. 根因分析

## 5. 修复方式

## 6. 复发预防

## 7. 对应 C / 408 知识点

## 8. 是否转化为测试用例

## 9. 是否转化为 SRS 卡片
```

## 12. Git commit 要求

任务完成后，Codex 必须建议用户执行：

```bash
git status
git diff
make test_judge
git add Makefile tests/test_judge.c docs/daily/2026-05-24-test-judge-build-v0.md
```

如果有 Bug Lab：

```bash
git add bug_lab/2026-05-24-*.md
```

推荐 commit message：

```bash
git commit -m "test: add stable judge module build loop"
```

如果只改文档和 Makefile，可选：

```bash
git commit -m "build: add judge test target"
```

禁止把 `domain_snapshot` 加入本次 commit。

提交前必须确认：

```bash
git diff --cached
```

检查没有误提交：

```text
domain_snapshot/
exercises/06-projects/1.c
control 模块
csv_store 模块
硬件相关文件
```

## 13. 今日复盘要求

Codex 必须生成：

```text
docs/daily/2026-05-24-test-judge-build-v0.md
```

建议结构：

```markdown
# 2026-05-24 Test Judge Build v0

## 1. 今日主线

## 2. SRS 回顾结果

## 3. 领域证据边界

## 4. 工程修改

## 5. 构建与测试命令

## 6. 测试结果

## 7. Bug Lab

## 8. SRS 卡片候选

## 9. 408 映射

## 10. 明日建议
```

### 13.1 SRS 卡片候选

至少写入以下候选：

```text
Q: 为什么测试 judge 模块时需要把 src/judge.c 和 tests/test_judge.c 一起编译？
A: 因为 test_judge.c 调用了 judge 函数，但函数实现位于 src/judge.c；只编译测试文件会导致链接阶段找不到函数定义。

Q: gcc 命令中的 -Iinclude 是什么作用？
A: 它告诉编译器到 include 目录查找头文件，使 #include "judge.h" 和 #include "record.h" 能被正确解析。

Q: 编译错误和链接错误的区别是什么？
A: 编译错误通常发生在语法、类型、头文件、声明阶段；链接错误发生在函数声明存在但找不到实现，或目标文件没有参与链接时。
```

## 14. 408 映射

今日必须写入 docs/daily。

### 14.1 数据结构

```text
结构体 PondRecord
结构体字段访问 record.oxygen / record.temp
结构体作为函数参数
模块接口中的数据抽象
```

### 14.2 计算机组成原理

```text
C 源文件到目标文件
多个 .c 文件参与编译
目标文件链接
函数符号引用与解析
float 数据表示与比较边界
```

### 14.3 操作系统

```text
Makefile 调用编译器属于开发环境中的进程执行
./build/test_judge 是可执行文件加载运行
文件路径、目录结构、可执行权限属于 OS 文件系统视角
```

### 14.4 计算机网络

```text
今日不涉及
```

### 14.5 软件工程

```text
接口合同
模块边界
构建入口
单元测试
回归测试
Bug Lab
可重复工程流程
```

## 15. Codex 第一轮输出要求

Codex 收到本任务包后，第一轮只能输出诊断与计划，不允许直接改文件。

第一轮必须按以下结构输出：

```markdown
# Codex 第一轮：002-test-judge-build-v0 执行计划

## 1. 我已读取的文件

## 2. 当前 judge 模块状态判断

## 3. 当前 Makefile / 构建入口状态

## 4. 今日最小目标

## 5. 我建议选择的模式

A. 只检查，不改文件  
B. 补最小构建入口并运行测试  
C. 只生成诊断报告  

推荐：B

## 6. 预计修改文件

## 7. 明确不修改文件

## 8. 风险点

## 9. 等待用户确认

请用户回复：A / B / C
```

用户确认 B 后，Codex 才能进入文件修改阶段。
