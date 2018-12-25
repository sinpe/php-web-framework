<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Renderer;

use Psr\Http\Message\ResponseInterface;
use Spatie\ArrayToXml\ArrayToXml;
use Sinpe\Framework\DataObject;
use Sinpe\Framework\Renderer;

/**
 * Xml renderer for common.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class Xml extends Renderer
{
    /**
     * Process a handler context and assign result to "output" property.
     *
     * @return ResponseInterface
     */
    public function process(DataObject $context) : ResponseInterface
    {
        $this->output = ArrayToXml::convert($context->content->toArray());

        return $context->response;
    }

}
