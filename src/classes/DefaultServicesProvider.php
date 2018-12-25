<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework;

use Psr\Container\ContainerInterface;
use Sinpe\Container\ProviderInterface;
use Sinpe\Middleware\CallableResolver;
use Sinpe\Framework\Setting;
use Sinpe\Framework\SettingInterface;
use Sinpe\Route\Router;
use Sinpe\Route\RouterInterface;
use Sinpe\Route\Strategies\Autowiring;

/**
 * Default Service Provider.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class DefaultServicesProvider implements ProviderInterface
{
    /**
     * Register default services.
     *
     * @param ContainerInterface $container A DI container implementing ArrayAccess and container-interop.
     */
    public function register(ContainerInterface $container)
    {
        if (!isset($container[SettingInterface::class])) {
            /**
             * Setting
             */
            $container[SettingInterface::class] = Setting::class;
        }

        if (!isset($container['router'])) {

            /**
             * This service MUST return a SHARED instance
             * of \Sinpe\Route\RouterInterface.
             *
             * @param Container $container
             *
             * @return RouterInterface
             */
            $container['router'] = function ($container) {

                $routerCacheFile = false;

                $setting = $container->get(SettingInterface::class);

                if (isset($setting->routerCacheFile)) {
                    $routerCacheFile = $setting->routerCacheFile;
                }

                $router = (new Router($container))->setCacheFile($routerCacheFile);

                return $router;
            };

            $container[Router::class] = 'router';
            $container[RouterInterface::class] = 'router';
        }

        if (!isset($container['foundHandler'])) {
            /**
             * This service MUST return a SHARED instance
             * of \Sinpe\Route\InvocationInterface.
             *
             * @return InvocationInterface
             */
            $container['foundHandler'] = function ($container) {
                return new Autowiring($container);
            };
        }

        if (!isset($container['callableResolver'])) {
            /**
             * Instance of \Sinpe\Middleware\CallableResolverInterface
             *
             * @param Container $container
             *
             * @return \Sinpe\Middleware\CallableResolverInterface
             */
            $container['callableResolver'] = function ($container) {
                return new CallableResolver($container);
            };
        }
    }
}
