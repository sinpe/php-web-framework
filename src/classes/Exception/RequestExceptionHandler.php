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

/**
 * Handler for runtime error.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class RequestExceptionHandler extends RuntimeExceptionHandler
{
    /**
     * Create the content will be rendered.
     *
     * @return array
     */
    protected function getRendererOutput()
    {
        $ex = $this->getException();

        $error = [
            'code' => $ex->getCode(),
            'message' => $ex->getMessage()
        ];

        $data = $ex->getData();

        if (!empty($data)) {
            $error['data'] = $data;
        }

        return $error;
    }

}
