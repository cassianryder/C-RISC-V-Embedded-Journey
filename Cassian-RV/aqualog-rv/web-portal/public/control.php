<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'control';
$page_title = t('nav_control');
$page_heading = t('control_title');
$page_tagline = t('control_tagline');

if (request_method() === 'POST') {
	$device = isset($_POST['device']) ? $_POST['device'] : 'Unknown device';
	$action = isset($_POST['action']) ? $_POST['action'] : 'noop';
	$user = current_user();
	$operator = $user !== null ? $user['display_name'] : 'CLI';

	save_control_command($config, $device, $action, $operator);
	$_SESSION['flash_message'] = t('queued_command') . ': ' . $device . ' -> ' . $action;

	if (!is_cli()) {
		header('Location: ' . url_with_locale('control.php'));
		exit;
	}
}

$commands = fetch_control_commands($config, 10);

require __DIR__ . '/partials/header.php';
?>
<section class="grid-two">
	<div class="content-card">
		<h3><?= h(t('actuator_quick_actions')); ?></h3>
		<form class="control-form" method="post">
			<label>
				<span><?= h(t('target_device')); ?></span>
				<select name="device">
					<option value="Aerator Pump A">Aerator Pump A</option>
					<option value="Aerator Pump B">Aerator Pump B</option>
					<option value="Alarm Beacon">Alarm Beacon</option>
				</select>
			</label>
			<label>
				<span><?= h(t('requested_action')); ?></span>
				<select name="action">
					<option value="start"><?= h(t('start')); ?></option>
					<option value="stop"><?= h(t('stop')); ?></option>
					<option value="manual_override"><?= h(t('manual_override')); ?></option>
					<option value="trigger_alarm"><?= h(t('trigger_alarm')); ?></option>
				</select>
			</label>
			<button class="button-primary" type="submit"><?= h(t('queue_simulated_action')); ?></button>
		</form>
	</div>
	<div class="content-card">
		<h3><?= h(t('control_strategy')); ?></h3>
		<ul class="plain-list">
			<li>short-term: operator confirms alerts and triggers manual actions</li>
			<li>mid-term: portal writes commands to a backend queue</li>
			<li>long-term: expert rules and models recommend actions with approval policy</li>
			<li>future edge mode: critical fallback can run locally on the RISC-V node</li>
		</ul>
	</div>
</section>

<section class="content-card">
	<h3><?= h(t('command_history')); ?></h3>
	<?php foreach ($commands as $command): ?>
		<div class="table-row">
			<div>
				<strong><?= h($command['device_name']); ?></strong>
				<p><?= h($command['action_name']); ?> | <?= h(t('operator')); ?>: <?= h($command['operator_name']); ?></p>
			</div>
			<div class="table-row-meta">
				<span class="pill <?= h(format_status_class($command['command_status'])); ?>"><?= h($command['command_status']); ?></span>
				<span><?= h($command['issued_at']); ?></span>
			</div>
		</div>
	<?php endforeach; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
