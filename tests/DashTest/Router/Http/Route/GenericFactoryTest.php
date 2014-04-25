<?php
/**
 * Dash
 *
 * @link      http://github.com/DASPRiD/Dash For the canonical source repository
 * @copyright 2013 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace DashTest\Router\Http\Route;

use Dash\Router\Http\MatchResult\SuccessfulMatch;
use Dash\Router\Http\Parser\ParserManager;
use Dash\Router\Http\Route\GenericFactory;
use Dash\Router\Http\Route\RouteManager;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\PhpEnvironment\Request;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Uri\Http as HttpUri;

/**
 * @covers Dash\Router\Http\Route\GenericFactory
 */
class GenericFactoryTest extends TestCase
{
    public function testFactoryWithoutConfiguration()
    {
        $factory = new GenericFactory();
        $route   = $factory->createService($this->getMock(ServiceLocatorInterface::class));

        $this->assertNull($route->match($this->getHttpRequest(), 0));
    }

    public function testFactoryWithConfiguration()
    {
        $factory = new GenericFactory();
        $factory->setCreationOptions([
            '/foo',
            'action',
            'controller',
            'get',
            'hostname' => 'example.com',
            'secure' => true,
            'children' => [
                'bar' => ['path' => '/bar'],
            ],
        ]);

        $route = $factory->createService($this->getRouteManager());
        $this->assertInstanceOf(SuccessfulMatch::class, $route->match($this->getHttpRequest(), 0));
    }

    public function testPathOverwritesParameter()
    {
        $factory = new GenericFactory();
        $factory->setCreationOptions([
            '/bar',
            'action',
            'controller',
            'get',
            'hostname' => 'example.com',
            'secure' => true,
            'children' => [
                'bar' => ['path' => '/bar'],
            ],
            'path' => '/foo',
        ]);
        $route = $factory->createService($this->getRouteManager());
        $this->assertInstanceOf(SuccessfulMatch::class, $route->match($this->getHttpRequest(), 0));
    }

    public function testFactoryWithSpecifiedParsers()
    {
        $factory = new GenericFactory();
        $factory->setCreationOptions([
            '/foo/bar',
            'hostname' => 'example.com',
            'path_parser' => 'pathsegment',
            'hostname_parser' => 'hostnamesegment',
        ]);

        $route = $factory->createService($this->getRouteManager());
        $this->assertInstanceOf(SuccessfulMatch::class, $route->match($this->getHttpRequest(), 0));
    }

    protected function getHttpRequest()
    {
        $request = $this->getMock(Request::class);

        $request
            ->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue(new HttpUri('https://example.com/foo/bar')));

        return $request;
    }

    /**
     * @return RouteManager
     */
    protected function getRouteManager()
    {
        $serviceLocator = new ServiceManager();

        $parserManager = new ParserManager();
        $parserManager->setServiceLocator($serviceLocator);
        $serviceLocator->setService(ParserManager::class, $parserManager);

        $routeManager = new RouteManager();
        $routeManager->setServiceLocator($serviceLocator);

        return $routeManager;
    }
}
