<?php
// $Id: mb_wordwrap.php,v 1.0.0 2006/05/03 17:33:00 yoshis Exp $
//  ------------------------------------------------------------------------ //
//                    wordwrap for multi-byte strings                        //
//             Copyright (c) 2005 Yoshi Sakai @ Bluemoon inc.                //
//                     <http://www.bluemooninc.biz/>                         //
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
echo mb_wordwrap("‚ ‚¢‚¤‚¦‚¨",8,"<BR />",8);
//echo mb_substr("‚ ‚¢‚¤‚¦‚¨1234567890", 0, 4);
function mb_wordwrap($string, $length = 80, $break = "\n", $cut = 0){
	$lines = explode($break, $string);
	foreach ($lines as $line) {
		$i = 0;
		$maxline = strlen($line);
		while ($i < $maxline) {
		echo "$i<BR />";
			$a = substr($line,$i);
			$len = strlen($a);
			$taglen = $tag = 0;
			$out="";
			for ($j=0 ; $j<$len ; $j++) {
				$chrs = substr($a, $j);
				$code = ord($chrs[$j]);
				if ($code=="<"){
					$tag++;
				}elseif ($code==">"){
					$tag--;
				}
				if ( $tag ){
					$out .= $chrs[$j];
				}else{
	    			if($code == 0x8f) { 		// ”¼Šp‚©‚È
						$out .= $chrs[$j] . $chrs[$j+1];
						$j+=2;
					} elseif ($code >= 0x80) { // ‘SŠp
						$out .= $chrs[$j];
						$j++;
					}
					$out .= $chrs[$j];
					$taglen++;
					if ($taglen>=$length) break;
				}
			}
			/*
			$spc = strrpos($out," ");
			if ($spc!=false){
				$out = substr($out, 0, $spc);
			}*/
			$i += strlen($out);
			$res[]=$out;
		}
	}
	return join($break, $res);
}
?>
