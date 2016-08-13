<?php

/** Aiki Framework (PHP)
 *
 * AikiSession utility library.
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

/** @see SessionInterface.php */
require_once("libs/session/SessionInterface.php");

/** @link http://www.php.net/manual/en/function.session-set-save-handler.php */
class AikiSession implements SessionInterface {

    /** AikiSession extender */
    protected $_extender = "DatabaseSession";

    /** Extender constructor arguments */
    protected $_extenderArguments = Array();

    /** Constructs a new AikiSession
     * @param string|object $extender AikiSession extender
     * @return void */
    public function __construct($extender = NULL) {
        if (isset($extender)) {
            $this->setExtender($extender);
        }
    }

    /** Callback open function, this works like a constructor in classes and is
     * executed when the session is being opened. The open function expects
     * two parameters, where the first is the save path and the
     * second is the session name.
     * @param string $path Save path
     * @param string $name Session name
     * @return boolean */
    public function _open($path, $name) {
        return $this->getExtender()->_open($path, $name);
    }

    /** Callback close function, this works like a destructor in classes and is
     * executed when the session operation is done. As of PHP 5.0.5 the write
     * and close handlers are called after object
     * destruction and therefore cannot use objects or throw exceptions.
     * The object destructors can however use sessions.
     * It is possible to call session_write_close() from the destructor
     * to solve this chicken and egg problem.
     * @return boolean */
    public function _close() {
        return $this->getExtender()->_close();
    }

    /** Callback read function must return string value always to make save handler
     * work as expected. Return empty string if there is no data to read.
     * Return values from other handlers are converted to boolean expression.
     * TRUE for success, FALSE for failure.
     * @param string $id Session ID
     * @return string $data Session data */
    public function _read($id) {
        return $this->getExtender()->_read($id);
    }

    /** Callback write function that is called when session data is to be saved.
     * This function expects two parameters: an identifier and the data
     * associated with it. The "write" handler is not executed until after
     * the output stream is closed. Thus, output from debugging statements in
     * the "write" handler will never be seen in the browser. If debugging
     * output is necessary, it is suggested that the debug
     * output be written to a file instead.
     * @param string $id Session ID
     * @param string $data Session data
     * @return integer $count Number of rows affected */
    public function _write($id, $data) {
        return $this->getExtender()->_write($id, $data);
    }

    /** Callback destroy handler, this is executed when a session is destroyed with
     * session_destroy() and takes the session id as its only parameter.
     * @param string $id Session ID
     * @return integer $count Number of rows affected */
    public function _destroy($id) {
        return $this->getExtender()->_destroy($id);
    }

    /** Callback garbage collector, this is executed when the session garbage
     * collector is executed and takes the max session lifetime as its
     * only parameter.
     * @param integer $max Maximum session lifetime
     * @return integer $count Number of rows affected */
    public function _collect($max) {
        return $this->getExtender()->_collect($max);
    }

    /** Set the session maximum life time
     * @param integer $seconds Number of seconds to live
     * @return void
     * @throws AikiException */
    public function setLifetime($seconds) {
        // session.gc_maxlifetime integer
        if (!ini_set("session.gc_maxlifetime", $seconds)) {
            throw new AikiException("Failed to set life time " . $seconds);
        }
    }

    /** Reset this session ID
     * @return boolean */
    public function resetId() {
        return $this->getExtender()->resetId();
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
        if ( $this->_extender instanceof SessionInterface ) {
            return $this->_extender;
        }
        $extender = $this->_extender;
        $arguments = $this->getExtenderArguments();
        if ( false === class_exists($extender) ) {
            require_once("libs/session/" . $extender . ".php");
        }
        $this->_extender = new $extender($arguments);
        if ( false === $this->_extender instanceof SessionInterface ) {
            throw new AikiException("This $extender must implement SessionInterface");
        }
        return $this->_extender;
    }

    /** Sets AikiSession extender
     * @param  SessionInterface $extender Extender to use
     * @return AikiSession */
    public function setExtender($extender) {
        if ( $extender instanceof SessionInterface ) {
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