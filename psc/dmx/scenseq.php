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
 * scenseq.php
 * mod steps sequence of scenario
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

require("../config.php");
require("funct.php");
include("menu.php");

$id = $_GET['id'];

include("controlseq.php");

////
$dupscen=($_POST['dupscen']);

//dupscen
if ( isset($_POST['dupscen']) )
{
	//regarde le scenseq
	$sqlb="SELECT * FROM dmx_scenseq WHERE id_scenari=$dupscen ORDER BY id";
	$sqlb=mysql_query($sqlb);
	$p=0;
	while ($datab=mysql_fetch_array($sqlb)){
		//DO NOT add step 0 (names)
		$p++;

		//ajoute le meme, entry in seq
		$sqlc="INSERT INTO dmx_scenseq VALUES('','$id','$datab[stepname]','$datab[hold]','$datab[fade]','$datab[position]','$datab[disabled]')";
		$sqlc=mysql_query($sqlc) or die(mysql_error());
		//echo"<b>Scenseq$p</b>-";

		$last_step=mysql_insert_id();

		//regarde le step content for this entry (id_scenari optional)
		$sqle="SELECT * FROM dmx_scenari WHERE id_scenari=$dupscen AND step=$datab[id] ORDER BY id";
		$sqle=mysql_query($sqle);
		while ($datae=mysql_fetch_array($sqle)){
			//ajoute le meme
			$sqlf="INSERT INTO dmx_scenari VALUES('','$id','$datae[ch_name]','$datae[ch_value]','$last_step')";
			$sqlf=mysql_query($sqlf) or die(mysql_error());
			//echo"Step$p-";
		}
	}

echo'<i>'.TXT_SCENARIO_DUP.'</i><br>';
}
////

//reverse
if ( isset($_GET['way']) )
{
	//set way
	$sqlb="UPDATE dmx_scensum SET reverse='".$_GET['way']."' WHERE id=$id";
	$sqlb=mysql_query($sqlb);
}

echo'<div class="sideborder"><table><tr>

	<td style="background-color:white" width="" height="29">';

		//name
		//nom du scenari
		$sql="SELECT * FROM dmx_scensum WHERE id=$id";
		$sql=mysql_query($sql);
		while ($data=mysql_fetch_array($sql)){
			//
			echo'<font size="2"><b>'.TXT_SCENARIO.'</b>:</font>
			<a href="scenari.php?id='.$id.'">
			<font size="2">('.$id.')</font>
			<b>'.$data[scenari_name].'</b></a>';

			echo' - <a href="scenseq.php?id='.$id.'">'.TXT_STEPS.'</a>';
			//
			$reverse=$data[reverse]; //echo"$reverse";
		}

		if ($reverse=='0'){
			echo'&nbsp;<a href="scenseq.php?id='.$id.'&way=1"><font size="2" color="#676767">( Normal )</font></a>';
		}else{
			echo'&nbsp;<a href="scenseq.php?id='.$id.'&way=0"><font size="2" color="#676767">( Reverse )</font></a>';
		}

	echo'</td>';
	echo'<td>';
		echo'<form action="scenseq.php?id='.$id.'" method="post">';
		echo'<select name="dupscen">';

	//regarde le scensum
	$sqlb="SELECT * FROM dmx_scensum WHERE id=$id ORDER BY id";
	$sqlb=mysql_query($sqlb);
	while ($datab=mysql_fetch_array($sqlb)){

		//list all scenarios for this fixture
		$sqlc="SELECT * FROM dmx_scensum WHERE disabled!=1 AND id_fixture=$datab[id_fixture] ORDER BY id";
		$sqlc=mysql_query($sqlc);
		while ($datac=mysql_fetch_array($sqlc)){

			echo"<option value=\"$datac[id]\">";

			echo"$datac[scenari_name]"; //U$datac[univ] - $datad[schema_name] (Patch $datac[patch] - $datac[patch_after])
		}
	}

		echo'</select>';
		echo' <input type="submit" name="dup" value="DUP">';
		echo'</form>';
	echo'</td>';

echo'</tr></table></div>';

echo'<br>';

//delete step (in scenseq and scenari channels)
if ( isset($_GET['delstep']) )
{
	$sqlh="DELETE FROM dmx_scenseq WHERE id='".$_GET['delstep']."'";
	$sqlh=mysql_query($sqlh) or die(mysql_error());

	$sqli="DELETE FROM dmx_scenari WHERE step='".$_GET['delstep']."'";
	$sqli=mysql_query($sqli) or die(mysql_error());

    echo'<i>Step deleted</i>';
}

