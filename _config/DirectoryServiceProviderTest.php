<?php
namespace Concerto\test\container;

use Concerto\test\ConcertoTestCase;
use Concerto\test\container\TestDirectoryServiceProvider;
use Concerto\container\ServiceProviderContainer;
use Concerto\container\provider\AbstractDirectoryServiceProvider;
use Concerto\container\ServiceContainer;

////////////////////////////////////////////////////////////////////////////////
class TestDirectoryServiceProvider extends AbstractDirectoryServiceProvider
{
    protected $subDirName = 'directory';
}

class TestFailedDirectoryServiceProvider extends AbstractDirectoryServiceProvider
{
    protected $subDirName = 'faildPath';
}

class TestChildDirectoryServiceProvider extends AbstractDirectoryServiceProvider
{
    protected $subDirName = 'directory/child';
}

class TestNamedDirectoryServiceProvider extends AbstractDirectoryServiceProvider
{
    protected $subDirName = 'directory/child';
    protected $prefixId = 'myprovider';
}

////////////////////////////////////////////////////////////////////////////////

class DirectoryServiceProviderTest extends ConcertoTestCase
{
    /**
    *   @test
    **/
    public function first()
    {
//		$this->markTestIncomplete();
        
        $obj = new TestDirectoryServiceProvider();
        
        $actual = [
            __NAMESPACE__ . '\\directory\\ProviderTarget1',
            __NAMESPACE__ . '\\directory\\ProviderTarget2',
            __NAMESPACE__ . '\\directory\\ProviderTarget3',
            __NAMESPACE__ . '\\directory\\constructParameters',
        ];
        $expect = $this->getPrivateProperty($obj, 'provides');
        
        $this->assertEquals($actual, $expect);
        
        //use prefixed id
        $obj = new TestNamedDirectoryServiceProvider();
        
        $actual = [
            'myprovider.ChildProviderTarget1',
            'myprovider.ChildProviderTarget2',
            'myprovider.constructParameters',
        ];
        $expect = $this->getPrivateProperty($obj, 'provides');
        
        $this->assertEquals($actual, $expect);
    }
    
    /**
    *   @test
    *   @expectedException LogicException
    */
    public function faildPathName()
    {
//		$this->markTestIncomplete();
        
        $obj = new TestFailedDirectoryServiceProvider();
    }
    
    /**
    *   @test
    **/
    public function addProvider()
    {
//		$this->markTestIncomplete();
        
        $container = new ServiceContainer();
        $serviceProvider = new ServiceProviderContainer();
        $container->delegate($serviceProvider);
        
        $container->addServiceProvider(TestDirectoryServiceProvider::class);
        
        $actual = __NAMESPACE__ . '\\directory\\ProviderTarget1';
        $obj = $container->get($actual);
        $this->assertEquals($actual, $obj());
    }
    
    /**
    *   @test
    **/
    public function childSubDir()
    {
//		$this->markTestIncomplete();
        
        $container = new ServiceContainer();
        $serviceProvider = new ServiceProviderContainer();
        $container->delegate($serviceProvider);
        
        $container->addServiceProvider(TestChildDirectoryServiceProvider::class);
        
        $actual = __NAMESPACE__ . '\\directory\\child\\ChildProviderTarget2';
        $obj = $container->get($actual);
        $this->assertEquals($actual, $obj());
    }
    
    /**
    *   @test
    **/
    public function specifiedName()
    {
//		$this->markTestIncomplete();
        
        $container = new ServiceContainer();
        $serviceProvider = new ServiceProviderContainer();
        $container->delegate($serviceProvider);
        
        $container->addServiceProvider(TestNamedDirectoryServiceProvider::class);
        
        $actual = __NAMESPACE__ . '\\directory\\child\\ChildProviderTarget2';
        $obj = $container->get('myprovider.ChildProviderTarget2');
        $this->assertEquals(true, $obj instanceof $actual);
    }
    
    /**
    *   @test
    **/
    public function constructParameters()
    {
//		$this->markTestIncomplete();
        
        $container = new ServiceContainer();
        $serviceProvider = new ServiceProviderContainer();
        $container->delegate($serviceProvider);
        $container->addServiceProvider(TestDirectoryServiceProvider::class);
        
        //set construct parameters
        $container->raw(
            __NAMESPACE__ . '\\directory\\constructParameters',
            ['aaa', 'bbb']
        );
        
        $obj = $container->get(__NAMESPACE__ . '\\directory\\ProviderTarget3');
        $this->assertEquals('aaa_bbb', $obj());
        
        //call next object. not has args
        $obj = $container->get(__NAMESPACE__ . '\\directory\\ProviderTarget1');
        $this->assertEquals(__NAMESPACE__ . '\\directory\\ProviderTarget1', $obj());
        
        // re set construct parameters
        $container->raw(
            __NAMESPACE__ . '\\directory\\constructParameters',
            ['AAAA', 'BBBB']
        );
        
        $obj = $container->get(__NAMESPACE__ . '\\directory\\ProviderTarget3');
        $this->assertEquals('AAAA_BBBB', $obj());
    }
}
