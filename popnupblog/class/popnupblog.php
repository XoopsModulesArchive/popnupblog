<?php
// $Id: popnupblog.php,v 3.16 2007/11/09 16:36:56 yoshis Exp $
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
if(
	!defined('XOOPS_ROOT_PATH') || 
	!is_file(XOOPS_ROOT_PATH.'/modules/popnupblog/conf.php') ||
	!is_file(XOOPS_ROOT_PATH.'/class/snoopy.php') ||
	!is_file(XOOPS_ROOT_PATH.'/modules/popnupblog/class/PopnupBlogUtils.php')
){
	exit();
}
require_once XOOPS_ROOT_PATH.'/modules/popnupblog/conf.php';
require_once XOOPS_ROOT_PATH.'/class/snoopy.php';
require_once XOOPS_ROOT_PATH.'/modules/popnupblog/class/PopnupBlogUtils.php';
require_once XOOPS_ROOT_PATH.'/modules/popnupblog/class/sendmail.php';
require_once XOOPS_ROOT_PATH.'/modules/popnupblog/include/sanitize.php';

class PopnupBlog {
	var $VIEW_NUM = 20;
	
	var $user_list;
	var $blogid;
	var $blogUid;
	var $cat_id;
	var $targetUser;
	var $userHander;
	var $group_write = "";
	var $group_read = "";
	var $group_comment = "";
	var $group_vote = "";
	var $title = '';
	var $blog_desc = '';
	var $ts;
	var $xoopsDB;
	var $popnupblog_configs = array();
	var $plugin;
	var $pop_address;
	var $default_status;
	var $last_update;

	function PopnupBlog($blogid = -1, $postid = 0){
		global $xoopsUser, $xoopsDB;

		$this->xoopsDB =& $xoopsDB;
		$this->userHander = new XoopsUserHandler($this->xoopsDB);
		$this->ts =& MyTextSanitizer::getInstance();
		$this->user_list = array();
		if(!$blogid && $postid) $this->blogid = PopnupBlogUtils::get_blogid_from_postid($postid);
		if(!$this->blogUid) $this->loadBlogInfo($blogid);
		if($this->blogUid > 0){
			$this->targetUser = $this->userHander->get($this->blogUid);
//			if ($xoopsUser && $xoopsUser->isAdmin()) return;
//			$admin = 0;
//			if (!empty($xoopsUser)) $admin = $xoopsUser->isAdmin();
//			if( !$admin && (!$this->targetUser) || (!$this->targetUser->isActive())){
//				redirect_header(POPNUPBLOG_DIR,2,_MD_POPNUPBLOG_NORIGHTTOACCESS);
//				exit();
//			}
		}else{
			redirect_header(XOOPS_URL.'/',5,_MD_POPNUPBLOG_INTERNALERROR.' NoMatch blogid('.$blogid.')');
			exit();
		}
	}
	
	function getAllApplication(){
		global $xoopsDB;
		if(!$qResult = $xoopsDB->query('select uid, group_post, cat_id, title, blog_desc,group_read,group_comment,group_vote, UNIX_TIMESTAMP(create_date), email, emailalias from '.PBTBL_APPL.' order by create_date' )){
			return false;
		}
		$result = array();
		while(list($uid, $group_post, $cat_id, $title, $desc, $group_read,$group_comment,$group_vote, $create_date, $email, $emailalias) = $xoopsDB->fetchRow($qResult)){
			$result[] = array(
				'uid' => $uid, 
				'group_post' => $group_post, 
				'cat_id' => $cat_id, 
				'title' => $title, 
				'desc' => $desc, 
				'group_read' => $group_read, 
				'group_comment' => $group_comment, 
				'group_vote' => $group_vote, 
				'create_date' => $create_date,
				'email' => $email, 
				'emailalias' => $emailalias 
				);
		}
		return $result;
	}
	function getTargetUname(){
		return users::getUname($this->blogUid);
//		return $this->targetUser->uname();
	}
	
