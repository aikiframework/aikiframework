<?php

/** Aiki Framework (PHP)
 *
 * Extends the built-in Exception class
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

/** For more info see the following links
 * @link http://www.php.net/manual/en/language.exceptions.extending.php
 * @link http://www.php.net/manual/en/class.exception.php */
class AikiException extends Exception {
	
	/** Construct extended exception
	 * @param string $message The message is required
	 * @param integer $code The code defaults to user error
	 * @return void */
	public function __construct($message, $code = E_USER_ERROR) {
		parent::__construct($message, (int)$code);
	}
	/* Versions of PHP 5, prior to PHP 5.3.0
	 * do not support nesting of exceptions ($previous). */
}