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
use Sinpe\Framework\Http\ResponderHtmlResolver;

/**
 * Responder for runtime error.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class UnexpectedExceptionResponder extends InternalExceptionResponder
{
    /**
     * __construct
     * 
     * @param \Exception $except
     */
    public function __construct(\Exception $except)
    {
        parent::__construct($except);

        $this->registerResolvers([
            'text/html' => ResponderHtmlResolver::class
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
        $response = $this->_handle($response);

        return $response;
    }

    /**
     * Format the variable will be output.
     *
     * @return mixed
     */
    protected function fmtOutput()
    {
        $except = $this->getException();

        $error = [
            'code' => $except->getCode(),
            'message' => $except->getMessage()
        ];

        $data = $except->getContext();

        if (!empty($data)) {
            $error['data'] = $data;
        }

        return new ArrayObject($error);
    }
}
