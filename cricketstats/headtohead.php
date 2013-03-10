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
if (!isset($_SESSION['defaulthomeid'])) {
    $sql = "SELECT OpponentID FROM ".$xoopsDB->prefix("cricket_opponents")." WHERE OpponentLeagueID = $cricket_d_league_id AND OpponentSeasonID = $cricket_d_season_id LIMIT 0,2";
    $teamresults = $xoopsDB->query($sql);
    $teams = $xoopsDB->fetchArray($teamresults);
    $_SESSION['defaulthomeid'] = $teams['OpponentID'];
    $teams = $xoopsDB->fetchArray($teamresults);
    $_SESSION['defaultawayid'] = $teams['OpponentID'];
}
$cricket_defaulthomeid = intval($_SESSION['defaulthomeid']);
$cricket_defaultawayid = intval($_SESSION['defaultawayid']);

//if(!session_is_registered('defaultseasonid') || !session_is_registered('defaultleagueid'))
    if ( !isset( $_SESSION['defaultseasonid'] ) || !isset( $_SESSION['defaultleagueid'] ))
{
	$_SESSION['defaultseasonid'] = $cricket_d_season_id;
	$_SESSION['defaultleagueid'] = $cricket_d_league_id;
}
$cricket_defaultseasonid = intval($_SESSION['defaultseasonid']);
$cricket_defaultleagueid = intval($_SESSION['defaultleagueid']);

//If All is chosen from season or league, lets set default value for %
if($cricket_defaultseasonid == 0)
	$cricket_defaultseasonid = '%';

if($cricket_defaultleagueid == 0)
	$cricket_defaultleagueid = '%';
	
//Gets seasons and leagues and match types for dropdowns
$cricket_get_seasons = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_seasonnames")." WHERE SeasonPublish = '1' ORDER BY SeasonName");
$cricket_get_leagues = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_leaguenames")." WHERE LeaguePublish = '1' ORDER BY LeagueName");

