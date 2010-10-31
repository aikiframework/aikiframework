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
 * @copyright   (c) 2008-2010 Aikilab
 * @license     http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @link        http://www.aikiframework.org
 * @category    Aiki
 * @package     Library
 * @filesource
 */

if(!defined('IN_AIKI')){die('No direct script access allowed');}


/**
 * BriefDescription
 *
 * @category    Aiki
 * @package     Library
 */
class membership
{
	public $permissions;
	public $full_name;
	public $username;
	public $userid;
	public $group_level;
	public $guest_session = '';
	public $user_session = '';


	public function membership(){
		global $db, $config;

		if (isset ($config["allow_guest_sessions"]) and $config["allow_guest_sessions"] != false){
			session_start();
		}elseif (@$_COOKIE["PHPSESSID"]){
			session_start();
		}

		if (!isset($username) and isset($_SESSION['aikiuser']))
		$username = $db->get_var("SELECT user_name FROM aiki_users_sessions where user_session='".$_SESSION['aikiuser']."'");

		if (isset($username)){
			$this->getUserPermissions($username);
		}else{
			$this->group_level = '1000000000';
			$this->permissions = '';
		}

		$time_now = time();

		if (isset ($config["allow_guest_sessions"]) and $config["allow_guest_sessions"]){

			if (!isset($_SESSION['aikiuser']) and !isset($_SESSION['guest'])){

				$user_ip = $this->get_ip();

				$_SESSION['guest'] = $this->generate_session(100);
				$insert_session = $db->query("INSERT INTO aiki_users_sessions VALUES ('', '', 'guest' , '$time_now', '$time_now' , '".$_SESSION['guest']."', '1', '$user_ip', '$user_ip')");

			}else{

				$update_guest = $db->query("UPDATE `aiki_users_sessions` SET `last_hit` = '$time_now' WHERE `user_session`='".$_SESSION['guest']."' LIMIT 1");
			}

		}elseif(isset($_SESSION['aikiuser'])){

			$update_guest = $db->query("UPDATE `aiki_users_sessions` SET `last_hit` = '$time_now' WHERE `user_session`='".$_SESSION['aikiuser']."' LIMIT 1");

		}

		if (isset($config["session_timeout"])){
			$timeout = $config["session_timeout"];
		}else{
			$timeout = 7200;
		}

		$last_hour = time()."-$timeout";
		$make_offline = $db->query("DELETE FROM `aiki_users_sessions` WHERE last_hit < $last_hour");

		if (isset($_SESSION['aikiuser'])){
			$this->user_session = $_SESSION['aikiuser'];
		}

		if (isset($_SESSION['guest'])){
			$this->guest_session = $_SESSION['guest'];
		}

	}

