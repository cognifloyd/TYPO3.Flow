<?php
namespace TYPO3\Flow\Tests\Reflection\Fixture\Model;

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

/**
 * A model fixture which is used for testing the class schema building
 *
 * @Flow\ValueObject
 */
class ValueObject {

	/**
	 * Some string
	 *
	 * @var string
	 */
	protected $aString;

	protected $propertyWithoutAnnotation;

}

?>