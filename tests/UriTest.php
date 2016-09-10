<?php

namespace Saxulum\Tests\HttpMessage;

use PHPUnit\Framework\TestCase;
use Saxulum\HttpMessage\Uri;

/**
 * @covers Saxulum\HttpMessage\Uri
 */
class UriTest extends TestCase
{
    /**
     * @dataProvider getCreateProvider
     *
     * @param string $uri
     * @param array  $getters
     */
    public function testCreate(string $uri, array $getters)
    {
        $uri = Uri::create($uri);
        foreach ($getters as $method => $returnValue) {
            self::assertSame($returnValue, $uri->$method());
        }
    }

    /**
     * @return array
     */
    public function getCreateProvider()
    {
        return [
            [
                'uri' => 'http://user:password@hostname:8080/path?arg=value#anchor',
                'getters' => [
                    'getScheme' => 'http',
                    'getAuthority' => 'user:password@hostname:8080',
                    'getUserInfo' => 'user:password',
                    'getHost' => 'hostname',
                    'getPort' => 8080,
                    'getPath' => '/path',
                    'getQuery' => 'arg=value',
                    'getFragment' => 'anchor',
                    '__toString' => 'http://user:password@hostname:8080/path?arg=value#anchor',
                ],
            ],
            [
                'uri' => 'http://user@hostname:8080/path?arg=value#anchor',
                'getters' => [
                    'getScheme' => 'http',
                    'getAuthority' => 'user@hostname:8080',
                    'getUserInfo' => 'user',
                    'getHost' => 'hostname',
                    'getPort' => 8080,
                    'getPath' => '/path',
                    'getQuery' => 'arg=value',
                    'getFragment' => 'anchor',
                    '__toString' => 'http://user@hostname:8080/path?arg=value#anchor',
                ],
            ],
            [
                'uri' => 'http://hostname:8080/path?arg=value#anchor',
                'getters' => [
                    'getScheme' => 'http',
                    'getAuthority' => 'hostname:8080',
                    'getUserInfo' => '',
                    'getHost' => 'hostname',
                    'getPort' => 8080,
                    'getPath' => '/path',
                    'getQuery' => 'arg=value',
                    'getFragment' => 'anchor',
                    '__toString' => 'http://hostname:8080/path?arg=value#anchor',
                ],
            ],
            [
                'uri' => 'http://hostname:80/path?arg=value#anchor',
                'getters' => [
                    'getScheme' => 'http',
                    'getAuthority' => 'hostname',
                    'getUserInfo' => '',
                    'getHost' => 'hostname',
                    'getPort' => null,
                    'getPath' => '/path',
                    'getQuery' => 'arg=value',
                    'getFragment' => 'anchor',
                    '__toString' => 'http://hostname/path?arg=value#anchor',
                ],
            ],
            [
                'uri' => 'https://hostname:443/path?arg=value#anchor',
                'getters' => [
                    'getScheme' => 'https',
                    'getAuthority' => 'hostname',
                    'getUserInfo' => '',
                    'getHost' => 'hostname',
                    'getPort' => null,
                    'getPath' => '/path',
                    'getQuery' => 'arg=value',
                    'getFragment' => 'anchor',
                    '__toString' => 'https://hostname/path?arg=value#anchor',
                ],
            ],
            [
                'uri' => '//hostname/path?arg=value#anchor',
                'getters' => [
                    'getScheme' => '',
                    'getAuthority' => 'hostname',
                    'getUserInfo' => '',
                    'getHost' => 'hostname',
                    'getPort' => null,
                    'getPath' => '/path',
                    'getQuery' => 'arg=value',
                    'getFragment' => 'anchor',
                    '__toString' => '//hostname/path?arg=value#anchor',
                ],
            ],
            [
                'uri' => 'http://hostname:443',
                'getters' => [
                    'getScheme' => 'http',
                    'getAuthority' => 'hostname:443',
                    'getUserInfo' => '',
                    'getHost' => 'hostname',
                    'getPort' => 443,
                    'getPath' => '',
                    'getQuery' => '',
                    'getFragment' => '',
                    '__toString' => 'http://hostname:443',
                ],
            ],
            [
                'uri' => 'otherschema://hostname:443',
                'getters' => [
                    'getScheme' => 'otherschema',
                    'getAuthority' => 'hostname:443',
                    'getUserInfo' => '',
                    'getHost' => 'hostname',
                    'getPort' => 443,
                    'getPath' => '',
                    'getQuery' => '',
                    'getFragment' => '',
                    '__toString' => 'otherschema://hostname:443',
                ],
            ],
            [
                'uri' => '/path?arg=value#anchor',
                'getters' => [
                    'getScheme' => '',
                    'getAuthority' => '',
                    'getUserInfo' => '',
                    'getHost' => '',
                    'getPort' => null,
                    'getPath' => '/path',
                    'getQuery' => 'arg=value',
                    'getFragment' => 'anchor',
                    '__toString' => '/path?arg=value#anchor',
                ],
            ],
        ];
    }

