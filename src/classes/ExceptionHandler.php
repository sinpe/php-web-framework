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
 * The exception handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class ExceptionHandler extends Http\ResponseHandler
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * __construct
     * 
     * @param \Exception $ex
     */
    public function __construct(\Exception $ex)
    {
        $this->exception = $ex;
    }

    /**
     * Get exception
     *
     * @return \Throwable
     */
    protected function getException(): \Throwable
    {
        return $this->exception;
    }
}
