<?php

/** Aiki Framework (PHP)
 *
 * Updater utility library.
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
 */

/** @see UpdaterInterface.php */
require_once("libs/updater/UpdaterInterface.php");

/** @see File.php */
require_once("libs/File.php");

/** @see Upgrade.php */
require_once("libs/Upgrade.php");

class Updater implements UpdaterInterface {

    // use these to test the result of checkAvailability
    const UNAVAILABLE = 0;
    const AVAILABLE = 1;
    const REQUIREMENT_NOT_MET = 2;

    // use these to parse the URL update data
    const DELIMIT_FIELD = " ";
    const DELIMIT_LINE = "\n";

    // exception messages
    const ERROR_FOPEN_URL = "Allow fopen(URL) is disabled";
    const ERROR_PARSE_UPDATE = "Failed to parse update data";
    const ERROR_GET_UPDATE_DATA = "Failed to get update data";
    const ERROR_EXTENSION_MYSQLI = "Extension mysqli is not loaded";

    /** Updater extender */
    protected $_extender = "FileUpdater";

    /** Extender constructor arguments */
    protected $_extenderArguments = Array();

    /** The update data as parsed from the update URL */
    protected $_updateData = Array();

    /** The currently installed data */
    protected $_installedData = Array();

    /** File object utility library */
    protected $_File = "";

    /** Upgrade object utility library */
    protected $_Upgrade = "";

    /** Constructs a new Updater. This should
     * look like: $updater = new Updater(Array("File" => new File()));
     * @param array $deps The dependencies of the Updater.
     * @return void
     * @throws AikiException */
    public function __construct(Array $deps = NULL) {
        if (isset($deps)) {
            foreach ( $deps as $key => $val ) {
                $dep = "_" . $key;
                if ( isset($this->$dep) and $val instanceof $key ) {
                   $this->$dep = $val;
                }
                else {
                    throw new AikiException("Invalid dependency " . $key);
                }
            }
        }
    }

    /** Perform an update
     * @return boolean $result Whether or not the update succeeded */
    public function update() {
        $result = $this->getExtender()->update();
        return $result;
    }

    /** Get the contents of a remote file.
     * @param string $url URL to remote contents
     * @return string|boolean $contents Contents of the file or FALSE on failure
     * @throws AikiException */
    public function getRemoteContents($url) {
        if ($this->_File instanceof File) {
            $contents = $this->_File->getRemoteContents($url);
            if ( false === $contents ) {
                if (ini_get("allow_url_fopen")) {
                    $contents = file_get_contents($url);
                }
                else {
                    throw new AikiException(self::ERROR_FOPEN_URL);
                }
            }
        }
        else {
            throw new AikiException("Invalid File instance.");
        }
        return $contents;
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
        if ( $this->_extender instanceof UpdaterInterface ) {
            return $this->_extender;
        }
        $extender = $this->_extender;
        $arguments = $this->getExtenderArguments();
        if ( false === class_exists($extender) ) {
            require_once("libs/updater/" . $extender . ".php");
        }
        $this->_extender = new $extender($arguments);
        if ( false === $this->_extender instanceof UpdaterInterface ) {
            throw new AikiException("This $extender must implement UpdaterInterface");
        }
        return $this->_extender;
    }

