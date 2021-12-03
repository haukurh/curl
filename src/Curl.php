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

    public function __construct(array $options = [])
    {
        $this->options = $options;
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
     * @return Response
     */
    public function request(string $url, array $options = []): ResponseInterface
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

        if ($this->options) {
            curl_setopt_array($curl, $this->options);
        }
        if ($options) {
            curl_setopt_array($curl, $options);
        }

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);

        return $this->response($info, $result);
    }

    /**
     * Make a GET request with cURL
     *
     * @param string $url
     * @return Response
     */
    public function get(string $url): ResponseInterface
    {
        return $this->request($url);
    }

    /**
     * Make a POST request with cURL
     *
     * @param string $url
     * @param array $payload
     * @return Response
     */
    public function post(string $url, array $payload): ResponseInterface
    {
        $options = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
        ];
        return $this->request($url, $options);
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
