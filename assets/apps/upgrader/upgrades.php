<?php

/**
 * Aiki Framework (PHP)
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
 * @category    Aiki apps
 * @package     installer
 * @filesource
 *
 * 
 */

// Add sql as values for array $upgrades, where revision is key.
// example:   $upgrades[555] = "UPDATE aiki_foo SET bar='foo'"
// if you need add more than one statement, make an array: 
// example:   $upgrades[555] = array (
//                 "UPDATE aiki_foo SET bar='foo'", 
//                 "UPDATE aiki_bar SET bar=1");


// revision 1097 introduces engines for site.
$upgrades[1097]= 'UPDATE aiki_sites SET site_engine="aiki" WHERE site_engine="" OR site_engine is null';

// Revision 942 changes old php script markup with new (script(....)script)
$upgrades[942] =  'UPDATE aiki_widgets SET widget=REPLACE( REPLACE(widget,"<php ","(script("),"php>",")script)");';
