<?php
// $Id: users.php,v 3.0 2006/12/15 11:09:08 yoshis Exp $
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
class users {
	function getEmailByUid( $uid ){
		$poster = new XoopsUser( $uid ) ;
		// check if invalid uid
		if( $poster->uname() == '' ) return '' ;
		return $poster->email();
	}
	function getUname( $uid ){
		$show_name = PopnupBlogUtils::getXoopsModuleConfig('show_name');
		if ( $show_name==1 && (trim(users::realname($uid))!='') )
			return users::realname($uid);
		else
			return users::uname($uid);
	}
	// Added by hoshiba-farm.com 2006.02.28
	function email($uid){
		global $xoopsDB;
		$sql = "SELECT email FROM ".$xoopsDB->prefix("users")." WHERE uid = '$uid' limit 1;";
		if ( $result = $xoopsDB->query($sql) ) {
			$ret = $xoopsDB->fetchRow($result);
			return $ret[0];
		}
	}
	function uname($uid)	{
		global $xoopsDB;
		static $TblUser;
		if (isset($TblUser) && array_key_exists($uid,$TblUser)){
			$ret=$TblUser[$uid];
		}else{
			$sql = "SELECT uname FROM ".$xoopsDB->prefix("users")." WHERE uid= $uid limit 1;";
			$ret = '';
			if ( $result = $xoopsDB->query($sql) ) {
				if ( $myrow = $xoopsDB->fetchRow($result) ){
					$ret = $myrow[0];
				}
			}
			$TblUser[$uid]=$ret;
		}
		return $ret;
	}
	function realname($uid)	{
		global $xoopsDB;
		static $TblUser;
		if (isset($TblUser) && array_key_exists($uid,$TblUser)){
			$ret=$TblUser[$uid];
		}else{
			$sql = "SELECT name FROM ".$xoopsDB->prefix("users")." WHERE uid= $uid limit 1;";
			$ret = '';
			if ( $result = $xoopsDB->query($sql) ) {
				if ( $myrow = $xoopsDB->fetchRow($result) ){
					$ret = $myrow[0];
				}
			}
			$TblUser[$uid]=$ret;
		}
		return $ret;
	}
	function get_unames()	{
		global $xoopsDB;
		$sql = "SELECT uid,uname FROM ".$xoopsDB->prefix("users")." order by uname";
		$myrow = array();
		$myrow[0]=null;
		$result = $xoopsDB->query($sql);
		if ( !$result ) return 0;
		while( list($uid,$uname) = $xoopsDB->fetchRow($result) ){
			$myrow[$uid]=$uname;
		}
		return $myrow;
	}
}
?>
