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
use Sinpe\Framework\DataObject;
use Sinpe\Framework\ContentHandler as Base;

/**
 * Exception handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class Handler extends Base
{
    /**
     * @var Throwable
     */
    protected $thrown;

    /**
     * Set throwable
     */
    public function setThrowable($thrown)
    {
        $this->thrown = $thrown;
    }

    /**
     * Create message
     *
     * @return array
     */
    protected function getContentOfHandler()
    {
        $error = [
            'code' => $this->thrown->getCode(),
            'message' => $this->thrown->getMessage()
        ];

        return new DataObject($error);
    }

    /**
     * Create the renderer context.
     *
     * @param ResponseInterface $response PSR-7 Response object
     * 
     * @return array
     */
    protected function getRendererContext(ResponseInterface $response)
    {
        return [
            'thrown' => $this->thrown,
            'response' => $response,
            'content' => $this->getContentOfHandler()
        ];
    }

}
