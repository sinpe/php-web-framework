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
use Sinpe\Framework\DataObject;

/**
 * Handler for 404.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class NotFoundHandler extends BadRequestHandler
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Initliazation after construction.
     *
     * @return void
     */
    public function __init()
    {
        $this->registerRenderers([
            static::CONTENT_TYPE_HTML => function($request, $response) {
                $renderer = new NotFoundHtmlRenderer();
                $renderer->setHomeUrl((string)($request->getUri()->withPath('')->withQuery('')->withFragment('')));
                $response = $renderer->process(new DataObject($this->getRendererContext($request, $response)));
                $this->content = $renderer->getOutput();
                return $response
            }
        ]);
    }

    /**
     * Create message
     *
     * @return array
     */
    protected function getContentOfHandler()
    {
        $error = [
            'code' => $this->thrown->getCode(),
            'message' => $this->thrown->getMessage(),
            'data' => [
                'host' => $this->request->getUri()->getHost(),
                'path' => $this->request->getUri()->getPath()
            ]
        ];

        return new DataObject($error);
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
        
        $this->request = $request;

        $response = $response->withStatus(404);

        return $this->rendererProcess($request, $response);
    }

}
