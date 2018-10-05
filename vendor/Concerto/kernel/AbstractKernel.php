<?php
namespace Concerto\kernel;

use Psr\Container\ContainerInterface;
use Concerto\standard\InvokeInterface;

abstract class AbstractKernel implements InvokeInterface
{
    /**
    *   container
    *
    *   @var ContainerInterface
    **/
    protected $container;

    /**
    *   __construct
    *
    *   @param ContainerInterface
    **/
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
    *   {inherit}
    *
    **/
    public function getContainer()
    {
        return $this->container;
    }

    /**
    *   {inherit}
    *
    **/
    public function __invoke(...$args)
    {
        try {
            $this->run();
        } catch (\Exception $e) {
            $this->handleException($e);
        } catch (\Throwable $e) {
            $this->handlePhpError($e);
        }
    }

    /**
    *   run
    *
    **/
    abstract public function run();

    /**
    *   handleException
    *
    *   @param \Exception
    **/
    protected function handleException(\Exception $e){
        $callback = $this->container->get('error.exception');
        call_user_func($callback, $e, $this);
    }

    /**
    *   handlePhpError
    *
    *   @param \Throwable
    **/
    protected function handlePhpError(\Throwable $e){
        $callback = $this->container->get('error.throwable');
        call_user_func($callback, $e, $this);
    }
}
