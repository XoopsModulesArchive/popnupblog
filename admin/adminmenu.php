<?php
//  ------------------------------------------------------------------------ //
//                adminmenu.php                                              //
//                    Copyright (c) 2005 Yoshi.Sakai @ Bluemoon inc.         //
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
?>
	<!--/* Nice button styles */-->
	<style type="text/css">
	#buttontop { float:left; width:100%; background: #e7e7e7; font-size:93%; line-height:normal; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; margin: 0; }
	#buttonbar { float:left; width:100%; background: #e7e7e7 url("../images/bg.gif") repeat-x left bottom; font-size:93%; line-height:normal; border-left: 1px solid black; border-right: 1px solid black; margin-bottom: 12px; }
	#buttonbar ul { margin:0; margin-top: 15px; padding:10px 10px 0; list-style:none; }
	#buttonbar li { display:inline; margin:0; padding:0; }
	#buttonbar a { float:left; background:url("../images/left_both.gif") no-repeat left top; margin:0; padding:0 0 0 9px; border-bottom:1px solid #000; text-decoration:none; }
	#buttonbar a span { float:left; display:block; background:url("../images/right_both.gif") no-repeat right top; padding:5px 15px 4px 6px; font-weight:bold; color:#765; }
	/* Commented Backslash Hack hides rule from IE5-Mac \*/
	#buttonbar a span {float:none;}
	/* End IE5-Mac hack */
	#buttonbar a:hover span { color:#333; }
	#buttonbar #current a { background-position:0 -150px; border-width:0; }
	#buttonbar #current a span { background-position:100% -150px; padding-bottom:5px; color:#333; }
	#buttonbar a:hover { background-position:0% -150px; }
	#buttonbar a:hover span { background-position:100% -150px; }
	</style>
<?php
require_once XOOPS_ROOT_PATH.'/modules/popnupblog/class/popnupblog.php';

	global $xoopsDB, $xoopsModule, $xoopsConfig, $xoopsModuleConfig;
	$tblCol = Array();
	$tblCol[0]=$tblCol[1]=$tblCol[2]=$tblCol[3]=$tblCol[4]=$tblCol[5]=$tblCol[6]='';
//	$tblCol[$currentoption] = 'current';

	echo "<div id='buttontop'>";
	echo "<table style='width: 100%; padding: 0;' cellspacing='0'><tr>";
	echo "<td style='width: 45%; font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;'>
		<a class='nobutton' href='../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod="
		.$xoopsModule ->getVar('mid')."'>"
		._PREFERENCES."</a> | <a href='../index.php'>"
		._AM_POPNUPBLOG_GOMOD."</a>| <a href='http://www.bluemooninc.biz/' target='_blank'>"
		._AM_POPNUPBLOG_SUPPORTSITE."</a></td>";
	echo "<td style='width: 55%; font-size: 10px; text-align: right; color: #2F5376; padding: 0 6px; line-height: 18px;'><b>"
		.$xoopsModule->name()." v".POPNUPBLOG_VERSION."</b></td>";
	echo "</tr></table>";
	echo "</div>";
	$waitings = PopnupBlogUtils::getWaitingsCount();
	echo "<div id='buttonbar'>";
	echo "<ul>";
	echo "<li id='".$tblCol[0]."'><a href='create.php'><span>"._AM_POPNUPBLOG_CREATE."</span></a></li>";
	echo "<li id='".$tblCol[1]."'><a href='".$GLOBALS['BlogCNF']['admin']."/categories.php?mode=addcat' alt='"._AM_LINK2ADDCAT."'><span>"._AM_ADDACAT."</span></a></li>";
	echo "<li id='".$tblCol[2]."'><a href='".$GLOBALS['BlogCNF']['admin']."/categories.php?mode=editcat' alt='"._AM_LINK2EDITCAT."'><span>"._AM_EDITCATTTL."</span></a></li>";
	echo "<li id='".$tblCol[3]."'><a href='".$GLOBALS['BlogCNF']['admin']."/categories.php?mode=remcat' alt='"._AM_LINK2RMVCAT."'><span>"._AM_RMVACAT."</span></a></li>";
	echo "<li id='".$tblCol[4]."'><a href='".$GLOBALS['BlogCNF']['admin']."/categories.php?mode=catorder' alt='"._AM_LINK2ORDERCAT."'><span>"._AM_REORDERCAT."</span></a></li>";
	echo "<li id='".$tblCol[5]."'><a href='".$GLOBALS['BlogCNF']['admin']."/trackbacks.php?mode=list' alt='"._AM_LINK2TRACKBACKS."'><span>"._AM_TRACKBACKS."</span></a></li>";
	echo "<li id='".$tblCol[6]."'><a href='".$GLOBALS['BlogCNF']['admin']."/waiting.php' alt='"._AM_LINK2WAITING."'><span>"._AM_WAITING;
	if ($waitings>0) echo "<font color=red>(" . $waitings .")</font>";
	echo "</span></a></li>";
	//if (($xoopsModuleConfig['usecatperm'] == 1) || ($xoopsModuleConfig['usefileperm'] == 1)) {
	//}
	echo "</ul></div><br />";
?>
