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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 404.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class PageNotFound extends BadRequest
{
    /**
     * __construct
     */
    public function __construct() 
    {        
        parent::__construct('Page not found', -404);
    }

}
