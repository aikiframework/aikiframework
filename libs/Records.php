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
 * @category	Aiki
 * @package	 Library
 * 
 * @todo this class and its functions need serious refactoring 
 * @todo there is massive code that is used in multiple places that can
 * be extracted and abstracted
 */
class Records {
	/**
	 * @var		bool		???
	 * @todo	needs to be traced, no idea what it does
	 */
	public $stop;

	/**
	 * @var		string		???
	 */
	public $file_name;

	/**
	 * @var	 integer		???	
	 */
	public $file_size;

	/**
	 * @var	 string		???
	 */
	public $checksum_sha1;

	/**
	 * @var	 string		???
	 */
	public $checksum_md5;

	/**
	 * @var	 integer		???
	 */
	public $width;

	/**
	 * @var		integer		???
	 */
	public $height;

	/**
	 * @var	 string		???
	 */
	public $rand;

	/**
	 * @var	 string		???
	 */
	public $mime_type;

	/**
	 * @var		bool
	 */
	public $form_insert_success;

	/**
	 * @var		string		defines allowed_extensions if not in the configs
	 * @access	private
	 *
	 * @todo looks like aiki harcodes allowed extensions. This is really a 
	 * define and should be moved out of here.
	 */
	private $allowed_extensions = "jpg|gif|png|jpeg|svg|pdf"; 


