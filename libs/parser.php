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

if (!defined('IN_AIKI')) {
    die('No direct script access allowed');
}


/**
 * This is a parser for the ever random aikimarkup that provides some features
 * for adding capabilities quickly from within the aiki framework web
 * interface.
 *
 * @category    Aiki
 * @package     Library
 *
 * @todo rename class Parser
 * @todo this class is massively overloaded with functionality. looks to be
 * handling parsing for aiki in addition to loading external feeds. need
 * to strip this back to its singular function
 */
class parser extends aiki {

    /**
     * Curl timeout. set to zero for no timeout
     * @todo add timeout here and other places to config editor
     */
    private $timeout = 2;

    /**
     * Process some text that is marked up with aikimarkup and then output
     * the html version of it. This is supposed to be a general aikimarkup
     * parser, except so much aikimarkup is sprinkled throughout the codebase.
     *
     * @param    string    $text    text for processing
     * @return    string
     */
    public function process($text) {
        $text = $this->markup_ajax($text);
        $text = $this->images($text);
        $text = $this->inline($text);
        $text = $this->feed_parser($text);
        return $text;
    }

    /**
     * For reading a feed.
     *
     * @todo replace the usage of rss_parser in the code with feed_parser.
     * @todo there are different kinds of feeds other than just RSS, need
     * abstraction to support atom types as well!!!
     * @todo replace this entire feed library with the standard magpierss
     * feed routines which are vastly superior and updated!!! everyone uses!
     *
     * Example of using the rss parser to get rss.
     * <code>
     * <rss>
     *   <url>http://planet.openclipart.org/news/rss20.xml</url>
     *        <limit>3</limit>
     *        <output>
     *        <div class='news'>
     *        <h4>[[title]]</h4>
     *        <p>[[pubDate]]</p>
     *        <a href='[[link]]'>[[guid]]</a>
     *        <div>[[description]]</div>
     *        </div>
     *        //can use [[link:href]] to get href attribute from link
     *        //or [[author->name]] to get child data
     *        </output>
     *        <type>rss</type> //is the default and can be atom
     *    </rss>
     *    </code>
     *
     * Example to get an atom feed from improperly names <rss />
     * <code>
     * //can use link:href to get href attribute from link
     * //or [[author->name]] to get child data
     * // here's how you can parse a date:
     *
     * <php $aiki->web2date->parseweb2date( pubDate ); php>
     *
     * <rss>
     *        <url>http://feeds.launchpad.net/openclipart/latest-bugs.atom</url>
     *        <limit>7</limit>
     *        <output>
     *            <div class='news'>
     *            <h4><a href='[[link:href]]'>[[title]]</a></h4>
     *            <p>[[published]]</p>
     *            <div><a href='[[author->uri]]'>[[author->name]]</a></div>
     *            </div>
     *        </output>
     *        <type>atom</type>
     *    </rss>
     *    </code>
     *
     * @param   string $text text to process from aiki.
     * @return  mixed processed output.
     */
    public function feed_parser($text) {
        return $this->rss_parser($text);
    }


