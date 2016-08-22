<?php

/** Aiki Framework (PHP)
 *
 * DatabaseSession utility library.
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

/** @see SessionInterface.php */
require_once("libs/session/SessionInterface.php");

/** @link http://www.php.net/manual/en/function.session-set-save-handler.php */
class DatabaseSession implements SessionInterface {

    /** database */
    protected $_database = "";

    /** Constructs a new AikiSession
     * @param object $database Database object
     * @return void */
    public function __construct($database) {
        $this->_database = $database;

        /* Sets the user-level session storage functions which
         * are used for storing and retrieving data associated
         * with a session. This is most useful when a storage
         * method other than those supplied by PHP sessions is
         * preferred. i.e. Storing the session data in a local database. */
        session_set_save_handler(Array($this, "_open"),
                                Array($this, "_close"),
                                Array($this, "_read"),
                                Array($this, "_write"),
                                Array($this, "_destroy"),
                                Array($this, "_collect"));

        /* Shutdown functions are called before object destructors.
         * Registers the function named by function to be executed
         * when script processing is complete or when exit() is called. */
        register_shutdown_function('session_write_close');

        // ini_set('session.save_handler', 'user');
    }

    /** Destructs a AikiSession
     * @return void*/
    public function __destruct() {
        /* End the current session and store session data. Session data is
         * usually stored after your script terminated without the need to
         * call session_write_close(), but as session data is locked to
         * prevent concurrent writes only one script may operate on a
         * session at any time. You can reduce the time needed
         * to load all the frames by ending the session as soon as all
         * changes to session variables are done. */
        session_write_close(true);
    }

    /** Open function, this works like a constructor in classes and is
     * executed when the session is being opened. The open function expects
     * two parameters, where the first is the save path and the
     * second is the session name.
     * @param string $path Save path
     * @param string $name Session name
     * @return boolean */
    public function _open($path, $name) {
        return(true);
    }

    /** Close function, this works like a destructor in classes and is
     * executed when the session operation is done. As of PHP 5.0.5 the write
     * and close handlers are called after object
     * destruction and therefore cannot use objects or throw exceptions.
     * The object destructors can however use sessions.
     * It is possible to call session_write_close() from the destructor
     * to solve this chicken and egg problem.
     * @return boolean */
    public function _close() {
        return(true);
    }

    /** Read function must return string value always to make save handler
     * work as expected. Return empty string if there is no data to read.
     * Return values from other handlers are converted to boolean expression.
     * TRUE for success, FALSE for failure.
     * @param string $id Session ID
     * @return string $data Session data */
    public function _read($id) {
        $id = $this->_database->escape($id);
        $sql = sprintf("SELECT session_data FROM aiki_sessions " .
                       "WHERE session_id = '%s'", $id);
        return $this->_database->get_var($sql);
    }

    /** Write function that is called when session data is to be saved.
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
        /*$id = session_id();
        session_write_close();
        session_id($id);
        session_start();*/
        $id = $this->_database->escape($id);
        $data = $this->_database->escape($data);
        $time = $this->_database->escape(time());
        $sql = sprintf("REPLACE INTO aiki_sessions VALUES('%s', '%s', '%s')",
                        $id,
                        $data,
                        $time);
        return (boolean)$this->_database->query($sql);
    }

    /** The destroy handler, this is executed when a session is destroyed with
     * session_destroy() and takes the session id as its only parameter.
     * @param string $id Session ID
     * @return integer $count Number of rows affected */
    public function _destroy($id) {
        $id = $this->_database->escape($id);
        $sql = sprintf("DELETE FROM aiki_sessions WHERE session_id = '%s'",
            $id);
        return $this->_database->query($sql);
    }

    /** The garbage collector, this is executed when the session garbage
     * collector is executed and takes the max session lifetime as its
     * only parameter.
     * @param integer $max Maximum session lifetime
     * @return integer $count Number of rows affected */
    public function _collect($max) {
        // (filemtime($filename) + $maxlifetime < time())
        $sql = sprintf("DELETE FROM aiki_sessions WHERE session_time < '%s'",
            $this->_database->escape(time() - $max));
        return $this->_database->query($sql);
    }

    /** Reset this session ID
     * @return integer $count Number of rows affected */
    public function resetId() {
        $old = session_id();
        session_regenerate_id();
        $new = session_id();
        return $this->_regenerateId($old, $new);
    }

    /** Regenerate a Session ID
     * @return integer $count Number of rows affected */
    protected function _regenerateId($old, $new) {
        $new = $this->_database->escape($new);
        $old = $this->_database->escape($old);
        $sql = sprintf("UPDATE aiki_sessions SET session_id = '%s' " .
                      "WHERE session_id = '%s'", $new, $old);
        return $this->_database->query($sql);
    }
}
