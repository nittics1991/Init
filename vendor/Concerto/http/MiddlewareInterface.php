<?php
namespace Concerto\http;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponceInterface;

interface MiddlewareInterface
{
    /**
    *   dispatch
    *
    *   @param ServerRequestInterface
    *   @param ResponceInterface|null
    *   @return ResponceInterface
    **/
    public function dispatch(
        ServerRequestInterface $request
        ResponceInterface $responce = null
    );
}
