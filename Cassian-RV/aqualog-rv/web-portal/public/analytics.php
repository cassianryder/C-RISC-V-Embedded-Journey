<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'analytics';
$page_title = t('nav_analytics');
$page_heading = t('analytics_title');
$page_tagline = t('analytics_tagline');

$predictions = fetch_predictions($config);
$timeline = fetch_timeline($config);

require __DIR__ . '/partials/header.php';
?>
<section class="grid-two">
	<div class="content-card">
		<h3><?= h(t('model_outputs')); ?></h3>
		<?php foreach ($predictions as $prediction): ?>
			<div class="table-row">
				<div>
					<strong><?= h($prediction['model']); ?></strong>
					<p><?= h($prediction['result']); ?></p>
				</div>
				<div class="table-row-meta">
					<span><?= h($prediction['window']); ?></span>
					<span class="pill status-neutral"><?= h($prediction['confidence']); ?></span>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="content-card">
		<h3><?= h(t('quality_score_pattern')); ?></h3>
		<div class="timeline-chart">
			<?php foreach ($timeline as $point): ?>
				<div class="timeline-point">
					<div class="timeline-bar accent-bar" style="height: <?= h($point['value']); ?>%;"></div>
					<span><?= h($point['time']); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="content-card">
	<h3><?= h(t('planned_analytics_modules')); ?></h3>
	<div class="feature-grid">
		<div class="feature-box">
			<h4><?= h(t('water_trend_forecast')); ?></h4>
			<p><?= h(t('water_trend_forecast_desc')); ?></p>
		</div>
		<div class="feature-box">
			<h4><?= h(t('hybrid_recommendations')); ?></h4>
			<p><?= h(t('hybrid_recommendations_desc')); ?></p>
		</div>
		<div class="feature-box">
			<h4><?= h(t('shrimp_vision_insights')); ?></h4>
			<p><?= h(t('shrimp_vision_insights_desc')); ?></p>
		</div>
		<div class="feature-box">
			<h4><?= h(t('actuator_policy_tuning')); ?></h4>
			<p><?= h(t('actuator_policy_tuning_desc')); ?></p>
		</div>
	</div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
