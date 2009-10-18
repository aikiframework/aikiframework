<?php
define('IN_AIKI', true);

require_once("aiki.php");

$update = $db->query("UPDATE aiki_widgets SET `if_authorized` = '<h3><a href=\"#\" id=\"urls_widgets\">Urls & Widgets</a></h3>
<div>
	<ul id=\"tree-menu\" class=\"clearfix\">
		<li><a href=\"#\" id=\"create_new_url\"><img src=\"[root]/assets/images/icons/link_add.png\" />Add URL</a></li>
		<li><a href=\"#\" id=\"create_new_widget\"><img src=\"[root]/assets/images/icons/layout_add.png\" />Create Widget</a></li>
	</ul>
	<div id=\"widgettree\" class=\"demo\"></div>
</div>

<h3><a href=\"#\" id=\"database_forms\">Databases & Forms</a></h3>
<div>
	<ul id=\"tree-menu\" class=\"clearfix\">
		<li><a href=\"#\" id=\"create_new_table\"><img src=\"[root]/assets/images/icons/database.png\" />Create Table</a></li>
		<li><a href=\"#\" id=\"create_new_form\"><img src=\"[root]/assets/images/icons/application_form.png\" />Create Form</a></li>
	</ul>
<div id=\"databaseformstree\" class=\"demo\"></div>
</div>

<h3><a href=\"#\" id=\"javascript\">Javascript</a></h3>
<div>
	<ul id=\"tree-menu\" class=\"clearfix\">
		<li><a href=\"#\" id=\"create_new_javascript\"><img src=\"[root]/assets/images/icons/page_gear.png\" />Add Javascript</a></li>
	</ul>
<div id=\"javascripttree\" class=\"demo\"></div>
</div>


<h3><a href=\"#\" id=\"css\">Global CSS</a></h3>
<div>
	<ul id=\"tree-menu\" class=\"clearfix\">
		<li><a href=\"#\" id=\"create_new_css\"><img src=\"[root]/assets/images/icons/page_link.png\" />Add CSS</a></li>
	</ul>
<div id=\"csstree\" class=\"demo\"></div>
</div>' where id = 3");


$update = $db->query("UPDATE aiki_forms SET `form_array` = 'a:36:{s:9:\"tablename\";s:12:\"aiki_widgets\";s:4:\"pkey\";s:2:\"id\";s:10:\"textinput2\";s:23:\"app_id|SystemGOD:app id\";s:10:\"textinput3\";s:33:\"widget_name|SystemGOD:widget name\";s:10:\"textinput4\";s:33:\"widget_site|SystemGOD:widget site\";s:13:\"staticselect5\";s:69:\"widget_target|SystemGOD:widget target:custome:body>body&header>header\";s:13:\"staticselect6\";s:220:\"widget_type|SystemGOD:widget type:custome:div>div&none>0&span>span&paragraph>p&link>a&---html 5--->0&header>header&nav>nav&article>article&aside>aside&figure>figure&footer>footer&section>section&address>address&abbr>abbr\";s:10:\"textinput7\";s:37:\"display_order|SystemGOD:display order\";s:10:\"textinput8\";s:27:\"style_id|SystemGOD:style id\";s:13:\"staticselect9\";s:48:\"is_father|SystemGOD:is father:custome:No>0&Yes>1\";s:11:\"textinput10\";s:37:\"father_widget|SystemGOD:father widget\";s:11:\"textblock11\";s:35:\"display_urls|SystemGOD:display urls\";s:11:\"textblock12\";s:29:\"kill_urls|SystemGOD:kill urls\";s:11:\"textblock13\";s:37:\"normal_select|SystemGOD:normal select\";s:11:\"textblock14\";s:45:\"authorized_select|SystemGOD:authorized select\";s:11:\"textblock15\";s:37:\"if_no_results|SystemGOD:if no results\";s:11:\"textblock16\";s:23:\"widget|SystemGOD:widget\";s:11:\"textblock17\";s:17:\"css|SystemGOD:css\";s:11:\"textblock18\";s:35:\"nogui_widget|SystemGOD:nogui widget\";s:11:\"textinput19\";s:45:\"display_in_row_of|SystemGOD:display in row of\";s:11:\"textinput20\";s:41:\"records_in_page|SystemGOD:records in page\";s:11:\"textinput21\";s:35:\"link_example|SystemGOD:link example\";s:11:\"textinput22\";s:41:\"operators_order|SystemGOD:operators order\";s:11:\"textinput23\";s:45:\"dynamic_pagetitle|SystemGOD:dynamic pagetitle\";s:11:\"textblock24\";s:29:\"pagetitle|SystemGOD:pagetitle\";s:11:\"textblock25\";s:43:\"output_modifiers|SystemGOD:output modifiers\";s:14:\"staticselect26\";s:64:\"is_admin|SystemGOD:Require special permission:custome:No>0&Yes>1\";s:11:\"textblock27\";s:37:\"if_authorized|SystemGOD:if authorized\";s:11:\"textblock28\";s:33:\"permissions|SystemGOD:permissions\";s:11:\"textinput29\";s:43:\"remove_container|SystemGOD:remove container\";s:11:\"textinput30\";s:37:\"edit_in_place|SystemGOD:edit in place\";s:11:\"textinput31\";s:51:\"widget_cache_timeout|SystemGOD:widget cache timeout\";s:14:\"staticselect32\";s:58:\"custome_output|SystemGOD:custome output:custome:No>0&Yes>1\";s:11:\"textblock33\";s:39:\"custome_header|SystemGOD:custome header\";s:11:\"selection34\";s:62:\"javascript|SystemGOD:javascript:aiki_javascript:id:script_name\";s:14:\"staticselect35\";s:48:\"is_active|SystemGOD:is active:custome:Yes>1&No>0\";}' where id = 20");

echo "done :)";
?>
