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

/**
 * Exception with request response.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
trait ExceptionTrait
{
    /**
     * A request object
     *
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * A response object to send to the HTTP client
     *
     * @var ResponseInterface
     */
    private $response;

    /**
     * __construct
     *
     * @param string $message
     * @param mixed $code
     * @param mixed $previous
     * @param array $data
     */
    public function __construct(
        string $message,
        $code = null,
        \Throwable $previous = null
    ) {

        if (!is_int($code)) {
            // [message, previous]
            $previous = $code;
            $code = $this->getDefaultCode();
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return RuntimeExceptionHandler
     */
    public function getHandler()
    {
        return get_class($this) . 'Handler';
    }

    /**
     * Set request
     *
     * @param ServerRequestInterface $request
     * @return $this
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Set response
     *
     * @param ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Set request
     *
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Set response
     * 
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Has request
     *
     * @return bool
     */
    public function hasRequest(): bool
    {
        return isset($this->request);
    }

    /**
     * Has response
     * 
     * @return bool
     */
    public function hasResponse(): bool
    {
        return isset($this->response);
    }
    
}
