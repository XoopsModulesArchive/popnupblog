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
// For File to Table Map: You have to create tables before run this program.
// Then set parameter below.( file = CSV filename , table = MySQL table name , pkey = PrimaryKey separate as | mark.)
if (isset($upfile)){
	global $xoopsDB;
	$imptable = array(
	  array('file'=>"sample1.csv",'table'=>$xoopsDB->prefix("csv_stock1") , 'pkey'=>"key1|key2"),
	  array('file'=>"sample2.csv",'table'=>$xoopsDB->prefix("csv_stock2") , 'pkey'=>"key1|key2")
	);
	$icount = $ucount = 0;	// Insert and Update counter
	foreach($upfile as $key => $val){
		$tblname = "";
		foreach($imptable as $ikey => $ival){
			if (strpos($val['localname'],$ival['file'])){
				$tblname = $ival['table'];
				$primarykey = $ival['pkey'];
			}
		}
		if($tblname){
			$fname = $BlogCNF['uploads'].$val['localname'];
			if ($debug) echo "Open :" .$fname ."<BR />";
			$fp = fopen($fname, "r");
			$i = 0;	
			while(!feof($fp)){
			    $csvline = fgets($fp, 4096);
				if ($i==0){
					$header = preg_replace("/\"/","",$csvline);
					$headers = array();
					foreach( explode(",", $header) as $tmp){
						$headers[] = $tmp;
					}
				}elseif($csvline){
					while(strpos($csvline,",,")){
						$csvline = preg_replace("/,,/",",NULL,",$csvline);
					}
					$csvline = preg_replace("/,\r|,\n|,\r\n/",",NULL",$csvline);	// for end of csvline
					// Convert for Japanese str
					//if (function_exists('mb_convert_encoding')) $csvline = mb_convert_encoding($csvline, "EUC-JP", "SJIS");
					// Try insert first
					$sql = "insert into ${tblname} (${header}) values (${csvline})";
					$res = $xoopsDB->queryF($sql);
					if ($res){
						$icount++;
					}else{
						// try update when insert error occured
						/**
						 * CSV ��1�s���p�[�X���܂��B
						 * ���̊֐����Ή����Ă���CSV�̍s�`���͈ȉ��̒ʂ�ł��B
						 * �E��؂�̓J���}�ł���B
						 * �E�f�[�^�ɃJ���}���܂ޏꍇ�̓_�u���N�I�[�g�ň͂��B
						 * �E�_�u���N�I�[�g�ň͂����f�[�^���̃_�u���N�I�[�g�̓_�u���N�I�[�g2��Œu��������B
						 *
						 * @return CSV ���p�[�X�������ʂ̔z��
						 */
						preg_match_all('/("[^"]*(?:""[^"]*)*"|[^,]*),?/', $csvline, $a);
						foreach($a[1] as $key => $value) {
							if(preg_match('/^"(.*)"$/', $value, $value2)) {
								$a[1][$key] = preg_replace('/""/', '"', $value2[1]);
							}
							if ( $a[1][$key] != "NULL" )
								$a[1][$key] = '"'. $a[1][$key] . '"';
						}
						$sqldat = $a[1];
						$j = 0;
						$wstr = $upstr = "";
						echo $primarykey;
						foreach($headers as $h){
							if (preg_match("/^(".$primarykey.")$/i",$h)) {
								// For WHERE parameter
								if(strlen($wstr)>0) $wstr .= " and ";
								$wstr .= $h . "=" . $sqldat[$j];
							}else{
								// For Update parameter
								if(strlen($upstr)>0) $upstr .= ",";
								$upstr .= $h . "=" . $sqldat[$j];
							}
							$j++;
						}
						$sqlu = "update ${tblname} set ${upstr} where ${wstr}";
						$res = $xoopsDB->queryF($sqlu);
						if ($res) $ucount++;
						else $pluginmsg.= "error :" . $sql . "\n" . $sqlu . "\n";
					}
					//if ($debug) echo $sql;
				}
				$i++;
			}
			fclose($fp);
		}
	}
	if ($icount>0) $pluginmsg .= "Inserted(" . $icount . ")";
	if ($ucount>0) $pluginmsg .= "Updated(" . $ucount . ")";
	//if (debug) echo $pluginmsg;
}
?>
