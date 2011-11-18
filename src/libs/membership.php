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

/** @see AikiSession.php */
require_once("libs/AikiSession.php");

/** @see DatabaseSession.php */
require_once("libs/session/DatabaseSession.php");

/**
 * BriefDescription
 *
 * @category    Aiki
 * @package     Library
 */


class membership
{
    
	/**
	 * @var string  permissions for a user
	 */

	public $permissions;
	
	/**
	 * @var string a user's fullname
	 */
	public $full_name;
	/**
	 * @var string  the username of a user
	 */
	public $username;
	/**
	 * @var integer  the unique id of a user
	 */
	public $userid;
	/**
	 * @var string  really a number in a string for group level
	 */
	public $group_level;
	/**
	 * @var string  stored session variable
	 */
	public $guest_session = '';
	/**
	 * @var string  after user login, stored session variable
	 */
	public $user_session = '';

	/**
	 * Handles general session startup and setup of a guest or user/member.
	 * 
	 * @global	array	$db		global db instance
	 * @global	array	$config	global config instance
	 */
	public function membership() {
		global $db, $config, $log;
		
		try {
			/* Has user defined session handler as alternative
			 * to the default php file based session handler */
			$session = new AikiSession(new DatabaseSession($db));			
		}
		catch (AikiException $e) {
			$log->exception($e);		
		}
			

		$allowGuestSessions = isset($config["allow_guest_sessions"]) && 
							  $config["allow_guest_sessions"] ;
				
		if ( $allowGuestSessions || @$_COOKIE["PHPSESSID"] ) {
			session_start();
		}
		
		if (isset($_SESSION['aikiuser'])) {
			$username = $db->get_var("SELECT user_name FROM aiki_users_sessions where user_session='".$_SESSION['aikiuser']."'");
		}

		if (isset($username)) {
			$this->getUserPermissions($username);
			
		} else {			
			$this->group_level = '1000000000';
			$this->permissions = '';
		}

		$time_now = time();

		if ($allowGuestSessions) {
			if ( !isset($_SESSION['aikiuser']) and !isset($_SESSION['guest']) ) {
				$user_ip = $this->get_ip();

				$_SESSION['guest'] = $this->generate_session(100);
				$insert_session = $db->query(
					"INSERT INTO aiki_users_sessions".
					" VALUES ('', '', 'guest' , '$time_now', '$time_now' , '" .
					$_SESSION['guest']."', '1', '$user_ip', '$user_ip')");

			} else {
				$update_guest = $db->query("UPDATE `aiki_users_sessions` SET `last_hit` " .
					"= '$time_now' WHERE `user_session`='" . $_SESSION['guest'] . "' LIMIT 1");
			}

		} elseif (isset($_SESSION['aikiuser'])) {
			$update_guest = $db->query("UPDATE `aiki_users_sessions` SET `last_hit` = '$time_now' " .
				"WHERE `user_session`='" . $_SESSION['aikiuser'] . "' LIMIT 1");
		}

		$timeout =  isset($config["session_timeout"]) ? $config["session_timeout"] : 7200;
		
		$last_hour = time()."-$timeout";
		$make_offline = $db->query(
			"DELETE FROM `aiki_users_sessions` WHERE last_hit < $last_hour");

		if (isset($_SESSION['aikiuser'])) {
			$this->user_session = $_SESSION['aikiuser'];
		}
		if (isset($_SESSION['guest'])) {
			$this->guest_session = $_SESSION['guest'];
		}
	} // end of membership function


