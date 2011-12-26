<?php

/** Aiki Framework (PHP)
 *
 * CurlTransfer utility library.
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
 * @filesource */

// disable php script access
if(!defined("IN_AIKI")) { die("No direct script access allowed"); }

/** @see TransferInterface.php */
require_once("libs/file/TransferInterface.php");

class CurlTransfer implements TransferInterface {

	/** cURL session resource */
	protected $_curl = false;
    
    /** Constructs a new CurlTransfer
     * @return void
     * @throws AikiException */
    public function __construct() {
        if (extension_loaded("curl")) {
        	$this->_open();
            curl_setopt($this->_curl, CURLOPT_TIMEOUT, 50);
            curl_setopt($this->_curl, CURLOPT_FOLLOWLOCATION, true);
        }
        else {
            throw new AikiException("Extension curl is not loaded.");
        }
    }
    
    /** Destructs a CurlTransfer.
     * Closes this cURL session. */
    public function __destruct() {
        $this->_close();
    }
    
    /** Open the cURL resource.
     * @return void 
     * @throws AikiException */
    protected function _open() {
        // Initialize a cURL session
        $this->_curl = curl_init();
        
        if (false === $this->_curl) {
            throw new AikiException("Failed to initialize cURL.");
        }
    }
    
    /** Close the cURL resource.
     * @return void */
    protected function _close()
    {
        if (!(false === $this->_curl)) {
	        // close the curl resource
	        curl_close($this->_curl);
        }
    }
    
    /** Close a file stream resource.
     * @return void */
    protected function _closeStream($stream)
    {
        if (is_resource($stream)) {
            fclose($stream);
        }
    }
    
    /** Reset the time limit 
     * @param integer $seconds Seconds to timeout in 
     * @return void */
    protected function _setTimeLimit($seconds) {
        /* This is usefull when you are downloading big files, as it
        will prevent time out of the script */
        if(!ini_get("safe_mode")) {
            set_time_limit($seconds);
        }
    }
    
    /** Get remote file contents 
     * @param string $url URL to get contents of 
     * @return string|boolean $contents Contents of the file or FALSE on failure
     * @throws AikiException */
    public function getRemoteContents($url) {
    	$contents = false;
    	// Seconds to timeout in
    	$this->_setTimeLimit(0);
        
        // set the URL
        curl_setopt($this->_curl, CURLOPT_URL, $url);
    	
    	// Indicate that we want the output returned into a variable.
        curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
        
        // Execute the given cURL session. 
		$contents = curl_exec($this->_curl);
		
        if ($contents) {
            return $contents;
        }
        else {
            throw new AikiException("Failed to execute the cURL session.");
        }
    }
    
    /** Download a file 
     * @param string $url URL to file download 
     * @param string $destination The destination to save the download 
     * @return boolean $success Whether or not the download succeeded
     * @throws AikiException */
    public function download($url, $destination = NULL) {
    	
    	$success = false;
    	
    	// Seconds to timeout in
    	$this->_setTimeLimit(0);
	    
	    // when destination not given, default to the base name of the URL
	    if (NULL == $destination) {
	    	$destination = basename($url);
	    }
	    // This is the stream resource to save the contents of the URL
	    $stream = fopen($destination, 'wb+');
	    
	    if ($stream) {
	    	// set the URL
            curl_setopt($this->_curl, CURLOPT_URL, $url);
            
            // pass curl the stream resource
            curl_setopt($this->_curl, CURLOPT_FILE, $stream);
            
            // Execute the given cURL session. 
            if (curl_exec($this->_curl)) {
	            // close the stream resource
	            $this->_closeStream($stream);
	            $success = true;
            }
            else {
                // close the stream resource
                $this->_closeStream($stream);
                throw new AikiException("Failed to execute the cURL session.");
            }
	    }
	    else {
            throw new AikiException("Failed to open " . $destination);
	    }
	    return $success;
    }
}