<?php
// $Id: emailalias.php,v 3.1 2007/10/04 15:58:17 yoshis Exp $
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
class emailalias {
	function get_uid_bymail($bid,$pid,$emailalias){
		global $xoopsDB;
		$sql = "SELECT u.uid FROM ".PBTBL_EMAILALIAS." a LEFT JOIN ".$xoopsDB->prefix('users')
			." u ON u.email=a.email Where blogid=$bid and public=$pid and a.email='$emailalias' limit 1";
		if ( !$result = $xoopsDB->query($sql) ) return false;
		list($uid) = $xoopsDB->fetchRow($result);
		return $uid;
	}
	function _deletebylist($bid,$pid,$emailalias){
		global $xoopsDB;
		while(list($null,$email) = each($emailalias)){
			$sql = "DELETE FROM ".PBTBL_EMAILALIAS." Where blogid=$bid and public=$pid and email='$email'";
			if (!$result = $xoopsDB->query($sql)){
				return $result;
			}
		}
	}
	function _add($bid,$pid,$email,$uid=0){
		global $xoopsDB;
		$myts =& MyTextSanitizer::getInstance();
		$email = $myts->censorString($email);
		$email = $myts->addSlashes($email);
		$bid = intval($bid);
		$pid = intval($pid);
		$uid = intval($uid);

		if(!$email) $email = users::getEmailByUid($uid);
		$sql = "INSERT INTO ".PBTBL_EMAILALIAS." (blogid, public, email, uid) VALUES ($bid, $pid, '$email',$uid)";
		return $xoopsDB->query($sql);
	}
	function _delete($bid,$pid=0,$email='',$uid=0){
		global $xoopsDB;
		$myts =& MyTextSanitizer::getInstance();
		$email = $myts->censorString($email);
		$email = $myts->addSlashes($email);
		$bid = intval($bid);
		$pid = intval($pid);
		$uid = intval($uid);

		$sql = "DELETE FROM ".PBTBL_EMAILALIAS." Where blogid=$bid";
		if ($pid>0) $sql .= " and public = $pid";
		if ($email) $sql .= " and email = '$email'";
		if ($uid>0) $sql .= " and uid = $uid";
		return $xoopsDB->query($sql);
	}
	function registered_bid($uid=0,$pid=0){
		global $xoopsDB,$xoopsUser;
		$pid = intval($pid);
		$uid = intval($uid);
		if ($uid==0 && $xoopsUser) $uid = $xoopsUser->uid();
		$s = "SELECT blogid FROM ".PBTBL_EMAILALIAS." WHERE uid = $uid";
		$s .= ($pid>0) ? "and public = $pid;" : ";" ;
		if ( !$r = $xoopsDB->query($s) ) return false;
		$res = array();
		while(list($bid) = $xoopsDB->fetchRow($r)){
			$res[] = $bid;
		}
		if ($pid==0)
			return array_unique($res);
		else
			return $res;
	}
	function MyChecked($uid=0,$bid=0,$pid=0){
		global $xoopsDB,$xoopsUser;
		$uid = intval($uid);
		$bid = intval($bid);
		$pid = intval($pid);
		if ($uid==0 and !$xoopsUser) return 0;
		if ($uid==0) $uid = $xoopsUser->uid();
		$s = "SELECT count(*) FROM ".PBTBL_EMAILALIAS." WHERE uid=$uid and blogid=$bid";
		$s .= ($pid>0) ? "and public = $pid;" : ";" ;
		if ( !$r = $xoopsDB->query($s) ) return 0;
		list($ret) = $xoopsDB->fetchRow($r);
		return $ret;
	}
	//
	//  Modified by hoshiyan @ hoshiba-farm.com 2004.8.5
	//
	function createEmailAliasInfo($blogid='', $email = ''){
		global $xoopsUser,$xoopsDB;
		$myts =& MyTextSanitizer::getInstance();
		$email = $myts->addSlashes( $myts->censorString( $email ) );
		
		$debug = 0;
		// this query is just for convenience of novice user.
		$sqlcmd = 'create table if not exists '.PBTBL_EMAILALIAS.' (blogid int(5) unsigned not null default 0, public tinyint(1) not null default 1, email varchar(60), primary key(blogid))';
		if ($debug){ echo $sqlcmd; }
		$result = $xoopsDB->queryF($sqlcmd);
		if ($debug){ echo $result; }
		
		$sqlcmd = 'select * from '.PBTBL_EMAILALIAS.' where blogid = '.$blogid.' and email =\''.$email.'\'';
		if ($debug){ echo $sqlcmd; }
		$result = $xoopsDB->query($sqlcmd);
		if ($debug){ echo $result; }
		if($result){
			$sqlcmd = 'INSERT INTO '.PBTBL_EMAILALIAS.' set blogid ='.$blogid.', public = 1, email =\''.$email.'\'';
			if ($debug){ echo $sqlcmd; }
			$result = $xoopsDB->queryF($sqlcmd);
			if ($debug){ echo $result; }
		}
		return true;
	}
	//
	//  Modified by Yoshi.Sakai 2004.8.31
	//
	function setEmailAliasInfo($blogid='', $email = ''){
		global $xoopsUser,$xoopsDB;
		$myts =& MyTextSanitizer::getInstance();
		$email = $myts->addSlashes( $myts->censorString( $email ) );

		$debug = 0;
		$sql = sprintf('SELECT blogid, public, email from %s where blogid=%u', PBTBL_EMAILALIAS,$blogid);
		if ($debug){ echo $sql; }
		$qResult = $xoopsDB->query($sql);
		list($bid,$public,$cur_email) = $xoopsDB->fetchRow($qResult);
		if ($debug){ echo " blogid = (".$bid.")"; }
		if(!$bid){
			emailalias::createEmailAliasInfo($blogid, $email);
		} else {
			$sql = 'UPDATE '.PBTBL_EMAILALIAS.' set public=1, email=\''.$email.'\' where blogid ='.$blogid;
			if ($debug){ echo $sql; }
			$qResult = $xoopsDB->query($sql);
			if ($debug){ echo $qResult; }
		}
		return true;
	}
}
?>