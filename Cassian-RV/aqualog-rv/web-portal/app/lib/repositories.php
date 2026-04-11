<?php

function fetch_latest_sample(array $config)
{
	$pdo = get_pdo($config);

	if ($pdo === false) {
		$samples = sample_history_data();
		return end($samples);
	}

	try {
		$row = $pdo->query(
			"SELECT sampled_at, temperature, ph, do_value, turbidity, water_level, alert_text
			 FROM telemetry_samples
			 ORDER BY sampled_at DESC
			 LIMIT 1"
		)->fetch();
	} catch (Throwable $exception) {
		$row = null;
	}

	return $row ?: null;
}

function fetch_recent_telemetry(array $config, $limit = 24)
{
	$pdo = get_pdo($config);

	if ($pdo === false) {
		$samples = sample_history_data();
		return array_slice(array_reverse($samples), 0, $limit);
	}

	$statement = $pdo->prepare(
		"SELECT sampled_at, temperature, ph, do_value, turbidity, water_level, alert_text
		 FROM telemetry_samples
		 ORDER BY sampled_at DESC
		 LIMIT :limit_value"
	);
	$statement->bindValue(':limit_value', (int) $limit, PDO::PARAM_INT);
	try {
		$statement->execute();
		$rows = $statement->fetchAll();
	} catch (Throwable $exception) {
		return [];
	}

	return $rows ?: [];
}

function fetch_telemetry_history(array $config, $limit = 24)
{
	$rows = fetch_recent_telemetry($config, $limit);
	return array_reverse($rows);
}

function build_readings_from_sample($sample)
{
	if ($sample === null)
		return sample_readings_data();

	return [
		['name' => metric_label('temperature'), 'value' => $sample['temperature'] === null ? 'NA' : number_format((float) $sample['temperature'], 2), 'unit' => 'C', 'status' => metric_status_by_name('temperature', $sample['temperature']), 'trend' => '--'],
		['name' => metric_label('ph'), 'value' => $sample['ph'] === null ? 'NA' : number_format((float) $sample['ph'], 2), 'unit' => '', 'status' => metric_status_by_name('ph', $sample['ph']), 'trend' => '--'],
		['name' => metric_label('do_value'), 'value' => $sample['do_value'] === null ? 'NA' : number_format((float) $sample['do_value'], 2), 'unit' => 'mg/L', 'status' => metric_status_by_name('do_value', $sample['do_value']), 'trend' => '--'],
		['name' => metric_label('turbidity'), 'value' => $sample['turbidity'] === null ? 'NA' : number_format((float) $sample['turbidity'], 2), 'unit' => 'NTU', 'status' => metric_status_by_name('turbidity', $sample['turbidity']), 'trend' => '--'],
		['name' => metric_label('water_level'), 'value' => $sample['water_level'] === null ? 'NA' : number_format((float) $sample['water_level'], 2), 'unit' => 'cm', 'status' => metric_status_by_name('water_level', $sample['water_level']), 'trend' => '--'],
	];
}

function build_alert_rows_from_sample_rows(array $samples)
{
	$rows = [];

	foreach ($samples as $sample) {
		$alert_text = isset($sample['alert_text']) ? (string) $sample['alert_text'] : 'normal';
		if ($alert_text === '' || $alert_text === 'normal')
			continue;

		$tokens = explode(';', $alert_text);
		$severity = strpos($alert_text, 'error') !== false || strpos($alert_text, 'do_low') !== false ? 'critical' : 'warning';

		$rows[] = [
			'severity' => $severity,
			'title' => str_replace('_', ' ', $tokens[0]),
			'message' => 'Sample captured at ' . $sample['sampled_at'] . ' with alert string: ' . $alert_text,
			'time' => date('H:i', strtotime($sample['sampled_at'])),
		];
	}

	if (empty($rows))
		return sample_alerts_data();

	return array_slice($rows, 0, 8);
}

function fetch_overview(array $config)
{
	$sample = fetch_latest_sample($config);
	$devices = fetch_devices($config);

	if ($sample === null)
		return sample_overview_data();

	return [
		'site_name' => '养虾通示范虾场',
		'farm_mode' => 'Hybrid Expert + ML',
		'water_quality_score' => sample_quality_score($sample),
		'active_alerts' => count(build_alert_rows_from_sample_rows(fetch_recent_telemetry($config, 8))),
		'online_devices' => count($devices),
		'prediction_confidence' => 92,
		'last_sync' => $sample['sampled_at'],
	];
}

function fetch_latest_readings(array $config)
{
	$sample = fetch_latest_sample($config);
	return build_readings_from_sample($sample);
}

function fetch_alerts(array $config)
{
	$telemetry = fetch_recent_telemetry($config, 12);
	if (!empty($telemetry))
		return build_alert_rows_from_sample_rows($telemetry);

	$pdo = get_pdo($config);
	if ($pdo === false)
		return sample_alerts_data();

	try {
		$rows = $pdo->query(
			"SELECT severity, title, message, alert_time AS time
			 FROM alerts
			 ORDER BY created_at DESC
			 LIMIT 8"
		)->fetchAll();
	} catch (Throwable $exception) {
		return sample_alerts_data();
	}

	return $rows ?: sample_alerts_data();
}

