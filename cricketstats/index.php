<?php
/*
************************************************************
TPLLeagueStats is a league stats software designed for football (soccer)
team.

Copyright (C) 2003  Timo Leppänen / TPL Design
email:     info@tpl-design.com
www:       www.tpl-design.com/tplleaguestats

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

************************************************************
Ported to xoops by
Mythrandir http://www.web-udvikling.dk
and
ralf57 http://www.madeinbanzi.it

Cricket League Version & Modifications by M0nty <vaughan.montgomery@gmail.com>

************************************************************
*/

include ('../../mainfile.php');
include (XOOPS_ROOT_PATH.'/header.php');

//Include preferences
$sql = "SELECT SeasonID FROM ".$xoopsDB->prefix("cricket_seasonnames")." WHERE SeasonDefault=1";
$sql2 = "SELECT LeagueID FROM ".$xoopsDB->prefix("cricket_leaguenames")." WHERE LeagueDefault=1";
$cricket_season = $xoopsDB->query($sql);
$cricket_season = $xoopsDB->fetchArray($cricket_season);
$cricket_league = $xoopsDB->query($sql2);
$cricket_league = $xoopsDB->fetchArray($cricket_league);
$cricket_d_season_id = $cricket_season['SeasonID'];
$cricket_d_league_id = $cricket_league['LeagueID'];
$cricket_show_all_or_one = $xoopsModuleConfig['defaultshow'];
$cricket_show_table = $xoopsModuleConfig['defaulttable'];
$cricket_for_win = $xoopsModuleConfig['forwin'];
$cricket_for_draw = $xoopsModuleConfig['fordraw'];
$cricket_for_lose = $xoopsModuleConfig['forloss'];
$cricket_print_date = $xoopsModuleConfig['printdate'];
$cricket_top_bg = $xoopsModuleConfig['topoftable'];
$cricket_bg1 = $xoopsModuleConfig['bg1'];
$cricket_bg2 = $xoopsModuleConfig['bg2'];
$cricket_inside_c = $xoopsModuleConfig['inside'];
$cricket_border_c = $xoopsModuleConfig['bordercolour'];
$cricket_tb_width = $xoopsModuleConfig['tablewidth'];

//query to get last updated date and time
//
//Use site http://www.mysql.com/doc/en/Date_and_time_functions.html (DATE_FORMAT)
//to return date format that fits to your site
//

$updated_query = $xoopsDB->query("SELECT MAX(LeagueMatchCreated) AS last_updated FROM ".$xoopsDB->prefix("cricket_leaguematches"));
$ludata = $xoopsDB->fetchArray($updated_query);
$last_update = date('d.m.Y @ H:i', $ludata['last_updated']);

//If session variables are registered
//if(!session_is_registered('defaultseasonid') || !session_is_registered('defaultleagueid') || !session_is_registered('defaultshow') || !session_is_registered('defaulttable'))
    if ( !isset( $_SESSION['defaultseasonid'] ) || !isset( $_SESSION['defaultleagueid'] ) || !isset( $_SESSION['defaultshow'] ) || !isset( $_SESSION['defaulttable'] ))
{
    $_SESSION['defaultseasonid'] = $cricket_d_season_id;
    $_SESSION['defaultleagueid'] = $cricket_d_league_id;
    $_SESSION['defaultshow'] = $cricket_show_all_or_one;
    $_SESSION['defaulttable'] = $cricket_show_table;
    $cricket_defaultseasonid = intval($_SESSION['defaultseasonid']);
    $cricket_defaultleagueid = intval($_SESSION['defaultleagueid']);
    $cricket_defaultshow = $_SESSION['defaultshow'];
    $cricket_defaulttable = $_SESSION['defaulttable'];
}
else
{
    
    $cricket_defaultseasonid = intval($_SESSION['defaultseasonid']);
    $cricket_defaultleagueid = intval($_SESSION['defaultleagueid']);
    $cricket_defaultshow = $_SESSION['defaultshow'];
    $cricket_defaulttable = $_SESSION['defaulttable'];
}

//Gets seasons and leagues and match types for dropdowns
$cricket_get_seasons = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_seasonnames")." WHERE SeasonPublish = '1' ORDER BY SeasonName");
$cricket_get_leagues = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_leaguenames")." WHERE LeaguePublish = '1' ORDER BY LeagueName");

//Sort by points, sort variable is not set
//see if $sort is in GET or POST variables, POST overriding GET (Mithrandir)
if (isset($_GET['sort']) || isset($_POST['sort'])) {
if (isset($_POST['sort'])) {
    $sort = $_POST['sort'];
}
else {
    $sort = $_GET['sort'];
}
}
if(!isset($sort))
{
    $sort = 'pts';
}



?>
<!-- All the data print begin -->
<form method="post" action="change.php">

<table align="center" width="<?php echo $cricket_tb_width ?>" cellspacing="0" cellpadding="0" border="0" bgcolor="<?= $cricket_border_c ?>">
<tr>
<td>
<table width="100%" cellspacing="1" cellpadding="5" border="0">
<tr>
<td bgcolor="<?= $cricket_inside_c ?>" align="center">

<?php


?>

<?php echo _LS_CRICK_CHANGESEASON;?>
<select name="season">
<option value="0"><?php echo _LS_CRICK_ALLSEASONS;?></option>
<?php
while($cricket_data = $xoopsDB->fetchArray($cricket_get_seasons))
{
    if($cricket_data['SeasonID'] == $cricket_defaultseasonid)
    {
        echo "<option value=\"$cricket_data[SeasonID]\" SELECTED>$cricket_data[SeasonName]</option>\n";
    }
    else
    echo "<option value=\"$cricket_data[SeasonID]\">$cricket_data[SeasonName]</option>\n";
}
?>
</select>
<input type="submit" class="button" value=">>" name="submit">
&nbsp;&nbsp;&nbsp;

<?php echo _LS_CRICK_CHANGELEAGUE;?>
<select name="league">
<option value="0"><?php echo _LS_CRICK_ALLLEAGUES;?></option>
<?php
while($cricket_data = $xoopsDB->fetchArray($cricket_get_leagues))
{
    if($cricket_data['LeagueID'] == $cricket_defaultleagueid)
    {
        echo "<option value=\"$cricket_data[LeagueID]\" SELECTED>$cricket_data[LeagueName]</option>\n";
        $draw_line = explode(",", $cricket_data['LeagueLine']);
    }
    else
    echo "<option value=\"$cricket_data[LeagueID]\">$cricket_data[LeagueName]</option>\n";
}
?>
</select>
<input type="submit" class="button" value=">>" name="submit1">
&nbsp;&nbsp;&nbsp;

<?php echo _LS_CRICK_MOVETO;?><select name="moveto">
<option value="headtohead.php"><?php echo _LS_CRICK_HEADTOHEAD;?></option>
<option value="season.php"><?php echo _LS_CRICK_SEASONSTATS;?></option>
<option value="league.php"><?php echo _LS_CRICK_LEAGUESTATS;?></option>
</select> <input type="submit" class="button" value=">>" name="submit6">
<br>
<?php echo _LS_CRICK_CALENDAR;?>
<select name="change_show">
<?php

