<?php

use Concerto\container\provider\AbstractServiceProvider;
use Concerto\http\Emitter;
use Concerto\http\MiddlewareHandler;

class DefaultConfigProvider extends AbstractServiceProvider implements
    BootableServiceProviderInterface
{
    protected $provides = [
        'error.exception',
        'error.throwable',
        'Psr\Http\Message\ServerRequestInterface',
        'Psr\Http\Message\ResponseInterface',
        'Concerto\http\MiddlewareInterface',
        'Concerto\http\EmitterInterface',
    ];
    
    public function register()
    {
        $this->share('error.exception', function ($container) {
            return function ($e, $app) {
                $logger = $container->get('Concerto\Log\LogInterface');
                $message = sprintf('%s', $e->__toString());
                $logger->error($message);
            };
        });
        
        $this->share('error.throwable', function ($container) {
            return function ($e, $app) {
                $logger = $container->get('Concerto\Log\LogInterface');
                $message = sprintf('%s', $e->__toString());
                $logger->warning($message);
            };
        });
        
        
        //標準でvendorにmiddlewareが必要?  middlewareみたいにfactoryで作る?
        //Psr\Http\Message\ServerRequestInterface
        //Psr\Http\Message\ResponseInterface
        
        
        
        //must be middleware
        //標準でvendorにmiddlewareが必要?
        $this->share('Concerto\http\MiddlewareInterface', function ($container) {
            return new MiddlewareHandler($container, $container->get('X.X.middleware');  //どうする?
        });
        
        $this->share('Concerto\http\EmitterInterface', function ($container) {
            return new Emitter();
        });
        
        
        
    }
    
    public function boot()
    {
        
        
        
    }
}
