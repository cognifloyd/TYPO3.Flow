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

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Resource\ResourceMetaDataInterface;
use TYPO3\Flow\Utility\MediaTypes;

/**
 * An Object which is stored in a Storage
 *
 * This class is used internally as a representation of the actual storage data. It must not be used outside the
 * resource management framework.
 */
class Object implements ResourceMetaDataInterface {

	/**
	 * The IANA media type of the stored data
	 *
	 * @var string
	 */
	protected $mediaType;

	/**
	 * The suggested filename
	 *
	 * @var string
	 */
	protected $filename = '';

	/**
	 * The size of this object's data
	 *
	 * @var integer
	 */
	protected $fileSize;

	/**
	 * A suggested relative path for publication of this data
	 *
	 * @var string
	 */
	protected $relativePublicationPath = '';

	/**
	 * SHA1 hash identifying this object's data
	 *
	 * @var string
	 */
	protected $sha1;

	/**
	 * MD5 hash identifying this object's data
	 *
	 * @var string
	 */
	protected $md5;

	/**
	 * An internal URI pointing to the data of represented by this Object
	 *
	 * @var string
	 */
	protected $dataUri;

	/**
	 * Set the IANA media type of this Object
	 *
	 * @param $mediaType
	 * @return void
	 */
	public function setMediaType($mediaType) {
		$this->mediaType = $mediaType;
	}

	/**
	 * Retrieve the IANA media type of this Object
	 *
	 * @return string
	 */
	public function getMediaType() {
		return $this->mediaType;
	}

	/**
	 * Set the suggested filename of this Object
	 *
	 * @param string $filename
	 * @return void
	 */
	public function setFilename($filename) {
		$this->filename = $filename;
	}

	/**
	 * Retrieve the suggested filename of this Object
	 *
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}

	/**
	 * Set the suggested relative publication path
	 *
	 * @param $relativePublicationPath
	 * @return void
	 */
	public function setRelativePublicationPath($relativePublicationPath) {
		$this->relativePublicationPath = $relativePublicationPath;
	}

	/**
	 * Retrieve the suggested relative publication path
	 *
	 * @return string
	 */
	public function getRelativePublicationPath() {
		return $this->relativePublicationPath;
	}

	/**
	 * Returns the size of the content of this storage object
	 *
	 * @return string The md5 hash
	 */
	public function getFileSize() {
		return $this->fileSize;
	}

	/**
	 * Sets the size of the content of this storage object
	 *
	 * @param string $fileSize The content size
	 * @return void
	 */
	public function setFileSize($fileSize) {
		$this->fileSize = $fileSize;
	}

	/**
	 * Set the SHA1 hash identifying the data of this Object
	 *
	 * @param string $sha1
	 * @return void
	 */
	public function setSha1($sha1) {
		$this->sha1 = $sha1;
	}

	/**
	 * Retrieve the SHA1 hash identifying the data of this object
	 *
	 * @return string
	 */
	public function getSha1() {
		return $this->sha1;
	}

	/**
	 * Set the internal data URI
	 *
	 * @param $internalStorageUri
	 * @return void
	 */
	public function setDataUri($internalStorageUri) {
		$this->dataUri = $internalStorageUri;
	}

	/**
	 * Retrieve the internal data URI
	 *
	 * @return string
	 */
	public function getDataUri() {
		return $this->dataUri;
	}

	/**
	 * Returns the md5 hash of the content of this storage object
	 *
	 * @return string The md5 hash
	 */
	public function getMd5() {
		return $this->md5;
	}

	/**
	 * Sets the md5 hash of the content of this storage object
	 *
	 * @return string $md5 The md5 hash
	 */
	public function setMd5($md5) {
		$this->md5 = $md5;
	}

}
?>