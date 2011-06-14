<?php

/** Aiki Framework Tests (PHP)
 *
 * Tests the Log utility.
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Aikilab http://www.aikilab.com
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Tests
 * @filesource */

/** define this for script access */
define("IN_AIKI", TRUE);

require_once("libs/Log.php");

/** To test protected properties and methods */
class TestLog extends Log {
	public function getAllow() {
		return $this->_allow;
	}	
}

/** This is the test class which should be the same as the file name */
class LogTest extends PHPUnit_Framework_TestCase {

	public function testGetContents() {
		
		// test the get contents method
        $log = new TestLog("", "", "", "");
        $this->assertSame("NONE", $log->getContents());
        
        $log = new TestLog(".", "test.log", "none", ".");
        $this->assertSame("NONE", $log->getContents());
        
        // test protected property value
        $this->assertSame("NONE", $log->getAllow());
	}
}