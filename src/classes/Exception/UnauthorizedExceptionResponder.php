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

/**
 * Responder for 401.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class UnauthorizedExceptionResponder extends BadRequestExceptionResponder
{
    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function withResponse(ResponseInterface $response): ResponseInterface
    {
        $acceptType = $response->getHeaderLine('Content-Type');

        if ($acceptType == 'text/html') {
            $response = $response->withRedirect($this->getRedirectUrl())->withStatus(302);
        } else {
            $response = $response->withStatus(401);
        }

        return $response;
    }

    /**
     * Return the passport url
     *
     * @return string
     */
    abstract protected function getRedirectUrl();
}
