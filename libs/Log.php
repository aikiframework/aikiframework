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
 * @category    Aiki
 * @package     Library
 * @filesource */

// disable php script access
if (!defined("IN_AIKI")) {
    die("No direct script access allowed");
}

class Log {

    static $levels = array(
         0                =>"NONE",
         E_USER_ERROR     =>"ERROR",
         E_USER_WARNING   =>"WARN",
         E_USER_NOTICE    =>"INFO",
         E_USER_DEPRECATED=>"DEBUG");

    // these should be used to specify the log message level
    const NONE  = 0;
    const ERROR = E_USER_ERROR;
    const WARN  = E_USER_WARNING;
    const INFO  = E_USER_NOTICE;
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

    /** string $_path Full for aplication */
    protected $_root;


    /** Class constructor which creates a new log
     * @param string $dir The log directory
     * @param string $file The log file name
     * @param string $level Used to specify at what level of messages to log
     * @paran string $root application root
     * @return void */
    public function __construct($dir="", $file="", $level="NONE", $root=NULL) {

        if ( is_null($root) && !$file && $level=="NONE"){
            $root= $dir;
            $dir="";
        }

        $this->_root = $root;

        // date_default_timezone_set requires PHP 5.1 or greater
        date_default_timezone_set(@date_default_timezone_get());

        $levelCode = (int) array_search ( strtoupper($level), Log::$levels );
        $this->_open($dir,$file,$levelCode);

        set_error_handler(array($this, "_handler"));
    }

    private function _open($dir,$file,$levelCode){
        global $AIKI_ROOT_DIR;

        $this->_path= "";
        // correct relative path.
        if ( $dir &&  $dir[0]!="/" ){
            $dir =  $this->_root ."/$dir";
        }

        $this->_allow = $levelCode ;
        if ( $this->_allow && $this->_isDir($dir, $file) ) {
            $this->_path = "$dir/$file";
            $this->_stream = fopen( $this->_path, $this->_mode);
        } else {
            // when dir is not writable turn off logs
            $this->_allow = 0;
        }
    }

    /** To change log setting
     * @param string $dir The log directory
     * @param string $file The log file name
     * @param string $level Used to specify at what level of messages to log
     * @return void */

    public function change ($dir,$file,$level) {
        $levelCode =  (int) array_search ( strtoupper($level), Log::$levels );
        if ( $dir &&  $dir[0]!="/" ){
            $path=  $this->_root ."/$dir/$file";
        } else {
            $path=  "$dir/$file";
        }

        if ( $levelCode != $this->_allow || $this->_path != $path ){
            $this->_close();
            $this->_open($dir,$file,$levelCode);
        }
    }


    /** Class destructor which closes the log file
     * @return void */
    public function __destruct() {
        $this->_close();
    }


    /** Attempt to make the log directory if it does not exist
     * @param string $dir The log directory
     * @return boolean $result True when directory exists or is created */
    protected function _isDir($dir,$file) {
        if ( $dir=="" || $file=="" ) {
            return false;
        }

        if (!is_dir($dir)) {
            return @mkdir($dir, 0700);
        } elseif ( file_exists( "$dir/$file")) {
            return is_writable("$dir/$file");
        } else {
            return is_writable("$dir");
        }
    }


    /** Format a log message
     * @param string $message The message
     * @param array $data The context of the message
     * @param int $level The message level
     * @return string $message The formated message */
    protected function _format($message, $data, $level) {
        $message = "[" . date($this->_dateFormat) . "] " .
            "[" . Log::$levels[$level]  . "] " .
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
        $logLevel= $this->_isAllowed($level);
        if ($logLevel) {
            // get the message context data if necessary
            if ( $data == NULL ) {
                $trace = debug_backtrace();
                $data = current($trace);
            }
            // format the message
            $message = $this->_format($message, $data, $logLevel);
            // write the message
            $this->_write($message);
        }
    }


    /** Allows or disallows a log message
     * @param int $errno The current message level
     * @return boolean $allowed Whether or not to allow */

    protected function _isAllowed($errno) {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
                return ( $this->_allow == Log::ERROR ? LOG::ERROR : 0 );

            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
            case E_STRICT:
            case E_DEPRECATED:
                return ($this->_allow == Log::ERROR || $this->_allow == Log::WARN ? Log::WARN: 0);

            case E_NOTICE:
            case E_USER_NOTICE:
                return ($this->_allow == Log::ERROR || $this->_allow == Log::WARN ||
                        $this->_allow == Log::INFO ? Log::INFO : 0 );

            case E_USER_DEPRECATED:
                return ( $this->_allow == Log::ERROR || $this->_allow == Log::WARN ||
                         $this->_allow == Log::INFO  || $this->_allow == Log::DEBUG ? Log::DEBUG : 0);

        }
        return false;
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
        if ( $this->_allow == Log::NONE ){
            return "NONE";
        }

        $contents = file_get_contents($this->_path);
        if ( false === $contents ) {
            return "CANT'T OPEN LOG FILE";
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
