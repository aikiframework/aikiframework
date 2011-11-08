<?php

/** Aiki Framework (PHP)
 *
 * Backup utility library.
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

/** @see BackupInterface.php */
require_once("libs/backup/BackupInterface.php");

/** @see DatabaseBackup.php */
require_once("libs/backup/DatabaseBackup.php");

/** @see FileBackup.php */
require_once("libs/backup/FileBackup.php");

class Backup implements BackupInterface {
	
	/** Backup extender */
	protected $_extender = "DatabaseBackup";
	
	/** Backup extenders */
	protected $_extenders = Array();

	/** Extender constructor arguments */
	protected $_extenderArguments = Array();
	
	/** Constructs a new Backup */
	public function __construct(Array $extenders = NULL) {
		if (isset($extenders)) {
			foreach ( $extenders as $key => $val ) {
				if ( $val instanceof $key ) {
				   $this->addExtender($val);
				}
				else {
					throw new AikiException("Invalid instance " . $key);
				}
			}
		}
	}
	
	/** Append a Backup Extender
	 * @param object $extender Backup extender to add
	 * @throws AikiException */
	public function addExtender($extender) {
		if ( $extender instanceof BackupInterface ) {
			$this->_extenders[] = $extender;
		}
		else {
			throw new AikiException("Extender must implement BackupInterface.");
		}
	}
	
	/** Restore a Backup 
	 * @param string $source Directory where the backup is 
	 * @param string $destination Directory to restore the backup in 
	 * @param array $exclude Target files or directories to exclude
	 * @throws AikiException */
	public function restore($source, $destination, Array $exclude = Array() {
		foreach ( $this->_extenders as $extender ) {
			$extender->restore($source, $destination, $exclude);
		}
	}
	
	/** Save a Backup 
	 * @param string $destination Directory to save the backup in 
	 * @return void
	 * @throws AikiException */
	public function save($destination, Array $exclude = Array()) {
		foreach ( $this->_extenders as $extender ) {
			$extender->save($destination, $exclude);
		}
	}

	/** Call extender methods
	 * @param string $method  the method to call
	 * @param array $arguments the arguments for this method
	 * @return mixed Returns the function result, or FALSE on error */
	public function __call($method, $arguments) {
		$extender = $this->getExtender();
		if ( false === method_exists($extender, $method) ) {
			throw new AikiException("Failed to find method: $method");
		}
		$result = call_user_func_array(array($extender, $method), $arguments);
		return $result;
	}

	/** Returns the current extender, instantiating it if necessary
	 * @return string */
	public function getExtender() {
		if ( $this->_extender instanceof BackupInterface ) {
			return $this->_extender;
		}
		$extender = $this->_extender;
		$arguments = $this->getExtenderArguments();
		if ( false === class_exists($extender) ) {
			require_once("libs/backup/" . $extender . ".php");
		}
		$this->_extender = new $extender($arguments);
		if ( false === $this->_extender instanceof BackupInterface ) {
			throw new AikiException(
						"This $extender must implement BackupInterface");
		}
		return $this->_extender;
	}

	/** Sets Backup extender
	 * @param  BackupInterface $extender Extender to use
	 * @return Backup */
	public function setExtender($extender) {
		if ( $extender instanceof BackupInterface ) {
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