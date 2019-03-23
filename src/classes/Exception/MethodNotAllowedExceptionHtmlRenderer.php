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

use Psr\Http\Message\ResponseInterface;
use Sinpe\Framework\RendererInterface;

/**
 * The HTML renderer for method not allowed exception.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class MethodNotAllowedExceptionHtmlRenderer implements RendererInterface
{
    /**
     * Process a handler output and return the result.
     *
     * @return string
     */
    public function process(\ArrayObject $output) 
    {
        $allowed = implode(', ', $output->data->allowed);

        return <<<END
<html>
    <head>
        <title>{$output->message}</title>
        <style>
            body{
                margin:0;
                padding:30px;
                font:12px/1.5 Helvetica,Arial,Verdana,sans-serif;
            }
            h1{
                margin:0;
                font-size:48px;
                font-weight:normal;
                line-height:48px;
            }
        </style>
    </head>
    <body>
        <h1>{$output->message}</h1>
        <p>Method not allowed. Must be one of: <strong>{$allowed}</strong></p>
    </body>
</html>
END;

    }

}
