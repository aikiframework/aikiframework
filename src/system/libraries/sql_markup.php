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
 * @copyright   (c) 2008-2010 Aikilab
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

		$count_sql = preg_match_all('/\((.*)\)/s', $match, $matches);

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

						$html =  $sql_html[1];

						$result = $aiki->aiki_array->object2array($result);
						$result_key = @array_flip($result);

						foreach ($result as $field){
							if (isset($result_key[$field])){
								$html = str_replace("[-[".$result_key[$field]."]-]", $field,  $html);
							}
						}


						$match .= $html;

						$match .= $this->sql_query($html);

					}

				}
			}



		}



		return $match;
	}



	//version 2
	public function doInnerSql($text){
		global $aiki;

		$finalprocessedloop = "";

		$count_innersql = preg_match_all('/\(\@\((.*)\)\@\)/Us', $text, $sqlmatches );

		if ($count_innersql > 0){
			foreach ($sqlmatches[1] as $match)
			{
				$innerSql = $match;

				if ($innerSql){
					$loopElements = explode("||", $innerSql);
					$sqlQuery = $loopElements[0];

					if (preg_match('/UPDATE|DELETE|INSERT|CREATE|TRUNCATE|DROP|ALTER/i', $sqlQuery)){
						$sqlQuery = '';
					}

					$loopOutput = $loopElements[1];

					if ($sqlQuery){


						$innerSqlResult = mysql_query($sqlQuery);
						if ($innerSqlResult){

							$results_num = mysql_num_rows($innerSqlResult);

							if ($results_num > 0){


								if (!isset($loopElements[2])){
									while ($r = mysql_fetch_row($innerSqlResult)) {


										$loopProcessed = $loopOutput;



										$num_fetched = count($r);

										for($i=0; $i<$num_fetched; $i++){

											$y = $i + 1;
											if (isset($r[$i])){
												$row[$y] = $r[$i];
											}else{
												$row[$y] = "";
											}

											$loopProcessed = str_replace("(+($y)+)", $row[$y], $loopProcessed);
										}

										$loopProcessed = str_replace("[records_num]", $results_num, $loopProcessed);

										if ($results_num > 1){
											$finalprocessedloop .= $loopProcessed;
										}else{
											$finalprocessedloop = $loopProcessed;

										}



									}
								}else{

									$loopProcessed = str_replace("[records_num]", $results_num, $loopOutput);
									$finalprocessedloop = $loopProcessed;

								}
							}
						}

					}

				}
				//$finalprocessedloop = nl2br($finalprocessedloop);

				if ($results_num == 0){
					$finalprocessedloop = "0";
				}
				if (!isset($loopElements[2])){
					$text = str_replace("(@($match)@)", $finalprocessedloop, $text);

				}else{

					$text = str_replace("(@($sqlQuery||$loopOutput||$loopElements[2])@)", $finalprocessedloop, $text);
				}
				$finalprocessedloop = '';
			}
		}
		$text = preg_replace('/\(\+\([0-9]\)\+\)/U', '', $text);

		return $text;
	}



}