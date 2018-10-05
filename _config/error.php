<?php

return [
    'error.exception' => function ($e, $app) {
        $logger = $app->getContainer->get('Concerto\Log\LogInterface::class');
        $message = sprintf('%s', $e->__toString());
        $logger->error($message);
    },
    'error.throwable' => function ($e, $app) {
        $logger = $app->getContainer->get('Concerto\Log\LogInterface');
        $message = sprintf('%s', $e->__toString());
        $logger->warning($message);
    },
];
