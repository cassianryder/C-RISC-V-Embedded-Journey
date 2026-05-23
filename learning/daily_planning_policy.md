# Daily Planning Policy

## 1. 每日启动输入

用户进入工程前需要提供：

```text
今日可用时间：
今日状态：
是否能写代码：
是否只做复盘：
是否有新领域资料：
```

## 2. Codex 每日必须先读

Codex 每日启动前必须读取：

```text
AGENTS.md
notes/current.md
notes/log.md
learning/c_embedded_skill_graph.yml
learning/aquaculture_cycle_map.yml
codex/daily_packets/latest or current packet
domain_snapshot/README.md
```

如果涉及领域判断，必须读取：

```text
domain_snapshot/evidence_policy.md
domain_snapshot/source_index.md
domain_snapshot/cards/
```

## 3. 时间档位

### Mini：30 分钟

适合：

* 精力不足
* 时间不足
* 只做 SRS
* 只修一个小问题
* 只写一条问题记录

输出：

* 1 个小 commit，或
* 1 条 problem note，或
* 1 条 SRS 卡

### Standard：60-90 分钟

适合：

* 正常工程闭环
* 小模块迁移
* 一个函数族整理
* 简单测试验证

输出：

* 一个小模块修改
* 编译验证
* 简单测试
* daily loop 复盘

### Deep：2-3 小时

适合：

* 模块迁移
* 测试补齐
* Bug Lab
* 408 映射
* Git 沉淀

输出：

* 多文件修改
* 测试记录
* Bug Lab
* Git commit
* docs/daily 复盘
* mapping 记录

### Review：20-40 分钟

适合：

* 不写代码
* 做 SRS
* 做 408 映射
* 做复盘
* 整理问题

输出：

* SRS 卡
* mapping 记录
* problem note
* 明日任务建议

---

## 4. 模式选择

Codex 每日必须先推荐带练模式，但必须等待用户确认。

### A：严格启发模式

适合：

* SRS 回答不稳定
* 新概念第一次出现
* 用户状态较差
* 不适合直接写代码

Codex 行为：

* 只提问
* 只给提示
* 可给伪代码
* 不给核心实现

### B：用户先写，Codex 审查

适合：

* 用户对概念基本理解
* 需要训练独立实现能力
* 任务难度中等

Codex 行为：

* 用户先写
* Codex 检查 bug
* Codex 解释错误
* Codex 给最小修复建议

### C：骨架辅助模式

适合：

* 多文件结构复杂
* Makefile 涉及多个文件
* 模块边界需要先设计
* 用户需要工程框架辅助

Codex 行为：

* 可以给目录结构
* 可以给函数声明
* 可以给 include guard 示例
* 可以给 Makefile 修改方向
* 核心逻辑仍由用户亲手完成

## 5. 当前任务优先级

当前阶段是：

```text
Stage 2：多文件模块化
```

任务优先级：

```text
P0：保护 exercises/06-projects/1.c 单文件参考版
P1：拆 judge 模块
P2：拆 control 模块
P3：拆 csv_store 模块
P4：拆 input_cli 模块
P5：拆 output 模块
P6：main 变薄
P7：最小测试
P8：Bug Lab
P9：408 映射
P10：多塘口 MVP
```

## 6. 当前禁止事项

1. 不接硬件
2. 不做 MQTT / HTTP
3. 不做 AI 预测
4. 不做视觉识别
5. 不扩展新养殖功能
6. 不新增未验证领域阈值
7. 不重写整个项目
8. 不删除 `exercises/06-projects/1.c` 单文件参考版
9. 不跳过测试
10. 不跳过复盘

## 7. 每日闭环顺序

1. 用户提供今日可用时间和状态
2. Codex 读取 `AGENTS.md` 和当前任务包
3. Codex 读取 `notes/current.md` 和 `notes/log.md`
4. Codex 进行 SRS 抽查
5. Codex 推荐 A/B/C 模式
6. 用户确认模式
7. Codex 明确今日唯一主线
8. 用户亲手完成核心实现
9. Codex 审查代码
10. 编译和运行验证
11. 记录 Bug Lab
12. 生成 Git commit message
13. 写 `docs/daily` 复盘
14. 生成 408 映射和 SRS 候选卡

## 8. 每日闭环输出

每次工程闭环结束后，至少沉淀以下一项：

1. 代码变更
2. 测试记录
3. Bug Lab
4. SRS 卡
5. 408 映射
6. Git commit
7. `docs/daily` 复盘

标准闭环建议至少包含：

1. Git commit
2. `docs/daily` 复盘
3. 至少一条 SRS 候选卡
4. 至少一个 408 映射点

## 9. 新资料进入流程

新增日本沼虾资料时，不允许直接进入 Codex 或代码。

标准流程：

```text
新资料
→ Gemini / NotebookLM 初加工
→ GPT Project 审核
→ 更新 domain/cards
→ 更新 source_index
→ 同步 domain_snapshot
→ 生成 Codex 任务包
→ 工程实现
```

## 10. 领域知识同步规则

`domain_snapshot/` 是只读领域快照。

维护规则：

1. Codex 不允许主动修改 `domain_snapshot`。
2. 新资料必须先在 GPT Project 审核。
3. 审核通过后，人工同步到 `domain_snapshot`。
4. 同步后必须生成单独 commit。
5. 领域同步 commit 不应混入 C 代码修改。

推荐 commit message：

```text
domain: sync aquaculture knowledge snapshot v0.1

- add/update water quality cards
- add/update source index
- no code behavior changed
```

## 11. 领域变更影响代码的流程

如果领域卡更新会影响代码行为，例如阈值、建议动作、状态分类：

1. 先同步 `domain_snapshot`
2. 新建 Codex 任务包
3. Codex 只做差异分析
4. 用户人工确认
5. 再修改代码
6. 跑回归测试
7. 写变更复盘
8. 单独 Git commit

禁止直接做：

```text
新资料 → 直接改代码
```

## 12. 408 早晨闭环衔接

工程闭环结束后，GPT Project / Codex 生成 408 映射草稿。

Claude 作为早晨 408 闭环导师，负责：

1. 讲解对应王道小节
2. 拆考点
3. 给题型
4. 追问用户
5. 生成错题和 SRS

当前 Stage 2 重点映射：

### 数据结构

* 结构体
* 抽象数据类型
* 模块接口
* 数据对象 / 数据元素 / 数据项

### 计算机组成原理

* 浮点数表示
* 函数调用
* 编译与链接
* 数据在内存中的表示

### 操作系统

* 程序运行环境
* 文件系统
* 系统调用
* 错误处理

### 计算机网络

* 当前不强行映射
