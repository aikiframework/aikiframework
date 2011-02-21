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
class captcha
{

	public function create_random_text(){

		$md5 = md5(microtime() * mktime());
		$randnumber = rand(2, 6);
		$string = substr($md5,$randnumber,6);

		return $string;
	}


	public function write_text_to_image(){

		$string = $this->create_random_text();

		$captcha = imagecreatefrompng("../../../assets/apps/captcha/captcha.png");

		$black = imagecolorallocate($captcha, 0, 0, 0);
		$line = imagecolorallocate($captcha,233,239,239);
		imageline($captcha,0,0,39,29,$line);
		imageline($captcha,40,0,64,29,$line);
		imageline($captcha,29,64,0,29,$line);
		imageline($captcha,29,64,120,0,$line);
		imagestring($captcha, 5, 20, 5, $string, $black);

		$_SESSION['captcha_key'] = md5($string);

		header("Content-type: image/png");
		imagepng($captcha);

	}

}