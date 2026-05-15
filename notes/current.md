# Current Snapshot

> 每天覆写一次，不做历史累积；供 GPT Project / Codex 读取当前主线进度、问题与自然衔接。

- 当前长期目标：用 C 实现一个日本沼虾多塘口养殖辅助决策系统，先做命令行 + CSV + 阈值判断 + 多塘口结构，后续接 Milk-V Duo S、传感器、继电器、边缘端和机器学习。

- 当前主线文件：`exercises/06-projects/1.c`

- 当前主攻知识点：`多塘口建模`、`结构体数组`、`阈值判断`、`CSV 持久化`、`控制日志`、`模块化前置设计`

- 最近已完成：
  - 跑通 `PondRecord -> 输入 -> 时间戳 -> 状态判断 -> 终端输出 -> CSV 追加保存`。
  - 理解 `FILE *fp`、`fseek/ftell`、CSV 表头只写一次、字符串字面量等存储层基础。
  - 初步把系统动力学方法映射到 C：要素 -> 结构体字段，关系 -> 函数，反馈 -> 判断与控制日志，记忆 -> CSV。
  - 新版项目目标已沉淀到 `notes/project_goal.md`。
  - 完成 `aerator_should_be_on()`，把“低氧判断”从水质层转接到设备控制层，并将 `oxygen_alert.csv` 纳入 `.gitignore`。
  - 完成 `control_log.csv` 最小闭环：低氧记录 `ON / LOW_OXYGEN`，正常氧记录 `OFF / NORMAL_OXYGEN`。

- 当前未解决问题：
  - 当前 `1.c` 仍是单文件学习闭环，下一步需要升级为多塘口 MVP。
  - 还没有拆成 `include/` + `src/` 的正式模块。
  - 天气 API、真实传感器、继电器、手机端、AI 训练暂不进入第一版。
  - `control_log.csv` 已跑通，但表头空格、`ACTUATOR` 字段值和 `action/reason` 命名还可小收口。

- 下一步自然衔接：
  - 先用 SRS 校准 `FILE * / fprintf / fseek / ftell / 指针参数 / 结构体数组`。
  - 主线规划固定使用 7 问流程：现实问题、系统动力学变量、C 映射、代码证据、运行证据、教材补洞、GitHub 沉淀。
  - 项目带练固定遵守最小任务协议：每次只推进一个任务，先给函数签名、伪代码和验收标准，核心代码由用户亲手写，Codex 只做审查、校准和验证；编译命令和测试方法不默认展开，Codex 直接执行并简述结果。
  - 进入多塘口 MVP 前，先用一个短闭环收干净 `control_log.csv` 字段命名和 CSV 表头风格。
  - 主线随后进入“多塘口 MVP 骨架”：支持 2-3 个塘口输入记录、判断溶氧和水温、写入 `pond_records.csv`、`oxygen_alert.csv`、`control_log.csv`。
  - 启发带练继续按“系统动力学建模 -> C 数据结构 -> main 骨架 -> 函数骨架 -> 最小运行 -> 测试 -> 问题沉淀”推进。
