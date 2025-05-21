<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use ScnSocialAuth\Authentication\Adapter\HybridAuth as HybridAuthAdapter;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class HybridAuthAdapterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $this($services, null);
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $moduleOptions = $container->get('ScnSocialAuth-ModuleOptions');
        $lmcUserOptions = $container->get('lmcuser_module_options');

        $hybridAuth = $container->get('HybridAuth');
        $mapper = $container->get('ScnSocialAuth-UserProviderMapper');
        $lmcUserMapper = $container->get('lmcuser_user_mapper');

        $adapter = new HybridAuthAdapter($hybridAuth);
        $adapter->setOptions($moduleOptions);
        $adapter->setLmcUserOptions($lmcUserOptions);
        $adapter->setMapper($mapper);
        $adapter->setLmcUserMapper($lmcUserMapper);

        return $adapter;
    }
}
