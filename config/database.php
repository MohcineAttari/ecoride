<?php

return [
    'driver' => env_value('DB_DRIVER', 'mysql'),
    'host' => env_value('DB_HOST', '127.0.0.1'),
    'port' => (int) env_value('DB_PORT', 3306),
    'database' => env_value('DB_DATABASE', 'ecoride'),
    'username' => env_value('DB_USERNAME', 'root'),
    'password' => env_value('DB_PASSWORD', ''),
    'charset' => env_value('DB_CHARSET', 'utf8mb4'),
];
