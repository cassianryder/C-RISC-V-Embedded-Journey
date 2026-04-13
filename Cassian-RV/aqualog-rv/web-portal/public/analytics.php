<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'analytics';
$page_title = t('nav_analytics');
$page_heading = t('analytics_title');
$page_tagline = t('analytics_tagline');

$predictions = fetch_predictions($config);
$timeline = fetch_timeline($config);
$samples = fetch_history_series($config, 12);
$bounds = sensor_bounds();

require __DIR__ . '/partials/header.php';
?>
<div class="ui stackable two column grid portal-section">
	<div class="column">
		<section class="ui segment card dashboard">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow"><?= h(t('model_outputs')); ?></p>
					<h3><?= h(t('prediction_snapshot')); ?></h3>
				</div>
			</div>
			<div class="ui list portal-info-list">
				<?php foreach ($predictions as $prediction): ?>
					<div class="item">
						<div class="portal-info-main">
							<strong><?= h($prediction['model']); ?></strong>
							<p><?= h($prediction['result']); ?></p>
						</div>
						<div class="portal-info-side">
							<div class="ui blue basic label"><?= h($prediction['confidence']); ?></div>
							<div><?= h($prediction['window']); ?></div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	</div>
	<div class="column">
		<section class="ui segment card dashboard chart">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow"><?= h(t('quality_score_pattern')); ?></p>
					<h3><?= h(t('water_trend_forecast')); ?></h3>
				</div>
			</div>
			<div class="chart-frame">
				<svg class="chart-svg" viewBox="0 0 520 220" preserveAspectRatio="none">
					<g class="chart-grid">
						<line x1="0" y1="40" x2="520" y2="40"></line>
						<line x1="0" y1="110" x2="520" y2="110"></line>
						<line x1="0" y1="180" x2="520" y2="180"></line>
					</g>
					<polyline class="chart-line" points="<?= h(build_line_points($samples, 'do_value', $bounds['do_value']['min'], $bounds['do_value']['max'])); ?>"></polyline>
				</svg>
				<div class="chart-axis">
					<?php foreach ($timeline as $point): ?>
						<span><?= h($point['time']); ?></span>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	</div>
</div>

<section class="ui segment card dashboard portal-section">
	<div class="dashboard-header">
		<div>
			<p class="portal-eyebrow"><?= h(t('planned_analytics_modules')); ?></p>
			<h3><?= h(t('nav_analytics')); ?></h3>
		</div>
	</div>
	<div class="dashboard-card-grid">
		<div class="ui segment card dashboard">
			<h4><?= h(t('water_trend_forecast')); ?></h4>
			<p class="portal-muted"><?= h(t('water_trend_forecast_desc')); ?></p>
		</div>
		<div class="ui segment card dashboard">
			<h4><?= h(t('hybrid_recommendations')); ?></h4>
			<p class="portal-muted"><?= h(t('hybrid_recommendations_desc')); ?></p>
		</div>
		<div class="ui segment card dashboard">
			<h4><?= h(t('shrimp_vision_insights')); ?></h4>
			<p class="portal-muted"><?= h(t('shrimp_vision_insights_desc')); ?></p>
		</div>
		<div class="ui segment card dashboard">
			<h4><?= h(t('actuator_policy_tuning')); ?></h4>
			<p class="portal-muted"><?= h(t('actuator_policy_tuning_desc')); ?></p>
		</div>
	</div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