    /**
     * For reading a feed and outputting html from inside of aiki. Its an
     * aikimarkup way to read feeds in.
     *
     * @todo should move this function to feed_parser and deprecate this
     *       as an interface.
     * @todo this function should ONLY parse a feed, and then use output
     * in another function! This is way too overloaded!!!
     * @todo replace with magpierss
     *
     * @deprecated when version 0.7 of Aiki. @see feed_parser.
     *
     * @param   string $text text to process from aiki.
     * @return  mixed processed output.
     */
    public function rss_parser($text) {
        global $aiki;

        /**
         * Note this supports atom as well, just bad naming
         *
         * @todo add <feed>..</feed> to the markup possibilities
         */
        $feed_matchs =
            preg_match_all('/\<rss\>(.*)\<\/rss\>/Us', $text, $matchs);

        // if more than one rss section found
        if ( $feed_matchs > 0 ) {
            foreach ( $matchs[1] as $feed ) {
                $feed_url =
                    $aiki->get_string_between($feed , "<url>", "</url>");
                $feed_url = trim($feed_url);
                $limit =
                    $aiki->get_string_between($feed , "<limit>", "</limit>");
                $limit = trim($limit);
                $output =
                    $aiki->get_string_between($feed , "<output>", "</output>");
                $type = $aiki->get_string_between($feed , "<type>", "</type>");
                if (!$type)
                    $type = "rss";

                /**
                 * @todo rip out hardcoded HTML with aikimarkup built in!!!
                 * this makes it much harder to replace aikimarkup with plugin
                 * diff. language.
                 */
                if (!$output) {
                    $output = "<div class='news'>" .
                        "<h4>[[title]]</h4>" .
                        "<p>[[pubDate]]</p>" .
                        "<p><a href='[[link]]'>[[guid]]</a></p>" .
                        "<div class='description'>[[description]]</div>" .
                        "</div>";
                }

                /**
                 * Get content for each feed url item.
                 *
                 * @todo the variables here are not scoped properly
                 */
                if (function_exists("curl_init")) {
                    $ch = curl_init();
                    curl_setopt ($ch, CURLOPT_URL, $feed_url);
                    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

                    ob_start();
                    curl_exec($ch);
                    curl_close($ch);
                    $content = ob_get_contents();
                    ob_end_clean();
                }

                // parse the grabbed content
                if ( isset($content) && $content !== false ) {
                    $xml = @simplexml_load_string($content);

                    $i = 1;

                    $html_output = '';
                    if ($xml) {
                        switch ($type) {
                            /**
                             * @todo uhhh, atom is supported, but hidden!
                             */
                            case "atom":
                                $xml_items = $xml->entry;
                                break;

                            case "rss":
                                $xml_items = $xml->channel->item;
                                break;
                        }

                        if ($xml_items) {
                            foreach ( $xml_items as $item ) {
                                $items_matchs =
                                    preg_match_all('/\[\[(.*)\]\]/Us',
                                                   $output, $elements);

                                if ( $items_matchs > 0 ) {
                                    $processed_output = $output;

                                    foreach ( $elements[1] as $element ) {
                                        $element = trim($element);

                                        if (preg_match('/\-\>/', $element)) {
                                            $element = explode("->", $element);
                                            $element_sides =
                                                $item->$element[0]->$element[1];
                                            $processed_output =
                                                str_replace("[[" . $element[0] .
                                                "->" . $element[1] . "]]",
                                                $element_sides,
                                                $processed_output);

                                        } elseif (preg_match('/\:/', $element)) {
                                            $element = explode(":", $element);
                                            $element_sides =
                                                $item->$element[0]->attributes()->$element[1];
                                            $processed_output =
                                                str_replace("[[" . $element[0] .
                                                ":" . $element[1] . "]]",
                                                $element_sides,
                                                $processed_output);
                                        } else {
                                            $processed_output =
                                            str_replace("[[".$element."]]",
                                            $item->$element, $processed_output);
                                        }
                                    }
                                    $html_output .= $processed_output;
                                    $processed_output = '';
                                }

                                if ( isset($limit) and $limit == $i ) {
                                    break;
                                }
                                $i++;
                            }
                        }
                    }
                }
                if (isset($html_output)) {
                    $text = str_replace("<rss>$feed</rss>", $html_output , $text);
                }
            } // end of foreach loop
        } // end of if > 0 feeds
        return $text;
    } // end of rss_parser


    /**
     * This is a generic way to make a tag cloud with aiki markup.
     *
     * This is how you make a tag cloud:
     * <code>
     * (#(tags: some, tags, go, here)#)
     * </code>
     *
     * @param    string    $text        text for processing
     * @param    array    $widget_value    widget
     * @global    array    $db        global db instance
     * @global    aiki    $aiki        global aiki instance
     * @return    string
     */
    public function tags($text, $widget_value) {
        global $db, $aiki;

        /**
         * @todo all aiki markup must be refactored out of these classes
         * into its own holding tank!
         */
        $tags = $aiki->get_string_between($text, "(#(tags:", ")#)");
        if ($tags) {
            $tagsides = explode("||", $tags);

            if (isset($tagsides[2])) {
                $separator = $tagsides[2];
            } else {
                $separator = ",";
            }
            $tags_links = explode("$separator", $widget_value->$tagsides[0]);
            $tag_cloud = '';
            $i = 0;
            foreach ( $tags_links as $tag_link ) {
                if ($tag_link) {
                    $tag_link = trim($tag_link);
                    if ( $i > 0 ) {
                        $tag_cloud .= ' '.$separator;
                    }
                    /**
                     * @todo extract out bar html.
                     */
                    $tag_cloud .=
                        ' <a href="[root]/' . $tagsides[1] . '" rel="tag">' .
                        $tag_link . '</a>';
                    $tag_cloud = str_replace("_self", $tag_link, $tag_cloud);
                    $i++;
                }
            }
            $text = str_replace("(#(tags:$tags)#)", $tag_cloud, $text);
        }
        return $text;
    } // end of tags function


