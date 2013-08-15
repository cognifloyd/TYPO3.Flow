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
use TYPO3\Flow\Persistence\QueryResultInterface;
use TYPO3\Flow\Persistence\Repository;

/**
 * Resource Repository
 *
 * Note that this repository is not part of the public API and must not be used in client code. Please use the API
 * provided by Resource Manager instead.
 *
 * @Flow\Scope("singleton")
 * @see \TYPO3\Flow\Resource\ResourceManager
 */
class ResourceRepository extends Repository {

	/**
	 * @var string
	 */
	const ENTITY_CLASSNAME = 'TYPO3\Flow\Resource\Resource';

	/**
	 * Finds other resources which are referring to the same resource data and filename
	 *
	 * @param Resource $resource The resource used for finding similar resources
	 * @return QueryResultInterface The result, including the given resource
	 */
	public function findSimilarResources(Resource $resource) {
		$query = $this->createQuery();
		$query->matching(
			$query->logicalAnd(
				$query->equals('sha1', $resource->getSha1()),
				$query->equals('filename', $resource->getFilename())
			)
		);
		return $query->execute();
	}

}

?>