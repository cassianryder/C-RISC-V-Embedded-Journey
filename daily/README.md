# Daily Learning Control Center

`daily/` 是每日学习闭环入口，不重复实现问题解析和间隔复习算法。

分工：

- `skills/local_review/spaced_review.py`：SRS 算法实现，负责抽卡、排序、间隔更新。
- `daily/`：每日执行系统，负责把 SRS、理解校准、主线规划、启发带练和收口串起来。

## 每天怎么启动

如果今天只有一个时间段：

```bash
python3 daily/daily_review.py plan --minutes 60
```

如果今天有多个时间段：

```bash
python3 daily/daily_review.py plan --block morning:60 --block evening:120
```

确认计划后开始复习：

```bash
python3 daily/daily_review.py today --minutes 60
```

只看今天到期问题：

```bash
python3 daily/daily_review.py list --minutes 60
```

查看严格标签地图：

```bash
python3 daily/daily_review.py map
```

按相关标签复习：

```bash
python3 daily/daily_review.py list --minutes 60 --related-tag "FILE *"
python3 daily/daily_review.py list --minutes 60 --related-tag CSV
python3 daily/daily_review.py list --minutes 60 --related-tag 结构体指针
```

## 默认主线

当前默认主线是：

```text
06-projects
exercises/06-projects/1.c
```

当前每日闭环重点：

```text
SRS 复习
-> 理解校准
-> CSV 保存层 / 文件 I/O 主线
-> 启发带练
-> 编译运行验证
-> log / problems / git 建议
```

## 工程原则

- `daily/` 管流程，不直接管项目业务代码。
- SRS 不吞掉主线时间，复习必须服务当天项目推进。
- SRS 抽卡不只看日期，也要看主线相关标签和近期误区。
- 每次引入新 API 前，先解释它解决的现实问题。
- 主逻辑优先挖空，关键条件、返回值、调用位置由我先填。
- 每日结束必须估算时间流向，判断是否偏航。

## 当前恢复说明

这个目录是误删仓库后重构的本地版。当前 `.gitignore` 仍然忽略 `daily/`，所以它默认不提交到 GitHub。

如果希望以后不再丢失，可以把脱敏版 `daily/` 加入 Git 跟踪。
