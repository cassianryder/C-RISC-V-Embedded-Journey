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
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.5.0/dist/semantic.min.css">
	<link rel="stylesheet" href="<?= h(asset_url('css/styles.css')); ?>">
</head>
<body class="login-body" data-theme="<?= h(body_theme_name()); ?>">
	<div class="portal-login-shell">
		<div class="ui segment card dashboard portal-login-card">
			<div class="brand-block">
				<div class="brand-mark">ARV</div>
				<div>
					<p class="portal-eyebrow"><?= h(t('brand_subtitle')); ?></p>
					<h1><?= h(t('brand_name')); ?></h1>
				</div>
			</div>
			<div class="portal-login-locale">
				<?php foreach (supported_locales() as $locale_code => $locale_label): ?>
					<a class="ui button <?= current_locale() === $locale_code ? 'primary' : ''; ?>" href="<?= h(locale_switch_url($locale_code)); ?>"><?= h($locale_label); ?></a>
				<?php endforeach; ?>
			</div>
			<h2><?= h(t('login_heading')); ?></h2>
			<p class="portal-muted"><?= h(t('login_subtitle')); ?></p>
			<p class="portal-muted"><?= h(t('default_demo_credentials')); ?></p>
			<?php if ($error_message !== null): ?>
				<div class="ui negative message"><?= h($error_message); ?></div>
			<?php endif; ?>
			<form class="ui form portal-form" method="post">
				<div class="field">
					<label><?= h(t('username')); ?></label>
					<input type="text" name="username" value="admin">
				</div>
				<div class="field">
					<label><?= h(t('password')); ?></label>
					<input type="password" name="password" value="Shrimp123!">
				</div>
				<div class="field">
					<div class="ui toggle checkbox theme-toggle" data-day-url="<?= h(theme_switch_url('day')); ?>" data-night-url="<?= h(theme_switch_url('night')); ?>">
						<input type="checkbox" <?= current_theme() === 'night' ? 'checked' : ''; ?>>
						<label><?= h(current_theme() === 'night' ? t('theme_night') : t('theme_day')); ?></label>
					</div>
				</div>
				<button class="ui primary fluid button" type="submit"><?= h(t('sign_in')); ?></button>
			</form>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.5.0/dist/semantic.min.js"></script>
	<script>
	$(function () {
		$('.ui.checkbox').checkbox();
		$('.theme-toggle').checkbox({
			onChecked: function () {
				window.location.href = $('.theme-toggle').data('night-url');
			},
			onUnchecked: function () {
				window.location.href = $('.theme-toggle').data('day-url');
			}
		});
	});
	</script>
</body>
</html>
