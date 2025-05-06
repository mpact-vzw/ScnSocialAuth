<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use ScnSocialAuth\Controller\HybridAuthController;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class HybridAuthControllerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $controllerManager)
    {
        // Just making sure to instantiate and configure
        // It's not actually needed in HybridAuthController
        $hybridAuth = $controllerManager->getServiceLocator()->get('HybridAuth');

        $controller = new HybridAuthController();

        return $controller;
    }
}
