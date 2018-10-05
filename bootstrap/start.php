<?php
use Concerto\kernel\ConsoleKernel;
use Concerto\kernel\HttpKernel;

if (php_sapi_name() == 'cli') {
    $app = new ConsoleKernel($container);
} else {
    $app = new HttpKernel($container);
}
$app();
