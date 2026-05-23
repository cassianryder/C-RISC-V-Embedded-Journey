# Codex Daily Task Packet Template

## 0. Metadata

```yml
packet_id:
date:
stage:
task_name:
estimated_time:
recommended_mode:
user_confirmed_mode:
reference_files:
target_files:
domain_cards:
```

---

## 1. 今日主线

今天只做：

```text

```

今天不做：

```text

```

---

## 2. SRS 回顾要求

编码前必须回顾：

```text
1.
2.
3.
```

如果 SRS 不通过，降级为 A 模式。

---

## 3. 领域卡读取要求

如果任务涉及养殖判断，必须读取：

```text
domain_snapshot/evidence_policy.md
domain_snapshot/source_index.md
domain_snapshot/cards/
```

规则：

```text
1. 不允许新增领域阈值
2. 不允许改写领域卡
3. 代码中的阈值必须注释 card_id/source_id
4. 领域卡缺失时标记 needs_human_verification
```

---

## 4. 启发式带练规则

Codex 必须先输出：

```text
1. 今日推荐模式
2. 推荐理由
3. 用户亲手写的部分
4. Codex 辅助部分
```

用户确认前，不开始生成代码。

---

## 5. 验收标准

```text
1. 能编译
2. 能运行
3. 不新增领域事实
4. 不破坏旧功能
5. 有测试或手动验证
6. 有 Git diff
7. 有复盘记录
```

---

## 6. 测试要求

至少覆盖：

```text
1. 正常输入
2. 边界输入
3. 异常输入或 TODO 记录
4. 回归旧功能
```

---

## 7. Bug Lab 要求

如果出现 bug，必须记录：

```text
- bug 现象
- 触发输入
- 根因
- 修复方式
- 复发预防
- 对应 408 知识点
```

---

## 8. Git commit 要求

格式：

```text
stageX: short action summary

- what changed
- why changed
- test evidence
```

---

## 9. 今日复盘要求

写入：

```text
docs/daily/YYYY-MM-DD-task-name.md
```
