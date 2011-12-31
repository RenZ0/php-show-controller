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
 * fixtures.php
 * settings about fixtures
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

require("../config.php");
include("menu.php");

//disable fixture
if ( isset($_GET['disfix']) )
{
    //set disabled
    $sqlh="UPDATE dmx_fixture SET disabled='1' WHERE id=$disfix";
    $sqlh=mysql_query($sqlh) or die(mysql_error());
    //echo'<i>Fixture disabled</i><br>';
}

//add fixture
if ( isset($_POST['addfixture']) )
{
	$sqla="INSERT INTO dmx_fixture VALUES('','$fixture_name','$id_schema','$patch','$patch_after','$univ','')";
	$sqla=mysql_query($sqla) or die(mysql_error());
	//echo"Fixture added<br>";
}

?>

<script type="text/javascript">
function verif1()
{
	if (document.form1.fixture_name.value=="")
	{
		alert("<?=TXT_FIXTURES_MISSING?>");
		return false;
	}
	else
	{
		return true;
	}
}
</script>

<b><?=TXT_FIXTURES_TITLE?></b> (<a href="fixtmod.php"><font size="1" color="black"><?=TXT_EDIT?></font></a>)<br><br>

<form action="fixtures.php" method="post" name="form1" onsubmit="return verif1()">
	<b><?=TXT_LOCATION?></b> <?=TXT_LOCATION_EX?> <input type="text" name="fixture_name" size="20">
	<br><br>
	<b><?=TXT_SCHEMA?></b> <select name="id_schema">

	<?
	//define next patch
	$sqle="SELECT * FROM dmx_fixture WHERE disabled!=1 ORDER BY univ DESC";
	$sqle=mysql_query($sqle);
	$teste=mysql_num_rows($sqle);
	if ($teste==0){
		$lastuniv=1;
	}else{
		$lastuniv=mysql_result($sqle,0,'univ');
	}
	//
	$sqlc="SELECT * FROM dmx_fixture WHERE disabled!=1 AND univ=$lastuniv ORDER BY patch DESC";
	$sqlc=mysql_query($sqlc);
	$lastschema=mysql_result($sqlc,0,'id_schema');
	$lastpatch=mysql_result($sqlc,0,'patch');
	//
	$sqld="SELECT * FROM dmx_schsum WHERE id=$lastschema";
	$sqld=mysql_query($sqld);
	$lastfixture=mysql_result($sqld,0,'nb_channels');
	//
	$nextpatch=$lastpatch+$lastfixture;
	//

	$sql="SELECT * FROM dmx_schsum WHERE disabled!=1 ORDER BY id";
	$sql=mysql_query($sql);
	while ($data=mysql_fetch_array($sql)){
		echo"<option value=\"$data[id]\" "; if ($lastschema==$data[id]){echo"selected";} echo">$data[schema_name]";
	}

	echo'</select>';
	echo' <b>'.TXT_PATCH.'</b> <input type="text" name="patch" size="3" value="'.$nextpatch.'">';
	//echo' <b>After</b> <input type="text" name="patch_after" size="2" value="0">';
	echo' <b>'.TXT_UNIVERSE.'</b> <input type="text" name="univ" size="1" value="'.$lastuniv.'">';
	?>

	<input type="submit" name="addfixture" value="<?=TXT_ADD?>">
</form>



<?

//add fixture (info)
if ( isset($_POST['addfixture']) )
{
	echo''.TXT_FIXTURE_ADDED.'<br>';
}

//disable fixture (info)
if ( isset($_GET['disfix']) )
{
    echo'<i>'.TXT_FIXTURE_DISABLED.'</i><br>';
}

//list fixtures by schema
	echo"<hr>";
$sql="SELECT * FROM dmx_schsum WHERE disabled!=1 ORDER BY id";
$sql=mysql_query($sql);
while ($data=mysql_fetch_array($sql)){
	//
	//echo"$data[schema_name]<br>";
	//
	$sqlb="SELECT * FROM dmx_fixture WHERE disabled!=1 AND id_schema=$data[id] ORDER BY univ,patch,id";
	$sqlb=mysql_query($sqlb);
	while ($datab=mysql_fetch_array($sqlb)){
		echo"U$datab[univ] - ";

		$addr_dmx=$datab[patch]+1;
		$end_dmx=$datab[patch]+$data[nb_channels];

		echo"<font color=\"#808080\">
			<font size=\"3\">".TXT_PATCH." $datab[patch]</font>
			<font size=\"3\"> -> ".TXT_END_AT." $end_dmx</font>
			<font size=\"1\">(aft$datab[patch_after])</font>
			</font>
			----->
			<b>$data[schema_name]</b>
			<font size=\"2\">($data[nb_channels]CH - <a href=\"lxmath/index.html\" target=\"_blank\">ADDR $addr_dmx</a>)</font>";

		echo" - <font color=\"green\"><b>$datab[fixture_name]</b></font>
			<font size=\"1\">(id$datab[id])</font>";

	    echo' <a href="fixtures.php?disfix='.$datab[id].'"';
	    //echo" onclick=\"javascript:if(!confirm('DISABLE FIXTURE ?')) return false;\"";
	    echo'> <font size="1" color="#808080">('.TXT_DISABLE.')</font></a><br>';
	}
	echo"<hr>";
}

echo'<br><a href="fixtmod.php">('.TXT_EDIT.')</a>';

?>

<br><br><i><?=TXT_FIXTURES_INFO?></i><br><br>

</body>

