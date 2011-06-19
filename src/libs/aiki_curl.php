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
 * @package     Library
 * @filesource
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


/**
 * A setup class for using curl in Aiki.
 *
 * @category    Aiki
 * @package     Library
 *
 * @todo rename class AikiCurl
 * @todo trace to see if this is used actually, if not, ditch it.
 * @todo should actually implement this as a wrapper to curl in my opinion
 * @todo should test to see if curl is present and useful in php even
 */
class aiki_curl
{

    /**
     * @var integer timeout for curl
     */
	private $timeout    = 3;
    /**
     * @var object  curl instance
     */
	private $ch;
    /**
     * @var string a url to get using curl
     */
	public  $url;
    /**
     * @var array for curl results, appears unused.
     */
	private $properties = array();

    /** 
     * Constructor for AikiCurl looks like setup for curl.
     */
	function __construct() 
    {
		$this->ch = curl_init();
		curl_setopt ($this->ch, CURLOPT_URL, $this->url);
		curl_setopt ($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
	}

} // end of AikiCurl class
