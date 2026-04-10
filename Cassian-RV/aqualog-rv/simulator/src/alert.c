/*
 * File: alert.c
 * Purpose: 统一处理阈值判断和控制台提示，保持主程序简洁。
 * Learning: 字符数组、格式化输出、逐步拼接告警信息。
 * Date: 2026-04-10
 */

#include <stdio.h>
#include <string.h>

#include "alert.h"
#include "config.h"

static void append_message(char *buffer, int buffer_size, const char *message)
{
	size_t current_length;

	if (buffer == NULL || message == NULL || buffer_size <= 0)
		return;

	current_length = strlen(buffer);

	if (current_length > 0 && current_length < (size_t) (buffer_size - 1))
		strncat(buffer, ";", (size_t) (buffer_size - 1) - strlen(buffer));

	if (strlen(buffer) < (size_t) (buffer_size - 1))
		strncat(buffer, message, (size_t) (buffer_size - 1) - strlen(buffer));
}

void build_alert_message(const SensorData *data, char *buffer, int buffer_size)
{
	if (data == NULL || buffer == NULL || buffer_size <= 0)
		return;

	buffer[0] = '\0';

	if (!data->temperature_ok)
		append_message(buffer, buffer_size, "temperature_error");
	else if (data->temperature > TEMP_ALERT_HIGH)
		append_message(buffer, buffer_size, "temperature_high");

	if (!data->ph_ok)
		append_message(buffer, buffer_size, "ph_error");
	else if (data->ph < PH_ALERT_LOW || data->ph > PH_ALERT_HIGH)
		append_message(buffer, buffer_size, "ph_out_of_range");

	if (!data->do_ok)
		append_message(buffer, buffer_size, "do_error");
	else if (data->do_value < DO_ALERT_LOW)
		append_message(buffer, buffer_size, "do_low");

	if (!data->turbidity_ok)
		append_message(buffer, buffer_size, "turbidity_error");
	else if (data->turbidity > TURBIDITY_ALERT_HIGH)
		append_message(buffer, buffer_size, "turbidity_high");

	if (!data->water_level_ok)
		append_message(buffer, buffer_size, "water_level_error");
	else if (data->water_level < WATER_LEVEL_ALERT_LOW)
		append_message(buffer, buffer_size, "water_level_low");

	if (buffer[0] == '\0')
		snprintf(buffer, (size_t) buffer_size, "%s", "normal");
}

void print_console_status(const SensorData *data, const char *alert_message)
{
	if (data == NULL || alert_message == NULL)
		return;

	printf("temperature: ");
	if (data->temperature_ok)
		printf("%.2f C\n", data->temperature);
	else
		printf("read_failed\n");

	printf("ph: ");
	if (data->ph_ok)
		printf("%.2f\n", data->ph);
	else
		printf("read_failed\n");

	printf("do: ");
	if (data->do_ok)
		printf("%.2f mg/L\n", data->do_value);
	else
		printf("read_failed\n");

	printf("turbidity: ");
	if (data->turbidity_ok)
		printf("%.2f NTU\n", data->turbidity);
	else
		printf("read_failed\n");

	printf("water_level: ");
	if (data->water_level_ok)
		printf("%.2f cm\n", data->water_level);
	else
		printf("read_failed\n");

	printf("alert: %s\n", alert_message);
}
