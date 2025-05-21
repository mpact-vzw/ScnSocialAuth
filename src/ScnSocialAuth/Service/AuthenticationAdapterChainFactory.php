<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Authentication\Adapter\AdapterChainServiceFactory;

/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class AuthenticationAdapterChainFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $this($services, null);
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        // Temporarily replace the adapters in the module options with the HybridAuth adapter
        $lmcUserModuleOptions = $container->get('lmcuser_module_options');
        $currentAuthAdapters = $lmcUserModuleOptions->getAuthAdapters();
        $lmcUserModuleOptions->setAuthAdapters(array(100 => 'ScnSocialAuth\Authentication\Adapter\HybridAuth'));

        // Create a new adapter chain with HybridAuth adapter
        $factory = new AdapterChainServiceFactory();
        $chain = $factory->createService($container);

        // Reset the adapters in the module options
        $lmcUserModuleOptions->setAuthAdapters($currentAuthAdapters);

        return $chain;
    }
}
