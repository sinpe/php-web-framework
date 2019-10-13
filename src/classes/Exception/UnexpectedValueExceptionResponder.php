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

use Sinpe\Framework\ArrayObject;

/**
 * Exception handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class UnexpectedValueExceptionResponder extends UnexpectedExceptionResponder
{
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
            'message' => $except->getMessage()
        ];

        $field = $except->getField();

        if (!empty($field)) {
            $error['field'] = $field;
        }

        $data = $except->getContext();

        if (!empty($data)) {
            $error['data'] = $data;
        }

        return new ArrayObject($error);
    }
}
