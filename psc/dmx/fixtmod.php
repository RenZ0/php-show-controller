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
 * fixtmod.php
 * mod fixtures values
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

require("../config.php");
require("funct.php");
include("menu.php");

$delfixt=($_GET['delfixt']);

//delete fixture
if ( isset($_GET['delfixt']) )
{
    //remove fixture from fixture
    $sqlh="DELETE FROM dmx_fixture WHERE id=$delfixt";
    $sqlh=mysql_query($sqlh) or die(mysql_error());

    echo'<i>'.TXT_FIXTURE_DELETED.'</i>';
}

//all
if ( isset($_POST['allenabled']) )
{
	$sqlg="UPDATE dmx_fixture SET disabled='0'";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//all
if ( isset($_POST['alldisabled']) )
{
	$sqlg="UPDATE dmx_fixture SET disabled='1'";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//affiche les fixtures
echo'<div id="sequence"><table>';

	echo'<form action="fixtmod.php" method="post">';

	echo'<tr>';
		echo'<td><b>'.TXT_LOCATION.'</b></td>';
		echo'<td><b>'.TXT_SCHEMA.'</b></td>';
		echo'<td><b>'.TXT_PATCH.'</b></td>';
		echo'<td><b>'.TXT_AFTER.'</b></td>';
		echo'<td><b>'.TXT_UNIV.'</b></td>';
		echo'<td><b>'.TXT_ENABLED.'</b></td>';
	echo'</tr>';

	$sqlf="SELECT * FROM dmx_fixture ORDER BY id";
	$sqlf=mysql_query($sqlf);
	$testf=mysql_num_rows($sqlf);

	//chg values for each channel
	if ( isset($_POST['chgvalues']) )
	{
		//array values
		for ($j = 0; $j < $testf; $j++) {
			$sqlg="UPDATE dmx_fixture SET fixture_name='".$fixture_name[$j]."',id_schema='".$id_schema[$j]."',patch='".$patch[$j]."',patch_after='".$patch_after[$j]."',univ='".$univ[$j]."' WHERE id='".$id[$j]."'";
			$sqlg=mysql_query($sqlg) or die(mysql_error());
			//echo'ok_';
		}

		//all disabled first
		$sqlg="UPDATE dmx_fixture SET disabled='1'";
		$sqlg=mysql_query($sqlg) or die(mysql_error());

		if ( isset($_POST['enabled']) ){
			//this array values
			foreach($_POST['enabled'] as $val)
			{
				//echo $val,'<br />';
				$sqlg="UPDATE dmx_fixture SET disabled='0' WHERE id='".$val."'";
				$sqlg=mysql_query($sqlg) or die(mysql_error());
				//echo'ok_';
			}
		}
	}



	//request again for refresh
	$sqlf="SELECT * FROM dmx_fixture ORDER BY univ,patch,id";
	$sqlf=mysql_query($sqlf);
	while ($dataf=mysql_fetch_array($sqlf)){
		echo'<tr>';
			echo'<td><input name="fixture_name[]" value="'.$dataf[fixture_name].'" size="20"></td>';
			echo'<input name="id_schema[]" value="'.$dataf[id_schema].'" size="6" type="hidden">';

			echo'<td><select>';
			$sqli="SELECT * FROM dmx_schsum WHERE id=$dataf[id_schema]";
			$sqli=mysql_query($sqli);
			while ($datai=mysql_fetch_array($sqli)){
				//
				echo'<option>'.$datai[schema_name].'';
				//
			}
			echo'</select></td>';

			echo'<td><input name="patch[]" value="'.$dataf[patch].'" size="6"></td>';
			echo'<td><input name="patch_after[]" value="'.$dataf[patch_after].'" size="5"></td>';
			echo'<td><input name="univ[]" value="'.$dataf[univ].'" size="2"></td>';
			echo'<input name="id[]" value="'.$dataf[id].'" type="hidden">';
			//echo'<td><input name="disabled[]" value="'.$dataf[disabled].'" size="3"></td>';
			echo'<td><center><input type="checkbox" name="enabled[]" value="'.$dataf[id].'"'; if ($dataf[disabled]=='0'){echo' checked';} echo'></center></td>';

		    echo'<td><a href="fixtmod.php?delfixt='.$dataf[id].'"';
		    echo" onclick=\"javascript:if(!confirm('DELETE FIXTURE: ".$dataf[fixture_name]." ?')) return false;\"";
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

