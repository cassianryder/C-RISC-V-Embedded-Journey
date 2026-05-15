# 2026-05-15 控制日志与函数合同

## 我今天遇到的问题

### 1. 函数名和结构体实参混淆

问题：

我写出 `aerator_should_be_on(reason)`，把 `reason` 函数名当成了一条 `PondRecord` 记录传入。

解答：

`aerator_should_be_on(PondRecord record)` 需要的是结构体记录；`reason` 是函数入口，不是数据对象。函数调用时，实参类型必须匹配形参类型。

闭环：

遇到 `passing 'const char *(PondRecord)' to parameter of incompatible type 'PondRecord'` 时，先回看函数签名，再判断传入的是对象、地址、成员值还是函数名。

使用场景和启发思考：

后续接传感器、继电器或多文件模块时，函数合同会越来越重要；类型错误往往暴露的是“职责对象没分清”。

### 2. 保存函数和主流程错误处理边界混淆

问题：

我把 `failed to save control log` 的错误处理写进了 `save_control_log_csv()` 内部，并和控制判断混在一起。

解答：

`save_control_log_csv(record)` 只负责尝试保存并返回 `1/0`；`main()` 负责调用它，并在失败时打印错误。控制决策的 `ON/OFF` 不是保存成功/失败。

闭环：

保存函数内部只保留：打开文件、写 header、写记录、关闭文件、返回状态。错误提示放在 `main()` 的 `if (!save_control_log_csv(record))` 中。

使用场景和启发思考：

这是所有工程接口的基本边界：被调函数返回状态，主流程决定如何处理失败。

### 3. 告警日志和控制日志边界不清

问题：

我需要区分 `oxygen_alert.csv` 和 `control_log.csv` 到底分别记录什么，以及正常氧时是否要记录 `OFF`。

解答：

`oxygen_alert.csv` 记录水质异常事件，只记录低氧；`control_log.csv` 记录设备动作建议，`ON` 和 `OFF` 都是控制决策证据。

闭环：

低氧 A 应写入告警日志和控制日志；正常氧 B 不写告警日志，但写入控制日志的 `OFF / NORMAL_OXYGEN`。

使用场景和启发思考：

真实养殖系统不仅要知道“异常发生过”，还要知道“系统当时建议设备怎么做”，这会成为后续专家复盘和自动控制的证据链。

## 补充的高质量问题

### 1. CSV 字段值和函数命名需要逐步工程化

问题：

`"ACTUATOR"`、`action()`、`reason()` 当前能跑，但语义还不够精确。

解答：

当前设备具体是增氧机，字段值后续更适合写 `"AERATOR"`；辅助函数后续可收敛为 `aerator_action()`、`aerator_reason()`，避免多设备时含义变模糊。

闭环：

今天不继续改名，避免过度设计；下一次收口可只做字段名和表头风格整理。

使用场景和启发思考：

命名不是装饰，而是系统接口。越接近多设备、多塘口，命名越要表达真实对象。
