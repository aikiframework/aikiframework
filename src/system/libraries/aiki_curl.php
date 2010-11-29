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
 * @copyright   (c) 2008-2010 Aikilab
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Library
 * @filesource
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


/**
 * BriefDescription
 *
 * @category    Aiki
 * @package     Library
 */
class aiki_curl
{

	private $timeout = 3;
	private $ch;
	public $url;
	private $properties = array();

	function __construct() {

		$this->ch = curl_init();
		curl_setopt ($this->ch, CURLOPT_URL, $this->url);
		curl_setopt ($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

	}


}