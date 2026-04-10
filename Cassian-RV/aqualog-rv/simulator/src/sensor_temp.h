/*
 * File: sensor_temp.h
 * Purpose: 提供水温传感器模拟接口。
 * Learning: 一个模块只暴露必要函数。
 * Date: 2026-04-10
 */

#ifndef SENSOR_TEMP_H
#define SENSOR_TEMP_H

int read_temperature(double *value);

#endif
