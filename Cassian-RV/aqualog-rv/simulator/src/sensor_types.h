/*
 * File: sensor_types.h
 * Purpose: 定义整个项目共享的数据结构，让各模块围绕同一份数据协作。
 * Learning: 结构体、time_t、跨模块共享类型定义。
 * Date: 2026-04-10
 */

#ifndef SENSOR_TYPES_H
#define SENSOR_TYPES_H

#include <time.h>

typedef struct {
	char pond_code[16];
	double temperature;
	double ph;
	double do_value;
	double turbidity;
	double water_level;
	double ammonia_nitrogen;
	double nitrite;
	double salinity;
	double alkalinity;
	time_t timestamp;
	int temperature_ok;
	int ph_ok;
	int do_ok;
	int turbidity_ok;
	int water_level_ok;
	int ammonia_nitrogen_ok;
	int nitrite_ok;
	int salinity_ok;
	int alkalinity_ok;
} SensorData;

#endif
