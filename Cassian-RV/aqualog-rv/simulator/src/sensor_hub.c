/*
 * File: sensor_hub.c
 * Purpose: 采集一整组传感器数据，并把成功/失败状态写入结构体。
 * Learning: 结构体字段赋值、指针传参、模块组合。
 * Date: 2026-04-10
 */

#include <stddef.h>
#include <stdio.h>
#include <time.h>

#include "config.h"
#include "sensor_do.h"
#include "sensor_hub.h"
#include "sensor_ph.h"
#include "sensor_temp.h"
#include "sensor_turbidity.h"
#include "sensor_water_level.h"

static double clamp_value(double value, double min_value, double max_value)
{
	if (value < min_value)
		return min_value;
	if (value > max_value)
		return max_value;

	return value;
}

static void fill_derived_parameters(SensorData *data)
{
	double ammonia_value;
	double nitrite_value;
	double salinity_value;
	double alkalinity_value;

	ammonia_value = 0.08;
	if (data->temperature_ok)
		ammonia_value += (data->temperature - 24.0) * 0.015;
	if (data->turbidity_ok)
		ammonia_value += data->turbidity * 0.0015;
	if (data->do_ok && data->do_value < 5.0)
		ammonia_value += 0.08;

	nitrite_value = 0.03;
	if (data->do_ok && data->do_value < 4.5)
		nitrite_value += 0.05;
	if (data->ph_ok && data->ph > 7.8)
		nitrite_value += 0.02;
	if (data->turbidity_ok)
		nitrite_value += data->turbidity * 0.0005;

	salinity_value = 1.6;
	if (data->water_level_ok)
		salinity_value += (200.0 - data->water_level) * 0.003;
	if (data->temperature_ok)
		salinity_value += (data->temperature - 25.0) * 0.01;

	alkalinity_value = 118.0;
	if (data->ph_ok)
		alkalinity_value += (data->ph - 7.0) * 12.0;
	if (data->water_level_ok)
		alkalinity_value += (data->water_level - 100.0) * 0.08;

	data->ammonia_nitrogen = clamp_value(ammonia_value, AMMONIA_MIN_VALUE, AMMONIA_MAX_VALUE);
	data->nitrite = clamp_value(nitrite_value, NITRITE_MIN_VALUE, NITRITE_MAX_VALUE);
	data->salinity = clamp_value(salinity_value, SALINITY_MIN_VALUE, SALINITY_MAX_VALUE);
	data->alkalinity = clamp_value(alkalinity_value, ALKALINITY_MIN_VALUE, ALKALINITY_MAX_VALUE);

	data->ammonia_nitrogen_ok = 1;
	data->nitrite_ok = 1;
	data->salinity_ok = 1;
	data->alkalinity_ok = 1;
}

void collect_sensor_data(SensorData *data)
{
	if (data == NULL)
		return;

	snprintf(data->pond_code, (size_t) sizeof(data->pond_code), "%s", POND_CODE);
	data->timestamp = time(NULL);

	data->temperature_ok = read_temperature(&data->temperature);
	data->ph_ok = read_ph(&data->ph);
	data->do_ok = read_do(&data->do_value);
	data->turbidity_ok = read_turbidity(&data->turbidity);
	data->water_level_ok = read_water_level(&data->water_level);

	fill_derived_parameters(data);
}
