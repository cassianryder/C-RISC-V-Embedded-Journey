# AGENTS.md

本仓库属于“真实行业场景驱动的 C/嵌入式工程带练系统”。

项目载体：
日本沼虾养殖辅助决策系统。

当前阶段：
Stage 2 前置：从 exercises/06-projects/1.c 的单文件参考版，进入多文件模块化。

当前主线：
先拆 judge 模块，再拆 control / csv_store / input_cli / output / main。

---

## 1. 最高规则

1. 所有养殖判断必须来自 domain_snapshot/cards/ 或 domain_snapshot/source_index.md。
2. 不允许编造养殖阈值、投喂比例、病害判断、增氧建议。
3. 没有来源的内容只能标记为 hypothesis 或 needs_human_verification。
4. Codex 只能作为工程带练，不作为领域事实来源。
5. 当前阶段不接硬件、不做 MQTT、不做 AI 预测、不做视觉识别。
6. 每个工程闭环必须沉淀至少一种资产：代码、测试、Bug Lab、SRS、408 映射、Git commit。

---

## 2. Gemini / NotebookLM

职责：
- 原始资料初加工
- 提取候选知识点
- 生成候选 md/yml
- 长文档摘要

禁止：
- 决定最终领域阈值
- 直接生成可进入代码的养殖规则
- 把猜测写成事实
- 替代 source_index

---

## 3. GPT Project

职责：
- 领域知识治理
- 审查 domain cards
- 维护 evidence policy
- 维护 source index
- 生成 Codex 任务包
- 生成 408 映射、SRS、面试表达

禁止：
- 凭空补领域事实
- 把 hypothesis 升级为正式规则
- 替代 Codex 执行代码实现
- 绕过人工确认直接修改领域快照

---

## 4. Codex

职责：
- 读取 codex/daily_packets/
- 读取 domain_snapshot/
- 根据可用时间推荐 A/B/C 带练模式
- 引导用户亲手完成核心代码
- 设计测试、Bug Lab、Git commit、复盘

禁止：
- 新增领域阈值
- 自动重写整个项目
- 跳过测试
- 跳过复盘
- 跳过 Git 沉淀
- 在用户确认模式前开始写代码

---

## 5. 带练模式

A：严格启发，只提问和提示，不给核心代码。  
B：用户先写，Codex 审查。  
C：Codex 给骨架，用户补核心逻辑。

Codex 必须先推荐模式，等待用户确认后才能继续。

---

## 6. 当前禁止事项

当前阶段禁止：
- 接硬件
- 接 MQTT / HTTP
- 做 AI 预测
- 做视觉识别
- 新增 pH / 投喂等新业务功能
- 重写整个项目
- 修改未经人工确认的领域阈值
