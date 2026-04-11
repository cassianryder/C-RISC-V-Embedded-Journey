<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'devices';
$page_title = t('nav_devices');
$page_heading = t('devices_title');
$page_tagline = t('devices_tagline');

$devices = fetch_devices($config);

require __DIR__ . '/partials/header.php';
?>
<section class="content-card">
	<h3><?= h(t('device_fleet')); ?></h3>
	<?php foreach ($devices as $device): ?>
		<div class="table-row">
			<div>
				<strong><?= h($device['name']); ?></strong>
				<p><?= h($device['type']); ?> | <?= h($device['location']); ?></p>
			</div>
			<div class="table-row-meta">
				<span class="pill <?= h(format_status_class($device['status'])); ?>"><?= h($device['status']); ?></span>
				<span>Last seen: <?= h($device['last_seen']); ?></span>
			</div>
		</div>
	<?php endforeach; ?>
</section>

<section class="grid-two">
	<div class="content-card">
		<h3><?= h(t('deployment_concept')); ?></h3>
		<p><?= h(t('deployment_body_1')); ?></p>
		<p><?= h(t('deployment_body_2')); ?></p>
	</div>
	<div class="content-card">
		<h3><?= h(t('next_backend_integrations')); ?></h3>
		<ul class="plain-list">
			<li>device heartbeat endpoint</li>
			<li>sensor upload API</li>
			<li>actuator command queue</li>
			<li>firmware health snapshot</li>
		</ul>
	</div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
