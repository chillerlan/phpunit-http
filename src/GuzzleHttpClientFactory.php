<?php
/**
 * Class GuzzleHttpClientFactory
 *
 * @created      14.03.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */

declare(strict_types=1);

namespace chillerlan\PHPUnitHttp;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Guzzle http client
 *
 * requires Guzzle >= 7.3 (and Guzzle PSR-7 >= 2.0 for the PSR-17 factories)
 *
 * @see https://github.com/guzzle/guzzle
 */
final class GuzzleHttpClientFactory implements HttpClientFactoryInterface{

	/**
	 * @inheritDoc
	 */
	public function getClient(string $cacert, ResponseFactoryInterface $responseFactory):ClientInterface{
		return new Client([
			'verify'  => $cacert,
			'headers' => [
				'User-Agent' => self::USER_AGENT,
			],
		]);
	}

}
