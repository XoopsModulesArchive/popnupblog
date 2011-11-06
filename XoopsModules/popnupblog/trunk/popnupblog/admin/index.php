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
include '../../../include/cp_header.php';
if(
	(!defined('XOOPS_ROOT_PATH')) || 
	(!is_object($xoopsUser)) || 
	(!$xoopsUser->isAdmin()) ){
	exit();
}
include_once '../conf.php';
require_once XOOPS_ROOT_PATH.'/modules/popnupblog/class/popnupblog.php';
xoops_cp_header();
checkPermit();

include_once './adminmenu.php';

$member_handler =& xoops_gethandler('member');
$groupnames = $member_handler->getGroupList(); 
$categories = category::get_categories();
$alias_uid = $sends_uid = 0;
$alias_uname = isset($_POST['alias_uname']) ? $_POST['alias_uname'] : '';
$sends_uname = isset($_POST['sends_uname']) ? $_POST['sends_uname'] : '';
$alias_email = isset($_POST['alias_email']) ? $_POST['alias_email'] : '';
$sends_email = isset($_POST['sends_email']) ? $_POST['sends_email'] : '';
if ($alias_uname){ $alias_uid = isset($_POST['alias_uid']) ? intval($_POST['alias_uid']) : 0; }
if ($sends_uname){ $sends_uid = isset($_POST['sends_uid']) ? intval($_POST['sends_uid']) : 0; }
// updater check
/*
$updates = PopnupBlog::check_updater();
if($updates != false){
	if( !isset($updates['response']) || 
	    (empty($updates['response']))  ||
		($updates['response'] == $updates['local'])
	){
	}else{
//		echo " local_version='".$updates['local']."' new_version='".$updates['response']."'";
		echo "<center><h2><a href='http://sourceforge.jp/projects/xoops-modules/' target='_blank'>"._AM_POPNUPBLOG_NEW_VERSION.'('.$updates['response'].")</a></h2></center><br />\n";
	}
}
echo '<b><a href='.XOOPS_URL.'/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod='
	.$xoopsModule->getVar('mid').">"._AM_PREFERENCES."</a></b><P>";
*/
?>
	&nbsp;<br />
