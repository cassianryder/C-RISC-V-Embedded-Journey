# 2026-05-27 CSV Header Duplication Bug Lab

> 实际执行时间：2026-05-29  
> 任务包：`codex/daily_packets/005-csv-header-once-v0`

## 1. Bug 注入目标

训练 CSV 存储层中 header 只写一次的行为验证能力。

本次目标是理解：

```text
CSV header 是文件结构说明，只能在新文件或空文件中写一次。
如果每次 append 都写 header，历史记录会被重复 header 污染。
```

## 2. 注入位置

```text
src/csv_store.c
```

目标逻辑：

```c
if (size == 0)
```

含义：

```text
只有文件为空时才写入 header。
```

## 3. 注入前 Clean Baseline

```yaml
make_test_csv_store: pass
make_test: pass
git_diff_check: pass
tests_judge_control_diff: none
record_judge_control_csv_headers_diff: none
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
include/csv_store.h
src/judge.c
src/control.c
domain_snapshot/
exercises/06-projects/1.c
```

## 4. 注入动作

临时把：

```c
if (size == 0)
```

改成：

```c
if (1)
```

注入含义：

```text
不再判断文件是否为空，每次调用 csv_store_append_record() 都写 header。
```

## 5. 触发命令

```bash
make test_csv_store
```

## 6. 报错信息或异常行为

真实输出：

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

这不是编译错误，也不是链接错误。

原因：

```text
gcc 已经成功生成 build/test_csv_store。
测试程序也成功运行。
失败发生在运行阶段：CSV 文件内容不符合 header only once 的预期。
```

## 8. 根因分析

正确行为：

```text
第 1 次写入：文件为空，写 header + record1。
第 2 次写入：文件非空，只追加 record2。
第 3 次写入：文件非空，只追加 record3。
最终 line_count == 4，header_count == 1。
```

Bug 行为：

```text
第 1 次写入：写 header + record1。
第 2 次写入：再次写 header + record2。
第 3 次写入：再次写 header + record3。
最终 header_count == 3，line_count 也不再等于 4。
```

测试失败点：

```c
if (line_count != 4 || header_count != 1)
```

该断言能同时捕获：

```text
总行数异常。
header 重复出现。
```

## 9. 修复动作

恢复为：

```c
if (size == 0)
```

恢复含义：

```text
只有文件为空时写 header；已有内容时只追加数据行。
```

## 10. 回归测试

```yaml
make_test_csv_store: pass
make_test: pass
git_diff_check: pass
src_csv_store_diff_after_restore: none
```

输出摘要：

```text
make test_csv_store:
  csv_store_append_record函数测试通过！

make test:
  测试通过！
  control_should_aerate函数测试通过！
  csv_store_append_record函数测试通过！
```

## 11. 防复发规则

```text
CSV header 写入必须受文件空判断保护。
涉及 header once 的测试必须同时验证总行数和 header 出现次数。
```

最小测试规则：

```text
连续写入 3 条记录。
期望 line_count == 4。
期望 header_count == 1。
```

## 12. SRS 卡片

Q: 为什么 `if (size == 0)` 是 CSV header once 的关键判断？

A:

```text
size == 0 表示文件为空，只有这时才需要写 header。文件已有内容时继续写 header 会造成重复表头。
```

Q: 为什么这次 Bug 不是编译错误或链接错误？

A:

```text
代码能编译、链接并运行，失败发生在运行后文件内容不符合预期，因此是文件 I/O 行为退化。
```

Q: 为什么测试要同时检查 `line_count` 和 `header_count`？

A:

```text
line_count 验证总记录结构是否正确；header_count 验证 header 是否只出现一次。两者合起来才能覆盖 header 重复写入。
```

Q: 为什么用 `fgets` 而不是 `fgetc` 检查 header？

A:

```text
header 是一整行文本，fgets 逐行读取后可以直接用 strcmp 比较整行内容。
```

## 13. 408 映射

```yaml
data_structure:
  - point: "顺序记录"
    project_mapping: "CSV 文件可视为外存上的顺序记录集合，header 是结构说明，数据行是记录元素。"
    task_evidence: "tests/test_csv_store.c"

computer_organization:
  - point: "程序的编译、链接与运行"
    project_mapping: "本次 Bug 不影响编译链接，属于运行阶段文件内容行为错误。"
    task_evidence: "make test_csv_store"

operating_system:
  - point: "文件管理与文件操作"
    project_mapping: "fseek/ftell 判断文件大小，fprintf 写入，fgets 读取，fclose 关闭文件流。"
    task_evidence: "src/csv_store.c + tests/test_csv_store.c"

computer_network:
  - point: "不涉及"
    project_mapping: "今日无网络通信内容。"
```

## 14. 是否进入长期问题库

```text
是
```
