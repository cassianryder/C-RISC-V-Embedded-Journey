# Prompts Context Layer

这个目录是本地 Codex 上下文工程层，用来让命令行里的 Codex 快速接上项目状态、学习模式和启发带练流程。

## 使用顺序

1. 先读 `core_codex_system.md`
2. 再读 `daily_input_template.md`
3. 如果今天要沉淀新能力，再读 `skill_template.md`
4. 每次开始前同步读取：
   - `README.md`
   - `notes/current.md`
   - `notes/log.md`
   - `notes/problems/problem.md`
   - `exercises/CLASSIFICATION.md`
   - 当前主线代码文件

## 当前核心模式

- 先 SRS 间隔复习
- 再理解校准
- 再主线任务规划
- 再启发带练
- 最后验证、记录、git 建议

## 标签驱动 SRS

每日复习优先通过 `daily/` 入口调用：

```bash
python3 daily/daily_review.py plan --minutes 60
python3 daily/daily_review.py map
python3 daily/daily_review.py list --minutes 60 --related-tag CSV
```

`--tag` 用于严格匹配，`--related-tag` 用于按相关知识网抽取，例如 `FILE *` 会关联文件入口、`fopen`、`fprintf`、`fseek`、`ftell`。

## 设计原则

- Codex 是项目推进和学习系统的辅助工具，不替代我的思考。
- 新知识必须先铺垫现实问题，再给 API 或代码。
- 主逻辑尽量挖空，由我先填写关键条件、返回值、调用位置和循环体。
- 每天根据可用时间动态调整 SRS、主线、验证和收口比例。
- 不记录隐私，不把私人时间安排、身份信息或账号细节写进公开文件。
