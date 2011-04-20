<?php
// $Id: pop_func.php,v 3.16 2007/11/09 16:38:47 yoshis Exp $
//  ------------------------------------------------------------------------ //
//             Copyright (c) 2005-2007 Yoshi.Sakai @ Bluemoon inc.           //
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
// Special Thanks to hoshiyan at hoshiba-farm.com

function ReceiveFromPop3Server(&$dat,$host="",$user="",$pass="",$mail=""){
	global $img_mode,$BlogCNF,$JUST_POPED;
	// Connect start!!!
	//$sock = fsockopen($host, 110, $err, $errno, 10) or error_output("Can't connect to POP Server.");
	$sock = fsockopen($host, 110, $err, $errno, 15);
	
	if (!$sock) {
		//echo 'POP host='.$host;
		//echo "$errno ($err)<br />\n";
		popnupblog_PopDeny_log($host,$errno,$err,"POP Server fsockopen error.");
		error_output("Can't connect to POP Server.");
		return 0;
	}
	$buf = fgets($sock, 512);
	if(substr($buf, 0, 3) != '+OK'){
		popnupblog_PopDeny_log($host,$errno,$buf,"POP Server fgets error.");
		error_output($buf);
		return 0;
	}
	$buf = _sendcmd($sock,"USER $user");
	$buf = _sendcmd($sock,"PASS $pass");
	$data = _sendcmd($sock,"STAT");//STAT -件数とサイズ取得 +OK 8 1234
	sscanf($data, '+OK %d %d', $num, $size);
	if ($num == "0") {
		$buf = _sendcmd($sock,"QUIT"); // Quit
		fclose($sock);
		// update a log file timestamp
		@touch($poplog);
		return $num;
		/*
		if (!$img_mode){
			redirect_header($BlogCNF['root'],2,$JUST_POPED.'(no email found)');
		} else {
			// call a img tag.
			header("Content-Type: image/gif");
			readfile('spacer.gif');
		}
		exit;
		*/
	}
	// 件数分
	for($i=1;$i<=$num;$i++) {
		$line = _sendcmd($sock,"RETR $i");//RETR n -n番目のメッセージ取得（ヘッダ含
		$dat[$i] = "";
		while (!ereg("^\.\r\n",$line)) {//EOFの.まで読む
			$line = fgets($sock,512);
			$dat[$i].= $line;
		}
		$data = _sendcmd($sock,"DELE $i");//DELE n n番目のメッセージ削除
	}
	$buf = _sendcmd($sock,"QUIT"); //GOOD-BYE POP3!
	fclose($sock);
	return $num;
}
/* Send Command */
function _sendcmd($sock,$cmd) {
	fputs($sock, $cmd."\r\n");
	$buf = fgets($sock, 512);
	if(substr($buf, 0, 3) == '+OK') {
		return $buf;
	} else {
		error_output($buf);
	}
	return false;
}
function MailData2Blog(&$MailData,$num,$defblogid=0,$mailto,&$lines,&$ThisBlog,$strip_attach){
	global $BlogCNF,$head_save,$deny_mailer,$deny_lang,$deny_title,$deny_from,$del_ereg,$del_word,$imgonly,$denylog_save,$SHOW_NAME;
	global $uid;
	$debug = 0;
	for($j=1;$j<=$num;$j++) {
		$goahead = true;
		$filename = $subject = $from = $text = $atta = $part = $attach = $notice ="";
		list($head, $body) = mime_split($MailData[$j]);
		// To:ヘッダ確認
		if (preg_match("/(?:^|\n|\r)To:[ \t]*([^\r\n]+)/i", $head, $treg)){
			$toreg = "/".quotemeta($mailto)."/";
			if (!preg_match($toreg,$treg[1])) $goahead = false; //投稿アドレス以外
		} else {
			// To: ヘッダがない
			$goahead = false; $notice ="No 'To:' Header";
		}
		// メーラーのチェック
		if ($goahead && (eregi("(\nX-Mailer|\nX-Mail-Agent):[ \t]*([^\r\n]+)", $head, $mreg))) {
			if ($deny_mailer){
				if (preg_match($deny_mailer,$mreg[2])){ $goahead = false; $notice=$deny_mailer;}
			}
		}
		// キャラクターセットのチェック
		if ($goahead && (eregi("charset[\s]*=[\s]*([^\r\n]+)", $head, $mreg))) {
			if ($deny_lang){
				if (preg_match($deny_lang,$mreg[1])){ $goahead = false; $notice=$deny_lang;}
			}
		}
		// 日付の抽出
		eregi("\nDate:[ \t]*([^\r\n]+)", $head, $datereg);
		$posttime = make_timestamp_for_post($datereg);
		// サブジェクトの抽出
		if (preg_match("/\nSubject:[ \t]*(.+?)(\n[\w-_]+:|$)/is", $head, $subreg)) {
			// 改行文字削除
			$subject = str_replace(array("\r","\n"),"",$subreg[1]);
			// エンコード文字間の空白を削除
			$subject = preg_replace("/\?=[\s]+?=\?/","?==?",$subject);
			
			while (eregi("(.*)=\?iso-[^\?]+\?B\?([^\?]+)\?=(.*)",$subject,$regs)) {//MIME B
				$subject = $regs[1].base64_decode($regs[2]).$regs[3];
			}
			while (eregi("(.*)=\?iso-[^\?]+\?Q\?([^\?]+)\?=(.*)",$subject,$regs)) {//MIME Q
				$subject = $regs[1].quoted_printable_decode($regs[2]).$regs[3];
			}
			$subject = htmlspecialchars(convert($subject));
			//$subject = htmlspecialchars(JcodeConvert($subject,0,1));
			// 未承諾広告カット
			if ($goahead && $deny_title){
				if (preg_match($deny_title,$subject)){ $goahead = false; $notice=$deny_title;}
			}
			$maillog_subject = $subject;
			// Get Blogid from Subject. [b1,Subject strings]
			if($defblogid==0){
				if (eregi("^([Bb])([0-9]*),[ \t]*([^\r\n]+)", $subject, $freg)){
					$blogid = intval($freg[2]);
					if ($blogid==0){
						$goahead = false; $notice = "No Blogid on Subject[$subject] from[$from].";
					}else{
						$subject = $freg[3];
					}
				}else{
					$blogid = 0;
				}
			}else{
				// For Mailing list
				$blogid = $defblogid;
			}
			if ($blogid==0){
				$goahead = false; $notice="Blog ID error at subject. [$subject]";
			}
		}
		// 送信者アドレスの抽出
		if (eregi("\nFrom:[ \t]*([^\r\n]+)", $head, $freg)) {
			$from = addr_search($freg[1]);
	        //echo "Message van $from <br>";
		} elseif (eregi("\nReply-To:[ \t]*([^\r\n]+)", $head, $freg)) {
			$from = addr_search($freg[1]);
		} elseif (eregi("\nReturn-Path:[ \t]*([^\r\n]+)", $head, $freg)) {
			$from = addr_search($freg[1]);
		}
		// For maillog.cgi
		list($old,,,,) = explode("<>", $lines[0]);
		$id = $old + 1;
		if(trim($maillog_subject)=="") $maillog_subject = $nosubject;
		$line = "$id<>" . date("Y/m/d H:i:s", time()) . "<>" . date("Y/m/d-H:i:s", $posttime) . "<>$from<>$maillog_subject<>\r\n";
	
		// For POP3 log for debug
		//$pfp = fopen($log_dir . 'pop3.log', 'a');
		//fwrite($pfp,$line);
		//fclose($pfp);
	
		// Check deny address			move check point hoshiyan hoshiba-farm.com 2006.03.07
		if ($goahead){
			for ($f=0; $f<count($deny_from); $f++)
				if (eregi($deny_from[$f], $from)){ $goahead = false; $notice=$deny_from[$f];}
		}
		if ($goahead) {
			// Get Blog info Parameters
			// echo $from . "bid=" . $blogid;
			$uid = get_uid_from_email($from,$blogid);
			if ( $uid != null ){
				$ThisBlog = new PopnupBlog($blogid);
				$uname = users::uname($uid);	//$ThisBlog->getTargetUname();
			} else {
				$anonymous_bid = PopnupBlogUtils::getXoopsModuleConfig('guestpost_blogid');
				if ($anonymous_bid) {
					$ThisBlog = new PopnupBlog($blogid);
					$uid = 0;
					$uname = $from;
				} else {
					$goahead = false; $notice="No Match at blogid[$blogid] from[$from].";
				}
			}
			if ($debug) echo "from=$from,blogid=$blogid,uid=$uid,uname=$uname<br>";
		}
		if ($goahead) {				// Added by hoshiyan hoshiba-farm.com 2006.03.07
			// if multipart then devide boundary 
			if (eregi("\nContent-type:.*multipart/",$head)) {
				eregi('boundary="([^"]+)"', $head, $boureg);
				$body = str_replace($boureg[1], urlencode($boureg[1]), $body);
				$part = split("\r\n--".urlencode($boureg[1])."-?-?",$body);
				if (eregi('boundary="([^"]+)"', $body, $boureg2)) {//multipart/altanative
					$body = str_replace($boureg2[1], urlencode($boureg2[1]), $body);
					$body = eregi_replace("\r\n--".urlencode($boureg[1])."-?-?\r\n","",$body);
					$part = split("\r\n--".urlencode($boureg2[1])."-?-?",$body);
				}
			} else {
				$part[0] = $MailData[$j];// ordinary text mail
			}
			//print_r($part);
			$addimg = $addfile = '';
			foreach ($part as $multi) {
				list($m_head, $m_body) = mime_split($multi);
				$m_body = ereg_replace("\r\n\.\r\n$", "", $m_body);
				// キャラクターセットのチェック
				if ($goahead && (eregi("charset[\s]*=[\s]*([^\r\n]+)", $m_head, $mreg))) {
					if ($deny_lang){
						if (preg_match($deny_lang,$mreg[1])){
							$goahead = false; $notice=$deny_lang; break;
						}
					}
				}
				if (!eregi("\nContent-type: *([^;\n]+)", $m_head, $type)) continue;
				list($main, $sub) = explode("/", $type[1]);
				// 本文をデコード
				if (strtolower($main) == "text") {
					if (eregi("\nContent-Transfer-Encoding:.*base64", $m_head))
						$m_body = base64_decode($m_body);
					if (eregi("\nContent-Transfer-Encoding:.*quoted-printable", $m_head))
						$m_body = quoted_printable_decode($m_body);
					$text = trim(convert($m_body));
					//$text = JcodeConvert($m_body,0,1);
					if ($sub == "html") $text = strip_tags($text);
					$text = str_replace(">","&gt;",$text);
					$text = str_replace("<","&lt;",$text);
					$text = str_replace("\r\n", "\r",$text);
					$text = str_replace("\r", "\n",$text);
					$text = preg_replace("/\n{2,}/", "\n\n", $text);
					// Delete phone number
					$text = eregi_replace("([[:digit:]]{11})|([[:digit:]\-]{13})", "", $text);
					// Delete under line
					$text = eregi_replace($del_ereg, "", $text);
					// Delete mac //mac削除
					$text = ereg_replace("Content-type: multipart/appledouble;[[:space:]]boundary=(.*)","",$text);
					// Delete Ads.
					if (is_array($del_word)) {
						foreach ($del_word as $delstr)
							$text = str_replace($delstr, "", $text);
					}
					if (strlen($text) > $BlogCNF['post_limit']) $text = substr($text, 0, $BlogCNF['post_limit'])."...";
				}elseif ($strip_attach == false){
					// Pickup filename
					if (eregi("name=\"?([^\"\n]+)\"?",$m_head, $filereg)) {
						$filename = trim($filereg[1]);
						// Omit the space char between encode strings
						$filename = preg_replace("/\?=[\s]+?=\?/","?==?",$filename);
						// MIME B
						while (eregi("(.*)=\?iso-[^\?]+\?B\?([^\?]+)\?=(.*)",$filename,$regs)) {
							$filename = $regs[1].base64_decode($regs[2]).$regs[3];
						}
						$filename = strtolower(convert($filename));
					}
					// Decode attached file and save it
					if (eregi($BlogCNF['subtype'], $sub)){ $deny_ftype=0; } else { $deny_ftype=1; };
					$ext = end(explode(".",$filename));
					if (eregi($BlogCNF['viri'], $ext)) $deny_ftype = 1;
					// asign new unique name for this upload file( uname_time_+ base name)
					// replace some special character in base name for stable functionality
					$sc = array("~");		// 2006.03.02 added by hoshiba-farm.com
					$dc = array("-");
					$filename = str_replace($sc, $dc, $filename);
					$tmp = base64_decode($m_body);
					if (eregi("\nContent-Transfer-Encoding:.*base64", $m_head) &&
								eregi($BlogCNF['imgtype'].'|'.$BlogCNF['embedtype'], $sub)) {
						//	echo "size=".strlen($tmp)." maxsize=".intval($BlogCNF['maxbyte']);
						if ((strlen($tmp) < intval($BlogCNF['maxbyte'])) && ($deny_ftype == 0)) {
							$upfile_localname = $uname."_".time()."_".$filename; 
							//	echo $upfile_localname."<br />";
							//	echo " deny_ftype=".$deny_ftype." ext=".$ext;
							$upfile_localname = mbstrings::cnv_mbstr($upfile_localname);
							$upfile_url = XOOPS_URL.$BlogCNF['img_dir'].rawurlencode($upfile_localname);
							$upfile_path = XOOPS_ROOT_PATH.$BlogCNF['img_dir'].$upfile_localname;
							$fp = fopen($upfile_path, "wb");
							fputs($fp, $tmp);
							fclose($fp);
							$attach = $filename;
		 					if (eregi($BlogCNF['imgtype'], $sub)){
								// Thumbs Support ( PHP GD Libraly Required )
								if (eregi($BlogCNF['thumb_ext'],$upfile_localname)) {
									$size = getimagesize($upfile_path);
									if ($size[0] > $BlogCNF['w'] || $size[1] > $BlogCNF['h']) {
										$thumbfilename = thumb_create($upfile_path,$BlogCNF['w'],$BlogCNF['h'],XOOPS_ROOT_PATH.$BlogCNF['thumb_dir']);
										$addimg .= "[url=".$upfile_url."][img align=left]".XOOPS_URL.$BlogCNF['thumb_dir'].rawurlencode($thumbfilename)."[/img][/url]";
									} else {
										$addimg .= "[img align=left]".$upfile_url."[/img]";
									}
								} else {
									$addimg .= "[img align=left]".$upfile_url."[/img]";
								}
		 					} elseif (eregi($BlogCNF['embedtype'], $sub)){
								$addimg .= "\n<EMBED src=\"".$upfile_url."\" WIDTH=\"".$BlogCNF['w']."\" HEIGHT=\"".$BlogCNF['h'].
								"\" autostart=\"false\" controller=\"true\" hspace=\"5\" align=\"left\" alt=\"\"></EMBED>\n";
							}
						} else {
							$goahead = false;
							$notice = "Over sized (" . strlen($tmp) . " >= " .$BlogCNF['maxbyte'] . ") or Illegal file type.($ext)";
						}
					} elseif ($deny_ftype==0) {
						$upfile_localname=$uname."_".$filename.".".time();
						$upfile_url='/'.rawurlencode($upfile_localname);	// XOOPS_UPLOAD_URL.
						$upfile_localname = mbstrings::cnv_mbstr($upfile_localname);		// convert for mbstrings
						$fp = fopen($BlogCNF['uploads'].$upfile_localname, "wb");
						fputs($fp, $tmp);
						fclose($fp);
						$addfile .= "\n:download:[url=".$BlogCNF['root']."download.php?url=".$upfile_url."]".$filename."[/url]\n";
						// $addfile .= $m_head.$sub;
					} elseif ( strlen($ext)>0 ) {
						$addfile .= "\nUpload Denied...".$sub." ext ".$ext;
					}
					// For Plugin parameter
					if($upfile_localname) $upfile[] = array( 'localname' => $upfile_localname, 'url'=>$upfile_url );
				}
			}
			$text = $addimg.$text.$addfile;
			if ($imgonly && $attach==""){ $goahead = false; $notice ="Accept image only";}
		}
		// Write popnupblog page
		if ($goahead) {
			$usename = XoopsUser::getUnameFromId( $uid, $SHOW_NAME );
			if( $blogurl = pop_enterBlog( $ThisBlog, $text, $subject, $blogid, $uid, $uname, $posttime, $from, $usename )){
				// For Plugin
				if (isset($upfile)){
					$GLOBALS['upfile']=$upfile;
					$pluginmsg = sprintf(_MI_POPNUPBLOG_ATTACHEDFILE,count($upfile));
				} else{
					$pluginmsg = ""; // Will be add by plugin
				}
				if ($debug && $ThisBlog->plugin) echo "Plugin :" . XOOPS_ROOT_PATH . "/modules/popnupblog/plugin/" . $ThisBlog->plugin . "<BR />";
				if ($ThisBlog->plugin) @include(XOOPS_ROOT_PATH . "/modules/popnupblog/plugin/" . $ThisBlog->plugin );
				if ($debug && $ThisBlog->plugin) echo "Return :" . $pluginmsg . "<BR />";
				if ($ThisBlog->default_status==0) $pluginmsg .= "\n"._MD_THANKS_POSTING."\n";
				sendmail::ret_result_mail($from,$uname,$subject,$blogid,$ThisBlog->title,$blogurl,$pluginmsg);
				//echo "blog Updated!";
			}else{
				$goahead = false; $notice ="Couldn't post Blog by pop_enterBlog";
			}
		}
		array_unshift($lines, $line);
		// Save a header info.
		if ($head_save) popnupblog_head_save($id,$head);
		if (!$goahead) {
			// Save a deny mail log
			print("Error - ".$notice);
			if ($denylog_save) popnupblog_deny_log($head,$subject,str_replace("<br />","\n",$text),$notice);
		}
	}
}
/* Divide header and body */
function mime_split($data) {
	$part = split("\r\n\r\n", $data, 2);
	$part[0] = ereg_replace("\r\n[\t ]+", " ", $part[0]);
	return $part;
}
/* Pickup email address */
function addr_search($addr) {
	if (eregi("[-!#$%&\'*+\\./0-9A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+", $addr, $fromreg)) {
		return $fromreg[0];
	} else {
		return false;
	}
}
/* Convert to local language (it need a mb function support on php.ini */
function convert($str,$code=_CHARSET) {
	global $BlogCNF;
	if (function_exists('mb_convert_encoding')) {
		return mb_convert_encoding($str, $code, $BlogCNF['autoForMail']);
	} elseif (function_exists('JcodeConvert')) {
		return JcodeConvert($str, 0, 2);
	}
	return $str;
}
// Save a POP connect deny log.
function popnupblog_PopDeny_log($host,$errno,$err,$notice){
	global $denylog;
	// Save to file
	$fp = fopen($denylog, "a+b");
	flock($fp, LOCK_EX);
	fwrite($fp, date("Y/m/d H:i:s ", time()) . "{$host}, Error: {$errno},{$err},{$notice}\r\n");
	fclose($fp);
	return;
}
// Save a denied mail log.
function popnupblog_deny_log($head,$subject,$body,$notice){
	global $denylog;
	$subject = unhtmlentities($subject);
	$body = unhtmlentities($body);
	// Save to file
	$fp = fopen($denylog, "a+b");
	flock($fp, LOCK_EX);
	fwrite($fp, date("Y/m/d H:i:s ", time()) . "{$head}, Subject: {$subject}, Body: {$body},{$notice}\r\n");
	fclose($fp);
	return;
}
// Save a header information
function popnupblog_head_save($id,$head){
	global $headerlog;
	// Save to file
	$fp = fopen($headerlog, "a+b");
	$op = $id . "<>" . date("Y/m/d H:i:s ", time()) . "\r\n" . $head . "\r\n";
	flock($fp, LOCK_EX);
	fputs($fp, $op);
	fclose($fp);
	return;
}

