<?PHP
// $Id$
//  ------------------------------------------------------------------------ //
//              CSV file to MySQL data transporter for PopnUpBlog            //
//                Copyright (c) 2006 Yoshi Sakai @ Bluemoon inc.             //
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
/* This script work as the plugin of PopnUpBlog.
** Those parameter come from pop.php
**   $text = Blog Body
**   $subject = Blog Title
**   $blogid = Blog id
**   $uid = user id
**   $now = blog date
**   $upfile = upload files (array parameter 'localname' , 'url' )
*/
//*****************************************************************************
//  DO IT YOURSELF: SET CSV FILE NAME,MYSQL TABLE AND PRIMARY KEY BELOW.
//*****************************************************************************
// For File to Table Map: You have to create table before run this program.
//  file = CSV filename , table = MySQL table name , pkey = PrimaryKey separate as | mark.
/*
[ SAMPLE HERE ]
	$imptable = array(
		array('file'=>"sample.csv",'table'=>$xoopsDB->prefix("csv_stock") , 'pkey'=>"key1|key2")
	);
*/
if (isset($upfile)){
	global $xoopsDB;
	$debug = 0;
	$imptable = array(
		array('file'=>"discount.csv",'table'=>$xoopsDB->prefix("exsale_discount") , 'pkey'=>"shipdate|invoice|Hbn_Cd|Quantity|uid",'updatelock'=>true),
		array('file'=>"expansion.csv",'table'=>$xoopsDB->prefix("exsale_expansion") , 'pkey'=>"ApplicationCode",'updatelock'=>false),
		array('file'=>"event.csv",'table'=>$xoopsDB->prefix("exsale_event") , 'pkey'=>"ApplicationCode",'updatelock'=>false),
		array('file'=>"eventplan.csv",'table'=>$xoopsDB->prefix("exsale_eventplan") , 'pkey'=>"ApplicationCode|ID",'updatelock'=>false),
		array('file'=>"eventresult.csv",'table'=>$xoopsDB->prefix("exsale_eventresult") , 'pkey'=>"ApplicationCode|ID",'updatelock'=>false)
	);
	$icount = $ucount = $igcount = 0;	// Insert,Update,Ignored counter
	$pluginmsg .= "\r\n";
	foreach($upfile as $key => $val){
		$tblname = "";
		foreach($imptable as $ikey => $ival){
			if (strpos($val['localname'],$ival['file'])){
				$tblname = $ival['table'];
				$primarykey = $ival['pkey'];
				$updatelock = $ival['updatelock'];
			}
		}
		if($tblname){
			$fname = $BlogCNF['uploads'].$val['localname'];
			if ($debug) echo "Open :" .$fname ."<BR />";
			$fp_csv = fopen($fname, "r");
			$i = 0;	
			$pkeys = explode("|",$primarykey);
			while(!feof($fp_csv)){
			    $csvline = fgets($fp_csv, 4096);
			    $csvline = mb_convert_encoding($csvline,"EUC-JP","SJIS");
				if ($i==0){
					$header = preg_replace("/\"/","",$csvline);
					$headers = array();
					foreach( explode(",", $header) as $tmp){
						$headers[] = $tmp;
					}
					// Add uid for Header
					if (preg_match("/uid/i",$primarykey)) {
						$headers[] = "uid";
						$header .= ",uid"; 
					}
					$cstr = preg_replace ("/,/","",$csvline);
					$colnum = strlen($csvline) - strlen($cstr);
				}elseif($csvline){
					$cstr = preg_replace ("/,/","",$csvline);
					$cnum = strlen($csvline) - strlen($cstr);
					if( $colnum > $cnum ){
						do {
							$chkend = fgets($fp_csv, 4096);
							$csvline .= $chkend;
							$cstr = preg_replace ("/,/","",$chkend);
							$cnum += strlen($chkend) - strlen($cstr);
						}while( $colnum > $cnum );
						//$pluginmsg .= substr($csvline ,0, 11) . $colnum . ":" . $cnum . "\r\n";
						//$pluginmsg .= mb_convert_encoding($csvline , "EUC-JP", "SJIS"). "\r\n";
					}
					while(strpos($csvline,",,")){
						$csvline = preg_replace("/,,/",",NULL,",$csvline);
					}
					$csvline = preg_replace("/,\r|,\n|,\r\n/",",NULL",$csvline);	// for end of csvline

					$sqldat = csv_parse($csvline);
					//
					// Seek primarykey record
					//
					$wstr = "";
					foreach($pkeys as $k){
						// For WHERE parameter
						if(strlen($wstr)>0) $wstr .= " and ";
						if($k=="uid")
							$wstr .= $k . "=" . $uid;
						else{
							$j = array_search($k,$headers);
							$wstr .= $k . "=" . $sqldat[$j];
						}
					}
					$sqlc = "SELECT sendDate FROM ${tblname} WHERE ${wstr}";
					if ($debug) echo $sqlc."<BR>";
					$res = $xoopsDB->query($sqlc);
					if ( !$xoopsDB->getRowsNum($res) ){
						// Insert record
						$j = 0;
						$istr = "";
						foreach($headers as $h){
							if(strlen($istr)>0) $istr .= ",";
							if($h=="uid")
								$istr .= $uid;
							else
								$istr .= $sqldat[$j];
							$j++;
						}
						$sql = "insert into ${tblname} (${header}) values (${istr})";
						echo $sql . "<BR>";
						if ($debug) sql_log($sql); 
						//$xoopsDB->query("SET NAMES SJIS;");
						$res = $xoopsDB->queryF($sql);
						if ($res) $icount++;
					}else{
						list($sendDate) = $xoopsDB->fetchRow($res);
						// Update record
						$j = 0;
						$wstr = $upstr = "";
						if ($debug) echo $primarykey;
						foreach($headers as $h){
							if (preg_match("/^(".$primarykey.")$/i",$h)) {
								// For WHERE parameter
								if(strlen($wstr)>0) $wstr .= " and ";
								if($h=="uid")
									$wstr .= $h . "=" . $uid;
								else
									$wstr .= $h . "=" . $sqldat[$j];
							}else{
								// For Update parameter
								if(strlen($upstr)>0) $upstr .= ",";
								$upstr .= $h . "=" . $sqldat[$j];
							}
							$j++;
						}
						$sqlu = "update ${tblname} set ${upstr} where ${wstr}";
						if ( preg_match("/DiscountAmount=\"0\"/",$upstr) )
							$OverWrite = true;
						else
							$OverWrite = false;
						if( $sendDate && $updatelock==TRUE && $OverWrite==false ){
							if ($debug) echo $upstr."<BR>";
							$pluginmsg .= "\r\n" . "[" . $sendDate . "]" . $sqlc . "\r\n" ;
							$igcount++;
						}else{
							if ($debug) sql_log($sqlu); 
							$res = $xoopsDB->queryF($sqlu);
							if ($res) $ucount++;
							else {
								$pluginmsg.= "error :" . $sqlu  . "\n";
								// For sql debug
								$pluginfp = fopen($log_dir . 'csv2sql.log', 'a');
								fwrite($pluginfp , $sql."\r\n");
								fwrite($pluginfp , $sqlu."\r\n");
								fclose($pluginfp);
							}
						}
					}
				}
				$i++;
			}
			fclose($fp_csv);
		}
	}
	if ($icount>0) $pluginmsg .= "\r\nInserted(" . $icount . ")";
	if ($ucount>0) $pluginmsg .= "\r\nUpdated(" . $ucount . ")";
	if ($igcount>0){
		 $pluginmsg .= "\r\nIgnored(" . $igcount . ")" .
		 mb_convert_encoding("件はサーバに登録済みです。再送の必要がある場合は経理に連絡下さい。" , "EUC-JP", "SJIS");
	}
	//$pluginmsg = mb_convert_encoding($pluginmsg, "EUC-JP", "SJIS");
	//if ($debug) echo $pluginmsg;
}
function csv_parse($csvline){
	/**
	 * CSV の1行をパースします。
	 * この関数が対応しているCSVの行形式は以下の通りです。
	 * ・区切りはカンマである。
	 * ・データにカンマを含む場合はダブルクオートで囲う。
	 * ・ダブルクオートで囲ったデータ中のダブルクオートはダブルクオート2回で置き換える。
	 * ・文字列中にCRLFが在ると ダブルクオート2回置換が入るので事前に<BR>に置換して後で戻す。
	 * @return CSV をパースした結果の配列
	 */
	$csvline = preg_replace ("/\r\n/","<BR>",$csvline);
	preg_match_all('/("[^"]*(?:""[^"]*)*"|[^,]*),?/', $csvline, $a);
	foreach($a[1] as $key => $value) {
		if(preg_match('/^"(.*)"$/', $value, $value2)) {
			$a[1][$key] = preg_replace('/""/', '"', $value2[1]);
		}
		if ( $a[1][$key] != "NULL" )
			$a[1][$key] = '"'. addslashes(mb_convert_encoding($a[1][$key],"SJIS","EUC-JP")) . '"';
	}
	//$sqldat = $a[1];
	return preg_replace ("/<BR>/","\r\n",$a[1]);
}
function sql_log($sql){
	global $denylog;
	// Save to file
	$fp = fopen("sql.log", "a+b");
	flock($fp, LOCK_EX);
	fwrite($fp, date("Y/m/d H:i:s ", time()) . "{$sql}\r\n");
	fclose($fp);
	return;
}
?>