	function setBlogInfo( $cat_id=0,$title="",$desc="",$group_post=NULL,$group_read=NULL,$group_comment=NULL,$group_vote=NULL
		,$email='',$plugin='',$ml_function=0,$pop_server='',$pop_user='',$pop_password='',$pop_address='',$default_status=NULL){
		global $xoopsUser;
		$myts =& MyTextSanitizer::getInstance();
		$title = $myts->addSlashes( $myts->censorString( $title ) );
		$title = htmlspecialchars($title);
		$sql = 'UPDATE '.PBTBL_INFO.' set cat_id='.intval($cat_id);
		if (!is_null($group_post   )) $sql .= $group_post    ? ',group_post   =\''.$group_post   .'\'' : ',group_post   =NULL';
		if (!is_null($group_read   )) $sql .= $group_read    ? ',group_read   =\''.$group_read   .'\'' : ',group_read   =NULL';
		if (!is_null($group_comment)) $sql .= $group_comment ? ',group_comment=\''.$group_comment.'\'' : ',group_comment=NULL';
		if (!is_null($group_vote   )) $sql .= $group_vote    ? ',group_vote   =\''.$group_vote   .'\'' : ',group_vote   =NULL';
		$sql .= ',title=\''.PopnupBlogUtils::convert2sqlString($title)
			.'\',blog_desc=\''.PopnupBlogUtils::convert2sqlString($desc)
			.'\',email=\''.$email.'\'';
		if ($plugin      ) $sql .= ',plugin=\''.$plugin.'\'';
		$sql .= ',pop_server  =\''.$pop_server  .'\'';
		$sql .= ',pop_user    =\''.$pop_user    .'\'';
		$sql .= ',pop_password=\''.$pop_password.'\'';
		$sql .= ',pop_address =\''.$pop_address .'\'';
		if (isset($default_status)) $sql .= ',default_status ='.intval($default_status);
		$sql .= ',ml_function ='.intval($ml_function);
		$sql .= ' where blogid='.$this->blogid;
		$this->xoopsDB->queryF($sql);
	}
	
	function deleteAll(){
		$this->xoopsDB->queryF('delete from '.PBTBL_INFO.' where blogid = '.$this->blogid);
		$this->xoopsDB->queryF('delete from '.PBTBL_BLOG.' where blogid = '.$this->blogid);
		$this->xoopsDB->queryF('delete from '.PBTBL_COMMENT.' where blogid = '.$this->blogid);
		$this->xoopsDB->queryF('delete from '.PBTBL_TRACKBACK.' where blogid = '.$this->blogid);
		//  Modified by hoshiyan@hoshiba-farm.com 2004.8.5
		$this->xoopsDB->queryF('delete from '.PBTBL_EMAILALIAS.' where blogid = '.$this->blogid);
	}
	
	function loadBlogInfo( $blogid = 0 ){
		global $xoopsUser;

		if ($blogid==0) $blogid=$this->blogid;
		$sql = 'select uid,blogid,cat_id,group_post,group_read,group_comment,group_vote,title,blog_desc,plugin,pop_address,default_status,last_update from '
			.PBTBL_INFO.' where blogid='.$blogid;
		if(!$result = $this->xoopsDB->query($sql)){
			return false;
		}
		if(list($uid,$blogid,$cat_id,$group_post,$group_read,$group_comment,$group_vote,$title,$desc,$plugin,$pop_address,$default_status,$last_update)
			= $this->xoopsDB->fetchRow($result)){
			$this->blogUid = $uid;
			$this->blogid = $blogid;
			$this->cat_id = $cat_id;
			$this->group_write = $group_post;
			$this->group_read = $group_read;
			$this->group_comment = $group_comment;
			$this->group_vote = $group_vote;
			$this->title = $title;	//$this->ts->makeTareaData4Edit();
			$this->blog_desc = $desc;	//$this->ts->makeTareaData4Edit();
			$this->plugin = $plugin;
			$this->pop_address = $pop_address;
			$this->default_status = $default_status;
			$this->last_update = $last_update;
			return true;
		}
		return false;
	}
	function canWrite($blogid=0){
		global $xoopsUser;
		if($xoopsUser){
			if ( $xoopsUser->uid() == $this->blogUid ) return true;
		}
		if( $this->group_write=="" ) $this->loadBlogInfo($blogid);
		if ( $this->isPrivate($blogid) ) return true;
		$ret = grp_checkAccess($this->group_write);
		return $ret;
	}
	function canRead($blogid=0){
		global $xoopsUser;
		if($xoopsUser){
			if ( $xoopsUser->uid() == $this->blogUid ) return true;
		}
		if( $this->group_read=="" ) $this->loadBlogInfo($blogid);
		if ( $this->isPrivate($blogid) ) return true;
		$ret = grp_checkAccess($this->group_read);
		return $ret;
	}
	
	function canComment($blogid=0){
		global $xoopsUser;
		if( $this->group_comment=="" ) $this->loadBlogInfo($blogid);
		$ret = grp_checkAccess($this->group_comment);
		return $ret;
	}
	function canVote($blogid=0){
		global $xoopsUser;
		if( $this->group_vote=="" ) $this->loadBlogInfo($blogid);
		$ret = grp_checkAccess($this->group_vote);
		return $ret;
	}
	function isPrivate($blogid=0){
		$debug = 0;
		global $xoopsUser;
		if (!$xoopsUser) return false;
		if ( $xoopsUser->isAdmin() )  return true;
		$sql = 'select count(*) from '.PBTBL_EMAILALIAS.' where blogid = '.$blogid.' and uid='. $xoopsUser->uid();
		if ($debug>0) echo $sql;
		if(list($num) = $this->xoopsDB->fetchRow($this->xoopsDB->query($sql))){
			if ($num>0){ if ($debug>0) echo "true"; return true; }
		}
	}
	function isPublicBlog($blogid=0){
		if( $this->group_read=="" )  $this->loadBlogInfo($blogid);
		$grpsum = array_sum(grp_getGroupIda($this->group_read));
		if( $grpsum > 1 ){	// admin = not public
			return true;
		}
		return false;
	}
	
