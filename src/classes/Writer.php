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

use Psr\Http\Message\ResponseInterface;

/**
 * The request handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class Writer
{
    /**
     * Known handled content types
     *
     * @var array
     */
    protected $formatters = [
        'application/json' => Formatter\JsonFormatter::class,
        'text/html' => Formatter\HtmlFormatter::class,
        'text/xml' => Formatter\XmlFormatter::class,
        'application/xml' => Formatter\XmlFormatter::class
    ];

    /**
     * __construct
     *
     * @return static
     */
    public function __construct(array $formatters)
    {
        $this->formatters = array_merge($this->formatters, $formatters);
    }

    /**
     * Handler dispatchs a writer to render the content.
     *
     * @throws UnexpectedValueException
     */
    public function resolve(
        ResponseInterface $response,
        $output
    ): ResponseInterface {
        // 
        $acceptType = $response->getHeaderLine('Content-Type');
        // 
        if (array_key_exists($acceptType, $this->formatters)) {

            $formatter = $this->formatters[$acceptType];

            if ($formatter instanceof \Closure) {
                /*
                renderer需要依赖时，注入通过handler引入，再用此方式调用renderer
                比如：依赖Setting
                function ($output) use($setting) {
                    $formatter = new $formatter($setting);
                    return $formatter->process(new ArrayObject($content));
                }
                */
                $content = $formatter($output);
            } else {
                $content = (new $formatter)->process($output);
            }
        } else {
            throw new \UnexpectedValueException('can not render unknown content type ' . $acceptType);
        }

        return $response->write($content ?? '');
    }
}
