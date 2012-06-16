<?

/*
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Library General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 * chgall.php
 * requests to change values
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

		//chg all rgb values
		if ( isset($_POST['chgallval']) || isset($_POST['chgallvaljs_rgb']) || isset($_POST['chgallvaljs_cmy']) || isset($_POST['chgallvalmy']) )
		{

				if ( isset($_POST['chgallval']) )
				{
					$new_allch_value=$_POST['allch_value'];
				}
				elseif ( isset($_POST['chgallvaljs_rgb']) )
				{
					$new_allch_value=$_POST['allch_valuejs_rgb'];
				}
				elseif ( isset($_POST['chgallvaljs_cmy']) )
				{
					$new_allch_value=$_POST['allch_valuejs_cmy'];
				}
				elseif ( isset($_POST['chgallvalmy']) )
				{
					$new_allch_value=$_POST['allch_valuemy'];
				}

			//que les cases cochees
			if ( isset($_POST['chkbox']) ){

				//this array values
				foreach($_POST['chkbox'] as $val)
				{
					//echo $val,'<br />';
					$sqlg="UPDATE $table_dmx1 SET ch_value='".$new_allch_value."' WHERE id='".$val."'";
					$sqlg=mysql_query($sqlg) or die(mysql_error());
					//echo'ok_';
				}

			//sinon toutes les rgb
			}elseif ( ( isset($_POST['chgallvaljs_rgb']) || isset($_POST['chgallvalmy']) ) AND (!isset($_POST['colorstab'])) ){
				//array values
				for ($j = 0; $j < $testf; $j++) {
					$match=substr($_POST['ch_name'][$j], 0, 3);
					//echo"".$_POST['ch_name'][$j]." $match<br>";
					if ($match=='rgb'){
						$sqlg="UPDATE $table_dmx1 SET ch_value='".$new_allch_value."' WHERE id='".$_POST['ch_id'][$j]."'";
						$sqlg=mysql_query($sqlg) or die(mysql_error());
						//echo'ok_';
					}
				}

			//sinon toutes les cmy
			}elseif ( (isset($_POST['chgallvaljs_cmy'])) AND (!isset($_POST['colorstab'])) ){
				//array values
				for ($j = 0; $j < $testf; $j++) {
					$match=substr($_POST['ch_name'][$j], 0, 3);
					//echo"".$_POST['ch_name'][$j]." $match<br>";
					if ($match=='cmy'){
						$sqlg="UPDATE $table_dmx1 SET ch_value='".$new_allch_value."' WHERE id='".$_POST['ch_id'][$j]."'";
						$sqlg=mysql_query($sqlg) or die(mysql_error());
						//echo'ok_';
					}
				}

			//sinon toutes les rgb when no filter
			}elseif ( (isset($_POST['chgallval'])) AND (!isset($_POST['colorstab'])) AND $new_allch_value!="" AND (!isset($_SESSION['filter_exp'])) ){
				//array values
				for ($j = 0; $j < $testf; $j++) {
					$match=substr($_POST['ch_name'][$j], 0, 3);
					//echo"".$_POST['ch_name'][$j]." $match<br>";
					if ($match=='rgb'){
						$sqlg="UPDATE $table_dmx1 SET ch_value='".$new_allch_value."' WHERE id='".$_POST['ch_id'][$j]."'";
						$sqlg=mysql_query($sqlg) or die(mysql_error());
						//echo'ok_';
					}
				}

			//sinon toutes
			}elseif ( (isset($_POST['chgallval'])) AND (!isset($_POST['colorstab'])) AND $new_allch_value!="" ){
				//array values
				for ($j = 0; $j < $testf; $j++) {
					//$match=substr($_POST['ch_name'][$j], 0, 3);
					//echo"".$_POST['ch_name'][$j]." $match<br>";
					//if ($match==''){
						$sqlg="UPDATE $table_dmx1 SET ch_value='".$new_allch_value."' WHERE id='".$_POST['ch_id'][$j]."'";
						$sqlg=mysql_query($sqlg) or die(mysql_error());
						//echo'ok_';
					//}
				}
			}
		}

		//chg all rgb values random
		if ( isset($_POST['chgallrand']) )
		{
			//que les cases cochees
			if ( isset($_POST['chkbox']) ){

				//this array values
				foreach($_POST['chkbox'] as $val)
				{
					$rand1=rand(0,255);
					$rand2=rand(0,255);
					$rand3=rand(0,255);
					$random_value="$rand1.$rand2.$rand3";
					//echo $val,'<br />';
					$sqlg="UPDATE $table_dmx1 SET ch_value='".$random_value."' WHERE id='".$val."'";
					$sqlg=mysql_query($sqlg) or die(mysql_error());
					//echo'ok_';
				}

			//sinon toutes les rgb
			}elseif(!isset($_POST['colorstab'])){
				//array values
				for ($j = 0; $j < $testf; $j++) {
					$rand1=rand(0,255);
					$rand2=rand(0,255);
					$rand3=rand(0,255);
					$random_value="$rand1.$rand2.$rand3";
					$match=substr($_POST['ch_name'][$j], 0, 3);
					//echo"".$_POST['ch_name'][$j]." $match<br>";
					if ($match=='rgb'){
						$sqlg="UPDATE $table_dmx1 SET ch_value='".$random_value."' WHERE id='".$_POST['ch_id'][$j]."'";
						$sqlg=mysql_query($sqlg) or die(mysql_error());
						//echo'ok_';
					}
				}
			}
		}

		//chg all rgb values with fade
		if ( isset($_POST['chgallvalfade']) )
		{
				if ( isset($_POST['chkbox']) ){
					//this array values
					$p=0;
					foreach($_POST['chkbox'] as $val)
					{
						$p++;
					}
					$nb_rgb=$p;
				}

				//echo"_$nb_rgb";

				//get array to increment each channel
				$src_color=explode('.',$_POST['ch_value_src']);
				$dst_color=explode('.',$_POST['ch_value_dst']);

				//define variation (delta / nb)
				for($k=0; $k<3; $k++){
					$color_color[$k] = (( abs($dst_color[$k]-$src_color[$k]) ) / ($nb_rgb-1));
				}

			//que les cases cochees
			if ( isset($_POST['chkbox']) ){

				//this array values
		        $n=-1;
				foreach($_POST['chkbox'] as $val)
				{
					//echo $val,'<br />';
					//if ($match=='rgb'){
		            $n++; //echo"$n";

						for($k=0; $k<3; $k++){

		                    if ($_POST['offset']!=""){
		                        if ($n>$_POST['offset']){
		                            //echo"normal<br>";
								    //normal
								    if (($src_color[$k]-$dst_color[$k]) < 0){
		                				$src_color_new[$k] = $src_color[$k]+ ( ($n-$_POST['offset'])*$color_color[$k] );
								    }else{
		                				$src_color_new[$k] = $src_color[$k]- ( ($n-$_POST['offset'])*$color_color[$k] );
								    }
		                        }else{
		                            //echo"reverse<br>";
		                            //reverse
								    if (($src_color[$k]-$dst_color[$k]) < 0){
		                				$src_color_new[$k] = $src_color[$k]+ ( ($_POST['offset']-$n)*$color_color[$k] );
								    }else{
		                				$src_color_new[$k] = $src_color[$k]- ( ($_POST['offset']-$n)*$color_color[$k] );
								    }
		                        }
		                        //echo"n=$n,offset=$_POST['offset']<br>";
		                    }else{
								//define if increase or decrease
								if (($src_color[$k]-$dst_color[$k]) < 0){
		                				$src_color_new[$k] = $src_color[$k]+ ( ($n)*$color_color[$k] );
								}else{
		                				$src_color_new[$k] = $src_color[$k]- ( ($n)*$color_color[$k] );
								}
		                    }

							//not decimal
		    				$src_color_new[$k] = round($src_color_new[$k],0);
		                    //echo"$src_color[$k]";

							//get legal values
							if ($src_color_new[$k] > 255){
		    					$src_color_new[$k] = 255;
							}
							if ($src_color_new[$k] < 0){
		    					$src_color_new[$k] = 0;
							}
						}

						//concat
						$ch_value_new=$src_color_new[0];
						$ch_value_new.=".";
						$ch_value_new.=$src_color_new[1];
						$ch_value_new.=".";
						$ch_value_new.=$src_color_new[2];
						//echo"just end:$ch_value_new";

						$sqlg="UPDATE $table_dmx1 SET ch_value='".$ch_value_new."' WHERE id='".$val."'";
						$sqlg=mysql_query($sqlg) or die(mysql_error());
						//echo'ok_';
					//}
				}

			//sinon toutes les rgb
			}elseif(!isset($_POST['colorstab'])){
				//array values
		        $n=-1;
				for ($j = 0; $j < $testf; $j++) {
					$match=substr($_POST['ch_name'][$j], 0, 3);
					//echo"".$_POST['ch_name'][$j]." $match<br>";
					if ($match=='rgb'){
		            $n++;

						for($k=0; $k<3; $k++){

		                    if ($_POST['offset']!=""){
		                        if ($n>$_POST['offset']){
		                            //echo"normal<br>";
								    //normal
								    if (($src_color[$k]-$dst_color[$k]) < 0){
		                				$src_color_new[$k] = $src_color[$k]+ ( ($n-$_POST['offset'])*$color_color[$k] );
								    }else{
		                				$src_color_new[$k] = $src_color[$k]- ( ($n-$_POST['offset'])*$color_color[$k] );
								    }
		                        }else{
		                            //echo"reverse<br>";
		                            //reverse
								    if (($src_color[$k]-$dst_color[$k]) < 0){
		                				$src_color_new[$k] = $src_color[$k]+ ( ($_POST['offset']-$n)*$color_color[$k] );
								    }else{
		                				$src_color_new[$k] = $src_color[$k]- ( ($_POST['offset']-$n)*$color_color[$k] );
								    }
		                        }
		                        //echo"n=$n,offset=$_POST['offset']<br>";
		                    }else{
								//define if increase or decrease
								if (($src_color[$k]-$dst_color[$k]) < 0){
		                				$src_color_new[$k] = $src_color[$k]+ ( ($n)*$color_color[$k] );
								}else{
		                				$src_color_new[$k] = $src_color[$k]- ( ($n)*$color_color[$k] );
								}
		                    }

							//not decimal
		    				$src_color_new[$k] = round($src_color_new[$k],0);
		                    //echo"$src_color[$k]";

							//get legal values
							if ($src_color_new[$k] > 255){
		    					$src_color_new[$k] = 255;
							}
							if ($src_color_new[$k] < 0){
		    					$src_color_new[$k] = 0;
							}
						}

						//concat
						$ch_value_new=$src_color_new[0];
						$ch_value_new.=".";
						$ch_value_new.=$src_color_new[1];
						$ch_value_new.=".";
						$ch_value_new.=$src_color_new[2];
						//echo"just end:$ch_value_new";

						$sqlg="UPDATE $table_dmx1 SET ch_value='".$ch_value_new."' WHERE id='".$_POST['ch_id'][$j]."'";
						$sqlg=mysql_query($sqlg) or die(mysql_error());
						//echo'ok_';
					}
				}
			}// else checkbox
		}

?>
