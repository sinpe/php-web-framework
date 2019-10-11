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

use Sinpe\Framework\Http\Response;
use Sinpe\Framework\Http\RequestHandler;

require_once __DIR__ . '/../defines.php';
require_once __DIR__ . '/../helpers.php';

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
        $this->environment = $environment;

        set_exception_handler(function ($except) {
            $responder = new Exception\InternalErrorResponder($except);
            $request = Http\Request::createFromEnvironment($this->environment);
            $response = $responder->handle(new Response($request));
            $response->flush();
        });

        // container instance
        $container = container();
        // 
        if (!$container instanceof ContainerInterface) {
            throw new \Exception(i18n('container() return Must be %s', ContainerInterface::class));
        }

        // config instance
        $config = $this->configFactory();
        if (!$config || !method_exists($config, 'get')) {
            throw new \Exception(i18n(
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
        throw new \Exception(i18n('%s needs to be overrided', __METHOD__));
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

        if (container(EventDispatcherInterface::class, true)) {
            $request = container(EventDispatcherInterface::class)
                ->dispatch(new Event\AppRunBegin($request))->getRequest();
        }

        if (APP_DEBUG) {
            ob_start();
        }
        // Traverse middleware stack
        try {
            $requestHandler = new RequestHandler(container('router'));
            //
            $requestHandler->manyUse(array_reverse($this->middlewares));
            // if exception thrown, response should be loss.
            $response = $requestHandler->handle($request);
        } catch (\Exception $except) {

            $exceptions = config('exceptions');

            if ($except instanceof Exception\InternalException) {
                // use default handler
                if (!array_key_exists(get_class($except), $exceptions)) {
                    $responder = $except->getResponder();
                }
            }

            if (!isset($responder)) {
                foreach ($exceptions as $targetClass => $handlerClass) {
                    if ($targetClass == get_class($except) || $except instanceof $targetClass) {
                        $responder = new $handlerClass($except);
                    }
                }
            }

            if (isset($responder)) {
                $response = $responder->handle(new Response($request));
            } else {
                $responder = new Exception\InternalExceptionResponder($except);
                $response = $responder->handle(new Response($request));
            }
        } catch (\Throwable $except) {
            $responder = new Exception\InternalErrorResponder($except);
            $response = $responder->handle(new Response($request));
        }

        if (APP_DEBUG) {
            $output = ob_get_clean();
        }

        if (container(EventDispatcherInterface::class, true)) {
            $response = container(EventDispatcherInterface::class)
                ->dispatch(new Event\AppRunEnd($response))->getResponse();
        }

        if (APP_DEBUG && !empty($output) && $response->getBody()->isWritable()) {
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

        if ($request->isOptions()) {
            $response = $response->withStatus(200)->withHeader('Content-Type', 'text/plain');
        }

        if (!$silent) {
            $response->flush();
        }

        return $response;
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

        if ($response->isEmpty() && !$response->getRequest()->isHead()) {
            return $response->withoutHeader('Content-Type')->withoutHeader('Content-Length');
        }

        if (ob_get_length() > 0) {
            throw new \RuntimeException(i18n("unexpected data in output buffer. " .
                "Maybe you have characters before an opening <?php tag?"));
        }

        $size = $response->getBody()->getSize();

        if ($size !== null && !$response->hasHeader('Content-Length')) {
            $response = $response->withHeader('Content-Length', (string) $size);
        }

        // clear the body if this is a HEAD request
        if ($response->getRequest()->isHead()) {
            return $response->withBody(new Http\Body(fopen('php://temp', 'r+')));
        }

        return $response;
    }
}
