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
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class Action
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * __construct
     * 
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * 页面输出
     *
     * @param  ResponseInterface      $response The most recent Response object
     * @param  string                 $content 输出页面
     *
     * @return ResponseInterface
     */
    protected function display(string $content)
    {
        return $this->getResponder()->display($content);
    }

    /**
     * 输出数据（非页面输出）
     *
     * @param  ResponseInterface      $response The most recent Response object
     * @param  mixed                  $data 输出内容
     *
     * @return ResponseInterface
     */
    protected function output($data, $code = 0)
    {
        return $this->getResponder()->handle([
            'code' => $code ?? 0,
            'data' => $data
        ]);
    }

    /**
     * 状态输出（中性，非页面输出）
     *
     * @param  ResponseInterface      $response The most recent Response object
     * @param  mixed                  $message 输出信息
     *
     * @return ResponseInterface
     */
    protected function message(string $message, $code = 0, $data = null)
    {
        return $this->getResponder()->handle([
            'code' => $code ?? 0,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * 输出成功（非页面输出）
     *
     * @param  ResponseInterface      $response The most recent Response object
     * @param  mixed                  $message 输出信息
     *
     * @return ResponseInterface
     */
    protected function success(string $message, $code = 0, $data = null)
    {
        if ($code < 0) {
            throw new \RuntimeException(i18n('normal code must be greater than or equal to 0'));
        }

        return $this->message($message, $code, $data);
    }

    /**
     * success别名
     */
    protected function succee(string $message, $code = 0, $data = null)
    {
        return $this->success($message, $code, $data);
    }

    /**
     * 输出失败（非页面输出）
     *
     * @param  ResponseInterface      $response The most recent Response object
     * @param  mixed                  $message 输出信息
     *
     * @return ResponseInterface
     */
    protected function error(string $message, $code = -1, $data = null)
    {
        if ($code >= 0) {
            throw new \RuntimeException(i18n('error code must be less than 0'));
        }

        return $this->message($message, $code, $data);
    }

    /**
     * error别名
     */
    protected function fail(string $message, $code = -1, $data = null)
    {
        return $this->error($message, $code, $data);
    }

    /**
     * 下载
     *
     * @param  mixed                  $message 输出内容
     *
     * @return ResponseInterface
     */
    protected function download(string $file, callable $fileExists = null)
    {
        return $this->getStreamResponder()->handle([
            'file' => $file,
            'file_exists' => is_callable($fileExists) ? $fileExists : function ($file) {
                return file_exists($file);
            }
        ], 'application/octet-stream');
    }

    /**
     * @return Http\Responder
     */
    protected function getResponder(): Http\Responder
    {
        return new ActionTextResponder($this->request);
    }

    /**
     * @return Http\Responder
     */
    protected function getStreamResponder(): Http\Responder
    {
        return new ActionStreamResponder($this->request);
    }
}
