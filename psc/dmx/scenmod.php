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
 * scenmod.php
 * mod scenarios values
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

require("../config.php");
require("funct.php");
include("menu.php");

//delete scenari (in scensum, in scenseq and scenari channels)
if ( isset($_GET['delscen']) )
{
    //remove all values of scenari
    $sqlh="DELETE FROM dmx_scenari WHERE id_scenari='".$_GET['delscen']."'";
    $sqlh=mysql_query($sqlh) or die(mysql_error());

    //remove all steps of scenari
    $sqlh="DELETE FROM dmx_scenseq WHERE id_scenari='".$_GET['delscen']."'";
    $sqlh=mysql_query($sqlh) or die(mysql_error());

    //remove scenari from scensum
    $sqlh="DELETE FROM dmx_scensum WHERE id='".$_GET['delscen']."'";
    $sqlh=mysql_query($sqlh) or die(mysql_error());

    echo'<i>'.TXT_SCENARIO_DELETED.'</i>';
}

//all
if ( isset($_POST['allenabled']) )
{
	$sqlg="UPDATE dmx_scensum SET disabled='0'";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//all
if ( isset($_POST['alldisabled']) )
{
	$sqlg="UPDATE dmx_scensum SET disabled='1'";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//dupscen
if ( isset($_GET['dupscen']) )
{
	//regarde le scensum
	$sqlb="SELECT * FROM dmx_scensum WHERE id='".$_GET['dupscen']."' ORDER BY id";
	$sqlb=mysql_query($sqlb);
	while ($datab=mysql_fetch_array($sqlb)){
		//ajoute le meme
		$sqlc="INSERT INTO dmx_scensum VALUES('','$datab[scenari_name]_copie','$datab[id_fixture]','$datab[reverse]','$datab[disabled]')";
		$sqlc=mysql_query($sqlc) or die(mysql_error());
		//echo"<b>Scensum added</b>-";
	}

	$last_id=mysql_insert_id();

	//regarde le scenseq
	$sqlb="SELECT * FROM dmx_scenseq WHERE id_scenari='".$_GET['dupscen']."' ORDER BY id";
	$sqlb=mysql_query($sqlb);
	$p=0;
	while ($datab=mysql_fetch_array($sqlb)){
		//add step 0 (names)
		if ($p==0){
			//regarde le step content for this entry (id_scenari mandatory)
			$sqle="SELECT * FROM dmx_scenari WHERE id_scenari='".$_GET['dupscen']."' AND step=0 ORDER BY id";
			$sqle=mysql_query($sqle);
			while ($datae=mysql_fetch_array($sqle)){
				//ajoute le meme
				$sqlf="INSERT INTO dmx_scenari VALUES('','$last_id','$datae[ch_name]','$datae[ch_value]','0')";
				$sqlf=mysql_query($sqlf) or die(mysql_error());
				//echo"Step0-";
			}
		}
		$p++;

		//ajoute le meme, entry in seq
		$sqlc="INSERT INTO dmx_scenseq VALUES('','$last_id','$datab[stepname]','$datab[hold]','$datab[fade]','$datab[position]','$datab[disabled]')";
		$sqlc=mysql_query($sqlc) or die(mysql_error());
		//echo"<b>Scenseq$p</b>-";

		$last_step=mysql_insert_id();

		//regarde le step content for this entry (id_scenari optional)
		$sqle="SELECT * FROM dmx_scenari WHERE id_scenari='".$_GET['dupscen']."' AND step=$datab[id] ORDER BY id";
		$sqle=mysql_query($sqle);
		while ($datae=mysql_fetch_array($sqle)){
			//ajoute le meme
			$sqlf="INSERT INTO dmx_scenari VALUES('','$last_id','$datae[ch_name]','$datae[ch_value]','$last_step')";
			$sqlf=mysql_query($sqlf) or die(mysql_error());
			//echo"Step$p-";
		}
	}

echo'<i>'.TXT_SCENARIO_DUP.'</i><br>';
}

//affiche la sequence (steps)
echo'<div id="sequence"><table>';

	echo'<form action="scenmod.php" method="post">';

	echo'<tr>';
		echo'<td><b>'.TXT_SCENARIO.'</b></td>';
		echo'<td><b>'.TXT_FIXTURE.'</b></td>';
		echo'<td><b>'.TXT_REVERSE.'</b></td>';
		echo'<td><b>'.TXT_ENABLED.'</b></td>';
	echo'</tr>';

	$sqlf="SELECT * FROM dmx_scensum ORDER BY id";
	$sqlf=mysql_query($sqlf);
	$testf=mysql_num_rows($sqlf);

	//chg values for each channel
	if ( isset($_POST['chgvalues']) )
	{
		//array values
		for ($j = 0; $j < $testf; $j++) {
			$sqlg="UPDATE dmx_scensum SET scenari_name='".$_POST['scenari_name'][$j]."',id_fixture='".$_POST['id_fixture'][$j]."' WHERE id='".$_POST['id'][$j]."'";
			$sqlg=mysql_query($sqlg) or die(mysql_error());
			//echo'ok_';
		}

		//all disabled first
		$sqlg="UPDATE dmx_scensum SET disabled='1'";
		$sqlg=mysql_query($sqlg) or die(mysql_error());

		if ( isset($_POST['enabled']) ){
			//this array values
			foreach($_POST['enabled'] as $val)
			{
				//echo $val,'<br />';
				$sqlg="UPDATE dmx_scensum SET disabled='0' WHERE id='".$val."'";
				$sqlg=mysql_query($sqlg) or die(mysql_error());
				//echo'ok_';
			}
		}

		//all reverse off first
		$sqlg="UPDATE dmx_scensum SET reverse='0'";
		$sqlg=mysql_query($sqlg) or die(mysql_error());

		if ( isset($_POST['reverse']) ){
			//this array values
			foreach($_POST['reverse'] as $val)
			{
				//echo $val,'<br />';
				$sqlg="UPDATE dmx_scensum SET reverse='1' WHERE id='".$val."'";
				$sqlg=mysql_query($sqlg) or die(mysql_error());
				//echo'ok_';
			}
		}
	}



	//request again for refresh
	$sqlf="SELECT * FROM dmx_scensum ORDER BY id_fixture,scenari_name,id";
	$sqlf=mysql_query($sqlf);
	while ($dataf=mysql_fetch_array($sqlf)){
		echo'<tr>';
			echo'<td><input name="scenari_name[]" value="'.$dataf[scenari_name].'" size="25"></td>';
			//echo'<td><input name="id_fixture[]" value="'.$dataf[id_fixture].'" size="2"></td>';
			echo'<td>';
			echo'<select name="id_fixture[]">';

				//get schema of current fixture
				$sqlb="SELECT * FROM dmx_fixture WHERE id=$dataf[id_fixture]";
				$sqlb=mysql_query($sqlb);
				while ($datab=mysql_fetch_array($sqlb)){
					//list all fixtures which have the same schema
					$sqlc="SELECT * FROM dmx_fixture WHERE id_schema=$datab[id_schema] ORDER BY id";
					$sqlc=mysql_query($sqlc);
					while ($datac=mysql_fetch_array($sqlc)){

						echo"<option value=\"$datac[id]\" "; if ($dataf[id_fixture]==$datac[id]){echo"selected";} echo">";

						echo"---$datac[fixture_name]---";
					}
				}

			echo'</select>';
			echo'</td>';

			echo'<input name="id[]" value="'.$dataf[id].'" type="hidden">';

			echo'<td><center><input type="checkbox" name="reverse[]" value="'.$dataf[id].'"'; if ($dataf[reverse]=='1'){echo' checked';} echo'></center></td>';

			//echo'<td><input name="disabled[]" value="'.$dataf[disabled].'" size="3"></td>';
			echo'<td><center><input type="checkbox" name="enabled[]" value="'.$dataf[id].'"'; if ($dataf[disabled]=='0'){echo' checked';} echo'></center></td>';

		    echo'<td width="90"><a href="scenmod.php?dupscen='.$dataf[id].'"';
		    echo" onclick=\"javascript:if(!confirm('DUPLICATE SCENARIO: ".$dataf[scenari_name]." ?')) return false;\"";
		    echo'>'.TXT_DUPLICATE.'</a></td>';

		    echo'<td width="50"><a href="scenari.php?id='.$dataf[id].'">';
		    echo''.TXT_VIEW.'</a></td>';

		    echo'<td><a href="scenmod.php?delscen='.$dataf[id].'"';
		    echo" onclick=\"javascript:if(!confirm('DELETE SCENARIO: ".$dataf[scenari_name]." ?')) return false;\"";
		    echo'>'.TXT_DELETE.'</a></td>';
		echo'</tr>';
	}

echo'</table></div>';

echo''.TXT_SEP_VALUES.' <input type="submit" name="chgvalues" value="'.TXT_SAVE.'"><br><br>';

echo''.TXT_ALL.' : <input type="submit" name="allenabled" value="'.TXT_ENABLE.'">';
echo'<input type="submit" name="alldisabled" value="'.TXT_DISABLE.'"><br><br>';

echo'</form>';

//print_r($_POST);

?>

</body>

