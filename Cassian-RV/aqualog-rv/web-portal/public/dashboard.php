<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'dashboard';
$page_title = '仪表盘';
$page_heading = '塘口实时水质仪表盘';
$page_tagline = '10 个塘口切换查看，参数卡与动态曲线一体化展示';

$pond_code = current_pond_code();
$ponds = pond_options();
$latest_sample = fetch_latest_sample($config, $pond_code);
$selected_templates = selected_parameter_templates();
$timeline_samples = fetch_history_series($config, 12, $pond_code);
$bounds = sensor_bounds();
$dashboard_cards = [];

foreach ($selected_templates as $template) {
	$card = build_parameter_card($template, $latest_sample);

	if ($template['field'] !== null && isset($bounds[$template['field']])) {
		$card['chart_points'] = build_line_points($timeline_samples, $template['field'], $bounds[$template['field']]['min'], $bounds[$template['field']]['max'], 360, 96);
		$card['chart_dots'] = build_chart_dots($timeline_samples, $template['field'], $bounds[$template['field']]['min'], $bounds[$template['field']]['max'], 360, 96);
	} else {
		$card['chart_points'] = '';
		$card['chart_dots'] = [];
	}

	$dashboard_cards[] = $card;
}

require __DIR__ . '/partials/header.php';
?>
<div class="portal-stack">
	<section class="ui segment card dashboard">
		<div class="dashboard-header portal-dashboard-header">
			<div>
				<p class="portal-eyebrow">当前塘口</p>
				<h3><?= h(pond_name($pond_code)); ?></h3>
			</div>
			<div class="portal-inline-actions">
				<?php if ($latest_sample !== null): ?>
					<span class="ui basic label" id="dashboard-sampled-at">更新于 <?= h($latest_sample['sampled_at']); ?></span>
				<?php endif; ?>
				<a class="ui button" href="<?= h(url_with_locale('cards.php', ['pond' => $pond_code])); ?>">参数显示与排序</a>
			</div>
		</div>
		<div class="ui small compact menu portal-pond-menu">
			<?php foreach ($ponds as $pond): ?>
				<a class="item <?= $pond['code'] === $pond_code ? 'active' : ''; ?>" href="<?= h(url_with_locale('dashboard.php', ['pond' => $pond['code']])); ?>"><?= h($pond['name']); ?></a>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="dashboard-card-grid portal-dashboard-parameter-grid" id="dashboard-parameter-grid">
		<?php if (empty($dashboard_cards)): ?>
			<div class="ui segment card dashboard">
				<p class="portal-empty">当前没有选择参数，请先到参数显示与排序页面勾选要展示的实时参数。</p>
			</div>
		<?php else: ?>
			<?php foreach ($dashboard_cards as $card): ?>
				<div class="ui segment card dashboard portal-live-parameter-card" data-parameter-key="<?= h($card['key']); ?>">
					<div class="dashboard-header">
						<div>
							<p class="portal-eyebrow"><?= h($card['title']); ?></p>
							<div class="portal-parameter-value">
								<span class="js-parameter-value"><?= h($card['display_value']); ?></span>
								<span class="js-parameter-unit"><?= h($card['unit'] !== '' && $card['display_value'] !== '未接入' && $card['display_value'] !== '暂无数据' ? $card['unit'] : ''); ?></span>
							</div>
						</div>
						<div class="ui <?= h(semantic_label_class($card['status'])); ?> label js-parameter-status"><?= h(chinese_status_text($card['status'])); ?></div>
					</div>
					<div class="portal-threshold-band portal-threshold-band-<?= h($card['band_class']); ?> js-parameter-band">
						<span class="portal-threshold-fill"></span>
					</div>
					<div class="portal-parameter-footer">
						<span class="portal-band-text js-parameter-band-text"><?= h($card['band_text']); ?></span>
						<span class="portal-muted"><?= h($card['note']); ?></span>
					</div>
					<?php if ($card['field'] === null): ?>
						<p class="portal-empty">该参数尚未接入实时采集。</p>
					<?php else: ?>
						<div class="chart-frame portal-mini-chart-frame">
							<div class="portal-chart-tooltip"></div>
							<svg class="chart-svg portal-mini-chart-svg" viewBox="0 0 360 120" preserveAspectRatio="none">
								<g class="chart-grid">
									<line x1="0" y1="20" x2="360" y2="20"></line>
									<line x1="0" y1="60" x2="360" y2="60"></line>
									<line x1="0" y1="100" x2="360" y2="100"></line>
								</g>
								<polyline class="chart-line js-card-chart-line" points="<?= h($card['chart_points']); ?>"></polyline>
								<g class="js-card-chart-dots">
									<?php foreach ($card['chart_dots'] as $dot): ?>
										<circle class="chart-dot" cx="<?= h((string) $dot['x']); ?>" cy="<?= h((string) $dot['y']); ?>" r="4" data-time="<?= h($dot['time']); ?>" data-value="<?= h($dot['value']); ?>"></circle>
									<?php endforeach; ?>
								</g>
							</svg>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</section>
</div>
<script>
window.dashboardInitialState = {
	pondCode: <?= json_encode($pond_code, JSON_UNESCAPED_UNICODE); ?>
};
</script>
<?php require __DIR__ . '/partials/footer.php'; ?>
