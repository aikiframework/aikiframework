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
 * Handle sql inside of aiki. 
 *
 * @category    Aiki
 * @package     Library
 *
 * @todo this entire class needs a code review
 */
class sql_markup
{
    /**
     * Handle Aiki sql.
     * 
     * @param   string  $text   text for processing
     * @global  aiki    $aiki   global aiki instance
     * @global  array   $db     global db instance
     * @return  string 
     *
     * @todo need a serious security review
     */
	public function sql($text)
    {
		global $aiki, $db;

		$count_innersql = preg_match_all('/\(sql\((.*)\)sql\)/Us', $text, 
                                         $sqlmatches);
		if ($count_innersql > 0)
        {
			foreach ($sqlmatches[1] as $match)
			{
				$html_output = $this->sql_query($match);

				$text = preg_replace('/\(sql\('.
                        preg_quote($match, '/').'\)sql\)/Us', 
                        $html_output, $text);
			}
		}
		$text = preg_replace('/\(select(.*)\)/Us', '', $text);
		return $text;
	}

    /**
     * Another form of aiki sql query
     * @param   string  $match  a string to match
     * @global  aiki    $aiki   global aiki instance
     * @global  array   $db     global db instance
     */
	public function sql_query($match)
    {
		global $aiki, $db;

		$match = $aiki->url->apply_url_on_query($match);

		$html_output = '';
		$count_sql = preg_match_all('/\((.*)| (?R)\)/s', $match, $matches);

		$match = '';

		foreach ($matches[1] as $sql)
		{

			$sql_html = explode("||", trim($sql));

			$sql_html[1] = str_replace($sql_html[0]."||", '', $sql);

			$sql_query = $sql_html[0];

			if ($sql_query)
            {
				$sql_query = str_replace("\'", "'", $sql_query);
				$sql_query = str_replace('\"', '"', $sql_query);

				$results = $db->get_results($sql_query);

				if ($results)
                {
					foreach ($results as $result)
                    {
						$html = trim($sql_html[1]);
						if (!preg_match('/\(select(.*)/', $html))
							$html = substr($html,0,-1);

						$result = $aiki->aiki_array->object2array($result);
						$result_key = @array_flip($result);

						foreach ($result as $field)
                        {
							if (isset($result_key[$field]))
                            {
								$html = str_replace("[-[".$result_key[$field].
                                                    "]-]", $field,  $html);
							}
						}

						$match .= $html;
                        
                        /**
                         * @todo investigate why these are hidden, kill or keep
                         */
						//$match = str_replace("\r", ' ', $match);
						//$match = str_replace("\n", ' ', $match);
						$match = preg_replace("/\)\s\s+\(/", '(', $match);

						$match .= $this->sql_query($html);
					} // loop over results
				} // if results
			} // if query
		} // loop over matches
		return $match;
	} // end of function
} // end of class
