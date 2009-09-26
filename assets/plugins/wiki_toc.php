<?php

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class wiki_toc extends aiki
{

	function  wiki_toc(){

	}

	//Original function: doHeadings from wikimedia /includes/parser/Parser.php
	//Parts of the new rebuilt function are from function called formatHeadings
	function do_wiki_toc($text){


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
			//TODO translate contents not المحتويات
			$custome_toc_place = preg_match_all("/\[toc\]/", $text, $tocmatches);
			if ($custome_toc_place > 0 ){
				$text = str_replace("[toc]", "<br /><div id='toc'><p align='center'><b>المحتويات:</b></p><br /><ul>".$toc."</ul></div><br />", $text);
			}else{
				$text = "<br /><div id='toc'><p align='center'><b>المحتويات:</b></p><br /><ul>".$toc."</ul></div><br />".$text;
			}
		}
			

		return $text;

	}
}
?>