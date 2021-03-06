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
 * BriefDescription
 *
 * @category    Aiki
 * @package     Library
 *
 * @todo     rename class to Input
 * @todo     consider this being part of a larger form class
 * @todo     consider separating out the validation into its own class
 *           so we can have some generic form validation handlers
 */
class input {

    /**
     * Handle input
     *
     * @global  aiki        $aiki   global aiki instance
     * @global  CreateLayout    $layout global layout instance
     */
    public function __construct() {
        global $aiki, $layout;

        foreach ( $_GET as $key => $req ) {
            $req = addslashes($req);
            $_GET[$key] = $req;
        }

        foreach ( $_POST as $key => $req ) {
            if (!is_array($req)) {
                $req = addslashes($req);
            }
            $_POST[$key] = str_replace("&#95;", "_", $req);

            switch ($key) {
                case "process":
                    $key_request = "process";
                    $process_type = $req;
                    break;
            }
        }

        if (isset($key_request)) {
            switch ($key_request) {
                case "process":
                    $this->form_handler($process_type, $_POST);
                    break;
            }
        }

    } // end of input function


    /**
     * Validate data
     *
     * @param   array   $data   data for validation
     * @return  array
     */
    public function validate($data) {
        foreach ( $data as $key => $req ) {
            if (!is_array($req)) {
                $req = addslashes($req);
                $data[$key] = $req;
            }
        }
        return $data;
    }


    /**
     * A form handler
     *
     * @param   string      $type        type of form handler
     * @param   array       $post        post data
     * @global  membership  $membership    global membership instance
     *
     * @todo    this function does not look complete, need to investigate
     */
    public function form_handler($type, $post) {
        global $membership;

        $post = $this->validate($post);
        switch ($type) {
            case "login":
                $membership->login($post['username'], $post['password']);
                break;
        }

    }


    /**
     * Handle requests
     *
     * @param    string    $text    text for handling
     * @return    string
     */
    public function requests($text) {
        $text = $this->get_handler($text);
        $text = $this->post_handler($text);

        return $text;
    }


    /**
     * A general form GET handler.
     *
     * @param    string    $text    text for handling
     * @return    string
     */
    public function get_handler($text) {
        if ( !isset($_POST['add_to_form']) and
            !preg_match("/\<form(.*)GET\[(.*)\](.*)\<\/form\>/Us", $text) ) {
            $get_matchs = preg_match_all('/GET\[(.*)\]/Us', $text, $gets);

        } else {
            $get_matchs = 0;
        }

        if ( $get_matchs > 0 ) {
            foreach ( $gets[1] as $get ) {
                if (isset($_GET["$get"])) {
                    $text =  str_replace("GET[$get]", $_GET["$get"], $text);
                }
            }
            $text = preg_replace('/GET\[(.*)\]/Us', '', $text);
        }
        return $text;

    }

    /**
     * A general form POST handler.
     *
     * @param    string    $text    text for handling
     * @return    string
     */
    public function post_handler($text) {
        if ( !isset($_POST['add_to_form']) and
            !preg_match("/\<form(.*)POST\[(.*)\](.*)\<\/form\>/Us", $text) ) {
            $post_matchs = preg_match_all('/POST\[(.*)\]/Us', $text, $posts);

        } else {
            $post_matchs = 0;
        }

        if ( $post_matchs > 0 ) {
            foreach ( $posts[1] as $post ) {
                if (isset($_POST["$post"])) {
                    $text = str_replace("POST[$post]", $_POST["$post"], $text);
                }
            }
            $text = preg_replace('/POST\[(.*)\]/Us', '', $text);
        }
        return $text;
    }

} // end of Input class
