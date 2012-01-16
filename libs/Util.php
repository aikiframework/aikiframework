<?php
/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Jon Phillips
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Library
 * @filesource
 */
 
if(!defined('IN_AIKI')) {
	die('No direct script access allowed');
}


/**
 * A utility class for some global strange operations, until the get a new home.
 *
 * Use like: Util::get_last_revision();
 *
 *
 */


class Util {

	/**
	 * Returns the last revision of aiki if .bzr exists, or 0 assuming this
     * is a release, since .bzr is stripped out.
     *
     * @return number
	 */

    public static function get_last_revision() {
        global $AIKI_ROOT_DIR;
        $last_revision_file = $AIKI_ROOT_DIR . "/.bzr/branch/last-revision";
        $last_revision = '0';
        if ( file_exists($last_revision_file) )
            list($last_revision) = 
                explode(' ', file_get_contents($last_revision_file) );

        return $last_revision;
    }
}
