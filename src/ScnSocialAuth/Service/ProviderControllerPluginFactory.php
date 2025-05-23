<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use ScnSocialAuth\Controller\Plugin\ScnSocialAuthProvider;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerInterface;

/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class ProviderControllerPluginFactory implements FactoryInterface
{  
    public function createService(ServiceLocatorInterface $serviceManager)
    {
        $this($serviceManager, null);
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $mapper = $container->get('ScnSocialAuth-UserProviderMapper');
        $controllerPlugin = new ScnSocialAuthProvider($mapper);

        return $controllerPlugin;
    }
}
