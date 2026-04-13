<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'alerts';
$page_title = t('nav_alerts');
$page_heading = t('alerts_title');
$page_tagline = t('alerts_tagline');

$alerts = fetch_alerts($config);

require __DIR__ . '/partials/header.php';
?>
<section class="ui segment card dashboard portal-section">
	<div class="dashboard-header">
		<div>
			<p class="portal-eyebrow"><?= h(t('open_alerts')); ?></p>
			<h3><?= h(t('nav_alerts')); ?></h3>
		</div>
		<a class="ui button" href="<?= h(url_with_locale('control.php')); ?>"><?= h(t('open_control_panel')); ?></a>
	</div>
	<div class="dashboard-card-grid">
		<?php foreach ($alerts as $alert): ?>
			<div class="ui segment card dashboard">
				<div class="dashboard-header">
					<div class="ui <?= h(semantic_label_class($alert['severity'])); ?> label"><?= h(chinese_status_text($alert['severity'])); ?></div>
					<span class="portal-muted"><?= h($alert['time']); ?></span>
				</div>
				<h4><?= h($alert['title']); ?></h4>
				<p class="portal-muted"><?= h($alert['message']); ?></p>
				<div class="portal-inline-actions">
					<a class="ui button" href="<?= h(url_with_locale('control.php')); ?>"><?= h(t('open_control_panel')); ?></a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
