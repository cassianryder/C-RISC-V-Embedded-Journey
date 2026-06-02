# 2026-05-30 Input CLI Module v0

实际执行时间：2026-06-02

## 0. 闭环身份确认

本次 input_cli 模块创建、测试命令执行、Bug Lab 注入与修复由用户本人完成。Codex 负责带练、审查、解释、公司级样板测试示范和文档整理。

用户已授权 Codex 整理本 daily 与 Bug Lab 文档。

## 1. 今日主线

用户本人手动拆出最小 input_cli 模块 v0，并在后半段将其优化为生产级 v0 parser 样板。

主线数据流：

```text
text line -> input_cli -> PondRecord
```

input_cli 只负责把固定格式文本解析为 `PondRecord`，不调用 `judge/control/csv_store/output`，不接 `main`，不新增领域阈值，不修改 `domain_snapshot`。

## 2. C Foundation Gate

今日补齐的重点：

- `const char *line`：只读输入字符串入口。
- `PondRecord *out_record`：结构体指针输出参数，用于把解析结果写回调用方对象。
- 指针输出参数：返回值表达状态，输出参数承载结果。
- `NULL` 参数检查：防止无效地址读写。
- `sscanf` 返回值：成功匹配并赋值的字段数量。
- 字段数量校验：`matched != 3` 表示解析合同不满足。
- `extra` 尾部脏数据检测：拒绝多余字段和尾部垃圾。
- 解析失败 vs 领域非法：input_cli 只处理文本格式，不处理养殖阈值或领域合法性。
- `=` vs `==` / `!=`：条件中误写赋值会破坏 parse contract。

## 3. 上一个闭环 SRS 回顾

- `snprintf` 返回值不是是否成功，而是空间足够时本来需要写入的字符数。
- `written >= buffer_size` 表示输出被截断。
- `output_format_record_line` 不直接 `printf`，而是写入 buffer，供终端、文件、串口、网络复用。
- `char buffer[]` 在函数参数中会退化为指向首元素的指针；普通数组和指针变量不是同一个概念。
- `input_cli` 不应调用 `judge/control/csv_store/output`，因为它只属于输入解析层。

## 4. 领域证据与 gap 边界

- `WQ-DO-GROWOUT-001 / SRC-001`：本次未新增、未修改领域规则。
- `GAP-001`：仍保持 open，不关闭。
- `temp_status`：未修改，仍为 legacy behavior / needs_human_verification。
- input_cli 是否新增领域规则：否。
- input_cli 是否调用 `judge/control/csv_store/output`：否。
- `domain_snapshot` 是否修改：否。

## 5. 用户手动执行步骤

- 创建 `include/input_cli.h`，声明 input_cli 模块接口。
- 创建 `src/input_cli.c`，实现 `input_parse_record_line`。
- 将返回值从模糊 `int` 优化为 `InputParseResult`。
- 在 `src/input_cli.c` 中补齐 `NULL` 参数检查、`sscanf` 字段数量检查、`out_record` 写回。
- 编写并接入 `tests/test_input_cli.c`。
- 修改 `Makefile`，加入 `test_input_cli` 并纳入 `make test`。
- 将 parser 从教学 v0 优化为生产级 v0：拒绝多余字段和尾部脏数据。
- 完成 Bug Lab：注入 `matched = 3` 条件赋值 bug，触发失败后修复。

Codex 在用户授权下提供了 `tests/test_input_cli.c` 公司级样板测试示范，并整理本 daily 与 Bug Lab。

## 6. input_cli 模块职责边界

- `record`：定义 `PondRecord` 数据结构。
- `input_cli`：把文本行解析为 `PondRecord`。
- `judge`：判断水质状态，本次不调用。
- `control`：形成控制决策，本次不调用。
- `csv_store`：保存记录，本次不调用。
- `output`：格式化展示，本次不调用。
- `main`：后续串联流程，本次不接入。
- 今日不做：键盘循环、串口、网络、硬件、领域阈值、AI、数据库、前端。

## 7. 主线测试结果

```bash
make test_input_cli
```

结果：通过，输出 `input_parse_record_line函数测试通过！`

```bash
make test
```

结果：通过，`judge/control/csv_store/output/input_cli` 五组测试均通过。

```bash
git diff --check
```

结果：无输出。

## 8. Clean Baseline

以下检查无 diff：

```bash
git diff -- tests/test_judge.c tests/test_control.c tests/test_csv_store.c tests/test_output.c
git diff -- include/record.h include/judge.h include/control.h include/csv_store.h include/output.h
git diff -- src/judge.c src/control.c src/csv_store.c src/output.c
git diff -- domain_snapshot
git diff -- exercises/06-projects/1.c
```

本次主线修改集中在：

```text
Makefile
include/input_cli.h
src/input_cli.c
tests/test_input_cli.c
```

