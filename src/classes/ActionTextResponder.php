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

use Psr\Http\Message\ResponseInterface;

/**
 * The throwable handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class ActionTextResponder extends Http\Responder
{
    /**
     * 页面输出
     *
     * @param  ResponseInterface      $response The most recent Response object
     * @param  string                 $content 输出页面
     *
     * @return ResponseInterface
     */
    public function display(string $content): ResponseInterface
    {
        return $this->handle(['data' => $content]);
    }
}
