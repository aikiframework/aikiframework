<?php
define('IN_AIKI', true);

require_once("aiki.php");

$update = $db->query("UPDATE aiki_widgets` SET `if_authorized` = '<h3><a href=\"#\" id=\"urls_widgets\">Urls & Widgets</a></h3>
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

echo "done :)";
?>
