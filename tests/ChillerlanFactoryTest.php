<?php
/**
 * Class ChillerlanFactoryTest
 *
 * @created      14.03.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */

declare(strict_types=1);

namespace chillerlan\PHPUnitHttpTest;

use chillerlan\HTTP\CurlClient;
use chillerlan\HTTP\Psr7\HTTPFactory;
use chillerlan\PHPUnitHttp\ChillerlanHttpClientFactory;

/**
 *
 */
class ChillerlanFactoryTest extends PHPUnitHttpFactoryTestAbstract{

	protected string $REQUEST_FACTORY     = HTTPFactory::class;
	protected string $RESPONSE_FACTORY    = HTTPFactory::class;
	protected string $STREAM_FACTORY      = HTTPFactory::class;
	protected string $URI_FACTORY         = HTTPFactory::class;
	protected string $HTTP_CLIENT_FACTORY = ChillerlanHttpClientFactory::class;

	public function testHttpClientInstance():void{
		$this::assertInstanceOf(CurlClient::class, $this->httpClient);
	}

}
