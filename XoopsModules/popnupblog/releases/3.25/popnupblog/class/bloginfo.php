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
class bloginfo {
	function get_email_from_blogid( $blogid ){
		global $xoopsDB;
		$rawuid = $xoopsDB->fetchArray($xoopsDB->query("SELECT email FROM ".PBTBL_INFO." WHERE blogid = '".$blogid."' limit 1"));
		return $rawuid['email'];
	}
	function get_uid_from_blogid( $blogid ){
		global $xoopsDB;
		$rawuid = $xoopsDB->fetchArray($xoopsDB->query("SELECT uid FROM ".PBTBL_INFO." WHERE blogid = '".$blogid."' limit 1"));
		return $rawuid['uid'];
	}
	function get_all_uids(){
		global $xoopsDB;
		$sql = "SELECT i.uid FROM ".PBTBL_INFO." i LEFT JOIN ".$xoopsDB->prefix('users')." u ON u.uid=i.uid GROUP by uid ORDER BY uname";
		if(!$result = $xoopsDB->query($sql)){
			return false;
		}
		$ret = array();
		while(list($uid)=$xoopsDB->fetchRow($result)){
			$ret[]=$uid;
		}
		return $ret;
	}
	function get_bloginfo( $blogid ){
		global $xoopsDB;
		$sql = "SELECT blogid,title,default_status FROM ".PBTBL_INFO." WHERE blogid=".$blogid;
		if(!$result = $xoopsDB->query($sql)){
			return false;
		}
		$ret = array();
		while(list($blogid,$title,$default_status)=$xoopsDB->fetchRow($result)){
			$ret[$blogid]['title']=$title;
			$ret[$blogid]['default_status']=$default_status;
		}
		return $ret;
	}
	function get_blogid_from_uid( $uid ){
		global $xoopsDB;
		$sql= "SELECT blogid,title FROM ".PBTBL_INFO." WHERE uid=".$uid;
		$result = $xoopsDB->query($sql);
		$ret=array();
		while(list($blogid,$title)=$xoopsDB->fetchRow($result)){
			$r=array();
			$r['blogid']=$blogid;
			$r['title']=$title;
			$ret[]=$r;
		}
		return $ret;
	}
	function get_MLinfo( $blogid ){
		global $xoopsDB;
		$ret = $xoopsDB->fetchArray($xoopsDB->query("SELECT title,uid,pop_address FROM ".PBTBL_INFO." WHERE blogid = '".$blogid."' limit 1"));
		return $ret;
	}
	function get_PopAccessInfo(&$definfo){
		global $xoopsDB;
		$sql="SELECT blogid,pop_server,pop_user,pop_password,pop_address FROM ".PBTBL_INFO." WHERE ml_function=1 and LENGTH(pop_server)>0;";
		if(!$result = $xoopsDB->query($sql)){
			return false;
		}
		while(list($blogid,$pop_server,$pop_user,$pop_password,$pop_address )=$xoopsDB->fetchRow($result)){
			$info = array();
			$info['blogid']=intval($blogid);
			$info['pop_server']=$pop_server;
			$info['pop_user']=$pop_user;
			$info['pop_password']=$pop_password;
			$info['pop_address']=$pop_address;
			$definfo[]=$info;
		}
		return $definfo;
	}
	/**
	 * create new blog user
	 */
	function createNewBlogUser($uid='',$group_post='',$cat_id=0,$read='',$comment='',$vote='',$title='',$desc='',$email='',$emailalias=''){
		global $xoopsUser,$xoopsDB,$BlogCNF;
		$myts =& MyTextSanitizer::getInstance();
		$title      = $myts->addSlashes( $myts->censorString( $title      ) );
		$desc       = $myts->addSlashes( $myts->censorString( $desc       ) );
		$email      = $myts->addSlashes( $myts->censorString( $email      ) );
		$emailalias = $myts->addSlashes( $myts->censorString( $emailalias ) );
		$uid = intval($uid);
		$cat_id = intval($cat_id);
		
		$result = $xoopsDB->query('select count(*) from '.PBTBL_INFO.' where uid = '.$uid);
		list($count) = $xoopsDB->fetchRow($result);
		if( $count > $BlogCNF['maxuserblogs'] ){
			return false;
		}else{
			$sql='INSERT INTO '.PBTBL_INFO
				.' (uid, group_post, cat_id, group_read,group_comment,group_vote, last_update, title, blog_desc, email) values ('.$uid
				.',\''.$group_post.'\','.intval($cat_id)
				.',\''.$read.'\',\''.$comment.'\',\''.$vote
				.'\',CURRENT_TIMESTAMP(), \'' .PopnupBlogUtils::convert2sqlString($title) .'\',\'' .$desc.'\',\''.$email.'\')';
			$ret = $xoopsDB->queryF($sql);
			if ($ret){
				$result = $xoopsDB->query('SELECT blogid FROM '.PBTBL_INFO.' Where uid='.$uid.' and title=\''.$title.'\'');
				list($blogid) = $xoopsDB->fetchRow($result);
				if ($email && $emailalias){
					if ($blogid) emailalias::createEmailAliasInfo($blogid,$emailalias);
				}
				// Delete From Waiting list
				$result = $xoopsDB->query('select uid from '.PBTBL_APPL.' where uid = '.$uid);
				if(list($uid) = $xoopsDB->fetchRow($result)){
					$xoopsDB->queryF('delete from '.PBTBL_APPL.' where uid = '.$uid);
				}
				// Notify New Blog
				$notification_handler = & xoops_gethandler( 'notification' );
				$tags = array();
				$tags['BLOG_NAME'] = $title;
				$tags['BLOG_DESCRIPTION'] = $desc;
				$tags['BLOG_URL'] =  PopnupBlogUtils::createUrl($blogid);
				$notification_handler -> triggerEvent( 'global', 0, 'new_blog', $tags );
				$notification_handler -> subscribe('global', 0, 'new_blog');
				return true;
			}
		}
	}
}
?>