	/**
	 * Does a record exist.
	 *
	 * @param	string	$value		a generic value to check the db for
	 * @param	string	$tablename	name of a table to check value against
	 * @param	sting	$field		fielname as a type of key
	 * @global	array	$db			global db instance
	 * @return	bool
	 */
	public function record_exists($value, $tablename, $field) {
		global $db;

		$get_value = $db->get_var("SELECT $field from " . 
					"$tablename where $field='$value'");

		if ( $get_value and $get_value == $value ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Checks if a file exists by its sha1 hash
	 * 
	 * @param	string	$tablename	tablename to check if file exists in
	 * @param	string	$sha1		sha1 of a file to test against stored value
	 * @global	array	$db			global db instance
	 * @return	bool
	 */
	public function file_exists_sha1($tablename, $sha1) {
		global $db;

		$get_value = $db->get_var("SELECT filename from " . 
					 "$tablename where sha1='$sha1'");
		if ($get_value) {
			return $get_value;
		} else {
			return false;
		}
	}

	/**
	 *  Locks a document.
	 * 
	 * @param	string	$pkey		name of a db table's field
	 * @param	mixed	$pkey_value	
	 * @param	string	$tablename	name of a table
	 * @return string
	 *
	 * @todo rip out the output html and put the new message class in itsplace!
	 * and test it well!
	 */
	public function lockdocument($pkey, $pkey_value, $tablename) {
		global $db, $membership;

		if ( $pkey and $tablename ) {

			$not_editable = $db->get_var("select is_editable from " . 
				"$tablename where $pkey='$pkey_value' limit 1");

			if (!$not_editable) {
				$currentdatetime = time();

				/** 
				 * @TODO "Date output control" and what does that mean?
				 */
				$datetime = date('m/d/y g:ia', $currentdatetime);

				/**
				 * @todo where bare html needs to be replaced, actually, at
				 * the place of usage!
				 */
				$is_editable = "__warning__ <b>$membership->full_name</b>" . 
							   "__started_editing__: $datetime";

				$lockdocument = $db->query("update $tablename " . 
				"set is_editable = '$is_editable' where $pkey='$pkey_value'");
			} else {
				return "<br />" . $not_editable . "<br /><br />";
			}
		}
	} // end of function


	/**
	 * Unlocks a document, which is a database setting.
	 *
	 * @param	string		$pkey		database table field name
	 * @param	mixed		$pkey_value	various values to search against
	 * @param	string		$tablename	name of table to look in
	 * @global	array		$db			global db instance
	 * @global	membership	$membership	global membership instance
	 */
	public function unlockdocument($pkey, $pkey_value, $tablename)
	{
		global $db, $membership;
		if ( $pkey and $tablename ) {
			$lockdocument = $db->query("update $tablename " . 
				"set is_editable = null where $pkey='$pkey_value'");
		}
	}


	/**
	 * Inserts content from a form to the database.
	 *
	 * @param	array		$post
	 * @param	integer		$form_id
	 * @param	integer		$form_posted_id
	 * @global	array		$db				global db instance
	 * @global	aiki		$aiki			global aiki instance
	 * @global	membership	$membership		global membership instance
	 * @global	array		$config			global config options
	 * @global	string		$AIKI_ROOT_DIR	path to system folder
	 * @return	string
	 */
	public function insert_from_form_to_db($post, $form_id, $form_posted_id) {
		global $db, $aiki, $membership, $config, $AIKI_ROOT_DIR;

		if (!$form_posted_id) {
			return '';
		}
		if (!$post) {
			return '';
		} else {
			$post = unserialize($post);

			if (empty($post)) {
				return '';
			} else {
				foreach ( $post as $post_value ) {
					if ( $post_value and $post_value != '' ) {
						$found_a_value = true;
					}
				}
			}
		}

		if (!isset($found_a_value)) {
			return '';
		}
		$output_result = "";
		$insert_values = "";
		$tableFields = "";
		$preinsertQuery = "";
		$values_array = array();

		$form = $db->get_row("SELECT * from aiki_forms where ".
							 "id='$form_id' limit 1");

		$form_array = unserialize($form->form_array);

		$arraykeys = array_keys($form_array);

		if (in_array("tablename", $arraykeys)) {
			$tablename = $form_array["tablename"];
		}
		if (in_array("submit", $arraykeys)) {
			$submit = $form_array["submit"];
		}
		if (in_array("permission", $arraykeys)) {
			$permission = $form_array["permission"];
		} else {
			$permission = '';
		}
		if (in_array("send_email", $arraykeys)) {
			$send_email = $form_array["send_email"];
		}
		if (in_array("events", $arraykeys)) {
			$events = $form_array["events"];
		}
		if (in_array("pkey", $arraykeys)) {
			$pkey = $form_array["pkey"];
		} else {
			$pkey = 'id';
		}
		if (isset($_POST['unique_filename'])) {
			$unique_filename = $_REQUEST['unique_filename'];
		}
		if (isset($_POST['multifiles_plupload'])) {
			$multifiles_plupload = $_POST['multifiles_plupload'];
		}
		$insertQuery = "insert into $tablename ";
		$i = 0;
		$insertCount = count($form_array);

		foreach( $form_array as $field ) {

			$field = $aiki->url->apply_url_on_query($field);
			$field = $aiki->SqlMarkup->sql($field);

			$intwalker = explode(":", $field);

			$get_permission_and_man_info = explode("|", $intwalker[0]);

			if (isset($get_permission_and_man_info[1])) {
				$get_group_level = $db->get_var ("SELECT group_level from ".
					"aiki_users_groups where group_permissions='".
					"$get_permission_and_man_info[1]'");
			}

			$intwalker[0] = $get_permission_and_man_info[0];

			// Security Check to remove unauthorized POST data
			if (isset($get_group_level)) {
				if ( isset($get_permission_and_man_info['1']) and 
					$get_permission_and_man_info[1]==$membership->permissions or
					$membership->group_level < $get_group_level ) {
					/**
					 * @todo uh, there is nothing here, can this be reversed?
					 */
				} elseif (isset($get_permission_and_man_info[1])) {
					$_POST[$intwalker[0]] = '';
				}
			}

			if ( isset($get_permission_and_man_info[2]) and 
				$get_permission_and_man_info[2] == "true" and 
				!$_POST[$intwalker[0]] ) {
				/**
				 * @todo trace this down and integrate bare html into messages
				 */
				$output_result .= 
					"__warning__ __please_fill__ $intwalker[1]<br />";
				$this->stop = true;
			}

			if ( $insertCount == $i+1 ) {
				$insert_values .= "'" . $intwalker[0] . "'";
			} else {
				$insert_values .= "'" . $intwalker[0] . "', ";
			}
			if (isset($intwalker['2'])) {
				switch ($intwalker['2']) {
					case "full_path":
						//get full path dir value from post
						$full_path = $_POST[$intwalker[0]];
						if (!$full_path and isset($config["upload_path"])) {
							$full_path = 
								$aiki->processVars($config["upload_path"]);
							$_POST[$intwalker[0]] = $full_path;
						}
						break;

					case "password":
						/**
						 * @todo need to handle bar output
						 */
						if (!$_POST[$intwalker[0]]) {
							$output_result .= "__please_enter_a_password__";
							$this->stop = true;
						}

						if ($intwalker[3] and $_POST[$intwalker[0]]) {
							$num_levels = explode("|", $intwalker[3]);
							foreach ( $num_levels as $crypt_level ) {
								$_POST[$intwalker[0]] = 
									md5(stripcslashes($_POST[$intwalker[0]]));
							}
						}
						break;

					case "value":
						$_POST[$intwalker[0]] = 
							$aiki->url->apply_url_on_query($intwalker[3]);

						$_POST[$intwalker[0]] = 
							$aiki->processVars($_POST[$intwalker[0]]);

						$_POST[$intwalker[0]] = 
						$aiki->url->apply_url_on_query($_POST[$intwalker[0]]);

						$values_array[$intwalker[0]] = $_POST[$intwalker[0]];

						break;

					case "rand":
						$_POST[$intwalker[0]] = 
							substr(md5(uniqid(rand(),true)),1,15);
						$this->rand = $_POST[$intwalker[0]];
						break;

					case "email":

						/**
						 * @todo need to rip out this string
						 */
						if (!$aiki->text->is_valid("email",
								$_POST[$intwalker[0]])) {
							$output_result .= 
								"__the_email_address_is_not_valid__";
							$this->stop = true;
						}
						break;

					case "datetime":
						$_POST[$intwalker[0]]= 'NOW()';
						break;

					case "unique":
						/**
						 * @todo ripout this string
						 */
						if ($this->record_exists($_POST[$intwalker[0]], 
								$tablename, $intwalker[0])) {
							$output_result .= 
								"__this_value_is_already_in_use__";
							$this->stop = true;
						}
						break;

					/**
					 * @todo orderby is not implemented!
					 */
					case "orderby":
						break;

					case "mime_type":
						if (isset($this->mime_type)) {
							$_POST[$intwalker[0]] = $this->mime_type;
						}
						break;

					case "upload_file_name":
						if (isset($this->file_name)) {
							$_POST[$intwalker[0]] = $this->file_name;
						}
						break;

					case "upload_file_size":
						if (isset($this->file_size)) {
							$_POST[$intwalker[0]] = $this->file_size;
						}
						break;

					case "width":
						if (isset($this->width)) {
							$_POST[$intwalker[0]] = $this->width;
						}
						break;

					case "height":
						if (isset($this->hight)) {
							$_POST[$intwalker[0]] = $this->hight;
						}
						break;

					case "checksum_sha1":
						if (isset($this->checksum_sha1)) {
							$_POST[$intwalker[0]] = $this->checksum_sha1;
						}
						break;

					case "checksum_md5":
						if (isset($this->checksum_md5)) {
							$_POST[$intwalker[0]] = $this->checksum_md5;
						}
						break;

					case "plupload":

						$plupload_files = array();

						if ( isset($_POST['multifiles_plupload']) and 
							isset($_POST[$intwalker[0].'_count']) and 
							$_POST[$intwalker[0].'_count'] > 0 ) {
							$total_uploaded_files = 
								$_POST[$intwalker[0].'_count'];

							for ( $i = 0; $i < $_POST[$intwalker[0] . '_count']; $i++ ) {
								if (isset($_POST[$intwalker[0] . "_" . $i . "_status"]) and
									$_POST[$intwalker[0] . "_" . $i."_status"] ==  "done" ) {
									$plupload_files[$i] = 
										$_POST[$intwalker[0]."_".$i."_name"];
								}
							}
							$_POST[$intwalker[0]] = "__FILE__";
						}
						break;
				}
			} // end of foreach loop


			if ( !isset($full_path) and isset($config["upload_path"]) ) {
				$full_path = $aiki->processVars($config["upload_path"]);
			} else {
				$full_path = $aiki->processVars($full_path);
			}
			// need a unique filename for processing
			if ( isset($unique_filename) and 
				isset($intwalker[2]) and 
				$unique_filename == $intwalker[2] and 
				$full_path ) {
				$uploadexploded = explode(":", $intwalker[0]);

				$filename = $_FILES[$uploadexploded[0]];
				$filename = str_replace(" ", "_", $filename);

				$name = $filename['name'];
				$name = str_replace(" ", "_", $name);

				$this->file_name = $name;

				$path = $config['top_folder'] . "/" . $full_path . "";

				$tmp_filename = $filename['tmp_name'];

				$this->file_size = filesize($tmp_filename);

				if (function_exists("mime_content_type")) {
					$this->mime_type = @mime_content_type($tmp_filename);
				}
				$this->checksum_sha1 = @sha1_file($tmp_filename);
				$this->checksum_md5 = @md5_file($tmp_filename);
				$size = @getimagesize($tmp_filename);
				if ($size) {
					$this->width = $size["0"];
					$this->hight = $size["1"];
				}

				if ($tmp_filename) {
					$tmp_filesize = filesize($tmp_filename);
					if ( $tmp_filesize == 0 ) {
						return "__error_while_uploading__";
					}
					if (!isset($config['allowed_extensions'])) {
						$config['allowed_extensions'] = 
							$this->allowed_extensions;
					}
					if (!preg_match("/^[a-zA-Z0-9\-\_\.]+\.(".
						$config['allowed_extensions'].")$/i",$name)) {
						return "__not_valid_filename__";
					}

					$filename_array = explode(".", $name);
					/**
					 * @todo there must be a better way to loop to find var
					 */
					foreach ( $filename_array as $type_value ) {
						//just an empty loop to get the latest match
					}
					$type = $type_value;

					$exists_filename = $this->file_exists_sha1($tablename, 
									   $this->checksum_sha1);
					/**
					 * @todo rip out this bare string
					 */
					if ($exists_filename) {
						return "__file_is_already_uploaded__ $exists_filename";
					}
					//check if filename already exists
					if ( !file_exists($path.$name) and 
						!$this->record_exists($name, $tablename,$intwalker[0]) ) {
						$newfile = $path.$name;
					} else {
						$current_time = time();
						$name = $current_time.".".$type;
						$newfile = $path.$name;
					}

					if (!file_exists($newfile)) {
						@$result = move_uploaded_file($tmp_filename, $newfile);
						if (!$result)  {
							if (@mkdir($path, 0775, true)) {
								/**
								 * @todo ripout strings~
								 */
								$output_result .= 
									"__new_directory_created__ $path";
								@$result = move_uploaded_file($tmp_filename,
											  $newfile);
							} else {
								return "__folder_not_found__";
							}
						}
						/** 
						 * @TODO: keep original file name for insert into 
						 * original_filename field
						 */

					} else {
						/**
						 * @todo ripout this string
						 */
						$output_result .= ("__sorry__ __the_file__ '" . 
									"$newfile' __already_exists__");
					}
				} else {
					/**
					 * @todo ripout this string
					 */
					return "__please_choose_a_file_to_upload__";
				}

				$_POST[$intwalker[0]] = $name;

			} // end of long if to handle unique filename

			if (!isset($send_email)) {
				$send_email = '';
			}
			if (!isset($submit)) {
				$submit = '';
			}
			if (!preg_match("/\-\>/Us", $intwalker[0])) {
				if ( $field != $tablename and 
					$field != $permission and 
					$field != $send_email and 
					$field != $submit and 
					isset($_POST[$intwalker[0]]) and 
					$_POST[$intwalker[0]] ) {
					if ( $insertCount == $i+1 ) {
						$tableFields .=$intwalker[0];
						$preinsertQuery .= "'".$_POST[$intwalker[0]]."'";
					} else {
						$tableFields .= $intwalker[0].", ";
						$preinsertQuery .= "'".$_POST[$intwalker[0]]."', ";
					}
				}
			} else {
				$delimitered_field = explode("->", $intwalker[0]);

				$secondery_queries[$delimitered_field[1]][$delimitered_field[0]] = $_POST[$delimitered_field[0]."->".$delimitered_field[1]];
			}

			$i++;
		} // end of foreach loop

		/** 
		 * verify captcha
		 * @todo rip out this captcha stuff!
		 */
		if (isset($form_array["captcha"])) {
			switch ($form_array["captcha"]) {
				case "default":
					if ($_POST['default_captcha']) {
						$captcha_input = md5($_POST['default_captcha']);
					}
					if ( !isset($captcha_input) or 
						!$captcha_input or 
						$captcha_input != $_SESSION['captcha_key'] ) {
						/** 
						 * @todo ripout bare strings."
						 */
						$output_result .= "Input error: invalid captcha";
						$this->stop = true;
					}
					break;
			}
		}

		/**
		 * @todo need to track down what this $stop variable does
		 */
		if (!$this->stop) {
			$insertQuery .= "($tableFields) values ($preinsertQuery)";
			$insertQuery = str_replace(', )', ')', $insertQuery);
			$insertQuery = str_replace("'NOW()'", 'NOW()', $insertQuery);
			/** 
			 * @todo is this for testing or? need to kill or keep
			 */
			//die("$insertQuery");

			//handle multi files insert query
			if ( isset($_POST['multifiles_plupload']) and 
				isset($_POST[$intwalker[0].'_count']) and 
				$_POST[$intwalker[0].'_count'] > 0 ) {
				$num_of_uploaded_files = 0;
				$files_names_output = "";
				$not_uploaded_output = "";
				for ( $i=0; $i<$_POST[$intwalker[0].'_count']; $i++ ) {
					$plupload_files[$i] = str_replace(" ", "_", 
						$plupload_files[$i]);

					$multi_files_query = str_replace('__FILE__', 
						$plupload_files[$i], $insertQuery);

					$plupload_filename = $plupload_files[$i];
					$plupload_filename = preg_replace('/\.svg$/Us', "", 
						$plupload_filename);
					$plupload_filename = str_replace("_", " ", $plupload_filename);
					$multi_files_query = str_replace('plupload_filename', 
						$plupload_filename, $multi_files_query);

					if ( isset($_POST[$intwalker[0]."_".$i."_status"]) and 
						$_POST[$intwalker[0]."_".$i."_status"] == "done" ) {
						if (preg_match("/^[a-zA-Z0-9\-\_\.]+\.(".
							$config['allowed_extensions'].")$/i",
							$plupload_files[$i])) {
							if (!$this->record_exists($plupload_files[$i], 
								$tablename, $intwalker[0])) {
								$files_names_output .= 
									"$plupload_files[$i] <br />";
								$insertResult = $db->query($multi_files_query);
								$num_of_uploaded_files++;
							} else {
								/**
								 * @todo rip out hardcoded strings
								 */
								$not_uploaded_output .= 
								"$plupload_files[$i] __file_already_exists__";
							}
						} else {
							/**
							 * @todo rip out hardcoded strings
							 */
							$not_uploaded_output .= 
								"$plupload_files[$i] __not_allowed_file_name__";
						}
					} else {
						/** 
						 * @todo rip out hardcoded strings
						 */
						$not_uploaded_output .= 
							"$plupload_files[$i] __file_upload_fail__";
					}
				}
			} else {
				$insertResult = $db->query($insertQuery);
				$this_pkey =  mysql_insert_id();

				if ( isset($secondery_queries) and 
					is_array($secondery_queries) ) {
					foreach ( $secondery_queries as $table => $secondery_query ) {
						$secondery_insert_query = "";
						$secondery_insert_query .= "INSERT into $table (";

						foreach ( $secondery_query as 
								 $field_name => $field_value ) {
							$secondery_insert_query .= "$field_name, ";
						}
						$secondery_insert_query = 
							preg_replace("/\, $/", "", $secondery_insert_query);

						$secondery_insert_query .= ") values (";

						foreach ( $secondery_query as 
								 $field_name => $field_value ) {
							$field_value = str_replace("this_pkey", 
										  $this_pkey, $field_value);
							$field_value = $aiki->SqlMarkup->sql($field_value);

							$field_value_var = 
								$aiki->get_string_between($field_value, 
											 '[', ']');
							if ($field_value_var) {
								$field_value = 
									str_replace('['.$field_value_var.']', 
									$_POST["$field_value_var"], $field_value);
							}

							$secondery_insert_query .= "'".$field_value."', ";
						}
						$secondery_insert_query = 
							preg_replace("/\, $/", "", $secondery_insert_query);

						$secondery_insert_query .= ")";

						$secondery_insert_query = 
							str_replace("'NOW()'", "NOW()", 
								$secondery_insert_query);

						$secondery_result = $db->query($secondery_insert_query);
					}
				}
			} // end of else statement

			if (isset($insertResult)) {
				/**
				 * @todo rip out hardcoded strings
				 */
				$output_result .= "__added_successfully__";
				$this->form_insert_success = true;

				if ( isset($num_of_uploaded_files) and 
					$num_of_uploaded_files ) {
					/**
					 * @todo rip out hardcoded strings
					 */
					$output_result .= "__uploaded__ <b>" . 
						"$num_of_uploaded_files</b> __files_out_of__ " . 
						"<b>$total_uploaded_files</b> __selected_files__";
					$output_result .= "__uploaded_files__".$files_names_output;
				}
				/** 
				 * @todo rip out hardcoded strings
				 */
				if (isset($not_uploaded_output) and $not_uploaded_output) {
					$output_result .= 
						"__not_uploaded_files__".$not_uploaded_output;
				}
				/**
				 * @todo this should be a config option and a global way of
				 * handlign email.
				 */
				if ($send_email) {
					$send_email = explode("|", $send_email);

					$get_email = 
						$aiki->get_string_between($send_email[0], '[', ']');

					if ($get_email)
						$send_email[0] = $_POST["$get_email"];

					$get_from = 
						$aiki->get_string_between($send_email[1], '[', ']');
					if ($get_from)
						$send_email[1] = $_POST[$get_from];

					$message = $send_email[3];
					$count = preg_match_all( '/\[(.*)\]/U', $message, $matches );
					foreach ( $matches[1] as $parsed ) {
						if (isset($_POST[$parsed])) {
							$message = str_replace("[$parsed]", 
									$_POST[$parsed], $message);
						}
					}

					$from = $send_email[1];
					$headers  = "MIME-Version: 1.0\r\n";
					$headers .= "Content-type: text/html; charset=utf-8\r\n";
					$headers .= "From: $from\r\n";

					$message = nl2br($message);

					$send_email[2] = $aiki->processVars($send_email[2]);
					$message = $aiki->processVars($message);

					mail($send_email[0],$send_email[2],$message,$headers);
				} // end of handling email

				/**
				 * @todo rip out hardcoded strings and html
				 */
				if (isset($filename)) {
					$output_result .= "__filename__";
					$output_result .= "<p dir='ltr'>".$name."</p>";
				}
			} else {
				$output_result = "__error_inserting_into_database__";
				$output_result .= "Nothing uploaded <br />";
				if (isset($not_uploaded_output) and $not_uploaded_output)
					$output_result .= 
						"__not_uploaded_files__".$not_uploaded_output;
			}

			if (isset($events)) {
				$events_loop = explode("|", $events);
				foreach ( $events_loop as $event ) {
					preg_match_all( '/\[(.*)\]/U', $event, $matches );
					foreach ( $matches[1] as $parsed ) {
						$event = str_replace("[$parsed]", 
									$values_array["$parsed"], $event);
					}
					$event = str_replace("this_pkey", $this_pkey, $event);
					$event = $aiki->url->apply_url_on_query($event);

					$event = explode(":", $event);

					switch ($event[0]) {
						case "upload_success":
							if ( isset($filename) and isset($name) ) {
								$event[1] = $config['url'].$event[1];

								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $event[1]);
								curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);

								ob_start();
								curl_exec($ch);
								curl_close($ch);
								$content = ob_get_contents();
								ob_end_clean();

								if ( $content !== false ) {
									echo $content;
								}
							}
							break;

						case "on_submit":
							switch ($event[1]) {
								case "redirect":
									if (isset ($event[2])) {
										header("Location: $event[2]");
									}
									break;

								default:
									$event[1] = $config['url'].$event[1];

									$ch = curl_init();
									curl_setopt($ch, CURLOPT_URL, $event[1]);
									curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
									ob_start();
									curl_exec($ch);
									curl_close($ch);
									$content = ob_get_contents();
									ob_end_clean();

									if ($content !== false) {
										echo $content;
									}
									break;

							}
							break;
					} // end of switch
				} // end of event loop
			}
		} // end of mysterious stop or not
		return $output_result;
	} // end of function insert_from_form_to_db


