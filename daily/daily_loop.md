# Daily Loop

这是每天启动和收口的固定流程。它的目标是让复习、项目推进和问题沉淀形成闭环，而不是变成零散聊天。

## 1. 启动检查

先确认工作区和上下文：

```bash
git status --short
sed -n '1,180p' notes/current.md
tail -8 notes/log.md
sed -n '1,180p' notes/problems/problem.md
```

如果 `notes/current.md` 明显过期，先根据 `notes/log.md` 和当前代码校正。

## 2. SRS 复习

根据今天可用时间动态规划：

```bash
python3 daily/daily_review.py plan --minutes 60
```

分段时间：

```bash
python3 daily/daily_review.py plan --block morning:60 --block evening:120
```

复习原则：

- 30 分钟：1-2 张卡
- 60 分钟：3 张左右
- 90 分钟：4-5 张
- 2 小时以上：可以增加，但主线必须仍是最大块

如果今天卡在具体概念，用相关标签抽卡：

```bash
python3 daily/daily_review.py list --minutes 60 --related-tag "FILE *"
python3 daily/daily_review.py list --minutes 60 --related-tag CSV
python3 daily/daily_review.py list --minutes 60 --related-tag 返回值
```

如果需要查看标签体系：

```bash
python3 daily/daily_review.py map
```

## 3. 理解校准

每张卡至少过下面这道门：

```text
我能用自己的话解释吗？
我能指出误区吗？
我能映射到当前代码吗？
函数的输入、输出、返回值、副作用是什么？
如果涉及指针：对象、地址、指针变量、解引用分别是什么？
如果涉及文件 I/O：文件名、FILE *、文件位置、缓冲区分别是什么？
```

没过门，不进入写代码；但也不无限深挖，只补今天主线需要的最小理解。

## 4. 主线规划

每天只选一个最小工程目标。

当前推荐主线：

```text
exercises/06-projects/1.c
save_pond_record_csv()
CSV 保存层 / FILE * / fseek / ftell / 表头只写一次
```

规划必须写清：

```text
今天做什么：
今天不做什么：
成功标准：
验证方式：
收口产物：
```

## 5. 启发带练

Codex 不直接给完整实现，先挖空主逻辑：

```text
打开资源
失败路径
核心判断
主动作
关闭资源
返回状态
```

我先填写关键代码，再由 Codex 检查。

## 6. 验证

每个功能必须最小验证：

```bash
make 61
```

如果会生成运行产物，优先在临时目录验证：

```bash
gcc -Wall -g exercises/06-projects/1.c -o /tmp/pond_program
tmpdir=$(mktemp -d)
printf '25.5 5.5 A\nq\n' | (cd "$tmpdir" && /tmp/pond_program)
sed -n '1,10p' "$tmpdir/pond_records.csv"
```

## 7. 收口

最后 5-10 分钟只做收口，不开新功能：

- `notes/log.md`：记录今日高密度进度
- `notes/problems/*.md`：只记录真正有价值的问题
- `notes/problems/problem.md`：更新索引
- `notes/current.md`：覆写当前主线状态
- `git status`：区分源码、文档、运行产物

## 8. 时间流复盘

每天估算：

```text
SRS：
概念校准：
主线规划：
代码推进：
验证收口：
最大偏航：
明日优化：
```

目标不是精确计时，而是防止复习和概念讨论吞掉项目推进。
