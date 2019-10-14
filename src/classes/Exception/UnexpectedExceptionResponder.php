<?php
/*
 * This file is part of the long/dragon package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Exception;

use Psr\Http\Message\ServerRequestInterface;
use Sinpe\Framework\ArrayObject;
use Sinpe\Framework\Http\ResponderHtmlResolver;

/**
 * Responder for 400.
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
     * Format the data for resolver.
     *
     * @return ArrayObject
     */
    protected function fmtData(): ArrayObject
    {
        $except = $this->getData('thrown');

        $fmt = [
            'code' => $except->getCode(),
            'message' => $except->getMessage()
        ];

        $data = $except->getContext();

        if (!empty($data)) {
            $fmt['data'] = $data;
        }

        return new ArrayObject($fmt);
    }
}
