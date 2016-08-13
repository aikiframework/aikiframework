<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Roger Martin, Aikilab http://www.aikilab.com
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki apps
 * @package     installer
 * @filesource
 *
 *
 * Installer function library
 *
 */



/**
 *
 * construct html form inserting config values, buttons, and next step.
 *
 * @param $step next step
 * @param $buttons string with additional html controls.
 *
 * @return string containg complete form
 */



function form_hidden ( $step , $buttons="") {
    global $config, $steps, $t;
    $form_hidden = "";
    foreach ( $config as $name => $value) {
        $form_hidden .= "\n<input type='hidden' name='$name' value='$value'>";
    }

    $next= sprintf( "<input type='submit' class='button next' value='%s %s'>",
        $t->t("Next:"),
        $t->t( $steps[$step] ) );
    return  "\n<div class='actions'><form method='post'>%s<input type='hidden' name='step' value='$step'>{$form_hidden}{$next}{$buttons}</form></div>\n";
}


/**
 *
 * construct select control for language select
 *
 * @param array translations available
 *
 * @return string containg comple html select or "" if not necesary
 */

function select_language (){
    global $t;

    $translations = $t->translations();
    if ( !is_array($translations) || count($translations)==0 ){
        return "";
    }

    $options = "<option value='en'>English</option>";
    foreach ( $translations as $isoCode ){
        $options .= "<option value='$isoCode'>" . Util::iso639($isoCode) ."</option>\n";
    }
    return
        "<form id='f_language'><label for='language'>" . $t->t("Select language for installation") ."</label>\n" .
        "<select name='language' id='language' class='user-input'>".
        $options.
        "</select><input type='submit' value='" . $t->t("Change") ."'></form>";
}


/**
 *
 * construct select control for sql server type
 *
 * @param actual selected $db_type
 *
 * @return string containg comple html select.
 */

function select_db_type( $db_type ){
    global $t;
    $selectType="<select name='db_type' id='db_type' class='user-input'>\n";
    $options = array (
        "mysql" =>"MySQL",
        "mssql" =>"mssql",
        "oracle" => $t->t("oracle 8 or higher"),
        "pdo"=>"PDO",
        "postgresql" =>"postgresql",
        "sqlite" =>"sqlite");
    foreach ( $options as $value=>$option){
        $selected= ($value==$db_type ? " selected ": "" );
        $selectType .= "\t<option value='$value'$selected>$option</option>\n";
    }
    $selectType .="</select>\n";
    return $selectType;
}

/**
 *
 * check step
 *
 * @param by value step.
 *
 * @return message or "". Correct step.
 */

function check_step(&$step) {
    global $AIKI_ROOT_DIR, $config, $t;

    switch ($step){
        case 5:
            if ( !file_exists($AIKI_ROOT_DIR ."/config.php") ){
                $step=4;
            }

        default:
            if ( file_exists($AIKI_ROOT_DIR ."/config.php" )  && $step!=5 && !isset($_REQUEST["try_step_4"]) ) {
                $step=0;
                return  $t->t("There is a existing configuration file.") .
                        "<em>".  $t->t("Please remove file to continue installation") ."<br>".
                        "$AIKI_ROOT_DIR/config.php".
                        "</em>";
            }

            $testFiles = array (
                "/sql/CreateTables.sql",
                "/sql/InsertDefaults.sql",
                "/sql/InsertVariable-in.sql",
                "/configs/htaccess.inc",
                "/configs/config.php");
            $message ="";
            foreach ( $testFiles as $name){
                $file = $AIKI_ROOT_DIR . $name ;
                if ( !file_exists($file) ){
                    $message .= $file . "<br>";
                    $step=0;
                }
            }
            if ($message!="") {
                $step=0;
                $message = $t->t("Essential files missing") . ":<em>$message</em>";
            }
            return $message;
    }
}


