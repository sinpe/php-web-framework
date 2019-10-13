<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class ResponderResolver implements ResponderResolverInterface
{
    /**
     * Custom response
     * 
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function withResponse(ResponseInterface $response): ResponseInterface
    {
        return $response;
    }
}
