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

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sinpe\Framework\Exception\Exception as FrameworkException;
use Sinpe\Framework\Exception\Message as FrameworkMessage;
use Sinpe\Framework\Http\Response;
use Sinpe\Framework\Http\Uri;
use Sinpe\Framework\Http\Headers;
use Sinpe\Framework\Http\Body;
use Sinpe\Framework\Http\Request;
use Sinpe\Framework\Http\EnvironmentInterface;
use Sinpe\Framework\SettingInterface;
use Sinpe\Route\RouteInterface;

/**
 * This is the primary class with which you instantiate,
 * configure, and run a Sinpe Framework application.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class Application
{
    /**
     * @var array
     */
    protected $middlewares = [];

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
     * __construct
     *
     * @param EnvironmentInterface $environment
     * 
     * @throws \InvalidArgumentException when no container is provided that implements ContainerInterface
     */
    final public function __construct(EnvironmentInterface $environment)
    {
        $container = $this->generateContainer();

        // set_exception_handler(
        //     function ($e) use ($request) {
        //         $response = $this->handleThrowable($e, $request);
        //         $this->end($response);
        //     }
        // );

        $this->environment = $environment;
        $this->container = $container;

        // 生命周期函数__init
        $this->__init();
    }

    /**
     * 启动
     *
     * @return void
     */
    final public function bootstrap()
    {
        // 生命周期函数__bootstrap
        $this->__bootstrap();
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
    { }

    /**
     * 注册路由
     *
     * @return void
     */
    protected function registerRoutes()
    { }

    /**
     * create container
     * 
     * 需要替换默认的container，覆盖此方法
     *
     * @return ContainerInterface
     */
    protected function generateContainer(): ContainerInterface
    {
        class_alias(Container::class, 'C');

        return new Container();
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
     * @return RouteGroupInterface
     */
    public function group($pattern, $callable)
    {
        $router = $this->container->get('router');

        $group = $router->pushGroup($pattern, $callable);

        $group->run();

        $router->popGroup();

        return $group;
    }

    /**
     * 中间件
     */
    public function middleware($middleware)
    {
        $this->middlewares[] = $middleware;
        return $this;
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
     * @throws PageNotFound
     */
    public function run($silent = false)
    {
        $request = Request::createFromEnvironment($this->environment);

        try {
            ob_start();
            // Traverse middleware stack
            try {
                $handler = new ApplicationHandler($this->container->get('router'));

                $handler->middlewares(array_reverse($this->middlewares));
                // normal
                $response = $handler->handle($request);
            } catch (\Throwable $e) {
                // error
                $response = $this->handleThrowable($e, $request);
            }
        } finally {
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
                    header(sprintf('%s: %s', $name, $value), false);
                }
            }

            // Status
            header(
                sprintf(
                    'HTTP/%s %s %s',
                    $response->getProtocolVersion(),
                    $response->getStatusCode(),
                    $response->getReasonPhrase()
                )
            );
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
     * @return ResponseInterface
     */
    public function subRequest(
        $method,
        $path,
        $query = '',
        array $headers = [],
        array $cookies = [],
        $bodyContent = ''
    ) {
        $env = $this->environment;
        $uri = Uri::createFromEnvironment($env)->withPath($path)->withQuery($query);
        $headers = new Headers($headers);
        $serverParams = $env->all();
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($bodyContent);
        $body->rewind();
        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body);

        return $this->handle($request);
    }

    /**
     * Finalize response
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function finalize(ResponseInterface $response)
    {
        $headers = Headers::createFromEnvironment($this->environment);

        foreach ($headers->all() as $key => $value) {
            if (!$response->hasHeader($key)) {
                $response = $response->withHeader($key, $value);
            }
        }

        // stop PHP sending a Content-Type automatically
        ini_set('default_mimetype', '');

        if ($this->isEmptyResponse($response)) {
            return $response->withoutHeader('Content-Type')->withoutHeader('Content-Length');
        }

        $setting = $this->container->get(SettingInterface::class);

        // Add Content-Length header if `addContentLengthHeader` setting is set
        if ($setting->addContentLengthHeader == true) {

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
     *
     * @return ResponseInterface
     * @throws Exception if a handler is needed and not found
     */
    protected function handleThrowable(
        \Throwable $ex,
        ServerRequestInterface $request
    ) {
        $setting = $this->container->get(SettingInterface::class);

        $handler = null;

        if ($ex instanceof FrameworkException || $ex instanceof FrameworkMessage) {
            if (!array_key_exists(get_class($ex), $setting->throwableHandlers)) {
                $handlerClass = $ex->getHandler();
                if (class_exists($handlerClass)) {
                    $handler = $this->container->make($handlerClass);
                }
            }
        }

        if (!$handler) {
            foreach ($setting->throwableHandlers as $targetClass => $handlerClass) {
                // 
                if ($ex instanceof $targetClass) {
                    $handler = $this->container->make($handlerClass);
                    $handler->setThrowable($ex);
                }
            }
        }

        if ($handler) {
            try {
                return $handler->handle($request);
            } catch (\Exception $ex) {
                $this->handleThrowable($ex, $request);
            }
        }

        // No handlers found, so just throw the exception
        throw $ex;
    }
}
