<?php
// $Id: sanitize.php,v 2.52 2006/06/09 22:29:08 yoshis Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
function sanitize_blog($str, $isArea=false, $isURL=false, $HTMLdecode=false,$nl2br=true) {
	if (get_magic_quotes_gpc()) {
		$str = stripslashes($str);
	}
	$patterns = array();
	$replacements = array();

	$patterns[] = "/&amp;/i";
	$replacements[] = '&';
	$patterns[] = "/&nbsp;/";
	$replacements[] = '&amp;nbsp;';

	if ($isArea) {
		$patterns[] = "/&lt;(\/)?\s*script.*?&gt;/si";
		$replacements[] = '[$1script]';
		$patterns[] = "/&lt;(\/)?\s*style.*?&gt;/si";
		$replacements[] = '[$style]';
		$patterns[] = "/&lt;(\/)?\s*body.*?&gt;/si";
		$replacements[] = '[$body]';
		$patterns[] = "/&lt;(\/)?\s*link.*?&gt;/si";
		$replacements[] = '[$link]';
		$patterns[] = "/(&lt;.*)(?:onError|onUnload|onBlur|onFocus|onClick|onMouseOver|onSubmit|onReset|onChange|onSelect|onAbort)\s*=\s*(&quot;|&#039;).*\\2(.*?&gt;)/si";
		$replacements[] = '$1$3';
		if ($isURL) {
			$patterns[] = "/(&quot;|&#039;).*/";
			$replacements[] = "";
			$patterns[] = "/(?:onError|onUnload|onBlur|onFocus|onClick|onMouseOver|onSubmit|onReset|onChange|onSelect|onAbort)\s*=\s*('|\"|&quot;|&#039;).*(\\1)?/si";
			$replacements[] = "";
		}
	} else {
		$patterns[] = "/(&#13|&#10).*/";
		$replacements[] = "";
	}
	if ($isURL) {
		$patterns[] = "/javascript:/si";
		$replacements[] = "javascript|";
		$patterns[] = "/vbscript:/si";
		$replacements[] = "vbscript|";
		$patterns[] = "/about:/si";
		$replacements[] = "about|";
	}
	$ts =& MyTextSanitizer::getInstance();
    $str = $ts->codePreConv($str);
	$str = htmlspecialchars($str, ENT_QUOTES);
	$str = preg_replace($patterns,$replacements, $str);
	if ($HTMLdecode){
		$str = html_entity_decode($str);
	}
	$str = phpBBsmiley($str);
	$str = $ts->smiley($str);
	$str = $ts->xoopsCodeDecode($str);
	if ($nl2br) $str = nl2br($str);
    $str = $ts->codeConv($str);

	return $str;
}
/**
* Replace emoticons in the message with phpBB images
* @param	string  $message
* @return	string
*/
function phpBBsmiley($message){
	//print($message);
	$message =& str_replace(":download:", "[img]".XOOPS_URL."/modules/popnupblog/images/attachment.gif[/img]", $message);
	//$message =& str_replace("[img ", "<div style='clear:both'></div>[img ",$message);
	return $message;
}
function remove_html_tags($t){
	return preg_replace_callback(
		"/(<[a-zA-Z0-9\"\'\=\s\/\-\~\_;\:\.\n\r\t\?\&\+\%\&]*?>)/ms", 
		/* "/(<[*]*?>|\n|\r)/ms", */
		"popnupblog_remove_html_tags_callback", 
		$t);
}
function popnupblog_remove_html_tags_callback($t){
	return "";
}
?>
