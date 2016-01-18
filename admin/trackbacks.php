<?php
include_once '../../../include/cp_header.php';
if(
	(!defined('XOOPS_ROOT_PATH')) || 
	(!is_object($xoopsUser)) || 
	(!$xoopsUser->isAdmin()) ){
	exit();
}
include_once '../conf.php';
require_once '../pop.ini.php';


if(isset($_GET['mode'])){
     $mode = trim($_GET['mode']);
}elseif(isset($_POST['mode'])){
     $mode = trim($_POST['mode']);
}
switch($mode){
case 'delete':
  if(isset($_POST['item'])){
    $where = implode($_POST['item']," or tbid=");
    $sql = "DELETE FROM ".PBTBL_TRACKBACK." WHERE tbid=".$where;
    $xoopsDB->queryF($sql);
  }
  break;
case 'change':
  break;
}

// list
  $start = isset($_GET['start']) ? $_GET['start'] : 0;
  $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
  $limit = isset($_POST['limit']) ? $_POST['limit'] : 10;

  $sql = "SELECT t.tbid,t.blogid,t.postid,t_date,t.count,t.title as tb_title,t.url,i.title as blogname FROM "
    .PBTBL_TRACKBACK." as t "
      ." LEFT JOIN ".PBTBL_INFO." as i ON t.blogid=i.blogid ORDER BY t.tbid DESC LIMIT ".$start.", ".$limit;
  $result = $xoopsDB->query($sql);
  xoops_cp_header();
  include_once("./adminmenu.php");
  echo '&nbsp;<form action="'.$_SERVER['PHP_SELF'].'" method="post">';

  echo '<input type="edit" size="4" name="limit" id="limit" value="'.$limit.'" /> items/page <input type=submit />';
  // list
  echo '<table width="100%" border="0" class="outer">';
  echo '<tr><th>blog</th><th>tb_title</th><th>post time</th><th>count</th><th>url</th><th>delete</th></tr>';
  $count=0;
  while($item = $xoopsDB->fetchArray($result)){
    echo '<tr class="odd">';
    echo '<td><a href="../index.php?postid='.$item['postid'].'">';
    echo $item['blogname'].'</a></td><td>'.$item['tb_title'].'</td><td>'.$item['t_date'].'</td>';
    echo '<td>'.$item['count'].'</td><td><a href="'.$item['url'].'" target="_blank">visit</a></td>';
    echo '<td><input type="checkbox" name="item['.$count.']" id="item'.$count++.'" value="'.$item['tbid'].'" /></td>';
    echo '</tr>';
  }
  echo '</table>';
if($start > 0){
  $prev = $start-$limit;
  echo '<a href="'.$_SERVER['PHP_SELF'].'?start='.($prev>0 ? $prev : 0).'&limit='.$limit.'">back</a>';
}
if($count == $limit){
  $next = $start+$limit;
  echo '&nbsp;|&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?start='.$next.'&limit='.$limit.'">next</a>';
}
  echo '<div align="right"><select id="mode" name="mode"><option value="delete">Delete</option></select>';
  echo '<input type="submit" name="delete" />&nbsp;';
echo '<script language="JavaScript">function setAll(val){ var i=0; var c; while(c=document.getElementById("item"+i)){c.checked = val?"checked":""; i++;}return false;}</script>';
echo '<a onclick="if(event&&event.preventDefault)event.preventDefault(); setAll(1);" href="">Select All</a>';
echo '&nbsp;|&nbsp;<a onclick="if(event&&event.preventDefault)event.preventDefault(); setAll(0);" href="">Clear All</a>';
  echo '</div></form>';

echo '<td></tr></table>';

xoops_cp_footer();
?>
