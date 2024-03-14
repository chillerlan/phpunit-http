<?php
/**
 * Interface HttpClientFactoryInterface
 *
 * @created      14.03.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */

declare(strict_types=1);

namespace chillerlan\PHPUnitHttp;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseFactoryInterface;

interface HttpClientFactoryInterface{

	public const USER_AGENT = 'chillerlanPHPUnitHttp/1.0.0 +https://github.com/chillerlan/phpunit-http';

	/**
	 * Returns a fully prepared http client instance
	 *
	 * @param string $cacert the (preferably absolute) path to a CA certificate bundle
	 *
	 * @see https://curl.se/docs/caextract.html
	 */
	public function getClient(string $cacert, ResponseFactoryInterface $responseFactory):ClientInterface;

}
