<?php
// $Id: application.php,v 2.51 2006/05/25 19:13:08 yoshis Exp $
require('header.php');
//global $xoopsConfigUser;	
	if(!$xoopsUser){
		redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_NORIGHTTOACCESS.'(1.1)');
		exit();
	}
	if(!xoops_refcheck()){
		redirect_header(XOOPS_URL.'/modules/popnupblog/',2,'Referer Check Failed');
		exit();
	}
	if((!empty($xoopsModuleConfig['POPNUPBLOG_APPL'])) && ($xoopsModuleConfig['POPNUPBLOG_APPL'] == 1) ){
		redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_NORIGHTTOACCESS.'(1.2)');
		exit();
	}
	$uname = $xoopsUser->getVar('uname');
	$group_post    = grp_saveAccess( isset($_POST['g_post'])    ? $_POST['g_post']    : "" );
	$group_read    = grp_saveAccess( isset($_POST['g_read'])    ? $_POST['g_read']    : "" );
	$group_comment = grp_saveAccess( isset($_POST['g_comment']) ? $_POST['g_comment'] : "" );
	$group_vote    = grp_saveAccess( isset($_POST['g_vote'])    ? $_POST['g_vote']    : "" );
	$result = PopnupBlogUtils::newApplication($_POST['title'], $_POST['desc'], $group_read,$group_comment,$group_vote,$group_post,$_POST['cat_id'],$_POST['email'],$_POST['emailalias']);
	if($result == ''){
		$new_user_notify = PopnupBlogUtils::getXoopsModuleConfig('new_user_notify');
		if ( $new_user_notify == 1 ) {
			$myts =& MyTextSanitizer::getInstance();
			$subj = sprintf(_MD_POPNUPBLOG_NEWUSERREGAT,$xoopsConfig['sitename']);
			$msgs = sprintf(_MD_POPNUPBLOG_HASJUSTREG,$myts->oopsStripSlashesGPC($uname));
			sendmail::notify($xoopsConfig['adminmail'],$xoopsUser->getVar("email"),$xoopsConfig['sitename'],$subj,$msgs);
		}
		redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_APPLICATION_APPLIED);
		exit();
	}else{
		redirect_header(XOOPS_URL.'/', 2, $result);
		exit();
	}
require('footer.php');
?>
