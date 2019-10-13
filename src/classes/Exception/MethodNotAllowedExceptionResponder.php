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
use Sinpe\Framework\ArrayObject;

/**
 * Responder for 405.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class MethodNotAllowedExceptionResponder extends BadRequestExceptionResponder
{
    /**
     * __construct
     * 
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct($request);

        $this->registerResolvers([
            'text/html' => MethodNotAllowedExceptionHtmlResolver::class
        ]);
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function withResponse(ResponseInterface $response): ResponseInterface
    {
        $except = $this->getData('except');

        $response = $response->withStatus(405)
            ->withHeader('Allow', implode(', ', $except->getAllowedMethods()));
        return $response;
    }

    /**
     * Format the variable will be output.
     *
     * @return mixed
     */
    protected function getData():ArrayObject
    {
        $except = parent::getData('except');

        $error = [
            'code' => $except->getCode(),
            'message' => $except->getMessage(),
            'data' => [
                'allowed' => $except->getAllowedMethods()
            ]
        ];

        return new ArrayObject($error);
    }
}
