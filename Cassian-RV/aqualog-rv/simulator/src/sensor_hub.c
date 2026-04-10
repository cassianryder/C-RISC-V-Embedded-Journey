/*
 * File: sensor_hub.c
 * Purpose: 采集一整组传感器数据，并把成功/失败状态写入结构体。
 * Learning: 结构体字段赋值、指针传参、模块组合。
 * Date: 2026-04-10
 */

#include <stddef.h>
#include <time.h>

#include "sensor_do.h"
#include "sensor_hub.h"
#include "sensor_ph.h"
#include "sensor_temp.h"
#include "sensor_turbidity.h"
#include "sensor_water_level.h"

void collect_sensor_data(SensorData *data)
{
	if (data == NULL)
		return;

	data->timestamp = time(NULL);

	data->temperature_ok = read_temperature(&data->temperature);
	data->ph_ok = read_ph(&data->ph);
	data->do_ok = read_do(&data->do_value);
	data->turbidity_ok = read_turbidity(&data->turbidity);
	data->water_level_ok = read_water_level(&data->water_level);
}
