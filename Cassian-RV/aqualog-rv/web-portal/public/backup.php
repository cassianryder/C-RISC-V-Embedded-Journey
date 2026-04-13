<?php
require_once __DIR__ . '/../app/bootstrap.php';
require_login();

$current_page = 'backup';
$page_title = t('nav_backup');
$page_heading = t('backup_title');
$page_tagline = t('backup_tagline');

$user = current_user();
$operator = $user !== null ? $user['display_name'] : 'system';

if (request_method() === 'POST') {
	if (isset($_POST['backup_action']) && $_POST['backup_action'] === 'create') {
		$result = create_database_backup($config, $operator);
		$_SESSION['flash_message'] = $result['message'];
	} else if (isset($_POST['backup_action']) && $_POST['backup_action'] === 'restore' && isset($_POST['file_name'])) {
		$result = restore_database_backup($config, $_POST['file_name'], $operator);
		$_SESSION['flash_message'] = $result['message'];
	}

	if (!is_cli()) {
		header('Location: ' . url_with_locale('backup.php'));
		exit;
	}
}

$snapshots = fetch_backup_snapshots($config);

require __DIR__ . '/partials/header.php';
?>
<div class="ui stackable two column grid portal-section">
	<div class="column">
		<section class="ui segment card dashboard">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow"><?= h(t('backup_snapshot')); ?></p>
					<h3><?= h(t('create_backup')); ?></h3>
				</div>
			</div>
			<form method="post">
				<input type="hidden" name="backup_action" value="create">
				<button class="ui primary button" type="submit"><?= h(t('create_backup')); ?></button>
			</form>
			<p class="portal-muted"><?= h(t('backup_hint')); ?></p>
		</section>
	</div>
	<div class="column">
		<section class="ui segment card dashboard">
			<div class="dashboard-header">
				<div>
					<p class="portal-eyebrow"><?= h(t('restore_workflow')); ?></p>
					<h3><?= h(t('restore_backup')); ?></h3>
				</div>
			</div>
			<form class="ui form portal-form" method="post">
				<input type="hidden" name="backup_action" value="restore">
				<div class="field">
					<label><?= h(t('backup_file')); ?></label>
					<select class="ui dropdown" name="file_name">
						<?php foreach ($snapshots as $snapshot): ?>
							<option value="<?= h($snapshot['file_name']); ?>"><?= h($snapshot['file_name']); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<button class="ui button" type="submit" <?= empty($snapshots) ? 'disabled' : ''; ?>><?= h(t('restore_backup')); ?></button>
			</form>
		</section>
	</div>
</div>

<section class="ui segment card dashboard portal-section">
	<div class="dashboard-header">
		<div>
			<p class="portal-eyebrow"><?= h(t('backup_history')); ?></p>
			<h3><?= h(t('nav_backup')); ?></h3>
		</div>
	</div>
	<?php if (empty($snapshots)): ?>
		<p class="portal-empty"><?= h(t('backup_empty')); ?></p>
	<?php else: ?>
		<div class="ui list portal-info-list">
			<?php foreach ($snapshots as $snapshot): ?>
				<div class="item">
					<div class="portal-info-main">
						<strong><?= h($snapshot['file_name']); ?></strong>
						<p><?= h($snapshot['created_by']); ?></p>
					</div>
					<div class="portal-info-side">
						<div class="ui <?= h(semantic_label_class($snapshot['backup_status'])); ?> label"><?= h($snapshot['backup_status']); ?></div>
						<div><?= h($snapshot['created_at']); ?></div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
