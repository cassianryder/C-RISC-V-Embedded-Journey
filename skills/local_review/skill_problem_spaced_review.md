# Skill 名称

本地问题间隔复习系统

## 适用阶段

- `03-structs` 到 `06-projects` 的主线推进阶段
- 已经开始持续积累 `notes/problems/*.md` 问题单
- 需要把“记过一次”升级成“按时间反复提取”

## 它解决什么问题

- 问题单越积越多，但没有节奏地回看，容易形成“写过但没真正长进”
- 同一个误区会在几天后重新出现，比如：
  - `record.temp` / `record->temp`
  - 值传递 / 指针传递
  - 缓冲区 / 文件 I/O / 补码 / 结构体内存布局
- 这个系统把“问题笔记”变成“复习卡片”，并按间隔算法安排复习时间

## 第一性原理拆解

- 它是什么
  - 一个读取 `notes/problems/*.md` 的本地间隔复习脚本
  - 以“每个 `### 问题`”作为一张卡片
  - 每次复习后，根据回忆质量自动调整下次复习日期

- 为什么存在
  - 只记笔记不复习，知识会快速衰减
  - 你当前主线是“项目推进 + 问题沉淀”，所以最自然的复习单位就是你自己写下的问题

- 解决什么问题
  - 什么时候该复习哪几个问题
  - 哪些问题你已经真正掌握，哪些只是短期看懂
  - 哪些误区需要高频回看

- 和已学内容如何连接
  - `notes/problems`：作为问题来源
  - `notes/log.md`：记录每天推进到哪
  - `03-structs / 06-projects`：作为当前最常复习的主线代码场景

## 最小项目模板

每天或每次推进结束后，用下面 3 个动作闭环：

1. 看今天到期的问题
2. 自评回忆质量
3. 让脚本自动计算下一次复习时间

建议命令：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py today
```

如果只想看今天到期清单：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py list
```

如果今天只想先复习 10 张：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py today --limit 10
```

如果只想复习某个主题，比如“结构体”：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py list --grep 结构体
```

如果只想看“本周新问题”：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py list --this-week
```

如果想按主线主题复习：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py today --topic 03-structs --limit 5
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py today --topic 06-projects --limit 5
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py today --topic 补码 --limit 5
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py today --topic 文件I/O --limit 5
```

如果只想看统计：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py stats
```

## CLI 验证步骤

1. 首次初始化卡片和状态

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py stats
```

2. 查看到期问题

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py list
```

3. 开始一次复习会话

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py today
```

## 常见错误

- 只打开问题看，不给评分
  - 这样间隔不会推进，系统就失去价值

- 给自己评分过高
  - 如果实际上讲不清，只是“看答案后觉得会”，不要打 5

- 把所有问题都堆成一天看完
  - 复习系统的意义是“分散提取”，不是一次性补作业

- 忘记“复习单位是问题，不是整篇笔记”
  - 一次只抓一个误区，效果更稳

## 和水产项目如何耦合

- 可以优先高频复习这些问题：
  - 结构体与结构体指针
  - 输入封装与函数职责边界
  - 阈值判断、状态输出、记录组织
  - 日志、CSV、队列、状态机准备问题

- 以后如果主线推进到 `06-projects`，可以把“项目 bug / 设计误区”也写成问题卡片

## 和 Linux 副线如何耦合

- 每次复习结束，顺手练一遍：
  - `git status`
  - `sed -n`
  - `make 31 / 61`
  - `rg`

- 这样复习不会停留在纯笔记层，也会带回命令行工作流

## 后续如何升级

- 升级 1：增加“只复习本周新问题”的模式
- 升级 2：增加“按主题过滤”，比如只看结构体/补码/文件 I/O
- 升级 3：把 `06-projects` 中真实 bug 也接入卡片系统
- 升级 4：增加“周复盘”导出，反推本周最常错的知识点
