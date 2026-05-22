# Domain Snapshot

本目录保存从 GPT Project / 领域知识库同步过来的只读快照。

用途：
- 给 Codex 提供可引用的领域证据
- 防止 Codex 编造养殖阈值
- 让代码中的规则能够追溯到 card_id 和 source_id

维护规则：
1. 本目录内容不由 Codex 自动修改。
2. 新增资料后，先进入 Gemini / NotebookLM 初加工。
3. 初加工结果必须进入 GPT Project 审核。
4. 审核通过后，再同步到本目录。
5. 每次同步需要记录版本、日期和来源。

当前状态：
TODO: sync domain knowledge base v0.1 from GPT Project.