    /**
     * This is generic markup for placing in image in a widget using aikimarkup.
     *
     * This is a way with aikimarkup to output a stored image with a link
     * around it:
     * <code>
     * {+{SOME_STORED_IMAGE_NAME}+}
     * </code>
     *
     * @param    string    $text    text for processing
     * @global    array    $db        global db instance
     * @global    aiki    $aiki    global aiki instance
     * @global    array    $config    global config instance
     * @return    string
     */
    public function images($text) {
        global $db, $aiki, $config;

        $numMatches = preg_match_all( '/\{\+\{/', $text, $matches);

        for ( $i=0; $i<$numMatches; $i++ ) {
            $get_photo_info = $this->get_string_between($text, "{+{", "}+}");
            $photo_info_array = explode("|", $get_photo_info);
            $html_photo = "";

            /**
             * @todo rip out bare html
             */
            if (!isset($photo_info_array[7])) {
                $html_photo .= "<a href='" . $config['url'] . "file/image|" .
                               $photo_info_array[0] . "'>";
            }

            $html_photo .= "<img ";

            $html_photo .= "src='".$config['url']."image/";

            if ( $photo_info_array[5] and $photo_info_array[5] != "px" ) {
                //add spesific size virtual folder
                $html_photo .= "$photo_info_array[5]/";
            }
            $html_photo .= "$photo_info_array[0]'";

            //this will overwrite the alt value from the database
            if ( isset($photo_info_array[1]) and $photo_info_array[1] != "0" ) {
                $html_photo .= "alt='$photo_info_array[1]' ";
            }

            if ( isset($photo_info_array[2]) and
                $photo_info_array[2] != "0" and
                !$photo_info_array[6] ) {
                //no need to align if it's contained in aligned div
                $html_photo .= "align='$photo_info_array[2]' ";
            }

            if ( isset($photo_info_array[3]) and $photo_info_array[3] != "v:" ) {
                $photo_info_array[3] =
                    str_replace("v:", "", $photo_info_array[3]);
                $html_photo .= "vspace='$photo_info_array[3]' ";
            }

            if ( $photo_info_array[4] and $photo_info_array[4] != "h:" ) {
                $photo_info_array[4] =
                    str_replace("h:", "", $photo_info_array[4]);
                $html_photo .= "hspace='$photo_info_array[4]' ";
            }
            $html_photo .= "/ >";

            if (!isset($photo_info_array[7])) {
                $html_photo .= "</a>";
            }
            if ( isset($photo_info_array[6]) and $photo_info_array[6] != "0" ) {
                $html_photo .= "<br />$photo_info_array[6]";
            }
            /**
             * @todo rip out this bare html
             */
            if ( $photo_info_array[6] and $photo_info_array[6] != "0" ) {
                $html_photo = "<div id='img_container' style='z-index: 9; clear: " .
                $photo_info_array[2] . "; float: " . $photo_info_array[2] .
                "; border-width: .5em 0 .8em 1.4em; padding: 10px'><div style='z-index" .
                ": 10; border: 1px solid #ccc; padding: 3px; background-color: #f9f9f9;" .
                "font-size: 80%;text-align: center;overflow: hidden;'>$html_photo</div></div>";
            }

            $text = str_replace("{+{".$get_photo_info."}+}",
                                $html_photo, $text);
        }
        return $text;
    } // end of images function


    /**
     * Display content of external file inside a widget
     *
     * This is the aikimarkup for displaying output from external file in
     * aiki widget.
     *
     * <code>
     * (#(inline:[root]/weather.php?city=(!(1)!))#)
     * </code>
     *
     * @param    string    $text    text for processing
     * @return    string
     */
    public function inline($text) {
        $inline = preg_match_all('/\(\#\(inline\:(.*)\)\#\)/U', $text, $matchs);

        if ( $inline > 0 ) {
            foreach ( $matchs[1] as $inline_per ) {
                $content = file_get_contents($inline_per);
                $text = str_replace("(#(inline:$inline_per)#)",
                              $content, $text);
            }
        }
        return $text;
    }


