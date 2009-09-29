<?php

class aiki_errors
{

	function page_not_found(){
		global $db, $aiki, $config;

		Header("HTTP/1.1 404 Not Found");
		
		$aiki->html->set_title("404 Page Not Found");

		$request = $_SERVER['REQUEST_URI'];
		$request = urldecode($request);

		$check_request = $db->get_row("SELECT * FROM aiki_redirects where url='$request'");
		if ($check_request->url){
			$update_hits = $db->query("update aiki_redirects set hits=hits+1 where url='$request'");

			if ($check_request->redirect){
				$catch_patterns[$check_request->url] = $check_request->redirect;
				$catch_regex = '#((' . implode('|', array_keys($catch_patterns)) . '))#i';
				if ( preg_match($catch_regex, urldecode($_SERVER['REQUEST_URI']), $caught) ) {
					$redir = $catch_patterns[$caught[1]];

					Header("Location: $redir", false, 301);
					exit;
				}
			}

		}else{
			$add_e = $db->query("insert into aiki_redirects values ('$request', '', '1')");
		}

		return $config['error_404'];
			
	}


}

?>