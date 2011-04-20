<?php
// $Id: pop.php,v 3.00 2006/12/15 10:58:08 yoshis Exp $
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
//  Based : http://php.s3.to ToR Last Modify 03/07/24 v2.61
//
// mb_関数が使えない場合http://www.spencernetwork.org/にて漢字コード変換(簡易版)を入手して下さい
//
// if (file_exists("jcode-LE.php")) require_once("./jcode-LE.php");
//
/*-----------------*/
require('header.php');
include_once('../../mainfile.php');
include_once('pop.ini.php');
include_once('./include/thumb.php');
include_once( XOOPS_ROOT_PATH.'/modules/popnupblog/class/popnupblog.php');
include( XOOPS_ROOT_PATH.'/modules/popnupblog/include/pop_func.php');
/*-----------------*/
$debug = 0;	// When you debugging this source set to 1, 0 is off.
$uid = 0;	// get uid from mail address;
if ( !(isset($host) && $host) ) include('pop.ini.php');
if ( !defined('POPNUPBLOG_VERSION') ) popnupblog_init();
$img_mode = false;
if (isset($_GET['img'])) {
	if ($_GET['img']) $img_mode = true;
}
$PopAccessInfo = array();
// First of all, receive from primary server setting at module preference.
$PopAccessInfo[0]['blogid']       = 0;
$PopAccessInfo[0]['pop_server']   = PopnupBlogUtils::getXoopsModuleConfig('MAILSERVER');
$PopAccessInfo[0]['pop_user']     = PopnupBlogUtils::getXoopsModuleConfig('MAILUSER');
$PopAccessInfo[0]['pop_password'] = PopnupBlogUtils::getXoopsModuleConfig('MAILPWD');
$PopAccessInfo[0]['pop_address']  = PopnupBlogUtils::getXoopsModuleConfig('MAILADDR');
$SHOW_NAME = PopnupBlogUtils::getXoopsModuleConfig('show_name');

$start = $PopAccessInfo[0]['pop_server'] 
	&& $PopAccessInfo[0]['pop_user'] 
	&& $PopAccessInfo[0]['pop_password'] 
	&& $PopAccessInfo[0]['pop_address'] ? 0 : 1;
bloginfo::get_PopAccessInfo($PopAccessInfo);
for($i=$start;$i<count($PopAccessInfo);$i++){
	$blogid = $PopAccessInfo[$i]['blogid']      ;
	$host =   $PopAccessInfo[$i]['pop_server']  ;
	$user =   $PopAccessInfo[$i]['pop_user']    ;
	$pass =   $PopAccessInfo[$i]['pop_password'];
	$mail =   $PopAccessInfo[$i]['pop_address'] ;
	// Accept attach file
	$strip_attach = $i>0 ? $BlogCNF['StripAttachForML'] : False;
	
	if (!$host or !$user or !$pass or !$mail) continue;
	echo "Server: $host Address: $mail BlogID: $blogid";
	$MailData = array();
	$num = ReceiveFromPop3Server($MailData,$host,$user,$pass,$mail);
	echo " Received: $num<BR />";
	if ($num==0) continue;
	$ThisBlog = null;		// PopnupBlog object   added by hoshiyan hoshiba-farm.com 2006.02.27
	$lines = array();
	$lines = @file($poplog);
	$lines = preg_replace("/\x0D\x0A|\x0D|\x0A/","\n",$lines);
	//echo "bid[$blogid],num[$num]"; var_dump($MailData);
	
	if ($num>0) MailData2Blog($MailData,$num,$blogid,$mail,$lines,$ThisBlog,$strip_attach);
	
	// Logfile max line
	if (count($lines) > $maillogmax) {
		for ($k=count($lines)-1; $k>=$maillogmax; $k--) {
			list($now,$tim,$fro,$sub) = explode("<>", $lines[$k]);
			//if (file_exists($BlogCNF['uploads'].$at)) @unlink($BlogCNF['uploads'].$at);
			$lines[$k] = "";
		}
	}
	// Save to log.
	//if ($goahead) {
		$fp = fopen($poplog, "wb");
		flock($fp, LOCK_EX);
		fputs($fp, implode('', $lines));
		fclose($fp);
	//} else {
		// Update a timestamp for log file.
	//	@touch($poplog);
	//}
}
redirect_header($BlogCNF['root'],3,$JUST_POPED);
exit;
?>
