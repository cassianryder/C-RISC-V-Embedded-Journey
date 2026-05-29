# 2026-05-27 CSV Header Once and Duplication

> 实际完成时间：2026-05-29

## 今日主线

今天完成 `005 - CSV Header Once v0`：在 csv_store 存储层中保证 CSV header 只写一次。

本次链路保持：

```text
record -> csv_store
```

不接 `main`，不调用 judge/control，不新增领域规则，不修改 `domain_snapshot`。

## 1. 每日问题

### 问题 1：为什么 CSV header 只能写一次？

CSV header 是文件结构说明，不是每条数据的一部分。

正确结构应是：

```text
header
record1
record2
record3
```

如果每次 append 都写 header，文件会变成：

```text
header
record1
header
record2
header
record3
```

这会污染历史记录，影响后续读取、统计、复盘和模型训练。

### 问题 2：为什么 `size == 0` 是 header once 的关键判断？

`fseek(fp, 0, SEEK_END)` 把文件位置移动到末尾，`ftell(fp)` 返回当前位置相对文件开头的偏移量。

因此：

```text
size == 0：文件为空，需要先写 header。
size > 0：文件已有内容，只追加数据行。
```

`size == 0` 是“是否第一次写这个 CSV 文件”的最小判断。

### 问题 3：为什么用 `fgets` 而不是 `fgetc` 检查 header？

`fgetc` 每次读取一个字符，适合做字符级统计，例如数换行。

但 header 是一整行文本，测试要判断“这一行是不是 header”，所以更适合：

```c
fgets(line, sizeof(line), fp)
```

它把一整行读入 `char line[128]`，再用字符串比较判断是否等于 header。

### 问题 4：为什么不能用 `line == "sampled_at,pond_id,temp,oxygen\n"` 比较 header？

在 C 中，`==` 比较的是两个指针/地址是否相同，不是比较字符串内容是否相同。

`line` 是数组名，表达式中常退化为首元素地址；字符串字面量也有自己的地址。

比较字符串内容应使用：

```c
strcmp(line, "sampled_at,pond_id,temp,oxygen\n") == 0
```

`strcmp` 返回 0 表示两段文本内容完全相同。

### 问题 5：为什么 `fclose(fp)` 必须只在 `fp != NULL` 的分支中执行？

`FILE *fp` 是 `fopen` 成功后得到的文件流操作入口。

如果 `fopen` 失败，`fp == NULL`，此时没有有效文件流可以关闭。

因此安全结构是：

```text
if fp == NULL:
  记录失败
else:
  读取或写入文件
  fclose(fp)
```

### 问题 6：为什么 `line_count != 4 || header_count != 1` 能抓住 header 重复？

本次测试连续写入 3 条 `PondRecord`。

正确结果是：

```text
line_count == 4
header_count == 1
```

如果 header 每次都重复写入，`header_count` 会大于 1，`line_count` 也会超过 4。

使用 `||` 的含义是：

```text
总行数不对，失败。
或 header 次数不对，失败。
```

任意一个条件异常都说明 CSV 结构不符合预期。

## 2. 解答总结

今天的核心不是“写一行 header”，而是建立文件结构不变量：

```text
CSV 文件 = 1 行 header + N 行数据。
```

测试也从“是否写入成功”升级为“文件结构是否正确”：

```text
写入 3 条记录后，总行数必须是 4。
header 只能出现 1 次。
```

这一步让 csv_store 从简单追加数据，升级为更稳定的最小持久化层。

## 3. 闭环

已完成闭环：

- `src/csv_store.c` 使用 `a+ / fseek / ftell / size == 0` 判断是否写 header。
- `tests/test_csv_store.c` 使用 `fgets` 逐行读取 CSV。
- 使用 `strcmp` 统计 header 出现次数。
- 验证 `line_count == 4`。
- 验证 `header_count == 1`。
- `make test_csv_store` 通过。
- `make test` 通过。
- `git diff --check` 通过。

Bug Lab：

```text
临时把 if (size == 0) 改成 if (1)。
测试触发 header 重复写入。
make test_csv_store 失败。
恢复 if (size == 0) 后回归通过。
```

错误类型：

```text
file_io / behavior regression
```

这不是编译错误，也不是链接错误；程序能构建并运行，但文件内容结构不符合预期。

## 4. 使用场景和启发思考

CSV 是当前项目的最小持久化层。

它的作用是：

```text
保存历史观测。
方便人工查看。
方便后续导入表格、Python、数据库或模型训练流程。
```

header 重复不是小格式问题，而是数据污染问题。对于养殖系统，历史记录的结构稳定性会直接影响复盘、趋势分析和后续自动化决策。

## 5. 408 映射

数据结构：

- `PondRecord` 是内存中的结构体对象，CSV 文件把结构体字段序列化为外存顺序记录。
- `header + 多行 record` 可弱映射为顺序记录集合。

计算机组成原理：

- 本次 Bug 能通过编译和链接，失败发生在程序运行后的文件内容行为。
- 这有助于区分编译错误、链接错误和运行阶段行为退化。

操作系统：

- `fopen` 打开文件流。
- `fseek / ftell` 判断文件是否为空。
- `fprintf` 写入 header 和数据行。
- `fgets` 逐行读取文件。
- `fclose` 关闭有效文件流。

计算机网络：

- 今日不涉及网络通信。

## 6. 明日衔接

下一步先复盘：

- CSV header once
- `fgets`
- `strcmp`
- `FILE *fp` 生命周期
- `size == 0` 文件空判断

稳定后再进入 `input_cli` 或 `output` 模块，不急着接硬件。
