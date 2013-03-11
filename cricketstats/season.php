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
$cricket_league = $xoopsDB->query($sql2);
$cricket_season = $xoopsDB->fetchArray($cricket_season);
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

//Check if there are session variables registered
//if(!session_is_registered('defaultseasonid') || !session_is_registered('defaultleagueid'))
    if ( !isset( $_SESSION['defaultseasonid'] ) || !isset( $_SESSION['defaultleagueid'] ))
{
	$_SESSION['defaultseasonid'] = $cricket_d_season_id;
	$_SESSION['defaultleagueid'] = $cricket_d_league_id;
}
$cricket_defaultseasonid = intval($_SESSION['defaultseasonid']);
$cricket_defaultleagueid = intval($_SESSION['defaultleagueid']);

//If All is chosen from season & league, lets set default value for %
if($cricket_defaultseasonid == 0)
	$cricket_defaultseasonid = '%';

if($cricket_defaultleagueid == 0)
	$cricket_defaultleagueid = '%';

//Gets seasons and leagues and match types for dropdowns
$cricket_get_seasons = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_seasonnames")." WHERE SeasonPublish = '1' ORDER BY SeasonName");
$cricket_get_leagues = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_leaguenames")." WHERE LeaguePublish = '1' ORDER BY LeagueName");
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

