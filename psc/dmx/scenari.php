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
 * scenari.php
 * scenario editor
 * Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
 */

session_start();

require("../config.php");
require("funct.php");
include("menu.php");

$id = $_GET['id'];

include("control.php");

//light
if ( isset($_POST['unset_light']) )
{
	unset($_SESSION['light']);
}

if ( isset($_POST['set_light']) )
{
	$_SESSION['light'] = 1;
	//echo"OK";
}

//session filter
if ( isset($_POST['unset_filter']) )
{
	unset($_SESSION['filter_exp_a']);
	unset($_SESSION['filter_exp_b']);
}

if ( isset($_POST['set_filter']) )
{
	if ( $_POST['filter_exp_a']!="" ){
		$_SESSION['filter_exp_a'] = $_POST['filter_exp_a'];
	}

	if ( $_POST['filter_exp_b']!="" ){
		$_SESSION['filter_exp_b'] = $_POST['filter_exp_b'];
	}
}

//group session filter
if ( isset($_POST['unset_group_filter']) )
{
	unset($_SESSION['group_filter_a']);
	unset($_SESSION['group_filter_b']);
}

if ( isset($_POST['set_group_filter']) )
{
	if ( $_POST['group_filter_a']!="" ){
		$_SESSION['group_filter_a'] = $_POST['group_filter_a'];
	}

	if ( $_POST['group_filter_b']!="" ){
		$_SESSION['group_filter_b'] = $_POST['group_filter_b'];
	}
}

//group mode
if ( isset($_POST['unset_group']) )
{
	unset($_SESSION['group']);
}

if ( isset($_POST['set_group_edit']) )
{
	$_SESSION['group'] = 0;
	//echo"OK";
}

if ( isset($_POST['set_group_use']) )
{
	$_SESSION['group'] = 1;
	//echo"OK";
}

//new group
if ( isset($_POST['add_group']) )
{
	$sqla="INSERT INTO dmx_grpsum VALUES('','$_POST[id_schema]','$_POST[newgrp]','')";
	$sqla=mysql_query($sqla) or die(mysql_error());
	//$last_id=mysql_insert_id();
	echo'group added: '.$_POST[newgrp].'';
}

//add channel into a group
if ( isset($_GET['group_id']) AND isset($_GET['ch_name']) )
{
	$sqlhf="SELECT * FROM dmx_groups WHERE id_group=$_GET[group_id] AND ch_name='$_GET[ch_name]'";
	$sqlhf=mysql_query($sqlhf);
	$testhf=mysql_num_rows($sqlhf);
	if ($testhf==0){
		$sqla="INSERT INTO dmx_groups VALUES('','$_GET[group_id]','$_GET[ch_name]')";
		$sqla=mysql_query($sqla) or die(mysql_error());
		//$last_id=mysql_insert_id();
		//echo'channel added to group';
	}else{
		$sqla="DELETE FROM dmx_groups WHERE id_group=$_GET[group_id] AND ch_name='$_GET[ch_name]'";
		$sqla=mysql_query($sqla) or die(mysql_error());
	}
}

//dhold dfade
if ($_SESSION['dhold_value']==""){
	$_SESSION['dhold_value'] = "1.0";
}

if ($_SESSION['dfade_value']==""){
	$_SESSION['dfade_value'] = "1.0";
}

//
if ( isset($_POST['set_default']) )
{
	//unset($_SESSION['dhold_value']);
	//unset($_SESSION['dfade_value']);

	if ($_POST['dhold_value']=="0" AND $_POST['dfade_value']=="0"){
		echo''.TXT_ZEROS_ERROR.'';
	}else{
		if ($_POST['dhold_value']!=""){
			$_SESSION['dhold_value'] = $_POST['dhold_value'];
		}

		if ($_POST['dfade_value']!=""){
			$_SESSION['dfade_value'] = $_POST['dfade_value'];
		}
	}
}

//scename
if ( isset($_POST['chgscename']) )
{
	//set name
	$sqlb="UPDATE dmx_scensum SET scenari_name='".$_POST['scename']."' WHERE id=$id";
	$sqlb=mysql_query($sqlb);
}

//reverse
if ( isset($_GET['way']) )
{
	//set way
	$sqlb="UPDATE dmx_scensum SET reverse='".$_GET['way']."' WHERE id=$id";
	$sqlb=mysql_query($sqlb);
}

echo'<div class="sideborder"><table><tr>

	<td style="background-color:white">';

		//nom du scenari et liens pour rafraichir ou changer la sequence
		$sql="SELECT * FROM dmx_scensum WHERE id=$id";
		$sql=mysql_query($sql);
		while ($data=mysql_fetch_array($sql)){
			//
			echo'<b>'.TXT_SCENARIO.'</b>:';
			echo'&nbsp;';

			if ( !isset($_GET['editname']) ){
				echo'<a href="scenari.php?id='.$id.'"><font size="2">('.$id.')</font>&nbsp;<b>'.$data[scenari_name].'</b></a>';
			}else{
				echo'<a href="scenari.php?id='.$id.'"><font size="2">('.$id.')</font></a>';

				echo'<form action="scenari.php?id='.$id.'" method="post">';
				echo'<input name="scename" value="'.$data[scenari_name].'" size="30" style="width:300px;">';
				echo'<input type="submit" name="chgscename" value="OK">';
				echo'</form>';
			}

			echo'&nbsp;<a href="scenari.php?id='.$id.'&editname=1"';
			echo' onmousemove="over(\''.TXT_EDIT.'\', event)" onmouseout="overstop()"';
			echo'><font size="1">[E]</font></a>';

			echo' - <a href="scenseq.php?id='.$id.'">'.TXT_MODIFY_STEPS.'</a>';
			//
			$reverse=$data[reverse]; //echo"$reverse";

			$sqle="SELECT * FROM dmx_fixture WHERE id=$data[id_fixture]";
			$sqle=mysql_query($sqle);
			while ($datae=mysql_fetch_array($sqle)){
				//regarde le schema correspondant
				$id_schema=$datae[id_schema];
			}
		}

		if ($reverse=='0'){
			echo' <a href="scenari.php?id='.$id.'&way=1"';
			echo' onmousemove="over(\'Reverse ?\', event)" onmouseout="overstop()"';
			echo'><font size="2" color="#676767">(&nbsp;Normal&nbsp;)</font></a>';
		}else{
			echo' <a href="scenari.php?id='.$id.'&way=0"';
			//echo' onmousemove="over(\'Normal ?\', event)" onmouseout="overstop()"';
			echo'><font size="2" color="#676767">(&nbsp;Reverse&nbsp;)</font></a>';
		}

	echo'</td>';

