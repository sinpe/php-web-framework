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
 * Environment Interface
 *
 * @package Sinpe\Framework
 * @since   1.0.0
 */
interface EnvironmentInterface
{
    public static function mock(array $settings = []);

    public function getHost();
    public function getScheme();
}
