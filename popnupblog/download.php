<?php
// $Id: download.php,v 3.21 2009/03/02 14:16:36 yoshis Exp $
//  ------------------------------------------------------------------------ //
//                  Copyright (c) Yoshi.Sakai @ Bluemoon inc.                //
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
/******************************************************************************
    Special Thanks to    : Nat Sakimura,funran7
******************************************************************************/
ini_set("memory_limit","20M");
include("../../mainfile.php");
include_once "./class/mbstrings.php";
include_once "./class/download.class.php";
include 'pop.ini.php';	

if (!is_object($xoopsUser) && $BlogCNF['guest_dl']==0){
	redirect_header(XOOPS_URL."/user.php",2,_MD_DOWNLOAD_ERROR);
	exit();
}
$fpathname = htmlspecialchars ( rawurldecode($_GET['url']) , ENT_QUOTES );
$down = new download($fpathname);
$dl_filename = $down->fnameToDownload();
$fpathname = mbstrings::cnv_mbstr($BlogCNF['uploads'].$down->fnameOnServer());
$ctype = $down->contentType() ;
if(!file_exists($fpathname)){
	print("Error - $fpathname does not exist.");
	return ;
}
ob_clean();
$browser = $version =0;
UsrBrowserAgent($browser,$version);
@ignore_user_abort();
@set_time_limit(0);
if ($browser == 'IE' && (ini_get('zlib.output_compression')) ) {
    ini_set('zlib.output_compression', 'Off');
}
//if (!empty($content_encoding)) {
//    header('Content-Encoding: ' . $content_encoding);
//}
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . filesize($fpathname) );
header("Content-type: " . $ctype);
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Last-Modified: ' . date("D M j G:i:s T Y"));
//header('Content-Disposition: attachment; filename="' . $dl_filename . '"');
header("Content-Disposition: inline; " . cnv_mbstr4browser($dl_filename,$browser));
if ($browser == 'IE') {
    header('Pragma: public');
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
} else {
    header('Pragma: no-cache');
}
$fp=fopen($fpathname,'r');
while(!feof($fp)) {
	$buffer = fread($fp, 1024*6); //speed-limit 64kb/s
	print $buffer;
	flush();
	ob_flush();
	usleep(10000); 
}
fclose($fp);
//
// Save download log
//
if ($xoopsUser) $uname = $xoopsUser->getVar('uname'); else $uname = "Anonymous";
$str = $uname.",".date("Y-m-d H:i:s", time());
$postlog = $fpathname.'.log';
$fp = fopen($postlog, 'a');
fwrite($fp, $str."\n");
fclose($fp);
//
// Check User Browser
//
function UsrBrowserAgent(&$browser,&$version) {
    if (preg_match('@Opera(/| )([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        $version= $log_version[2];
        $browser='OPERA';
    } elseif (preg_match('@MSIE ([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        $version= $log_version[1];
        $browser='IE';
    } elseif (preg_match('@OmniWeb/([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        $version= $log_version[1];
        $browser='OMNIWEB';
    } elseif (preg_match('@(Konqueror/)(.*)(;)@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        $version= $log_version[2];
        $browser='KONQUEROR';
    } elseif (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)
               && preg_match('@Safari/([0-9]*)@', $_SERVER['HTTP_USER_AGENT'], $log_version2)) {
        $version= $log_version[1] . '.' . $log_version2[1];
        $browser='SAFARI';
    } elseif (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        $version= $log_version[1];
        $browser='MOZILLA';
    } else {
        $version= 0;
        $browser='OTHER';
    }
    return $browser;
}
//
// for Content-disposition by funran7
//     2006/05/12 add $d_enc tweak by yoshis
function cnv_mbstr4browser($str,$browser) {
	global $xoopsModuleConfig;
	if (extension_loaded('mbstring')){
		$d_enc = mb_internal_encoding();
		if ( $d_enc != "ASCII" ){
			switch($browser){
			case 'MOZILLA':
		        $fn = "filename*=ISO-2022-JP''" .  rawurlencode(mb_convert_encoding($str,"ISO-2022-JP",$d_enc));
		        break;
			case 'IE':
		        $fn = "filename=\"" . mb_convert_encoding($str,"SJIS",$d_enc) . "\"";
		        break;
			case 'OPERA':
		        $fn = "filename=\"" . mb_convert_encoding($str,"UTF-8",$d_enc) . "\"";
		        break;
			case 'SAFARI':
		        $fn = "filename=\"" . mb_convert_encoding($str,"UTF-8",$d_enc) . "\"";
		        break;
		    default:
				$fn = "filename=\"" . $browser . "\"";
		        break;
		    }
		} else {
			$fn = "filename=$str";
		}
    } else {
		$fn = "filename=$str";
    }
    return $fn;
}
?>
