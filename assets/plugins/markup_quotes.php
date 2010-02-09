<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}

class markup_quotes extends aiki
{

	
	//From mediawiki 1.14.0 Parser
	function markup_quotes( $text ) {
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