	/**
	 * Handles the login of a user.
	 * 
	 * @param	string			$username	name of user
	 * @param	string			$password	a user's password
	 * @global	array			$db		a global db instance
	 * @global	CreateLayout		$layout		a global layout instance
	 * @global	array			$config		a global config instance
	 * @global	aiki			$aiki		a global aiki instance
	 */
	public function login($username, $password) {
		global $db, $layout, $config, $aiki;
		 
		$password = stripslashes($password);
		$password = md5(md5($password));

		$time_now = time();
		
		
		if ( !isset($_SESSION['aikiuser']) and 
			!isset($_SESSION['guest']) and 
			!isset($_COOKIE["PHPSESSID"]) ) {
			session_start();
		}
		
		$get_user = $db->get_row(
			"SELECT * FROM aiki_users".
			" WHERE username='$username' ".
			"  AND password='$password' ".
			"  AND is_active=1" .		  
			" LIMIT 1");
		if ($get_user) {
			$host_name = $_SERVER['HTTP_HOST'];
			$user_ip   = $this->get_ip();

			if ( isset($config["allow_guest_sessions"]) and 
				$config["allow_guest_sessions"] ) {
				$_SESSION['aikiuser'] = $_SESSION['guest'];
				$register_user = $db->query("UPDATE `aiki_users_sessions` SET `user_id`='" .
					$get_user->userid . "', `user_name` = '" . $get_user->username .
					"', `user_ip`='$user_ip' WHERE `user_session`='" . $_SESSION['aikiuser'] . "' LIMIT 1");
			} else {
				$_SESSION['aikiuser'] = $this->generate_session(100);
				$register_user = $db->query("INSERT INTO aiki_users_sessions VALUES ('', '" . $get_user->userid .
				"', '" . $get_user->username . "' , '$time_now', '$time_now' ,'" . $_SESSION['aikiuser'] .
				"', '1', '$user_ip', '$user_ip')");
			}

			if ( !isset($config["allow_multiple_sessions"]) || $config["allow_multiple_sessions"] == false ) {
				$delete_previous_open_sessions = $db->query("DELETE FROM `aiki_users_sessions` WHERE" .
					" `user_session`!='" . $_SESSION['aikiuser'] . "' and `user_name` = '" .
					$get_user->username . "' and `user_id`='" . $get_user->userid . "'");
			}
			$this->getUserPermissions($get_user->username);

			$update_acces = $db->query("UPDATE `aiki_users` SET `last_login`= NOW(),`last_ip`='$user_ip'," .
				" `logins_number`=`logins_number`+1 WHERE `userid`='" . $get_user->userid . "' LIMIT 1");
				
			if ( $get_user->logins_number == 0 ) {
				$update_acces = $db->query("UPDATE `aiki_users` SET `first_login`= NOW(),`first_ip`=" .
				"'$user_ip' WHERE `userid`='" . $get_user->userid . "' LIMIT 1");
			}			
			
		} else {
			$aiki->message->set_login_error(__("Wrong username or password.") );
		}

	} // handle login function


	/**
	 * Checks to see if a user is logged in.
	 * 
	 * @param	integer	$userid	id of a user
	 * @global	array	$db		global db user
	 * @return	bool
	 */
	public function isUserLogged($userid) {
		global $db;
		
		$SQL = "SELECT user_id" .
		       " FROM aiki_users_sessions".
		       " WHERE user_session='{$_SESSION['aikiuser']}' and user_id='{$userid}'";		
		return  (is_null( $db->get_var($SQL))? false : true );

	}


	/**
	 * Get a user's permissions.
	 * 
	 * @param	string	$user	name of user
	 * @global	array	$db		global db instance
	 */
	public function getUserPermissions($user) {
		global $db;

		$user   = addslashes($user);
		$session= addslashes($_SESSION['aikiuser']);

		$SQL = "SELECT userid, usergroup, full_name, username,group_level,group_permissions".
		       " FROM aiki_users ".
		       " INNER JOIN aiki_users_sessions ON aiki_users.userid = aiki_users_sessions.user_id".
		       " INNER JOIN aiki_users_groups   ON aiki_users.usergroup= aiki_users_groups.id".
		       " WHERE aiki_users.username='$user' AND user_session='$session'";        
		$user = $db->get_row($SQL);		
		if ( $user )	{
			$this->full_name   = $user->full_name;
			$this->username    = $user->username;
			$this->userid      = $user->userid;			
			$this->group_level = $user->group_level;			
			$this->permissions = $user->group_permissions;
		} else {
			$this->permissions = "";	
			
			//unset the browser session if the session
			//record was deleted from aiki_users_sessions	
			unset($_SESSION['guest']);
			unset($_SESSION['aikiuser']);
		}
	}


