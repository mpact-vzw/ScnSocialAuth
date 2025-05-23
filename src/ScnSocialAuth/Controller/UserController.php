<?php
namespace ScnSocialAuth\Controller;

use Hybridauth\Hybridauth as Hybrid_Auth;
use ScnSocialAuth\Mapper\Exception as MapperException;
use ScnSocialAuth\Mapper\UserProviderInterface;
use ScnSocialAuth\Options\ModuleOptions;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    /**
     * @var UserProviderInterface
     */
    protected $mapper;

    /**
     * @var Hybrid_Auth
     */
    protected $hybridAuth;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var \LmcUser\Options\ModuleOptions
     */
    protected $lmcmoduleoptions;

    /**
     * @var \LmcUser\Options\ModuleOptions
     */
    protected $ScnSocialAuthAuthenticationAdapterChain;

    /*
     * @todo Make this dynamic / translation-friendly
     * @var string
     */
    protected $failedAddProviderMessage = 'Add provider failed. Please try again.';

    /**
     * @var callable $redirectCallback
     */
    protected $redirectCallback;

    /**
     * @param callable $redirectCallback
     */
    public function __construct($redirectCallback, $ScnSocialAuthAuthenticationAdapterChain, $hybridAuth)
    {
        $this->setScnSocialAuthAuthenticationAdapterChain($ScnSocialAuthAuthenticationAdapterChain);
        $this->setHybridAuth($hybridAuth);

        if (!is_callable($redirectCallback)) {
            throw new \InvalidArgumentException('You must supply a callable redirectCallback');
        }
        $this->redirectCallback = $redirectCallback;

    }

    public function addProviderAction()
    {
        // Make sure the provider is enabled, else 404
        $provider = $this->params('provider');
        if (!in_array($provider, $this->getOptions()->getEnabledProviders())) {
            return $this->notFoundAction();
        }

        $authService = $this->lmcUserAuthentication()->getAuthService();

        // If user is not logged in, redirect to login page
        if (!$authService->hasIdentity()) {
            return $this->redirect()->toRoute('lmcuser/login');
        }

        $hybridAuth = $this->getHybridAuth();
        $adapter = $hybridAuth->authenticate($provider);

        if (!$adapter->isUserConnected()) {
            $this->flashMessenger()->setNamespace('lmcuser-index')->addMessage($this->failedAddProviderMessage);

            return $this->redirect()->toRoute('lmcuser');
        }

        $localUser = $authService->getIdentity();
        $userProfile = $adapter->getUserProfile();
        $accessToken = $adapter->getAccessToken();

        try {
            $this->getMapper()->linkUserToProvider($localUser, $userProfile, $provider, $accessToken);
        } catch (MapperException\ExceptionInterface $e) {
            $this->flashMessenger()->setNamespace('lmcuser-index')->addMessage($e->getMessage());
        }

        $redirect = $this->redirectCallback;

        return $redirect();
    }

    public function providerLoginAction()
    {
        $provider = $this->getEvent()->getRouteMatch()->getParam('provider');
        if (!in_array($provider, $this->getOptions()->getEnabledProviders())) {
            return $this->notFoundAction();
        }
        $hybridAuth = $this->getHybridAuth();

        $query = array();
        if ($this->getLmcModuleOptions()->getUseRedirectParameterIfPresent() && $this->getRequest()->getQuery()->get('redirect')) {
            $query = array('redirect' => $this->getRequest()->getQuery()->get('redirect'));
        }
        $redirectUrl = $this->url()->fromRoute('scn-social-auth-user/authenticate/provider', array('provider' => $provider), array('query' => $query));

        $adapter = $hybridAuth->authenticate(
            $provider,
            array(
                'hauth_return_to' => $redirectUrl,
            )
        );

        $redirect = $this->redirectCallback;

        return $redirect();
    }

    public function loginAction()
    {
        $lmcUserLogin = $this->forward()->dispatch('lmcuser', array('action' => 'login'));
        if (!$lmcUserLogin instanceof ModelInterface) {
            return $lmcUserLogin;
        }
        $viewModel = new ViewModel();
        $viewModel->addChild($lmcUserLogin, 'lmcUserLogin');
        $viewModel->setVariable('options', $this->getOptions());

        $redirect = false;
        if ($this->getLmcModuleOptions()->getUseRedirectParameterIfPresent() && $this->getRequest()->getQuery()->get('redirect')) {
            $redirect = $this->getRequest()->getQuery()->get('redirect');
        }
        $viewModel->setVariable('redirect', $redirect);

        return $viewModel;
    }

    public function logoutAction()
    {
        Hybrid_Auth::logoutAllProviders();

        return $this->forward()->dispatch('lmcuser', array('action' => 'logout'));
    }

    public function providerAuthenticateAction()
    {
        // Get the provider from the route
        $provider = $this->getEvent()->getRouteMatch()->getParam('provider');
        if (!in_array($provider, $this->getOptions()->getEnabledProviders())) {
            return $this->notFoundAction();
        }

        if (!$this->hybridAuth) {
            // This is likely user that cancelled login...
            return $this->redirect()->toRoute('lmcuser/login');
        }

        // For provider authentication, change the auth adapter in the LmcUser Controller Plugin
        $this->lmcUserAuthentication()->setAuthAdapter($this->getScnSocialAuthAuthenticationAdapterChain());

        // Adding the provider to request metadata to be used by HybridAuth adapter
        $this->getRequest()->setMetadata('provider', $provider);

        // Forward to the LmcUser Authenticate action
        return $this->forward()->dispatch('lmcuser', array('action' => 'authenticate'));
    }

    public function registerAction()
    {
        $lmcUserRegister = $this->forward()->dispatch('lmcuser', array('action' => 'register'));
        if (!$lmcUserRegister instanceof ModelInterface) {
            return $lmcUserRegister;
        }
        $viewModel = new ViewModel();
        $viewModel->addChild($lmcUserRegister, 'lmcUserLogin');
        $viewModel->setVariable('options', $this->getOptions());

        $redirect = false;
        if ($this->getLmcModuleOptions()->getUseRedirectParameterIfPresent() && $this->getRequest()->getQuery()->get('redirect')) {
            $redirect = $this->getRequest()->getQuery()->get('redirect');
        }
        $viewModel->setVariable('redirect', $redirect);

        return $viewModel;
    }

    /**
     * set mapper
     *
     * @param  UserProviderInterface $mapper
     * @return HybridAuth
     */
    public function setMapper(UserProviderInterface $mapper)
    {
        $this->mapper = $mapper;

        return $this;
    }

    /**
     * get mapper
     *
     * @return UserProviderInterface
     */
    public function getMapper()
    {
        if (!$this->mapper instanceof UserProviderInterface) {
            $this->setMapper($this->getServiceLocator()->get('ScnSocialAuth-UserProviderMapper'));
        }

        return $this->mapper;
    }

    /**
     * Get the Hybrid_Auth object
     *
     * @return Hybrid_Auth
     */
    public function getHybridAuth()
    {
        if (!$this->hybridAuth) {
            $this->hybridAuth = $this->getServiceLocator()->get('HybridAuth');
        }

        return $this->hybridAuth;
    }

    /**
     * Set the Hybrid_Auth object
     *
     * @param  Hybrid_Auth    $hybridAuth
     * @return UserController
     */
    public function setHybridAuth(Hybrid_Auth $hybridAuth)
    {
        $this->hybridAuth = $hybridAuth;

        return $this;
    }

    /**
     * set options
     *
     * @param  ModuleOptions  $options
     * @return UserController
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * get options
     *
     * @return ModuleOptions
     */
    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceLocator()->get('ScnSocialAuth-ModuleOptions'));
        }

        return $this->options;
    }

    /**
     * @return \LmcUser\Options\ModuleOptions
     */
    public function getLmcModuleOptions()
    {
        return $this->lmcmoduleoptions;
    }

    /**
     * @param \LmcUser\Options\ModuleOptions $lmcmoduleoptions
     */
    public function setLmcModuleOptions($lmcmoduleoptions)
    {
        $this->lmcmoduleoptions = $lmcmoduleoptions;
    }

    /**
     * @return \LmcUser\Options\ModuleOptions
     */
    public function getScnSocialAuthAuthenticationAdapterChain()
    {
        return $this->ScnSocialAuthAuthenticationAdapterChain;
    }


    public function setScnSocialAuthAuthenticationAdapterChain($ScnSocialAuthAuthenticationAdapterChain){
        $this->ScnSocialAuthAuthenticationAdapterChain = $ScnSocialAuthAuthenticationAdapterChain;
    }
}
