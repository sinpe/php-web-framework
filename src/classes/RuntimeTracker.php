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
 * 运行跟踪器.
 */
class RuntimeTracker
{
    /**
     * @var float
     */
    protected static $startTime = 0;

    /**
     * @var float
     */
    protected static $stopTime = 0;

    /**
     * 获取微秒时间.
     *
     * @return float
     */
    protected static function getMicrotime()
    {
        list($usec, $sec) = explode(' ', microtime());

        return (float) $usec + (float) $sec;
    }

    /**
     * 开始计时.
     */
    public static function start()
    {
        self::$startTime = self::getMicrotime();
    }

    /**
     * 结束计时.
     */
    public static function end()
    {
        self::$stopTime = self::getMicrotime();
    }

    /**
     * 计算用时.
     *
     * @return float
     */
    public static function spent()
    {
        return round((self::$stopTime - self::$startTime) * 1000, 2);
    }

    /**
     * 计算用时.
     *
     * @return float
     */
    public static function exec(\Closure $fn)
    {
        $startTime = self::getMicrotime();

        $fn();

        $stopTime = self::getMicrotime();

        return round(($stopTime - $startTime) * 1000, 2);
    }

    /**
     * 格式化当前内存消耗.
     */
    public static function memory()
    {
        $size = memory_get_usage();

        $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2)
            .' '.$unit[$i];
    }
}
