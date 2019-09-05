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
     * @param \Exception $ex
     */
    public function __construct(\Exception $ex)
    {
        parent::__construct($ex);

        static::registerRenderers([
            static::CONTENT_TYPE_HTML => MethodNotAllowedExceptionHtmlRenderer::class
        ]);
    }

    /**
     * Handler procedure.
     *
     * @return string
     */
    protected function process(
        ServerRequestInterface $request, 
        ResponseInterface $response
    ) : ResponseInterface {
        
        $response = $response->withStatus(405)
            ->withHeader('Allow', implode(', ', $this->getException()->getAllowedMethods()));

        return $this->doProcess($request, $response);
    }

    /**
     * Create the variable will be rendered.
     *
     * @return []
     */
    protected function getRendererOutput()
    {
        $ex = $this->getException();

        $error = [
            'code' => $ex->getCode(),
            'message' => $ex->getMessage(),
            'data' => [
                'allowed' => $ex->getAllowedMethods()
            ]
        ];

        return $error;
    }

}
