<?php
/**
 * Class GuzzleFactoryTest
 *
 * @created      14.03.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\PHPUnitHttpTest;

use chillerlan\PHPUnitHttp\GuzzleHttpClientFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;

class GuzzleFactoryTest extends PHPUnitHttpFactoryTestAbstract{

	protected string $REQUEST_FACTORY     = HTTPFactory::class;
	protected string $RESPONSE_FACTORY    = HTTPFactory::class;
	protected string $STREAM_FACTORY      = HTTPFactory::class;
	protected string $URI_FACTORY         = HTTPFactory::class;
	protected string $HTTP_CLIENT_FACTORY = GuzzleHttpClientFactory::class;

	public function testHttpClientInstance():void{
		$this::assertInstanceOf(Client::class, $this->httpClient);
	}

}