## 9. Bug Lab 受控注入

### 9.1 注入目标

训练 `sscanf` 字段数量检查、C 条件表达式、解析合同和测试对行为回归的捕获能力。

### 9.2 注入位置

`src/input_cli.c` 中字段数量判断：

```text
matched != 3
```

### 9.3 注入动作

用户手动将判断误写为赋值：

```text
matched = 3
```

### 9.4 触发命令

```bash
make test_input_cli
```

### 9.5 报错信息或异常行为

触发编译 warning：

```text
warning: using the result of an assignment as a condition without parentheses
note: use '==' to turn this assignment into an equality comparison
```

触发测试失败：

```text
input_parse_record_line有效输入返回值测试未通过！
input_parse_record_line塘口解析测试未通过！
input_parse_record_line温度解析测试未通过！
input_parse_record_line溶氧解析测试未通过！
input_parse_record_line时间戳默认值测试未通过！
make: *** [test_input_cli] Error 5
```

### 9.6 错误类型判断

```text
parse_contract / behavior_regression / C operator misuse
```

### 9.7 修复动作

恢复为字段数量不等于 3 才返回格式错误：

```text
matched != 3
```

### 9.8 回归测试

```bash
make test_input_cli
make test
git diff --check
```

结果：全部通过。

## 10. 今日新增 SRS

```yaml
- question: 为什么 input_parse_record_line 需要 PondRecord *out_record？
  answer: 因为函数需要把解析结果写回调用方提供的结构体对象，返回值则用于表达解析状态。
  category: 指针输出参数

- question: sscanf 的返回值表示什么？
  answer: 表示成功匹配并赋值的字段数量，必须检查它是否等于预期字段数。
  category: sscanf

- question: 为什么字段数量不足或尾部有脏数据都应返回 FORMAT？
  answer: 因为整行输入没有满足 parser 合同，后续模块不能安全使用半成品或含垃圾的数据。
  category: 输入解析

- question: 为什么 input_cli 不应该调用 judge/control？
  answer: input_cli 是输入解析层，只负责 text line -> PondRecord，不拥有判断或控制逻辑。
  category: 模块边界

- question: if (matched = 3) 为什么危险？
  answer: 这是赋值不是比较，表达式值为 3，条件恒真，会破坏字段数量判断。
  category: Bug 归因

- question: 解析失败和领域非法有什么区别？
  answer: 解析失败是文本格式无法转成结构体；领域非法是数值虽然能解析，但是否合理需要领域规则或校验层判断。
  category: 领域证据
```

## 11. 408 映射

```yaml
data_structure:
  - chapter: "第1章 绪论"
    section: "数据结构的基本概念"
    point: "数据对象、数据元素、数据结构"
    project_mapping: "input_cli 将文本字段解析为 PondRecord 这一结构化数据对象。"

  - chapter: "第1章 绪论"
    section: "抽象数据类型"
    point: "基本操作"
    project_mapping: "input_parse_record_line(line, out_record) 可视作对 PondRecord 的构造/赋值操作。"

computer_organization:
  - chapter: "第1章 计算机系统概述"
    section: "计算机系统层次结构"
    point: "高级语言程序到可执行程序的转换"
    project_mapping: "Makefile 调用 gcc，把 src/input_cli.c 和 tests/test_input_cli.c 构建成 build/test_input_cli。"

  - chapter: "第2章 数据的表示和运算"
    section: "字符与字符串表示"
    point: "文本数字到二进制数值表示"
    project_mapping: "输入文本中的 temp/oxygen 被解析为 PondRecord 中的 float 字段。"
    note: "王道版本小节需手动校准。"

operating_system:
  - chapter: "第1章 计算机系统概述"
    section: "操作系统的概念、功能和目标"
    point: "程序执行"
    project_mapping: "make 启动 gcc 和测试程序进程。"

computer_network:
  - chapter: "不涉及"
    section: "不涉及"
    point: "今日无网络通信"
```

## 12. Git 提交

建议白名单：

```bash
git add codex/daily_packets/007-input-cli-module-v0.md
git add include/input_cli.h src/input_cli.c tests/test_input_cli.c Makefile
git add docs/daily/2026-05-30-input-cli-module-v0.md
git add bug_lab/2026-05-30-input-cli-parse-contract.md
```

建议 commit message：

```bash
git commit -m "feat: complete guided input cli loop"
```

注意：`build/` 不提交；`codex/daily_packets/005-csv-header-once-v0.md` 属于 005 路径整理支线，本次提交前需要单独决定是否纳入。

## 13. 明日建议

- 先复习 `const char *line`、`PondRecord *out_record`、`sscanf` 返回值和 `matched != 3`。
- 再做 007 Clean Baseline 和 git 白名单提交。
- 下一主线建议进入 `main` 串联前的规划：`input_cli -> judge -> control -> output -> csv_store`。
