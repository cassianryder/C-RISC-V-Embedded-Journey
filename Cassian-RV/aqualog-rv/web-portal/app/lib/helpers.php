<?php

function asset_url($path)
{
	return 'assets/' . ltrim($path, '/');
}

function page_title($title, array $config)
{
	return $title . ' | ' . $config['app_name'];
}

function nav_is_active($current_page, $target_page)
{
	return $current_page === $target_page ? 'active' : '';
}

function nav_icon($target_page)
{
	$map = [
		'home' => 'home',
		'dashboard' => 'chart pie',
		'devices' => 'microchip',
		'alerts' => 'warning circle',
		'analytics' => 'project diagram',
		'history' => 'chart line',
		'control' => 'sliders horizontal',
		'cards' => 'clone outline',
		'backup' => 'database',
		'logs' => 'clipboard list',
	];

	return isset($map[$target_page]) ? $map[$target_page] : 'circle';
}

function h($value)
{
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function format_status_class($status)
{
	$status = strtolower((string) $status);

	if ($status === 'critical' || $status === 'offline')
		return 'status-critical';
	if ($status === 'warning' || $status === 'maintenance')
		return 'status-warning';
	if ($status === 'healthy' || $status === 'online' || $status === 'normal' || $status === 'queued' || $status === 'sent' || $status === 'acknowledged' || $status === 'executed')
		return 'status-healthy';

	return 'status-neutral';
}

function semantic_label_class($status)
{
	$status = strtolower((string) $status);

	if ($status === 'critical' || $status === 'offline' || $status === 'error')
		return 'red';
	if ($status === 'warning' || $status === 'maintenance')
		return 'orange';
	if ($status === 'healthy' || $status === 'online' || $status === 'normal' || $status === 'queued' || $status === 'sent' || $status === 'acknowledged' || $status === 'executed' || $status === 'success')
		return 'green';

	return 'grey';
}

function chinese_status_text($status)
{
	$status = strtolower((string) $status);

	$map = [
		'healthy' => '正常',
		'normal' => '正常',
		'success' => '正常',
		'online' => '在线',
		'warning' => '预警',
		'maintenance' => '维护中',
		'critical' => '严重',
		'error' => '异常',
		'offline' => '离线',
		'queued' => '排队中',
		'sent' => '已发送',
		'acknowledged' => '已确认',
		'executed' => '已执行',
		'failed' => '执行失败',
		'start' => '启动',
		'stop' => '停止',
		'manual_override' => '人工接管',
		'trigger_alarm' => '触发告警',
	];

	return isset($map[$status]) ? $map[$status] : (string) $status;
}

function chinese_device_type($type)
{
	$type = strtolower(trim((string) $type));

	$map = [
		'risc-v collector' => 'RISC-V 采集节点',
		'actuator' => '执行设备',
		'underwater camera' => '水下摄像头',
	];

	return isset($map[$type]) ? $map[$type] : (string) $type;
}

function chinese_alert_token($token)
{
	$token = trim((string) $token);

	$map = [
		'temperature_high' => '温度偏高',
		'temperature_error' => '温度采样异常',
		'ph_out_of_range' => 'pH 超出范围',
		'do_low' => '溶氧偏低',
		'turbidity_high' => '浊度偏高',
		'water_level_low' => '水位偏低',
		'water_level_error' => '水位采样异常',
	];

	return isset($map[$token]) ? $map[$token] : str_replace('_', ' ', $token);
}

function flash_message()
{
	if (!isset($_SESSION['flash_message']))
		return null;

	$message = $_SESSION['flash_message'];
	unset($_SESSION['flash_message']);

	return $message;
}

function request_method()
{
	if (!isset($_SERVER['REQUEST_METHOD']))
		return 'GET';

	return strtoupper((string) $_SERVER['REQUEST_METHOD']);
}

function initialize_theme()
{
	$theme = 'day';

	if (isset($_GET['theme']) && in_array($_GET['theme'], ['day', 'night'], true))
		$theme = $_GET['theme'];
	else if (isset($_SESSION['theme']) && in_array($_SESSION['theme'], ['day', 'night'], true))
		$theme = $_SESSION['theme'];

	$_SESSION['theme'] = $theme;
}

function current_theme()
{
	return isset($_SESSION['theme']) ? $_SESSION['theme'] : 'day';
}

function body_theme_class()
{
	return current_theme() === 'night' ? 'theme-night' : 'theme-day';
}

function body_theme_name()
{
	return current_theme() === 'night' ? 'dark' : 'light';
}

function is_cli()
{
	return PHP_SAPI === 'cli';
}

function current_url_path()
{
	$script = isset($_SERVER['PHP_SELF']) ? basename((string) $_SERVER['PHP_SELF']) : 'index.php';

	if ($script === '')
		return 'index.php';

	return $script;
}

function locale_switch_url($locale)
{
	$query = $_GET;
	$query['lang'] = $locale;

	return current_url_path() . '?' . http_build_query($query);
}

function theme_switch_url($theme)
{
	$query = $_GET;
	$query['theme'] = $theme;

	return current_url_path() . '?' . http_build_query($query);
}

function next_theme()
{
	return current_theme() === 'night' ? 'day' : 'night';
}

function next_theme_url()
{
	return theme_switch_url(next_theme());
}

function url_with_locale($path, array $extra = [])
{
	$params = array_merge(['lang' => current_locale()], $extra);

	return $path . '?' . http_build_query($params);
}

function selected_card_titles()
{
	if (!isset($_SESSION['selected_card_titles']) || !is_array($_SESSION['selected_card_titles']))
		return [];

	return array_values(array_unique(array_filter($_SESSION['selected_card_titles'], 'is_string')));
}

function default_selected_parameter_keys()
{
	return ['ph', 'do_value', 'temperature', 'turbidity'];
}

function selected_parameter_keys()
{
	if (!isset($_SESSION['selected_parameter_keys']) || !is_array($_SESSION['selected_parameter_keys'])) {
		$_SESSION['selected_parameter_keys'] = default_selected_parameter_keys();
	}

	return array_values(array_unique(array_filter($_SESSION['selected_parameter_keys'], 'is_string')));
}

function is_parameter_selected($key)
{
	return in_array((string) $key, selected_parameter_keys(), true);
}

function set_parameter_selected($key, $selected)
{
	$key = trim((string) $key);
	if ($key === '')
		return;

	$current = selected_parameter_keys();

	if ($selected) {
		$current[] = $key;
		$_SESSION['selected_parameter_keys'] = array_values(array_unique($current));
		return;
	}

	$_SESSION['selected_parameter_keys'] = array_values(array_filter($current, function ($item) use ($key) {
		return $item !== $key;
	}));
}

function set_all_parameter_selected($selected)
{
	if ($selected) {
		$keys = [];
		foreach (parameter_templates() as $template) {
			$keys[] = $template['key'];
		}
		$_SESSION['selected_parameter_keys'] = $keys;
		return;
	}

	$_SESSION['selected_parameter_keys'] = [];
}

function reorder_selected_parameter_keys(array $ordered_keys)
{
	$allowed = [];
	foreach (parameter_templates() as $template) {
		$allowed[] = $template['key'];
	}

	$clean = [];
	foreach ($ordered_keys as $key) {
		$key = trim((string) $key);
		if ($key !== '' && in_array($key, $allowed, true) && !in_array($key, $clean, true))
			$clean[] = $key;
	}

	foreach (selected_parameter_keys() as $key) {
		if (!in_array($key, $clean, true))
			$clean[] = $key;
	}

	$_SESSION['selected_parameter_keys'] = $clean;
}

function pond_options()
{
	$ponds = [];
	for ($i = 1; $i <= 10; $i++) {
		$key = 'pond_' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
		$ponds[] = [
			'code' => $key,
			'name' => '塘口 ' . $i,
		];
	}

	return $ponds;
}

function current_pond_code()
{
	$allowed = array_map(function ($pond) {
		return $pond['code'];
	}, pond_options());

	if (isset($_GET['pond']) && in_array($_GET['pond'], $allowed, true)) {
		$_SESSION['current_pond_code'] = $_GET['pond'];
		return $_GET['pond'];
	}

	if (isset($_SESSION['current_pond_code']) && in_array($_SESSION['current_pond_code'], $allowed, true))
		return $_SESSION['current_pond_code'];

	$_SESSION['current_pond_code'] = 'pond_01';
	return 'pond_01';
}

function pond_name($pond_code)
{
	foreach (pond_options() as $pond) {
		if ($pond['code'] === $pond_code)
			return $pond['name'];
	}

	return (string) $pond_code;
}

function is_card_selected($title)
{
	return in_array((string) $title, selected_card_titles(), true);
}

function set_card_selected($title, $selected)
{
	$title = trim((string) $title);
	if ($title === '')
		return;

	$current = selected_card_titles();

	if ($selected) {
		$current[] = $title;
		$_SESSION['selected_card_titles'] = array_values(array_unique($current));
		return;
	}

	$_SESSION['selected_card_titles'] = array_values(array_filter($current, function ($item) use ($title) {
		return $item !== $title;
	}));
}

function normalize_float($value)
{
	if ($value === null || $value === '' || strtoupper((string) $value) === 'NA')
		return null;

	return (float) $value;
}

function sensor_bounds()
{
	return [
		'temperature' => ['min' => 15.0, 'max' => 35.0],
		'ph' => ['min' => 6.0, 'max' => 9.0],
		'do_value' => ['min' => 0.0, 'max' => 15.0],
		'turbidity' => ['min' => 0.0, 'max' => 100.0],
		'water_level' => ['min' => 0.0, 'max' => 200.0],
		'ammonia_nitrogen' => ['min' => 0.0, 'max' => 1.0],
		'nitrite' => ['min' => 0.0, 'max' => 0.5],
		'salinity' => ['min' => 0.0, 'max' => 5.0],
		'alkalinity' => ['min' => 80.0, 'max' => 200.0],
	];
}

function sample_quality_score(array $sample)
{
	$score = 100;
	$alert_text = isset($sample['alert_text']) ? (string) $sample['alert_text'] : '';

	if (isset($sample['temperature']) && $sample['temperature'] !== null && $sample['temperature'] > 32.0)
		$score -= 10;
	if (isset($sample['ph']) && $sample['ph'] !== null && ($sample['ph'] < 6.5 || $sample['ph'] > 8.5))
		$score -= 15;
	if (isset($sample['do_value']) && $sample['do_value'] !== null && $sample['do_value'] < 4.0)
		$score -= 25;
	if (isset($sample['turbidity']) && $sample['turbidity'] !== null && $sample['turbidity'] > 70.0)
		$score -= 12;
	if (isset($sample['water_level']) && $sample['water_level'] !== null && $sample['water_level'] < 30.0)
		$score -= 8;
	if (strpos($alert_text, 'error') !== false)
		$score -= 18;

	if ($score < 0)
		$score = 0;

	return $score;
}

function metric_status_by_name($name, $value)
{
	if ($value === null)
		return 'critical';

	switch ($name) {
	case 'temperature':
		return $value > 32.0 ? 'warning' : 'healthy';
	case 'ph':
		return ($value < 6.5 || $value > 8.5) ? 'warning' : 'healthy';
	case 'do_value':
		return $value < 4.0 ? 'critical' : 'healthy';
	case 'turbidity':
		return $value > 70.0 ? 'warning' : 'healthy';
	case 'water_level':
		return $value < 30.0 ? 'warning' : 'healthy';
	case 'ammonia_nitrogen':
		return $value > 0.30 ? 'warning' : 'healthy';
	case 'nitrite':
		return $value > 0.10 ? 'warning' : 'healthy';
	case 'salinity':
		return ($value < 1.0 || $value > 3.5) ? 'warning' : 'healthy';
	case 'alkalinity':
		return ($value < 100.0 || $value > 180.0) ? 'warning' : 'healthy';
	default:
		return 'neutral';
	}
}

function build_line_points(array $samples, $field, $min, $max, $width = 520, $height = 160)
{
	$count = count($samples);
	$points = [];

	if ($count === 0)
		return '';

	foreach ($samples as $index => $sample) {
		$value = isset($sample[$field]) ? $sample[$field] : null;
		if ($value === null)
			$value = $min;

		$ratio = ($value - $min) / max(($max - $min), 0.0001);
		if ($ratio < 0)
			$ratio = 0;
		if ($ratio > 1)
			$ratio = 1;

		$x = $count === 1 ? 0 : ($index / ($count - 1)) * $width;
		$y = $height - ($ratio * $height);

		$points[] = round($x, 2) . ',' . round($y, 2);
	}

	return implode(' ', $points);
}

function build_chart_dots(array $samples, $field, $min, $max, $width = 520, $height = 160)
{
	$count = count($samples);
	$dots = [];

	if ($count === 0)
		return $dots;

	foreach ($samples as $index => $sample) {
		$value = isset($sample[$field]) ? normalize_float($sample[$field]) : null;
		if ($value === null)
			continue;

		$ratio = ($value - $min) / max(($max - $min), 0.0001);
		if ($ratio < 0)
			$ratio = 0;
		if ($ratio > 1)
			$ratio = 1;

		$x = $count === 1 ? 0 : ($index / ($count - 1)) * $width;
		$y = $height - ($ratio * $height);

		$dots[] = [
			'x' => round($x, 2),
			'y' => round($y, 2),
			'value' => number_format((float) $value, 2),
			'time' => isset($sample['sampled_at']) ? date('H:i:s', strtotime($sample['sampled_at'])) : '',
		];
	}

	return $dots;
}

function metric_percent($field, $value)
{
	if ($value === null)
		return 0;

	$bounds = sensor_bounds();
	if (!isset($bounds[$field]))
		return 0;

	$min = $bounds[$field]['min'];
	$max = $bounds[$field]['max'];
	$ratio = ((float) $value - $min) / max(($max - $min), 0.0001);

	if ($ratio < 0)
		$ratio = 0;
	if ($ratio > 1)
		$ratio = 1;

	return (int) round($ratio * 100);
}

function gauge_dash_array($percent, $circumference = 339.292)
{
	$filled = ($circumference * max(0, min((int) $percent, 100))) / 100;
	return round($filled, 2) . ' ' . round($circumference - $filled, 2);
}

function metric_label($field)
{
	$map = [
		'temperature' => t('metric_temperature'),
		'ph' => t('metric_ph'),
		'do_value' => t('metric_do'),
		'turbidity' => t('metric_turbidity'),
		'water_level' => t('metric_water_level'),
	];

	return isset($map[$field]) ? $map[$field] : $field;
}

function fixed_core_metric_fields()
{
	return ['ph', 'do_value', 'temperature', 'turbidity'];
}

function metric_band_text($name, $value)
{
	if ($value === null)
		return '暂无数据';

	switch ($name) {
	case 'temperature':
		if ($value > 32.0)
			return '偏高';
		if ($value < 20.0)
			return '偏低';
		return '正常';
	case 'ph':
		if ($value > 8.5)
			return '偏高';
		if ($value < 6.5)
			return '偏低';
		return '正常';
	case 'do_value':
		if ($value < 4.0)
			return '偏低';
		return '正常';
	case 'turbidity':
		if ($value > 70.0)
			return '偏高';
		return '正常';
	case 'ammonia_nitrogen':
		if ($value > 0.30)
			return '偏高';
		return '正常';
	case 'nitrite':
		if ($value > 0.10)
			return '偏高';
		return '正常';
	case 'salinity':
		if ($value > 3.50)
			return '偏高';
		if ($value < 1.00)
			return '偏低';
		return '正常';
	case 'alkalinity':
		if ($value > 180.0)
			return '偏高';
		if ($value < 100.0)
			return '偏低';
		return '正常';
	default:
		return '正常';
	}
}

function metric_band_class($name, $value)
{
	if ($value === null)
		return 'no-data';

	switch ($name) {
	case 'temperature':
		if ($value > 32.0)
			return 'high';
		if ($value < 20.0)
			return 'low';
		return 'normal';
	case 'ph':
		if ($value > 8.5)
			return 'high';
		if ($value < 6.5)
			return 'low';
		return 'normal';
	case 'do_value':
		return $value < 4.0 ? 'low' : 'normal';
	case 'turbidity':
		return $value > 70.0 ? 'high' : 'normal';
	case 'ammonia_nitrogen':
		return $value > 0.30 ? 'high' : 'normal';
	case 'nitrite':
		return $value > 0.10 ? 'high' : 'normal';
	case 'salinity':
		if ($value > 3.50)
			return 'high';
		if ($value < 1.00)
			return 'low';
		return 'normal';
	case 'alkalinity':
		if ($value > 180.0)
			return 'high';
		if ($value < 100.0)
			return 'low';
		return 'normal';
	default:
		return 'normal';
	}
}

function parameter_templates()
{
	return [
		['key' => 'ph', 'field' => 'ph', 'title' => 'pH', 'unit' => '', 'note' => '酸碱平衡核心指标', 'style' => 'healthy'],
		['key' => 'do_value', 'field' => 'do_value', 'title' => '溶氧', 'unit' => 'mg/L', 'note' => '重点关注凌晨和雨后波动', 'style' => 'healthy'],
		['key' => 'ammonia_nitrogen', 'field' => 'ammonia_nitrogen', 'title' => '氨氮', 'unit' => 'mg/L', 'note' => '关注残饵积累和氨氮上升风险', 'style' => 'warning'],
		['key' => 'nitrite', 'field' => 'nitrite', 'title' => '亚盐', 'unit' => 'mg/L', 'note' => '结合换水和增氧判断亚盐风险', 'style' => 'warning'],
		['key' => 'turbidity', 'field' => 'turbidity', 'title' => '浊度', 'unit' => 'NTU', 'note' => '观察藻相和悬浮物变化', 'style' => 'neutral'],
		['key' => 'temperature', 'field' => 'temperature', 'title' => '温度', 'unit' => '°C', 'note' => '白天夜间需要对照查看', 'style' => 'healthy'],
		['key' => 'water_level', 'field' => 'water_level', 'title' => '水位', 'unit' => 'cm', 'note' => '用于判断塘口水位波动', 'style' => 'neutral'],
		['key' => 'salinity', 'field' => 'salinity', 'title' => '盐度', 'unit' => 'ppt', 'note' => '监控换水和盐度波动', 'style' => 'neutral'],
		['key' => 'alkalinity', 'field' => 'alkalinity', 'title' => '碱度', 'unit' => 'mg/L', 'note' => '用于判断水体缓冲能力', 'style' => 'neutral'],
	];
}

function parameter_template_by_key($key)
{
	foreach (parameter_templates() as $template) {
		if ($template['key'] === $key)
			return $template;
	}

	return null;
}

function selected_parameter_templates()
{
	$selected = selected_parameter_keys();
	$templates = [];

	foreach (parameter_templates() as $template) {
		if (in_array($template['key'], $selected, true))
			$templates[] = $template;
	}

	return $templates;
}

function parameter_card_status(array $template, $value)
{
	if ($template['field'] === null)
		return 'neutral';

	return metric_status_by_name($template['field'], $value);
}

function parameter_value_text(array $template, $value)
{
	if ($template['field'] === null)
		return '未接入';
	if ($value === null)
		return '暂无数据';

	return number_format((float) $value, 2);
}

function parameter_band_text(array $template, $value)
{
	if ($template['field'] === null)
		return '待接入';

	return metric_band_text($template['field'], $value);
}

function parameter_band_class(array $template, $value)
{
	if ($template['field'] === null)
		return 'no-data';

	return metric_band_class($template['field'], $value);
}

function build_parameter_card(array $template, $sample)
{
	$value = null;

	if ($template['field'] !== null && $sample !== null && isset($sample[$template['field']]))
		$value = normalize_float($sample[$template['field']]);

	return [
		'key' => $template['key'],
		'title' => $template['title'],
		'field' => $template['field'],
		'unit' => $template['unit'],
		'note' => $template['note'],
		'value' => $value,
		'display_value' => parameter_value_text($template, $value),
		'status' => parameter_card_status($template, $value),
		'band_text' => parameter_band_text($template, $value),
		'band_class' => parameter_band_class($template, $value),
	];
}
