# 2026-05-23 Problems: Judge Parameter Contract

## 今日主线
- 从 `exercises/06-projects/1.c` 单文件参考版中拆出 judge 模块 v0。
- 建立 `record.h -> judge.h -> judge.c -> test_judge.c` 的第一个多文件模块闭环。
- 今天的问题重点不是判断规则本身，而是函数接口合同：一个函数到底要“单个字段”，还是“一整条记录”。

## 1. 每日问题

### 问题 1
- 为什么 `oxygen_status(record)` 错，而 `oxygen_status(record.oxygen)` 对？

### 解答
- `oxygen_status()` 的声明是：

```c
const char *oxygen_status(float oxygen);
```

- 它的形参类型是 `float`，说明它只需要一个溶氧数值。
- `record` 是 `PondRecord` 类型，代表一整条池塘记录。
- `record.oxygen` 才是结构体里的溶氧字段，类型是 `float`。
- 所以 `oxygen_status(record)` 是把整条记录传给了只要一个字段的函数，参数合同不匹配。

### 闭环
- 修复写法：

```c
oxygen_status(record.oxygen)
```

- 测试已覆盖：
  - `oxygen 4.0 -> low`
  - `oxygen 5.0 -> normal`
  - `oxygen 6.0 -> normal`

### 使用场景和启发思考
- 场景：状态判断函数、字段级判断、传感器单值判断。
- 启发：函数名只能给语义提示，真正的调用边界要看函数声明里的参数类型。

---

### 问题 2
- 为什么 `needs_aeration(record.oxygen)` 错，而 `needs_aeration(record)` 对？

### 解答
- `needs_aeration()` 的声明是：

```c
int needs_aeration(PondRecord record);
```

- 它的形参类型是 `PondRecord`，说明它要的是一整条池塘记录。
- 即使当前实现主要看 `record.oxygen`，接口仍然设计成接收整条记录。
- 这样后续如果增氧判断需要加入塘口、时间、温度、设备状态或人工策略，不需要立刻改函数接口。
- `record.oxygen` 只是一个 `float` 字段，不能代替整条记录。

### 闭环
- 修复写法：

```c
needs_aeration(record)
```

- 测试已覆盖：
  - `oxygen 4.0 -> needs_aeration == 1`
  - `oxygen 5.0 -> needs_aeration == 0`
  - `oxygen 6.0 -> needs_aeration == 0`

### 使用场景和启发思考
- 场景：业务级判断、控制决策、后续 control 模块。
- 启发：字段级函数适合接收字段；业务级函数更适合接收记录对象。

---

### 问题 3
- `.h` 和 `.c` 的边界是什么？为什么 `judge.h` 只放声明，不放函数体？

### 解答
- `.h` 是接口合同，告诉其他文件“这个模块提供什么类型和函数”。
- `.c` 是实现，负责具体判断逻辑。
- `judge.h` 放：

```c
const char *temp_status(float temperature);
const char *oxygen_status(float oxygen);
int needs_aeration(PondRecord record);
```

- `judge.c` 放这些函数的具体实现。
- 这样测试文件和未来的 `main.c` 只依赖接口，不需要知道内部阈值宏和实现细节。

### 闭环
- 今日完成：
  - `include/record.h`
  - `include/judge.h`
  - `src/judge.c`
  - `tests/test_judge.c`

### 使用场景和启发思考
- 场景：多文件模块化、单元测试、后续拆 control / csv_store / input_cli。
- 启发：头文件不是“复制一份代码”，而是模块之间的合同。

---

### 问题 4
- include guard 到底在防什么？为什么它发生在预处理阶段？

### 解答
- include guard 用来防止同一个头文件在同一个 `.c` 文件的预处理过程中被重复展开。
- 典型形式是：

```c
#ifndef JUDGE_H
#define JUDGE_H

/* declarations */

#endif
```

- 预处理器在真正编译前处理 `#include`、`#define`、`#ifndef`。
- 如果同一个头文件被多条 include 路径重复包含，include guard 会让第二次展开被跳过。

### 闭环
- 今日结论：include guard 不解决链接问题，它先解决“同一翻译单元里重复声明/定义风险”的预处理问题。

### 使用场景和启发思考
- 场景：`record.h` 被 `judge.h`、`judge.c`、`test_judge.c` 间接或直接引用。
- 启发：多文件 C 项目里，头文件保护是最低限度的工程卫生。

---

### 问题 5
- 为什么 `temp_status()` 仍保留 legacy behavior，并标记 `needs_human_verification`？

### 解答
- 今日模块拆分重点是 judge 模块边界，不是重新校准温度领域阈值。
- `oxygen_status()` / `needs_aeration()` 已有领域证据 `WQ-DO-GROWOUT-001 / SRC-001` 支撑。
- `temp_status()` 当前只是保留原行为：
  - `24.0 -> low`
  - `25.0 -> normal`
  - `29.0 -> high`
- 因为温度阈值尚未做同等强度的领域校准，所以继续标记为需要人工验证。

### 闭环
- 今日测试保留温度 legacy 行为，但不新增温度阈值。
- 后续如果要修改温度阈值，应先补领域证据，再改 judge 实现和测试。

### 使用场景和启发思考
- 场景：领域规则、阈值判断、需求证据管理。
- 启发：工程上能跑的规则，不等于领域上已被验证的规则。

## 2. 今日闭环总结
- 今天真正打通的是第一个多文件 judge 模块闭环。
- 最重要的 bug 是函数参数合同混用：`float` 字段和 `PondRecord` 整条记录不能凭语义直觉互换。
- 修复方式不是“记住某一行”，而是养成调用函数前先读声明的习惯。

## 3. 408 映射
- 数据结构：结构体字段访问、结构体作为函数参数。
- 计算机组成原理：编译产物、目标文件、链接前后边界。
- 编译原理：语义分析阶段检查函数实参与形参类型是否匹配。
- 软件工程：接口合同、模块边界、最小单元测试。

## 4. 明日自然衔接
- 先用 Bug Lab 复盘 `float` vs `PondRecord` 参数合同。
- 把 judge 模块接回一个最小 `main` 或 `Makefile` 目标。
- 再考虑拆 `control` 模块，避免一次性拆大系统。
