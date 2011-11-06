<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                Copyright (c) 2005 Yoshi.Sakai @ Bluemoon inc.             //
//                       <http://www.bluemooninc.biz/>                       //
// ------------------------------------------------------------------------- //
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
class mbstrings{
	function cnv_mbstr($str,$mbfrom="auto") {
		global $xoopsModuleConfig;
	
		if (extension_loaded('mbstring')){
			return  mb_convert_encoding($str,$xoopsModuleConfig['FILE_CHARSET'],$mbfrom);
		} else {
			return $str;
		}
	}
	function cnv_mbFilename($str) {
		global $xoopsModuleConfig;
	
		if (extension_loaded('mbstring')){
			if ( mb_internal_encoding() == $xoopsModuleConfig['FILE_CHARSET'] ) return $str;
			return mb_encode_mimeheader( mb_convert_encoding($str,mb_internal_encoding(),$xoopsModuleConfig['FILE_CHARSET']) );
		} else {
			return $str;
		}
	}
	function internal2utf8($str) {
		global $xoopsModuleConfig;
	
		if (extension_loaded('mbstring')){
			return  mb_convert_encoding($str,'UTF-8',mb_internal_encoding());
		} else {
			return $str;
		}
	}
	function utf8tointernal($str) {
		global $xoopsModuleConfig;
	
		if (extension_loaded('mbstring')){
			return  mb_convert_encoding($str,mb_internal_encoding(),'UTF-8');
		} else {
			return $str;
		}
	}	//
	// modified by hoshiyan@hoshiba-farm.com  2005.02.03, 2006.03.03
	//
	function _strcut($text, $start, $end){
		$text = mbstrings::formatBlogText4Preview($text);
		if ( strlen($text) >= $end ) {
			$msg = xoops_substr($text, 0, $end-1);
		} else {
			$msg = $text;
		}
		return $msg;
	}
	//
	// added by hoshiyan,hoshiba-farm.com, 2004-10-15
	//
	function formatBlogText4Preview($srcStr) {
		$dstStr = preg_replace("/\[(url|img)[^]]*\].*\[\/(url|img)\]/","",$srcStr);
		$dstStr = strip_tags($dstStr);
		$dstStr = preg_replace("/([\r\n])[\s]+/","",$dstStr);
		return $dstStr;
    }
}
?>
