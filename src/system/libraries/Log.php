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
 *
 * A log utility to trace errors, warnings, info and debug messages
 * and save them into a log file readable through the admin interface.
 */

// disable php script access
if(!defined('IN_AIKI')) { die(); }

class Log {
	// these should be used to specify the log message level
	const ERROR = E_USER_ERROR;
	const WARN = E_USER_WARNING;
	const INFO = E_USER_NOTICE;
	const DEBUG = E_USER_DEPRECATED;
	/**
	 * string _allow Used to specify at what level of messages to log
	 */
	private $_allow = "NONE";
    /**
     * string _dateFormat Used to specify format of date and time
     */
    private $_dateFormat = "Y m d H:i:s";
    /**
     * resource _stream The log file handle
     */
    private $_stream;
    /**
     * string _path The full path including directory and file name of the log
     */
    private $_path;
    /**
     * Class constructor which creates a new log
     * 
     * @param string dir The log directory
     * @param string file The log file name
     * @param string level Used to specify at what level of messages to log
     * @return void
     */
    public function __construct($dir, $file, $level) {
        $this->_path = $dir . "/" . $file;
        $this->_allow = strtoupper($level);
        date_default_timezone_set("UTC");
        $levelNo = $this->_getLevelNumber($level);
        // if level is NONE, disable the log
    	if ($this->_isAllowed($levelNo)) {
	    	$this->_stream = fopen($this->_path, 'a+t');
        }
        set_error_handler(array($this, '_handler'));
    }
    /**
     * Class destructor which closes the log file
     * 
     *  @return void
     */
    public function __destruct() {
    	$this->_close();
    }
    /**
     * Allows or disallows a log message
     * 
     * @param int errno The current message level
     * @return boolean allowed Whether or not to allow
     */
    private function _isAllowed($errno) {
    	$allowed = false;
    	$level = $this->_getLevelString($errno);
    	switch (true) {
            case ("ERROR" === $this->_allow && $level === "ERROR"):
            	$allowed = true;
                break;
            case ("WARN" === $this->_allow && ($level === "ERROR" || $level === "WARN")):
                $allowed = true;
                break;
            case ("INFO" === $this->_allow && ($level === "ERROR" || $level === "WARN" || $level === "INFO")):
                $allowed = true;
                break;
            case ("DEBUG" === $this->_allow && ($level === "ERROR" || $level === "WARN" || $level === "INFO" || $level === "DEBUG")):
                $allowed = true;
                break;
    		case ("NONE" === $this->_allow):
    			break;
    		default :
    			break;
    	}
    	return $allowed;
    }
    /**
     * Format a log message
     * 
     * @param string message The message
     * @param array data The context of the message
     * @param int level The message level
     * @return string message The formated message
     */
    private function _format($message, $data, $level) {
        $message = "[" . date($this->_dateFormat) . "] " .
            "[". $this->_getLevelString($level) . "] " .
            $message . " " .
            "in ". $data['file'] . " " .
            "on line ". $data['line'] . " " .
            PHP_EOL;
    	return $message;
    }
    /**
     * Log a message
     * 
     * @param string message The message to log
     * @param int level The log level to use
     * @param array data The context data such as file and line number
     * @return void
     */
    public function message($message,
                            $level = Log::DEBUG,
                            $data = NULL) {
        if ($this->_isAllowed($level)) {
	        // get the message context data if necessary
	        if ($data == NULL) {
	        	$trace = debug_backtrace();
	        	$data = current($trace);
	        }
	        // format the message
	        $message = $this->_format($message, $data, $level);
	        // write the message
	        $this->_write($message);
        }
    }
    /**
     * Get a integer representation of the log level from a string.
     * 
     * @param string The log level as a string
     * @return int The log level number
     */
    private function _getLevelNumber($level) {
    	$number = 0;
        switch (true) {
            case ("ERROR" === $level) :
                $number = Log::ERROR;
                break;
            case ("WARN" === $level) :
                $number = Log::WARN;
                break;
            case ("INFO" === $level) :
                $number = Log::INFO;
                break;
            case ("DEBUG" === $level) :
                $number = Log::DEBUG;
                break;
            default :
                $number = Log::ERROR;
                break;
        }
        return $number;
    }
    /**
     * Get a string representation of the log level. Although
     * Some of the errors below may never reach our custom
     * error handler, they are included here for completeness.
     * 
     * @param int The error number
     * @return string The log level
     */
    private function _getLevelString($errno) {
    	$level = "NONE";
    	switch ($errno) {
    		case E_ERROR :
            case E_PARSE :
            case E_CORE_ERROR :
            case E_COMPILE_ERROR :
            case E_USER_ERROR :
            case E_RECOVERABLE_ERROR :
                $level = "ERROR";
                break;
            case E_WARNING :
            case E_CORE_WARNING :
            case E_COMPILE_WARNING :
            case E_USER_WARNING :
            case E_STRICT :
            case E_DEPRECATED :
                $level = "WARN";
                break;
    		case E_NOTICE :
            case E_USER_NOTICE :
    			$level = "INFO";
    			break;
            case E_USER_DEPRECATED :
            default :
                $level = "DEBUG";
                break;
    	}
    	return $level;
    }
    /**
     * Handle an error as a log message. This must be public
     * for use as a callback and is not ment to be called otherwise.
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     * @return boolean
     * @link http://www.php.net/manual/en/function.set-error-handler.php
     */
    public function _handler($errno, $errstr, $errfile, $errline, $errcontext) {
        $errorLevel = error_reporting();

        if ($errorLevel && $errno) {
            $this->message($errstr,
                    $errno,
                    array('file'=>$errfile,
                        'line'=>$errline,
                        'context'=>$errcontext));
        }
        return false;
    }
    /**
     * Get the contents of the log
     * 
     * @return mixed The log file contents or FALSE on failure.
     */
    public function getContents() {
    	return fread($this->_stream, filesize($this->_path));
    }
    /**
     * Close the file resource.
     *
     * @return void
     */
    private function _close()
    {
        if (is_resource($this->_stream)) {
            fclose($this->_stream);
        }
    }
    /**
     * Write a message to the log.
     *
     * @param  string $message The message to write to the log
     * @return void
     */
    private function _write($message)
    {
    	// do NOT suppress errors here which should
    	// otherwise be logged by the web server
        fwrite($this->_stream, $message);
    }    
}
