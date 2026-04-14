<?php
require_once __DIR__ . '/../../app/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');

if (request_method() !== 'GET') {
	http_response_code(405);
	echo json_encode(['ok' => false, 'message' => 'GET required'], JSON_UNESCAPED_UNICODE);
	exit;
}

$pond_code = isset($_GET['pond_code']) ? trim((string) $_GET['pond_code']) : 'pond_01';
$device_type = isset($_GET['device_type']) ? trim((string) $_GET['device_type']) : '增氧机';
$device_no = isset($_GET['device_no']) ? (int) $_GET['device_no'] : 1;
$claim = isset($_GET['claim']) && $_GET['claim'] === '1';

$queue = fetch_device_queue($config, $pond_code, $device_type, $device_no, 10, $claim);

echo json_encode([
	'ok' => true,
	'pond_code' => $pond_code,
	'device_type' => $device_type,
	'device_no' => $device_no,
	'queue_count' => count($queue),
	'commands' => $queue,
], JSON_UNESCAPED_UNICODE);
