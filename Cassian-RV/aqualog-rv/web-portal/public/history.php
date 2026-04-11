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
<section class="history-grid">
	<div class="content-card">
		<h3><?= h(metric_label('temperature')); ?></h3>
		<svg class="history-chart" viewBox="0 0 520 160" preserveAspectRatio="none">
			<polyline points="<?= h(build_line_points($samples, 'temperature', $bounds['temperature']['min'], $bounds['temperature']['max'])); ?>" />
		</svg>
	</div>
	<div class="content-card">
		<h3><?= h(metric_label('ph')); ?></h3>
		<svg class="history-chart" viewBox="0 0 520 160" preserveAspectRatio="none">
			<polyline points="<?= h(build_line_points($samples, 'ph', $bounds['ph']['min'], $bounds['ph']['max'])); ?>" />
		</svg>
	</div>
	<div class="content-card">
		<h3><?= h(metric_label('do_value')); ?></h3>
		<svg class="history-chart" viewBox="0 0 520 160" preserveAspectRatio="none">
			<polyline points="<?= h(build_line_points($samples, 'do_value', $bounds['do_value']['min'], $bounds['do_value']['max'])); ?>" />
		</svg>
	</div>
	<div class="content-card">
		<h3><?= h(metric_label('turbidity')); ?></h3>
		<svg class="history-chart" viewBox="0 0 520 160" preserveAspectRatio="none">
			<polyline points="<?= h(build_line_points($samples, 'turbidity', $bounds['turbidity']['min'], $bounds['turbidity']['max'])); ?>" />
		</svg>
	</div>
</section>

<section class="content-card">
	<h3><?= h(t('history_samples')); ?></h3>
	<div class="history-table">
		<table>
			<thead>
				<tr>
					<th><?= h(t('time')); ?></th>
					<th>Temp</th>
					<th>pH</th>
					<th>DO</th>
					<th>Turbidity</th>
					<th>Water Level</th>
					<th>Alert</th>
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
						<td><?= h($sample['alert_text']); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
