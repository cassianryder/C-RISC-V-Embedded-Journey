<?php

require_once __DIR__ . '/../app/bootstrap.php';

if (!is_cli()) {
	echo "This script is intended for CLI use.\n";
	exit(1);
}

$pdo = get_pdo($config);
if ($pdo === false) {
	fwrite(STDERR, "MySQL connection is required.\n");
	exit(1);
}

$count = 0;

for ($i = 1; $i <= 10; $i++) {
	$pond_code = 'pond_' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
	$samples = sample_history_data($pond_code);

	foreach ($samples as $sample) {
		if (save_uploaded_telemetry($config, $sample))
			$count++;
	}
}

echo "Seeded {$count} telemetry rows for 10 ponds.\n";
