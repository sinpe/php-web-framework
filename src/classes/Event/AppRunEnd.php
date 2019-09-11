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

/**
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class AppRunEnd extends \Sinpe\Event\Event
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * __construct
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
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
}
