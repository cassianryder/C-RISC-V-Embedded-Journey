/*
 * File: config.h
 * Purpose: 集中管理模拟器的采样间隔和告警阈值，方便你练习“配置集中定义”。
 * Learning: 宏常量、头文件保护、用配置减少魔法数字。
 * Date: 2026-04-10
 */

#ifndef CONFIG_H
#define CONFIG_H

#define SAMPLE_INTERVAL_SECONDS 5

#define DEFAULT_POND_CODE "pond_01"

#define TEMP_MIN_VALUE 15.0
#define TEMP_MAX_VALUE 35.0
#define PH_MIN_VALUE 6.0
#define PH_MAX_VALUE 9.0
#define DO_MIN_VALUE 0.0
#define DO_MAX_VALUE 15.0
#define TURBIDITY_MIN_VALUE 0.0
#define TURBIDITY_MAX_VALUE 100.0
#define WATER_LEVEL_MIN_VALUE 0.0
#define WATER_LEVEL_MAX_VALUE 200.0
#define AMMONIA_MIN_VALUE 0.05
#define AMMONIA_MAX_VALUE 0.80
#define NITRITE_MIN_VALUE 0.02
#define NITRITE_MAX_VALUE 0.25
#define SALINITY_MIN_VALUE 1.20
#define SALINITY_MAX_VALUE 3.20
#define ALKALINITY_MIN_VALUE 100.0
#define ALKALINITY_MAX_VALUE 180.0

#define TEMP_ALERT_HIGH 32.0
#define PH_ALERT_LOW 6.5
#define PH_ALERT_HIGH 8.5
#define DO_ALERT_LOW 4.0
#define TURBIDITY_ALERT_HIGH 70.0
#define WATER_LEVEL_ALERT_LOW 30.0

#define SENSOR_FAIL_PERCENT 8
#define LOG_FILE_NAME "aqualog.csv"
#define PORTAL_IMPORT_COMMAND "cd ../web-portal && php scripts/import_aqualog.php"
#define PORTAL_API_URL "http://127.0.0.1:8000/api/upload_telemetry.php"

#endif
