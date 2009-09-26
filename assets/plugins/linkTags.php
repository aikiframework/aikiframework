<?php
/***************************************************************************
 *   Copyright (C) 2008-2009 by Bassel Khartabil                           *
 *   http://www.aikicms.org                                                *
 *   bassel@aikicms.org                                                    *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 *   This program is distributed in the hope that it will be useful,       *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 *   GNU General Public License for more details.                          *
 *                                                                         *
 *   GNU General Public License Version 2 or later (the "GPL")             *
 *    http://www.gnu.org/licenses/gpl.html                                 *
 ***************************************************************************/
/*
 V6
 Rewriten on 22/5/2009
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class linkTags extends aiki
{

	function  linkTags(){

	}

	function do_linkTags($text){
		global $aiki, $membership, $db;


		if (preg_match('/\(\+\((.*)\)\+\)/', $text)){

			$link_tags = $db->get_results("SELECT * FROM aiki_linker_tags");
			$query = '';
			if ($link_tags){

				foreach ($link_tags as $tag)
				{
					$query = '';


					$count = preg_match_all( '/'.preg_quote($tag->tagstart, '/').preg_quote($tag->parlset, '/').'(.*)'.preg_quote($tag->tagend, '/').'/U', $text, $match );

					if ($count > 0){

						if ($tag->linkexample){

							$query = "SELECT $tag->idcolumn, $tag->namecolumn FROM $tag->dbtable WHERE ";
							$tagidcolumn = $tag->idcolumn;
							$tagnamecolumn = $tag->namecolumn;
							$is_extrasql_loop = $tag->is_extrasql_loop;

							$i = 1;

							if ($tag->extrasql){
								$extrasql = "$tag->extrasql";
							}else{
								$extrasql = "";
							}

							$tagnamecolumn = $tag->namecolumn;

							foreach ($match[1] as $tag_match){

								$tag_match_array = explode('=', $tag_match);

								if (!isset($tag_match_array[1])){

									$tag_text = $tag_match_array[0];
									$tag_equivalent = $tag_match_array[0];

								}else{

									$tag_text = $tag_match_array[0];
									$tag_equivalent = $tag_match_array[1];

								}

								$query .= "$tagnamecolumn LIKE '$tag_equivalent'";


								if ($extrasql and $is_extrasql_loop){
									$extrasql = str_replace('[tag_equivalent]', $tag_equivalent, $extrasql);
									$query .= " $extrasql ";
								}


								if ($count != $i){
									$add_or = "or";
								}else{
									$add_or = "";
								}

								$query .= " $add_or ";


								$i++;
							}

							if ($extrasql and !$is_extrasql_loop){
								$extrasql = str_replace('[tag_equivalent]', $tag_equivalent, $extrasql);
								$query .= " $extrasql ";
							}

							$result = $db->get_results($query);
							if ($result){

								foreach($result as $replacment){

									$tagname = $replacment->$tagnamecolumn;
									$tagid = $replacment->$tagidcolumn;

									foreach ($match[1] as $tag_output){
										$tag_output = explode('=', $tag_output);
										if ($tag_output[1]){

											$tag_output_side = $tag_output[1];

											if ($tag_output_side == $tagname){

												$text = str_replace($tag->tagstart.$tag->parlset.$tag_output[0].'='.$tag_output[1].$tag->tagend, "<a href=\"aikicore->setting[url]/$tag->linkexample/$tagid\">$tag_output[0]</a>", $text);

											}

										}else{
											$tag_output = $tag_output[0];

											if ($tag_output == $tagname){

												$text = str_replace($tag->tagstart.$tag->parlset.$tag_output.$tag->tagend, "<a href=\"aikicore->setting[url]/$tag->linkexample/$tagid\">$tag_output</a>", $text);

											}

										}



									}

								}


							}

						}


					}
					if ($membership->permissions == "SystemGOD"){
						$text = preg_replace( '/'.preg_quote($tag->tagstart, '/').preg_quote($tag->parlset, '/').'(.*)(\=.*)?'.preg_quote($tag->tagend, '/').'/U', "<a style='color:#FF0000' target=\"_blank\" href=\"aikicore->setting[url]/$tag->linkexample/new\"><b>\\1</b></a>", $text );
						//$text = preg_replace( '/'.preg_quote($tag->tagstart, '/').preg_quote($tag->parlset, '/').'[\x0627-\x0649](\=.*)?'.preg_quote($tag->tagend, '/').'/U', "<a style='color:#FF0000' target=\"_blank\" href=\"aikicore->setting[url]/$tag->linkexample/new\"><b>\\1</b></a>", $text );
						//'/\(\+\(tag:(.*?)[^)]*\)\+\)/';
						//$text = preg_replace( '/'.preg_quote($tag->tagstart, '/').preg_quote($tag->parlset, '/').'(.*)(\=[^)].*)?'.preg_quote($tag->tagend, '/').'/U', "<a style='color:#FF0000' target=\"_blank\" href=\"aikicore->setting[url]/$tag->linkexample/new\"><b>\\1</b></a>", $text );

					}else{
						$text = preg_replace( '/'.preg_quote($tag->tagstart, '/').preg_quote($tag->parlset, '/').'(.*)(\=.*)?'.preg_quote($tag->tagend, '/').'/U', '\\1', $text );
					}

				}
			}


		}
		return $text;

	}
}
?>