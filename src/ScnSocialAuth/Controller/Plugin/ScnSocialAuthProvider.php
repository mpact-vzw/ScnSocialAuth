<?php

namespace ScnSocialAuth\Controller\Plugin;

use ScnSocialAuth\Mapper\UserProviderInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcUser\Entity\UserInterface;

class ScnSocialAuthProvider extends AbstractPlugin
{

    /**
     * @var UserProviderInterface
     */
    protected $mapper;

    function __construct(UserProviderInterface $mapper) {
        $this->mapper = $mapper;
    }

    /**
     * Returns a UserProviderInterface for $user and $provider
     *
     * @param UserInterface $user
     * @param string        $provider
     */
    public function getProvider(UserInterface $user, $provider)
    {
        return $this->getMapper()->findProviderByUser($user, $provider);
    }

    /**
     * Returns an array of UserProviderInterface for $user
     *
     * @param UserInterface $user
     */
    public function getProviders(UserInterface $user)
    {
        return $this->getMapper()->findProvidersByUser($user);
    }

    /**
     * get mapper
     *
     * @return UserProviderInterface
     */
    public function getMapper()
    {
        return $this->mapper;
    }

}
