<?php

function initialize_locale(array $config)
{
	$default = $config['default_locale'];
	$supported = $config['supported_locales'];
	$selected = $default;

	if (isset($_GET['lang']) && in_array($_GET['lang'], $supported, true))
		$selected = $_GET['lang'];
	else if (isset($_SESSION['locale']) && in_array($_SESSION['locale'], $supported, true))
		$selected = $_SESSION['locale'];

	$_SESSION['locale'] = $selected;
}

function current_locale()
{
	return isset($_SESSION['locale']) ? $_SESSION['locale'] : 'zh-CN';
}

function supported_locales()
{
	return [
		'zh-CN' => '简中',
		'zh-TW' => '繁中',
		'en' => 'EN',
		'ja' => '日本語',
	];
}

function t($key)
{
	$locale = current_locale();
	$catalog = translation_catalog();

	if (isset($catalog[$locale][$key]))
		return $catalog[$locale][$key];

	if (isset($catalog['en'][$key]))
		return $catalog['en'][$key];

	return $key;
}

function translation_catalog()
{
	return include __DIR__ . '/../translations.php';
}