?>

	<td>

		<div class="insideborder"><table><tr><td>

			<form action="scenari.php?id=<?echo$id?>" method="post">

				<?
				if ( isset($_SESSION['group']) AND $_SESSION['group']==0 ){ //Group Edit
					?>
					<b><?=TXT_GROUP?></b>:
					<input type="text" name="newgrp" size="8">
					<input type="submit" name="add_group" value="<?=TXT_ADD?>">
					<input type="submit" name="set_group_use" value="<?=TXT_SET_GROUP_USE?>">
					<input type="submit" name="unset_group" value="<?=TXT_UNSET_GROUP?>">
					<a href="grpmod.php?sch=<?=$id_schema?>" target="blank"><font size="2">(Mod)</font></a>
					<input type="hidden" name="id_schema" value="<?=$id_schema?>">
					<?
				}elseif ( isset($_SESSION['group']) AND $_SESSION['group']==1 ){ //Group Use
					?>
					<b><?=TXT_GROUP_FILTER?></b>:

					<?
					echo'<select name="group_filter_a">';

						echo'<option value="">';
						$sqlh="SELECT * FROM dmx_grpsum WHERE disabled!=1 AND id_schema=$id_schema ORDER BY group_name,id";
						$sqlh=mysql_query($sqlh);
						while ($datah=mysql_fetch_array($sqlh)){
							echo'<option value="'.$datah[group_name].'" ';
							if ( $_SESSION['group_filter_a']==$datah[group_name] ){ echo'selected'; }
							echo'>'.$datah[group_name].'';
						}

					echo'</select>';
					//
					echo'<select name="group_filter_b">';

						echo'<option value="">';
						$sqlh="SELECT * FROM dmx_grpsum WHERE disabled!=1 AND id_schema=$id_schema ORDER BY group_name,id";
						$sqlh=mysql_query($sqlh);
						while ($datah=mysql_fetch_array($sqlh)){
							echo'<option value="'.$datah[group_name].'" ';
							if ( $_SESSION['group_filter_b']==$datah[group_name] ){ echo'selected'; }
							echo'>'.$datah[group_name].'';
						}

					echo'</select>';
					?>

					<input type="submit" name="set_group_filter" value="<?=TXT_SET?>">
					<input type="submit" name="unset_group_filter" value="<?=TXT_UNSET?>">
					<input type="submit" name="set_group_edit" value="<?=TXT_SET_GROUP_EDIT?>">
					<?
				}else{ //Classic filter
					?>
					<b><?=TXT_FILTER?></b>:
					<input type="text" name="filter_exp_a" size="6">
					<input type="text" name="filter_exp_b" size="6">
					<input type="submit" name="set_filter" value="<?=TXT_SET?>">
					<input type="submit" name="unset_filter" value="<?=TXT_UNSET?>">
					<input type="submit" name="set_group_use" value="<?=TXT_SET_GROUP_USE?>">
					<?
				}

				if ( isset($_SESSION['filter_exp_a']) OR isset($_SESSION['filter_exp_b']) )
				{
					echo'<font color="red"><b>OK</b></font> : ';

					if ( isset($_SESSION['filter_exp_a']) ){
						echo''.$_SESSION['filter_exp_a'].'';
					}

					if ( isset($_SESSION['filter_exp_b']) ){
						echo' # '.$_SESSION['filter_exp_b'].'';
					}
				}
				?>
			</form>

		</td><td width="300">

			<div align="right">

				<form action="scenari.php?id=<?echo$id?>" method="post">
					<?=TXT_HOLD_H?> <input type="text" name="dhold_value" size="2" style="width:35px;">
					<?=TXT_FADE_F?> <input type="text" name="dfade_value" size="2" style="width:35px;">
					<input type="submit" name="set_default" value="<?=TXT_DEFAULT?>">
				</form>

			</div>

		</td></tr></table></div>

	</td>

</tr>
<tr>

	<td style="background-color:white">

		<form action="scenari.php?id=<?echo$id?>" method="post">
			<b><?=TXT_STEP?></b>:
			<input type="text" name="stepname" size="12">
			<?=TXT_HOLD?> <input type="text" name="hold" size="3" value="<?=$_SESSION['dhold_value']?>">
			<?=TXT_FADE?> <input type="text" name="fade" size="3" value="<?=$_SESSION['dfade_value']?>">
			<input type="submit" name="addstep" value="<?=TXT_ADD?>">
		</form>

	</td><td>

		<form action="scenari.php?id=<?echo$id?>" method="post">
			<b><?=TXT_VALUE?></b>:
			<input type="text" name="newvalueman" size="8">

			<?
			echo'<select name="newvaluetab">';

				$sqlh="SELECT * FROM dmx_colors WHERE disabled!=1 ORDER BY position,id";
				$sqlh=mysql_query($sqlh);
				while ($datah=mysql_fetch_array($sqlh)){
					echo"<option value=\"$datah[ch_value]\">$datah[colorname]";
				}

			echo'</select>';
			?>

			<?=TXT_LIKE?> <input type="text" name="likevalue" size="10" value="">
			<?=TXT_OLD?> <input type="text" name="oldvalue" size="8" value="">
			<?=TXT_TOALL?><input type="checkbox" name="applytoall" value="1">
			<input type="submit" name="superchg" value="<?=TXT_SAVE?>">
		</form>

	</td>

