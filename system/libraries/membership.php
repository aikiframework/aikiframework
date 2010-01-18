<?php
if(!defined('IN_AIKI')){die('No direct script access allowed');}


class aiki_membership
{
	var $permissions;
	var $full_name;
	var $username;
	var $userid;
	var $group_level;

	function aiki_membership(){

		session_start();

	}

	function login ($username, $password){
		global $db, $layout;

		$password = stripslashes($password);
		$password = md5(md5($password));

		$get_user = $db->get_row("SELECT * FROM aiki_users where username='".$username."' and password='".$password."' limit 1");

		if($get_user and $get_user->username == $username and $get_user->password == $password){

			$host_name = $_SERVER['HTTP_HOST'];
			$user_ip = $this->get_ip();


			$usersession = $this->generate_session(100);
			//setcookie("usersession", $usersession, time()+31104000000, "/", $host_name, 0);
			$_SESSION['aiki'] = $usersession;

			$insert_session = $db->query("INSERT INTO aiki_users_sessions (`session_id`,`user_id`,`user_name`,`session_date`,`user_session`, `user_ip`) VALUES ('','$get_user->userid','$username',NOW(),'$usersession','$user_ip')");
			$update_acces = $db->query("UPDATE `aiki_users` SET `last_login`= NOW(),`last_ip`='$user_ip', `logins_number`=`logins_number`+1 WHERE `userid`='$get_user->userid' LIMIT 1");
			//echo '<META HTTP-EQUIV="refresh" content="1"><center><b>الرجاء الانتظار... جاري تسجيل الدخول</b></center>';
			//die();
			
		} else{
			echo '<center><b>Sorry wrong username or password</b></center>';
		}

	}

	function isUserLogged ($userid){
		global $db;
		$user_session = $db->get_var("SELECT user_id FROM aiki_users_sessions where user_session='$_SESSION[aiki]'");
		if ($user_session == $userid){
			return true;
		}else{
			return false;
		}
	}

	function getUserPermissions ($user){
		global $db;
		$user = mysql_escape_string($user);

		$user = $db->get_row("SELECT userid, usergroup, full_name, username FROM aiki_users where username='$user'");
		if ($user->userid and $this->isUserLogged($user->userid)){
			$group_permissions = $db->get_row("SELECT group_permissions, group_level FROM aiki_users_groups where id='$user->usergroup'");

			$this->full_name = $user->full_name;
			$this->username = $user->username;
			$this->group_level= $group_permissions->group_level;
			$this->userid = $user->userid;


		}else{
			$this->permissions = "";
		}

		$this->permissions = $group_permissions->group_permissions;
	}

	//function from Membership V1.0
	//http://AwesomePHP.com/gpl.txt
	function get_ip(){
		$ipParts = explode(".", $_SERVER['REMOTE_ADDR']);
		if ($ipParts[0] == "165" && $ipParts[1] == "21") {
			if (getenv("HTTP_CLIENT_IP")) {
				$ip = getenv("HTTP_CLIENT_IP");
			} elseif (getenv("HTTP_X_FORWARDED_FOR")) {
				$ip = getenv("HTTP_X_FORWARDED_FOR");
			} elseif (getenv("REMOTE_ADDR")) {
				$ip = getenv("REMOTE_ADDR");
			}
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	//Generate session
	function generate_session($strlen){
		return substr(md5(uniqid(rand(),true)),1,$strlen);
	}



	function LogOut(){
		global $db, $layout;
		$domain = $_SERVER['HTTP_HOST'];
		$path = $_SERVER['SCRIPT_NAME'];
		$queryString = $_SERVER['QUERY_STRING'];
		$thisurlnologout = "http://" . $domain . $path . "?" . $queryString;
		$thisurlnologout = str_replace("&operators=logout", "", $thisurlnologout);

		$make_offline = $db->query("UPDATE `aiki_guests` SET `is_online`='0' WHERE `guest_session`='$_SESSION[aiki]' LIMIT 1");
		$delete_session_data = $db->query("DELETE FROM aiki_users_sessions where user_session='$_SESSION[aiki]'");
		unset($_SESSION['aiki']);
		session_destroy();
		session_unset();
		$layout->html_output .= '<META HTTP-EQUIV="refresh" content="1;URL=http://'.$domain.$path.'"><center><b>الرجاء الإنتظار جاري تسجيل الخروج</b></center>';
		//die();
	}

}
?>