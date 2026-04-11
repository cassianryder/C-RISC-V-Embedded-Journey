<?php

if (session_status() !== PHP_SESSION_ACTIVE)
	session_start();

$config = require __DIR__ . '/config.php';

require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/i18n.php';
require_once __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/data/sample_data.php';
require_once __DIR__ . '/lib/repositories.php';

initialize_locale($config);
