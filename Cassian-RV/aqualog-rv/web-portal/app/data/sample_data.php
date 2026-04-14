<?php

function sample_overview_data()
{
	return [
		'site_name' => '养虾通示范虾场',
		'farm_mode' => 'Hybrid Expert + ML',
		'water_quality_score' => 86,
		'active_alerts' => 3,
		'online_devices' => 7,
		'prediction_confidence' => 92,
		'last_sync' => '2026-04-11 09:18:24',
	];
}

function sample_readings_data()
{
	return [
		['name' => '温度', 'value' => '28.4', 'unit' => '°C', 'status' => 'healthy', 'trend' => '+0.3'],
		['name' => 'pH', 'value' => '7.3', 'unit' => '', 'status' => 'healthy', 'trend' => '-0.1'],
		['name' => '溶氧', 'value' => '4.1', 'unit' => 'mg/L', 'status' => 'warning', 'trend' => '-0.5'],
		['name' => '浊度', 'value' => '22', 'unit' => 'NTU', 'status' => 'healthy', 'trend' => '+2'],
		['name' => '水位', 'value' => '84', 'unit' => 'cm', 'status' => 'healthy', 'trend' => '+1'],
	];
}

function sample_alerts_data()
{
	return [
		['severity' => 'critical', 'title' => '夜间溶氧风险', 'message' => '预计 45 分钟内溶氧可能低于 3.8 mg/L。', 'time' => '09:12'],
		['severity' => 'warning', 'title' => '建议启动增氧组', 'message' => '建议优先开启 2 号塘增氧泵 B 组。', 'time' => '09:06'],
		['severity' => 'warning', 'title' => '摄像头可视度下降', 'message' => '浊度上升导致水下画面质量下降。', 'time' => '08:54'],
	];
}

function sample_devices_data()
{
	return [
		['name' => '边缘采集节点 01', 'type' => 'RISC-V collector', 'status' => 'online', 'location' => '塘口 1', 'last_seen' => '09:18:24'],
		['name' => '增氧泵 A 组', 'type' => 'Actuator', 'status' => 'online', 'location' => '塘口 1', 'last_seen' => '09:18:02'],
		['name' => '增氧泵 B 组', 'type' => 'Actuator', 'status' => 'maintenance', 'location' => '塘口 2', 'last_seen' => '09:05:48'],
		['name' => '声光报警器', 'type' => 'Actuator', 'status' => 'online', 'location' => '控制室', 'last_seen' => '09:17:55'],
		['name' => '水下摄像头 01', 'type' => 'Underwater camera', 'status' => 'online', 'location' => '塘口 1', 'last_seen' => '09:18:19'],
	];
}

function sample_predictions_data()
{
	return [
		['model' => 'Water Quality Forecast', 'window' => 'Next 2 hours', 'result' => 'Moderate oxygen stress probability', 'confidence' => '92%'],
		['model' => 'Shrimp Activity Vision Model', 'window' => 'Next 30 min', 'result' => 'Feeding cluster density slightly below baseline', 'confidence' => '84%'],
		['model' => 'Expert Hybrid Recommendation', 'window' => 'Immediate', 'result' => 'Open Pump B after manual inspection', 'confidence' => 'Rule-based'],
	];
}

function sample_timeline_data()
{
	return [
		['time' => '06:00', 'value' => 72],
		['time' => '08:00', 'value' => 80],
		['time' => '10:00', 'value' => 67],
		['time' => '12:00', 'value' => 74],
		['time' => '14:00', 'value' => 90],
	];
}