<?php
if ( isset($_POST['delete_ok']) ) {
	$targetUid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
	$targetBid = isset($_POST['bid']) ? intval($_POST['bid']) : 0;
	if($targetUid > 0){
		$blog = new PopnupBlog($targetBid);
		$blog->deleteAll();
	}
}
if ( isset($_POST['delete']) ) {
	$targetUid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
	$targetBid = isset($_POST['bid']) ? intval($_POST['bid']) : 0;
	echo "<table width='100%' border='0' cellspacing='1' class='outer'><form action='index.php' method='post'>";
	echo "<tr><td align='center'><h4>Delete blogid: ". $targetBid."</h4>";
	echo "<input type=hidden name=uid value=".$targetUid." />";
	echo "<input type=hidden name=bid value=".$targetBid." />";
	echo "<input type=submit name=Cancel value=Cancel />&nbsp<input type=submit name=delete_ok value=OK />";
	echo "</form></td></tr></table>";
	xoops_cp_footer();
	exit();
} 
if(	isset($_POST['edit']) || 
	isset($_POST['create']) || 
	isset($_POST['delete']) || 
	isset($_POST['add_alias']) || 
	isset($_POST['add_sends']) || 
	isset($_POST['reject']) ){
	if(!xoops_refcheck()){
		redirect_header(XOOPS_URL.'/modules/popnupblog/',2,'Referer Check Failed');
		exit();
	}
}
if(isset($_POST['edit'])) {
	$targetUid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
	$targetBid = isset($_POST['bid']) ? intval($_POST['bid']) : 0;
	$cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
	$group_post    = grp_saveAccess( isset($_POST['g_post'])    ? $_POST['g_post']    : "" );
	$group_read    = grp_saveAccess( isset($_POST['g_read'])    ? $_POST['g_read']    : "" );
	$group_comment = grp_saveAccess( isset($_POST['g_comment']) ? $_POST['g_comment'] : "" );
	$group_vote    = grp_saveAccess( isset($_POST['g_vote'])    ? $_POST['g_vote']    : "" );
	$title = isset($_POST['title']) ? ($_POST['title']) : "";
	$desc = isset($_POST['desc']) ? ($_POST['desc']) : "";
	$email = isset($_POST['email']) ? ($_POST['email']) : "";
	$plugin = isset($_POST['plugin']) ? ($_POST['plugin']) : "";
	$ml_function = isset($_POST['ml_function']) ? intval($_POST['ml_function']) : 0;
	$pop_server = isset($_POST['pop_server']) ? ($_POST['pop_server']) : "";
	$pop_user = isset($_POST['pop_user']) ? ($_POST['pop_user']) : "";
	$pop_password = isset($_POST['pop_password']) ? ($_POST['pop_password']) : "";
	$pop_address = isset($_POST['pop_address']) ? ($_POST['pop_address']) : "";
	$default_status = isset($_POST['default_status']) ? ($_POST['default_status']) : 0;
	//      Modified by hoshiyan@hoshiba-farm.com 2004.8.4
	$emailalias = isset($_POST['emailalias']) ? ($_POST['emailalias']) : "";
	$emailsends = isset($_POST['emailsends']) ? ($_POST['emailsends']) : "";
	if($targetUid > 0){
		$blog = new PopnupBlog($targetBid);
		$blog->setBlogInfo(
			$cat_id,$title,$desc,$group_post,$group_read,$group_comment,$group_vote,$email,$plugin,
			$ml_function,$pop_server,$pop_user,$pop_password,$pop_address,$default_status
			);
		//emailalias::setEmailAliasInfo($targetBid,$emailalias);
	}
}elseif(isset($_POST['create'])) {
	$targetUid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
	$cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
	$group_post    = grp_saveAccess( isset($_POST['g_post'])    ? $_POST['g_post']    : "" );
	$group_read    = grp_saveAccess( isset($_POST['g_read'])    ? $_POST['g_read']    : "" );
	$group_comment = grp_saveAccess( isset($_POST['g_comment']) ? $_POST['g_comment'] : "" );
	$group_vote    = grp_saveAccess( isset($_POST['g_vote'])    ? $_POST['g_vote']    : "" );
	$title = isset($_POST['title']) ? ($_POST['title']) : "";
	$desc = isset($_POST['desc']) ? ($_POST['desc']) : "";
	$email = isset($_POST['email']) ? ($_POST['email']) : "";
	$plugin = isset($_POST['plugin']) ? ($_POST['plugin']) : "";
	$emailalias = isset($_POST['emailalias']) ? ($_POST['emailalias']) : "";
	if($targetUid > 0){
		if(bloginfo::createNewBlogUser($targetUid,$group_post,$cat_id,$group_read,$group_comment,$group_vote, $title, $desc, $email, $emailalias)){
			echo '<font color="red">success: create blog uid='.$targetUid.'</font>';
		}else{
			echo '<font color="red">failed: create blog uid='.$targetUid.'</font>';
		}
	}
}elseif(isset($_POST['delete'])) {
	$targetUid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
	$targetBid = isset($_POST['bid']) ? intval($_POST['bid']) : 0;
    if ( !empty( $ok ) ){
		if($targetUid > 0){
			$blog = new PopnupBlog($targetBid);
			$blog->deleteAll();
		}
    } 
}elseif(isset($_POST['reject'])) {
	$targetUid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
	if($targetUid > 0){
//		$blog = new PopnupBlog($targetBid);
		PopnupBlogUtils::deleteApplication($targetUid);
	}
}elseif ( isset($_POST['del_sends'])){
	// Add user(s) to the list for this forum.
	if ($_POST['emailsends']){
		$result = emailalias::_deletebylist($_POST["bid"],2,$_POST["emailsends"]);
	}
}elseif (isset($_POST['del_alias'])){
	// Add user(s) to the list for this forum.
	if ($_POST['emailalias']){
		$result = emailalias::_deletebylist($_POST["bid"],1,$_POST["emailalias"]);
	}
}elseif (isset($_POST['cnf_alias'])){
	$myrow = PopnupBlogUtils::find_uid_mail($_POST["alias_uname"]);
	$alias_uid = $myrow[0];
	$alias_email = $myrow[1];
}elseif (isset($_POST['add_alias'])){
	// Add user(s) to the list for this forum.
	$result = emailalias::_add($_POST["bid"],1,$alias_email,$alias_uid);
	if (!$result){ echo '<h1>'._AM_POPNUPBLOG_CANTADDEMAIL.'</h1>'; }
	$alias_uname = $alias_email = null;
}elseif (isset($_POST['cnf_sends'])){
	// Add user(s) to the list for this forum.
	$myrow = PopnupBlogUtils::find_uid_mail($_POST["sends_uname"]);
	$sends_uid = $myrow[0];
	$sends_email = $myrow[1];
}elseif (isset($_POST['add_sends'])){
	$result = emailalias::_add($_POST["bid"],2,$sends_email,$sends_uid);
	if (!$result){ echo '<h1>'._AM_POPNUPBLOG_CANTADDEMAIL.'</h1>'; }
	$sends_uname = $sends_email = null;
}
$appList = popnupblog::getAllApplication();
if(count($appList) > 0){
?>
<table width='100%' border='0' cellspacing='1' class='outer'>
<tr><td colspan="5" class="head" ><b><?php echo _AM_POPNUPBLOG_APPLICATED_USER; ?></b></td></tr>
		<tr class="head">
			<th width="5%">Edit</th>
			<th>Title<br>Category<br>Create date</th>
			<th colspan=2>Permission</th>
			<th width="20%">(uid)User<br>Group<br>Email</th>
		</tr>
<?php foreach($appList as $app){ ?>
		<tr><form method="post" action="index.php">
		<tr class="even">
		<td rowspan=4>
			<input type="submit" name="create" value="<?php echo _AM_POPNUPBLOG_ALLOW_APPLICATION; ?>"><p>
			<input type="submit" name="reject" value="<?php echo _AM_POPNUPBLOG_REJECT_APPLICATION; ?>" />
			<input type=hidden name=uid value='<?php echo $app['uid'] ?>' />
			<input type=hidden name=application value='application' />
		</td>
		<td><input type="text" name="title" value="<?php echo $app['title']; ?>" /></td>
		<td rowspan=3 colspan=2><?php
			echo _AM_READ. grp_listGroups($app['group_read'],"g_read[]");
			echo _AM_COMMENT. grp_listGroups($app['group_comment'],"g_comment[]");
			echo _AM_VOTE. grp_listGroups($app['group_vote'],"g_vote[]");
		?></td>
		<td rowspan=4>
			<?php echo "(".$app['uid'].") ".XoopsUser::getUnameFromId($app['uid']); ?>
			<br><?php echo _AM_POPNUPBLOG_POST. grp_listGroups($app['group_post'],"g_post[]"); ?>
			<input type="text" name="email" value="<?php echo  $app['email'];?>" />
			<input type="text" name="emailalias" value="<?php echo  $app['emailalias'];?>" />
		</td>
		</tr>
		<tr class="even">
		<td><span class='fg2'><?php echo _AM_CATEGORY;?></span>
			<?php
			if ( $categories ) {
				echo PopnupBlogUtils::mkselect('cat_id',$categories,$app['cat_id']);
			} else {
				echo "<select name='cat_id'><option value=\"0\">"._AM_NONE."</option>\n";
			}
			?>
    	   	</select>
		</td>
		</tr>
		<tr class="even"><td>
			<?php echo date("Y-m-d", $app['create_date']); ?>
		</td>
		</tr>
        <tr class="even"><td align="right">
			<span class='fg2'><?php echo _AM_BLOGDESCRIPTION;?></span>
		</td><td colspan=2>
			<TEXTAREA name='desc' ROWS="3" COLS="50" WRAP="VIRTUAL"><?php echo $app['desc']?></TEXTAREA></td>
		</td></tr>
		</form>
		</tr>
	<?php	} ?>
	</table><br />
	<?php
	}
	$bloguids = bloginfo::get_all_uids();
	if (!$bloguids){
		xoops_cp_footer();
		exit();
	}
	?>
	<table width='100%' border='0' cellspacing='1' class='outer'>
	<tr><td class="head" colspan="8"><b><?php echo _AM_POPNUPBLOG_EDIT; ?></b></td></tr>
	<tr><td valign="top" class='even' align='center' width='20%'>
		<?php
		$unames = users::get_unames();
		echo '<form action=' . $_SERVER['PHP_SELF'] . ' method="post">';
		echo '<select name="uid" size="10" style="width: 100px;">';
		if (!$bloguids){
			echo "</td></tr></table>";
			xoops_cp_footer();
			exit();
		}
		foreach($bloguids as $row){
			echo '<option value='.$row.'>';
			if (isset($unames[$row])) echo $unames[$row]; else echo "----";
			echo '</option>';
		}
		echo '</select>';
		?>
	</td><td colspan=3 class='odd' align='center'>
		<input type="submit" name="submit" value="<?php echo _AM_POPNUPBLOG_EDIT;?>" />
		</form>
	</td></tr>
	<tr class='bg1' align="left">
		<th><?php echo "("._AM_POPNUPBLOG_UID.")"._AM_POPNUPBLOG_NAME; ?></th>
		<th colspan=2><?php echo _AM_POPNUPBLOG_LASTUPDATE; ?></th>
	</tr>
