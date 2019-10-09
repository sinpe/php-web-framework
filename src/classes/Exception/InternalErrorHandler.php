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
use Sinpe\Framework\Http\ResponseHandler;

/**
 * The throwable handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class InternalErrorHandler extends ResponseHandler
{
    /**
     * @var \Throwable
     */
    private $except;

    /**
     * var string
     */
    private $acceptType;

    /**
     * __construct
     * 
     * @param \Throwable $except
     */
    public function __construct(\Throwable $except)
    {
        $this->except = $except;

        $this->registerResolvers([
            'text/html' => InternalErrorHtmlResolver::class
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
        $response = parent::handle($response);
        $response = $response->withStatus(500);
        return $response;
    }

    /**
     * Get exception
     *
     * @return \Throwable
     */
    protected function getException(): \Throwable
    {
        return $this->except;
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
        if (APP_DEBUG) {

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