</tr></table></div>

<a name="stepview"></a>

<?

//update for all steps
if ( isset($_POST['superchg']) )
{
	if ($_POST['newvalueman']!=""){
		$newvalue=$_POST['newvalueman'];
	}else{
		$newvalue=$_POST['newvaluetab'];
	}

	if ($_POST['likevalue']!=""){
		$sqlb="UPDATE dmx_scenari SET ch_value='$newvalue' WHERE step!=0 AND ch_name LIKE '%".$_POST['likevalue']."%'";
		if ($_POST['applytoall']!='1'){
			//this scen only
			$sqlb.=" AND id_scenari=$id";
		}
		$sqlb=mysql_query($sqlb) or die(mysql_error());
	//
	}elseif ($_POST['oldvalue']!=""){
		$sqlb="UPDATE dmx_scenari SET ch_value='$newvalue' WHERE step!=0 AND ch_value = '".$_POST['oldvalue']."'";
		if ($_POST['applytoall']!='1'){
			//this scen only
			$sqlb.=" AND id_scenari=$id";
		}
		$sqlb=mysql_query($sqlb) or die(mysql_error());
	}
}

//add one step using schema
if ( isset($_POST['addstep']) )
{
	if ($_POST['hold']=="0" AND $_POST['fade']=="0"){
		echo''.TXT_ZEROS_ERROR.'';
	}elseif ($_POST['hold']!="" AND $_POST['fade']!=""){

		//ajoute le pas dans sequence
		$sqld="INSERT INTO dmx_scenseq VALUES('','$id','$_POST[stepname]','$_POST[hold]','$_POST[fade]','100','')";
		$sqld=mysql_query($sqld) or die(mysql_error());
		$last_id=mysql_insert_id();

		//regarde dans le sum
		$sqla="SELECT * FROM dmx_scensum WHERE id=$id";
		$sqla=mysql_query($sqla);
		while ($dataa=mysql_fetch_array($sqla)){
			//regarde le fixture
			$sqle="SELECT * FROM dmx_fixture WHERE id=$dataa[id_fixture]";
			$sqle=mysql_query($sqle);
			while ($datae=mysql_fetch_array($sqle)){
				//regarde le schema correspondant
				$sqlb="SELECT * FROM dmx_schema WHERE id_schema=$datae[id_schema] ORDER BY id";
				$sqlb=mysql_query($sqlb);
				while ($datab=mysql_fetch_array($sqlb)){
					//ajoute un pas avec le schema
					$sqlc="INSERT INTO dmx_scenari VALUES('','$id','$datab[ch_name]','$datab[ch_defvalue]','$last_id')";
					$sqlc=mysql_query($sqlc) or die(mysql_error());
				}
			}
		}
		//
	}
}

//dup step
if ( isset($_GET['dupstep']) )
{
	//regarde le scenseq for this step
	$sqlb="SELECT * FROM dmx_scenseq WHERE id='".$_GET['dupstep']."'";
	$sqlb=mysql_query($sqlb);
	while ($datab=mysql_fetch_array($sqlb)){
		//ajoute le meme, entry in seq
		$sqlc="INSERT INTO dmx_scenseq VALUES('','$id','$datab[stepname]','$datab[hold]','$datab[fade]','$datab[position]','$datab[disabled]')";
		$sqlc=mysql_query($sqlc) or die(mysql_error());
		//echo"<b>Scenseq</b>-";

		$last_step=mysql_insert_id();

		//regarde le step content
		$sqle="SELECT * FROM dmx_scenari WHERE step='".$_GET['dupstep']."' ORDER BY id";
		$sqle=mysql_query($sqle);
		while ($datae=mysql_fetch_array($sqle)){
			//ajoute le meme
			$sqlf="INSERT INTO dmx_scenari VALUES('','$id','$datae[ch_name]','$datae[ch_value]','$last_step')";
			$sqlf=mysql_query($sqlf) or die(mysql_error());
			//echo"Step-";
		}
	}
}

