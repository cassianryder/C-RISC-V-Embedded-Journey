/*
 * File: main.c
 * Purpose: 驱动 AquaLog-RV 模拟器主循环，串联采样、告警和日志写入。
 * Learning: 主循环设计、结构体使用、文件写入、时间处理、函数协作。
 * Date: 2026-04-10
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>

#include "alert.h"
#include "config.h"
#include "logger.h"
#include "sensor_hub.h"
#include "sensor_types.h"

static void wait_seconds(int seconds)
{
	time_t start_time;

	start_time = time(NULL);
	while (time(NULL) - start_time < seconds)
		;
}

static void print_banner(void)
{
	printf("Cassian-RV 水质模拟器\n");
	printf("采样间隔: %d 秒\n", SAMPLE_INTERVAL_SECONDS);
	printf("日志文件: %s\n\n", LOG_FILE_NAME);
}

static void format_timestamp_text(time_t raw_time, char *buffer, int buffer_size)
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

static int should_quit(void)
{
	char input[32];

	printf("按回车继续采样，输入 q 退出: ");
	if (fgets(input, (int) sizeof(input), stdin) == NULL)
		return 1;

	if (input[0] == 'q' || input[0] == 'Q')
		return 1;

	return 0;
}

static void select_pond_code(char *buffer, int buffer_size)
{
	char input[32];
	int pond_number;

	if (buffer == NULL || buffer_size <= 0)
		return;

	snprintf(buffer, (size_t) buffer_size, "%s", DEFAULT_POND_CODE);
	printf("请输入塘口编号 (1-10，默认 1): ");

	if (fgets(input, (int) sizeof(input), stdin) == NULL)
		return;

	if (input[0] == '\n' || input[0] == '\0')
		return;

	pond_number = atoi(input);
	if (pond_number < 1 || pond_number > 10) {
		printf("输入无效，已使用默认塘口 1。\n\n");
		return;
	}

	snprintf(buffer, (size_t) buffer_size, "pond_%02d", pond_number);
	printf("当前采样塘口: %s\n\n", buffer);
}

static void sync_portal_from_csv(void)
{
	int result;

	result = system(PORTAL_IMPORT_COMMAND);
	if (result != 0)
		fprintf(stderr, "warning: portal sync command failed\n");
}

static void upload_portal_telemetry(const SensorData *data, const char *alert_message)
{
	char command[2048];
	char timestamp_text[32];
	int result;

	if (data == NULL || alert_message == NULL)
		return;

	format_timestamp_text(data->timestamp, timestamp_text, (int) sizeof(timestamp_text));

	snprintf(
		command,
		(size_t) sizeof(command),
		"curl -s -o /dev/null -X POST %s "
		"-d 'sampled_at=%s' "
		"-d 'pond_code=%s' "
		"-d 'temperature=%.2f' "
		"-d 'ph=%.2f' "
		"-d 'do_value=%.2f' "
		"-d 'turbidity=%.2f' "
		"-d 'water_level=%.2f' "
		"-d 'ammonia_nitrogen=%.2f' "
		"-d 'nitrite=%.2f' "
		"-d 'salinity=%.2f' "
		"-d 'alkalinity=%.2f' "
		"-d 'alert_text=%s'",
		PORTAL_API_URL,
		timestamp_text,
		data->pond_code,
		data->temperature,
		data->ph,
		data->do_value,
		data->turbidity,
		data->water_level,
		data->ammonia_nitrogen,
		data->nitrite,
		data->salinity,
		data->alkalinity,
		alert_message
	);

	result = system(command);
	if (result != 0) {
		fprintf(stderr, "warning: portal api upload failed, fallback to csv import\n");
		sync_portal_from_csv();
	}
}

int main(void)
{
	SensorData data;
	char alert_message[128];
	char pond_code[16];

	srand((unsigned int) time(NULL));

	if (!ensure_log_header(LOG_FILE_NAME)) {
		fprintf(stderr, "failed to initialize log file: %s\n", LOG_FILE_NAME);
		return 1;
	}

	print_banner();
	select_pond_code(pond_code, (int) sizeof(pond_code));

	for (;;) {
		collect_sensor_data(&data, pond_code);
		build_alert_message(&data, alert_message, (int) sizeof(alert_message));

		if (!append_log_entry(LOG_FILE_NAME, &data, alert_message)) {
			fprintf(stderr, "failed to append log entry\n");
			return 1;
		}

		upload_portal_telemetry(&data, alert_message);

		print_console_status(&data, alert_message);
		printf("\n");

		if (should_quit())
			break;

		printf("等待 %d 秒后进行下一次采样...\n\n", SAMPLE_INTERVAL_SECONDS);
		wait_seconds(SAMPLE_INTERVAL_SECONDS);
	}

	printf("模拟器已停止\n");
	return 0;
}
