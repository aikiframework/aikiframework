<html>
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
 * @package     Aiki
 * @filesource
 */

/*
 * Provisional TEST for installing plugins
 *
 ****/

include "bootstrap.php";

$founded= plugins::search_plugins(AIKI_PLUGIN_DIR);

echo "<pre>";
echo "AIKI PLUGIN INSTALLATION pre-beta...\n";
if ( $founded) {
    echo "Plugins found: ", count($founded), "\n";
    echo "Installed: ", plugins::available_plugins($founded), "\n";
    echo "Activated: ";
    global $db;
    $ids= $db->get_results("SELECT plugin_name,plugin_id FROM aiki_plugins LEFT JOIN aiki_plugin_configurations ON plugin_id= plconf_plugin_id WHERE plconf_id is NULL");
    if ( !is_array($ids) ){
        echo "No plugins is found or all have configuration\n";
    } else {
        foreach ($ids as $id){
            echo "lugin {$id->plugin_name} activated for *\n";
            plugins::insert_plugin_configuration($id->plugin_id,"*");
        }
    }
} else {
    echo "No plugin found in ", AIKI_PLUGIN_DIR;
    
}
echo "</pre>";
?>
</html>
