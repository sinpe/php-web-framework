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

use Psr\Http\Message\ResponseInterface;
use Sinpe\Framework\ArrayObject;

/**
 * Html
 */
class ResponderHtmlResolver extends ResponderResolver
{
    /**
     * Custom response
     * 
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function withResponse(ResponseInterface $response): ResponseInterface
    {
        $response = $response->withHeader('Content-Type', 'text/html');

        return $response;
    }

    /**
     * Render HTML not allowed message
     *
     * @return string
     */
    public function resolve(ArrayObject $output)
    {
        $html = '';

        if ($output->has('message')) {

            $html .= '<h1>' . $output->message . "({$output->code})</h1>";

            if ($output->has('data')) {
                $html .= '<p>' . (is_string($output->data)
                    ? $output->data
                    : '<pre>' . $this->serialize($output->data)) . '</pre>' . '</p>';
            }
        } else {
            if ($output->has('data')) {
                $html .= is_string($output->data)
                    ? $output->data
                    : '<pre>' . $this->serialize($output->data) . '</pre>';
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
