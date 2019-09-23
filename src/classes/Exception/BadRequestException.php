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

use Sinpe\Framework\Http\ResponseHandlerInterface;

/**
 * 400.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class BadRequestException extends UnexpectedException
{
    /**
     * Error code
     *
     * @var integer
     */
    protected $errorCode = -400;

    /**
     * @return ResponseHandlerInterface
     */
    public function getResponseHandler(): ResponseHandlerInterface
    {
        return new BadRequestExceptionHandler($this);
    }
}
