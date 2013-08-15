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
use TYPO3\Flow\Resource\Target\TargetInterface;

/**
 * Interface for a resource collection
 */
interface CollectionInterface {

	/**
	 * Returns the name of this collection
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Returns the storage used for this collection
	 *
	 * @return StorageInterface
	 */
	public function getStorage();

	/**
	 * Returns the publication target defined for this collection
	 *
	 * @return TargetInterface
	 */
	public function getTarget();

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
	public function importResource($source);

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
	public function importResourceFromContent($content);

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
	public function importUploadedResource(array $uploadInfo);

	/**
	 * Publishes the whole collection to the corresponding publishing target
	 *
	 * @return void
	 */
	public function publish();

	/**
	 * Returns all internal data objects of the storage attached to this collection.
	 *
	 * @return array<\TYPO3\Flow\Resource\Storage\Object>
	 */
	public function getObjects();

}
?>