<?php
// $Id: pop.ini.php,v 3.00 2006/11/23 19:41:10 yoshis Exp $
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
//
// URL and Path
//
$BlogCNF['root'] = XOOPS_URL."/modules/popnupblog/";			// mod root url
$BlogCNF['admin'] = $BlogCNF['root']."admin";					// mod admin url
$BlogCNF['path'] = XOOPS_ROOT_PATH."/modules/popnupblog/";		// mod root path
//
// View Option
//
$BlogCNF['default_view'] = 0;				// 0 = Title view, 1= With contents
$BlogCNF['wordwrap_width_title'] = 40;		// altanate width for title
$BlogCNF['wordwrap_width_contents'] = 60;	// altanate width for contents
//
// Edit Option ( If you care about attack by malicious people, keep WYSIWYG editor off. )
//
$BlogCNF['use_spaw'] = 1;		// 0 = OFF, 1= ON ( Anonymous are always off )
$BlogCNF['spaw_path'] = XOOPS_ROOT_PATH."/common/spaw/";	// SPAW folder ( Recommend in TinyD )
//
// Attached file section
//
$BlogCNF['StripAttachForML'] = true;			// Strip attach file for mailinglist
$BlogCNF['gd_ver'] = 2;							// PHP GD Version (0:No, 1:Ver 1, 2:Ver 2)
$BlogCNF['guest_dl'] = 0; 			// Guest Download Acceptable 0 = No , 1 = YES
$BlogCNF['uploads'] = XOOPS_ROOT_PATH.'/uploads/';	// Upload folder. You should set more secure folder (ex.'c:/upload/').
$BlogCNF['img_dir'] = "/uploads/";			// Attach and direct image file folder. Work with XOOPS_ROOT_PATH,XOOPS_URL
$BlogCNF['thumb_dir'] = "/uploads/thumbs/";	// Thumbnail folder. Work with XOOPS_ROOT_PATH,XOOPS_URL
$BlogCNF['w'] = 240;						// Thumbnail width pixsel 
$BlogCNF['h'] = 160;						// Thumbnail height pixsel 
$BlogCNF['img_ext'] = "gif|jpe?g|png|bmp|swf|3gp|avi|mov|ra?m|mpe?g|wmv";	// rename method for multimedia
$BlogCNF['thumb_ext'] = ".+\.jpe?g$|.+\.png$|.+\.gif$";	// Thumb image target file extentions
$BlogCNF['subtype'] = "gif|jpe?g|png|bmp|zip|lzh|rar|pdf|excel|powerpoint|msword|octet-stream|x-pmd|x-mld|x-mid|x-smd|x-smaf|x-mpeg";	// Acceptable MIME Content-Type
$BlogCNF['imgtype'] = "gif|jpe?g|png|bmp|x-pmd|x-mld|x-mid|x-smd|x-smaf|x-mpeg";	// Acceptable MIME for images
$BlogCNF['embedtype'] = "video|audio|x-shockwave-flash|3gpp";	// embedding EMBED MIME Content-Type
$BlogCNF['viri'] = "cgi|php|jsp|pl|htm";			// reject file extentions
$BlogCNF['maxbyte'] = "8000000";					// Max attach file byte size (8M)
//
// Mail Ctrl section
//
$limit_min = 1;							// Time intreval about Auto POP ( minutes )
$JUST_POPED = "Just Poped!";			// POP execute message for URL pop
$log_dir = './log/';					// Log folder
$poplog = $log_dir.'maillog.cgi';		// log file ( Change the 'maillog' strings for sequrity ¡Ë
$denylog = $log_dir.'deny.cgi';			// Deny log file ( Change the 'deny' strings for sequrity ¡Ë
$headerlog = $log_dir.'header.cgi';		// Mail header log file ( Change the 'header' strings for sequrity ¡Ë
$denylog_save = 1;				// Save the deny log (0:No, 1:Yes)
$head_save = 1;					// Save the header infomation (0:No, 1:Yes)
$maillogmax = 1024;						// Max saving log numbers
$nosubject = "No Title";				// Title for no subject
$BlogCNF['autoForMail'] = "ASCII,JIS,SJIS,EUC-JP,UTF-8";	// for mb_convert_encoding as expanding auto
//
// TrackBack section
//
$hide_referer = 0;				// You can hide for referer list from blog view
$tb_by_serchengine = "p|q|qt|web|query|MT|search|searchText|Text|QueryString";	// TB from Search engine
//
// Deny section
//
$BlogCNF['maxuserblogs'] = 10;			// Max Blogs par one user
$imgonly = 0;							// Doesn't save if w/o attach file (Yes=1 No=0¡Ë
$BlogCNF['post_limit']=10000;			// Max post charactor for body words from mail
$BlogCNF['text_limit']=1024;			// Max charactor for recently blog, 0 = Subject only (Work with 'blockview'=1)
$BlogCNF['blockview']=0;				// View mode on top block ( 0 = Full, 1 = Strip image and tag)
//
// MailingList control
//
$BlogCNF['mail_view']=1;				// View mode on mailinglist ( 0 = Full, 1 = Strip image and tag)
$BlogCNF['use_sitefrom']=0;				// mailing list from address ( 0 = Blog Poster, 1 = Site from as preference)
$BlogCNF['mail_body_trunc']=0;	// truncate mail body ( 0 = no,  1 = yes )
$BlogCNF['Attache_Select']=1;	// choose image file for atachement ( 0 = Original, 1 = Thumbnail )

// Deny POP address ( w/o log )
$deny_from = array('163.com','bigfoot.com','boss.com');

// Deny Mailer ( w/o log )
$deny_mailer = '';						// sample '/(Mail\s*Magic|Easy\s*DM|Friend\s*Mailer|Extra\s*Japan|The\s*Bat|IM2001)/i';

// Deny title ( w/o log )
$deny_title = '';						// sample '/((Ì¤|Ëö)\s?¾µ\s?(Âú|Ç§)\s?¹­\s?¹ð)|Áê¸ß¥ê¥ó¥¯/i';

// Deny charctorset (w/o log )
$deny_lang = '';						// sample '/big5|euc-kr|gb2312|iso-2022-kr|ks_c_5601-1987/i';

// Deny words for Comment-SPAM
$BlogCNF['deny_words'] = '/href=|url=/i';

// Cut the '_' over 25chars ( for Ad section)
$del_ereg = "[_]{25,}";

// Delete strings from body
$word[] = "http://auction.msn.co.jp/";
$word[] = "Do You Yahoo!?";
$word[] = "Yahoo! BB is Broadband by Yahoo!";
$word[] = "http://bb.yahoo.co.jp/";

// Update Ping Site  added by kazy 2006.11.24
$i = 1;
$update_ping[$i]['url'] = "http://rpc.technorati.jp/rpc/ping";
$update_ping[$i]['charset'] = "UTF-8";
$i++;
$update_ping[$i]['url'] = "http://ping.rss.drecom.jp";
$update_ping[$i]['charset'] = "UTF-8";
//$i++;
//$update_ping[$i]['url'] = "http://ping.bloggers.jp/rpc";
//$update_ping[$i]['charset'] = "UTF-8";
$i++;
$update_ping[$i]['url'] = "http://bulkfeeds.net/rpc";
$update_ping[$i]['charset'] = "euc-jp";
$BlogCNF['update_ping'] = $update_ping;

if (isset($GLOBALS)) {
    $GLOBALS['BlogCNF'] = $BlogCNF;
} else {
    global $BlogCNF;
}
?>
