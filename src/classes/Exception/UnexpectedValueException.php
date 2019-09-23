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

use Sinpe\Framework\Http\ResponseHandlerInterface;

/**
 * UnexpectedValueException
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class UnexpectedValueException extends UnexpectedException
{
    /**
     * @var string
     */
    private $field;

    /**
     * __construct
     *
     * @param string $message
     * @param string $field
     * @param mixed $code
     * @param mixed $previous
     * @param array $context
     */
    public function __construct(
        string $message,
        string $field,
        $code = null,
        $previous = null,
        $context = []
    ) {
        $this->field = $field;

        parent::__construct($message, $code, $previous, $context);
    }

    /**
     * 返回异常的字段名
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return ResponseHandlerInterface
     */
    public function getResponseHandler(): ResponseHandlerInterface
    {
        return new UnexpectedValueExceptionHandler($this);
    }
}
