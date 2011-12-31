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
 * prefs.php
 * preferences settings
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

require("../config.php");
require("funct.php");
include("menu.php");

echo'<br><b><a href="prefs.php">'.TXT_LINK_PREFS.'</a></b><br><br>';

//affiche les preferences
echo'<div id="sequence"><table cellpadding="5">';

	echo'<form action="prefs.php" method="post">';

	echo'<tr>';
		echo'<td><b>Language</b></td>';
		echo'<td><b>RGB</b></td>';
		echo'<td><b>CMY</b></td>';
	echo'</tr>';

	//$sqlf="SELECT * FROM dmx_preferences WHERE id=1";
	//$sqlf=mysql_query($sqlf);
	//$testf=mysql_num_rows($sqlf);

	//chg values for each preferences
	if ( isset($_POST['chgvalues']) )
	{
		//array values
		$sqlg="UPDATE dmx_preferences SET lang='".$language."',display_rgb='".$display_rgb."',display_cmy='".$display_cmy."' WHERE id='".$id."'";
		$sqlg=mysql_query($sqlg) or die(mysql_error());
		//echo'OK';
	}



	//request again for refresh
	$sqlf="SELECT * FROM dmx_preferences ORDER BY id";
	$sqlf=mysql_query($sqlf);
	while ($dataf=mysql_fetch_array($sqlf)){
		echo'<tr>';
			echo'<td><input name="language" value="'.$dataf[lang].'" size="4"></td>';
			echo'<td><input name="display_rgb" type="checkbox" value="1"'; if($dataf[display_rgb]==1){echo'checked';} echo'></td>';
			echo'<td><input name="display_cmy" type="checkbox" value="1"'; if($dataf[display_cmy]==1){echo'checked';} echo'></td>';

			echo'<input name="id" value="'.$dataf[id].'" type="hidden">';

		echo'</tr>';
	}

	//engine info
	$sqlg="SELECT * FROM dmx_engine WHERE id=1";
	$sqlg=mysql_query($sqlg);
	while ($datag=mysql_fetch_array($sqlg)){
		echo'<tr><td colspan="3"><br><i><b><font size="" color="#808080">Engine Rate</b> : '.$datag[freq_ms].' ms</font></i></td></tr>';
	}

	//save
	echo'<tr><td colspan="3"><br><input type="submit" name="chgvalues" value="'.TXT_SAVE.'">';

		if ( isset($_POST['chgvalues']) )
		{
			echo' OK';
		}

	echo'</td></tr>';

	echo'</form>';

echo'</table></div>';

//print_r($_POST);

?>

<br><br>

</body>