function sample_history_data($pond_code = 'pond_01')
{
	$index = (int) str_replace('pond_', '', $pond_code);
	if ($index < 1)
		$index = 1;

	$base = [
		['sampled_at' => '2026-04-10 21:29:47', 'temperature' => 26.18, 'ph' => 6.48, 'do_value' => 3.00, 'turbidity' => 70.24, 'water_level' => 18.78, 'ammonia_nitrogen' => 0.21, 'nitrite' => 0.09, 'salinity' => 1.80, 'alkalinity' => 122.00, 'alert_text' => 'do_low;turbidity_high;water_level_low'],
		['sampled_at' => '2026-04-10 21:29:52', 'temperature' => 26.57, 'ph' => 7.98, 'do_value' => 4.76, 'turbidity' => 47.23, 'water_level' => 116.25, 'ammonia_nitrogen' => 0.18, 'nitrite' => 0.06, 'salinity' => 1.82, 'alkalinity' => 124.00, 'alert_text' => 'normal'],
		['sampled_at' => '2026-04-11 08:24:51', 'temperature' => 28.73, 'ph' => 7.70, 'do_value' => 4.08, 'turbidity' => 62.75, 'water_level' => 108.56, 'ammonia_nitrogen' => 0.25, 'nitrite' => 0.11, 'salinity' => 1.85, 'alkalinity' => 126.00, 'alert_text' => 'normal'],
		['sampled_at' => '2026-04-11 08:25:19', 'temperature' => 28.64, 'ph' => 7.06, 'do_value' => 6.60, 'turbidity' => 55.28, 'water_level' => 129.60, 'ammonia_nitrogen' => 0.23, 'nitrite' => 0.08, 'salinity' => 1.88, 'alkalinity' => 129.00, 'alert_text' => 'normal'],
		['sampled_at' => '2026-04-11 08:25:34', 'temperature' => 32.56, 'ph' => 6.76, 'do_value' => 6.24, 'turbidity' => 32.74, 'water_level' => 121.34, 'ammonia_nitrogen' => 0.28, 'nitrite' => 0.12, 'salinity' => 1.90, 'alkalinity' => 131.00, 'alert_text' => 'temperature_high'],
	];

	$offset = ($index - 1) * 0.07;
	$rows = [];

	foreach ($base as $row) {
		$rows[] = [
			'sampled_at' => $row['sampled_at'],
			'pond_code' => $pond_code,
			'temperature' => round($row['temperature'] + ($index % 3) * 0.35, 2),
			'ph' => round($row['ph'] + (($index % 4) - 1) * 0.03, 2),
			'do_value' => round($row['do_value'] - (($index % 5) * 0.18), 2),
			'turbidity' => round($row['turbidity'] + ($index * 1.6), 2),
			'water_level' => round($row['water_level'] + ($index * 1.25), 2),
			'ammonia_nitrogen' => round($row['ammonia_nitrogen'] + $offset, 2),
			'nitrite' => round($row['nitrite'] + ($index * 0.01), 2),
			'salinity' => round($row['salinity'] + ($index * 0.04), 2),
			'alkalinity' => round($row['alkalinity'] + ($index * 1.8), 2),
			'alert_text' => $row['alert_text'],
		];
	}

	return $rows;
}

function sample_control_commands()
{
	return [
		['command_uuid' => 'cmd_demo_001', 'pond_code' => 'pond_01', 'device_type' => '增氧机', 'device_no' => 1, 'device_name' => '增氧机 1 号', 'action_name' => 'start', 'operator_name' => '调度中心', 'command_status' => 'queued', 'issued_at' => '2026-04-11 09:21:10', 'dispatched_at' => null, 'acknowledged_at' => null, 'executed_at' => null, 'device_response' => '等待设备拉取'],
		['command_uuid' => 'cmd_demo_002', 'pond_code' => 'pond_02', 'device_type' => '水泵', 'device_no' => 2, 'device_name' => '水泵 2 号', 'action_name' => 'start', 'operator_name' => '调度中心', 'command_status' => 'executed', 'issued_at' => '2026-04-11 09:18:33', 'dispatched_at' => '2026-04-11 09:18:40', 'acknowledged_at' => '2026-04-11 09:18:45', 'executed_at' => '2026-04-11 09:18:52', 'device_response' => '设备执行完成'],
	];
}

function sample_weather_data()
{
	return [
		'recorded_at' => '2026-04-11 09:30:00',
		'air_temperature' => 19.60,
		'rainfall_mm' => 12.40,
		'humidity' => 83.00,
		'forecast_summary' => 'Rain band expected in the next 3 hours; dissolved oxygen stress may rise overnight.',
		'stress_risk' => 'medium',
	];
}

function sample_camera_feeds()
{
	return [
		['camera_name' => 'Underwater Cam A1', 'location' => 'Pond A', 'stream_status' => 'online', 'visibility_score' => 76, 'shrimp_activity_index' => 0.62, 'last_frame_at' => '2026-04-11 09:31:00'],
		['camera_name' => 'Underwater Cam B1', 'location' => 'Pond B', 'stream_status' => 'warning', 'visibility_score' => 54, 'shrimp_activity_index' => 0.41, 'last_frame_at' => '2026-04-11 09:30:42'],
	];
}

function sample_medication_recommendations()
{
	return [
		['recommendation_title' => 'Medication timing watch', 'recommendation_text' => 'Delay dosing during the current rain window and reassess after dissolved oxygen stabilizes.', 'recommended_window' => 'After 14:00 if DO remains above 5.0 mg/L', 'risk_level' => 'warning', 'status' => 'pending_review'],
		['recommendation_title' => 'Stress-sensitive feeding plan', 'recommendation_text' => 'Reduce aggressive feeding and observe shrimp activity after rainfall passes.', 'recommended_window' => 'Next 6 hours', 'risk_level' => 'medium', 'status' => 'active'],
	];
}

function sample_custom_cards()
{
	return [];
}

function sample_backup_snapshots()
{
	return [];
}

function sample_system_logs()
{
	return [
		['log_level' => 'info', 'log_source' => 'portal', 'message' => '门户当前运行在演示模式。', 'created_at' => '2026-04-11 10:20:00'],
		['log_level' => 'info', 'log_source' => 'telemetry', 'message' => '采样导入链路已完成配置。', 'created_at' => '2026-04-11 10:21:00'],
	];
}
