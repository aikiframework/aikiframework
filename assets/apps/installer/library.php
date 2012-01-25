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
 * return english name of iso6391 codec
 * 
 * @param string iso code
 * 
 * @return english name or code.
 */

function iso639($code) {	
	// source: http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
	$iso639 = array(
		'aa' => 'Afar',
		'ab' => 'Abkhaz',
		'ae' => 'Avestan',
		'af' => 'Afrikaans',
		'ak' => 'Akan',
		'am' => 'Amharic',
		'an' => 'Aragonese',
		'ar' => 'Arabic',
		'as' => 'Assamese',
		'av' => 'Avaric',
		'ay' => 'Aymara',
		'az' => 'Azerbaijani',
		'ba' => 'Bashkir',
		'be' => 'Belarusian',
		'bg' => 'Bulgarian',
		'bh' => 'Bihari',
		'bi' => 'Bislama',
		'bm' => 'Bambara',
		'bn' => 'Bengali',
		'bo' => 'Tibetan Standard, Tibetan, Central',
		'br' => 'Breton',
		'bs' => 'Bosnian',
		'ca' => 'Catalan; Valencian',
		'ce' => 'Chechen',
		'ch' => 'Chamorro',
		'co' => 'Corsican',
		'cr' => 'Cree',
		'cs' => 'Czech',
		'cu' => 'Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic',
		'cv' => 'Chuvash',
		'cy' => 'Welsh',
		'da' => 'Danish',
		'de' => 'German',
		'dv' => 'Divehi; Dhivehi; Maldivian;',
		'dz' => 'Dzongkha',
		'ee' => 'Ewe',
		'el' => 'Greek, Modern',
		'en' => 'English',
		'eo' => 'Esperanto',
		'es' => 'Spanish; Castilian',
		'et' => 'Estonian',
		'eu' => 'Basque',
		'fa' => 'Persian',
		'ff' => 'Fula; Fulah; Pulaar; Pular',
		'fi' => 'Finnish',
		'fj' => 'Fijian',
		'fo' => 'Faroese',
		'fr' => 'French',
		'fy' => 'Western Frisian',
		'ga' => 'Irish',
		'gd' => 'Scottish Gaelic; Gaelic',
		'gl' => 'Galician',
		'gn' => 'GuaranÃ',
		'gu' => 'Gujarati',
		'gv' => 'Manx',
		'ha' => 'Hausa',
		'he' => 'Hebrew (modern)',
		'hi' => 'Hindi',
		'ho' => 'Hiri Motu',
		'hr' => 'Croatian',
		'ht' => 'Haitian; Haitian Creole',
		'hu' => 'Hungarian',
		'hy' => 'Armenian',
		'hz' => 'Herero',
		'ia' => 'Interlingua',
		'id' => 'Indonesian',
		'ie' => 'Interlingue',
		'ig' => 'Igbo',
		'ii' => 'Nuosu',
		'ik' => 'Inupiaq',
		'io' => 'Ido',
		'is' => 'Icelandic',
		'it' => 'Italian',
		'iu' => 'Inuktitut',
		'ja' => 'Japanese (ja)',
		'jv' => 'Javanese (jv)',
		'ka' => 'Georgian',
		'kg' => 'Kongo',
		'ki' => 'Kikuyu, Gikuyu',
		'kj' => 'Kwanyama, Kuanyama',
		'kk' => 'Kazakh',
		'kl' => 'Kalaallisut, Greenlandic',
		'km' => 'Khmer',
		'kn' => 'Kannada',
		'ko' => 'Korean',
		'kr' => 'Kanuri',
		'ks' => 'Kashmiri',
		'ku' => 'Kurdish',
		'kv' => 'Komi',
		'kw' => 'Cornish',
		'ky' => 'Kirghiz, Kyrgyz',
		'la' => 'Latin',
		'lb' => 'Luxembourgish, Letzeburgesch',
		'lg' => 'Luganda',
		'li' => 'Limburgish, Limburgan, Limburger',
		'ln' => 'Lingala',
		'lo' => 'Lao',
		'lt' => 'Lithuanian',
		'lu' => 'Luba-Katanga',
		'lv' => 'Latvian',
		'mg' => 'Malagasy',
		'mh' => 'Marshallese',
		'mi' => 'Maori',
		'mk' => 'Macedonian',
		'ml' => 'Malayalam',
		'mn' => 'Mongolian',
		'mr' => 'Marathi',
		'ms' => 'Malay',
		'mt' => 'Maltese',
		'my' => 'Burmese',
		'na' => 'Nauru',
		'nb' => 'Norwegian Bokmål',
		'nd' => 'North Ndebele',
		'ne' => 'Nepali',
		'ng' => 'Ndonga',
		'nl' => 'Dutch',
		'nn' => 'Norwegian Nynorsk',
		'no' => 'Norwegian',
		'nr' => 'South Ndebele',
		'nv' => 'Navajo, Navaho',
		'ny' => 'Chichewa; Chewa; Nyanja',
		'oc' => 'Occitan',
		'oj' => 'Ojibwe, Ojibwa',
		'om' => 'Oromo',
		'or' => 'Oriya',
		'os' => 'Ossetian, Ossetic',
		'pa' => 'Panjabi, Punjabi',
		'pi' => 'Pali',
		'pl' => 'Polish',
		'ps' => 'Pashto, Pushto',
		'pt' => 'Portuguese',
		'qu' => 'Quechua',
		'rm' => 'Romansh',
		'rn' => 'Kirundi',
		'ro' => 'Romanian, Moldavian, Moldovan',
		'ru' => 'Russian',
		'rw' => 'Kinyarwanda',
		'sa' => 'Sanskrit (Saṁskṛta)',
		'sc' => 'Sardinian',
		'sd' => 'Sindhi',
		'se' => 'Northern Sami',
		'sg' => 'Sango',
		'si' => 'Sinhala, Sinhalese',
		'sk' => 'Slovak',
		'sl' => 'Slovene',
		'sm' => 'Samoan',
		'sn' => 'Shona',
		'so' => 'Somali',
		'sq' => 'Albanian',
		'sr' => 'Serbian',
		'ss' => 'Swati',
		'st' => 'Southern Sotho',
		'su' => 'Sundanese',
		'sv' => 'Swedish',
		'sw' => 'Swahili',
		'ta' => 'Tamil',
		'te' => 'Telugu',
		'tg' => 'Tajik',
		'th' => 'Thai',
		'ti' => 'Tigrinya',
		'tk' => 'Turkmen',
		'tl' => 'Tagalog',
		'tn' => 'Tswana',
		'to' => 'Tonga (Tonga Islands)',
		'tr' => 'Turkish',
		'ts' => 'Tsonga',
		'tt' => 'Tatar',
		'tw' => 'Twi',
		'ty' => 'Tahitian',
		'ug' => 'Uighur, Uyghur',
		'uk' => 'Ukrainian',
		'ur' => 'Urdu',
		'uz' => 'Uzbek',
		've' => 'Venda',
		'vi' => 'Vietnamese',
		'vo' => 'Volapük',
		'wa' => 'Walloon',
		'wo' => 'Wolof',
		'xh' => 'Xhosa',
		'yi' => 'Yiddish',
		'yo' => 'Yoruba',
		'za' => 'Zhuang, Chuang',
		'zh' => 'Chinese',
		'zu' => 'Zulu');
	return  isset($iso639[$code]) ? $iso639[$code] : $code ;
		
}


