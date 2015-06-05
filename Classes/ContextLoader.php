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

use Cobweb\Context\ContextStorageInterface;
use Cobweb\Expressions\ExpressionParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * This class manages the loading of contexts in the FE.
 *
 * @author		Francois Suter <typo3@cobweb.ch>
 * @package		TYPO3
 * @subpackage	tx_context
 */
class ContextLoader {
	protected $extKey = 'context';	// The extension key

	/**
	 * @var array Local copy of the context information
	 */
	static protected $contextData = array();

	/**
	 * Loads the context in the FE.
	 *
	 * This method responds to the configArrayPostProc hook of \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController.
	 * It takes the context information from the template and calls on handlers
	 * to load the data where ever necessary.
	 *
	 * @param array $params Single entry array containing the "config" part of the template (not used)
	 * @param TypoScriptFrontendController $parentObject back-reference to the calling object
	 *
	 * @return void
	 */
	public function loadContext($params, TypoScriptFrontendController $parentObject) {
		$context = array();
		$tsKey = 'tx_' . $this->extKey . '.';
		// Check for existing context information
		if (isset($params['config'][$tsKey])) {
			$contextSetup = GeneralUtility::removeDotsFromTS($params['config'][$tsKey]);
			// Parse the context to make it into a simple hash table
			if (count($contextSetup) > 0) {
				foreach ($contextSetup as $key => $value) {
					$context[$key] = $this->cleanUpValues($value);
				}

				// Store the context data locally
				self::$contextData = $context;
				// Load the context setup into the expression parser's extra data
				ExpressionParser::setExtraData($context);
				// Call additional context storing handlers to make the context setup available
				// where ever else it may be needed
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['contextStorage'])) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['contextStorage'] as $className) {
						/** @var $contextStorage ContextStorageInterface */
						$contextStorage = GeneralUtility::getUserObj($className);
						if ($contextStorage instanceof ContextStorageInterface) {
							$contextStorage->storeContext($context);
						}
					}
				}
			}
		}
	}

	/**
	 * Returns a value from the context data.
	 *
	 * Multidimensional keys can be passed separated by |, e.g.
	 * 	$pid = tx_context::getContextValue('foo|pid');
	 *
	 * @param string $key Key to search for in the context array
	 * @return mixed Value found in the context array
	 * @throws \OutOfRangeException
	 */
	static public function getContextValue($key) {
		if (empty($key)) {
			throw new \OutOfRangeException();
		} else {
			$keyList = GeneralUtility::trimExplode('|', $key, TRUE);
			$value = self::$contextData;
			while (count($keyList) > 0) {
				$key = array_shift($keyList);
				if (isset($value[$key])) {
					$value = $value[$key];
				} else {
					throw new \OutOfRangeException();
				}
			}
		}
		return $value;
	}

	/**
	 * Cleans up value for contexts.
	 *
	 * The values may come with the syntax foo:bar where "foo" is expected to be a table name and "bar" a uid
	 * Only the "bar" part should be kept.
	 *
	 * @param $value
	 * @return array
	 */
	public function cleanUpValues($value) {
		$returnValue = $value;
		if (is_array($value)) {
			$returnValue = array();
			foreach ($value as $key => $subValue) {
				$returnValue[$key] = $this->cleanUpValues($subValue);
			}

		// If the value contains a colon (:), it means it has a syntax like:
		//		tablename:uid
		// In this case, keep only the uid part
		} elseif (strpos($value, ':') !== FALSE) {
			$valueParts = GeneralUtility::trimExplode(':', $value, TRUE);
			if (isset($valueParts[1])) {
				$returnValue = $valueParts[1];
			}
		}
		return $returnValue;
	}
}
