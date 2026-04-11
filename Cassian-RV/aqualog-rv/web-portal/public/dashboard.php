<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'dashboard';
$page_title = t('nav_dashboard');
$page_heading = t('dashboard_title');
$page_tagline = t('dashboard_tagline');

$overview = fetch_overview($config);
$readings = fetch_latest_readings($config);
$alerts = fetch_alerts($config);
$timeline = fetch_timeline($config);
$weather = fetch_latest_weather($config);
$cameras = fetch_camera_feeds($config);
$medications = fetch_medication_recommendations($config);

require __DIR__ . '/partials/header.php';
?>
<section class="stats-grid">
	<div class="stat-card">
		<p class="eyebrow"><?= h(t('quality_score')); ?></p>
		<div class="big-number"><?= h($overview['water_quality_score']); ?></div>
	</div>
	<div class="stat-card">
		<p class="eyebrow"><?= h(t('active_alerts')); ?></p>
		<div class="big-number"><?= h($overview['active_alerts']); ?></div>
	</div>
	<div class="stat-card">
		<p class="eyebrow"><?= h(t('online_devices')); ?></p>
		<div class="big-number"><?= h($overview['online_devices']); ?></div>
	</div>
	<div class="stat-card">
		<p class="eyebrow"><?= h(t('prediction_confidence')); ?></p>
		<div class="big-number"><?= h($overview['prediction_confidence']); ?>%</div>
	</div>
</section>

<section class="grid-two">
	<div class="content-card">
		<h3><?= h(t('latest_telemetry')); ?></h3>
		<div class="metric-grid">
			<?php foreach ($readings as $reading): ?>
				<div class="metric-card">
					<p class="eyebrow"><?= h($reading['name']); ?></p>
					<div class="metric-value"><?= h($reading['value']); ?> <span><?= h($reading['unit']); ?></span></div>
					<div class="metric-footer">
						<span class="pill <?= h(format_status_class($reading['status'])); ?>"><?= h($reading['status']); ?></span>
						<span><?= h($reading['trend']); ?></span>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="content-card">
		<h3><?= h(t('quality_timeline')); ?></h3>
		<div class="timeline-chart">
			<?php foreach ($timeline as $point): ?>
				<div class="timeline-point">
					<div class="timeline-bar" style="height: <?= h($point['value']); ?>%;"></div>
					<span><?= h($point['time']); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
		<p class="subtle-text"><?= h(t('placeholder_chart')); ?></p>
	</div>
</section>

<section class="content-card">
	<h3><?= h(t('alert_summary')); ?></h3>
	<?php foreach ($alerts as $alert): ?>
		<div class="table-row">
			<div>
				<strong><?= h($alert['title']); ?></strong>
				<p><?= h($alert['message']); ?></p>
			</div>
			<div class="table-row-meta">
				<span class="pill <?= h(format_status_class($alert['severity'])); ?>"><?= h($alert['severity']); ?></span>
				<span><?= h($alert['time']); ?></span>
			</div>
		</div>
	<?php endforeach; ?>
</section>

<section class="grid-two">
	<div class="content-card">
		<h3><?= h(t('weather_and_stress')); ?></h3>
		<div class="feature-grid">
			<div class="feature-box">
				<h4>Air Temp</h4>
				<p><?= h($weather['air_temperature']); ?> C</p>
			</div>
			<div class="feature-box">
				<h4>Rainfall</h4>
				<p><?= h($weather['rainfall_mm']); ?> mm</p>
			</div>
			<div class="feature-box">
				<h4>Humidity</h4>
				<p><?= h($weather['humidity']); ?> %</p>
			</div>
			<div class="feature-box">
				<h4>Stress Risk</h4>
				<p><span class="pill <?= h(format_status_class($weather['stress_risk'])); ?>"><?= h($weather['stress_risk']); ?></span></p>
			</div>
		</div>
		<p class="subtle-text"><?= h($weather['forecast_summary']); ?></p>
	</div>
	<div class="content-card">
		<h3><?= h(t('camera_and_vision')); ?></h3>
		<?php foreach ($cameras as $camera): ?>
			<div class="table-row">
				<div>
					<strong><?= h($camera['camera_name']); ?></strong>
					<p><?= h($camera['location']); ?> | visibility <?= h($camera['visibility_score']); ?> | activity <?= h($camera['shrimp_activity_index']); ?></p>
				</div>
				<div class="table-row-meta">
					<span class="pill <?= h(format_status_class($camera['stream_status'])); ?>"><?= h($camera['stream_status']); ?></span>
					<span><?= h($camera['last_frame_at']); ?></span>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<section class="content-card">
	<h3><?= h(t('medication_recommendation')); ?></h3>
	<?php foreach ($medications as $item): ?>
		<div class="table-row">
			<div>
				<strong><?= h($item['recommendation_title']); ?></strong>
				<p><?= h($item['recommendation_text']); ?></p>
			</div>
			<div class="table-row-meta">
				<span><?= h($item['recommended_window']); ?></span>
				<span class="pill <?= h(format_status_class($item['risk_level'])); ?>"><?= h($item['status']); ?></span>
			</div>
		</div>
	<?php endforeach; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
