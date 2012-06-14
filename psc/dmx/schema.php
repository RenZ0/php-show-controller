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
 * schema.php
 * profile editor
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

require("../config.php");
include("menu.php");

$id = $_GET['id'];

//nom du schema
$sql="SELECT * FROM dmx_schsum WHERE id=$id";
$sql=mysql_query($sql);
while ($data=mysql_fetch_array($sql)){
	echo'<br><b>'.TXT_SCHEMA.'</b>: <a href="schema.php?id='.$id.'">'.$data[schema_name].'</a><br><br>';
}
?>

<div class="sideborder"><table><tr>
<td>

<form action="schema.php?id=<?echo$id?>" method="post">
	<b><?=TXT_CHANNEL?></b>
	<select name="ch_name">
		<option value="master">master
		<option value="rgb">rgb (3ch)
		<option value="cmy">cmy (3ch)
		<option value="white">white
		<option value="off">off (3ch)
		<option value="fx">fx
		<option value="strobe">strobe
		<option value="fade">fade
		<option value="line">line
		<option value="move">move
		<option value="pan">pan (x)
		<option value="tilt">tilt (y)
		<option value="special">special (3ch)
		<option value="rotate">rotate (3ch)
	</select>
	* <input type="text" name="qty" value="1" size="2">
	<input type="submit" name="addch" value="<?=TXT_ADD?>">
</form>

</td>

<?

//from existing schema
echo'<td><form action="schema.php?id='.$id.'" method="post">';

	echo'<b>'.TXT_MULTI.'</b> <select name="schema_src">';
	$sqli="SELECT * FROM dmx_schsum";
	$sqli=mysql_query($sqli);
	while ($datai=mysql_fetch_array($sqli)){
		//
		echo'<option value="'.$datai[id].'">'.$datai[schema_name].'';
		//
	}
	echo'</select>';

	echo' * <input type="text" name="qty" value="1" size="2">';
	echo' '.TXT_NUM_FROM.' <input type="text" name="numfrom" value="1" size="1">';

	echo' <input type="submit" name="addchfromschema" value="'.TXT_ADD.'">';

echo'</form></td>';

echo'</tr></table></div>';

//add ch to schema
if ( isset($_POST['addch']) )
{
    if ($_POST['ch_name']=="master"){
        $ch_defvalue="255";
        $ch_info="intensity";
    }
    elseif ($_POST['ch_name']=="rgb" or $_POST['ch_name']=="off" or $_POST['ch_name']=="special" or $_POST['ch_name']=="rotate"){
        $ch_defvalue="0.0.0";
    }
    elseif ($_POST['ch_name']=="cmy"){
        $ch_defvalue="255.255.255";
    }
    else{
        $ch_defvalue="0";
    }
    //
    if ($_POST['ch_name']=="rgb" or $_POST['ch_name']=="cmy"){
        $ch_info="color";
    }
	//
    if ($_POST['ch_name']=="white"){
        $ch_info="white";
    }
	//
	$i=0;
	while ( $i<$_POST['qty'] ){
		$n=$i+1;
		$sqla="INSERT INTO dmx_schema VALUES('','$id','$_POST[ch_name]$n','$ch_defvalue','$ch_info')";
		$sqla=mysql_query($sqla) or die(mysql_error());
		echo"add$n-";
		$i++;
	}
}

//add ch to schema from another one
if ( isset($_POST['addchfromschema']) )
{
	$i=$_POST['numfrom'];
	while ( $i < ($_POST['numfrom']+$_POST['qty']) ){
		//
		$sqlj="SELECT * FROM dmx_schema WHERE id_schema=$_POST[schema_src] ORDER BY id";
		$sqlj=mysql_query($sqlj);
		while ($dataj=mysql_fetch_array($sqlj)){
			$sqla="INSERT INTO dmx_schema VALUES('','$id','$dataj[ch_name]-a$i','$dataj[ch_defvalue]','$dataj[ch_info]')";
			$sqla=mysql_query($sqla) or die(mysql_error());
		}
		echo"add$i-";
		$i++;
	}

	$sqli="SELECT * FROM dmx_schsum WHERE id=$_POST[schema_src]";
	$sqli=mysql_query($sqli);
	while ($datai=mysql_fetch_array($sqli)){
		//
		$total_ch=$datai[nb_channels]*$_POST['qty'];
		echo' (+'.$total_ch.' channels)';
		//
	}
}

###
$sqlf="SELECT * FROM dmx_schema WHERE id_schema=$id ORDER BY id";
$sqlf=mysql_query($sqlf);
$testf=mysql_num_rows($sqlf);

#chg def for each channel
if ( isset($_POST['chgdefch']) )
{
	#array values
	for ($j = 0; $j < $testf; $j++) {
		$sqlg="UPDATE dmx_schema SET ch_name='".$_POST['ch_name'][$j]."',ch_defvalue='".$_POST['ch_defvalue'][$j]."',ch_info='".$_POST['ch_info'][$j]."' WHERE id='".$_POST['ch_id'][$j]."'";
		$sqlg=mysql_query($sqlg) or die(mysql_error());
		//echo'ok_';
	}
}
###

#chg def for all channel
if ( isset($_POST['chgdefallch']) )
{
	#array values
	for ($j = 0; $j < $testf; $j++) {
		$sqlg="UPDATE dmx_schema SET ch_defvalue='".$_POST['allch_defvalue']."' WHERE id='".$_POST['ch_id'][$j]."'";
		$sqlg=mysql_query($sqlg) or die(mysql_error());
		//echo'ok_';
	}
}
###

echo'<div id="sequence"><table>';

//ch list
echo'<form action="schema.php?id='.$id.'" method="post">';

	echo'<tr>';
		echo'<td><b>'.TXT_CHANNEL.'</b></td>';
		echo'<td><b>'.TXT_DEF_VALUE.'</b></td>';
		echo'<td><b>'.TXT_DMX_INFO.'</b></td>';
	echo'</tr>';

$sqlb="SELECT * FROM dmx_schema WHERE id_schema=$id ORDER BY id";
$sqlb=mysql_query($sqlb);
while ($datab=mysql_fetch_array($sqlb)){
	echo'<tr>';
		echo'<td><input name="ch_name[]" value="'.$datab[ch_name].'" size="15"></td>';
		echo'<td><input name="ch_defvalue[]" value="'.$datab[ch_defvalue].'" size="8"></td>';
		echo'<td><input name="ch_info[]" value="'.$datab[ch_info].'" size="120"></td>';
		echo'<input name="ch_id[]" value="'.$datab[id].'" type="hidden">';
	echo'</tr>';
}

echo'</table></div>';

echo'<input type="submit" name="chgdefch" value="'.TXT_SAVE.'">';

echo'<br><br>'.TXT_DEF_VALUE.' <input name="allch_defvalue" value="" size="6">';
echo'<input type="submit" name="chgdefallch" value="'.TXT_ALL.'">';

echo'</form>';

//print_r($_POST);

?>

<br><br><i><?=TXT_SCHEMA_INFO?></i><br><br>

</body>

