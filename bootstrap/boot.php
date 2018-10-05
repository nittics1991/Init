<?php

if ($_ENV['DEBUG'])  {
    ini_set('display_errors', '1');
} else {
    ini_set('display_errors', '0');
}

error_reporting(E_ALL);

call_user_func(function() {
    $bootFiles = [
        __DIR__ . '/paths.php',
        __DIR__ . '/autoload.php',
        __DIR__ . '/iniset.php',
        __DIR__ . '/container.php',
        __DIR__ . '/start.php',
    ];

    foreach ($bootFiles as $path) {
        require_once realpath($path);
    }
});
