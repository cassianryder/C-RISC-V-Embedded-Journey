<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'history';
$page_title = t('nav_history');
$page_heading = t('history_title');
$page_tagline = t('history_tagline');

$pond_code = current_pond_code();
$samples = fetch_history_series($config, 24, $pond_code);
$bounds = sensor_bounds();
$metrics = [
	['field' => 'temperature', 'unit' => '°C'],
	['field' => 'ph', 'unit' => 'pH'],
	['field' => 'do_value', 'unit' => 'mg/L'],
	['field' => 'turbidity', 'unit' => 'NTU'],
];

require __DIR__ . '/partials/header.php';
?>
<div class="portal-stack">
	<section class="ui segment card dashboard">
		<div class="dashboard-header portal-dashboard-header">
			<div>
				<p class="portal-eyebrow">当前塘口</p>
				<h3><?= h(pond_name($pond_code)); ?></h3>
			</div>
		</div>
		<div class="ui small compact menu portal-pond-menu">
			<?php foreach (pond_options() as $pond): ?>
				<a class="item <?= $pond['code'] === $pond_code ? 'active' : ''; ?>" href="<?= h(url_with_locale('history.php', ['pond' => $pond['code']])); ?>"><?= h($pond['name']); ?></a>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="dashboard-card-grid portal-history-grid">
		<?php foreach ($metrics as $metric): ?>
			<section class="ui segment card dashboard chart">
				<div class="dashboard-header">
					<div>
						<p class="portal-eyebrow"><?= h(metric_label($metric['field'])); ?></p>
						<h3><?= h(t('quality_timeline')); ?></h3>
					</div>
					<span class="ui blue basic label"><?= h($metric['unit']); ?></span>
				</div>
				<div class="chart-frame portal-history-chart-frame">
					<div class="portal-chart-tooltip"></div>
					<svg class="chart-svg" viewBox="0 0 520 220" preserveAspectRatio="none">
						<g class="chart-grid">
							<line x1="0" y1="40" x2="520" y2="40"></line>
							<line x1="0" y1="110" x2="520" y2="110"></line>
							<line x1="0" y1="180" x2="520" y2="180"></line>
						</g>
						<polyline class="chart-line" points="<?= h(build_line_points($samples, $metric['field'], $bounds[$metric['field']]['min'], $bounds[$metric['field']]['max'])); ?>"></polyline>
						<g class="chart-dots">
							<?php foreach (build_chart_dots($samples, $metric['field'], $bounds[$metric['field']]['min'], $bounds[$metric['field']]['max']) as $dot): ?>
								<circle class="chart-dot" cx="<?= h((string) $dot['x']); ?>" cy="<?= h((string) $dot['y']); ?>" r="5" data-time="<?= h($dot['time']); ?>" data-value="<?= h($dot['value']); ?>" data-label="<?= h(metric_label($metric['field'])); ?>"></circle>
							<?php endforeach; ?>
						</g>
					</svg>
				</div>
			</section>
		<?php endforeach; ?>
	</section>
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
