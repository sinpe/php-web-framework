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
    private $originalExcept;

    /**
     * __construct
     */
    public function __construct(\Exception $except, \Exception $originalExcept)
    {
        $this->originalExcept = $originalExcept;

        parent::__construct($except);
    }

    /**
     * Get original exception
     *
     * @return \Exception
     */
    protected function getOriginalException(): \Exception
    {
        return $this->originalExcept;
    }
}
