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
	unset($_SESSION['filter_exp']);
}

if ( isset($_POST['set_filter']) AND $_POST['filter_exp']!="" )
{
	$_SESSION['filter_exp'] = $_POST['filter_exp'];
	//echo"OK";
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

//reverse
if ( isset($_GET['way']) )
{
	//set way
	$sqlb="UPDATE dmx_scensum SET reverse=$way WHERE id=$id";
	$sqlb=mysql_query($sqlb);
}

echo'<div class="sideborder"><table><tr>

	<td style="background-color:white">';

		//nom du scenari et liens pour rafraichir ou changer la sequence
		$sql="SELECT * FROM dmx_scensum WHERE id=$id";
		$sql=mysql_query($sql);
		while ($data=mysql_fetch_array($sql)){
			//
			echo'<b>'.TXT_SCENARIO.'</b>:
			<a href="scenari.php?id='.$id.'">
			<font size="2">('.$id.')</font>
			<b>'.$data[scenari_name].'</b>
			</a> - <a href="scenseq.php?id='.$id.'">'.TXT_MODIFY_STEPS.'</a>';
			//
			$reverse=$data[reverse]; //echo"$reverse";
		}

		if ($reverse=='0'){
			echo'<a href="scenari.php?id='.$id.'&way=1"><font size="2" color="#676767"> ( Normal ) </font></a>';
		}else{
			echo'<a href="scenari.php?id='.$id.'&way=0"><font size="2" color="#676767"> ( Reverse ) </font></a>';
		}

	echo'</td>';

?>

	<td>

		<div class="insideborder"><table><tr><td>

			<form action="scenari.php?id=<?echo$id?>" method="post">
				<b><?=TXT_FILTER?></b>:
				<input type="text" name="filter_exp" size="12">
				<input type="submit" name="set_filter" value="<?=TXT_SET?>">
				<input type="submit" name="unset_filter" value="<?=TXT_UNSET?>">
				<?
				if ( isset($_SESSION['filter_exp']) )
				{
					echo'<font color="red"><b>ACTIF</b></font> : '.$_SESSION['filter_exp'].'';
				}
				?>
			</form>

		</td><td width="300">

			<div align="right">

				<form action="scenari.php?id=<?echo$id?>" method="post">
					<?=TXT_HOLD_H?> <input type="text" name="dhold_value" size="3">
					<?=TXT_FADE_F?> <input type="text" name="dfade_value" size="3">
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
	if ($newvalueman!=""){
		$newvalue=$newvalueman;
	}else{
		$newvalue=$newvaluetab;
	}

	if ($likevalue!=""){
		$sqlb="UPDATE dmx_scenari SET ch_value='$newvalue' WHERE step!=0 AND ch_name LIKE '%$likevalue%'";
		if ($applytoall!="1"){
			//this scen only
			$sqlb.=" AND id_scenari=$id";
		}
		$sqlb=mysql_query($sqlb) or die(mysql_error());
	//
	}elseif ($oldvalue!=""){
		$sqlb="UPDATE dmx_scenari SET ch_value='$newvalue' WHERE step!=0 AND ch_value = '$oldvalue'";
		if ($applytoall!="1"){
			//this scen only
			$sqlb.=" AND id_scenari=$id";
		}
		$sqlb=mysql_query($sqlb) or die(mysql_error());
	}
}

