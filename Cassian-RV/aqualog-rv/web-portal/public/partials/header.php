<?php
$flash = flash_message();
$user = current_user();
?>
<!DOCTYPE html>
<html lang="<?= h(current_locale()); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= h(page_title($page_title, $config)); ?></title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.5.0/dist/semantic.min.css">
	<link rel="stylesheet" href="<?= h(asset_url('css/styles.css')); ?>">
</head>
<body class="<?= h(body_theme_class()); ?>" data-theme="<?= h(body_theme_name()); ?>">
	<div class="portal-shell">
		<div class="ui grid portal-grid">
			<div class="four wide computer five wide large screen sixteen wide mobile column portal-sidebar-column">
				<aside class="portal-sidebar">
					<div class="brand-block">
						<div class="brand-mark">ARV</div>
						<div>
							<p class="portal-eyebrow"><?= h(t('brand_subtitle')); ?></p>
							<h1><?= h(t('brand_name')); ?></h1>
						</div>
					</div>
					<div class="ui vertical fluid menu portal-menu">
						<a class="item <?= h(nav_is_active($current_page, 'home')); ?>" href="<?= h(url_with_locale('index.php')); ?>"><i class="<?= h(nav_icon('home')); ?> icon"></i><span><?= h(t('nav_overview')); ?></span></a>
						<a class="item <?= h(nav_is_active($current_page, 'dashboard')); ?>" href="<?= h(url_with_locale('dashboard.php')); ?>"><i class="<?= h(nav_icon('dashboard')); ?> icon"></i><span><?= h(t('nav_dashboard')); ?></span></a>
						<a class="item <?= h(nav_is_active($current_page, 'devices')); ?>" href="<?= h(url_with_locale('devices.php')); ?>"><i class="<?= h(nav_icon('devices')); ?> icon"></i><span><?= h(t('nav_devices')); ?></span></a>
						<a class="item <?= h(nav_is_active($current_page, 'alerts')); ?>" href="<?= h(url_with_locale('alerts.php')); ?>"><i class="<?= h(nav_icon('alerts')); ?> icon"></i><span><?= h(t('nav_alerts')); ?></span></a>
						<a class="item <?= h(nav_is_active($current_page, 'analytics')); ?>" href="<?= h(url_with_locale('analytics.php')); ?>"><i class="<?= h(nav_icon('analytics')); ?> icon"></i><span><?= h(t('nav_analytics')); ?></span></a>
						<a class="item <?= h(nav_is_active($current_page, 'history')); ?>" href="<?= h(url_with_locale('history.php')); ?>"><i class="<?= h(nav_icon('history')); ?> icon"></i><span><?= h(t('nav_history')); ?></span></a>
						<a class="item <?= h(nav_is_active($current_page, 'control')); ?>" href="<?= h(url_with_locale('control.php')); ?>"><i class="<?= h(nav_icon('control')); ?> icon"></i><span><?= h(t('nav_control')); ?></span></a>
						<a class="item <?= h(nav_is_active($current_page, 'cards')); ?>" href="<?= h(url_with_locale('cards.php')); ?>"><i class="<?= h(nav_icon('cards')); ?> icon"></i><span><?= h(t('nav_cards')); ?></span></a>
						<a class="item <?= h(nav_is_active($current_page, 'backup')); ?>" href="<?= h(url_with_locale('backup.php')); ?>"><i class="<?= h(nav_icon('backup')); ?> icon"></i><span><?= h(t('nav_backup')); ?></span></a>
						<a class="item <?= h(nav_is_active($current_page, 'logs')); ?>" href="<?= h(url_with_locale('logs.php')); ?>"><i class="<?= h(nav_icon('logs')); ?> icon"></i><span><?= h(t('nav_logs')); ?></span></a>
					</div>
				</aside>
			</div>
			<div class="twelve wide computer eleven wide large screen sixteen wide mobile column portal-main-column">
				<main class="portal-main">
					<div class="ui segment card portal-topbar">
						<div class="portal-topbar-row">
							<div class="portal-title-row portal-title-panel">
								<div>
									<p class="portal-eyebrow"><?= h($page_tagline); ?></p>
									<h2><?= h($page_heading); ?></h2>
									<p class="portal-title-note">面向塘口实时巡检、告警联动与设备控制的一体化监控界面</p>
								</div>
							</div>
							<div class="portal-toolbar">
								<div class="portal-toolbar-strip">
									<a class="ui icon basic button portal-theme-button" href="<?= h(next_theme_url()); ?>" aria-label="<?= h(current_theme() === 'night' ? t('theme_day') : t('theme_night')); ?>">
										<span class="theme-icon-stack" aria-hidden="true">
											<i class="sun outline icon theme-sun"></i>
											<i class="moon icon theme-moon"></i>
										</span>
									</a>
									<span class="ui <?= h(semantic_label_class(database_is_available($config) ? 'success' : 'warning')); ?> label"><?= database_is_available($config) ? h(t('db_connected')) : h(t('sample_mode')); ?></span>
									<?php if ($user !== null): ?>
										<div class="ui compact menu portal-user-menu">
											<div class="item"><?= h($user['display_name']); ?></div>
											<a class="item" href="<?= h(url_with_locale('logout.php')); ?>"><?= h(t('nav_logout')); ?></a>
										</div>
									<?php else: ?>
										<a class="ui button" href="<?= h(url_with_locale('login.php')); ?>"><?= h(t('nav_login')); ?></a>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<?php if ($flash !== null): ?>
						<div class="ui info message"><?= h($flash); ?></div>
					<?php endif; ?>
