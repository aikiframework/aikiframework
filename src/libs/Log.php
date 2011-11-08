<?php

/** Aiki Framework (PHP)
 *
 * A log utility to trace errors, exceptions, warnings, info and debug messages
 * and save them into a log file readable through the admin interface.
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
 * @category	Aiki
 * @package     Library
 * @filesource */

// disable php script access
if (!defined("IN_AIKI")) {
	die("No direct script access allowed");
}

class Log {
	// these should be used to specify the log message level
	const ERROR = E_USER_ERROR;
	const WARN = E_USER_WARNING;
	const INFO = E_USER_NOTICE;
	const DEBUG = E_USER_DEPRECATED;
	
	/** string $_allow Used to specify at what level of messages to log */
	protected $_allow = "NONE";
	
	/** string $_dateFormat Used to specify format of date and time */
	protected $_dateFormat = "Y m d H:i:s";
	
	/** string $_mode The file mode for opening a log */
	protected $_mode = "a+t";
	
	/** resource $_stream The log file handle */
	protected $_stream;
	
	/** string $_path Full path including directory and file name of the log */
	protected $_path;
	
	/** string $_root The root directory of this application */
	protected $_root;
	
	/** Class constructor which creates a new log
	 * @param string $dir The log directory
	 * @param string $file The log file name
	 * @param string $level Used to specify at what level of messages to log
	 * @param string $root The root directory of this application
	 * @return void */
	public function __construct($dir, $file, $level, $root) {
		$this->_root = $root;
		$this->_path = $dir . "/" . $file;
		// date_default_timezone_set requires PHP 5.1 or greater
		date_default_timezone_set(@date_default_timezone_get());
		$levelNo = $this->_getLevelNumber(strtoupper($level));
		$this->_allow = $this->_getLevelString($levelNo);
		// if level is NONE, disable the log
		if ( $this->_isAllowed($levelNo) and $this->_isDir($dir) ) {
			$this->_stream = fopen($this->_path, $this->_mode);
		}
		set_error_handler(array($this, "_handler"));
	}
	/** Class destructor which closes the log file
	 * @return void */
	public function __destruct() {
		$this->_close();
	}
	/** Attempt to make the log directory if it does not exist
	 * @param string $dir The log directory
	 * @return boolean $result True when directory exists or is created */
	protected function _isDir($dir) {
		$result = true;
		if (!is_dir($dir)) {
			if (!is_dir($this->_root . "/" . $dir)) {
				$result = mkdir($dir, 0700);
			}
		}
		return $result;
	}
	/** Allows or disallows a log message
	 * @param int $errno The current message level
	 * @return boolean $allowed Whether or not to allow */
	protected function _isAllowed($errno) {
		$allowed = false;
		$level = $this->_getLevelString($errno);
		switch (true) {
			case ( "ERROR" === $this->_allow and $level === "ERROR" ):
				$allowed = true;
				break;
			case ( "WARN" === $this->_allow and
				( $level === "ERROR" or $level === "WARN" ) ):
				$allowed = true;
				break;
			case ( "INFO" === $this->_allow and 
				( $level === "ERROR" or $level === "WARN" or $level === "INFO" ) ):
				$allowed = true;
				break;
			case ( "DEBUG" === $this->_allow and
				($level === "ERROR" or $level === "WARN" or $level === "INFO" or $level === "DEBUG" ) ):
				$allowed = true;
				break;
			case ( "NONE" === $this->_allow ):
				break;
			default :
				break;
		}
		return $allowed;
	}
	/** Format a log message
	 * @param string $message The message
	 * @param array $data The context of the message
	 * @param int $level The message level
	 * @return string $message The formated message */
	protected function _format($message, $data, $level) {
		$message = "[" . date($this->_dateFormat) . "] " .
			"[" . $this->_getLevelString($level) . "] " .
			$message . " " .
			"in " . $data["file"] . " " .
			"on line " . $data["line"] . " " .
			PHP_EOL;
		return $message;
	}
	/** Log an exception
	 * @param Exception $exception The exception from a try catch block
	 * @return void */
	public function exception($exception) {
		$data = Array();
		if ( $exception instanceof Exception ) {
			$message = $exception->getMessage();
			$level = $exception->getCode();
			$data["file"] = $exception->getFile();
			$data["line"] = $exception->getLine();
			$this->message($message, $level, $data);
		}
	}
	/** Log a message
	 * @param string $message The message to log
	 * @param int $level The log level to use
	 * @param array $data The context data such as file and line number
	 * @return void */
	public function message($message, $level = Log::DEBUG, $data = NULL) {
		if ($this->_isAllowed($level)) {
			// get the message context data if necessary
			if ( $data == NULL ) {
				$trace = debug_backtrace();
				$data = current($trace);
			}
			// format the message
			$message = $this->_format($message, $data, $level);
			// write the message
			$this->_write($message);
		}
	}
	/** Get a integer representation of the log level from a string.
	 * @param string $level The log level as a string
	 * @return int $number The log level number */
	protected function _getLevelNumber($level) {
		$number = 0;
		switch (true) {
			case ( "ERROR" === $level ):
				$number = Log::ERROR;
				break;
			case ( "WARN" === $level ):
				$number = Log::WARN;
				break;
			case ( "INFO" === $level ):
				$number = Log::INFO;
				break;
			case ( "DEBUG" === $level ):
				$number = Log::DEBUG;
				break;
			default:
				break;
		}
		return $number;
	}
	/** Get a string representation of the log level. Although
	 * Some of the errors below may never reach our custom
	 * error handler, they are included here for completeness.
	 * @param int $errno The error number
	 * @return string $level The log level */
	/*
	 * The following error types cannot be handled with a user
	 * defined function: E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING,
	 * E_COMPILE_ERROR, E_COMPILE_WARNING, and most of E_STRICT raised
	 * in the file where set_error_handler() is called. */
	protected function _getLevelString($errno) {
		$level = "NONE";
		switch ($errno) {
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
				$level = "ERROR";
				break;
			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
			case E_STRICT:
			case E_DEPRECATED:
				$level = "WARN";
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$level = "INFO";
				break;
			case E_USER_DEPRECATED:
				$level = "DEBUG";
				break;
			default:
				$level = "NONE";
				break;
		}
		return $level;
	}
	/** Handle an error as a log message. This must be public
	 * for use as a callback and is not ment to be called otherwise.
	 * @param int $errno The error number
	 * @param string $errstr The error message
	 * @param string $errfile The file name in which the error occured
	 * @param int $errline The line number on which the error occured
	 * @param array $errcontext The error context such as function name
	 * @return boolean False enables the original error handler afterwards
	 * @link http://www.php.net/manual/en/function.set-error-handler.php */
	public function _handler($errno, $errstr, $errfile, $errline, $errcontext) {
		$errorLevel = error_reporting();

		if ( $errorLevel and $errno ) {
			$this->message($errstr,
					$errno,
					array("file"=>$errfile,
						"line"=>$errline,
						"context"=>$errcontext));
		}
		return false;
	}
	/** Get the contents of the log
	 * @return mixed The log file contents, NONE or FALSE on failure. */
	public function getContents() {
		$levelNo = $this->_getLevelNumber($this->_allow);
		$contents = $this->_allow;
		// if level is NONE, disable the log
		if ($this->_isAllowed($levelNo)) {
			$contents = file_get_contents($this->_path);
			if ( false === $contents ) {
				$contents = file_get_contents($this->_root . "/" . $this->_path);
			}
		}
		return $contents;
	}
	/** Insert spans into contents and get the result
	 * @param string $contents The contents of the log
	 * @param string $level The log message level
	 * @param string $category The message element category
	 * @return string $markup The resulting HTML markup */
	protected function _getInsertSpans($contents, $level, $category) {
		$markup = $contents;
		switch (true) {
			case ( "tag" === $category ):
				$markup = preg_replace('/(\[' . $level . '\])/',
					'<span class="log-content-' . $category .
					'-' . strtolower($level) . '">$1</span>', $markup);
				break;
			case ( "line" === $category ):
				$markup = preg_replace('/(.+\[' . $level . '\].+)/',
				   '<span class="log-content-' . $category .
				   '-' . strtolower($level) . '">$1</span>', $markup);
				break;
			default:
				break;
		}
		return $markup;
	}
	/** Get the contents of the log as HTML
	 * @return string $markup The log contents as HTML */
	public function getContentsAsHtml() {
		// get the simple text contents of the log
		$markup = $this->getContents();
		if ( false === $markup ) {
			$markup = "Failed to get the log contents";
		} else {
			// insert message-level-tag spans
			$markup = $this->_getInsertSpans($markup, "ERROR", "tag");
			$markup = $this->_getInsertSpans($markup, "WARN", "tag");
			$markup = $this->_getInsertSpans($markup, "INFO", "tag");
			$markup = $this->_getInsertSpans($markup, "DEBUG", "tag");
			// insert per-line spans
			$markup = $this->_getInsertSpans($markup, "ERROR", "line");
			$markup = $this->_getInsertSpans($markup, "WARN", "line");
			$markup = $this->_getInsertSpans($markup, "INFO", "line");
			$markup = $this->_getInsertSpans($markup, "DEBUG", "line");
		}
		// the root element of this markup is pre-formated text
		$markup = "<pre>" . $markup . "</pre>";
		return $markup;
	}
	/** Close the file resource.
	 * @return void */
	protected function _close() {
		if (is_resource($this->_stream)) {
			fclose($this->_stream);
		}
	}
	/** Write a message to the log.
	 * @param  string $message The message to write to the log
	 * @return void */
	protected function _write($message) {
		// do NOT suppress errors here which should
		// otherwise be logged by the web server
		fwrite($this->_stream, $message);
	}	
}

?>