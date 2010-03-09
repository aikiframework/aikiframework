<?php

/*
 * Aiki framework (PHP)
 *
 * @author		http://www.aikilab.com
 * @license		http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link		http://www.aikiframework.org
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


class aiki_membership
{
	var $permissions;
	var $full_name;
	var $username;
	var $userid;
	var $group_level;


	function aiki_membership(){
		global $db, $config;

		if (isset ($config["allow_guest_sessions"]) and $config["allow_guest_sessions"]){
			session_start();
		}elseif (@$_COOKIE["PHPSESSID"]){
			session_start();
		}

		if (!isset($username) and isset($_SESSION['aikiuser']))
		$username = $db->get_var("SELECT user_name FROM aiki_users_sessions where user_session='".$_SESSION['aikiuser']."'");

		if (isset($username)){
			$this->getUserPermissions($username);
		}else{
			$this->group_level = '';
			$this->permissions = '';
		}

		$time_now = time();

		//$user_ip = $this->get_ip();

		if (isset ($config["allow_guest_sessions"]) and $config["allow_guest_sessions"]){
			if (!isset($_SESSION['aikiuser']) and !isset($_SESSION['guest'])){

				$_SESSION['guest'] = $this->generate_session(100);
				$insert_session = $db->query("INSERT INTO aiki_users_sessions VALUES ('', '', 'guest' , '$time_now', '$time_now' ,'$_SESSION[guest]', '1', '', '')");

			}else{

				$update_guest = $db->query("UPDATE `aiki_users_sessions` SET `last_hit` = '$time_now' WHERE `user_session`='$_SESSION[guest]' LIMIT 1");
			}

		}elseif(isset($_SESSION['aikiuser'])){

			$update_guest = $db->query("UPDATE `aiki_users_sessions` SET `last_hit` = '$time_now' WHERE `user_session`='$_SESSION[aikiuser]' LIMIT 1");

		}

		if (isset($config["session_timeout"])){
			$timeout = $config["session_timeout"];
		}else{
			$timeout = 2000;
		}

		$last_hour = time()."-$timeout";
		$make_offline = $db->query("DELETE FROM `aiki_users_sessions` WHERE last_hit < $last_hour");


	}

	function login ($username, $password){
		global $db, $layout, $config;

		$password = stripslashes($password);
		$password = md5(md5($password));

		$time_now = time();

		if (!isset ($config["allow_guest_sessions"]) and !isset($_SESSION['aikiuser'])){
			session_start();
		}

		$get_user = $db->get_row("SELECT * FROM aiki_users where username='$username' and password='$password' limit 1");

		if($get_user and $get_user->username == $username and $get_user->password == $password){

			$host_name = $_SERVER['HTTP_HOST'];
			$user_ip = $this->get_ip();

			if (isset ($config["allow_guest_sessions"]) and $config["allow_guest_sessions"]){
				$_SESSION['aikiuser'] = $_SESSION['guest'];
			}else{
				$_SESSION['aikiuser'] = $this->generate_session(100);
			}

			if (isset ($config["allow_guest_sessions"]) and $config["allow_guest_sessions"]){
				$register_user = $db->query("UPDATE `aiki_users_sessions` SET `user_id`='$get_user->userid', `user_name` = '$get_user->username' WHERE `user_session`='$_SESSION[aikiuser]' LIMIT 1");
			}else{
				$register_user = $db->query("INSERT INTO aiki_users_sessions VALUES ('', '$get_user->userid', '$get_user->username' , '$time_now', '$time_now' ,'$_SESSION[aikiuser]', '1', '', '')");
			}

			if (!isset($config["allow_multiple_sessions"])){
				$delete_previous_open_sessions =$db->query("DELETE FROM `aiki_users_sessions` WHERE `user_session`!='$_SESSION[aikiuser]' and `user_name` = '$get_user->username' and `user_id`='$get_user->userid'");
			}

			$this->getUserPermissions($get_user->username);

			$update_acces = $db->query("UPDATE `aiki_users` SET `last_login`= NOW(),`last_ip`='$user_ip', `logins_number`=`logins_number`+1 WHERE `userid`='$get_user->userid' LIMIT 1");


		} else{
			die('<center><b>Sorry wrong username or password</b></center>');
		}

	}

	function isUserLogged ($userid){
		global $db;
		$user_session = $db->get_var("SELECT user_id FROM aiki_users_sessions where user_session='$_SESSION[aikiuser]'");
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

			$this->permissions = $group_permissions->group_permissions;

		}else{
			$this->permissions = "";
		}

		//some one deleted the session record from aiki_users_sessions
		//hack attack red alert
		if (!isset($group_permissions) or !$group_permissions){
			unset($_SESSION['guest']);
			unset($_SESSION['aikiuser']);
		}

	}

	//function from Membership V1.0
	//http://AwesomePHP.com/
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

		$make_offline = $db->query("UPDATE `aiki_guests` SET `is_online`='0' WHERE `guest_session`='$_SESSION[aikiuser]' LIMIT 1");
		$delete_session_data = $db->query("DELETE FROM aiki_users_sessions where user_session='$_SESSION[aikiuser]'");
		unset($_SESSION['aikiuser']);
		unset($_SESSION['guest']);
		session_destroy();
		session_unset();
		$layout->html_output .= '<META HTTP-EQUIV="refresh" content="1;URL=http://'.$domain.$path.'"><center><b>الرجاء الإنتظار جاري تسجيل الخروج</b></center>';
		//die();
	}

}
?>