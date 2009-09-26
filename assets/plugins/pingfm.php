<?php
$email   = "your@email.com";
$subject = "Ping.fm Custom URL Post";
$body    = "You've pinged a message!\n\n";
$body   .= "Method: {$_POST["method"]}\n";

if($_POST["title"] != ""){
	$body .= "Title: {$_POST["title"]}\n";
}

$body   .= "Message: " .stripslashes($_POST["message"]) ."\n";

if($_POST["location"] != ""){
	$body .= "Location: {$_POST["location"]}\n";
}

if($_POST["media"] != ""){
	$body .= "\nExtra media variables:\n\n";
	$body .= "Raw Message: {$_POST["raw_message"]}\n";
	$body .= "Media URL: {$_POST["media"]}\n";
}



if($_POST["trigger"] != ""){
	$body .= "Trigger: {$_POST["trigger"]}\n";
}


$body   .= "\nThanks for playing!";

if(count($_POST) > 0){
	mail($email, $subject, $body, "From: \"Ping.fm Custom URL Sample\" <support@ping.fm>");
}
?>