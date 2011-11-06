<?php
// $Id$

include '../../mainfile.php';
include_once XOOPS_ROOT_PATH.'/modules/popnupblog/popnupblog.php';
include_once XOOPS_ROOT_PATH.'/modules/popnupblog/PopnupBlogUtils.php';

$ok = '<?xml version="1.0" encoding="iso-8859-1"?>'."\n<response><error>0</error><message>Ping saved successfully.</message></response>";
$failed = '<?xml version="1.0" encoding="iso-8859-1"?>'."\n<response><error>1</error><message>Ping Failed.</message></response>";
$failed1 = '<?xml version="1.0" encoding="iso-8859-1"?>'."\n<response><error>1</error><message>";
$failed2 = '</message></response>';
$result = '';
$errmes = '';

$params = PopnupBlogUtils::getDateFromHttpParams();
if($params['postid']){
	$blog = new PopnupBlog($params['blogid'],$params['postid']);
	if(!$blog->useTrackBack()){
		$errmes = 'This blog cannot recieve trackback';
		$result = $failed1.$errmes.$failed2;
	}elseif(!array_key_exists('title', $_POST) || empty($_POST['title'])){
		$errmes = 'need Title';
		$result = $failed1.$errmes.$failed2;
	//}elseif(preg_match("/^[[:print:][:cntrl:]]+$/",$_POST['excerpt'])){
    //  $errmes = "require non-ascii.";
    //  $result = $failed1.$errmes.$failed2;
    }else{
		$errmes .= $blog->recieve_trackback_ping($params);
		$result = $ok;
	}
}else{
	$errmes .= 'Invalid link(postid is not complete)';
	$result = $failed1.$errmes.$failed2;
}
$tblog = formatTimestamp(mktime(), 'm');
$tblog .= ' start trackback from '.$_SERVER["REMOTE_ADDR"]."=========================\n";
ob_start();
//print_r($_SERVER);
echo "HTTP_GET_VARS";
print_r($_GET);
echo "HTTP_POST_VARS";
print_r($_POST);
$tblog .= ob_get_contents();
ob_end_clean();
$tblog .= "========================================\n";
$tblog .= $result."\n";
$tblog .= 'end   trackback from '.$_SERVER["REMOTE_ADDR"]."=========================\n";PopnupBlogUtils::log($tblog);
// log
ob_start();
print_r($_SERVER);
print_r($_POST);
print_r($params);
print($errmes."\n");
$cnt = ob_get_contents();
ob_end_clean();
$fp = fopen( XOOPS_ROOT_PATH."/cache/php.log", "a");
fwrite($fp, $cnt."--\n".$_SERVER['HTTP_RAW_POST_DATA']."\n".$result."\n==============================\n");
fclose($fp);

// header('Content-Type:text/xml; charset=utf-8');
header('Content-Type: text/xml');
header('Content-Length: '.strlen($result));


echo($result);
?>
