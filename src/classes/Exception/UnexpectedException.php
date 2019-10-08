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
 * Client error.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class UnexpectedException extends InternalException
{
    /**
     * Error code
     *
     * @var integer
     */
    protected $errorCode = -1;

    /**
     * @return ResponseHandlerInterface
     */
    public function getResponseHandler(): ResponseHandlerInterface
    {
        return new UnexpectedExceptionHandler($this);
    }
}
