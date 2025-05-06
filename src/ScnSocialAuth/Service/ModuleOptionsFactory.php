<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use ScnSocialAuth\Options;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;


/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class ModuleOptionsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $config = $container->get('Configuration');

        return new Options\ModuleOptions(isset($config['scn-social-auth']) ? $config['scn-social-auth'] : array());
    }
}
