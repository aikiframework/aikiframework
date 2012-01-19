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
 * @todo PHPDOC
 */


define ("SQLS_DELIMITER", "-- ------------------------------------------------------");

function iso639($code) {
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
		'nb' => 'Norwegian BokmÃ¥l',
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
		'sa' => 'Sanskrit (Sa?sk?ta)',
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
		'vo' => 'VolapÃ¼k',
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



function form_hidden ( $step , $buttons) {
	global $config;
	$form_hidden = "";
	foreach ( $config as $name => $value) {
		$form_hidden .= "\n<input type='hidden' name='$name' value='$value'>";
	}
	$form_hidden = "\n<div class='actions'><form method='post'>%s<input type='hidden' name='step' value='$step'>$form_hidden$buttons</form></div>\n";
	return $form_hidden;
}

// html templates for controls.
function select_db_type( $db_type ){
	$selectType="<select name='db_type' id='db_type' class='user-input'>\n";
	$options = array (
		"mysql" =>"MySQL",
		"mssql" =>"mssql",
		"oracle" =>"oracle 8 or higher",
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


function check_step(&$step) {
	global $AIKI_ROOT_DIR, $config;

	switch ($step){
		case 4:
		case 5:
			if ( !@mysql_connect ($config['db_host'],  $config['db_user'], $config['db_pass']) ) {
				return  "_t(Error: no connection)" ;
			} elseif ( !@mysql_selectdb ($config['db_name']) ){
				return  "_t(Error: no database selected)";
			}
			if ( $step==5 && !file_exists($AIKI_ROOT_DIR ."/config.php") ){
				$step=4;
			}

		case 2:
		default:
			if ( file_exists($AIKI_ROOT_DIR ."/config.php" )  && $step!=5 ) {
				return  "_t(There is a existing configuration file)<em>_t(Please remove file to continue installation)<br>".
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
				$message ="_t(Essential files missing):<em>$message</em>";
		    }
			return $message;
	}
}


function clean_url($url){
	$top= strpos( $url, "/assets/apps/installer");
	return ( $top ? substr($url,0,$top) ."/" : $url . "/");
}


function send_data_by_email(){
	global $config, $AIKI_SITE_URL, $t;
	
	if (!$config['ADMIN_EMAIL'] ||
	    !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $config['ADMIN_EMAIL'])){
			return false;
	}
	
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=utf-8\r\n";
	$headers .= "From: noreply@aikiframework.org\r\n";

	$message = "_t(Hello) {$config['ADMIN_FULLNAME']} _t(your new Aiki installation is ready to be used) <br>\n".
			   "_t(Go to): " . $AIKI_SITE_URL . "admin <br>\n".
			   "_t(Username): {$config['ADMIN_USER']} <br>\n".
			   "_t(Password): {$config['ADMIN_PASSWORD']}<br>\n".
			   " <br>\n".
			   "_t(Have a nice day)<br>\n";

    // translate.
	$message = preg_replace_callback (
		"/_t\(([^\)]*)\)/",  // all _t(literals)
		array($t,"t"),       // will be translated by $t->t()
		$message );
	mail($config['ADMIN_EMAIL'], $t->t('Your new Aiki installation'),$message,$headers);	
	
	return true;	
	
}

/** 
 *
 * Get new htaccess file from template /configs/htaccess.inc 
 * 
 * @param string aiki root dir
 * 
 * @return false or string htaccess content.
 */

function get_new_htaccess($url){
	$htaccess_file = file_get_contents("$url/configs/htaccess.inc");
	if ( $htaccess_file == false ){				
		return false;
	}	
	$replace= array (	"@AIKI_REWRITE_BASE@" => clean_url($_SERVER["REQUEST_URI"]) );
	$htaccess_file = strtr( $htaccess_file, $replace);
	return $htaccess_file;
}


function sqls(){
	global $config, $AIKI_ROOT_DIR, $AIKI_SITE_URL, $AIKI_AUTHORS ;

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
		"$AIKI_ROOT_DIR/sql/Site.sql");
			
	foreach ($files as $file ){
		if ( file_exists($file) ){
			$ret.= SQLS_DELIMITER . "\n". @file_get_contents($file) ;
		}
	}
	
	return explode(SQLS_DELIMITER, strtr ($ret, $replace));
	// note: files can contain sql_delimeters,
}
