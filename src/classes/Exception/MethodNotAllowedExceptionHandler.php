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
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handler for 405.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class MethodNotAllowedExceptionHandler extends BadRequestExceptionHandler
{
    /**
     * __construct
     * 
     * @param \Exception $except
     */
    public function __construct(\Exception $except)
    {
        parent::__construct($except);

        $this->registerWriters([
            static::CONTENT_TYPE_HTML => MethodNotAllowedExceptionHtmlFormatter::class
        ]);
    }

    /**
     * Handler procedure.
     *
     * @return string
     */
    protected function process(ResponseInterface $response): ResponseInterface
    {
        $response = $response->withStatus(405)
            ->withHeader('Allow', implode(', ', $this->getException()->getAllowedMethods()));

        return $response;
    }

    /**
     * Create the variable will be rendered.
     *
     * @return []
     */
    public function getOutput()
    {
        $except = $this->getException();

        $error = [
            'code' => $except->getCode(),
            'message' => $except->getMessage(),
            'data' => [
                'allowed' => $except->getAllowedMethods()
            ]
        ];

        return $error;
    }
}