	/**
	 * Delete a record
	 *
	 * @param	string	$tablename	name of a table to delete from
	 * @param	integer	$recordid	id of record to delete
	 * @param	string	$confirm	string based boolean to do or not
	 * @param	string	$pkey		field name
	 * @global	array	$db			global db instance
	 */
	public function delete_record($tablename, $recordid, $confirm, $pkey) {
		global $db;

		/** 
		 * @todo rip out string
		 */
		if (!$recordid)
			return "__no_primary_key__";

		/**
		 * @todo rip out hardcoded html
		 */
		if ( !isset($confirm) or $confirm != "yes" ) {
			$result = ("<div id='delete_confirm_box'>Delete record #");
			$result .= ("<b>$recordid</b>");
			$result .= (" From: ");
			$result .= ("<b>$tablename</b> ?");
			$result .= ("<br />");
			$result .= ("<a href=\"" . $_GET["pretty"] .
				":yes\" rel=\"delete_record\" " .
				"rev=\"#table_information_container\">__yes__</a> | " .
				"<a href=\"\" id=\"delete_confirm_no\">__no__</a></div>");
		} else {
			$delete = $db->query("delete from $tablename where $pkey=".
								 $recordid);
			if ($tablename) {
				$result = ("__record__ <b>#$recordid</b> __deleted_from__ ".
						   "<b>$tablename</b>");
			}
		}
		return $result;
	} // end of function delete_record