// Trun back to HTML Entities
function unhtmlentities ($string){
	$trans_tbl = get_html_translation_table (HTML_ENTITIES);
	$trans_tbl = array_flip ($trans_tbl);
	return strtr ($string, $trans_tbl);
}

// Output Error
function error_output ($str){
	global $img_mode;
	if ($img_mode){
		header("Content-Type: image/gif");
		readfile('poperror.gif');
	}
	exit;
}
function popnupdate( $blogid, $posttime ){
	global $xoopsDB;
	$sql = sprintf("UPDATE %s set last_update = '%s' where blogid = %u",
		PBTBL_INFO, date("YmdHis", $posttime), $blogid );
	$xoopsDB->queryF($sql);
}
//
// added by hoshiba-farm 2006.02.28
//
function pop_enterBlog(&$ThisBlog, $text, $subject = '', $blogid, $uid, $comment_name, $posttime, $from, $usename){
	global $xoopsConfig;
	$postid = 0;
	$dates=getdate($posttime);				// update by hoshiyan@hoshiba-farm.com 2004.7.14
//	echo date("M d Y H:i:s", $posttime);
//	$sqlDate =sprintf("%04d-%02d-%02d %02d:%02d:%02d",$dates['year'],$dates['mon'],$dates['mday'],$dates['hours'],$dates['minutes'],$dates['seconds']);
	$postDate['year'] = sprintf("%04d",$dates['year']);
	$postDate['month'] = sprintf("%02d",$dates['mon']);
	$postDate['date'] = sprintf("%02d",$dates['mday']);
	$postDate['hours'] = sprintf("%02d",$dates['hours']);
	$postDate['minutes'] = sprintf("%02d",$dates['minutes']);
	$postDate['seconds'] = sprintf("%02d",$dates['seconds']);
	//echo $postDate, $text, $subject, $blogid, 0 ,$from;
	$status = $ThisBlog->default_status;
	if ( preg_match("/^(re: \[)([^\:]+)([^0-9]+)([^\]]+)/i", $subject,$arg) ) {
		$blogname = $arg[2];
		$blog_count = intval($arg[4]);
		$postinfo = popnupblog::postInfo($blogid,$blog_count);
		$postid = $postinfo['postid'];
		$subj = "Re: " . $postinfo['title'];
		$ret = pb_comment::insertComment($blogid,$postid,$uid,$comment_name,$text,"",$status,1);
		if ($ret>0){
			$ownersemail = users::email($ThisBlog->blogUid);
			$blogurl = PopnupBlogUtils::createUrlpostid($postid);
			sendmail::send_mailalias($blogid,$blog_count,$from,$usename,$subj,$ThisBlog->title,$blogurl,$text,$ThisBlog->pop_address);
			sendmail::xoops_notify('new_comment',$blogid,$ThisBlog->title,$blogurl,$subj,$text);
		}
	} else {
		$ret = $ThisBlog->updateBlog( $postid, $postDate, $text, $subject, $blogid, $uid, $from, $status, 1);
	}
	if($ret>0){
		return PopnupBlogUtils::createUrlpostid($postid);
	}elseif($ret==0){
		$new_user_notify = PopnupBlogUtils::getXoopsModuleConfig('new_user_notify');
		if ( $new_user_notify == 1 ) {
			$subj = sprintf(_MD_POPNUPBLOG_NEWWAITING,$xoopsConfig['sitename']);
			$msgs = sprintf(_MD_POPNUPBLOG_NEWWAITING_DESC,"\n\n".XOOPS_URL."/modules/popnupblog/admin/waiting.php");
			sendmail::notify($xoopsConfig['adminmail'],$from,$xoopsConfig['sitename'],$subj,$msgs);
		}
		return PopnupBlogUtils::createUrlpostid($postid);
	}
	return null;
}
function get_uid_from_email( $email, $blogid ){
	global $xoopsDB;
	$uid = emailalias::get_uid_bymail($blogid,1,$email);
	if ($uid) return $uid;
	$rawuid = $xoopsDB->fetchArray($xoopsDB->query("SELECT uid,group_post,email FROM ".PBTBL_INFO." WHERE blogid=".$blogid." limit 1"));
	if($rawuid['uid'] && strcmp($rawuid['email'],$email)==0 ) return $rawuid['uid'];
	// Match as user group
	if($rawuid['group_post']){
		$group_posts = grp_getGroupIda($rawuid['group_post']);
		//if ( in_array( "3" , $group_posts ) ) return false;		// For Guest Group ID
		$sql = "SELECT l.groupid, u.uid FROM ".$xoopsDB->prefix("groups_users_link")
			." l LEFT JOIN ".$xoopsDB->prefix("users")." u ON l.uid=u.uid WHERE email='$email' ORDER BY l.groupid";
		$result = $xoopsDB->query($sql);
		while( list( $groupid, $uid ) = $xoopsDB->fetchRow( $result ) ) {
			if ( in_array( $groupid , $group_posts ) ) return $uid;
		}
	}
	$rawuid = $xoopsDB->fetchArray($xoopsDB->query("SELECT uid FROM ".PBTBL_EMAILALIAS." WHERE blogid = '".$blogid."' and email = '".$email."' limit 1"));
	if($rawuid['uid']) return $rawuid['uid'];
	return null;
}
// make timestamp in server side timezone to post blog data
// By hoshiyan@hoshiba-farm.com 2004/7/14
function make_timestamp_for_post($datereg) {
	$debug = 0;
	$posttime = strtotime($datereg[1]);
	if ($posttime == -1) $posttime = date('U');
	if ($debug) {
		print("datereg".$datereg[1]);
		print("now".$posttime);
		echo "Server Timezone = ".date("Y-m-d H:i:s", $posttime);
		echo "User Timezone = ".formatTimeZone($posttime, 'l');
	}
	return $posttime;
}

?>
