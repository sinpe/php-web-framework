<?php
 /*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Event;

use Psr\Http\Message\ResponseInterface;
use Sinpe\Framework\Http\Response;

/**
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class UnHandledException extends \Sinpe\Event\Event
{
    /**
     * @var \Throwable
     */
    private $ex;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * __construct
     *
     * @param \Throwable $ex
     */
    public function __construct(\Throwable $ex)
    {
        $this->ex = $ex;
        $this->response = new Response();
    }

    /**
     * Get
     *
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Set
     * 
     * @return void
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Get exception
     * 
     * @return \Throwable
     */
    public function getException(): \Throwable
    {
        return $this->ex;
    }
}
