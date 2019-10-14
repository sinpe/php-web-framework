<?php
/*
 * This file is part of the long/dragon package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework;

use Psr\Http\Message\ResponseInterface;

/**
 * 断点续传
 */
class ActionRangeResponder extends Http\Responder
{
    /**
     * Create response
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

        $response = new Http\Response($this->getRequest());

        $httpRange = $this->getRequest()->getServerParam('HTTP_RANGE');

        if ($httpRange) {
            $response = $response->withStatus(206);
            list($name, $range) = explode('=', $httpRange);
            list($begin, $end) = explode('-', $range);
            if ($end == 0) {
                $end = $size - 1;
            }
        } else {
            $begin = 0;
            $end = $size - 1;
        }

        $contentType = $acceptType ?: 'application/octet-stream';

        $response = $response->withHeader('Content-Type', "{$contentType};charset=utf-8")
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Accept-Ranges', 'bytes')
            ->withHeader('Content-Length', ($end - $begin + 1))
            ->withHeader('Content-Range', "bytes {$begin}-{$end}/{$size}")
            ->withHeader('Content-Disposition', 'attachment; filename=' . $baseName);

        return $response->withBody(new Http\Body(fopen($file, 'r+')));
    }
}
