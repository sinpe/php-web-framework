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
use Psr\Http\Message\ResponseInterface;
use Sinpe\Framework\ArrayObject;

/**
 * Responder for this 400 exception.
 */
class BadRequestExceptionResponder extends UnexpectedExceptionResponder
{
    /**
     * __construct
     * 
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct($request);

        $this->subscribeResponse(function(ResponseInterface $response){
            return $response->withStatus(400);
        });
    }

    // /**
    //  * Attach "Response" somme attribute and return a "Response" copy.
    //  * 
    //  * @param ResponseInterface $response
    //  * @return ResponseInterface
    //  */
    // protected function withResponse(ResponseInterface $response): ResponseInterface
    // {
    //     return $response->withStatus(400);
    // }

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

        return new ArrayObject($fmt);
    }
}
