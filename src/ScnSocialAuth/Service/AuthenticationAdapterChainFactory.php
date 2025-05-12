<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use Laminas\ServiceManager\FactoryInterface;
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
        // Temporarily replace the adapters in the module options with the HybridAuth adapter
        $lmcUserModuleOptions = $services->get('lmcuser_module_options');
        $currentAuthAdapters = $lmcUserModuleOptions->getAuthAdapters();
        $lmcUserModuleOptions->setAuthAdapters(array(100 => 'ScnSocialAuth\Authentication\Adapter\HybridAuth'));

        // Create a new adapter chain with HybridAuth adapter
        $factory = new AdapterChainServiceFactory();
        $chain = $factory->createService($services);

        // Reset the adapters in the module options
        $lmcUserModuleOptions->setAuthAdapters($currentAuthAdapters);

        return $chain;
    }
}
