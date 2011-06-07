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
 * BriefDescription
 *
 * @category    Aiki
 * @package     Library
 */
class sql_markup
{

	public function sql($text){
		global $aiki, $db;


		$count_innersql = preg_match_all('/\(sql\((.*)\)sql\)/Us', $text, $sqlmatches);

		if ($count_innersql > 0){

			foreach ($sqlmatches[1] as $match)
			{

				$html_output = $this->sql_query($match);

				$text = preg_replace('/\(sql\('.preg_quote($match, '/').'\)sql\)/Us', $html_output, $text);
			}

		}
		
		$text = preg_replace('/\(select(.*)\)/Us', '', $text);

		return $text;
			
	}


	public function sql_query($match){
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

			if ($sql_query){

				$sql_query = str_replace("\'", "'", $sql_query);
				$sql_query = str_replace('\"', '"', $sql_query);

				$results = $db->get_results($sql_query);

				if ($results){

					foreach ($results as $result) {

						$html = trim($sql_html[1]);
						if (!preg_match('/\(select(.*)/', $html)){
							$html = substr($html,0,-1);
						}

						$result = $aiki->aiki_array->object2array($result);
						$result_key = @array_flip($result);

						foreach ($result as $field){
							if (isset($result_key[$field])){
								$html = str_replace("[-[".$result_key[$field]."]-]", $field,  $html);
							}
						}

						$match .= $html;

						//$match = str_replace("\r", ' ', $match);
						//$match = str_replace("\n", ' ', $match);
						$match = preg_replace("/\)\s\s+\(/", '(', $match);

						$match .= $this->sql_query($html);

					}

				}
			}



		}


		return $match;
	}

}
