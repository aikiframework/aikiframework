<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class aiki_input
{

	public function aiki_input(){
		global $aiki, $layout;

		foreach ($_GET as $key => $req){
			$req = $aiki->escape($req);
			$_GET[$key] = $req;
		}


		foreach ($_POST as $key => $req){

			$req = $aiki->escape($req);

			$_POST[$key] = $req;

			switch ($key){

				case "process":
					$key_request = "process";
					$process_type = $req;
					break;

				case "edit_form":
					$key_request = "edit_form";
					break;

				case "edit_form_keep_history":
					$key_request = "edit_form_keep_history";
					break;

				case "form_id":
					$form_id = $req;
					break;

				case "record_id":
					$record_id = $req;
					break;

			}

		}

		if (isset($key_request)){
			switch ($key_request){

				case "process":

					$this->form_handler($process_type, $_POST);

					break;

				case "edit_form":

					echo $aiki->records->edit_db_record_by_form_post($_POST, $form_id, $record_id);

					break;

				case "edit_form_keep_history":

					break;
			}
		}

	}

	public function validate($data){

		foreach ($data as $key => $req){
			$req = mysql_escape_string($req);
			$data[$key] = $req;
		}

		return $data;
	}

	public function form_handler($type, $post){
		global $membership;

		$post = $this->validate($post);
		switch ($type){
			case "login":
				$membership->login($post['username'], $post['password']);
				break;

		}

	}


	public function requests($text){

		$text = $this->get_handler($text);
		$text = $this->post_handler($text);

		return $text;
	}


	public function get_handler($text){

		if (!isset($_POST['add_to_form']) and !preg_match ("/\<form(.*)GET\[(.*)\](.*)\<\/form\>/Us", $text)){

			$get_matchs = preg_match_all('/GET\[(.*)\]/Us', $text, $gets);

		}else{

			$get_matchs = 0;
		}

		if ($get_matchs > 0){

			foreach ($gets[1] as $get){

				if (isset($_GET["$get"])){

					$text =  str_replace("GET[$get]", $_GET["$get"], $text);
				}

			}

			$text = preg_replace('/GET\[(.*)\]/Us', '', $text);

		}

		return $text;

	}

	public function post_handler($text){

		if (!isset($_POST['add_to_form']) and !preg_match ("/\<form(.*)POST\[(.*)\](.*)\<\/form\>/Us", $text)){

			$post_matchs = preg_match_all('/POST\[(.*)\]/Us', $text, $posts);

		}else{
			$post_matchs = 0;
		}

		if ($post_matchs > 0){

			foreach ($posts[1] as $post){

				if (isset($_POST["$post"])){

					$text =  str_replace("POST[$post]", $_POST["$post"], $text);
				}

			}

			$text = preg_replace('/POST\[(.*)\]/Us', '', $text);

		}


		return $text;
	}


}
?>