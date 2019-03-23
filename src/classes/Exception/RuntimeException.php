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

use Sinpe\Framework\ExceptionTrait;

/**
 * Exception with request response.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class RuntimeException extends \RuntimeException
{
    use ExceptionTrait;

    /**
     * Return default code.
     *
     * @return integer
     */
    protected function getDefaultCode()
    {
        return -500;
    }
}
