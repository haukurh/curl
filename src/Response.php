<?php

namespace Curl;

class Response
{
    const HTTP_URL = 'url';
    const HTTP_CODE = 'http_code';
    const HTTP_CONTENT_TYPE = 'content_type';
    const CURL_SIZE_DOWNLOAD = 'size_download';

    protected $info = [];
    protected $body;

    public function __construct(array $info, string $body)
    {
        $this->info = $info;
        $this->body = $body;
    }

    /**
     * Returns the response information
     *
     * @return array
     */
    public function info(): array
    {
        return $this->info;
    }

    /**
     * Returns cURL info
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $this->info[$key] ?? null;
    }

    /**
     * Returns the http response code
     *
     * @return int
     */
    public function code(): int
    {
        return $this->get(self::HTTP_CODE);
    }

    /**
     * Returns the request URL
     *
     * @return string
     */
    public function url(): string
    {
        return $this->get(self::HTTP_URL);
    }

    /**
     * Returns the media type of the response
     *
     * @return string
     */
    public function contentType(): string
    {
        return $this->get(self::HTTP_CONTENT_TYPE);
    }

    /**
     * Returns the size of response payload in bytes
     * does not include the headers, only the response body
     *
     * @return int
     */
    public function size(): int
    {
        return $this->get(self::CURL_SIZE_DOWNLOAD);
    }

    /**
     * Returns the response body
     *
     * @return string
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * Returns true if the http response code is 200, false otherwise
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->code() === 200;
    }

    /**
     * Returns true if the request was successful, false otherwise
     * i.e. if the http response code is in the 2xx range
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->code() >= 200 && $this->code() < 300;
    }

    /**
     * Saves the response body to specified file
     *
     * @param string $filename
     */
    public function saveTo(string $filename): void
    {
        $stream = fopen($filename, 'w');
        fwrite($stream, $this->body);
        fclose($stream);
    }
}
