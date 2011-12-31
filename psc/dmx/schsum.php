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
 * schsum.php
 * settings about schemas
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

require("../config.php");
include("menu.php");
?>

<script type="text/javascript">
function verif1()
{
	if (document.form1.schema_name.value=="")
	{
		alert("<?=TXT_SCHSUM_MISSING?>");
		return false;
	}
	else
	{
		return true;
	}
}
</script>

<b><?=TXT_SCHSUM_TITLE?></b> (<a href="schmod.php"><font size="1" color="black"><?=TXT_EDIT?></font></a>)<br><br>

<form action="schsum.php" method="post" name="form1" onsubmit="return verif1()">
	<b><?=TXT_NAME?></b> <input type="text" name="schema_name" size="20">
	<b><?=TXT_CHANNELS?></b> <input type="text" name="nb_channels" size="2">
	<input type="submit" name="addschema" value="<?=TXT_ADD?>">
</form>



<?

//add schema
if ( isset($_POST['addschema']) )
{
	$sqla="INSERT INTO dmx_schsum VALUES('','$schema_name','$nb_channels','')";
	$sqla=mysql_query($sqla) or die(mysql_error());
	echo''.TXT_SCHEMA_ADDED.'<br><br>';
}

$sqlb="SELECT * FROM dmx_schsum WHERE disabled!=1 ORDER BY id";
$sqlb=mysql_query($sqlb);
while ($datab=mysql_fetch_array($sqlb)){
	echo"<a href=\"schema.php?id=$datab[id]\">$datab[id] - $datab[schema_name]</a> ($datab[nb_channels] Ch)<br>";
}

echo'<br><a href="schmod.php">('.TXT_EDIT.')</a>';

?>

<br><br><i><?=TXT_SCHSUM_INFO?></i><br><br>

</body>

