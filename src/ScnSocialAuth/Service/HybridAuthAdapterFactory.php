<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use ScnSocialAuth\Authentication\Adapter\HybridAuth as HybridAuthAdapter;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class HybridAuthAdapterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $moduleOptions = $services->get('ScnSocialAuth-ModuleOptions');
        $lmcUserOptions = $services->get('lmcuser_module_options');

        $hybridAuth = $services->get('HybridAuth');
        $mapper = $services->get('ScnSocialAuth-UserProviderMapper');
        $lmcUserMapper = $services->get('lmcuser_user_mapper');

        $adapter = new HybridAuthAdapter($hybridAuth);
        $adapter->setOptions($moduleOptions);
        $adapter->setLmcUserOptions($lmcUserOptions);
        $adapter->setMapper($mapper);
        $adapter->setLmcUserMapper($lmcUserMapper);

        return $adapter;
    }
}
