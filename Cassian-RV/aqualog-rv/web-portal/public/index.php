<?php
require_once __DIR__ . '/../app/bootstrap.php';

$current_page = 'home';
$page_title = t('nav_overview');
$page_heading = t('overview_title');
$page_tagline = t('overview_tagline');

$overview = fetch_overview($config);
$alerts = fetch_alerts($config);
$predictions = fetch_predictions($config);

require __DIR__ . '/partials/header.php';
?>
<section class="hero-panel">
	<div class="hero-copy">
		<p class="eyebrow"><?= h(t('production_vision')); ?></p>
		<h3><?= h(t('vision_heading')); ?></h3>
		<p><?= h(t('vision_body')); ?></p>
		<div class="hero-actions">
			<a class="button-primary" href="<?= h(url_with_locale('dashboard.php')); ?>"><?= h(t('open_dashboard')); ?></a>
			<a class="button-secondary" href="<?= h(url_with_locale('analytics.php')); ?>"><?= h(t('view_prediction_layer')); ?></a>
		</div>
	</div>
	<div class="hero-card-stack">
		<div class="glass-card">
			<p class="eyebrow"><?= h(t('current_water_quality_score')); ?></p>
			<div class="big-number"><?= h($overview['water_quality_score']); ?></div>
			<p>Site: <?= h($overview['site_name']); ?></p>
		</div>
		<div class="glass-card accent-card">
			<p class="eyebrow"><?= h(t('automation_mode')); ?></p>
			<h4><?= h($overview['farm_mode']); ?></h4>
			<p><?= h($overview['active_alerts']); ?> alerts, <?= h($overview['online_devices']); ?> devices</p>
		</div>
	</div>
</section>

<section class="grid-two">
	<div class="content-card">
		<h3><?= h(t('system_capability_map')); ?></h3>
		<div class="feature-grid">
			<div class="feature-box">
				<h4><?= h(t('telemetry')); ?></h4>
				<p><?= h(t('telemetry_desc')); ?></p>
			</div>
			<div class="feature-box">
				<h4><?= h(t('prediction')); ?></h4>
				<p><?= h(t('prediction_desc')); ?></p>
			</div>
			<div class="feature-box">
				<h4><?= h(t('actuation')); ?></h4>
				<p><?= h(t('actuation_desc')); ?></p>
			</div>
			<div class="feature-box">
				<h4><?= h(t('visual_intelligence')); ?></h4>
				<p><?= h(t('visual_desc')); ?></p>
			</div>
		</div>
	</div>
	<div class="content-card">
		<h3><?= h(t('latest_alert_focus')); ?></h3>
		<?php foreach ($alerts as $alert): ?>
			<div class="alert-row">
				<span class="pill <?= h(format_status_class($alert['severity'])); ?>"><?= h($alert['severity']); ?></span>
				<div>
					<strong><?= h($alert['title']); ?></strong>
					<p><?= h($alert['message']); ?></p>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section class="content-card">
	<h3><?= h(t('prediction_snapshot')); ?></h3>
	<div class="prediction-grid">
		<?php foreach ($predictions as $prediction): ?>
			<div class="prediction-card">
				<p class="eyebrow"><?= h($prediction['window']); ?></p>
				<h4><?= h($prediction['model']); ?></h4>
				<p><?= h($prediction['result']); ?></p>
				<span class="pill status-neutral"><?= h($prediction['confidence']); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
