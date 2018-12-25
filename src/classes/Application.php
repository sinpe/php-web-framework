<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework;

use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sinpe\Container\ContainerAwareTrait;
use Sinpe\Framework\Http\Response;
use Sinpe\Framework\Http\Uri;
use Sinpe\Framework\Http\Headers;
use Sinpe\Framework\Http\Body;
use Sinpe\Framework\Http\Request;
use Sinpe\Framework\Http\EnvironmentInterface;
use Sinpe\Framework\SettingInterface;
use Sinpe\Middleware\CallableDeferred;
use Sinpe\Middleware\MiddlewareAwareTrait;
use Sinpe\Route\GroupInterface;
use Sinpe\Route\RouteInterface;
use Sinpe\Route\RouterInterface;

/**
 * This is the primary class with which you instantiate,
 * configure, and run a Sinpe Framework application.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class Application
{
    use MiddlewareAwareTrait;

    /**
     * ContainerInterface
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * Environment
     *
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * Request
     *
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Response
     *
     * @var ResponseInterface
     */
    private $response;

    /**
     * __construct
     *
     * @param EnvironmentInterface $environment
     * @param ServerRequestInterface $request PSR-7 Request object
     * @param ResponseInterface $response PSR-7 Response object
     * 
     * @throws \InvalidArgumentException when no container is provided that implements ContainerInterface
     */
    final public function __construct(
        EnvironmentInterface $environment,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {

        $container = $this->generateContainer();

        // set_exception_handler(
        //     function ($e) use ($request, $response) {
        //         $response = $this->handleThrowable($e, $request, $response);
        //         $this->end($response);
        //     }
        // );

        $this->environment = $environment;
        $this->request = $request;
        $this->response = $response;
        $this->container = $container;
        // 生命周期函数__init
        $this->__init();

        $this->registerRoutes();
    }

    /**
     * Get container
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * __init
     * 
     * 需要额外的初始化，覆盖此方法
     *
     * @return void
     */
    protected function __init()
    {
    }

    /**
     * 注册路由
     *
     * @return void
     */
    protected function registerRoutes()
    {
    }

    /**
     * create container
     * 
     * 需要替换默认的container，覆盖此方法
     *
     * @return ContainerInterface
     */
    protected function generateContainer() : ContainerInterface
    {
        return new Container();
    }

    /**
     * 添加中间件，调度时间点分在application的invoke之前或之后
     *
     * @param  callable|string    $callable The callback routine
     *
     * @return static
     */
    public function before($callable)
    {
        return $this->pushToBefore(new CallableDeferred($callable, $this->container));
    }

    /**
     * 添加中间件，调度时间点分在application的invoke之前或之后
     *
     * @param  callable|string    $callable The callback routine
     * @param  boolean    $after 是否在kernel执行体之后的中间件，默认是在kernel执行体之前
     *
     * @return static
     */
    public function after($callable)
    {
        return $this->pushToAfter(new CallableDeferred($callable, $this->container));
    }

    /**
     * Add GET route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Sinpe\Route\RouteInterface
     */
    public function get($pattern, $callable)
    {
        return $this->map(['GET'], $pattern, $callable);
    }

    /**
     * Add POST route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Sinpe\Route\RouteInterface
     */
    public function post($pattern, $callable)
    {
        return $this->map(['POST'], $pattern, $callable);
    }

    /**
     * Add PUT route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Sinpe\Route\RouteInterface
     */
    public function put($pattern, $callable)
    {
        return $this->map(['PUT'], $pattern, $callable);
    }

    /**
     * Add PATCH route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Sinpe\Route\RouteInterface
     */
    public function patch($pattern, $callable)
    {
        return $this->map(['PATCH'], $pattern, $callable);
    }

    /**
     * Add DELETE route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Sinpe\Route\RouteInterface
     */
    public function delete($pattern, $callable)
    {
        return $this->map(['DELETE'], $pattern, $callable);
    }

    /**
     * Add OPTIONS route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Sinpe\Route\RouteInterface
     */
    public function options($pattern, $callable)
    {
        return $this->map(['OPTIONS'], $pattern, $callable);
    }

    /**
     * Add route for any HTTP method
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
     *
     * @return \Sinpe\Route\RouteInterface
     */
    public function any($pattern, $callable)
    {
        return $this->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $pattern, $callable);
    }

    /**
     * Add route with multiple methods
     *
     * @param  string[] $methods  Numeric array of HTTP method names
     * @param  string   $pattern  The route URI pattern
     * @param  callable|string    $callable The route callback routine
     *
     * @return RouteInterface
     */
    public function map(array $methods, $pattern, $callable)
    {
        if ($callable instanceof \Closure) {
            $callable = $callable->bindTo($this->container);
        }

        $route = $this->container->get('router')->map($methods, $pattern, $callable);

        if (is_callable([$route, 'setOutputBuffering'])) {
            $setting = $this->container->get(SettingInterface::class);
            $route->setOutputBuffering($setting->outputBuffering);
        }

        return $route;
    }

    /**
     * Add a route that sends an HTTP redirect
     *
     * @param string              $from
     * @param string|UriInterface $to
     * @param int                 $status
     *
     * @return RouteInterface
     */
    public function redirect($from, $to, $status = 302)
    {
        $handler = function ($request, ResponseInterface $response) use ($to, $status) {
            return $response->withHeader('Location', (string)$to)->withStatus($status);
        };

        return $this->get($from, $handler);
    }

    /**
     * Route Groups
     *
     * This method accepts a route pattern and a callback. All route
     * declarations in the callback will be prepended by the group(s)
     * that it is in.
     *
     * @param string   $pattern
     * @param callable $callable
     *
     * @return GroupInterface
     */
    public function group($pattern, $callable)
    {
        /** @var Route\Group $group */
        $group = $this->container->get('router')->pushGroup($pattern, $callable);

        $group();

        $this->container->get('router')->popGroup();

        return $group;
    }

    /**
     * Run application
     *
     * This method traverses the application middleware stack and then sends the
     * resultant Response object to the HTTP client.
     *
     * @param bool|false $silent
     * @return ResponseInterface
     *
     * @throws Exception
     * @throws MethodNotAllowed
     * @throws NotFound
     */
    public function run($silent = false)
    {
        $response = $this->response;

        try {
            ob_start();
            $response = $this->process($this->request, $response);
        }
        finally {
            $output = ob_get_clean();
        }

        if (!empty($output) && $response->getBody()->isWritable()) {
            $setting = $this->container->get(SettingInterface::class);
            $outputBuffering = $setting->outputBuffering;
            if ($outputBuffering === 'prepend') {
                // prepend output buffer content
                $body = new Http\Body(fopen('php://temp', 'r+'));
                $body->write($output . $response->getBody());
                $response = $response->withBody($body);
            } elseif ($outputBuffering === 'append') {
                // append output buffer content
                $response->getBody()->write($output);
            }
        }

        $response = $this->finalize($response);

        if (!$silent) {
            $this->end($response);
        }

        return $response;
    }

    /**
     * Process a request
     *
     * This method traverses the application middleware stack and then returns the
     * resultant Response object.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     *
     * @throws Exception
     * @throws MethodNotAllowed
     * @throws NotFound
     */
    public function process(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        // Ensure basePath is set
        $router = $this->container->get('router');

        if (is_callable([$request->getUri(), 'getBasePath']) && is_callable([$router, 'setBasePath'])) {
            $router->setBasePath($request->getUri()->getBasePath());
        }

        // Dispatch router (note: you won't be able to alter routes after this)
        $request = $this->dispatchRouterAndPrepareRoute($request, $router);

        // Traverse middleware stack
        try {
            $response = $this->callMiddlewareStack($request, $response);
        } catch (\Throwable $e) {
            $response = $this->handleThrowable($e, $request, $response);
        }

        return $response;
    }

    /**
     * Send the response to the client
     *
     * @param ResponseInterface $response
     */
    public function end(ResponseInterface $response)
    {
        // Send response
        if (!headers_sent()) {
            // Headers
            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(i18n('%s: %s', $name, $value), false);
                }
            }

            // Status
            header(i18n(
                'HTTP/%s %s %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        }

        // Body
        if (!$this->isEmptyResponse($response)) {
            $body = $response->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }

            $setting = $this->container->get(SettingInterface::class);
            $chunkSize = $setting->responseChunkSize;

            $contentLength = $response->getHeaderLine('Content-Length');
            if (!$contentLength) {
                $contentLength = $body->getSize();
            }

            if (isset($contentLength)) {
                $amountToRead = $contentLength;
                while ($amountToRead > 0 && !$body->eof()) {
                    $data = $body->read(min($chunkSize, $amountToRead));
                    echo $data;

                    $amountToRead -= strlen($data);

                    if (connection_status() != CONNECTION_NORMAL) {
                        break;
                    }
                }
            } else {
                while (!$body->eof()) {
                    echo $body->read($chunkSize);
                    if (connection_status() != CONNECTION_NORMAL) {
                        break;
                    }
                }
            }
        }
    }

    /**
     * Invoke application
     *
     * This method implements the middleware interface. It receives
     * Request and Response objects, and it returns a Response object
     * after compiling the routes registered in the Router and dispatching
     * the Request object to the appropriate Route callback routine.
     *
     * @param  ServerRequestInterface $request  The most recent Request object
     * @param  ResponseInterface      $response The most recent Response object
     *
     * @return ResponseInterface
     * @throws MethodNotAllowed
     * @throws NotFound
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        // Get the route info
        $routeInfo = $request->getAttribute('routeInfo');

        /** @var \Sinpe\Route\RouterInterface $router */
        $router = $this->container->get('router');

        // If router hasn't been dispatched or the URI changed then dispatch
        if (null === $routeInfo
            || ($routeInfo['request'] !== [$request->getMethod(), (string)$request->getUri()])) {
            $request = $this->dispatchRouterAndPrepareRoute($request, $router);
            $routeInfo = $request->getAttribute('routeInfo');
        }

        if ($routeInfo[0] === Dispatcher::FOUND) {
            $route = $router->lookupRoute($routeInfo[1]);
            return $route->run($request, $response);
        } elseif ($routeInfo[0] === Dispatcher::METHOD_NOT_ALLOWED) {
            throw new MethodNotAllowed(
                $routeInfo[1],
                ['request' => $request, 'response' => $response]
            );
        }

        throw new NotFound(['request' => $request, 'response' => $response]);
    }

    /**
     * Perform a sub-request from within an application route
     *
     * This method allows you to prepare and initiate a sub-request, run within
     * the context of the current request. This WILL NOT issue a remote HTTP
     * request. Instead, it will route the provided URL, method, headers,
     * cookies, body, and server variables against the set of registered
     * application routes. The result response object is returned.
     *
     * @param  string            $method      The request method (e.g., GET, POST, PUT, etc.)
     * @param  string            $path        The request URI path
     * @param  string            $query       The request URI query string
     * @param  array             $headers     The request headers (key-value array)
     * @param  array             $cookies     The request cookies (key-value array)
     * @param  string            $bodyContent The request body
     * @param  ResponseInterface $response     The response object (optional)
     * @return ResponseInterface
     */
    public function subRequest(
        $method,
        $path,
        $query = '',
        array $headers = [],
        array $cookies = [],
        $bodyContent = '',
        ResponseInterface $response = null
    ) {
        $env = $this->container->get('environment');
        $uri = Uri::createFromEnvironment($env)->withPath($path)->withQuery($query);
        $headers = new Headers($headers);
        $serverParams = $env->all();
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($bodyContent);
        $body->rewind();
        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body);

        if (!$response) {
            $response = $this->response;
        }

        return $this($request, $response);
    }

    /**
     * Dispatch the router to find the route. Prepare the route for use.
     *
     * @param ServerRequestInterface $request
     * @param RouterInterface        $router
     * @return ServerRequestInterface
     */
    protected function dispatchRouterAndPrepareRoute(
        ServerRequestInterface $request,
        RouterInterface $router
    ) {
        $routeInfo = $router->dispatch($request);

        if ($routeInfo[0] === Dispatcher::FOUND) {
            $routeArguments = [];
            foreach ($routeInfo[2] as $k => $v) {
                $routeArguments[$k] = urldecode($v);
            }

            $route = $router->lookupRoute($routeInfo[1]);
            $route->prepare($request, $routeArguments);
            // add route to the request's attributes in case a middleware or handler needs access to the route
            $request = $request->withAttribute('route', $route);
        }

        $routeInfo['request'] = [$request->getMethod(), (string)$request->getUri()];

        return $request->withAttribute('routeInfo', $routeInfo);
    }

    /**
     * Finalize response
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function finalize(ResponseInterface $response)
    {
        // stop PHP sending a Content-Type automatically
        ini_set('default_mimetype', '');

        if ($this->isEmptyResponse($response)) {
            return $response->withoutHeader('Content-Type')->withoutHeader('Content-Length');
        }

        $setting = $this->container->get(SettingInterface::class);

        // Add Content-Length header if `addContentLengthHeader` setting is set
        if (isset($setting->addContentLengthHeader) &&
            $setting->addContentLengthHeader == true) {
            if (ob_get_length() > 0) {
                throw new \RuntimeException("Unexpected data in output buffer. " .
                    "Maybe you have characters before an opening <?php tag?");
            }
            $size = $response->getBody()->getSize();
            if ($size !== null && !$response->hasHeader('Content-Length')) {
                $response = $response->withHeader('Content-Length', (string)$size);
            }
        }

        return $response;
    }

    /**
     * Helper method, which returns true if the provided response must not output a body and false
     * if the response could have a body.
     *
     * @see https://tools.ietf.org/html/rfc7231
     *
     * @param ResponseInterface $response
     * @return bool
     */
    protected function isEmptyResponse(ResponseInterface $response)
    {
        if (method_exists($response, 'isEmpty')) {
            return $response->isEmpty();
        }

        return in_array($response->getStatusCode(), [204, 205, 304]);
    }

    /**
     * Call relevant handler from the Container if needed. If it doesn't exist,
     * then just re-throw.
     *
     * @param  \Throwable $e
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface $response
     *
     * @return ResponseInterface
     * @throws Exception if a handler is needed and not found
     */
    protected function handleThrowable(
        \Throwable $ex,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $setting = $this->container->get(SettingInterface::class);

        foreach ($setting->throwableHandlers as $targetClass => $handlerClass) {
            // 
            if ($ex instanceof $targetClass) {

                $handler = $this->container->make($handlerClass);
                $handler->setThrowable($ex);

                try {
                    return $handler->handle(
                        $ex->request ? $ex->request : $request,
                        $ex->response ? $ex->response : $response
                    );
                } catch (\Exception $ex) {
                    $this->handleThrowable($ex, $request, $response);
                }
            }
        }

        // No handlers found, so just throw the exception
        throw $ex;
    }

    /**
     * __get
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (in_array($name, ['eventManager'])) {
            return $this->{$name};
        }

        throw new \RuntimeException(i18n('Property %s::%s not exist.', get_class($this), $name));
    }

}
