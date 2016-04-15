<?php

namespace Cobweb\Context\Tests\Unit;

/**
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

use Cobweb\Context\ContextLoader;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Testcase for the context loader
 *
 * @author Francois Suter <typo3@cobweb.ch>
 * @package TYPO3
 * @subpackage tx_context
 */
class ContextLoaderTest extends UnitTestCase
{

    /**
     * @var ContextLoader
     */
    protected $subject;

    public function setUp()
    {
        $this->subject = GeneralUtility::makeInstance(ContextLoader::class);
    }

    /**
     * Provides values to parse and the expected result.
     *
     * @return array
     */
    public function valuesProvider()
    {
        $values = array(
            // Value should be unchanged
            'simple value' => array(
                    'value' => 'foo',
                    'result' => 'foo'
            ),
            // Value should be stripped of the "foo:" prefix
            'value with table' => array(
                    'value' => 'foo:bar',
                    'result' => 'bar'
            ),
            // An array of value should remain an array, with prefixed values cleaned up
            'array of values' => array(
                    'value' => array(
                            'foo' => 'bar',
                            'baz' => array(
                                    'say' => 'hello:world'
                            )
                    ),
                    'result' => array(
                            'foo' => 'bar',
                            'baz' => array(
                                    'say' => 'world'
                            )
                    )
            )
        );
        return $values;
    }

    /**
     * Test a simple value. It should be unchanged.
     *
     * @param mixed $value Value to test
     * @param mixed $result Expected result
     * @test
     * @dataProvider valuesProvider
     */
    public function simpleValue($value, $result)
    {
        $actualResult = $this->subject->cleanUpValues($value);
        self::assertEquals(
                $result,
                $actualResult
        );
    }
}
