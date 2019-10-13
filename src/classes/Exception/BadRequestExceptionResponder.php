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

use Psr\Http\Message\ResponseInterface;
use Sinpe\Framework\ArrayObject;

/**
 * Responder for 400.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class BadRequestExceptionResponder extends UnexpectedExceptionResponder
{
    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function withResponse(ResponseInterface $response): ResponseInterface
    {
        return $response->withStatus(400);
    }

    /**
     * Create the variable will be rendered.
     *
     * @return 
     */
    protected function fmtData(): ArrayObject
    {
        $except = $this->getData('except');

        $error = [
            'code' => $except->getCode(),
            'message' => $except->getMessage()
        ];

        return new ArrayObject($error);
    }
}
