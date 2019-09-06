<?php

namespace Sinpe\Framework\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * Handles a server response and produces a new response.
 */
interface ResponseHandlerInterface
{
    /**
     * Handles a response and produces a new response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ResponseInterface $response): ResponseInterface;
}
