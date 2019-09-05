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

use Sinpe\Framework\Http\NormalHandler;

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
     * __construct
     *
     * @param EnvironmentInterface $environment
     */
    final public function __construct(EnvironmentInterface $environment)
    {
        // TODO
        // set_exception_handler(function ($except) {
        //     // $response = $this->($except, $request);
        //     //$this->end($response);
        // });

        $this->environment = $environment;

        // container instance
        $container = container();
        // 
        if (!$container instanceof ContainerInterface) {
            throw new \Exception(sprintf('container() return Must be %s', ContainerInterface::class));
        }

        // config instance
        $config = $this->configFactory();
        if (!$config || !method_exists($config, 'get')) {
            throw new \Exception(sprintf(
                '%s::configFactory return Must has "get" method',
                static::class
            ));
        }
        $container->set('config', $config);
        $container->set(get_class($config), $config);

        $config->load(__DIR__ . '/../runtime.php');

        // 生命周期函数__init
        $this->__init();
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
     * Config factory
     * 
     * You MUST override this method.
     *
     * @return Object
     */
    protected function configFactory()
    {
        throw new \Exception(sprintf('%s needs to be overrided', __METHOD__));
    }

    /**
     * Add GET route
     *
     * @param  string $pattern  The route URI pattern
     * @param  callable|string  $callable The route callback routine
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
     */
    public function map(array $methods, $pattern, $callable)
    {
        if ($callable instanceof \Closure) {
            $callable = $callable->bindTo(container());
        }

        $route = container('router')->map($methods, $pattern, $callable);

        if (is_callable([$route, 'setOutputBuffering'])) {
            $route->setOutputBuffering(config('runtime.output_buffering'));
        }

        return $route;
    }

    /**
     * Add a route that sends an HTTP redirect
     *
     * @param string              $from
     * @param string|UriInterface $to
     * @param int                 $status
     */
    public function redirect($from, $to, $status = 302)
    {
        $handler = function (ServerRequestInterface $request, ResponseInterface $response) use ($to, $status) {
            return $response->withHeader('Location', (string) $to)->withStatus($status);
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
        $router = container('router');

        $group = $router->pushGroup($pattern, $callable);
        $group->run();
        $router->popGroup();

        return $group;
    }

    /**
     * 中间件
     */
    public function use($middleware)
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Determine which content type we know about is wanted using Accept header
     *
     * Note: This method is a bare-bones implementation designed specifically for
     * error handling requirements. Consider a fully-feature solution such
     * as willdurand/negotiation for any other situation.
     *
     * @param  ServerRequestInterface $request
     * @return string
     */
    protected function prepare(ServerRequestInterface $request): ServerRequestInterface
    {
        $acceptHeader = $request->getHeaderLine('Accept');

        $contentTypes = array_keys(config('runtime.writers'));

        $selectedContentTypes = array_intersect(explode(',', $acceptHeader), $contentTypes);

        if (count($selectedContentTypes)) {
            $contentType = current($selectedContentTypes);
        } else {
            // handle +json and +xml specially
            if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
                //
                $mediaType = 'application/' . $matches[1];
                if (in_array($mediaType, $contentTypes)) {
                    $contentType = $mediaType;
                }
            }
        }

        if (empty($contentType)) {
            $contentType = 'text/html';
        }

        return $request->withHeader('Accept', $contentType);
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

        $request = $this->prepare($request);

        if (container()->has(EventDispatcherInterface::class)) {
            $request = container(EventDispatcherInterface::class)
                ->dispatch(new Event\AppRunBefore($request))->getRequest();
        }

        try {
            ob_start();
            // Traverse middleware stack
            try {
                $handler = new NormalHandler(container('router'));
                //
                $handler->manyUse(array_reverse($this->middlewares));
                // if exception thrown, response should be loss.
                $response = $handler->handle($request);
            } catch (\Exception $except) {

                $handlers = config('runtime.exception_handlers');

                if ($except instanceof Exception\RuntimeException) {
                    // use default handler
                    if (!array_key_exists(get_class($except), $handlers)) {
                        $responseHandler = $except->getResponseHandler();
                    }
                }

                if (!isset($responseHandler)) {
                    foreach ($handlers as $targetClass => $handlerClass) {
                        if ($except instanceof $targetClass) {
                            $responseHandler = new $handlerClass($except);
                        }
                    }
                }

                if (isset($responseHandler)) {
                    try {
                        $response = $responseHandler->handle($request);
                    } catch (\Exception $exAgain) {
                        $responseHandler = new Exception\HandlingExceptionHandler($exAgain, $except);
                        $response = $responseHandler->handle($request);
                    } catch (\Throwable $exAgain) {
                        throw $exAgain;
                    }
                } else {
                    throw $except;
                }
            }
        } catch (\Throwable $except) {
            throw $except;
        } finally {
            $output = ob_get_clean();
        }

        if (container()->has(EventDispatcherInterface::class)) {
            $response = container(EventDispatcherInterface::class)
                ->dispatch(new Event\AppRunAfter($response))->getResponse();
        }

        if (!empty($output) && $response->getBody()->isWritable()) {
            // 
            $outputBuffering = config('runtime.output_buffering');
            //
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

        if ($request->getMethod() === 'OPTIONS') {
            $response = $response->withStatus(200)
                ->withHeader('Content-Type', 'text/plain');
        }

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
            header(sprintf(
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

            $chunkSize = config('runtime.response_chunk_size');

            $contentLength = $response->getHeaderLine('Content-Length');

            if (!$contentLength) {
                $contentLength = $body->getSize();
            }

            if (container()->has(EventDispatcherInterface::class)) {
                $body = container(EventDispatcherInterface::class)
                    ->dispatch(new Event\AppEchoBefore($body))->getBody();
            }

            $offset = 0;
            $contentRange = $response->getHeaderLine('Content-Range');
            if ($contentRange) {
                if (preg_match('#(\\d+)-(\\d+)/(\\d+)#', $contentRange, $matches)) {
                    $offset = (int) $matches[1];
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

        $handler = new NormalHandler(container('router'));
        //
        return $handler->handle($request);
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

        if (ob_get_length() > 0) {
            throw new \RuntimeException("Unexpected data in output buffer. " .
                "Maybe you have characters before an opening <?php tag?");
        }

        $size = $response->getBody()->getSize();

        if ($size !== null && !$response->hasHeader('Content-Length')) {
            $response = $response->withHeader('Content-Length', (string) $size);
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