//paint steps
if ( isset($_GET['paintfrom']) )
{
	//regarde le scenseq for this step
	$sqlb="SELECT * FROM dmx_scenseq WHERE id='".$_GET['paintfrom']."'";
	$sqlb=mysql_query($sqlb);
	while ($datab=mysql_fetch_array($sqlb)){

		//regarde le step content rgb1
		$sqle="SELECT * FROM dmx_scenari WHERE step='".$_GET['paintfrom']."' AND ch_name = 'rgb1'";
		$sqle=mysql_query($sqle);
		while ($datae=mysql_fetch_array($sqle)){
			//get rgb1 value
			$myrgb1=$datae[ch_value];
		}

		//$nb_rgb=8;
		$sqle="SELECT * FROM dmx_scenari WHERE step='".$_GET['paintfrom']."'";
		$sqle=mysql_query($sqle);
		//$nb_rgb=mysql_num_rows($sqle);
		$nb_rgb=0;
		while ($datae=mysql_fetch_array($sqle)){

			//trois premieres lettres du nom
			$match=substr($datae[ch_name], 0, 3);

			//rgb color
			if ($match=='rgb'){
				$nb_rgb++;
			}
		}

		//regarde le step content rgb2 (last)
		$sqle="SELECT * FROM dmx_scenari WHERE step='".$_GET['paintfrom']."' AND ch_name = 'rgb$nb_rgb'";
		$sqle=mysql_query($sqle);
		while ($datae=mysql_fetch_array($sqle)){
			//get rgb1 value
			$myrgb2=$datae[ch_value];
		}

		//echo"rgb$nb_rgb -$myrgb1-$myrgb2-";

		if ( !isset($_GET['rewind']) ){

			// de haut en bas NORMAL
			for ($i=2;$i<=$nb_rgb;$i++){		

				//ajoute le meme, entry in seq
				$sqlc="INSERT INTO dmx_scenseq VALUES('','$id','$datab[stepname]','$datab[hold]','$datab[fade]','$datab[position]','$datab[disabled]')";
				$sqlc=mysql_query($sqlc) or die(mysql_error());
				//echo"<b>Scenseq</b>-";

				$last_step=mysql_insert_id();

				//regarde le step content
				$sqle="SELECT * FROM dmx_scenari WHERE step='".$_GET['paintfrom']."' ORDER BY id";
				$sqle=mysql_query($sqle);
				$j=0;
				while ($datae=mysql_fetch_array($sqle)){

					$match=substr($datae[ch_name], 0, 3);

					if ($match=='rgb'){

						$j++; //echo"$j-";

						if ( !isset($_GET['recover']) ){

							//DEFIL

							if ($j!=$i){
								//ajoute le fond
								$sqlf="INSERT INTO dmx_scenari VALUES('','$id','$datae[ch_name]','$myrgb2','$last_step')";
								//$sqlf=mysql_query($sqlf) or die(mysql_error());
								//echo"Step-";
							}else{
								//sinon ajoute avec couleur qui bouge
								$sqlf="INSERT INTO dmx_scenari VALUES('','$id','$datae[ch_name]','$myrgb1','$last_step')";
							}

						}else{

							//RECOUVR

							if ($j>$i){
								//ajoute le fond
								$sqlf="INSERT INTO dmx_scenari VALUES('','$id','$datae[ch_name]','$myrgb2','$last_step')";
								//$sqlf=mysql_query($sqlf) or die(mysql_error());
								//echo"Step-";
							}else{
								//sinon ajoute avec couleur qui remplit
								$sqlf="INSERT INTO dmx_scenari VALUES('','$id','$datae[ch_name]','$myrgb1','$last_step')";
							}

						}

					}else{
						$sqlf="INSERT INTO dmx_scenari VALUES('','$id','$datae[ch_name]','$datae[ch_value]','$last_step')";
					}

					$sqlf=mysql_query($sqlf) or die(mysql_error());
				}
			} //for

		}else{

			// de bas en haut REWIND
			for ($i=$nb_rgb-1;$i>=1;$i--){		

				//ajoute le meme, entry in seq
				$sqlc="INSERT INTO dmx_scenseq VALUES('','$id','$datab[stepname]','$datab[hold]','$datab[fade]','$datab[position]','$datab[disabled]')";
				$sqlc=mysql_query($sqlc) or die(mysql_error());
				//echo"<b>Scenseq</b>-";

				$last_step=mysql_insert_id();

				//regarde le step content
				$sqle="SELECT * FROM dmx_scenari WHERE step='".$_GET['paintfrom']."' ORDER BY id";
				$sqle=mysql_query($sqle);
				$j=0;
				while ($datae=mysql_fetch_array($sqle)){

					$match=substr($datae[ch_name], 0, 3);

					if ($match=='rgb'){

						$j++; //echo"$j-";

						if ( !isset($_GET['recover']) ){

							//DEFIL REW

							if ($j!=$i){
								//ajoute le fond
								$sqlf="INSERT INTO dmx_scenari VALUES('','$id','$datae[ch_name]','$myrgb1','$last_step')";
								//$sqlf=mysql_query($sqlf) or die(mysql_error());
								//echo"Step-";
							}else{
								//sinon ajoute avec couleur qui bouge
								$sqlf="INSERT INTO dmx_scenari VALUES('','$id','$datae[ch_name]','$myrgb2','$last_step')";
							}

						}else{

							//RECOUVR REW

							if ($j<$i){
								//ajoute le fond
								$sqlf="INSERT INTO dmx_scenari VALUES('','$id','$datae[ch_name]','$myrgb1','$last_step')";
								//$sqlf=mysql_query($sqlf) or die(mysql_error());
								//echo"Step-";
							}else{
								//sinon ajoute avec couleur qui remplit
								$sqlf="INSERT INTO dmx_scenari VALUES('','$id','$datae[ch_name]','$myrgb2','$last_step')";
							}

						}

					}else{
						$sqlf="INSERT INTO dmx_scenari VALUES('','$id','$datae[ch_name]','$datae[ch_value]','$last_step')";
					}

					$sqlf=mysql_query($sqlf) or die(mysql_error());
				}
			} //for

		}

	} //while scenseq orig
}

//dis step
if ( isset($_GET['distep']) )
{
	//disable step of scenseq
	$sqlb="UPDATE dmx_scenseq SET disabled='1' WHERE id='".$_GET['distep']."'";
	$sqlb=mysql_query($sqlb);
}

//edit step
if ( isset($_POST['chgstep']) )
{
	//update step of scenseq
	$sqlb="UPDATE dmx_scenseq SET stepname='".$_POST['step_name']."',hold='".$_POST['step_hold']."',fade='".$_POST['step_fade']."',position='".$_POST['step_position']."' WHERE id='".$_POST['step_id']."'";
	$sqlb=mysql_query($sqlb);
}

//step list

