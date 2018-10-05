<?php
/**
*   Service Container
*
*   @ver 170210
*   @see https://github.com/ecfectus/container
**/
namespace Concerto\container;

use Psr\Container\ContainerInterface;

interface ContainerAwareInterface
{
    /**
    *   setContainer
    *
    * @param ContainerInterface
    * @return mixed
    **/
    public function setContainer(ContainerInterface $container);
    
    /**
    *   getContainer
    *
    *   @return mixed
    **/
    public function getContainer();
}
