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
     * @param string $uri
     * @param array $getters
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
                    '__toString' => 'http://user:password@hostname:8080/path?arg=value#anchor'
                ]
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
                    '__toString' => 'http://user@hostname:8080/path?arg=value#anchor'
                ]
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
                    '__toString' => 'http://hostname:8080/path?arg=value#anchor'
                ]
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
                    '__toString' => 'http://hostname/path?arg=value#anchor'
                ]
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
                    '__toString' => 'https://hostname/path?arg=value#anchor'
                ]
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
                    '__toString' => '//hostname/path?arg=value#anchor'
                ]
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
                    '__toString' => 'http://hostname:443'
                ]
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
                    '__toString' => 'otherschema://hostname:443'
                ]
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
                    '__toString' => '/path?arg=value#anchor'
                ]
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
        $uri = new Uri;
        $newUri = $uri->withScheme('HTTPS');

        self::assertSame('https', $newUri->getScheme());
        self::assertSame('', $newUri->getAuthority());
        self::assertSame('', $newUri->getUserInfo());
        self::assertSame('', $newUri->getHost());
        self::assertSame(null, $newUri->getPort());
        self::assertSame('', $newUri->getPath());
        self::assertSame('', $newUri->getQuery());
        self::assertSame('', $newUri->getFragment());

        self::assertSame('https:', (string) $newUri);

        self::assertSame($uri, $newUri->getPrevious());
    }

    public function testWithUserInfo()
    {
        $uri = new Uri;
        $newUri = $uri->withUserInfo('user', 'password');

        self::assertSame('', $newUri->getScheme());
        self::assertSame('', $newUri->getAuthority());
        self::assertSame('user:password', $newUri->getUserInfo());
        self::assertSame('', $newUri->getHost());
        self::assertSame(null, $newUri->getPort());
        self::assertSame('', $newUri->getPath());
        self::assertSame('', $newUri->getQuery());
        self::assertSame('', $newUri->getFragment());

        self::assertSame('', (string) $newUri);

        self::assertSame($uri, $newUri->getPrevious());
    }

    public function testWithHost()
    {
        $uri = new Uri;
        $newUri = $uri->withHost('HOSTNAME');

        self::assertSame('', $newUri->getScheme());
        self::assertSame('hostname', $newUri->getAuthority());
        self::assertSame('', $newUri->getUserInfo());
        self::assertSame('hostname', $newUri->getHost());
        self::assertSame(null, $newUri->getPort());
        self::assertSame('', $newUri->getPath());
        self::assertSame('', $newUri->getQuery());
        self::assertSame('', $newUri->getFragment());

        self::assertSame('//hostname', (string) $newUri);

        self::assertSame($uri, $newUri->getPrevious());
    }

    public function testWithPort()
    {
        $uri = new Uri;
        $newUri = $uri->withPort(8080);

        self::assertSame('', $newUri->getScheme());
        self::assertSame('', $newUri->getAuthority());
        self::assertSame('', $newUri->getUserInfo());
        self::assertSame('', $newUri->getHost());
        self::assertSame(8080, $newUri->getPort());
        self::assertSame('', $newUri->getPath());
        self::assertSame('', $newUri->getQuery());
        self::assertSame('', $newUri->getFragment());

        self::assertSame('', (string) $newUri);

        self::assertSame($uri, $newUri->getPrevious());
    }

    public function testWithPath()
    {
        $uri = new Uri;
        $newUri = $uri->withPath('/path');

        self::assertSame('', $newUri->getScheme());
        self::assertSame('', $newUri->getAuthority());
        self::assertSame('', $newUri->getUserInfo());
        self::assertSame('', $newUri->getHost());
        self::assertSame(null, $newUri->getPort());
        self::assertSame('/path', $newUri->getPath());
        self::assertSame('', $newUri->getQuery());
        self::assertSame('', $newUri->getFragment());

        self::assertSame('/path', (string) $newUri);

        self::assertSame($uri, $newUri->getPrevious());
    }

    public function testWithQuery()
    {
        $uri = new Uri;
        $newUri = $uri->withQuery('arg=value');

        self::assertSame('', $newUri->getScheme());
        self::assertSame('', $newUri->getAuthority());
        self::assertSame('', $newUri->getUserInfo());
        self::assertSame('', $newUri->getHost());
        self::assertSame(null, $newUri->getPort());
        self::assertSame('', $newUri->getPath());
        self::assertSame('arg=value', $newUri->getQuery());
        self::assertSame('', $newUri->getFragment());

        self::assertSame('?arg=value', (string) $newUri);

        self::assertSame($uri, $newUri->getPrevious());
    }

    public function testWithFragment()
    {
        $uri = new Uri;
        $newUri = $uri->withFragment('anchor');

        self::assertSame('', $newUri->getScheme());
        self::assertSame('', $newUri->getAuthority());
        self::assertSame('', $newUri->getUserInfo());
        self::assertSame('', $newUri->getHost());
        self::assertSame(null, $newUri->getPort());
        self::assertSame('', $newUri->getPath());
        self::assertSame('', $newUri->getQuery());
        self::assertSame('anchor', $newUri->getFragment());

        self::assertSame('#anchor', (string) $newUri);

        self::assertSame($uri, $newUri->getPrevious());
    }

    public function testWithAllWithMethod()
    {
        $uri = new Uri;

        $newUri = $uri
            ->withScheme('https')
            ->withUserInfo('user', 'password')
            ->withHost('hostname')
            ->withPort(8080)
            ->withPath('path')
            ->withQuery('arg=value')
            ->withFragment('anchor')
        ;

        self::assertSame('https', $newUri->getScheme());
        self::assertSame('user:password@hostname:8080', $newUri->getAuthority());
        self::assertSame('user:password', $newUri->getUserInfo());
        self::assertSame('hostname', $newUri->getHost());
        self::assertSame(8080, $newUri->getPort());
        self::assertSame('path', $newUri->getPath());
        self::assertSame('arg=value', $newUri->getQuery());
        self::assertSame('anchor', $newUri->getFragment());

        self::assertSame('https://user:password@hostname:8080/path?arg=value#anchor', (string) $newUri);
    }
}
