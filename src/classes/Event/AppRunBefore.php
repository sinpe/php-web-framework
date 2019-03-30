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

use Psr\Http\Message\ServerRequestInterface;
use Sinpe\Event\Event;

/**
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class AppRunBefore extends Event
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * __construct
     *
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Get
     *
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @return void
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
}
