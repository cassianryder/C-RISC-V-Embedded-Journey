# Current Snapshot

> 每天覆写一次，不做历史累积；供 GPT Project / Codex 读取当前主线进度、问题与自然衔接。

- 当前主线文件：`exercises/06-projects/1.c`

- 当前主攻知识点：`CSV 保存层`、`FILE * 文件入口`、`fseek/ftell 文件位置`、`表头只写一次`、`结构体记录持久化`

- 最近已完成：
  - 跑通 `PondRecord -> 输入 -> 时间戳 -> 状态判断 -> 终端输出 -> CSV 追加保存`。
  - 将 `CSV_FILE_NAME` 宏用于 `fopen(CSV_FILE_NAME, "a")`，避免把宏名误写成字符串字面量。
  - 在 `save_pond_record_csv()` 中加入空文件判断：文件大小为 `0` 时先写 CSV header，再写当前记录。
  - 误删仓库后已从 GitHub 恢复主仓，并重建今天未提交的 CSV 表头逻辑。

- 当前未解决问题：
  - `FILE *fp`、`fseek(fp, 0, SEEK_END)`、`ftell(fp)` 的底层模型仍需继续费曼校准。
  - `save_pond_record_csv()` 目前能工作，但缩进、失败路径和注释还需要收干净。
  - `prompts/` 已本地重建，但当前 `.gitignore` 仍忽略它；后续需要决定是否提交脱敏版提示词工程。

- 下一步自然衔接：
  - 先用启发带练复盘：文件名、`FILE *`、文件位置指针、文件大小之间的关系。
  - 再运行验证两次：第一次新建 CSV 写 header，第二次追加记录但不重复 header。
  - 最后收口 `save_pond_record_csv()` 的代码风格，并决定是否把脱敏版 `prompts/` 纳入 Git。
