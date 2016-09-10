<?php

namespace Saxulum\Tests\HttpMessage;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Saxulum\HttpMessage\Response;

/**
 * @covers Saxulum\HttpMessage\Response
 */
class ResponseTest extends TestCase
{
    public function testWithStatus()
    {
        $response = (new Response())->withStatus(200);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('', $response->getReasonPhrase());
        self::assertSame('', $response->getProtocolVersion());
        self::assertSame([], $response->getHeaders());
        self::assertSame($this->getStream(), $response->getBody());
    }

    /**
     * @return StreamInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getStream()
    {
        return $this
            ->getMockBuilder(StreamInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMockForAbstractClass()
        ;
    }
}
