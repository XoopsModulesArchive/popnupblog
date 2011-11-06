<?php
// $Id$
require('header.php');
	if(!xoops_refcheck()){
		redirect_header(XOOPS_URL.'/modules/popnupblog/',2,'Referer Check Failed');
		exit();
	}
	$params = PopnupBlogUtils::getDateFromHttpParams();
	$comment = isset($_POST['comment']) ? $_POST['comment'] : '';
	$vote = isset($_POST['vote']) ? $_POST['vote'] : '';
	$commentersname = isset($_POST['commentersname']) ? $_POST['commentersname'] : "";
	if($params['blogid'] <= 0){
		redirect_header(XOOPS_URL.'/',3,_MD_POPNUPBLOG_NORIGHTTOACCESS.'(0)');
		exit();
	}elseif($comment == '' && $vote == '' ){
		redirect_header(PopnupBlogUtils::createUrl($params['blogid']),3,_MD_POPNUPBLOG_COMMENT_NO_COMMENT);
		exit();
	}
	if(!$xoopsUser){
		if($commentersname == ''){
			$commentersname = _MD_POPNUPBLOG_FORM_ANONYMOUS_NAME;
		}elseif(strlen($commentersname) > 200){
			redirect_header(PopnupBlogUtils::createUrl($params['blogid']), 3, _MD_POPNUPBLOG_COMMENT_NAME_TOO_LONG);
			exit();
		}
	}else{
		$commentersname=$xoopsUser->uname();
	}
	$ThisBlog = new PopnupBlog($params['blogid']);
	$dates = $params;
	if(!$dates || !PopnupBlogUtils::isCompleteDate($dates)){
		redirect_header(XOOPS_URL.'/',3,_MD_POPNUPBLOG_INVALID_DATE.'(1.0)');
		exit();
	}
	
	if($comment && !$ThisBlog->canComment($params['blogid'])){
		redirect_header(XOOPS_URL.'/',3,_MD_POPNUPBLOG_NORIGHTTOACCESS.'(1)');
		exit();
	}
	if($vote && !$ThisBlog->canVote($params['blogid'])){
		redirect_header(XOOPS_URL.'/',3,_MD_POPNUPBLOG_NORIGHTTOACCESS.'(1)');
		exit();
	}
	//$comment = str_replace("\n", "<br />\n", $comment);
	$uid = $xoopsUser ? $xoopsUser->uid() : 0;
	$status = 1;
	$ret = pb_comment::insertComment($ThisBlog->blogid,$params['postid'],$uid,$commentersname,$comment,$vote,$status);
	if($ret){
		redirect_header(PopnupBlogUtils::createUrl($params['blogid']) ,2,_MD_POPNUPBLOG_THANKS_VOTE);
		$new_user_notify = PopnupBlogUtils::getXoopsModuleConfig('new_user_notify');
		if ( $new_user_notify == 1 ) {
			$fromaddress = $xoopsUser ? $xoopsUser->getVar("email") : $xoopsConfig['adminmail'];
			$subj = sprintf(_MD_POPNUPBLOG_NEWWAITING,$xoopsConfig['sitename']);
			$msgs = sprintf(_MD_POPNUPBLOG_NEWWAITING_DESC,"\n\n".XOOPS_URL."/modules/popnupblog/admin/waiting.php");
			sendmail::notify($xoopsConfig['adminmail'],$fromaddress,$xoopsConfig['sitename'],$subj,$msgs);
		}
		redirect_header(PopnupBlogUtils::createUrl($params['blogid']) ,2,_MD_THANKS_POSTING);
		exit();
	}else{
		redirect_header(PopnupBlogUtils::createUrl($params['blogid']) ,2,_MD_POPNUPBLOG_NODUPLICATIONVOTE);
		exit();
	}
require('footer.php');
?>
