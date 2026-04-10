/*
 * File: sensor_do.c
 * Purpose: 模拟 DO 传感器读数，并保留读取失败情况。
 * Learning: 用返回值表达成功或失败。
 * Date: 2026-04-10
 */

#include <stdlib.h>

#include "config.h"
#include "sensor_do.h"

static double random_double(double min, double max)
{
	double scale;

	scale = (double) rand() / (double) RAND_MAX;
	return min + (max - min) * scale;
}

int read_do(double *value)
{
	if (value == NULL)
		return 0;

	if ((rand() % 100) < SENSOR_FAIL_PERCENT)
		return 0;

	*value = random_double(DO_MIN_VALUE, DO_MAX_VALUE);
	return 1;
}
