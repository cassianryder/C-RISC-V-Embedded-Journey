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
<section class="ui segment card dashboard portal-section">
	<div class="dashboard-header">
		<div>
			<p class="portal-eyebrow"><?= h(t('device_fleet')); ?></p>
			<h3><?= h(t('nav_devices')); ?></h3>
		</div>
	</div>
	<div class="ui list portal-info-list">
		<?php foreach ($devices as $device): ?>
			<div class="item">
				<div class="portal-info-main">
					<strong><?= h($device['name']); ?></strong>
					<p><?= h(chinese_device_type($device['type'])); ?> | <?= h($device['location']); ?></p>
				</div>
				<div class="portal-info-side">
					<div class="ui <?= h(semantic_label_class($device['status'])); ?> label"><?= h(chinese_status_text($device['status'])); ?></div>
					<div>最后上报：<?= h($device['last_seen']); ?></div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<div class="ui stackable two column grid portal-section">
	<div class="column">
		<section class="ui segment card dashboard">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow"><?= h(t('deployment_concept')); ?></p>
					<h3><?= h(t('future_stack')); ?></h3>
				</div>
			</div>
			<div class="ui list portal-info-list">
				<div class="item">
					<div class="portal-info-main"><strong>Milk-V Duo S 边缘节点</strong><p><?= h(t('deployment_body_1')); ?></p></div>
				</div>
				<div class="item">
					<div class="portal-info-main"><strong>云端可视化门户</strong><p><?= h(t('deployment_body_2')); ?></p></div>
				</div>
			</div>
		</section>
	</div>
	<div class="column">
		<section class="ui segment card dashboard">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow"><?= h(t('next_backend_integrations')); ?></p>
					<h3><?= h(t('nav_analytics')); ?></h3>
				</div>
			</div>
			<div class="ui list portal-info-list">
				<div class="item"><div class="portal-info-main"><strong>心跳接口</strong><p>用于判断设备在线状态和心跳延迟。</p></div></div>
				<div class="item"><div class="portal-info-main"><strong>采样上传接口</strong><p>用于接收传感器上报并驱动主页数据刷新。</p></div></div>
				<div class="item"><div class="portal-info-main"><strong>控制命令队列</strong><p>用于执行增氧、告警和远程操作命令。</p></div></div>
				<div class="item"><div class="portal-info-main"><strong>固件健康快照</strong><p>用于汇总版本、温度、运行时间和异常状态。</p></div></div>
			</div>
		</section>
	</div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
