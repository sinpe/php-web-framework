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

use Sinpe\Framework\ArrayObject;
use Sinpe\Framework\Http\ResponseHandlerHtmlResolver;

/**
 * Html
 */
class UnexpectedValueExceptionHtmlResolver extends ResponseHandlerHtmlResolver
{
    /**
     * Render HTML not allowed message
     *
     * @return string
     */
    public function resolve(ArrayObject $output)
    {
        $html = '';

        $html .= '<h1>' . $output->message . "({$output->code})</h1>";

        if ($output->has('field')) {
            $html .= '<p>Field: ' . $output->field . '</p>';
        }

        if ($output->has('data')) {
            $html .= '<p>Context: ' . (is_string($output->data) ? $output->data : $this->serialize($output->data)) . '</p>';
        }

        return $html;
    }
}
