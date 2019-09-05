<?php 
/*
 * This file is part of long/framework.
 *
 * (c) Sinpe Inc. <dev@sinpe.com>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Exception;

use Psr\Http\Server\RequestHandlerInterface;

/**
 * 401.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class UnauthorizedException extends BadRequestException
{
    /**
     * __construct
     */
    public function __construct() 
    {
        parent::__construct('Unauthorized', -401);
    }

}
