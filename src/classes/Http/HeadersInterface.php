<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Http;

/**
 * Headers Interface
 *
 * @package Sinpe\Framework
 * @since   1.0.0
 */
interface HeadersInterface
{
    public function add($key, $value);

    public function normalizeKey($key);
}
