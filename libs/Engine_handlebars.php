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
            return new \Handlebars\SafeString($source);
        });
        $self = $this;
        $expr_engine = new Expression();
        $this->expressions = $expr_engine;
        $this->handlebars->addHelper('if', function($template, $context, $arg, $source) use ($self, $expr_engine) {
            //$parsedArgs = $template->parseArguments($arg);
            //$parsedArgs = array($arg);
            $arg = preg_replace("/\\\\'/", "'", preg_replace("/^'(.*)'/", "\\1", $arg));
            $expr = $self->process_args($context, $arg);
            $tmp = $expr_engine->evaluate($expr);
            if ($expr_engine->last_error) {
                throw new Exception($expr_engine->last_error);
            }
            
            if ($tmp) {
                if (preg_match("/=~/", $expr)) {
                    $new = array();
                    foreach ($expr_engine->v as $var => $value) {
                        if (preg_match("/^\\\$[0-9]+$/", $var)) {
                            $new[$var] = $value;
                        }
                    }
                    $context->push($new);
                }
                $template->setStopToken('else');
                $buffer = $template->render($context);
                $template->setStopToken(false);
                $template->discard($context);
                if (preg_match("/=~/", $expr)) {
                    $context->pop();
                }
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
            return $self->iterate($template, $context, $db->get_results($sql));
        });

        $this->handlebars->addHelper('script', function($template, $context, $arg, $source) use ($self, $expr_engine) {
            global $aiki;
            $parsedArgs = $template->parseArguments($arg);
            $arg = $self->process_args($context, $parsedArgs[0]);
            // unescape quotes
            $arg = preg_replace('/\\\\"/', '"', $arg);
            $result = $expr_engine->evaluate($arg);
            if (is_string($result)) {
                return new \Handlebars\SafeString($result);
            } else {
                return $result;
            }
        });
        
        $this->handlebars->addHelper('file', function($template, $context, $arg, $source) use ($self, $expr_engine) {
            global $aiki;
            $parsedArgs = $template->parseArguments($arg);
            try { 
                $arg = $self->process_args($context, $parsedArgs[0]);
                if (preg_match("/^\\\".*\\\"$/", $arg)) {
                    $arg = json_decode($arg);
                }
                if (file_exists($arg)) {
                    return new \Handlebars\SafeString(file_get_contents($arg));
                }
            } catch(Exception $e) {}
        });
        
        $this->handlebars->addHelper('permission', function($template, $context, $arg, $source) use ($self) {
            global $aiki, $db;
            $parsedArgs = $template->parseArguments($arg);
            $arg = $self->process_args($context, $parsedArgs[0]);
            if (preg_match("/^\\\".*\\\"$/", $arg)) {
                $arg = json_decode($arg);
            }
            $permissions = explode(" ", preg_replace("/\s+/", " ", $arg));
            $permission = false;
            if (in_array($aiki->membership->permissions, $permissions)) {
                $permission = true;
            } else {
                $sql = "SELECT group_level" .
                    " FROM aiki_users_groups".
                    " WHERE group_permissions in ('". implode("','", $permissions) ."')";

                $levels = $db->get_col($sql);
            
                if ($levels) {
                    foreach ($levels as $level) {
                        if ($aiki->membership->group_level < $level) {
                            $permission = true;
                            break;
                        }
                    }
                }
            }
            if ($permission) {
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
        
        $this->handlebars->addHelper('json_encode', function($template, $context, $arg, $source) use ($self) {
            $parsedArgs = $template->parseArguments($arg);
            $arg = $self->process_args($context, $parsedArgs[0]);
            if (preg_match("/^\\\".*\\\"$/", $arg)) {
                return new \Handlebars\SafeString($arg);
            } else {
                return new \Handlebars\SafeString(json_encode($arg));
            }
        });
        
        $this->handlebars->addHelper('read', function($template, $context, $arg, $source) use ($self) {
            global $aiki;
            $parsedArgs = $template->parseArguments($arg);
            try {
                $arg = $self->process_args($context, $parsedArgs[0]);
                if ($arg) {
                    if (preg_match("/^\\\".*\\\"$/", $arg)) {
                        $arg = json_decode($arg);
                    }
                    if (file_exists($arg)) {
                        $file = explode("\n", file_get_contents($arg)); // file function inlcude newline characters
                        return new \Handlebars\SafeString($self->iterate($template, $context, $file));
                    }
                }
            } catch(Exception $e) { }
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
        });
        
        $this->handlebars->addHelper('set', function($template, $context, $arg, $source) use ($self, $expr_engine) {
            if (!$source) {
                //echo json_encode($source) . "\n";
            }
            $parsedArgs = $template->parseArguments($arg);
            $arg = preg_replace('/\\\\"/', '"', $self->process_args($context, $parsedArgs[0]));
            if (preg_match("/^\s*(.*?)\s*(=|\+=)\s*(.*)\s*$/", $arg, $matched)) {
                $value = $expr_engine->evaluate($matched[3]);
                if (preg_match("/^\.\.\//", $matched[1])) {
                    $selectors = explode("/", $matched[1]);
                    $stack = array();
                    foreach ($selectors as $selector) {
                        array_push($stack, $context->pop());
                    }
                    $selector = explode(".", end($selectors));
                    $end = end($stack);
                    $top = &$end;
                    foreach ($selector as $i => $part) {
                        if (array_key_exists($part, $top)) {
                            if ($i == count($selector)-1) {
                                if ($matched[2] == '+=') {
                                    if (is_array($top) && array_key_exists($part, $top)) {
                                        if (is_string($value)) {
                                            $top[$part] .= $value;
                                        } else {
                                            $top[$part] += $value;
                                        }
                                    } elseif ($part == 'this') {
                                        if (is_string($value)) {
                                            $top .= $value;
                                        } else {
                                            $top += $value;
                                        }
                                    }
                                } else {
                                    if (is_array($top)) {
                                        $top[$part] = $value;
                                    } else {
                                        $array = array(
                                            "this" => $top
                                        );
                                        $array[$part] = $value;
                                        $top = $array;
                                    }
                                }
                            } else {
                                $top = &$top[$part];
                            }
                        }

                    }
                    // push modified context on the stack
                    array_pop($stack);
                    array_push($stack, $end);
                    // restore context
                    while (end($stack)) {
                        $context->push(array_pop($stack));
                    }
                } else {
                    $top = $context->pop();
                    if ($matched[2] == '+=') {
                        if (array_key_exists($matched[1], $top)) {
                            if (is_string($value)) {
                                $top[$matched[1]] .= $value;
                            } else {
                                $top[$matched[1]] += $value;
                            }
                        } elseif ($matched[1] == 'this') {
                            if (is_string($value)) {
                                $top .= $value;
                            } else {
                                $top += $value;
                            }
                        }
                    } else {
                        
                        if (is_array($top)) {
                            $top[$matched[1]] = $value;
                        } else {
                            $array = array(
                                "this" => $top
                            );
                            $array[$matched[1]] = $value;
                            $top = $array;
                        }
                    }
                    $context->push($top);
                }
            } else {
                throw new Error("Invalid argument to set helper");
            }
            //*/
        });
        
    }
    
    function process_args($context, $str) {
        $self = $this;
        $string_re = '(?<!\\\\)"(?:(?<!\\\\)\\\\"|(?:(?<!\\\\)(?:\\\\{2})*\\\\)"|[^"])*(?<![^\\\]\\\\)"';
        $re = "~\'[^\']+\'(*SKIP)(*F)|(?<double>$string_re)|(?<!\\\\)\\$(?<name>[\w.\/]+)\b(?!->)~";
        //echo "1: " . $str . "\n";
        $str = preg_replace_callback($re, function($matches) use ($context, $self) {
            if (!empty($matches['double'])) {
                return preg_replace_callback('/(?<!\\\\)\$([\w.\/]+)\b(?!->)/', function($matches) use ($context, $self) {
                    return $self->get_context_var($context, $matches[1]);
                }, $matches['double']);
            } else if (!empty($matches['name'])) {
                return json_encode($self->get_context_var($context, $matches['name']));
            }
        }, (string)$str);
        //echo "2: " . $str . "\n";
        $str = preg_replace_callback('/(@\w+)/', function($matches) use ($context, $self) {
            return json_encode($self->get_context_var($context, $matches[1]));
        }, $str);
        $str = preg_replace_callback('/\$aiki\-\>(.*?)\-\>([^(]+)\((.*)\)/', function($matches) {
            global $aiki;
            return json_encode($aiki->AikiScript->aiki_function($matches[1], $matches[2], $matches[3]));
        }, $str);
        
        $str = preg_replace_callback('/\$aiki\-\>(.*?)\-\>([^(]+)/', function($matches) {
            global $aiki;
            $class = $matches[1];
            $property = $matches[2];
            return json_encode($aiki->$class->$property);
        }, $str);
        return $str;
    }
    
    /**
     * render template by iterating over array values
     */

    function iterate($template, $context, $array) {
        $buffer = '';
        if ($array && count($array)) {
            $index = 0;
            $lastIndex = count($array) - 1;
            foreach ($array as $key => $value) {
                $specialVariables = array(
                    '@index' => $index,
                    '@first' => ($index === 0),
                    '@last'  => ($index === $lastIndex),
                    '@key'   => $key
                );
                $context->pushSpecialVariables($specialVariables);
                $context->push($value);
                $buffer .= $template->render($context);
                $context->pop();
                $template->rewind();
                $index++;
            }
        }
        return $buffer;
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
                    //restore the context
                    while (end($stack)) {
                       $context->push(array_pop($stack));
                    }
                    throw new Exception("Can't find variable $var");
                }
            }
        }
    }
    
    /**
     * Create layout
     */

    function layout($parameters) {
        global $db, $aiki;

        // Initialize

        // @TODO javascript? id for some elements?
        $this->target = array(
            "body"=>"" ,
            "header"=>"",
            "css"=>array() );

        // the widget is given directly or
        if (isset($_GET["widget"])) {
            if ($widget = $aiki->widgets->get_widget($_GET['widget'])) {
                return $this->parse($widget);
            }
            return;//all work is done
        }

        // or in url,
        // search widget and test there is a unique response
        $module_widgets = $aiki->widgets->get_candidate_widgets();
        $unique_widget_exists = false;
        if ($module_widgets) {
            foreach($module_widgets as $tested_widget){
                if ($tested_widget->display_urls != "*"){
                    $unique_widget_exists = true;
                    break;
                }
            }
        }

        // Error 404 page not found
        $allMatch=false;
        if (!$unique_widget_exists) {
            // first look for widget that responds error_404,
            // else use config error_404.
            $module_widgets = $aiki->widgets->get_Page_Not_Found_Widgets();
            if ( $module_widgets ) {
                $aiki->Errors->pageNotFound(false);
                $allMatch = true;
            } else {
                return $aiki->Errors->pageNotFound(true);
            }
        }

        // now filter canditate widgets, before create content
        foreach ( $module_widgets as $parent ) {

            // first parent
            if ( $allMatch or
                ($aiki->url->match($parent->display_urls) && !$aiki->url->match($parent->kill_urls)) ) {
                if ( $parent->have_css == 1) {
                    $this->target["css"][] = $parent->id;
                }
                $this->target[$parent->widget_target] .= $this->parse($parent);

                // children..
                /* @TODO..a function */
                if ( is_array($descendants = $aiki->widgets->get_candidate_widgets($parent->id)) ){
                    foreach ($descendants as $descendant){
                        if ( $aiki->url->match($descendant->display_urls) && !$aiki->url->match($descendant->kill_urls) ) {
                            $this->target["css"][] = $descendant->id;
                            $this->target[$descendant->widget_target] .= $this->parse($descendant);
                        }
                    }
                }
            }
        }

        return $this->render_html();
    }

    /**
     *  parse a given widget
     */

    function parse($widget) {
        global $aiki, $db, $membership;
        

        $this->widget = $widget;
        
        // Security check to determine which widget content to display.
        if ($widget->is_admin &&
            $membership->permissions &&
            $widget->if_authorized &&
            $membership->have_permission($widget->permissions)) {
            $widget_text = $widget->if_authorized;
        } else {
            $widget_text = $this->widget->widget;
        }
        if (isset($widget->custome_header)) {
            $widget->custom_header = $widget->custome_header;
        }

        if ($widget->custom_header && $widget->custom_header != '') {
            $custom_headers = explode("\n", $widget->custom_header);
            foreach ($custom_headers as $custom_header) {
                if ( $custom_header != "" ) {
                    header($custom_header);
                }
            }
        }
        
        // handlerbars helpers require to quote arguments if then have special characters
        $helpers = array('#?sql', '#?if', '#?script', 'set', 'json_encode', '#permission');
        $re = "/\{\{~?(" . implode('|', $helpers).")\s+(.*?)\s*\~?}\}/";
        $widget_text = preg_replace_callback($re, function($matches) {
            return '{{' . $matches[1] . " '". preg_replace("/(?<!\\\\)'/", "\\'", $matches[2]) . "'}}";
        }, $widget_text);
        //echo $widget_text . "\n";
        $widget_text = $this->handlebars->render($widget_text, $this->global_vars($widget->widget_site));

        if (is_debug_on() && !$this->widget->custom_output) {
            $widgetName = $this->widget->widget_name;
            return "\n<!-- start {$widgetName} ($widgetID) -->" . $widget_text . "\n<!-- end {$widgetName} ($widgetID) -->";
        }
        return $widget_text;
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

    function global_vars($site="default") {
        global $aiki, $page, $config;

        $pretty = $aiki->config->get('pretty_urls', 1, "CURRENT", $site);
        $url = $aiki->config->get('url', false, "CURRENT", $site);

        $current_month = date("n");
        $current_year = date("Y");
        $current_day = date("j");

        // calculate view, prefix, route
        $view = $aiki->site->view();
        $language = $aiki->site->language();
        $prefix = $aiki->site->prefix();
        $view_prefix = $aiki->site->view_prefix();

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
        //print_r($paths);
        if ( isset($_SERVER["HTTPS"])) {
            $url = str_replace("http://", "https://", $url);
        }

        $trimedUrl = preg_replace('#/$#', "", $url); // reg: remove ending /
        return array(
            'paths' => $url,
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
            'SESSION'       => isset($_SESSION) ? $_SESSION : array(),
            'config'        => $config
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
