# AquaLog-RV Progress Log

Use this file to keep a durable engineering record of each learning milestone.
The goal is to turn practice into evidence: what was built, why it matters, what was learned, and what should happen next.

---

## How To Use

For each meaningful change, add one new milestone entry.

A good entry should answer:

- What did I build or change?
- Why did I change it?
- What C or embedded concept did I practice?
- What bug, limitation, or tradeoff did I notice?
- What should I do next?

---

## Milestone Template

### Milestone: `<title>`

- Date:
- Commit:
- Status:
- Scope:

### Change Summary

- 

### Technical Details

- Files touched:
- Core functions changed:
- Data structures involved:
- Build or runtime behavior affected:

### Concepts Practiced

- 

### Problems Encountered

- 

### Tradeoffs

- 

### Validation

- Build command:
- Run command:
- What I observed:

### Resume Translation

Write this milestone as one resume-ready bullet in English:

- 

Write the same idea in Chinese:

- 

Write the same idea in Japanese:

- 

### Next Step

- 

---

## Example Entry

### Milestone: Initial simulator scaffold

- Date: 2026-04-10
- Commit: `feat(simulator): add AquaLog-RV C simulator scaffold`
- Status: completed
- Scope: MVP simulator

### Change Summary

- Added a pure C simulator with five sensor modules, alert generation, and CSV logging.

### Technical Details

- Files touched: `main.c`, `sensor_hub.c`, `sensor_*.c`, `alert.c`, `logger.c`, `config.h`
- Core functions changed: `collect_sensor_data()`, `build_alert_message()`, `append_log_entry()`
- Data structures involved: `SensorData`
- Build or runtime behavior affected: command-line simulator now compiles and produces `aqualog.csv`

### Concepts Practiced

- `struct`
- pointer-based output parameters
- `time()` / `localtime()` / `strftime()`
- file append logging
- module separation

### Problems Encountered

- Standard C does not provide a clean portable way to support fully asynchronous quit input.

### Tradeoffs

- Used a prompt-after-each-sample quit flow to keep the implementation portable and beginner-friendly.

### Validation

- Build command: `make`
- Run command: `./aqualog`
- What I observed: the program collected samples, printed alert states, and appended rows to `aqualog.csv`

### Resume Translation

Write this milestone as one resume-ready bullet in English:

- Built a modular pure C telemetry simulator for aquaculture monitoring with multi-sensor sampling, threshold alerts, and CSV logging.

Write the same idea in Chinese:

- 实现了一个模块化的纯 C 水产养殖遥测模拟系统，支持多传感器采样、阈值告警和 CSV 日志记录。

Write the same idea in Japanese:

- マルチセンサ計測、しきい値アラート、CSV ログ出力を備えた純 C の水産養殖向け遠隔計測シミュレータを実装した。

### Next Step

- Add sample identifiers and reduce duplicated random helper logic.
