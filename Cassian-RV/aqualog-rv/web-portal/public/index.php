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
<div class="portal-stack">
	<section class="ui segment card dashboard">
		<div class="ui stackable two column grid">
			<div class="ten wide column">
				<p class="portal-eyebrow"><?= h(t('production_vision')); ?></p>
				<h3><?= h(t('vision_heading')); ?></h3>
				<p class="dashboard-copy"><?= h(t('vision_body')); ?></p>
				<div class="dashboard-actions">
					<a class="ui primary button" href="<?= h(url_with_locale('dashboard.php')); ?>"><?= h(t('open_dashboard')); ?></a>
					<a class="ui button" href="<?= h(url_with_locale('analytics.php')); ?>"><?= h(t('view_prediction_layer')); ?></a>
				</div>
			</div>
			<div class="six wide column">
				<div class="dashboard-meta-grid">
					<div class="dashboard-meta-card">
						<p class="portal-eyebrow"><?= h(t('current_water_quality_score')); ?></p>
						<h4><?= h($overview['water_quality_score']); ?></h4>
						<p class="portal-muted">Site: <?= h($overview['site_name']); ?></p>
					</div>
					<div class="dashboard-meta-card">
						<p class="portal-eyebrow"><?= h(t('automation_mode')); ?></p>
						<h4><?= h($overview['farm_mode']); ?></h4>
						<p class="portal-muted"><?= h($overview['active_alerts']); ?> alerts, <?= h($overview['online_devices']); ?> devices</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<div class="ui stackable two column grid portal-section">
		<div class="column">
			<section class="ui segment card dashboard">
				<div class="dashboard-header">
					<div>
						<p class="portal-eyebrow"><?= h(t('system_capability_map')); ?></p>
						<h3><?= h(t('nav_dashboard')); ?></h3>
					</div>
				</div>
				<div class="dashboard-card-grid">
					<div class="ui segment card dashboard">
						<h4><?= h(t('telemetry')); ?></h4>
						<p class="portal-muted"><?= h(t('telemetry_desc')); ?></p>
					</div>
					<div class="ui segment card dashboard">
						<h4><?= h(t('prediction')); ?></h4>
						<p class="portal-muted"><?= h(t('prediction_desc')); ?></p>
					</div>
					<div class="ui segment card dashboard">
						<h4><?= h(t('actuation')); ?></h4>
						<p class="portal-muted"><?= h(t('actuation_desc')); ?></p>
					</div>
					<div class="ui segment card dashboard">
						<h4><?= h(t('visual_intelligence')); ?></h4>
						<p class="portal-muted"><?= h(t('visual_desc')); ?></p>
					</div>
				</div>
			</section>
		</div>
		<div class="column">
			<section class="ui segment card dashboard">
				<div class="dashboard-header">
					<div>
						<p class="portal-eyebrow"><?= h(t('latest_alert_focus')); ?></p>
						<h3><?= h(t('nav_alerts')); ?></h3>
					</div>
				</div>
				<div class="ui list portal-info-list">
					<?php foreach ($alerts as $alert): ?>
						<div class="item">
							<div class="portal-info-main">
								<strong><?= h($alert['title']); ?></strong>
								<p><?= h($alert['message']); ?></p>
							</div>
							<div class="portal-info-side">
								<div class="ui <?= h(semantic_label_class($alert['severity'])); ?> label"><?= h($alert['severity']); ?></div>
								<div><?= h($alert['time']); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
		</div>
	</div>

	<section class="ui segment card dashboard portal-section">
		<div class="dashboard-header">
			<div>
				<p class="portal-eyebrow"><?= h(t('prediction_snapshot')); ?></p>
				<h3><?= h(t('nav_analytics')); ?></h3>
			</div>
		</div>
		<div class="dashboard-card-grid">
			<?php foreach ($predictions as $prediction): ?>
				<div class="ui segment card dashboard">
					<p class="portal-eyebrow"><?= h($prediction['window']); ?></p>
					<h4><?= h($prediction['model']); ?></h4>
					<p class="portal-muted"><?= h($prediction['result']); ?></p>
					<span class="ui blue basic label"><?= h($prediction['confidence']); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
