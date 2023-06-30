<?php declare(strict_types=1);

return [
    'authorization.allow' => [
        'AuthenticatedUser' => [
            '/rest/v1/profile/*',
        ],
    ],
];
