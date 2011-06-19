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
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


/**
 * A class to handle errors.
 *
 * @category    Aiki
 * @package     Library
 *
 * @todo actually implement a real error handling class.
 * @todo rename class to Errors
 */
class errors
{

	/**
	 * Handle a page not found error (404).
	 *
	 * Handles a 404 and checks if config option is set to register errors. If
	 * it is, then also check if any redirects are in place. If the error is
	 * a redirect, then redirect, otherwise return error message.
	 *
     * @param   array   $db     global db instance
     * @param   aiki    $aiki   global aiki instance
     * @param   array   $config global config instance
	 * @return  mixed
	 */
	public function page_not_found()
    {
		global $db, $aiki, $config;

		Header("HTTP/1.1 404 Not Found");

        /**
         * @todo actually handle translating the page name
         */
		$aiki->output->set_title("404 Page Not Found");

		if (isset($config["register_errors"]))
        {
			$request = $_SERVER['REQUEST_URI'];
			$request = urldecode($request);

			$check_request = 
            $db->get_row("SELECT * FROM aiki_redirects where url='$request'");

			if (isset($check_request->url))
            {
				$update_hits = 
                $db->query(
                "update aiki_redirects set hits=hits+1 where url='$request'");

				if ($check_request->redirect)
                {
					$catch_patterns[$check_request->url] = 
                        $check_request->redirect;
					$catch_regex = 
                    '#((' . implode('|', array_keys($catch_patterns)) . '))#i';

					if ( preg_match($catch_regex, 
                                    urldecode($_SERVER['REQUEST_URI']), 
                                    $caught) ) 
					{
						$redir = $catch_patterns[$caught[1]];

						Header("Location: $redir", false, 301);
						exit;
					}
				}

			} else {
				$add_e = $db->query(
					"insert into aiki_redirects values ('$request', '', '1')");
			}
		}
		return $config['error_404'];

	} // end of page_not_found function

} // end of Errors class
