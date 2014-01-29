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
 * grpmod.php
 * mod groups values
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

require("../config.php");
require("funct.php");
include("menu.php");

//delete group
if ( isset($_GET['delgrp']) )
{
    //remove group from grpsum
    $sqlh="DELETE FROM dmx_grpsum WHERE id='".$_GET['delgrp']."'";
    $sqlh=mysql_query($sqlh) or die(mysql_error());

    echo'<i>'.TXT_GROUP_DELETED.'</i>';
}

//all
if ( isset($_POST['allenabled']) )
{
	$sqlg="UPDATE dmx_grpsum SET disabled='0' WHERE id_schema='".$_GET[sch]."'";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//all
if ( isset($_POST['alldisabled']) )
{
	$sqlg="UPDATE dmx_grpsum SET disabled='1' WHERE id_schema='".$_GET[sch]."'";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//affiche les groups
echo'<div id="sequence"><table>';

	echo'<form action="grpmod.php?sch='.$_GET[sch].'" method="post">';

	echo'<tr>';
		echo'<td><b>'.TXT_GROUP.'</b></td>';
		echo'<td><b>'.TXT_ENABLED.'</b></td>';
	echo'</tr>';

	$sqlf="SELECT * FROM dmx_grpsum WHERE id_schema='".$_GET[sch]."' ORDER BY id";
	$sqlf=mysql_query($sqlf);
	$testf=mysql_num_rows($sqlf);

	//chg values for each group
	if ( isset($_POST['chgvalues']) )
	{
		//array values
		for ($j = 0; $j < $testf; $j++) {
			$sqlg="UPDATE dmx_grpsum SET group_name='".$_POST['group_name'][$j]."' WHERE id='".$_POST['id'][$j]."'";
			$sqlg=mysql_query($sqlg) or die(mysql_error());
			//echo'ok_';
		}

		//all disabled first
		$sqlg="UPDATE dmx_grpsum SET disabled='1' WHERE id_schema='".$_GET[sch]."'";
		$sqlg=mysql_query($sqlg) or die(mysql_error());

		if ( isset($_POST['enabled']) ){
			//this array values
			foreach($_POST['enabled'] as $val)
			{
				//echo $val,'<br />';
				$sqlg="UPDATE dmx_grpsum SET disabled='0' WHERE id='".$val."'";
				$sqlg=mysql_query($sqlg) or die(mysql_error());
				//echo'ok_';
			}
		}
	}



	//request again for refresh
	$sqlf="SELECT * FROM dmx_grpsum WHERE id_schema='".$_GET[sch]."' ORDER BY id";
	$sqlf=mysql_query($sqlf);
	while ($dataf=mysql_fetch_array($sqlf)){
		echo'<tr>';
			echo'<td><input name="group_name[]" value="'.$dataf[group_name].'" size="8"></td>';
			echo'<input name="id[]" value="'.$dataf[id].'" type="hidden">';
			//echo'<td><input name="disabled[]" value="'.$dataf[disabled].'" size="3"></td>';
			echo'<td><center><input type="checkbox" name="enabled[]" value="'.$dataf[id].'"'; if ($dataf[disabled]=='0'){echo' checked';} echo'></center></td>';

		    echo'<td><a href="grpmod.php?sch='.$_GET[sch].'&delgrp='.$dataf[id].'"';
		    echo" onclick=\"javascript:if(!confirm('DELETE GROUP: ".$dataf[group_name]." ?')) return false;\"";
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

