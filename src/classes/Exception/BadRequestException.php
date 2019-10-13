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
use Sinpe\Framework\Http\ResponderInterface;

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
     * @param ServerRequestInterface $request
     * @return ResponderInterface
     */
    public function getResponder(ServerRequestInterface $request): ResponderInterface
    {
        return new BadRequestExceptionResponder($request);
    }
}
