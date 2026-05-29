# 2026-05-27 CSV Header Once v0

> 实际完成时间：2026-05-29  
> 任务包：`codex/daily_packets/005-csv-header-once-v0`

## 1. 今日主线

本次从 `004 - CSV Store Module v0` 继续推进，只增强 csv_store 的 CSV 表头行为：

```text
新文件或空文件：先写 header，再写记录。
已有内容的文件：只追加记录，不重复写 header。
```

今日系统链路保持：

```text
record -> csv_store
```

本次不接 `main`，不生成时间戳，不读取解析 CSV，不调用 judge/control，不新增领域规则，不修改 `domain_snapshot`。

## 2. C Foundation Gate

今日重点补齐：

```text
char line[128]：逐行读取 CSV 的行缓冲区。
fgets(line, sizeof(line), fp)：从文件流读取一整行。
strcmp(line, header) == 0：比较字符串内容，而不是比较地址。
line_count：统计总行数。
header_count：统计 header 出现次数。
filename：文件路径字符串入口。
FILE *fp：打开文件后的文件流操作入口。
```

关键校准：

```text
写入 3 条 PondRecord 时，正确文件应为 4 行：1 行 header + 3 行数据。
header_count 必须为 1。
```

## 3. 用户完成的主线修改

`src/csv_store.c` 已具备：

```text
fopen(filename, "a+")
fseek(fp, 0, SEEK_END)
ftell(fp)
size == 0 时写入 header
随后写入 PondRecord 数据行
```

`tests/test_csv_store.c` 增强为：

```text
remove(filename) 清理测试文件。
连续写入 3 条 PondRecord。
重新以 "r" 打开 CSV。
使用 fgets 逐行读取。
验证 line_count == 4。
验证 header_count == 1。
```

## 4. 测试结果

主线验证通过：

```yaml
make_test_csv_store: pass
make_test: pass
git_diff_check: pass
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

## 5. Clean Baseline

确认无 diff：

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

## 6. Bug Lab

Bug Lab 文件：

```text
bug_lab/2026-05-27-csv-header-duplication.md
```

受控注入：

```text
临时把 if (size == 0) 改成 if (1)，让每次调用都写 header。
```

触发结果：

```text
csv_store_append_record写入行数测试未通过！
make: *** [test_csv_store] Error 1
```

错误类型：

```text
file_io / behavior regression
```

结论：

```text
测试能够抓住 header 重复写入问题。
```

## 7. 今日新增 SRS 候选

Q1: 为什么 CSV header 不能每次 append 都写？

A1:

```text
因为 header 是文件结构说明，只应出现一次。每条数据前都重复 header 会污染历史记录，影响后续读取、统计和分析。
```

Q2: `fgets` 和 `fgetc` 在本测试中的区别是什么？

A2:

```text
fgetc 逐字符读取，适合数换行；fgets 逐行读取，适合判断某一整行是否等于 header。
```

Q3: 为什么不能用 `line == "sampled_at,pond_id,temp,oxygen\n"` 判断 header？

A3:

```text
这会比较地址，不是比较字符串内容。字符串内容比较应使用 strcmp。
```

Q4: 为什么 `fclose(fp)` 必须在 `fp != NULL` 的分支里？

A4:

```text
只有 fopen 成功后才有有效文件流，才能安全 fclose。
```

## 8. 408 映射

```yaml
data_structure:
  - point: "结构化数据对象与顺序记录"
    project_mapping: "PondRecord 是内存结构体；CSV 文件由 header + 多行记录组成。"
    task_evidence: "tests/test_csv_store.c"

computer_organization:
  - point: "编译、链接、运行"
    project_mapping: "本次 Bug 能编译链接，但运行后文件内容不符合预期。"
    task_evidence: "make test_csv_store"

operating_system:
  - point: "文件打开、定位、写入、读取、关闭"
    project_mapping: "fopen / fseek / ftell / fprintf / fgets / fclose 构成 CSV 存储测试闭环。"
    task_evidence: "src/csv_store.c + tests/test_csv_store.c"

computer_network:
  - point: "不涉及"
    project_mapping: "今日无网络通信内容。"
```

## 9. 明日建议

下一步可以继续 Stage 2 模块化，但不要急着接硬件：

```text
1. 先复盘 CSV header once、fgets、strcmp、FILE* 生命周期。
2. 若时间短，做 Bug Lab 复盘。
3. 若时间充足，再进入 input_cli 或 output 模块。
4. 继续保持分层：record 管数据，csv_store 管存储，judge/control 不进入存储层。
```
