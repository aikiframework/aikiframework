<?php

/**
 * Aiki Framework (PHP)
 *
 * LICENSE
 *
 * This source file is subject to the AGPL-3.0 license that is bundled
 * with this package in the file LICENSE.
 *
 * @author      Jakub Jankiewicz (jcubic) - Aikilab http://www.aikilab.com
 * @copyright   (c) 2008-2011 Aiki Lab Pte Ltd
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Library
 * @filesource
 * PARENT
 * update aiki_widgets inner join aiki_widget_porsi on aiki_widgets.father_widget=aiki_widget_porsi.id set parent_widget= aiki_widget_porsi.widget_name
 */

if (!defined('IN_AIKI')) {
    die('No direct script access allowed');
}

require('Engine_v8.php');
require('handlebars.php/src/Handlebars/Autoloader.php');
require('expression.php/expression.php');
Handlebars\Autoloader::register();

use Handlebars\Handlebars;

class engine_handlebars extends engine_v8 {

    var $handlebars;
    var $widget;

    function __construct() {
        $this->handlebars = new Handlebars();
        
        $this->handlebars->addHelper('noaiki', function($template, $context, $arg, $source) {
            return $source;
        });
        $self = $this;
        $this->handlebars->addHelper('if', function($template, $context, $arg, $source) use ($self) {
            $math = new Expression();
            $parsedArgs = $template->parseArguments($arg);
            $expr = $self->process_args($context, $parsedArgs[0]);
            $tmp = $math->evaluate($expr);
            if ($math->last_error) {
                throw new Exception($math->last_error);
            }
            if ($tmp) {
                $template->setStopToken('else');
                $buffer = $template->render($context);
                $template->setStopToken(false);
                $template->discard($context);
            } else {
                $template->setStopToken('else');
                $template->discard($context);
                $template->setStopToken(false);
                $buffer = $template->render($context);
            }
            
            return $buffer;
        });
        $this->handlebars->addHelper('sql', function($template, $context, $arg, $source) use ($self) {
            global $db;
            $parsedArgs = $template->parseArguments($arg);
            $sql = $self->process_args($context, $parsedArgs[0]);
            $results = $db->get_results($sql);
            $buffer = '';
            if ($results) {
                foreach ($results as $object) {
                    $context->push($object);
                    $buffer .= $template->render($context);
                    $context->pop();
                    $template->rewind();
                }
            }
            return $buffer;
        });

        $this->handlebars->addHelper('script', function($template, $context, $arg, $source) use ($self) {
            global $aiki;
            $parsedArgs = $template->parseArguments($arg);
            $arg = $self->process_args($context, $parsedArgs[0]);
            // unescape quotes
            $arg = preg_replace('/\\\\"/', '"', $arg);
            return new \Handlebars\SafeString($aiki->AikiScript->parser($arg, false));
        });
        
        $this->handlebars->addHelper('header', function($template, $context, $arg, $source) use ($self) {
            $parsedArgs = $template->parseArguments($arg);
            $para = array();
            for ($i=0; $i<count($parsedArgs); ++$i) {
                $para[] = $self->process_args($context, $parsedArgs[$i]);
            }
            switch (count($para)) {
                case 1: header($para[0]); break;
                case 2: header($para[0], $para[1]); break;
                default: header($para[0], $para[1], $para[2]); break;
            }
            return '';
        });
    }
    
    function process_args($context, $str) {
        $self = $this;
        return preg_replace_callback('/\$([a-zA-Z_][a-zA-Z_0-9]*+(?!->))/', function($matches) use ($context, $self) {
            return $self->get_context_var($context, $matches[1]);
        }, (string)$str);
    }
    
    /**
     * get variable from handlebars context
     */
    
    function get_context_var($context, $var) {
        $stack = array();
        while (true) {
            try {
                $value = $context->get($var, true);
                //restore the context
                while (end($stack)) {
                    $context->push(array_pop($stack));
                }
                return $value;
            } catch(Exception $e) {
                // save context for restoration
                array_push($stack, $context->pop());
                if (!$context->last()) {
                    throw new Exception("Can't find variable $var");
                }
            }
        }
    }

    /**
     *  parse a given widget
     */

    function parse($widgetID) {
        global $aiki, $db;
        

        $this->widget = $db->get_row("SELECT * FROM aiki_widgets WHERE id=" . (int)$widgetID);
        $widget       = $this->widget->widget;
        $widgetName   = $this->widget->widget_name;
        // fix handlebars helper arguments
        $helpers = array('#sql', '#if', 'script');
        $re = "/\{\{(" . implode('|', $helpers).")\s+([^\"'](.*?)[^\"'])\s*\}\}/";
        $widget = preg_replace_callback($re, function($matches) {
            return '{{' . $matches[1] . ' "'. preg_replace('/(?<!\\\\)"/', '\\"', $matches[2]) . '"}}';
        }, $widget);

        $widget = $this->handlebars->render($widget, $this->global_vars());

        if ( is_debug_on() ){
            return "\n<!-- start {$widgetName} ($widgetID) -->" . $widget . "\n<!-- end {$widgetName} ($widgetID) -->";
        }
        return $widget;
    }
    
    function render_html() {
        global $aiki;
        if ($this->widget->custom_output) {
            $html = $this->target['body'];
        } else {
            $html  = $aiki->Output->header($this->target['css'],  $this->target['header']);
            $html .= $aiki->Output->body($this->target['body']);
            $html .= $aiki->Output->end();
        }

        return $html;
    }

