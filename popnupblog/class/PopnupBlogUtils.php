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
	!is_file($incpath.'conf.php') ||
	!is_file($incpath.'pop.ini.php') ||
	!is_file($incpath.'class/PopnupBlogPing2.php') 
){
	exit();
}
include_once $incpath.'pop.ini.php';
include_once $incpath.'conf.php';
include_once $incpath.'class/users.php';
include_once $incpath.'class/PopnupBlogPing2.php';
include_once $incpath.'class/bloginfo.php';
include_once $incpath.'class/sendmail.php';
include_once $incpath.'class/emailalias.php';
include_once $incpath.'class/category.php';
include_once $incpath.'class/comment.php';
include_once $incpath.'class/log.php';
include_once $incpath.'include/groupaccess.php';
include_once $incpath.'include/sanitize.php';
//include_once $incpath.'include/mb_wordwrap.php';
require_once(XOOPS_ROOT_PATH.'/mainfile.php');
require_once(XOOPS_ROOT_PATH.'/kernel/user.php');

class PopnupBlogUtils {
	
	function getStartFromHttpParams(){
		global $_POST, $_GET, $xoopsUser;
		
		$start = isset($_POST['start']) ? ($_POST['start']) : 0;
		if($start == 0){
			$start = isset($_GET['start']) ? ($_GET['start']) : 0;
		}
		return $start;
	}
	function getDateFromHttpParams(){
		global $_SERVER,$_POST, $_GET, $xoopsUser;
		
		$param = isset($_POST['param']) ? trim( htmlspecialchars( $_POST['param'], ENT_QUOTES)) : NULL;
		if (!$param) $param = isset($_GET['param']) ?  htmlspecialchars($_GET['param'], ENT_QUOTES) : NULL;
		$postid = isset($_POST['postid']) ? intval($_POST['postid']) : 0;
		if (!$postid) $postid = isset($_GET['postid']) ? intval($_GET['postid']) : 0;
		$trackback = isset($_GET['trackback']) ? htmlspecialchars( $_GET['trackback'], ENT_QUOTES) : NULL;

		if (!$param && !$postid){
	    	$path_info = isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : null;
	    	if(!$path_info) $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;
	    	if ( isset($path_info) ){
	    		$pa = explode( "/" , $path_info );
				$postid = intval($pa[count($pa)-1]);
			}
		}
		if(!$param && !$postid){
			return false;
		}
		$result = array();
		$result['trackback'] = $trackback;
		$result['year'] = $result['month'] = $result['date'] =
			$result['hours'] = $result['minutes'] = $result['seconds'] = 0;
			$result['command'] = "";
		if ($xoopsUser) $result['uid'] = $xoopsUser->uid();
		$result['blogid'] = $param;
		if (isset($_GET['vote'])) $result['vote'] = $_GET['vote'];
		if(preg_match("/^([0-9]+)-([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})-([a-zA-Z0-9]*)/", $param, $m)){
			$result['blogid'] = PopnupBlogUtils::checkUid($m[1]);
			$result['year'] = PopnupBlogUtils::checkYear($m[2]);
			$result['month'] = PopnupBlogUtils::checkMonth($m[3]);
			$result['date'] = PopnupBlogUtils::checkDate($m[2], $m[3], $m[4]);
			$result['hours']=$m[5];
			$result['minutes']=$m[6];
			$result['seconds']=$m[7];
			$result['command'] = trim($m[8]);		// enc type for MT user
		}elseif(preg_match("/^([0-9]+)-([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", $param, $m)){
			$result['blogid'] = PopnupBlogUtils::checkUid($m[1]);
			$result['year'] = PopnupBlogUtils::checkYear($m[2]);
			$result['month'] = PopnupBlogUtils::checkMonth($m[3]);
			$result['date'] = PopnupBlogUtils::checkDate($m[2], $m[3], $m[4]);
			//print("$m[5]:$m[6]:$m[7]");
			$result['hours']=$m[5];
			$result['minutes']=$m[6];
			$result['seconds']=$m[7];
		}elseif(preg_match("/^([0-9]+)-([0-9]{4})([0-9]{2})([0-9]{2})/", $param, $m)){
			$result['blogid'] = PopnupBlogUtils::checkUid($m[1]);
			$result['year'] = PopnupBlogUtils::checkYear($m[2]);
			$result['month'] = PopnupBlogUtils::checkMonth($m[3]);
			$result['date'] = PopnupBlogUtils::checkDate($m[2], $m[3], $m[4]);
		}elseif(preg_match("/^([0-9]+)-([0-9]{4})([0-9]{2})/", $param, $m)){
			$result['blogid'] = PopnupBlogUtils::checkUid($m[1]);
			$result['year'] = PopnupBlogUtils::checkYear($m[2]);
			$result['month'] = PopnupBlogUtils::checkMonth($m[3]);
		}elseif(preg_match("/^([0-9]+)/", $param, $m)){
			$result['blogid'] = PopnupBlogUtils::checkUid($m[1]);
		}elseif(!$postid){
			redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_INVALID_DATE.'(INVALID PARAM)');
			exit();
		}
		//
		//	modified by hoshiyan@hoshiba-farm.com    2004.7.16
		//
		if (isset($_GET['today'])) {
			$today = array();
			$today['user_time'] = xoops_getUserTimestamp(time());
			if (is_object($xoopsUser)) {
				if ($_GET['today'] == 'on') {
					$MyBlogIDs = bloginfo::get_blogid_from_uid($xoopsUser->uid());
					$today['blogid'] = $MyBlogIDs[0]['blogid'];
				}else{
					$today['blogid'] = intval($_GET['today']);
				}
			}else{
				$today['blogid'] = 0;
			}
			$today['year'] = strftime("%Y", $today['user_time']);
			$today['month'] = strftime("%m", $today['user_time']);
			$today['date'] = strftime("%d", $today['user_time']);
			$cmd = 0;
			$param = PopnupBlogUtils::makeParams($today['blogid'], $today['year'],$today['month'],$today['date'], $cmd );
		} else {
			$param = PopnupBlogUtils::makeParams($result['blogid'], $result['year'],$result['month'],$result['date'], $result['command']);
		}
		$result['postid'] = $postid;
		$result['params'] = $param;
		return $result;
	}
	function getWaitingsCount($status=0){
		global $xoopsDB;
		$ret=0;
		if($dbResult = $xoopsDB->query("SELECT count(*) as cpt FROM ".PBTBL_BLOG." WHERE status=0")){
			if(list($num) = $xoopsDB->fetchRow($dbResult)) $ret = $num;
		}
		if($dbResult = $xoopsDB->query("SELECT count(*) as cpt FROM ".PBTBL_COMMENT." WHERE status=0")){
			if(list($num) = $xoopsDB->fetchRow($dbResult)) $ret += $num;
		}
		return $ret;
	}
	function getApplicationNum(){
		global $xoopsDB;
		if(!$dbResult = $xoopsDB->query('select count(*) num from '.PBTBL_APPL)){
			return 0;
		}
		if(list($num) = $xoopsDB->fetchRow($dbResult)){
			return $num;
		}
		return 0;
	}
	function weblogUpdatesPing($rss, $url, $blog_name = null, $title = null, $excerpt = null){
		$ping = new PopnupBlogPing2($rss, $url, $blog_name, $title, $excerpt);
		$ping->send();
		/* debug log
		ob_start();
		print_r($ping);
		$oblog = ob_get_contents();
		ob_end_clean();
		log::addlog($oblog);
		*/
	}
	function newApplication($in_title, $in_desc, $in_read,$in_comment,$in_vote, $in_gpost, $in_cid, $in_email, $in_emailalias){
		global $xoopsUser, $xoopsDB, $xoopsConfig, $BlogCNF;
		$myts =& MyTextSanitizer::getInstance();
		$in_title      = $myts->addSlashes( $myts->censorString( $in_title      ) );
		$in_desc       = $myts->addSlashes( $myts->censorString( $in_desc       ) );
		$in_email      = $myts->addSlashes( $myts->censorString( $in_email      ) );
		$in_emailalias = $myts->addSlashes( $myts->censorString( $in_emailalias ) );

		$in_cid = intval($in_cid);
		$title = "";
		$desc = "";
		if(!empty($in_title)){
			$title = PopnupBlogUtils::convert2sqlString($in_title);
		}
		if(!empty($in_desc)){
			$desc = PopnupBlogUtils::convert2sqlString($in_desc);
		}
		if(!$result = $xoopsDB->query('select uid from '.PBTBL_APPL.' where uid = '.$xoopsUser->uid())){
			return "select error";
		}
		if(list($tmpUid) = $xoopsDB->fetchRow($result)){
			return _MD_POPNUPBLOG_ERR_APPLICATION_ALREADY_APPLIED;
		}
		if(!$result = $xoopsDB->query('select blogid from '.PBTBL_INFO.' where title=\''.$in_title.'\' limit 1')){
			return "select error";
		}
		if(list($blogid) = $xoopsDB->fetchRow($result)){
			return _MD_POPNUPBLOG_ERR_APPLICATION_ALREADY_TITLED;
		}
		if(!$result = $xoopsDB->query('select count(*) from '.PBTBL_INFO.' where uid = '.$xoopsUser->uid())){
			return "select error";
		}
		if(list($num) = $xoopsDB->fetchRow($result)){
			if( $num > $BlogCNF['maxuserblogs'] )
				return _MD_POPNUPBLOG_ERR_MAXBLOGS;
		}
		$sql = sprintf("INSERT INTO %s (uid,group_post,cat_id,title,blog_desc,group_read,group_comment,group_vote,create_date,email,emailalias) values(%u, '%s', %u, '%s', '%s','%s','%s','%s', CURRENT_TIMESTAMP(), '%s', '%s')", 
			PBTBL_APPL, $xoopsUser->uid(),$in_gpost,$in_cid,$title,$desc,$in_read,$in_comment,$in_vote,$in_email,$in_emailalias);
		if(!$result = $xoopsDB->query($sql)){
			return "insert error";
		}
		$email = users::getEmailByUid($xoopsUser->uid());
		if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $email)) { 
			$email="";
			echo "<font color=\"red\">email address error</font>"; 
		}
		$admin_approve = PopnupBlogUtils::getXoopsModuleConfig('activation_type');
		if ($admin_approve==0){
			if(bloginfo::createNewBlogUser($xoopsUser->uid(),$in_gpost,$in_cid,$in_read,$in_comment,$in_vote,$title,$desc,$email) ){
				$msg = "Succeed: ";
			}else{
				$msg = "Failed: ";
			}
			$msg .= "Create blog uid=".$xoopsUser->uid()." title=".$title." email=".$email;
			if ($email) mail($email,"Create new blog",$msg);
		}
		return "";
	}
	function deleteApplication($uid){
		global $xoopsDB;
		$uid = intval($uid);
		if($uid > 0){
			 $xoopsDB->queryF(sprintf('delete from %s where uid = %u', PBTBL_APPL, $uid));
		}
	}
	function is_tzoffset(){
		global $xoopsConfig, $xoopsUser;
		if ( $xoopsUser ) {
			$user_TZ = $xoopsUser->timezone();
		} else {
			$user_TZ = $xoopsConfig['default_TZ'];
		}
		$tzoffset = ( $user_TZ - $xoopsConfig['server_TZ'] ) * 3600 ;
		return $tzoffset;
	}
	function get_blog_list($start = 0, $cat_id=0, $select_uid=0,$showNums=0,$showDays=0){
		global $xoopsConfig, $xoopsUser, $xoopsDB, $BlogCNF;

		$useRerite = PopnupBlogUtils::getXoopsModuleConfig('POPNUPBLOG_REWRITE');
		$categories = category::get_categories();
		$block_list_num = POPNUPBLOG_BLOCK_LIST_NUM;
		$dateFormat = '%m/%d %k:%i';

		$uid=0;
		if ( $xoopsUser ) {
			$uid = $xoopsUser->uid();
		}
		if (!$showNums) $showNums = POPNUPBLOG_BLOCK_LIST_NUM;
		if (!$start) $selectMax = "0," . $showNums;											//POPNUPBLOG_BLOCK_LIST_NUM ;
		else $selectMax = intval($start). "," . $showNums;									//POPNUPBLOG_BLOCK_LIST_NUM ;
		if ($showDays)
			$lastdays = 'last_update > CURRENT_TIMESTAMP() - interval ' . $showDays .' day '; 	// POPNUPBLOG_BLOCK_LASTDAYS
		else
			$lastdays = 'last_update >0';
		$wstr='';
		if($cat_id>0) $wstr = " and cat_id=".$cat_id;
		if($select_uid>0) $wstr .= " and uid=".$select_uid;
		$wstr .= PopnupBlogUtils::isGroupAndPrivate();
		$sql_select = sprintf('select uid, blogid,cat_id,group_read,last_update,title,blog_desc,ml_function FROM %s '.
			'WHERE ' . $lastdays . $wstr.' ORDER BY last_update desc limit %s',
			 PBTBL_INFO, $selectMax);
		if(!$result_select = $xoopsDB->query($sql_select)){
			return false;
		}
		$rblog = array();
		$rcomment = array();
		$tmp = array();
		$i = 0;
		$reccount=0;
		$ts =& MyTextSanitizer::getInstance();
		$ugroup_post = array();
		$pbids = array();
		if ($xoopsUser){
			$ugroup_post = $xoopsUser->getGroups();
			$pbids = PopnupBlogUtils::your_private();
		} else {
			$ugroup_post[] = 3;
			$pbids[] = 3;
		}
		$tzoffset = PopnupBlogUtils::is_tzoffset();
		while(list($result_uid,$result_bid,$result_cid,$group_read,$last_update,$title,$desc,$ml_function)=$xoopsDB->fetchRow($result_select)){
			//echo " blogid(".$result_bid.")";
			//print_r($ugroup_post);echo " - ".$group_read;	// for group_read
			//print_r($pbids);	// for private blog
			$group_postarray = explode(" ", $group_read);
			if (array_intersect($group_postarray,$ugroup_post) || $result_uid==$uid || in_array($result_bid,$pbids)){
					$res = array();
					$res['bid'] = $result_bid;
					$res['uid'] = $result_uid;
					if ($result_cid && isset($categories[$result_cid])) $res['catname'] = $categories[$result_cid];
					$upd = strtotime($last_update) + $tzoffset;
					$res['last_update'] = $upd;
					$res['last_update_s'] = formatTimestamp($upd, 's');
					$res['last_update_m'] = formatTimestamp($upd, 'm');
					$res['last_update_l'] = formatTimestamp($upd, 'l');
					$res['title'] = $title;
					$res['desc'] = $ts->makeTareaData4Show($desc);
					$res['ml_function'] = $ml_function;
					if ($ml_function) $res['ml_checked'] = emailalias::MyChecked($uid,$result_bid);
					$rblog = PopnupBlogUtils::get_RecentlyBlog($result_bid);
					if($rblog){
						$res['last_title'] = $rblog['title'];
						$post_text = $rblog['post_text'];
						if ($GLOBALS['BlogCNF']['blockview']==1){
							if ($GLOBALS['BlogCNF']['text_limit']>0)
								$post_text = mbstrings::_strcut($post_text,0,$GLOBALS['BlogCNF']['text_limit']);
						}
						$post_text = sanitize_blog($post_text,true,false,true);
						$res['last_text']=$post_text;
						$res['url'] = PopnupBlogUtils::createUrl($result_bid);
						/*
						$rcomment = pb_comment::get_RecentlyComment($result_bid);
						if($rcomment){
							$res['commentuname'] = $rcomment['uname'].": ";
							$res['comment'] = $rcomment['comment'];
						}
						*/
					}
					//$res['last_title'] =mb_strimwidth($res['last_title'],0,$BlogCNF['wordwrap_width_title'],"...");
					//$res['last_text'] = mb_wordwrap($res['last_text'],$BlogCNF['wordwrap_width_contents'],"<BR />");
					$tmp[$i] = $res;
				$i++;
				if ($i>=POPNUPBLOG_BLOCK_LIST_NUM) break;
			}
			$reccount++;
		}
		$block = array();
		$userHander = new XoopsUserHandler($xoopsDB);
		$i = 0;
		foreach ( $tmp as $target ) {
			$target['uname']='';
			$tUser = $userHander->get($target['uid']);
			$show_name = PopnupBlogUtils::getXoopsModuleConfig('show_name');
			if ( $show_name==1 && (trim(users::realname($target['uid']))!='') )
				$target['uname'] = users::realname($target['uid']);
			else
				if($tUser) $target['uname'] = $tUser->uname();
			$target['last_update4rss'] =  PopnupBlogUtils::toRssDate($target['last_update']);
			$block[$i] = $target;
			$i++;
		
		}
		return $block;
	}

	function getBlogInfo( $uid=0 ){
		global $xoopsDB;

		//$myts =& MyTextSanitizer::getInstance();
		$member_handler =& xoops_gethandler('member');
		$groupnames = $member_handler->getGroupList(); 
		$categories = category::get_categories();
		$i = 0;
		$users = array();
		$wstr='';
		if ($uid>0) $wstr = " Where uid=".$uid;
		$sql = 'SELECT uid, blogid, group_post, cat_id, group_read, group_comment, group_vote, DATE_FORMAT(last_update, \'%Y-%m-%d\') last_update, title, blog_desc, email, plugin, ml_function, pop_server, pop_user, pop_password, pop_address,default_status FROM '
			.PBTBL_INFO.$wstr.' order by uid, blogid';
		$result = $xoopsDB->query($sql);
		while( list($uid,$blogid,$group_post,$cat_id,$group_read,$group_comment,$group_vote,$lastUpdate,$blogTitle,$blogdesc,$blogemail,
			$plugin,$ml_function,$pop_server,$pop_user,$pop_password,$pop_address,$default_status) = $xoopsDB->fetchRow($result) ){
			$users[$i]['uid'] = $uid;
			$users[$i]['bid'] = $blogid;
			$users[$i]['cat_id'] = $cat_id;
			$users[$i]['cidselect'] = PopnupBlogUtils::mkselect('cat_id',$categories, $cat_id);
			$users[$i]['g_post'] = grp_listGroups($group_post,'g_post[]');
			$users[$i]['g_read'] = grp_listGroups($group_read,'g_read[]');
			$users[$i]['g_comment'] = grp_listGroups($group_comment,'g_comment[]');
			$users[$i]['g_vote'] = grp_listGroups($group_vote,'g_vote[]');
			$users[$i]['lastUpdate'] = $lastUpdate;
			$users[$i]['title'] = $blogTitle;	//$myts->makeTareaData4Edit();
			$users[$i]['desc'] = $blogdesc;	//$myts->makeTareaData4Edit();
			$users[$i]['email'] = $blogemail;
			$users[$i]['plugin'] = $plugin;
			$users[$i]['ml_function'] = $ml_function;
			if ($ml_function) $users[$i]['ml_checked'] = emailalias::MyChecked(0,$blogid);
			$users[$i]['pop_server'] = $pop_server;
			$users[$i]['pop_user'] = $pop_user;
			$users[$i]['pop_password'] = $pop_password;
			$users[$i]['pop_address'] = $pop_address;
			$users[$i]['default_status'] = $default_status;
			//$users[$i]['emailalias'] = array;          // Modified by hoshiyan@hoshiba-farm.com 2004.8.4
			$s = "SELECT email,uid FROM ".PBTBL_EMAILALIAS." WHERE blogid = $blogid and public = 1";
			$r = $xoopsDB->query($s);
			if ($r){	//$fa = $xoopsDB->fetchArray($r);
				$users[$i]['emailalias'] = $r;		//$fa['email'];
				$users[$i]['emailalias_options'] = PopnupBlogUtils::b_mailalias_show($users[$i]['emailalias']);
			}
			$s = "SELECT email,uid FROM ".PBTBL_EMAILALIAS." WHERE blogid = $blogid and public = 2";
			$r = $xoopsDB->query($s);
			if ($r){
				$users[$i]['emailsends'] = $r;
				$users[$i]['emailsends_options'] = PopnupBlogUtils::b_mailalias_show($users[$i]['emailsends']);
			 }
			//print_r ($users[$i]);
			$i++;
		}
		return $users;
	}
	function b_mailalias_show($options){
		global $xoopsDB;
		$unames = users::get_unames();
		$email_options = '';
		while ($row = $xoopsDB->fetchArray($options)){
			$email_options .= '<option value="'.$row['email'].'">';
			$email_options .= isset($unames[$row['uid']]) ? $unames[$row['uid']] : "";
			$email_options .= '&lt;'.$row['email'].'&gt;</option>';
		}
		return $email_options;
	}

	/* {{{ proto string mkselect(string name, array options)
	   Returns HTML format for a select box (dropdown). */
	function mkselect ($_name, $options, $varr = null) {
		$myts = MyTextSanitizer::getInstance();
	    if ($varr == null) $varr =& $_POST;
		$str  = "<select name=\"${_name}\">\n";
		$str .= "<option></option>\n";
		while(list($cid, $content) = each($options)) {
			$checked = '';
			$cid = is_array($content) ? $content['blogid'] : $cid;
			$title = is_array($content) ? $content['title'] : $content;
			$title = $myts->makeTboxData4Show($title);
			if (isset($varr[$_name]) && $varr[$_name] == $cid)
				$checked = ' selected';
			if (intval($varr) == $cid)
				$checked = ' selected';
			$str .= "<option value=\"${cid}\"${checked}>${title}</option>\n";
		}
		$str .= "</select>\n";
		return($str);
	}
	/* }}} */
	
	function getXoopsModuleConfig($key){
		global $xoopsDB;
		$mid = -1;

		$sql = "SELECT mid FROM ".$xoopsDB->prefix('modules')." WHERE dirname = 'popnupblog'";
		if (!$result = $xoopsDB->query($sql)) {
			return false;
		}
		$numrows = $xoopsDB->getRowsNum($result);
		if ($numrows == 1) {
			list($l_mid) = $xoopsDB->fetchRow($result);
			$mid = $l_mid;
		}else{
			return false;
		}
		$sql = "select conf_value from ".$xoopsDB->prefix('config')." where conf_modid = ".$mid." and conf_name = '".trim($key)."'";
		if (!$result = $xoopsDB->query($sql)) {
			return false;
		}
		$numrows = $xoopsDB->getRowsNum($result);
		if ($numrows == 1) {
			list($value) = $xoopsDB->fetchRow($result);
			//return intval($value);
			return $value;
		}else{
			return false;
		}
	}

	function get_RecentlyBlog($bid){
		global $xoopsUser, $xoopsDB;
		$sql = 'select blog_date,title,post_text FROM '.PBTBL_BLOG.' WHERE status>0 and blogid = '.$bid.' order by blog_date desc limit 1;';
		if(!$result_select = $xoopsDB->query($sql)){
			return false;
		}
		$result = array();
		if(list($blog_date,$title,$post_text) = $xoopsDB->fetchRow($result_select)){
			$result['blog_date'] = $blog_date;
			$result['title'] = $title;
			$result['post_text'] = $post_text;
		}
		return $result;
	}	
	function your_private(){
		global $xoopsUser, $xoopsDB;
		if ( $xoopsUser ) {
			$sql = 'select blogid from '.PBTBL_EMAILALIAS.' where uid = '.$xoopsUser->uid();
			if($result = $xoopsDB->query($sql)){
				$bids = array();
				while( list($blogid) = $xoopsDB->fetchRow($result) ){
					$bids[] = $blogid;
				}
				return $bids;
			}
		}
	}
	
	function createRssURL($uid){
		$useRerite = PopnupBlogUtils::getXoopsModuleConfig('POPNUPBLOG_REWRITE');
		if((empty($useRerite)) || ($useRerite == 0) ){
			return POPNUPBLOG_DIR.'rss.php'.POPNUPBLOG_REQUEST_URI_SEP.$uid;
		}else{
			return POPNUPBLOG_DIR.'rss/'.$uid.".xml";
		}
	}
	
	function createUrlpostid($postid){
		return XOOPS_URL."/modules/popnupblog/index.php?postid=".$postid;
	}
	function createUrl($blogid, $year = 0, $month = 0, $date = 0, $hours = 0, $minutes = 0, $seconds = 0, $command = null){
		return XOOPS_URL."/modules/popnupblog/".PopnupBlogUtils::createUrlNoPath($blogid, $year, $month, $date, $hours, $minutes, $seconds, $command);
	}
	
	function createUrlNoPath($blogid, $year = 0, $month = 0, $date = 0, $hours = 0, $minutes = 0, $seconds = 0, $command = null){
		$useRerite = PopnupBlogUtils::getXoopsModuleConfig('POPNUPBLOG_REWRITE');
		$result = '';
		if((empty($useRerite)) || ($useRerite == 0) ){
			$result .= "index.php".POPNUPBLOG_REQUEST_URI_SEP.PopnupBlogUtils::makeParams($blogid, $year, $month, $date, $command);
		}else{
			$result .= "view/".PopnupBlogUtils::makeParams($blogid, $year, $month, $date, $command).".html";
		}
		return $result;
	}
	//
	// function toRssDate($time, $timezone = null)
	//
	function toRssDate($time){
		//if (empty($timezone)) return 0;
		//if(!empty($timezone)){
		//	$time = xoops_getUserTimestamp($time);
		//}
		$res =  date("Y-m-d\\TH:i:sO", $time);
		return substr($res, 0, strlen($res) -2).":".substr($res, -2);
	}
	
	function checkUid($iuid){
		$uid = intval($iuid);
		if( $uid > 0){
			return $uid;
		}
	}

	function checkYear($iyear){
		$year = intval($iyear);
		if ( ($year > 1000) && ($year < 3000) ){
			return $iyear;
		}
		redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_INVALID_DATE.'(YEAR)'.$iyear);
		exit();
	}
	
	function checkMonth($imonth){
		$month = intval($imonth);
		if ( ($month > 0) && ($month < 13) ){
			return $imonth;
		}
		redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_INVALID_DATE.'(MONTH)');
		exit();
	}
	
	function checkDate($year, $month, $date){
		if(checkdate(intval($month), intval($date), intval($year))){
			return $date;
		}
		redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_INVALID_DATE.'(ALL DATE) '.intval($year)."-".intval($month)."-". intval($date));
		exit();
	}
	
	function makeParams($blogid, $year=0, $month=0, $date=0, $command = null){
		$result = '';
		$c = '';
		if(!empty($command)){
			$c = '-'.$command;
		}
		if($year == 0){
			$result = $blogid;
		}elseif($date == 0){
			$result = sprintf("%s-%04u%02u%s", "".$blogid, $year, $month, $c);
		}else{
			$result = sprintf("%s-%04u%02u%02u%s", "".$blogid, $year, $month, $date, $c);
		}
		return $result;
	}
	
	function makeTrackBackURL($postid){
		$tburl = XOOPS_URL.'/modules/popnupblog/trackback.php'.POPNUPBLOG_TRACKBACK_URI_SEP."?postid=".$postid;
		return $tburl;
	}
	
	function isCompleteDate($d){
		if(!empty($d['year'])){
			if(checkdate(intval($d['month']), intval($d['date']), intval($d['year']))){
				return true;
			}
		}
		return false;
	}
	function complementDate($d){
		//if(!checkdate(intval($d['month']), intval($d['date']), intval($d['year']))){
			$time = time();
			$d['year'] = date('Y',$time);
			$d['month'] = sprintf('%02u', date('m',$time));
			$d['date'] =  sprintf('%02u', date('d',$time));
			$d['hours'] =  sprintf('%02u', date('H',$time));
			$d['minutes'] =  sprintf('%02u', date('i',$time));
			$d['seconds'] =  sprintf('%02u', date('s',$time));
		//}
		return $d;
	}
	
	function convert_encoding(&$text, $from = 'auto', $to){
		if(function_exists('mb_convert_encoding')){
			return mb_convert_encoding($text, $to, $from); 
		} elseif (function_exists('iconv')){
			return iconv($from, $to, $text);
		} elseif (function_exists('JcodeConvert')) {
			return JcodeConvert($str, 0, 1);
		}else{
			return $text;
		}
	}
	
	function assign_message(&$tpl){
		$all_constants_ = get_defined_constants();
		foreach($all_constants_ as $key => $val){
			if(preg_match("/^_(MB|MD|AM|MI)_POPNUPBLOG_(.)*$/", $key) || preg_match("/^POPNUPBLOG_(.)*$/", $key)){
				if(is_array($tpl)){
					$tpl[$key] = $val;
				}elseif(is_object($tpl)){
					$tpl->assign($key, $val);
				}
			}
		}
	}
	/*
	function get_recent_trackback($date){
		global $xoopsDB;
		$sql = 'select title, url from '.PBTBL_TRACKBACK.' where blogid = '.$date['blogid'].' order by t_date desc';
		if(!$db_result = $this->xoopsDB->query($sql)){
			return false;
		}
		$i = 0;
		
		$result['html'] = '<div>';
		while(list($title, $url) = $this->xoopsDB->fetchRow($db_result)){
			$result[data][] = new array(){ 'title' => $title, 'url' => $url};
			$i++;
			$result['html'] .= '<a href="'.$url.'" target="_blank">'.$title.'</a><br />';
		}
		$result['html'] .= '</div>';
		
		return $result;
	}
	*/
	function send_trackback_ping($trackback_url, $url, $title, $blog_name, $excerpt = null) {
		PopnupBlogPing2::send_trackback_ping($trackback_url, $url, $title, $blog_name, $excerpt) ;
	}
	
	function convert2sqlString($text){
		$ts =& MyTextSanitizer::getInstance();
		if(!is_object($ts)){
			exit();
		}
		$res = $ts->stripSlashesGPC($text);
		$res = $ts->censorString($res);
		$res = addslashes($res);
		return $res;
	}

	function mail_popimg(){
		global $poplog,$limit_min;
		$host = PopnupBlogUtils::getXoopsModuleConfig('MAILSERVER');
		if ($host==null || strlen($host)==0) return "Without Mail Option.";
		if (filemtime($poplog) < time() - $limit_min * 60) {
			return "<div style=\"text-align:center;\"><img src=./pop.php?img=1&time=".time()."\" width=70 height=1 /></div>Poped";
		} else {
			return "Snoozed";
		}
	}

	function get_blogid_from_postid($postid){
		global $xoopsDB;
		$sql = 'select blogid from '.PBTBL_BLOG.' WHERE postid='.$postid;
		list($blogid) = $xoopsDB->fetchRow($xoopsDB->query($sql));
		return $blogid;
	}

	function isGroupAndPrivate(){
		global $xoopsDB,$xoopsUser,$start;
		// For Group selection
		$gid = is_object($xoopsUser) ? $xoopsUser->getGroups() : array(XOOPS_GROUP_ANONYMOUS);
		$grps = implode("|",$gid);
		// For Private selection
		$pbids = PopnupBlogUtils::your_private();
		// For your own blog
		$uid = is_object($xoopsUser) ? $xoopsUser->uid() : 0;
		if ($gid && $pbids){
			$pids = implode(",",$pbids);
			$sql_where = " and (group_read REGEXP '". $grps ."' or blogid IN (". $pids .")";
			$sql_where .= $uid>0 ? " or uid=" . $uid . ")" : ")" ;
		}else {
			$sql_where = " and (group_read REGEXP '". $grps ."'";
			$sql_where .= $uid>0 ? " or uid=" . $uid . ")" : ")" ;
		}
		return $sql_where;
	}
	function mk_list_url($cat_id=0,$view=0){
		global $xoopsDB,$xoopsUser,$start;
		
		$sql_where = PopnupBlogUtils::isGroupAndPrivate();
		if ($cat_id>0) $sql_where = ' and cat_id = ' . $cat_id;
		$n = 1;
		$urls = '';
		$nurl = '';
		$cflg = false;
		$sql = "select count(*) from ".PBTBL_INFO." Where last_update != 0".$sql_where;
		if(list($num) = $xoopsDB->fetchRow($xoopsDB->query($sql))){
			for ($i=0;$i<$num;$i+=POPNUPBLOG_BLOCK_LIST_NUM){
				$cstart = $i.",".POPNUPBLOG_BLOCK_LIST_NUM;
				if ($start==$cstart){
					$nstr='('.$n.')';
					$cflg=true;
				} else {
					if ($cflg==true)
						$nurl='<a href="'."index.php".POPNUPBLOG_REQUEST_URI_SEP.'&start='.$cstart.'&cat_id='.$cat_id.'&view='.$view.'"><u>&raquo</u></a>&nbsp';
					$nstr=$n;
					$cflg=false;
				}
				$urls = $urls.'<a href="'."index.php".POPNUPBLOG_REQUEST_URI_SEP.'&start='.$cstart.'&cat_id='.$cat_id.'&view='.$view.'">'.$nstr.'</a>&nbsp';
				$n++;
			}
			$urls = $urls.$nurl;
		}
//		echo $urls;
		return $urls;
	}

	function find_uid_mail($uname){
		global $xoopsDB,$xoopsUser;
		if($xoopsUser->isAdmin()){
			$sql = "SELECT uid,email FROM ".$xoopsDB->prefix("users")." WHERE uname = '$uname'";
		}else{
			// Show only visible mail
			$sql = "SELECT uid,email FROM ".$xoopsDB->prefix("users")." WHERE uname = '$uname' and user_viewemail = 1";
		}
		if ( $result = $xoopsDB->query($sql) ) {
			$ret = $xoopsDB->fetchRow($result);
			return $ret;
		}
	}
}
?>
