<?php
namespace TYPO3\Flow\Resource\Storage;

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
use TYPO3\Flow\Resource\Resource;
use TYPO3\Flow\Utility\Files;

/**
 * A resource storage which stores and retrieves resources from active Flow packages.
 */
class PackageStorage extends FileSystemStorage {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 */
	protected $packageManager;

	/**
	 * Initializes this resource storage
	 *
	 * @return void
	 */
	public function initializeObject() {
		// override the FileSystemStorage method because we don't need that here
	}

	/**
	 * Retrieve all Objects stored in this storage.
	 *
	 * @return array<\TYPO3\Flow\Resource\Storage\Object>
	 */
	public function getObjects() {
		return $this->getObjectsByPathPattern('*');
	}


	/**
	 * @param $collectionName
	 * @param null $pattern
	 * @return array
	 */
	public function getObjectsByPathPattern($pattern) {
		$objects = array();
		$directories = array();

		if (strpos($pattern, '/') !== FALSE) {
			list($packageKeyPattern, $directoryPattern) = explode('/', $pattern, 2);
		} else {
			$packageKeyPattern = $pattern;
			$directoryPattern = '*';
		}

		$packages = $this->packageManager->getActivePackages();
		foreach ($packages as $packageKey => $package) {
			if ($directoryPattern === '*') {
				$directories[$packageKey][] = $package->getPackagePath();
			} else {
				$directories[$packageKey] = glob($package->getPackagePath() . $directoryPattern, GLOB_ONLYDIR);
			}
		}

		foreach ($directories as $packageKey => $packageDirectories) {
			foreach ($packageDirectories as $directoryPath) {
				foreach (Files::readDirectoryRecursively($directoryPath) as $resourcePathAndFilename) {
					$pathInfo = pathinfo($resourcePathAndFilename);

					$object = new Object();
					$object->setFilename($pathInfo['basename']);
					$object->setSha1(sha1_file($resourcePathAndFilename));
					$object->setMd5(md5_file($resourcePathAndFilename));
					$object->setFileSize(filesize($resourcePathAndFilename));
					if (isset($pathInfo['dirname'])) {
						list(, $path) = explode('/', str_replace($packages[$packageKey]->getResourcesPath(), '', $pathInfo['dirname']), 2);
						$object->setRelativePublicationPath($packageKey . '/' . $path . '/');
					}
					$object->setDataUri($resourcePathAndFilename);
					$objects[] = $object;
				}
			}
		}

		return $objects;
	}

	/**
	 * Returns a URI which can be used internally to open / copy the given resource
	 * stored in this storage.
	 *
	 * The path and filename returned by this specific implementation is always a
	 * regular, local path and filename.
	 *
	 * @param \TYPO3\Flow\Resource\Resource $resource The resource stored in this storage
	 * @return mixed A URI (for example the full path and filename) leading to the resource file or FALSE if it does not exist
	 * FIXME file_exists
	 */
	public function getPrivateUriByResource(Resource $resource) {
		return $this->getPrivateUriByResourcePath($resource->getPath());
	}

	/**
	 * Returns a URI which can be used internally to open / copy the given resource
	 * stored in this storage.
	 *
	 * The $relativePath must contain a package key as its first path segment,
	 * followed by the a path relative to that package.
	 *
	 * Example: "TYPO3.Flow/Resources/Public/Logo.png"
	 *
	 * The path and filename returned is always a regular, local path and filename.
	 *
	 * @param string $relativePath A relative path of this storage, first the package key, then the relative path
	 * @return mixed The full path and filename leading to the resource file or FALSE if it does not exist
	 * FIXME file_exists
	 */
	public function getPrivateUriByResourcePath($relativePath) {
		list($packageKey, $relativePath) = explode('/', $relativePath, 2);
		$package = $this->packageManager->getPackage($packageKey);
		return $package->getPackagePath() . $relativePath;
	}

}

?>