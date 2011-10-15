<?php

/** Aiki Framework (PHP)
 *
 * File utility library.
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
if (!defined("IN_AIKI")) {
	die("No direct script access allowed");
}

/** @see TransferInterface.php */
require_once("libs/file/TransferInterface.php");

class File implements TransferInterface {
	
	/** File extender */
	protected $_extender = "CurlTransfer";

	/** Extender constructor arguments */
	protected $_extenderArguments = Array();
	
	/** string $_root The root directory of this application */
	protected $_root;
	
	/** Constructs a new File */
	public function __construct($root) {
		$this->_root = $root;
	}
	
	/** Attempt to make a directory if it does not exist
	 * @param string $dir The directory to make
	 * @param string $mode Set file mode (as in chmod). Four octal digits (0-7)
	 * @return void
	 * @throws AikiException */
	public function makeDir($dir, $mode = "0700") {
		if (!is_dir($dir)) {
			if (!is_dir($this->_root . "/" . $dir)) {
				$success = mkdir($dir, octdec($mode), true);
				if ( false === $success ) {
					throw new AikiException("Failed to make directory " . $dir);
				}
			}
		}
	}
	
	/** Get mode/permissions of a file or directory
	 * @param string $target File or directory to get mode/permissions of
	 * @return string $result Octal mode/permissions
	 * or FALSE on failure
	 * @throws AikiException
	 */
	public function getMode($target) {
		$mode = fileperms($target);
		if ($mode) {
		   $mode = substr(sprintf('%o', $mode), -4);
		}
		else {
			throw new AikiException("Failed to get mode from " . $target);
		}
		return $mode;
	}
	
	/** Remove a file or directory
	 * @param string $target File or directory to remove
	 * @return void
	 * @throws AikiException
	 */
	public function remove($target) {
		/** @todo implement this */
	}
	
	/** Move a file or directory
	 * @param string $target File or directory to move
	 * @param string $destination File or directory to move to
	 * @return void
	 * @throws AikiException */
	public function move($target, $destination) {
		/** @todo implement this */
	}
	
	/** Get this application's root directory
	 * @return string $_root This application's root directory */
	public function getRootDir() {
		return $this->_root;
	}
	
	/** Copy files and directories recursively
	 * @param string $target File or directory to copy
	 * @param string $destination File or directory to copy to
	 * @param array $exclude Target files or directories to exclude
	 * @return void
	 * @throws AikiException */
	public function recurseCopy($target, $destination, Array $exclude = Array()) {
		if (false === in_array('.', $exclude)) {
			$exclude[] = '.';
			$exclude[] = '..';
		}
		if (is_dir($target)) {
			$mode = $this->getMode($target);
			$this->makeDir($destination, $mode);
			$files = scandir($target);
			if ($files) {
				foreach ($files as $file) {
					// skip excluded files
					if (in_array($file, $exclude)) continue;
					if (is_dir($target . DIRECTORY_SEPARATOR . $file)) {
						$this->recurseCopy($target . DIRECTORY_SEPARATOR .
							$file, $destination . DIRECTORY_SEPARATOR .
							$file, $exclude);
					}
					else {
						if (!copy($target . DIRECTORY_SEPARATOR .
								$file, $destination .
								DIRECTORY_SEPARATOR . $file)) {
							throw new AikiException("Failed to copy " . $target);
						}
					}
				}
			} else {
				throw new AikiException("Failed to get files from " . $target);
			}
		} elseif (is_file($target)) {
			if (!copy($target, $destination)) {
				throw new AikiException("Failed to copy " . $target);
			}
		} else {
			throw new AikiException("Invalid target " . $target);
		}
	}
	
	/** Download a file 
	 * @param string $url URL to file download 
	 * @param string $destination The destination to save the download 
	 * @return boolean $success Whether or not the download succeeded
	 * @throws AikiException */
	public function download($url, $destination = NULL) {
		$result = $this->getExtender()->download($url, $destination);
		return $result;
	}
	
	/** Get the contents of a remote file.
	 * @param string $url URL to remote contents
	 * @return string|boolean $contents Contents of the file or FALSE on failure
	 * @throws AikiException */
	public function getRemoteContents($url) {
		$result = $this->getExtender()->getRemoteContents($url);
		return $result;
	}

	/** Call extender methods
	 * @param string $method  the method to call
	 * @param array $arguments the arguments for this method
	 * @return mixed Returns the function result, or FALSE on error */
	public function __call($method, $arguments) {
		$extender = $this->getExtender();
		if (false === method_exists($extender, $method)) {
			throw new AikiException("Failed to find method: $method");
		}
		$result = call_user_func_array(Array($extender, $method), $arguments);
		return $result;
	}

	/** Returns the current extender, instantiating it if necessary
	 * @return string */
	public function getExtender() {
		if ( $this->_extender instanceof TransferInterface ) {
			return $this->_extender;
		}
		$extender = $this->_extender;
		$arguments = $this->getExtenderArguments();
		if ( false === class_exists($extender) ) {
			require_once("libs/file/" . $extender . ".php");
		}
		$this->_extender = new $extender($arguments);
		if ( false === $this->_extender instanceof TransferInterface ) {
			throw new AikiException("This $extender must implement TransferInterface");
		}
		return $this->_extender;
	}

	/** Sets File extender
	 * @param  TransferInterface $extender Extender to use
	 * @return File */
	public function setExtender($extender) {
		if ( $extender instanceof TransferInterface ) {
			$this->_extender = $extender;
			return $this;
		}
		if ( false === is_string($extender) ) {
			throw new AikiException("Invalid type: $extender");
		}
		$this->_extender = $extender;
		return $this;
	}

	/** Get extender arguments
	 * @return array */
	public function getExtenderArguments() {
		return $this->_extenderArguments;
	}

	/** Set extender arguments
	 * @param  array $arguments
	 * @return void */
	public function setExtenderArguments(Array $arguments) {
		$this->_extenderArguments = $arguments;
	}
}

?>