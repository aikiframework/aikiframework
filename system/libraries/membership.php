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
		global $db;

		session_start();

		if (!isset($username) and isset($_SESSION['aiki']))
		$username = $db->get_var("SELECT user_name FROM aiki_users_sessions where user_session='".$_SESSION['aiki']."'");

		if (isset($username)){
			$this->getUserPermissions($username);
		}else{
			$this->group_level = true;
			$this->permissions = 'ViewPublished';
		}

		$time_now = time();

		$user_ip = $this->get_ip();


		if (!isset($_SESSION['aiki']) and !isset($_SESSION['guest'])){

			$_SESSION['guest'] = $this->generate_session(100);
			$insert_session = $db->query("INSERT INTO aiki_users_sessions VALUES ('', '', 'guest' , '$time_now', '$time_now' ,'$_SESSION[guest]', '1', '$user_ip', '$user_ip')");

		}else{

			$update_guest = $db->query("UPDATE `aiki_users_sessions` SET `last_hit` = '$time_now' ,`last_ip`='$user_ip', `hits`=`hits`+1 WHERE `user_session`='$_SESSION[guest]' LIMIT 1");
		}

		//Delete inactive online users
		$last_hour = time()."-3600";
		$make_offline = $db->query("DELETE FROM `aiki_users_sessions` WHERE last_hit_unix < $last_hour");


	}

	function login ($username, $password){
		global $db, $layout, $config;

		$password = stripslashes($password);
		$password = md5(md5($password));

		$get_user = $db->get_row("SELECT * FROM aiki_users where username='$username' and password='$password' limit 1");

		if($get_user and $get_user->username == $username and $get_user->password == $password){

			$host_name = $_SERVER['HTTP_HOST'];
			$user_ip = $this->get_ip();


			$_SESSION['aiki'] = $_SESSION['guest'];
			//setcookie("usersession", $usersession, time()+31104000000, "/", $host_name, 0);

			$register_user = $db->query("UPDATE `aiki_users_sessions` SET `user_id`='$get_user->userid', `user_name` = '$get_user->username' WHERE `user_session`='$_SESSION[aiki]' LIMIT 1");

			if (!isset($config["allow_multiple_sessions"])){
				$delete_previous_open_sessions =$db->query("DELETE FROM `aiki_users_sessions` WHERE `user_session`!='$_SESSION[aiki]' and `user_name` = '$get_user->username' and `user_id`='$get_user->userid'");
			}

			$this->getUserPermissions($get_user->username);

			$update_acces = $db->query("UPDATE `aiki_users` SET `last_login`= NOW(),`last_ip`='$user_ip', `logins_number`=`logins_number`+1 WHERE `userid`='$get_user->userid' LIMIT 1");


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

			$this->permissions = $group_permissions->group_permissions;

		}else{
			$this->permissions = "";
		}

		//some one deleted the session record from aiki_users_sessions
		//hack attack red alert
		if (!isset($group_permissions) or !$group_permissions){
			unset($_SESSION['guest']);
			unset($_SESSION['aiki']);
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

		$make_offline = $db->query("UPDATE `aiki_guests` SET `is_online`='0' WHERE `guest_session`='$_SESSION[aiki]' LIMIT 1");
		$delete_session_data = $db->query("DELETE FROM aiki_users_sessions where user_session='$_SESSION[aiki]'");
		unset($_SESSION['aiki']);
		unset($_SESSION['guest']);
		session_destroy();
		session_unset();
		$layout->html_output .= '<META HTTP-EQUIV="refresh" content="1;URL=http://'.$domain.$path.'"><center><b>الرجاء الإنتظار جاري تسجيل الخروج</b></center>';
		//die();
	}

}
?>