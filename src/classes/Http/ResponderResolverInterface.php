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
use Sinpe\Framework\ArrayObject;

/**
 * The writer interface.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
interface ResponderResolverInterface
{
    /**
     * Custom response
     * 
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function withResponse(ResponseInterface $response): ResponseInterface;

    /**
     * Process a handler context and assign result to "output" property.
     *
     * @return string
     */
    public function resolve(ArrayObject $data);
}
