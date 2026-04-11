<?php
require_once __DIR__ . '/../app/bootstrap.php';

if (is_logged_in() && !is_cli()) {
	header('Location: ' . url_with_locale('dashboard.php'));
	exit;
}

$error_message = null;

if (request_method() === 'POST') {
	$username = isset($_POST['username']) ? trim((string) $_POST['username']) : '';
	$password = isset($_POST['password']) ? (string) $_POST['password'] : '';

	if (attempt_login($config, $username, $password)) {
		if (!is_cli()) {
			header('Location: ' . url_with_locale('dashboard.php'));
			exit;
		}
	} else {
		$error_message = t('invalid_credentials');
	}
}
?>
<!DOCTYPE html>
<html lang="<?= h(current_locale()); ?>">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= h(page_title(t('nav_login'), $config)); ?></title>
	<link rel="stylesheet" href="<?= h(asset_url('css/styles.css')); ?>">
</head>
<body class="login-body">
	<div class="login-panel">
		<div class="login-brand">
			<div class="brand-mark">ARV</div>
			<div>
				<p class="eyebrow"><?= h(t('brand_subtitle')); ?></p>
				<h1><?= h(t('brand_name')); ?></h1>
			</div>
		</div>
		<div class="language-switcher">
			<?php foreach (supported_locales() as $locale_code => $locale_label): ?>
				<a class="locale-chip <?= current_locale() === $locale_code ? 'is-active' : ''; ?>" href="<?= h(locale_switch_url($locale_code)); ?>"><?= h($locale_label); ?></a>
			<?php endforeach; ?>
		</div>
		<h2><?= h(t('login_heading')); ?></h2>
		<p class="subtle-text"><?= h(t('login_subtitle')); ?></p>
		<p class="subtle-text"><?= h(t('default_demo_credentials')); ?></p>
		<?php if ($error_message !== null): ?>
			<div class="flash-banner"><?= h($error_message); ?></div>
		<?php endif; ?>
		<form class="control-form" method="post">
			<label>
				<span><?= h(t('username')); ?></span>
				<input class="text-input" type="text" name="username" value="admin">
			</label>
			<label>
				<span><?= h(t('password')); ?></span>
				<input class="text-input" type="password" name="password" value="Shrimp123!">
			</label>
			<button class="button-primary" type="submit"><?= h(t('sign_in')); ?></button>
		</form>
	</div>
</body>
</html>
