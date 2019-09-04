<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sinpe\Framework\Exception\BadRequestException;
use Sinpe\Framework\Exception\BadRequestExceptionHandler;
use Sinpe\Framework\Exception\RuntimeExceptionHandler;
use Sinpe\Framework\Exception\RequestException;
use Sinpe\Framework\Exception\RequestExceptionHandler;

return [
    'http_version' => '1.1',
    'host_readers' => [
        'X-FORWARDED-HOST',
        'X-FORWARDED-SERVER',
        'HTTP_HOST',
        'SERVER_NAME'
    ],
    'response_chunk_size' => 4096,
    'output_buffering' => 'append',
    'debug' => false,
    'route_cache' => function () {
        return false;
    },
    'exception_handlers' => [
        BadRequestException::class => BadRequestExceptionHandler::class,
        RequestException::class => RequestExceptionHandler::class,
        \Exception::class => RuntimeExceptionHandler::class,
        \Error::class => RuntimeExceptionHandler::class
    ]
];
