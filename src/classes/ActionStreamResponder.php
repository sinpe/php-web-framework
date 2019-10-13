<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework;

/**
 * The throwable handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class ActionStreamResponder extends Http\Responder
{
    /**
     * 生成Response
     *
     * @return ResponseInterface
     */
    protected function genResponse(string $acceptType = null): ResponseInterface
    {
        $file = $this->getData('file');
        $fileExists = $this->getData('file_exists');

        if (!call_user_func($fileExists, $file)) {
            throw new Exception\PageNotFoundException([
                'home' => (string) $this->getRequest()->getUri()->withPath('')->withQuery('')->withFragment('')
            ]);
        }
        // 
        $size = filesize($file);
        $baseName = basename($file);

        $response = new Response($this->getRequest());

        $contentType = $acceptType ?: 'application/octet-stream';

        $response = $response->withHeader('Content-Type', "{$contentType};charset=utf-8")
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Accept-Ranges', 'bytes')
            ->withHeader('Content-Length', $size)
            ->withHeader('Content-Disposition', 'attachment; filename=' . $baseName);

        return $response->withBody(new Body(fopen($file, 'r+')));
    }
}