if($cricket_defaultshow == 1)
{
    echo"<option value=\"1\" SELECTED>"._LS_CRICK_CALENDARALL."</option>
	<option value=\"2\">"._LS_CRICK_CALENDAROWN." </option>
	<option value=\"3\">"._LS_CRICK_CALENDARNONE."</option>";
}
elseif($cricket_defaultshow == 2)
{
    echo"<option value=\"1\">"._LS_CRICK_CALENDARALL."</option>
	<option value=\"2\" SELECTED>"._LS_CRICK_CALENDAROWN."</option>
	<option value=\"3\">"._LS_CRICK_CALENDARNONE."</option>";
}
elseif($cricket_defaultshow == 3)
{
    echo"<option value=\"1\">"._LS_CRICK_CALENDARALL."</option>
	<option value=\"2\">"._LS_CRICK_CALENDAROWN."</option>
	<option value=\"3\" SELECTED>"._LS_CRICK_CALENDARNONE."</option>";
}

//If all is chosen from season or league selector, set default to %
if($cricket_defaultseasonid == 0)
$cricket_defaultseasonid = '%';
if($cricket_defaultleagueid == 0)
$cricket_defaultleagueid = '%';
?>
</select>
<input type="submit" class="button" value=">>" name="submit2">
&nbsp;&nbsp;&nbsp;
<?php echo _LS_CRICK_MODETABLE;?>
<select name="change_table">
<?php
if($cricket_defaulttable == 1)
{
    echo"<option value=\"4\">"._LS_CRICK_MODETABLESIMP."</option>
	<option value=\"1\" SELECTED>"._LS_CRICK_MODETABLETRA."</option>
	<option value=\"2\">"._LS_CRICK_MODETABLEMAT."</option>
	<option value=\"3\">"._LS_CRICK_MODETABLEREC."</option>";
}
elseif($cricket_defaulttable == 2)
{
    echo"<option value=\"4\">"._LS_CRICK_MODETABLESIMP."</option>
	<option value=\"1\" SELECTED>"._LS_CRICK_MODETABLETRA."</option>
	<option value=\"2\">"._LS_CRICK_MODETABLEMAT."</option>
	<option value=\"3\">"._LS_CRICK_MODETABLEREC."</option>";
}
elseif($cricket_defaulttable == 3)
{
    echo"<option value=\"4\">"._LS_CRICK_MODETABLESIMP."</option>
	<option value=\"1\" SELECTED>"._LS_CRICK_MODETABLETRA."</option>
	<option value=\"2\">"._LS_CRICK_MODETABLEMAT."</option>
	<option value=\"3\">"._LS_CRICK_MODETABLEREC."</option>";
}
elseif($cricket_defaulttable == 4)
{
    echo"<option value=\"4\">"._LS_CRICK_MODETABLESIMP."</option>
	<option value=\"1\" SELECTED>"._LS_CRICK_MODETABLETRA."</option>
	<option value=\"2\">"._LS_CRICK_MODETABLEMAT."</option>
	<option value=\"3\">"._LS_CRICK_MODETABLEREC."</option>";
}
?>
</select> <input type="submit" class="button" value=">>" name="submit3">
</td></tr>
</table>
</td></tr>
</table>