//affiche toutes les steps
echo'<table><tr>';

	//
	$sqlf="SELECT * FROM dmx_scenari WHERE id_scenari=$id AND step=0";
	$sqlf=mysql_query($sqlf);
	$testf=mysql_num_rows($sqlf);

	//chg names for each channel (for step0)
	if ( isset($_POST['chgnames']) )
	{
		//array values
		for ($j = 0; $j < $testf; $j++) {
			//if ($ch_name[$j]!=""){
				$sqlg="UPDATE dmx_scenari SET ch_name='".$_POST['ch_name'][$j]."' WHERE id='".$_POST['ch_id'][$j]."'";
				$sqlg=mysql_query($sqlg) or die(mysql_error());
				//echo'ok_';
			//}
		}
	}
	//

	//premiere colonne avec les titres
	echo'<form action="scenari.php?id='.$id.'#stepview" method="post">';

	//colonne du tableau global
	echo'<td>';

	echo'<b>'.TXT_CHANNELS.'</b><br>';

	echo'<font color="#808080">DMX<div style="float:right;">Info</div></font>';

	echo'<br><br>';

	if ( isset($_GET['editstep']) ){
		echo'<br>';
	}

	if ( !isset($_SESSION['light']) ){

		echo'<div class="colorview"><table>';

			//request again for refresh
			$sqlf="SELECT * FROM dmx_scenari WHERE id_scenari=$id AND step=0";

			if ( isset($_SESSION['group']) AND $_SESSION['group']==1 ){ //Group Use

				if ( isset($_SESSION['group_filter_a']) ){
					$sqlf.=" AND ch_name IN";
					$sqlf.=" ( SELECT ch_name FROM dmx_groups INNER JOIN dmx_grpsum ON dmx_groups.id_group = dmx_grpsum.id";
					$sqlf.=" WHERE dmx_grpsum.disabled=0 AND dmx_grpsum.group_name = '$_SESSION[group_filter_a]' )";
				}

				if ( isset($_SESSION['group_filter_b']) ){
					$sqlf.=" AND ch_name IN";
					$sqlf.=" ( SELECT ch_name FROM dmx_groups INNER JOIN dmx_grpsum ON dmx_groups.id_group = dmx_grpsum.id";
					$sqlf.=" WHERE dmx_grpsum.disabled=0 AND dmx_grpsum.group_name = '$_SESSION[group_filter_b]' )";
				}

			}

			if ( isset($_SESSION['filter_exp_a']) ){
				$sqlf.=" AND ch_name LIKE '%$_SESSION[filter_exp_a]%'";
			}

			if ( isset($_SESSION['filter_exp_b']) ){
				$sqlf.=" AND ch_name LIKE '%$_SESSION[filter_exp_b]%'";
			}

			$sqlf.=" ORDER BY id";

			$sqlf=mysql_query($sqlf);
			$testf=mysql_num_rows($sqlf);
			while ($dataf=mysql_fetch_array($sqlf)){
				echo'<tr>';

					echo'<td>';
						echo'<input name="ch_name[]" value="'.$dataf[ch_name].'" size="15" style="width:150px; height:24px;">';
					echo'</td>';

					// DMX INFO //

					//regarde le channel du schema (id stored in ch_value)
					$sqlb="SELECT * FROM dmx_schema WHERE id=$dataf[ch_value]";
					$sqlb=mysql_query($sqlb);
					while ($datab=mysql_fetch_array($sqlb)){

						echo'<td>';
							//affiche les infos de table dmx du schema
							echo'<a href="" onmousemove="over(\''.$datab[ch_name].' / '.$datab[ch_info].'\', event)" onmouseout="overstop()"><font color="#808080">o</font></a>';
						echo'</td>';

					}

					echo'<input name="ch_id[]" value="'.$dataf[id].'" type="hidden">';

				echo'</tr>';
			}

			echo'<tr><td colspan="2">';

				//echo"<input name=\"\" value=\"\" size=\"8\"></td>";
				echo'<input type="submit" name="chgnames" value="'.TXT_SAVE.'">';

			echo'</td></tr>';

			echo'<tr><td colspan="2">';
				echo'&nbsp;';
			echo'</td></tr>';

		echo'</table></div>';

		echo'<div class="ctrlzone"><table>';

				//colonnes vides pour la hauteur

			echo'<tr><td>';
				echo'<input name="" value="" size="8">';
			echo'</td></tr>';

			$sqlp="SELECT * FROM dmx_preferences WHERE id=1";
			$sqlp=mysql_query($sqlp);
			while ($datap=mysql_fetch_array($sqlp)){
				if ($datap[display_rgb]==1){

					echo'<tr><td>';
						echo'<input name="" value="RGB" size="8">';
					echo'</td></tr>';

				}

				if ($datap[display_cmy]==1){

					echo'<tr><td>';
						echo'<input name="" value="CMY" size="8">';
					echo'</td></tr>';

				}
			}

			echo'<tr><td>';
				echo'<input name="" value="" size="8">';
			echo'</td></tr>';

			echo'<tr><td>';
				echo'<input name="" value="" size="8">';
			echo'</td></tr>';

			echo'<tr><td>';
				//fade
				echo'<br>';
				echo'<br><input name="" value="" size="8">';
				echo'<br><input name="" value="" size="8">';
				echo'<br><input name="" value="" size="8">';
			echo'</td></tr>';

		echo'</table></div>';

	}//light

	//colonne du tableau global
	echo'</td>';

	echo'</form>';

