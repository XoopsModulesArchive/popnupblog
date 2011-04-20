<?php
// $Id: PopnupBlogPing2.php,v 3.00 2006/12/15 11:01:56 yoshis Exp $
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
// Original by Kousuke as simpleblog
if(!defined('XOOPS_ROOT_PATH') || !is_file(XOOPS_ROOT_PATH.'/class/snoopy.php')){
	exit();
}
require_once XOOPS_ROOT_PATH.'/class/snoopy.php';

class PopnupBlogPing2 {
	var $url;
	var $title;
	var $excerpt;
	var $blog_name;
	var $rss;
	var $timeout = 10000;
	
	var $DEBUG = false;
	
	function PopnupBlogPing2($rss, $url, $blog_name = null, $title = null, $excerpt = null){
		$this->rss = $rss;
		$this->url = $url;
		$this->blog_name = $blog_name;
		$this->title = $title;
		$this->excerpt = $excerpt;
	}
	
	function send(){
		// modified by kazy 2006.11.24
		global $BlogCNF;
		foreach($BlogCNF['update_ping'] as $item){
			$this->weblogUpdates_ping($item['url'], $item['charset']);
		}
	}

	
/*	
	function pingWeblogs($host, $port, $path, $encoding = 'UTF-8') {
		// original function by Dries Buytaert for Drupal
		$client = new xmlrpc_client($path, $host, $port);
		$resultFlg = true;
		$message = new xmlrpcmsg(
			"weblogUpdates.ping", 
			array(
				// new xmlrpcval(PopnupBlogUtils::convert_encoding($this->blog_name, _CHARSET, $encoding)), 
				new xmlrpcval($this->blog_name), 
				new xmlrpcval($this->url)
			)
		);
		ob_start();
		print_r($message);
		$log = ob_get_contents();
		ob_end_clean();
		log::addlog($log);
		// $message->encoding = $encoding;
		
		$log = 'pingWeblogs('.$host.':'.$port.$path.")\n";
		
		
		$result = $client->send($message);
		if (!$result) {
			if($this->DEBUG){
				$log .= 'pingWeblogs failed['.$client->errno.'] '.$client->errstring." ".$host.':'.$port.$path."\n";
			}
			$resultFlg = false;
		}else if($result->faultCode()) {
			// error_reporting(0);
			if($this->DEBUG)
				$log .= 'pingWeblogs failed['.$result->faultCode().'] '.$result->faultString()." ".$host.':'.$port.$path."\n";
				// trigger_error('pingWeblogs failed['.$result->faultCode().'] '.$result->faultString().$message->payload." ".$host.':'.$port.$path, E_USER_ERROR);
			$resultFlg = false;
		}else if($this->DEBUG){
			$log .= "request start  ======================\n";
			$log .= $client->raw_request."\n";
			$log .= "request end    ======================\n";
			$log .= "response start ======================\n";
			$log .= $result->raw_res."\n";
			$log .= "response end   ======================\n";
		}
		$log .= 'pingWeblogs('.$host.':'.$port.$path.") -> ".$resultFlg;
		
		// log::addlog($log);
		return $resultFlg;
	}
*/

	
	function weblogUpdates_ping($url, $encoding = 'UTF-8'){
		$param = array();
		$result = false;
		$title = $this->blog_name;
		$snoopy = new Snoopy();
		// $snoopy->_fp_timeout = $this->$timeout;
		$snoopy->set_submit_xml();
		$xml = '<?xml version="1.0" encoding="'.$encoding.'"?>'."\n";
		$xml .="<methodCall>\n";
		$xml .="<methodName>weblogUpdates.ping</methodName>\n";
		$xml .="<params>\n";
		$xml .="<param>\n";
		$xml .="  <value>".htmlspecialchars($this->blog_name)."</value>\n";
		$xml .="</param>\n";
		$xml .="<param>\n";
		$xml .="  <value>".htmlspecialchars($this->url)."</value>\n";
		$xml .="</param>\n";
		$xml .="</params>\n";
		$xml .="</methodCall>\n";
		
		$param[0] = PopnupBlogUtils::convert_encoding($xml, _CHARSET , $encoding);
		if($snoopy->submit ( $url, $param)){
			$result = true;
		}
		$log = formatTimestamp(mktime(), 'm');
		$log .= ' start weblogUpdates_ping('.$url.")========================\n";
		$log .= $xml."\n";
		$log .= "====================\n";
		$log .= $snoopy->results."\n";
		$log .= 'end weblogUpdates_ping('.$url.")========================\n";
		log::addlog($log);
		return $result;
	}

	function post_ping($url){
		$result = false;
		$param = array();
		$param['url'] = $this->url;
		if(!empty($this->blog_name)){
			$param['blog_name'] = $this->blog_name;
		}
		$param['title'] = (empty($this->title)) ? $this->url : $this->title;
		$snoopy = new Snoopy();
		$snoopy->_fp_timeout = $this->$timeout;
		$snoopy->set_submit_normal();
		if($snoopy->submit ( 'http://ping.myblog.jp/', $param)){
			$result = true;
		}
		if($this->DEBUG){
			print "post_ping(".$url.")\n";
			print "==================================>\n";
			print_r($param);
			print "\n";
			print "<==================================\n";
			print $snoopy->results."\n";
		}
		return $result;
	}
	
	
	function send_trackback_ping($trackback_url, $url, $title, $blog_name, $excerpt=null) {
		$query_string = 'url='.urlencode($url);
		if(!empty($title)){
			$query_string .= '&title='.urlencode($title);
		}
		if(!empty($blog_name)){
			$query_string .= '&blog_name='.urlencode($blog_name);
		}
		if(!empty($excerpt)){
			$query_string .= '&excerpt='.urlencode($excerpt);
		}
		
		
		/*
		if (strstr($trackback_url, '?')) {
			$trackback_url .= "&".$query_string;
			$fp = @fopen($trackback_url, 'r');
			$result = @fread($fp, 4096);
			@fclose($fp);
		} else {
		*/
			$trackback_url = parse_url($trackback_url);
			if(!array_key_exists('port', $trackback_url)){
				$trackback_url['port'] = 80;
			}
			$path = $trackback_url['path'];
			if(array_key_exists('query', $trackback_url)){
				$path .= "?".$trackback_url['query'];
			}
			$result = '';
			$http_request  = 'POST '.$path." HTTP/1.0\r\n";
			$http_request .= 'Host: '.$trackback_url['host']."\r\n";
			$http_request .= 'Content-Type: application/x-www-form-urlencoded'."\r\n";
			$http_request .= 'Content-Length: '.strlen($query_string)."\r\n";
			$http_request .= "\r\n";
			$http_request .= $query_string;
			$errNo = 0;
			$errStr = "";
			$fs = @fsockopen($trackback_url['host'], $trackback_url['port'], $errNo, $errStr, 10);
			@fputs($fs, $http_request);
			while($data = @fread($fs, 4096)){
				$result .= $data;
			}
			@fclose($fs);
			log::addlog("\n\n[send_trackback_ping]\n".$http_request."\n\n".$result);
		//}
		// return $result;
	}

}
?>