	/**
	 * check is user is root (systemgod)
	 *  
	 * @return boolean 
	 */
	
	public function is_root() {
		return $this->permissions == "SystemGod";
	}

	/**
	 * check is user is root (systemgod)= is_root.
	 *  
	 * @return boolean 
	 */

	public function is_admin(){
		return $this->permissions=="SystemGod";
	}


	/**
	 * check permissions (permission or group_level)
	 * 
	 * @return boolean 
	 */
		
	public function have_permission($permission="SystemGOD") {
		global $db;
		if ( $permission=="SystemGOD" ) {
			return $this->permissions == "SystemGOD";
		} elseif ( $permission == $this->permissions ) {
			return true;
		}
		
		// permissions don't match. Try group level.
		$get_group_level = $db->get_var(
			"SELECT group_level from aiki_users_groups where group_permissions='$permission'");
		
		return ( !is_null($get_group_level) && $this->group_level < $get_group_level );
	}

	/**
	 * Attempt to get a user's ip address.
	 *
	 * @return string
	 */
	public function get_ip() {
		if (isset($_SERVER["REMOTE_ADDR"])) {
			return $_SERVER["REMOTE_ADDR"];
		} elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			return $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
			return $_SERVER["HTTP_CLIENT_IP"];
		}
	}


	/**
	 * Generate a safer session.
	 * 
	 * @param	integer	$strlen	length of session string
	 * @return	string
	 */
	public function generate_session($strlen) {
		return substr(md5(uniqid(rand(),true)),1,$strlen);
	}


	/**
	 * Generate a new password.
	 * 
	 * @param	string	$key	some random key
	 * @global	array	$db		global db instance
	 * @global	aiki	$aiki	global aiki instance
	 * @global	array	$config	global config instance
	 * @return	string
	 *
	 * @todo	good to remove the html if possible.
	 * @todo	rename this function to newPassword
	 * @todo	check to make sure that the returned string is being output
	 *		with a message class.
	 */
	public function NewPassword($key) {
		global $db, $aiki, $config;

		$is_user = $db->get_var("SELECT userid, username FROM aiki_users WHERE randkey = '$key'");
		if ($is_user) {
			$form = '
<div id="form_container">
  <form method="post" enctype="multipart/form-data" id="reset_password_form" name="reset_password_form">

  <fieldset class="fields">
	<div class="password field">
	  <label for="password">' . __("New Password") . '</label>
	  <input class="input" type="password" dir="" name="password">
	 </div>
	 
	 <div class="password_confirm field">
		<label for="password_confirm">' . __("Confirm New Password") . '</label>
		<input class="input" type="password" dir="" name="password_confirm">
	 </div>
   </fieldset>

   <fieldset class="buttons">
	  <input type="hidden" value="'.$key.'" name="key">
	  <input class="button" type="submit" name="submit" value="' . __("Set Password") .'">
	</fieldset>
<form>
</div>
';				

			if ( !isset($_POST['password']) and 
				!isset($_POST['password_confirm']) and 
				!isset($_POST['key']) ) {
				return $form;
			} else {
				if ( $_POST['password'] and 
					$_POST['password_confirm'] and 
					$_POST['key'] and 
					$_POST['password_confirm'] == $_POST['password'] ) {
					$password = md5(md5($_POST['password']));
					$update = $db->query("UPDATE aiki_users SET password = '$password' WHERE randkey = '" .
						$_POST['key'] . "'");

					return $aiki->message->ok("Your password has been reset. You can now log in to your account.", NULL, false);
				} else {

					$error_message = $aiki->message->error("The two passwords do not match. Please try again.", NULL, false);
					return $error_message . $form;
				}
			}

		} else {
			return "The key was incorrect or has expired.";
		}

	} // end of newPassword function


	/**
	 * Resets a user's password and alerts them.
	 * 
	 * @param	string	$input		input string for attempting password reset
	 * @global	array	$db			global db instance
	 * @global	aiki	$aiki		global aiki instance
	 * @global	array	$config		global config options instance
	 * @return	string
	 *
	 * @todo	really the view should be separated out from this function
	 * @todo	Change this to resetPassword
	 */
	public function ResetPassword($username, $email, $emailfrom, $subject, $message) {
		global $db, $aiki, $config;

		if ( !$username and !$email ) {
			return '';
		}
		if (!$username) {
			return $aiki->message->warning('You must provide your username in order to reset your password.', NULL, false);
		}

		if (!$email) {
			return $aiki->message->warning('You must enter the email address you used to sign up for the account.', NULL, false);
		}

		$is_user = $db->get_var("SELECT userid FROM aiki_users WHERE username = '$username' AND email = '$email'");
		if (!$is_user) {
			$is_user = $db->get_var("SELECT userid FROM aiki_users WHERE username = '$username'");
			if (!$is_user) {
				return $aiki->message->error( __sprintf("The user %s doesn't exist. Make sure you typed the name correctly.",$username), NULL, false);
			} else {
				return $aiki->message->error( __("The email address and username do not match what we have on file."), NULL, false);
			}

		} else {

			/**
			 * @todo emailing should be separated out into its own class
			 * and function.
			 */
			$randkey = md5(uniqid(rand(),true));

			$add_rand_key = $db->query("update aiki_users set randkey = '$randkey' where userid = '$is_user' limit 1");

			$headers  = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=utf-8\r\n";
			$headers .= "From: $emailfrom\r\n";

			$message = $message."\n\r".
			__("To reset your password please click this link:"). " ".
			"<a href='".$config['url']."secure?key=".$randkey."'>".
			$config['url']."secure?key=".$randkey."</a>";

			mail($email,$subject,$message,$headers);
			return $aiki->message->ok( __("An email has been sent to your address. Please follow the link to reset your password."), NULL, false);

		}

	} // end of resetPassword function


	/**
	 * Handle logging out a user.
	 * 
	 * @global	array	$db		a global db instance
	 * @global	aiki	$aiki	a global aiki instance
	 * @return	string
	 *
	 * @todo	rename this function to logOut
	 */
	public function LogOut()  {
		global $db, $aiki;

		if (isset($_SESSION['aikiuser'])) {
			$delete_session_data = $db->query("DELETE FROM aiki_users_sessions where user_session='" . 
				$_SESSION['aikiuser'] . "'");

			unset($_SESSION['aikiuser']);
			unset($_SESSION['guest']);
			session_destroy();
			session_unset();

			return $aiki->message->ok(__("Logged out."), NULL, false);
		} else {
			return $aiki->message->warning(__("You are already logged out."), NULL, false);
		}
	} // end of logOut function


    
    /**
     * return number of registered user online.
     *
     * @return integer 
     * @global $db
     */
    
    function how_many_are_online(){
		global $db;
		return $db->get_var("SELECT count(DISTINCT user_id) FROM aiki_users_sessions");
	}	
		

    /**
     * Give a list (ul/li) of online users
     *
     *  format is the  sprintf format used for generate each line, between li.
     *  examples:  
     *   '%s' (default) display username
     *   '<a href='user-detail/%$s'>%1$s</a>'  a link to user page.
     *  $format receives two arguments in this order: user_name, user_id.
     * @todo pagination 
     
     * @return string user list in html using ul/li tags.
     * @global $db
     */
    
    function who_is_online( $format = '%s', $max=100, $id="who-is-online"){

		global $db;
		
		$max= (int) $max;
		$max=  (  $max <=0 || $max > 100 ?  100 : $max);
		
		$count= 0;
		$output="<ul id='$id' >";
		$users= $db->get_results("SELECT user_id, user_name FROM aiki_users_sessions");		
		if ( !is_null($users) ){
			foreach ($users as $user){		
				$output .= sprintf("<li>{$format}</li>", $user->user_name, $user->user_id );
				$count++;
				if ( $count > 100) {
				    // @todo pagination of result.
					break;
				}	
			}
		} else {
			$output .= "<li>" . __("Nobody is online") . "</li>";
		}
		$output .= "</ul>";
		return $output;		
	}



} // end of membership class

// NOTE: closing php necessary in this file
?>
