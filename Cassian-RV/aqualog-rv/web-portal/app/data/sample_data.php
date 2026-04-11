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
		['name' => 'Temperature', 'value' => '28.4', 'unit' => 'C', 'status' => 'healthy', 'trend' => '+0.3'],
		['name' => 'pH', 'value' => '7.3', 'unit' => '', 'status' => 'healthy', 'trend' => '-0.1'],
		['name' => 'Dissolved Oxygen', 'value' => '4.1', 'unit' => 'mg/L', 'status' => 'warning', 'trend' => '-0.5'],
		['name' => 'Turbidity', 'value' => '22', 'unit' => 'NTU', 'status' => 'healthy', 'trend' => '+2'],
		['name' => 'Water Level', 'value' => '84', 'unit' => 'cm', 'status' => 'healthy', 'trend' => '+1'],
	];
}

function sample_alerts_data()
{
	return [
		['severity' => 'critical', 'title' => 'Night-time oxygen risk', 'message' => 'Predicted DO may drop below 3.8 mg/L within 45 minutes.', 'time' => '09:12'],
		['severity' => 'warning', 'title' => 'Aerator response recommended', 'message' => 'Expert rule suggests opening pump group B for pond #2.', 'time' => '09:06'],
		['severity' => 'warning', 'title' => 'Camera visibility degraded', 'message' => 'Underwater vision stream quality dropped due to turbidity rise.', 'time' => '08:54'],
	];
}

function sample_devices_data()
{
	return [
		['name' => 'Edge Node - Milk-V Duo S', 'type' => 'RISC-V collector', 'status' => 'online', 'location' => 'Pond A', 'last_seen' => '09:18:24'],
		['name' => 'Aerator Pump A', 'type' => 'Actuator', 'status' => 'online', 'location' => 'Pond A', 'last_seen' => '09:18:02'],
		['name' => 'Aerator Pump B', 'type' => 'Actuator', 'status' => 'maintenance', 'location' => 'Pond B', 'last_seen' => '09:05:48'],
		['name' => 'Alarm Beacon', 'type' => 'Actuator', 'status' => 'online', 'location' => 'Control Room', 'last_seen' => '09:17:55'],
		['name' => 'Vision Camera 01', 'type' => 'Underwater camera', 'status' => 'online', 'location' => 'Pond A', 'last_seen' => '09:18:19'],
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

function sample_history_data()
{
	return [
		['sampled_at' => '2026-04-10 21:29:47', 'temperature' => null, 'ph' => 6.48, 'do_value' => 3.00, 'turbidity' => 70.24, 'water_level' => 18.78, 'alert_text' => 'temperature_error;ph_out_of_range;do_low;turbidity_high;water_level_low'],
		['sampled_at' => '2026-04-10 21:29:52', 'temperature' => 26.57, 'ph' => 7.98, 'do_value' => 0.76, 'turbidity' => 47.23, 'water_level' => 116.25, 'alert_text' => 'do_low'],
		['sampled_at' => '2026-04-11 08:24:51', 'temperature' => 18.73, 'ph' => 7.70, 'do_value' => 4.08, 'turbidity' => 92.75, 'water_level' => 18.56, 'alert_text' => 'turbidity_high;water_level_low'],
		['sampled_at' => '2026-04-11 08:25:19', 'temperature' => 28.64, 'ph' => 7.06, 'do_value' => 6.60, 'turbidity' => 55.28, 'water_level' => 179.60, 'alert_text' => 'normal'],
		['sampled_at' => '2026-04-11 08:25:34', 'temperature' => 32.56, 'ph' => 6.76, 'do_value' => 6.24, 'turbidity' => 32.74, 'water_level' => null, 'alert_text' => 'temperature_high;water_level_error'],
	];
}

function sample_control_commands()
{
	return [
		['device_name' => 'Aerator Pump A', 'action_name' => 'start', 'operator_name' => 'Cassian Admin', 'command_status' => 'queued', 'issued_at' => '2026-04-11 09:21:10'],
		['device_name' => 'Alarm Beacon', 'action_name' => 'trigger_alarm', 'operator_name' => 'Cassian Admin', 'command_status' => 'sent', 'issued_at' => '2026-04-11 09:18:33'],
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
