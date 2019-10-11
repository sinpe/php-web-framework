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
class Controller extends Http\Responder
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
     * 页面输出
     *
     * @param  ResponseInterface      $response The most recent Response object
     * @param  string                 $content 输出页面
     *
     * @return ResponseInterface
     */
    protected function display(
        ResponseInterface $response,
        string $content
    ) {
        $this->data = $content;
        $response = $response->withHeader('Content-Type', 'text/html');
        return $this->handle($response);
    }

    /**
     * 输出数据（非页面输出）
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

        return $this->handle($response);
    }

    /**
     * 状态输出（中性，非页面输出）
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

        return $this->handle($response);
    }

    /**
     * 输出成功（非页面输出）
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
            throw new \RuntimeException(i18n('normal code must be greater than or equal to 0'));
        }

        return $this->message($response, $message, $code, $data);
    }

    /**
     * success别名
     */
    protected function succee(
        ResponseInterface $response,
        string $message,
        $code = 0,
        $data = null
    ) {
        return $this->success($response, $message, $code, $data);
    }

    /**
     * 输出失败（非页面输出）
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
            throw new \RuntimeException(i18n('error code must be less than 0'));
        }

        return $this->message($response, $message, $code, $data);
    }

    /**
     * error别名
     */
    protected function fail(
        ResponseInterface $response,
        string $message,
        $code = -1,
        $data = null
    ) {
        return $this->error($response, $message, $code, $data);
    }

    /**
     * Format the variable will be output.
     *
     * @return mixed
     */
    protected function fmtOutput()
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
