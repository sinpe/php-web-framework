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
 * Handler for 400.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class BadRequestExceptionHandler extends RequestExceptionHandler
{
    /**
     * Invoke the handler
     *
     * @param  ResponseInterface $response
     * @return ResponseInterface
     * @throws UnexpectedValueException
     */
    public function handle(ResponseInterface $response): ResponseInterface
    {
        $response = parent::handle($response);
        $response = $response->withStatus(400);
        return $response;
    }

    /**
     * Create the variable will be rendered.
     *
     * @return 
     */
    protected function fmtOutput()
    {
        $except = $this->getException();

        $error = [
            'code' => $except->getCode(),
            'message' => $except->getMessage()
        ];

        return new ArrayObject($error);
    }
}
