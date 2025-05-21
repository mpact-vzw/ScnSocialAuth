<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use ScnSocialAuth\Controller\HybridAuthController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class HybridAuthControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        $this($services, null);
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        // Just making sure to instantiate and configure
        // It's not actually needed in HybridAuthController
        $hybridAuth = $container->get('HybridAuth');

        $controller = new $requestedName();

        return $controller;
    }
}