	function useTrackBack(){
		if(!array_key_exists('POPNUPBLOG_TRACKBACK', $this->popnupblog_configs)){
			$tb = PopnupBlogUtils::getXoopsModuleConfig('POPNUPBLOG_TRACKBACK');
			$this->popnupblog_configs['POPNUPBLOG_TRACKBACK'] = ($tb == 1) ? true : false;
		}
		return $this->popnupblog_configs['POPNUPBLOG_TRACKBACK'];
	}
	
	function useUpdatePing(){
		if(!array_key_exists('POPNUPBLOG_UPDATE_PING', $this->popnupblog_configs)){
			$conf = PopnupBlogUtils::getXoopsModuleConfig('POPNUPBLOG_UPDATE_PING');
			$this->popnupblog_configs['POPNUPBLOG_UPDATE_PING'] = ($conf == 1) ? true : false;
		}
		return $this->popnupblog_configs['POPNUPBLOG_UPDATE_PING'];
	}
	
	function getTitle(){
		if ($this->title=='') $this->loadBlogInfo();
		if ($this->title!='') return $this->title;
	}
	function getBlogdesc(){
		if($this->blog_desc=='') $this->loadBlogInfo();
		if($this->blog_desc != '') return $this->ts->makeTareaData4Show($this->blog_desc);
		return null;
	}
	function getBlogData($postid=0, $year=0, $month=0, $date=0, $command=0, $limit=0, $voteorder=0,$status=1){
		global $xoopsConfig,$xoopsUser,$weekday,$d_month;
		$debug = 0;

		if($limit == 0){
			$limit = POPNUPBLOG_VIEW_LIST_NUM;
		}
		$dateFormat = '%y/%m/%d';
		if(!$this->canRead($this->blogid)){
			redirect_header(XOOPS_URL.'/',1,_MD_POPNUPBLOG_NORIGHTTOACCESS);
			exit();
		}
		$addorder = '';
		if ($voteorder==1) $addorder = " votes_yes desc,";
		$sql_blog = '';
		$sql_select = 'select postid,uid,last_update, blog_date, title, post_text, votes_yes, votes_no, status from ';
		$tb = $this->useTrackBack();
		$sql_where = ' where status>='.$status;
		if ($postid > 0){
			$sql_where .= ' and postid='.$postid.' order by'.$addorder.' blog_date desc ';
		}else{
			if($this->blogid){
				$sql_where .= ' and blogid = '.$this->blogid;
			}
			if( ($year > 1000) && ($month > 0) ){
				if($date > 0){
				 	// display daily blog
				 	/*
					if ( $xoopsUser ) { 
						$userTZ = $xoopsUser->getVar("timezone_offset");
					} else { 
						$userTZ = $xoopsConfig['default_TZ'];
					}
					$user_time = strtotime($year.'-'.$month.'-'.$date.' '.$hours.':'.$minutes.':'.$seconds);
					$server_time = userTimeToServerTime($user_time,$userTZ);
					$year    = strftime("%Y", $server_time);
					$month   = strftime("%m", $server_time);
					$date    = strftime("%d", $server_time);
					*/
					$sql_where .= ' and DATE_FORMAT(blog_date, \'%Y\')='.$year.' and DATE_FORMAT(blog_date, \'%m\') = '.$month.' and DATE_FORMAT(blog_date, \'%d\') = '.$date.' order by'.$addorder.' blog_date desc limit '.$limit;
				}else{ // display month blog
					$sql_where .= ' and DATE_FORMAT(blog_date, \'%Y\')='.$year.' and DATE_FORMAT(blog_date, \'%m\') = '.$month.' order by'.$addorder.' blog_date desc limit '.$limit;
					$tb = false;
				}
			}else{
				// Show current blog
				$sql_where .= ' order by'.$addorder.' blog_date desc limit '.$limit;
				$tb = false;
			}
		}
		$sql_blog = $sql_select.PBTBL_BLOG.$sql_where;
		if ($debug) {
			echo $sql_blog."\n<br>";
		}
		if(!$result_blog = $this->xoopsDB->query($sql_blog)){
			return false;
		}
		$result = array();
		$i = 0;
		$cDate=0;
		$tzoffset = PopnupBlogUtils::is_tzoffset();
		while(list($postid,$uid,$last_update,$post_date, $result_title,$result_post_text,$votes_yes,$votes_no)
			= $this->xoopsDB->fetchRow($result_blog)){
			$poster = new XoopsUser( $uid ) ;
			$result['blog'][$i]['postid']  = $postid;
			$result['blog'][$i]['uid']  = $uid;
			$result['blog'][$i]['uname'] = users::getUname($uid);
			//$result['blog'][$i]['uname'] = $this->uname($uid);
			//
			// modified by hoshiyan@hoshiba-farm.com 2004.07.15
			//
			$user_time = strtotime($post_date) + $tzoffset;
			//$user_time = xoops_getUserTimestamp($server_time);
			if ($debug) {
				echo $result_title;
				echo $post_date;
				echo 'ServerTime = '.$server_time.'UserTime = '.$user_time;
			}
			$result['blog'][$i]['year']  = strftime("%Y", $user_time);
			$result['blog'][$i]['month'] = strftime("%m", $user_time);
			$result['blog'][$i]['date']  = strftime("%d", $user_time);
			setlocale(LC_ALL,'ja-JP');
			// Make a Date strings 2004.9.9 Yoshi.Sakai
			if (_MD_CAL_FORMAT=="Y-M-D"){		// ISO-8601
				$result['blog'][$i]['date_str']  =
					strftime("%Y", $user_time)._MD_CAL_PERMIT_Y.
					$d_month[date('m', $user_time)]._MD_CAL_PERMIT_M.
					strftime("%d", $user_time)._MD_CAL_PERMIT_D.
					"(".$weekday[date('w', $user_time)].")";
			} elseif(_MD_CAL_FORMAT=="M-D-Y") {	// US
				$result['blog'][$i]['date_str']  =
					$d_month[date('m', $user_time)]._MD_CAL_PERMIT_M.
					strftime("%d", $user_time)._MD_CAL_PERMIT_D.
					"(".$weekday[date('w', $user_time)].")"._MD_CAL_PERMIT_Y.
					strftime("%Y", $user_time);
			} elseif(_MD_CAL_FORMAT=="D-M-Y") {	// Other
				$result['blog'][$i]['date_str']  =
					strftime("%d", $user_time)._MD_CAL_PERMIT_D.
					$d_month[date('m', $user_time)]._MD_CAL_PERMIT_M.
					"(".$weekday[date('w', $user_time)].")"._MD_CAL_PERMIT_Y.
					strftime("%Y", $user_time);
			}else{
				strftime("%Y", $user_time)."/".
				strftime("%m", $user_time)."/".
				strftime("%d", $user_time);
			}
			if ($cDate!=strftime("%d", $user_time)){
				$result['blog'][$i]['hidedate']  = false;
				$cDate = $result['blog'][$i]['date'];
			}else{
				$result['blog'][$i]['hidedate']  = true;
			}
			$result['blog'][$i]['hours']  = strftime("%H", $user_time);
			$result['blog'][$i]['minutes']  = strftime("%M", $user_time);
			$result['blog'][$i]['seconds']  = strftime("%S", $user_time);
			$result['blog'][$i]['title'] = $result_title;
			$result['blog'][$i]['post_text'] = sanitize_blog($result_post_text,true,false,true);
			$result['blog'][$i]['text_edit'] = $this->ts->makeTareaData4Edit($result_post_text);
			$result['blog'][$i]['comments'] = pb_comment::getComments($postid);
			$result['blog'][$i]['url'] = XOOPS_URL."/modules/popnupblog/index.php?postid=".$result['blog'][$i]['postid'];
			/*
			$last_update = strtotime($last_update);
			$result['blog'][$i]['last_update_s'] = formatTimestamp($last_update, 's');
			$result['blog'][$i]['last_update_m'] = formatTimestamp($last_update, 'm');
			$result['blog'][$i]['last_update_l'] = formatTimestamp($last_update, 'l');
			*/
			$result['blog'][$i]['last_update4rss'] = PopnupBlogUtils::toRssDate(strtotime($post_date));
			$votes_all = $votes_yes + $votes_no;
			$result['blog'][$i]['votes_yes'] = $votes_yes;
			$result['blog'][$i]['votes_no']  = $votes_no;
			$result['blog'][$i]['votes_all'] = $votes_all;
			$result['blog'][$i]['votes_yes_par'] = $votes_yes ? intval($votes_yes/$votes_all*1000)/10 : 0;
			$result['blog'][$i]['votes_no_par']  = $votes_no  ? intval($votes_no/$votes_all*1000)/10  : 0;
			$result['blog'][$i]['votes_yes_pix'] = $votes_yes ? ($votes_yes/$votes_all*100)*2 : 0;
			$result['blog'][$i]['votes_no_pix']  = $votes_no  ? ($votes_no/$votes_all*100)*2  : 0;
			if($tb == true){
				$result['blog'][$i]['trackback_url'] = "index.php?trackback=".$result['blog'][$i]['postid'];
			}
			$result['blog'][$i]['trackbacks'] = $this->getTrackBack($result['blog'][$i]['postid']);
			$result['blog'][$i]['tb_count'] = count($result['blog'][$i]['trackbacks']);
			$i++;
		}
		$result['jump_url'] = $this->mk_jump_url($year,$month,$date);
		$user_time = xoops_getUserTimestamp(time());			//  $time = time();
		$result['today']['year'] = strftime("%Y", $user_time);          // date('Y',$time);
		$result['today']['month'] = strftime("%m", $user_time);         // date('m',$time);
		$result['today']['date'] = strftime("%d", $user_time);          // date('d',$time);
		$result['today']['hours'] = strftime("%H", $user_time);         // date('H',$time);
		$result['today']['minutes'] = strftime("%M", $user_time);       // date('i',$time);
		$result['today']['seconds'] = strftime("%S", $user_time);       // date('s',$time);
		//$result['blog_num'] = $i;
		//$result['user'] = $this->targetUser;
		//$result['uid'] = $this->blogUid;
		//$result['uname'] = users::getUname($this->blogUid);	//$this->targetUser->uname();
		if ($debug) {
			echo 'Today = '.$user_time;
			print_r($result);
		}
		return $result;
	}
	
