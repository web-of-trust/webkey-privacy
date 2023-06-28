<?php declare(strict_types=1);

return [
    // main configuration
    'app.mode'      => \DI\env('APP_MODE', 'development'),
    'app.base_dir'  => \DI\env('APP_BASE_DIR', BASE_DIR),
    'app.cache_dir' => \DI\string('{app.base_dir}/var/cache/app'),

    // database configuration
    'database.dsn'          => \DI\env('DATABASE_DSN', 'mysqli://user:secret@localhost/webkey-db'),
    'database.cache_dir'    => \DI\string('{app.base_dir}/var/cache/doctrine'),
    'database.metadata_dir' => \DI\string('{app.base_dir}/app/src/Entity'),

    // error configuration
    'error.display' => \DI\env('ERROR_DISPLAY', true),
    'error.log'     => \DI\env('ERROR_LOG', true),
    'error.details' => \DI\env('ERROR_DETAILS', true),

    // logger configuration
    'logger.name'  => \DI\env('LOGGER_NAME', 'webkey-privacy'),
    'logger.level' => \DI\env('LOGGER_LEVEL', 200),
    'logger.file'  => \DI\string('{app.base_dir}/var/log/app.log'),

    // encryption configuration
    'encryption.passphase'   => \DI\env('ENCRYPTION_PASSPHASE', 'passphase'),
    'encryption.key_size'    => \DI\env('ENCRYPTION_KEY_SIZE', 16),
    'encryption.cipher'      => \DI\env('ENCRYPTION_CIPHER', 'AES'),
    'encryption.cipher_mode' => \DI\env('ENCRYPTION_CIPHER_MODE', 'gcm'),

    // jwt configuration
    'jwt.sign_key'      => \DI\env('JWT_SIGN_KEY', 'MDEyMzQ1Njc4OWFiY2RlZjAxMjM0NTY3ODlhYmNkZWY='),
    'jwt.verify_key'    => \DI\env('JWT_VERIFY_KEY', 'MDEyMzQ1Njc4OWFiY2RlZjAxMjM0NTY3ODlhYmNkZWY='),
    'jwt.algorithm'     => \DI\env('JWT_ALGORITHM', 'HS256'),
    'jwt.expires'       => \DI\env('JWT_EXPIRES', 86400),
    'jwt.issued_by'     => \DI\env('JWT_ISSUED_BY', 'https://www.example.com'),
    'jwt.identified_by' => \DI\env('JWT_IDENTIFIED_BY', 'example.com'),

    // cookie configuration
    'cookie.domain'   => \DI\env('COOKIE_DOMAIN'),
    'cookie.hostonly' => \DI\env('COOKIE_HOSTONLY'),
    'cookie.path'     => \DI\env('COOKIE_PATH'),
    'cookie.expires'  => \DI\env('COOKIE_EXPIRES'),
    'cookie.secure'   => \DI\env('COOKIE_SECURE', false),
    'cookie.httponly' => \DI\env('COOKIE_HTTPONLY', true),
    'cookie.samesite' => \DI\env('COOKIE_SAMESITE', 'LAX'),
];
