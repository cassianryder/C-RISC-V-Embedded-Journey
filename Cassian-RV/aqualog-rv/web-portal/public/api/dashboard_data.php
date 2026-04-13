<?php
require_once __DIR__ . '/../../app/bootstrap.php';

header('Content-Type: application/json; charset=UTF-8');

if (!is_logged_in()) {
	http_response_code(401);
	echo json_encode(['ok' => false, 'message' => '未登录'], JSON_UNESCAPED_UNICODE);
	exit;
}

$pond_code = isset($_GET['pond']) ? trim((string) $_GET['pond']) : current_pond_code();
$latest_sample = fetch_latest_sample($config, $pond_code);
$selected_templates = selected_parameter_templates();
$timeline_samples = fetch_history_series($config, 12, $pond_code);
$bounds = sensor_bounds();
$cards = [];

foreach ($selected_templates as $template) {
	$card = build_parameter_card($template, $latest_sample);

	if ($template['field'] !== null && isset($bounds[$template['field']])) {
		$card['chart_points'] = build_line_points($timeline_samples, $template['field'], $bounds[$template['field']]['min'], $bounds[$template['field']]['max'], 360, 96);
		$card['chart_dots'] = build_chart_dots($timeline_samples, $template['field'], $bounds[$template['field']]['min'], $bounds[$template['field']]['max'], 360, 96);
	} else {
		$card['chart_points'] = '';
		$card['chart_dots'] = [];
	}

	$cards[] = $card;
}

echo json_encode([
	'ok' => true,
	'pond_code' => $pond_code,
	'pond_name' => pond_name($pond_code),
	'sampled_at' => $latest_sample !== null ? $latest_sample['sampled_at'] : '',
	'cards' => $cards,
], JSON_UNESCAPED_UNICODE);