    /** Sets Updater extender
     * @param  UpdaterInterface $extender Extender to use
     * @return Updater */
    public function setExtender($extender) {
        if ( $extender instanceof UpdaterInterface ) {
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
    public function setExtenderArguments(array $arguments) {
        $this->_extenderArguments = $arguments;
    }

    /** Get the update data
     * @return array $_updateData*/
    public function getUpdateData() {
        return $this->_updateData;
    }

    /** Get the installed data
     * @return array $_installedData */
    public function getInstalledData() {
        return $this->_installedData;
    }

    /** Check for available update
     * @param string $version The installed package version
     * @param string $url URL containing update information
     * @return int $available Whether or not an update is available */
    public function checkForUpdate($version, $url) {
        $available = self::UNAVAILABLE;
        $this->_installedData["aiki"] = $version;
        $this->_updateData = $this->_parseUpdateData($url);
        if ($this->_isNewerVersion($this->_updateData, $version)) {
            $available = self::AVAILABLE;
            if ( false === $this->_areRequirementsMet($this->_updateData) ) {
                $available = self::REQUIREMENT_NOT_MET;
            }
        }
        return $available;
    }

    /** Parse the update data
     * @param string $url URL to update information and requirements
     * @return array $data Contians latest update version
     * and the minimum requirements for the update
     * @throws AikiException */
    protected function _parseUpdateData($url) {
        $data = Array();
        $contents = $this->getRemoteContents($url);
        if ($contents) {
            $lines = explode(self::DELIMIT_LINE, $contents);
            if ($lines) {
                foreach ( $lines as $line ) {
                    $fields = explode(self::DELIMIT_FIELD, $line);
                    if ( $fields and 2 <= count($fields) ) {
                        $key = strtolower($fields[0]);
                        $val = $fields[1];
                        $data["$key"] = $val;
                    }
                }
            }
            else {
                throw new AikiException(self::ERROR_PARSE_UPDATE);
            }
        }
        else {
            throw new AikiException(self::ERROR_GET_UPDATE_DATA);
        }
        return $data;
    }

    /** Is the available update version newer than the current installation
     * @param array $data Contians latest update version
     * and the minimum requirements for the update
     * @param string $version The installed aiki version
     * @return boolean $newer Whether or not the version is newer
     * @throws AikiException */
    protected function _isNewerVersion($data, $version) {
        $newer = false;
        if (array_key_exists("aiki", $data)) {
            $newer = version_compare($version, $data["aiki"], '<');
        }
        else {
            throw new AikiException("Missing update data key aiki");
        }
        return $newer;
    }

    /** Check whether or not the minimum requirements are met
     * @param array $data Contians latest update version
     * and the minimum requirements for the update
     * @return boolean $newer Whether or not the requirements are met
     * @throws AikiException */
    protected function _areRequirementsMet($data) {
        $met = true;
        $this->_installedData["php"] = phpversion();
        if (extension_loaded("mysqli")) {
            $this->_installedData["mysql"] = mysqli_get_client_info();
        }
        else {
            throw new AikiException(self::ERROR_EXTENSION_MYSQLI);
        }
        $this->_installedData["apache"] = $this->_getApacheVersion();

        foreach ($this->_installedData as $key => $val) {
            if ( !( $key === "aiki" ) and !( $key === "suphp" ) ) {
                if (array_key_exists($key, $data)) {
                    $compatible = version_compare($val, $data[$key], '>=');
                    if ( false === $compatible ) {
                        $met = false;
                    }
                } else {
                    $met = false;
                    throw new AikiException("Missing update data key " . $key);
                }
            }
        }
        if (!$this->_isSuPhpReady()) {
            $met = false;
        }
        return $met;
    }

    /** Get whether suPHP is running
     * @return boolean $ready Whether suPHP is running */
    protected function _isSuPhpReady() {
        $ready = false;
        if (array_key_exists("SUPHP_URI", $_SERVER)
            and isset($_SERVER["SUPHP_URI"])) {
                $ready = true;
        }
        return $ready;
    }

    /** Get the installed Apache version
     * @return string $version the installed Apache version
     * @throws AikiException */
    protected function _getApacheVersion() {
        $version = explode(" ",getenv("SERVER_SOFTWARE"),2);
        /* Do NOT use apache_get_version() which fails under suPHP */
        if (isset($version[0])) {
            // remove all characters that are not a number or dot
            $pattern = '/[^0-9\.]*/';
            $version = preg_replace($pattern, '', $version[0]);
        }
        return $version;
    }
}

?>