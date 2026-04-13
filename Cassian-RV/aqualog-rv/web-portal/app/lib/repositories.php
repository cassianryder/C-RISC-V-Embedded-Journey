<?php

function fetch_latest_sample(array $config, $pond_code = null)
{
	if ($pond_code === null)
		$pond_code = current_pond_code();

	$pdo = get_pdo($config);

	if ($pdo === false) {
		$samples = sample_history_data($pond_code);
		return end($samples);
	}

	try {
		$statement = $pdo->prepare(
			"SELECT sampled_at, pond_code, temperature, ph, do_value, turbidity, water_level,
			        ammonia_nitrogen, nitrite, salinity, alkalinity, alert_text
			 FROM telemetry_samples
			 WHERE pond_code = :pond_code
			 ORDER BY sampled_at DESC
			 LIMIT 1"
		);
		$statement->execute(['pond_code' => $pond_code]);
		$row = $statement->fetch();
	} catch (Throwable $exception) {
		$row = null;
	}

	return $row ?: null;
}

function fetch_recent_telemetry(array $config, $limit = 24, $pond_code = null)
{
	if ($pond_code === null)
		$pond_code = current_pond_code();

	$pdo = get_pdo($config);

	if ($pdo === false) {
		$samples = sample_history_data($pond_code);
		return array_slice(array_reverse($samples), 0, $limit);
	}

	$statement = $pdo->prepare(
		"SELECT sampled_at, pond_code, temperature, ph, do_value, turbidity, water_level,
		        ammonia_nitrogen, nitrite, salinity, alkalinity, alert_text
		 FROM telemetry_samples
		 WHERE pond_code = :pond_code
		 ORDER BY sampled_at DESC
		 LIMIT :limit_value"
	);
	$statement->bindValue(':pond_code', $pond_code, PDO::PARAM_STR);
	$statement->bindValue(':limit_value', (int) $limit, PDO::PARAM_INT);
	try {
		$statement->execute();
		$rows = $statement->fetchAll();
	} catch (Throwable $exception) {
		return [];
	}

	return $rows ?: [];
}

function fetch_telemetry_history(array $config, $limit = 24, $pond_code = null)
{
	$rows = fetch_recent_telemetry($config, $limit, $pond_code);
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
		$translated_tokens = array_map('chinese_alert_token', $tokens);

		$rows[] = [
			'severity' => $severity,
			'title' => $translated_tokens[0],
			'message' => '采样时间 ' . $sample['sampled_at'] . '，触发告警：' . implode('、', $translated_tokens),
			'time' => date('H:i', strtotime($sample['sampled_at'])),
		];
	}

	if (empty($rows))
		return sample_alerts_data();

	return array_slice($rows, 0, 8);
}

function fetch_overview(array $config)
{
	$sample = fetch_latest_sample($config, current_pond_code());
	$devices = fetch_devices($config);

	if ($sample === null)
		return sample_overview_data();

	return [
		'site_name' => '养虾通示范虾场',
		'farm_mode' => 'Hybrid Expert + ML',
		'water_quality_score' => sample_quality_score($sample),
		'active_alerts' => count(build_alert_rows_from_sample_rows(fetch_recent_telemetry($config, 8, current_pond_code()))),
		'online_devices' => count($devices),
		'prediction_confidence' => 92,
		'last_sync' => $sample['sampled_at'],
	];
}

function fetch_latest_readings(array $config)
{
	$sample = fetch_latest_sample($config, current_pond_code());
	return build_readings_from_sample($sample);
}

function fetch_alerts(array $config)
{
	$telemetry = fetch_recent_telemetry($config, 12, current_pond_code());
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
	$samples = fetch_telemetry_history($config, 8, current_pond_code());

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

function fetch_history_series(array $config, $limit = 24, $pond_code = null)
{
	if ($pond_code === null)
		$pond_code = current_pond_code();

	$samples = fetch_telemetry_history($config, $limit, $pond_code);
	return !empty($samples) ? $samples : sample_history_data($pond_code);
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
		 (sampled_at, pond_code, temperature, ph, do_value, turbidity, water_level,
		  ammonia_nitrogen, nitrite, salinity, alkalinity, alert_text)
		 VALUES (:sampled_at, :pond_code, :temperature, :ph, :do_value, :turbidity, :water_level,
		         :ammonia_nitrogen, :nitrite, :salinity, :alkalinity, :alert_text)
		 ON DUPLICATE KEY UPDATE
		 pond_code = VALUES(pond_code),
		 temperature = VALUES(temperature),
		 ph = VALUES(ph),
		 do_value = VALUES(do_value),
		 turbidity = VALUES(turbidity),
		 water_level = VALUES(water_level),
		 ammonia_nitrogen = VALUES(ammonia_nitrogen),
		 nitrite = VALUES(nitrite),
		 salinity = VALUES(salinity),
		 alkalinity = VALUES(alkalinity),
		 alert_text = VALUES(alert_text)"
	);

	try {
		return $statement->execute([
			'sampled_at' => $payload['sampled_at'],
			'pond_code' => isset($payload['pond_code']) && $payload['pond_code'] !== '' ? $payload['pond_code'] : 'pond_01',
			'temperature' => normalize_float($payload['temperature']),
			'ph' => normalize_float($payload['ph']),
			'do_value' => normalize_float($payload['do_value']),
			'turbidity' => normalize_float($payload['turbidity']),
			'water_level' => normalize_float($payload['water_level']),
			'ammonia_nitrogen' => isset($payload['ammonia_nitrogen']) ? normalize_float($payload['ammonia_nitrogen']) : null,
			'nitrite' => isset($payload['nitrite']) ? normalize_float($payload['nitrite']) : null,
			'salinity' => isset($payload['salinity']) ? normalize_float($payload['salinity']) : null,
			'alkalinity' => isset($payload['alkalinity']) ? normalize_float($payload['alkalinity']) : null,
			'alert_text' => isset($payload['alert_text']) && $payload['alert_text'] !== '' ? $payload['alert_text'] : 'normal',
		]);
	} catch (Throwable $exception) {
		return false;
	}
}

