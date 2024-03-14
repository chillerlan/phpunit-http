# chillerlan/phpunit-http

Add [PSR-17](https://www.php-fig.org/psr/psr-17/) factories and a [PSR-18](https://www.php-fig.org/psr/psr-18/) client for use in your [PHPUnit](https://phpunit.de/) tests.

[![PHP Version Support][php-badge]][php]
[![Packagist version][packagist-badge]][packagist]
[![License][license-badge]][license]
[![Continuous Integration][gh-action-badge]][gh-action]
[![Packagist downloads][downloads-badge]][downloads]

[php-badge]: https://img.shields.io/packagist/php-v/chillerlan/phpunit-http?logo=php&color=8892BF&logoColor=fff
[php]: https://www.php.net/supported-versions.php
[packagist-badge]: https://img.shields.io/packagist/v/chillerlan/phpunit-http.svg?logo=packagist&logoColor=fff
[packagist]: https://packagist.org/packages/chillerlan/phpunit-http
[license-badge]: https://img.shields.io/github/license/chillerlan/phpunit-http
[license]: https://github.com/chillerlan/phpunit-http/blob/main/LICENSE
[gh-action-badge]: https://img.shields.io/github/actions/workflow/status/chillerlan/phpunit-http/ci.yml?branch=main&logo=github&logoColor=fff
[gh-action]: https://github.com/chillerlan/phpunit-http/actions/workflows/ci.yml?query=branch%3Amain
[downloads-badge]: https://img.shields.io/packagist/dt/chillerlan/phpunit-http.svg?logo=packagist&logoColor=fff
[downloads]: https://packagist.org/packages/chillerlan/phpunit-http/stats

## Overview

### Features

A PSR-18 HTTP client and PSR-17 factories for your unit tetss. That's it.

### Requirements

- PHP 8.1+
  - required extensions may vary depending on the used HTTP client


## Documentation

### Installation with [composer](https://getcomposer.org)

Add this library along with PHPUnit and an optional HTTP client to the `require-dev` section of your `composer.json`:

```json
{
	"require": {
		"php": "^8.1"
	},
	"require-dev": {
		"chillerlan/phpunit-http": "^1.0",
		"guzzlehttp/guzzle": "^7.8",
		"phpunit/phpunit": "^10.5"
	}
}
```


### Usage

Include the `HttpFactoryTrait` and call `initFactories()` from within PHPUnit's `TestCase::setUp()`:

```php
class MyUnitTest extends \PHPUnit\Framework\TestCase{
	// include the factory trait
	use \chillerlan\PHPUnitHttp\HttpFactoryTrait;

	// you can define the factories either as properties in your test class or in phpunit.xml
	protected string $REQUEST_FACTORY  = MyRequestFactory::class;
	protected string $RESPONSE_FACTORY = MyResponseFactory::class;
	protected string $STREAM_FACTORY   = MyStreamFactory::class;
	protected string $URI_FACTORY      = MyUriFactory::class;

	// these three factories may not always be needed and/or implemented,
	// you can just unset or simply omit the properties
	protected string $HTTP_CLIENT_FACTORY = \chillerlan\PHPUnitHttp\GuzzleHttpClientFactory::class;
	protected string $SERVER_REQUEST_FACTORY;
	protected string $UPLOADED_FILE_FACTORY;

	// a CA bundle is required when using a http client
	protected const CACERT = __DIR__.'/cacert.pem';

	// in PHPUnit's setUp, call the factory initializer
	protected function setUp():void{
		try{
			$this->initFactories(realpath($this::CACERT));
		}
		catch(\Throwable $e){
			$this->markTestSkipped('unable to init http factories: '.$e->getMessage());
		}
	}

	// use the factories
	public function testSomething():void{
		$uri      = $this->uriFactory->createUri('https://example.com');
		$request  = $this->requestFactory->createRequest('GET', $uri);
		$response = $this->httpClient->sendRequest($request);

		// do stuff
		$this::assertSame(200, $response->getStatusCode());
	}

}
```

Instead of setting the properties in the test classes, you can define them as constants in your `phpunit.xml`:

```xml
<?xml version="1.0"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.5/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         cacheDirectory=".build/phpunit-cache"
>
	<testsuites>
		<testsuite name="my test suite">
			<directory>./tests/</directory>
		</testsuite>
	</testsuites>
	<source>
		<include>
			<directory>src</directory>
		</include>
	</source>
	<coverage>
		<report>
			<clover outputFile=".build/coverage/clover.xml"/>
		</report>
	</coverage>
	<php>
		<!-- define your factories here -->
		<const name="REQUEST_FACTORY" value="MyLibrary\MyRequestFactory"/>
		<const name="RESPONSE_FACTORY" value="MyLibrary\MyResponseFactory"/>
		<const name="STREAM_FACTORY" value="MyLibrary\MyStreamFactory"/>
		<const name="URI_FACTORY" value="MyLibrary\MyUriFactory"/>
		<!--
		<const name="SERVER_REQUEST_FACTORY" value=""/>
		<const name="UPLOADED_FILE_FACTORY" value=""/>
		-->
		<const name="HTTP_CLIENT_FACTORY" value="chillerlan\PHPUnitHttp\GuzzleHttpClientFactory"/>
	</php>
</phpunit>

```

Profit!

### Custom HTTP client

You can implement the `HttpClientFactoryInterface` to create your own HTTP client factory:

```php
final class MyHttpClientFactory implements HttpClientFactoryInterface{

	public function getClient(string $cacert, ResponseFactoryInterface $responseFactory):ClientInterface{
		return new MyHttpClient(['cacert' => $cacert, /* ... */]);
	}

}
```

## Disclaimer

Use at your own risk!
