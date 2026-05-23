# 001 - Judge Module v0

## 0. Metadata

```yml
packet_id: "001-judge-module-v0"
stage: "Stage 2 - multi-file modularization"
task_name: "Split judge module from exercises/06-projects/1.c"
recommended_mode: "C -> B"
reference_files:
  - "exercises/06-projects/1.c"
target_files_future:
  - "include/record.h"
  - "include/judge.h"
  - "src/judge.c"
  - "tests/test_judge.c"
domain_cards:
  - "WQ-DO-GROWOUT-001"
```

---

## 1. 今日主线

从 `exercises/06-projects/1.c` 中拆出 judge 模块 v0。

迁移函数：

```text
- temp_status(...)
- oxygen_status(...)
- needs_aeration(...)
```

今天不做：

```text
- 不新增 pH 判断
- 不新增投喂建议
- 不新增硬件控制
- 不新增 MQTT/HTTP
- 不重写整个项目
- 不修改领域阈值
```

---

## 2. SRS 回顾要求

编码前先回顾：

```text
1. PondRecord record 与 PondRecord *record 的区别
2. record.temp 与 record->temp 的区别
3. 函数声明和函数定义的区别
4. .h 文件与 .c 文件的分工
5. 编译错误与链接错误的区别
6. const char * 返回字符串字面量的含义
7. 宏常量和字符串字面量的区别
```

---

## 3. 领域卡读取要求

Codex 必须先读取：

```text
domain_snapshot/evidence_policy.md
domain_snapshot/source_index.md
domain_snapshot/cards/water_quality_cards.yml
```

规则：

```text
1. 氧气阈值只能来自已审核领域卡。
2. 不允许新增任何养殖阈值。
3. 如果找不到 WQ-DO-GROWOUT-001，只能标记 needs_human_verification。
4. 代码注释必须标明 card_id/source_id。
```

---

## 4. 启发式带练规则

Codex 第一步必须输出：

```text
1. 今日推荐模式：A / B / C
2. 推荐理由
3. 用户需要亲手写的部分
4. Codex 只辅助的部分
```

用户确认前，不开始生成代码。

---

## 5. 禁止直接代写规则

Codex 不允许一次性输出完整最终实现。

允许：

```text
- 目录结构建议
- 头文件函数声明
- include guard 示例
- Makefile 修改思路
- 测试用例表
- 局部错误解释
```

用户必须亲手完成：

```text
- temp_status 核心逻辑迁移
- oxygen_status 核心逻辑迁移
- needs_aeration 核心逻辑迁移
- 至少一次手动编译和运行
```

---

## 6. 验收标准

```text
1. include/judge.h 存在
2. src/judge.c 存在
3. 判断函数从单文件中迁移成功
4. 编译通过
5. 旧的低氧/正常氧样例输出不变
6. 不新增领域阈值
7. 至少有一个 test_judge.c 或手动测试记录
8. 生成 Bug Lab 候选条目
9. 生成 Git commit message
10. 写入 docs/daily/YYYY-MM-DD.md
```

---

## 7. 测试要求

最低测试输入：

```text
A 25.0 4.0
B 25.0 6.0
C 25.0 5.0
D 25.0 -1.0
```

预期：

```text
A: low oxygen / needs aeration
B: normal oxygen / no aeration
C: boundary behavior must match existing single-file version
D: invalid value should be discussed; if not implemented, mark TODO
```

---

## 8. Bug Lab 候选 bug

```text
1. judge.h 没有 include guard
2. judge.c 忘记 include judge.h
3. main.c 调用函数但没有声明
4. PondRecord 在 judge.h 中不可见
5. 宏 OXYGEN_LOW_LIMIT 重复定义
6. 函数声明参数类型和定义不一致
7. undefined reference 链接错误
8. 使用 == 比较字符串内容
9. 把 "OXYGEN_LOW_LIMIT" 当作宏使用
10. 边界值 oxygen == 5.0f 行为不一致
```

---

## 9. Git commit 要求

建议：

```text
stage2: split judge module from pond project

- add judge.h and judge.c for water quality decision logic
- keep low oxygen behavior consistent with single-file reference
- prepare module boundary for future unit tests
```

---

## 10. 今日复盘要求

写入：

```text
docs/daily/YYYY-MM-DD-judge-module-v0.md
```

包含：

```text
1. 今日主线
2. SRS 结果
3. 实际修改文件
4. 编译证据
5. 测试输入/输出
6. Bug 或候选 Bug
7. 408 映射
8. 明日建议
```
