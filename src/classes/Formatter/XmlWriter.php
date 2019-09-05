<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Formatter;

use Spatie\ArrayToXml\ArrayToXml;
use Sinpe\Framework\ArrayObject;
use Sinpe\Framework\WriterFormatterInterface;

/**
 * Xml Formatter for common.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class XmlFormatter implements WriterFormatterInterface
{
    /**
     * Process a handler output and return the result.
     *
     * @return string
     */
    public function process(ArrayObject $output)
    {
        return ArrayToXml::convert($output->toArray());
    }
}
