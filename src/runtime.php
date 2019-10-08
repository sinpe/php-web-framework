<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'response_chunk_size' => 4096,
    'output_buffering' => 'append',
    'route_cache' => function () {
        return false;
    }
];
