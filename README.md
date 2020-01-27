# haukurh/curl

A simple PHP library wrapper for cURL to simplify the usage of cURL requests.

## Basic usage

```php
<?php

require 'vendor/autoload.php';

use Haukurh\Curl\Curl;
use Haukurh\Curl\Response as CurlResponse;

$curl = new Curl();

/** @var Haukurh\Curl\Response $response */
$response = $curl->get("http://example.com/feed.xml");

if ($response->isOk()) {
    $xml = $response->body();
    // Do something with the XML
}
```

## Response

Every request made returns an instance of Response class, which has some useful methods to work with.

```php
$curl = new Curl();

$response = $curl->get("http://example.com/feed.xml");

echo $response->url(); // "http://example.com/feed.xml"
echo $response->code(); // 200
echo $response->isOk(); // true
echo $response->contentType(); // text/xml;
echo $response->size(); // 3510

$body = $response->body();
```

## Set some options

Common cURL options have been made available to configure through some methods

```php
$curl = new Curl();

$curl->setTimeout(3);
$curl->setFollowLocation(true);
$curl->setUserAgent('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36');

$response = $curl->get("http://example.com/feed.xml");

if ($response->isOk()) {
    $xml = $response->body();
    // Do something with the XML
}
```

or if you're more comfortable with good ol' way of configuring cURL

```php
$curl = new Curl([
    CURLOPT_TIMEOUT => 3,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36',
]);

$response = $curl->get("http://example.com/feed.xml");

if ($response->isOk()) {
    $xml = $response->body();
    // Do something with the XML
}
```

you can also set cURL options per request

```php
$curl = new Curl();

$options = [
    CURLOPT_TIMEOUT => 3,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36',
];

$response = $curl->request("http://example.com/feed.xml", $options);

if ($response->isOk()) {
    $xml = $response->body();
    // Do something with the XML
}
```