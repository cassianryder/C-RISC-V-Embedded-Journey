# Domain Evidence Gap List

## GAP-001：Growout temperature status thresholds

### 触发位置

- `exercises/06-projects/1.c`
- future `src/judge.c`
- function: `temp_status()`

### 当前问题

当前代码中存在温度状态判断逻辑，但 `domain_snapshot` 中没有找到可直接用于成虾 growout 阶段 `temp_status()` 的温度上下限判断卡。

### 已查证据

1. 广义养殖适温：19–24°C，但非特指成虾 growout。
2. 水温低于 10°C 时，一般采用虾拖网集中捕捞，但这是捕捞操作，不是日常状态判断阈值。
3. 夏秋高温季节每天后半夜至天亮开机，但这是增氧机使用时段建议，不是温度风险阈值。
4. 苗种、亲虾、运输、幼体阶段温度证据不能直接迁移到成虾 growout 阶段。

### 当前结论

```text
status: needs_human_verification
can_enter_code: false