    public function testCreateWithInvalidUri()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('Invalid uri format for parse_url: ///path');

        Uri::create('///path');
    }

    public function testWithScheme()
    {
        $uri = (new Uri())->withScheme('HTTPS');

        self::assertSame('https', $uri->getScheme());
        self::assertSame('', $uri->getAuthority());
        self::assertSame('', $uri->getUserInfo());
        self::assertSame('', $uri->getHost());
        self::assertSame(null, $uri->getPort());
        self::assertSame('', $uri->getPath());
        self::assertSame('', $uri->getQuery());
        self::assertSame('', $uri->getFragment());

        self::assertSame('https:', (string) $uri);
    }

    public function testWithUserInfo()
    {
        $uri = (new Uri())->withUserInfo('user', 'password');

        self::assertSame('', $uri->getScheme());
        self::assertSame('', $uri->getAuthority());
        self::assertSame('user:password', $uri->getUserInfo());
        self::assertSame('', $uri->getHost());
        self::assertSame(null, $uri->getPort());
        self::assertSame('', $uri->getPath());
        self::assertSame('', $uri->getQuery());
        self::assertSame('', $uri->getFragment());

        self::assertSame('', (string) $uri);
    }

    public function testWithHost()
    {
        $uri = (new Uri())->withHost('HOSTNAME');

        self::assertSame('', $uri->getScheme());
        self::assertSame('hostname', $uri->getAuthority());
        self::assertSame('', $uri->getUserInfo());
        self::assertSame('hostname', $uri->getHost());
        self::assertSame(null, $uri->getPort());
        self::assertSame('', $uri->getPath());
        self::assertSame('', $uri->getQuery());
        self::assertSame('', $uri->getFragment());

        self::assertSame('//hostname', (string) $uri);
    }

    public function testWithPort()
    {
        $uri = (new Uri())->withPort(8080);

        self::assertSame('', $uri->getScheme());
        self::assertSame('', $uri->getAuthority());
        self::assertSame('', $uri->getUserInfo());
        self::assertSame('', $uri->getHost());
        self::assertSame(8080, $uri->getPort());
        self::assertSame('', $uri->getPath());
        self::assertSame('', $uri->getQuery());
        self::assertSame('', $uri->getFragment());

        self::assertSame('', (string) $uri);
    }

    public function testWithPath()
    {
        $uri = (new Uri())->withPath('/path');

        self::assertSame('', $uri->getScheme());
        self::assertSame('', $uri->getAuthority());
        self::assertSame('', $uri->getUserInfo());
        self::assertSame('', $uri->getHost());
        self::assertSame(null, $uri->getPort());
        self::assertSame('/path', $uri->getPath());
        self::assertSame('', $uri->getQuery());
        self::assertSame('', $uri->getFragment());

        self::assertSame('/path', (string) $uri);
    }

    public function testWithQuery()
    {
        $uri = (new Uri())->withQuery('arg=value');

        self::assertSame('', $uri->getScheme());
        self::assertSame('', $uri->getAuthority());
        self::assertSame('', $uri->getUserInfo());
        self::assertSame('', $uri->getHost());
        self::assertSame(null, $uri->getPort());
        self::assertSame('', $uri->getPath());
        self::assertSame('arg=value', $uri->getQuery());
        self::assertSame('', $uri->getFragment());

        self::assertSame('?arg=value', (string) $uri);
    }

    public function testWithFragment()
    {
        $uri = (new Uri())->withFragment('anchor');

        self::assertSame('', $uri->getScheme());
        self::assertSame('', $uri->getAuthority());
        self::assertSame('', $uri->getUserInfo());
        self::assertSame('', $uri->getHost());
        self::assertSame(null, $uri->getPort());
        self::assertSame('', $uri->getPath());
        self::assertSame('', $uri->getQuery());
        self::assertSame('anchor', $uri->getFragment());

        self::assertSame('#anchor', (string) $uri);
    }

    public function testWithAllWithMethod()
    {
        $uri = (new Uri())
            ->withScheme('https')
            ->withUserInfo('user', 'password')
            ->withHost('hostname')
            ->withPort(8080)
            ->withPath('path')
            ->withQuery('arg=value')
            ->withFragment('anchor')
        ;

        self::assertSame('https', $uri->getScheme());
        self::assertSame('user:password@hostname:8080', $uri->getAuthority());
        self::assertSame('user:password', $uri->getUserInfo());
        self::assertSame('hostname', $uri->getHost());
        self::assertSame(8080, $uri->getPort());
        self::assertSame('path', $uri->getPath());
        self::assertSame('arg=value', $uri->getQuery());
        self::assertSame('anchor', $uri->getFragment());

        self::assertSame('https://user:password@hostname:8080/path?arg=value#anchor', (string) $uri);
    }
}
