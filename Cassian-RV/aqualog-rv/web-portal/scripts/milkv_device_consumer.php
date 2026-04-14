<?php

if (PHP_SAPI !== 'cli') {
	fwrite(STDERR, "This script must run in CLI mode.\n");
	exit(1);
}

$options = getopt('', [
	'base-url::',
	'pond-code::',
	'device-type::',
	'device-no::',
	'interval::',
	'once',
]);

$base_url = isset($options['base-url']) ? rtrim((string) $options['base-url'], '/') : 'http://127.0.0.1:8000';
$pond_code = isset($options['pond-code']) ? (string) $options['pond-code'] : 'pond_01';
$device_type = isset($options['device-type']) ? (string) $options['device-type'] : '增氧机';
$device_no = isset($options['device-no']) ? (int) $options['device-no'] : 1;
$interval = isset($options['interval']) ? max(1, (int) $options['interval']) : 5;
$run_once = array_key_exists('once', $options);

function http_json_request($url, $method = 'GET', ?array $payload = null)
{
	$headers = [
		'Accept: application/json',
	];

	$context = [
		'http' => [
			'method' => $method,
			'ignore_errors' => true,
			'timeout' => 10,
			'header' => implode("\r\n", $headers),
		],
	];

	if ($payload !== null) {
		$headers[] = 'Content-Type: application/json; charset=UTF-8';
		$context['http']['header'] = implode("\r\n", $headers);
		$context['http']['content'] = json_encode($payload, JSON_UNESCAPED_UNICODE);
	}

	$response = @file_get_contents($url, false, stream_context_create($context));
	if ($response === false)
		return null;

	$data = json_decode($response, true);
	return is_array($data) ? $data : null;
}

function execute_device_action($device_type, $device_no, array $command)
{
	$action = isset($command['action_name']) ? (string) $command['action_name'] : '';
	$pond_code = isset($command['pond_code']) ? (string) $command['pond_code'] : 'pond_01';

	$message = sprintf(
		'[%s] %s %d 号接收到命令: %s / %s',
		date('Y-m-d H:i:s'),
		$device_type,
		$device_no,
		$pond_code,
		$action
	);
	fwrite(STDOUT, $message . "\n");

	usleep(500000);

	switch ($action) {
	case 'start':
		return ['status' => 'executed', 'device_response' => '设备已启动'];
	case 'stop':
		return ['status' => 'executed', 'device_response' => '设备已停止'];
	case 'manual_override':
		return ['status' => 'acknowledged', 'device_response' => '设备已切换人工接管'];
	case 'trigger_alarm':
		return ['status' => 'executed', 'device_response' => '设备已触发本地告警'];
	default:
		return ['status' => 'failed', 'device_response' => '未知命令，设备未执行'];
	}
}

fwrite(STDOUT, "Milk-V 设备消费者已启动\n");
fwrite(STDOUT, "目标设备: {$pond_code} / {$device_type} / {$device_no} 号\n");
fwrite(STDOUT, "队列地址: {$base_url}\n");

do {
	$query = http_build_query([
		'pond_code' => $pond_code,
		'device_type' => $device_type,
		'device_no' => $device_no,
		'claim' => 1,
	]);

	$queue = http_json_request($base_url . '/api/device_queue.php?' . $query, 'GET');

	if ($queue === null || !isset($queue['ok']) || !$queue['ok']) {
		fwrite(STDERR, '[' . date('Y-m-d H:i:s') . "] 拉取队列失败\n");
	} else if (empty($queue['commands'])) {
		fwrite(STDOUT, '[' . date('Y-m-d H:i:s') . "] 当前无待执行命令\n");
	} else {
		foreach ($queue['commands'] as $command) {
			if (!isset($command['command_uuid']))
				continue;

			http_json_request($base_url . '/api/device_ack.php', 'POST', [
				'command_uuid' => $command['command_uuid'],
				'status' => 'acknowledged',
				'device_response' => '设备已收到命令，准备执行',
			]);

			$result = execute_device_action($device_type, $device_no, $command);
			http_json_request($base_url . '/api/device_ack.php', 'POST', [
				'command_uuid' => $command['command_uuid'],
				'status' => $result['status'],
				'device_response' => $result['device_response'],
			]);
		}
	}

	if ($run_once)
		break;

	sleep($interval);
} while (true);
