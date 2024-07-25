<?php
/**
 * Trait HttpFactoryTrait
 *
 * @created      14.03.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\PHPUnitHttp;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{
	RequestFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface,
	StreamFactoryInterface, UploadedFileFactoryInterface, UriFactoryInterface
};
use InvalidArgumentException;
use function class_exists, constant, defined, file_exists, in_array, sprintf;

/**
 * Invokes a set of PSR-17 HTTP factories and a PSR-18 HTTP client
 */
trait HttpFactoryTrait{

	/**
	 * @todo: constants in traits are allowed as of PHP 8.2
	 *
	 * @var array<string, string>
	 */
	private array $FACTORIES = [
		'requestFactory'       => 'REQUEST_FACTORY',
		'responseFactory'      => 'RESPONSE_FACTORY',
		'streamFactory'        => 'STREAM_FACTORY',
		'uriFactory'           => 'URI_FACTORY',
		'serverRequestFactory' => 'SERVER_REQUEST_FACTORY',
		'uploadedFileFactory'  => 'UPLOADED_FILE_FACTORY',
		'httpClientFactory'    => 'HTTP_CLIENT_FACTORY',
	];

	protected RequestFactoryInterface       $requestFactory;
	protected ResponseFactoryInterface      $responseFactory;
	protected StreamFactoryInterface        $streamFactory;
	protected UriFactoryInterface           $uriFactory;
	protected ServerRequestFactoryInterface $serverRequestFactory;
	protected UploadedFileFactoryInterface  $uploadedFileFactory;
	protected HttpClientFactoryInterface    $httpClientFactory;
	protected ClientInterface               $httpClient;

	/**
	 * initializes the http factories
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function initFactories(string $cacert = ''):void{

		foreach($this->FACTORIES as $property => $const){
			$class = $this->getFactoryClass($const);

			// some interfaces may not always be needed or implemented
			if($class === null){
				continue;
			}

			if(!class_exists($class)){
				throw new InvalidArgumentException(sprintf('invalid class: "%s"', $class));
			}

			// we don't need to check the interface as it is enforced by the property
			$this->{$property} = new $class;
		}

		if(isset($this->httpClientFactory)){

			if(empty($cacert) || !file_exists($cacert)){
				throw new InvalidArgumentException(sprintf('invalid CA bundle: "%s"', $cacert));
			}

			$this->httpClient = $this->httpClientFactory->getClient($cacert, $this->responseFactory);
		}
	}

	/**
	 * gets the class FQCN for the given property or constant
	 *
	 * @throws \InvalidArgumentException
	 */
	private function getFactoryClass(string $constantName):string|null{

		// class was defined as property in the test file
		if(isset($this->{$constantName})){
			return $this->{$constantName};
		}

		// check phpunit.xml constants
		if(defined($constantName)){
			return constant($constantName);
		}

		// we don't throw on these factories as they might not always be needed
		if(in_array($constantName, ['SERVER_REQUEST_FACTORY', 'UPLOADED_FILE_FACTORY', 'HTTP_CLIENT_FACTORY'], true)){
			return null;
		}

		throw new InvalidArgumentException(sprintf('property/constant "%s" not defined -> see phpunit.xml', $constantName));
	}

}