//// GROUPS

	if ( isset($_SESSION['group']) AND !isset($_SESSION['light']) ){

		//display groups
		$sqlgf="SELECT * FROM dmx_grpsum WHERE id_schema=$id_schema AND disabled=0 ORDER BY group_name";
		$sqlgf=mysql_query($sqlgf);
		$testgf=mysql_num_rows($sqlgf);
		while ($datagf=mysql_fetch_array($sqlgf)){

			//premiere colonne avec les titres
			//echo'<form action="scenari.php?id='.$id.'#stepview" method="post">';

			//colonne du tableau global
			echo'<td>';
			echo'<font color="#808080"><b>'.TXT_GROUP.'</b></font><br>';
			echo'<font color="#808080"><div style="float:right;"></div></font>';
			echo'<br><br>';
			echo'<div class="colorview"><table>';

			$sqlf="SELECT * FROM dmx_scenari WHERE id_scenari=$id AND step=0";

			if ( isset($_SESSION['group']) AND $_SESSION['group']==1 ){ //Group Use

				if ( isset($_SESSION['group_filter_a']) ){
					$sqlf.=" AND ch_name IN";
					$sqlf.=" ( SELECT ch_name FROM dmx_groups INNER JOIN dmx_grpsum ON dmx_groups.id_group = dmx_grpsum.id";
					$sqlf.=" WHERE dmx_grpsum.disabled=0 AND dmx_grpsum.group_name = '$_SESSION[group_filter_a]' )";
				}

				if ( isset($_SESSION['group_filter_b']) ){
					$sqlf.=" AND ch_name IN";
					$sqlf.=" ( SELECT ch_name FROM dmx_groups INNER JOIN dmx_grpsum ON dmx_groups.id_group = dmx_grpsum.id";
					$sqlf.=" WHERE dmx_grpsum.disabled=0 AND dmx_grpsum.group_name = '$_SESSION[group_filter_b]' )";
				}

			}

			if ( isset($_SESSION['filter_exp_a']) ){
				$sqlf.=" AND ch_name LIKE '%$_SESSION[filter_exp_a]%'";
			}

			if ( isset($_SESSION['filter_exp_b']) ){
				$sqlf.=" AND ch_name LIKE '%$_SESSION[filter_exp_b]%'";
			}

			$sqlf.=" ORDER BY id";
			$sqlf=mysql_query($sqlf);
			while ($dataf=mysql_fetch_array($sqlf)){
				echo'<tr>';

					echo'<td>';

						if ( isset($_SESSION['group']) AND $_SESSION['group']==0 ){ //Group Edit Link
							echo'<a href="scenari.php?id='.$id.'&ch_name='.$dataf[ch_name].'&group_id='.$datagf[id].'#stepview">';
						}

						//show a button for each line
						$sqlhf="SELECT * FROM dmx_groups WHERE id_group=$datagf[id] AND ch_name='$dataf[ch_name]'";
						$sqlhf=mysql_query($sqlhf);
						$testhf=mysql_num_rows($sqlhf);
						if ($testhf==0){
							echo'<input name="" value="'.$datagf[group_name].'" size="3" class="off_group">';
						}else{
							echo'<input name="" value="'.$datagf[group_name].'" size="3" class="in_group">';
						}

						if ( isset($_SESSION['group']) AND $_SESSION['group']==0 ){ //Group Edit Link
							echo'</a>';
						}

					echo'</td>';

				echo'</tr>';
			}

				echo'<tr><td>';

					echo'<input type="submit" name="" value="" style="width:55px;">';

				echo'</td></tr>';

				echo'<tr><td>';
					echo'&nbsp;';
				echo'</td></tr>';

			echo'</table></div>';

			echo'<div class="ctrlzone"><table>';

					//colonnes vides pour la hauteur

				echo'<tr><td>';
					echo'<input name="" value="" size="3" style="width:55px;">';
				echo'</td></tr>';

				$sqlp="SELECT * FROM dmx_preferences WHERE id=1";
				$sqlp=mysql_query($sqlp);
				while ($datap=mysql_fetch_array($sqlp)){
					if ($datap[display_rgb]==1){

						echo'<tr><td>';
							echo'<input name="" value="" size="3" style="width:55px;">';
						echo'</td></tr>';

					}

					if ($datap[display_cmy]==1){

						echo'<tr><td>';
							echo'<input name="" value="" size="3" style="width:55px;">';
						echo'</td></tr>';

					}
				}

				echo'<tr><td>';
					echo'<input name="" value="" size="3" style="width:55px;">';
				echo'</td></tr>';

				echo'<tr><td>';
					echo'<input name="" value="" size="3" style="width:55px;">';
				echo'</td></tr>';

				echo'<tr><td>';
					//fade
					echo'<br>';
					echo'<br><input name="" value="" size="3" style="width:55px;">';
					echo'<br><input name="" value="" size="3" style="width:55px;">';
					echo'<br><input name="" value="" size="3" style="width:55px;">';
				echo'</td></tr>';

			echo'</table></div>';

			//colonne du tableau global
			echo'</td>';
			//echo'</form>';

		}//groups

	}//session_group

