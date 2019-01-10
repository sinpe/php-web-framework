<?php
/*
 * This file is part of long/framework.
 *
 * (c) Sinpe Inc. <dev@sinpe.com>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Exception;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handler for 401.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class UnauthorizedHandler extends BadRequestHandler
{
    /**
     * Handler procedure.
     *
     * @return string
     */
    protected function process(
        ServerRequestInterface $request, 
        ResponseInterface $response
    ) : ResponseInterface {
        
        if ($this->getContentType() == static::CONTENT_TYPE_HTML) {
            $response = $response->withRedirect($this->getRedirectUrl());
        }

        $response = $response->withStatus(401);

        return $this->rendererProcess($request, $response);
    }

    /**
     * Return the passport url
     *
     * @return string
     */
    abstract protected function getRedirectUrl();

}
