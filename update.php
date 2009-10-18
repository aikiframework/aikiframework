<?php
define('IN_AIKI', true);

require_once("aiki.php");

$update = $db->query("UPDATE `aiki_widgets` SET `css` = '

#breadcrumbs li{
	float:left;	
}

#breadcrumbs li a{
	float:left;
}

#breadcrumbs li a img{
	height:12px;
	margin-right:4px;
	top: 5px;
}

#breadcrumbs li img{
	float:left;
	position: relative; 
	top: 8px;
	margin-left:10px;
}

.codetext {
	margin:0 15px 0 15px;
	color:#555753;
	font-size:80%;
}

.options-button {
	background:#eeeeee;
	margin:15px 15px 0 15px;
	width:80px;
	height:20px;
	text-align:center;
}

.options-button a{
	margin:5px;
	color: #1b3b6b;
}
.options-button a:hover {
    	text-decoration: none;
}

.options {
	border:1px solid #eeeeee;
	margin:0px 15px 0 15px;
	padding:10px;
	color: #1b3b6b;
}
#big_form {
	margin:0px 15px 0 15px;
}
textarea, input, select {
	border:2px solid #c3c3c3;
	font-family: \"Courier New\";
	padding:3px;
	color:#555753;
	margin:0 15px 0 15px;
	font-size:120%;
	background:GhostWhite ;
}

.form-buttons {
	text-align:right;
}

#widget_container, #normal_select_container, #if_authorized_container{
border: 1px solid black;
padding: 3px;
background-color: #F8F8F8
}

#widget-form h2{
border-color:#CCCCCC;
border-style:dotted none;
border-width:1px 0 0;
display:block;
margin-top:16px;
padding-bottom:6px;
padding-top:4px;
}' where id = 4");

echo "done :)";
?>