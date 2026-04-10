# AquaLog-RV Simulator

Cassian-RV 学习项目中的第一个纯 C 模拟版子项目。

这个版本专注于：

- 结构体组织多传感器数据
- 函数拆分与模块化
- `time()` / `localtime()` / `strftime()` 时间处理
- CSV 追加写入
- 简单阈值告警
- 在不依赖硬件的情况下模拟水产养殖水质监测

## 目录

```text
simulator/
├── Makefile
├── README.md
└── src/
    ├── alert.c
    ├── alert.h
    ├── config.h
    ├── logger.c
    ├── logger.h
    ├── main.c
    ├── sensor_do.c
    ├── sensor_do.h
    ├── sensor_hub.c
    ├── sensor_hub.h
    ├── sensor_ph.c
    ├── sensor_ph.h
    ├── sensor_temp.c
    ├── sensor_temp.h
    ├── sensor_turbidity.c
    ├── sensor_turbidity.h
    ├── sensor_water_level.c
    ├── sensor_water_level.h
    └── sensor_types.h
```

## 编译

```bash
cd /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/Cassian-RV/aqualog-rv/simulator
make
```

或直接用 `gcc`：

```bash
gcc -Wall -Wextra -pedantic -std=c11 -o aqualog \
src/main.c \
src/sensor_temp.c \
src/sensor_ph.c \
src/sensor_do.c \
src/sensor_turbidity.c \
src/sensor_water_level.c \
src/sensor_hub.c \
src/alert.c \
src/logger.c
```

## 运行

```bash
./aqualog
```

每轮采样结束后：

- 输入 `Enter` 继续采样
- 输入 `q` 后回车退出

程序会在当前目录生成 `aqualog.csv`。

## 当前知识点

- `struct` 用来把同一时刻的多个传感器值打包
- 通过“传结构体指针”让函数修改一整份数据
- 用 `time_t` 保存时间，用格式化函数再输出成字符串
- 文件用追加模式 `"a"` 写入，不覆盖历史日志
- 每个传感器各自独立成模块，主程序只负责调度

## 本阶段建议你主动练习

- 把阈值改成更严格，观察告警变化
- 让某个传感器失败概率变高，观察错误处理
- 把采样间隔从 5 秒改成 2 秒
- 暂时删除一个字段，再自己补回来

## 建议记录的收获

- 为什么 `SensorData` 适合做主数据结构
- 为什么主程序不直接写所有传感器逻辑
- 为什么日志写文件要先写表头
- 为什么纯标准 C 下不容易做“随时按 q 即退出”
