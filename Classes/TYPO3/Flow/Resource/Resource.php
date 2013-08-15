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

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Utility\MediaTypes;

/**
 * Model representing a persistable resource
 *
 * @Flow\Entity
 */
class Resource implements ResourceMetaDataInterface {

	/**
	 * Name of a collection whose storage is used for storing this resource and whose
	 * target is used for publishing.
	 *
	 * @var string
	 */
	protected $collectionName = ResourceManager::DEFAULT_PERSISTENT_COLLECTION_NAME;

	/**
	 * Filename which is used when the data of this resource is downloaded as a file or acting as a label
	 *
	 * @var string
	 * @Flow\Validate(type="StringLength", options={ "maximum"=255 })
	 * @ORM\Column(length=255)
	 */
	protected $filename = '';

	/**
	 * The size of this object's data
	 *
	 * @var integer
	 */
	protected $fileSize;

	/**
	 * An optional relative path which can be used by a publishing target for structuring resources into directories
	 *
	 * @var string
	 */
	protected $relativePublicationPath = '';

	/**
	 * The IANA media type of this resource
	 *
	 * @var string
	 * @Flow\Validate(type="StringLength", options={ "maximum"=100 })
	 * @ORM\Column(length=100)
	 */
	protected $mediaType;

	/**
	 * SHA1 hash identifying the content attached to this resource
	 *
	 * @var string
	 * @ORM\Column(length=40)
	 */
	protected $sha1;

	/**
	 * MD5 hash identifying the content attached to this resource
	 *
	 * @var string
	 * @ORM\Column(length=32)
	 */
	protected $md5;

	/**
	 * As soon as the Resource has been published, modifying this object is not allowed
	 *
	 * @Flow\Transient
	 * @var boolean
	 */
	protected $protected = FALSE;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Resource\ResourceManager
	 */
	protected $resourceManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Log\SystemLoggerInterface
	 */
	protected $systemLogger;

	/**
	 * Protects this Resource if it has been persisted already.
	 *
	 * @param integer $initializationCause
	 * @return void
	 */
	public function initializeObject($initializationCause) {
		if ($initializationCause === ObjectManagerInterface::INITIALIZATIONCAUSE_RECREATED) {
			$this->protected = TRUE;
		}
	}

	/**
	 * Returns a resource://<sha1> URI for use with read-only file operations such as
	 * reading or copying.
	 *
	 * @return string A URI which points to the data of this resource for read-access
	 * @api
	 */
	public function getUri() {
		return 'resource://' . $this->sha1;
	}

	/**
	 * Sets the name of the collection this resource should be part of
	 *
	 * @param string $collectionName Name of the collection
	 * @return void
	 * @api
	 */
	public function setCollectionName($collectionName) {
		$this->throwExceptionIfProtected();
		$this->collectionName = $collectionName;
	}

	/**
	 * Returns the name of the collection this resource is part of
	 *
	 * @return string Name of the collection, for example "persistentResources"
	 * @api
	 */
	public function getCollectionName() {
		return $this->collectionName;
	}

	/**
	 * Sets the filename which is used when this resource is downloaded or saved as a file
	 *
	 * @param string $filename
	 * @return void
	 * @api
	 */
	public function setFilename($filename) {
		$this->throwExceptionIfProtected();
		$this->filename = $filename;
		if ($this->mediaType === NULL) {
			$this->mediaType = MediaTypes::getMediaTypeFromFilename($filename);
		}
	}

	/**
	 * Gets the filename
	 *
	 * @return string The filename
	 * @api
	 */
	public function getFilename() {
		return ($this->filename !== '' ? $this->filename : $this->getSha1() . '.bin');
	}

	/**
	 * Sets a relative path which can be used by a publishing target for structuring resources into directories
	 *
	 * @param string $path
	 * @return void
	 * @api
	 */
	public function setRelativePublicationPath($path) {
		$this->throwExceptionIfProtected();
		$this->relativePublicationPath = $path;
	}

	/**
	 * Returns the relative publication path
	 *
	 * @return string
	 * @api
	 */
	public function getRelativePublicationPath() {
		return $this->relativePublicationPath;
	}

	/**
	 * Returns the file extension used for this resource
	 *
	 * @return string The file extension used for this file
	 * @api
	 * @deprecated since 2.1.0
	 */
	public function getFileExtension() {
		$pathInfo = pathinfo($this->filename);
		return isset($pathInfo['extension']) ? $pathInfo['extension'] : '';
	}

