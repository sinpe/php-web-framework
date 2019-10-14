<?php
/*
 * This file is part of the long/dragon package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sinpe\Framework;

/**
 * Environment
 *
 * This class decouples the application from the global PHP environment.
 * This is particularly useful for unit testing.
 */
class Environment extends ArrayObject implements EnvironmentInterface
{
    /**
     * Sources for host, You can override by your subclass
     * @var array
     */
    protected $hostLiterals = [
        'HTTP_X_FORWARDED_HOST',
        'X-FORWARDED-HOST',
        'HTTP_X_FORWARDED_SERVER',
        'X-FORWARDED-SERVER',
        'HTTP_HOST',
        'SERVER_NAME'
    ];

    /**
     * Sources for scheme, You can override by your subclass
     * @var array
     */
    protected $schemeLiterals = [
        'HTTP_X_FORWARDED_PROTO',
        'X-FORWARDED-PROTO',
        'REQUEST_SCHEME',
    ];

    /**
     * {@inheritDoc}
     */
    public static function mock(array $mock = [])
    {
        //Validates if default protocol is HTTPS to set default port 443
        if (isset($mock['REQUEST_SCHEME']) && $mock['REQUEST_SCHEME'] === 'https') {
            $scheme = 'https';
            $port = 443;
        } else {
            $scheme = 'http';
            $port = 80;
        }

        $data = array_merge([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_METHOD' => 'GET',
            'REQUEST_SCHEME' => $scheme,
            'SCRIPT_NAME' => '',
            'REQUEST_URI' => '',
            'QUERY_STRING' => '',
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => $port,
            'HTTP_HOST' => 'localhost',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
            'HTTP_USER_AGENT' => 'Sinpe Framework',
            'REMOTE_ADDR' => '127.0.0.1',
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
        ], $mock);

        return new static($data);
    }

    /**
     * {@inheritDoc}
     */
    public function getHost(): string
    {
        $host = '127.0.0.1';

        $literals = $this->hostLiterals;

        foreach ((array) $literals as $item) {
            if ($this->has($item)) {
                $host = $this->get($item);
                break;
            }
        }

        return $host;
    }

    /**
     * {@inheritDoc}
     */
    public function getScheme(): string
    {
        $scheme = 'http';

        $literals = $this->schemeLiterals;

        foreach ((array) $literals as $item) {
            if ($this->has($item)) {
                $scheme = $this->get($item);
                break;
            }
        }

        return $scheme;
    }
}
