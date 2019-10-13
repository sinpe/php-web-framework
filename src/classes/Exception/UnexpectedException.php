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

use Psr\Http\Message\ServerRequestInterface;
use Sinpe\Framework\Http\Responder;

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
     * @param ServerRequestInterface $request
     * @return Responder
     */
    public function getResponder(ServerRequestInterface $request): Responder
    {
        return new UnexpectedExceptionResponder($request);
    }
}
