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
        $zfcUserOptions = $services->get('zfcuser_module_options');

        $hybridAuth = $services->get('HybridAuth');
        $mapper = $services->get('ScnSocialAuth-UserProviderMapper');
        $zfcUserMapper = $services->get('zfcuser_user_mapper');

        $adapter = new HybridAuthAdapter($hybridAuth);
        $adapter->setOptions($moduleOptions);
        $adapter->setZfcUserOptions($zfcUserOptions);
        $adapter->setMapper($mapper);
        $adapter->setZfcUserMapper($zfcUserMapper);

        return $adapter;
    }
}
