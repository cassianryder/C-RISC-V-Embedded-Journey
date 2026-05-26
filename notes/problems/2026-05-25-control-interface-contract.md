# 2026-05-25 Control Interface Contract

> 实际执行时间：2026-05-26

## 今日主线

今天从 `record -> judge` 推进到 `record -> judge -> control`，完成 `Control Module v0` 闭环记录。

control v0 的职责很窄：

```text
接收 PondRecord。
调用 needs_aeration(record)。
返回是否建议增氧。
```

本次不新增领域阈值，不重复写 `oxygen < 5.0f`，不使用 `temp_status()` 作为控制依据，不接硬件。

## 1. 每日问题

### 问题 1：为什么 `control.h` 和 `control.c` 的函数签名必须完全一致？

`control.h` 是接口合同，告诉外部模块：

```c
int control_should_aerate(PondRecord record);
```

`control.c` 是合同实现，必须严格实现同一个函数名、返回值类型和参数列表。

如果实现改成：

```c
int control_should_aerate(float oxygen)
```

同一个函数名就出现了两套合同。编译器无法确认调用方到底应该按 `PondRecord` 还是 `float` 检查类型，所以报：

```text
conflicting types for 'control_should_aerate'
```

### 问题 2：为什么这次是编译错误，不是链接错误？

这次错误发生在 `src/control.c` 内部：编译器读到 `control.h` 的声明后，又看到 `control.c` 里的定义，立刻发现二者冲突。

也就是说，目标文件还没有正常生成，链接器还没有开始解析符号，所以它是：

```text
compile error / interface contract error
```

对比昨日：

```text
Undefined symbols: 声明能看见，但实现文件没参与链接，属于链接错误。
conflicting types: 声明和定义本身冲突，属于编译错误。
```

### 问题 3：为什么还会出现 `use of undeclared identifier 'record'`？

Bug 注入后，函数参数变成了：

```c
int control_should_aerate(float oxygen)
```

但函数体仍然写：

```c
return needs_aeration(record);
```

此时当前函数作用域里没有名为 `record` 的变量，只有 `oxygen`，所以 `record` 变成未声明标识符。

这是连带错误，根因仍然是接口合同被改坏。

### 问题 4：为什么 control 模块不应该自己写 `oxygen < 5.0f`？

因为溶氧阈值判断属于 judge 层和领域证据层。

如果 control 层也写一次：

```c
oxygen < 5.0f
```

领域规则就会分散。以后阈值改变时，judge 和 control 可能出现不一致。

更稳的边界是：

```text
judge: 判断是否低氧。
control: 使用 judge 的判断结果形成控制建议。
hardware: 未来再把建议转成真实设备动作。
```

### 问题 5：为什么 `control_should_aerate(record)` 接收 `PondRecord`，而不是 `float oxygen`？

当前 control v0 内部复用的是：

```c
needs_aeration(record)
```

所以接收整条 `PondRecord` 可以保持接口一致。

这也为后续扩展留空间：未来 control 可能不仅看溶氧，还可能结合人工模式、定时策略、设备状态、塘口信息等字段。

## 2. 解答总结

今天的核心不是“多写一个函数”，而是建立第二个模块的接口合同意识。

最小模型：

```text
record.h: 定义 PondRecord。
judge.h / judge.c: 判断水质状态。
control.h / control.c: 复用 judge 结果，形成控制建议。
test_control.c: 只通过 control 公开接口验证行为。
```

关键区别：

```text
声明和定义冲突：编译错误。
声明存在但实现没参与链接：链接错误。
```

## 3. 闭环

已完成验证：

- `make test_control` 通过。
- `make test` 通过。
- `git diff --check` 通过。
- 临时修改函数签名后触发 `conflicting types`。
- 恢复 `PondRecord record` 后回归通过。

防复发规则：

```text
每新增一个模块，先写 .h 接口合同，再让 .c 严格实现该合同，测试只通过公开接口调用。
```

## 4. 使用场景和启发思考

control 模块是从“判断系统”走向“决策系统”的第一步。

在水产项目里：

```text
judge 说：这条记录是否低氧。
control 说：基于这个判断，是否建议增氧。
hardware 以后再说：真实设备是否执行动作。
```

这个边界能防止项目过早接硬件，也能防止领域规则散落在多个模块里。

## 5. 408 映射

数据结构：

- `PondRecord` 是池塘记录对象，`control_should_aerate(record)` 是对该对象执行控制决策操作。

计算机组成原理：

- 函数签名冲突发生在编译阶段，目标文件尚未正常生成。
- `.h` 提供声明，`.c` 提供定义，二者必须一致。

操作系统：

- shell 执行 `make test`，`make` 启动 `gcc` 和测试程序进程。
- `include/`、`src/`、`tests/`、`build/` 体现 C 工程的文件路径组织。

计算机网络：

- 今日不涉及网络通信。

## 6. 明日衔接

下一步不要急着接硬件。

推荐顺序：

- 复盘 `control.h / control.c / test_control.c` 的接口合同。
- 时间短就做 SRS + Bug Lab 回顾。
- 时间充足再读下一份 daily packet，准备进入 `csv_store` 或 `input_cli`。
