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

/**
 * Exception handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class RuntimeExceptionHandler extends ExceptionHandler
{
    /**
     * __construct
     * 
     * @param \Exception $except
     */
    public function __construct(\Exception $except)
    {
        parent::__construct($except);

        $this->registerWriters([
            static::CONTENT_TYPE_HTML => RuntimeExceptionHtmlFormatter::class
        ]);
    }

    /**
     * Handler procedure.
     *
     * @return string
     */
    protected function process(ResponseInterface $response): ResponseInterface
    {
        // Write to the error log if debug is false
        if (!config('runtime.debug')) {
            self::errorLog($this->getException());
        }

        $response = $response->withStatus(500);

        return $response;
    }

    /**
     * Create the variable will be rendered.
     *
     * @return []
     */
    public function getOutput()
    {
        $error = [
            'code' => $this->getException()->getCode(),
            'message' => 'Error'
        ];

        // 
        if (config('runtime.debug')) {

            $except = $this->getException();

            $error['type'] = get_class($except);
            $error['message'] = self::createCdataSection($except->getMessage());
            $error['file'] = $except->getFile();
            $error['line'] = $except->getLine();
            $error['trace'] = self::createCdataSection($except->getTraceAsString());

            while ($except = $except->getPrevious()) {
                $error['previous'][] = [
                    'type' => get_class($except),
                    'code' => $except->getCode(),
                    'message' => self::createCdataSection($except->getMessage()),
                    'file' => $except->getFile(),
                    'line' => $except->getLine(),
                    'trace' => self::createCdataSection($except->getTraceAsString())
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
    private static function errorLog($except)
    {
        $message = 'Error:' . PHP_EOL;

        $message .= self::ex2text($except);

        while ($except = $except->getPrevious()) {
            $message .= PHP_EOL . 'previous error:' . PHP_EOL;
            $message .= self::ex2text($except);
        }

        $message .= PHP_EOL . 'view in rendered output by enabling the "debug" setting.' . PHP_EOL;

        error_log($message);
    }

    /**
     * Render error as Text.
     *
     * @param \Throwable $except
     *
     * @return string
     */
    private static function ex2text($except)
    {
        $text = sprintf('type: %s' . PHP_EOL, get_class($except));

        if ($code = $except->getCode()) {
            $text .= sprintf('code: %s' . PHP_EOL, $code);
        }

        if ($message = $except->getMessage()) {
            $text .= sprintf('message: %s' . PHP_EOL, htmlentities($message));
        }

        if ($file = $except->getFile()) {
            $text .= sprintf('file: %s' . PHP_EOL, $file);
        }

        if ($line = $except->getLine()) {
            $text .= sprintf('line: %s' . PHP_EOL, $line);
        }

        if ($trace = $except->getTraceAsString()) {
            $text .= sprintf('trace: %s', $trace);
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
