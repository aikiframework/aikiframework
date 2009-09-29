<?php
set_time_limit('999999999999999999');

//error_reporting(E_ALL);
define('IN_AIKICMS', true);
//require_once ("aiki.php");


if ($membership->permissions != "SystemGOD"){
	//die("You do not have permissions to access this file");
}

$do = $_REQUEST['do'];



switch ($do){


	case "rebuild_events_dates":
		$where = 'id>7214';

		$results = $db->get_results("SELECT * FROM `dsyria_events` where $where");
		foreach ($results as $result){
			if (!$result->finishdate or $result->finishdate = ''){
				$update = $db->query("UPDATE dsyria_events set finishdate = '$result->startdate' where id ='$result->id'");
			}
		}



		$results = $db->get_results("SELECT * FROM `dsyria_events` where $where");
		foreach ($results as $result){
			$data = explode("-", $result->finishdate);
			$stamp = mktime(0,0, 0, $data[1], $data[0], $data[2]);  // 17th March 200 12:30
			echo $result->id." - "; echo($stamp . "<br>");
			$upload = $db->query("UPDATE dsyria_events set unix_finish ='$stamp' where id = '$result->id'");

		}


		$results = $db->get_results("SELECT * FROM `dsyria_events` where $where");
		foreach ($results as $result){
			$data = explode("-", $result->startdate);
			$stamp = mktime(0,0, 0, $data[1], $data[0], $data[2]);  // 17th March 200 12:30
			echo $result->id." - "; echo($stamp . "<br>");
			$upload = $db->query("UPDATE dsyria_events set unix_start ='$stamp' where id = '$result->id'");

		}





		$results = $db->get_results("SELECT * FROM `dsyria_events` where $where");
		foreach ($results as $result){

			$mysql_start =  date('D M d Y', $result->unix_start);
			$mysql_finish =  date('D M d Y', $result->unix_finish);
			$upload = $db->query("UPDATE dsyria_events set mysql_start='$mysql_start', mysql_finish ='$mysql_finish' where id = '$result->id'");

		}


		break;


	case "rename_files_by_stamp":
		$path = "/home/discove2/public_html/upload/dead_cities/KaserAlBanat";

		$handle = opendir($path);
		$path = str_replace(" ", "\ ", $path);
		while (($file = readdir($handle))!==false) {
			if ($file != "." and $file != ".."){

				$file = str_replace(" ", "\ ", $file);
				$file = str_replace("(", "\(", $file);
				$file = str_replace(")", "\)", $file);

				$or_file = $file;

				$file = time().".jpg";



				echo $or_file."<br>";
				sleep(1);
				exec("mv -v $path/$or_file $path/$file", $output);
				print_r($output);
				sleep(1);


			}



		}
		closedir($handle);


		break;

	case "add_photo_folder":
		$path = "/home/discove2/public_html/upload/spring2009";

		$handle = opendir($path);
		$path = str_replace(" ", "\ ", $path);
		while (($file = readdir($handle))!==false) {
			if ($file != "." and $file != ".."){

				$insert = $db->query("insert into modules_photo_archive(categorie,full_path, filename, keywords) values('1', 'upload/spring2009/', '$file', 'دمشق|الربيع في دمشق')");

			}



		}
		closedir($handle);

		break;




	case "update_all_photos":

		$articles = $db->get_results("SELECT * FROM modules_photo_archive where checksum_sha1 =''");
		foreach ( $articles as $article )
		{
			$path = $article->full_path;

			if (file_exists($aikicore->setting['top_folder'].'/'.$path.$article->filename)){
				$sha1 = sha1_file($aikicore->setting['top_folder'].'/'.$path.$article->filename);
				$md5 = md5_file($aikicore->setting['top_folder'].'/'.$path.$article->filename);
				$filesize = filesize($aikicore->setting['top_folder'].'/'.$path.$article->filename);

				$size = getimagesize($aikicore->setting['top_folder'].'/'.$path.$article->filename);
				$width = $size["0"];
				$hight = $size["1"];

				$db->query("update modules_photo_archive set checksum_sha1='$sha1', checksum_md5='$md5', upload_file_size='$filesize', width='$width', height='$hight', is_missing='0' where id='$article->id'");

			}else{
				$db->query("update modules_photo_archive set is_missing='1' where id='$article->id'");
			}
			echo $article->id."<br>";
		}
		break;







	case "ModuleArray":

		$ModuleArray = array(
		 "tablename" => "dsyria_articles_text",
		 "pkey" => "id",
		 "textinput1" => "first_title|employees|true:العنوان الأول",
		 "textinput2" => "second_title|employees:العنوان الثاني",
		 "textinput3" => "writers|employees:كتاب المقال",
		 "selection1" => "categories1|employees:التصنيف الأول:dsyria_articles_categories:id:name",
		 "selection2" => "categories2|employees:التصنيف الثاني:dsyria_articles_categories:id:name",		
		 "staticselect1" => "maintype|employees:نوع المقال:custome:خبر>2&مقال>1",
	     "normaltextblock1" => "hometext|employees|true:الافتتاحية",
			
		 "textinput4" => "sources|employees:المصادر",
		 "textinput5" => "extras|employees:ملاحظات إضافية",		
		 "normaltextblock1" => "keywords|employees:الكلمات المفتاحية",
		 "imagenoupload22" => "filename|employees:صورة المقال",
		 "autofiled1" => "publish_date|employees:تاريخ النشر:publishdate",
		 "autofiled2" => "insert_date:تاريخ الإدخال:uploaddate",	
		 "autofiled3" => "order_by|SystemGOD:ترتيب الإظهار:orderby",		
		 "autofiled4" => "insert_by:تم الإدخال بواسطة:insertedby",		
		 "staticselect2" => "publish_cond|SystemGOD:حالة المقال:custome:عدم النشر>0&مقال مدقق>1&نشر فورا>2",
		 "staticselect3" => "allow_comments|SystemGOD:السماح بالتعليقات:custome:نعم>1&لا>0",		
		 "autofiled6" => "edit_by:تاريخ التعديلات:EditingHistory:Users&Dates&changes",		
		 "staticselect5" => "is_sticky|SystemGOD:مقال مثبت:custome:نعم>1&لا>0",		

		);
		//	  "verify_password1" => "password:تأكيد كلمة المرور",
		//	  "filemanager1" => "filename:الصورة الشخصية:unique_filename",


		/*$ModuleArray = array(
		 "tablename" => "aiki_users",
		 "pkey" => "userid",
		 "unique_textinput" => "username:اسم المستخدم:unique",
		 "password1" => "password:كلمة المرور:password:md5|md5",
		 "textinput1" => "full_name:الاسم الكامل",
		 "textinput2" => "country:الدولة",
		 "staticselect3" => "sex:الجنس:custome:ذكر>ذكر&أنثى>أنثى&بدون تحديد>بدون تحديد",
		 "textinput4" => "job:العمل",
		 "static_input1" => "usergroup:الصلاحيات:value:5",
		 "textinput_if_valid1" => "email:البريد الإلكتروني:email",
		 "textinput_if_valid2" => "homepage:الموقع الإلكتروني:url",
		 "autofiled1" => "first_ip:الآي بي:ip",			
		 "autofiled2" => "first_login:تاريخ التسجيل:uploaddate",
		 "staticselect5" => "maillist:الاشتراك بالقائمة البريدية:custome:نعم>1&لا>0"

		 );*/


		/*$ModuleArray = array(
		 "tablename" => "dsyria_bank_riched",
		 "pkey" => "id",
		 "hidden1" => "original_bank_id:رقم المقال الأصل",
		 "textinput10" => "name:اسمك الكامل",
		 "textinput_if_valid1" => "email:البريد الإلكتروني:email",
		 "textinput1" => "title:عنوان المشاركة",
		 "normaltextblock1" => "text:النص",
		 "textinput3" => "source:المصدر ( في حال الاقتباس )",
		 "autofiled4" => "full_name:تم الإدخال بواسطة:insertedby",
		 "autofiled5" => "insert_by:تم الإدخال بواسطة:insertedby_username",		
		 "autofiled2" => "insert_date:تاريخ الإدخال:uploaddate",	


		 );
		 */
		/*$ModuleArray = array(
		 "tablename" => "dsyria_articles_comments",
		 "pkey" => "id",


		 "hidden2" => "categorie:رقم المقال",
		 "textinput2" => "username:اسمك",
		 "textinput4" => "email:بريدك الإلكتروني",
		 "textinput5" => "country:الدولة",
		 "aikitextblock1" => "comment:مشاركتك",


		 );*/

		$ModuleArray = array(
		 "tablename" => "dsyria_events",
		 "pkey" => "id",
		 "textinput1" => "startdate|employees|true:تاريخ البدء",
		 "textinput2" => "finishdate|employees:تاريخ الانتهاء",
	     "textinput3" => "title|employees:عنوان الحدث",			
		 "normaltextblock1" => "info|employees:شرح الحدث",
		 "textinput4" => "startime|employees:ساعة البدء",
	     "textinput5" => "finitime|employees:ساعة الإنتهاء",
	     "textinput6" => "place|employees:المكان",			
	     "textinput7" => "cat|employees:التصنيف",			

		);


		$ModuleArray = array(
		 "tablename" => "dsyria_articles_comments",
		 "pkey" => "id",
		 "textinput1" => "categorie|SystemGOD:رقم المقال",
		 "textinput2" => "username|SystemGOD:اسم المستخدم",	
		 "textinput3" => "email|SystemGOD:البريد الإلكتروني",
		 "textinput4" => "country|SystemGOD:الدولة",
		 "bigtextblock5" => "comment|SystemGOD:التعليق",	
   	  "staticselect5" => "published:النشر:custome:نعم>1&لا>0&اخفاء>3"
   	  );

   	  $ModuleArray = array(
		 "tablename" => "modules_photo_archive",
		 "pkey" => "id",

	  "static_input1" => "categorie:نوع الصورة:value:5",
		 "textinput2" => "title:عنوان الصورة",
		 "selection1" => "original_artist_id:الفنان:dsyria_cv_plast:id:full_name",
		 "static_input2" => "full_path:المسار الكامل للملف:full_path",
		 "filemanager49" => "filename:ملف الصورة:unique_filename",
   	  );

   	  $ModuleArray = array(
		 "tablename" => "modules_photo_archive",
		 "pkey" => "id",

	  "static_input1" => "categorie:نوع الصورة:value:3",
		 "textinput2" => "title:عنوان الصورة",
  		 "normaltextblock1" => "keywords:الكلمات المفتاحية",
  		 "textinput3" => "people_in_photo:الأشخاص في الصورة",
  "textinput4" => "article_id:رقم المقال",
  		 "textinput5" => "full_path:المسار الكامل للملف:full_path",
		 "filemanager49" => "filename:ملف الصورة:unique_filename",
   	  );
   	  	
   	  	
   	  	
   	  $ModuleArray = array(
		 "tablename" => "modules_photo_archive",
		 "pkey" => "id",


		 "filemanager91" => "colored_label:لون المجموعة",
		 "filemanager92" => "rating:التقييم",
		 "filemanager93" => "ratings_num:عدد التقييمات",
		 "textinput10" => "alt_text:النص المرتبط",
		 "normaltextblock1" => "keywords:الكلمات المفتاحية",
		 "filemanager80" => "original_artist_id:الفنان الأصلي",
		 "filemanager81" => "original_width:العرض الأصلي",
		 "filemanager82" => "original_height:الارتفاع الأصلي",
		 "filemanager83" => "article_title:عنوان المقال",
		 "filemanager84" => "article_keywords:كلمات المقال المفتاحية",
		 "filemanager85" => "article_source:مصدر المقال",
		 "filemanager86" => "article_pubdate:تاريخ نشر المقال",
		 "filemanager87" => "article_writer:كاتب المقال",
		 "filemanager88" => "article_id:رقم المقال",
		 "filemanager34" => "resolution:الدقة",
		 "filemanager35" => "depth:عمق الألوان",
		 "filemanager36" => "color_space:الفضاء اللوني",
		 "filemanager37" => "compression:الضغط",
		 "filemanager39" => "source_device:الجهاز الأصلي",
		 "filemanager40" => "exif_data:معلومات ميتا الصورة",
		 "filemanager41" => "capture_date:تاريخ التصوير",
		 "filemanager42" => "aperture:aperture",
		 "filemanager43" => "shutter_speed:سرعة العدسة",
		 "filemanager44" => "focal_length:فتحة العدسة",
		 "filemanager45" => "iso_speed:حساسية الصورة",
		 "filemanager851" => "available_sizes:القياسات المتوفرة",
		 "filemanager49" => "filename:ملف الصورة:unique_filename",
"filemanager1" => "mime_type:نوع الملف:mime_type",
		 "filemanager94" => "upload_file_name:اسم ملف الأصل:upload_file_name",
		 "filemanager95" => "upload_file_size:حجم ملف الأصل:upload_file_size",
		 "filemanager8" => "width:العرض:width",
		 "filemanager9" => "height:الارتفاع:height",
"filemanager119" => "checksum_sha1:sha1:checksum_sha1",
"filemanager120" => "checksum_md5:md5:checksum_md5",

   	  );
   	  	
   	  	

   	  $ModuleArray = array(
		 "tablename" => "f_essays",
		 "pkey" => "id",
   	  	 "send_email" => "info@falestiny.com|[from_email]|عمل جديد|الكاتب:[writer]<br>العنوان:[first_title]<br>[second_title]<br><br>[post]",
   	  	 "textinput_if_valid1" => "from_email||true:بريدك الإلكتروني:email",
   	  	 "bigtextblock2" => "post:المادة",
   	  	 "autofiled2" => "stime:تاريخ الادخال:uploaddate"
  	  

   	  	 );


   	  	 $ModuleArray = array(
		 "tablename" => "aiki_widgets",
		 "pkey" => "id",
   	     "normaltextblock1" => "display_urls|SystemGOD:Urls",
   	     "normaltextblock2" => "kill_urls|SystemGOD:Kill Urls",
   	     "normaltextblock3" => "normal_select|SystemGOD:SQL",
   	     "normaltextblock4" => "if_no_results|SystemGOD:if no results",
   	     "textinput3" => "display_in_row_of|SystemGOD:Rows",
 	     "textinput4" => "records_in_page|SystemGOD:Columns",
 	     "textinput5" => "link_example|SystemGOD:Pagination link",
 	     "bigtextblock1" => "widget|SystemGOD:HTML",	
  	     "normaltextblock8" => "pagetitle|SystemGOD:Page title",
   	     "normaltextblock9" => "output_modifiers|SystemGOD:Output modifiers",
   	     "textinput6" => "widget_cache_timeout|SystemGOD:Cache Timeout",
   	     "staticselect10" => "custome_output|SystemGOD:Custome Output:custome:Yes>1&No>0",
   	     "normaltextblock11" => "custome_header|SystemGOD:Custome http headers",
   	     "selection6" => "javascript|SystemGOD:Javascript:aiki_javascript:id:script_name",
             "staticselect20" => "is_active|SystemGOD:Is Active:custome:Yes>1&No>0",		   	  
   	  	 );




/*


a:31:{s:9:"tablename";s:12:"aiki_widgets";s:4:"pkey";s:2:"id";s:10:"textinput1";s:38:"widget_name|SystemGOD||ltr:Widget Name";s:10:"selection1";s:79:"widget_group|SystemGOD||ltr:Widget Group:aiki_sites:site_shortcut:site_shortcut";s:10:"textinput2";s:42:"widget_folder|SystemGOD||ltr:Widget Folder";s:10:"textinput3";s:42:"display_order|SystemGOD||ltr:Display Order";s:10:"textinput4";s:33:"style_id|SystemGOD||ltr:CSS Class";s:13:"staticselect1";s:61:"is_father|SystemGOD||ltr:is father widget?:custome:No>0&Yes>1";s:10:"selection2";s:70:"father_widget|SystemGOD||ltr:Father Widget:aiki_widgets:id:widget_name";s:16:"normaltextblock1";s:43:"father_module|SystemGOD||ltr:Father Modules";s:13:"staticselect2";s:61:"global_use|SystemGOD||ltr:Display Globally:custome:No>0&Yes>1";s:10:"textinput5";s:46:"dis_operators|SystemGOD||ltr:Display Operators";s:10:"textinput6";s:44:"kill_operators|SystemGOD||ltr:Kill Operators";s:16:"normaltextblock2";s:39:"aiki_select|SystemGOD||ltr:SQL statment";s:16:"normaltextblock3";s:48:"if_no_results|SystemGOD||ltr:No results messages";s:13:"bigtextblock1";s:26:"widget|SystemGOD||ltr:HTML";s:16:"normaltextblock4";s:39:"nogui_widget|SystemGOD||ltr:No GUI HTML";s:10:"textinput7";s:50:"display_in_row_of|SystemGOD||ltr:Display in row of";s:10:"textinput8";s:46:"records_in_page|SystemGOD||ltr:Records in page";s:10:"textinput9";s:40:"link_example|SystemGOD||ltr:Link example";s:11:"textinput10";s:46:"operators_order|SystemGOD||ltr:Operators order";s:13:"staticselect5";s:71:"dynamic_pagetitle|SystemGOD||ltr:Dynamic Page title:custome:No>&Yes>yes";s:16:"normaltextblock8";s:35:"pagetitle|SystemGOD||ltr:Page title";s:16:"normaltextblock9";s:48:"output_modifiers|SystemGOD||ltr:Output modifiers";s:13:"staticselect8";s:66:"edit_in_place|SystemGOD||ltr:Allow edit in place:custome:No>&Yes>1";s:14:"staticselect21";s:61:"is_admin|SystemGOD||ltr:require permission:custome:No>0&Yes>1";s:13:"bigtextblock2";s:52:"if_authorized|SystemGOD||ltr:Authorized members html";s:11:"textinput12";s:47:"permissions|SystemGOD||ltr:Targeted users group";s:11:"textinput14";s:49:"widget_cache_timeout|SystemGOD||ltr:cache timeout";s:10:"selection6";s:74:"javascript|SystemGOD||ltr:call a javascript:aiki_javascript:id:script_name";s:14:"staticselect20";s:52:"is_active|SystemGOD||ltr:Activate:custome:No>0&Yes>1";}




   	     "staticselect8" => "is_admin|SystemGOD:تحتاج صلاحيات:custome:لا>0&نعم>1",
   	  	 "bigtextblock2" => "if_authorized|SystemGOD:HTML",	
   	     "textinput12" => "permissions|SystemGOD:مستوى الصلاحيات المطلوب",
   	  	 "staticselect8" => "edit_in_place|SystemGOD:السماح بالتعديل في المكان:custome:لا>&نعم>1",		
   	     "textinput14" => "widget_cache_timeout|SystemGOD:وقت الكاش",
   	     "selection6" => "javascript:استدعاء جافاسكربت:aiki_javascript:id:script_name",


*/

		$ModuleArray = array(
		 "tablename" => "dsyria_bank_riched",
		 "pkey" => "id",
		 	

   	  );






   	  	 echo serialize($ModuleArray);
   	  	 break;




}

?>
