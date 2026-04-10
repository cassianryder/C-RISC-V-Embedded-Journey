/*
 * File: sensor_turbidity.c
 * Purpose: 模拟浊度传感器读数。
 * Learning: 继续练习范围内随机值生成。
 * Date: 2026-04-10
 */

#include <stdlib.h>

#include "config.h"
#include "sensor_turbidity.h"

static double random_double(double min, double max)
{
	double scale;

	scale = (double) rand() / (double) RAND_MAX;
	return min + (max - min) * scale;
}

int read_turbidity(double *value)
{
	if (value == NULL)
		return 0;

	if ((rand() % 100) < SENSOR_FAIL_PERCENT)
		return 0;

	*value = random_double(TURBIDITY_MIN_VALUE, TURBIDITY_MAX_VALUE);
	return 1;
}
