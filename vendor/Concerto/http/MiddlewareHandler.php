<?php
namespace Concerto\http;

use \InvalidArgumentExceprion;
use Psr\Http\Server\MiddlewareInterface as HandleInterface;
use Psr\Http\Server\RequestHandlerInterface as ProcessInterface;
use Concerto\http\MiddlewareInterface;

class MiddlewareHandler implements MiddlewareInterface
{
    /**
    *   middleware
    *
    *   @var RequestHandlerInterface|MiddlewareInterface
    **/
    protected $middleware;

    /**
    *   __construct
    *
    *   @param ContainerInterface
    *   @param RequestHandlerInterface|MiddlewareInterface
    **/
    public function __construct(ContainerInterface $container, $middleware)
    {
        if ($middleware instanceof HandleInterface ||
            $middleware instanceof ProcessInterface
        ) {
            $this->middleware = $middleware;
        }
        throw new InvalidArgumentExceprion(
            "must be MiddlewareInterface or RequestHandlerInterface"
        );
    }

    /**
    *   {inherit}
    *
    **/
    public function dispatch(
        ServerRequestInterface $request
        ResponceInterface $responce = null
    ) {
        
        
        //containerからmiddleware定義を持ってくるか?
        
        
        foreach ($middlewares as $x) {
            
            //frameworkではaddの定義は色々
            //interfaceで定義必要
            
            $x->add($x);
            
        }
        
        
        
        if ($this->middleware instanceof HandleInterface) {
            return $this->middleware->handle($request);
        }
        if (!is_null($responce)) {
            return $this->middleware->process($request, $responce);
        }
        throw new InvalidArgumentExceprion(
            "must be ResponceInterface"
        );
    }
}
