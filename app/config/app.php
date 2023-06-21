<?php declare(strict_types=1);

return [
    // main configuration
    'app.mode'      => \DI\env('app.mode', 'development'),
    'app.base_dir'  => \DI\env('app.base_dir', BASE_DIR),
    'app.cache_dir' => \DI\string('{app.base_dir}/var/cache/app'),

    // database configuration
    'database.dsn'          => \DI\env('database.dsn', 'mysqli://user:secret@localhost/webkey-db'),
    'database.cache_dir'    => \DI\string('{app.base_dir}/var/cache/doctrine'),
    'database.metadata_dir' => \DI\string('{app.base_dir}/app/src/Entity'),

    // error configuration
    'error.display' => \DI\env('error.display', true),
    'error.log'     => \DI\env('error.log', true),
    'error.details' => \DI\env('error.log', true),

    // logger configuration
    'logger.name'  => \DI\env('logger.name', 'webkey-privacy'),
    'logger.level' => \DI\env('logger.level', 200),
    'logger.file'  => \DI\string('{app.base_dir}/var/log/app.log'),

    // encryption configuration
    'encryption.key'         => \DI\env('encryption.key', 'oimgLPz9mrZyYV7IpVI+wBh9jFB2mVovdDwC5BLo2VU='),
    'encryption.key_size'    => \DI\env('encryption.key_size', 256),
    'encryption.cipher'      => \DI\env('encryption.cipher', 'AES'),
    'encryption.cipher_mode' => \DI\env('encryption.cipher_mode', 'cfb'),
    'encryption.iv'          => \DI\env('encryption.iv', 'i33p1SjLLzg='),

    // jwt configuration
    'jwt.key'            => \DI\env('jwt.key', 'a2EN6gWqHDmbzDCLVkNyLvTaqbEHbrbj8VD3a1KYj6Y='),
    'jwt.sign_algorithm' => \DI\env('jwt.sign_algorithm', 'Hmac'),
    'jwt.sign_hash'      => \DI\env('jwt.sign_hash', 'Sha256'),
    'jwt.expires'        => \DI\env('jwt.expires', 86400),
    'jwt.issued_by'      => \DI\env('jwt.issued_by', 'https://www.example.com'),
    'jwt.identified_by'  => \DI\env('jwt.identified_by', 'example.com'),
];
