<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework;

/**
 * Framework exception.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
trait ExceptionTrait
{
    /**
     * __construct
     *
     * @param string $message
     * @param mixed $code
     * @param mixed $previous
     */
    public function __construct(
        string $message,
        $code = null,
        \Throwable $previous = null
    ) {

        if (!is_int($code)) {
            // [message, previous]
            $previous = $code;
            $code = $this->getDefaultCode();
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getHandler()
    {
        return get_class($this) . 'Handler';
    }
    
}
