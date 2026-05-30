# 2026-05-29 Output Module v0

实际执行时间：2026-05-30

## 0. 闭环身份确认

本次 output 模块创建、测试命令执行、Bug Lab 注入与修复由用户本人完成。Codex 负责带练、审查、解释和纠偏。

用户已授权 Codex 整理本 daily 与 Bug Lab 文档。

## 1. 今日主线

今日主线是拆出最小 output 模块 v0：

```text
PondRecord + should_aerate -> output_format_record_line -> char buffer[]
```

output 模块只负责把一条池塘记录和控制决策格式化为一行人类可读文本。

今日不接 `main`，不调用 `judge/control/csv_store`，不新增领域阈值，不修改 `domain_snapshot`。

## 2. C Foundation Gate

今日补齐的 C 基础：

- `char buffer[]`：调用方提供的一段字符缓冲区。
- `size_t buffer_size`：缓冲区容量，用来保护写入边界。
- `'\0'`：C 字符串结束标记。
- `snprintf`：把格式化文本写入指定 buffer，并限制最大写入范围。
- `snprintf` 返回值：表示如果空间足够，本来需要写入的字符数。
- `written >= buffer_size`：说明输出被截断。
- `strcmp`：比较字符串内容，不能用 `==` 比较内容。
- output 不直接 `printf`：先写入 buffer，让 main 或其他出口决定打印、写文件、发串口或发网络。

## 3. 上一个闭环 SRS 回顾

- CSV header 不能每次 append 都写，否则数据文件会混入重复表头。
- `fseek(fp, 0, SEEK_END)` 把文件位置移动到末尾，`ftell(fp)` 返回当前位置到文件开头的字节偏移。
- `filename` 是文件路径字符串入口，`FILE *fp` 是 `fopen` 成功后返回的文件流操作入口。
- `csv_store` 只做持久化，不做判断和控制。
- `a` 是追加模式，`w` 会截断已有文件内容。

## 4. 领域证据与 gap 边界

- `WQ-DO-GROWOUT-001 / SRC-001`：本次未新增、未修改领域规则。
- `GAP-001`：温度阈值 gap 未在本次关闭。
- `temp_status`：未修改。
- output 是否新增领域规则：否。
- output 是否调用 `judge/control/csv_store`：否。
- `domain_snapshot` 是否修改：否。

## 5. 用户手动执行步骤

- 创建 `include/output.h`，声明 output 模块接口。
- 创建 `src/output.c`，用 `snprintf` 格式化 `PondRecord` 与 `should_aerate`。
- 创建 `tests/test_output.c`，验证正常 buffer 和 small buffer 场景。
- 修改 `Makefile`，加入 `test_output` 并纳入 `make test`。
- 运行 `make test_output`，通过。
- 运行 `make test`，四组测试通过。
- 进行 output buffer truncation Bug Lab 注入、触发、修复和回归。

## 6. output 模块职责边界

- `record`：定义系统数据结构 `PondRecord`。
- `judge`：判断水质状态。
- `control`：根据判断结果形成控制决策。
- `csv_store`：保存原始记录到 CSV。
- `output`：把已有数据和决策格式化到调用方提供的 buffer。
- `main`：后续负责串联流程，本次不接入。
- 今日不做：输入层、硬件层、网络层、数据库、前端、AI、领域阈值扩展。

## 7. 主线测试结果

```bash
make test_output
```

结果：通过，输出 `output_format_record_line函数测试通过！`

```bash
make test
```

结果：通过，`judge/control/csv_store/output` 四组测试均通过。

```bash
git diff --check
```

结果：无输出。

## 8. Clean Baseline

以下路径不属于本次 output 主线修改，检查结果为无 diff：

```bash
git diff -- tests/test_judge.c tests/test_control.c tests/test_csv_store.c
git diff -- include/record.h include/judge.h include/control.h include/csv_store.h
git diff -- src/judge.c src/control.c src/csv_store.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
```

说明：`Makefile`、`include/output.h`、`src/output.c`、`tests/test_output.c` 属于本次 output 主线修改。

## 9. Bug Lab 受控注入

### 9.1 注入目标

训练 C 字符串缓冲区、`snprintf` 返回值和 small buffer 截断检测。

### 9.2 注入位置

`src/output.c` 中 `snprintf` 返回值与 `buffer_size` 的比较。

### 9.3 注入动作

临时破坏或去掉：

```text
written >= buffer_size -> BUFFER_TOO_SMALL
```

### 9.4 触发命令

```bash
make test_output
```

### 9.5 报错信息或异常行为

触发失败：

```text
output_format_record_line函数测试未通过！
make: *** [test_output] Error 1
```

### 9.6 错误类型判断

```text
string_buffer / behavior_regression
```

### 9.7 修复动作

恢复 `snprintf` 返回值与 `buffer_size` 的比较，让小 buffer 场景返回 `BUFFER_TOO_SMALL`。

### 9.8 回归测试

```bash
make test_output
make test
git diff --check
```

结果：全部通过。

## 10. 今日新增 SRS

```yaml
- question: 为什么 output_format_record_line 不应该直接 printf 到终端？
  answer: 因为写入 buffer 更容易测试，也让 main 决定最终输出到终端、日志、串口或网络。
  category: 模块边界

- question: snprintf 的返回值表示什么？
  answer: 它返回如果空间足够本来需要写入的字符数；如果返回值大于等于 buffer_size，说明输出被截断。
  category: C 字符串

- question: 为什么写入 char buffer 的函数必须接收 buffer_size？
  answer: 因为函数需要知道可写空间上限，避免越界写入。
  category: char buffer

- question: 为什么不能用 == 比较两个字符串内容？
  answer: == 比较的是地址；字符串内容比较应使用 strcmp。
  category: C 字符串

- question: output 为什么不应该调用 judge/control？
  answer: output 是展示/格式化层，只展示已经传入的数据和决策结果，不拥有判断或控制逻辑。
  category: 模块边界
```

## 11. 408 映射

- 数据结构：结构体字段访问、顺序初始化、字符串数组。
- 计算机组成原理：字符在内存中的字节表示、buffer 连续存储、边界保护。
- 操作系统：用户缓冲区、文件/终端/串口/网络作为不同 I/O 出口的抽象。
- 计算机网络：buffer 可作为应用层协议消息的承载，例如后续 UART/socket/MQTT。

## 12. Git 提交

建议白名单：

```bash
git add include/output.h src/output.c tests/test_output.c Makefile
git add docs/daily/2026-05-29-output-module-v0.md
git add bug_lab/2026-05-29-output-buffer-truncation.md
```

建议 commit message：

```text
feat: add output module v0
```

注意：`build/` 不提交；`codex/daily_packets/005-csv-header-once-v0` 的路径状态属于另一条整理问题，本次不处理。

## 13. 明日建议

- 先复习 `char buffer[]`、`snprintf` 返回值、small buffer 截断检测。
- 然后检查 `005` 任务包路径状态，决定是否保留 `.md` 后缀版本。
- output 稳定后再进入 `input_cli` 或 `main` 串联，不提前接硬件。