	function mk_jump_url($year = 0, $month = 0, $date = 0){
		global $start;
		$sql_where = ' where blogid = '.$this->blogid;
		if( ($year > 1000) && ($month > 0) ){
			if($date > 0){
				$sql_where = $sql_where.' and DATE_FORMAT(blog_date, \'%Y\')='.$year.' and DATE_FORMAT(blog_date, \'%m\') = '.$month.' and DATE_FORMAT(blog_date, \'%d\') = '.$date;
			}else{ // display month blog
				$sql_where = $sql_where.' and DATE_FORMAT(blog_date, \'%Y\')='.$year.' and DATE_FORMAT(blog_date, \'%m\') = '.$month;
			}
		}
		$maxn = 10;
		$n = 1;
		$urls = '';
		$nurl = '';
		$sql = "select count(*) from ".PBTBL_BLOG.$sql_where;
		if(list($num) = $this->xoopsDB->fetchRow($this->xoopsDB->query($sql))){
			$s = substr($start,0,strpos($start,","));
			$top = $s-POPNUPBLOG_VIEW_LIST_NUM*$maxn < 0 ? 0 : $s-POPNUPBLOG_VIEW_LIST_NUM*$maxn;
			$end = $s+POPNUPBLOG_VIEW_LIST_NUM*$maxn > $num ? $num : $s+POPNUPBLOG_VIEW_LIST_NUM*$maxn;
			$n = intval($top / POPNUPBLOG_VIEW_LIST_NUM)+1;
			$pstart = $top>0 ? $top-POPNUPBLOG_VIEW_LIST_NUM.",".POPNUPBLOG_VIEW_LIST_NUM : 0;
			for ($i=$top;$i<$end;$i+=POPNUPBLOG_VIEW_LIST_NUM){
				$cstart=$i.",".POPNUPBLOG_VIEW_LIST_NUM;
				$nstart=$i+POPNUPBLOG_VIEW_LIST_NUM.",".POPNUPBLOG_VIEW_LIST_NUM;
				if ($start==$cstart){
					$nstr='('.$n.')';
				} else {
					$nurl='<a href="'.PopnupBlogUtils::createUrl($this->blogid,$year,$month,$date).'&start='.$nstart.'"><u>&raquo</u></a>&nbsp';
					$nstr=$n;
				}
				$urls = $urls.'<a href="'.PopnupBlogUtils::createUrl($this->blogid,$year,$month,$date).'&start='.$cstart.'">'.$nstr.'</a>&nbsp';
				$n++;
			}
			if ($pstart>0) $purl='<a href="'.PopnupBlogUtils::createUrl($this->blogid,$year,$month,$date).'&start='.$pstart.'"><u>&laquo</u></a>&nbsp';
			else $purl="";
			$urls = $purl.$urls.$nurl;
		}
		return $urls;
	}
	
