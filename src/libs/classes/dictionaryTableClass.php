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
 * Implementation of a dictionary using arrays.
 *
 *
 */


class DictionaryTable{
  private $table, $to, $from, $fake;
  
  function __construct( $to, $table="aiki_dictionaries", $from="en" ) {
    $this->table= $table;
    $this->to   = $to;
    $this->from = $from;
    $this->fake = ($to == $from) ;
  }
  
  function search($term){
    global $db;    
    $SQL = 
        "SELECT translation FROM ". $this->table .
        " WHERE term='$term'".
        "  AND translateto='{$this->to}' AND translatefrom = '{$this->from}'";
    $found= $db->get_var($SQL);
    return ( is_null($found) ? false : $found );
  }
  
  function translate($term){
    if ($this->fake){
        return $term;
    }
    $founded= $this->search($term);  
    return ( $founded===false ? $term : $founded);
  }
  
  function translateTo(){
    return $this->to;
  }
  
  function translateFrom(){
    return $this->from;
  }
  
}
