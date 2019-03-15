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
    protected $request;

    /**
     * A response object to send to the HTTP client
     *
     * @var ResponseInterface
     */
    protected $response;

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
        \Throwable $previous = null,
        // array $data = null,
        ServerRequestInterface $request = null,
        ResponseInterface $response = null
    ) {

        list($this->request, $this->response) = $this->extractRR(func_get_args());

        if (!is_int($code)) {

            if (!$code instanceof \Throwable) { // [message, data]
                // $data = $code;
            } else { // [message, previous]
                $previous = $code;
            }
            $code = $this->getDefaultCode();
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ExceptionHandler
     */
    public function getHandler()
    {
        return get_class($this) . 'Handler';
    }

    
    /**
     * Set request and response
     *
     * @param array $args
     * @return array
     */
    protected function getArgs($args): array
    {
        $request = null;
        $response = null;

        $r = array_pop($args);

        if ($r instanceof ResponseInterface) {
            $response = $r;
        } elseif ($r instanceof ServerRequestInterface) {
            $request = $r;
        }

        $r = array_pop($args);

        if ($r instanceof ServerRequestInterface) {
            $request = $r;
        } elseif ($r instanceof ResponseInterface) {
            $response = $r;
        }

        return [$request, $response];
    }
    
    /**
     * Set request and response
     *
     * @param array $args
     * @return array
     */
    protected function extractRR($args): array
    {
        $request = null;
        $response = null;

        $r = array_pop($args);

        if ($r instanceof ResponseInterface) {
            $response = $r;
        } elseif ($r instanceof ServerRequestInterface) {
            $request = $r;
        }

        $r = array_pop($args);

        if ($r instanceof ServerRequestInterface) {
            $request = $r;
        } elseif ($r instanceof ResponseInterface) {
            $response = $r;
        }

        return [$request, $response];
    }
}
