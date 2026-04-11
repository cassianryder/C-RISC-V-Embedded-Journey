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
<section class="content-card">
	<h3><?= h(t('open_alerts')); ?></h3>
	<?php foreach ($alerts as $alert): ?>
		<div class="alert-detail-card">
			<div class="alert-detail-header">
				<span class="pill <?= h(format_status_class($alert['severity'])); ?>"><?= h($alert['severity']); ?></span>
				<span><?= h($alert['time']); ?></span>
			</div>
			<h4><?= h($alert['title']); ?></h4>
			<p><?= h($alert['message']); ?></p>
			<div class="action-line">
				<a class="button-secondary" href="<?= h(url_with_locale('control.php')); ?>"><?= h(t('open_control_panel')); ?></a>
			</div>
		</div>
	<?php endforeach; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
