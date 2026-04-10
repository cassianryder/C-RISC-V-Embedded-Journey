/*
 * File: sensor_temp.c
 * Purpose: 模拟水温传感器读数，并保留失败场景，练习错误处理。
 * Learning: 指针传参、随机数、范围映射。
 * Date: 2026-04-10
 */

#include <stdlib.h>

#include "config.h"
#include "sensor_temp.h"

static double random_double(double min, double max)
{
	double scale;

	scale = (double) rand() / (double) RAND_MAX;
	return min + (max - min) * scale;
}

int read_temperature(double *value)
{
	if (value == NULL)
		return 0;

	if ((rand() % 100) < SENSOR_FAIL_PERCENT)
		return 0;

	*value = random_double(TEMP_MIN_VALUE, TEMP_MAX_VALUE);
	return 1;
}
