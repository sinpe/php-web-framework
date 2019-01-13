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

use Sinpe\Framework\Exception\BadRequest;
use Sinpe\Framework\Exception\BadRequestHandler;
use Sinpe\Framework\Exception\Exception;
use Sinpe\Framework\Exception\ExceptionHandler;
use Sinpe\Framework\Exception\Message;
use Sinpe\Framework\Exception\MessageHandler;

/**
 * Application settings.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class Setting extends DataObject implements SettingInterface
{
    /**
     * The data
     *
     * @var array
     */
    protected $data = [
        // 'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'displayErrorDetails' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => false,
        'throwableHandlers' => [
            BadRequest::class => BadRequestHandler::class,
            Message::class => MessageHandler::class,
            \Exception::class => ExceptionHandler::class,
            \Error::class => ExceptionHandler::class
        ]
    ];
    
}
