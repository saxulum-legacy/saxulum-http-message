<?php

namespace Saxulum\HttpMessage;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class Request extends AbstractMessage implements RequestInterface
{
    public function getRequestTarget()
    {
        // TODO: Implement getRequestTarget() method.
    }

    public function withRequestTarget($requestTarget)
    {
        // TODO: Implement withRequestTarget() method.
    }

    public function getMethod()
    {
        // TODO: Implement getMethod() method.
    }

    public function withMethod($method)
    {
        // TODO: Implement withMethod() method.
    }

    public function getUri()
    {
        // TODO: Implement getUri() method.
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        // TODO: Implement withUri() method.
    }

    /**
     * @param array $parameters
     * @return Request
     */
    protected function with(array $parameters): static
    {
        $defaults = [
            'protocolVersion' => $this->protocolVersion,
            'headers' => $this->headers,
            'body' => $this->body
        ];

        $arguments = array_values(array_replace($defaults, $parameters, ['__previous' => $this]));

        return new static(...$arguments);
    }
}
