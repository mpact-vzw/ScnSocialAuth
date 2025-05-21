<?php

namespace ScnSocialAuth\Entity;

use LmcUser\Entity\User;

class UserProvider extends User implements UserProviderInterface
{
    protected $providerId;
    protected $provider;

    /**
     * @return the $userId
     */
    public function getUserId()
    {
        return $this->getId();
    }

    /**
     * @param  integer      $userId
     * @return UserProvider
     */
    public function setUserId($userId)
    {
        $this->setId($userId);
        return $this;
    }

    /**
     * @return the $providerId
     */
    public function getProviderId()
    {
        return $this->providerId;
    }

    /**
     * @param  integer      $providerId
     * @return UserProvider
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;

        return $this;
    }

    /**
     * @return the $provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param  string       $provider
     * @return UserProvider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }
}
