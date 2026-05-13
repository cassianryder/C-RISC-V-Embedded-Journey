# Project Goal: Multi-Pond Aquaculture Decision Support

> 长期记忆文件。用于让 Codex / GPT Project 在后续启发带练中始终围绕同一个项目目标推进。

## 0. 总目标

用 C 实现一个面向日本沼虾多塘口养殖的辅助决策系统。

系统第一版只做命令行、CSV、阈值判断、多塘口结构；后续逐步接入 Milk-V Duo S、传感器、继电器、边缘端、数据可视化、机器学习与专家经验数字化。

项目定位不是完全替代人类，而是辅助人类养殖专家决策：把传感器数据、人工记录、专家判断和设备控制动作沉淀成可追踪、可复盘、可训练的数据系统。

## 1. 项目层级定位 L1-L9

- L1：C 基础闭环。输入、输出、函数、结构体、文件保存。
- L2：单文件 CLI 小系统。用一个 `.c` 文件表达真实业务闭环。
- L3：多文件模块化。拆分 `record / input / judge / output / csv_store / main`。
- L4：多塘口数据模型。支持 1-10 个或更多塘口。
- L5：采样任务调度。不同变量使用不同采样周期，降低机器负担。
- L6：设备控制抽象。增氧机、水泵、继电器、手动/自动/定时模式。
- L7：嵌入式接入。Milk-V Duo S、传感器、GPIO、UART、I2C/ADC。
- L8：边缘端与外部数据。天气 API、串口/MQTT/API、本地缓存。
- L9：智能决策与论文方向。专家经验数字化、趋势预测、视觉分析、可解释建议。

当前主线处在 L2 -> L4 的过渡：已经有单文件 CLI + CSV + 阈值判断，下一步要走向多塘口结构和模块化。

## 2. 现实水产系统拆解

- 核心对象：塘口、一次采样记录、传感器、采样任务、执行设备、人工事件、专家决策。
- 核心变量：溶氧、氨氮、水温、pH、亚硝酸盐、天气、饲料投喂量、用药记录、人工观察、专家建议。
- 核心事件：采样、阈值异常、投喂、用药、观察、专家判断、设备开启/关闭、天气变化。
- 控制动作：提醒开增氧机、提醒开水泵、记录人工处理、后期控制继电器执行动作。

## 3. C 数据结构设计

### Pond

表示一个塘口的稳定身份信息。

- `pond_id`：塘口编号。
- `name`：塘口名称。
- `area`：面积。
- `depth`：水深。
- `enabled`：是否启用。

### PondRecord

表示某个塘口在某个时间点的一次状态记录。

- `timestamp`：采样时间。
- `pond_id`：关联塘口。
- `dissolved_oxygen`：溶氧。
- `ammonia`：氨氮。
- `temperature`：水温。
- `ph`：pH。
- `nitrite`：亚硝酸盐。
- `feed_amount`：投喂量。
- `medicine_note`：用药记录。
- `observation_note`：人工观察。
- `source`：数据来源，传感器或人工。

### Sensor

表示一个传感器或一个模拟传感器入口。

- `sensor_id`：传感器编号。
- `pond_id`：绑定塘口。
- `type`：检测变量类型。
- `sample_interval_sec`：采样间隔。
- `enabled`：是否启用。
- `last_sample_time`：上次采样时间。

### SamplingTask

表示一条采样任务规则。

- `task_id`：任务编号。
- `pond_id`：塘口编号。
- `variable_type`：变量类型。
- `interval_sec`：周期。
- `last_run_time`：上次执行时间。
- `priority`：优先级。

### Actuator

表示一个可控制设备。

- `actuator_id`：设备编号。
- `pond_id`：绑定塘口。
- `type`：增氧机、水泵等。
- `mode`：手动、自动、定时。
- `state`：开或关。
- `last_action_time`：最近动作时间。

### ExpertDecision

表示一次专家建议或人工经验记录。

- `decision_id`：建议编号。
- `timestamp`：记录时间。
- `pond_id`：塘口编号。
- `observation`：专家观察。
- `suggestion`：专家建议。
- `action_taken`：是否执行、执行了什么。
- `result_note`：后续结果，用于训练经验闭环。

## 4. 文件模块设计

- `include/`：头文件，放结构体声明、枚举、函数声明。
- `src/`：源码文件，放输入、判断、存储、控制、主流程。
- `data/`：CSV 数据输出。
- `docs/`：架构、硬件计划、协议、实验记录。
- `notes/`：学习日志、问题沉淀、当前进度、项目目标。

建议模块：

- `include/pond.h`：塘口和记录数据结构。
- `include/sensor.h`：传感器与采样任务结构。
- `include/actuator.h`：设备控制结构。
- `include/judge.h`：阈值判断接口。
- `include/csv_store.h`：CSV 保存接口。
- `src/main.c`：主逻辑骨架。
- `src/input_cli.c`：命令行输入。
- `src/judge.c`：阈值判断。
- `src/csv_store.c`：CSV 写入。
- `src/control.c`：控制决策。
- `src/scheduler.c`：采样周期调度。

