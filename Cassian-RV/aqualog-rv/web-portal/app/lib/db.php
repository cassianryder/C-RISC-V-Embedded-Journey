<?php

function get_pdo(array $config)
{
	static $pdo = null;

	if ($pdo !== null) {
		return $pdo;
	}

	$dsn = sprintf(
		'mysql:host=%s;port=%d;dbname=%s;charset=%s',
		$config['db']['host'],
		$config['db']['port'],
		$config['db']['database'],
		$config['db']['charset']
	);

	if (isset($config['db']['unix_socket']) && $config['db']['unix_socket'] !== '') {
		$dsn = sprintf(
			'mysql:unix_socket=%s;dbname=%s;charset=%s',
			$config['db']['unix_socket'],
			$config['db']['database'],
			$config['db']['charset']
		);
	}

	try {
		$pdo = new PDO(
			$dsn,
			$config['db']['username'],
			$config['db']['password'],
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			]
		);
	} catch (Throwable $exception) {
		$pdo = false;
	}

	return $pdo;
}

function database_is_available(array $config)
{
	return get_pdo($config) !== false;
}
