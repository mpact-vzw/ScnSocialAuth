<?php
/**
 * @link      https://github.com/SocalNick/ScnSocialAuth for the canonical source repository
 * @copyright Copyright (c) 2012 Nicholas Calugar (http://socalnick.github.com)
 */

namespace ScnSocialAuthTest\Controller;

use ScnSocialAuth\Controller\UserController;
use PHPUnit_Framework_TestCase as TestCase;
use ScnSocialAuth\Options\ModuleOptions;
use Laminas\Mvc\Controller\PluginManager;
use Laminas\Mvc\MvcEvent;
use Laminas\Http\PhpEnvironment\Request;
use Laminas\ServiceManager\ServiceManager;

class UserControllerTest extends TestCase
{
    /**
     * @var \Laminas\ServiceManager\ServiceManager
     */
    protected $sm;

    /**
     * @var \Laminas\Mvc\Controller\PluginManager
     */
    protected $pm;

    /**
     * @var \ScnSocialAuth\Controller\UserController
     */
    protected $controller;

    /**
     * @var \Laminas\Mvc\MvcEvent;
     */
    protected $event;

    /**
     * @var \Laminas\Http\PhpEnvironment\Request
     */
    protected $request;

    public function setUp()
    {
        $this->sm = new ServiceManager();
        $this->pm = new PluginManager();
        $this->event = new MvcEvent();
        $this->request = new Request();
        $this->controller = new UserController(\Mockery::mock('ScnSocialAuth\Controller\RedirectCallback'));
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($this->sm);
        $this->controller->setPluginManager($this->pm);

        $forwardPlugin = \Mockery::mock('Laminas\Mvc\Controller\Plugin\Forward[dispatch]');
        $this->pm->setService('forward', $forwardPlugin);
    }

    public function tearDown()
    {
        \Mockery::close();
    }

    protected function dispatch($action, $params = array())
    {
        $routeMatch = new \Laminas\Mvc\Router\RouteMatch(array_merge(array('action' => $action), $params));
        $this->event->setRouteMatch($routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->dispatch($this->request);

        return $this->event->getResult();
    }

    public function testIsEventManagerAware()
    {
        $this->assertInstanceOf('Laminas\EventManager\EventManagerAwareInterface', $this->controller);
    }

    public function testIsDispatchable()
    {
        $this->assertInstanceOf('Laminas\Stdlib\DispatchableInterface', $this->controller);
    }

    public function testIsEventInjectable()
    {
        $this->assertInstanceOf('Laminas\Mvc\InjectApplicationEventInterface', $this->controller);
    }

    public function testRaisesExceptionOnDispatchIfEventDoesNotContainRouteMatch()
    {
        $request = new Request();
        $this->setExpectedException('Laminas\Mvc\Exception\DomainException', 'Missing route matches');
        $this->controller->dispatch($request);
    }

    public function testLoginProxiesToZfcUserAndReturnsNonModelInterface()
    {
        /** @var $forwardPlugin \Mockery\MockInterface */
        $forwardPlugin = $this->pm->get('forward');
        $forwardPlugin->shouldReceive('dispatch')
            ->with('zfcuser', array('action' => 'login'))
            ->andReturn('zfc-user-login');

        $result = $this->dispatch('login');
        $this->assertEquals('zfc-user-login', $result);
    }

    public function testProviderLoginInvalidProvider()
    {
        $this->controller->setOptions(new ModuleOptions());
        $result = $this->dispatch('provider-login', array('provider' => 'facebook'));
        $this->assertEquals('Page not found', $result->content);
    }
}
