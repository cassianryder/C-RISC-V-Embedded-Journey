/*
 * File: sensor_water_level.c
 * Purpose: 模拟水位传感器读数。
 * Learning: 继续巩固返回值 + 输出参数的写法。
 * Date: 2026-04-10
 */

#include <stdlib.h>

#include "config.h"
#include "sensor_water_level.h"

static double random_double(double min, double max)
{
	double scale;

	scale = (double) rand() / (double) RAND_MAX;
	return min + (max - min) * scale;
}

int read_water_level(double *value)
{
	if (value == NULL)
		return 0;

	if ((rand() % 100) < SENSOR_FAIL_PERCENT)
		return 0;

	*value = random_double(WATER_LEVEL_MIN_VALUE, WATER_LEVEL_MAX_VALUE);
	return 1;
}
