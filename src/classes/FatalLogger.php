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
 * FatalLogger class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class FatalLogger
{
    /**
     * Write to the error log
     *
     * @return void
     */
    public static function write($except)
    {
        $message = 'Error:' . PHP_EOL;

        $message .= self::ex2text($except);

        while ($except = $except->getPrevious()) {
            $message .= PHP_EOL . 'previous error:' . PHP_EOL;
            $message .= self::ex2text($except);
        }

        $message .= PHP_EOL . 'view in rendered output by enabling the "debug" setting.' . PHP_EOL;

        error_log($message);
    }

    /**
     * Render error as Text.
     *
     * @param \Throwable $except
     *
     * @return string
     */
    private static function ex2text($except)
    {
        $text = sprintf('type: %s' . PHP_EOL, get_class($except));

        if ($code = $except->getCode()) {
            $text .= sprintf('code: %s' . PHP_EOL, $code);
        }

        if ($message = $except->getMessage()) {
            $text .= sprintf('message: %s' . PHP_EOL, htmlentities($message));
        }

        if ($file = $except->getFile()) {
            $text .= sprintf('file: %s' . PHP_EOL, $file);
        }

        if ($line = $except->getLine()) {
            $text .= sprintf('line: %s' . PHP_EOL, $line);
        }

        if ($trace = $except->getTraceAsString()) {
            $text .= sprintf('trace: %s', $trace);
        }

        return $text;
    }
}
