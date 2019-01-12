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
trait AllTrait
{
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
        $code=null, 
        $previous=null
    ) {

        if (!is_int($code)) {
            if (!$code instanceof \Throwable) {
                $data = $code;
            } else {
                $previous = $code;
            }
            $code = $this->getDefaultCode();
        }

        parent::__construct($message, $code, $previous);
    }

}
