<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Renderer;

use Psr\Http\Message\ResponseInterface;
use Sinpe\Framework\DataObject;
use Sinpe\Framework\RendererInterface;

/**
 * JSON renderer for common.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class Json implements RendererInterface
{
    /**
     * Unicode convertors
     *
     * @var array
     */
    private static $convertors = [
        [self::class, 'gb2312']
    ];

    /**
     * gb2312
     *
     * @return mixed
     */
    protected static function gb2312($content)
    {
        $result = @iconv('gb2312', 'utf-8', $content);

        if ($result) {
            return $result;
        }

        return $content;
    }

    /**
     * Set convertors.
     *
     * @param mixed $convertors
     * @param boolean $override
     * @return void
     */
    public static function setConvertors($convertors, $override = false)
    {
        if (is_array($convertors)) {
            if ($override) {
                self::$convertors = $convertors;
            } else {
                self::$convertors = array_merge(self::$convertors, $convertors);
            }
        } else {
            self::$convertors[] = $convertors;
        }
    }

    /**
     * @return mixed
     */
    protected function convert($content) 
    {
        $content = (array) $content;

        return array_map(function($item) {
            if (is_array($item)) {
                return $this->convert($item);
            } else {
                if (is_string($item)) {
                    foreach (self::$convertors as $callable) {
                        $item = call_user_func($callable, $item);
                    }
                }
                return $item;
            }
        }, $content);        
    }

    /**
     * Process a handler output and return the result.
     *
     * @return string
     */
    public function process(DataObject $output)
    {
        $content = json_encode($this->convert($output->all()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Ensure that the json encoding passed successfully
        if ($content === false) {
            throw new \RuntimeException(json_last_error_msg(), json_last_error());
        }

        return $content;
    }

}
