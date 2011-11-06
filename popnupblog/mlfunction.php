<?php
// $Id$
/*
** Copyright (C) 2006  Yoshi Sakai - http://www.bluemooninc.biz/
** This program is distributed under the terms and conditions of the GPL
** See http://www.gnu.org/copyleft/gpl.html for details
*/
require('header.php');
require_once('./class/users.php');
require_once('./class/emailalias.php');
require_once('./class/bloginfo.php');
require_once('./class/sendmail.php');

if(!xoops_refcheck()){
	redirect_header(XOOPS_URL.'/modules/popnupblog/',2,'Referer Check Failed');
	exit();
}
if(!$xoopsUser){
	redirect_header(XOOPS_URL.'/modules/popnupblog/',2,'Registered User Only');
	exit();
}
// Get blog IDs as displayed.
$blogids = isset($_POST['blogid']) ? $_POST['blogid'] : array();

// Get blog IDs as ML join checked.
$mlsets = isset($_POST['mlset']) ? $_POST['mlset'] : array();

// Make blog IDs as ML join unchecked.
$unsets = array_diff($blogids, $mlsets);

// Load blog IDs as registered.
$registered_bids = emailalias::registered_bid();

// Make blog IDs as insert.
$inserts = array_diff($mlsets,$registered_bids);

// Make blog IDs as delete.
$deletes = array_intersect($unsets,$registered_bids);

/* Debug for Blog IDs
echo "Request ML set on by User."; var_dump($mlsets); echo "<BR />";
echo "Request ML set off by User."; var_dump($unsets); echo "<BR />";
echo "Will SQL insert as Blog ID."; var_dump($inserts); echo "<BR />";
echo "Will SQL delete as Blog ID."; var_dump($deletes); echo "<BR />";
*/
$ins_titles = array();
$del_titles = array();
if ($inserts){
	foreach ( $inserts as $bid ) {
		emailalias::_add($bid,1,$xoopsUser->getVar("email"),$xoopsUser->uid());
		emailalias::_add($bid,2,$xoopsUser->getVar("email"),$xoopsUser->uid());
		$MLinfo = bloginfo::get_MLinfo($bid);
		$ins_titles[]=$MLinfo;
		$subj = _MD_POPNUPBLOG_ML_RECEPTION;
		$message = _MD_POPNUPBLOG_ML_APPLIED . ": " . _MD_POPNUPBLOG_ML_JOIN  . "\n" .
			_MD_POPNUPBLOG_TITLE . ": " . $MLinfo['title'] . "\n" . _MD_POPNUPBLOG_POSTMAILADDR . ": " . $MLinfo['pop_address'] . "\n";
		$blogurl = XOOPS_URL . '/modules/popnupblog/index.php?param=' . $bid;
		$ownername = users::uname($MLinfo['uid']);
		$ownermail = users::email($MLinfo['uid']);
		sendmail::send_ML($ownermail,$xoopsUser->getVar("email"),$ownername,0,$subj,$MLinfo['title'],$blogurl,$message,"",$MLinfo['pop_address']);
	}
}
if ($deletes){
	foreach ( $deletes as $bid ) {
		emailalias::_delete($bid,1,'',$xoopsUser->uid());
		emailalias::_delete($bid,2,'',$xoopsUser->uid());
		$MLinfo = bloginfo::get_MLinfo($bid);
		$del_titles[]=$MLinfo;
		$subj = _MD_POPNUPBLOG_ML_RECEPTION;
		$message = _MD_POPNUPBLOG_ML_APPLIED . ": " . _MD_POPNUPBLOG_ML_LEAVE  . "\n" .
			_MD_POPNUPBLOG_TITLE . ": " . $MLinfo['title'] . "\n" . _MD_POPNUPBLOG_POSTMAILADDR . ": " . $MLinfo['pop_address'] . "\n";
		$blogurl = XOOPS_URL . '/modules/popnupblog/index.php?param=' . $bid;
		$ownername = users::uname($MLinfo['uid']);
		$ownermail = users::email($MLinfo['uid']);
		sendmail::send_ML($ownermail,$xoopsUser->getVar("email"),$ownername,0,$subj,$MLinfo['title'],$blogurl,$message,"",$MLinfo['pop_address']);
	}
}
$xoopsTpl->assign('ins_titles',$ins_titles);
$xoopsTpl->assign('del_titles',$del_titles);
$xoopsOption['template_main'] = 'popnupblog_ml.html';
require('footer.php');
?>
