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

use Sinpe\Framework\Http\ResponderInterface;

/**
 * Exception with response.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class InternalException extends \RuntimeException
{
    /**
     * Error code
     *
     * @var integer
     */
    protected $errorCode = -500;

    /**
     * @var array
     */
    private $context = [];

    /**
     * __construct
     *
     * @param string $message
     * @param mixed $code
     * @param mixed $previous
     * @param mixed $context
     */
    public function __construct(
        string $message,
        $code = null,
        $previous = null,
        $context = []
    ) {

        if (!is_int($code)) {
            $context = $previous;
            $previous = $code;
            $code = $this->errorCode;
        }

        if (!$previous instanceof \Exception) {
            $context = $previous;
            $previous = null;
        }

        if (!is_array($context)) {
            $context = [];
        }

        $this->context = $context;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Attached data.
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return ResponderInterface
     */
    public function getResponder(): ResponderInterface
    {
        return new InternalExceptionResponder($this);
    }
}