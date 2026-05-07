# Engineering Gaps

从工程视角看，项目当前不是缺学习热情，而是需要逐步补齐“可恢复、可验证、可扩展、可迁移”的工程骨架。

## 1. 恢复能力

当前问题：

- `daily/`、`prompts/` 这类上下文工程目录默认被 `.gitignore` 忽略。
- 一旦本地误删，项目代码能从 GitHub 恢复，但学习系统上下文会断。

建议：

- 脱敏版 `daily/`、`prompts/` 应考虑纳入 Git。
- 私人状态文件继续忽略，例如 SRS 个人进度、临时运行数据。

## 2. 架构边界

当前项目已经有：

```text
数据层 PondRecord
输入层 read_pond_record
判断层 temp_status / oxygen_status
输出层 print_pond_record
存储层 save_pond_record_csv
```

下一步缺：

- `pond_record.h / pond_record.c`：数据模型拆分
- `judge.h / judge.c`：状态判断拆分
- `csv_store.h / csv_store.c`：存储层拆分
- `input_cli.c`：命令行输入拆分
- `main.c`：只保留流程编排

## 3. 数据合同

当前 CSV 能写，但还需要稳定合同：

- CSV header 固定字段
- 每条记录的字段顺序固定
- 成功返回 `1`，失败返回 `0`
- 失败时不写半条记录
- 后续需要考虑异常输入、文件打不开、磁盘写失败

## 4. 测试和验证

当前主要靠手动运行。

下一步需要：

- 最小测试用例：状态判断函数
- 临时目录测试：CSV 第一次写 header，第二次不重复 header
- Makefile 增加 `test` 或 `check`
- 后续引入小型 C 单元测试或 shell 测试

## 5. 运行产物边界

当前 `.gitignore` 已处理：

- `bin/program`
- `pond_records.csv`
- `CSV_FILE_NAME`
- `pond_records_csv`

后续需要继续保持：

- 示例数据可以放 `data/samples/`
- 真实运行数据不要提交
- 测试临时数据放 `/tmp` 或临时目录

## 6. 嵌入式迁移前置

现在还不急着接板子。接板子前至少需要：

- CLI 版记录链路稳定
- 业务逻辑和 I/O 分离
- 状态判断函数不依赖终端输入输出
- 存储层和设备层可替换
- 清楚哪些是标准 C，哪些依赖操作系统

## 7. 当前最优推进顺序

短期：

```text
CSV 表头逻辑收干净
-> 文件 I/O 费曼校准
-> 临时目录验证
-> log/problems/current 收口
```

中期：

```text
单文件项目
-> 多文件模块化
-> Makefile 多目标
-> 测试脚本
-> 查询/统计 CLI
```

再后面：

```text
串口模拟
-> 传感器数据模拟
-> 板子接入
-> 缓存/队列
-> 后端/前端/模型
```

## 8. 工程判断

当前项目缺的不是“大功能”，而是工程闭环：

```text
清晰入口
稳定数据合同
可重复验证
可恢复上下文
小步模块化
运行产物边界
```

每天只要补齐其中一个点，项目会越来越像真实工程，而不是练习文件集合。