<table align="center" width="<?php echo $cricket_tb_width ?>" cellspacing="0" cellpadding="0" border="0" bgcolor="<?php echo $cricket_border_c ?>">
<tr>
<td>

	<table width="100%" cellspacing="1" cellpadding="5" border="0">
	<tr>
	<td bgcolor="<?php echo $cricket_inside_c ?>" align="center">

		<!-- last updated table -->
		<table width="100%" cellspacing="1" cellpadding="2" border="0">
		<tr>
		<td align="left" valign="middle">&nbsp;
		
		</td>
		</tr>
		</table>

		<?php
		//Tarkastetaan, mikä taulukko tulostetaan
		if($cricket_defaulttable == 1 || $cricket_defaulttable == 3)
		{
		?>
		<table width="100%" cellspacing="1" cellpadding="2" border="0">
		<tr>
		<td align="center" valign="middle" colspan="3">&nbsp;</td>

		<td align="center" valign="middle" colspan="7" bgcolor="<?php echo $cricket_top_bg ?>">
		<b><i><?php echo _LS_CRICK_COLOVERALL;?></i></b></td>

		<td align="center" valign="middle" colspan="7" bgcolor="<?php echo $cricket_top_bg ?>">
		<b><i><?php echo _LS_CRICK_COLHOME;?></i></b></td>

		<td align="center" valign="middle" colspan="7" bgcolor="<?php echo $cricket_top_bg ?>">
		<b><i><?php echo _LS_CRICK_COLAWAY;?></i></b></td>

		<td align="center" valign="middle" colspan="2">&nbsp;</td>
		</tr>

		<tr>
		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		&nbsp;<b><?php echo _LS_CRICK_POSSHORT;?></b></td>

		<td align="left" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		&nbsp;<b><?php echo _LS_CRICK_TEAM;?></b></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=pts"><?php echo _LS_CRICK_PTSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=tw"><?php echo _LS_CRICK_WINSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=td"><?php echo _LS_CRICK_DRAWSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=tl"><?php echo _LS_CRICK_LOSESSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=tf"><?php echo _LS_CRICK_RUNSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=ta"><?php echo _LS_CRICK_RUNSAGSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=wtf"><?php echo _LS_CRICK_WICKETSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=wta"><?php echo _LS_CRICK_WICKETSAGSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=hw"><?php echo _LS_CRICK_WINSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=hd"><?php echo _LS_CRICK_DRAWSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=hl"><?php echo _LS_CRICK_LOSESSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=hf"><?php echo _LS_CRICK_RUNSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=ha"><?php echo _LS_CRICK_RUNSAGSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=whf"><?php echo _LS_CRICK_WICKETSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=wha"><?php echo _LS_CRICK_WICKETSAGSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=aw"><?php echo _LS_CRICK_WINSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=ad"><?php echo _LS_CRICK_DRAWSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=al"><?php echo _LS_CRICK_LOSESSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=af"><?php echo _LS_CRICK_RUNSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=aa"><?php echo _LS_CRICK_RUNSAGSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=waf"><?php echo _LS_CRICK_WICKETSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=waa"><?php echo _LS_CRICK_WICKETSAGSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=d">+/-</a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=pld"><?php echo _LS_CRICK_PLAYEDSHORT;?></a></td>
		</tr>
		<?php
		}
		elseif($cricket_defaulttable == 2)
		{
		?>
		<table width="100%" cellspacing="1" cellpadding="2" border="0">
		<tr>
		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		&nbsp;<b><?php echo _LS_CRICK_POSSHORT;?></b></td>

		<td align="left" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		&nbsp;<b><?php echo _LS_CRICK_TEAM;?></b></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=pts"><?php echo _LS_CRICK_PTSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=a_pts"><?php echo _LS_CRICK_AVGPTS;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=w"><?php echo _LS_CRICK_WINSPERC;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=d"><?php echo _LS_CRICK_DRAWSPERC;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=l"><?php echo _LS_CRICK_LOSSPERC;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=af"><?php echo _LS_CRICK_AVGGSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=aa"><?php echo _LS_CRICK_AVGGAGSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=waf"><?php echo _LS_CRICK_AVGWSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=waa"><?php echo _LS_CRICK_AVGWAGSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=awd"><?php echo _LS_CRICK_AVWDIFF;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=agd"><?php echo _LS_CRICK_AVGDIFF;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=pld"><?php echo _LS_CRICK_PLAYEDSHORT;?></a></td>
		</tr>
		<?php
		}
		elseif($cricket_defaulttable == 4)
		{
		?>
		<table width="100%" cellspacing="1" cellpadding="2" border="0">
		<tr>
		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		&nbsp;<b><?php echo _LS_CRICK_POSSHORT;?></b></td>

		<td align="left" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		&nbsp;<b><?php echo _LS_CRICK_TEAM;?></b></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=pts"><?php echo _LS_CRICK_PTSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=tw"><?php echo _LS_CRICK_WINSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=td"><?php echo _LS_CRICK_DRAWSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=tl"><?php echo _LS_CRICK_LOSESSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=tf"><?php echo _LS_CRICK_RUNSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=ta"><?php echo _LS_CRICK_RUNSAGSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=wtf"><?php echo _LS_CRICK_WICKETSSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=wta"><?php echo _LS_CRICK_WICKETSAGSHORT;?></a></td>

		<td align="center" valign="middle" bgcolor="<?php echo $cricket_top_bg ?>">
		<a href="?sort=pld"><?php echo _LS_CRICK_PLAYEDSHORT;?></a></td>
		</tr>
		<?php
		}
		?>

		<?php
		//query to get teams from selected season & league
		$cricket_get_teams = $xoopsDB->query("SELECT DISTINCT
		O.OpponentName AS name,
		O.OpponentID AS id
		FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
		WHERE LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
		LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid' AND
		(O.OpponentID = LM.LeagueMatchHomeID OR
		O.OpponentID = LM.LeagueMatchAwayID)
		ORDER BY name
		");
		
		//Lets read teams into the table
		$i = 0;
		while($cricket_data = $xoopsDB->fetchArray($cricket_get_teams))
		{
		    $team[$i] = $cricket_data['name'];
		    $teamid[$i] = $cricket_data['id'];
		    
		    //Which table style is chosen
		    if($cricket_defaulttable == 1 || $cricket_defaulttable == 2 || $cricket_defaulttable == 4)
		    {
		        //Home Bonus Points Data
		        $query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchHomeBonus) AS homebonus
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Home Bonus Points into the table
		        $mdata = $xoopsDB->fetchArray($query);
		        $homebonus[$i] = $mdata['homebonus'];

		        //Away Bonus Points Data
		        $query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchHomeBonus) AS awaybonus
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Away Bonus Points into the table
		        $mdata = $xoopsDB->fetchArray($query);
		        $awaybonus[$i] = $mdata['awaybonus'];

		        //Home Data
		        $query = $xoopsDB->query("SELECT
				COUNT(DISTINCT LM.LeagueMatchID) AS homewins
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeWinnerID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Home wins into the table
		        $mdata = $xoopsDB->fetchArray($query);
		        $homewins[$i] = $mdata['homewins'];
		        
		        //Home draws
				$query = $xoopsDB->query("SELECT
				COUNT(DISTINCT LM.LeagueMatchID) AS homedraws
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeTieID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Home draws into the table
		        $mdata = $xoopsDB->fetchArray($query);
		        $homedraws[$i] = $mdata['homedraws'];
		        
		        //Home Losses
				$query = $xoopsDB->query("SELECT
				COUNT(DISTINCT LM.LeagueMatchID) AS homeloses
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeLoserID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Home loses into the table
		        $mdata = $xoopsDB->fetchArray($query);
		        $homeloses[$i] = $mdata['homeloses'];
		        
		        //Away Data
		        $query = $xoopsDB->query("SELECT
				COUNT(DISTINCT LM.LeagueMatchID) AS awaywins
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayWinnerID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Away wins into the table
		        $mdata = $xoopsDB->fetchArray($query);
		        $awaywins[$i] = $mdata['awaywins'];
		        
		        //Away Draws
				$query = $xoopsDB->query("SELECT
				COUNT(DISTINCT LM.LeagueMatchID) AS awaydraws
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayTieID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Away draws into the table
		        $mdata = $xoopsDB->fetchArray($query);
		        $awaydraws[$i] = $mdata['awaydraws'];
		        
		        //Away Losses
				$query = $xoopsDB->query("SELECT
				COUNT(DISTINCT LM.LeagueMatchID) AS awayloses
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayLoserID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Away loses into the table
		        $mdata = $xoopsDB->fetchArray($query);
		        $awayloses[$i] = $mdata['awayloses'];
		        
		        //Home Runs Data
		        $query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchHomeRuns) AS homeruns
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Home Runs scored
		        $mdata = $xoopsDB->fetchArray($query);
		        if(is_null($mdata['homeruns']))
		        $homeruns[$i] = 0;
		        else
		        $homeruns[$i] = $mdata['homeruns'];
		        
		        //Home Runs scored Against
				$query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchAwayRuns) AS homerunsagainst
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Home Runs against
		        $mdata = $xoopsDB->fetchArray($query);
		        if(is_null($mdata['homerunsagainst']))
		        $homerunsagainst[$i] = 0;
		        else
		        $homerunsagainst[$i] = $mdata['homerunsagainst'];
		        
		        //Away Runs Data
				$query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchAwayRuns) AS awayruns
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Away Runs scored
		        $mdata = $xoopsDB->fetchArray($query);
		        if(is_null($mdata['awayruns']))
		        $awayruns[$i] = 0;
		        else
		        $awayruns[$i] = $mdata['awayruns'];
		        
		        //Away Runs scored Against
				$query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchHomeRuns) AS awayrunsagainst
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Away Runs against
		        $mdata = $xoopsDB->fetchArray($query);
		        if(is_null($mdata['awayrunsagainst']))
		        $awayrunsagainst[$i] = 0;
		        else
		        $awayrunsagainst[$i] = $mdata['awayrunsagainst'];
				
		        //Home Wickets Data
		        $query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchHomeWickets) AS homewickets
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Home Wickets scored
		        $mdata = $xoopsDB->fetchArray($query);
		        if(is_null($mdata['homewickets']))
		        $homewickets[$i] = 0;
		        else
		        $homewickets[$i] = $mdata['homewickets'];
		        
		        //Home Wickets scored Against
				$query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchAwayWickets) AS homewicketsagainst
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchHomeID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Home Wickets against
		        $mdata = $xoopsDB->fetchArray($query);
		        if(is_null($mdata['homewicketsagainst']))
		        $homewicketsagainst[$i] = 0;
		        else
		        $homewicketsagainst[$i] = $mdata['homewicketsagainst'];
		        
		        //Away Wickets Data
				$query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchAwayWickets) AS awaywickets
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Away Runs scored
		        $mdata = $xoopsDB->fetchArray($query);
		        if(is_null($mdata['awaywickets']))
		        $awaywickets[$i] = 0;
		        else
		        $awaywickets[$i] = $mdata['awaywickets'];
		        
		        //Away Wickets scored Against
				$query = $xoopsDB->query("SELECT
				SUM( LM.LeagueMatchHomeWickets) AS awaywicketsagainst
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				LM.LeagueMatchAwayID = '$teamid[$i]' AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				");
		        
		        //Away Wickets against
		        $mdata = $xoopsDB->fetchArray($query);
		        if(is_null($mdata['awaywicketsagainst']))
		        $awaywicketsagainst[$i] = 0;
		        else
		        $awaywicketsagainst[$i] = $mdata['awaywicketsagainst'];
		    }

		    //Recent form
		    elseif($cricket_defaulttable == 3)
		    {
		        //Counters are set to zero
		        $homewins[$i] = 0;
		        $homedraws[$i] = 0;
		        $homeloses[$i] = 0;
		        $awaywins[$i] = 0;
		        $awaydraws[$i] = 0;
		        $awayloses[$i] = 0;
		        $homebonus[$i] = 0;
		        $awaybonus[$i] = 0;
		        $homeruns[$i] = 0;
		        $homerunsagainst[$i] = 0;
		        $awayruns[$i] = 0;
		        $awayrunsagainst[$i] = 0;
		        $homewickets[$i] = 0;
		        $homewicketsagainst[$i] = 0;
		        $awaywickets[$i] = 0;
		        $awaywicketsagainst[$i] = 0;
		        
		        //query to get latest 6 matches
		        $query = $xoopsDB->query("SELECT
				LM.LeagueMatchHomeID AS homeid,
				LM.LeagueMatchAwayID AS awayid,
				LM.LeagueMatchHomewinnerID AS homewinner,
				LM.LeagueMatchHomeLoserID AS homeloser,
				LM.LeagueMatchAwayWinnerID AS awaywinner,
				LM.LeagueMatchAwayLoserID AS awayloser,
				LM.LeagueMatchHomeTieID AS hometie,
				LM.LeagueMatchAwayTieID AS awaytie,
				LM.LeagueMatchHomeBonus AS homebonus,
				LM.LeagueMatchAwayBonus AS awaybonus,
				LM.LeagueMatchHomeRuns AS homeruns,
				LM.LeagueMatchAwayRuns AS awayruns,
				LM.LeagueMatchHomeWickets AS homewickets,
				LM.LeagueMatchAwayWickets AS awaywickets
				FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				(LM.LeagueMatchHomeWinnerID = '$teamid[$i]' OR
				LM.LeagueMatchHomeLoserID = '$teamid[$i]' OR
				LM.LeagueMatchAwayWinnerID = '$teamid[$i]' OR
				LM.LeagueMatchAwayLoserID = '$teamid[$i]' OR
				LM.LeagueMatchHomeTieID = '$teamid[$i]' OR
				LM.LeagueMatchAwayTieID = '$teamid[$i]') AND
				LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
				LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
				ORDER BY LM.LeagueMatchDate DESC
				LIMIT 6
				");
		        
		        //Lets use while to get correct numbers
		        while($row = $xoopsDB->fetchArray($query))
		        {
		            //If bonus points are null
		            if(is_null($row['homebonus']))
		            $row['homebonus'] = 0;
		            
		            if(is_null($row['awaybonus']))
		            $row['awaybonus'] = 0;

		            //If runs & wickets are null
		            if(is_null($row['homeruns']))
		            $row['homeruns'] = 0;
		            
		            if(is_null($row['awayruns']))
		            $row['awayruns'] = 0;
		            
		            if(is_null($row['homewickets']))
		            $row['homewickets'] = 0;
		            
		            if(is_null($row['awaywickets']))
		            $row['awaywickets'] = 0;
		            
		            //Home win
		            if($row['homewinner'] == $teamid[$i])
		            {
		                $homewins[$i]++;
		            }
		            //Home lost
		            elseif($row['homeloser'] == $teamid[$i])
		            {
		                $homeloses[$i]++;
		            }
		            //Home draw
		            elseif($row['hometie'] == $teamid[$i])
		            {
		                $homedraws[$i]++;
		            }
		            //Away win
		            elseif($row['awaywinner'] == $teamid[$i])
		            {
		                $awaywins[$i]++;
		            }
		            //Away lost
		            elseif($row['awayloser'] == $teamid[$i])
		            {
		                $awayloses[$i]++;
		            }
		            //Away draw
		            elseif($row['awaytie'] == $teamid[$i])
		            {
		                $awaydraws[$i]++;
		            }
		            
		            //Calculates runs and runs against
		            if($row['homeid'] == $teamid[$i])
		            {
		                $homebonus[$i] = $homebonus[$i] + $row['homebonus'];
		                $homeruns[$i] = $homeruns[$i] + $row['homeruns'];
						$homewickets[$i] = $homewickets[$i] + $row['homewickets'];
		                $homerunsagainst[$i] = $homerunsagainst[$i] + $row['awayruns'];
						$homewicketsagainst[$i] = $homewicketsagainst[$i] + $row['awaywickets'];
		            }
		            else
		            {
						$awaybonus[$i] = $awaybonus[$i] + $row['awaybonus'];
						$awayruns[$i] = $awayruns[$i] + $row['awayruns'];
						$awaywickets[$i] = $awaywickets[$i] + $row['awaywickets'];
		                $awayrunsagainst[$i] = $awayrunsagainst[$i] + $row['homeruns'];
						$awaywicketsagainst[$i] = $awaywicketsagainst[$i] + $row['homewickets'];
		            }
		        }
		    }
		    
		    //Check what table is used..
		    if($cricket_defaulttable == 1 || $cricket_defaulttable == 3 || $cricket_defaulttable == 4)
		    {
		        //Calculates points and matches
		        $bonus[$i] = ($homebonus[$i]+$awaybonus[$i]);
		        $wins[$i] = ($homewins[$i]+$awaywins[$i]);
		        $draws[$i] = ($homedraws[$i]+$awaydraws[$i]);
		        $loses[$i] = ($homeloses[$i]+$awayloses[$i]);
		        $runs_for[$i] = ($homeruns[$i] + $awayruns[$i]);
		        $runs_against[$i] = ($homerunsagainst[$i] + $awayrunsagainst[$i]);
		        $wickets_for[$i] = ($homewickets[$i] + $awaywickets[$i]);
		        $wickets_against[$i] = ($homewicketsagainst[$i] + $awaywicketsagainst[$i]);
		        
		        //Lets make change in points if there are data in cricket_deductedpoints-table
		        if($cricket_defaulttable == 1 || $cricket_defaulttable == 4)
		        {
		            $cricket_get_deduct = $xoopsDB->query("SELECT points
					FROM ".$xoopsDB->prefix("cricket_deductedpoints")." 
					WHERE seasonid LIKE '$cricket_defaultseasonid' AND
					leagueid LIKE '$cricket_defaultleagueid' AND
					teamid = '$teamid[$i]'
					LIMIT 1
					");
		            
		            $temp_points = 0;
		            
		            if($xoopsDB->getRowsNum($cricket_get_deduct) > 0)
		            {
		                while($cricket_d_points = $xoopsDB->fetchArray($cricket_get_deduct))
		                {
		                    $temp_points = $temp_points + $cricket_d_points['points'];
		                }
		            }
		        }
		        else
		        {
		            $temp_points = 0;
		        }
		        
		        $points[$i] = $temp_points + $bonus[$i] + (($homewins[$i]+$awaywins[$i])*$cricket_for_win) + (($homedraws[$i]+$awaydraws[$i])*$cricket_for_draw) + (($homeloses[$i]+$awayloses[$i])*$cricket_for_lose);
		        $pld[$i] = $homewins[$i]+$homedraws[$i]+$homeloses[$i]+$awaywins[$i]+$awaydraws[$i]+$awayloses[$i];
		        
		        //Calculates run differences
		        $diff[$i] = ($homeruns[$i] + $awayruns[$i]) - ($homerunsagainst[$i] + $awayrunsagainst[$i]);
		    }
		    elseif($cricket_defaulttable == 2)
		    {
		        $bonus[$i] = ($homebonus[$i]+$awaybonus[$i]);
		        $wins[$i] = ($homewins[$i]+$awaywins[$i]);
		        $draws[$i] = ($homedraws[$i]+$awaydraws[$i]);
		        $loses[$i] = ($homeloses[$i]+$awayloses[$i]);
		        $runs_for[$i] = ($homeruns[$i] + $awayruns[$i]);
		        $runs_against[$i] = ($homerunsagainst[$i] + $awayrunsagainst[$i]);
		        $wickets_for[$i] = ($homewickets[$i] + $awaywickets[$i]);
		        $wickets_against[$i] = ($homewicketsagainst[$i] + $awaywicketsagainst[$i]);
		        
		        //Lets make change in points if there are data in cricket_deductedpoints-table
		        $cricket_get_deduct = $xoopsDB->query("SELECT points
				FROM ".$xoopsDB->prefix("cricket_deductedpoints")."
				WHERE seasonid LIKE '$cricket_defaultseasonid' AND
				leagueid LIKE '$cricket_defaultleagueid' AND
				teamid = '$teamid[$i]'
				LIMIT 1
				");
		        
		        $temp_points = 0;
		        
		        if($xoopsDB->getRowsNum($cricket_get_deduct) > 0)
		        {
		            while($cricket_d_points = $xoopsDB->fetchArray($cricket_get_deduct))
		            {
		                $temp_points = $temp_points + $cricket_d_points['points'];
		            }
		        }
		        
		        $points[$i] = $temp_points + $bonus[$i] +(($homewins[$i]+$awaywins[$i])*$cricket_for_win) + (($homedraws[$i]+$awaydraws[$i])*$cricket_for_draw) + (($homeloses[$i]+$awayloses[$i])*$cricket_for_lose);
		        $pld[$i] = $homewins[$i]+$homedraws[$i]+$homeloses[$i]+$awaywins[$i]+$awaydraws[$i]+$awayloses[$i];
		        
		        //To avoid divide by zero
		        if($pld[$i] != 0)
		        {
		            $win_pros[$i] = round(100*($wins[$i]/$pld[$i]), 2);
		            $draw_pros[$i] = round(100*($draws[$i]/$pld[$i]), 2);
		            $loss_pros[$i] = round(100*($loses[$i]/$pld[$i]), 2);
		            
		            $av_points[$i] = round($points[$i]/$pld[$i], 2);
		            
		            $av_for[$i] = round($runs_for[$i]/$pld[$i], 2);
		            $av_against[$i] = round($runs_against[$i]/$pld[$i], 2);
		            $avw_for[$i] = round($wickets_for[$i]/$pld[$i], 2);
		            $avw_against[$i] = round($wickets_against[$i]/$pld[$i], 2);
		        }
		        else
		        {
		            $win_pros[$i] = 0;
		            $draw_pros[$i] = 0;
		            $loss_pros[$i] = 0;
		            
		            $av_points[$i] = 0;
		            
		            $av_for[$i] = 0;
		            $av_against[$i] = 0;
		            $avw_for[$i] = 0;
		            $avw_against[$i] = 0;
		        }
		        
		        $av_diff[$i] = $av_for[$i] - $av_against[$i];
		        $avw_diff[$i] = $avw_for[$i] - $avw_against[$i];
		        
		    }
		    
		    $i++;
		}
		
		$qty = $xoopsDB->getRowsNum($cricket_get_teams);
		
		//Which table?
		if($cricket_defaulttable == 1 || $cricket_defaulttable == 3 || $cricket_defaulttable == 4)
		{
		    //What sort type is chosen?
		    switch($sort)
		    {
		        case 'pts':
                    if (isset($points)){
		        array_multisort($points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'd':
                    if (isset($diff)){
		        array_multisort($diff, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'pld':
                    if (isset($pld)){
		        array_multisort($pld, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'tw':
                    if (isset($wins)){
		        array_multisort($wins, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC,  $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_ASC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses,$pld, SORT_DESC, SORT_NUMERIC, $team, $homedraws, $homeloses,$homewins, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'td':
                    if (isset($draws)){
		        array_multisort($draws, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homeloses, $awaywins, $homedraws, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'tl':
                    if (isset($loses)){
		        array_multisort($loses, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $awaywins, $awaydraws, $homeloses, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                }
                    break;
		        
		        case 'tf':
                    if (isset($runs_for)){
		        array_multisort($runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'ta':
                    if (isset($runs_against)){
		        array_multisort($runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'wf':
                    if (isset($wickets_for)){
		        array_multisort($wickets_for, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'wa':
                    if (isset($wickets_against)){
		        array_multisort($wickets_against, SORT_ASC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_ASC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;

		        case 'hw':
                    if (isset($homewins)){
		        array_multisort($homewins, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC,  $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses,$pld, SORT_DESC, SORT_NUMERIC, $team, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'hd':
                    if (isset($homedraws)){
		        array_multisort($homedraws, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'hl':
                    if (isset($homeloses)){
		        array_multisort($homeloses, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'hf':
                    if (isset($homeruns)){
		        array_multisort($homeruns, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'ha':
                    if (isset($homerunsagainst)){
		        array_multisort($homerunsagainst, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'whf':
                    if (isset($homewickets)){
		        array_multisort($homewickets, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_ASC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'wha':
                    if (isset($homewicketsagainst)){
		        array_multisort($homewicketsagainst, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_ASC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;

		        case 'aw':
                    if (isset($awaywins)){
		        array_multisort($awaywins, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'ad':
                    if (isset($awaydraws)){
		        array_multisort($awaydraws, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'al':
                    if (isset($awayloses)){
		        array_multisort($awayloses, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'af':
                    if (isset($awayruns)){
		        array_multisort($awayruns, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'aa':
                    if (isset($awayrunsagainst)){
		        array_multisort($awayrunsagainst, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'waf':
                    if (isset($awaywickets)){
		        array_multisort($awaywickets, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		        
		        case 'waa':
                    if (isset($awaywicketsagainst)){
		        array_multisort($awaywicketsagainst, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;

		        default:
                    if (isset($points)){
		        array_multisort($points, SORT_DESC, SORT_NUMERIC, $diff, SORT_DESC, SORT_NUMERIC, $runs_for, SORT_DESC, SORT_NUMERIC, $wickets_for, SORT_DESC, SORT_NUMERIC, $wins, SORT_DESC, SORT_NUMERIC, $runs_against, SORT_ASC, SORT_NUMERIC, $wickets_against, SORT_ASC, SORT_NUMERIC, $draws, $loses, $pld, SORT_DESC, SORT_NUMERIC, $team, $homewins, $homedraws, $homeloses, $awaywins, $awaydraws, $awayloses, $homeruns, $homerunsagainst, $awayruns, $awayrunsagainst, $homewickets, $homewicketsagainst, $awaywickets, $awaywicketsagainst);
                    }
                        break;
		    }
		    
		    if($cricket_defaulttable == 1 || $cricket_defaulttable == 3)
		    {
		        //Lets print data
		        $j=1;
		        $i=0;
		        while($i< $qty)
		        {
		            if(isset($draw_line))
		            {
		                for($k = 0 ; $k < sizeof($draw_line) ; $k++)
		                {
		                    if($draw_line[$k] == $i)
		                    {
		                        $templine_width = $cricket_tb_width-10;
		                        echo"
								<tr>
								<td height=\"5\" colspan=\"27\" align=\"center\" valign=\"middle\">
								<img src=\"images/line.gif\" width=\"$templine_width\" height=\"5\" ALT=\"\"><br>
								</td>
								</tr>
								";
		                    }
		                }
		            }
		            
		            echo"
					<tr>
					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">
					&nbsp;<b>$j</b></td>

					<td align=\"left\" valign=\"middle\" bgcolor=\"$cricket_bg1\">
					&nbsp;<b>$team[$i]</b></td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg2\">";
		            if($sort == 'pts')
		            echo'<b>';
		            
		            echo"$points[$i]";
		            
		            if($sort == 'pts')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'tw')
		            echo'<b>';
		            
		            echo"$wins[$i]";
		            
		            if($sort == 'tw')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'td')
		            echo'<b>';
		            
		            echo"$draws[$i]";
		            
		            if($sort == 'td')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'tl')
		            echo'<b>';
		            
		            echo"$loses[$i]";
		            
		            if($sort == 'tl')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'tf')
		            echo'<b>';
		            
		            echo"$runs_for[$i]";
		            
		            if($sort == 'tf')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'ta')
		            echo'<b>';
		            
		            echo"$runs_against[$i]";
		            
		            if($sort == 'ta')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'wf')
		            echo'<b>';
		            
		            echo"$wickets_for[$i]";
		            
		            if($sort == 'wf')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'wa')
		            echo'<b>';
		            
		            echo"$wickets_against[$i]";
		            
		            if($sort == 'wa')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'hw')
		            echo'<b>';
		            
		            echo"$homewins[$i]";
		            
		            if($sort == 'hw')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'hd')
		            echo'<b>';
		            
		            echo"$homedraws[$i]";
		            
		            if($sort == 'hd')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'hl')
		            echo'<b>';
		            
		            echo"$homeloses[$i]";
		            
		            if($sort == 'hl')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'hf')
		            echo'<b>';
		            
		            echo"$homeruns[$i]";
		            
		            if($sort == 'hf')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'ha')
		            echo'<b>';
		            
		            echo"$homerunsagainst[$i]";
		            
		            if($sort == 'ha')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'whf')
		            echo'<b>';
		            
		            echo"$homewickets[$i]";
		            
		            if($sort == 'whf')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'wha')
		            echo'<b>';
		            
		            echo"$homewicketsagainst[$i]";
		            
		            if($sort == 'wha')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'aw')
		            echo'<b>';
		            
		            echo"$awaywins[$i]";
		            
		            if($sort == 'aw')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'ad')
		            echo'<b>';
		            
		            echo"$awaydraws[$i]";
		            
		            if($sort == 'ad')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'al')
		            echo'<b>';
		            
		            echo"$awayloses[$i]";
		            
		            if($sort == 'al')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'af')
		            echo'<b>';
		            
		            echo"$awayruns[$i]";
		            
		            if($sort == 'af')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'aa')
		            echo'<b>';
		            
		            echo"$awayrunsagainst[$i]";
		            
		            if($sort == 'aa')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'waf')
		            echo'<b>';
		            
		            echo"$awaywickets[$i]";
		            
		            if($sort == 'waf')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'waa')
		            echo'<b>';
		            
		            echo"$awaywicketsagainst[$i]";
		            
		            if($sort == 'waa')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'd')
		            echo'<b>';
		            
		            if($diff[$i] > 0)
		            echo'+';
		            
		            echo"$diff[$i]";
		            
		            if($sort == 'd')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg2\">";
		            if($sort == 'pld')
		            echo'<b>';
		            
		            echo"$pld[$i]";
		            
		            if($sort == 'pld')
		            echo'</b>';
		            echo"</td>
					</tr>";
		            
		            $i++;
		            $j++;
		        }
		    }

		    //Simple table print
		    elseif($cricket_defaulttable == 4)
		    {
		        //Lets print data
		        $j=1;
		        $i=0;
		        while($i< $qty)
		        {
		            if(isset($draw_line))
		            {
		                for($k = 0 ; $k < sizeof($draw_line) ; $k++)
		                {
		                    if($draw_line[$k] == $i)
		                    {
		                        $templine_width = $cricket_tb_width-10;
		                        echo"
								<tr>
								<td height=\"5\" colspan=\"22\" align=\"center\" valign=\"middle\">
								<img src=\"images/line.gif\" width=\"$templine_width\" height=\"5\" alt=\"\"><br>
								</td>
								</tr>
								";
		                    }
		                }
		            }
		            
		            echo"
					<tr>
					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">
					&nbsp;<b>$j</b></td>

					<td align=\"left\" valign=\"middle\" bgcolor=\"$cricket_bg1\">
					&nbsp;<b>$team[$i]</b></td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg2\">";
		            if($sort == 'pts')
		            echo'<b>';
		            
		            echo"$points[$i]";
		            
		            if($sort == 'pts')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'tw')
		            echo'<b>';
		            
		            echo"$wins[$i]";
		            
		            if($sort == 'tw')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'td')
		            echo'<b>';
		            
		            echo"$draws[$i]";
		            
		            if($sort == 'td')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'tl')
		            echo'<b>';
		            
		            echo"$loses[$i]";
		            
		            if($sort == 'tl')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'tf')
		            echo'<b>';
		            
		            echo"$runs_for[$i]";
		            
		            if($sort == 'tf')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'ta')
		            echo'<b>';
		            
		            echo"$runs_against[$i]";
		            
		            if($sort == 'ta')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'wtf')
		            echo'<b>';
		            
		            echo"$wickets_for[$i]";
		            
		            if($sort == 'wtf')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		            if($sort == 'wta')
		            echo'<b>';
		            
		            echo"$wickets_against[$i]";
		            
		            if($sort == 'wta')
		            echo'</b>';
		            echo"</td>

					<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg2\">";
		            if($sort == 'pld')
		            echo'<b>';
		            
		            echo"$pld[$i]";
		            
		            if($sort == 'pld')
		            echo'</b>';
		            echo"</td>
					</tr>";
		            
		            $i++;
		            $j++;
		        }
		    }
		}
		elseif($cricket_defaulttable == 2)
		{
		    //What sort type is chosen?
		    switch($sort)
		    {
		        case 'pts':
                    if (isset($points)){
		        array_multisort($points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $avw_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $avw_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $avw_against, SORT_ASC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                        break;
		        
		        case 'a_pts':
                    if (isset($points)){
		        array_multisort($av_points, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $avw_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $avw_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $avw_against, SORT_ASC, SORT_NUMERIC, $pld, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                        break;
		        
		        case 'w':
                    if (isset($win_pros)){
		        array_multisort($win_pros, SORT_DESC, SORT_NUMERIC, $av_points, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $avw_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $avw_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $avw_against, SORT_ASC, SORT_NUMERIC, $pld, $draw_pros, $loss_pros, $team);
                    }
                        break;
		        
		        case 'd':
                    if (isset($draw_pros)){
		        array_multisort($draw_pros, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $avw_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $avw_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $avw_against, SORT_ASC, SORT_NUMERIC, $pld, $av_points, $win_pros, $loss_pros, $team);
                    }
                        break;
		        
		        case 'l':
                    if (isset($loss_pros)){
		        array_multisort($loss_pros, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $avw_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $avw_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $avw_against, SORT_ASC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $team);
                    }
                        break;
		        
		        case 'af':
                    if (isset($av_for)){
		        array_multisort($av_for, SORT_DESC, SORT_NUMERIC, $avw_for, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $avw_against, SORT_ASC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                        break;
		        
		        case 'aa':
                    if (isset($av_against)){
		        array_multisort($av_against, SORT_ASC, SORT_NUMERIC, $avw_against, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $avw_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $avw_for, SORT_DESC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                        break;
		        
		        case 'waf':
                    if (isset($avw_for)){
		        array_multisort($avw_for, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $avw_diff, SORT_DESC, SORT_NUMERIC, $avw_against, SORT_ASC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                        break;
		        
		        case 'waa':
                    if (isset($avw_against)){
		        array_multisort($avw_against, SORT_ASC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $avw_diff, SORT_DESC, SORT_NUMERIC, $avw_for, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                        break;

		        case 'agd':
                    if (isset($av_diff)){
		        array_multisort($av_diff, SORT_DESC, SORT_NUMERIC, $avw_diff, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $avw_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $avw_against, SORT_ASC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                        break;
		        
		        case 'awd':
                    if (isset($avw_diff)){
		        array_multisort($avw_diff, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $avw_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $avw_against, SORT_ASC, SORT_NUMERIC, $pld, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                        break;

		        case 'pld':
                    if (isset($pld)){
		        array_multisort($pld, SORT_DESC, SORT_NUMERIC, $points, SORT_DESC, SORT_NUMERIC, $av_diff, SORT_DESC, SORT_NUMERIC, $avw_diff, SORT_DESC, SORT_NUMERIC, $av_for, SORT_DESC, SORT_NUMERIC, $avw_for, SORT_DESC, SORT_NUMERIC, $av_against, SORT_ASC, SORT_NUMERIC, $avw_against, SORT_ASC, SORT_NUMERIC, $av_points, $win_pros, $draw_pros, $loss_pros, $team);
                    }
                        break;
		        
		    }
		    
		    //Print data
		    $j=1;
		    $i=0;
		    while($i< $qty)
		    {
		        $av_points[$i] = number_format($av_points[$i], 0, '.', '');
		        $av_for[$i] = number_format($av_for[$i], 0, '.', '');
		        $av_against[$i] = number_format($av_against[$i], 0, '.', '');
		        $avw_for[$i] = number_format($avw_for[$i], 0, '.', '');
		        $avw_against[$i] = number_format($avw_against[$i], 0, '.', '');
		        $av_temp = number_format($av_diff[$i], 0, '.', '');
		        $avw_temp = number_format($avw_diff[$i], 0, '.', '');
		        $win_pros[$i] = number_format($win_pros[$i], 0, '.', '');
		        $draw_pros[$i] = number_format($draw_pros[$i], 0, '.', '');
		        $loss_pros[$i] = number_format($loss_pros[$i], 0, '.', '');
		        
		        echo"
				<tr>
				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">
				&nbsp;<b>$j</b></td>

				<td align=\"left\" valign=\"middle\" bgcolor=\"$cricket_bg1\">
				&nbsp;<b>$team[$i]</b></td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg2\">";
		        if($sort == 'pts')
		        echo'<b>';
		        
		        echo"$points[$i]";
		        
		        if($sort == 'pts')
		        echo'</b>';
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		        if($sort == 'a_pts')
		        echo'<b>';
		        
		        echo"$av_points[$i]";
		        
		        if($sort == 'a_pts')
		        echo'</b>';
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		        if($sort == 'w')
		        echo'<b>';
		        
		        echo"$win_pros[$i]";
		        
		        if($sort == 'w')
		        echo'</b>';
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		        if($sort == 'd')
		        echo'<b>';
		        
		        echo"$draw_pros[$i]";
		        
		        if($sort == 'd')
		        echo'</b>';
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		        if($sort == 'l')
		        echo'<b>';
		        
		        echo"$loss_pros[$i]";
		        
		        if($sort == 'l')
		        echo'</b>';
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		        if($sort == 'af')
		        echo'<b>';
		        
		        echo"$av_for[$i]";
		        
		        if($sort == 'af')
		        echo'</b>';
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		        if($sort == 'aa')
		        echo'<b>';
		        
		        echo"$av_against[$i]";
		        
		        if($sort == 'aa')
		        echo'</b>';
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		        if($sort == 'waf')
		        echo'<b>';
		        
		        echo"$avw_for[$i]";
		        
		        if($sort == 'waf')
		        echo'</b>';
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		        if($sort == 'waa')
		        echo'<b>';
		        
		        echo"$avw_against[$i]";
		        
		        if($sort == 'waa')
		        echo'</b>';
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		        if($sort == 'awd')
		        echo'<b>';
		        
		        if($avw_diff[$i] >= 0)
		        echo'+';
		        
		        echo"$avw_temp";
		        
		        if($sort == 'awd')
		        echo'</b>';
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg1\">";
		        if($sort == 'agd')
		        echo'<b>';
		        
		        if($av_diff[$i] >= 0)
		        echo'+';
		        
		        echo"$av_temp";
		        
		        if($sort == 'agd')
		        echo'</b>';
		        echo"</td>

				<td align=\"center\" valign=\"middle\" bgcolor=\"$cricket_bg2\">";
		        if($sort == 'pld')
		        echo'<b>';
		        
		        echo"$pld[$i]";
		        
		        if($sort == 'pld')
		        echo'</b>';
		        echo"</td>
				</tr>";
		        
		        $i++;
		        $j++;
		    }
		}
		?>
		</table>

		<?php
		//Check if match calendar want to be shown
		if($cricket_defaultshow != 3)
		{
		?>

		<!-- Last Updated -->
               <div height="15" align="left" valign="top"> 
               <h6><?php echo _LS_CRICK_LASTUPDT;?><?= "$last_update" ?></h6>
               </div>

                <?php
                include('notes.txt');
		?>	

        <div align="center" valign="top">
		<h3><?php echo _LS_CRICK_CALENDARFIXED;?></h3>
		</div>

                <div  align="center" width="100%">
                <table width="100%"><tr>
                <td align="center" width="50%" bgcolor="#E6E6FF"><?php echo _LS_CRICK_MATPLD;?></td>
                <td align="center" width="50%" bgcolor="#E6E6FF"><?php echo _LS_CRICK_MATUPC;?></td>
                </tr></table>
                </div>
            
    <table width="100%">
       <tr>
         <td width="50%"> 
                <table border="0" width="100%" cellspacing="2" cellpadding="2">

		<?php
		//How to print date
		if($cricket_print_date == 1)
		{
		    $cricket_print_date = '%d.%m.%Y';
		}
		elseif($cricket_print_date == 2)
		{
		    $cricket_print_date = '%m.%d.%Y';
		}
		elseif($cricket_print_date == 3)
		{
		    $cricket_print_date = '%b %D %Y';
		}
		
		//Check which matche want to be printed
		//All
		if($cricket_defaultshow == 1)
		{
		    $cricket_get_matches = $xoopsDB->query("SELECT O.OpponentName AS hometeam,
			OP.OpponentName AS awayteam,
			LM.LeagueMatchHomeRuns AS runs_home,
			LM.LeagueMatchAwayRuns AS runs_away,
			LM.LeagueMatchHomeWickets AS wickets_home,
			LM.LeagueMatchAwayWickets AS wickets_away,
			LM.LeagueMatchID AS id,
			DATE_FORMAT(LM.LeagueMatchDate, '$cricket_print_date') AS date
			FROM ".$xoopsDB->prefix("cricket_leaguematches")." LM, ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_opponents")." OP
			WHERE O.OpponentID = LM.LeagueMatchHomeID AND
			OP.OpponentID = LM.LeagueMatchAwayID AND
            LM.LeagueMatchDate < CURDATE() AND
			LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
			LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
			ORDER BY LM.LeagueMatchDate DESC");
		}
		//Own only
		else
		{
		    $cricket_get_matches = $xoopsDB->query("SELECT O.OpponentName AS hometeam,
			OP.OpponentName AS awayteam,
			LM.LeagueMatchHomeRuns AS runs_home,
			LM.LeagueMatchAwayRuns AS runs_away,
			LM.LeagueMatchHomeWickets AS wickets_home,
			LM.LeagueMatchAwayWickets AS wickets_away,
			LM.LeagueMatchID AS id,
			DATE_FORMAT(LM.LeagueMatchDate, '$cricket_print_date') AS date
			FROM ".$xoopsDB->prefix("cricket_leaguematches")." LM, ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_opponents")." OP
			WHERE O.OpponentID = LM.LeagueMatchHomeID AND
			OP.OpponentID = LM.LeagueMatchAwayID AND
            LM.LeagueMatchDate < CURDATE() AND
			LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
			LeagueMatchLeagueID LIKE '$cricket_defaultleagueid' AND
			(O.OpponentOwn = '1' OR OP.OpponentOwn = '1')
			ORDER BY LM.LeagueMatchDate DESC");
		}
		
		if($xoopsDB->getRowsNum($cricket_get_matches) < 1)
		{
		    echo "&nbsp;<b>"._LS_CRICK_NOMATCHES."</b>";
		}
		else
		{
		    $i = 0;
		    $temp = '';
		    
		    while($cricket_data = $xoopsDB->fetchArray($cricket_get_matches))
		    {
		        if($i == 0)
		        {
		            echo"
					<tr>
					<td style=\"padding-left:5px;\" align=\"left\" colspan=\"2\">
					<u><b>$cricket_data[date]</b></u>
					</td>
					</tr>
					";
		        }
		        
		        if($cricket_data['date'] != "$temp" && $i > 0)
		        {
		            echo"
					<tr>
					<td style=\"padding-left:5px;\" align=\"left\" colspan=\"2\">
					<br>
					<u><b>$cricket_data[date]</b></u>
					</td>
					</tr>
					";
		        }
		        
		        echo "
				<tr>
				<td width=\"45%\" align=\"right\"><b>$cricket_data[hometeam]</b></td><td width=\"10%\"> - </td><td width=\"45%\" align=\"left\"><b>$cricket_data[awayteam]</b></td>
				</tr>
				<tr>
				<td width=\"45%\" align=\"right\">";
		        
		        if(!is_null($cricket_data['runs_home']))
		        echo"$cricket_data[runs_home] for $cricket_data[wickets_home]</td><td width=\"10%\"> - </td><td width=\"45%\" align=\"left\">$cricket_data[runs_away] for $cricket_data[wickets_away]";
		        else
		        echo'&nbsp;';
		        
		        echo"
				</td>
				</tr>";
		        
		        $temp = "$cricket_data[date]";
		        
		        $i++;
		    }
		}
		?>
		</table>
              </td>
              <td width="50%">
                   <table border="0" width="100%" cellspacing="2" cellpadding="2">

		<?php
		//How to print date
		if($cricket_print_date == 1)
		{
		    $cricket_print_date = '%d.%m.%Y';
		}
		elseif($cricket_print_date == 2)
		{
		    $cricket_print_date = '%m.%d.%Y';
		}
		elseif($cricket_print_date == 3)
		{
		    $cricket_print_date = '%b %D %Y';
		}
		
		//Check which matche want to be printed
		//All
		if($cricket_defaultshow == 1)
		{
		    $cricket_get_matches = $xoopsDB->query("SELECT O.OpponentName AS hometeam,
			OP.OpponentName AS awayteam,
			LM.LeagueMatchHomeRuns AS runs_home,
			LM.LeagueMatchAwayRuns AS runs_away,
			LM.LeagueMatchHomeWickets AS wickets_home,
			LM.LeagueMatchAwayWickets AS wickets_away,
			LM.LeagueMatchID AS id,
			DATE_FORMAT(LM.LeagueMatchDate, '$cricket_print_date') AS date
			FROM ".$xoopsDB->prefix("cricket_leaguematches")." LM, ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_opponents")." OP
			WHERE O.OpponentID = LM.LeagueMatchHomeID AND
			OP.OpponentID = LM.LeagueMatchAwayID AND
            LM.LeagueMatchDate > CURDATE() AND
			LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
			LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
			ORDER BY LM.LeagueMatchDate ASC");
		}
		//Own only
		else
		{
		    $cricket_get_matches = $xoopsDB->query("SELECT O.OpponentName AS hometeam,
			OP.OpponentName AS awayteam,
			LM.LeagueMatchHomeRuns AS runs_home,
			LM.LeagueMatchAwayRuns AS runs_away,
			LM.LeagueMatchHomeWickets AS wickets_home,
			LM.LeagueMatchAwayWickets AS wickets_away,
			LM.LeagueMatchID AS id,
			DATE_FORMAT(LM.LeagueMatchDate, '$cricket_print_date') AS date
			FROM ".$xoopsDB->prefix("cricket_leaguematches")." LM, ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_opponents")." OP
			WHERE O.OpponentID = LM.LeagueMatchHomeID AND
			OP.OpponentID = LM.LeagueMatchAwayID AND
            LM.LeagueMatchDate > CURDATE() AND
			LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
			LeagueMatchLeagueID LIKE '$cricket_defaultleagueid' AND
			(O.OpponentOwn = '1' OR OP.OpponentOwn = '1')
			ORDER BY LM.LeagueMatchDate ASC");
		}
		
		if($xoopsDB->getRowsNum($cricket_get_matches) < 1)
		{
		    echo "&nbsp;<b>"._LS_CRICK_NOMATCHES."</b>";
		}
		else
		{
		    $i = 0;
		    $temp = '';
		    
		    while($cricket_data = $xoopsDB->fetchArray($cricket_get_matches))
		    {
		        if($i == 0)
		        {
		            echo"
					<tr>
					<td style=\"padding-left:5px;\" align=\"left\" colspan=\"2\">
					<u><b>$cricket_data[date]</b></u>
					</td>
					</tr>
					";
		        }
		        
		        if($cricket_data['date'] != "$temp" && $i > 0)
		        {
		            echo"
					<tr>
					<td style=\"padding-left:5px;\" align=\"left\" colspan=\"2\">
					<br>
					<u><b>$cricket_data[date]</b></u>
					</td>
					</tr>
					";
		        }
		        
		        echo "
				<tr>
				<td width=\"45%\" align=\"right\"><b>$cricket_data[hometeam]</b></td><td width=\"10%\"> - </td><td width=\"45%\" align=\"left\"><b>$cricket_data[awayteam]</b></td>
				</tr>
				<tr>
				<td width=\"45%\" align=\"right\">";
		        
		        if(!is_null($cricket_data['runs_home']))
		        echo"$cricket_data[runs_home] for $cricket_data[wickets_home]</td><td width=\"10%\"> - </td><td width=\"45%\" align=\"left\">$cricket_data[runs_away] for $cricket_data[wickets_away]";
		        else
		        echo'&nbsp;';
		        
		        echo"
				</td>
				</tr>";
		        
		        $temp = "$cricket_data[date]";
		        
		        $i++;
		    }
		}
		?>
		</table>
             </td>
           </tr>
       </table><br><br>

		<?php
		}
		?>
	</td>
	</tr>
	</table>
</td>
</tr>
</table>

<?php
include('bottom.txt');
?>
</form>

<?php
include(XOOPS_ROOT_PATH.'/footer.php');
?>