	public function login ($username, $password){
		global $db, $layout, $config;

		$password = stripslashes($password);
		$password = md5(md5($password));

		$time_now = time();

		if (!isset($_SESSION['aikiuser']) and !isset($_SESSION['guest']) and !isset($_COOKIE["PHPSESSID"])){
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
				$register_user = $db->query("UPDATE `aiki_users_sessions` SET `user_id`='".$get_user->userid."', `user_name` = '".$get_user->username."', `user_ip`='$user_ip' WHERE `user_session`='".$_SESSION['aikiuser']."' LIMIT 1");
			}else{
				$register_user = $db->query("INSERT INTO aiki_users_sessions VALUES ('', '".$get_user->userid."', '".$get_user->username."' , '$time_now', '$time_now' ,'".$_SESSION['aikiuser']."', '1', '$user_ip', '$user_ip')");
			}

			if ($config["allow_multiple_sessions"] == false){
				$delete_previous_open_sessions =$db->query("DELETE FROM `aiki_users_sessions` WHERE `user_session`!='".$_SESSION['aikiuser']."' and `user_name` = '".$get_user->username."' and `user_id`='".$get_user->userid."'");
			}

			$this->getUserPermissions($get_user->username);

			$update_acces = $db->query("UPDATE `aiki_users` SET `last_login`= NOW(),`last_ip`='$user_ip', `logins_number`=`logins_number`+1 WHERE `userid`='".$get_user->userid."' LIMIT 1");

		} else{
			echo '<center><b>Sorry wrong username or password</b></center>';
		}

	}

	public function isUserLogged ($userid){
		global $db;
		$user_session = $db->get_var("SELECT user_id FROM aiki_users_sessions where user_session='".$_SESSION['aikiuser']."'");
		if ($user_session == $userid){
			return true;
		}else{
			return false;
		}
	}

	public function getUserPermissions ($user){
		global $db;
		$user = mysql_real_escape_string($user);

		$user = $db->get_row("SELECT userid, usergroup, full_name, username FROM aiki_users where username='$user'");
		if ($user->userid and $this->isUserLogged($user->userid)){
			$group_permissions = $db->get_row("SELECT group_permissions, group_level FROM aiki_users_groups where id='".$user->usergroup."'");

			$this->full_name = $user->full_name;
			$this->username = $user->username;
			$this->group_level= $group_permissions->group_level;
			$this->userid = $user->userid;

			$this->permissions = $group_permissions->group_permissions;

		}else{
			$this->permissions = "";
		}

		//unset the browser session if the session
		//record was deleted from aiki_users_sessions
		if (!isset($group_permissions) or !$group_permissions){
			unset($_SESSION['guest']);
			unset($_SESSION['aikiuser']);
		}

	}

	public function get_ip(){
		if ( isset($_SERVER["REMOTE_ADDR"]) )    {
			return $_SERVER["REMOTE_ADDR"];
		} else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    {
			return $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )    {
			return $_SERVER["HTTP_CLIENT_IP"];
		}
	}

	//Generate session
	public function generate_session($strlen){
		return substr(md5(uniqid(rand(),true)),1,$strlen);
	}


	public function NewPassword($key){
		global $db, $aiki, $config;

		$is_user = $db->get_var("select userid, username from aiki_users where randkey = '$key'");
		if ($is_user){

			$form = '
<form method=\'post\'>
<div>
<table border=0>
  <tbody>
    <tr>
      <td><b>New Password:</b></td>
      <td><input class="input" type="password" dir="" name="password"></td>
    </tr>
<tr>
      <td><b>Confirm New Password:</b></td>
      <td><input class="input" type="password" dir="" name="password_confirm"></td>
    </tr>    
    <tr>
      <td></td>
      <td><input class="button" type="submit" name="submit" value="Set Password"></td>
    </tr>
<input type="hidden" value="'.$key.'" name="key">
  </tbody>
</table>
</div>
<form>			
';			

			if (!isset($_POST['password']) and !isset($_POST['password_confirm']) and !isset($_POST['key'])){

				return $form;

			}else{

				if ($_POST['password'] and $_POST['password_confirm'] and $_POST['key'] and $_POST['password_confirm'] == $_POST['password']){

					$password = md5(md5($_POST['password']));
					$update = $db->query("update aiki_users set password = '$password' where randkey = '".$_POST['key']."'");

					return "Password reseted! you can now login to your account";
				}else{

					return "Sorry: the two passwords don't match, please try again <br /> $form ";
				}


			}


		}else{
			return "Sorry worng or expired key";
		}

	}

	public function ResetPassword($input){
		global $db, $aiki, $config;

		$vars_array = str_replace('"', '', $input);
		$vars_array = str_replace("'", '', $vars_array);
		$vars_array = explode(',', $vars_array);

		$username = trim($vars_array['0']);
		$email = trim($vars_array['1']);
		$emailfrom = trim($vars_array['2']);
		$subject = trim($vars_array['3']);
		$message = trim($vars_array['4']);

		if (!$username and !$email){
			return '';
		}

		if (!$username){
			return 'Username is needed to reset your password';
		}

		if (!$email){
			return 'Email is needed to reset your password';
		}



		$is_user = $db->get_var("select userid from aiki_users where username = '$username' and email = '$email'");
		if (!$is_user){

			$is_user = $db->get_var("select userid from aiki_users where username = '$username'");
			if (!$is_user){

				return "The user: $username doesn't exists. please make sure you typed the name correctly";
			}else{

				return "The email address doesn't match the username";
			}

		}else{

			$randkey = md5(uniqid(rand(),true));

			$add_rand_key = $db->query("update aiki_users set randkey = '$randkey' where userid = '$is_user' limit 1");

			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=utf-8\r\n";
			$headers .= "From: $emailfrom\r\n";

			$message = $message."\n\r".
			"To reset your password please click this link: 
			<a href='".$config['url']."secure?key=".$randkey."'>".
			$config['url']."secure?key=".$randkey."</a>";

			mail($email,$subject,$message,$headers);
			return "an email had been sent to your address. please follow the link to reset your password";

		}


	}

	public function LogOut(){
		global $db;

		if (isset($_SESSION['aikiuser'])){

			$delete_session_data = $db->query("DELETE FROM aiki_users_sessions where user_session='".$_SESSION['aikiuser']."'");

			unset($_SESSION['aikiuser']);
			unset($_SESSION['guest']);
			session_destroy();
			session_unset();

			return "Logged out";
		}else{

			return "You are already logged out";
		}

	}

}
?>