# 2026-05-12 告警 CSV 表头与字符串字面量

## 我今天遇到的问题

### 1. 文件名和 `FILE *fp` 的角色混淆

问题：

我把 `OXYGEN_ALERT_FILE_NAME` 理解成文件入口。

解答：

`OXYGEN_ALERT_FILE_NAME` 是文件名；`fp` 是 `fopen()` 返回的文件流入口。`fseek`、`ftell`、`fprintf`、`fclose` 操作的都是 `fp`。

闭环：

```c
FILE *fp = fopen(OXYGEN_ALERT_FILE_NAME, "a");
fseek(fp, 0, SEEK_END);
long file_size = ftell(fp);
```

使用场景和启发思考：

文件名负责定位文件，`FILE *` 负责操作文件。后续所有文件 I/O 都先分清这两层。

### 2. 告警 CSV 表头属于哪一层

问题：

我把“写 header”同时归到记忆和关系层，边界不够清楚。

解答：

`needs_aeration(record)` 是关系/反馈判断；CSV header 是存储格式管理，属于记忆层。

闭环：

在 `save_oxygen_alert_csv()` 中用 `file_size == 0` 判断空文件，只在空文件时写 header。

使用场景和启发思考：

系统动力学映射到 C 时，要分清“判断系统状态”和“保存系统记忆”。

### 3. `LOW_OXYGEN` 和 `"LOW_OXYGEN"` 的区别

问题：

我在 `fprintf` 中写 `LOW_OXYGEN`，编译器报未声明。

解答：

`LOW_OXYGEN` 是名字，C 会查找变量/宏/枚举；`"LOW_OXYGEN"` 是字符串字面量，表示这段文本本身。新增 `event` 字段后，格式串也要增加一个 `%s`。

闭环：

```c
fprintf(fp, "%s,%c,%s,%.1f,%.1f\n",
        record.sampled_at,
        record.pond_id,
        "LOW_OXYGEN",
        record.oxygen,
        OXYGEN_LOW_LIMIT);
```

使用场景和启发思考：

没引号是名字，单引号是字符，双引号是字符串。CSV 事件名是文本，应使用字符串字面量。

## 补充的高质量问题

### 1. 运行产物是否提交

问题：

`oxygen_alert.csv` 是否应该进入 Git？

解答：

不应该。它是运行数据，不是源码。

闭环：

后续把 `oxygen_alert.csv` 加入 `.gitignore`。

使用场景和启发思考：

Git 保存代码、文档和测试；本地运行生成的数据应保持可再生、可忽略。

### 2. 是否立即进入多文件拆分

问题：

告警 CSV 表头完成后，是否马上拆文件？

解答：

不急。先收干净运行产物、旧注释和函数职责说明，再进入 Stage 2。

闭环：

先确认每个函数属于输入、关系、观察、记忆、反馈或控制中的哪一层。

使用场景和启发思考：

拆文件不是为了显得工程化，而是把已经稳定的职责边界落成文件结构。

