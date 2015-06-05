<?php
namespace Cobweb\Context;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Interface for the contextStorage hook of class tx_context
 *
 * @author Francois Suter <typo3@cobweb.ch>
 * @package TYPO3
 * @subpackage tx_context
 */
interface ContextStorageInterface {

	/**
	 * Receives an array containing the context (simply a hash table)
	 * and stores it according to its needs.
	 *
	 * @param array $context List of key-value pairs
	 * @return void
	 */
	public function storeContext($context);
}
