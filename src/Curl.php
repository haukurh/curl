<?php

namespace Haukurh\Curl;

use InvalidArgumentException;

class Curl
{
    protected $timeout = 10;
    protected $encoding = '';
    protected $maxRedirects = 3;
    protected $followLocation = true;
    protected $userAgent = "PHP cURL Library/1.0 (https://github.com/haukurh/curl)";
    protected $options;
    protected $headers = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function addHeader(string $key, string $value): void
    {
        $this->headers[] = "{$key}: {$value}";
    }

    /**
     * Set the maximum number of seconds to allow cURL functions to execute.
     *
     * @param int $seconds
     */
    public function setTimeout(int $seconds): void
    {
        $this->timeout = $seconds;
    }

    /**
     * Set the maximum amount of HTTP redirects to follow.
     *
     * @param int $max
     */
    public function setMaxRedirects(int $max): void
    {
        $this->maxRedirects = $max;
    }

    /**
     * Set the User agent to be used in cURL requests
     *
     * @param string $userAgent
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    /**
     * Set the contents of the "Accept-Encoding: " header.
     * Supported encodings are "identity", "deflate", and "gzip".
     * Empty string will result in all supported encoding types being used.
     *
     * @param string $encoding
     * @throws InvalidArgumentException
     */
    public function setEncoding(string $encoding): void
    {
        if (!in_array($encoding, ['identity', 'deflate', 'gzip', ''])) {
            throw new InvalidArgumentException("Unsupported encoding: '{$encoding}'");
        }
        $this->encoding = $encoding;
    }

    /**
     * Set follow any "Location: " header that the server sends as part of the HTTP header
     *
     * @param bool $follow
     */
    public function setFollowLocation(bool $follow): void
    {
        $this->followLocation = $follow;
    }

    /**
     * Make a basic (GET) request with cURL
     *
     * @param string $url
     * @param array $options
     * @param array $headers
     * @return Response
     */
    public function request(string $url, array $options = [], array $headers = []): ResponseInterface
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $this->followLocation);
        curl_setopt($curl, CURLOPT_USERAGENT,$this->userAgent);
        curl_setopt($curl, CURLOPT_ENCODING, $this->encoding);

        if ($this->followLocation) {
            curl_setopt($curl, CURLOPT_MAXREDIRS, $this->maxRedirects);
        }

        $requestOptions = array_merge_preserve_keys($this->options, $options);

        $requestHeaders = $this->prepareHeadersForRequest($requestOptions, $headers);
        if ($requestHeaders) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $requestHeaders);
        }

        if ($requestOptions) {
            curl_setopt_array($curl, $requestOptions);
        }

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);

        return $this->response($info, $result);
    }

    protected function prepareHeadersForRequest(array &$requestOptions, array $headers = []): array
    {
        $requestHeaders = [];
        if (array_key_exists(CURLOPT_HTTPHEADER, $requestOptions)) {
            $requestHeaders = is_array($requestOptions[CURLOPT_HTTPHEADER])
                ? $requestOptions[CURLOPT_HTTPHEADER]
                : [$requestOptions[CURLOPT_HTTPHEADER]];
            unset($requestOptions[CURLOPT_HTTPHEADER]);
        }
        return array_unique(array_merge($this->headers, $requestHeaders, $headers));
    }

    /**
     * Make a GET request with cURL
     *
     * @param string $url
     * @param array $headers
     * @return Response
     */
    public function get(string $url, array $headers = []): ResponseInterface
    {
        return $this->request($url, [], $headers);
    }

    /**
     * Make a POST request with cURL
     *
     * @param string $url
     * @param array|string $payload
     * @param array $headers
     * @return Response
     */
    public function post(string $url, $payload, array $headers = []): ResponseInterface
    {
        $options = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
        ];
        return $this->request($url, $options, $headers);
    }

    /**
     * Build the response
     *
     * @param array $info
     * @param string $body
     * @return ResponseInterface
     */
    protected function response(array $info, string $body): ResponseInterface
    {
        return new Response($info, $body);
    }
}
