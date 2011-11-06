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
class category {
	function get_categories(){
		global $xoopsDB;
		if(!$result = $xoopsDB->query("SELECT cat_id,cat_title FROM ".$xoopsDB->prefix("popnupblog_categories")
			." order by cat_order")){
			return false;
		}
		$ret = array();
		while(list($cat_id,$cat_title)=$xoopsDB->fetchRow($result)){
			$ret[$cat_id]=$cat_title;
		}
		return $ret;
	}
	function get_categoryname($cat_id=0){
		global $xoopsDB;
		$wstr = $cat_id ? " WHERE cat_id=".$cat_id :"";
		if(!$result = $xoopsDB->query("SELECT cat_title FROM ".$xoopsDB->prefix("popnupblog_categories")
			.$wstr)){
			return false;
		}
		list($cat_title) = $xoopsDB->fetchRow($result);
		return $cat_title;
	}
}
?>
