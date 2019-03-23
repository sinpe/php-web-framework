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
use Sinpe\Framework\Exception\ServerException;
use Sinpe\Framework\Exception\ServerExceptionHandler;
use Sinpe\Framework\Exception\Message;
use Sinpe\Framework\Exception\MessageHandler;

if (php_sapi_name() == 'cli') {
    // 
    return [];
} else {
    return [
        // 'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'displayErrorDetails' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => false,
        'throwableHandlers' => [
            BadRequestException::class => BadRequestExceptionHandler::class,
            Message::class => MessageHandler::class,
            \Exception::class => ServerExceptionHandler::class,
            \Error::class => ServerExceptionHandler::class
        ]
    ];
}
