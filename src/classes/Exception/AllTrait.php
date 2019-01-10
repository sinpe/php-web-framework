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

/**
 * Exception with request response.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
trait AllTrait
{
    /**
     * A request object
     *
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * A response object to send to the HTTP client
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * __construct
     *
     * @param string $message
     * @param mixed $code
     * @param mixed $previous
     * @param array $data
     */
    public function __construct(
        string $message, 
        $code=null, 
        $previous=null
    ) {
        $this->setRequest(func_get_args());

        if (!is_int($code)) {
            if (!$code instanceof \Throwable) {
                $data = $code;
            } else {
                $previous = $code;
            }
            $code = $this->getDefaultCode();
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Set request and response
     *
     * @param [type] $args
     * @return void
     */
    protected function setRequest($args)
    {
        $r = array_pop($args);

        if ($r instanceof ServerRequestInterface) {
            $this->request = $r;
        }
    }

    /**
     * __get
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (in_array($name, ['request'])) {
            return $this->{$name};
        }

        throw new \RuntimeException(i18n('Property %s::%s not exist.', get_class($this), $name));
    }

}