## 5. 第一版 MVP 范围

只做：

- C 命令行输入。
- 支持多个塘口。
- 记录核心水质变量。
- 阈值判断和终端告警。
- 写入 CSV。
- 保持模块化，便于后续替换为真实传感器和板子。

暂时不做：

- 复杂 AI。
- 云平台。
- 手机 APP。
- 真实传感器驱动。
- 继电器实际控制。
- 摄像头和视觉模型。
- 完整天气 API 接入。

## 6. 采样周期设计

高频变量：

- 溶氧：建议高频，夜间和阴雨天气更关键。
- 水温：中高频，影响代谢、溶氧和氨氮毒性。

低频变量：

- pH：变化通常比溶氧慢，可以中低频。
- 氨氮：可低频或人工检测，异常时提高频率。
- 亚硝酸盐：可低频或人工检测，异常时提高频率。

事件触发变量：

- 天气：天气变化、降雨、闷热、台风前后触发。
- 饲料投喂量：每次投喂后记录。
- 用药记录：每次用药后记录。
- 人工观察：发现异常时记录。
- 专家建议：专家判断或复盘时记录。

第一版可以先不实现真实定时器，只把“采样周期”设计进结构体和文档。

## 7. 存储设计

### pond_records.csv

保存水质与人工记录。

推荐字段：`timestamp,pond_id,temperature,dissolved_oxygen,ph,ammonia,nitrite,feed_amount,medicine_note,observation_note,source`

### control_log.csv

保存设备控制与告警动作。

推荐字段：`timestamp,pond_id,actuator_type,mode,action,reason,result`

### expert_decisions.csv

保存专家经验。

推荐字段：`timestamp,pond_id,observation,suggestion,action_taken,result_note`

## 8. 阈值报警逻辑

注意：以下是工程占位规则，不是最终养殖标准；真实阈值需要结合父亲经验、养殖场实际数据和传感器校准。

- 溶氧：低于安全阈值时提醒增氧；低于危险阈值时进入紧急告警。
- 水温：低温或高温都需要提示，因为会影响虾活动、摄食和溶氧。
- pH：过低或过高都告警，后续应结合氨氮毒性判断。
- 氨氮：超过阈值时提示换水、减料、检查水质。
- 亚硝酸盐：超过阈值时提示风险，需要和溶氧、pH、近期投喂量一起看。

第一版只做单变量阈值；第二版再做组合规则，例如“高温 + 低溶氧 + 阴雨天气”提高风险等级。

## 9. 控制模式设计

- 手动模式：人类直接决定是否开启设备，系统只记录。
- 自动模式：系统根据阈值和规则建议动作，后期可控制继电器。
- 定时模式：根据时间段执行，例如夜间定时增氧。

安全优先级：

- 人工强制控制优先。
- 危险告警优先。
- 定时规则其次。
- 普通自动规则最后。

## 10. 第一版建议创建哪些文件

第一版最小模块化文件：

- `include/pond.h`
- `include/judge.h`
- `include/csv_store.h`
- `src/main.c`
- `src/input_cli.c`
- `src/judge.c`
- `src/csv_store.c`
- `data/pond_records.csv`
- `data/control_log.csv`
- `data/expert_decisions.csv`
- `docs/mvp_design.md`

## 11. 第一版编译和运行方式

建议保留简单 Makefile：

- `make`：编译 CLI 程序。
- `make run`：运行程序。
- `make clean`：清理二进制文件。

运行流程：

1. 选择塘口。
2. 输入或模拟一条采样记录。
3. 系统判断阈值状态。
4. 终端输出人类可读提示。
5. 追加写入 CSV。
6. 如果触发控制建议，写入 `control_log.csv`。

## 12. 完成后的学习反馈

涉及的 C 知识点：

- 结构体与真实业务对象建模。
- 枚举表示状态、变量类型和控制模式。
- 数组管理多个塘口。
- 指针参数修改外部对象。
- 字符串、字符数组和字符串字面量。
- 文件 I/O：`FILE *`、`fopen`、`fprintf`、`fclose`。
- 模块化：`.h / .c`、函数声明、编译链接。
- Makefile 和命令行编译。
- 时间戳与采样周期设计。

C Primer Plus 建议回看：

- 函数。
- 数组和指针。
- 字符串和字符函数。
- 结构体、联合、枚举。
- 文件输入输出。
- 预处理器和多文件程序。

下一步最小闭环：

把当前单文件 `exercises/06-projects/1.c` 的经验升级为“多塘口 MVP 骨架”：

1. 先只支持 2-3 个塘口。
2. 每个塘口输入一条记录。
3. 判断溶氧和水温。
4. 输出告警。
5. 保存到 `pond_records.csv`。
6. 若需要增氧，写入 `control_log.csv`。

后续启发带练必须围绕这个项目目标推进：先建模，再映射到 C 数据结构，再写 main 骨架，再补函数骨架，再实现最小功能，再测试，再沉淀问题。
