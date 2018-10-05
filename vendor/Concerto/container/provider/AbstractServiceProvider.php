<?php
/**
*   AbstractServiceProvider
*
*   @ver 180621
*   @see https://github.com/ecfectus/container
**/
namespace Concerto\container\provider;

use Concerto\container\ContainerAwareTrait;
use Concerto\container\provider\ServiceProviderInterface;

abstract class AbstractServiceProvider implements ServiceProviderInterface
{
    use ContainerAwareTrait;
    
    /**
    *   provides
    *
    *   @var array
    **/
    protected $provides = [];
    
    /**
    *   {inherit}
    *
    **/
    public function provides($alias = null)
    {
        if (!is_null($alias)) {
            return (in_array($alias, $this->provides));
        }
        return $this->provides;
    }
    
    /**
    *   bind
    *
    *   @param $id
    *   @param null $concrete
    *   @param bool|false $shared
    **/
    protected function bind($id, $concrete = null, $shared = false)
    {
        return $this->getContainer()->bind($id, $concrete, $shared);
    }
    
    /**
    *   share
    *
    *   @param $id
    *   @param null $concrete
    **/
    protected function share($id, $concrete = null)
    {
        return $this->getContainer()->share($id, $concrete);
    }
    
    /**
    *   raw
    *
    *   @param $id
    *   @param null $concrete
    **/
    protected function raw($id, $concrete = null)
    {
        return $this->getContainer()->raw($id, $concrete);
    }
}
