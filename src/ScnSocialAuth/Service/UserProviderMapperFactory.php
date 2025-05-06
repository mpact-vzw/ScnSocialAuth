<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use ScnSocialAuth\Mapper\UserProvider;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Stdlib\Hydrator;

/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class UserProviderMapperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $options = $services->get('ScnSocialAuth-ModuleOptions');
        $entityClass = $options->getUserProviderEntityClass();

        $mapper = new UserProvider();
        $mapper->setDbAdapter($services->get('ScnSocialAuth_ZendDbAdapter'));
        $mapper->setEntityPrototype(new $entityClass);
        $mapper->setHydrator(new Hydrator\ClassMethods);

        return $mapper;
    }
}
