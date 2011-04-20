<?php
/***************************************************************************
                          admin_forums.php  -  description
                             -------------------
    begin                : Wed July 19 2000
    copyright            : (C) 2001 The phpBB Group
    email                : support@phpbb.com

    $Id: categories.php,v 1.1.1.1 2005/08/28 02:13:08 yoshis Exp $
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

include '../../../include/cp_header.php';
//include '../functions.php';
include '../conf.php';
require_once '../pop.ini.php';

if(
	(!defined('XOOPS_ROOT_PATH')) || 
	(!is_object($xoopsUser)) || 
	(!$xoopsUser->isAdmin()) ){
	exit();
}

if (isset($_GET['mode'])){
	$mode = $_GET['mode'];
}

foreach ($_POST as $k => $v) {
	${$k} = $v;
}


switch (trim($mode)) {
case 'editcat':
	$myts =& MyTextSanitizer::getInstance();
   	if ( isset($_POST['submit']) && isset($_POST['save']) ) {
		$new_title = $myts->makeTboxData4Save( $myts->censorString( $_POST['new_title'] ) );
		$sql = "UPDATE ".$xoopsDB->prefix("popnupblog_categories")." SET cat_title = '$new_title' WHERE cat_id = $cat_id";
		if ( !$result = $xoopsDB->query($sql) ) {
			redirect_header("./index.php", 1);
			exit();
   		} else {
			redirect_header("./index.php", 1, _AM_CATEGORYUPDATED);
	 	}
	} else if(isset($_POST['submit']) && $_POST['submit'] != "") {
		$sql = "SELECT cat_title FROM ".$xoopsDB->prefix("popnupblog_categories")." WHERE cat_id = '$cat'";
   		if ( !$result = $xoopsDB->query($sql) ) {
			redirect_header("./index.php", 1);
			exit();
   		}
		xoops_cp_header();
		include("adminmenu.php");
		echo"&nbsp;<table width='100%' border='0' cellspacing='1' class='outer'>"
		."<tr><td class=\"odd\">";
   		$cat_data = $xoopsDB->fetchArray($result);
   		$cat_title = $myts->makeTboxData4Edit($cat_data["cat_title"]);
		?>
		<form action="<?php echo $_SERVER['PHP_SELF'];?>" method='post'>
		<table border="0" cellpadding="1" cellspacing="0" align='center' Valign="TOP" width="95%"><tr><td class='bg2'>
		<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr class='bg3' align='left'>
		<td align='center' colspan="2"><span class='fg2'><b><?php echo _AM_EDITCATEGORY;?> <?php echo $cat_title ?></b><span class='fg2'></td>
		</tr>
		<tr class='bg1' align='left'>
		<td><?php echo _AM_CATEGORYTITLE;?></td>
		<td><input type="text" name="new_title" value="<?php echo $cat_title ?>" size="45" maxlength="100"></td>
		</tr>
		<tr class='bg3' align='left'>
		<td align='center' colspan="2">
		<input type="hidden" name='mode' value="editcat" />
		<input type="hidden" name="save" value="TRUE" />
		<input type="hidden" name="cat_id" value="<?php echo $cat?>" />
		<input type='submit' name='submit' value="<?php echo _AM_SAVECHANGES;?>" />
		</td>
		</tr>
		</tr>
		</table></td></tr></table>
		<?php
	} else {
		$sql = "SELECT cat_id, cat_title FROM ".$xoopsDB->prefix("popnupblog_categories")." ORDER BY cat_order";
   		if ( !$result = $xoopsDB->query($sql) ) {
			redirect_header("./index.php", 1);
			exit();
   		}
		xoops_cp_header();
		include("adminmenu.php");
		echo"&nbsp;<table width='100%' border='0' cellspacing='1' class='outer'>"
		."<tr><td class=\"odd\">";
		?>
		<form action="<?php echo $_SERVER['PHP_SELF'];?>" method='post'>
		<table border="0" cellpadding="1" cellspacing="0" align='center' Valign="TOP" width="95%"><tr><td class='bg2'>
		<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr class='bg3' align='left'>
		<td align='center' colspan="2"><span class='fg2'><b><?php echo _AM_SELECTACATEGORYEDIT;?></b><span class='fg2'></td>
		</tr>
		<tr class='bg1' align='left'>
		<td align='center' colspan="2"><select name='cat' SIZE="0">
		<?php
		while ( $cat_data = $xoopsDB->fetchArray($result) ) {
			echo "<option value=\"".$cat_data["cat_id"]."\">".$myts->makeTboxData4Show($cat_data["cat_title"])."</option>\n";
		}
		?>
		</select></td>
		<tr class='bg3' align='left'>
		<td align='center' colspan="2">
		<input type="hidden" name='mode' value="editcat">
		<input type='submit' name='submit' value="<?php echo _AM_EDIT;?>">&nbsp;&nbsp;
		</td>
		</tr>
		</tr>
		</table></td></tr></table>
		<?php
   	}
	break;
case 'remcat':
	$myts =& MyTextSanitizer::getInstance();
    if ( isset($_POST['submit']) && $_POST['submit'] != "" ) {
		$sql = sprintf("DELETE FROM %s WHERE cat_id = %u", $xoopsDB->prefix("popnupblog_categories"), $cat);
		if ( !$r = $xoopsDB->query($sql) ) {
			redirect_header("./index.php", 1);
			exit();
		}
		redirect_header("./index.php", 1, _AM_CATEGORYDELETED);
	} else {
		xoops_cp_header();
		include("adminmenu.php");
		echo"&nbsp;<table width='100%' border='0' cellspacing='1' class='outer'>"
		."<tr><td class=\"odd\">";
		?>
		<form action="<?php echo $_SERVER['PHP_SELF'];?>" method='post'>
		<table border="0" cellpadding="1" cellspacing="0" align='center' width="95%"><tr><td class='bg2'>
		<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr class='bg3' align='left'>
		<td align='center' colspan="2"><span class='fg2'><b><?php echo _AM_RMVACAT;?></b></span></td>
		</tr>
		<tr class='bg3' align='left'>
		<td align='center' colspan="2"><span class='fg2'><i><?php echo _AM_NTWNRTFUTCYMDTVTEFS;?></i></span></td>
		</tr>
		<tr class='bg1'>
		<td align='center' colspan="2"><span class='fg2'>
		<select name='cat'>
		<?php
		$sql = "SELECT * FROM ".$xoopsDB->prefix("popnupblog_categories")." ORDER BY cat_title";
		if ( !$r = $xoopsDB->query($sql) ) {
			echo"</td></tr></table>";
			xoops_cp_footer();
			exit();
		}
		while (  $m = $xoopsDB->fetchArray($r) ) {
			echo "<option value=\"".$m['cat_id']."\">".$myts->makeTboxData4Show($m['cat_title'])."</option>\n";
		}
		?>
		</select>
		<input type='hidden' name='mode' value='<?php echo $mode ?>' /></td>
		</tr>
		<tr class='bg3'>
		<td align='center' colspan="2"><span class='fg2'>
		<input type="submit" name="submit" value="<?php echo _AM_REMOVECATEGORY;?>" /></td></tr>
		</table></table></form>
		<?php
	}
	break;
case 'addcat':
	$myts =& MyTextSanitizer::getInstance();
    if ( isset($_POST['submit']) && $_POST['submit'] != "" ) {
		$nextid = $xoopsDB->genId($xoopsDB->prefix("popnupblog_categories")."_cat_id_seq");
		$sql = "SELECT max(cat_order) AS highest FROM ".$xoopsDB->prefix("popnupblog_categories")."";
		if ( !$r = $xoopsDB->query($sql) ) {
			redirect_header("./index.php", 1);
			exit();
		}
		list($highest) = $xoopsDB->fetchRow($r);
		$highest++;
		$title = $myts->makeTboxData4Save( $myts->censorString( $title ) );
		$sql = "INSERT INTO ".$xoopsDB->prefix("popnupblog_categories")." (cat_id, cat_title, cat_order) VALUES ($nextid, '$title', '$highest')";
		if ( !$result = $xoopsDB->query($sql) ) {
			redirect_header("./index.php", 1);
			exit();
		}
		redirect_header("./index.php", 1, _AM_CATEGORYCREATED);
	} else {
		xoops_cp_header();
		include("adminmenu.php");
		echo"&nbsp;<table width='100%' border='0' cellspacing='1' class='outer'>"
		."<tr><td class=\"odd\">";
		?>
		<form action="<?php echo $_SERVER['PHP_SELF'];?>" method='post'>
		<table border="0" cellpadding="1" cellspacing="0" align='center' Valign="TOP" width="95%"><tr><td class='bg2'>
		<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr class='bg3' align='left'>
		<td align='center' colspan="2"><span class='fg2'><b><?php echo _AM_CREATENEWCATEGORY;?></b></span></td>
		</tr>
		<tr class='bg1' align='left'>
		<td><span class='fg2'><?php echo _AM_CATEGORYTITLE;?></span></td>
		<td><input type="text" name="title" size="40" maxlength="100"></td>
		</tr>
		<tr class='bg3' align="left">
		<td align='center' colspan="2">
		<input type="hidden" name="mode" value="addcat" />
		<input type="submit" name="submit" value="<?php echo _AM_CREATENEWCATEGORY;?>" />&nbsp;&nbsp;
		<input type="reset" value="<?php echo _AM_CLEAR;?>" />
		</td>
		</tr>
		</tr>
		</table></td></tr></table>
		<?php
	}
	break;
case 'catorder':
	$myts =& MyTextSanitizer::getInstance();
    xoops_cp_header();
	include("adminmenu.php");
    echo"&nbsp;<table width='100%' border='0' cellspacing='1' class='outer'>"
	."<tr><td class=\"odd\">";
    //    update catagories set cat_order = cat_order + 1 WHERE cat_order >= 2; update catagories set cat_order = cat_order - 2 where cat_id = 3;
	if ( isset($up) && $up != "" ) {
		if ( $current_order > 1 ) {
			$order = $current_order - 1;
			$sql1 = "UPDATE ".$xoopsDB->prefix("popnupblog_categories")." SET cat_order = $order WHERE cat_id = $cat_id";
			if ( !$r = $xoopsDB->query($sql1) ) {
				echo"</td></tr></table>";
				xoops_cp_footer();
				exit();
			}
			$sql2 = "UPDATE ".$xoopsDB->prefix("popnupblog_categories")." SET cat_order = $current_order WHERE cat_id = $last_id";
			if ( !$r = $xoopsDB->query($sql2) ) {
				echo"</td></tr></table>";
				xoops_cp_footer();
				exit();
			}
			echo "<div>"._AM_CATEGORYMOVEUP."</div><br />";
		} else {
			echo "<div>"._AM_TCIATHU."</div><br />";
		}
	} else if ( isset($down) && $down != "" ) {
		$sql = "SELECT cat_order FROM ".$xoopsDB->prefix("popnupblog_categories")." ORDER BY cat_order DESC";
		if ( !$r  = $xoopsDB->query($sql,1,0) ) {
			echo"</td></tr></table>";
			xoops_cp_footer();
			exit();
		}
		list($last_number) = $xoopsDB->fetchRow($r);
		if ( $last_number != $current_order ) {
			$order = $current_order + 1;
			$sql = "UPDATE ".$xoopsDB->prefix("popnupblog_categories")." SET cat_order = $current_order WHERE cat_order = $order";
			if ( !$r  = $xoopsDB->query($sql) ) {
				echo"</td></tr></table>";
				xoops_cp_footer();
				exit();
			}
			$sql = "UPDATE ".$xoopsDB->prefix("popnupblog_categories")." SET cat_order = $order where cat_id = $cat_id";
			if ( !$r  = $xoopsDB->query($sql) ) {
				echo"</td></tr></table>";
				xoops_cp_footer();
				exit();
			}
			echo "<div>"._AM_CATEGORYMOVEDOWN."</div><br />";
		} else {
			echo "<div>"._AM_TCIATLD."</div><br />";
		}
	}
	?>
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method='post'>
    <table border="0" cellpadding="1" cellspacing="0" align='center' Valign="TOP" width="95%"><tr><td class='bg2'>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr class='bg3' align='left'>
    <td align='center' colspan="3"><span class='fg2'><b><?php echo _AM_SETCATEGORYORDER;?></b></span><br />
    <?php echo _AM_TODHITOTCWDOTIP;?><br />
    <?php echo _AM_ECWMTCPUODITO;?></td>
    </tr>
    <tr class='bg3' align='center'>
    <td><?php echo _AM_CATEGORY1;?></td><td><?php echo _AM_MOVEUP;?></td><td><?php echo _AM_MOVEDOWN;?></td>
    </tr>
	<?php
    $sql = "SELECT * FROM ".$xoopsDB->prefix("popnupblog_categories")." ORDER BY cat_order";
	if ( !$r = $xoopsDB->query($sql) ) {
		exit();
	}
	while ( $m = $xoopsDB->fetchArray($r) ) {
		echo "<!-- New Row -->\n";
		echo "<form action=\"".$_SERVER['PHP_SELF']."\" METHOD=\"POST\">\n";
		echo "<tr class='bg1' align='center'>\n";
		echo "<td>".$myts->makeTboxData4Show($m['cat_title'])."</td>\n";
		echo "<td><input type=\"hidden\" name=\"mode\" value=\"$mode\">\n";
		echo "<input type=\"hidden\" name=\"cat_id\" value=\"".$m['cat_id']."\">\n";
		echo "<input type=\"hidden\" name=\"last_id\" value=\"";
		if ( isset($last_id) ) {
			echo $last_id;
		}
		echo "\">\n";
		echo "<input type=\"hidden\" name=\"current_order\" value=\"".$m['cat_order']."\"><input type=\"submit\" name=\"up\" value=\""._AM_MOVEUP."\"></td>\n";
		echo "<td><input type=\"submit\" name=\"down\" value=\""._AM_MOVEDOWN."\"></td></tr></form>\n<!-- End of Row -->\n";
		$last_id = $m['cat_id'];
	}
	?>
    </TABLE></TABLE>
	<?php
	break;
case 'sync':
	if ( $submit ) {
		flush();
		sync(null, "all forums");
		flush();
		sync(null, "all topics");
		redirect_header("./index.php", 1, _AM_SYNCHING);
	} else {
		xoops_cp_header();
		include("adminmenu.php");
		echo"&nbsp;<table width='100%' border='0' cellspacing='1' class='outer'>"
		."<tr><td class=\"odd\">";
		?>
		<table border="0" cellpadding="1" cellspacing="0" align="center" width="95%"><tr><td class='bg2'>
		<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr class='bg3' align='left'>
		<td><?php echo _AM_CLICKBELOWSYNC;?></td>
		</tr>
		<tr class='bg1' align='center'>
		<td><form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		<input type="hidden" name="mode" value="<?php echo $mode?>"><input type="submit" name="submit" value="<?php echo _AM_SYNCFORUM;?>"></form></td>
		</td>
		</tr>
		</table>
		</td></tr></table>
		<?php
	}
	break;
}

echo"</td></tr></table>";
xoops_cp_footer();
?>
