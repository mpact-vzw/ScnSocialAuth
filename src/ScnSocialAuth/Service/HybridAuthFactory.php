<?php
/**
 * ScnSocialAuth Module
 *
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */

namespace ScnSocialAuth\Service;

use Hybridauth\Hybridauth as Hybrid_Auth;
use Laminas\Router\Http\TreeRouteStack;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerInterface;

/**
 * @category   ScnSocialAuth
 * @package    ScnSocialAuth_Service
 */
class HybridAuthFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        $this($services, null);
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        // Making sure the SessionManager is initialized
        // before creating HybridAuth components
        $sessionManager = $container->get('ScnSocialAuth_LaminasSessionManager')->start();

        /* @var $options \ScnSocialAuth\Options\ModuleOptions */
        $options = $container->get('ScnSocialAuth-ModuleOptions');

        $baseUrl = $this->getBaseUrl($container);

        $hybridAuth = new Hybrid_Auth(
            array(
                'base_url' => $baseUrl,
                "debug_mode" => $options->getDebugMode(),
                "debug_file" => $options->getDebugFile(),
                'callback' => $options->getCallback(),
                'providers' => array(
                    'BitBucket' => array(
                        'enabled' => $options->getBitbucketEnabled(),
                        'keys' => array(
                            'key' => $options->getBitbucketKey(),
                            'secret' => $options->getBitbucketSecret(),
                        ),
                        'scope' => '',
                        'wrapper' => array(
                            'class' => 'Hybridauth\Provider\BitBucket',
                            'path' => realpath(__DIR__ . '/../HybridAuth/Provider/BitBucket.php'),
                        ),
                    ),
                    'Facebook' => array(
                        'enabled' => $options->getFacebookEnabled(),
                        'keys' => array(
                            'id' => $options->getFacebookClientId(),
                            'secret' => $options->getFacebookSecret(),
                        ),
                        'scope' => $options->getFacebookScope(),
                        'display' => $options->getFacebookDisplay(),
                        'trustForwarded' => $options->getFacebookTrustForwarded(),
                    ),
                    'Foursquare' => array(
                        'enabled' => $options->getFoursquareEnabled(),
                        'keys' => array(
                            'id' => $options->getFoursquareClientId(),
                            'secret' => $options->getFoursquareSecret(),
                        ),
                    ),
                    'GitHub' => array(
                        'enabled' => $options->getGithubEnabled(),
                        'keys' => array(
                            'id' => $options->getGithubClientId(),
                            'secret' => $options->getGithubSecret(),
                        ),
                        'scope' => $options->getGithubScope(),
                        'wrapper' => array(
                            'class' => 'Hybridauth\Provider\GitHub',
                            'path' => realpath(__DIR__ . '/../HybridAuth/Provider/GitHub.php'),
                        ),
                    ),
                    'Google' => array(
                        'enabled' => $options->getGoogleEnabled(),
                        'keys' => array(
                            'id' => $options->getGoogleClientId(),
                            'secret' => $options->getGoogleSecret(),
                        ),
                        'scope' => $options->getGoogleScope(),
                        'hd' => $options->getGoogleHd(),
                    ),
                    'LinkedIn' => array(
                        'enabled' => $options->getLinkedInEnabled(),
                        'keys' => array(
                            'key' => $options->getLinkedInClientId(),
                            'secret' => $options->getLinkedInSecret(),
                        ),
                    ),
                    'Twitter' => array(
                        'enabled' => $options->getTwitterEnabled(),
                        'keys' => array(
                            'key' => $options->getTwitterConsumerKey(),
                            'secret' => $options->getTwitterConsumerSecret(),
                        ),
                    ),
                    'Yahoo' => array(
                        'enabled' => $options->getYahooEnabled(),
                        'keys' => array(
                            'key' => $options->getYahooClientId(),
                            'secret' => $options->getYahooSecret(),
                        ),
                    ),
                    'Tumblr' => array(
                        'enabled' => $options->getTumblrEnabled(),
                        'keys' => array(
                            'key' => $options->getTumblrConsumerKey(),
                            'secret' => $options->getTumblrConsumerSecret(),
                        ),
                        'wrapper' => array(
                            'class' => 'Hybridauth\Provider\Tumblr',
                            'path' => realpath(__DIR__ . '/../HybridAuth/Provider/Tumblr.php'),
                        ),
                    ),
                    'Instagram' => array(
                        'enabled' => $options->getInstagramEnabled(),
                        'keys' => array(
                            'id' => $options->getInstagramClientId(),
                            'secret' => $options->getInstagramClientSecret(),
                        ),
                        'wrapper' => array(
                            'class' => 'Hybridauth\Provider\Instagram',
                            'path' => realpath(__DIR__ . '/../HybridAuth/Provider/Instagram.php'),
                        ),
                    ),
                ),
            )
        );

        return $hybridAuth;
    }

    public function getBaseUrl(ContainerInterface $services)
    {
        $router = $services->get('Router');
        if (!$router instanceof TreeRouteStack) {
            throw new ServiceNotCreatedException('TreeRouteStack is required to create a fully qualified base url for HybridAuth');
        }

        $request = $services->get('Request');
        if (!$router->getRequestUri() && method_exists($request, 'getUri')) {
            $router->setRequestUri($request->getUri());
        }
        if (!$router->getBaseUrl() && method_exists($request, 'getBaseUrl')) {
            $router->setBaseUrl($request->getBaseUrl());
        }

        return $router->assemble(
            array(),
            array(
                'name' => 'scn-social-auth-hauth',
                'force_canonical' => true,
            )
        );
    }
}
