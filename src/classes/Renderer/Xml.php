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
use Sinpe\Framework\RendererInterface;

/**
 * Xml renderer for common.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class Xml implements RendererInterface
{
    /**
     * Process a handler output and return the result.
     *
     * @return string
     */
    public function process(DataObject $output)
    {
        return ArrayToXml::convert($output->toArray());
    }

}
