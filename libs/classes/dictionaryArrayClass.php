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


class DictionaryArray{
  private $words, $to, $from;

  function __construct( $words, $from, $to ) {
    $this->words= $words;
    $this->to   = $to;
    $this->from = $from;
  }

  function search($word){
    return ( isset($this->words[$word]) ? $this->words[$word] : false );
  }

  function translate($word){
    $t= search($word);
    return ( $t===false ? $word : $t);
  }

  function translateTo(){
    return $this->to;
  }

  function translateFrom(){
    return $this->from;
  }

}
