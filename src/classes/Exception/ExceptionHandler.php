<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Http\Exception;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sinpe\Framework\Http\RequestHandler;
use Sinpe\Framework\Http\Body;
use Sinpe\Framework\Http\Response;

/**
 * The exception handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class ExceptionHandler extends RequestHandler
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * __construct
     * 
     * @param \Exception $ex
     */
    public function __construct(\Exception $ex)
    {
        $this->exception = $ex;
    }

    /**
     * Get exception
     *
     * @return \Exception
     */
    protected function getException(): \Exception
    {
        return $this->exception;
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
        $this->determineContentType($request);

        $response = $this->process($request, new Response());

        $body = new Body(fopen('php://temp', 'r+'));

        $body->write($this->content);

        return $response->withBody($body);
    }
}