function log_system_event(array $config, $level, $source, $message)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return false;

	try {
		$statement = $pdo->prepare(
			"INSERT INTO system_logs (log_level, log_source, message)
			 VALUES (:log_level, :log_source, :message)"
		);
		return $statement->execute([
			'log_level' => $level,
			'log_source' => $source,
			'message' => $message,
		]);
	} catch (Throwable $exception) {
		return false;
	}
}

function fetch_custom_cards(array $config)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return sample_custom_cards();

	try {
		$rows = $pdo->query(
			"SELECT card_title, card_value, card_note, card_style
			 FROM custom_cards
			 ORDER BY display_order ASC, id DESC"
		)->fetchAll();
	} catch (Throwable $exception) {
		return sample_custom_cards();
	}

	return $rows ?: [];
}

function save_custom_card(array $config, $title, $value, $note, $style)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return true;

	try {
		$order = (int) $pdo->query("SELECT COALESCE(MAX(display_order), 0) + 1 AS next_order FROM custom_cards")->fetch()['next_order'];
		$statement = $pdo->prepare(
			"INSERT INTO custom_cards (card_title, card_value, card_note, card_style, display_order)
			 VALUES (:card_title, :card_value, :card_note, :card_style, :display_order)"
		);
		$result = $statement->execute([
			'card_title' => $title,
			'card_value' => $value,
			'card_note' => $note,
			'card_style' => $style,
			'display_order' => $order,
		]);
		if ($result)
			log_system_event($config, 'info', 'custom_cards', 'Added custom card: ' . $title);
		return $result;
	} catch (Throwable $exception) {
		return false;
	}
}

function delete_custom_card(array $config, $title)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return false;

	try {
		$statement = $pdo->prepare(
			"DELETE FROM custom_cards
			 WHERE card_title = :card_title
			 ORDER BY id DESC
			 LIMIT 1"
		);
		$result = $statement->execute([
			'card_title' => $title,
		]);
		if ($result)
			log_system_event($config, 'warning', 'custom_cards', 'Removed custom card: ' . $title);
		return $result;
	} catch (Throwable $exception) {
		return false;
	}
}

function fetch_backup_snapshots(array $config)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return sample_backup_snapshots();

	try {
		$rows = $pdo->query(
			"SELECT file_name, file_path, created_by, backup_status, created_at
			 FROM backup_snapshots
			 ORDER BY created_at DESC"
		)->fetchAll();
	} catch (Throwable $exception) {
		return sample_backup_snapshots();
	}

	return $rows ?: [];
}

function backups_directory()
{
	$storage_dir = __DIR__ . '/../../storage/backups';

	if (!is_dir($storage_dir))
		mkdir($storage_dir, 0775, true);

	return $storage_dir;
}

function database_cli_auth_options(array $config)
{
	$options = [];

	if (isset($config['db']['unix_socket']) && $config['db']['unix_socket'] !== '')
		$options[] = '--socket=' . escapeshellarg($config['db']['unix_socket']);
	else {
		$options[] = '--host=' . escapeshellarg($config['db']['host']);
		$options[] = '--port=' . (int) $config['db']['port'];
	}

	$options[] = '-u' . escapeshellarg($config['db']['username']);

	return implode(' ', $options);
}

function database_cli_env_prefix(array $config)
{
	if (!isset($config['db']['password']) || $config['db']['password'] === '')
		return '';

	return 'MYSQL_PWD=' . escapeshellarg($config['db']['password']) . ' ';
}

function configured_binary(array $config, $key, $default_name)
{
	if (isset($config['bin'][$key]) && is_string($config['bin'][$key]) && $config['bin'][$key] !== '')
		return $config['bin'][$key];

	return $default_name;
}

