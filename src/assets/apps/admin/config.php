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
 * @category    Aiki
 * @package     Admin
 * @filesource
 */

error_reporting(0);

header('Content-type: text/xml');


/**
 * @see bootstrap.php
 */
require_once("../../../bootstrap.php");

/**
 * @see /src/libs/membership.php
 */
if ($membership->permissions != "SystemGOD"){
	die("You do not have permissions to access this file");
}


echo '<?xml version="1.0" encoding="UTF-8"?>
<root>';

/**
 * @see /src/libs/database/index.php
 */
$get_config = $db->get_results("select  config_id, config_type from aiki_config order by config_id");

foreach ($get_config as $config_group){
	echo '<item parent_id="0" id="'.$config_group->config_id.'" ><content><name icon="'.$config['url'].'assets/apps/admin/images/icons/application_form.png"><![CDATA['.$config_group->config_type.']]></name></content></item>';
}


echo "</root>";
