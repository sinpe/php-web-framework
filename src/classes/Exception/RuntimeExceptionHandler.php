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
use Sinpe\Framework\ArrayObject;
use Sinpe\Framework\FatalLogger;

/**
 * Exception handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class RuntimeExceptionHandler extends ExceptionHandler
{
    /**
     * var string
     */
    private $acceptType;

    /**
     * __construct
     * 
     * @param \Exception $except
     */
    public function __construct(\Exception $except)
    {
        parent::__construct($except);
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
        $this->acceptType = $response->getHeaderLine('Content-Type');

        // Write to the error log if debug is false
        if (!APP_DEBUG) {
            FatalLogger::write($this->getException());
        }

        $response = parent::handle($response);
        $response = $response->withStatus(500);

        return $response;
    }

}
