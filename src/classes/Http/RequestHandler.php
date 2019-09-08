<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework\Http;

use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sinpe\Route\MiddlewareAwareTrait;
use Sinpe\Route\MiddlewareAwareInterface;
use Sinpe\Route\RouterInterface;
use Sinpe\Framework\Exception\MethodNotAllowedException;
use Sinpe\Framework\Exception\PageNotFoundException;
use Sinpe\Framework\Http\Response;

/**
 * Handle the request and output a response
 */
class RequestHandler implements RequestHandlerInterface, MiddlewareAwareInterface
{
    use MiddlewareAwareTrait;

    /**
     * @var Router
     */
    private $router;

    /**
     * __construct
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Dispatch route callable against current Request and Response objects
     *
     * This method invokes the route object's callable. If middleware is
     * registered for the route, each callable middleware is invoked in
     * the order specified.
     *
     * @param ServerRequestInterface $request  The current Request object
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception  if the route callable throws an exception
     */
    protected function run(ServerRequestInterface $request): ResponseInterface
    {
        // Ensure basePath is set
        $router = $this->router;

        if (is_callable([$request->getUri(), 'getBasePath']) && is_callable([$router, 'setBasePath'])) {
            $router->setBasePath($request->getUri()->getBasePath());
        }

        $routeInfo = $router->dispatch($request);

        if ($routeInfo[0] === Dispatcher::FOUND) {

            $routeArguments = [];

            foreach ($routeInfo[2] as $k => $v) {
                $routeArguments[$k] = urldecode($v);
            }

            $route = $routeInfo[1];

            // $route->prepare($request, $routeArguments);

            return $route->run(new Response($request), $routeArguments);
            //
        } elseif ($routeInfo[0] === Dispatcher::METHOD_NOT_ALLOWED) {
            throw new MethodNotAllowedException($routeInfo[1]);
        }

        throw new PageNotFoundException([
            'home' => (string) $request->getUri()->withPath('')->withQuery('')->withFragment('')
        ]);
    }
}