function fetch_devices(array $config)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return sample_devices_data();

	try {
		$rows = $pdo->query(
			"SELECT device_name AS name, device_type AS type, device_status AS status, location, last_seen
			 FROM devices
			 ORDER BY device_name ASC"
		)->fetchAll();
	} catch (Throwable $exception) {
		return sample_devices_data();
	}

	return $rows ?: sample_devices_data();
}

function fetch_predictions(array $config)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return sample_predictions_data();

	try {
		$rows = $pdo->query(
			"SELECT model_name AS model, forecast_window AS window, prediction_result AS result, confidence
			 FROM predictions
			 ORDER BY created_at DESC
			 LIMIT 6"
		)->fetchAll();
	} catch (Throwable $exception) {
		return sample_predictions_data();
	}

	return $rows ?: sample_predictions_data();
}

function fetch_timeline(array $config)
{
	$samples = fetch_telemetry_history($config, 8);

	if (!empty($samples)) {
		$rows = [];
		foreach ($samples as $sample) {
			$rows[] = [
				'time' => date('H:i', strtotime($sample['sampled_at'])),
				'value' => sample_quality_score($sample),
			];
		}
		return $rows;
	}

	$pdo = get_pdo($config);
	if ($pdo === false)
		return sample_timeline_data();

	try {
		$rows = $pdo->query(
			"SELECT time_label AS time, quality_score AS value
			 FROM quality_timeline
			 ORDER BY point_order ASC"
		)->fetchAll();
	} catch (Throwable $exception) {
		return sample_timeline_data();
	}

	return $rows ?: sample_timeline_data();
}

function fetch_history_series(array $config, $limit = 24)
{
	$samples = fetch_telemetry_history($config, $limit);
	return !empty($samples) ? $samples : sample_history_data();
}

function fetch_control_commands(array $config, $limit = 10)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return sample_control_commands();

	$statement = $pdo->prepare(
		"SELECT device_name, action_name, operator_name, command_status, issued_at
		 FROM control_commands
		 ORDER BY issued_at DESC
		 LIMIT :limit_value"
	);
	$statement->bindValue(':limit_value', (int) $limit, PDO::PARAM_INT);
	try {
		$statement->execute();
		$rows = $statement->fetchAll();
	} catch (Throwable $exception) {
		return sample_control_commands();
	}

	return $rows ?: sample_control_commands();
}

function save_control_command(array $config, $device, $action, $operator)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return true;

	$statement = $pdo->prepare(
		"INSERT INTO control_commands
		 (device_name, action_name, operator_name, command_status, issued_at)
		 VALUES (:device_name, :action_name, :operator_name, :command_status, NOW())"
	);

	try {
		return $statement->execute([
			'device_name' => $device,
			'action_name' => $action,
			'operator_name' => $operator,
			'command_status' => 'queued',
		]);
	} catch (Throwable $exception) {
		return false;
	}
}

function fetch_latest_weather(array $config)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return sample_weather_data();

	try {
		$row = $pdo->query(
			"SELECT recorded_at, air_temperature, rainfall_mm, humidity, forecast_summary, stress_risk
			 FROM weather_snapshots
			 ORDER BY recorded_at DESC
			 LIMIT 1"
		)->fetch();
	} catch (Throwable $exception) {
		return sample_weather_data();
	}

	return $row ?: sample_weather_data();
}

function fetch_camera_feeds(array $config)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return sample_camera_feeds();

	try {
		$rows = $pdo->query(
			"SELECT camera_name, location, stream_status, visibility_score, shrimp_activity_index, last_frame_at
			 FROM camera_feeds
			 ORDER BY camera_name ASC"
		)->fetchAll();
	} catch (Throwable $exception) {
		return sample_camera_feeds();
	}

	return $rows ?: sample_camera_feeds();
}

function fetch_medication_recommendations(array $config)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return sample_medication_recommendations();

	try {
		$rows = $pdo->query(
			"SELECT recommendation_title, recommendation_text, recommended_window, risk_level, status
			 FROM medication_recommendations
			 ORDER BY created_at DESC
			 LIMIT 6"
		)->fetchAll();
	} catch (Throwable $exception) {
		return sample_medication_recommendations();
	}

	return $rows ?: sample_medication_recommendations();
}

function save_uploaded_telemetry(array $config, array $payload)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return false;

	$statement = $pdo->prepare(
		"INSERT INTO telemetry_samples
		 (sampled_at, temperature, ph, do_value, turbidity, water_level, alert_text)
		 VALUES (:sampled_at, :temperature, :ph, :do_value, :turbidity, :water_level, :alert_text)
		 ON DUPLICATE KEY UPDATE
		 temperature = VALUES(temperature),
		 ph = VALUES(ph),
		 do_value = VALUES(do_value),
		 turbidity = VALUES(turbidity),
		 water_level = VALUES(water_level),
		 alert_text = VALUES(alert_text)"
	);

	try {
		return $statement->execute([
			'sampled_at' => $payload['sampled_at'],
			'temperature' => normalize_float($payload['temperature']),
			'ph' => normalize_float($payload['ph']),
			'do_value' => normalize_float($payload['do_value']),
			'turbidity' => normalize_float($payload['turbidity']),
			'water_level' => normalize_float($payload['water_level']),
			'alert_text' => isset($payload['alert_text']) && $payload['alert_text'] !== '' ? $payload['alert_text'] : 'normal',
		]);
	} catch (Throwable $exception) {
		return false;
	}
}
