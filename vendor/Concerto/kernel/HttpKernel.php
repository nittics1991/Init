<?php
namespace Concerto\kernel;

use Concerto\kernel\AbstractKernel;

class HttpKernel extends AbstractKernel
{
    /**
    *   {inherit}
    *
    **/
    protected function run()
    {
        $request = $this->getContaner()
            ->get('Psr\Http\Message\ServerRequestInterface');
        $responce = $this->getContaner()
            ->get('Psr\Http\Message\ResponseInterface');

        //middlewareのhandle methodが2種類ある事に対応
        $middleware = $this->getContaner()
            ->get('Concerto\http\MiddlewareInterface');
        $responce = $middleware->dispatch($request, $responce);

        $emitter = $this->getContaner()
            ->get('Concerto\http\EmitterInterface');
        $emitter->emit($responce);
    }
}
