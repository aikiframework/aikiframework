<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      roger martin 
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Library
 *
 * Implementation of a dictionary using tables.
 *
 *
 */


class DictionaryTable{
  private $table, $to, $from, $fake;

  /**
   * Construct a dictionary.
   * 
   * @param $to language to translate
   * @param $from language from translate (optional, "en" by default)
   * @param $table where terms are stored (optional, aiki_dictionaries by default)  
   *
   * @Examples: $diccio = new ( "eu", "en", "plugins_diccio");
   *
   */

  function __construct( $to, $from="en", $table="aiki_dictionaries"  ) {
    $this->table= $table;
    $this->to   = $to;
    $this->from = $from;
    $this->fake = ($to == $from) ;
  }
  
  function search($term){
    global $db;    
    $search = addslashes($term);
    $SQL = 
        "SELECT translation FROM ". $this->table .
        " WHERE term='$search'".
        "  AND translateto='{$this->to}' AND translatefrom = '{$this->from}'";
    $found= $db->get_var($SQL);
    return ( is_null($found) ? false : $found );
  }
  
  /**
   * translate given term
   * 
   * @param $term to translate
   * @return string term translated (if necesary)   
   */  
  
  function translate($term){
    if ($this->fake){
        return $term;
    }
    $founded= $this->search($term);  
    return ( $founded===false ? $term : $founded);
  }
  
  /**
   * return language which terms will be translated 
   * 
   * @return string original language   
   */  
  
  function translateTo(){
    return $this->to;
  }
  
  /**
   * return original language of terms 
   * 
   * @return string original language   
   */    
  
  function translateFrom(){
    return $this->from;
  }
  
}
