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

$command_uuid = isset($payload['command_uuid']) ? trim((string) $payload['command_uuid']) : '';
$status = isset($payload['status']) ? trim((string) $payload['status']) : '';
$device_response = isset($payload['device_response']) ? trim((string) $payload['device_response']) : '';

if ($command_uuid === '' || $status === '') {
	http_response_code(422);
	echo json_encode(['ok' => false, 'message' => 'Missing command_uuid or status'], JSON_UNESCAPED_UNICODE);
	exit;
}

if (!update_control_command_status($config, $command_uuid, $status, $device_response)) {
	http_response_code(500);
	echo json_encode(['ok' => false, 'message' => 'Failed to update command status'], JSON_UNESCAPED_UNICODE);
	exit;
}

echo json_encode([
	'ok' => true,
	'command_uuid' => $command_uuid,
	'status' => $status,
], JSON_UNESCAPED_UNICODE);
