# 2026-05-26 CSV Store Module v0

> 实际执行时间：2026-05-27  
> 任务包：`codex/daily_packets/004-csv-store-module-v0.md`

## 0. 闭环身份确认

本次 csv_store 模块代码、测试、Bug Lab 注入与修复由用户亲手完成；Codex 负责 SRS 校准、代码审查、错误归因和文档整理。

今日从：

```text
record -> judge -> control
```

扩展出独立存储分支：

```text
record -> csv_store
```

## 1. 今日主线

今日最小目标：

```text
拆出 csv_store 模块 v0，只负责把 PondRecord 原始字段追加写入 CSV 文件。
```

今日不做：

```text
不写表头
不生成时间戳
不读取/解析 CSV
不接 main
不调用 judge/control
不新增领域规则
不修改 domain_snapshot
```

## 2. 上一个闭环 SRS 回顾

```text
1. control 调用 needs_aeration(record)，避免重复写 oxygen < 5.0f。
2. control.h 和 control.c 函数签名不一致属于编译阶段接口合同错误。
3. test_control 需要同时编译 src/control.c 和 src/judge.c。
4. judge 负责判断，control 负责建议，hardware 未来负责真实执行。
5. csv_store 属于存储层，只保存原始记录，不判断、不控制。
```

## 3. 领域证据与 gap 边界

```yaml
WQ-DO-GROWOUT-001:
  status: allowed
  usage: "oxygen_status() / needs_aeration() 保持既有存在，本任务不新增使用"

GAP-001:
  status: open
  meaning: "growout temperature status thresholds 未证据化"

temp_status:
  status: legacy_behavior
  verification: needs_human_verification

csv_store:
  new_domain_rule: false
  calls_judge: false
  calls_control: false
  writes_threshold: false

domain_snapshot:
  modified: false
```

## 4. 用户手动执行步骤

用户完成：

```text
1. 创建 include/csv_store.h。
2. 创建 src/csv_store.c。
3. 创建 tests/test_csv_store.c。
4. 修改 Makefile，新增 test_csv_store，并让 test 依赖 test_judge / test_control / test_csv_store。
5. 运行 make test_csv_store。
6. 运行 make test。
7. 运行 git diff --check。
8. 完成 Clean Baseline。
9. 注入并修复 fopen 文件模式 Bug。
```

## 5. csv_store 模块职责边界

```text
record:
  定义 PondRecord 数据结构。

judge:
  判断水质状态，不参与本次存储模块。

control:
  使用 judge 结果生成控制建议，不参与本次存储模块。

csv_store:
  把 PondRecord 原始字段序列化为 CSV 文本并追加写入文件。

main:
  今日不接入。
```

当前接口：

```c
int csv_store_append_record(const char *filename, PondRecord record);
```

当前语义：

```text
成功返回 0。
失败返回非 0。
```

当前实现要点：

```text
fopen(filename, "a")
fprintf(fp, ...)
fclose(fp)
```

## 6. 主线测试结果

```yaml
make_test_csv_store: pass
make_test: pass
git_diff_check: pass
```

测试输出摘要：

```text
make test_csv_store:
  csv_store_append_record函数测试通过！

make test:
  测试通过！
  control_should_aerate函数测试通过！
  csv_store_append_record函数测试通过！
```

## 7. Clean Baseline

```yaml
tests_judge_control_diff: none
record_judge_control_header_diff: none
judge_control_source_diff: none
domain_snapshot_diff: none
exercise_reference_diff: none
```

确认未修改：

```text
tests/test_judge.c
tests/test_control.c
include/record.h
include/judge.h
include/control.h
src/judge.c
src/control.c
domain_snapshot/
exercises/06-projects/1.c
```

## 8. Bug Lab 受控注入

Bug Lab 文件：

```text
bug_lab/2026-05-26-csv-store-file-mode.md
```

### 8.1 注入目标

训练 CSV 存储层中追加写入和覆盖写入的区别。

### 8.2 注入位置

```text
src/csv_store.c
```

### 8.3 注入动作

临时把：

```c
fopen(filename, "a")
```

改成：

```c
fopen(filename, "w")
```

### 8.4 触发命令

```bash
make test
```

### 8.5 报错信息或异常行为

真实输出摘要：

```text
csv_store_append_record写入行数测试未通过！
make: *** [test_csv_store] Error 1
```

### 8.6 错误类型判断

```text
file_io / behavior regression
```

不是编译错误，也不是链接错误。程序能构建并运行，但文件写入行为不符合追加记录预期。

### 8.7 根因

```text
"w" 每次打开文件都会截断旧内容。
第二次写入覆盖第一次写入。
最终文件只剩 1 行。
测试期望至少 2 行，所以失败。
```

### 8.8 修复与回归

修复：

```c
fopen(filename, "a")
```

回归：

```yaml
make_test_csv_store: pass
make_test: pass
git_diff_check: pass
```

## 9. 今日新增 SRS

Q1: `fopen(filename, "a")` 和 `fopen(filename, "w")` 的区别是什么？

