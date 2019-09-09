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

use Spatie\ArrayToXml\ArrayToXml;
use Sinpe\Framework\ArrayObject;

/**
 * Xml ContentType for common.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class ResponseHandlerXmlResolver implements ResponseHandlerResolverInterface
{
    /**
     * Process a handler output and return the result.
     *
     * @return string
     */
    public function resolve(ArrayObject $output)
    {
        return ArrayToXml::convert($output->toArray();
    }
}