	/*
	function hasBlog($dates){
		$sqlDate = $this->xoopsDB->quoteString($dates['year'].'-'.$dates['month'].'-'.$dates['date']);
		$sql = "select count(*) from ".PBTBL_BLOG." where uid = ".$this->blogUid.' and  blog_date = '.$sqlDate;
		if(!$result_select = $this->xoopsDB->query($sql)){
			if(list($num) = $this->xoopsDB->fetchRow($result_select)){
				if($num > 0){
					return true;
				}
			}
		}
		return false;
	}
	*/
	function getBlog1($postid=0){
		global $xoopsUser,$xoopsDB;
		$debug = 0;
		$sql = 'SELECT blog_count,uid,title,post_text,status,notifypub FROM '.PBTBL_BLOG.' WHERE postid='.$postid;
		if(!$result_select = $xoopsDB->query($sql)){
			return false;
		}
		$result = array();
		//
		// modified by hoshiyan@hoshiba-farm.com 2004.07.15
		/*
		$server_time = mktime($dates['hours'],$dates['minutes'],$dates['seconds'],$dates['month'],$dates['date'],$dates['year']);
		$user_time = xoops_getUserTimestamp($server_time);
		$result['year'] = strftime("%Y", $user_time);
		$result['month'] = strftime("%m", $user_time);
		$result['date'] = strftime("%d", $user_time);
		$result['hours'] = strftime("%H", $user_time);
		$result['minutes'] = strftime("%M", $user_time);
		$result['seconds'] = strftime("%S", $user_time);
		$usrDate =  formatTimestamp($server_time, 'mysql');
		*/
		if(list($blog_count,$uid,$title,$post_text,$status,$notifypub) = $xoopsDB->fetchRow($result_select)){
			$result['blog_count'] = $blog_count;
			$result['uid'] = $uid;
			$result['title'] = $title;
			$result['post_text'] = sanitize_blog($post_text,true,false,true);
			$myts =& MyTextSanitizer::getInstance();
			$result['text_edit'] = $myts->makeTareaData4Edit($post_text);
			$result['status'] = $status;
			$result['notifypub'] = $notifypub;
		}
		if ($debug) {
			print_r($dates);
			echo $server_time;
			echo $user_time;
			print_r($result);
		}
		return $result;
	}
	function deleteBlog1($postid=0){
		global $xoopsUser,$xoopsDB;
		if(!$xoopsUser->isadmin() && $xoopsUser->uid() <> $this->blogUid ) return false;
		if ($postid>0) $sql = sprintf("DELETE from %s where postid=%u", PBTBL_BLOG, $postid);
		$xoopsDB->queryF($sql);
		return true;
	}
	function last_blogcount($blogid){
		$sql = sprintf("SELECT blog_count from %s WHERE blogid=%u ORDER BY postid DESC limit 1;",PBTBL_BLOG, $blogid);
		$result_select = $this->xoopsDB->query($sql);
		if(list($blog_count) = $this->xoopsDB->fetchRow($result_select)){
			return $blog_count;
		}
		return 0;
	}
	function updateBlog(&$postid,$dates=0, $text, $title = '', $blogid='', $uid=0, $emailfrom='',$status=NULL,$notifypub=NULL){
		global $_POST,$xoopsConfig;

		if (!$dates)
			$sqlDate = date("Y-m-d H:i:s", time());
		else
			$sqlDate = $dates['year'].'-'.$dates['month'].'-'.$dates['date'].' '
				.$dates['hours'].':'.$dates['minutes'].':'.$dates['seconds'];
		$title = htmlspecialchars($title);
		$sqlText = PopnupBlogUtils::convert2sqlString($text);
		$sqlTitle = PopnupBlogUtils::convert2sqlString($title);
		if(!isset($status)) $status=$this->default_status;
		$sql = "";
		if ( empty($uid) ){
			$uid = $this->blogUid;			// modified by hoshiyan 2006.02.28
			if ( empty($uid) ){
				$this->loadBlogInfo($blogid);
				$uid = $this->blogUid;
			}					// ---
		}
		if(empty($text)){
			if ($postid>0) $sql = sprintf("delete from %s where postid=%u", PBTBL_BLOG, $postid);
			$sqlcmd = "DELETE";
		}else{
			$blog_count = popnupblog::last_blogcount($blogid) + 1 ;
			$inssql = sprintf("INSERT INTO %s(uid, blogid, blog_count, blog_date, title, post_text,status,notifypub) values(%u,%u,%u,'%s','%s','%s',%u,%u)"
				, PBTBL_BLOG, $uid, $blogid, $blog_count, $sqlDate, $sqlTitle, $sqlText,$status,$notifypub);
			if (!$postid){
				$sql = $inssql;
				$sqlcmd = "INSERT";
			}else{
				$sql = sprintf("select blogid,status,notifypub from %s where postid = %u",PBTBL_BLOG, $postid);
				if(!$result_select = $this->xoopsDB->query($sql)){
					return null;
				}
				if($this->xoopsDB->getRowsNum($result_select) == 0){
					$sql = $inssql;
					$sqlcmd = "INSERT";
				}else{
					$sql = $status ? 
						sprintf("UPDATE %s set title='%s', post_text='%s', status=status+%u where postid = %u"
						,PBTBL_BLOG,$sqlTitle,$sqlText,$status,$postid)
						:
						sprintf("UPDATE %s set title='%s', post_text='%s', status=%u where postid = %u"
						,PBTBL_BLOG,$sqlTitle,$sqlText,$status,$postid);
					list($blogid,$priv_status,$notifypub)=$this->xoopsDB->fetchRow($result_select);
					$sqlcmd = "UPDATE";
				}
			}
		}
		//$last_text = $sqlTitle."\r\n<hr>".$sqlText;
		//$last_text = ereg_replace("\r\n[\t ]+", " ",$sqlTitle." : ".$sqlText);
		//$last_text = mbstrings::_strcut($last_text,0,200);

		$this->xoopsDB->queryF($sql);
		if (!$postid) $postid = mysql_insert_id();
		$status = popnupblog::GetStatusByPostid($postid);
		log::addlog("updateBlog() POSTID:$postid UID:$uid BLOGID:$blogid STATUS:$status TITLE:$sqlTitle FROM:$emailfrom CMD:$sqlcmd");

		if(!empty($text) && $status>0){
			$this->update_info($blogid);	// get postid for url param. added by hoshiyan 2006.02.28
			$blogurl = PopnupBlogUtils::createUrlpostid($postid);
			$show_name = PopnupBlogUtils::getXoopsModuleConfig('show_name');
			if ( $show_name==1 ){
				$uname = users::realname($uid);
				if( empty($uname) ){
					$uname = users::uname($uid);
				}
			}else{
				$uname = users::uname($uid);
			}
			if (!$emailfrom) $emailfrom = users::email($uid);
			//
			// Send mail alias
			//
			if ($status==1){
				sendmail::send_mailalias($blogid,$blog_count,$emailfrom,$uname,$title,$this->getTitle(),$blogurl,$text,$this->pop_address);
			}
			//
			// Send Notify
			//
			if ($status==1){
				// for XOOPS event
				sendmail::xoops_notify('new_post',$blogid,$this->getTitle(),$blogurl,$title,$text);
				sendmail::xoops_notify('new_fullpost',$blogid,$this->getTitle(),$blogurl,$title,$text);
				if($notifypub==1 && !$this->default_status){
					$msgs = sprintf(_MD_POPNUPBLOG_NOTIFYPUB_DESC,"\n\n".$blogurl);
					$subj = sprintf(_MD_POPNUPBLOG_NOTIFYPUB,$xoopsConfig['sitename']);
					sendmail::notify($emailfrom,$xoopsConfig['adminmail'],$xoopsConfig['sitename'],$subj,$msgs);	// to writer
				}
			}
			//
			// Publish action
			//
			if( $this->isPublicBlog() ){
				if($this->useUpdatePing()){
					PopnupBlogUtils::weblogUpdatesPing(
						PopnupBlogUtils::createRssURL($blogid), 
						PopnupBlogUtils::createUrl($blogid), 
						$this->getTitle(), 
						$title);
				}
				if( (array_key_exists('trackback', $_POST)) && 
					(!empty( $_POST['trackback'] ))
				){
					//$tb_text = $this->ts->makeTareaData4Show($sqlText);
					$tb_text = sanitize_blog($sql_text,true,false,true);
					$tb_text = mbstrings::_strcut($tb_text,0,255);
					PopnupBlogUtils::send_trackback_ping(
						trim($_POST['trackback']), $blogurl,$title,$this->getTitle(),$tb_text);
				}
			}
			return true;
		}
		if (!empty($text))
			return $status;
	}
	function postInfo($blogid,$blog_count){
		global $xoopsDB;
		$sql = 'SELECT postid,title FROM '.PBTBL_BLOG.' WHERE blogid='.$blogid.' and blog_count='.$blog_count ;
		$myrow = $xoopsDB->fetcharray($xoopsDB->query($sql));
		return $myrow;
	}	
	function GetStatusByPostid($postid){
		global $xoopsDB;
		$sql = 'select status from '.PBTBL_BLOG.' WHERE postid='.$postid;
		list($status) = $xoopsDB->fetchRow($xoopsDB->query($sql));
		return $status;
	}
	function update_info($blogid){
		$sql = sprintf("UPDATE %s set last_update = CURRENT_TIMESTAMP() where blogid = %u",
				PBTBL_INFO,$blogid );
		$this->xoopsDB->queryF($sql);
	}

