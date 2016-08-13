<?php

/** Aiki Framework Tests (PHP)
 *
 * Tests the Util utility.
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


require_once("libs/Util.php");

/** This is the test class which should be the same as the file name */
class UtilTest extends PHPUnit_Framework_TestCase {

    public function testLastRevisionReal() {

        $last_revision = Util::get_last_revision();
        // echo $last_revision;

        // make sure not empty
        $this->assertNotEmpty($last_revision);
    }

    public function testGetAuthors () {
        $authors = Util::get_authors();

        $this->assertNotEmpty($authors);

        $this->assertInternalType('string', $authors);
    }
}
