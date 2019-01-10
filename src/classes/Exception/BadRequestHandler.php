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
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handler for 400.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class BadRequestHandler extends MessageHandler
{
    /**
     * Handler procedure.
     *
     * @return string
     */
    protected function process(
        ServerRequestInterface $request, 
        ResponseInterface $response
    ) : ResponseInterface {
        
        $response = $response->withStatus(400);

        return $this->rendererProcess($request, $response);
    }

}
