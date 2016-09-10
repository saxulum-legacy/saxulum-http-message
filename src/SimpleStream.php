<?php

namespace Saxulum\HttpMessage;

use Psr\Http\Message\StreamInterface;

final class SimpleStream implements StreamInterface
{
    /**
     * @var string
     */
    private $stream = '';

    /**
     * @var int
     */
    private $pointer = 0;

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->stream;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(): int
    {
        return strlen($this->stream);
    }

    /**
     * {@inheritdoc}
     */
    public function tell(): int
    {
        return $this->pointer;
    }

    /**
     * {@inheritdoc}
     */
    public function eof(): bool
    {
        return $this->pointer + 1 === $this->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (SEEK_SET === $whence) {
            $this->pointer = $offset;
        } elseif (SEEK_CUR === $whence) {
            $this->pointer += $offset;
        } elseif (SEEK_END === $whence) {
            $this->pointer = $this->getSize() - 1 + $offset;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->pointer = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string): int
    {
        $stringLength = strlen($string);
        for ($i = 0; $i < $stringLength; ++$i) {
            $this->stream[$this->pointer] = $string[$i];
            ++$this->pointer;
        }

        return $stringLength;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length): string
    {
        $read = '';
        for ($i = 0; $i < $length; ++$i) {
            $read .= $this->stream[$this->pointer + $i] ?? '';
        }

        return $read;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(): string
    {
        $contents = '';
        $size = $this->getSize();

        while ($this->pointer < $size) {
            $contents .= $this->stream[$this->pointer];
            ++$this->pointer;
        }

        return $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        $metadata = [
            'timed_out' => false,
            'blocked' => false,
            'eof' => $this->eof(),
            'unread_bytes' => $this->getSize() - $this->pointer - 1,
            'mode' => 'rw',
            'seekable' => true,
        ];

        if (null !== $key) {
            return $metadata[$key] ?? null;
        }

        return $metadata;
    }
}
