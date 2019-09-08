<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Note: Other Loaded before me can override all.
 */

if (!function_exists('container')) {
    /**
     * Dependency injection container.
     * 
     * You can override me directly.
     *
     * @param  string  $value
     * @return string
     */
    function container(string $name = null)
    {
        static $container;

        if (!$container) {
            $container = new \Sinpe\Framework\Container;
        }

        if (is_null($name)) {
            return $container;
        }

        return $container->get($name);
    }
}

if (!function_exists('config')) {
    /**
     * 配置
     */
    function config(string $key = null)
    {
        $config = container('config');

        if (is_null($key)) {
            return $config;
        }

        return $config->get($key);
    }
}

if (!function_exists('snake')) {
    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    function snake($value, $delimiter = '_')
    {
        $value = trim(preg_replace_callback(
            '#[A-Z]#',
            function ($matches) use ($delimiter) {
                return $delimiter . strtolower($matches[0]);
            },
            $value
        ), $delimiter);

        return $value;
    }
}

if (!function_exists('camel')) {
    /**
     * Convert a value to camel case.
     *
     * @param  string  $value
     * @return string
     */
    function camel($value)
    {
        return lcfirst(studly($value));
    }
}

if (!function_exists('studly')) {
    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    function studly(string $value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', $value);
    }
}

if (!function_exists('i18n')) {
    /**
     * 多语言
     */
    function i18n()
    {
        $arguments = func_get_args();
        return sprintf(...$arguments);
    }
}