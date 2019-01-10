<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Sinpe\Framework\DataObject;
use Sinpe\Framework\Http\Body;
use Sinpe\Framework\Http\Response;
use Sinpe\Framework\Renderer\Json as JsonRenderer;
use Sinpe\Framework\Renderer\Html as HtmlRenderer;
use Sinpe\Framework\Renderer\Xml as XmlRenderer;

/**
 * The output handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class ContentHandler implements RequestHandlerInterface
{
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_HTML = 'text/html';
    const CONTENT_TYPE_XML1 = 'text/xml';
    const CONTENT_TYPE_XML2 = 'application/xml';

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    protected $content = '';

    /**
     * Known handled content types
     *
     * @var array
     */
    protected $renderers = [
        self::CONTENT_TYPE_JSON => JsonRenderer::class,
        self::CONTENT_TYPE_XML1 => XmlRenderer::class,
        self::CONTENT_TYPE_XML2 => XmlRenderer::class,
        self::CONTENT_TYPE_HTML => HtmlRenderer::class
    ];

    /**
     * Content type
     *
     * @return string
     */
    protected function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Determine which content type we know about is wanted using Accept header
     *
     * Note: This method is a bare-bones implementation designed specifically for
     * error handling requirements. Consider a fully-feature solution such
     * as willdurand/negotiation for any other situation.
     *
     * @param  ServerRequestInterface $request
     * @return string
     */
    private function determineContentType($request)
    {
        $acceptHeader = $request->getHeaderLine('Accept');

        $defaultContentTypes = array_keys($this->renderers);

        $selectedContentTypes = array_intersect(explode(',', $acceptHeader), $defaultContentTypes);

        if (count($selectedContentTypes)) {
            $this->contentType = current($selectedContentTypes);
        } else {
            // handle +json and +xml specially
            if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
                //
                $mediaType = 'application/' . $matches[1];
                if (in_array($mediaType, $defaultContentTypes)) {
                    $this->contentType = $mediaType;
                }
            }
        }

        if (empty($this->contentType)) {
            $this->contentType = self::CONTENT_TYPE_HTML;
        }

        return $this->contentType;
    }

    /**
     * Invoke the handler
     *
     * @param  ServerRequestInterface $request
     * 
     * @return ResponseInterface
     * @throws UnexpectedValueException
     */
    final public function handle(ServerRequestInterface $request) : ResponseInterface 
    {
        $this->determineContentType($request);

        $response = new Response();

        $response = $this->process($request, $response);

        $body = new Body(fopen('php://temp', 'r+'));

        $body->write($this->content);

        return $response->withBody($body);
    }

    /**
     * Handler procedure.
     *
     * @return ResponseInterface
     */
    protected function process(ServerRequestInterface $request, ResponseInterface $response) : ResponseInterface
    {
        return $this->rendererProcess($request, $response);
    }

    /**
     * Handler dispatchs a renderer to render the content.
     *
     * @return string
     * @throws UnexpectedValueException
     */
    final protected function rendererProcess(
        ServerRequestInterface $request, 
        ResponseInterface $response
    ) : ResponseInterface {

        if ($request->getMethod() === 'OPTIONS') {
            $response = $response->withHeader('Content-type', 'text/plain');
        } else {
            if (array_key_exists($this->getContentType(), $this->renderers)) {

                $renderer = $this->renderers[$this->getContentType()];

                if ($renderer instanceof \Closure) {
                    /*
                    renderer需要依赖时，注入通过handler引入，再用此方式调用renderer
                    比如：依赖Setting
                    function ($response, $content) use($setting) {
                        $renderer = new $renderer($setting);
                        $response = $renderer->process(new DataObject($content));
                        $this->content = $renderer->getOutput();
                        return $response;
                    }
                    */
                    $this->content = $renderer($request, $response, $this->getContentOfHandler());
                } else {
                    $renderer = new $renderer;
                    $response = $renderer->process(new DataObject($this->getRendererContext($response)));

                    $this->content = $renderer->getOutput();
                }

                $response = $response->withHeader('Content-type', $this->contentType);

            } else {
                throw new \UnexpectedValueException('Cannot render unknown content type ' . $this->contentType);
            }
        }

        return $response;
    }

    /**
     * Register more renderers.
     * you can alse use this method to override the default renderer.
     *
     * @return void
     */
    protected function registerRenderers(array $renderers)
    {
        $this->renderers = array_merge($this->renderers, $renderers);
    }

    /**
     * Create the renderer context.
     *
     * @param ResponseInterface $response PSR-7 Response object
     * 
     * @return array
     */
    protected function getRendererContext(ResponseInterface $response)
    {
        return [
            'response' => $response,
            'content' => $this->getContentOfHandler()
        ];
    }

    /**
     * Create the content will be rendered.
     *
     * @return DataObject
     */
    abstract protected function getContentOfHandler();

}