	function escapeHtml($text){
		$result = $text;
		// $result = ereg_replace('&', '&amp;', $text);
		$result = ereg_replace('<', '&lt;', $result);
		$result = ereg_replace('>', '&gt;', $result);
		// $result = ereg_replace('\'', '&apos;', $result);
		$result = ereg_replace('"', '&quot;', $result);
		//$result = ereg_replace('\r\n', '\n', $result);
		//$result = ereg_replace('\r', '\n', $result);
		//$result = ereg_replace('\n', '<br />', $result);
		return $result;
	}
	
	function getBlogIndex(){
		global $xoopsUser;
		$sql = 'select distinct DATE_FORMAT(blog_date, \'%Y\') year, DATE_FORMAT(blog_date, \'%m\') month from '.PBTBL_BLOG.' where blogid = '.$this->blogid.' and blog_date != \'0000-00-00\' order by year desc, month';
		if(!$result_select = $this->xoopsDB->query($sql)){
			return false;
		}
		$result = array();
		while(list($year, $month) = $this->xoopsDB->fetchRow($result_select)){
			// $result[$year][$month] = $month;
			$result[$year][$month]['month'] = $month;
			$result[$year][$month]['url'] = PopnupBlogUtils::createUrl($this->blogid, $year, $month);
		}
		return $result;
	}

