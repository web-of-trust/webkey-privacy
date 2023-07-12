<?php declare(strict_types=1);

define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR . '/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(BASE_DIR)->safeLoad();
(new App\Application\Kernel())->serve();
