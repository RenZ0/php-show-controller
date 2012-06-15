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
 * colors.php
 * color bank to store values
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

require("../config.php");
require("funct.php");
include("menu.php");

if ( isset($_GET['newcolor']) ){
	$newcolor=$_GET['newcolor'];
}else{
	$newcolor="0.0.0";
}
?>

<b><?=TXT_COLORS_TITLE?></b> (<a href="chart.php" target="blank"><font size="1" color="black"><?=TXT_COLORS_CHART?></font></a>)<br><br>

<form action="colors.php" method="post">
	<b><?=TXT_NAME?></b>
	<input type="text" name="colorname" value="" size="6">
	* <input type="text" name="qty" value="1" size="2">
	<select name="ch_name">
		<option value="rgb">rgb
		<option value="cmy" <?if (isset($_GET['iscmy'])){echo'selected';}?>>cmy
		<option value="">none
	</select>
	<input type="text" name="colorvalue" value="<?=$newcolor?>" size="8">
	<input type="submit" name="addcolor" value="<?=TXT_ADD?>">
</form>



<?

$delcolor=($_GET['delcolor']);

//delete color
if ( isset($_GET['delcolor']) )
{
    //remove color from colors
    $sqlh="DELETE FROM dmx_colors WHERE id=$delcolor";
    $sqlh=mysql_query($sqlh) or die(mysql_error());

    echo'<i>'.TXT_COLOR_DELETED.'</i>';
}

