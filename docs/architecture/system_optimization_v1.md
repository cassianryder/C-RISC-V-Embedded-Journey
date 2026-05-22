# System Optimization v1

本文件记录当前系统优化版本。

## 1. 当前判断

项目已经完成 Stage 1 单文件参考版。

Stage 1 已经完成：

```text
结构体记录
CLI 输入
时间戳
温度 / 溶氧状态判断
终端输出
pond_records.csv
oxygen_alert.csv
control_log.csv
needs_aeration()
aerator_should_be_on()
aerator_action()
aerator_reason()
```

当前进入：

```text
Stage 2：多文件模块化
```

当前最重要的训练目标不是新增业务功能，而是：

```text
把已经验证过的单文件业务闭环，拆成可测试、可维护、可解释、可映射 408 的 C 工程模块。
```

## 2. 当前主线

从 `exercises/06-projects/1.c` 中拆出模块。

拆分顺序：

```text
1. judge
2. control
3. csv_store
4. input_cli
5. output
6. main
```

第一个模块：

```text
judge
```

第一个任务包：

```text
codex/daily_packets/001-judge-module-v0.md
```

## 3. 当前仓库新增结构

```text
AGENTS.md
learning/
codex/
docs/
domain_snapshot/
tests/
bug_lab/
mapping/
```

目录作用：

```text
AGENTS.md：三层 Agent 总规则
learning/：C/嵌入式能力图谱与每日规划策略
codex/：Codex 可执行任务包
docs/：架构文档与每日复盘模板
domain_snapshot/：领域知识库只读快照
tests/：测试代码
bug_lab/：真实 bug 沉淀
mapping/：408 / 王道 / 面试表达映射
```

## 4. 最终工具架构

```text
Gemini / NotebookLM
→ GPT Project
→ Codex
→ GitHub
```

## 5. Gemini / NotebookLM 职责

Gemini / NotebookLM 是资料初加工层。

负责：

* 原始资料初加工
* 候选知识点抽取
* 候选 md / yml 生成
* 长文档摘要
* 帮助从 PDF、网页、规范中提取结构化字段

禁止：

* 决定最终领域阈值
* 直接生成可进入代码的养殖规则
* 把猜测写成事实
* 替代 `source_index`
* 替代人工确认

## 6. GPT Project 职责

GPT Project 是知识治理与系统架构层。

负责：

* 知识库治理
* evidence policy
* source index
* domain cards 审核
* Codex 任务包生成
* 408 映射
* SRS 候选卡
* 面试表达材料
* 项目路线维护
* 阶段升级判断

禁止：

* 凭空补领域事实
* 把 hypothesis 升级为正式规则
* 绕过人工确认直接修改领域快照
* 直接替代 Codex 执行代码实现

## 7. Codex 职责

Codex 是工程带练执行层。

负责：

* 工程带练
* C 代码实现引导
* 多文件模块化
* 测试设计
* Bug Lab
* Git commit
* daily loop 复盘
* 从工程任务中抽取 408 映射触发点

禁止：

* 作为领域事实来源
* 新增养殖阈值
* 修改 `domain_snapshot`
* 直接重写整个项目
* 跳过测试
* 跳过复盘
* 跳过 Git 沉淀
* 在用户确认 A/B/C 模式前开始写代码

## 8. 数据流

```text
原始资料 / 父辈经验 / 行业规范
→ Gemini / NotebookLM 初加工
→ GPT Project 审核
→ domain/cards + source_index + evidence_policy
→ domain_snapshot 同步
→ Codex daily packet
→ C 工程实现
→ tests / bug_lab / docs/daily / mapping
→ Git commit
```

## 9. 核心约束

1. 领域事实必须来自 `domain_snapshot/`。
2. Codex 不作为领域事实来源。
3. 当前不接硬件。
4. 当前不扩新养殖功能。
5. 当前优先训练多文件模块化、测试、Bug Lab、Git 沉淀。
6. 未确认资料只能标记为 `hypothesis` 或 `needs_human_verification`。
7. 每个工程闭环都必须能沉淀为 Git 资产。
8. 每个涉及领域判断的代码规则都必须能追溯到 `card_id` 和 `source_id`。
9. 领域同步 commit 不应混入 C 代码修改。
10. C 代码修改 commit 不应悄悄修改领域规则。

## 10. 当前禁止事项

当前阶段禁止：

```text
不接硬件
不做 MQTT / HTTP
不做 AI 预测
不做视觉识别
不新增 pH / 投喂等新业务功能
不重写整个项目
不修改未经确认的领域阈值
不删除 exercises/06-projects/1.c 单文件参考版
不跳过测试
不跳过复盘
```

## 11. 当前推荐任务包

```text
codex/daily_packets/001-judge-module-v0.md
```

目标：

```text
把 temp_status()
把 oxygen_status()
把 needs_aeration()
从 exercises/06-projects/1.c 迁移到 judge 模块。
```

今日不做：

```text
不新增 pH 判断
不新增投喂建议
不新增硬件控制
不新增 MQTT / HTTP
不重写整个项目
不修改领域阈值
```

## 12. Stage 2 模块拆分路线

Stage 2 的目标是把单文件参考版拆成模块化 C 工程。

推荐顺序：

