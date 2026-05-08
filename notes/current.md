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
  - `FILE *fp`、`fseek(fp, 0, SEEK_END)`、`ftell(fp)` 已完成项目级理解，但后续仍可结合 CSAPP / OS 深挖。
  - `save_pond_record_csv()` 已能工作并完成基本代码风格收口，下一步需要决定先做最小测试还是拆多文件模块。
  - `daily/`、`prompts/` 的脱敏版已纳入 Git；个人 SRS 状态和运行产物继续保持本地忽略。

- 下一步自然衔接：
  - 明天先用 SRS 回炉 `FILE * / fseek / ftell / fprintf`，确认不遗忘今天的文件位置模型。
  - 主线选择一条：要么为 CSV 保存层加最小测试，要么开始拆 `record / judge / csv_store / input_cli / main`。
  - 收口时继续保持“先项目小闭环，再问题沉淀，再 git 分组提交”的节奏。
