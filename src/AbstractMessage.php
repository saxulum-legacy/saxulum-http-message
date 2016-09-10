<?php

namespace Saxulum\HttpMessage;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

abstract class AbstractMessage implements MessageInterface
{
    /**
     * @var string|null
     */
    protected $protocolVersion;

    const PROTOCOL_VERSION_1_0 = '1.0';
    const PROTOCOL_VERSION_1_1 = '1.1';

    /**
     * @var string[]|array
     */
    protected $headers = [];

    /**
     * @var StreamInterface
     */
    protected $body;

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion(): string
    {
        return (string) $this->protocolVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version): static
    {
        return $this->with(['protocolVersion' => $version]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(): array
    {
         return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name): bool
    {
        return null !== $this->getOriginalHeaderName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name): array
    {
        if (null === $originalHeaderName = $this->getOriginalHeaderName($name)) {
            return [];
        }

        return $this->headers[$originalHeaderName];
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name): string
    {
         return implode(',', $this->getHeader($name));
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value): static
    {
        $originalHeaderName = $this->getOriginalHeaderName($name) ?? $name;

        $headers = $this->headers;
        $headers[$originalHeaderName] = $this->prepareHeader($value);

        return $this->with(['headers' => $headers]);
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value)
    {
        $originalHeaderName = $this->getOriginalHeaderName($name) ?? $name;

        $headers = $this->headers;
        $headers[$originalHeaderName] = $this->prepareHeader($value, $headers[$originalHeaderName] ?? []);

        return $this->with(['headers' => $headers]);
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name)
    {
        $headers = $this->headers;
        if (null !== $originalHeaderName = $this->getOriginalHeaderName($name)) {
            unset($headers[$originalHeaderName]);
        }

        return $this->with(['headers' => $headers]);
    }

    /**
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
         return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body)
    {
        return $this->with(['body' => $body]);
    }

    /**
     * @param array $parameters
     * @return AbstractMessage
     */
    abstract protected function with(array $parameters): static;

    /**
     * @param string $name
     * @return string|null
     */
    private function getOriginalHeaderName(string $name)
    {
        if ([] === $this->headers) {
            return null;
        }

        $lowerName = strtolower($name);
        foreach ($this->headers as $header => $values) {
            if ($lowerName === strtolower($header)) {
                return $header;
            }
        }

        return null;
    }

    /**
     * @param array|string $value
     * @param array $originalValue
     * @return array
     */
    private function prepareHeader($value, array $originalValue = []): array
    {
        return array_merge($originalValue, is_array($value) ? $value : [$value]);
    }
}
