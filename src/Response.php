<?php

namespace Saxulum\HttpMessage;

use Psr\Http\Message\ResponseInterface;

final class Response extends AbstractMessage implements ResponseInterface
{
    /**
     * @var int|null
     */
    private $statusCode;

    /**
     * @var string|null
     */
    private $reasonPhrase;

    const REASON_PHRASE_100 = 'Continue';
    const REASON_PHRASE_101 = 'Switching Protocols';
    const REASON_PHRASE_102 = 'Processing';
    const REASON_PHRASE_200 = 'OK';
    const REASON_PHRASE_201 = 'Created';
    const REASON_PHRASE_202 = 'Accepted';
    const REASON_PHRASE_203 = 'Non-Authoritative Information';
    const REASON_PHRASE_204 = 'No Content';
    const REASON_PHRASE_205 = 'Reset Content';
    const REASON_PHRASE_206 = 'Partial Content';
    const REASON_PHRASE_207 = 'Multi-Status';
    const REASON_PHRASE_208 = 'Already Reported';
    const REASON_PHRASE_226 = 'IM Used';
    const REASON_PHRASE_300 = 'Multiple Choices';
    const REASON_PHRASE_301 = 'Moved Permanently';
    const REASON_PHRASE_302 = 'Found';
    const REASON_PHRASE_303 = 'See Other';
    const REASON_PHRASE_304 = 'Not Modified';
    const REASON_PHRASE_305 = 'Use Proxy';
    const REASON_PHRASE_306 = '(reserved)';
    const REASON_PHRASE_307 = 'Temporary Redirect';
    const REASON_PHRASE_308 = 'Permanent Redirect';
    const REASON_PHRASE_400 = 'Bad Request';
    const REASON_PHRASE_401 = 'Unauthorized';
    const REASON_PHRASE_402 = 'Payment Required';
    const REASON_PHRASE_403 = 'Forbidden';
    const REASON_PHRASE_404 = 'Not Found';
    const REASON_PHRASE_405 = 'Method Not Allowed';
    const REASON_PHRASE_406 = 'Not Acceptable';
    const REASON_PHRASE_407 = 'Proxy Authentication Required';
    const REASON_PHRASE_408 = 'Request Time-out';
    const REASON_PHRASE_409 = 'Conflict';
    const REASON_PHRASE_410 = 'Gone';
    const REASON_PHRASE_411 = 'Length Required';
    const REASON_PHRASE_412 = 'Precondition Failed';
    const REASON_PHRASE_413 = 'Request Entity Too Large';
    const REASON_PHRASE_414 = 'Request-URL Too Long';
    const REASON_PHRASE_415 = 'Unsupported Media Type';
    const REASON_PHRASE_416 = 'Requested range not satisfiable';
    const REASON_PHRASE_417 = 'Expectation Failed';
    const REASON_PHRASE_418 = 'I’m a teapot';
    const REASON_PHRASE_420 = 'Policy Not Fulfilled';
    const REASON_PHRASE_421 = 'There are too many connections from your internet address';
    const REASON_PHRASE_422 = 'Unprocessable Entity';
    const REASON_PHRASE_423 = 'Locked';
    const REASON_PHRASE_424 = 'Failed Dependency';
    const REASON_PHRASE_425 = 'Unordered Collection';
    const REASON_PHRASE_426 = 'Upgrade Required';
    const REASON_PHRASE_428 = 'Precondition Required';
    const REASON_PHRASE_429 = 'Too Many Requests';
    const REASON_PHRASE_431 = 'Request Header Fields Too Large';
    const REASON_PHRASE_444 = 'No Response';
    const REASON_PHRASE_449 = 'The request should be retried after doing the appropriate action';
    const REASON_PHRASE_451 = 'Unavailable For Legal Reasons';
    const REASON_PHRASE_500 = 'Internal Server Error';
    const REASON_PHRASE_501 = 'Not Implemented';
    const REASON_PHRASE_502 = 'Bad Gateway';
    const REASON_PHRASE_503 = 'Service Unavailable';
    const REASON_PHRASE_504 = 'Gateway Time-out';
    const REASON_PHRASE_505 = 'HTTP Version not supported';
    const REASON_PHRASE_506 = 'Variant Also Negotiates';
    const REASON_PHRASE_507 = 'Insufficient Storage';
    const REASON_PHRASE_508 = 'Loop Detected';
    const REASON_PHRASE_509 = 'Bandwidth Limit Exceeded';
    const REASON_PHRASE_510 = 'Not Extended';

    /**
     * @var ResponseInterface|null
     */
    protected $__previous;

    /**
     * @param int                    $statusCode
     * @param string                 $reasonPhrase
     * @param string                 $protocolVersion
     * @param array                  $headers
     * @param string|null            $body
     * @param ResponseInterface|null $__previous
     */
    public function __construct(
        int $statusCode = null,
        string $reasonPhrase = null,
        string $protocolVersion = null,
        array $headers = [],
        string $body = null,
        ResponseInterface $__previous = null
    ) {
        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
        $this->protocolVersion = $protocolVersion;
        $this->headers = $headers;
        $this->body = $body;
        $this->__previous = $__previous;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return (int) $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        return $this->with(['statusCode' => $code, 'reasonPhrase' => $reasonPhrase]);
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return (string) $this->reasonPhrase;
    }

    /**
     * @param array $parameters
     *
     * @return Response
     */
    protected function with(array $parameters): self
    {
        $defaults = [
            'statusCode' => $this->statusCode,
            'reasonPhrase' => $this->reasonPhrase,
            'protocolVersion' => $this->protocolVersion,
            'headers' => $this->headers,
            'body' => $this->body,
        ];

        $arguments = array_values(array_replace($defaults, $parameters, ['__previous' => $this]));

        return new static(...$arguments);
    }
}