<?php
	$targetUid = isset($_POST['uid']) ? intval($_POST['uid']) : 0;
	if ($targetUid==0){
		echo"</td></tr></table>";
		xoops_cp_footer();
		exit();
	}
	$users = array();
	$users = PopnupBlogUtils::getBlogInfo($targetUid);
	if(count($users) > 0){
		$userHander = new XoopsUserHandler($xoopsDB);
		foreach ( $users as $user ) {
			$user['user'] = $userHander->get($user['uid']);	?>
		<tr><form method="post" action="index.php">
			<tr><td class="head" align='left'>
				(<?php echo $user['uid']; ?>)<input type=hidden name=uid value='<?php echo $user['uid'] ?>' />
				<?php if($user['user']) echo htmlspecialchars($user['user']->uname()); ?>
			</td><td class="head" colspan=2>
				<?php echo $user['lastUpdate']; ?>
			</td></tr>
			<tr><td class="even" align='right'>
				(BlogID)<?php echo _AM_POPNUPBLOG_BLOG_TITLE; ?>
			</td><td class="odd" colspan=2>
				(<?php echo $user['bid']; ?><input type=hidden name=bid value='<?php echo $user['bid'] ?>' />)
				<input type="text" name="title" value="<?php echo htmlspecialchars($user['title']); ?>" />
				&nbsp;<span class='fg2'><?php echo _AM_CATEGORY;?></span>
				<?php
				if ( $categories ) {
					echo PopnupBlogUtils::mkselect('cat_id',$categories,$user['cat_id']);
				} else {
					echo "<select name='cat_id'><option value=\"0\">"._AM_NONE."</option>\n";
				}
				?>
        		</select>
        		&nbsp;<?php echo _AM_POPNUPBLOG_PLUGIN; ?><input type="text" name="plugin" value="<?php echo htmlspecialchars($user['plugin']); ?>" />
			</td></tr>
			<tr><td class="even" align="right"><?php echo _AM_BLOGDESCRIPTION; ?></td>
			<td class="odd" colspan=2>
				<TEXTAREA name='desc' ROWS="2" COLS="100%" WRAP="VIRTUAL"><?php echo $user['desc'];?></TEXTAREA>
			</td></tr>
			<tr><td class="even" align='right'>
			<?php echo _AM_POPNUPBLOG_PERMISSION; ?></td>
			<td class="odd" colspan=2>
				<?php 
				echo _AM_POPNUPBLOG_POST. $user['g_post'];
				echo _AM_READ. $user['g_read'];
				echo _AM_COMMENT. $user['g_comment'];
				echo _AM_VOTE. $user['g_vote'];
			?>
			</td></tr>
			<tr><td class="even" align='right'><?php echo _AM_POPNUPBLOG_BLOG_EMAIL."1"; ?></td>
			<td class="odd" colspan=2>
			<input type="text" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" />
			<?php
			echo _AM_POPNUPBLOG_FORM_ADMIN_USAGE."<BR />"; 
			echo sprintf(_AM_POPNUPBLOG_MAILPRIFIX_DESC, $user['bid']);
			?>
			</td>
			</tr>
			<tr><td class="even" align='right'><?php echo _AM_POPNUPBLOG_BLOG_EMAIL."2"; ?></td>
			<td class="odd">
			<!--<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">-->
			<select name="emailalias[]" size="5" multiple='multiple' style="width: 250px;">
			<?php echo $user['emailalias_options']; ?>
			</select>
			<input type="hidden" name="bid" value="<?php echo $user['bid'] ?>" />
			<input type="hidden" name="pid" value="1" />
			<input type="hidden" name="uid" value="<?php echo $targetUid ?>" />
			<input type="hidden" name="op" value="del_alias" />
			<input type="submit" name="del_alias" value="<?php echo _AM_REMOVE;?>" />
			<!--</form>-->
			</td><td class="odd">
			<?php echo _AM_POPNUPBLOG_UNAME; ?>
			<input type="text" SIZE="8" name="alias_uname" value="<?php echo $alias_uname;?>"/>&nbsp;
			<input type="submit" name="cnf_alias" value="<?php echo _AM_CONFIRM;?>" />
			<input type="hidden" name="bid" value="<?php echo $user['bid'] ?>" />
			<input type="hidden" name="pid" value="1" />
			<input type="hidden" name="uid" value="<?php echo $targetUid ?>" />
			<br />email&nbsp;<input type="text" name="alias_email" value="<?php echo $alias_email;?>" />
			<input type="hidden" name="alias_uid" value="<?php echo $alias_uid;?>" />
			<input type="submit" name="add_alias" value="<?php echo _AM_ADD;?>" />
			</td></tr>
			<tr><td class="even" align='right'><?php echo _AM_POPNUPBLOG_SEND_EMAIL; ?></td>
			<td class="odd">
			<select name="emailsends[]" size="5" multiple='multiple' style="width: 250px;">
			<?php echo $user['emailsends_options']; ?>
			</select>
			<input type="hidden" name="bid" value="<?php echo $user['bid'] ?>" />
			<input type="hidden" name="pid" value="2" />
			<input type="hidden" name="uid" value="<?php echo $targetUid ?>" />
			<input type="submit" name="del_sends" value="<?php echo _AM_REMOVE;?>" />
			</td><td class="odd">
			<?php echo _AM_POPNUPBLOG_UNAME; ?>
			<input type="text" SIZE="8" name="sends_uname" value="<?php echo $sends_uname;?>"/>&nbsp;
			<input type="submit" name="cnf_sends" value="<?php echo _AM_CONFIRM;?>" />
			<input type="hidden" name="bid" value="<?php echo $user['bid'] ?>" />
			<input type="hidden" name="pid" value="2" />
			<input type="hidden" name="uid" value="<?php echo $targetUid ?>" />
			<br />email&nbsp;<input type="text" name="sends_email" value="<?php echo $sends_email;?>" />
			<input type="hidden" name="sends_uid" value="<?php echo $sends_uid;?>" />
			<input type="submit" name="add_sends" value="<?php echo _AM_ADD;?>" />
			</td></tr>
			<tr><td class="even" align='right'>
			<?php echo _AM_POPNUPBLOG_MLFUNCTION; ?></td>
			<td class="odd" colspan=2><?php 
			echo '<input type="checkbox" name="ml_function" value="1" '; if ($user['ml_function']) echo "checked";
			echo ' />'._AM_POPNUPBLOG_ACTIVATE  .'<BR />';
			echo '<input type="text"     name="pop_server"   value="'.$user['pop_server']   .'" />'._AM_POPNUPBLOG_POPSERVER  .'<BR />';
			echo '<input type="text"     name="pop_user"     value="'.$user['pop_user']     .'" />'._AM_POPNUPBLOG_POPUSER    .'<BR />';
			echo '<input type="password" name="pop_password" value="'.$user['pop_password'] .'" />'._AM_POPNUPBLOG_POPPASSWORD.'<BR />';
			echo '<input type="text"     name="pop_address"  value="'.$user['pop_address']  .'" />'._AM_POPNUPBLOG_POPADDRESS.'&nbsp;'._AM_POPNUPBLOG_ML_DESC.'<BR />';
			?></td></tr>
			<tr><td class="even" align="right"><?php echo _AM_AUTOAPPROVE; ?></td>
			<td class="odd" colspan=2>
			<?php
			if ($user['default_status']==1){
				$dy="CHECKED"; $dn="";
			}else{
				$dy=""; $dn="CHECKED";
			}
			echo '<input type="radio" name="default_status" value="1" '.$dy.'>' . _YES . '</input>';
			echo '<input type="radio" name="default_status" value="0" '.$dn.'>' . _NO  . '</input>';
			?>
			</td></tr>
			<td class="even" align="center" colspan=3>
				<?php echo _AM_POPNUPBLOG_OPERATION; ?>&nbsp;&nbsp;
				<input type=submit name='edit' value='edit' />&nbsp;&nbsp;
				<input type=submit name='delete' value='delete' />
			</td></tr>
			</form>
		</tr><?php
		}
	}	
