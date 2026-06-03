# 2026-06-02 Main One-Shot Integration Plan

> 实际整理时间：2026-06-03  
> 任务包：`codex/daily_packets/008-main-one-shot-integration-v0.md`  
> 状态：规划 / 待执行，不记录为已完成闭环

## 1. 每日问题

- 为什么 `main` 应该先做 one-shot 集成，而不是一开始就写菜单、循环采集或真实传感器输入？
- 为什么 `main` 不能绕过 `input_cli / control / output / csv_store`，直接手写解析、判断、打印和保存逻辑？
- 为什么每一步模块调用后都要检查返回值，而不是假设前一步一定成功？
- `int main(void)` 的 `return 0` 和非 0 返回值在真实程序里分别表达什么？
- 为什么 output 模块要把结果写进 `char buffer[]`，而不是在 `main` 里到处 `printf`？
- 为什么 demo CSV 应该写入 `build/`，而不是写进源码目录或长期数据目录？
- `main` 和 unit test 的边界是什么？为什么还需要 `tests/test_main_flow.c`？
- Bug Lab 如果围绕错误路径合同设计，应该注入哪一类错误最有价值？

## 2. 解答

- one-shot 是最小可执行应用骨架：先证明一条固定输入能穿过完整模块链，再考虑循环、菜单、真实输入和硬件接入。这样失败时更容易定位是哪一层出了问题。
- `main` 的职责是编排，不是重新实现业务逻辑。解析属于 `input_cli`，控制建议属于 `control`，格式化属于 `output`，持久化属于 `csv_store`。如果 `main` 重写这些逻辑，模块化边界会被打散，后续测试也会失去意义。
- 返回值检查是工程闭环的安全阀。每一步都可能失败：解析失败、buffer 太小、CSV 打开失败。`main` 必须在失败点停下并返回非 0，而不是继续使用无效数据。
- `return 0` 表示进程正常结束，非 0 表示程序失败。后续 `make`、脚本、CI 或系统服务都可以通过退出码判断 demo 是否成功。
- `char buffer[]` 是调用方提供的输出缓冲区。output 模块只负责格式化字符串，不直接控制终端输出，这让同一份格式化结果未来可以流向终端、日志、网络或测试断言。
- `build/` 是运行产物区域。demo CSV 放在 `build/` 可以避免污染源码目录，也能提醒自己：这类文件是生成物，不应默认提交。
- unit test 验证单个模块或最小集成行为，`main` 是真实程序入口。`tests/test_main_flow.c` 应该验证模块链是否能按合同工作，而不是替代用户运行 demo。
- 有价值的 Bug Lab 应该打在“错误路径合同”上，例如某一步失败后 `main` 没有返回非 0，或者绕过某个模块导致测试无法捕获真实链路问题。

## 3. 闭环

- 当前已完成：读取 008 任务包，明确下一条主线是 `one fixed input line -> input_cli -> control -> output -> csv_store`。
- 当前未完成：尚未记录 `main.c`、`tests/test_main_flow.c`、`Makefile` 的完整实现与测试通过证据。
- 当前未完成：尚未执行 `make test_main_flow`、`make run_demo`、`make test`、`git diff --check` 的完整回归闭环。
- 当前未完成：尚未执行正式 Bug Lab 注入、触发、修复和回归。
- 后续完成标准：用户手动完成 main one-shot 集成、测试入口、run_demo 入口、Clean Baseline、Bug Lab，并将 daily 与 bug_lab 写成完成态。

## 4. 使用场景和启发思考

- 在水产项目里，one-shot main 是从“模块都能单独工作”走向“程序真正能跑起来”的第一步。
- 这一步解决的不是新算法，而是系统编排：输入从哪里来，如何变成 `PondRecord`，如何生成控制建议，如何格式化输出，如何保存成 CSV。
- 后续接入循环采集、串口、传感器、MQTT 或板子时，主链路仍然会复用这次的结构，只是把固定输入替换成真实输入源。
- 如果 one-shot 阶段不检查错误路径，未来接硬件时会出现更难定位的问题，因为真实设备失败往往不是“代码不能编译”，而是某一步返回失败后程序继续往下跑。

## 5. 408 映射

- 数据结构：`PondRecord` 作为结构化数据对象，在多个模块之间按合同流动。
- 计算机组成原理：`main`、函数调用、局部变量、返回值共同构成高级语言程序到可执行程序的基本执行路径。
- 操作系统：`main` 的返回码会成为进程退出状态，`make run_demo` / `make test_main_flow` 依赖这个退出状态判断成功或失败。
- 操作系统：demo CSV 写入 `build/` 体现文件路径、运行产物和文件 I/O 边界。
- 计算机网络：今日不涉及网络通信，但同样的 input/output 合同未来可迁移到 UART、socket 或 MQTT payload。

## 6. 下一步

- 先过 C Foundation Gate：`int main(void)`、返回码、局部变量、buffer、每步返回值检查。
- 再手动实现最小 `main.c`，只跑一条固定输入，不加菜单、不加循环。
- 然后补 `tests/test_main_flow.c` 和 Makefile 目标，先让 `make test_main_flow` 与 `make run_demo` 跑通。
- 最后再做错误路径合同 Bug Lab，不能在没有回归证据时写完成态 daily。
