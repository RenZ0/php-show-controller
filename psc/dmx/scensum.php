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
 * scensum.php
 * settings about scenarios
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

require("../config.php");
include("menu.php");
?>

<script type="text/javascript">
function verif1()
{
	if (document.form1.scenari_name.value=="")
	{
		alert("<?=TXT_SCENSUM_MISSING?>");
		return false;
	}
	else
	{
		return true;
	}
}
</script>

<b><?=TXT_SCENSUM_TITLE?></b> (<a href="scenmod.php"><font size="1" color="black"><?=TXT_EDIT?></font></a>)<br><br>

<form action="scensum.php" method="post" name="form1" onsubmit="return verif1()">
	<b><?=TXT_NAME?></b> <?=TXT_SCENARIO_EX?> <input type="text" name="scenari_name" size="20">
	<br><br><b><?=TXT_LOCATION?></b>
	<select name="id_fixture">
	<?
	$sql="SELECT * FROM dmx_fixture WHERE disabled!=1 ORDER BY univ,patch,id";
	$sql=mysql_query($sql);
	while ($data=mysql_fetch_array($sql)){
		echo"<option value=\"$data[id]\">$data[fixture_name]";
	}
	?>
	</select>

	<input type="submit" name="addscenari" value="<?=TXT_ADD?>">
</form>



<?

//add scenari
if ( isset($_POST['addscenari']) AND isset($_POST['id_fixture']) )
{
	$sqla="INSERT INTO dmx_scensum VALUES('','$_POST[scenari_name]','$_POST[id_fixture]','','')";
	$sqla=mysql_query($sqla) or die(mysql_error());
	echo''.TXT_SCENARIO_ADDED.'<br>';

	$last_id=mysql_insert_id();

	//regarde le fixture
	$sqle="SELECT * FROM dmx_fixture WHERE id=$_POST[id_fixture]";
	$sqle=mysql_query($sqle);
	while ($datae=mysql_fetch_array($sqle)){
		//regarde le schema correspondant
		$sqlb="SELECT * FROM dmx_schema WHERE id_schema=$datae[id_schema] ORDER BY id";
		$sqlb=mysql_query($sqlb);
		while ($datab=mysql_fetch_array($sqlb)){
			//ajoute le pas 0 avec le schema (with id in ch_value)
			$sqlc="INSERT INTO dmx_scenari VALUES('','$last_id','$datab[ch_name]','$datab[id]','0')";
			$sqlc=mysql_query($sqlc) or die(mysql_error());
		}
	}
}

//disable scenari
if ( isset($_GET['discen']) )
{
    //set disabled
    $sqlh="UPDATE dmx_scensum SET disabled='1' WHERE id='".$_GET['discen']."'";
    $sqlh=mysql_query($sqlh) or die(mysql_error());

    echo'<i>'.TXT_SCENARIO_DISABLED.'</i><br>';
}

//list scenarios by fixture
	echo"<hr>";
$sql="SELECT * FROM dmx_fixture WHERE disabled!=1 ORDER BY univ,patch,id";
$sql=mysql_query($sql);
while ($data=mysql_fetch_array($sql)){
	//
	echo"<b>$data[fixture_name]</b>";
	//
	$sqld="SELECT * FROM dmx_schsum WHERE id=$data[id_schema]";
	$sqld=mysql_query($sqld);
	while ($datad=mysql_fetch_array($sqld)){
		echo" <font size=\"2\">($datad[schema_name])</font>";
	}
	echo"<br>";
	//
	$sqlb="SELECT * FROM dmx_scensum WHERE disabled!=1 AND id_fixture=$data[id] ORDER BY scenari_name,id";
	$sqlb=mysql_query($sqlb);
	while ($datab=mysql_fetch_array($sqlb)){

		echo"<a href=\"scenari.php?id=$datab[id]\"><font size=\"1\">(id$datab[id])</font> - $datab[scenari_name] ";

			if ($datab[reverse]=='1'){
				echo"<font size=\"2\">(REV) </font>";
			}

		echo"-</a>";

	echo" <a href=\"scenseq.php?id=$datab[id]\"><font size=\"1\" color=\"#402626\">(".TXT_STEPS.")</font></a>";

        echo' <a href="scenmod.php?dupscen='.$datab[id].'"';
        echo" onclick=\"javascript:if(!confirm('DUPLICATE SCENARIO: ".$datab[scenari_name]." ?')) return false;\"";
        echo'><font size="1" color="purple">('.TXT_DUP.')</font></a>';

	    echo' <a href="scensum.php?discen='.$datab[id].'"';
	    //echo" onclick=\"javascript:if(!confirm('DISABLE SCENARIO ?')) return false;\"";
	    echo'> <font size="1" color="#808080">('.TXT_DISABLE.')</font></a><br>';
	}
	echo"<hr>";
}

echo'<br><a href="scenmod.php">('.TXT_EDIT.')</a>';

//print_r($_POST);

?>

<br><br><i><?=TXT_SCENSUM_INFO?></i><br><br>

</body>

