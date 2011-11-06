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
class pb_comment {
	function getComments($postid){
		global $xoopsDB,$xoopsUser,$xoopsModuleConfig,$BlogCNF;

		$vote_yes = $vote_no = $vote_all = 0;
		$debug=1;
		$sql = 'select comment_id, comment_uid,comment_name, post_text, create_date, vote from '.PBTBL_COMMENT.
			' where status>0 and postid = '.$postid.' order by comment_id '; 
		if(!$result_select = $xoopsDB->query($sql)){
			return false;
		}
		$i = 0;
		$comments = array();
		if (!empty($xoopsUser)) $admin = $xoopsUser->isAdmin(); else $admin = "";
		$cDate = 0;
		while(
			list($comment_id, $comment_uid, $comment_name, $post_text, $create_date, $vote) = $xoopsDB->fetchRow($result_select)
		){
			if (preg_match($BlogCNF['deny_words'],$post_text)) continue;
			if (!$post_text) continue;
			
			$comments[$i]['id'] = $comment_id;
			$comments[$i]['uid'] = $comment_uid;

			$server_time = strtotime($create_date);
			$user_time = xoops_getUserTimestamp($server_time);
//			if ($debug) {
//				echo $create_date;
//				echo 'ServerTime = '.$server_time.'UserTime = '.$user_time;
//			}
			$result['blog'][$i]['year']  = strftime("%Y", $user_time);
			$result['blog'][$i]['month'] = strftime("%m", $user_time);
			$result['blog'][$i]['date']  = strftime("%d", $user_time);
			if ($cDate!=strftime("%d", $user_time)){
				$result['blog'][$i]['hidedate']  = false;
				$cDate = $result['blog'][$i]['date'];
			}else{
				$result['blog'][$i]['hidedate']  = true;
			}
			$result['blog'][$i]['hours']  = strftime("%H", $user_time);
			$result['blog'][$i]['minutes']  = strftime("%M", $user_time);
			$result['blog'][$i]['seconds']  = strftime("%S", $user_time);
			$comments[$i]['create_date'] = $create_date;
			$comments[$i]['create_date_s'] = ($create_date == 0) ? '<unknown>' : strftime("%m/%d", $user_time);
			$comments[$i]['create_date_m'] = ($create_date == 0) ? '<unknown>' : strftime("%m/%d %H:%M", $user_time);
			$comments[$i]['create_date_l'] = ($create_date == 0) ? '<unknown>' : strftime("%y/%m/%d %H:%M", $user_time);
			if($comment_uid > 0){
				$xuser = new XoopsUser($comment_uid);
				if($xuser->uid()){
					$comments[$i]['name'] = $xoopsModuleConfig['show_name'] && $xuser->name() ? $xuser->name() : $xuser->uname();
//					echo $xuser->name() , $xuser->uname();
				}
				if($xoopsUser){
					if ( $comments[$i]['uid'] == $xoopsUser->uid() || $admin )
						$comments[$i]['commentedit'] = true;
				}
			}else{
				$comments[$i]['name'] = $comment_name.'@'._MD_POPNUPBLOG_FORM_GUEST;
				if ( $admin ) $comments[$i]['commentedit'] = true;
			}
			$post_text = sanitize_blog($post_text,true,false,true);
			$comments[$i]['comment'] = $post_text;
			$comments[$i]['vote'] = $vote;
			$i++;
		}
		return $comments;
	}
	function get_RecentlyComment($blogid){
		global $xoopsUser, $xoopsDB,$BlogCNF;
		$sql = 'select comment_uid,comment_name,post_text FROM '.PBTBL_COMMENT
			.' WHERE blogid = \''.$blogid.'\' order by comment_id desc limit 1;';
		if(!$result_select = $xoopsDB->query($sql)){
			return false;
		}
		$result = array();
		if(list($uid,$uname,$comment) = $xoopsDB->fetchRow($result_select)){
			if (!preg_match($BlogCNF['deny_words'],$comment)){
				$show_name = PopnupBlogUtils::getXoopsModuleConfig('show_name');
				if ( $show_name==1 && (trim(users::realname($uid)!='')))
					$result['uname'] = users::realname($uid);
				else
					$result['uname'] = $uname;
				$result['comment'] = $comment;
			}
		}
		return $result;
	}
	function getComment1($cid){
		global $xoopsUser,$xoopsDB,$BlogCNF;
		$sql = 'select blogid,comment_uid, post_text,vote,status,notifypub FROM '.PBTBL_COMMENT.' WHERE comment_id = '.$cid;
		if(!$result_select = $xoopsDB->query($sql)){
			return false;
		}
		$result = array();
		$ts =& MyTextSanitizer::getInstance();
		if(list($blogid,$comment_uid,$text,$vote,$status,$notifypub) = $xoopsDB->fetchRow($result_select)){
			//echo $BlogCNF['deny_words'].$text;
			if (!preg_match($BlogCNF['deny_words'],$text)){
				$result['blogid'] = $blogid;
				$result['comment_uid'] = $comment_uid;
				$result['text'] = sanitize_blog($text,true,false,true);
				$result['text_edit'] = $ts->makeTareaData4Edit($text);
				$result['vote'] = $vote;
				$result['status'] = $status;
				$result['notifypub'] = $notifypub;
			}
		}
		return $result;
	}
	function insertComment($blogid,$postid,$uid=0,$name,$comment,$vote,$status,$notifypub=NULL){
		global $xoopsUser,$xoopsConfig,$xoopsDB;

		$userTZ = 0;
		if ($uid == 0){
			if($xoopsUser){
				$uid = $xoopsUser->uid();
				$userTZ = $xoopsUser->getVar("timezone_offset");
			} else { 
				$userTZ = $xoopsConfig['default_TZ'];
			}
		}
		$sqlName = PopnupBlogUtils::convert2sqlString($name);
		//$comment = htmlspecialchars($comment);
		$sqlComment = PopnupBlogUtils::convert2sqlString($comment); 
		// Check Duplicate vote

		if ($vote!=0 && $uid>0 ){
			$sql='select count(*) from '.PBTBL_COMMENT.' where comment_uid='.$uid.' and postid='.$postid.' and vote<>0';
			if ( $dbResult = $xoopsDB->query($sql)){ 
				list($cnt) = $xoopsDB->fetchRow($dbResult);
				if ($cnt>0) return null;
			}
		}
		$sql_base = "INSERT INTO %s (blogid, postid, comment_id, comment_uid, comment_name, post_text, create_date, vote, status, notifypub)"
		 	." values(%u, %u, null, %u, '%s', '%s', '%s', %d, %u, %u)";
		$sql = sprintf($sql_base, PBTBL_COMMENT, $blogid, $postid, $uid, $sqlName, $sqlComment, date("Y-m-d H:i:s",time()), $vote,$status,$notifypub);
		$result = $xoopsDB->queryF($sql);
		if ($vote==1){
			$sql = sprintf("UPDATE %s set votes_yes = votes_yes + 1 where postid = %u", PBTBL_BLOG, $postid);
			$result = $xoopsDB->queryF($sql);
		}elseif ($vote==-1){
			$sql = sprintf("UPDATE %s set votes_no = votes_no + 1 where postid = %u", PBTBL_BLOG, $postid);
			$result = $xoopsDB->queryF($sql);
		}
		return $status;
	}
	function updateComment(&$blogid,&$postid,$comment_uid,&$comment_name, $comment_id, $comment='', $updatevote=0, $status, $notifypub=NULL){
		global $xoopsUser,$xoopsDB;
	
		$myts =& MyTextSanitizer::getInstance();
		$comment = $myts->addSlashes( $myts->censorString( $comment ) );
		$comment_uid = intval($comment_uid);
		$comment_id = intval($comment_id);
		$updatevote = intval($updatevote);
		$uid = 0;
		if($xoopsUser){
			$uid = $xoopsUser->uid();
		}
		if($xoopsUser->isAdmin()){
			$uid = $comment_uid;
		}
		//$comment = htmlspecialchars($comment);
		$sqlComment = PopnupBlogUtils::convert2sqlString($comment); 
		// Get Current Comment
		$sql = sprintf("select blogid,postid,comment_name,vote,notifypub from %s where comment_id = %u", PBTBL_COMMENT, $comment_id);
		$result_select = $xoopsDB->query($sql);
		if(!$result_select) return false;
		list($blogid,$postid,$comment_name,$vote,$notifypub) = $xoopsDB->fetchRow($result_select);
		// Delete Comment
		if(empty($sqlComment) and $updatevote==0){
			// Cancel for voted
			$sql = sprintf("select comment_id,vote from %s where comment_uid = %u and comment_id = %u", PBTBL_COMMENT, $uid,$comment_id);
			$result_select = $xoopsDB->query($sql);
			if(list($comment_id,$vote) = $xoopsDB->fetchRow($result_select)){
				if($vote==1){
					$sql = sprintf("UPDATE %s set votes_yes = votes_yes-1 where comment_id = %u", PBTBL_BLOG, $comment_id);
				}elseif($vote==-1){
					$sql = sprintf("UPDATE %s set votes_no = votes_no-1 where comment_id = %u", PBTBL_BLOG, $comment_id);
				}
				$result = $xoopsDB->queryF($sql);
			}
			if (empty($sqlComment)){
				$sql = sprintf("delete from %s where comment_uid = %u and comment_id = %u", PBTBL_COMMENT, $uid,$comment_id);
			}else{
				$sql = sprintf("UPDATE %s set vote = 0 where comment_uid = %u and comment_id = %u", PBTBL_COMMENT, $uid,$comment_id);
			}
			$result = $xoopsDB->queryF($sql);
			return true;
		} else {
			// Do Update
			$sql_base = "UPDATE %s set post_text='%s', create_date='%s', vote='%d', notifypub=%u";
			if (isset($status)){
				$sql_base .= ", status=status+%u where comment_id=%u";
				$sql = sprintf($sql_base, PBTBL_COMMENT, $sqlComment, date("Y-m-d H:i:s",time()),$updatevote,$notifypub,$status,$comment_id);
			}else{
				$sql_base .= " where comment_id=%u";
				$sql = sprintf($sql_base, PBTBL_COMMENT, $sqlComment, date("Y-m-d H:i:s",time()),$updatevote,$notifypub,$comment_id);
			}
			$result = $xoopsDB->queryF($sql);
		}
		$sql='';
		if ($vote==-1 && $updatevote==1){
			$sql = sprintf("UPDATE %s set votes_no = votes_no - 1 where postid = %u", PBTBL_BLOG, $postid);
		}elseif ($vote==1 && $updatevote==-1){
			$sql = sprintf("UPDATE %s set votes_yes = votes_yes - 1 where postid = %u", PBTBL_BLOG, $postid);
		}elseif ($vote==0 && $updatevote==1){
			$sql = sprintf("UPDATE %s set votes_yes = votes_yes + 1 where postid = %u", PBTBL_BLOG, $postid);
		}elseif ($vote==0 && $updatevote==-1){
			$sql = sprintf("UPDATE %s set votes_no = votes_no + 1 where postid = %u", PBTBL_BLOG, $postid);
		}
		if ($sql) $result = $xoopsDB->queryF($sql);
		return pb_comment::GetStatusByCommentid($comment_id);
	}
	function deleteComment($comment_id){
		global $xoopsUser,$xoopsDB;
		$sql = sprintf("delete from %s where comment_id = %u", PBTBL_COMMENT, $comment_id);
		$result = $xoopsDB->queryF($sql);
		return $result;
	}
	function get_blogid_from_commentid($comment_id){
		global $xoopsDB;
		$sql = 'select blogid from '.PBTBL_COMMENT.' WHERE comment_id='.$comment_id;
		list($blogid) = $xoopsDB->fetchRow($xoopsDB->query($sql));
		return $blogid;
	}
	function GetStatusByCommentid($comment_id){
		global $xoopsDB;
		$sql = 'select status from '.PBTBL_COMMENT.' WHERE comment_id='.$comment_id;
		list($status) = $xoopsDB->fetchRow($xoopsDB->query($sql));
		return $status;
	}
}
?>
