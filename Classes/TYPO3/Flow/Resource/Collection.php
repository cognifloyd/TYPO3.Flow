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
use TYPO3\Flow\Resource\Storage\StorageInterface;
use TYPO3\Flow\Resource\Storage\WritableStorageInterface;
use TYPO3\Flow\Resource\Target\TargetInterface;
use TYPO3\Flow\Utility\Arrays;

/**
 * A resource collection
 */
class Collection implements CollectionInterface {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var StorageInterface
	 */
	protected $storage;

	/**
	 * @var TargetInterface
	 */
	protected $target;

	/**
	 * @var array
	 */
	protected $pathPatterns;

	/**
	 * @var array
	 */
	protected $files;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Resource\ResourceRepository
	 */
	protected $resourceRepository;

	/**
	 * Constructor
	 *
	 * @param string $name User-space name of this collection, as specified in the settings
	 * @param StorageInterface $storage The storage for data used in this collection
	 * @param TargetInterface $target The publication target for this collection
	 * @param array $pathPatterns
	 * @param array $fileNames
	 */
	public function __construct($name, StorageInterface $storage, TargetInterface $target, array $pathPatterns, array $fileNames) {
		$this->name = $name;
		$this->storage = $storage;
		$this->target = $target;
		$this->pathPatterns = $pathPatterns;
		$this->files = $fileNames;
	}

	/**
	 * Returns the name of this collection
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the storage used for this collection
	 *
	 * @return StorageInterface
	 */
	public function getStorage() {
		return $this->storage;
	}

	/**
	 * Returns the publication target defined for this collection
	 *
	 * @return TargetInterface
	 */
	public function getTarget() {
		return $this->target;
	}

	/**
	 * Imports a resource (file) from the given URI or PHP resource stream into this collection.
	 *
	 * On a successful import this method returns a Resource object representing the newly
	 * imported persistent resource.
	 *
	 * Note that this collection must have a writable storage in order to import resources.
	 *
	 * @param string | resource $source The URI (or local path and filename) or the PHP resource stream to import the resource from
	 * @return Resource A resource object representing the imported resource
	 * @throws Exception
	 */
	public function importResource($source) {
		if (!$this->storage instanceof WritableStorageInterface) {
			throw new Exception(sprintf('Could not import resource into collection "%s" because its storage "%s" is a read-only storage.', $this->name, $this->storage->getName()), 1375197288);
		}

		$resource = $this->storage->importResource($source, $this->name);
		$this->resourceRepository->add($resource);
		return $resource;
	}

	/**
	 * Imports a resource from the given string content into this collection.
	 *
	 * On a successful import this method returns a Resource object representing the newly
	 * imported persistent resource.
	 *
	 * Note that this collection must have a writable storage in order to import resources.
	 *
	 * @param string $content The actual content to import
	 * @return Resource A resource object representing the imported resource
	 * @throws Exception
	 */
	public function importResourceFromContent($content) {
		if (!$this->storage instanceof WritableStorageInterface) {
			throw new Exception(sprintf('Could not import resource into collection "%s" because its storage "%s" is a read-only storage.', $this->name, $this->storage->getName()), 1381155740);
		}

		$resource = $this->storage->importResourceFromContent($content, $this->name);
		$this->resourceRepository->add($resource);
		return $resource;
	}

	/**
	 * Imports a resource (file) from the given upload info array into this collection.
	 *
	 * On a successful import this method returns a Resource object representing
	 * the newly imported persistent resource.
	 *
	 * Note that this collection must have a writable storage in order to import resources.
	 *
	 * @param array $uploadInfo An array detailing the resource to import (expected keys: name, tmp_name)
	 * @return mixed A resource object representing the imported resource or a string containing an error message if an error ocurred
	 * @throws Exception
	 */
	public function importUploadedResource(array $uploadInfo) {
		if (!$this->storage instanceof WritableStorageInterface) {
			throw new Exception(sprintf('Could not import resource into collection "%s" because its storage "%s" is a read-only storage.', $this->name, $this->storage->getName()), 1375197388);
		}
		$resource = $this->storage->importUploadedResource($uploadInfo, $this->name);
		$this->resourceRepository->add($resource);
		return $resource;
	}

	/**
	 * Publishes the whole collection to the corresponding publishing target
	 *
	 * @return void
	 */
	public function publish() {
		$this->target->publishCollection($this);
	}

	/**
	 * Returns all internal data objects of the storage attached to this collection.
	 *
	 * @return array<\TYPO3\Flow\Resource\Storage\Object>
	 * TODO Return an iterator which also only applies filters on demand etc
	 * TODO Implement filters HERE not in Storage (or?)
	 */
	public function getObjects() {
		$objects = array();
		if ($this->pathPatterns === array() && $this->files === array()) {
			$objects = $this->storage->getObjectsByCollectionName($this->name);
		} else {
			foreach ($this->pathPatterns as $pathPattern) {
				$objects = array_merge($objects, $this->storage->getObjectsByPathPattern($pathPattern));
			}
			foreach ($this->files as $pathAndFilename) {
				$objects = array_merge($objects, $this->storage->getObjectsByPathAndFilename($pathAndFilename));
			}
		}
		return $objects;
	}

}
?>