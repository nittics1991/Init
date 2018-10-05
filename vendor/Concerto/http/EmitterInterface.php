<?php
namespace Concerto\http;

use Psr\Http\Message\ResponseInterface;

interface EmitterInterface
{
    /**
    *   emit
    *
    *   @param ResponceInterface
    **/
    public function emit(ResponceInterface $responce);
}
