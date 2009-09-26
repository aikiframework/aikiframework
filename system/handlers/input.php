<?php

class aiki_input
{

	function aiki_input(){

		foreach ($_GET as $key => $req){
			$req = mysql_escape_string($req);
			$_GET[$key] = $req;
		}


		foreach ($_POST as $key => $req){
			$req = mysql_escape_string($req);
			$_POST[$key] = $req;

			if ($key == 'handle_form'){
				$this->form_handler($req, $_POST);
			}
		}


	}

	function validate($data){

		foreach ($data as $key => $req){
			$req = mysql_escape_string($req);
			$data[$key] = $req;
		}

		return $data;
	}

	function form_handler($type, $post){
		global $membership;

		$post = $this->validate($post);
		switch ($type){
			case "login":
				$membership->login($post['username'], $post['password']);
				break;

		}

	}

	function get_handler(){


	}

	function post_handler(){


	}


}
?>