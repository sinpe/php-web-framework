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
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use Sinpe\Event\EventDispatcher;
use Sinpe\Framework\Http\EnvironmentInterface;
use Sinpe\Framework\Http\RequestHandler;
use Sinpe\Route\RouteInterface;

/**
 * This is the primary class with which you instantiate,
 * configure, and run a Sinpe Framework application.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class App
{
    /**
     * ContainerInterface
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * EventDispatcherInterface
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * Environment
     *
     * @var EnvironmentInterface
     */
    private $environment;

    /**
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
        $this->container = $container = $this->generateContainer();
        $this->eventDispatcher = $this->generateEventDispatcher();

        $container->setEventDispatcher($this->eventDispatcher);

        $container[SettingInterface::class] = $this->generateSetting();

        // set_exception_handler(
        //     function ($ex) use ($request) {
        //         $response = $this->($ex, $request);
        //         $this->end($response);
        //     }
        // );

        // function exception_error_handler($errno, $errstr, $errfile, $errline ) {
        //     throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        // }
        // set_error_handler("exception_error_handler");

        $this->environment = $environment;

        // 生命周期函数__init
        $this->__init();
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
     * create setting
     * 
     * 需要替换默认的setting，覆盖此方法
     *
     * @return SettingInterface
     */
    protected function generateSetting(): SettingInterface
    {
        $settings = require_once __DIR__  . '/../settings.php';

        return new Setting($settings);
    }

    /**
     * create container
     * 
     * 需要替换默认的container，覆盖此方法
     *
     * @return ContainerInterface
     */
    protected function generateContainer(): ContainerInterface
    {
        return new Container();
    }

    /**
     * Create event dispatcher
     * 
     * 需要替换默认的EventDispatcher，覆盖此方法
     *
     * @return EventDispatcherInterface
     */
    protected function generateEventDispatcher(): EventDispatcherInterface
    {
        return new EventDispatcher();
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
     * __runBefore
     *
     * @return ServerRequestInterface
     */
    protected function __runBefore(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request;
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
     * @throws MethodNotAllowedException
     * @throws PageNotFoundException
     */
    public function run($silent = false)
    {
        $request = Http\Request::createFromEnvironment($this->environment);

        $request = $this->eventDispatcher->dispatch(new Event\AppRunBefore($request))->getRequest();

        try {
            ob_start();
            // Traverse middleware stack
            try {
                $handler = new RequestHandler($this->container->get('router'));
                $handler->middlewares(array_reverse($this->middlewares));
                // if exception thrown, response should be loss.
                $response = $handler->handle($request);
            } catch (\Throwable $ex) {
                $handler = new ExceptionHandler($ex);
                $response = $handler->handle($request);
            }
        } finally {
            $output = ob_get_clean();
        }

        $response = $this->eventDispatcher->dispatch(new Event\AppRunAfter($response))->getResponse();

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

            $body = $this->__echoBefore($body);

            $offset = 0;
            $contentRange = $response->getHeaderLine('Content-Range');
            if ($contentRange) {
                if (preg_match('#(\\d+)-(\\d+)/(\\d+)#', $contentRange, $matches)) {
                    $offset = (int)$matches[1];
                }
            }
            $body->seek($offset);

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
     * before echo
     *
     * @param StreamInterface $body
     * @return StreamInterface
     */
    protected function __echoBefore(StreamInterface $body): StreamInterface
    {
        return $body;
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
        $uri = Http\Uri::createFromEnvironment($env)->withPath($path)->withQuery($query);
        $headers = new Http\Headers($headers);
        $serverParams = $env->all();
        $body = new Http\Body(fopen('php://temp', 'r+'));
        $body->write($bodyContent);
        $body->rewind();
        $request = new Http\Request($method, $uri, $headers, $cookies, $serverParams, $body);

        // return $this->handle($request); TODO
    }

    /**
     * Finalize response
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function finalize(ResponseInterface $response)
    {
        $headers = Http\Headers::createFromEnvironment($this->environment);

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
}
