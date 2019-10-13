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
use Sinpe\Framework\ArrayObject;
use Sinpe\Framework\Http\Responder;

/**
 * The throwable handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class InternalErrorResponder extends Responder
{
    /**
     * var string
     */
    private $acceptType;

    /**
     * __construct
     * 
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct($request);

        $this->registerResolvers([
            'text/html' => InternalErrorHtmlResolver::class
        ]);
    }

    /**
     * Invoke the handler
     *
     * @param \Exception $data
     * @return ResponseInterface
     */
    public function handle(\Exception $except): ResponseInterface
    {
        return parent::handle(['except' => $except]);
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function withResponse(ResponseInterface $response): ResponseInterface
    {
        // Write to the error log if debug is false
        if (!APP_DEBUG) {
            InternalErrorLogger::write($this->getData('except'));
        }

        $this->acceptType = $response->getHeaderLine('Content-Type');

        $response = $response->withStatus(500);

        return $response;
    }

    /**
     * Format the variable will be output.
     *
     * @return mixed
     */
    protected function fmtData(): ArrayObject
    {
        $except = $this->getData('except');

        $error = [
            'code' => $except->getCode(),
            'message' => 'Error'
        ];

        // 
        if (APP_DEBUG) {
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
