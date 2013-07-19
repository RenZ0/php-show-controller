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
 * panel.php
 * control panel for scenarios
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

require("../config.php");
require("funct.php");
include("menu.php");

$list=$_GET['list'];
$multi=$_GET['multi'];
$act=$_POST['act'];

if (isset($act)){
	if ( $multi==1 AND isset($_POST['chkbox']) ){
		//multi
		foreach($_POST['chkbox'] as $val)
		{
			//SOCKET
			sendtoserver("$act.$val",$tcp_host,$tcp_port);
			//
			sleep($_POST['offset']);
		}
	}else{
		//SOCKET
		sendtoserver("$act",$tcp_host,$tcp_port);
		//
	}
}

//display panel
if ($list=='all'){

	echo'<br><a href="panel.php?list=all&multi='.$multi.'"><b>'.TXT_LINK_CONTROL.'</b></a>';
	echo' - <a href="panel.php?list=all&multi=0">MultiOff</a>';
	echo' - <a href="panel.php?list=all&multi=1">MultiOn</a>';
	echo'<br><br>';

	echo'<table><tr>';

		echo'<form action="panel.php?list=all&multi='.$multi.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
		echo'<input type="hidden" name="act" value="list">';
		echo'<td><input type="submit" name="go" value="list"></td>';
		echo'</form>';

		echo'<form action="panel.php?list=all&multi='.$multi.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
		echo'<input type="hidden" name="act" value="stopall">';
		echo'<td><input type="submit" name="go" value="stopall"></td>';
		echo'</form>';

		echo'<form action="panel.php?list=all&multi='.$multi.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
		echo'<input type="hidden" name="act" value="bo">';
		echo'<td><input type="submit" name="go" value="bo"></td>';
		echo'</form>';

		echo'<td width="10"></td>';

		echo'<form action="panel.php?list=all&multi='.$multi.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
		echo'<input type="hidden" name="act" value="resetall">';
		echo'<td><input type="submit" name="go" value="resetall"></td>';
		echo'</form>';

	echo'</tr></table>';

	echo'<hr>';

	//list scenarios
	echo'<table cellpadding="2">';

		$sqlb="SELECT * FROM dmx_scensum WHERE disabled!=1 ORDER BY id";
		$sqlb=mysql_query($sqlb) or die(mysql_error());
		$testb=mysql_num_rows($sqlb);

		if ($multi==1){
			echo'<form action="panel.php?list=all&multi='.$multi.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
		}

		while ($datab=mysql_fetch_array($sqlb)){
			echo'<tr>';

			if ($multi==1){
				echo'<td><input name="chkbox[]" value="'.$datab[id].'" type="checkbox"></td>';
			}else{
				//
				echo'<form action="panel.php?list=all&multi='.$multi.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
				echo'<input type="hidden" name="act" value="start.'.$datab[id].'">';
				echo'<td><input type="submit" name="go" value="start"></td>';
				echo'</form>';
				//
				echo'<form action="panel.php?list=all&multi='.$multi.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
				echo'<input type="hidden" name="act" value="stop.'.$datab[id].'">';
				echo'<td><input type="submit" name="go" value="stop"></td>';
				echo'</form>';
				//
				echo'<form action="panel.php?list=all&multi='.$multi.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
				echo'<input type="hidden" name="act" value="reset.'.$datab[id].'">';
				echo'<td><input type="submit" name="go" value="reset"></td>';
				echo'</form>';
				//
			}

			//get fixture (escalier)
			$sqld="SELECT * FROM dmx_fixture WHERE id=$datab[id_fixture] ORDER BY id";
			$sqld=mysql_query($sqld);
			while ($datad=mysql_fetch_array($sqld)){
				echo"<td>$datad[fixture_name]</td>";
			}

			echo'<td width="">(id'.$datab[id].') <a href="scenari.php?id='.$datab[id].'"><b>'.$datab[scenari_name].'</b></a></td>';

			echo'</tr>';
		}

	echo'</table>';

	if ($multi==1){
		if ($testb!=0){
			//
			echo'<input type="submit" name="act" value="start">';
			echo' Offset<input type="text" name="offset" value="0" size="1">sec<br>';
			//
			echo'<input type="submit" name="act" value="stop"><br>';
			//
			echo'<input type="submit" name="act" value="reset">';
			//
			echo'</form>';
		}else{
			echo'Nothing yet !<br><br>';
		}
	}

	echo'<hr>';

}
//display panel

//print_r($_POST);

?>

</body>