//all
if ( isset($_POST['allenabled']) )
{
	$sqlg="UPDATE dmx_scenseq SET disabled='0' WHERE id_scenari=$id";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//all
if ( isset($_POST['alldisabled']) )
{
	$sqlg="UPDATE dmx_scenseq SET disabled='1' WHERE id_scenari=$id";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//pos
if ( isset($_POST['chgallpos']) )
{
	$sqlg="UPDATE dmx_scenseq SET position='".$_POST['allpos']."' WHERE id_scenari=$id";
	$sqlg=mysql_query($sqlg) or die(mysql_error());
}

//affiche la sequence (steps)
echo'<div id="sequence"><table>';

	echo'<form action="scenseq.php?id='.$id.'" method="post">';

	echo'<tr>';
		echo'<td><b>'.TXT_STEP.'</b></td>';
		echo'<td><b>'.TXT_HOLD.'</b></td>';
		echo'<td><b>'.TXT_FADE.'</b></td>';
		echo'<td><b>'.TXT_POSITION.'</b></td>';
		echo'<td><b>'.TXT_ENABLED.'</b></td>';
	echo'</tr>';

	$sqlf="SELECT * FROM dmx_scenseq WHERE id_scenari=$id ORDER BY id";
	$sqlf=mysql_query($sqlf);
	$testf=mysql_num_rows($sqlf);

	//chg values for each channel
	if ( isset($_POST['chgvalues']) )
	{
		//array values: nom du pas, hold, fade
		for ($j = 0; $j < $testf; $j++) {
			if ($_POST['hold'][$j]==0 AND $_POST['fade'][$j]==0){
				echo''.TXT_ZEROS_ERROR.'.';
			}elseif ($_POST['hold'][$j]!="" AND $_POST['fade'][$j]!=""){
				$sqlg="UPDATE dmx_scenseq SET stepname='".$_POST['stepname'][$j]."',hold='".$_POST['hold'][$j]."',fade='".$_POST['fade'][$j]."',position='".$_POST['position'][$j]."' WHERE id='".$_POST['step_id'][$j]."'";
				$sqlg=mysql_query($sqlg) or die(mysql_error());
				//echo'ok_';
			}
		}

		//all disabled first
		$sqlg="UPDATE dmx_scenseq SET disabled='1' WHERE id_scenari=$id";
		$sqlg=mysql_query($sqlg) or die(mysql_error());

		if ( isset($_POST['enabled']) ){
			//this array values
			foreach($_POST['enabled'] as $val)
			{
				//echo $val,'<br />';
				$sqlg="UPDATE dmx_scenseq SET disabled='0' WHERE id='".$val."'";
				$sqlg=mysql_query($sqlg) or die(mysql_error());
				//echo'ok_';
			}
		}
	}

	//chg all values
	if ( isset($_POST['chgallval']) )
	{
		if ($_POST['allhold']==0 AND $_POST['allfade']==0){
			echo''.TXT_ZEROS_ERROR.'.';
		}elseif ($_POST['allhold']!="" AND $_POST['allfade']!=""){
			//array values: hold, fade
			for ($j = 0; $j < $testf; $j++) {
				$sqlg="UPDATE dmx_scenseq SET hold='".$_POST['allhold']."',fade='".$_POST['allfade']."' WHERE id='".$_POST['step_id'][$j]."'";
				$sqlg=mysql_query($sqlg) or die(mysql_error());
				//echo'ok_';
			}
		}
	}

	//request again for refresh
	$sqlf="SELECT * FROM dmx_scenseq WHERE id_scenari=$id ORDER BY position,id";
	$sqlf=mysql_query($sqlf);
	while ($dataf=mysql_fetch_array($sqlf)){
		echo'<tr>';
			echo'<td><input name="stepname[]" value="'.$dataf[stepname].'" size="12"></td>';
			echo'<td><input name="hold[]" value="'.$dataf[hold].'" size="4"></td>';
			echo'<td><input name="fade[]" value="'.$dataf[fade].'" size="4"></td>';
			echo'<td><input name="position[]" value="'.$dataf[position].'" size="4"></td>';
			//echo'<td><input name="disabled[]" value="'.$dataf[disabled].'" size="3"></td>';
			echo'<td><center><input type="checkbox" name="enabled[]" value="'.$dataf[id].'"'; if ($dataf[disabled]=='0'){echo' checked';} echo'></center></td>';

			echo'<input name="step_id[]" value="'.$dataf[id].'" type="hidden">';

		    echo'<td><a href="scenseq.php?id='.$id.'&delstep='.$dataf[id].'"';
		    echo" onclick=\"javascript:if(!confirm('DELETE STEP: ".$dataf[stepname]." ?')) return false;\"";
		    echo'>'.TXT_DELETE.'</a></td>';
		echo'</tr>';
	}

echo'</table></div>';

echo''.TXT_SEP_VALUES.' <input type="submit" name="chgvalues" value="'.TXT_SAVE.'">';

echo'<br><br>'.TXT_HOLD.' <input name="allhold" value="" size="4">';
echo' '.TXT_FADE.' <input name="allfade" value="" size="4">';
echo' <input type="submit" name="chgallval" value="'.TXT_ALL.'">';

echo'<br><br><br><br>'.TXT_ALL.' : <input type="submit" name="allenabled" value="'.TXT_ENABLE.'">';
echo'<input type="submit" name="alldisabled" value="'.TXT_DISABLE.'">';

echo' '.TXT_POSITION.' : <input name="allpos" value="" size="4">';
echo' <input type="submit" name="chgallpos" value="'.TXT_ALL.'"><br><br>';

echo'</form>';

//print_r($_POST);

?>

</body>

