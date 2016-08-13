<?php

/** Aiki Framework (PHP)
 *
 * SessionInterface is the interface that all AikiSessions must implement.
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

interface SessionInterface {

    /** Callback open function, this works like a constructor in classes and is
     * executed when the session is being opened. The open function expects
     * two parameters, where the first is the save path and the
     * second is the session name.
     * @param string $path Save path
     * @param string $name Session name
     * @return boolean */
    public function _open($path, $name);

    /** Callback close function, this works like a destructor in classes and is
     * executed when the session operation is done. As of PHP 5.0.5 the write
     * and close handlers are called after object
     * destruction and therefore cannot use objects or throw exceptions.
     * The object destructors can however use sessions.
     * It is possible to call session_write_close() from the destructor
     * to solve this chicken and egg problem.
     * @return boolean */
    public function _close();

    /** Callback read function must return string value always to make save
     * handler work as expected. Return empty string if there is no data to read
     * Return values from other handlers are converted to boolean expression.
     * TRUE for success, FALSE for failure.
     * @param string $id Session ID
     * @return string $data Session data */
    public function _read($id);

    /** Callback write function that is called when session data is to be saved.
     * This function expects two parameters: an identifier and the data
     * associated with it. The "write" handler is not executed until after
     * the output stream is closed. Thus, output from debugging statements in
     * the "write" handler will never be seen in the browser. If debugging
     * output is necessary, it is suggested that the debug
     * output be written to a file instead.
     * @param string $id Session ID
     * @param string $data Session data
     * @return boolean */
    public function _write($id, $data);

    /** Callback destroy handler, this is executed when a session is destroyed
     * with session_destroy() and takes the session id as its only parameter.
     * @param string $id Session ID
     * @return boolean */
    public function _destroy($id);

    /** Callback garbage collector, this is executed when the session garbage
     * collector is executed and takes the max session lifetime as its
     * only parameter.
     * @param integer $max Maximum session lifetime
     * @return boolean */
    public function _collect($max);

    /** Reset this session ID
     * @return boolean */
    public function resetId();
}