<?php
// $Id: edit.php,v 3.25 2009/12/02 17:57:56 yoshis Exp $
//  ------------------------------------------------------------------------ //
//                               Popnup Blog                                 //
//                     Copyright (c) 2009 bluemoon inc.                      //
//                       <http://www.bluemooninc.biz/>                       //
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
require('header.php');
include_once XOOPS_ROOT_PATH."/include/xoopscodes.php";
include_once('pop.ini.php');		// Load init file
include_once('./include/thumb.php');
include_once "./class/mbstrings.php";

if (!empty($xoopsUser)) $admin = $xoopsUser->isAdmin();
if(!$xoopsUser || !is_object($xoopsUser)){
	redirect_header(XOOPS_URL.'/modules/popnupblog/',2,_MD_POPNUPBLOG_CAN_WRITE_USER_ONLY);
	exit();
}
if(!xoops_refcheck()){
	redirect_header(XOOPS_URL.'/modules/popnupblog/',2,'Referer Check Failed');
	exit();
}
//print_r($_SERVER);
$debug = 0;
$uid = $xoopsUser->uid();
$confirm_mailalias = isset($_POST['confirm_mailalias']) ? 'on' : 'off';
$confirm_mailsends = isset($_POST['confirm_mailsends']) ? 'on' : 'off';
$add_mailalias = isset($_POST['add_mailalias']) ? 'on' : 'off';
$add_mailsends = isset($_POST['add_mailsends']) ? 'on' : 'off';
$remove_mailalias = isset($_POST['remove_mailalias']) ? 'on' : 'off';
$remove_mailsends = isset($_POST['remove_mailsends']) ? 'on' : 'off';
$updatepreference = isset($_POST['updatepreference']) ? 'on' : 'off';
$commit = isset($_POST['commit']) ? 'on' : 'off';
$preview = isset($_POST['preview']) ? 'on' : 'off';
$delete = isset($_POST['delete']) ? 'on' : 'off';
$delete_tb = isset($_POST['delete_tb']) ? 'on' : 'off';
$usespaw = empty( $_GET['usespaw'] ) ? 0 : 1 ;
$usespaw = empty( $_POST['usespaw'] ) ? $usespaw : 1 ;
$today = isset($_GET['today']) ? htmlspecialchars ( $_GET['today'] , ENT_QUOTES ) : '';
if ($today && $today!='on'){
	$blogid = intval($today);
}else{
	$MyBlogIDs = bloginfo::get_blogid_from_uid($xoopsUser->uid());
	$blogid = isset($_POST['blogid']) ? $_POST['blogid'] : $MyBlogIDs[0]['blogid'];
}
$bloginfo = array();
$bloginfo = bloginfo::get_bloginfo($blogid);

