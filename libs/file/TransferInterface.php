<?php

/** Aiki Framework (PHP)
 *
 * TransferInterface is the interface that all Transfers must implement.
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

interface TransferInterface {

    /** Download a file
     * @param string $url URL to file download
     * @param string $destination The destination to save the download
     * @return boolean $success Whether or not the download succeeded
     * @throws AikiException */
    public function download($url, $destination = NULL);

    /** Get remote file contents
     * @param string $url URL to get contents of
     * @return string|boolean $contents Contents of the file or FALSE on failure
     * @throws AikiException */
    public function getRemoteContents($url);
}