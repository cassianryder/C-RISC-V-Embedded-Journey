# AquaLog-RV Simulator

Pure C aquaculture telemetry simulator for multi-sensor logging, threshold alerting, and CSV-based observability.

面向水产养殖场景的纯 C 多传感器采集与日志系统模拟器，用于练习结构体、模块化、时间处理与文件 I/O。

水産養殖向けの純 C マルチセンサ計測・CSV ロギング・しきい値アラートのシミュレータです。

---

## Quick Navigation

- [Overview](#overview)
- [Architecture](#architecture)
- [Console Demo](#console-demo)
- [Build](#build)
- [Run](#run)
- [Resume-Ready Project Summary](#resume-ready-project-summary)
- [Learning Notes](#learning-notes)
- [Systematic Growth Plan](#systematic-growth-plan)
- [Roadmap](#roadmap)

---

## Overview

`AquaLog-RV Simulator` is the software-only MVP of `Cassian-RV`, a personal embedded systems learning project.
It simulates water-quality monitoring for aquaculture and is designed to be migrated later to a RISC-V based environment.

This version focuses on:

- multi-sensor data collection
- modular C design
- timestamp generation with standard C time APIs
- CSV log persistence
- basic threshold alerting
- failure-tolerant sensor simulation

### Project Value

For portfolio and interview purposes, this project demonstrates:

- clean C module boundaries
- structured data modeling with `struct`
- pointer-based output parameters
- defensive handling of sensor read failures
- append-only logging for operational traceability
- incremental design suitable for embedded migration

---

## 中文说明

`AquaLog-RV Simulator` 是 `Cassian-RV` 学习项目中的第一阶段软件模拟版。
它不依赖真实硬件，先在命令行中完成“多传感器采样 -> 阈值判断 -> CSV 日志记录”的完整闭环，后续再移植到 RISC-V / QEMU 环境。

这个版本适合展示以下能力：

- 用纯标准 C 组织中小型项目
- 用结构体统一管理一轮采样数据
- 用指针传参实现“返回状态 + 写回结果”
- 用模块拆分降低主程序复杂度
- 用 CSV 日志保留可观测性
- 在读取失败时保持程序持续运行

---

## 日本語概要

`AquaLog-RV Simulator` は、`Cassian-RV` の初期段階にあたるソフトウェアのみのシミュレータです。
実機に依存せず、コマンドライン上で「センサ取得 -> 異常判定 -> CSV ログ保存」の一連の流れを実装しています。

このバージョンで示せるポイント:

- 標準 C によるモジュール設計
- `struct` による計測データの集約
- ポインタ渡しによる出力値の更新
- センサ失敗時の継続動作
- ログ中心の観測性
- 将来の RISC-V 移植を意識した段階的設計

---

## Features

- Simulates five sensors:
  - temperature
  - pH
  - dissolved oxygen
  - turbidity
  - water level
- Records one timestamped sample per cycle
- Writes data to `aqualog.csv` in append mode
- Emits threshold-based alerts such as `ph_out_of_range` and `do_low`
- Marks failed sensor reads as `NA` in the CSV log
- Keeps the control flow simple enough for C fundamentals practice

---

## Example CSV Output

```csv
timestamp,temperature,ph,do,turbidity,water_level,alert
2026-04-10 21:29:47,NA,6.48,3.00,70.24,18.78,temperature_error;ph_out_of_range;do_low;turbidity_high;water_level_low
2026-04-10 21:29:52,26.57,7.98,0.76,47.23,116.25,do_low
```

---

## Console Demo

```text
$ ./aqualog
Cassian-RV AquaLog Simulator
sample interval: 5 seconds
log file: aqualog.csv

temperature: read_failed
ph: 6.48
do: 3.00 mg/L
turbidity: 70.24 NTU
water_level: 18.78 cm
alert: temperature_error;ph_out_of_range;do_low;turbidity_high;water_level_low

Press Enter to continue, or input q to quit:
```

This kind of terminal-first output is useful for:

- showing observable runtime behavior
- explaining alert semantics during interviews
- documenting the path from simulation to embedded deployment

---

## Architecture

```text
main.c
  -> sensor_hub.c
       -> sensor_temp.c
       -> sensor_ph.c
       -> sensor_do.c
       -> sensor_turbidity.c
       -> sensor_water_level.c
  -> alert.c
  -> logger.c
```

### Module Responsibilities

- `main.c`
  - owns the application loop
  - initializes randomness
  - coordinates collection, alerting, logging, and exit flow
- `sensor_types.h`
  - defines the shared `SensorData` model
- `sensor_hub.c`
  - performs one full collection cycle
- `sensor_*.c`
  - simulates individual sensor readings
- `alert.c`
  - converts raw values into alert semantics
- `logger.c`
  - writes header and appends CSV rows
- `config.h`
  - centralizes thresholds, ranges, and sampling interval

---

## Technical Focus

This project intentionally uses straightforward standard C patterns to reinforce fundamentals:

- `struct` for grouped state
- pointers for output parameters
- `time_t`, `localtime()`, and `strftime()` for timestamps
- `fopen()`, `fprintf()`, and `fclose()` for persistent logging
- header-based configuration for simple compile-time tuning
- modular source layout instead of monolithic `main.c`

### Current Constraints

- no threads
- no dynamic memory
- no platform-specific async input
- no hardware drivers yet

The quit flow is therefore implemented as:

- `Enter` to continue after each sample
- `q` then `Enter` to stop

This is a deliberate tradeoff to stay within beginner-friendly standard C.

---

## Directory Layout

```text
simulator/
├── Makefile
├── README.md
├── learn.md
├── progress.md
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
    ├── sensor_types.h
    ├── sensor_water_level.c
    └── sensor_water_level.h
```

---

## Build

```bash
cd /Users/a15951407904/projects/C-RISC-V-Embedded-Journey/Cassian-RV/aqualog-rv/simulator
make
```

Direct `gcc` build:

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

---

## Run

```bash
./aqualog
```

Runtime interaction:

- press `Enter` to continue sampling
- input `q` and press `Enter` to stop

The simulator writes `aqualog.csv` to the current working directory.

---

## Resume-Ready Project Summary

### English

Developed a modular pure C aquaculture telemetry simulator that collects multi-sensor water-quality data, evaluates threshold-based alerts, and appends timestamped CSV logs for observability. Designed the system with clear module boundaries, structured data models, and failure-tolerant sensor handling as a stepping stone toward future RISC-V embedded deployment.

### 中文

独立实现了一个纯 C 的水产养殖多传感器遥测模拟系统，能够完成水质数据采样、阈值告警判断和带时间戳的 CSV 日志追加写入。项目采用模块化设计、结构体建模和失败可恢复的传感器处理流程，并为后续迁移到 RISC-V 嵌入式环境预留了清晰路径。

### 日本語

純 C で水産養殖向けのマルチセンサ遠隔計測シミュレータを実装し、水質データ取得、しきい値アラート判定、タイムスタンプ付き CSV ログ出力までを一貫して構築した。モジュール分割、`struct` ベースのデータ設計、センサ失敗時の継続動作を重視し、将来の RISC-V 組み込み環境への移植を見据えた構成とした。

### Interview Talking Points

- Why `SensorData` is the central model and how it reduces control-flow complexity
- Why output parameters were chosen over single-value returns in sensor APIs
- How failed reads are represented without corrupting downstream logging semantics
- Why simulation-first development is useful before touching hardware
- How this codebase can evolve into a RISC-V or RTOS-oriented project

---

## Learning Notes

If you are reading this repository as a learning record, start here:

- [learn.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/Cassian-RV/aqualog-rv/simulator/learn.md)
- [progress.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/Cassian-RV/aqualog-rv/simulator/progress.md)

Topics covered in the guided notes:

- why `SensorData` is the core data model
- how pointer-based sensor APIs work
- why alerting and logging are split into separate modules
- how CSV append logging maps to operational thinking
- what tradeoffs were made to keep the project in standard C

---

## Systematic Growth Plan

To make this repository useful for both learning and hiring, each iteration should leave behind three kinds of artifacts:

- code artifact: the actual implementation or refactor
- engineering artifact: a clear commit message and updated README/progress note
- learning artifact: a short written reflection on what changed and why

Recommended workflow for each milestone:

1. define one small technical goal
2. implement and test it
3. record the design tradeoff in [progress.md](/Users/a15951407904/projects/C-RISC-V-Embedded-Journey/Cassian-RV/aqualog-rv/simulator/progress.md)
4. update this README if the project surface area changed
5. summarize the milestone in resume-friendly language

Suggested milestone categories:

- C fundamentals
- file I/O and logging
- configuration and error handling
- module refactoring
- embedded migration preparation
- RISC-V/QEMU adaptation

This turns the repository into a visible engineering timeline instead of a pile of exercises.

---

## Roadmap

- refine configuration handling
- add sample identifiers and richer log metadata
- reduce duplicated random-range helpers across sensors
- add unit-style test scaffolding for pure logic modules
- migrate the simulator design toward a RISC-V/QEMU target
- replace simulated sensors with hardware-backed drivers in later stages

---

## Suggested Commits

Examples of clear commit messages for future iterations:

- `feat(simulator): add sample id to sensor records`
- `refactor(simulator): extract shared random helper`
- `docs(simulator): expand multilingual project overview`
- `test(simulator): raise failure rate for error handling practice`

---

## Why This Project Matters

For a hiring manager or interviewer, this repository is not just a toy logger.
It shows a disciplined approach to learning embedded-oriented C through a scoped, incremental system:

- a real monitoring scenario
- explicit data modeling
- modular growth path
- observable outputs
- clear migration path from simulation to embedded execution

That is a stronger signal than isolated syntax exercises.
