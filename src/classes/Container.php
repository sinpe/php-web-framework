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
        $this->register(new DefaultServicesProvider());
    }

}