	/**
	 * Returns the mime type for this resource
	 *
	 * @return string The mime type
	 * @deprecated since 1.1.0
	 * @see getMediaType()
	 */
	public function getMimeType() {
		return $this->getMediaType();
	}

	/**
	 * Returns the Media Type for this resource
	 *
	 * @return string The IANA Media Type
	 * @api
	 */
	public function getMediaType() {
		return MediaTypes::getMediaTypeFromFilename($this->filename);
	}

	/**
	 * Returns the size of the content of this resource
	 *
	 * @return string The md5 hash
	 */
	public function getFileSize() {
		return $this->fileSize;
	}

	/**
	 * Sets the size of the content of this resource
	 *
	 * @param integer $fileSize The content size
	 * @return void
	 */
	public function setFileSize($fileSize) {
		$this->throwExceptionIfProtected();
		$this->fileSize = $fileSize;
	}

	/**
	 * Returns the sha1 hash of the content of this resource
	 *
	 * @return string The sha1 hash
	 * @api
	 * @deprecated since 2.1.0 - please use getSha1() instead
	 */
	public function getHash() {
		return $this->sha1;
	}

	/**
	 * Sets the SHA1 hash of the content of this resource
	 *
	 * @param string $hash The sha1 hash
	 * @return void
	 * @api
	 * @deprecated since 2.1.0 - please use setSha1() instead
	 */
	public function setHash($hash) {
		$this->throwExceptionIfProtected();
		if (strlen($hash) !== 40) {
			throw new \InvalidArgumentException('Specified invalid hash to setHash()', 1362564119);
		}
		$this->sha1 = $hash;
	}

	/**
	 * Returns the SHA1 hash of the content of this resource
	 *
	 * @return string The sha1 hash
	 * @api
	 */
	public function getSha1() {
		return $this->sha1;
	}

	/**
	 * Sets the SHA1 hash of the content of this resource
	 *
	 * @param string $hash The sha1 hash
	 * @return void
	 * @api
	 */
	public function setSha1($hash) {
		$this->throwExceptionIfProtected();
		if (strlen($hash) !== 40) {
			throw new \InvalidArgumentException('Specified invalid hash to setSha1()', 1362564220);
		}
		$this->sha1 = $hash;
	}

	/**
	 * Returns the md5 hash of the content of this resource
	 *
	 * @return string The md5 hash
	 */
	public function getMd5() {
		return $this->md5;
	}

	/**
	 * Sets the md5 hash of the content of this resource
	 *
	 * @return string $md5 The md5 hash
	 */
	public function setMd5($md5) {
		$this->throwExceptionIfProtected();
		$this->md5 = $md5;
	}
	/**
	 * Sets the resource pointer
	 *
	 * Deprecated – use setHash() instead!
	 *
	 * @param \TYPO3\Flow\Resource\ResourcePointer $resourcePointer
	 * @return void
	 * @deprecated since 2.1.0
	 * @see setSha1()
	 */
	public function setResourcePointer(ResourcePointer $resourcePointer) {
		$this->throwExceptionIfProtected();
		$this->sha1 = $resourcePointer->getHash();
	}

	/**
	 * Returns the resource pointer
	 *
	 * Deprecated – use getHash() instead!
	 *
	 * @return \TYPO3\Flow\Resource\ResourcePointer $resourcePointer
	 * @api
	 * @deprecated since 2.1.0
	 */
	public function getResourcePointer() {
		return new ResourcePointer($this->sha1);
	}

	/**
	 * Returns the SHA1 of the content this Resource is related to
	 *
	 * @return string
	 * @deprecated since 2.1.0
	 */
	public function __toString() {
		return $this->sha1;
	}

	/**
	 * Doctrine lifecycle event callback which is triggered on "postPersist" events.
	 * This method triggers the publication of this resource.
	 *
	 * @return void
	 * @ORM\PostPersist
	 */
	public function postPersist() {
		$collection = $this->resourceManager->getCollection($this->collectionName);
		$collection->getTarget()->publishResource($this, $collection);
	}

	/**
	 * Doctrine lifecycle event callback which is triggered on "preRemove" events.
	 * This method triggers the deletion of data related to this resource.
	 *
	 * @return void
	 * @ORM\PreRemove
	 */
	public function preRemove() {
		$this->resourceManager->deleteResource($this);
	}

	/**
	 * Throws an exception if this Resource object is protected against modifications.
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function throwExceptionIfProtected() {
		if ($this->protected) {
			throw new Exception(sprintf('Tried to set a property of the resource object with SHA1 hash %s after it has been protected. Modifications are not allowed as soon as the Resource has been published or persisted.', $this->sha1), 1377852347);
		}
	}

}
?>
