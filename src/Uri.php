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
    private $previous;

    /**
     * @param string|null       $scheme
     * @param string|null       $host
     * @param int|null          $port
     * @param string|null       $user
     * @param string|null       $password
     * @param string|null       $path
     * @param string|null       $query
     * @param string|null       $fragment
     * @param UriInterface|null $previous
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
        UriInterface $previous = null
    ) {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
        $this->previous = $previous;
    }

    /**
     * @param string            $uri
     * @param UriInterface|null $previous
     *
     * @return UriInterface
     *
     * @throws \InvalidArgumentException
     */
    public static function create(string $uri, UriInterface $previous = null)
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
            $uriParts['fragment'] ?? null,
            $previous
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(): string
    {
        if (null === $this->scheme) {
            return '';
        }

        return strtolower($this->scheme);
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
        if (null === $this->host) {
            return '';
        }

        return strtolower($this->host);
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
        if (null === $this->path) {
            return '';
        }

        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery(): string
    {
        if (null === $this->query) {
            return '';
        }

        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment(): string
    {
        if (null === $this->fragment) {
            return '';
        }

        return $this->fragment;
    }

    /**
     * @return UriInterface|null
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme($scheme): self
    {
        return new self(
            $scheme ?? null,
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->path,
            $this->query,
            $this->fragment,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null): self
    {
        return new self(
            $this->scheme,
            $this->host,
            $this->port,
            $user ?? null,
            $password ?? null,
            $this->path,
            $this->query,
            $this->fragment,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($host): self
    {
        return new self(
            $this->scheme,
            $host ?? null,
            $this->port,
            $this->user,
            $this->password,
            $this->path,
            $this->query,
            $this->fragment,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port): self
    {
        return new self(
            $this->scheme,
            $this->host,
            $port ?? null,
            $this->user,
            $this->password,
            $this->path,
            $this->query,
            $this->fragment,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($path): self
    {
        return new self(
            $this->scheme,
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $path ?? null,
            $this->query,
            $this->fragment,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withQuery($query): self
    {
        return new self(
            $this->scheme,
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->path,
            $query ?? null,
            $this->fragment,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withFragment($fragment): self
    {
        return new self(
            $this->scheme,
            $this->host,
            $this->port,
            $this->user,
            $this->password,
            $this->path,
            $this->query,
            $fragment ?? null,
            $this
        );
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
}
