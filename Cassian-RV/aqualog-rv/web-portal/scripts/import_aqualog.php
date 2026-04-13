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

$header = fgetcsv($handle, 0, ',', '"', '\\');
if ($header === false) {
	fclose($handle);
	fwrite(STDERR, "CSV header missing.\n");
	exit(1);
}

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

$count = 0;

while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
	if (count($row) < 7)
		continue;

	$statement->execute([
		'sampled_at' => $row[0],
		'pond_code' => count($row) >= 12 ? $row[1] : (isset($argv[2]) ? $argv[2] : 'pond_01'),
		'temperature' => normalize_float(count($row) >= 12 ? $row[2] : $row[1]),
		'ph' => normalize_float(count($row) >= 12 ? $row[3] : $row[2]),
		'do_value' => normalize_float(count($row) >= 12 ? $row[4] : $row[3]),
		'turbidity' => normalize_float(count($row) >= 12 ? $row[5] : $row[4]),
		'water_level' => normalize_float(count($row) >= 12 ? $row[6] : $row[5]),
		'ammonia_nitrogen' => count($row) >= 12 ? normalize_float($row[7]) : null,
		'nitrite' => count($row) >= 12 ? normalize_float($row[8]) : null,
		'salinity' => count($row) >= 12 ? normalize_float($row[9]) : null,
		'alkalinity' => count($row) >= 12 ? normalize_float($row[10]) : null,
		'alert_text' => (count($row) >= 12 ? $row[11] : $row[6]) === '' ? 'normal' : (count($row) >= 12 ? $row[11] : $row[6]),
	]);

	$count++;
}

fclose($handle);

echo "Imported {$count} telemetry rows from {$csv_path}\n";
