/*
 * File: sensor_hub.h
 * Purpose: 统一组织一次完整采样，避免 main.c 直接依赖每个细节。
 * Learning: 模块调度、结构体聚合。
 * Date: 2026-04-10
 */

#ifndef SENSOR_HUB_H
#define SENSOR_HUB_H

#include "sensor_types.h"

void collect_sensor_data(SensorData *data);

#endif
