<?php
/*
 * This file is part of long/framework.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework;

use Psr\Http\Message\ResponseInterface;

/**
 * Class Controller
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class Controller
{
    /**
     * @var int
     */
    private $code = 0;

    /**
     * @var string
     */
    private $message = '';

    /**
     * @var array
     */
    private $data = [];

    /**
     * 输出
     *
     * @param  ResponseInterface      $response The most recent Response object
     * @param  mixed                  $data 输出内容
     *
     * @return ResponseInterface
     */
    protected function output(
        ResponseInterface $response,
        $data,
        $code = 0
    ) {

        $this->code = $code ?? 0;
        $this->data = $data;

        array_keys(config('runtime.writers'));

        return $response;
    }

    /**
     * 输出
     *
     * @param  ResponseInterface      $response The most recent Response object
     * @param  mixed                  $message 输出信息
     *
     * @return ResponseInterface
     */
    protected function message(
        ResponseInterface $response,
        string $message,
        $code = 0,
        $data = null
    ) {

        $this->code = $code ?? 0;
        $this->message = $message;
        $this->data = $data;

        return $response;
    }

    /**
     * 成功信息
     *
     * @param  ResponseInterface      $response The most recent Response object
     * @param  mixed                  $message 输出信息
     *
     * @return ResponseInterface
     */
    protected function success(
        ResponseInterface $response,
        string $message,
        $code = 0,
        $data = null
    ) {

        if ($code < 0) {
            throw new \RuntimeException(i18n('Code must be greater than or equal to 0'));
        }

        return $this->message($response, $message, $code, $data);
    }

    /**
     * 错误信息
     *
     * @param  ResponseInterface      $response The most recent Response object
     * @param  mixed                  $message 输出信息
     *
     * @return ResponseInterface
     */
    protected function error(
        ResponseInterface $response,
        string $message,
        $code = -1,
        $data = null
    ) {

        if ($code >= 0) {
            throw new \RuntimeException(i18n('Code must be less than 0'));
        }

        return $this->message($response, $message, $code, $data);
    }

    /**
     * 构建输出的内容
     *
     * @return void
     */
    protected function getContentOfHandler()
    {
        $content = [
            'code' => $this->code,
        ];

        if (!empty($this->message)) {
            $content['message'] = $this->message;
        }

        if (isset($this->data)) {
            $content['data'] = $this->data;
        }

        return new ArrayObject($content);
    }

    /**
     * 下载
     *
     * @param  ResponseInterface      $response The most recent Response object
     * @param  mixed                  $message 输出内容
     *
     * @return ResponseInterface
     */
    protected function download(
        ResponseInterface $response,
        string $file
    ) {

        if (!file_exists($file)) {
            return $response->withStatus(404);
        }
        // 
        $size = filesize($file);
        $baseName = basename($file);

        $response = $response->withStatus(200)
            ->withHeader('Content-type', 'application/octet-stream;charset=utf-8')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Accept-Ranges', 'bytes')
            ->withHeader('Content-Length', $size)
            ->withHeader('Content-Disposition', 'attachment; filename=' . $baseName);

        return $response->withBody(new Http\Body(fopen($file, 'r+')));
    }
}
