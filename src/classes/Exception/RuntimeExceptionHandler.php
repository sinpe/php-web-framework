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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sinpe\Framework\Http\ResponseHandler;

/**
 * Exception handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class RuntimeExceptionHandler extends ResponseHandler
{
    /**
     * @var Setting
     */
    private $setting;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * __construct
     */
    public function __construct(SettingInterface $setting)
    {
        $this->setting = $setting;
    }

    /**
     * Initliazation after construction.
     *
     * @return void
     */
    public function __init()
    {
        $this->registerRenderers([
            static::CONTENT_TYPE_HTML => RuntimeExceptionHtmlRenderer::class
        ]);
    }

    /**
     * Invoke the handler
     *
     * @param  ServerRequestInterface $request
     * 
     * @return ResponseInterface
     * @throws UnexpectedValueException
     */
    public function handle(
        \Exception $ex = null,
        ServerRequestInterface $request,
        ResponseInterface $response = null
    ): ResponseInterface {

        $this->exception = $ex;

        return parent::handle($request, $response);
    }

    /**
     * @return \Exception
     */
    protected function getException()
    {
        return $this->exception;
    }

    /**
     * Handler procedure.
     *
     * @return string
     */
    protected function process(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        // Write to the error log if debug is false
        if (!$this->setting->debug) {
            self::errorLog($this->getException());
        }

        $response = $response->withStatus(500);

        return $this->rendererProcess($request, $response);
    }

    /**
     * Create the variable will be rendered.
     *
     * @return []
     */
    protected function getRendererOutput()
    {
        $error = [
            'code' => $this->getException()->getCode(),
            'message' => 'Error'
        ];

        // 
        if ($this->setting->debug) {

            $ex = $this->getException();

            $error['type'] = get_class($ex);
            $error['message'] = self::createCdataSection($ex->getMessage());
            $error['file'] = $ex->getFile();
            $error['line'] = $ex->getLine();
            $error['trace'] = self::createCdataSection($ex->getTraceAsString());

            while ($ex = $ex->getPrevious()) {
                $error['previous'][] = [
                    'type' => get_class($ex),
                    'code' => $ex->getCode(),
                    'message' => self::createCdataSection($ex->getMessage()),
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'trace' => self::createCdataSection($ex->getTraceAsString())
                ];
            }
        }

        return $error;
    }

    /**
     * Write to the error log
     *
     * @return void
     */
    private static function errorLog($ex)
    {
        $message = 'Error:' . PHP_EOL;

        $message .= self::ex2text($ex);

        while ($ex = $ex->getPrevious()) {
            $message .= PHP_EOL . 'Previous error:' . PHP_EOL;
            $message .= self::ex2text($ex);
        }

        $message .= PHP_EOL . 'View in rendered output by enabling the "debug" setting.' . PHP_EOL;

        error_log($message);
    }

    /**
     * Render error as Text.
     *
     * @param \Throwable $ex
     *
     * @return string
     */
    private static function ex2text($ex)
    {
        $text = sprintf('Type: %s' . PHP_EOL, get_class($ex));

        if ($code = $ex->getCode()) {
            $text .= sprintf('Code: %s' . PHP_EOL, $code);
        }

        if ($message = $ex->getMessage()) {
            $text .= sprintf('Message: %s' . PHP_EOL, htmlentities($message));
        }

        if ($file = $ex->getFile()) {
            $text .= sprintf('File: %s' . PHP_EOL, $file);
        }

        if ($line = $ex->getLine()) {
            $text .= sprintf('Line: %s' . PHP_EOL, $line);
        }

        if ($trace = $ex->getTraceAsString()) {
            $text .= sprintf('Trace: %s', $trace);
        }

        return $text;
    }

    /**
     * Returns a CDATA section with the given content.
     *
     * @param  string $content
     * @return string
     */
    private static function createCdataSection($content)
    {
        if (in_array($this->getContentType(), [
            static::CONTENT_TYPE_XML1,
            static::CONTENT_TYPE_XML2
        ])) {
            return sprintf('<![CDATA[%s]]>', str_replace(']]>', ']]]]><![CDATA[>', $content));
        } else {
            return $content;
        }
    }
}