<?php
namespace TYPO3\Flow\Resource;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Core\Bootstrap;
use TYPO3\Flow\Configuration\ConfigurationManager;
use TYPO3\Flow\Http\HttpRequestHandlerInterface;
use TYPO3\Flow\Http\Response;
use TYPO3\Flow\Utility\MediaTypes;

/**
 * A request handler which returns data of static resources
 *
 * @Flow\Scope("singleton")
 * @Flow\Proxy(false)
 */
class StaticResourcesRequestHandler implements HttpRequestHandlerInterface {

	/**
	 * @var \TYPO3\Flow\Core\Bootstrap
	 */
	protected $bootstrap;

	/**
	 * Make exit() a closure so it can be manipulated during tests
	 *
	 * @var \Closure
	 */
	public $exit;

	/**
	 * Constructor
	 *
	 * @param \TYPO3\Flow\Core\Bootstrap $bootstrap
	 */
	public function __construct(Bootstrap $bootstrap) {
		$this->bootstrap = $bootstrap;
		$this->exit = function() { exit(); };
	}

	/**
	 * This request handler can handle any web request.
	 *
	 * @return boolean If the request is a web request, TRUE otherwise FALSE
	 * @api
	 */
	public function canHandleRequest() {
		return (PHP_SAPI !== 'cli') && isset($_GET['TYPO3_Flow_Resource_StaticResourcePath']);
	}

	/**
	 * Returns the priority - how eager the handler is to actually handle the
	 * request.
	 *
	 * @return integer The priority of the request handler.
	 * @api
	 */
	public function getPriority() {
		return 200;
	}

	/**
	 * Handles a HTTP request
	 *
	 * @return void
	 */
	public function handleRequest() {
		$response = new Response();

		$packageManager = $this->bootstrap->getEarlyInstance('TYPO3\Flow\Package\PackageManagerInterface');
		$pathParts = explode('/', $_GET['TYPO3_Flow_Resource_StaticResourcePath']);
			// FIXME Implement proper relative path support
		if (isset($pathParts[2]) && $pathParts[2] === '..' && $pathParts[3] === '..') {
			array_shift($pathParts);
			array_shift($pathParts);
			array_shift($pathParts);
			array_shift($pathParts);
		}
		list($packageKey, $path) = explode('/', implode('/', $pathParts), 2);

		if (!$packageManager->isPackageActive($packageKey)) {
			$response->setStatus(404, sprintf('Package "%s" not found', $packageKey));
			$response->send();
			$this->exit->__invoke();
		}

		$package = $packageManager->getPackage($packageKey);
		$pathAndFilename = $package->getResourcesPath() . 'Public/' . $path;
		if (!file_exists($pathAndFilename)) {
			$response->setStatus(404, 'Static resource not found in public package resources');
			$response->send();
			$this->exit->__invoke();
		}

		$response->setContent(function() use ($pathAndFilename) { readfile($pathAndFilename); });
		$response->setHeader('Content-Type', MediaTypes::getMediaTypeFromFilename($pathAndFilename));
		$response->setHeader('Content-Length', filesize($pathAndFilename));

		$response->send();
		$this->exit->__invoke();
	}

	/**
	 * Returns the currently processed HTTP request
	 *
	 * @return \TYPO3\Flow\Http\Request
	 * @api
	 */
	public function getHttpRequest() {
	}

	/**
	 * Returns the HTTP response corresponding to the currently handled request
	 *
	 * @return \TYPO3\Flow\Http\Response
	 * @api
	 */
	public function getHttpResponse() {
	}


}
