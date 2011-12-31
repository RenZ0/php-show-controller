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
 * head.php
 * html header and lang inclusion
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>PSC</title>
	<link rel="stylesheet" href="style.css">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="generator" content="gedit">
	<meta http-equiv="date" content="2011">

   <!-- Overscript JS -->
   <script type="text/javascript" src="overscript/overscript.js"></script>

   <!-- Overscript CSS -->
   <link href="overscript/overscript.css" rel="stylesheet" type="text/css">

   <!-- jscolor JS -->
   <script type="text/javascript" src="jscolor/jscolor.js"></script>
</head>
<body>

<?
//

$sqlp="SELECT * FROM dmx_preferences WHERE id=1";
$sqlp=mysql_query($sqlp);
while ($datap=mysql_fetch_array($sqlp)){
	$lang=$datap[lang];
	//echo"$lang";
}

include("../lang/$lang.lang.php");
?>