//all
if ( isset($_POST['allenabled']) )
{
	$sqlg="UPDATE dmx_colors SET disabled='0'";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//all
if ( isset($_POST['alldisabled']) )
{
	$sqlg="UPDATE dmx_colors SET disabled='1'";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//pos
if ( isset($_POST['chgallpos']) )
{
	$sqlg="UPDATE dmx_colors SET position=$allpos";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//add color(s)
if ( isset($_POST['addcolor']) )
{
    if ($_POST['ch_name']=="rgb" or $_POST['ch_name']=="cmy"){
        $ch_defvalue=$_POST['colorvalue'];
    }
    else{
        $ch_defvalue="0";
    }

	$i=0;
	while ( $i<$_POST['qty'] ){
		$n=$i+1;
		$sqla="INSERT INTO dmx_colors VALUES('','$_POST[colorname]$n','$_POST[ch_name]','$ch_defvalue','100','')";
		$sqla=mysql_query($sqla) or die(mysql_error());
		echo"add$n-";
		$i++;
	}
}

//color list

//affiche toutes les colors
echo'<table><tr>';

	echo'<form action="colors.php" method="post">';

	echo'<td>';

	$sqlf="SELECT * FROM dmx_colors";
	$sqlf=mysql_query($sqlf);
	$testf=mysql_num_rows($sqlf);

	//chg values for each channel
	if ( isset($_POST['chgvalues']) )
	{
		//array values
		for ($j = 0; $j < $testf; $j++) {
			$sqlg="UPDATE dmx_colors SET colorname='".$_POST['colorname'][$j]."',ch_value='".$_POST['ch_value'][$j]."',position='".$_POST['position'][$j]."' WHERE id='".$_POST['ch_id'][$j]."'";
			$sqlg=mysql_query($sqlg) or die(mysql_error());
			//echo'ok_';
		}

		//all disabled first
		$sqlg="UPDATE dmx_colors SET disabled='1'";
		$sqlg=mysql_query($sqlg) or die(mysql_error());

		if ( isset($_POST['enabled']) ){
			//this array values
			foreach($_POST['enabled'] as $val)
			{
				//echo $val,'<br />';
				$sqlg="UPDATE dmx_colors SET disabled='0' WHERE id='".$val."'";
				$sqlg=mysql_query($sqlg) or die(mysql_error());
				//echo'ok_';
			}
		}
	}

	$table_dmx1="dmx_colors";
	include("chgall.php");

	echo'<div class="colorview"><table>';

		//request again for refresh
		$sqlf="SELECT * FROM dmx_colors ORDER BY position,id";
		$sqlf=mysql_query($sqlf);
		$nb_rgb=0;

			echo'<tr>';

				echo'<td></td>';

				echo'<td><b>'.TXT_NAME.'</b></td>';

				echo'<td><b>'.TXT_COLORS_VALUE.'</b></td>';

				echo'<td><b>'.TXT_COLORS_POS.'</b></td>';

				echo'<td><b>'.TXT_COLORS_SEEN.'</b></td>';

				echo'<td></td>';

				echo'<td></td>';

				echo'<td></td>';

			echo'</tr>';

			echo'<tr><td></td></tr>';

		while ($dataf=mysql_fetch_array($sqlf)){

			echo'<tr>';

				echo'<td>';
					//echo'<input name="ch_name[]" value="'.$dataf[ch_name].'" size="3">';
					echo''.$dataf[ch_name].'';
				echo'</td>';

				echo'<td>';
					echo'<input name="colorname[]" value="'.$dataf[colorname].'" size="7">';
				echo'</td>';

				echo'<td>';
					echo'<input name="ch_value[]" value="'.$dataf[ch_value].'" size="8">';
				echo'</td>';

				echo'<td>';
					echo'<input name="position[]" value="'.$dataf[position].'" size="2">';
				echo'</td>';

				echo'<td>';
					//echo'<input name="disabled[]" value="'.$dataf[disabled].'" size="1">';
					echo'<input type="checkbox" name="enabled[]" value="'.$dataf[id].'"'; if ($dataf[disabled]=='0'){echo' checked';} echo'> ';
				echo'</td>';

				echo'<td>';
					echo'<a href="colors.php?delcolor='.$dataf[id].'"';
					echo" onclick=\"javascript:if(!confirm('DELETE COLOR: ".$dataf[colorname]." ?')) return false;\"";
					echo'><font size="1" color="#808080">('.TXT_COLORS_DEL.')</font>&nbsp;</a>';
				echo'</td>';


				echo'<input name="ch_id[]" value="'.$dataf[id].'" type="hidden">';


				//split la value rgb ou cmy pour convertir en html
				$string=$dataf[ch_value];
				$array=explode('.',$string);
				//echo"$array[0],$array[1],$array[2]";

				//trois premieres lettres du nom
				$match=substr($dataf[ch_name], 0, 3);

				echo'<td>';

					//rgb color
					if ($match=='rgb'){
						echo'<div class="carre" style="background-color:'.rgb2html($array[0],$array[1],$array[2]).';"></div>';
						$nb_rgb++;
					}

					//cmy color
					if ($match=='cmy'){
						echo'<div class="carre" style="background-color:'.cmy2html($array[0],$array[1],$array[2]).';"></div>';
					}

				echo'</td>';

				echo'<td>';
					echo'<input name="chkbox[]" value="'.$dataf[id].'" type="checkbox">';
				echo'</td>';

			echo'</tr>';
			//echo''.rgb2html($dataf[ch_value]).'<br>';
			//echo gettype($dataf[ch_value]);
		}

		echo'<input type="hidden" name="nb_rgb" value="'.$nb_rgb.'">';
		echo'<input type="hidden" name="colorstab" value="1">';

		echo'<tr><td colspan="8">';

			echo'<br><div align="right">';

				echo'<input type="submit" name="chgvalues" value="'.TXT_SAVE.'">';

				//free
				echo'<br><br><input name="allch_value" value="" size="8">';
				echo'<input type="submit" name="chgallval" value="A">';

				//jscolor
				echo'<br><input name="allch_valuejs_rgb" value="0000ff" size="8" class="color {dmx_rgb:true}">';
				echo'<input type="submit" name="chgallvaljs_rgb" value="RGB">';

				echo'<br><input name="allch_valuejs_cmy" value="00ffff" size="8" class="color {dmx_cmy:true}">';
				echo'<input type="submit" name="chgallvaljs_cmy" value="CMY">';

				//random
				echo'<br><input type="submit" name="chgallrand" value="* RGB RANDOM *">';

				echo'<br><br><input name="ch_value_src" value="" size="8">src';
				echo'<br><input name="ch_value_dst" value="" size="8">dst';
				echo'<br>offset<input name="offset" value="" size="1">';
				echo'<input type="submit" name="chgallvalfade" value="Gradient">';

				echo'<br><br><br>'.TXT_ALL.' : <input type="submit" name="allenabled" value="'.TXT_ENABLE.'">';
				echo'<input type="submit" name="alldisabled" value="'.TXT_DISABLE.'">';

				echo'<br>'.TXT_COLORS_POS.' <input name="allpos" value="" size="4">';
				echo' <input type="submit" name="chgallpos" value="'.TXT_ALL.'">';

			echo'</div>';

		echo'</td></tr>';

	echo'</table></div>';

	echo'</td>';

	echo'</form>';

echo'</tr></table>';

//echo'<div class="carre" style="background-color:'.rgb2html(255,0,255).';"></div><br>';
//print_r($_POST);

?>

</body>