	/**
	 *  Edit a record in the database by form post.
	 * 
	 * @param	array		$post		the post for editing
	 * @param	integer		$form_id	id of form for use
	 * @param	integer		$record_id	id of record to edit
	 * @global	array		$db			global db instance
	 * @global	membership	$membership	global membership instance
	 * @global	array		$config		global config instance
	 * 
	 * @todo this code awefully looks similar to
	 *
	 * <code>
	 *	 $layout->forms = $aiki->SqlMarkup->sql($module_form);
	 *	 $layout->forms = $this->fill_form($layout->forms, 
	 *		 "select * from $tablename where $pkey='$postedpkey' limit 1");
	 *	 $dolock = $this->lockdocument($pkey, $postedpkey, $tablename);
	 *	 $layout->forms .= $dolock;
	 * </code>
	 * @example of how to handle forms and layout code in api form
	 */
	public function edit_db_record_by_form_post($post, $form_id, $record_id) {
		global $db, $aiki, $membership, $config;

		if (!$post) {
			return '';
		} else {

			$post = unserialize($post);

			if (empty($post)) {
				return '';
			} else {

				foreach ($post as $post_value) {
					if ( $post_value and $post_value != '' ) {
						$found_a_value = true;
					}
				}
			}
		}

		if (!isset($found_a_value)) {
			return '';
		}
		if (!isset($post['form_post_type'])) {
			$post['form_post_type'] = "save";
		}
		$output_result = '';

		$form = $db->get_row("SELECT * from aiki_forms where ".
							 "id='$form_id' limit 1");

		$form_array = unserialize($form->form_array);

		$arraykeys = array_keys($form_array);

		if (in_array("tablename", $arraykeys)) {
			$tablename = $form_array["tablename"];
		}
		if (in_array("submit", $arraykeys)) {
			$submit = $form_array["submit"];
		}
		if (in_array("permission", $arraykeys)) {
			$special_permission = $form_array["permission"];
		}
		if (in_array("send_email", $arraykeys)) {
			$send_email = $form_array["send_email"];
		}
		if (in_array("pkey", $arraykeys))  {
			$pkey = $form_array["pkey"];
		} else {
			$pkey = 'id';
		}
		if (isset($post['unique_filename'])) {
			$unique_filename = $_REQUEST['unique_filename'];
		}
		if (!isset($submit)) {
			$submit = '';
		}
		switch ($post['form_post_type']) {
			case "save":
				$editQuery = "update $tablename set ";
				break;

			case "insert_new":
				$editQuery = "INSERT into $tablename ";
				break;
		}

		$insert_query_fields = "";
		$insert_query_values = "";

		$i = 0;
		$viewCount = count($form_array);
		foreach ( $form_array as $field ) {

			if ( $field != $tablename and 
				$field != $pkey and 
				$field != $submit ) {
				$intwalker = explode(":", $field);

				$get_permission_and_man_info = explode("|", $intwalker[0]);
				$intwalker[0] = $get_permission_and_man_info[0];

				if (isset($intwalker['2'])) {
					switch ($intwalker['2']) {
						case "orderby":
							//$post[$intwalker[0]] = $post['editpkey'];
							$post[$intwalker[0]] = ($post['publish_date'] * 
													1000)+$post['editpkey'];
							break;

						case "password":
							if (!$post[$intwalker[0]]) {
								/** 
								 * @todo ripout hardcoded string
								 */
								$output_result .= 
									"Password is not changed<br />";
								$post[$intwalker[0]] = 
									$db->get_var("select $intwalker[0] from ".
										"$tablename where $pkey=$record_id");
							} else {

								if(!$intwalker['3']) {
									$intwalker['3'] = "md5|md5";
								}
								if ( $intwalker['3'] and $post[$intwalker['0']] ) {
									$num_levels = explode("|", $intwalker['3']);
									foreach ( $num_levels as $crypt_level ) {
										$post[$intwalker[0]] = 
											md5(stripcslashes(
												$post[$intwalker[0]]));
									}
								}
							}
							break;

						case "value":
							if ( !isset($post[$intwalker[0]]) or 
								!$post[$intwalker[0]] ) {
								//if this is not secondery query
								if (!preg_match("/\-\>/Us", $intwalker[0])) {
									$post[$intwalker[0]] = 
										$db->get_var("select $intwalker[0] " .
										"from $tablename where " .
										"$pkey=$record_id");
								} else {
									$post[$intwalker[0]] = 
										$aiki->url->apply_url_on_query(
													$intwalker[3]);

									$post[$intwalker[0]] = 
									$aiki->processVars($post[$intwalker[0]]);

									$post[$intwalker[0]] = 
									$aiki->url->apply_url_on_query(
												$post[$intwalker[0]]);

									$values_array[$intwalker[0]] = 
										$post[$intwalker[0]];
								}
							}
							break;

						case "datetime":
							$post[$intwalker[0]]= 'NOW()';
							break;

						case "email":
							if (!$aiki->text->is_valid("email",
								$post[$intwalker[0]])) {
								/**
								 * @todo rip out hardcoded string
								 */
								$output_result .= 
									"The email address is not valid<br />";
								$post[$intwalker[0]] = 
									$db->get_var("select $intwalker[0] from ".
										"$tablename where $pkey=$record_id");
							}
							break;
					} // end of switch statement
				}

				if (isset($get_permission_and_man_info[1])) {
					$get_group_level = 
						$db->get_var ("SELECT group_level from ".
							"aiki_users_groups where ".
							"group_permissions='".
							"$get_permission_and_man_info[1]'");
				}
				if (!preg_match("/\-\>/Us", $intwalker[0])) {
					if ( ( !isset($get_permission_and_man_info[1]) or 
						!$get_permission_and_man_info[1] or 
						$get_permission_and_man_info[1] == 
							$membership->permissions or 
							$membership->group_level < $get_group_level ) and 
							isset($post[$intwalker[0]]) ) {
						$post[$intwalker[0]] = 
							@str_replace('&lt;', '<' , $post[$intwalker[0]]);
						$post[$intwalker[0]] = 
							@str_replace('&gt;', '>' , $post[$intwalker[0]]);

						$insert_query_fields .= "$intwalker[0], ";
						$insert_query_values .= "'".$post[$intwalker[0]]."', ";

						if ($post['form_post_type'] == "save") {
							$editQuery .= ", ".$intwalker[0]."='".
											$post[$intwalker[0]]."'";
						}
					}
				} else {
					$delimitered_field = explode("->", $intwalker[0]);
					/**
					 * @todo I'm afraid to fix this string
					 */
					$secondery_queries[$delimitered_field[1]][$delimitered_field[0]] = $post[$delimitered_field[0]."->".$delimitered_field[1]];
				}
			}
			$i++;
		} // end of foreach loop

		$insert_query_fields = preg_replace("/\, $/", "", $insert_query_fields);
		$insert_query_values = preg_replace("/\, $/", "", $insert_query_values);

		switch ($post['form_post_type']) {
			case "save":
				$editQuery .= " where ".$pkey."=".$record_id;
				$editQuery = str_replace("set ,", "set", $editQuery);
				break;

			case "insert_new":
				$editQuery .= 
					"($insert_query_fields) VALUES ($insert_query_values)";
				break;
		}
		
		if (isset($special_permission)) {
			$special_permission = explode("|", $special_permission);

			if ($special_permission[1]) {
				$special_group = $special_permission[1];
			}
			if ($special_permission[0]) {
				$normal_accounts = $special_permission[0];
			}
			if (isset($normal_accounts)) {
				$get_user_name = 
					$db->get_var("select $normal_accounts from ".
						"$tablename where $pkey = $record_id");
			}
			
			if ( isset($normal_accounts) and 
				isset($get_user_name) and 
				$get_user_name == $membership->username ) {
				$editResult = $db->query($editQuery);
			} elseif ( isset($special_group) and 
					  $special_group == $membership->permissions ) {
				$editResult = $db->query($editQuery);
			}

		} else {
			$editResult = $db->query($editQuery);
		}

		/** 
		 * @todo saving revision history needs to be abstracted
		 */
		if ( isset($config["save_revision_history"]) and 
			$config["save_revision_history"] != false ) {
			$original_revision = 
				$db->get_row("select data, revision from aiki_revisions ".
					"where table_name = '$tablename' and ".
					"record_number = '$record_id' order by ".
					"revision DESC limit 1");

			$revision_number = $original_revision->revision + 1;

			$revision_data = 
				"($insert_query_fields) VALUES ($insert_query_values)";

			if ( $original_revision->data != $revision_data ) {
				$revision_data = addslashes($revision_data);

				/**
				 * @todo check this query for issues
				 */
				$revision_query = $db->query("INSERT into aiki_revisions
				(`id` ,`table_name` ,`record_number` ,`data` ,`date` , 
				`username` ,`revision`) VALUES ('', '$tablename', '$record_id',
				'$revision_data', NOW(), '$membership->username' ,
				'$revision_number')");
			}
		}

		/**
		 * @todo handling secondary queries here, means what?
		 */
		if ( isset($secondery_queries) and is_array($secondery_queries) ) {
			foreach ( $secondery_queries as $table => $secondery_query ) {
				$secondery_insert_query = "";
				$secondery_insert_query .= "INSERT into $table (";

				foreach ( $secondery_query as $field_name => $field_value ) {
					$secondery_insert_query .= "$field_name, ";
				}
				$secondery_insert_query = 
					preg_replace("/\, $/", "", $secondery_insert_query);

				$secondery_insert_query .= ") values (";

				foreach ($secondery_query as $field_name => $field_value) {
					$field_value = 
						str_replace("this_pkey", $record_id, $field_value);

					$field_value_var = 
						$aiki->get_string_between($field_value, '[', ']');
					if ($field_value_var) {
						$field_value = 
							str_replace('['.$field_value_var.']', 
										$post["$field_value_var"], 
										$field_value);
					}
					$secondery_insert_query .= "'".$field_value."', ";
				}
				$secondery_insert_query = 
					preg_replace("/\, $/", "", $secondery_insert_query);

				$secondery_insert_query .= ")";

				$secondery_insert_query = 
					str_replace("'NOW()'", "NOW()", $secondery_insert_query);

				$secondery_result = $db->query($secondery_insert_query);
			}
		}

		if (isset($editResult)) {
			switch ($post['form_post_type']) {
				/**
				 * @todo ripout hardcoded strings
				 */
				case "save":
					$output_result .= 
						"Edited record $record_id in $tablename successfully";
					break;

				case "insert_new":
					$output_result .= 
						"Inserted new record in $tablename successfully";
					break;
			}

			/**
			 * @todo investigate more, keep or kill
			 */
			//$this->unlockdocument($pkey, $postedpkey, $tablename);
		} else {
			/**
			 * @todo ripout this hardcoded string
			 */
			$output_result .= 
				"__faild_to_edit_record__ $record_id __in__ $tablename";
		}
		return $output_result;
	} // end of function edit_db_record_by_form_post


	/**
	 * Edit a record in place on the site.
	 *
	 * @param	string			$text			text for processing
	 * @param	string			$widget_value	widget content for insertion
	 * @global	aiki			$aiki			global aiki instance
	 * @global	array			$db				global db instance
	 * @global	membership		$membership		global membership instance
	 * @global	CreateLayout	$layout			global layout of aiki instance
	 * @return	string
	 *
	 * @todo why is layout being handled in a handling function?
	 */
	public function edit_in_place($text, $widget_value) {
		global $aiki,$db, $membership, $layout;
		
		if (isset($_POST['edit_form']) and
			isset($_POST['form_id']) and
			isset($_POST['record_id']) ) {
			$serial_post = serialize($_POST);
			/** 
			 * @todo remove this bare echo in the code!!!
			 */
			echo $aiki->Records->edit_db_record_by_form_post($serial_post, 
				$_POST['form_id'], $_POST['record_id']);
		}
		$edit_matchs = 
			preg_match_all('/\<edit\>(.*)\<\/edit\>/Us', $text, $matchs);

		if ( $edit_matchs > 0 ) {
			foreach ( $matchs[1] as $edit ) {
				$select_menu = false;
				$table = 
					$aiki->get_string_between($edit , "<table>", "</table>");
				$table = trim($table);
				$form_num = 
					$db->get_var("select id from aiki_forms where ".
								 "form_table = '$table'");
				$field = 
					$aiki->get_string_between($edit , "<field>", "</field>");
				$field = trim($field);
				$post_to_url = 
				$aiki->get_string_between($edit , "<post_to>", "</post_to>");

				$post_to_url = trim($post_to_url);

				$label = 
					$aiki->get_string_between($edit , "<label>", "</label>");

				$output = 
					$aiki->get_string_between($edit , "<output>", "</output>");

				$primary = 
				$aiki->get_string_between($edit , "<primary>", "</primary>");

				if (!$primary) {
					$primary = 'id';
				}
				$primary = trim($primary);
				$primary_value = $widget_value->$primary;

				$type = $aiki->get_string_between($edit , "<type>", "</type>");

				if (preg_match("/select\:(.*)/Us", $type)) {
					$select_menu = true;
					$select_output = '<select>';
					$select_elements = explode(":", $type);

					$explodeStaticSelect = explode("&", $select_elements[1]);
					foreach ($explodeStaticSelect as $option) {
						$optionsieds = explode(">", $option);
						$select_output .= 
							'<option value="'.$optionsieds['1'].'"';
						$select_output .= '>'.$optionsieds['0'].'</option>';
					}
					$select_output .= '</select>';
				}

				if (!$type) {
					$type = 'textarea';
				}
				$type = trim($type);

				if ($form_num) {
					$user = $aiki->get_string_between($edit , 
								  "<user>", "</user>");
					if ($user) {
						$user = $widget_value->$user;
					} else {
						$user = '';
					}
					$permissions = $aiki->get_string_between($edit , 
						"<permissions>", "</permissions>");
					$permissions = trim($permissions);

					if ( ( $permissions and 
						$permissions != $membership->permissions ) and 
						( $user and $user != $membership->username ) ) {
						if ( !isset($output) or !$output ) {
							/**
							 * @todo why is this code commented out?
							 */
							$output = "(($field))";
							// $output = $layout->parsDBpars($output, 
							// $widget_value);
						}
					} else {
						if (!$widget_value->$field)
						{
							if ($label) {
								$widget_value->$field = trim($label);
							} else {
								$widget_value->$field = 'Click here to edit';
							}
						} else {
							$widget_value->$field = $aiki->convert_to_specialchars($widget_value->$field);
						}

						/**
						 * @todo this is bare layout code, right directly in
						 * the file seems bad, should review for extraction
						 */
						if ($select_menu) {
							$output = '
<script type="text/javascript">
$(function () { 
$(".edit_ready_'.$primary_value.$field.'").live("click", function () {
var htmldata = $(this).html();
$(this).html(\''.$select_output.'<br /><button id="button_'.$primary_value.$field.'">Save</button>\');
$(this).removeClass(\'edit_ready_'.$primary_value.$field.'\');
$(this).addClass(\'edit_in_progress'.$primary_value.$field.'\');
});
';

							$output .= '

$("#button_'.$primary_value.$field.'").live("click", function () {
var htmldata = $("#'.$primary_value.$field.' select").val();
$.post("'.$post_to_url.'?noheaders=true&nogui=true&no_output=true",  { edit_form: "ok", record_id: '.$primary_value.', '.$field.': htmldata, form_id: "'.$form_num.'" }, function(data){
$("div #'.$primary_value.$field.'").removeClass(\'edit_in_progress'.$primary_value.$field.'\');
$("div #'.$primary_value.$field.'").addClass(\'edit_ready_'.$primary_value.$field.'\');
$("div #'.$primary_value.$field.'").html(htmldata);
});

});
});
</script>
';
						} else {

							$output = '
<script type="text/javascript">
$(function () { 
if (typeof js_'.$primary_value.$field.' == "undefined"){
$(".edit_ready_'.$primary_value.$field.'").live("click", function () {
var htmldata = $(this).html();
js_'.$primary_value.$field.' = "true";
$(this).html(\'<textarea>\' + htmldata + \'</textarea><br /><button id="button_'.$primary_value.$field.'">Save</button> <button id="cancel_'.$primary_value.$field.'">Cancel</button>\');
$(this).removeClass(\'edit_ready_'.$primary_value.$field.'\');
$(this).addClass(\'edit_in_progress'.$primary_value.$field.'\');
});
';

							$output .= '
$("#cancel_'.$primary_value.$field.'").live("click", function () {
var originaldata = $("#'.$primary_value.$field.' textarea").text();
$("div #'.$primary_value.$field.'").removeClass(\'edit_in_progress'.$primary_value.$field.'\');
$("div #'.$primary_value.$field.'").addClass(\'edit_ready_'.$primary_value.$field.'\');
$("div #'.$primary_value.$field.'").html(originaldata);
});

$("#button_'.$primary_value.$field.'").live("click", function () {
var htmldata = $("#'.$primary_value.$field.' textarea").val();
var originaldata = $("#'.$primary_value.$field.' textarea").text();
if (htmldata != originaldata){
$.post("'.$post_to_url.'?noheaders=true&nogui=true&no_output=true",  { edit_form: "ok", record_id: '.$primary_value.', '.$field.': htmldata, form_id: "'.$form_num.'" }, function(data){
$("div #'.$primary_value.$field.'").removeClass(\'edit_in_progress'.$primary_value.$field.'\');
$("div #'.$primary_value.$field.'").addClass(\'edit_ready_'.$primary_value.$field.'\');
$("div #'.$primary_value.$field.'").html(htmldata);
});
}
});
}
});
</script>
';
						}

						$output = str_replace("\n", '', $output);

						//$aiki->Output->set_headers($output);

						$output .= '<div id="'.$primary_value.$field.'" class="edit_ready_'.$primary_value.$field.' edit_in_place">'.$widget_value->$field.'</div>';

					}
				} else {
					/**
					 * @todo ripout hardcoded string
					 */
					$output = '__wrong_table_name__';
				}

				$text = str_replace("<edit>$edit</edit>", $output , $text);

			} // end of loop over matchesO
		} // end of if
		return $text;
	} // end of edit_in_place function

} // end of class records

?>
