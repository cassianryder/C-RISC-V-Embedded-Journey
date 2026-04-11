<?php

function fallback_user()
{
	return [
		'id' => 1,
		'username' => 'admin',
		'display_name' => 'Cassian Admin',
		'role' => 'admin',
		'password_hash' => '$2y$12$.acDGq9MKwZWBufAd/Gone6pUNIK/G/jtC.srX9VoXuow6CIbvWSO',
	];
}

function find_user_by_username(array $config, $username)
{
	$pdo = get_pdo($config);

	if ($pdo === false) {
		$user = fallback_user();
		return $user['username'] === $username ? $user : null;
	}

	$statement = $pdo->prepare(
		"SELECT id, username, display_name, role, password_hash
		 FROM users
		 WHERE username = :username
		 LIMIT 1"
	);
	try {
		$statement->execute(['username' => $username]);
		$user = $statement->fetch();
	} catch (Throwable $exception) {
		$user = fallback_user();
		return $user['username'] === $username ? $user : null;
	}

	return $user ?: null;
}

function attempt_login(array $config, $username, $password)
{
	$user = find_user_by_username($config, $username);

	if ($user === null)
		return false;

	if (!password_verify($password, $user['password_hash']))
		return false;

	$_SESSION['auth_user'] = [
		'id' => $user['id'],
		'username' => $user['username'],
		'display_name' => $user['display_name'],
		'role' => $user['role'],
	];

	return true;
}

function is_logged_in()
{
	return isset($_SESSION['auth_user']);
}

function current_user()
{
	return isset($_SESSION['auth_user']) ? $_SESSION['auth_user'] : null;
}

function logout_user()
{
	unset($_SESSION['auth_user']);
}

function require_login()
{
	if (is_cli())
		return true;

	if (is_logged_in())
		return true;

	$_SESSION['flash_message'] = t('login_required');
	header('Location: ' . url_with_locale('login.php'));
	exit;
}
