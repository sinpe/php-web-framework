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
use Sinpe\Framework\Http\ResponderInterface;

/**
 * 405.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class MethodNotAllowedException extends BadRequestException
{
    /**
     * HTTP methods allowed
     *
     * @var string[]
     */
    protected $allowedMethods;

    /**
     * __construct
     *
     * @param string[] $allowedMethods
     * @param mixed $previous
     */
    public function __construct(array $allowedMethods)
    {
        $this->allowedMethods = $allowedMethods;

        parent::__construct('Method not allowed', -405);
    }

    /**
     * Get allowed methods
     *
     * @return string[]
     */
    public function getAllowedMethods()
    {
        return $this->allowedMethods;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponderInterface
     */
    public function getResponder(ServerRequestInterface $request): ResponderInterface
    {
        return new MethodNotAllowedExceptionResponder($request);
    }
}
