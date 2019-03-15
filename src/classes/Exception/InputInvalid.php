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
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class InputInvalid extends Message
{
    /**
     * __construct
     *
     * @param string $field
     * @param mixed $message
     * @param int $code
     */
    public function __construct(
        string $field,
        string $message,
        int $code = -1,
        $tips = null
    ) {

        $data['field'] = $field;

        if (!empty($tips)) {
            $data['tips'] = $tips;
        }

        parent::__construct(
            $message,
            $code,
            null,
            $data
        );
    }
}
