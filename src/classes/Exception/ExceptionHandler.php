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
use Psr\Http\Server\RequestHandlerInterface;

use Sinpe\Framework\Http\RequestHandler;
use Sinpe\Framework\Http\Body;
use Sinpe\Framework\Http\Response;

/**
 * The exception handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class ExceptionHandler implements RequestHandlerInterface
{
    /**
     * @var \Exception
     */
    private $except;

    /**
     * __construct
     * 
     * @param \Exception $except
     */
    public function __construct(\Exception $except)
    {
        $this->except = $except;
    }

    /**
     * Get exception
     *
     * @return \Exception
     */
    protected function getException(): \Exception
    {
        return $this->except;
    }

    /**
     * Invoke the handler
     *
     * @param  ServerRequestInterface $request
     * 
     * @return ResponseInterface
     * @throws UnexpectedValueException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // 
        $response = $this->process(new Response($request));

        $acceptType = $response->getHeaderLine('Content-Type');

        $writers = array_keys(config('runtime.writers'));

        if (array_key_exists($acceptType, $writers)) {

            $writer = $writers[$acceptType];

            if ($writer instanceof \Closure) {
                /*
                renderer需要依赖时，注入通过handler引入，再用此方式调用renderer
                比如：依赖Setting
                function ($response) use($setting) {
                    $writer = new $writer($setting);
                    return $writer->process(new ArrayObject($content));
                }
                */
                $content = $writer($response);
            } else {
                $content = (new $writer)->process(new ArrayObject($this->getOutput()));
            }
            //
        } else {
            throw new \UnexpectedValueException('can not render unknown content type ' . $acceptType);
        }

        $body = new Body(fopen('php://temp', 'r+'));

        $body->write($content ?? '');

        return $response->withBody($body);
    }
}
