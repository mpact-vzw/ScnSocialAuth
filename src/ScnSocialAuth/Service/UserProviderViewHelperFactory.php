<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class UserProviderViewHelperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this($services, null);
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $viewHelper = new \ScnSocialAuth\View\Helper\ScnUserProvider();
        $viewHelper->setUserProviderMapper($container->get('ScnSocialAuth-UserProviderMapper'));

        return $viewHelper;
    }
}
