<?php
/**
*   サブディレクトリ内ファイルの一括登録Provider
*
*   @ver 180621
*   @example constructに引数がある場合,getする前に引数を配列でbindする
*       $container->bind(prefixId.constructParametersId, [argv1, argv2, ...])
*       $container->get(prefixId.constructParametersId)
**/
namespace Concerto\container\provider;

use \ReflectionClass;
use Concerto\container\provider\AbstractServiceProvider;

abstract class AbstractDirectoryServiceProvider extends AbstractServiceProvider
{
    /**
    *   subDirName(over write)
    *
    *   @var string
    *   @example $subDirName = 'subDir', 'subDir/childDir'
    **/
    protected $subDirName;
    
    /**
    *   prefixId(over write)
    *
    *   @var string|null
    **/
    protected $prefixId;
    
    /**
    *   resolevedItems
    *
    *   @var array
    **/
    protected $resolevedItems = [];
    
    /**
    *   constructParametersId
    *
    *   @var string
    **/
    protected $constructParametersId;
    
    /**
    *   __construct
    *
    *   @param array
    **/
    public function __construct()
    {
        $this->addProvides();
    }
    
    /**
    *   addProvides
    *
    **/
    private function addProvides()
    {
        $namespace = $this->resolveNamespace();
        
        $targetPath = $this->createRealPath();
        $iterator = new \FilesystemIterator($targetPath);
        
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $this->resolveItem($fileinfo, $namespace);
            }
        }
        $this->provides = array_merge(
            $this->provides,
            array_keys($this->resolevedItems)
        );
        
        //construct parameters
        $this->constructParametersId = isset($this->prefixId)?
            "{$this->prefixId}.constructParameters":
            "{$namespace}\constructParameters";
        array_push($this->provides, $this->constructParametersId);
    }
    
    /**
    *   resolveNamespace
    *
    *   @return string
    **/
    private function resolveNamespace()
    {
        $splited = explode('\\', get_called_class());
        array_pop($splited);
        
        $subDirName = mb_ereg_replace('/', '\\', $this->subDirName);
        
        return implode('\\', $splited) . '\\' . $subDirName;
    }
    
    /**
    *   createRealPath
    *
    *   @return string
    *   @throw LogicException
    **/
    private function createRealPath()
    {
        $thisPath = dirname(
            (new \ReflectionClass($this))->getFileName()
        );
        
        $subDirName = mb_ereg_replace(
            '/',
            DIRECTORY_SEPARATOR,
            $this->subDirName
        );
        
        $targetPath = $thisPath . DIRECTORY_SEPARATOR . $subDirName;
        
        if (!file_exists($targetPath) || is_null($this->subDirName)) {
            throw new \LogicException(
                "subDirName not found:{$this->subDirName}"
            );
        }
        return $targetPath;
    }
    
    /**
    *   resolveItem
    *
    *   @param SplFileInfo
    *   @param string
    **/
    private function resolveItem(\SplFileInfo $fileinfo, $namespace)
    {
        $className = $fileinfo->getBasename('.php');
        
        $id = isset($this->prefixId)?
            "{$this->prefixId}.{$className}":
            "{$namespace}\\{$className}";
        $class = "{$namespace}\\{$className}";
        
        $this->resolevedItems[$id] = $class;
    }
    
    /**
    *   {inherit}
    *
    **/
    public function register()
    {
        $constructParametersId = $this->constructParametersId;
        
        //$constructParametersIdの初期値を設定する
        //$container->get()前に$container->raw()した場合、初期化は不要
        //get()前にraw()した時は$constructParametersIdの初期値しない
        try {
            $this->getContainer()->get($constructParametersId);
        } catch (\Throwable $e) {
            $this->raw($constructParametersId, []);
        }
        
        foreach ($this->resolevedItems as $id => $className) {
            $this->bind($id, function ($container) use ($className, $constructParametersId) {
                $reflection = new ReflectionClass($className);
                
                $result = $reflection->newInstanceArgs(
                    $container->get($constructParametersId)
                );
                
                //$constructParametersIdをリセット
                $container->raw($constructParametersId, []);
                return $result;
            });
        }
    }
}
