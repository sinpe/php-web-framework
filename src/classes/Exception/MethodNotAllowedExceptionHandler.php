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

        $this->registerResolvers([
            'text/html' => MethodNotAllowedExceptionHtmlResolver::class
        ]);
    }

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
        $response = $response->withStatus(405)
            ->withHeader('Allow', implode(', ', $this->getException()->getAllowedMethods()));
        return $response;
    }

    /**
     * Format the variable will be output.
     *
     * @return mixed
     */
    protected function fmtOutput()
    {
        $except = $this->getException();

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
