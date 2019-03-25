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

use Sinpe\Framework\ArrayObject;
use Sinpe\Framework\RendererInterface;

/**
 * Html
 */
class HtmlRenderer implements RendererInterface
{
    /**
     * Render HTML not allowed message
     *
     * @return string
     */
    public function process(ArrayObject $output)
    {
        $html = '';

        if ($output->has('message')) {

            $html .= '<h1>'.$output->message."({$output->code})</h1>";

            if ($output->has('data')) {
                $html .= '<p>'.is_string($output->data) ? $output->data : $this->serialize($output->data).'</p>';
            }
        } else {
            if ($output->has('data')) {
                $html .= is_string($output->data) ? $output->data : $this->serialize($output->data);
            }
        }

        return $html;
    }

    /**
     * 序列化
     *
     * @param [type] $data
     * @return void
     */
    protected function serialize($data)
    {
        return json_encode($data);
    }

}
