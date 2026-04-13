<?php
require_once __DIR__ . '/../../app/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');

if (request_method() !== 'POST') {
	http_response_code(405);
	echo json_encode(['ok' => false, 'message' => 'POST required'], JSON_UNESCAPED_UNICODE);
	exit;
}

$raw_body = file_get_contents('php://input');
$json = json_decode($raw_body, true);
$payload = is_array($json) ? $json : $_POST;

$required = ['sampled_at', 'temperature', 'ph', 'do_value', 'turbidity', 'water_level'];

foreach ($required as $field) {
	if (!array_key_exists($field, $payload)) {
		http_response_code(422);
		echo json_encode(['ok' => false, 'message' => 'Missing field: ' . $field], JSON_UNESCAPED_UNICODE);
		exit;
	}
}

if (!save_uploaded_telemetry($config, $payload)) {
	http_response_code(500);
	echo json_encode(['ok' => false, 'message' => 'Failed to save telemetry'], JSON_UNESCAPED_UNICODE);
	exit;
}

echo json_encode(['ok' => true, 'message' => 'Telemetry saved'], JSON_UNESCAPED_UNICODE);
