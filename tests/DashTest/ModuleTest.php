<?php
/**
 * Dash
 *
 * @link      http://github.com/DASPRiD/Dash For the canonical source repository
 * @copyright 2013 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace DashTest;

use Dash\Module;
use Dash\Router\Http\Parser\ParserManager;
use Dash\Router\Http\Route\RouteManager;
use Dash\Router\Http\Router as HttpRouter;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Loader\AutoloaderFactory;
use Zend\Loader\StandardAutoloader;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers Dash\Module
 */
class ModuleTest extends TestCase
{
    /**
     * @var Module
     */
    protected $module;

    public function setUp()
    {
        $this->module = new Module();
    }

    public function testServiceSetup()
    {
        $config = $this->module->getConfig();
        $serviceManager = new ServiceManager(new Config($config['service_manager']));
        $serviceManager->setService('Config', []);

        $this->assertInstanceOf(
            ParserManager::class,
            $serviceManager->get(ParserManager::class)
        );

        $this->assertInstanceOf(
            RouteManager::class,
            $serviceManager->get(RouteManager::class)
        );

        $this->assertInstanceOf(
            HttpRouter::class,
            $serviceManager->get(HttpRouter::class)
        );
    }

    public function testAutoloaderSetup()
    {
        $config = $this->module->getAutoloaderConfig();
        AutoloaderFactory::factory($config);

        $autoloaders = AutoloaderFactory::getRegisteredAutoloaders();
        $this->assertEquals(1, count($autoloaders));
        $this->assertTrue(isset($autoloaders[StandardAutoloader::class]));

        $autoloader = $autoloaders[StandardAutoloader::class];
        $this->assertInstanceOf(StandardAutoloader::class, $autoloader);

        AutoloaderFactory::unregisterAutoloaders();
    }
}
