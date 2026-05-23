# 2026-05-23 Judge Parameter Contract

## 1. Bug 现象

在 `tests/test_judge.c` 中调用 judge 函数时，多次把 `float` 和 `PondRecord` 混用。

典型错误：

```c
oxygen_status(record)
needs_aeration(record.oxygen)
```

编译器提示参数类型不匹配。

## 2. 触发输入

测试文件中构造了 `PondRecord record`，但调用函数时没有严格按照函数声明传参。

相关函数合同：

```c
const char *oxygen_status(float oxygen);
int needs_aeration(PondRecord record);
```

## 3. 根因

没有先读函数声明中的参数类型，而是凭语义直觉调用函数。

- `oxygen_status()` 只判断一个氧气数值，所以参数是 `float`。
- `needs_aeration()` 判断一条池塘记录是否需要增氧，所以参数是 `PondRecord`。

## 4. 修复方式

按函数合同传参：

```c
oxygen_status(record.oxygen)
needs_aeration(record)
```

## 5. 复发预防

调用函数前先做三步检查：

```text
1. 看函数名：它想做什么？
2. 看参数类型：它要一个字段，还是一整条记录？
3. 看返回值：它返回状态文本，还是判断结果？
```

## 6. 对应 C / 408 知识点

- C：函数声明、函数定义、形参类型、实参类型、结构体传参。
- 数据结构：结构体字段访问。
- 编译原理：编译器在语义分析阶段检查参数类型。
- 软件工程：接口合同。

## 7. 是否转化为测试用例

是。`tests/test_judge.c` 已覆盖：

- `oxygen_status(record.oxygen)`
- `needs_aeration(record)`

## 8. 是否转化为 SRS 卡片

建议转化。

SRS 问题：

```text
Q: 为什么 oxygen_status(record.oxygen) 正确，而 needs_aeration(record.oxygen) 错误？
A: oxygen_status 的形参是 float，所以传氧气字段；needs_aeration 的形参是 PondRecord，所以传整条记录。
```
