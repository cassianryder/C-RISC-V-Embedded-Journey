<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'history';
$page_title = t('nav_history');
$page_heading = t('history_title');
$page_tagline = t('history_tagline');

$samples = fetch_history_series($config, 24);
$bounds = sensor_bounds();

require __DIR__ . '/partials/header.php';
?>
<div class="ui stackable two column grid portal-section">
	<div class="column">
		<section class="ui segment card dashboard chart">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow"><?= h(metric_label('temperature')); ?></p>
					<h3><?= h(t('quality_timeline')); ?></h3>
				</div>
				<span class="ui blue basic label">°C</span>
			</div>
			<div class="chart-frame">
				<svg class="chart-svg" viewBox="0 0 520 220" preserveAspectRatio="none">
					<g class="chart-grid">
						<line x1="0" y1="40" x2="520" y2="40"></line>
						<line x1="0" y1="110" x2="520" y2="110"></line>
						<line x1="0" y1="180" x2="520" y2="180"></line>
					</g>
					<polyline class="chart-line" points="<?= h(build_line_points($samples, 'temperature', $bounds['temperature']['min'], $bounds['temperature']['max'])); ?>"></polyline>
				</svg>
			</div>
		</section>
	</div>
	<div class="column">
		<section class="ui segment card dashboard chart">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow"><?= h(metric_label('ph')); ?></p>
					<h3><?= h(t('quality_timeline')); ?></h3>
				</div>
				<span class="ui blue basic label">pH</span>
			</div>
			<div class="chart-frame">
				<svg class="chart-svg" viewBox="0 0 520 220" preserveAspectRatio="none">
					<g class="chart-grid">
						<line x1="0" y1="40" x2="520" y2="40"></line>
						<line x1="0" y1="110" x2="520" y2="110"></line>
						<line x1="0" y1="180" x2="520" y2="180"></line>
					</g>
					<polyline class="chart-line" points="<?= h(build_line_points($samples, 'ph', $bounds['ph']['min'], $bounds['ph']['max'])); ?>"></polyline>
				</svg>
			</div>
		</section>
	</div>
	<div class="column">
		<section class="ui segment card dashboard chart">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow"><?= h(metric_label('do_value')); ?></p>
					<h3><?= h(t('quality_timeline')); ?></h3>
				</div>
				<span class="ui blue basic label">mg/L</span>
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
			</div>
		</section>
	</div>
	<div class="column">
		<section class="ui segment card dashboard chart">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow"><?= h(metric_label('turbidity')); ?></p>
					<h3><?= h(t('quality_timeline')); ?></h3>
				</div>
				<span class="ui blue basic label">NTU</span>
			</div>
			<div class="chart-frame">
				<svg class="chart-svg" viewBox="0 0 520 220" preserveAspectRatio="none">
					<g class="chart-grid">
						<line x1="0" y1="40" x2="520" y2="40"></line>
						<line x1="0" y1="110" x2="520" y2="110"></line>
						<line x1="0" y1="180" x2="520" y2="180"></line>
					</g>
					<polyline class="chart-line" points="<?= h(build_line_points($samples, 'turbidity', $bounds['turbidity']['min'], $bounds['turbidity']['max'])); ?>"></polyline>
				</svg>
			</div>
		</section>
	</div>
</div>

<section class="ui segment card dashboard portal-section">
	<div class="dashboard-header">
		<div>
			<p class="portal-eyebrow"><?= h(t('history_samples')); ?></p>
			<h3><?= h(t('nav_history')); ?></h3>
		</div>
	</div>
	<div class="portal-table-wrap">
		<table class="ui celled table portal-table">
			<thead>
				<tr>
					<th><?= h(t('time')); ?></th>
					<th>温度</th>
					<th>pH</th>
					<th>溶氧</th>
					<th>浊度</th>
					<th>水位</th>
					<th>告警</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach (array_reverse($samples) as $sample): ?>
					<tr>
						<td><?= h($sample['sampled_at']); ?></td>
						<td><?= h($sample['temperature'] === null ? 'NA' : $sample['temperature']); ?></td>
						<td><?= h($sample['ph'] === null ? 'NA' : $sample['ph']); ?></td>
						<td><?= h($sample['do_value'] === null ? 'NA' : $sample['do_value']); ?></td>
						<td><?= h($sample['turbidity'] === null ? 'NA' : $sample['turbidity']); ?></td>
						<td><?= h($sample['water_level'] === null ? 'NA' : $sample['water_level']); ?></td>
						<td><span class="ui <?= h(semantic_label_class($sample['alert_text'] === 'normal' ? 'healthy' : 'warning')); ?> label"><?= h($sample['alert_text'] === 'normal' ? '正常' : $sample['alert_text']); ?></span></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
