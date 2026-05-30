# 2026-05-29 Output Buffer Truncation Bug Lab

实际执行时间：2026-05-30

## 1. Bug 注入目标

训练 C 字符串缓冲区和 `snprintf` 截断检测能力。

核心问题：

```text
snprintf 可以防止越界写入，但如果不检查返回值，调用者可能把被截断的半截字符串误认为完整输出。
```

## 2. 注入位置

`src/output.c` 的 `snprintf` 返回值判断：

```text
written >= buffer_size
```

## 3. 注入前 Clean Baseline

```bash
make test_output
```

结果：通过。

```bash
make test
```

结果：`judge/control/csv_store/output` 四组测试均通过。

```bash
git diff --check
```

结果：无输出。

```bash
git diff -- domain_snapshot
```

结果：无输出。

## 4. 注入动作

临时去掉或破坏 small buffer 截断判断，使函数在输出被截断时不再返回 `BUFFER_TOO_SMALL`。

被破坏的工程合同：

```text
当 snprintf 返回值 >= buffer_size 时，函数必须报告 buffer 太小。
```

## 5. 触发命令

```bash
make test_output
```

## 6. 报错信息或异常行为

真实触发结果：

```text
gcc -Wall -g -Wextra -Iinclude src/output.c tests/test_output.c -o build/test_output
./build/test_output
output_format_record_line函数测试未通过！
make: *** [test_output] Error 1
```

## 7. 错误类型判断

```text
string_buffer / behavior_regression
```

不是编译错误，也不是链接错误；程序可以编译运行，但行为不符合 output 模块合同。

## 8. 根因分析

`small_buffer` 只有很小的空间，无法容纳完整输出：

```text
pond=A temp=23.60 oxygen=4.50 aerate=on
```

`snprintf` 会根据 `buffer_size` 限制写入，避免越界；但它的返回值表示“如果空间足够，本来需要写入的字符数”。

因此：

```text
written >= buffer_size
```

说明输出发生截断。

如果不检查这个条件，函数会继续返回成功，调用者会误以为 buffer 中保存的是完整文本。

## 9. 修复动作

恢复截断检测：

```text
如果 written >= buffer_size，返回 BUFFER_TOO_SMALL。
```

修复后的语义：

```text
snprintf 负责内存安全；
返回值检查负责语义完整性。
```

## 10. 回归测试

```bash
make test_output
```

结果：通过，输出 `output_format_record_line函数测试通过！`

```bash
make test
```

结果：通过，四组测试均通过。

```bash
git diff --check
```

结果：无输出。

## 11. 防复发规则

凡是写入调用方 `char buffer[]` 的函数，都必须同时检查：

- buffer 容量参数是否传入。
- 写入函数是否限制最大长度。
- `snprintf` 返回值是否小于 0。
- `snprintf` 返回值是否大于等于 `buffer_size`。

## 12. SRS 卡片

```yaml
- question: snprintf 为什么仍然需要检查返回值？
  answer: 因为 snprintf 只保证不越界写入；返回值检查用于判断输出是否被截断。

- question: written >= buffer_size 表示什么？
  answer: 表示如果空间足够本来要写入的字符数不小于 buffer 容量，实际输出发生截断。

- question: small buffer 测试在保护什么？
  answer: 保护 output 模块不要把半截字符串当成完整格式化结果。
```

## 13. 408 映射

- 数据结构：字符数组作为连续存储空间。
- 计算机组成原理：字符以字节形式存储，字符串以 `'\0'` 结束。
- 操作系统：用户态程序需要保护自己的缓冲区边界。
- 计算机网络：应用层协议消息通常先在 buffer 中构造，再发往 socket/UART/MQTT。

## 14. 是否进入长期问题库

是。

原因：该问题连接 C 字符串、缓冲区安全、模块合同、测试设计和后续协议输出，是 output 模块后续复用的关键基础。
