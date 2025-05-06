<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use ScnSocialAuth\Controller\UserController;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $mapper = $container->get('ScnSocialAuth-UserProviderMapper');
        $moduleOptions = $container->get('ScnSocialAuth-ModuleOptions');
        $redirectCallback = $container->get('lmcuser_redirect_callback');
        $lmcuserModuleOptions = $container->get('lmcuser_module_options');
        $ScnSocialAuthAuthenticationAdapterChain = $container->get('ScnSocialAuth-AuthenticationAdapterChain');
        $hybridAuth = $container->get('HybridAuth');

        $controller = new UserController($redirectCallback, $ScnSocialAuthAuthenticationAdapterChain, $hybridAuth);
        $controller->setMapper($mapper);
        $controller->setOptions($moduleOptions);
        $controller->setLmcModuleOptions($lmcuserModuleOptions);

        try {
          $hybridAuth = $container->get('HybridAuth');
          $controller->setHybridAuth($hybridAuth);
        } catch (\Laminas\ServiceManager\Exception\ServiceNotCreatedException $e) {
          // This is likely the user cancelling login...
        }

        return $controller;
    }
}
