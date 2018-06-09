<?php
return [
    'router' => [
        'binding' => true,
        'suffix' => '.html',
        'rule' => [],
        'host' => [
            'admin.revoke.cc' => [
                'module' => 'admin',
                'suffix' => '',
                'rule' => [],
            ],
        ],
    ],
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=lying_blog;charset=utf8',
        'user' => 'root',
        'pass' => 'root',
        'prefix' => 'lying_',
    ],
];