if($add_mailalias == 'on') {
	if ($_POST['emailalias_txt']){
		$alias_uid = $_POST['alias_uid'];
		if(!$alias_uid) $alias_uid = 0; 
		emailalias::_add($_POST["bid"],1,$_POST["emailalias_txt"],$alias_uid);
	}
	$xoopsTpl->assign('bloginfo',PopnupBlogUtils::getBlogInfo($xoopsUser->uid()));
	$xoopsTpl->assign('GroupSetByUser',$xoopsModuleConfig['GroupSetByUser']);
	$xoopsOption['template_main'] = 'popnupblog_application.html';
}elseif($confirm_mailalias == 'on') {
	if ($_POST['alias_uname']){
		$myrow = PopnupBlogUtils::find_uid_mail($_POST['alias_uname']);
		$alias_uid = $myrow[0];
		$alias_email = $myrow[1];
		$xoopsTpl->assign('alias_uname',$_POST['alias_uname']);
		$xoopsTpl->assign('alias_uid',$alias_uid);
		$xoopsTpl->assign('alias_email',$alias_email);
	}
	$xoopsTpl->assign('bloginfo',PopnupBlogUtils::getBlogInfo($xoopsUser->uid()));
	$xoopsTpl->assign('GroupSetByUser',$xoopsModuleConfig['GroupSetByUser']);
	$xoopsOption['template_main'] = 'popnupblog_application.html';
}elseif($remove_mailalias == 'on') {
	$bid = isset($_POST['bid']) ? intval($_POST['bid']) : 0;
	if ($_POST['emailalias']){
		emailalias::_deletebylist($_POST["bid"],1,$_POST['emailalias']);
	}
	$xoopsTpl->assign('bloginfo',PopnupBlogUtils::getBlogInfo($xoopsUser->uid()));
	$xoopsTpl->assign('GroupSetByUser',$xoopsModuleConfig['GroupSetByUser']);
	$xoopsOption['template_main'] = 'popnupblog_application.html';
}elseif($confirm_mailsends == 'on') {
	if ($_POST['sends_uname']){
		$myrow = PopnupBlogUtils::find_uid_mail($_POST['sends_uname']);
		$sends_uid = $myrow[0];
		$sends_email = $myrow[1];
		$xoopsTpl->assign('sends_uname',$_POST['sends_uname']);
		$xoopsTpl->assign('sends_uid',$sends_uid);
		$xoopsTpl->assign('sends_email',$sends_email);
	}
	$xoopsTpl->assign('bloginfo',PopnupBlogUtils::getBlogInfo($xoopsUser->uid()));
	$xoopsTpl->assign('GroupSetByUser',$xoopsModuleConfig['GroupSetByUser']);
	$xoopsOption['template_main'] = 'popnupblog_application.html';
}elseif($add_mailsends == 'on') {
	if ($_POST['emailsends_txt']){
		$sends_uid = $_POST['sends_uid'];
		if(!$sends_uid) $sends_uid = 0; 
		emailalias::_add($_POST["bid"],2,$_POST["emailsends_txt"],$sends_uid);
	}
	$xoopsTpl->assign('bloginfo',PopnupBlogUtils::getBlogInfo($xoopsUser->uid()));
	$xoopsTpl->assign('GroupSetByUser',$xoopsModuleConfig['GroupSetByUser']);
	$xoopsOption['template_main'] = 'popnupblog_application.html';
}elseif($remove_mailsends == 'on') {
	$bid = isset($_POST['bid']) ? intval($_POST['bid']) : 0;
	if ($_POST['emailsends']){
		emailalias::_deletebylist($_POST["bid"],2,$_POST['emailsends']);
	}
	$xoopsTpl->assign('bloginfo',PopnupBlogUtils::getBlogInfo($xoopsUser->uid()));
	$xoopsTpl->assign('GroupSetByUser',$xoopsModuleConfig['GroupSetByUser']);
	$xoopsOption['template_main'] = 'popnupblog_application.html';
}elseif($updatepreference == 'on') {
	$targetBid = isset($_POST['bid']) ? intval($_POST['bid']) : 0;
	$cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
	$group_post    = grp_saveAccess( isset($_POST['g_post'])    ? $_POST['g_post']    : NULL );
	$group_read    = grp_saveAccess( isset($_POST['g_read'])    ? $_POST['g_read']    : NULL );
	$group_comment = grp_saveAccess( isset($_POST['g_comment']) ? $_POST['g_comment'] : NULL );
	$group_vote    = grp_saveAccess( isset($_POST['g_vote'])    ? $_POST['g_vote']    : NULL );
	$title = isset($_POST['title']) ? ($_POST['title']) : "";
	$desc = isset($_POST['desc']) ? ($_POST['desc']) : "";
	$email = isset($_POST['email']) ? ($_POST['email']) : "";
	//      Modified by hoshiyan@hoshiba-farm.com 2004.8.4
	//$emailalias = isset($_POST['emailalias']) ? ($_POST['emailalias']) : "";
	if($xoopsUser->uid()){
		$blog = new PopnupBlog($targetBid);
		$blog->setBlogInfo($cat_id,$title,$desc,$group_post,$group_read,$group_comment,$group_vote,$email);
		//emailalias::setEmailAliasInfo($targetBid,$emailalias);
	}
	$xoopsTpl->assign('bloginfo',PopnupBlogUtils::getBlogInfo($xoopsUser->uid()));
	$xoopsTpl->assign('GroupSetByUser',$xoopsModuleConfig['GroupSetByUser']);
	$xoopsOption['template_main'] = 'popnupblog_application.html';
}elseif($today == 'preference'){
	if( !$xoopsUser ){
		redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_NORIGHTTOACCESS.'(1.2)');
		exit();
	}
	$xoopsTpl->assign('bloginfo',PopnupBlogUtils::getBlogInfo($xoopsUser->uid()));
	$member_handler =& xoops_gethandler('member');
	$groupnames = $member_handler->getGroupList(); 
	$categories = category::get_categories();
	$xoopsTpl->assign('categories', PopnupBlogUtils::mkselect('cat_id',$categories,null));
	$xoopsTpl->assign('GroupSetByUser',$xoopsModuleConfig['GroupSetByUser']);
	$xoopsTpl->assign('mail_prefix', sprintf(_MD_POPNUPBLOG_MAILPRIFIX_DESC,$blogid));
	$xoopsOption['template_main'] = 'popnupblog_application.html';
}elseif((!$bloginfo && $today == 'on') || ($today == 'apply')){
	if((!empty($xoopsModuleConfig['POPNUPBLOG_APPL'])) && ($xoopsModuleConfig['POPNUPBLOG_APPL'] == 1) ){
		redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_NORIGHTTOACCESS.'(1.2)');
		exit();
	}
	$member_handler =& xoops_gethandler('member');
	$groupnames = $member_handler->getGroupList(); 
	$categories = category::get_categories();
	$xoopsTpl->assign('categories', PopnupBlogUtils::mkselect('cat_id',$categories,null));
	$xoopsTpl->assign('g_post', grp_listGroups("","g_post[]"));
	$xoopsTpl->assign('g_read', grp_listGroups("1 2 3","g_read[]"));
	$xoopsTpl->assign('g_comment', grp_listGroups("1 2 3","g_comment[]"));
	$xoopsTpl->assign('g_vote', grp_listGroups("1 2 3","g_vote[]"));
	$xoopsTpl->assign('GroupSetByUser',$xoopsModuleConfig['GroupSetByUser']);
	$xoopsOption['template_main'] = 'popnupblog_application.html';
}else{
	$params = PopnupBlogUtils::getDateFromHttpParams();
    // Delete TrackBack added by kazy 2006.11.18
	if($delete_tb == 'on' && isset($_POST['batch'])){
      $tbs = $_POST['batch'];
      $where = " WHERE 1=0 ";
      $ts =& MyTextSanitizer::getInstance();
      foreach($tbs as $tbid){
        $where .= " or tbid=".$ts->addSlashes($tbid);
      }
      // check permission
      $sql = "SELECT blogid FROM ".PBTBL_TRACKBACK.$where;
      $result = $xoopsDB->query($sql);
      $flag = ( count($tbs) == $xoopsDB->getRowsNum($result) );
      unset($blogid);
      while($item = $xoopsDB->fetchArray($result)){
        if(isset($blogid)){
          $flag = $flag && ($blogid == $item['blogid']);
        }else{
          $blogid = $item['blogid'];
        }
      }
      if($flag){
        $blog = new PopnupBlog($blogid);
        if($blog->canWrite()){
          $sql = "DELETE FROM ".PBTBL_TRACKBACK." WHERE tbid=".implode($tbs," or tbid=");
          $xoopsDB->queryF($sql);
          redirect_header(PopnupBlogUtils::createUrlpostid($params['postid']),2,_MD_POPNUPBLOG_BLOG_UPDATE);
          exit();
        }
      }
      redirect_header(PopnupBlogUtils::createUrlpostid($params['postid']),2,_MD_POPNUPBLOG_INTERNALERROR.'(0.2)');
      exit();
	}elseif($delete == 'on'){
		$blog = new PopnupBlog($blogid);
		if($blog->deleteBlog1($params['postid'])){
			redirect_header(PopnupBlogUtils::createUrl($params['blogid']),2,_MD_POPNUPBLOG_BLOG_UPDATE);
			exit();
		}else{
			redirect_header(PopnupBlogUtils::createUrl($params['blogid']),2,_MD_POPNUPBLOG_INTERNALERROR.'(0.3)');
			exit();
		}
	} elseif($commit == 'on') {
		if ($blogid!=$params['blogid']) $params['blogid']=$blogid;
		if (!isset($params['blogid'])) $params['blogid'] = substr($params['params'],0,1);
		if (!isset($params['postid'])) $params['postid'] = 0;
		$blog = new PopnupBlog($params['blogid'],$params['postid']);
		$title = isset($_POST['title']) ? $_POST['title'] : '';
		if ($xoopsUser) $notifypub = isset($_POST['notifypub']) ? $_POST['notifypub'] : 0;
		if ($admin) $status = isset($_POST['status']) ? $_POST['status'] : 0;
		else $status = null;
		$text = get_uploadfile(isset($_POST['text_edit']) ? $_POST['text_edit'] : '');
		$ret = $blog->updateBlog($params['postid'],null, $text, $title,$params['blogid'],$uid,null,$status,$notifypub);
		if ( $ret==0 && $status==0){
			$new_user_notify = PopnupBlogUtils::getXoopsModuleConfig('new_user_notify');
			if ( $new_user_notify == 1 ) {
				$subj = sprintf(_MD_POPNUPBLOG_NEWWAITING,$xoopsConfig['sitename']);
				$msgs = sprintf(_MD_POPNUPBLOG_NEWWAITING_DESC,"\n\n".XOOPS_URL."/modules/popnupblog/admin/waiting.php");
				sendmail::notify($xoopsConfig['adminmail'],$xoopsUser->getVar("email"),$xoopsConfig['sitename'],$subj,$msgs);
			}
			if (is_object($xoopsUser)) $xoopsUser->incrementPost();
			redirect_header(PopnupBlogUtils::createUrl($params['blogid']),2,_MD_THANKS_POSTING);
		}elseif( isset($ret) ){
			redirect_header(PopnupBlogUtils::createUrlpostid($params['postid']),2,_MD_POPNUPBLOG_BLOG_UPDATE);
		}else{
			redirect_header(PopnupBlogUtils::createUrl($params['blogid']),2,_MD_POPNUPBLOG_INTERNALERROR.'(0.4)');
		}
		exit();
	}else{
		$ts =& MyTextSanitizer::getInstance();
		if ($params['blogid'] && $params['postid'])
			$blog = new PopnupBlog($params['blogid'],$params['postid']);
		else
			$blog = new PopnupBlog($blogid);
		$selectblog = "(".$blogid.") ";
		if (isset($MyBlogIDs) &&  count($MyBlogIDs)>1 && $today=='on' ){
			$selectblog .= PopnupBlogUtils::mkselect("blogid",$MyBlogIDs,$blogid);
		}else{
			$selectblog .= $blog->title . '<input type="hidden" name="blogid" value="'.$blogid.'" />'."\n";
		}
		if($preview == 'on'){
			$text_edit = get_uploadfile($_POST['text_edit']);
			//$text_edit = PopnupBlogUtils::phpbbsmiley($text_edit);
			$formValue['preview_text'] = sanitize_blog(nl2br($text_edit),true,false,true);
			$formValue['post_text'] = $ts->previewTarea($text_edit, 0, 1, 1, 1, 1);
			$formValue['text_edit'] = $ts->makeTboxData4PreviewInForm($text_edit);
			$formValue['title'] = $ts->makeTboxData4PreviewInForm($_POST['title']);
			$formValue['trackback'] = $_POST['trackback'];
			$xoopsTpl->assign('tplsig_edit_ispreview',TRUE);
			$xoopsTpl->assign('lang_edit_ref_subject',_MD_POPNUPBLOG_FORM_PREVIEW);
			$xoopsTpl->assign('pblog_edit_ref_message',$formValue['preview_text']);
			$spaw_text = $formValue['text_edit'];
		}else{
			// write blog
			if ((!(empty($params['year'])) && !($today=='on')) || $params['postid']){
				if (!$params['blogid'] && $params['postid']) $params['blogid']=PopnupBlogUtils::get_blogid_from_postid($params['postid']);
				$blogid = $params['blogid'];
				$result = $blog->getBlog1($params['postid']);
				if (!$blog->canWrite() and !$admin){
					redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_NORIGHTTOACCESS.'(1.3)');
					exit();
				}
				$formValue['post_text']  = array_key_exists('post_text', $result)  ? $result['post_text']  : "";
				$formValue['text_edit']  = array_key_exists('text_edit', $result)  ? $result['text_edit']  : "";
				$formValue['title'] = array_key_exists('title', $result) ? $result['title'] : "";
				$formValue['status']  = array_key_exists('status', $result)  ? $result['status']  : $bloginfo[$blogid]['default_status'];
				$formValue['notifypub']  = array_key_exists('notifypub', $result)  ? $result['notifypub']  : 0;
				$formValue['trackback'] = '';
			} else {
				$formValue['text_edit'] = '';
				$formValue['title'] = '';
				$formValue['trackback'] = '';
				$formValue['status']  = $bloginfo[$blogid]['default_status'];
				$formValue['notifypub']  = 0;
			}
			$spaw_text = $formValue['text_edit'];
		}
		if( check_browser_can_use_spaw() ) {
			$can_use_spaw = true ;
			if ($params['postid'])
				$submitlink_with_spaw = $_SERVER['PHP_SELF']. "?postid=".$params['postid']."&";
			else
				$submitlink_with_spaw = $_SERVER['PHP_SELF']. "?today=on&";
			$submitlink_with_spaw = "(<a href='".$submitlink_with_spaw."usespaw=1' style='font-size:xx-small;'>SPAW</a>)" ;
		} else {
			$can_use_spaw = false ;
			$submitlink_with_spaw = '' ;
		}
		$GLOBALS['text_edit'] = $formValue['text_edit'];
		$xoopsTpl->assign('lang_edit_pagetitle',_MD_SUBMIT_BLOG);
		$xoopsTpl->assign('lang_edit_editpost',_MD_SUBMIT_BLOG);
		$xoopsTpl->assign('pblog_edit_form_attrs', " method=\"post\" enctype=\"multipart/form-data\" action=\"edit.php\"");
		$xoopsTpl->assign('lang_select_blog', _MD_POPNUPBLOG_FORM_SELECTBLOG);
		$xoopsTpl->assign('pblog_edit_selectblog', $selectblog);
		$xoopsTpl->assign('lang_edit_subject', _MD_POPNUPBLOG_FORM_TITLE);
		$xoopsTpl->assign('pblog_edit_subject_input_attrs', " type='text' name='title' size='70' value='" . $formValue['title'] . "'");
		$xoopsTpl->assign('lang_edit_tb_title', "Trackback URL");
		$xoopsTpl->assign('pblog_edit_tb_input_attrs'," type='text' name='trackback' size='70' value='" . $formValue['trackback'] . "'");
		$xoopsTpl->assign('lang_edit_message', _MD_POPNUPBLOG_FORM_CONTENS);
		// for xoopsCodeTarea
		/*
		ob_start();
		xoopsCodeTarea("text_edit",65,15);
		$xoopsTpl->assign('pblog_edit_message', ob_get_contents());
		ob_end_clean();
		*/
		//
		$xoopsTpl->assign('pblog_edit_message',
			"<textarea id='markItUp' name='text_edit'>".$formValue['text_edit']."</textarea>");
		
		$xoopsTpl->assign("pblog_edit_postid", '<input type="hidden" name="postid" value="'.$params['postid'].'" />');
		//
		// File upload By Yoshi.Sakai, bluemooninc.biz 2004/3/14
		//
		if (is_object($xoopsUser)){
			if ($BlogCNF['maxbyte']>=1000000){
				$maxbyte_str = sprintf("%d M",$BlogCNF['maxbyte']/1000000);
			} elseif ($BlogCNF['maxbyte']>=1000){
				$maxbyte_str = sprintf("%d K",$BlogCNF['maxbyte']/1000);
			} else {
				$maxbyte_str = sprintf("%d ",$BlogCNF['maxbyte']);
			}
			$xoopsTpl->assign("lang_edit_attach", _MD_FILEUP_TITLE);
			$xoopsTpl->assign("pblog_edit_maxsize", "<input type='hidden' name='MAX_FILE_SIZE' value='".$maxbyte_str."' />");
			$xoopsTpl->assign("pblog_edit_attach", "<input type='file' size='50' name='upfile' /> <span>Max ".$maxbyte_str."B</span>");
		//	$xoopsTpl->assign("pblog_edit_uptypes", preg_replace("/\|/", "&nbsp;" , $xoopsModuleConfig['uploadtypes']) );  
		}
		// upload end.
		$xoopsTpl->assign("lang_edit_option", _OPTIONS );
		if ($admin) {
			$cbox = '<input type="checkbox" name="status"  value="1"';
			$cbox .= $formValue['status'] ? "checked" : "" ;
			$cbox .= ">"._MD_POPNUPBLOG_ML_APPROVE."<BR />";
			$xoopsTpl->assign("pblog_edit_option_approve",$cbox);
		}
		if ($xoopsUser) {
			$cbox = '<input type="checkbox" name="notifypub"  value="1"';
			$cbox .= $formValue['notifypub'] ? "checked" : "";
			$cbox .= ">"._MD_NOTIFYPUBLISH;
			$xoopsTpl->assign("pblog_edit_option_notify",$cbox);
		}
		if ( $admin || $xoopsUser->uid()==$blog->blogUid) {
			$xoopsTpl->assign("pblog_edit_delete", '<input type="submit" value="' . _MD_POPNUPBLOG_FORM_DELETE . '" name="delete"/>');
		}
		$xoopsTpl->assign("pblog_edit_preview", '<input type="submit" value="' . _MD_POPNUPBLOG_FORM_PREVIEW . '" name="preview"/>');
		$xoopsTpl->assign("pblog_edit_submit", '<input type="submit" value="' . _MD_POPNUPBLOG_FORM_SEND . '" name="commit"/>');
		// Delete TrackBack added by kazy 2006.11.18
		if($params['postid']){
		  $trackbacks = $blog->getTrackBack($params['postid']);
		  $count = 0;
		  if(count($trackbacks)>0){
			$xoopsTpl->assign("lang_edit_trackback", _MD_POPNUPBLOG_TRACKBACK );
		    foreach($trackbacks as $t){
		      if (strlen($t['title'])>60) $t['title'] = xoops_substr($t['title'],0,60);
		      $tbacks[] = '<input name="batch['.$count.']" id="batch'.$count++.'" type="checkbox" value="'.$t['tbid'].'"/> '
		        .$t['count'].' <a href="'.$t['url'].'" target="_blank">'.$t['title'].'</a>' ;
		    }
		    $xoopsTpl->assign("pblog_edit_trackbacks", $tbacks );
			$xoopsTpl->assign("pblog_edit_delete_tb",
				'<input type="submit" value="' . _MD_POPNUPBLOG_FORM_DELETE . '" name="delete_tb"/>');
		  }
		}
		$xoopsTpl->assign( 'xoops_module_header', 
		  '<link rel="stylesheet" type="text/css" media="screen,tv,print" href="style.css" />
		  <!-- jQuery -->
		  <script type="text/javascript" src="js/jquery-1.2.6.min.js"></script>
		  <!-- markItUp! -->
		  <script type="text/javascript" src="js/markitup/jquery.markitup.pack.js"></script>
		  <!-- markItUp! toolbar settings -->
		  <script type="text/javascript" src="js/markitup/sets/bbcode/set.js"></script>
		  <!-- markItUp! skin -->
		  <link rel="stylesheet" type="text/css" href="js/markitup/skins/simple/style.css" />
		  <!--  markItUp! toolbar skin -->
		  <link rel="stylesheet" type="text/css" href="js/markitup/sets/bbcode/style.css" />
		');
		$xoopsOption['template_main'] = 'popnupblog_submit.html';
	}
}


