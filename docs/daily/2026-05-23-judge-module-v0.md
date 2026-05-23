# 2026-05-23 Judge Module v0

## 1. 今日主线

从 `exercises/06-projects/1.c` 的单文件参考版中拆出 `judge` 模块 v0。

本次只做：

- `record.h`：定义 `PondRecord` 数据结构。
- `judge.h`：声明 `temp_status()`、`oxygen_status()`、`needs_aeration()`。
- `judge.c`：实现 judge 判断逻辑。
- `test_judge.c`：验证 judge 模块行为。

本次不做：

- 不新增温度阈值。
- 不修改 `domain_snapshot`。
- 不拆 `control / csv_store / input_cli / output / main`。
- 不破坏 `exercises/06-projects/1.c` 单文件参考版。

## 2. SRS 结果

今日重点校准：

- `.h` 是接口合同，`.c` 是实现。
- `include guard` 在预处理阶段生效，防止同一头文件在同一 `.c` 预处理过程中重复展开。
- `oxygen_status(float oxygen)` 接收 `float`。
- `needs_aeration(PondRecord record)` 接收整条记录。
- `record.temp` 是结构体字段，`temperature` 只是函数形参名。

结果：通过，适合继续 Stage 2 多文件模块化。

## 3. 领域证据

- `oxygen_status()` / `needs_aeration()` 使用 `WQ-DO-GROWOUT-001 / SRC-001`。
- `temp_status()` 保留 legacy behavior，仍标记为 `needs_human_verification`。

## 4. 工程修改

修改文件：

- `include/record.h`
- `include/judge.h`
- `src/judge.c`
- `tests/test_judge.c`

核心变化：

- 建立 `record.h -> judge.h -> judge.c -> test_judge.c` 的第一个多文件模块闭环。
- `judge.h` 只放函数声明，不放函数体。
- `judge.c` 内部保留判断阈值宏，避免污染 `record.h`。
- `test_judge.c` 覆盖低氧、边界值、正常氧和温度 legacy 行为。

本次没有修改 `exercises/06-projects/1.c`。

## 5. 测试与验证

验证结果：

```text
测试通过！
```

覆盖行为：

- oxygen `4.0` -> `low`，`needs_aeration == 1`
- oxygen `5.0` -> `normal`，`needs_aeration == 0`
- oxygen `6.0` -> `normal`，`needs_aeration == 0`
- temp `24.0` -> `low`
- temp `25.0` -> `normal`
- temp `29.0` -> `high`

## 6. Bug Lab

今日 bug：有。

核心 bug：

```text
函数参数合同混用：oxygen_status 需要 float，但 needs_aeration 需要 PondRecord。
```

Bug Lab 文件：

```text
bug_lab/2026-05-23-judge-parameter-contract.md
```

## 7. 408 映射

- 数据结构：结构体字段、结构体作为函数参数。
- 计算机组成原理：编译产物、目标文件、链接前后边界。
- 操作系统：无直接新增。
- 计算机网络：不涉及。
- 软件工程：模块边界、接口合同、最小单元测试。

## 8. 明日建议

下一步继续 Stage 2，但不要急着拆大系统。

推荐顺序：

1. 用 Bug Lab 复盘 `float` vs `PondRecord` 参数合同。
2. 把 `judge` 模块接回一个最小 `main` 或 Makefile 目标。
3. 再考虑拆 `control` 模块。
