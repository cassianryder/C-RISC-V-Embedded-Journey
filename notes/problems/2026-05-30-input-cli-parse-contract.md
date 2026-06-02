# 2026-05-30 Input CLI Parse Contract

> 实际完成时间：2026-06-02

## 今日主线

今天完成 `Input CLI Module v0`：把固定格式文本行解析为 `PondRecord`。

核心链路：

```text
text line -> input_cli -> PondRecord
```

input_cli 只负责输入解析，不调用 judge/control/csv_store/output，不接 `main`，不新增领域规则。

## 1. 每日问题

### 问题 1：为什么 `input_parse_record_line` 需要 `PondRecord *out_record`？

解析函数有两类结果：

```text
解析状态：成功、空指针、格式错误。
解析产物：PondRecord 字段值。
```

返回值适合表达解析状态；`PondRecord *out_record` 适合把解析出的字段写回调用方提供的结构体对象。

因此这是典型的“返回值 + 指针输出参数”模式：

```text
return：告诉调用方有没有解析成功。
out_record：承载解析成功后的数据。
```

### 问题 2：`sscanf` 的返回值表示什么？

`sscanf` 的返回值不是“有没有运行”，而是：

```text
成功匹配并赋值的字段数量。
```

如果输入合同是：

```text
pond_id,temp,oxygen
```

那么成功解析必须满足：

```text
matched == 3
```

如果 `matched != 3`，说明字段缺失、字段非法或整行输入没有满足 parser 合同。

### 问题 3：为什么 `matched != 3` 是解析合同？

input_cli 的职责是把一整行文本转换为完整的 `PondRecord` 输入字段。

只有 3 个字段都成功匹配并赋值，后续模块才能安全使用这条记录。

因此：

```text
matched == 3：文本符合最小输入合同。
matched != 3：文本不符合输入合同，返回 FORMAT。
```

这不是领域判断；它只判断文本是否能被解析为结构体字段。

### 问题 4：为什么 `if (matched = 3)` 会让条件恒真？

`=` 是赋值运算符，不是比较运算符。

当代码写成：

```c
if (matched = 3)
```

实际发生：

```text
1. 把 3 赋值给 matched。
2. 整个表达式的值为 3。
3. C 中非 0 为真。
4. if 条件恒真。
```

于是有效输入也会走错误路径，导致返回值和字段写入全部失败。

### 问题 5：为什么 valid input 测试能抓住这个 bug？

如果错误路径恒真，有效输入也会被当成格式错误。

因此 valid input 测试会同时暴露：

```text
有效输入返回值不对。
塘口字段未正确写入。
温度字段未正确写入。
溶氧字段未正确写入。
默认时间戳未正确保持。
```

这说明 parser 测试不能只测非法输入，也必须保护主路径。

### 问题 6：为什么 parser 要拒绝尾部脏数据？

生产级 parser 不能只接受“前缀看起来正确”的输入。

例如：

```text
A 23.6 4.5 garbage
```

前三个字段也许能被解析，但整行输入并没有满足合同。

拒绝尾部脏数据可以防止后续模块误用半正确、半污染的输入。

### 问题 7：解析失败和领域非法有什么区别？

解析失败：

```text
文本格式无法转换成结构体字段。
例如字段缺失、类型不匹配、尾部垃圾。
```

领域非法：

```text
文本能成功解析成数值，但这些数值是否合理需要领域规则或校验层判断。
```

input_cli 只负责解析，不负责判断水质是否合理。

## 2. 解答总结

今天的核心是建立输入解析合同：

```text
输入文本必须完整、干净、字段数量正确。
解析成功才写回 PondRecord。
解析状态和解析结果分离。
```

关键模型：

```text
const char *line：只读输入字符串入口。
PondRecord *out_record：输出参数，写回解析结果。
InputParseResult：返回解析状态。
sscanf 返回值：成功赋值字段数。
matched != 3：字段数量合同不满足。
extra：尾部脏数据检测。
```

这一步把 input_cli 从教学解析推进到生产级 parser 样板。

## 3. 闭环

已完成闭环：

- 创建 `include/input_cli.h`。
- 创建 `src/input_cli.c`。
- 创建 `tests/test_input_cli.c`。
- `Makefile` 增加 `test_input_cli` 并纳入 `make test`。
- 从模糊 `int` 返回值优化为 `InputParseResult`。
- 增加 `NULL` 参数检查。
- 检查 `sscanf` 字段数量。
- 拒绝尾部脏数据。
- `make test_input_cli` 通过。
- `make test` 通过。
- `git diff --check` 通过。

Bug Lab：

```text
临时把 matched != 3 误写为 matched = 3。
编译器发出 -Wparentheses warning。
测试运行后 valid input 路径失败。
恢复 matched != 3 后回归通过。
```

错误类型：

```text
parse_contract / behavior_regression / C operator misuse
```

这不是链接错误，也不是领域规则错误，而是 C 运算符误用破坏了解析合同。

## 4. 使用场景和启发思考

input_cli 是项目进入 `main` 串联前的入口层。

它未来可能复用于：

```text
命令行输入。
串口 UART 文本。
socket payload。
MQTT payload。
测试样例输入。
```

因此 parser 必须严格：

```text
字段数量必须正确。
字段类型必须能解析。
尾部不能有脏数据。
解析失败不能写出不可信结构体。
```

这一步为后续 `input_cli -> judge -> control -> output -> csv_store` 主流程打下入口合同。

## 5. 408 映射

数据结构：

- 文本字段被构造为 `PondRecord` 结构化数据对象。
- `input_parse_record_line(line, out_record)` 可视作对结构体对象的构造/赋值操作。

计算机组成原理：

- 文本字符序列被转换为 `char` 和 `float` 字段。
- `=`、`==`、`!=` 在机器执行层面对应不同表达式语义，误用会改变控制流。

操作系统：

- `make` 启动 gcc 和测试程序进程。
- 编译 warning、测试返回码和 make 失败共同构成工程反馈链。

计算机网络：

- 今日无网络通信。
- 但 parser 结构可复用于后续 UART、socket、MQTT payload 解析。

## 6. 明日衔接

下一步先复盘：

- `const char *line`
- `PondRecord *out_record`
- `sscanf` 返回值
- `matched != 3`
- `=` / `==` / `!=`
- 尾部脏数据检测

稳定后再规划 `main` 串联：

```text
input_cli -> judge -> control -> output -> csv_store
```
