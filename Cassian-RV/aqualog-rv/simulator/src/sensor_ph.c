/*
 * File: sensor_ph.c
 * Purpose: 模拟 pH 传感器读数，帮助练习函数拆分和失败返回值。
 * Learning: 指针输出参数、随机模拟。
 * Date: 2026-04-10
 */

#include <stdlib.h>

#include "config.h"
#include "sensor_ph.h"

static double random_double(double min, double max)
{
	double scale;

	scale = (double) rand() / (double) RAND_MAX;
	return min + (max - min) * scale;
}

int read_ph(double *value)
{
	if (value == NULL)
		return 0;

	if ((rand() % 100) < SENSOR_FAIL_PERCENT)
		return 0;

	*value = random_double(PH_MIN_VALUE, PH_MAX_VALUE);
	return 1;
}
