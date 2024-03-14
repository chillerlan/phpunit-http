<?php
/**
 * Class PHPUnitHttpFactoryTestAbstract
 *
 * @created      14.03.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */

declare(strict_types=1);

namespace chillerlan\PHPUnitHttpTest;

use chillerlan\PHPUnitHttp\HttpFactoryTrait;
use chillerlan\PHPUnitHttp\HttpClientFactoryInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;
use function json_decode, realpath;

/**
 *
 */
abstract class PHPUnitHttpFactoryTestAbstract extends TestCase{
	use HttpFactoryTrait;

	protected const CACERT = __DIR__.'/cacert.pem';

	protected string $REQUEST_FACTORY;
	protected string $RESPONSE_FACTORY;
	protected string $STREAM_FACTORY;
	protected string $URI_FACTORY;

	protected function setUp():void{

		try{
			$this->initFactories(realpath($this::CACERT));
		}
		catch(Throwable $e){
			$this->markTestSkipped('unable to init http factories: '.$e->getMessage());
		}

	}

	abstract public function testHttpClientInstance():void;

	public function testSendRequest():void{
		// wrapping this in try/catch as httpbin may have an outage
		try{
			$uri      = $this->uriFactory->createUri('https://httpbin.org/get');
			$request  = $this->requestFactory->createRequest('GET', $uri);
			$response = $this->httpClient->sendRequest($request);

			$this::assertSame(200, $response->getStatusCode());

			$body = $response->getBody();

			if($body->isSeekable()){
				$body->rewind();
			}

			$json = json_decode($body->getContents());

			$this::assertSame((string)$uri, $json->url);
			$this::assertSame(HttpClientFactoryInterface::USER_AGENT, $json->headers->{'User-Agent'});
		}
		catch(Throwable $e){
			$this->markTestSkipped('error: '.$e->getMessage());
		}

	}

	public function testInvalidClassException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid class: "whatever"');

		$this->REQUEST_FACTORY = 'whatever';

		$this->initFactories('');
	}

	public function testVonstantNotDefinedException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('constant "REQUEST_FACTORY" not defined');
		/** @phan-suppress-next-line PhanTypeObjectUnsetDeclaredProperty */
		unset($this->REQUEST_FACTORY);

		$this->initFactories('');
	}

	public function testNoCacertException():void{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('invalid CA bundle: "foo"');

		$this->initFactories('foo');
	}

	/**
	 * @phan-suppress PhanTypeObjectUnsetDeclaredProperty, PhanUndeclaredProperty, PhanPossiblyUnsetPropertyOfThis
	 */
	public function testInvokeWithoutHttpClient():void{
		unset($this->httpClientFactory, $this->httpClient, $this->HTTP_CLIENT_FACTORY);

		$this->initFactories();

		$this::assertFalse(isset($this->httpClientFactory, $this->httpClient));
	}

}
