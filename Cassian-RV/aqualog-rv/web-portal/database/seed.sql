INSERT INTO portal_overview
	(site_name, farm_mode, water_quality_score, active_alerts, online_devices, prediction_confidence, last_sync)
VALUES
	('Cassian Shrimp Farm - Demo Site', 'Auto-supervised', 86, 3, 7, 92, '2026-04-11 09:18:24');

INSERT INTO latest_metrics
	(metric_name, metric_value, metric_unit, metric_status, metric_trend, display_order)
VALUES
	('Temperature', 28.40, 'C', 'healthy', '+0.3', 1),
	('pH', 7.30, '', 'healthy', '-0.1', 2),
	('Dissolved Oxygen', 4.10, 'mg/L', 'warning', '-0.5', 3),
	('Turbidity', 22.00, 'NTU', 'healthy', '+2', 4),
	('Water Level', 84.00, 'cm', 'healthy', '+1', 5);

INSERT INTO alerts
	(severity, title, message, alert_time)
VALUES
	('critical', 'Night-time oxygen risk', 'Predicted DO may drop below 3.8 mg/L within 45 minutes.', '09:12'),
	('warning', 'Aerator response recommended', 'Expert rule suggests opening pump group B for pond #2.', '09:06'),
	('warning', 'Camera visibility degraded', 'Underwater vision stream quality dropped due to turbidity rise.', '08:54');

INSERT INTO devices
	(device_name, device_type, device_status, location, last_seen)
VALUES
	('Edge Node - Milk-V Duo S', 'RISC-V collector', 'online', 'Pond A', '09:18:24'),
	('Aerator Pump A', 'Actuator', 'online', 'Pond A', '09:18:02'),
	('Aerator Pump B', 'Actuator', 'maintenance', 'Pond B', '09:05:48'),
	('Alarm Beacon', 'Actuator', 'online', 'Control Room', '09:17:55'),
	('Vision Camera 01', 'Underwater camera', 'online', 'Pond A', '09:18:19');

INSERT INTO predictions
	(model_name, forecast_window, prediction_result, confidence)
VALUES
	('Water Quality Forecast', 'Next 2 hours', 'Moderate oxygen stress probability', '92%'),
	('Shrimp Activity Vision Model', 'Next 30 min', 'Feeding cluster density slightly below baseline', '84%'),
	('Expert Hybrid Recommendation', 'Immediate', 'Open Pump B after manual inspection', 'Rule-based');

INSERT INTO quality_timeline
	(time_label, quality_score, point_order)
VALUES
	('06:00', 72, 1),
	('08:00', 80, 2),
	('10:00', 67, 3),
	('12:00', 74, 4),
	('14:00', 90, 5);

INSERT INTO users
	(username, display_name, role, password_hash)
VALUES
	('admin', 'Cassian Admin', 'admin', '$2y$12$.acDGq9MKwZWBufAd/Gone6pUNIK/G/jtC.srX9VoXuow6CIbvWSO');

INSERT INTO control_commands
	(device_name, action_name, operator_name, command_status, issued_at)
VALUES
	('Aerator Pump A', 'start', 'Cassian Admin', 'queued', '2026-04-11 09:21:10'),
	('Alarm Beacon', 'trigger_alarm', 'Cassian Admin', 'sent', '2026-04-11 09:18:33');

INSERT INTO weather_snapshots
	(recorded_at, air_temperature, rainfall_mm, humidity, forecast_summary, stress_risk)
VALUES
	('2026-04-11 09:30:00', 19.60, 12.40, 83.00, 'Rain band expected in the next 3 hours; dissolved oxygen stress may rise overnight.', 'medium');

INSERT INTO camera_feeds
	(camera_name, location, stream_status, visibility_score, shrimp_activity_index, last_frame_at)
VALUES
	('Underwater Cam A1', 'Pond A', 'online', 76, 0.62, '2026-04-11 09:31:00'),
	('Underwater Cam B1', 'Pond B', 'warning', 54, 0.41, '2026-04-11 09:30:42');

INSERT INTO medication_recommendations
	(recommendation_title, recommendation_text, recommended_window, risk_level, status)
VALUES
	('Medication timing watch', 'Delay dosing during the current rain window and reassess after dissolved oxygen stabilizes.', 'After 14:00 if DO remains above 5.0 mg/L', 'warning', 'pending_review'),
	('Stress-sensitive feeding plan', 'Reduce aggressive feeding and observe shrimp activity after rainfall passes.', 'Next 6 hours', 'medium', 'active');

INSERT INTO custom_cards
	(card_title, card_value, card_note, card_style, display_order)
VALUES
	('今日投喂批次', '3', '上午批次已完成，夜间批次待确认', 'neutral', 1),
	('重点池塘', 'Pond B', '建议优先观察溶氧与降雨影响', 'warning', 2);

INSERT INTO system_logs
	(log_level, log_source, message)
VALUES
	('info', 'seed', 'Initial portal seed data loaded.'),
	('info', 'telemetry', 'Historical telemetry import pipeline ready.');
