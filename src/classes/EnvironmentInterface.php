<?php
/*
 * This file is part of the long/dragon package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework;

/**
 * Environment Interface
 */
interface EnvironmentInterface
{
    /**
     * Create a mock server environment
     *
     * @param  array $settings Array of custom environment keys and values
     * @return static
     */
    public static function mock(array $settings = []);

    /**
     * Get host
     *
     * @return string
     */
    public function getHost(): string;

    /**
     * Get scheme
     *
     * @return string
     */
    public function getScheme(): string;
}
