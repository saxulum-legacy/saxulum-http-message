<?php

namespace Saxulum\HttpMessage;

use Psr\Http\Message\UriInterface;

final class Uri implements UriInterface
{
    /**
     * @var string|null
     */
    private $scheme;

    /**
     * @var string|null
     */
    private $host;

    /**
     * @var int|null
     */
    private $port;

    const PORT_HTTP = 80;
    const PORT_HTTPS = 443;

    /**
     * @var string|null
     */
    private $user;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @var string|null
     */
    private $path;

    /**
     * @var string|null
     */
    private $query;

    /**
     * @var string|null
     */
    private $fragment;

    /**
     * @var UriInterface|null
     */
    private $__previous;

    /**
     * @param string|null       $scheme
     * @param string|null       $host
     * @param int|null          $port
     * @param string|null       $user
     * @param string|null       $password
     * @param string|null       $path
     * @param string|null       $query
     * @param string|null       $fragment
     * @param UriInterface|null $__previous
     */
    public function __construct(
        string $scheme = null,
        string $host = null,
        int $port = null,
        string $user = null,
        string $password = null,
        string $path = null,
        string $query = null,
        string $fragment = null,
        UriInterface $__previous = null
    ) {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
        $this->__previous = $__previous;
    }

    /**
     * @param string $uri
     *
     * @return UriInterface
     *
     * @throws \InvalidArgumentException
     */
    public static function create(string $uri)
    {
        $uriParts = parse_url($uri);
        if (false === $uriParts) {
            throw new \InvalidArgumentException(sprintf('Invalid uri format for parse_url: %s', $uri));
        }

        return new self(
            $uriParts['scheme'] ?? null,
            $uriParts['host'] ?? null,
            $uriParts['port'] ?? null,
            $uriParts['user'] ?? null,
            $uriParts['pass'] ?? null,
            $uriParts['path'] ?? null,
            $uriParts['query'] ?? null,
            $uriParts['fragment'] ?? null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(): string
    {
        return strtolower((string) $this->scheme);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority(): string
    {
        if ('' === $host = $this->getHost()) {
            return '';
        }

        $authority = '';

        if ('' !== $userInfo = $this->getUserInfo()) {
            $authority .= $userInfo.'@';
        }

        $authority .= $host;

        if (null !== $port = $this->getPort()) {
            $authority .= ':'.$port;
        }

        return $authority;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo(): string
    {
        if (null === $this->user) {
            return '';
        }

        if (null === $this->password) {
            return $this->user;
        }

        return $this->user.':'.$this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(): string
    {
        return strtolower((string) $this->host);
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
        if (null === $this->port) {
            return null;
        }

        if ($this->port === $this->getPortForScheme($this->getScheme())) {
            return null;
        }

        return $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return (string) $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery(): string
    {
        return (string) $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment(): string
    {
        return (string) $this->fragment;
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme($scheme): self
    {
        return $this->with(['scheme' => $scheme]);
    }

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null): self
    {
        return $this->with(['user' => $user, 'password' => $password]);
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($host): self
    {
        return $this->with(['host' => $host]);
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port): self
    {
        return $this->with(['port' => $port]);
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($path): self
    {
        return $this->with(['path' => $path]);
    }

    /**
     * {@inheritdoc}
     */
    public function withQuery($query): self
    {
        return $this->with(['query' => $query]);
    }

    /**
     * {@inheritdoc}
     */
    public function withFragment($fragment): self
    {
        return $this->with(['fragment' => $fragment]);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $uri = '';

        if ('' !== $scheme = $this->getScheme()) {
            $uri .= $scheme.':';
        }

        if ('' !== $authority = $this->getAuthority()) {
            $uri .= '//'.$authority;
        }

        if ('' !== $path = $this->getPath()) {
            $uri .= $this->getPathForUri($authority, $path);
        }

        if ('' !== $query = $this->getQuery()) {
            $uri .= '?'.$query;
        }

        if ('' !== $fragment = $this->getFragment()) {
            $uri .= '#'.$fragment;
        }

        return $uri;
    }

    /**
     * @param string $scheme
     *
     * @return int|null
     */
    private function getPortForScheme(string $scheme)
    {
        $constantName = 'PORT_'.strtoupper($scheme);
        $reflection = new \ReflectionObject($this);
        if ($reflection->hasConstant($constantName)) {
            return $reflection->getConstant($constantName);
        }

        return null;
    }

    /**
     * @param string $authority
     * @param string $path
     *
     * @return string
     */
    private function getPathForUri(string $authority, string $path): string
    {
        if ('' !== $authority) {
            return $this->getPathForUriWithAuthority($path);
        }

        return $this->getPathForUriWithoutAuthority($path);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getPathForUriWithAuthority(string $path)
    {
        return '/' === $path[0] ? $path : '/'.$path;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getPathForUriWithoutAuthority(string $path)
    {
        $pathLength = strlen($path);
        for ($i = 0; $i < $pathLength; ++$i) {
            if ('/' !== $path[$i]) {
                break;
            }
        }

        return 0 === $i ? $path : '/'.substr($path, $i);
    }

    /**
     * @param array $parameters
     *
     * @return Uri
     */
    public function with(array $parameters): self
    {
        $defaults = [
            'scheme' => $this->scheme,
            'host' => $this->host,
            'port' => $this->port,
            'user' => $this->user,
            'password' => $this->password,
            'path' => $this->path,
            'query' => $this->query,
            'fragment' => $this->fragment,
        ];

        $arguments = array_values(array_replace($defaults, $parameters, ['__previous' => $this]));

        return new self(...$arguments);
    }
}