require('footer.php');
/*
** Get image type with binary check
*/
function getimagetype($data){
    if (strncmp("\x00\x00\x01\x00", $data, 4) == 0) {
        return "ico";
    } else if (strncmp("\x89PNG\x0d\x0a\x1a\x0a", $data, 8) == 0) {
        return "png";
    } else if (strncmp('BM', $data, 2) == 0) {
        return "bmp";
    } else if (strncmp('GIF87a', $data, 6) == 0 || strncmp('GIF89a', $data, 6) == 0) {
        return "gif";
    } else if (strncmp("\xff\xd8", $data, 2) == 0) {
        return "jpg";
    } else {
        return false;
    }
}
/*
** Get upload file by Yoshi.Sakai, bluemooninc.biz 2004/3/14
*/
function get_uploadfile($text) {
	global $xoopsUser,$BlogCNF;
	$addmsg = $text;
	$upfile        = $_FILES['upfile'];
	$upfile_tmp    = $_FILES['upfile']['tmp_name'];	// Temp File name
    $upfile_name  = basename($_FILES['upfile']['name']);	//Local File Name ( Use basename for security )
    $upfile_name  = (get_magic_quotes_gpc()) ? stripslashes($upfile_name) : $upfile_name;
	$upfile_size    = $_FILES['upfile']['size'];        // Size
	$upfile_type    = $_FILES['upfile']['type'];        // Type
	if ($upfile_tmp != "" && is_object($xoopsUser)){
		// Disp File Infomation for debug
		/*
		print("File Infomation:<BR>\n");
		print("File From : $upfile_tmp<BR>\n");
		print("File To : $upfile_name<BR>\n");
		print("File Size - $upfile_size<BR>\n");
		print("File type - $upfile_type<BR>\n");
		*/
		if (eregi($BlogCNF['imgtype'], $upfile_type)){
			$size = getimagesize($upfile_tmp);
			$type = getimagetype($upfile_tmp);
			if ( !$size || !strcmp($type,$upfile_type) ) return $addmsg."getimagesize or type error!";
		}
		if (eregi($BlogCNF['imgtype'].'|'.$BlogCNF['embedtype'], $upfile_type)){
			$upfile_localname = $xoopsUser->getVar('uname')."_".time()."_".$upfile_name;
			$upfile_url = XOOPS_URL.$BlogCNF['img_dir'].rawurlencode(mbstrings::internal2utf8($upfile_localname));
			$upfile_path = XOOPS_ROOT_PATH.$BlogCNF['img_dir'].mbstrings::cnv_mbstr($upfile_localname);
			move_uploaded_file($upfile_tmp,$upfile_path);
			//chmod($upfile_path,0644);
			// Thumbs Support ( PHP GD Libraly Required )
			if (eregi($BlogCNF['thumb_ext'],strtolower($upfile_localname))) {
				if ($size[0] > $BlogCNF['w'] || $size[1] > $BlogCNF['h']) {
					$thumb_localname = thumb_create($upfile_path,$BlogCNF['w'],$BlogCNF['h'],XOOPS_ROOT_PATH.$BlogCNF['thumb_dir']);
					$thumbfile_url = XOOPS_URL.$BlogCNF['thumb_dir'].rawurlencode(mbstrings::internal2utf8($thumb_localname));
					//chmod($thumbfile_path,0644);
					$addimg = "[url=".$upfile_url."][img align=left]".$thumbfile_url."[/img][/url]";
				} else {
					$addimg = "[img align=left]".$upfile_url."[/img]";
				}
			} elseif (eregi($BlogCNF['embedtype'], $upfile_type)){
				$addimg .= "\n<EMBED src=\"".$upfile_url."\" WIDTH=\"".$BlogCNF['w']."\" HEIGHT=\"".$BlogCNF['h'].
				"\" autostart=\"false\" controller=\"true\" hspace=\"5\" align=\"left\" alt=\"\"></EMBED>\n";
			} else {
				$addimg = "[img align=left]".$upfile_url."[/img]";
			}
			$addmsg = $addimg.$addmsg;
		} else {
			$upfile_localname=$xoopsUser->getVar('uname')."_".$upfile_name.".".time();
			$upfile_url="/".rawurlencode(mbstrings::internal2utf8($upfile_localname));	// XOOPS_UPLOAD_URL.
			$upfile_localname = mbstrings::cnv_mbstr($upfile_localname);		// convert for mbstrings
		   	move_uploaded_file($upfile_tmp, $BlogCNF['uploads'].$upfile_localname);
			//chmod($upfile_path,0644);
			$addmsg .= "\n:download:";
			$addmsg .= "[url=".$BlogCNF['root']."download.php?url=".$upfile_url."]".$upfile_name."[/url]\n";
		}
	}
	return $addmsg;
}

// checks browser compatibility with the control
function check_browser_can_use_spaw() {
	global $BlogCNF;
	if ($BlogCNF['use_spaw']==0) return false;
	$browser = $_SERVER['HTTP_USER_AGENT'] ;
	// check if msie
	if( eregi( "MSIE[^;]*" , $browser , $msie ) ) {
		// get version 
		if( eregi( "[0-9]+\.[0-9]+" , $msie[0] , $version ) ) {
			// check version
			if( (float)$version[0] >= 5.5 ) {
				// finally check if it's not opera impersonating ie
				if( ! eregi( "opera" , $browser ) ) {
					return true ;
				}
			}
		}
	}else
		return true ;
}
?>
