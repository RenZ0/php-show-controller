<?
//tcp
$tcp_host="localhost";
$tcp_port=9999;

//sql
$host="localhost";
$user="root";
$pass="";
$base="psc";

$c=@mysql_connect("$host","$user","$pass") or die('Database connection failed');
mysql_select_db("$base") or die('Could not open database : '.$base.'');
?>
