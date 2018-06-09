<?php
return [
    'classMap' => [

    ],
    'psr-4' => [
        'lying' => DIR_KERNEL,
        'module' => DIR_MODULE,
        'console' => DIR_CONSOLE,
        'extend' => DIR_ROOT . '/extend',
    ],
    'psr-0' => [
        DIR_ROOT,
    ],
];
