<?php

require_once __DIR__ . '/../app/bootstrap.php';

if (!is_cli()) {
	echo "This script is intended for CLI use.\n";
	exit(1);
}

$pdo = get_pdo($config);
if ($pdo === false) {
	fwrite(STDERR, "MySQL connection is required for import.\n");
	exit(1);
}

$default_csv = realpath(__DIR__ . '/../../simulator/aqualog.csv');
$csv_path = isset($argv[1]) ? $argv[1] : $default_csv;

if ($csv_path === false || !file_exists($csv_path)) {
	fwrite(STDERR, "CSV file not found.\n");
	exit(1);
}

$handle = fopen($csv_path, 'r');
if ($handle === false) {
	fwrite(STDERR, "Unable to open CSV file.\n");
	exit(1);
}

$header = fgetcsv($handle);
if ($header === false) {
	fclose($handle);
	fwrite(STDERR, "CSV header missing.\n");
	exit(1);
}

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

$count = 0;

while (($row = fgetcsv($handle)) !== false) {
	if (count($row) < 7)
		continue;

	$statement->execute([
		'sampled_at' => $row[0],
		'temperature' => normalize_float($row[1]),
		'ph' => normalize_float($row[2]),
		'do_value' => normalize_float($row[3]),
		'turbidity' => normalize_float($row[4]),
		'water_level' => normalize_float($row[5]),
		'alert_text' => $row[6] === '' ? 'normal' : $row[6],
	]);

	$count++;
}

fclose($handle);

echo "Imported {$count} telemetry rows from {$csv_path}\n";
