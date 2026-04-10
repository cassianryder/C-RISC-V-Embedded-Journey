/*
 * File: logger.c
 * Purpose: 以 CSV 形式记录采样结果，方便后续分析和移植。
 * Learning: fopen/fprintf/fclose、追加写入、时间字符串格式化。
 * Date: 2026-04-10
 */

#include <stdio.h>
#include <string.h>
#include <time.h>

#include "logger.h"

static void format_timestamp(time_t raw_time, char *buffer, int buffer_size)
{
	struct tm *local_time_info;

	if (buffer == NULL || buffer_size <= 0)
		return;

	local_time_info = localtime(&raw_time);
	if (local_time_info == NULL) {
		snprintf(buffer, (size_t) buffer_size, "%s", "1970-01-01 00:00:00");
		return;
	}

	strftime(buffer, (size_t) buffer_size, "%Y-%m-%d %H:%M:%S", local_time_info);
}

static void write_value_or_na(FILE *fp, int ok, double value, const char *format)
{
	if (ok)
		fprintf(fp, format, value);
	else
		fprintf(fp, "NA");
}

int ensure_log_header(const char *file_name)
{
	FILE *fp;
	int first_char;

	fp = fopen(file_name, "r");
	if (fp == NULL) {
		fp = fopen(file_name, "w");
		if (fp == NULL)
			return 0;

		fprintf(fp, "timestamp,temperature,ph,do,turbidity,water_level,alert\n");
		fclose(fp);
		return 1;
	}

	first_char = fgetc(fp);
	fclose(fp);

	if (first_char != EOF)
		return 1;

	fp = fopen(file_name, "w");
	if (fp == NULL)
		return 0;

	fprintf(fp, "timestamp,temperature,ph,do,turbidity,water_level,alert\n");
	fclose(fp);
	return 1;
}

int append_log_entry(const char *file_name, const SensorData *data, const char *alert_message)
{
	FILE *fp;
	char timestamp_text[32];

	if (file_name == NULL || data == NULL || alert_message == NULL)
		return 0;

	fp = fopen(file_name, "a");
	if (fp == NULL)
		return 0;

	format_timestamp(data->timestamp, timestamp_text, (int) sizeof(timestamp_text));

	fprintf(fp, "%s,", timestamp_text);
	write_value_or_na(fp, data->temperature_ok, data->temperature, "%.2f");
	fprintf(fp, ",");
	write_value_or_na(fp, data->ph_ok, data->ph, "%.2f");
	fprintf(fp, ",");
	write_value_or_na(fp, data->do_ok, data->do_value, "%.2f");
	fprintf(fp, ",");
	write_value_or_na(fp, data->turbidity_ok, data->turbidity, "%.2f");
	fprintf(fp, ",");
	write_value_or_na(fp, data->water_level_ok, data->water_level, "%.2f");
	fprintf(fp, ",%s\n", alert_message);

	fclose(fp);
	return 1;
}
