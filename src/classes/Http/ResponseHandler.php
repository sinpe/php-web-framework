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

/**
 * The response handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class ResponseHandler implements ResponseHandlerInterface
{
    /**
     * Known handled content types
     *
     * @var array
     */
    protected $resolvers = [
        'application/json' => ResponseHandlerJsonResolver::class,
        'text/html' => ResponseHandlerHtmlResolver::class,
        'application/xml' => ResponseHandlerXmlResolver::class,
        'text/xml' => ResponseHandlerXmlResolver::class,
    ];

    /**
     * Invoke the handler
     *
     * @param  ResponseInterface $response
     * @return ResponseInterface
     * @throws UnexpectedValueException
     */
    public function handle(ResponseInterface $response): ResponseInterface
    {
        // 
        $acceptType = $response->getHeaderLine('Content-Type');

        if (array_key_exists($acceptType, $this->resolvers)) {

            $resolver = $this->resolvers[$acceptType];

            if ($resolver instanceof \Closure) {
                /*
                renderer需要依赖时，注入通过handler引入，再用此方式调用renderer
                比如：依赖Setting
                function ($response) use($setting) {
                    $resolver = new $resolver($setting);
                    return $resolver->process(new ArrayObject($content));
                }
                */
                $content = $resolver($this->fmtOutput());
            } else {
                $content = container($resolver)->resolve($this->fmtOutput());
            }
            //
        } else {
            throw new \UnexpectedValueException('can not render unknown content type ' . $acceptType);
        }

        $body = new Body(fopen('php://temp', 'r+'));

        $body->write($content ?? '');

        return $response->withBody($body);
    }

    /**
     * Register resolver
     *
     * @return static
     */
    protected function registerResolvers(array $resolvers)
    {
        $this->resolvers = array_merge($this->resolvers, $resolvers);
        return $this;
    }

    /**
     * Format the variable will be output.
     *
     * @return mixed
     */
    abstract protected function fmtOutput();
}
