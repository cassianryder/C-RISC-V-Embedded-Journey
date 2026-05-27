# 2026-05-26 CSV Store File Mode Bug Lab

> 实际执行时间：2026-05-27  
> 任务包：`codex/daily_packets/004-csv-store-module-v0.md`

## 1. Bug 注入目标

训练 C 文件写入模式中追加写入和覆盖写入的区别：

```text
"a" = append，保留旧内容，在文件末尾追加新记录。
"w" = write，打开时截断原文件，再写入新内容。
```

本次 Bug Lab 对应真实养殖系统中的存储层风险：

```text
养殖记录需要长期累积历史数据。
如果误用覆盖写入，历史观测会被抹掉，后续复盘、趋势分析和模型训练都会失去数据基础。
```

## 2. 注入位置

```text
src/csv_store.c
```

目标代码：

```c
FILE *fp = fopen(filename, "a");
```

## 3. 注入前 Clean Baseline

```yaml
make_test_csv_store: pass
make_test: pass
git_diff_check: pass
tests_judge_control_diff: none
record_judge_control_source_diff: none
domain_snapshot_diff: none
exercise_reference_diff: none
```

## 4. 注入动作

临时把：

```c
FILE *fp = fopen(filename, "a");
```

改成：

```c
FILE *fp = fopen(filename, "w");
```

## 5. 触发命令

```bash
make test
```

## 6. 报错信息或异常行为

真实输出摘要：

```text
gcc -Wall -g -Wextra -Iinclude src/csv_store.c tests/test_csv_store.c -o build/test_csv_store
./build/test_csv_store
csv_store_append_record写入行数测试未通过！
make: *** [test_csv_store] Error 1
```

## 7. 错误类型判断

```text
file_io / behavior regression
```

这不是编译错误，也不是链接错误。`gcc` 已经成功生成并运行 `build/test_csv_store`。失败发生在运行阶段：文件写入行为不符合追加记录的预期。

## 8. 根因分析

测试流程：

```text
1. remove(filename) 清理旧测试文件。
2. 写入 record_test_1。
3. 写入 record_test_2。
4. 重新打开文件统计换行数。
5. 如果行数少于 2，则测试失败。
```

当使用 `"a"` 时：

```text
第一次写入后：文件有 1 行。
第二次写入后：文件追加到 2 行。
```

当误用 `"w"` 时：

```text
第一次写入后：文件有 1 行。
第二次 fopen(filename, "w") 会先截断文件。
第二次写入后：文件仍然只剩 1 行。
```

因此 `line_count < 2`，测试输出：

```text
csv_store_append_record写入行数测试未通过！
```

## 9. 修复动作

恢复为：

```c
FILE *fp = fopen(filename, "a");
```

## 10. 回归测试

```yaml
make_test_csv_store: pass
make_test: pass
git_diff_check: pass
```

## 11. 防复发规则

```text
养殖记录日志默认使用追加模式 "a"，不能使用覆盖模式 "w"。
涉及历史数据的存储层测试，必须至少写入两条记录并验证记录数量。
```

## 12. SRS 卡片

Q: `fopen(filename, "a")` 和 `fopen(filename, "w")` 的区别是什么？

A:

```text
"a" 是追加模式，保留旧内容并在文件末尾写入；"w" 是写入模式，打开时会截断原文件并覆盖历史内容。
```

Q: 为什么养殖记录系统需要 append，而不是 overwrite？

A:

```text
养殖数据需要长期保存历史观测。覆盖写入会丢失之前记录，破坏复盘、趋势分析和后续模型训练。
```

Q: `filename` 和 `FILE *fp` 的区别是什么？

A:

```text
filename 是文件路径字符串的首字符地址；FILE *fp 是 fopen 成功后返回的文件流操作入口。
```

Q: 为什么这次错误属于行为退化，而不是编译或链接错误？

A:

```text
代码能编译、链接并运行，但运行结果不符合“追加两条记录”的预期，所以属于文件 I/O 行为退化。
```

Q: 为什么测试前要 `remove(filename)`？

A:

```text
因为追加模式会保留旧内容。测试前删除旧文件可以让每次测试从干净状态开始，避免历史测试数据影响判断。
```

## 13. 408 映射

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
    point: "编译、链接与运行"
    project_mapping: "本次 bug 不影响编译链接，失败发生在可执行程序运行后的文件写入行为。"
    task_evidence: "make test 运行到 test_csv_store 后失败"

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
    task_evidence: "Bug Lab 中 fopen 模式注入"

computer_network:
  - chapter: "不涉及"
    section: "不涉及"
    point: "今日任务无网络通信内容"
    project_mapping: "无"
    task_evidence: "无"

note:
  - "王道版本小节名称可能存在差异，后续需要结合手头教材做 needs_manual_alignment_with_wangdao。"
```

## 14. 是否进入长期问题库

```text
是
```
