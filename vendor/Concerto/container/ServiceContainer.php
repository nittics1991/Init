<?php
/**
*   Service Container
*
*   @version 180621
*   @see https://github.com/ecfectus/container
**/
namespace Concerto\container;

use Psr\Container\ContainerInterface;
use Concerto\container\exception\ContainerException;
use Concerto\container\exception\NotFoundException;

class ServiceContainer implements ContainerInterface
{
    /**
    *   definitions
    *
    *   @var array
    */
    protected $definitions = [];
    
    /**
    *   sharedDefinitions
    *
    *   @var array
    */
    protected $sharedDefinitions = [];
    
    /**
    *sharedInstances
    *
    *   @var array
    */
    protected $sharedInstances = [];
    
    /**
    *   delegates
    *
    *   @var array
    */
    protected $delegates = [];
    
    /**
    *   extenders
    *
    *   @var array
    */
    protected $extenders = [];
    
    /**
    *   raws
    *
    *   @var array
    */
    protected $raws = [];
    
    /**
    *   {inherit}
    *
    */
    public function get($id)
    {
        if (array_key_exists($id, $this->raws)) {
            return $this->raws[$id];
        }
        
        if (array_key_exists($id, $this->sharedInstances)) {
            return $this->sharedInstances[$id];
        }
        
        if (array_key_exists($id, $this->sharedDefinitions)) {
            $instance = $this->makeFromDefinition($this->sharedDefinitions[$id]);
            $instance = $this->applyExtenders($id, $instance);
            $this->sharedInstances[$id] = $instance;
            return $instance;
        }
        
        if (array_key_exists($id, $this->definitions)) {
            $instance = $this->makeFromDefinition($this->definitions[$id]);
            $instance = $this->applyExtenders($id, $instance);
            return $instance;
        }
        
        if ($resolved = $this->getFromDelegate($id)) {
            $resolved = $this->applyExtenders($id, $resolved);
            return $resolved;
        }
        throw new NotFoundException("{$id} is not being managed by the container");
    }
    
    /**
    *   {inherit}
    *
    **/
    public function has($id)
    {
        //notes:bind()のconcreteに配列で定義した時、引数にarrayが使えない
        //example:bind('name', [function($arg){return new ArrayObject($arg);}, [1, 2, 3]]);
        if (!is_string($id)) {
            return false;
        }
        //ここまで
        
        if (array_key_exists($id, $this->definitions)) {
            return true;
        }
        
        if (array_key_exists($id, $this->sharedDefinitions)) {
            return true;
        }
        
        if (array_key_exists($id, $this->sharedInstances)) {
            return true;
        }
        return $this->hasInDelegate($id);
    }
    
    /**
    *   bind
    *
    *   @param $id
    *   @param null $concrete
    *   @param bool|false $shared
    **/
    public function bind($id, $concrete = null, $shared = false)
    {
        if (null === $concrete) {
            $concrete = $id;
        }
        $id = $this->normalizeString($id);
        
        if (is_object($concrete) && !$concrete instanceof \Closure) {
            $instance = $this->applyExtenders($id, $concrete);
            $this->sharedInstances[$id] = $instance;
            return;
        }
        $concrete = $this->normalizeString($concrete);
        
        //定数arrayをbindする時、$container[0]が無いと言われる
        // if (is_callable($concrete)
        //    || (is_array($concrete) && is_callable($concrete[0]))
        if (is_callable($concrete)
            || (is_array($concrete) && isset($concrete[0]) && is_callable($concrete[0]))
        ) {
            if (false === $shared) {
                $this->definitions[$id] = (array) $concrete;
                return;
            }
            $this->sharedDefinitions[$id] = (array) $concrete;
            return;
        }
        
        //定数arrayをbindする時、$container[0]が無いと言われる
        // if (is_string($concrete) && class_exists($concrete)
        //    || (is_array($concrete) && class_exists($concrete[0]))
        if (is_string($concrete) && class_exists($concrete)
            || (is_array($concrete) && isset($concrete[0]) && class_exists($concrete[0]))
        ) {
            if (false === $shared) {
                $this->definitions[$id] = (array) $concrete;
                return;
            }
            $this->sharedDefinitions[$id] = (array) $concrete;
            return;
        }
        $this->sharedInstances[$id] = $concrete;
    }
    
    /**
    *   share
    *
    *   @param $id
    *   @param null $concrete
    **/
    public function share($id, $concrete = null)
    {
        return $this->bind($id, $concrete, true);
    }
    
    /**
    *   extend
    *
    *   @param $id
    *   @param callable $extender
    **/
    public function extend($id, callable $extender)
    {
        if (!$this->has($id)) {
            throw new NotFoundException("{$id} is not being managed by the container");
        }
        $this->extenders[$id][] = $extender;
    }
    
    /**
    *   delegate
    *
    *   @param ContainerInterface $container
    *   @return $this
    **/
    public function delegate(ContainerInterface $container)
    {
        $this->delegates[] = $container;
        if ($container instanceof ContainerAwareInterface) {
            $container->setContainer($this);
        }
        return $this;
    }
    
    /**
    *   hasInDelegate
    *
    *   @param $id
    *   @return bool
    **/
    private function hasInDelegate($id)
    {
        foreach ($this->delegates as $container) {
            if ($container->has($id)) {
                return true;
            }
        }
        return false;
    }
    
    /**
    *   getFromDelegate
    *
    *   @param $id
    *   @return bool
    **/
    protected function getFromDelegate($id)
    {
        foreach ($this->delegates as $container) {
            if ($container->has($id)) {
                return $container->get($id);
            }
            continue;
        }
        return false;
    }
    
    /**
    *   makeFromDefinition
    *
    *   @param $definition
    *   @return bool|mixed|object
    **/
    private function makeFromDefinition($definition)
    {
        $target = array_shift($definition);
        
        if (empty($definition)) {
            //引数無しfunctionが動かない
            if ($target instanceof \Closure) {
                return $target($this);
            }
            //ここまで
            
            if (is_callable($target)) {
                return $target();
            }
                
            if (class_exists($target)) {
                $instance = new $target;
                return $instance;
            }
            return false;
        }
        
        foreach ($definition as $key => $value) {
            if ($this->has($value)) {
                $definition[$key] = $this->get($value);
                continue;
            }
        }
        
        if (is_callable($target)) {
            return $target(...$definition);
        }
        
        if (class_exists($target)) {
            return new $target(...$definition);
        }
        return false;
    }
    
    /**
    *   normalizeString
    *
    *   @param $string
    *   @return string
    **/
    private function normalizeString($string)
    {
        return (is_string($string) && strpos($string, '\\') === 0)?
        substr($string, 1) : $string;
    }
    
    /**
    *   applyExtenders
    *
    *   @param $id
    *   @param $instance
    *   @return mixed
    **/
    private function applyExtenders($id, $instance)
    {
        if (isset($this->extenders[$id]) && !empty($this->extenders[$id])) {
            foreach ($this->extenders[$id] as $extender) {
                $instance = $extender($instance, $this);
            }
        }
        return $instance;
    }
    
    /**
    *   __call
    *
    *   @param string
    *   @param array
    **/
    public function __call($method, $arguments)
    {
        foreach ($this->delegates as $container) {
            if (is_callable([$container, $method])) {
                return $container->$method(...$arguments);
            }
        }
    }
    
    /**
    *   raw
    *
    *   @param string
    *   @param mixed
    **/
    public function raw($id, $concrete)
    {
        if ($concrete instanceof \Closure) {
            throw InvalidArgumentException("required scala,array,resource,object");
        }
        $this->raws[$id] = $concrete;
    }
}
