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

use Psr\Http\Message\ResponseInterface;
use Sinpe\Framework\ArrayObject;

/**
 * Exception handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class RuntimeExceptionHandler extends ExceptionHandler
{
    /**
     * var string
     */
    private $acceptType;

    /**
     * __construct
     * 
     * @param \Exception $except
     */
    public function __construct(\Exception $except)
    {
        parent::__construct($except);

        $this->registerResolvers([
            'text/html' => RuntimeExceptionHtmlResolver::class
        ]);
    }

    /**
     * Invoke the handler
     *
     * @param  ResponseInterface $response
     * @return ResponseInterface
     * @throws UnexpectedValueException
     */
    public function handle(ResponseInterface $response): ResponseInterface
    {
        $this->acceptType = $response->getHeaderLine('Content-Type');

        // Write to the error log if debug is false
        if (!config('runtime.debug')) {
            self::errorLog($this->getException());
        }

        $response = parent::handle($response);
        $response = $response->withStatus(500);
        return $response;
    }

    /**
     * Format the variable will be output.
     *
     * @return mixed
     */
    protected function fmtOutput()
    {
        $error = [
            'code' => $this->getException()->getCode(),
            'message' => 'Error'
        ];

        // 
        if (config('runtime.debug')) {

            $except = $this->getException();

            $error['type'] = get_class($except);
            $error['message'] = $this->wrapCdata($except->getMessage());
            $error['file'] = $except->getFile();
            $error['line'] = $except->getLine();
            $error['trace'] = $this->wrapCdata($except->getTraceAsString());

            while ($except = $except->getPrevious()) {
                $error['previous'][] = [
                    'type' => get_class($except),
                    'code' => $except->getCode(),
                    'message' => $this->wrapCdata($except->getMessage()),
                    'file' => $except->getFile(),
                    'line' => $except->getLine(),
                    'trace' => $this->wrapCdata($except->getTraceAsString())
                ];
            }
        }

        return new ArrayObject($error);
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
    private function wrapCdata($content)
    {
        if (in_array($this->acceptType, [
            'application/xml',
            'text/xml'
        ])) {
            return sprintf('<![CDATA[%s]]>', str_replace(']]>', ']]]]><![CDATA[>', $content));
        } else {
            return $content;
        }
    }
}
