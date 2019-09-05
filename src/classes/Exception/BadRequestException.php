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
 * 400.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class BadRequestException extends RequestException
{
    /**
     * @return RequestHandlerInterface
     */
    public function getResponseHandler(): RequestHandlerInterface
    {
        return new BadRequestExceptionHandler($this);
    }

    /**
     * Return default code.
     *
     * @return integer
     */
    protected function getDefaultCode()
    {
        return -400;
    }
}
