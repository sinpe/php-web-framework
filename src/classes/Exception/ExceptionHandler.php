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

use Psr\Http\Message\ResponseInterface;
use Sinpe\Framework\Http\ResponseHandler;

/**
 * The exception handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class ExceptionHandler extends ResponseHandler
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
     * Invoke the handler
     *
     * @param  ResponseInterface $response
     * @return ResponseInterface
     * @throws UnexpectedValueException
     */
    public function handle(ResponseInterface $response): ResponseInterface
    {
        $response = parent::handle($response);
        $response = $response->withStatus(500);
        return $response;
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
}
