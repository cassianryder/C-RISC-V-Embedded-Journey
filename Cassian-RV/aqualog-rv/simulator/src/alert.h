/*
 * File: alert.h
 * Purpose: 负责判断本轮数据是否需要告警，并生成结果字符串。
 * Learning: 字符串拼接、条件判断、模块职责分离。
 * Date: 2026-04-10
 */

#ifndef ALERT_H
#define ALERT_H

#include "sensor_types.h"

void build_alert_message(const SensorData *data, char *buffer, int buffer_size);
void print_console_status(const SensorData *data, const char *alert_message);

#endif
