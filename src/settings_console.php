<?php
 /*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sinpe\Framework\Exception\BadRequest;
use Sinpe\Framework\Exception\BadRequestHandler;
use Sinpe\Framework\Exception\ServerError;
use Sinpe\Framework\Exception\ServerErrorHandler;
use Sinpe\Framework\Exception\Message;
use Sinpe\Framework\Exception\MessageHandler;

return [
    // 'httpVersion' => '1.1',
    'responseChunkSize' => 4096,
    'outputBuffering' => 'append',
    'displayErrorDetails' => false,
    'addContentLengthHeader' => true,
    'routerCacheFile' => false,
    'throwableHandlers' => [
        BadRequest::class => BadRequestHandler::class,
        Message::class => MessageHandler::class,
        \Exception::class => ServerErrorHandler::class,
        \Error::class => ServerErrorHandler::class
    ]
];