function run_shell_command($command, &$output_lines, &$exit_code)
{
	$output_lines = [];
	exec($command . ' 2>&1', $output_lines, $exit_code);
	return trim(implode("\n", $output_lines));
}

function create_database_backup(array $config, $operator)
{
	$backup_dir = backups_directory();
	if (!is_dir($backup_dir))
		return ['ok' => false, 'message' => 'Backup directory missing'];

	$file_name = 'aqualog_rv_' . date('Ymd_His') . '.sql';
	$file_path = $backup_dir . '/' . $file_name;
	$mysqldump = configured_binary($config, 'mysqldump', 'mysqldump');
	$auth = database_cli_auth_options($config);
	$env_prefix = database_cli_env_prefix($config);
	$error_file = tempnam(sys_get_temp_dir(), 'aqualog_backup_err_');

	$command = sprintf(
		"%s%s --single-transaction --quick --skip-lock-tables --no-tablespaces --set-gtid-purged=OFF %s %s > %s 2> %s",
		$env_prefix,
		escapeshellcmd($mysqldump),
		$auth,
		escapeshellarg($config['db']['database']),
		escapeshellarg($file_path),
		escapeshellarg($error_file)
	);

	exec($command, $lines, $exit_code);
	$output = $error_file !== false && file_exists($error_file) ? trim((string) file_get_contents($error_file)) : '';
	if ($error_file !== false && file_exists($error_file))
		unlink($error_file);
	if ($exit_code !== 0 || !file_exists($file_path) || filesize($file_path) === 0) {
		if (file_exists($file_path))
			unlink($file_path);
	}

	$pdo = get_pdo($config);

	if ($pdo !== false) {
		$status = ($exit_code === 0 && file_exists($file_path)) ? 'success' : 'failed';
		try {
			$statement = $pdo->prepare(
				"INSERT INTO backup_snapshots (file_name, file_path, created_by, backup_status)
				 VALUES (:file_name, :file_path, :created_by, :backup_status)"
			);
			$statement->execute([
				'file_name' => $file_name,
				'file_path' => $file_path,
				'created_by' => $operator,
				'backup_status' => $status,
			]);
		} catch (Throwable $exception) {
		}
	}

	$success = ($exit_code === 0 && file_exists($file_path));
	log_system_event(
		$config,
		$success ? 'info' : 'error',
		'backup',
		$success ? ('Backup created: ' . $file_name) : ('Backup failed: ' . $file_name . ($output !== '' ? ' | ' . $output : ''))
	);

	return [
		'ok' => $success,
		'message' => $success ? ('Backup created: ' . $file_name) : ('Backup failed' . ($output !== '' ? ': ' . $output : '')),
	];
}

function restore_database_backup(array $config, $file_name, $operator)
{
	$backup_dir = backups_directory();
	$file_path = realpath($backup_dir . '/' . basename($file_name));

	if ($file_path === false)
		return ['ok' => false, 'message' => 'Backup file not found'];

	$mysql = configured_binary($config, 'mysql', 'mysql');
	$auth = database_cli_auth_options($config);
	$env_prefix = database_cli_env_prefix($config);
	$error_file = tempnam(sys_get_temp_dir(), 'aqualog_restore_err_');
	$command = sprintf(
		"%s%s %s %s < %s 2> %s",
		$env_prefix,
		escapeshellcmd($mysql),
		$auth,
		escapeshellarg($config['db']['database']),
		escapeshellarg($file_path),
		escapeshellarg($error_file)
	);

	exec($command, $lines, $exit_code);
	$output = $error_file !== false && file_exists($error_file) ? trim((string) file_get_contents($error_file)) : '';
	if ($error_file !== false && file_exists($error_file))
		unlink($error_file);
	log_system_event(
		$config,
		$exit_code === 0 ? 'warning' : 'error',
		'restore',
		$exit_code === 0
			? ('Backup restored: ' . basename($file_path) . ' by ' . $operator)
			: ('Restore failed: ' . basename($file_path) . ($output !== '' ? ' | ' . $output : ''))
	);

	return [
		'ok' => $exit_code === 0,
		'message' => $exit_code === 0 ? ('Backup restored: ' . basename($file_path)) : ('Restore failed' . ($output !== '' ? ': ' . $output : '')),
	];
}

function fetch_system_logs(array $config, $limit = 50)
{
	$pdo = get_pdo($config);

	if ($pdo === false)
		return sample_system_logs();

	try {
		$statement = $pdo->prepare(
			"SELECT log_level, log_source, message, created_at
			 FROM system_logs
			 ORDER BY created_at DESC
			 LIMIT :limit_value"
		);
		$statement->bindValue(':limit_value', (int) $limit, PDO::PARAM_INT);
		$statement->execute();
		$rows = $statement->fetchAll();
	} catch (Throwable $exception) {
		return sample_system_logs();
	}

	return $rows ?: [];
}
