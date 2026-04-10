/*
 * File: logger.h
 * Purpose: 负责 CSV 表头和日志追加写入。
 * Learning: 文件打开模式、时间格式化、模块封装。
 * Date: 2026-04-10
 */

#ifndef LOGGER_H
#define LOGGER_H

#include "sensor_types.h"

int ensure_log_header(const char *file_name);
int append_log_entry(const char *file_name, const SensorData *data, const char *alert_message);

#endif
