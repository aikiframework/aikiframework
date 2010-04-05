<?php

/**
 * Aiki framework (PHP)
 *
 * @author		Aikilab http://www.aikilab.com
 * @copyright  (c) 2008-2010 Aikilab
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class wiki_parser extends aiki
{

	public function process($text){

		$text = $this->aikiTemplates($text);
		$text = $this->intlinks($text);
		$text = $this->extlinks($text);
		$text = $this->markup_toc($text);
		$text = $this->markup_tables($text);
		$text = $this->markup_quotes($text);
		return $text;
	}

	public function aikiTemplates($widget){
		global $db, $aiki;

		$numMatches = preg_match_all( '/\{\{/', $widget, $matches);


		for ($i=0; $i<$numMatches; $i++){

			$templateFullText = $aiki->get_string_between($widget, "{{", "}}");
			$templateText = str_replace("| ", "|", $templateFullText);

			$templateText = str_replace("<br>", "", $templateText); //fix for after nl2br function is used
			$templateText = str_replace("<br />", "", $templateText);

			$templateElement = explode("|", $templateText);
			$templateName = trim($templateElement[0]);


			if ($templateName){


				$template_output = $db->get_var("SELECT template_output from apps_wiki_templates where template_name = '$templateName'");

			}

			foreach ($templateElement as $element){
				$element = trim($element);
				$elementSides = explode("=", $element);
				$elementSides[0] = trim($elementSides[0]);
				$elementSides[1] = trim($elementSides[1]);

				$template_output = str_replace("($elementSides[0])", $elementSides[1], $template_output);

			}
			$widget = str_replace ("{{".$templateFullText."}}", $template_output, $widget);

		}

		return $widget;

	}


	public function intlinks($text){
		global $aiki, $membership, $db, $config;


		if (preg_match('/\(\+\((.*)\)\+\)/', $text)){

			$link_tags = $db->get_results("SELECT * FROM apps_wiki_links");
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
										if (isset($tag_output[1])){

											$tag_output_side = $tag_output[1];

											if ($tag_output_side == $tagname){

												$text = str_replace($tag->tagstart.$tag->parlset.$tag_output[0].'='.$tag_output[1].$tag->tagend, "<a href=\"".$config['url']."$tag->linkexample/$tagid\">$tag_output[0]</a>", $text);

											}

										}else{
											$tag_output = $tag_output[0];

											if ($tag_output == $tagname){

												$text = str_replace($tag->tagstart.$tag->parlset.$tag_output.$tag->tagend, "<a href=\"".$config['url']."$tag->linkexample/$tagid\">$tag_output</a>", $text);

											}

										}



									}

								}


							}

						}


					}
					if ($membership->permissions == "SystemGOD"){
						$text = preg_replace( '/'.preg_quote($tag->tagstart, '/').preg_quote($tag->parlset, '/').'(.*)(\=.*)?'.preg_quote($tag->tagend, '/').'/U', "<a style='color:#FF0000' target=\"_blank\" href=\"".$config['url']."$tag->linkexample/new\"><b>\\1</b></a>", $text );
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


	public function extlinks($text){
		global $aiki, $config;

		$tags_output = array();

		$count = preg_match_all( '/'."\(\+\(".'(.*)'."\)\+\)".'/U', $text, $match );

		if ($count > 0){

			foreach ($match[1] as $tag_match){

				$tag_match_array = explode('=', $tag_match);

				if (!isset($tag_match_array[1])){

					$tag_text = $tag_match_array[0];
					$tag_equivalent = $tag_match_array[0];

				}else{

					$tag_text = $tag_match_array[0];
					$tag_equivalent = $tag_match_array[1];

				}


				//TODO: make sure it's correct link and if not correct it, and check for email addresses
				$processed_tag = "<a target=\"_blank\" href=\"$tag_equivalent\" style=\"background:transparent url(".$config['url']."assets/images/external.png) no-repeat scroll left center; padding-left:13px;\">".$tag_text.'</a>';


				$tags_output[] .= $processed_tag;

			}
			$text = str_replace($match[0], $tags_output, $text);

		}


		return $text;

	}

	//Original function: doHeadings from wikimedia /includes/parser/Parser.php
	//Parts of the new rebuilt function are from function called formatHeadings
	public function markup_toc($text){


		for ( $i = 6; $i >= 1; --$i ) {
			$h = str_repeat( '=', $i );
			//$text = preg_replace( "/^{$h}(.+){$h}\\s*$/m",
			//"<a name='\\1'><h{$i}>\\1</h{$i}></a>\\2", $text );

			$text = preg_replace( "/^$h(.+)$h\\s*/m", "<a name='\\1'><h{$i}>\\1</h{$i}></a>\\2", $text);

			//$text = preg_replace( "/^$h(.+)$h\\s*$/m", "<h$i>\\1</h$i>", $text );
		}


		$toc = '';
		$matches = array();
		$numMatches = preg_match_all( '/<H(?P<level>[1-6])(?P<attrib>.*?'.'>)(?P<header>.*?)<\/H[1-6] *>/i', $text, $matches );


		$i = 0;
		$oldertoc = "";
		foreach( $matches[3] as $headline ) {

			switch ($matches['level'][$i]){
				case 2:


					if($oldertoc == 3 ){$toc .= "</ul>";}
					if($oldertoc == 4 ){$toc .= "</ul></ul>";}
					if($oldertoc == 5 ){$toc .= "</ul></ul></ul>";}
					if($oldertoc == 6 ){$toc .= "</ul></ul></ul></ul>";}

					$toc .= "<li style='list-style-type: upper-roman'><a href=\"#$headline\">".$headline."</a></li>";
					$oldertoc = 2;
					break;

				case 3:

					if($oldertoc == 4 ){$toc .= "</ul>";}
					if($oldertoc == 5 ){$toc .= "</ul></ul>";}
					if($oldertoc == 6 ){$toc .= "</ul></ul></ul>";}

					if ($oldertoc == 2){$toc .= "<ul>";}

					$toc .= "<li style='list-style-type: decimal'><a href=\"#$headline\">".$headline."</a></li>";


					$oldertoc = 3;

					break;

				case 4:

					if($oldertoc == 5 ){$toc .= "</ul>";}
					if($oldertoc == 6 ){$toc .= "</ul></ul>";}

					if ($oldertoc == 3){$toc .= "<ul>";}

					$toc .= "<li style='list-style-type: decimal'><a href=\"#$headline\">".$headline."</a></li>";



					$oldertoc = 4;
					break;

				case 5:

					if($oldertoc == 6 ){$toc .= "</ul>";}
					//if($oldertoc == 4 ){$toc .= "</ul>";}

					if ($oldertoc == 4){$toc .= "<ul>";}
					$toc .= "<li style='list-style-type: decimal'><a href=\"#$headline\">".$headline."</a></li>";



					$oldertoc = 5;
					break;

				case 6:

					if ($oldertoc == 5){$toc .= "<ul>";}

					$toc .= "<li style='list-style-type: decimal'><a href=\"#$headline\">".$headline."</a></li>";



					$oldertoc = 6;
					break;

			}

			//$toc .= " <li style='list-style-type: decimal'><a href=\"#$headline\">- ".$headline."</a></li>";
			$i++;
		}

		if ($toc){

			$custome_toc_place = preg_match_all("/\[toc\]/", $text, $tocmatches);
			if ($custome_toc_place > 0 ){
				$text = str_replace("[toc]", "<br /><div id='toc'><p align='center'><b>__content__</b></p><br /><ul>".$toc."</ul></div><br />", $text);
			}else{
				$text = "<br /><div id='toc'><p align='center'><b>__content__</b></p><br /><ul>".$toc."</ul></div><br />".$text;
			}
		}
			

		return $text;

	}

	public function markup_tables($text){

		global $aiki;

		$numMatches = preg_match_all( '/\{\|/', $text, $matches);
		for ($i=0; $i<$numMatches; $i++){

			$get_table_contents = $this->get_string_between($text, "{|", "|}");

			$table_atr = $aiki->get_string_between($text, "{|", "|-");
			$table_atr = trim($table_atr);
			$table_atr = str_replace("<br>", "", $table_atr);
			$table_atr = str_replace("<br />", "", $table_atr);

			$table = "<table $table_atr >";


			$get_rows = explode("|-", $get_table_contents);
			unset($get_rows[0]); //fix for the first element being empty
			foreach ($get_rows as $row){
				$table .= "<tr>";

				$get_cells = explode("|", $row);
				unset($get_cells[0]); //fix for the first element being empty


				foreach ($get_cells as $cell){
					$table .= "<td>$cell</td>";

				}

				$table .= "</tr>";
			}


			$table .= "</table>";

			$text = str_replace ("{|".$get_table_contents."|}", $table, $text);
		}


		return $text;

	}

	//From mediawiki 1.14.0 Parser
	public function markup_quotes( $text ) {
		$arr = preg_split( "/(''+)/", $text, -1, PREG_SPLIT_DELIM_CAPTURE );
		if ( count( $arr ) == 1 )
		return $text;
		else
		{
			# First, do some preliminary work. This may shift some apostrophes from
			# being mark-up to being text. It also counts the number of occurrences
			# of bold and italics mark-ups.
			$i = 0;
			$numbold = 0;
			$numitalics = 0;
			foreach ( $arr as $r )
			{
				if ( ( $i % 2 ) == 1 )
				{
					# If there are ever four apostrophes, assume the first is supposed to
					# be text, and the remaining three constitute mark-up for bold text.
					if ( strlen( $arr[$i] ) == 4 )
					{
						$arr[$i-1] .= "'";
						$arr[$i] = "'''";
					}
					# If there are more than 5 apostrophes in a row, assume they're all
					# text except for the last 5.
					else if ( strlen( $arr[$i] ) > 5 )
					{
						$arr[$i-1] .= str_repeat( "'", strlen( $arr[$i] ) - 5 );
						$arr[$i] = "'''''";
					}
					# Count the number of occurrences of bold and italics mark-ups.
					# We are not counting sequences of five apostrophes.
					if ( strlen( $arr[$i] ) == 2 )      { $numitalics++;             }
					else if ( strlen( $arr[$i] ) == 3 ) { $numbold++;                }
					else if ( strlen( $arr[$i] ) == 5 ) { $numitalics++; $numbold++; }
				}
				$i++;
			}

			# If there is an odd number of both bold and italics, it is likely
			# that one of the bold ones was meant to be an apostrophe followed
			# by italics. Which one we cannot know for certain, but it is more
			# likely to be one that has a single-letter word before it.
			if ( ( $numbold % 2 == 1 ) && ( $numitalics % 2 == 1 ) )
			{
				$i = 0;
				$firstsingleletterword = -1;
				$firstmultiletterword = -1;
				$firstspace = -1;
				foreach ( $arr as $r )
				{
					if ( ( $i % 2 == 1 ) and ( strlen( $r ) == 3 ) )
					{
						$x1 = substr ($arr[$i-1], -1);
						$x2 = substr ($arr[$i-1], -2, 1);
						if ($x1 === ' ') {
							if ($firstspace == -1) $firstspace = $i;
						} else if ($x2 === ' ') {
							if ($firstsingleletterword == -1) $firstsingleletterword = $i;
						} else {
							if ($firstmultiletterword == -1) $firstmultiletterword = $i;
						}
					}
					$i++;
				}

				# If there is a single-letter word, use it!
				if ($firstsingleletterword > -1)
				{
					$arr [ $firstsingleletterword ] = "''";
					$arr [ $firstsingleletterword-1 ] .= "'";
				}
				# If not, but there's a multi-letter word, use that one.
				else if ($firstmultiletterword > -1)
				{
					$arr [ $firstmultiletterword ] = "''";
					$arr [ $firstmultiletterword-1 ] .= "'";
				}
				# ... otherwise use the first one that has neither.
				# (notice that it is possible for all three to be -1 if, for example,
				# there is only one pentuple-apostrophe in the line)
				else if ($firstspace > -1)
				{
					$arr [ $firstspace ] = "''";
					$arr [ $firstspace-1 ] .= "'";
				}
			}

			# Now let's actually convert our apostrophic mush to HTML!
			$output = '';
			$buffer = '';
			$state = '';
			$i = 0;
			foreach ($arr as $r)
			{
				if (($i % 2) == 0)
				{
					if ($state === 'both')
					$buffer .= $r;
					else
					$output .= $r;
				}
				else
				{
					if (strlen ($r) == 2)
					{
						if ($state === 'i')
						{ $output .= '</i>'; $state = ''; }
						else if ($state === 'bi')
						{ $output .= '</i>'; $state = 'b'; }
						else if ($state === 'ib')
						{ $output .= '</b></i><b>'; $state = 'b'; }
						else if ($state === 'both')
						{ $output .= '<b><i>'.$buffer.'</i>'; $state = 'b'; }
						else # $state can be 'b' or ''
						{ $output .= '<i>'; $state .= 'i'; }
					}
					else if (strlen ($r) == 3)
					{
						if ($state === 'b')
						{ $output .= '</b>'; $state = ''; }
						else if ($state === 'bi')
						{ $output .= '</i></b><i>'; $state = 'i'; }
						else if ($state === 'ib')
						{ $output .= '</b>'; $state = 'i'; }
						else if ($state === 'both')
						{ $output .= '<i><b>'.$buffer.'</b>'; $state = 'i'; }
						else # $state can be 'i' or ''
						{ $output .= '<b>'; $state .= 'b'; }
					}
					else if (strlen ($r) == 5)
					{
						if ($state === 'b')
						{ $output .= '</b><i>'; $state = 'i'; }
						else if ($state === 'i')
						{ $output .= '</i><b>'; $state = 'b'; }
						else if ($state === 'bi')
						{ $output .= '</i></b>'; $state = ''; }
						else if ($state === 'ib')
						{ $output .= '</b></i>'; $state = ''; }
						else if ($state === 'both')
						{ $output .= '<i><b>'.$buffer.'</b></i>'; $state = ''; }
						else # ($state == '')
						{ $buffer = ''; $state = 'both'; }
					}
				}
				$i++;
			}
			# Now close all remaining tags.  Notice that the order is important.
			if ($state === 'b' || $state === 'ib')
			$output .= '</b>';
			if ($state === 'i' || $state === 'bi' || $state === 'ib')
			$output .= '</i>';
			if ($state === 'bi')
			$output .= '</b>';
			# There might be lonely ''''', so make sure we have a buffer
			if ($state === 'both' && $buffer)
			$output .= '<b><i>'.$buffer.'</i></b>';
			return $output;
		}
	}

}

?>