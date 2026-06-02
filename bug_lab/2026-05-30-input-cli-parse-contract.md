# 2026-05-30 Input CLI Parse Contract Bug Lab

实际执行时间：2026-06-02

## 1. Bug 注入目标

训练 `sscanf` 字段数量检查、输入解析合同、C 条件表达式和测试回归捕获能力。

核心问题：

```text
解析函数不能只看 sscanf 是否运行，而必须检查成功解析并赋值的字段数量。
```

## 2. 注入位置

`src/input_cli.c` 的字段数量判断：

```text
matched != 3
```

## 3. 注入前 Clean Baseline

```bash
make test_input_cli
```

结果：通过。

```bash
make test
```

结果：`judge/control/csv_store/output/input_cli` 五组测试均通过。

```bash
git diff --check
```

结果：无输出。

以下路径无 diff：

```bash
tests/test_judge.c tests/test_control.c tests/test_csv_store.c tests/test_output.c
include/record.h include/judge.h include/control.h include/csv_store.h include/output.h
src/judge.c src/control.c src/csv_store.c src/output.c
domain_snapshot
exercises/06-projects/1.c
```

## 4. 注入动作

用户手动将字段数量判断误写为赋值表达式：

```text
matched = 3
```

这会把 `3` 赋值给 `matched`，并让 `if` 条件恒真。

## 5. 触发命令

```bash
make test_input_cli
```

## 6. 报错信息或异常行为

真实触发结果：

```text
gcc -Wall -g -Wextra -Iinclude src/input_cli.c tests/test_input_cli.c -o build/test_input_cli
src/input_cli.c:20:15: warning: using the result of an assignment as a condition without parentheses [-Wparentheses]
   20 |   if (matched = 3)
      |       ~~~~~~~~^~~
src/input_cli.c:20:15: note: use '==' to turn this assignment into an equality comparison
./build/test_input_cli
input_parse_record_line有效输入返回值测试未通过！
input_parse_record_line塘口解析测试未通过！
input_parse_record_line温度解析测试未通过！
input_parse_record_line溶氧解析测试未通过！
input_parse_record_line时间戳默认值测试未通过！
make: *** [test_input_cli] Error 5
```

## 7. 错误类型判断

```text
parse_contract / behavior_regression / C operator misuse
```

不是链接错误，也不是领域规则错误；它是 C 运算符误用导致的解析合同破坏。

## 8. 根因分析

`=` 是赋值运算符，不是比较运算符。

当代码写成：

```text
if (matched = 3)
```

实际发生的是：

```text
1. 把 3 赋值给 matched。
2. 整个表达式的值为 3。
3. C 中非 0 为真。
4. if 条件恒真。
```

因此，即使有效输入成功解析，函数也会提前返回 `INPUT_PARSE_ERR_FORMAT`，导致 `out_record` 没有被写入，测试中的有效输入与字段检查全部失败。

## 9. 修复动作

恢复字段数量合同：

```text
只有 matched 不等于 3 时，才返回 INPUT_PARSE_ERR_FORMAT。
```

修复后的语义：

```text
matched == 3  表示整行输入符合 pond_id,temp,oxygen 合同。
matched != 3  表示字段缺失、字段非法、多余字段或尾部脏数据。
```

## 10. 回归测试

```bash
make test_input_cli
```

结果：通过，输出 `input_parse_record_line函数测试通过！`

```bash
make test
```

结果：通过，五组测试均通过。

```bash
git diff --check
```

结果：无输出。

## 11. 防复发规则

- 解析函数必须检查 `sscanf` 返回的字段数量。
- 条件判断中必须区分 `=`、`==`、`!=`。
- 保留 `-Wall -Wextra`，把编译 warning 当作高优先级信号。
- 测试必须覆盖 valid input，避免“错误路径恒真”误伤主路径。
- 生产 parser 不能接受字段缺失、多余字段或尾部脏数据。

## 12. SRS 卡片

```yaml
- question: sscanf 的返回值表示什么？
  answer: 表示成功匹配并赋值的字段数量，解析函数必须检查它是否等于预期字段数。

- question: if (matched = 3) 为什么会让条件恒真？
  answer: 因为 = 是赋值，表达式值为 3；C 中非 0 为真。

- question: 为什么 valid input 测试能抓住 parse contract bug？
  answer: 因为条件恒真会让有效输入也提前返回 FORMAT，导致返回值和字段写入都失败。

- question: 为什么 parser 要拒绝尾部脏数据？
  answer: 因为整行输入必须符合合同，不能只接受前缀有效而忽略后续垃圾。
```

## 13. 408 映射

- 数据结构：文本字段映射为结构体字段，体现数据对象的构造。
- 计算机组成原理：字符序列到 float/char 的数据表示转换。
- 操作系统：`make` 启动编译和测试进程，warning 和返回码形成工程反馈。
- 计算机网络：今日无网络通信；但 parser 结构可复用于后续 UART/socket/MQTT payload 解析。

## 14. 是否进入长期问题库

是。

原因：该问题连接 `sscanf`、C 运算符、输入解析合同、测试设计和生产级 parser 边界，是后续所有输入协议解析的核心基础。
