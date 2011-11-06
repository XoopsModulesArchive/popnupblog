<?php
// $Id$
require('header.php');
include_once XOOPS_ROOT_PATH."/include/xoopscodes.php";
include_once('./class/comment.php');
include_once('pop.ini.php');		// Load ini file
if(!xoops_refcheck()){
	redirect_header(XOOPS_URL.'/modules/popnupblog/',2,'Referer Check Failed');
	exit();
}
$commit = isset($_POST['commit']) ? 'on' : 'off';
$preview = isset($_POST['preview']) ? 'on' : 'off';
$delete = isset($_POST['delete']) ? 'on' : 'off';
/*	if(!$blog->canWrite()){
	if((!empty($xoopsModuleConfig['POPNUPBLOG_APPL'])) && ($xoopsModuleConfig['POPNUPBLOG_APPL'] == 1) ){
		redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_NORIGHTTOACCESS.'(1.2)');
		exit();
	}
	$xoopsOption['template_main'] = 'popnupblog_application.html';
}else{
*/
	$postid      = isset($_GET['postid'])       ? intval($_GET['postid']) : 0;
	$blogid      = isset($_POST['blogid'])      ? intval($_POST['blogid']) : 0;
	$comment_id  = isset($_POST['comment_id'])  ? intval($_POST['comment_id']) : 0;
	$comment_uid = isset($_POST['comment_uid']) ? intval($_POST['comment_uid']) : 0;
	$vote        = isset($_POST['vote'])        ? intval($_POST['vote']) : 0;
	$notifypub   = isset($_POST['notifypub'])   ? intval($_POST['notifypub']) : 0;
	if ($postid==0) $postid = isset($_POST['postid']) ? intval($_POST['postid']) : 0;
	if (!$comment_id) $comment_id = isset($_GET['comment_id']) ? intval($_GET['comment_id']) : 0;
	$comment_txt = isset($_POST['text']) ? $_POST['text'] : '';
	$comment_name = isset($_POST['comment_name']) ? $_POST['comment_name'] : "";

	if(!$blogid){
		if($postid) $blogid = PopnupBlogUtils::get_blogid_from_postid($postid);
		if($comment_id) $blogid = pb_comment::get_blogid_from_commentid($comment_id);
	}
	$ThisBlog = new PopnupBlog($blogid);
	if($comment_txt && !$ThisBlog->canComment($blogid)){
		redirect_header(XOOPS_URL.'/',3,_MD_POPNUPBLOG_NORIGHTTOACCESS.'(1)');
		exit();
	}
	if($vote && !$ThisBlog->canVote($blogid)){
		redirect_header(XOOPS_URL.'/',3,_MD_POPNUPBLOG_NORIGHTTOACCESS.'(1)');
		exit();
	}
	if(!$xoopsUser){
		if($comment_name == ''){
			$comment_name = _MD_POPNUPBLOG_FORM_ANONYMOUS_NAME;
		}elseif(strlen($comment_name) > 200){
			redirect_header(PopnupBlogUtils::createUrl($blogid), 3, _MD_POPNUPBLOG_COMMENT_NAME_TOO_LONG);
			exit();
		}
		$admin = null;
	}else{
		$admin = $xoopsUser->isAdmin();
	}
	if($delete == 'on'){
		if(pb_comment::updateComment($blogid,$postid,$comment_uid,$comment_name,$comment_id, null,0)){
			redirect_header(PopnupBlogUtils::createUrl($comment_uid),2,_MD_POPNUPBLOG_DELETE_COMMENT);
			exit();
		}
	} elseif($commit == 'on'){
		if ($admin) $status = array_key_exists('status', $_POST) ? $_POST['status'] : 0;
		else $status = null;
		if(!$comment_uid){
			$comment_uid = $xoopsUser ? $xoopsUser->uid() : 0;
			$status = $ThisBlog->default_status;
			$ret = pb_comment::insertComment($blogid,$postid,$comment_uid,$comment_name,$comment_txt,$vote,$status,$notifypub);
		}else{
			$ret = pb_comment::updateComment($blogid,$postid,$comment_uid,$comment_name,$comment_id, $comment_txt, $vote,$status,$notifypub);
		}
		if($ret){
			//
			// Sending Mails
			//
			if ( $ret==1 ){
				$ownersemail = users::email($ThisBlog->blogUid);
				$uname = users::uname($ThisBlog->blogUid);
				$blogurl = PopnupBlogUtils::createUrlpostid($postid);
				$blog = popnupblog::getBlog1($postid);
				$subject = "Re: " . $blog['title'];
				//sendmail::send_comment($ownersemail,$uname,$postid,$subject,$ThisBlog->title,$blogurl,$comment_txt,$comment_name);
				sendmail::send_mailalias($blogid,$blog['blog_count'],$ownersemail,$uname,$subject,$ThisBlog->title,$blogurl,$comment_txt,$ThisBlog->pop_address);
				sendmail::xoops_notify('new_comment',$blogid,$ThisBlog->title,$blogurl,$subject,$comment_txt);
				if( $notifypub==1 ){
					$subj = sprintf(_MD_POPNUPBLOG_NOTIFYPUB,$xoopsConfig['sitename']);
					$msgs = sprintf(_MD_POPNUPBLOG_NOTIFYPUB_DESC,"\n\n".$blogurl);
					$emailfrom = users::email($comment_uid);
					sendmail::notify($emailfrom,$xoopsConfig['adminmail'],$xoopsConfig['sitename'],$subj,$msgs);
				}
			}
			redirect_header(PopnupBlogUtils::createUrl($blogid),2,_MD_POPNUPBLOG_THANKS_COMMENT);
			exit();
		}else{
			//
			// Sending notify Mail to Admin
			//
			$new_user_notify = PopnupBlogUtils::getXoopsModuleConfig('new_user_notify');
			if ( $new_user_notify == 1 ) {
				$fromaddress = $xoopsUser ? $xoopsUser->getVar("email") : $xoopsConfig['adminmail'];
				$subj = sprintf(_MD_POPNUPBLOG_NEWWAITING,$xoopsConfig['sitename']);
				$msgs = sprintf(_MD_POPNUPBLOG_NEWWAITING_DESC,"\n\n".XOOPS_URL."/modules/popnupblog/admin/waiting.php");
				sendmail::notify($xoopsConfig['adminmail'],$fromaddress,$xoopsConfig['sitename'],$subj,$msgs);
			}
			redirect_header(PopnupBlogUtils::createUrl($blogid),2,_MD_THANKS_POSTING);
			exit();
		}
	}else{
		if($preview == 'on'){
			$ts =& MyTextSanitizer::getInstance();
			$formValue['text'] = $_POST['text'];
			$formValue['text'] = $ts->previewTarea($formValue['text'], 0, 1, 1, 1, 1);
			$formValue['text_edit'] = $_POST['text'];
			$formValue['text_edit'] = $ts->makeTboxData4PreviewInForm($formValue['text_edit']);
			echo "<p><table class='outer' cellspacing='1'>\n";
			echo "<tr><th>"._MD_POPNUPBLOG_FORM_PREVIEW."</th></tr>\n";
			echo '<tr class="even"><td><div class="comText">'.$formValue['text'].'</div></td></tr>';
			echo "</table></p>\n";
		}else{
			$result = pb_comment::getComment1($comment_id);
			if($result){
				$blogid  = array_key_exists('blogid', $result)  ? $result['blogid']  : 0;
				$comment_uid  = array_key_exists('comment_uid', $result)  ? $result['comment_uid']  : 0;
				$vote  = array_key_exists('vote', $result)  ? $result['vote']  : "";
				$status  = array_key_exists('status', $result)  ? $result['status']  : 0;
				$notifypub  = array_key_exists('notifypub', $result)  ? $result['notifypub']  : 0;
				$formValue['text']  = array_key_exists('text', $result)  ? $result['text']  : "";
				$formValue['text_edit']  = array_key_exists('text_edit', $result)  ? $result['text_edit']  : "";
			}
		}
		echo "<!-- begin of popnupblog edit form -->\n";
		echo "<table class='outer' cellspacing='1'>\n";
		echo '<form method="post" action="'.XOOPS_URL.'/modules/popnupblog/commentedit.php">'."\n";
		echo '<tr><td align="left" class="head">'._MD_POPNUPBLOG_FORM_COMMENT."</td>\n";
		echo '<td class="even" ><CENTER>';
        if (!$xoopsUser){
            echo '<input type="text" name="comment_name" value="" size="20" />@'._MD_POPNUPBLOG_FORM_GUEST.'&nbsp';
        }else{
	        echo '<input type="hidden" name="comment_name" value="'.$xoopsUser->uname().'" />';
        }
		echo '<textarea name="text" cols="60" rows="10">'.$formValue['text_edit'].'</textarea>';
/*
		echo '<BR />'._MD_POPNUPBLOG_FORM_VOTE.":";
		switch ($vote){
			case 0: 
				$ychk= ""; $ymsg=_MD_POPNUPBLOG_FORM_YES;
				$nchk= ""; $nmsg=_MD_POPNUPBLOG_FORM_NO;
				$yval=1; $nval=-1;
				break;
			case 1: 
				$ychk= " CHECKED"; $ymsg=_MD_POPNUPBLOG_FORM_YES;
				$nchk= ""; $nmsg=_MD_POPNUPBLOG_FORM_CANCEL;
				$yval=1; $nval=0;
				break;
			case -1: 
				$ychk= ""; $ymsg=_MD_POPNUPBLOG_FORM_CANCEL;
				$nchk= " CHECKED"; $nmsg=_MD_POPNUPBLOG_FORM_NO;
				$yval=0; $nval=-1;
				break;
		}
		echo '<input type="radio" name="vote" value='.$yval.' '.$ychk.'>'.$ymsg.'</input>';
		echo '<input type="radio" name="vote" value='.$nval.' '.$nchk.'>'.$nmsg.'</input>';
*/
		echo '<input type="hidden" name="comment_id" value='.$comment_id.' />'."\n";
		echo '<input type="hidden" name="comment_uid" value='.$comment_uid.' />'."\n";
		echo '<input type="hidden" name="blogid" value='.$blogid.' />'."\n";
		echo '<input type="hidden" name="postid" value='.$postid.' />'."\n";
//			echo '<input type="hidden" name="date" value='.$date.' />'."\n";
//			echo '<input type="hidden" name="name" value='.$name.' />'."\n";
		echo '</CENTER></td></tr>';
		echo "<tr><td class='head' valign='top' nowrap='nowrap'>". _OPTIONS . "</td>\n<TD class='even'>";
		if ($admin) {
			echo '<input type="checkbox" name="status" value="1"';
			if ($status) echo "checked";
			echo " />"._MD_POPNUPBLOG_ML_APPROVE."<BR />";
		}
		if ($xoopsUser) {
			echo '<input type="checkbox" name="notifypub"  value="1"';
			if ($notifypub) echo "checked";
			echo ">"._MD_NOTIFYPUBLISH;
		}
		echo "</td></tr>";
		echo '<tr><td align="center" colspan="2" class="odd">
			<input type="submit" value="'._MD_POPNUPBLOG_FORM_DELETE.'" name="delete"/> &nbsp 
			<input type="submit" value="'._MD_POPNUPBLOG_FORM_PREVIEW.'" name="preview"/> &nbsp 
			<input type="submit" value="'._MD_POPNUPBLOG_FORM_SEND.'" name="commit"/>';
		echo "\n";
		echo "<!-- begin of popnupblog edit form -->\n";
		echo "</td></tr></form>\n";
		echo "</table>\n";
	}
//}
require('footer.php');
?>