A1:

```text
"a" 是追加模式，保留旧内容并在文件末尾写入；"w" 是写入模式，打开时会截断原文件并覆盖历史内容。
```

Q2: 为什么养殖记录系统需要 append，而不是 overwrite？

A2:

```text
养殖数据需要长期保存历史观测。覆盖写入会丢失之前记录，破坏复盘、趋势分析和后续模型训练。
```

Q3: `filename` 和 `FILE *fp` 的区别是什么？

A3:

```text
filename 是文件路径字符串的首字符地址；FILE *fp 是 fopen 成功后返回的文件流操作入口。
```

Q4: 为什么 csv_store 不应该调用 judge/control？

A4:

```text
csv_store 是持久化层，只保存原始记录；judge 负责判断，control 负责建议。混在一起会破坏模块边界。
```

Q5: 为什么测试前要 `remove(filename)`？

A5:

```text
追加模式会保留旧内容。测试前删除旧文件，可以让每次测试从干净状态开始，避免历史测试数据影响判断。
```

Q6: 为什么 CSV 是当前项目的最小持久化层？

A6:

```text
CSV 能用纯 C 标准库完成写入，便于观察、测试和后续导入分析工具，同时暂时不引入数据库复杂度。
```

## 10. 408 映射

```yaml
data_structure:
  - chapter: "第1章 绪论"
    section: "数据结构的基本概念"
    point: "数据对象、数据元素、数据结构"
    project_mapping: "PondRecord 是内存中的结构化数据对象，csv_store 将其字段序列化为 CSV 文本记录。"
    task_evidence: "include/record.h / src/csv_store.c"

  - chapter: "第2章 线性表"
    section: "线性表的定义和基本操作"
    point: "顺序记录与追加"
    project_mapping: "CSV 文件中的多行记录可视为外存上的顺序记录集合。"
    task_evidence: "tests/test_csv_store.c 写入两条记录并统计行数"
    note: "该映射为弱映射，需结合王道版本手动校准。"

computer_organization:
  - chapter: "第1章 计算机系统概述"
    section: "高级语言程序到可执行程序的转换"
    point: "预处理、编译、链接、运行"
    project_mapping: "Makefile 调用 gcc，把 src/csv_store.c 和 tests/test_csv_store.c 构建成 build/test_csv_store。"
    task_evidence: "Makefile test_csv_store"

  - chapter: "needs_manual_alignment_with_wangdao"
    section: "内存数据与外存表示"
    point: "结构体字段文本化输出"
    project_mapping: "PondRecord 在内存中是结构体，写入 CSV 后成为外存文本记录。"
    task_evidence: "fprintf(fp, ...)"

operating_system:
  - chapter: "第4章 文件管理"
    section: "文件、目录与路径名"
    point: "文件路径与目录组织"
    project_mapping: "csv_store 通过 filename 定位 CSV 文件，测试使用 build/test_pond_log.csv。"
    task_evidence: "tests/test_csv_store.c"

  - chapter: "第4章 文件管理"
    section: "文件操作"
    point: "文件打开、写入、关闭"
    project_mapping: "fopen / fprintf / fclose 对应 CSV 文件的打开、写入、关闭流程。"
    task_evidence: "src/csv_store.c"

  - chapter: "第4章 文件管理"
    section: "文件打开方式"
    point: "追加写入与覆盖写入"
    project_mapping: "\"a\" 保留历史记录并追加，\"w\" 截断并覆盖历史记录。"
    task_evidence: "bug_lab/2026-05-26-csv-store-file-mode.md"

computer_network:
  - chapter: "不涉及"
    section: "不涉及"
    point: "今日任务无网络通信内容"
    project_mapping: "无"
    task_evidence: "无"

note:
  - "王道版本小节名称可能存在差异，后续需要结合手头教材做 needs_manual_alignment_with_wangdao。"
```

## 11. Git 提交

允许 add：

```bash
git add codex/daily_packets/004-csv-store-module-v0.md
git add include/csv_store.h
git add src/csv_store.c
git add tests/test_csv_store.c
git add Makefile
git add docs/daily/2026-05-26-csv-store-module-v0.md
git add bug_lab/2026-05-26-csv-store-file-mode.md
```

禁止 add：

```text
build/
domain_snapshot/
exercises/06-projects/1.c
tests/test_judge.c
tests/test_control.c
include/record.h
include/judge.h
include/control.h
src/judge.c
src/control.c
input_cli / output / main / hardware / database / frontend 相关文件
```

推荐 commit message：

```bash
git commit -m "feat: complete guided csv store loop"
```

## 12. 明日建议

```text
1. 先 SRS 复习 FILE*、filename、fopen 模式、append vs overwrite。
2. 如果时间短，只做 csv_store 代码和 Bug Lab 回顾。
3. 如果时间充足，读取下一份 daily packet，再进入 input_cli 或 output 模块。
4. 保持分层：record 管数据，judge 管判断，control 管建议，csv_store 管存储。
```
