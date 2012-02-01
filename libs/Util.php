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

	public static function get_changelog($actualRevision) {
		global $AIKI_ROOT_DIR;        
		$ret = "No changelog information";
		$fileWithChanges = "$AIKI_ROOT_DIR/configs/changelog.php";
		if ( file_exists($fileWithChanges) ){
			include_once($fileWithChanges); 
			// Note: create a array $changes with revision=>text.
			$ret="";
			foreach ( $changes as $revision => $text ){
				if ( $actualRevision < $revision ){
					$ret .= "<div><strong>$revision:</strong> $text</div>\n";
				}
			}
		}	
		return $ret;		
	}	


    public static function get_last_revision() {
        global $AIKI_ROOT_DIR;        
        foreach ( array(".bzr/branch/last-revision", "configs/last-revision") as $file){
			if ( file_exists ( "$AIKI_ROOT_DIR/$file" ) ){
				list($last_revision) = explode(' ', file_get_contents("$AIKI_ROOT_DIR/$file") );
				return $last_revision;
			}			
		} 
		return 0;
        
    }

	public static function get_license( ) {
		global $AIKI_ROOT_DIR;
		$file = $AIKI_ROOT_DIR . "/LICENSE";
		if ( file_exists($file) ) {
			return file_get_contents($file);
		} 
		return  "GNU AFFERO GENERAL PUBLIC LICENSE\nVrsion 3, 19 November 2007";
	}
	
    public static function get_authors( $format="plain") {
        global $AIKI_ROOT_DIR;
        $authors_file = $AIKI_ROOT_DIR . "/AUTHORS";
        // $authors = _('See AUTHORS file.');
        $authors_array = array(_('See AUTHORS file.'));
        if ( file_exists($authors_file) ) {
            $authors_array = explode("\n", file_get_contents($authors_file));
        }
           
		if ( $format=="list") {	
			return "<ul><li>". join('</li><li>', array_filter($authors_array)) ."</li></ul>";
		}

        return join(', ', $authors_array);

    }
}
