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

每天或每次推进开始前，用下面 4 个动作闭环：

1. 输入今天可用时间，生成动态复习预算
2. 优先抽取与当前主线相关、最近低分、最近新增的问题
3. 自评回忆质量
4. 让脚本自动计算下一次复习时间

复习结束后新增一条硬规则：

5. 必须做理解校准，达标后才进入启发式带练模式

理解校准标准：

- 我能用自己的话解释问题，不只是复述答案
- 我能指出自己刚才的误区在哪里
- 我能把这个问题映射到当前主线代码中的某一行或某个函数
- 如果涉及函数，必须说清输入、输出、返回值和副作用
- 如果涉及指针，必须说清对象、地址、指针变量、解引用分别处在哪一层
- 如果涉及文件 I/O 或返回值，必须说清成功路径和失败路径

如果没有达标：

- 不进入写代码
- 先用费曼输出和第一性原理补齐
- 只补今天主线需要的最小理解，不扩展成大课

达标后进入启发式带练：

- 主逻辑必须挖空
- 关键判断、循环体、返回值、函数调用位置先由我填写
- 你只给最小提示、追问和检查
- 只有我连续卡住，才逐级给更多提示

建议命令：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py plan --minutes 90 --main-topic 06-projects
```

如果今天分成多个时间段，比如早上 60 分钟、晚上 120 分钟：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py plan --block morning:60 --block evening:120 --main-topic 06-projects
```

如果确认计划后直接开始复习：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py today --minutes 90 --main-topic 06-projects
```

如果确认分段计划后直接开始复习：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py today --block morning:60 --block evening:120 --main-topic 06-projects
```

如果今天只有 30 分钟：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py plan --minutes 30 --main-topic 06-projects
```

如果只想看今天到期清单：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py list
```

如果只想看智能排序后的推荐清单：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py list --smart --main-topic 06-projects --limit 8
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

如果想按“相关标签网”复习，而不是只精确匹配一个标签：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py list --related-tag "FILE *" --main-topic 06-projects --smart
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py list --related-tag CSV --main-topic 06-projects --smart
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py list --related-tag 结构体指针 --main-topic 06-projects --smart
```

查看完整标签地图：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py map
```

标签系统分三层：

- `tag_groups`：严格标签分组，用来避免标签越写越散。
- `tag_relations`：相关标签图，用来从一个卡点追踪到相邻知识点。
- `workflow_weights`：主线权重，用来让 `06-projects` 优先抽 CSV、文件 I/O、返回值、结构体指针等当前真正相关的问题。

如果只想看统计：

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py stats
```

## CLI 验证步骤

1. 首次初始化卡片和状态

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py stats
```

2. 查看按今天可用时间动态生成的计划

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py plan --minutes 90 --main-topic 06-projects
```

3. 查看到期问题

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py list
```

4. 开始一次复习会话

```bash
python3 /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/skills/local_review/spaced_review.py today --minutes 90 --main-topic 06-projects
```

## 动态复习算法

- `--minutes` 会根据当天总时间自动计算复习预算。
- `--block` 可以重复使用，用于输入早上/中午/晚上等多个可用时间段。
- 如果同时给出多个 `--block`，脚本会先加总全天时间，再把 SRS、规划、主线、收口分配到不同时间段。
- 30 分钟左右：只复习 1-2 张，避免吞掉主线。
- 90 分钟左右：复习约 4 张，保留 1 小时左右推进项目。
- 3 小时以上：复习量上升，但仍设上限，避免变成纯复习日。
- `--main-topic` 只提升优先级，不会过滤掉所有其他问题。
- `--smart` 会综合当前主线、低分卡、近期问题、逾期天数和 lapses 排序。
- 低分卡会更早回炉，已经掌握的卡会逐步拉长间隔。

分段规划原则：

- SRS 和理解校准优先放在最早的可用时间段。
- 每日主线规划紧跟 SRS 校准之后。
- 主线推进占用当天最长的连续可用时间。
- 收口、验证、notes/git 建议优先放在最后一个时间段。
- 如果早上时间很短，早上只做 SRS + 校准；晚上再进入主线推进。

## 每日时间流复盘

每天闭环结束时，需要估算当天时间使用占比，并给出明日优化建议。

固定输出：

- SRS 抽卡与理解校准占用多少时间
- 概念补强/费曼输出占用多少时间
- 主线规划占用多少时间
- 代码推进占用多少时间
- 编译运行验证与 notes/git 收口占用多少时间
- 与原计划相比最大偏差在哪里
- 明日如何优化时间分配

这条规则的目的不是精确计时，而是防止 SRS、系统优化或概念讨论无意识吞掉主线推进时间。

## SRS 到启发带练的切换门槛

每日固定顺序：

1. SRS 间隔复习
2. 理解校准，确认达到今天继续推进的最低要求
3. 今日主线规划
4. 启发带练模式
5. 编译/运行验证与收口

每天切换到项目推进前，必须完成下面 3 句校准：

1. “我现在最不稳的点是：_____”
2. “这个点在今天主线代码里对应：_____”
3. “接下来写代码时，我需要特别检查：_____”

如果这 3 句说不清，说明今天还没有进入带练条件。
此时先补理解，不急着改代码。

进入带练后默认使用挖空模板：

```c
while (__________)
{
    ____________;

    if (__________)
    {
        ____________;
    }
}
```

这个模板不是固定代码，而是提醒：主流程应该由我先思考并填写。

启发带练规则：

- 先问真实场景职责，再写代码
- 先判断输入、输出、返回值、失败路径，再动手
- 主逻辑必须挖空，不能直接给完整实现
- 关键条件、循环体、函数调用位置必须先让我填写
- 我提交后再检查、追问、校准
- 只有连续卡住时，才逐级提示，最后才给参考答案

## 常见错误

- 只打开问题看，不给评分
  - 这样间隔不会推进，系统就失去价值

- 给自己评分过高
  - 如果实际上讲不清，只是“看答案后觉得会”，不要打 5

- 把所有问题都堆成一天看完
  - 复习系统的意义是“分散提取”，不是一次性补作业

- 只按最旧到期顺序复习
  - 这样容易让旧卡吞掉当前主线，应该用 `plan --minutes ... --main-topic ...` 保护项目推进

- 忘记“复习单位是问题，不是整篇笔记”
  - 一次只抓一个误区，效果更稳

- SRS 后没有校准理解就直接写代码
  - 这会把“看过答案”误当成“能主动使用”，必须先通过三句校准

- 启发带练时直接看完整答案
  - 这会降低主动建模能力，主逻辑必须先挖空再让我填写

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
