<?php
use Concerto\container\ServiceContainer as Container;

$container = call_user_func(function() {
    $containers = [
        'Concerto\container\ReflectionContainer',
        'Concerto\container\ServiceProviderContainer',
    ];

    $providers = [
        'DefaultConfigProvider',
        'Concerto\log\LogInterface',
    ];

    $container = new Container();

    foreach ($containers as $class) {
        $container->delegate(new $class);
    }

    foreach ($providers as $provider) {
        $container->addServiceProvider($provider);
    }

    $container->bootServiceProviders();
    return $container;
});
