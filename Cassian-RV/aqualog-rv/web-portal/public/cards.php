<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'cards';
$page_title = '参数卡';
$page_heading = '参数显示与排序';
$page_tagline = '选择仪表盘显示参数，并拖拽调整展示顺序';
$templates = parameter_templates();
$latest_sample = fetch_latest_sample($config, current_pond_code());

if (request_method() === 'POST') {
	$action = isset($_POST['parameter_action']) ? trim((string) $_POST['parameter_action']) : '';

	if ($action === 'toggle') {
		$key = isset($_POST['parameter_key']) ? trim((string) $_POST['parameter_key']) : '';
		set_parameter_selected($key, isset($_POST['parameter_selected']) && $_POST['parameter_selected'] === '1');
		$_SESSION['flash_message'] = '仪表盘参数显示已更新';
	} else if ($action === 'select_all') {
		set_all_parameter_selected(true);
		$_SESSION['flash_message'] = '已显示全部参数';
	} else if ($action === 'clear_all') {
		set_all_parameter_selected(false);
		$_SESSION['flash_message'] = '已隐藏全部参数';
	} else if ($action === 'reorder') {
		$order = isset($_POST['parameter_order']) ? explode(',', (string) $_POST['parameter_order']) : [];
		reorder_selected_parameter_keys($order);
		$_SESSION['flash_message'] = '参数顺序已更新';
	}

	if (!is_cli()) {
		header('Location: ' . url_with_locale('cards.php', ['pond' => current_pond_code()]));
		exit;
	}
}

$selected_templates = selected_parameter_templates();

require __DIR__ . '/partials/header.php';
?>
<div class="ui stackable two column grid portal-section">
	<div class="column">
		<section class="ui segment card dashboard">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow">可选参数</p>
					<h3>实时采集参数库</h3>
				</div>
				<div class="portal-inline-actions">
					<form method="post">
						<input type="hidden" name="parameter_action" value="select_all">
						<button class="ui tiny button" type="submit">全部显示</button>
					</form>
					<form method="post">
						<input type="hidden" name="parameter_action" value="clear_all">
						<button class="ui tiny basic button" type="submit">全部隐藏</button>
					</form>
				</div>
			</div>
			<div class="portal-template-grid">
				<?php foreach ($templates as $template): ?>
					<?php $card = build_parameter_card($template, $latest_sample); ?>
					<form class="ui form portal-form portal-template-card" method="post">
						<input type="hidden" name="parameter_action" value="toggle">
						<input type="hidden" name="parameter_key" value="<?= h($template['key']); ?>">
						<input type="hidden" name="parameter_selected" value="<?= is_parameter_selected($template['key']) ? '0' : '1'; ?>">
						<div class="dashboard-header">
							<div>
								<p class="portal-eyebrow">实时参数</p>
								<h4><?= h($template['title']); ?></h4>
							</div>
							<div class="ui <?= h(semantic_label_class($card['status'])); ?> label">
								<?= h($card['display_value']); ?><?= $template['unit'] !== '' && $card['display_value'] !== '未接入' && $card['display_value'] !== '暂无数据' ? ' ' . h($template['unit']) : ''; ?>
							</div>
						</div>
						<p class="portal-muted"><?= h($template['note']); ?></p>
						<div class="portal-parameter-meta">
							<span class="ui basic label"><?= h($template['field'] === null ? '待接入' : '实时采集'); ?></span>
							<span class="portal-band-text"><?= h($card['band_text']); ?></span>
						</div>
						<button class="ui <?= is_parameter_selected($template['key']) ? 'basic' : 'primary'; ?> button" type="submit">
							<?= is_parameter_selected($template['key']) ? '从仪表盘隐藏' : '显示到仪表盘'; ?>
						</button>
					</form>
				<?php endforeach; ?>
			</div>
		</section>
	</div>
	<div class="column">
		<section class="ui segment card dashboard">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow">已选参数</p>
					<h3>拖拽排序</h3>
				</div>
				<span class="ui blue basic label"><?= h((string) count($selected_templates)); ?></span>
			</div>
			<?php if (empty($selected_templates)): ?>
				<p class="portal-empty">当前没有选择参数，仪表盘不会显示参数卡。</p>
			<?php else: ?>
				<form method="post" id="parameter-order-form">
					<input type="hidden" name="parameter_action" value="reorder">
					<input type="hidden" name="parameter_order" id="parameter-order-input" value="">
					<div class="ui relaxed divided list portal-sortable-list" id="parameter-sortable-list">
						<?php foreach ($selected_templates as $template): ?>
							<?php $card = build_parameter_card($template, $latest_sample); ?>
							<div class="item portal-sortable-item" draggable="true" data-parameter-key="<?= h($template['key']); ?>">
								<i class="bars icon portal-drag-handle"></i>
								<div class="content">
									<div class="header"><?= h($template['title']); ?></div>
									<div class="description"><?= h($card['display_value']); ?><?= $template['unit'] !== '' && $card['display_value'] !== '未接入' && $card['display_value'] !== '暂无数据' ? ' ' . h($template['unit']) : ''; ?>，<?= h($card['band_text']); ?></div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					<button class="ui primary button" type="submit">保存排序</button>
				</form>
			<?php endif; ?>
		</section>
	</div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