	function recieve_trackback_ping($params){
		global $_POST, $_GET;
		$referer = null;
		$title = null;
		$tb = $this->useTrackBack();
		if(($tb == true) && array_key_exists('url', $_POST) && !empty($_POST['url'])){
				$referer = trim($_POST['url']);
				$title = array_key_exists('title', $_POST) ? PopnupBlogUtils::convert_encoding(trim($_POST['title']),'auto', _CHARSET) : null;
		}elseif(($tb == true) && array_key_exists('url', $_GET) && !empty($_GET['url'])){
			$referer = trim($_GET['url']);
			$title = array_key_exists('blog_name', $_GET) ? PopnupBlogUtils::convert_encoding(trim($_GET['blog_name']),'auto', _CHARSET).'&nbsp;/&nbsp;' : null;
			$title .= array_key_exists('title', $_GET) ? PopnupBlogUtils::convert_encoding(trim($_GET['title']),'auto', _CHARSET) : null;
		}elseif(array_key_exists('HTTP_REFERER', $_SERVER)){
			$referer = trim($_SERVER['HTTP_REFERER']);
			if( (empty($referer)) || (preg_match('/^'.ereg_replace('/', '\\/', XOOPS_URL).'*/', $referer)) ){
				return "same site";
			}
		}else{
			return "no args";
		}
		
		
//		if(PopnupBlogUtils::isCompleteDate($params)){
//			$targetDate = $params['year'].'-'.$params['month'].'-'.$params['date'].' '.$params['hours'].':'.$params['minutes'].':'.$params['seconds'];
//		}
		
		// get current date
		$sql = 'select postid from '.PBTBL_BLOG.' where postid = '.$params['postid'];
		if(!$result_select = $this->xoopsDB->query($sql)){
			return "sql error";
		}
		list($postid) = $this->xoopsDB->fetchRow($result_select);
		
		if(!empty($postid)){
			$this->incrementTrackBack($postid, $referer, $title);
//			if( (!empty($targetDate)) && ($current_date != $targetDate)){
//				$this->incrementTrackBack($targetDate, $referer, $title);
//			}
		}
		return "ok";
	}
	function incrementTrackBack($postid, $url, $title){
		$title  = $this->ts->addSlashes( $this->ts->censorString( $title ) );
		$url    = $this->ts->addSlashes( $this->ts->censorString( $url   ) );
		$postid = intval($postid);
		$t = empty($title) ? 'null' : $this->xoopsDB->quoteString($title);
		$u = $this->xoopsDB->quoteString($url);
		$update = "UPDATE ".PBTBL_TRACKBACK." set count = count+1, title = %s , t_date=CURRENT_TIMESTAMP() where postid = %u and url = %s";
		$this->xoopsDB->queryF(sprintf($update,  $t, $postid, $u));
		if($this->xoopsDB->getAffectedRows() == 0){
			$insert = "INSERT INTO ".PBTBL_TRACKBACK." (blogid, postid, t_date, count, title, url) VALUES (%u, %u, CURRENT_TIMESTAMP(), 1, %s, %s)";
			$sql=sprintf($insert, $this->blogid, $postid, $t, $u);
			$this->xoopsDB->queryF($sql);
		}
	}
	