<?php echo _LS_CRICK_MOVETO;?><select name="moveto">
<option value="index.php"><?php echo _LS_CRICK_TABLES;?></option>
<option value="headtohead.php"><?php echo _LS_CRICK_HEADTOHEAD;?></option>
</select> <input type="submit" class="button"  value=">>" name="submit6">
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
	<tr>
	<td bgcolor="<?= $cricket_bg1 ?>" align="left" valign="middle" colspan="2" style="padding-left:2px;">
	<h3><?php echo _LS_CRICK_SEASONSTATS;?></h3>
	<?php echo _LS_CRICK_SEASONFILTER;?> <select name="season">
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
	</select> <input type="submit" class="button" value=">>" name="submit">
	</td>
	</tr>

	<?php
	//query to get data from the matches
	$query = $xoopsDB->query("SELECT
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchHomeWickets AS homewickets,
	LM.LeagueMatchAwayRuns AS awayruns,
	LM.LeagueMatchAwayWickets AS awaywickets
	FROM
	".$xoopsDB->prefix("cricket_leaguematches")." LM
	WHERE
	LM.LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
	LM.LeagueMatchLeagueID LIKE '$cricket_defaultleagueid' AND
	LM.LeagueMatchHomeRuns IS NOT NULL AND
	LM.LeagueMatchHomeWickets IS NOT NULL AND
	LM.LeagueMatchAwayRuns IS NOT NULL AND
	LM.LeagueMatchAwayWickets IS NOT NULL
	");

	//Sets counter variables into zero
	$home_wins = 0;
	$away_wins = 0;

	$draws = 0;

	$home_runs = 0;
	$home_wickets = 0;
	$away_runs = 0;
	$away_wickets = 0;

	$played = 0;
	$runs = 0;
	$wickets = 0;

	//data check
	while($cricket_data = $xoopsDB->fetchArray($query))
	{
		//Home win
		if($cricket_data['homeruns'] > $cricket_data['awayruns'])
		{
			$home_wins++;
			$home_runs = $home_runs + $cricket_data['homeruns'];
			$home_wickets = $home_wickets + $cricket_data['homewickets'];
			$away_runs = $away_runs + $cricket_data['awayruns'];
			$away_wickets = $away_wickets + $cricket_data['awaywickets'];
		}
		//Draw
		elseif($cricket_data['homeruns'] == $cricket_data['awayruns'])
		{
			$draws++;
			$home_runs = $home_runs + $cricket_data['homeruns'];
			$home_wickets = $home_wickets + $cricket_data['homewickets'];
			$away_runs = $away_runs + $cricket_data['awayruns'];
			$away_wickets = $away_wickets + $cricket_data['awaywickets'];
		}
		//Away win
		elseif($cricket_data['homeruns'] < $cricket_data['awayruns'])
		{
			$away_wins++;
			$home_runs = $home_runs + $cricket_data['homeruns'];
			$home_wickets = $home_wickets + $cricket_data['homewickets'];
			$away_runs = $away_runs + $cricket_data['awayruns'];
			$away_wickets = $away_wickets + $cricket_data['awaywickets'];
		}
	}

	$played = $home_wins + $draws + $away_wins;

	$runs = $home_runs + $away_runs;
	$wickets = $home_wickets + $away_wickets;

	//Avoid divide by zero
	if($xoopsDB->getRowsNum($query) < 1)
	{
		$home_win_percent = 0;
		$away_win_percent = 0;
		$draw_percent = 0;

		$home_run_average = 0;
		$home_wicket_average = 0;
		$away_run_average = 0;
		$away_wicket_average = 0;
		$run_average = 0;
		$wicket_average = 0;

		$home_win_percent_ = number_format($home_win_percent, 2, '.', '');
		$away_win_percent_ = number_format($away_win_percent, 2, '.', '');
		$draw_percent_ = number_format($draw_percent, 2, '.', '');
		$home_run_average_ = number_format($home_run_average, 2, '.', '');
		$home_wicket_average_ = number_format($home_wicket_average, 2, '.', '');
		$away_run_average_ = number_format($away_run_average, 2, '.', '');
		$away_wicket_average_ = number_format($away_wicket_average, 2, '.', '');
		$run_average_ = number_format($run_average, 2, '.', '');
		$wicket_average_ = number_format($wicket_average, 2, '.', '');
	}
	else
	{
		//Calculates percents and averages
		$home_win_percent = round(100*($home_wins/$played),2);
		$away_win_percent = round(100*($away_wins/$played),2);
		$draw_percent = round(100*($draws/$played),2);

		$home_run_average = round(($home_runs/$played),2);
		$home_wicket_average = round(($home_wickets/$played),2);
		$away_run_average = round(($away_runs/$played),2);
		$away_wicket_average = round(($away_wickets/$played),2);
		$run_average = round(($runs/$played),2);
		$wicket_average = round(($wickets/$played),2);

		$home_win_percent_ = number_format($home_win_percent, 2, '.', '');
		$away_win_percent_ = number_format($away_win_percent, 2, '.', '');
		$draw_percent_ = number_format($draw_percent, 2, '.', '');
		$home_run_average_ = number_format($home_run_average, 2, '.', '');
		$home_wicket_average_ = number_format($home_wicket_average, 2, '.', '');
		$away_run_average_ = number_format($away_run_average, 2, '.', '');
		$away_wicket_average_ = number_format($away_wicket_average, 2, '.', '');
		$run_average_ = number_format($run_average, 2, '.', '');
		$wicket_average_ = number_format($run_average, 2, '.', '');
	}
	?>

	<tr>
	<td align="left" valign="middle" width="40%" style="padding-left:5px;">
	<b><?php echo _LS_CRICK_MATCHESPLAYED;?></b></td>

	<td align="left" valign="middle" width="60%">
	<?= $played ?></td>
	</tr>

	<tr>
		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_CRICK_HOMEWINS;?></b></td>

		<td align="left" valign="middle" width="60%">
		<?= "$home_wins ($home_win_percent_ %)" ?></td>
	</tr>

	<tr>
		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_CRICK_AWAYWINS;?></b></td>

		<td align="left" valign="middle" width="60%">
		<?= "$away_wins ($away_win_percent_ %)" ?></td>
	</tr>

	<tr>
		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_CRICK_DRAWS;?></b></td>

		<td align="left" valign="middle" width="60%">
		<?= "$draws ($draw_percent_ %)" ?></td>
	</tr>

	<tr>
		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_CRICK_TOTRUNS;?></b></td>

		<td align="left" valign="middle" width="60%">
		<?= "$runs ("._LS_CRICK_AVERAGE." $run_average "._LS_CRICK_PERMATCH.")" ?></td>
	</tr>

	<tr>
		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_CRICK_TOTWICKETS;?></b></td>

		<td align="left" valign="middle" width="60%">
		<?= "$wickets ("._LS_CRICK_WICKET_AVERAGE." $wicket_average "._LS_CRICK_WICKET_PERMATCH.")" ?></td>
	</tr>

	<tr>
		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_CRICK_HOMETEAMRUNS;?></b></td>

		<td align="left" valign="middle" width="60%">
		<?= "$home_runs ("._LS_CRICK_AVERAGE." $home_run_average "._LS_CRICK_PERMATCH.")" ?></td>
	</tr>

	<tr>
		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_CRICK_HOMETEAMWICKETS;?></b></td>

		<td align="left" valign="middle" width="60%">
		<?= "$home_wickets ("._LS_CRICK_WICKET_AVERAGE." $home_wicket_average "._LS_CRICK_WICKET_PERMATCH.")" ?></td>
	</tr>

	<tr>
		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_CRICK_AWAYTEAMRUNS;?></b></td>

		<td align="left" valign="middle" width="60%">
		<?= "$away_runs ("._LS_CRICK_AVERAGE." $away_run_average "._LS_CRICK_PERMATCH.")" ?></td>
	</tr>

	<tr>
		<td align="left" valign="middle" width="40%" style="padding-left:5px;">
		<b><?php echo _LS_CRICK_AWAYTEAMWICKETS;?></b></td>

		<td align="left" valign="middle" width="60%">
		<?= "$away_wickets ("._LS_CRICK_WICKET_AVERAGE." $away_wicket_average "._LS_CRICK_WICKET_PERMATCH.")" ?></td>
	</tr>
	</table>

	<table width="100%" cellspacing="1" cellpadding="5" border="0" align="center">
	<tr>
	<td bgcolor="<?= $cricket_top_bg ?>" align="center" valign="middle" colspan="3">
	<b><?php echo _LS_CRICK_BIGHOMEWIN;?></b></td>
	</tr>

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

	//Max home win
	$maxhomewin = $xoopsDB->query("SELECT
	MAX(LeagueMatchHomeRuns - LeagueMatchAwayRuns) AS ero
	FROM ".$xoopsDB->prefix("cricket_leaguematches")."
	WHERE
	LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
	LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	$temp_data = $xoopsDB->fetchArray($maxhomewin);
	$temp_number = $temp_data['ero'];

	//query to get all final scores with maximum value from previous query
	$maxhomewin = $xoopsDB->query("SELECT
	O.OpponentName AS hometeam,
	OP.OpponentName AS awayteam,
	DATE_FORMAT(LM.LeagueMatchDate, '$cricket_print_date') AS date,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM
	".$xoopsDB->prefix("cricket_leaguematches")." AS LM,
	".$xoopsDB->prefix("cricket_opponents")." O,
	".$xoopsDB->prefix("cricket_opponents")." OP
	WHERE
	O.OpponentID = LM.LeagueMatchHomeID AND
	OP.OpponentID = LM.LeagueMatchAwayID AND
	(LM.LeagueMatchHomeRuns - LM.LeagueMatchAwayRuns) = '$temp_number' AND
	LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
	LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	ORDER BY LM.LeagueMatchDate
	");

	//Print max home wins
	$i = 0;
	while($cricket_data = $xoopsDB->fetchArray($maxhomewin))
	{
		if($i = 0)
			$temp_color = $cricket_bg1;
		else
			$temp_color = $cricket_bg2;

		echo"
		<tr bgcolor=\"$temp_color\">
		<td align=\"right\" valign=\"middle\" width=\"30%\">
		$cricket_data[date]</td>

		<td align=\"center\" valign=\"middle\" width=\"40%\">
		$cricket_data[hometeam] - $cricket_data[awayteam]</td>

		<td align=\"left\" valign=\"middle\" width=\"30%\">
		$cricket_data[homeruns] - $cricket_data[awayruns]</td>
		</tr>
		";

		$i++;
	}
	?>

	<tr>
	<td colspan="4">
	<br>
	</td>
	</tr>

	<tr>
	<td bgcolor="<?= $cricket_top_bg ?>" align="center" valign="middle" colspan="3">
	<b><?php echo _LS_CRICK_BIGAWAYWIN;?></b></td>
	</tr>

	<?php

	//Max away win
	$maxawaywin = $xoopsDB->query("SELECT
	MIN(LeagueMatchHomeRuns - LeagueMatchAwayRuns) AS ero
	FROM ".$xoopsDB->prefix("cricket_leaguematches")."
	WHERE
	LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
	LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	$temp_data = $xoopsDB->fetchArray($maxawaywin);
	$temp_number = $temp_data['ero'];

	//query to get all max away wins
	$maxawaywin = $xoopsDB->query("SELECT
	O.OpponentName AS hometeam,
	OP.OpponentName AS awayteam,
	DATE_FORMAT(LM.LeagueMatchDate, '$cricket_print_date') AS date,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM
	".$xoopsDB->prefix("cricket_leaguematches")." AS LM,
	".$xoopsDB->prefix("cricket_opponents")." O,
	".$xoopsDB->prefix("cricket_opponents")." OP
	WHERE
	O.OpponentID = LM.LeagueMatchHomeID AND
	OP.OpponentID = LM.LeagueMatchAwayID AND
	(LM.LeagueMatchHomeRuns - LM.LeagueMatchAwayRuns) = '$temp_number' AND
	LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
	LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	ORDER BY LM.LeagueMatchDate
	");

	//Prints max away wins
	$i = 0;
	while($cricket_data = $xoopsDB->fetchArray($maxawaywin))
	{
		if($i = 0)
			$temp_color = $cricket_bg1;
		else
			$temp_color = $cricket_bg2;

		echo"
		<tr bgcolor=\"$temp_color\">
		<td align=\"right\" valign=\"middle\" width=\"30%\">
		$cricket_data[date]</td>

		<td align=\"center\" valign=\"middle\" width=\"40%\">
		$cricket_data[hometeam] - $cricket_data[awayteam]</td>

		<td align=\"left\" valign=\"middle\" width=\"30%\">
		$cricket_data[homeruns] - $cricket_data[awayruns]</td>
		</tr>
		";

		$i++;
	}
	?>

	<tr>
	<td colspan="4">
	<br>
	</td>
	</tr>

	<tr>
	<td bgcolor="<?= $cricket_top_bg ?>" align="center" valign="middle" colspan="3">
	<b><?php echo _LS_CRICK_HIGHAGGSCORE;?></b></td>
	</tr>

	<?php
	//Most runs & wickets scored in one match
	$maxruns = $xoopsDB->query("SELECT
	MAX(LeagueMatchHomeRuns + LeagueMatchAwayRuns) AS summa
	FROM ".$xoopsDB->prefix("cricket_leaguematches")."
	WHERE
	LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
	LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	");

	$temp_data = $xoopsDB->fetchArray($maxruns);
	$temp_number = $temp_data['summa'];

	//query to get max values
	$maxruns = $xoopsDB->query("SELECT
	O.OpponentName AS hometeam,
	OP.OpponentName AS awayteam,
	DATE_FORMAT(LM.LeagueMatchDate, '$cricket_print_date') AS date,
	LM.LeagueMatchHomeRuns AS homeruns,
	LM.LeagueMatchAwayRuns AS awayruns
	FROM
	".$xoopsDB->prefix("cricket_leaguematches")." AS LM,
	".$xoopsDB->prefix("cricket_opponents")." O,
	".$xoopsDB->prefix("cricket_opponents")." OP
	WHERE
	O.OpponentID = LM.LeagueMatchHomeID AND
	OP.OpponentID = LM.LeagueMatchAwayID AND
	(LM.LeagueMatchHomeRuns + LM.LeagueMatchAwayRuns) = '$temp_number' AND
	LeagueMatchSeasonID LIKE '$cricket_defaultseasonid' AND
	LeagueMatchLeagueID LIKE '$cricket_defaultleagueid'
	ORDER BY LM.LeagueMatchDate
	");

	//Print max aggregate scores
	$i = 0;
	while($cricket_data = $xoopsDB->fetchArray($maxruns))
	{
		if($i = 0)
			$temp_color = $cricket_bg1;
		else
			$temp_color = $cricket_bg2;

		echo"
		<tr bgcolor=\"$temp_color\">
		<td align=\"right\" valign=\"middle\" width=\"30%\">
		$cricket_data[date]</td>

		<td align=\"center\" valign=\"middle\" width=\"40%\">
		$cricket_data[hometeam] - $cricket_data[awayteam]</td>

		<td align=\"left\" valign=\"middle\" width=\"30%\">
		$cricket_data[homeruns] - $cricket_data[awayruns]</td>
		</tr>
		";

		$i++;
	}
	?>
	</table>
	</td>
	</tr>
	</table><br>
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