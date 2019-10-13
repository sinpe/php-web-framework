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
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct($request);

        $this->registerResolvers([
            'text/html' => ResponderHtmlResolver::class
        ]);
    }

    /**
     * Format the variable will be output.
     *
     * @return mixed
     */
    protected function getData():ArrayObject
    {
        $except = parent::getData('except');

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
