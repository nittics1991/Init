<?php
/**
*   ContainerException
*
*   @ver 170208
**/
namespace Concerto\container\exception;

use \Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface
{
}
