<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use ScnSocialAuth\Controller\RedirectCallback;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;


/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class RedirectCallbackFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
  {
    $router = $container->get('Router');
    $application = $container->get('Application');
    $options = $container->get('lmcuser_module_options');

    return new RedirectCallback($application, $router, $options);
  }
}
