<?php
require_once __DIR__ . '/../app/bootstrap.php';

logout_user();

if (!is_cli()) {
	header('Location: ' . url_with_locale('login.php'));
	exit;
}
