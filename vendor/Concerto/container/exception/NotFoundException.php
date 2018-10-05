<?php
/**
*   NotFoundException
*
*   @ver 170208
**/
namespace Concerto\container\exception;

use \Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
}
