<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'control';
$page_title = t('nav_control');
$page_heading = t('control_title');
$page_tagline = t('control_tagline');

if (request_method() === 'POST') {
	$pond_code = current_pond_code();
	$device_type = isset($_POST['device_type']) ? trim((string) $_POST['device_type']) : '增氧机';
	$device_no = isset($_POST['device_no']) ? trim((string) $_POST['device_no']) : '1';
	$device = $device_type . ' ' . $device_no . ' 号';
	$action = isset($_POST['action']) ? $_POST['action'] : 'noop';
	$user = current_user();
	$operator = $user !== null ? $user['display_name'] : 'CLI';

	save_control_command($config, $pond_code, $device_type, $device_no, $action, $operator);
	$_SESSION['flash_message'] = t('queued_command') . ': ' . pond_name($pond_code) . ' / ' . $device . ' -> ' . chinese_status_text($action);

	if (!is_cli()) {
		header('Location: ' . url_with_locale('control.php'));
		exit;
	}
}

$commands = fetch_control_commands($config, 10);

require __DIR__ . '/partials/header.php';
?>
<div class="ui stackable two column grid portal-section">
	<div class="column">
		<section class="ui segment card dashboard">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow"><?= h(t('actuator_quick_actions')); ?></p>
					<h3><?= h(t('nav_control')); ?></h3>
				</div>
				<a class="ui button js-open-modal" href="#"><?= h(t('control_strategy')); ?></a>
			</div>
			<form class="ui form portal-form" method="post">
				<div class="field">
					<label><?= h(t('target_device')); ?></label>
					<select class="ui dropdown" name="device_type">
						<option value="增氧机">增氧机</option>
						<option value="水泵">水泵</option>
					</select>
				</div>
				<div class="field">
					<label>设备编号</label>
					<select class="ui dropdown" name="device_no">
						<?php for ($i = 1; $i <= 10; $i++): ?>
							<option value="<?= h((string) $i); ?>"><?= h((string) $i); ?> 号</option>
						<?php endfor; ?>
					</select>
				</div>
				<div class="field">
					<label>当前塘口</label>
					<div class="ui basic label"><?= h(pond_name(current_pond_code())); ?></div>
				</div>
				<div class="field">
					<label><?= h(t('requested_action')); ?></label>
					<select class="ui dropdown" name="action">
						<option value="start"><?= h(t('start')); ?></option>
						<option value="stop"><?= h(t('stop')); ?></option>
						<option value="manual_override"><?= h(t('manual_override')); ?></option>
						<option value="trigger_alarm"><?= h(t('trigger_alarm')); ?></option>
					</select>
				</div>
				<div class="portal-inline-actions">
					<button class="ui primary button" type="submit"><?= h(t('queue_simulated_action')); ?></button>
				</div>
			</form>
		</section>
	</div>
	<div class="column">
		<section class="ui segment card dashboard">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow"><?= h(t('control_strategy')); ?></p>
					<h3><?= h(t('nav_analytics')); ?></h3>
				</div>
			</div>
			<div class="ui list portal-info-list">
				<div class="item">
					<div class="portal-info-main"><strong>短期策略</strong><p>人工确认告警后，先对增氧机或水泵做手动操作。</p></div>
				</div>
				<div class="item">
					<div class="portal-info-main"><strong>中期策略</strong><p>门户把控制命令写入队列，由后端统一审核和下发。</p></div>
				</div>
				<div class="item">
					<div class="portal-info-main"><strong>长期策略</strong><p>专家规则和模型共同生成联动建议，再按权限自动执行。</p></div>
				</div>
				<div class="item">
					<div class="portal-info-main"><strong>边缘兜底</strong><p>云端不可用时，可由 RISC-V 边缘节点执行本地控制策略。</p></div>
				</div>
			</div>
		</section>
	</div>
</div>

<section class="ui segment card dashboard portal-section">
	<div class="dashboard-header">
		<div>
			<p class="portal-eyebrow"><?= h(t('command_history')); ?></p>
			<h3>设备下发队列</h3>
		</div>
	</div>
	<div class="ui list portal-info-list">
		<?php foreach ($commands as $command): ?>
			<div class="item">
				<div class="portal-info-main">
					<strong><?= h((isset($command['device_type']) ? $command['device_type'] : '设备') . ' ' . (isset($command['device_no']) ? (string) $command['device_no'] : '1') . ' 号'); ?></strong>
					<p><?= h(pond_name(isset($command['pond_code']) ? $command['pond_code'] : 'pond_01')); ?> | <?= h(chinese_status_text($command['action_name'])); ?> | <?= h(t('operator')); ?>: <?= h($command['operator_name']); ?></p>
					<?php if (isset($command['command_uuid'])): ?>
						<p>命令编号：<?= h($command['command_uuid']); ?></p>
					<?php endif; ?>
					<?php if (!empty($command['device_response'])): ?>
						<p>设备回执：<?= h($command['device_response']); ?></p>
					<?php endif; ?>
				</div>
				<div class="portal-info-side">
					<div class="ui <?= h(semantic_label_class($command['command_status'])); ?> label"><?= h(chinese_status_text($command['command_status'])); ?></div>
					<div><?= h($command['issued_at']); ?></div>
					<?php if (!empty($command['dispatched_at'])): ?>
						<div>下发：<?= h($command['dispatched_at']); ?></div>
					<?php endif; ?>
					<?php if (!empty($command['executed_at'])): ?>
						<div>执行：<?= h($command['executed_at']); ?></div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