//query to get teams from chosen season & league
$cricket_get_teams = $xoopsDB->query("SELECT DISTINCT
O.OpponentName AS name,
O.OpponentID AS id
FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
WHERE LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid' AND
(O.OpponentID = LM.LeagueMatchHomeID OR
O.OpponentID = LM.LeagueMatchAwayID) 
ORDER BY name
");
?>

<?php

//Width of the line
$templine_width = $cricket_tb_width-25;

//query to get team names
$cricket_get_names = $xoopsDB->query("SELECT O.OpponentName AS homename,
OP.OpponentName AS awayname
FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_opponents")." OP
WHERE
O.OpponentID = ".intval($cricket_defaulthomeid)." AND
OP.OpponentID = ".intval($cricket_defaultawayid)."
LIMIT 1
");

$namedata = $xoopsDB->fetchArray($cricket_get_names);
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
<?php echo _LS_CRICK_MOVETO;?> <select name="moveto">
<option value="index.php"><?php echo _LS_CRICK_TABLES;?></option>
<option value="season.php"><?php echo _LS_CRICK_SEASONSTATS;?></option>
<option value="league.php"><?php echo _LS_CRICK_LEAGUESTATS;?></option>
</select> <input type="submit" class="button" value=">>" name="submit6">
<br>
<?php echo _LS_CRICK_HOMETEAM;?>
<select name="home_id">
<?php
while($cricket_data = $xoopsDB->fetchArray($cricket_get_teams))
{
	if($cricket_data['id'] == $cricket_defaulthomeid)
		echo"<option value=\"$cricket_data[id]\" SELECTED>$cricket_data[name]</option>\n";
	else
		echo"<option value=\"$cricket_data[id]\">$cricket_data[name]</option>\n";
}
?>
</select> <input type="submit" class="button" value=">>" name="submit4">
&nbsp;&nbsp;&nbsp;
<?php echo _LS_CRICK_AWAYTEAM;?>
<select name="away_id">

    <?php
    if (mysql_num_rows($cricket_get_teams) >= 1) {
        mysql_data_seek($cricket_get_teams, 0);

        while ($cricket_data = $xoopsDB->fetchArray($cricket_get_teams)) {
            if ($cricket_data['id'] == $cricket_defaultawayid
            ) {
                echo"<option value=\"$cricket_data[id]\" SELECTED>$cricket_data[name]</option>\n";
            } else {
                echo"<option value=\"$cricket_data[id]\">$cricket_data[name]</option>\n";
            }
        }
    }
    ?>
</select> <input type="submit" class="button" value=">>" name="submit5">
</td>
</tr>
</table>
</td>
</tr>
</table>

<table align="center" width="<?php echo $cricket_tb_width ?>" cellspacing="0" cellpadding="0" border="0" bgcolor="<?php echo $cricket_border_c ?>">
<tr>
<td>
	<table width="100%" cellspacing="1" cellpadding="5" border="0">
	<tr>
	<td bgcolor="<?php echo $cricket_inside_c ?>" align="center">

	<table width="100%" cellspacing="1" cellpadding="5" border="0" align="center">

	<?php
	//How to print date?
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

	//query to get hometeam data
	$query = $xoopsDB->query("SELECT
	LM.LeagueMatchHomeID AS homeid,
	LM.LeagueMatchAwayID AS awayid,
	LM.LeagueMatchHomeBonus AS homebonus,
	LM.LeagueMatchAwayBonus AS awaybonus,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchHomeWickets AS homewickets,
	LM.LeagueMatchAwayRuns AS awayruns,
	LM.LeagueMatchAwayWickets AS awaywickets
	FROM
	".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE
	(LM.LeagueMatchHomeID = ".intval($cricket_defaulthomeid)." OR
	LM.LeagueMatchAwayID = ".intval($cricket_defaulthomeid).") AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid' AND 
	LM.LeagueMatchHomeRuns IS NOT NULL AND
	LM.LeagueMatchHomeWickets IS NOT NULL AND
	LM.LeagueMatchAwayRuns IS NOT NULL AND
	LM.LeagueMatchAwayWickets IS NOT NULL
	");

	//query to get away team data
	$query2 = $xoopsDB->query("SELECT
	LM.LeagueMatchHomeID AS homeid,
	LM.LeagueMatchAwayID AS awayid,
	LM.LeagueMatchHomeBonus AS homebonus,
	LM.LeagueMatchAwayBonus AS awaybonus,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchHomeWickets AS homewickets,
	LM.LeagueMatchAwayRuns AS awayruns,
	LM.LeagueMatchAwayWickets AS awaywickets
	FROM
	".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE
	(LM.LeagueMatchHomeID = ".intval($cricket_defaultawayid)." OR
	LM.LeagueMatchAwayID = ".intval($cricket_defaultawayid).") AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid' AND 
	LM.LeagueMatchHomeRuns IS NOT NULL AND
	LM.LeagueMatchHomeWickets IS NOT NULL AND
	LM.LeagueMatchAwayRuns IS NOT NULL AND
	LM.LeagueMatchAwayWickets IS NOT NULL
	");

	//Table variables: hometeam values into index 0 and awayteam into index 1
	$home_wins[0] = 0;
	$home_draws[0] = 0;
	$home_loses[0] = 0;
	$home_bonus[0] = 0;
	$home_runs[0] = 0;
	$home_wickets[0] = 0;
	$home_runsagainst[0] = 0;
	$home_wicketsagainst[0] = 0;
	$away_wins[0] = 0;
	$away_draws[0] = 0;
	$away_loses[0] = 0;
	$away_bonus[0] = 0;
	$away_runs[0] = 0;
	$away_wickets[0] = 0;
	$away_runsagainst[0] = 0;
	$away_wicketsagainst[0] = 0;
	$total_wins[0] = 0;
	$total_draws[0] = 0;
	$total_loses[0] = 0;
	$total_runs[0] = 0;
	$total_wickets[0] = 0;
	$total_runsagainst[0] = 0;
	$total_wicketsagainst[0] = 0;

	$home_wins[1] = 0;
	$home_draws[1] = 0;
	$home_loses[1] = 0;
	$home_bonus[1] = 0;
	$home_runs[1] = 0;
	$home_wickets[1] = 0;
	$home_runsagainst[1] = 0;
	$home_wicketsagainst[1] = 0;
	$away_wins[1] = 0;
	$away_draws[1] = 0;
	$away_loses[1] = 0;
	$away_bonus[1] = 0;
	$away_runs[1] = 0;
	$away_wickets[1] = 0;
	$away_runsagainst[1] = 0;
	$away_wicketsagainst[1] = 0;
	$total_wins[1] = 0;
	$total_draws[1] = 0;
	$total_loses[1] = 0;
	$total_runs[1] = 0;
	$total_wickets[1] = 0;
	$total_runsagainst[1] = 0;
	$total_wicketsagainst[1] = 0;

	//Lets check hometeam
	while($cricket_data = $xoopsDB->fetchArray($query))
	{
		//Home or away game?
		//Home match
		if($cricket_data['homeid'] == $cricket_defaulthomeid)
		{
			//Win
			if($cricket_data['homeruns'] > $cricket_data['awayruns'])
			{
				$home_wins[0]++;
				$home_bonus[0] = $home_bonus[0] + $cricket_data['homebonus'];
				$home_runs[0] = $home_runs[0] + $cricket_data['homeruns'];
				$home_wickets[0] = $home_wickets[0] + $cricket_data['homewickets'];
				$home_runsagainst[0] = $home_runsagainst[0] + $cricket_data['awayruns'];
				$home_wicketsagainst[0] = $home_wicketsagainst[0] + $cricket_data['awaywickets'];
			}
			//Draw
			elseif($cricket_data['homeruns'] == $cricket_data['awayruns'])
			{
				$home_draws[0]++;
				$home_bonus[0] = $home_bonus[0] + $cricket_data['homebonus'];
				$home_runs[0] = $home_runs[0] + $cricket_data['homeruns'];
				$home_wickets[0] = $home_wickets[0] + $cricket_data['homewickets'];
				$home_runsagainst[0] = $home_runsagainst[0] + $cricket_data['awayruns'];
				$home_wicketsagainst[0] = $home_wicketsagainst[0] + $cricket_data['awaywickets'];
			}
			//Lost
			elseif($cricket_data['homeruns'] < $cricket_data['awayruns'])
			{
				$home_loses[0]++;
				$home_bonus[0] = $home_bonus[0] + $cricket_data['homebonus'];
				$home_runs[0] = $home_runs[0] + $cricket_data['homeruns'];
				$home_wickets[0] = $home_wickets[0] + $cricket_data['homewickets'];
				$home_runsagainst[0] = $home_runsagainst[0] + $cricket_data['awayruns'];
				$home_wicketsagainst[0] = $home_wicketsagainst[0] + $cricket_data['awaywickets'];
			}
		}
		//Away mathc
		else
		{
			//Win
			if($cricket_data['awayruns'] > $cricket_data['homeruns'])
			{
				$away_wins[0]++;
				$away_bonus[0] = $away_bonus[0] + $cricket_data['awaybonus'];
				$away_runs[0] = $away_runs[0] + $cricket_data['awayruns'];
				$away_wickets[0] = $away_wickets[0] + $cricket_data['awaywickets'];
				$away_runsagainst[0] = $away_runsagainst[0] + $cricket_data['homeruns'];
				$away_wicketsagainst[0] = $away_wicketsagainst[0] + $cricket_data['homewickets'];
			}
			//Draw
			elseif($cricket_data['awayruns'] == $cricket_data['homeruns'])
			{
				$away_draws[0]++;
				$away_bonus[0] = $away_bonus[0] + $cricket_data['awaybonus'];
				$away_runs[0] = $away_runs[0] + $cricket_data['awayruns'];
				$away_wickets[0] = $away_wickets[0] + $cricket_data['awaywickets'];
				$away_runsagainst[0] = $away_runsagainst[0] + $cricket_data['homeruns'];
				$away_wicketsagainst[0] = $away_wicketsagainst[0] + $cricket_data['homewickets'];
			}
			//Lost
			elseif($cricket_data['awayruns'] < $cricket_data['homeruns'])
			{
				$away_loses[0]++;
				$away_bonus[0] = $away_bonus[0] + $cricket_data['awaybonus'];
				$away_runs[0] = $away_runs[0] + $cricket_data['awayruns'];
				$away_wickets[0] = $away_wickets[0] + $cricket_data['awaywickets'];
				$away_runsagainst[0] = $away_runsagainst[0] + $cricket_data['homeruns'];
				$away_wicketsagainst[0] = $away_wicketsagainst[0] + $cricket_data['homewickets'];
			}
		}
	}

	//Lets check away team
	while($cricket_data = $xoopsDB->fetchArray($query2))
	{
		//Home match
		if($cricket_data['homeid'] == $cricket_defaultawayid)
		{
			//Win
			if($cricket_data['homeruns'] > $cricket_data['awayruns'])
			{
				$home_wins[1]++;
				$home_bonus[1] = $home_bonus[1] + $cricket_data['homebonus'];
				$home_runs[1] = $home_runs[1] + $cricket_data['homeruns'];
				$home_wickets[1] = $home_wickets[1] + $cricket_data['homewickets'];
				$home_runsagainst[1] = $home_runsagainst[1] + $cricket_data['awayruns'];
				$home_wicketsagainst[1] = $home_wicketsagainst[1] + $cricket_data['awaywickets'];
			}
			//Draw
			elseif($cricket_data['homeruns'] == $cricket_data['awayruns'])
			{
				$home_draws[1]++;
				$home_bonus[1] = $home_bonus[1] + $cricket_data['homebonus'];
				$home_runs[1] = $home_runs[1] + $cricket_data['homeruns'];
				$home_wickets[1] = $home_wickets[1] + $cricket_data['homewickets'];
				$home_runsagainst[1] = $home_runsagainst[1] + $cricket_data['awayruns'];
				$home_wicketsagainst[1] = $home_wicketsagainst[1] + $cricket_data['awaywickets'];
			}
			//Lost
			elseif($cricket_data['homeruns'] < $cricket_data['awayruns'])
			{
				$home_loses[1]++;
				$home_bonus[1] = $home_bonus[1] + $cricket_data['homebonus'];
				$home_runs[1] = $home_runs[1] + $cricket_data['homeruns'];
				$home_wickets[1] = $home_wickets[1] + $cricket_data['homewickets'];
				$home_runsagainst[1] = $home_runsagainst[1] + $cricket_data['awayruns'];
				$home_wicketsagainst[1] = $home_wicketsagainst[1] + $cricket_data['awaywickets'];
			}
		}
		//Away match
		else
		{
			//Win
			if($cricket_data['awayruns'] > $cricket_data['homeruns'])
			{
				$away_wins[1]++;
				$away_bonus[1] = $away_bonus[1] + $cricket_data['awaybonus'];
				$away_runs[1] = $away_runs[1] + $cricket_data['awayruns'];
				$away_wickets[1] = $away_wickets[1] + $cricket_data['awaywickets'];
				$away_runsagainst[1] = $away_runsagainst[1] + $cricket_data['homeruns'];
				$away_wicketsagainst[1] = $away_wicketsagainst[1] + $cricket_data['homewickets'];
			}
			//Draw
			elseif($cricket_data['awayruns'] == $cricket_data['homeruns'])
			{
				$away_draws[1]++;
				$away_bonus[1] = $away_bonus[1] + $cricket_data['awaybonus'];
				$away_runs[1] = $away_runs[1] + $cricket_data['awayruns'];
				$away_wickets[1] = $away_wickets[1] + $cricket_data['awaywickets'];
				$away_runsagainst[1] = $away_runsagainst[1] + $cricket_data['homeruns'];
				$away_wicketsagainst[1] = $away_wicketsagainst[1] + $cricket_data['homewickets'];
			}
			//Lost
			elseif($cricket_data['awayruns'] < $cricket_data['homeruns'])
			{
				$away_loses[1]++;
				$away_bonus[1] = $away_bonus[1] + $cricket_data['awaybonus'];
				$away_runs[1] = $away_runs[1] + $cricket_data['awayruns'];
				$away_wickets[1] = $away_wickets[1] + $cricket_data['awaywickets'];
				$away_runsagainst[1] = $away_runsagainst[1] + $cricket_data['homeruns'];
				$away_wicketsagainst[1] = $away_wicketsagainst[1] + $cricket_data['homewickets'];
			}
		}
	}

	//Calculates home team data
	$bonus[0] = $home_bonus[0] + $away_bonus[0];
	$home_played[0] = $home_wins[0] + $home_draws[0] + $home_loses[0];
	$away_played[0] = $away_wins[0] + $away_draws[0] + $away_loses[0];

	$total_wins[0] = $home_wins[0] + $away_wins[0];
	$total_draws[0] = $home_draws[0] + $away_draws[0];
	$total_loses[0] = $home_loses[0] + $away_loses[0];
	$total_runs[0] = $home_runs[0] + $away_runs[0];
	$total_wickets[0] = $home_wickets[0] + $away_wickets[0];
	$total_runsagainst[0] = $home_runsagainst[0] + $away_runsagainst[0];
	$total_wicketsagainst[0] = $home_wicketsagainst[0] + $away_wicketsagainst[0];
	$total_played[0] = $total_wins[0] + $total_draws[0] + $total_loses[0];
	$total_points[0] = $bonus[0] + ($cricket_for_win*$total_wins[0]) + ($cricket_for_draw*$total_draws[0]) + ($cricket_for_lose*$total_loses[0]);

	$total_rd[0] = $total_runs[0] - $total_runsagainst[0];
	$total_wd[0] = $total_wickets[0] - $total_wicketsagainst[0];
	$home_rd[0] = $home_runs[0] - $home_runsagainst[0];
	$away_rd[0] = $away_runs[0] - $away_runsagainst[0];
	$home_wd[0] = $home_wickets[0] - $home_wicketsagainst[0];
	$away_wd[0] = $away_wickets[0] - $away_wicketsagainst[0];

	//Calculates away team data
	$bonus[1] = $home_bonus[1] + $away_bonus[1];
	$home_played[1] = $home_wins[1] + $home_draws[1] + $home_loses[1];
	$away_played[1] = $away_wins[1] + $away_draws[1] + $away_loses[1];

	$total_wins[1] = $home_wins[1] + $away_wins[1];
	$total_draws[1] = $home_draws[1] + $away_draws[1];
	$total_loses[1] = $home_loses[1] + $away_loses[1];
	$total_runs[1] = $home_runs[1] + $away_runs[1];
	$total_wickets[1] = $home_wickets[1] + $away_wickets[1];
	$total_runsagainst[1] = $home_runsagainst[1] + $away_runsagainst[1];
	$total_wicketsagainst[1] = $home_wicketsagainst[1] + $away_wicketsagainst[1];
	$total_played[1] = $total_wins[1] + $total_draws[1] + $total_loses[1];
	$total_points[1] = $bonus[1] + ($cricket_for_win*$total_wins[1]) + ($cricket_for_draw*$total_draws[1]) + ($cricket_for_lose*$total_loses[1]);

	$total_rd[1] = $total_runs[1] - $total_runsagainst[1];
	$total_wd[1] = $total_wickets[1] - $total_wicketsagainst[1];
	$home_rd[1] = $home_runs[1] - $home_runsagainst[1];
	$away_rd[1] = $away_runs[1] - $away_runsagainst[1];
	$home_wd[1] = $home_wickets[1] - $home_wicketsagainst[1];
	$away_wd[1] = $away_wickets[1] - $away_wicketsagainst[1];

	//query to get head-to-head data
	$headtohead_query = $xoopsDB->query("SELECT
	O.OpponentName AS hometeam,
	OP.OpponentName AS awayteam,
	LM.LeagueMatchHomeID AS homeid,
	LM.LeagueMatchAwayID AS awayid,
	DATE_FORMAT(LM.LeagueMatchDate, '$cricket_print_date') AS date,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns,
	LM.LeagueMatchHomeWickets AS homewickets,
	LM.LeagueMatchAwayWickets AS awaywickets
	FROM
	".$xoopsDB->prefix("cricket_leaguematches")." AS LM,
	".$xoopsDB->prefix("cricket_opponents")." O,
	".$xoopsDB->prefix("cricket_opponents")." OP
	WHERE
	O.OpponentID = LM.LeagueMatchHomeID AND
	OP.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid' AND 
	LM.LeagueMatchHomeRuns IS NOT NULL AND
    LM.LeagueMatchAwayRuns IS NOT NULL AND
	LM.LeagueMatchHomeWickets IS NOT NULL AND
    LM.LeagueMatchAwayWickets IS NOT NULL AND
	((LM.LeagueMatchHomeID = ".intval($cricket_defaulthomeid)." AND LM.LeagueMatchAwayID = ".intval($cricket_defaultawayid).") OR
	(LM.LeagueMatchHomeID = ".intval($cricket_defaultawayid)." AND LM.LeagueMatchAwayID = ".intval($cricket_defaulthomeid)."))
	ORDER BY LM.LeagueMatchDate DESC
	");

	//Sets zero for head-to-head variables
	//Also checks the data in while-loop

	$cricket_home_wins = 0;
	$cricket_home_draws = 0;
	$cricket_home_loses = 0;
	$cricket_home_runs = 0;
	$cricket_home_runs_against = 0;
	$cricket_home_wickets = 0;
	$cricket_home_wickets_against = 0;

	$cricket_away_wins = 0;
	$cricket_away_draws = 0;
	$cricket_away_loses = 0;
	$cricket_away_runs = 0;
	$cricket_away_runs_against = 0;
	$cricket_away_wickets = 0;
	$cricket_away_wickets_against = 0;

	$i = 0;
	while($cricket_data = $xoopsDB->fetchArray($headtohead_query))
	{
		//Maximum five games into variables
		if($i < 5)
		{
			$cricket_matches_date[$i] = $cricket_data['date'];
			$cricket_matches_home[$i] = $cricket_data['hometeam'];
			$cricket_matches_away[$i] = $cricket_data['awayteam'];
			$cricket_matches_score[$i] = $cricket_data['homeruns'] . " for " . $cricket_data['homewickets'] . " - " . $cricket_data['awayruns'] . " for " . $cricket_data['awaywickets'];

			$i++;
		}

		//hometeams home match
		if($cricket_data['homeid'] == $cricket_defaulthomeid)
		{
			if($cricket_data['homeruns'] > $cricket_data['awayruns'])
			{
				$cricket_home_wins++;
				$cricket_away_loses++;
			}
			elseif($cricket_data['homeruns'] == $cricket_data['awayruns'])
			{
				$cricket_home_draws++;
				$cricket_away_draws++;
			}
			elseif($cricket_data['homeruns'] < $cricket_data['awayruns'])
			{
				$cricket_home_loses++;
				$cricket_away_wins++;
			}

			$cricket_home_runs = $cricket_home_runs + $cricket_data['homeruns'];
			$cricket_home_runs_against = $cricket_home_runs_against + $cricket_data['awayruns'];
			$cricket_away_runs = $cricket_away_runs + $cricket_data['awayruns'];
			$cricket_away_runs_against = $cricket_away_runs_against + $cricket_data['homeruns'];

			$cricket_home_wickets = $cricket_home_wickets + $cricket_data['homewickets'];
			$cricket_home_wickets_against = $cricket_home_wickets_against + $cricket_data['awaywickets'];
			$cricket_away_wickets = $cricket_away_wickets + $cricket_data['awaywickets'];
			$cricket_away_wickets_against = $cricket_away_wickets_against + $cricket_data['homewickets'];

		}
		elseif($cricket_data['homeid'] == $cricket_defaultawayid)
		{
			if($cricket_data['homeruns'] > $cricket_data['awayruns'])
			{
				$cricket_away_wins++;
				$cricket_home_loses++;
			}
			elseif($cricket_data['homeruns'] == $cricket_data['awayruns'])
			{
				$cricket_away_draws++;
				$cricket_home_draws++;
			}
			elseif($cricket_data['homeruns'] < $cricket_data['awayruns'])
			{
				$cricket_away_loses++;
				$cricket_home_wins++;
			}

			$cricket_away_runs = $cricket_away_runs + $cricket_data['homeruns'];
			$cricket_away_runs_against = $cricket_away_runs_against + $cricket_data['awayruns'];
			$cricket_home_runs = $cricket_home_runs + $cricket_data['awayruns'];
			$cricket_home_runs_against = $cricket_home_runs_against + $cricket_data['homeruns'];

			$cricket_away_wickets = $cricket_away_wickets + $cricket_data['homewickets'];
			$cricket_away_wickets_against = $cricket_away_wickets_against + $cricket_data['awaywickets'];
			$cricket_home_wickets = $cricket_home_wickets + $cricket_data['awaywickets'];
			$cricket_home_wickets_against = $cricket_home_wickets_against + $cricket_data['homewickets'];
		}
	}
	?>

	<tr>
	<td align="center" valign="middle" width="35%">
	<b><u><?= $namedata['homename'] ?></u></b></td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_VERSUS;?></td>

	<td align="center" valign="middle" width="35%">
	<b><u><?= $namedata['awayname'] ?></u></b></td>
	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $cricket_top_bg ?>">
	<b><?php echo _LS_CRICK_HEADTOHEAD;?></b></td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?= "$cricket_home_wins-$cricket_home_draws-$cricket_home_loses" ?></b></td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_WDL;?></td>

	<td align="center" valign="middle">
	<b><?= "$cricket_away_wins-$cricket_away_draws-$cricket_away_loses" ?></b></td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?= "$cricket_home_runs-$cricket_home_runs_against" ?></b></td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_RUNSDIFF;?></td>

	<td align="center" valign="middle">
	<b><?= "$cricket_away_runs-$cricket_away_runs_against" ?></b></td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?= "$cricket_home_wickets-$cricket_home_wickets_against" ?></b></td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_WICKETSDIFF;?></td>

	<td align="center" valign="middle">
	<b><?= "$cricket_away_wickets-$cricket_away_wickets_against" ?></b></td>
	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $cricket_top_bg ?>">
	<b><?php echo _LS_CRICK_LATHEADTOHEAD;?></b></td>
	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle">

	<?php
	for($j = 0 ; $j < $i ; $j++)
	{
		echo"$cricket_matches_date[$j]: $cricket_matches_home[$j] - $cricket_matches_away[$j]&nbsp;&nbsp;&nbsp;$cricket_matches_score[$j]<br>";
	}
	?>
	</td>
	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $cricket_top_bg ?>">
	<b><?php echo _LS_CRICK_OVMATSTATS;?></b></td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?= $total_points[0] ?></b></td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_POINTSEARNED;?></td>

	<td align="center" valign="middle">
	<b><?= $total_points[1] ?></b></td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?= $total_played[0] ?></b></td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_OVMATPLAYED;?></td>

	<td align="center" valign="middle">
	<b><?= $total_played[1] ?></b></td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($total_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($total_wins[0]/$total_played[0]));
	}

	echo"$total_wins[0]</b> ($temp %)";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_OVMATWON;?></td>

	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($total_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($total_wins[1]/$total_played[1]));
	}

	echo"$total_wins[1]</b> ($temp %)";
	?>
	</b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($total_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($total_draws[0]/$total_played[0]));
	}

	echo"$total_draws[0]</b> ($temp %)";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_OVMATDRAWN;?></td>

	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($total_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($total_draws[1]/$total_played[1]));
	}

	echo"$total_draws[1]</b> ($temp %)";
	?></b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($total_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($total_loses[0]/$total_played[0]));
	}

	echo"$total_loses[0]</b> ($temp %)";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_OVMATLOST;?></td>

	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($total_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($total_loses[1]/$total_played[1]));
	}

	echo"$total_loses[1]</b> ($temp %)";
	?>
	</b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle" colspan="3">
	<img src="images/line.gif" width="<?= $templine_width ?>" height="5" alt=""><br></td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?= $home_played[0] ?></b></td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_HOMEMATPLAYED;?></td>

	<td align="center" valign="middle">
	<b><?= $home_played[1] ?></b></td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($home_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($home_wins[0]/$home_played[0]));
	}

	echo"$home_wins[0]</b> ($temp %)";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_HOMEMATWON;?></td>

	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($home_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($home_wins[1]/$home_played[1]));
	}

	echo"$home_wins[1]</b> ($temp %)";
	?></b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($home_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($home_draws[0]/$home_played[0]));
	}

	echo"$home_draws[0]</b> ($temp %)";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_HOMEMATDRAWN;?></td>

	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($home_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($home_draws[1]/$home_played[1]));
	}

	echo"$home_draws[1]</b> ($temp %)";
	?></b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($home_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($home_loses[0]/$home_played[0]));
	}

	echo"$home_loses[0]</b> ($temp %)";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_HOMEMATLOST;?></td>

	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($home_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($home_loses[1]/$home_played[1]));
	}

	echo"$home_loses[1]</b> ($temp %)";
	?></b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle" colspan="3">
	<img src="images/line.gif" width="<?= $templine_width ?>" height="5" alt=""><br></td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?= $away_played[0] ?></b></td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_AWAYMATPLAYED;?></td>

	<td align="center" valign="middle">
	<b><?= $away_played[1] ?></b></td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($away_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($away_wins[0]/$away_played[0]));
	}

	echo"$away_wins[0]</b> ($temp %)";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_AWAYMATWON;?></td>

	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($away_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($away_wins[1]/$away_played[1]));
	}

	echo"$away_wins[1]</b> ($temp %)";
	?></b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($away_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($away_draws[0]/$away_played[0]));
	}

	echo"$away_draws[0]</b> ($temp %)";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_AWAYMATDRAWN;?></td>

	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($away_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($away_draws[1]/$away_played[1]));
	}

	echo"$away_draws[1]</b> ($temp %)";
	?></b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($away_played[0] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($away_loses[0]/$away_played[0]));
	}

	echo"$away_loses[0]</b> ($temp %)";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_AWAYMATLOST;?></td>

	<td align="center" valign="middle">
	<b>
	<?php

	//Avoid divide by zero
	if($away_played[1] == 0)
	{
		$temp = 0;
	}
	else
	{
		$temp = round(100*($away_loses[1]/$away_played[1]));
	}

	echo"$away_loses[1]</b> ($temp %)";
	?></b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle" colspan="3">
	<img src="/images/line.gif" width="<?= $templine_width ?>" height="5" alt=""><br></td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?php

	if($total_rd[0] >= 0)
		echo'+';

	echo"$total_rd[0]</b> ($total_runs[0] - $total_runsagainst[0])";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_OVRUNSDIFF;?></td>

	<td align="center" valign="middle">
	<b><?php

	if($total_rd[1] >= 0)
		echo'+';

	echo"$total_rd[1]</b> ($total_runs[1] - $total_runsagainst[1])";
	?></b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?php

	if($home_rd[0] >= 0)
		echo'+';

	echo"$home_rd[0]</b> ($home_runs[0] - $home_runsagainst[0])";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_RUNSDIFFHOME;?></td>

	<td align="center" valign="middle">
	<b><?php

	if($home_rd[1] >= 0)
		echo'+';

	echo"$home_rd[1]</b> ($home_runs[1] - $home_runsagainst[1])";
	?></b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?php

	if($away_rd[0] >= 0)
		echo'+';

	echo"$away_rd[0]</b> ($away_runs[0] - $away_runsagainst[0])";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_RUNSDIFFAWAY;?></td>

	<td align="center" valign="middle">
	<b><?php

	if($away_rd[1] >= 0)
		echo'+';

	echo"$away_rd[1]</b> ($away_runs[1] - $away_runsagainst[1])";
	?></b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle" colspan="3">
	<img src="/images/line.gif" width="<?= $templine_width ?>" height="5" alt=""><br></td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?php

	if($total_wd[0] >= 0)
		echo'+';

	echo"$total_wd[0]</b> ($total_wickets[0] - $total_wicketsagainst[0])";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_OVWICKETSDIFF;?></td>

	<td align="center" valign="middle">
	<b><?php

	if($total_wd[1] >= 0)
		echo'+';

	echo"$total_wd[1]</b> ($total_wickets[1] - $total_wicketsagainst[1])";
	?></b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?php

	if($home_wd[0] >= 0)
		echo'+';

	echo"$home_wd[0]</b> ($home_wickets[0] - $home_wicketsagainst[0])";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_WICKETSDIFFHOME;?></td>

	<td align="center" valign="middle">
	<b><?php

	if($home_wd[1] >= 0)
		echo'+';

	echo"$home_wd[1]</b> ($home_wickets[1] - $home_wicketsagainst[1])";
	?></b>
	</td>
	</tr>

	<tr>
	<td align="center" valign="middle">
	<b><?php

	if($away_wd[0] >= 0)
		echo'+';

	echo"$away_wd[0]</b> ($away_wickets[0] - $away_wicketsagainst[0])";
	?>
	</td>

	<td align="center" valign="middle">
	<?php echo _LS_CRICK_WICKETSDIFFAWAY;?></td>

	<td align="center" valign="middle">
	<b><?php

	if($away_wd[1] >= 0)
		echo'+';

	echo"$away_wd[1]</b> ($away_wickets[1] - $away_wicketsagainst[1])";
	?></b>
	</td>
	</tr>
	
	<?php
	//query to get biggest home win/lost/aggr for hometeam
	$query = $xoopsDB->query("SELECT
	MAX(LeagueMatchHomeRuns - LeagueMatchAwayRuns) AS maxhomewin,
	MAX(LeagueMatchAwayRuns - LeagueMatchHomeRuns) AS maxhomelost,
	MAX(LeagueMatchHomeRuns + LeagueMatchAwayRuns) AS maxhomeaggregate
	FROM ".$xoopsDB->prefix("cricket_leaguematches")."
	WHERE LeagueMatchHomeID = ".intval($cricket_defaulthomeid)." AND
	LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LeagueMatchLeagueID LIKE '$cricket_defaultleagueid' 
	");

	$maxhomedata_hometeam = $xoopsDB->fetchArray($query);

	//query to get biggest away win/lost/aggr for hometeam
	$query = $xoopsDB->query("SELECT
	MAX(LeagueMatchAwayRuns - LeagueMatchHomeRuns) AS maxawaywin,
	MAX(LeagueMatchHomeRuns - LeagueMatchAwayRuns) AS maxawaylost,
	MAX(LeagueMatchHomeRuns + LeagueMatchAwayRuns) AS maxawayaggregate
	FROM ".$xoopsDB->prefix("cricket_leaguematches")."
	WHERE LeagueMatchAwayID = '$cricket_defaulthomeid' AND
	LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	$maxawaydata_hometeam = $xoopsDB->fetchArray($query);

	//query to get biggest home win/lost/aggr for awayteam
	$query = $xoopsDB->query("SELECT
	MAX(LeagueMatchHomeRuns - LeagueMatchAwayRuns) AS maxhomewin,
	MAX(LeagueMatchAwayRuns - LeagueMatchHomeRuns) AS maxhomelost,
	MAX(LeagueMatchHomeRuns + LeagueMatchAwayRuns) AS maxhomeaggregate
	FROM ".$xoopsDB->prefix("cricket_leaguematches")."
	WHERE LeagueMatchHomeID = '$cricket_defaultawayid' AND
	LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	$maxhomedata_awayteam = $xoopsDB->fetchArray($query);

	//query to get biggest away win/lost/aggr for awayteam
	$query = $xoopsDB->query("SELECT
	MAX(LeagueMatchAwayRuns - LeagueMatchHomeRuns) AS maxawaywin,
	MAX(LeagueMatchHomeRuns - LeagueMatchAwayRuns) AS maxawaylost,
	MAX(LeagueMatchHomeRuns + LeagueMatchAwayRuns) AS maxawayaggregate
	FROM ".$xoopsDB->prefix("cricket_leaguematches")."
	WHERE LeagueMatchAwayID = '$cricket_defaultawayid' AND
	LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	$maxawaydata_awayteam = $xoopsDB->fetchArray($query);

	//Lets put max results into variables
	$maxhomewin_home = $maxhomedata_hometeam['maxhomewin'];
	$maxhomelost_home = $maxhomedata_hometeam['maxhomelost'];
	$maxhomeaggregate_home = $maxhomedata_hometeam['maxhomeaggregate'];
	$maxawaywin_home = $maxawaydata_hometeam['maxawaywin'];
	$maxawaylost_home = $maxawaydata_hometeam['maxawaylost'];
	$maxawayaggregate_home = $maxawaydata_hometeam['maxawayaggregate'];

	$maxhomewin_away = $maxhomedata_awayteam['maxhomewin'];
	$maxhomelost_away = $maxhomedata_awayteam['maxhomelost'];
	$maxhomeaggregate_away = $maxhomedata_awayteam['maxhomeaggregate'];
	$maxawaywin_away = $maxawaydata_awayteam['maxawaywin'];
	$maxawaylost_away = $maxawaydata_awayteam['maxawaylost'];
	$maxawayaggregate_away = $maxawaydata_awayteam['maxawayaggregate'];
	?>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $cricket_top_bg ?>">
	<b><?php echo _LS_CRICK_BIGHOMEWIN;?></b></td>
	</tr>

	<tr>
	<td align="center" valign="top">
	<?php
	//query to get all the biggest home wins: home
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchHomeID = '$cricket_defaulthomeid' AND
	(LM.LeagueMatchHomeRuns - LM.LeagueMatchAwayRuns) = '$maxhomewin_home' AND
	(LM.LeagueMatchHomeRuns - LM.LeagueMatchAwayRuns) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	//If there are no home wins->print none
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_CRICK_NONE;
	}
	else
	{
		while($cricket_data = $xoopsDB->fetchArray($query))
		{
			echo"$cricket_data[homeruns] - $cricket_data[awayruns] "._LS_CRICK_VERSUS." $cricket_data[name]<br>\n";
		}
	}
	?>
	</td>

	<td align="center" valign="middle">&nbsp;</td>

	<td align="center" valign="top">
	<?php
	//query to get all the biggest home wins: away
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchHomeID = '$cricket_defaultawayid' AND
	(LM.LeagueMatchHomeRuns - LM.LeagueMatchAwayRuns) = '$maxhomewin_away' AND
	(LM.LeagueMatchHomeRuns - LM.LeagueMatchAwayRuns) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	//If there are no home wins->print none
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_CRICK_NONE;
	}
	else
	{
		while($cricket_data = $xoopsDB->fetchArray($query))
		{
			echo"$cricket_data[homeruns] - $cricket_data[awayruns] "._LS_CRICK_VERSUS." $cricket_data[name]<br>\n";
		}
	}
	?>
	</td>
	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $cricket_top_bg ?>">
	<b><?php echo _LS_CRICK_BIGHOMELOST;?></b></td>
	</tr>

	<tr>
	<td align="center" valign="top">
	<?php
	//query to get all the biggest home losses: home
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchHomeID = '$cricket_defaulthomeid' AND
	(LM.LeagueMatchAwayRuns - LM.LeagueMatchHomeRuns) = '$maxhomelost_home' AND
	(LM.LeagueMatchAwayRuns - LM.LeagueMatchHomeRuns) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	//If there are no home loses->print none
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_CRICK_NONE;
	}
	else
	{
		while($cricket_data = $xoopsDB->fetchArray($query))
		{
			echo"$cricket_data[homeruns] - $cricket_data[awayruns] "._LS_CRICK_VERSUS." $cricket_data[name]<br>\n";
		}
	}
	?>
	</td>

	<td align="center" valign="middle">&nbsp;</td>

	<td align="center" valign="top">
	<?php
	//query to get all the biggest home loses: away
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchHomeID = '$cricket_defaultawayid' AND
	(LM.LeagueMatchAwayRuns - LM.LeagueMatchHomeRuns) = '$maxhomelost_away' AND
	(LM.LeagueMatchAwayRuns - LM.LeagueMatchHomeRuns) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	//If there are no home loses->print none
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_CRICK_NONE;
	}
	else
	{
		while($cricket_data = $xoopsDB->fetchArray($query))
		{
			echo"$cricket_data[homeruns] - $cricket_data[awayruns] "._LS_CRICK_VERSUS." $cricket_data[name]<br>\n";
		}
	}
	?>
	</td>
	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $cricket_top_bg ?>">
	<b><?php echo _LS_CRICK_HIGHAGGHOME;?></b></td>
	</tr>

	<tr>
	<td align="center" valign="top">
	<?php
	//query to get all the biggest home aggregate: home
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchHomeID = '$cricket_defaulthomeid' AND
	(LM.LeagueMatchHomeRuns + LM.LeagueMatchAwayRuns) = '$maxhomeaggregate_home' AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	while($cricket_data = $xoopsDB->fetchArray($query))
	{
		echo"$cricket_data[homeruns] - $cricket_data[awayruns] "._LS_CRICK_VERSUS." $cricket_data[name]<br>\n";
	}
	?>
	</td>

	<td align="center" valign="middle">&nbsp;</td>

	<td align="center" valign="top">
	<?php
	//query to get all the biggest home aggregate: away
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchAwayID AND
	LM.LeagueMatchHomeID = '$cricket_defaultawayid' AND
	(LM.LeagueMatchHomeRuns + LM.LeagueMatchAwayRuns) = '$maxhomeaggregate_away' AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	while($cricket_data = $xoopsDB->fetchArray($query))
	{
		echo"$cricket_data[homeruns] - $cricket_data[awayruns] "._LS_CRICK_VERSUS." $cricket_data[name]<br>\n";
	}
	?>
	</td>
	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $cricket_top_bg ?>">
	<b><?php echo _LS_CRICK_BIGAWAYWIN;?></b></td>
	</tr>

	<tr>
	<td align="center" valign="top">
	<?php
	//query to get all the biggest away wins: home
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchHomeID AND
	LM.LeagueMatchAwayID = '$cricket_defaulthomeid' AND
	(LM.LeagueMatchAwayRuns - LM.LeagueMatchHomeRuns) = '$maxawaywin_home' AND
	(LM.LeagueMatchAwayRuns - LM.LeagueMatchHomeRuns) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	//If there are no away wins->print none
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_CRICK_NONE;
	}
	else
	{
		while($cricket_data = $xoopsDB->fetchArray($query))
		{
			echo"$cricket_data[awayruns] - $cricket_data[homeruns] "._LS_CRICK_VERSUS." $cricket_data[name]<br>\n";
		}
	}
	?>
	</td>

	<td align="center" valign="middle">&nbsp;</td>

	<td align="center" valign="top">
	<?php
	//query to get all the biggest away wins: away
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchHomeID AND
	LM.LeagueMatchAwayID = '$cricket_defaultawayid' AND
	(LM.LeagueMatchAwayRuns - LM.LeagueMatchHomeRuns) = '$maxawaywin_away' AND
	(LM.LeagueMatchAwayRuns - LM.LeagueMatchHomeRuns) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	//If there are no away wins->print none
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_CRICK_NONE;
	}
	else
	{
		while($cricket_data = $xoopsDB->fetchArray($query))
		{
			echo"$cricket_data[awayruns] - $cricket_data[homeruns] "._LS_CRICK_VERSUS." $cricket_data[name]<br>\n";
		}
	}
	?>
	</td>
	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $cricket_top_bg ?>">
	<b><?php echo _LS_CRICK_BIGAWAYLOSS;?></b></td>
	</tr>

	<tr>
	<td align="center" valign="top">
	<?php
	//query to get all the biggest away loses: home
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchHomeID AND
	LM.LeagueMatchAwayID = '$cricket_defaulthomeid' AND
	(LM.LeagueMatchHomeRuns - LM.LeagueMatchAwayRuns) = '$maxawaylost_home' AND
	(LM.LeagueMatchHomeRuns - LM.LeagueMatchAwayRuns) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	//If there are no away loses->print none
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_CRICK_NONE;
	}
	else
	{
		while($cricket_data = $xoopsDB->fetchArray($query))
		{
			echo"$cricket_data[awayruns] - $cricket_data[homeruns] "._LS_CRICK_VERSUS." $cricket_data[name]<br>\n";
		}
	}
	?>
	</td>

	<td align="center" valign="middle">&nbsp;</td>

	<td align="center" valign="top">
	<?php
	//query to get all the biggest away loses: away
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchHomeID AND
	LM.LeagueMatchAwayID = '$cricket_defaultawayid' AND
	(LM.LeagueMatchHomeRuns - LM.LeagueMatchAwayRuns) = '$maxawaylost_away' AND
	(LM.LeagueMatchHomeRuns - LM.LeagueMatchAwayRuns) > 0 AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	//If there are no away wins->print none
	if($xoopsDB->getRowsNum($query) == 0)
	{
		echo _LS_CRICK_NONE;
	}
	else
	{
		while($cricket_data = $xoopsDB->fetchArray($query))
		{
			echo"$cricket_data[awayruns] - $cricket_data[homeruns] "._LS_CRICK_VERSUS." $cricket_data[name]<br>\n";
		}
	}
	?>
	</td>
	</tr>

	<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="<?= $cricket_top_bg ?>">
	<b><?php echo _LS_CRICK_HIGHAGGAWAY;?></b></td>
	</tr>

	<tr>
	<td align="center" valign="top">
	<?php
	//query to get all the biggest away aggregate: home
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchHomeID AND
	LM.LeagueMatchAwayID = '$cricket_defaulthomeid' AND
	(LM.LeagueMatchAwayRuns + LM.LeagueMatchHomeRuns) = '$maxawayaggregate_home' AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	while($cricket_data = $xoopsDB->fetchArray($query))
	{
		echo"$cricket_data[awayruns] - $cricket_data[homeruns] "._LS_CRICK_VERSUS." $cricket_data[name]<br>\n";
	}
	?>
	</td>

	<td align="center" valign="middle">&nbsp;</td>

	<td align="center" valign="top">
	<?php
	//query to get all the biggest away aggregate: away
	$query = $xoopsDB->query("SELECT
	O.OpponentName AS name,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE O.OpponentID = LM.LeagueMatchHomeID AND
	LM.LeagueMatchAwayID = '$cricket_defaultawayid' AND
	(LM.LeagueMatchAwayRuns + LM.LeagueMatchHomeRuns) = '$maxawayaggregate_away' AND
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND 
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	while($cricket_data = $xoopsDB->fetchArray($query))
	{
		echo"$cricket_data[awayruns] - $cricket_data[homeruns] "._LS_CRICK_VERSUS." $cricket_data[name]<br>\n";
	}
	?>
	</td>
	</tr>
	</table>
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