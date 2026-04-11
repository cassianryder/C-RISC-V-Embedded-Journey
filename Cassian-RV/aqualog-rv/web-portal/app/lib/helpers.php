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
	return $current_page === $target_page ? 'is-active' : '';
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
	if ($status === 'healthy' || $status === 'online' || $status === 'normal' || $status === 'queued' || $status === 'sent')
		return 'status-healthy';

	return 'status-neutral';
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

function url_with_locale($path, array $extra = [])
{
	$params = array_merge(['lang' => current_locale()], $extra);

	return $path . '?' . http_build_query($params);
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