//add one step using schema
if ( isset($_POST['addstep']) )
{
	if ($hold=="0" AND $fade=="0"){
		echo''.TXT_ZEROS_ERROR.'';
	}elseif ($hold!="" AND $fade!=""){

		//ajoute le pas dans sequence
		$sqld="INSERT INTO dmx_scenseq VALUES('','$id','$stepname','$hold','$fade','100','')";
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
	$sqlb="SELECT * FROM dmx_scenseq WHERE id=$dupstep";
	$sqlb=mysql_query($sqlb);
	while ($datab=mysql_fetch_array($sqlb)){
		//ajoute le meme, entry in seq
		$sqlc="INSERT INTO dmx_scenseq VALUES('','$id','$datab[stepname]','$datab[hold]','$datab[fade]','$datab[position]','$datab[disabled]')";
		$sqlc=mysql_query($sqlc) or die(mysql_error());
		//echo"<b>Scenseq</b>-";

		$last_step=mysql_insert_id();

		//regarde le step content
		$sqle="SELECT * FROM dmx_scenari WHERE step=$dupstep ORDER BY id";
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
	$sqlb="SELECT * FROM dmx_scenseq WHERE id=$paintfrom";
	$sqlb=mysql_query($sqlb);
	while ($datab=mysql_fetch_array($sqlb)){

		//regarde le step content rgb1
		$sqle="SELECT * FROM dmx_scenari WHERE step=$paintfrom AND ch_name = 'rgb1'";
		$sqle=mysql_query($sqle);
		while ($datae=mysql_fetch_array($sqle)){
			//get rgb1 value
			$myrgb1=$datae[ch_value];
		}

		//$nb_rgb=8;
		$sqle="SELECT * FROM dmx_scenari WHERE step=$paintfrom";
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
		$sqle="SELECT * FROM dmx_scenari WHERE step=$paintfrom AND ch_name = 'rgb$nb_rgb'";
		$sqle=mysql_query($sqle);
		while ($datae=mysql_fetch_array($sqle)){
			//get rgb1 value
			$myrgb2=$datae[ch_value];
		}

		//echo"rgb$nb_rgb -$myrgb1-$myrgb2-";

		if ( !isset($rewind) ){

			// de haut en bas NORMAL
			for ($i=2;$i<=$nb_rgb;$i++){		

				//ajoute le meme, entry in seq
				$sqlc="INSERT INTO dmx_scenseq VALUES('','$id','$datab[stepname]','$datab[hold]','$datab[fade]','$datab[position]','$datab[disabled]')";
				$sqlc=mysql_query($sqlc) or die(mysql_error());
				//echo"<b>Scenseq</b>-";

				$last_step=mysql_insert_id();

				//regarde le step content
				$sqle="SELECT * FROM dmx_scenari WHERE step=$paintfrom ORDER BY id";
				$sqle=mysql_query($sqle);
				$j=0;
				while ($datae=mysql_fetch_array($sqle)){

					$match=substr($datae[ch_name], 0, 3);

					if ($match=='rgb'){

						$j++; //echo"$j-";

						if ( !isset($recover) ){

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
				$sqle="SELECT * FROM dmx_scenari WHERE step=$paintfrom ORDER BY id";
				$sqle=mysql_query($sqle);
				$j=0;
				while ($datae=mysql_fetch_array($sqle)){

					$match=substr($datae[ch_name], 0, 3);

					if ($match=='rgb'){

						$j++; //echo"$j-";

						if ( !isset($recover) ){

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
	$sqlb="UPDATE dmx_scenseq SET disabled='1' WHERE id=$distep";
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
				$sqlg="UPDATE dmx_scenari SET ch_name='".$ch_name[$j]."' WHERE id='".$ch_id[$j]."'";
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

	if ( !isset($_SESSION['light']) ){

		echo'<div class="colorview"><table>';

			//request again for refresh
			if ( isset($_SESSION['filter_exp']) )
			{
			$sqlf="SELECT * FROM dmx_scenari WHERE id_scenari=$id AND step=0 AND ch_name LIKE '%$_SESSION[filter_exp]%' ORDER BY id";
			//
			//$sqlf="SELECT * FROM dmx_scenari INNER JOIN dmx_schema ON dmx_scenari.ch_value=dmx_schema.id WHERE dmx_scenari.id_scenari=$id AND dmx_scenari.step=0 AND dmx_schema.ch_name LIKE '%$_SESSION[filter_exp]%' ORDER BY dmx_scenari.id";
			}
			else
			{
			$sqlf="SELECT * FROM dmx_scenari WHERE id_scenari=$id AND step=0 ORDER BY id";
			}
			$sqlf=mysql_query($sqlf);
			$testf=mysql_num_rows($sqlf);
			while ($dataf=mysql_fetch_array($sqlf)){
				echo'<tr>';

					echo'<td>';
						echo'<input name="ch_name[]" value="'.$dataf[ch_name].'" size="20">';
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

				//colonnes vides pour la hauteur

			echo'<tr><td colspan="2">';
				echo'<input name="" value="" size="8">';
			echo'</td></tr>';

			$sqlp="SELECT * FROM dmx_preferences WHERE id=1";
			$sqlp=mysql_query($sqlp);
			while ($datap=mysql_fetch_array($sqlp)){
				if ($datap[display_rgb]==1){

					echo'<tr><td colspan="2">';
						echo'<input name="" value="RGB" size="8">';
					echo'</td></tr>';

				}

				if ($datap[display_cmy]==1){

					echo'<tr><td colspan="2">';
						echo'<input name="" value="CMY" size="8">';
					echo'</td></tr>';

				}
			}

			echo'<tr><td colspan="2">';
				echo'<input name="" value="" size="8">';
			echo'</td></tr>';

			echo'<tr><td colspan="2">';
				echo'<input name="" value="" size="8">';
			echo'</td></tr>';

			echo'<tr><td colspan="2">';
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
    $sqle="SELECT * FROM dmx_scenseq WHERE disabled!=1 AND id_scenari=$id ORDER BY position $way,id $way";
    $sqle=mysql_query($sqle);
    $teste=mysql_num_rows($sqle);
	while ($datae=mysql_fetch_array($sqle)){

		echo'<form action="scenari.php?id='.$id.'#stepview" method="post">';

		//colonne du tableau global
		echo'<td>';

		if ( !isset($_SESSION['light']) ){

			//step infos
			echo'<b>S'.$i.'</b>&nbsp;'; // $datae[step] error need i

			echo'('.TXT_HOLD_H_MIN.''.$datae[hold].'-'.TXT_FADE_F_MIN.''.$datae[fade].')';

		}

		//dis link
		echo'&nbsp;<a href="scenari.php?id='.$id.'&distep='.$datae[id].'"';
		echo" onclick=\"javascript:if(!confirm('DISABLE STEP ?')) return false;\"";
		echo'>X</a>';

		//dup link
		echo'&nbsp;<a href="scenari.php?id='.$id.'&dupstep='.$datae[id].'"';
		//echo" onclick=\"javascript:if(!confirm('DUPLICATE STEP ?')) return false;\"";
		echo'>#</a>';

		//name
		echo'<br><font size="1"><b>'.$datae[stepname].'</b></font>&nbsp;';

		echo'<div style="float:right;">';

			//paint link
			echo'<a href="scenari.php?id='.$id.'&paintfrom='.$datae[id].'"';
			//echo" onclick=\"javascript:if(!confirm('PAINT STEP ?')) return false;\"";
			echo'>D</a>';
			//paint link
			echo'<a href="scenari.php?id='.$id.'&paintfrom='.$datae[id].'&rewind=1"';
			//echo" onclick=\"javascript:if(!confirm('PAINT STEP ?')) return false;\"";
			echo'>w</a>';

			//paint link recover
			echo'-<a href="scenari.php?id='.$id.'&paintfrom='.$datae[id].'&recover=1"';
			//echo" onclick=\"javascript:if(!confirm('PAINT STEP ?')) return false;\"";
			echo'>R</a>';
			//paint link recover
			echo'<a href="scenari.php?id='.$id.'&paintfrom='.$datae[id].'&recover=1&rewind=1"';
			//echo" onclick=\"javascript:if(!confirm('PAINT STEP ?')) return false;\"";
			echo'>w</a>';

		echo'</div>';

		if ( isset($_SESSION['light']) ){
			echo'<br><br>';
		}

		$sqlf="SELECT * FROM dmx_scenari WHERE id_scenari=$id AND step=$datae[id]";
		$sqlf=mysql_query($sqlf);
		$testf=mysql_num_rows($sqlf);

		//chg values for each channel
		if ( isset($_POST['chgvalues']) )
		{
			//array values
			for ($j = 0; $j < $testf; $j++) {
				if ($ch_value[$j]!=""){
					$sqlg="UPDATE dmx_scenari SET ch_value='".$ch_value[$j]."' WHERE id='".$ch_id[$j]."'";
					$sqlg=mysql_query($sqlg) or die(mysql_error());
					//echo'ok_';
				}
			}
		}

		$table_dmx1="dmx_scenari";
		include("chgall.php");

		echo'<div class="colorview"><table>';

			//request again for refresh
			if ( isset($_SESSION['filter_exp']) )
			{
			$sqlf="SELECT * FROM dmx_scenari WHERE id_scenari=$id AND step=$datae[id] AND ch_name LIKE '%$_SESSION[filter_exp]%' ORDER BY id";
			}
			else
			{
			$sqlf="SELECT * FROM dmx_scenari WHERE id_scenari=$id AND step=$datae[id] ORDER BY id";
			}
			$sqlf=mysql_query($sqlf);
			$nb_rgb=0;
			while ($dataf=mysql_fetch_array($sqlf)){
				echo'<tr>';

					if ( !isset($_SESSION['light']) ){

						echo'<td>';
							echo'<input name="ch_value[]" value="'.$dataf[ch_value].'" size="8">';
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
							echo'<a href="colors.php?newcolor='.$string.'" target="_blank">
							<div class="carre" style="background-color:'.rgb2html($array[0],$array[1],$array[2]).';"></div></a>';
							$nb_rgb++;
						}

						//cmy color
						if ($match=='cmy'){
							echo'<a href="colors.php?newcolor='.$string.'&iscmy=1" target="_blank">
							<div class="carre" style="background-color:'.cmy2html($array[0],$array[1],$array[2]).';"></div></a>';
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

				//start
				echo'<tr>';
					echo'<td>';

						//free
						echo'<input name="allch_value" value="" size="8">';

					echo'</td>';
					echo'<td colspan="2">';

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

								//jscolor
								echo'<input name="allch_valuejs_rgb" value="0000ff" size="8" class="color {dmx_rgb:true}">';

							echo'</td>';
							echo'<td colspan="2">';

								echo'<input type="submit" name="chgallvaljs_rgb" value="RGB">';

							echo'</td>';
						echo'</tr>';
						//end
					}

					if ($datap[display_cmy]==1){
						//start
						echo'<tr>';
							echo'<td>';

								//jscolor reverse
								echo'<input name="allch_valuejs_cmy" value="00ffff" size="8" class="color {dmx_cmy:true}">';

							echo'</td>';
							echo'<td colspan="2">';

								echo'<input type="submit" name="chgallvaljs_cmy" value="CMY">';

							echo'</td>';
						echo'</tr>';
						//end
					}
				}

				//col3
				echo'<tr><td colspan="3">';

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
					echo'<td colspan="2">';

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
					echo'<td colspan="2">';

						echo'<input type="submit" name="chgallvalfade" value="Gdt">';

					echo'</td>';
				echo'</tr>';
				//end

			} //light

		echo'</table></div>';

		//colonne du tableau global
		echo'</td>';

		echo'</form>';

    $i++;
	}

echo'</tr></table>';

//echo'<div class="carre" style="background-color:'.rgb2html(255,0,255).';"></div><br>';
//print_r($_POST);

?>

</body>