function clean_url($url, $ending=true){
    $top= strpos( $url, "/assets/apps/installer");
    return ( $top !== false ? substr($url,0,$top): $url) . ( $ending ? "/" : "");
}


function clean_host($url){
    if ( preg_match('#^https?://([^/])*/#Ui',$url ) ){
        return "/". trim(preg_replace('#^https?://([^/])*/#Ui',"", $url ),"/");
    }
    return "/";
}

/**
 *
 * send login and password via email
 *
 * @global $config,  $t
 *
 * @return false if mail is send else true
 */

function send_data_by_email(){
    global $config, $t;

    if (!$config['ADMIN_EMAIL'] ||
        !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $config['ADMIN_EMAIL'])){
            return false;
    }

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=utf-8\r\n";
    $headers .= "From: noreply@aikiframework.org\r\n";

    $message = $t->t("Hello"). "  {$config['ADMIN_FULLNAME']} \n".
               $t->t("Your new Aiki installation is ready to be used"). "\n\n".
               $t->t("Go to") .  ": {$config['AIKI_SITE_URL']}/admin \n".
               $t->t("Username") . ":{$config['ADMIN_USER']} \n".
               $t->t("Password") . ":{$config['ADMIN_PASSWORD']}\n\n".
               $t->t("Have a nice day")."<br>\n";

    return mail($config['ADMIN_EMAIL'], $t->t('Your new Aiki installation'),$message,$headers);

}

/**
 *
 * Get new htaccess file from template /configs/htaccess.inc
 *
 * @param string aiki installation path
 *
 * @return false or htaccess content as string.
 */

function get_new_htaccess($aikiPath){
    global $config;
    $htaccess_file = @file_get_contents("$aikiPath/configs/htaccess.inc");
    if ( $htaccess_file == false ){
        return false;
    }
    return str_replace( "@AIKI_REWRITE_BASE@", clean_host($config['AIKI_SITE_URL'], false), $htaccess_file);

}


/**
 *
 * Read all sql file, making some replacemnets
 *
 * @global $config, $AIKI_ROOT_DIR,  $AIKI_AUTHORS
 *
 * @return array of SQLS statments
 */

function sqls(){
    global $config, $AIKI_ROOT_DIR;

    $config["ADMIN_PASSWORD"]        = substr(md5(uniqid(rand(),true)),1,8);
    $config["ADMIN_PASSWORD_MD5_MD5"]= md5(md5($config["ADMIN_PASSWORD"]));

    $replace = array (
        "@AIKI_SITE_URL_LEN@"=> strlen($config['AIKI_SITE_URL']),
        "@AIKI_SITE_URL@"    => $config['AIKI_SITE_URL'],
        "@PKG_DATA_DIR_LEN@" => strlen($AIKI_ROOT_DIR),
        "@PKG_DATA_DIR@"     => $AIKI_ROOT_DIR,
        "@ADMIN_USER@"=> $config["ADMIN_USER"],
        "@ADMIN_NAME@"=> $config["ADMIN_FULLNAME"],
        "@ADMIN_PASS@"=> $config["ADMIN_PASSWORD_MD5_MD5"],
        "@ADMIN_MAIL@"=> $config["ADMIN_EMAIL"],
        "@VERSION@"   => AIKI_VERSION,
        "@REVISION@"  => Util::get_last_revision(),
        "@AUTHORS@"   => Util::get_authors());

    $files = array (
        "$AIKI_ROOT_DIR/sql/CreateTables.sql",
        "$AIKI_ROOT_DIR/sql/InsertDefaults.sql",
        "$AIKI_ROOT_DIR/sql/InsertVariable-in.sql",
        "$AIKI_ROOT_DIR/sql/CreateTablesSite.sql",
        "$AIKI_ROOT_DIR/sql/InsertDefaultsSite.sql",
        "$AIKI_ROOT_DIR/sql/InsertVariable-inSite.sql" );

    return Util::get_sqls_statements($files, $replace, true);

}