    function global_vars() {
        global $aiki, $page;

        $pretty = $aiki->config->get('pretty_urls', 1);
        $url = $aiki->config->get('url');

        $current_month = date("n");
        $current_year = date("Y");
        $current_day = date("j");

        // calculate view, prefix, route
        $view = $aiki->site->view();
        $language = $aiki->site->language();
        $prefix = $aiki->site->prefix();
        $view_prefix= $aiki->site->view_prefix();

        $paths = array();
        if ($prefix) {
            $paths[] = $prefix;
        }

        if ($view_prefix) {
            $paths[] = $view_prefix;
        }
        if ( count($aiki->site->languages()) > 1 ) {
            $paths[] = $language;
        }
        $paths = implode("/", $paths);

        if ( isset($_SERVER["HTTPS"])) {
            $url = str_replace("http://", "https://", $url);
        }

        $trimedUrl = preg_replace('#/$#',"",$url); // reg: remove ending /
        return array(
            'userid'    => $aiki->membership->userid,
            'full_name' => $aiki->membership->full_name,
            'username'  => $aiki->membership->username,
            'user_group_level' => $aiki->membership->group_level,
            'user_permissions' => $aiki->membership->permissions,
            'language'  => $aiki->site->language(),
            'page'      => $page,
            'site_name' => $aiki->site->site_name(),
            'site'      => $aiki->site->get_site(),
            'view'      => $aiki->site->view(),
            'direction' => $aiki->languages->dir,
            'insertedby_username' => $aiki->membership->username,
            'insertedby_userid' => $aiki->membership->userid,
            'current_month' => $current_month,
            'current_year'  => $current_year,
            'current_day'   => $current_day,
            'root'          => $url,
            'root-language' => $trimedUrl .  "/" . $aiki->site->language(),
            'site_prefix'   => $prefix ,
            'view_prefix'   => $view_prefix ,
            'route'         => $trimedUrl.  "/". $paths,
            'route-local'   => $paths,
            'GET'           => $_GET,
            'POST'          => $_POST,
            'COOKIE'        => $_COOKIE,
            'SESSION'       => $_SESSION,
            'foo' => array(1,2,3,4,5)
        );
        
    }


    function parse_get($matchs) {
        $token= $matchs[0];
        return $token && isset($_GET[ $token]) ? $_GET[$token] : "";
    }

    function parse_post($token) {
        $token= $matchs[0];
        return $token && isset($_POST[$token]) ? $_POST[$token] : "";
    }


    /*
     * Parse template
     */
    function parse_template($matches) {
        global $db;
        $id= $this->get_widget_id($matches[1]);
        return  is_null($id) ? "": $db->get_var("SELECT widget FROM aiki_widgets WHERE id='$id'");
    }

    function parse_script($code) {
        global $aiki;
        return $aiki->AikiScript->parser($code, false);
    }


    /**
     * translation
     */

    function parse_t($term) {
        static $translate;
        global $aiki;
        if (is_null($translate)) {
            $translate = $aiki->site->language() != "en";
        }
        return $translate ? t($term) : $term ;
    }


    function parse_translate($term) {
        return __($term);
    }



    /*
     * Parse sql markup
     */

    function parse_sql(&$text) {
        global $db;
        if (strpos($text,"||") === false) {
            return $text;
        }
        list($select, $content) = explode("||", $text, 2);

        $results = $db->get_results($select);

        $html = "";
        if ($results) {
            foreach ($results as $row) {
                $fields= array();
                foreach ($row as $field => $value) {
                    $fields[ "[$field]" ] = $value;
                }
                $html .= strtr( $content, $fields);
            }
        }
        return $html;

    }

    /*
     * Parse hits
     * @TODO implements trigger.
     */

    private function parse_hits(&$hidData) {
        global $db;

        $hit = explode("|", $hitData);
        if ( len($hit) == 3 ){
            $db->query(
                    "UPDATE {$hit[0]}".
                    " SET {$hit[2]}={$hit[2]}+1".
                    " WHERE {$hit[1]}");
        } elseif (is_debug_on() ) {
                return sprintf( __("BAD HITS PARAMETERS: 3 expected, %d  given"), len($hit) );
        }
        return "";
    }


    function parse_widget( &$text ){

        if ( strpos( $widget,"||")!== false ) {
            list($wigetId, $select) = explode("||",$widget,2);
        } else {
            $widgetId= $widget ;
        }

        return  $this->parse($widgetId, $select);
    }


    function parse_view( &$text){
        global $aiki;
        if ( strpos($text,"||") !== false ){
            list($filter,$content) = explode("||", $text, 2);
            if ($trim($filter)=="") {
                return $text;
            }
        } else {
            return $text;
        }

        list($view,$language)= exlode("/",$filter."/*",2);

        if  ( match_pair_one( $view, $aiki->site->view()) &&
              match_pair_one( $language, $aiki->site->language() )){
            return $content;
        }
        return "";

    }


    function parse_permission($widget){
        global $aiki, $db;
        if ( strpos($widget,"||") !== false ){
            list($filter,$content) = explode("||", $widget, 2);
        } else {
            return $widget;
        }

        /* fake permission */
        if ( trim($filter) == "user" ){
            return $content;
        }
        return "";

        $sql = "SELECT group_level" .
               " FROM  aiki_users_groups".
               " WHERE group_permissions='". addslashes($filter) ."'";

        $get_group_level = $db->get_var($sql);

        if ( trim($filter) == $aiki->membership->permissions ||
            $aiki->membership->group_level < $get_group_level ) {
            return $content;
        }

        return "";
    }

    function parse_noaiki(&$text){
        return strlen($text) ;
    }


}
