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
 * Exception handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class HandlingExceptionHandler extends RuntimeExceptionHandler
{
    /**
     * @var \Exception
     */
    private $originalException;

    /**
     * __construct
     */
    public function __construct($exception, $originalException)
    {
        $this->originalException = $originalException;

        parent::__construct($exception);
    }

    /**
     * Get original exception
     *
     * @return \Throwable
     */
    protected function getOriginalException(): \Throwable
    {
        return $this->originalException;
    }
}
