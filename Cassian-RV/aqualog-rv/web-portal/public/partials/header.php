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
	<link rel="stylesheet" href="<?= h(asset_url('css/styles.css')); ?>">
</head>
<body>
	<div class="site-shell">
		<aside class="sidebar">
			<div class="brand-block">
				<div class="brand-mark">ARV</div>
				<div>
					<p class="eyebrow"><?= h(t('brand_subtitle')); ?></p>
					<h1><?= h(t('brand_name')); ?></h1>
				</div>
			</div>
			<nav class="sidebar-nav">
				<a class="<?= h(nav_is_active($current_page, 'home')); ?>" href="<?= h(url_with_locale('index.php')); ?>"><?= h(t('nav_overview')); ?></a>
				<a class="<?= h(nav_is_active($current_page, 'dashboard')); ?>" href="<?= h(url_with_locale('dashboard.php')); ?>"><?= h(t('nav_dashboard')); ?></a>
				<a class="<?= h(nav_is_active($current_page, 'devices')); ?>" href="<?= h(url_with_locale('devices.php')); ?>"><?= h(t('nav_devices')); ?></a>
				<a class="<?= h(nav_is_active($current_page, 'alerts')); ?>" href="<?= h(url_with_locale('alerts.php')); ?>"><?= h(t('nav_alerts')); ?></a>
				<a class="<?= h(nav_is_active($current_page, 'analytics')); ?>" href="<?= h(url_with_locale('analytics.php')); ?>"><?= h(t('nav_analytics')); ?></a>
				<a class="<?= h(nav_is_active($current_page, 'history')); ?>" href="<?= h(url_with_locale('history.php')); ?>"><?= h(t('nav_history')); ?></a>
				<a class="<?= h(nav_is_active($current_page, 'control')); ?>" href="<?= h(url_with_locale('control.php')); ?>"><?= h(t('nav_control')); ?></a>
			</nav>
			<div class="language-switcher">
				<?php foreach (supported_locales() as $locale_code => $locale_label): ?>
					<a class="locale-chip <?= current_locale() === $locale_code ? 'is-active' : ''; ?>" href="<?= h(locale_switch_url($locale_code)); ?>"><?= h($locale_label); ?></a>
				<?php endforeach; ?>
			</div>
			<div class="sidebar-note">
				<p><?= h(t('future_stack')); ?>:</p>
				<p><?= h(t('stack_note')); ?></p>
			</div>
		</aside>
		<main class="main-panel">
			<header class="topbar">
				<div>
					<p class="eyebrow"><?= h($page_tagline); ?></p>
					<h2><?= h($page_heading); ?></h2>
				</div>
				<div class="topbar-meta topbar-meta-stack">
					<?php if ($user !== null): ?>
						<div class="user-chip">
							<span><?= h($user['display_name']); ?></span>
							<a href="<?= h(url_with_locale('logout.php')); ?>"><?= h(t('nav_logout')); ?></a>
						</div>
					<?php else: ?>
						<a class="pill status-neutral" href="<?= h(url_with_locale('login.php')); ?>"><?= h(t('nav_login')); ?></a>
					<?php endif; ?>
					<span class="pill <?= database_is_available($config) ? 'status-healthy' : 'status-warning'; ?>">
						<?= database_is_available($config) ? h(t('db_connected')) : h(t('sample_mode')); ?>
					</span>
				</div>
			</header>
			<?php if ($flash !== null): ?>
				<div class="flash-banner"><?= h($flash); ?></div>
			<?php endif; ?>
