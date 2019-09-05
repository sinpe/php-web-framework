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
use Psr\Http\Message\ServerRequestInterface;

/**
 * Handler for 404.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class PageNotFoundExceptionHandler extends BadRequestExceptionHandler
{
    /**
     * __construct
     * 
     * @param \Exception $ex
     */
    public function __construct(\Exception $ex)
    {
        parent::__construct($ex);

        $this->registerWriters([
            static::CONTENT_TYPE_HTML => PageNotFoundExceptionHtmlFormatter::class
        ]);
    }

    /**
     * Handler procedure.
     *
     * @return string
     */
    protected function process(ResponseInterface $response): ResponseInterface
    {
        $response = $response->withStatus(404);

        return $response;
    }

    /**
     * Create the variable will be rendered.
     *
     * @return []
     */
    public function getOutput()
    {
        $ex = $this->getException();

        $error = [
            'code' => $ex->getCode(),
            'message' => $ex->getMessage(),
            'data' => $this->getContext()
        ];

        return $error;
    }
}
