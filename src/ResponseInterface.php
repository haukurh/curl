<?php

namespace Haukurh\Curl;

use stdClass;

interface ResponseInterface
{
    /**
     * Returns the response information
     *
     * @return array
     */
    public function info(): array;

    /**
     * Returns cURL info
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key);

    /**
     * Returns the http response code
     *
     * @return int
     */
    public function code(): int;

    /**
     * Returns the request URL
     *
     * @return string
     */
    public function url(): string;

    /**
     * Returns the media type of the response
     *
     * @return string
     */
    public function contentType(): string;

    /**
     * Returns the size of response payload in bytes
     * does not include the headers, only the response body
     *
     * @return int
     */
    public function size(): int;

    /**
     * Returns the response body
     *
     * @return string
     */
    public function body(): string;

    /**
     * Parse json response
     *
     * @return stdClass|array|null
     */
    public function json(bool $associative = false);

    /**
     * Returns true if the http response code is 200, false otherwise
     *
     * @return bool
     */
    public function isOk(): bool;

    /**
     * Returns true if the request was successful, false otherwise
     * i.e. if the http response code is in the 2xx range
     *
     * @return bool
     */
    public function isSuccessful(): bool;

}