?>
</table>


<br /><center>
<a href="http://sourceforge.jp/">
	<img src="http://sourceforge.jp/sflogo.php?group_id=757" width="96" height="31" border="0" alt="SourceForge.jp" target="_blank">
</a> 
<a href="http://feeds.archive.org/validator/check?url=<?php echo XOOPS_URL; ?>/modules/popnupblog/backend.php" target="_blank">
	<img src="<?php echo XOOPS_URL; ?>/modules/popnupblog/rss-valid.gif" border="0">
</a><br />
Created by <a href="http://www.bluemooninc.biz/" target="_blank">Bluemoon inc.</a>
</center>
<?php
xoops_cp_footer();

/*****************************************************************************
	Check Permission
*****************************************************************************/
function checkPermit(){
	global $xoopsModule;

	$modpath = XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname();
	$permit_err = array();
	$_check_list = array(
		XOOPS_ROOT_PATH.$GLOBALS['BlogCNF']['img_dir'],
		XOOPS_ROOT_PATH.$GLOBALS['BlogCNF']['thumb_dir'],
		$modpath."/log/"
		);

	if ($dir = @opendir($modpath."/log/")) {
		while($file = readdir($dir)) {
			if($file == ".." || $file == "."  || eregi("\.html$",$file) || eregi("^\.(.*)",$file)) continue;
			array_push($_check_list, $modpath."/log/".$file);
		}
		closedir($dir);
	}

	foreach($_check_list as $dir){
		if(!is_writable($dir)){
			$permit_err[] = _AM_POPNUPBLOG_PERMITIONERR."=> ".$dir;
		}
	}

	$_alert_icon = "<img src='../images/alert.gif'>&nbsp;</img>";
//	$_alert_icon = "<img src='../caution.gif' height='15' width='50'>&nbsp;";
	foreach($permit_err as $er_msg){
//		echo "<img src='$_alert_icon' height='15' width='50'>&nbsp;$er_msg<br />";
		echo "$_alert_icon$er_msg<br />";
	}
}
?>
