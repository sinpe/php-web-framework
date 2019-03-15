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
 * Server error 500.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class ServerError extends \RuntimeException
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