//// GROUPS

	//from scensum
	if ($reverse=='0'){
		$way='ASC';
	}else{
		$way='DESC';
	}

	//values pour chaque step
	//for ($i = 1; $i <= $teste; $i++) {
    $i=1;
    // get steps info
    $sqle="SELECT * FROM dmx_scenseq WHERE disabled!=1 AND id_scenari=$id";

    if ( isset($_GET['editstep']) )
    {
        $sqle .= " AND id=$_GET[editstep]";
    }

    $sqle .= " ORDER BY position $way,id $way";

    $sqle=mysql_query($sqle);
    $teste=mysql_num_rows($sqle);
	while ($datae=mysql_fetch_array($sqle)){

		echo'<form action="scenari.php?id='.$id.'#stepview" method="post">';

		//colonne du tableau global
		echo'<td>';

		if ( isset($_GET['editstep']) )
		{

			echo''.TXT_HOLD_H.'<input name="step_hold" value="'.$datae[hold].'" size="1" style="width:35px;">&nbsp;';
			echo''.TXT_FADE_F.'<input name="step_fade" value="'.$datae[fade].'" size="1" style="width:35px;">&nbsp;';
			echo''.TXT_POSITION_POS.'<input name="step_position" value="'.$datae[position].'" size="1" style="width:35px;">';
			echo'<br>';
			echo'<input name="step_name" value="'.$datae[stepname].'" size="9" style="width:110px;">';
			//echo'<input type="hidden" name="step_id" value="'.$datae[id].'">'; //now at bottom
			echo'<input type="submit" name="chgstep" value="OK">';

		}else{

			if ( !isset($_SESSION['light']) ){

				//step infos
				echo'<b>S'.$i.'</b>&nbsp;'; // $datae[step] error need i

				echo'('.TXT_HOLD_H_MIN.''.$datae[hold].'-'.TXT_FADE_F_MIN.''.$datae[fade].')';

			}

			echo'<div style="float:right;">';

				//edit link
				echo'<a href="scenari.php?id='.$id.'&editstep='.$datae[id].'"';
				echo' onmousemove="over(\''.TXT_EDIT.'\', event)" onmouseout="overstop()"';
				//echo" onclick=\"javascript:if(!confirm('EDIT STEP ?')) return false;\"";
				echo'>E</a>';
				echo'&nbsp;&nbsp;';

				//dup link
				echo'<a href="scenari.php?id='.$id.'&dupstep='.$datae[id].'"';
				echo' onmousemove="over(\''.TXT_DUPLICATE.'\', event)" onmouseout="overstop()"';
				//echo" onclick=\"javascript:if(!confirm('DUPLICATE STEP ?')) return false;\"";
				echo'>C</a>';
				echo'&nbsp;&nbsp;';

				//dis link
				echo'<a href="scenari.php?id='.$id.'&distep='.$datae[id].'"';
				echo' onmousemove="over(\''.TXT_DISABLE.'\', event)" onmouseout="overstop()"';
				echo" onclick=\"javascript:if(!confirm('DISABLE STEP ?')) return false;\"";
				echo'>X</a>';

			echo'</div><br>';

			if ( !isset($_SESSION['light']) ){
				//name
				echo'<font size="1"><b>'.$datae[stepname].'</b></font>&nbsp;';
			}

			echo'<div style="float:right;">';

				//paint link
				echo'<a href="scenari.php?id='.$id.'&paintfrom='.$datae[id].'"';
				echo' onmousemove="over(\''.TXT_COLOR_MOVE.'\', event)" onmouseout="overstop()"';
				//echo" onclick=\"javascript:if(!confirm('PAINT STEP ?')) return false;\"";
				echo'>D</a>';
				//paint link
				echo'<a href="scenari.php?id='.$id.'&paintfrom='.$datae[id].'&rewind=1"';
				echo' onmousemove="over(\''.TXT_COLOR_MOVE_REWIND.'\', event)" onmouseout="overstop()"';
				//echo" onclick=\"javascript:if(!confirm('PAINT STEP ?')) return false;\"";
				echo'>w</a>';

				//paint link recover
				echo'-<a href="scenari.php?id='.$id.'&paintfrom='.$datae[id].'&recover=1"';
				echo' onmousemove="over(\''.TXT_COLOR_RECOVER.'\', event)" onmouseout="overstop()"';
				//echo" onclick=\"javascript:if(!confirm('PAINT STEP ?')) return false;\"";
				echo'>R</a>';
				//paint link recover
				echo'<a href="scenari.php?id='.$id.'&paintfrom='.$datae[id].'&recover=1&rewind=1"';
				echo' onmousemove="over(\''.TXT_COLOR_RECOVER_REWIND.'\', event)" onmouseout="overstop()"';
				//echo" onclick=\"javascript:if(!confirm('PAINT STEP ?')) return false;\"";
				echo'>w</a>';

			echo'</div>';

		}

		echo'<br><br>';

		$sqlf="SELECT * FROM dmx_scenari WHERE id_scenari=$id AND step=$datae[id]";
		$sqlf=mysql_query($sqlf);
		$testf=mysql_num_rows($sqlf);

		//chg values for each channel
		if ( isset($_POST['chgvalues']) )
		{
			//array values
			for ($j = 0; $j < $testf; $j++) {
				if ($_POST['ch_value'][$j]!=""){
					$sqlg="UPDATE dmx_scenari SET ch_value='".$_POST['ch_value'][$j]."' WHERE id='".$_POST['ch_id'][$j]."'";
					$sqlg=mysql_query($sqlg) or die(mysql_error());
					//echo'ok_';
				}
			}
		}

		$table_dmx1="dmx_scenari";
		include("chgall.php");

		echo'<div class="colorview"><table>';

			//request again for refresh
			$sqlf="SELECT * FROM dmx_scenari WHERE id_scenari=$id AND step=$datae[id]";

			if ( isset($_SESSION['group']) AND $_SESSION['group']==1 ){ //Group Use

				if ( isset($_SESSION['group_filter_a']) ){
					$sqlf.=" AND ch_name IN";
					$sqlf.=" ( SELECT ch_name FROM dmx_groups INNER JOIN dmx_grpsum ON dmx_groups.id_group = dmx_grpsum.id";
					$sqlf.=" WHERE dmx_grpsum.disabled=0 AND dmx_grpsum.group_name = '$_SESSION[group_filter_a]' )";
				}

				if ( isset($_SESSION['group_filter_b']) ){
					$sqlf.=" AND ch_name IN";
					$sqlf.=" ( SELECT ch_name FROM dmx_groups INNER JOIN dmx_grpsum ON dmx_groups.id_group = dmx_grpsum.id";
					$sqlf.=" WHERE dmx_grpsum.disabled=0 AND dmx_grpsum.group_name = '$_SESSION[group_filter_b]' )";
				}

			}

			if ( isset($_SESSION['filter_exp_a']) ){
				$sqlf.=" AND ch_name LIKE '%$_SESSION[filter_exp_a]%'";
			}

			if ( isset($_SESSION['filter_exp_b']) ){
				$sqlf.=" AND ch_name LIKE '%$_SESSION[filter_exp_b]%'";
			}

			$sqlf.=" ORDER BY id";

			$sqlf=mysql_query($sqlf);
			$nb_rgb=0;
			while ($dataf=mysql_fetch_array($sqlf)){
				echo'<tr>';

					if ( !isset($_SESSION['light']) ){

						echo'<td>';
							echo'<input name="ch_value[]" value="'.$dataf[ch_value].'" size="9" style="width:90px; height:24px;">';
						echo'</td>';

					}

					echo'<input name="ch_id[]" value="'.$dataf[id].'" type="hidden">';
					echo'<input name="ch_name[]" value="'.$dataf[ch_name].'" type="hidden">';

					//split la value rgb ou cmy pour convertir en html
					$string=$dataf[ch_value];
					$array=explode('.',$string);
					//echo"$array[0],$array[1],$array[2]";

					//trois premieres lettres du nom
					$match=substr($dataf[ch_name], 0, 3);

					echo'<td>';

						//rgb color
						if ($match=='rgb'){
							echo'<a href="colors.php?newcolor='.$string.'" target="_blank">';
							if ($string=='255.255.255'){
								echo'<div class="carre_border"';
							}else{
								echo'<div class="carre"';
							}
							echo' style="background-color:'.rgb2html($array[0],$array[1],$array[2]).';"></div></a>';
							$nb_rgb++;
						}

						//cmy color
						if ($match=='cmy'){
							echo'<a href="colors.php?newcolor='.$string.'&iscmy=1" target="_blank">';
							if ($string=='0.0.0'){
								echo'<div class="carre_border"';
							}else{
								echo'<div class="carre"';
							}
							echo' style="background-color:'.cmy2html($array[0],$array[1],$array[2]).';"></div></a>';
						}

					echo'</td>';

					if ( !isset($_SESSION['light']) ){

						echo'<td>';
							echo'<input name="chkbox[]" value="'.$dataf[id].'" type="checkbox">';
						echo'</td>';

					}

				echo'</tr>';
				//echo''.rgb2html($dataf[ch_value]).'<br>';
				//echo gettype($dataf[ch_value]);
			}

			echo'<input type="hidden" name="nb_rgb" value="'.$nb_rgb.'">';

			if ( !isset($_SESSION['light']) ){

				//col3
				echo'<tr><td colspan="3">';

					echo'<input type="submit" name="chgvalues" value="'.TXT_SAVE.'">';

				echo'</td></tr>';

				//col3
				echo'<tr><td colspan="3">';
					echo'&nbsp;';
				echo'</td></tr>';

			echo'</table></div>';

			echo'<div class="ctrlzone"><table>';

				//start
				echo'<tr>';
					echo'<td>';

						//free
						echo'<input name="allch_value" value="" size="9" style="width:90px;">';

					echo'</td>';
					echo'<td>';

						echo'<input type="submit" name="chgallval" value="ALL">';

					echo'</td>';
				echo'</tr>';
				//end

				$sqlp="SELECT * FROM dmx_preferences WHERE id=1";
				$sqlp=mysql_query($sqlp);
				while ($datap=mysql_fetch_array($sqlp)){
					if ($datap[display_rgb]==1){
						//start
						echo'<tr>';
							echo'<td>';

								if ( isset($_POST['chgallvaljs_rgb']) AND $_POST['step_id']==$datae['id'] )
								{
									$array_color=explode('.',$_POST['allch_valuejs_rgb']);
									$valjs_color_rgb=rgb2html($array_color[0],$array_color[1],$array_color[2]);
								}else{
									$valjs_color_rgb='0000ff';
								}

								//jscolor
								echo'<input name="allch_valuejs_rgb" value="'.$valjs_color_rgb.'" size="9" style="width:90px;" class="color {dmx_rgb:true}">';

							echo'</td>';
							echo'<td>';

								echo'<input type="submit" name="chgallvaljs_rgb" value="RGB">';

							echo'</td>';
						echo'</tr>';
						//end
					}

					if ($datap[display_cmy]==1){
						//start
						echo'<tr>';
							echo'<td>';

								if ( isset($_POST['chgallvaljs_cmy']) AND $_POST['step_id']==$datae['id'] )
								{
									$array_color=explode('.',$_POST['allch_valuejs_cmy']);
									$valjs_color_cmy=cmy2html($array_color[0],$array_color[1],$array_color[2]);
								}else{
									$valjs_color_cmy='00ffff';
								}

								//jscolor reverse
								echo'<input name="allch_valuejs_cmy" value="'.$valjs_color_cmy.'" size="9" style="width:90px;" class="color {dmx_cmy:true}">';

							echo'</td>';
							echo'<td>';

								echo'<input type="submit" name="chgallvaljs_cmy" value="CMY">';

							echo'</td>';
						echo'</tr>';
						//end
					}
				}

				//col2
				echo'<tr><td colspan="2">';

					//random
					echo'<input type="submit" name="chgallrand" value="* RGB RANDOM *">';

				echo'</td></tr>';

				//start
				echo'<tr>';
					echo'<td>';

						echo'<select name="allch_valuemy">';

							$sqlh="SELECT * FROM dmx_colors WHERE disabled!=1 ORDER BY position,id";
							$sqlh=mysql_query($sqlh);
							while ($datah=mysql_fetch_array($sqlh)){
								echo"<option value=\"$datah[ch_value]\">$datah[colorname]";
							}

						echo'</select>';

					echo'</td>';
					echo'<td>';

						echo'<input type="submit" name="chgallvalmy" value="A">';

					echo'</td>';
				echo'</tr>';
				//end

				//start
				echo'<tr>';
					echo'<td>';

						//echo'<br><br><input name="ch_value_src" value="" size="8">src';
						echo'<br><br><select name="ch_value_src">';
							$sqlh="SELECT * FROM dmx_colors WHERE disabled!=1 ORDER BY position ASC, id ASC";
							$sqlh=mysql_query($sqlh);
							while ($datah=mysql_fetch_array($sqlh)){
								echo"<option value=\"$datah[ch_value]\">$datah[colorname]";
							}
						echo'</select>';

						//echo'<br><input name="ch_value_dst" value="" size="8">dst';
						echo'<br><select name="ch_value_dst">';
							$sqlh="SELECT * FROM dmx_colors WHERE disabled!=1 ORDER BY position DESC, id DESC";
							$sqlh=mysql_query($sqlh);
							while ($datah=mysql_fetch_array($sqlh)){
								echo"<option value=\"$datah[ch_value]\">$datah[colorname]";
							}
						echo'</select>';

						echo'<br>offset<input name="offset" value="" size="1">';

					echo'</td>';
					echo'<td>';

						echo'<input type="submit" name="chgallvalfade" value="Gdt">';

					echo'</td>';
				echo'</tr>';
				//end

			} //light

		echo'</table></div>';

		//colonne du tableau global
		echo'</td>';

		echo'<input type="hidden" name="step_id" value="'.$datae[id].'">';

		echo'</form>';

    $i++;
	}

echo'</tr></table>';

//print_r($_POST);

?>

</body>

