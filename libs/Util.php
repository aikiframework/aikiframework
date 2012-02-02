<?php
/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Jon Phillips, Roger Martin
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
	 * return english name of iso6391 code
	 * 
	 * @param string iso code
	 * @return english name or code.
	 * 
	 */

	public static function iso639($code) {	
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
	 * Returns entries of changelog that are greater that given revision
     *
     * @param integer $currentRevision
     * @return string html
	 */

	public static function get_changelog($currentRevision) {
		global $AIKI_ROOT_DIR;        
		$ret = "No changelog information";
		$fileWithChanges = "$AIKI_ROOT_DIR/configs/changelog.php";
		if ( file_exists($fileWithChanges) ){
			include_once($fileWithChanges); 
			// Note: create a array $changes with revision=>text.
			$ret="";
			foreach ( $changes as $revision => $text ){
				if ( $currentRevision < $revision ){
					$ret .= "<div><strong>$revision:</strong> $text</div>\n";
				}
			}
		}	
		return $ret;		
	}	

	/**
	 * Returns the last revision of aiki if .bzr exists, or 0 assuming this
     * is a release, since .bzr is stripped out.
     *
     * @return number
	 */

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