    /**
     * This is some convenience aiki markup for ajax markup. Its used in
     * the admin widgets at this point mainly. grep -R ajax_a src/*
     *
     * This is an example of using ajax aiki markup:
     * <code>
     * (ajax_a(SEMICOLON_SEPARATED_STRING)ajax_a)
     * </code>
     *
     * @todo This code is used in the admin widgets primarily and is not
     * documented very well.
     *
     *
     * @param    string    $text    text for processing
     * @global    array    $db        global db instance
     * @return    string
     */
    public function markup_ajax($text) {
        global $db;

        $count_links =
            preg_match_all('/\(ajax\_a\((.*)\)ajax\_a\)/Us', $text, $links);

        if ( $count_links > 0 ) {
            foreach ( $links[1] as $set_of_requests ) {
                $output = '';
                $array = explode(';', $set_of_requests);
                $array_of_values = $array;
                unset($array_of_values[0]);
                $function_name = str_replace('-', '', $array[0]);

                /**
                 * @todo rip out bare html
                 */
                $output .= " <script type=\"text/javascript\">
                $(document).ready(function(){
                function $function_name(file, targetwidget, callback) {

                $(targetwidget).load(file, {limit: 25}, function() {
                eval(callback);
            });
            }
         $(\"#$array[0]\").click(function(event){
         ";

                foreach ( $array_of_values as $value ) {
                    $value = $this->get_string_between($value, "[", "]");

                    $value = explode(',', $value);

                    $url = $this->get_string_between($value['0'], "'", "'");
                    $target = $this->get_string_between($value['1'], "'", "'");

                    if (isset($value['2'])) {
                        $callback =
                            $this->get_string_between($value['2'], "'", "'");
                    }

                    $output .= "$function_name('$url', '$target'";

                    if ($callback) {
                        $output .= ", '$callback;'";
                    }
                    $output .= ");"."\n";
                }
                $output .= "return false;

         });
         });
         </script>";

                $text = preg_replace('/\(ajax\_a\('.
                        preg_quote($set_of_requests, '/').'\)ajax\_a\)/Us',
                        $output, $text);
            }
        }
        return $text;
    } // end of markup_ajax function


    /**
     * This is aiki markup to get a field from a widget with a named
     * field that holds a unix timestamp. It is not generic for date output.
     *
     * This is the aikimarkup if there is a field publish_date in database:
     * <code>
     * (#(datetime:publish_date)#)
     * </code>
     *
     * @param    string    $text            text for processing
     * @param    array    $widget_value    a widget
     * @global    aiki    $aiki            global aiki instance
     * @global    array    $config            global config options instance
     * @return
     */
    public function datetime($text, $widget_value) {
        global $aiki, $config;

        $datetimes = preg_match_all('/\(\#\(datetime\:(.*)\)\#\)/Us',
                                    $text, $matchs);
        if ( $datetimes > 0 ) {
            foreach ( $matchs[1] as $datetime ) {
                //Check if valid unix timestamp
                if (preg_match('/[0-9]{10}/', $widget_value->$datetime)) {
                    $widget_value->$datetime =
                        date($config['default_time_format'],
                        $widget_value->$datetime);
                } else {
                    $widget_value->$datetime = '';
                }
                /**
                 * @TODO: Custom Time output formats inserted by user
                 */
                $text = str_replace("(#(datetime:$datetime)#)",
                        $widget_value->$datetime , $text);
            }
        }
        return $text;
    } // end of datetime function


    /**
     * Looks for aikimarkup to to get related topics and then outputs them
     * as html so can be formatted for display.
     *
     * This is an example:
     * <code>
     * (#(related:some||keywords||here)#)
     * </code>
     *
     * @todo this needs to be tested to see how it really works.
     * @todo this entire function needs better documentation
     * @todo rip out bare strings and html
     *
     * @param    string    $text        text for processing
     * @global    aiki    $aiki        global aiki instance
     * @global    array    $db            global db instance
     * @global    array    $config        global config options instance
     * @return    string
     */
    public function related_records($text) {
        global $aiki, $db, $config;

        $related = $aiki->get_string_between($text, "(#(related:", ")#)");
        if ($related) {
            $relatedsides = explode("||", $related);

            $related_cloud = "
                        <ul class='relatedKeywords'>";

            $related_links = explode("|", $relatedsides[0]);
            $related_array = array();
            foreach ( $related_links as $related_link ) {
                $get_sim_topics = $db->get_results("SELECT $relatedsides[2], $relatedsides[7] FROM $relatedsides[1] where ($relatedsides[3] LIKE '%|".$related_link."|%' or $relatedsides[3] LIKE '".$related_link."|%' or $relatedsides[3] LIKE '%|".$related_link."' or $relatedsides[3]='$related_link') and $relatedsides[7] != '$relatedsides[8]' order by $relatedsides[5] DESC limit $relatedsides[4]");

                if ($get_sim_topics) {
                    foreach ($get_sim_topics as $related_topic) {
                        $related_cloud_input = '<li><a href="' . $config['url'] .
                            $relatedsides[6] . '">' .
                            $related_topic->$relatedsides[2] . '</a></li>';
                        $related_cloud_input =
                            str_replace("_self",
                                $related_topic->$relatedsides[7],
                                $related_cloud_input);
                        $related_array[$related_topic->$relatedsides[7]] =
                            $related_cloud_input;
                        $related_cloud_input = '';
                    }
                }
            }
            foreach ( $related_array as $related_cloud_output ) {
                $related_cloud .= $related_cloud_output;
            }
            $related_cloud .= "</ul>";
            /**
             * @todo horrible to use a variable for so diff functions like this
             */
            $text = str_replace("(#(related:$related)#)",
                    $related_cloud , $text);
        }
        return $text;
    } // end of related_records function
} // end of class parser

?>