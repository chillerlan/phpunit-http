<?php
/**
 * Class ChillerlanHttpClientFactory
 *
 * @created      14.03.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */

declare(strict_types=1);

namespace chillerlan\PHPUnitHttp;

use chillerlan\HTTP\{HTTPOptions, CurlClient};
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * chillerlan http client
 *
 * requires chillerlan/php-httpinterface >= 6.0
 *
 * @see https://github.com/chillerlan/php-httpinterface
 */
final class ChillerlanHttpClientFactory implements HttpClientFactoryInterface{

	/**
	 * @inheritDoc
	 */
	public function getClient(string $cacert, ResponseFactoryInterface $responseFactory):ClientInterface{
		$options             = new HTTPOptions;
		$options->ca_info    = $cacert;
		$options->user_agent = self::USER_AGENT;

		return new CurlClient($responseFactory, $options);
	}

}
