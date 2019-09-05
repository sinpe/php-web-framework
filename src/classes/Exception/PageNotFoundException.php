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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 404.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class PageNotFoundException extends BadRequestException
{
    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct('Page not found', -404);
    }

    /**
     * @return RequestHandlerInterface
     */
    public function getResponseHandler(): RequestHandlerInterface
    {
        return new PageNotFoundExceptionHandler($this);
    }
}