	function getTrackBack($postid){
		global $tb_by_serchengine,$hide_referer;
		if ($hide_referer==1) return;
/*
		$sqlDate = $this->xoopsDB->quoteString($date['year']."-".$date['month']."-".$date['date'].' '.$date['hours'].':'.$date['minutes'].':'.$date['seconds']);
		$sql = 'select count, t_date, title, url from '.PBTBL_TRACKBACK.' where blogid = '.$this->blogid.' and t_date = '.$sqlDate.'  order by count desc';
*/
		$sql = 'select tbid, count, t_date, title, url, excerpt from '.PBTBL_TRACKBACK.' where postid = '.$postid.' order by count desc';
		if(!$result_select = $this->xoopsDB->query($sql)){
			return false;
		}
		$result = array();
		while(list($tbid, $count, $date, $title, $url, $excerpt) = $this->xoopsDB->fetchRow($result_select)){
			$t = array();
            $t['tbid'] = $tbid;
			$t['count'] = $count;
			$t['date'] = $date;
			$t['url'] = htmlspecialchars($url);
			$t['excerpt'] = htmlspecialchars($excerpt);
			if( preg_match('/http:\/\/\w+\.(\w+).*('.$tb_by_serchengine.')=([^&]+)/',$t['url'],$m)){
				if ( function_exists('mb_convert_encoding') ){
					$t['title'] = 'Search ' . $m[1] . ' : ' . mb_convert_encoding(urldecode($m[3]),_CHARSET,'auto');
				}else{
					$t['title'] = 'Search ' . $m[1] . ' : ' . urldecode($m[3]);
				}
			}else{
				if (function_exists('mb_convert_encoding') ){
					$t['title'] = htmlspecialchars( mb_convert_encoding( empty($title) ? $url : 'TrackBack : '.$title.'',_CHARSET,'auto') );
				}else{
					$t['title'] = htmlspecialchars( empty($title) ? $url : 'TrackBack : '.$title );
				}
			}
			$result[] = $t;
		}
		return $result;
	}
	
	// deprecated method 
	function getApplicationNum(){
		return PopnupBlogUtils::getApplicationNum();
	}


}
?>
