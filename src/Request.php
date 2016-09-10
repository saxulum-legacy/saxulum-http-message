<?php

namespace Saxulum\HttpMessage;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

final class Request extends AbstractMessage implements RequestInterface
{
    /**
     * @var string|null
     */
    private $requestTarget;

    /**
     * @var string|null
     */
    private $method;

    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var RequestInterface|null
     */
    protected $__previous;

    /**
     * @param UriInterface          $uri
     * @param string                $method
     * @param array                 $headers
     * @param StreamInterface|null  $body
     * @param string                $protocolVersion
     * @param string|null           $requestTarget
     * @param RequestInterface|null $__previous
     */
    public function __construct(
        UriInterface $uri,
        string $method = self::METHOD_GET,
        array $headers = [],
        StreamInterface $body = null,
        string $protocolVersion = self::PROTOCOL_VERSION_1_1,
        string $requestTarget = null,
        RequestInterface $__previous = null
    ) {
        $this->uri = $uri;
        $this->method = $method;
        $this->headers = $headers;
        $this->body = $body;
        $this->protocolVersion = $protocolVersion;
        $this->requestTarget = $requestTarget;
        $this->__previous = $__previous;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTarget()
    {
        if (null !== $this->requestTarget) {
            return $this->requestTarget;
        }

        if ('' === $requestTarget = $this->uri->getPath()) {
            $requestTarget = '/';
        }

        if ('' !== $query = $this->uri->getQuery()) {
            $requestTarget .= '?'.$query;
        }

        return $requestTarget;
    }

    /**
     * {@inheritdoc}
     */
    public function withRequestTarget($requestTarget)
    {
        return $this->with(['requestTarget' => $requestTarget]);
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return null !== $this->method ? (string) $this->method : self::METHOD_GET;
    }

    /**
     * {@inheritdoc}
     */
    public function withMethod($method)
    {
        return $this->with(['method' => $method]);
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        return $this->with(['uri' => $uri]);
    }

    /**
     * @param array $parameters
     *
     * @return Request
     */
    protected function with(array $parameters): self
    {
        $defaults = [
            'uri' => $this->uri,
            'method' => $this->method,
            'headers' => $this->headers,
            'body' => $this->body,
            'protocolVersion' => $this->protocolVersion,
            'requestTarget' => $this->requestTarget,
        ];

        $arguments = array_values(array_replace($defaults, $parameters, ['__previous' => $this]));

        return new static(...$arguments);
    }
}