/** 
 *
 * construct html form inserting config values, buttons, and next step.
 * 
 * @param $step next step
 * @param $buttons string with additional html controls.
 *
 * @return string containg complete form
 */



function form_hidden ( $step , $buttons) {
	global $config;
	$form_hidden = "";
	foreach ( $config as $name => $value) {
		$form_hidden .= "\n<input type='hidden' name='$name' value='$value'>";
	}
	$form_hidden = "\n<div class='actions'><form method='post'>%s<input type='hidden' name='step' value='$step'>$form_hidden$buttons</form></div>\n";
	return $form_hidden;
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
		$options .= "<option value='$isoCode'>" . iso639($isoCode) ."</option>\n";
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
			if ( file_exists($AIKI_ROOT_DIR ."/config.php" )  && $step!=5 ) {
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


function clean_url($url){
	$top= strpos( $url, "/assets/apps/installer");
	return ( $top ? substr($url,0,$top) ."/" : $url . "/");
}


/** 
 *
 * send login and password via email
 * 
 * @global $config, $AIKI_SITE_URL, $t
 * 
 * @return false if mail is send else true
 */

function send_data_by_email(){
	global $config, $AIKI_SITE_URL, $t;
	
	if (!$config['ADMIN_EMAIL'] ||
	    !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $config['ADMIN_EMAIL'])){
			return false;
	}
	
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/plain; charset=utf-8\r\n";
	$headers .= "From: noreply@aikiframework.org\r\n";

	$message = $t->t("Hello"). "  {$config['ADMIN_FULLNAME']} \n".
	           $t->t("Your new Aiki installation is ready to be used"). "\n\n".
			   $t->t("Go to") .  ": $AIKI_SITE_URL/admin \n".
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
	$htaccess_file = @file_get_contents("$aikiPath/configs/htaccess.inc");
	if ( $htaccess_file == false ){				
		return false;
	}	
	return str_replace( "@AIKI_REWRITE_BASE@", clean_url($_SERVER["REQUEST_URI"]), $htaccess_file);
	
}


/** 
 *
 * Read all sql file, making some replacemnets
 * 
 * @global $config, $AIKI_ROOT_DIR, $AIKI_SITE_URL, $AIKI_AUTHORS
 * 
 * @return array of SQLS statments
 */

function sqls(){
	global $config, $AIKI_ROOT_DIR, $AIKI_SITE_URL, $AIKI_AUTHORS ;

	$SQLS_DELIMITER = "-- ------------------------------------------------------";
	
	$config["ADMIN_PASSWORD"]        = substr(md5(uniqid(rand(),true)),1,8);
	$config["ADMIN_PASSWORD_MD5_MD5"]= md5(md5($config["ADMIN_PASSWORD"]));

    $replace = array ( 
		"@AIKI_SITE_URL_LEN@"=> strlen($AIKI_SITE_URL),
		"@AIKI_SITE_URL@"    => $AIKI_SITE_URL,
		"@PKG_DATA_DIR_LEN@" => strlen($AIKI_ROOT_DIR),
		"@PKG_DATA_DIR@"     => $AIKI_ROOT_DIR, 
		"@ADMIN_USER@"=> $config["ADMIN_USER"],
		"@ADMIN_NAME@"=> $config["ADMIN_FULLNAME"],
		"@ADMIN_PASS@"=> $config["ADMIN_PASSWORD_MD5_MD5"],
		"@ADMIN_MAIL@"=> $config["ADMIN_EMAIL"],
		"@VERSION@"   => AIKI_VERSION,
		"@REVISION@"  => AIKI_REVISION,
		"@AUTHORS@"   => $AIKI_AUTHORS);
	
	$files = array (
		"$AIKI_ROOT_DIR/sql/CreateTables.sql",
		"$AIKI_ROOT_DIR/sql/InsertDefaults.sql",
		"$AIKI_ROOT_DIR/sql/InsertVariable-in.sql",
		"Site.sql");
			
	foreach ($files as $file ){
		if ( file_exists($file) ){
			$ret.= $SQLS_DELIMITER . "\n". @file_get_contents($file) ;
		}
	}
	
	return explode($SQLS_DELIMITER, strtr ($ret, $replace));
	// note: files can contain sql_delimeters,
}
