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

/**
 * Client error.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class RequestException extends RuntimeException
{
    use ExceptionTrait;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * __construct
     *
     * @param string $message
     * @param mixed $code
     * @param mixed $previous
     * @param mixed $data
     */
    public function __construct(
        string $message,
        $code = null,
        $previous = null,
        $data = []
    ) {

        if (!is_int($code)) {
            if (!$code instanceof \Throwable) {
                $data = $code;
            } else {
                $previous = $code;
            }
            $code = $this->getDefaultCode();
        }

        if (!$previous instanceof \Throwable) {
            $data = $previous;
            $previous = null;
        }

        if (!is_array($data)) {
            $data = [];
        }

        $this->data = $data;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Attached data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
