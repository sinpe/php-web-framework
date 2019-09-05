<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Exception;

use Psr\Http\Server\RequestHandlerInterface;

/**
 * Client error.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class RequestException extends RuntimeException
{
    /**
     * @return RequestHandlerInterface
     */
    public function getResponseHandler(): RequestHandlerInterface
    {
        return new RequestExceptionHandler($this);
    }

    /**
     * Return default code.
     *
     * @return integer
     */
    protected function getDefaultCode()
    {
        return -1;
    }
}
