<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'logs';
$page_title = t('nav_logs');
$page_heading = t('logs_title');
$page_tagline = t('logs_tagline');

$logs = fetch_system_logs($config, 80);

require __DIR__ . '/partials/header.php';
?>
<section class="ui segment card dashboard portal-section">
	<div class="dashboard-header">
		<div>
			<p class="portal-eyebrow"><?= h(t('system_logs')); ?></p>
			<h3><?= h(t('nav_logs')); ?></h3>
		</div>
	</div>
	<?php if (empty($logs)): ?>
		<p class="portal-empty"><?= h(t('logs_empty')); ?></p>
	<?php else: ?>
		<div class="ui list portal-info-list">
			<?php foreach ($logs as $log): ?>
				<div class="item">
					<div class="portal-info-main">
						<strong><?= h($log['log_source']); ?></strong>
						<p><?= h($log['message']); ?></p>
					</div>
					<div class="portal-info-side">
						<div class="ui <?= h(semantic_label_class($log['log_level'])); ?> label"><?= h($log['log_level']); ?></div>
						<div><?= h($log['created_at']); ?></div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
