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
class MethodNotAllowedHandler extends BadRequestHandler
{
    /**
     * Initliazation after construction.
     *
     * @return void
     */
    public function __init()
    {
        $this->registerRenderers([
            static::CONTENT_TYPE_HTML => MethodNotAllowedHtmlRenderer::class
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
            ->withHeader('Allow', implode(', ', $this->thrown->getAllowedMethods()));

        return $this->rendererProcess($request, $response);
    }

    /**
     * Create the variable will be rendered.
     *
     * @return []
     */
    protected function getRendererOutput()
    {
        $error = [
            'code' => $this->thrown->getCode(),
            'message' => $this->thrown->getMessage(),
            'data' => [
                'allowed' => $this->thrown->getAllowedMethods()
            ]
        ];

        return $error;
    }

}
