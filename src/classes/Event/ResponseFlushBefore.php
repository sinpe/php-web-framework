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

use Psr\Http\Message\StreamInterface;

/**
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class ResponseFlushBefore extends \Sinpe\Event\Event
{
    /**
     * @var StreamInterface
     */
    private $body;

    /**
     * __construct
     *
     * @param StreamInterface $body
     */
    public function __construct(StreamInterface $body)
    {
        $this->body = $body;
    }

    /**
     * Get
     *
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * Set
     * 
     * @return void
     */
    public function setBody(StreamInterface $body)
    {
        $this->body = $body;
    }
}
