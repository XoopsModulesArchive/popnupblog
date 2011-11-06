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
$incpath = XOOPS_ROOT_PATH."/modules/popnupblog/";
if(
	!defined('XOOPS_ROOT_PATH') ||
	!defined('XOOPS_CACHE_PATH') ||
	!is_file($incpath.'class/log.php') ||
	!is_file($incpath.'class/mbstrings.php') ||
	!is_file($incpath.'class/download.class.php') 
){
	exit();
}
include_once $incpath.'class/log.php';
include_once $incpath.'class/mbstrings.php';
include_once $incpath.'class/download.class.php';

class sendmail {
	function enc_mimeheader(&$subj){
		// if xoops 2.0.7 to 2.014 use below.
		// Add CR for mb_encode_mimeheader bug when PHP version 4.4.0 or 4.4.1.
		/*
		if ( function_exists('mb_encode_mimeheader') ){
			if ( preg_match('/^4\.4\.[01]([^0-9]+|$)/',PHP_VERSION)) $subj = "\n".$subj;
			$subj = mb_encode_mimeheader( $subj, sendmail::get_mailcode(), "B" );
		}
		*/
		return $subj;
	}
	function send_ML($from,$to,$uname,$blog_count,$subj,$blogtitle,$blogurl,$message='',$files='',$ml_address='') {
		global $xoopsConfig,$BlogCNF;
		$debug = 0;
		if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$",$from)) return false;
		if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$",$to)) return false;
		$xoopsMailer =& getMailer();
		$xoopsMailer->useMail();
		$xoopsMailer->setTemplateDir($BlogCNF['path']."language/".$xoopsConfig['language']."/mail_template/");
		$xoopsMailer->setTemplate("mailinglist.tpl");
		$xoopsMailer->setToEmails($to);
		$xoopsMailer->assign("POST_TITLE", $subj);
		$xoopsMailer->assign("POST_MESSAGE", $message);
		$xoopsMailer->assign("BLOG_URL", $blogurl);
		$xoopsMailer->assign("SITENAME", $xoopsConfig['sitename']);
		$xoopsMailer->assign("SITEURL", $xoopsConfig['xoops_url']."/");
		$xoopsMailer->assign("BLOG_UNAME", $uname);
		$xoopsMailer->assign("BLOG_MAIL", $from);
		if ($ml_address) $xoopsMailer->setFromEmail($ml_address);
		else $xoopsMailer->setFromEmail($from);
		$xoopsMailer->setFromName($uname);
		$subj = sprintf("[%s:%d] %s", $blogtitle,$blog_count,$subj);
		$subj = sendmail::enc_mimeheader($subj);
		$xoopsMailer->setSubject($subj);
		if ($debug){ echo "Attached files=".count($files)."<br />"; }
		for($i=0;$i<count($files);$i++) {
			$afp = $files[$i]['path'];	// attachement file path
			$type = isset($files[$i]['type']) ? $files[$i]['type'] : "application/octet-stream";
			if ($debug) echo "Attache Select=".$BlogCNF['Attache_Select']."  Path="
				.$afp." File name=".$files[$i]['name']."<br /> isfile=".(is_file($afp)?ok:notfound);
			if (preg_match("/\.(".$GLOBALS['BlogCNF']['img_ext'].")$/i",$afp)) {
				if( download::isImageFile($afp) ){
					if( $BlogCNF['Attache_Select']==1 ) {	// attache thumbnail file
						$res = $xoopsMailer->multimailer->AddAttachment($afp,$files[$i]['name']);
						if ($debug) echo " Thumb Attached(".(($res)?ok:error).")<br />";
					}
				} else {
					if( $BlogCNF['Attache_Select']==0 ) {	// attache original image file
						$res = $xoopsMailer->multimailer->AddAttachment($afp,$files[$i]['name']);
						if ($debug) echo " Source Attached(".(($res)?ok:error).")<br />";
					}
				}
			}else{
				$res = $xoopsMailer->multimailer->AddAttachment($afp,$files[$i]['name'],"base64",$type);
				if ($debug) echo " Source Attached(".(($res)?ok:error).")<br />";
			}
		}
		$xoopsMailer->send();
	}
	function send_comment($mailto,$uname,$postid,$subj,$blogtitle,$blogurl,$message,$commenter_name){
		global $xoopsConfig;
		$subj = sprintf("[%s:%d] %s", $blogtitle,$postid,$subj);
		$message = vsprintf(_MD_POPNUPBLOG_HELLO,$uname)."\n\n".
			_MD_POPNUPBLOG_GETCOMMENT."\n\n-----------\n".$commenter_name.": \n".$message;
		sendmail::send_ML($xoopsConfig['adminmail'],$mailto,$uname,$postid,$subj,$blogtitle,$blogurl,$message);
		/*
		global $xoopsConfig;
		$mailcode = sendmail::get_mailcode();
		$subj = sendmail::enc_mimeheader($subj);
		$message = PopnupBlogUtils::convert_encoding($message,"auto", $mailcode);
		$chgheader = 
			"From: ". $xoopsConfig['adminmail'] ."\n" . 
			"Reply-To: ". $mailto . "\n" . 
			"Mime-Version: 1.0\n" . 
			"Content-Type: text/plain; charset=". $mailcode."\n";
			"Content-Transfer-Encoding: 7bit\n";
		mail($mailto,$subject,$message,$chgheader);
		*/
	}
	function send_mailalias($blogid,$blog_count,$from,$uname,$subj,$blogtitle,$blogurl,$message='',$pop_address) {
		global $xoopsDB;

		$files = array();
		// Strip img,html,crlf
		if ($GLOBALS['BlogCNF']['mail_view']==1){
			// Make for AddAttachment
			$ret = preg_match_all("/http\:\/\/[\w\.\~\-\/\?\&\+\=\:\@\%\#]+/",$message,$matches);
			for ($i=0; $i< count($matches[0]); $i++) {
				$afp = preg_replace("'".XOOPS_URL."'",XOOPS_ROOT_PATH,$matches[0][$i]);
				if (preg_match("/\.(".$GLOBALS['BlogCNF']['img_ext'].")$/i",$afp)) {
					$filename = mbstrings::cnv_mbstr(rawurldecode($afp));
					$files[$i]['path'] = $filename;	// 2006.03.03 hoshiba-farm.com
					$dl_filename = download::get_original_name($filename);
				}else{
					$b = strrpos($afp,'/');
					$afp = $GLOBALS['BlogCNF']['uploads'].substr($afp,$b+1);
					$filename = mbstrings::cnv_mbstr(rawurldecode($afp));
					$files[$i]['path'] = $filename;	// 2006.03.03 hoshiba-farm.com
					$down = new download($filename);
					$dl_filename = $down->fnameToDownload();
					$files[$i]['type'] = $down->contentType();
					$dl_filename = mbstrings::cnv_mbFilename( $dl_filename );
				}
				$files[$i]['name']=$dl_filename;
			}
			$message = preg_replace("':download:'","",$message);
			$message = sanitize_blog($message,true,false,true);
			$message = preg_replace("/<br \/>\n/i","\r\n",$message);
			$message = remove_html_tags($message); // added by hoshiba-farm
			if ($GLOBALS['BlogCNF']['mail_body_trunc']==1){		// added by hoshiba-farm
			  $message = mbstrings::_strcut($message,0,$GLOBALS['BlogCNF']['post_limit']);
			}
		}
		$myts =& MyTextSanitizer::getInstance();
		$message = $myts->stripSlashesGPC($message);
		$subj = $myts->MakeTboxData4Show($subj);
		//
		// Send to emailalias for mailinglist
		//
		$sql = "SELECT email FROM ".PBTBL_EMAILALIAS." WHERE blogid = $blogid and public = 2";
		$r = $xoopsDB->query($sql);
		if ($GLOBALS['BlogCNF']['use_sitefrom']==1) $from = PopnupBlogUtils::getXoopsModuleConfig('MAILADDR');
		while( list($email) = $xoopsDB->fetchRow($r) ){
			sendmail::send_ML($from,$email,$uname,$blog_count,$subj,$blogtitle,$blogurl,$message,$files,$pop_address);
			log::addlog("send_mailalias() FROM:$from, TO:$email BLOGID:$blogid SUBJECT:$subj");
		}
	}
	function ret_result_mail($mailto,$uname,$subj,$blogid,$blogname,$blogurl,$message='') {
		global $xoopsConfig,$BlogCNF;
		$mailfrom = $xoopsConfig['adminmail'];
		$xoopsMailer =& getMailer();
		$xoopsMailer->useMail();
		$xoopsMailer->setTemplateDir($BlogCNF['path']."language/".$xoopsConfig['language']."/mail_template/");
		$xoopsMailer->setTemplate("mail_results.tpl");
		$xoopsMailer->setToEmails($mailto);
		$xoopsMailer->assign("BLOG_UNAME", $uname);
		$xoopsMailer->assign("BLOG_TITLE", $subj);
		$xoopsMailer->assign("BLOG_MESSAGE", $message);
		$xoopsMailer->assign("BLOG_URL", $blogurl);
		$xoopsMailer->assign("SITENAME", $xoopsConfig['sitename']);
		$xoopsMailer->assign("ADMINMAIL", $xoopsConfig['adminmail']);
		$xoopsMailer->assign("SITEURL", $xoopsConfig['xoops_url']."/");
		$xoopsMailer->setFromEmail($mailfrom);
		$xoopsMailer->setFromName($xoopsConfig['sitename']);
		log::addlog("ret_result_mail() FROM:$mailfrom, TO:$mailto BLOGID:$blogid SUBJECT:$subj");
		$subj = sprintf(_MD_POPNUPBLOG_POSTYOURMAIL,$xoopsConfig['sitename'],$blogname);
		$subj = sendmail::enc_mimeheader($subj);
		$xoopsMailer->setSubject($subj);
		$xoopsMailer->send();
	}
	function notify($mailto,$mailfrom,$fromname,$subj,$msgs) {
		global $xoopsConfig,$BlogCNF;

		$xoopsMailer =& getMailer();
		$xoopsMailer->useMail();
	    $xoopsMailer->setToEmails($mailto);
		$xoopsMailer->setFromEmail($mailfrom);
		$xoopsMailer->setFromName($fromname);
		$xoopsMailer->setSubject(sendmail::enc_mimeheader($subj));
		$xoopsMailer->setBody($msgs);
		if ( !$xoopsMailer->send() )
			echo "<br />".$xoopsMailer->getErrors();
	}
	function xoops_notify($tpl_name,$blogid,$blogname,$blogurl,$subj,$message=''){
		// RMV-NOTIFY
		// Define tags for notification message
		$tags = array();
		$tags['BLOG_NAME'] = $blogname;
		$tags['POST_NAME'] = sendmail::enc_mimeheader($subj);
		$tags['POST_URL'] = $blogurl;
		$tags['POST_CONTENT'] = $message;
		$notification_handler =& xoops_gethandler('notification');
		$notification_handler->subscribe('blog', $blogid, $tpl_name);
		$notification_handler->triggerEvent('blog', $blogid, $tpl_name, $tags);
		$notification_handler->triggerEvent('global', 0, $tpl_name, $tags);
		// If user checked notification box, subscribe them to the
		// appropriate event; if unchecked, then unsubscribe
		//if (!empty($xoopsUser)) {	// && !empty($xoopsModuleConfig['notification_enabled'])
		//}
	}	
	function get_mailcode(){
		switch (_LANGCODE){
			case "af": $code = "ISO-8859-1";break;	//Afrikaans
			case "ar": $code = "ISO-8859-6";break;	//Arabic
			case "be": $code = "ISO-8859-5";break;	//Byelorussian
			case "bg": $code = "ISO-8859-5";break;	//Bulgarian
			case "ca": $code = "ISO-8859-1";break;	//Catalan
			case "cs": $code = "ISO-8859-2";break;	//Czech
			case "da": $code = "ISO-8859-1";break;	//Danish
			case "de": $code = "ISO-8859-1";break;	//German
			case "el": $code = "ISO-8859-7";break;	//Greek
			case "en": $code = "us-ascii";	break;	//English
			case "eo": $code = "ISO-8859-3";break;	//Esperanto
			case "es": $code = "ISO-8859-1";break;	//Spanish
			case "eu": $code = "ISO-8859-1";break;	//Basque
			case "et": $code = "iso-8859-15";break;	//Estonian
			case "fi": $code = "ISO-8859-1";break;	//Finnish
			case "fo": $code = "ISO-8859-1";break;	//Faroese
			case "fr": $code = "ISO-8859-1";break;	//French
			case "ga": $code = "ISO-8859-1";break;	//Irish
			case "gd": $code = "ISO-8859-1";break;	//Scottish
			case "gl": $code = "ISO-8859-1";break;	//Galician
			case "hr": $code = "ISO-8859-2";break;	//Croatian
			case "hu": $code = "ISO-8859-2";break;	//Hungarian
			case "is": $code = "ISO-8859-1";break;	//Icelandic
			case "it": $code = "ISO-8859-1";break;	//Italian
			case "iw": $code = "ISO-8859-8";break;	//Hebrew
			case "ja": $code = "ISO-2022-JP";break;	//Japanese (Shift_JIS)
			case "ko": $code = "EUC_KR";	break;	//Korean	
			case "lt": $code = "ISO-8859-13";break;	//Lithuanian
			case "lv": $code = "ISO-8859-13";break;	//Latvian
			case "mk": $code = "ISO-8859-5";break;	//Macedonian
			case "mt": $code = "ISO-8859-5";break;	//Maltese
			case "nl": $code = "ISO-8859-1";break;	//Dutch
			case "no": $code = "ISO-8859-1";break;	//Norwegian
			case "pl": $code = "ISO-8859-2";break;	//Polish
			case "pt": $code = "ISO-8859-1";break;	//Portuguese
			case "ro": $code = "ISO-8859-2";break;	//Romanian
			case "ru": $code = "ISO-8859-5";break;	//Russian
			case "sh": $code = "ISO-8859-5";break;	//Serbo-Croatian
			case "sk": $code = "ISO-8859-2";break;	//Slovak
			case "sl": $code = "ISO-8859-2";break;	//Slovenian
			case "sq": $code = "ISO-8859-2";break;	//Albanian
			case "sr": $code = "ISO-8859-2";break;	//Serbian
			case "sv": $code = "ISO-8859-1";break;	//Swedish
			case "th": $code = "TIS620";	break;	//Thai
			case "tr": $code = "ISO-8859-9";break;	//Turkish
			case "uk": $code = "ISO-8859-5";break;	//Ukrainian
			case "zh": $code = "GB2312";	break;	//Chainese	
			default: $code = "UTF-8";break;
		}
		return $code;
	}
}
?>
