<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Http;

use Sinpe\Framework\ArrayObject;

/**
 * JSON writer for common.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class ResponseHandlerJsonResolver implements ResponseHandlerResolverInterface
{
    /**
     * @return mixed
     */
    protected function convert($content)
    {
        $content = (array) $content;

        return array_map(function ($item) {
            if (is_array($item)) {
                return $this->convert($item);
            } else {
                if (is_string($item)) {
                    $ary[] = "EUC-CN";
                    $ary[] = "UTF-8";
                    $item =  @iconv(mb_detect_encoding($item, $ary), 'utf-8', $item);
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
    public function resolve(ArrayObject $output)
    {
        $content = json_encode($this->convert($output->toArray()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Ensure that the json encoding passed successfully
        if ($content === false) {
            throw new \RuntimeException(json_last_error_msg(), json_last_error());
        }

        return $content;
    }
}
