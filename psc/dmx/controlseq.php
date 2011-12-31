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
 * controlseq.php
 * display control buttons on scenseq.php
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

$act=$_POST['act'];

if (isset($act)){
	//SOCKET
	sendtoserver("$act",$tcp_host,$tcp_port);
	//
}

echo'<table><tr>';

echo'<form action="scenseq.php?id='.$id.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
echo'<input type="hidden" name="act" value="list">';
echo'<td><input type="submit" name="go" value="list"></td>';
echo'</form>';

echo'<form action="scenseq.php?id='.$id.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
echo'<input type="hidden" name="act" value="start.'.$id.'">';
echo'<td><input type="submit" name="go" value="start"></td>';
echo'</form>';

echo'<form action="scenseq.php?id='.$id.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
echo'<input type="hidden" name="act" value="stop.'.$id.'">';
echo'<td><input type="submit" name="go" value="stop"></td>';
echo'</form>';

echo'<form action="scenseq.php?id='.$id.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
echo'<input type="hidden" name="act" value="stopall">';
echo'<td><input type="submit" name="go" value="stopall"></td>';
echo'</form>';

echo'<form action="scenseq.php?id='.$id.'" method="post" style="margin-top: 0px; margin-bottom: 0px">';
echo'<input type="hidden" name="act" value="bo">';
echo'<td><input type="submit" name="go" value="bo"></td>';
echo'</form>';

echo'</tr></table>';

?>
