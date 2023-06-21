<?php declare(strict_types=1);

define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR . '/vendor/autoload.php';

use App\Application\Kernel;

\Dotenv\Dotenv::createImmutable(BASE_DIR)->safeLoad();
Kernel::serve();