```text
Stage 2-1：judge 模块
Stage 2-2：control 模块
Stage 2-3：csv_store 模块
Stage 2-4：input_cli 模块
Stage 2-5：output 模块
Stage 2-6：main 变薄
Stage 2-7：最小测试
Stage 2-8：Bug Lab
Stage 2-9：408 映射与 SRS
```

## 13. judge 模块边界

`judge` 模块负责判断，不负责输入、输出、保存、控制设备。

迁移函数：

```text
temp_status()
oxygen_status()
needs_aeration()
```

输入：

```text
温度
溶氧
PondRecord
```

输出：

```text
状态文本
是否需要增氧
```

禁止：

```text
不写 CSV
不读用户输入
不控制增氧机
不打印终端输出
不新增领域阈值
```

## 14. control 模块边界

`control` 模块负责把判断结果转成设备动作语义。

可能包含：

```text
aerator_should_be_on()
aerator_action()
aerator_reason()
```

当前不做真实硬件控制。

当前只输出：

```text
ON / OFF
LOW_OXYGEN / NORMAL_OXYGEN
```

禁止：

```text
不操作 GPIO
不操作继电器
不接 ESP32-S3
不接 Milk-V Duo S
不实现 FreeRTOS
```

## 15. csv_store 模块边界

`csv_store` 模块负责结构化保存。

当前已有三类 CSV：

```text
pond_records.csv
oxygen_alert.csv
control_log.csv
```

未来拆分函数：

```text
save_pond_record_csv()
save_oxygen_alert_csv()
save_control_log_csv()
```

该模块重点训练：

```text
FILE *
fopen
fprintf
fclose
fseek
ftell
返回值合同
错误路径
CSV header
```

## 16. input_cli 模块边界

`input_cli` 模块负责从命令行读取输入。

可能包含：

```text
read_pond_record()
```

职责：

```text
读取 pond_id
读取温度
读取溶氧
处理 scanf 返回值
处理退出输入
```

禁止：

```text
不做状态判断
不写 CSV
不控制设备
```

## 17. output 模块边界

`output` 模块负责终端显示。

可能包含：

```text
print_pond_record()
print_oxygen_alert()
print_aeration_action()
```

职责：

```text
把已有判断结果显示给用户
```

禁止：

```text
不做领域判断
不写 CSV
不读用户输入
```

## 18. main 的目标

`main` 最终应该变薄。

目标结构：

```text
读取输入
填时间戳
调用 judge
调用 output
调用 csv_store
进入下一轮
```

`main` 不应该长期承担：

```text
状态判断
CSV 细节
设备动作细节
复杂输入处理
```

## 19. 领域知识同步策略

`domain_snapshot/` 是从 GPT Project 同步过来的只读快照。

同步流程：

```text
新资料
→ Gemini / NotebookLM 初加工
→ GPT Project 审核
→ 更新 domain/cards
→ 更新 source_index
→ 同步 domain_snapshot
→ 单独 Git commit
```

推荐 commit message：

```text
domain: sync aquaculture knowledge snapshot v0.1

- add/update water quality cards
- add/update source index
- no code behavior changed
```

## 20. 领域变更影响代码的流程

如果领域卡更新会影响代码行为，例如阈值、建议动作、状态分类：

```text
1. 先同步 domain_snapshot
2. 新建 Codex 任务包
3. Codex 只做差异分析
4. 用户人工确认
5. 再修改代码
6. 跑回归测试
7. 写变更复盘
8. 单独 Git commit
```

禁止直接做：

```text
新资料 → 直接改代码
```

## 21. 408 映射策略

Stage 2 当前重点映射：

### 数据结构

* 结构体
* 抽象数据类型
* 模块接口
* 数据对象
* 数据元素
* 数据项

### 计算机组成原理

* 浮点数表示
* 函数调用
* 参数传递
* 编译与链接
* 数据在内存中的表示

### 操作系统

* 程序运行环境
* 文件系统
* 系统调用
* 错误处理
* 文件 I/O 抽象

### 计算机网络

* 当前不强行映射
* 等 MQTT / HTTP / 数据上传阶段再进入

## 22. Claude 早晨 408 闭环

工程闭环结束后，GPT Project / Codex 生成 408 映射草稿。

Claude 作为早晨 408 闭环导师，负责：

1. 讲解对应王道小节
2. 拆考点
3. 给题型
4. 追问用户
5. 生成错题和 SRS

当前建议早晨闭环格式：

```text
1. 昨日工程任务回顾
2. 对应 408 小节
3. 核心考点讲解
4. 3-5 道选择题或小题
5. 错题归因
6. SRS 卡片
```

## 23. Git 提交策略

系统结构更新：

```text
chore: add training system architecture v1
```

领域快照同步：

```text
domain: sync aquaculture knowledge snapshot v0.1
```

模块迁移：

```text
stage2: split judge module from pond project
```

测试补充：

```text
test: add judge module regression cases
```

Bug 修复：

```text
fix: correct judge boundary behavior
```

文档复盘：

```text
docs: add daily loop for judge module v0
```

## 24. 下一步

下一步不是继续扩展业务，而是进入：

```text
Stage 2-1：judge 模块拆分
```

执行顺序：

```text
1. 同步 domain_snapshot v0.1
2. 进入 codex/daily_packets/001-judge-module-v0.md
3. Codex 推荐 A/B/C 模式
4. 用户确认模式
5. 迁移 judge 模块
6. 编译验证
7. 写测试或手动测试记录
8. 写 Bug Lab
9. Git commit
10. docs/daily 复盘
```
