<?php
namespace Concerto\http;

use Concerto\http\EmitterInterface;

class Emitter interface EmitterInterface
{
    /**
    *   chunkSize
    *
    *   @var int
    **/
    protected $chunkSize = 4096;

    /**
    *   chunkSize
    *
    *   @param int
    *   @return $this
    **/
    public function chunkSize($chunkSize)
    {
        $this->chunkSize = (int)$chunkSize;
        return $this;
    }

    /**
    *   {inherit}
    *
    **/
    public function emit(ResponceInterface $responce)
    {
        $this->emitHeader($request);
        $this->emitBody($request);
        return $responce;
    }

    /**
    *   emitHeader
    *
    *   @param ResponceInterface
    **/
    protected function emitHeader(ResponceInterface $responce)
    {
        if (headers_sent()) {
            return;
        }

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }

        header(sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ));
    }

    /**
    *   emitBody
    *
    *   @param ResponceInterface
    **/
    protected function emitBody(ResponceInterface $responce)
    {
        if ($this->isEmptyResponse($response)) {
            return;
        }

        $body = $response->getBody();
        if ($body->isSeekable()) {
            $body->rewind();
        }

        $chunkSize = $this->chunkSize;

        $contentLength  = $response->getHeaderLine('Content-Length');
        if (!$contentLength) {
            $contentLength = $body->getSize();
        }

        if (isset($contentLength)) {
            $amountToRead = $contentLength;
            while ($amountToRead > 0 && !$body->eof()) {
                $data = $body->read(min($chunkSize, $amountToRead));
                echo $data;

                $amountToRead -= strlen($data);

                if (connection_status() != CONNECTION_NORMAL) {
                    break;
                }
            }
            return;
        }

        while (!$body->eof()) {
            echo $body->read($chunkSize);
            if (connection_status() != CONNECTION_NORMAL) {
                break;
            }
        }
        return;
    }

    /**
    *   isEmptyResponse
    *
    *   @param ResponceInterface
    *   return bool
    **/
    protected function isEmptyResponse(ResponseInterface $response)
    {
        return in_array($response->getStatusCode(), [204, 205, 304]);
    }
}
