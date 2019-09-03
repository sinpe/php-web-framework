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

use Sinpe\Framework\CallableResolver;
use Sinpe\Framework\SettingInterface;
use Sinpe\Route\Router;
use Sinpe\Route\RouterInterface;
use Sinpe\Route\StrategyAutowiring;

/**
 * Dependency injection container.
 *
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class Container extends \Sinpe\Container\Container
{
    /**
     * Register the default items.
     */
    protected function registerDefaults()
    {
        $container = $this;

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

                if (isset($setting->router_cache_file)) {
                    $routerCacheFile = $setting->router_cache_file;
                }

                $router = (new Router())->setCacheFile($routerCacheFile);

                $router->setResolver(new CallableResolver($container));
                $router->setStrategy(new StrategyAutowiring($container));

                return $router;
            };

            $container[Router::class] = 'router';
            $container[RouterInterface::class] = 'router';
        }
    }

}
