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
include_once 'admin_header.php';
//Include file, which checks for permissions and sets navigation
include '../../../include/cp_header.php';

if (isset($_POST['season_select'])) {
    $cricket_season = explode("____",$_POST['season_select']);
}
elseif (isset($_POST['seasonid'])) {
    $cricket_season = array ($_POST['seasonid'], $_POST['seasonname']);
}
elseif (!isset($_SESSION['season_id'])) {
    $sql = "SELECT SeasonID, SeasonName FROM ".$xoopsDB->prefix("cricket_seasonnames")." WHERE SeasonDefault=1";
    $cricket_seasonname = $xoopsDB->query($sql);
    $cricket_seasonname = $xoopsDB->fetchArray($cricket_seasonname);
    $cricket_season = array($cricket_seasonname['SeasonID'], $cricket_seasonname['SeasonName']);
}
else {
    $cricket_season = array($_SESSION['season_id'], $_SESSION['season_name']);
}

if (isset($_POST['league_select'])) {
    $cricket_league = explode("____",$_POST['league_select']);
}
elseif (isset($_POST['leagueid'])) {
    $cricket_league = array ($_POST['leagueid'], $_POST['leaguename']);
}
elseif (!isset($_SESSION['league_id'])) {
    $sql2 = "SELECT LeagueID, LeagueName FROM ".$xoopsDB->prefix("cricket_leaguenames")." WHERE LeagueDefault=1";
    $cricket_leaguename = $xoopsDB->query($sql2);
    $cricket_leaguename = $xoopsDB->fetchArray($cricket_leaguename);
    $cricket_league = array($cricket_leaguename['LeagueID'], $cricket_leaguename['LeagueName']);
}
else {
    $cricket_league = array($_SESSION['league_id'], $_SESSION['league_name']);
}

$_SESSION['season_id'] = $cricket_season[0];
$_SESSION['season_name'] = $cricket_season[1];
$cricket_seasonid = $_SESSION['season_id'];
$cricket_seasonname = $_SESSION['season_name'];
$_SESSION['league_id'] = $cricket_league[0];
$_SESSION['league_name'] = $cricket_league[1];
$cricket_leagueid = $_SESSION['league_id'];
$cricket_leaguename = $_SESSION['league_name'];

$PHP_SELF = $_SERVER['PHP_SELF'];
$cricket_action = isset($_GET['action']) ? $_GET['action'] : null;
$cricket_action = isset($_POST['action']) ? $_POST['action'] : $cricket_action;

$cricket_add_submit = isset($_POST['add_submit']) ? $_POST['add_submit'] : false;
$cricket_modify_submit = isset($_POST['modify_submit']) ? $_POST['modify_submit'] : false;
$cricket_delete_submit = isset($_POST['delete_submit']) ? $_POST['delete_submit'] : false;
$cricket_modifyall_submit = isset($_POST['modifyall_submit']) ? $_POST['modifyall_submit']: null;

xoops_cp_header();
$indexAdmin = new ModuleAdmin();
echo $indexAdmin->addNavigation('leaguematches.php');

//Exit check, if there are less than 2 teams in database
$query = $xoopsDB->query("SELECT OpponentID FROM ".$xoopsDB->prefix("cricket_opponents")." WHERE OpponentLeagueID = $cricket_leagueid  AND OpponentSeasonID = $cricket_seasonid");

if($xoopsDB->getRowsNum($query) < 2)
{
//    echo "<br><br>"._AM_CRICK_ADDTWOTEAMS."<br><br>
//		<a href=\"opponents.php\">" ._AM_CRICK_ADDTEAMS. "</a>";
//    exit();
    redirect_header("opponents.php",1,_AM_CRICK_ADDTWOTEAMS);
}

if($cricket_add_submit)
{
	$year = $cricket_seasonname;
    $month = intval($_POST['month']);
    $day = intval($_POST['day']);
    $dateandtime = $year."-".$month."-".$day;
    
    //Check the data of the submitted form
    $i = 0;
    
    while($i < 15)
    {
        $home = $_POST['home'];	//home team id
        $away = $_POST['away'];	//away team id
        $home_bpoints = $_POST['home_bpoints']; //home team bonus points
        $away_bpoints = $_POST['away_bpoints']; //home team bonus points
        $home_runs = $_POST['home_runs'];
        $home_wickets = $_POST['home_wickets'];
        $away_runs = $_POST['away_runs'];
        $away_wickets = $_POST['away_wickets'];
        
        //Set the default
        $home_bonus = -1;
		$away_bonus = -1;
		$home_winner = -1;
        $home_loser = -1;
        $home_tie = -1;
        $away_winner = -1;
        $away_loser = -1;
        $away_tie = -1;
        
        //If home and away are not the same
        if($home[$i] != $away[$i])
        {
            $home[$i] = intval($home[$i]);
            $away[$i] = intval($away[$i]);
            $home_bpoints[$i] = $home_bpoints[$i] != null ? intval($home_bpoints[$i]) : null;
            $away_bpoints[$i] = $away_bpoints[$i] != null ? intval($away_bpoints[$i]) : null;
            $home_runs[$i] = $home_runs[$i] != null ? intval($home_runs[$i]) : null;
            $home_wickets[$i] = $home_wickets[$i] != null ? intval($home_wickets[$i]) : null;
            $away_runs[$i] = $away_runs[$i] != null ? intval($away_runs[$i]) : null;
            $away_wickets[$i] = $away_wickets[$i] != null ? intval($away_wickets[$i]) : null;

            //Home team wins
            if($home_runs[$i] > $away_runs[$i])
            {
                $home_winner = $home[$i];
                $away_loser = $away[$i];
				$home_bonus = $home_bpoints[$i];
				$away_bonus = $away_bpoints[$i];
            }
            
            //Away win
            elseif($home_runs[$i] < $away_runs[$i])
            {
                $away_winner = $away[$i];
                $home_loser = $home[$i];
				$home_bonus = $home_bpoints[$i];
				$away_bonus = $away_bpoints[$i];
            }
            
            //Draw
            elseif($home_runs[$i] == $away_runs[$i])
            {
                $home_tie = $home[$i];
                $away_tie = $away[$i];
				$home_bonus = $home_bpoints[$i];
				$away_bonus = $away_bpoints[$i];
            }
            
            //query to check if home or away team already exists in the current day
            $query = $xoopsDB->query("SELECT LM.LeagueMatchID FROM
				".$xoopsDB->prefix("cricket_leaguematches")." LM
				WHERE
				(LM.LeagueMatchHomeID = '$home[$i]' OR
				LM.LeagueMatchAwayID = '$home[$i]' OR
				LM.LeagueMatchHomeID = '$away[$i]' OR
				LM.LeagueMatchAwayID = '$away[$i]') AND
				LM.LeagueMatchDate = '$dateandtime'
				");
            
            if($xoopsDB->getRowsNum($query) == 0)
            {
                if (($home_runs[$i] !== null) && ($home_runs[$i] !== null)) {
                    $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("cricket_leaguematches")." SET
						LeagueMatchSeasonID = '$cricket_seasonid',
						LeagueMatchLeagueID = '$cricket_leagueid',
						LeagueMatchDate = '$dateandtime',
						LeagueMatchHomeID = '$home[$i]',
						LeagueMatchAwayID = '$away[$i]',
						LeagueMatchHomeWinnerID = '$home_winner',
						LeagueMatchHomeLoserID = '$home_loser',
						LeagueMatchHomeBonus = '$home_bonus',
						LeagueMatchAwayBonus = '$away_bonus',
						LeagueMatchHomeBpoints = '$home_bpoints[$i]',
						LeagueMatchAwayBpoints = '$away_bpoints[$i]',
						LeagueMatchAwayWinnerID = '$away_winner',
						LeagueMatchAwayLoserID = '$away_loser',
						LeagueMatchHomeTieID = '$home_tie',
						LeagueMatchAwayTieID = '$away_tie',
						LeagueMatchHomeRuns = '$home_runs[$i]',
						LeagueMatchHomeWickets = '$home_wickets[$i]',
						LeagueMatchAwayRuns = '$away_runs[$i]',
						LeagueMatchAwayWickets = '$away_wickets[$i]',
                        LeagueMatchCreated = ".time()."
						");
                }
                else {
                    $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("cricket_leaguematches")." SET
						LeagueMatchSeasonID = '$cricket_seasonid',
						LeagueMatchLeagueID = '$cricket_leagueid',
						LeagueMatchDate = '$dateandtime',
						LeagueMatchHomeID = '$home[$i]',
						LeagueMatchAwayID = '$away[$i]',
						LeagueMatchHomeBonus = '-1',
						LeagueMatchAwayBonus = '-1',
						LeagueMatchHomeWinnerID = '-1',
						LeagueMatchHomeLoserID = '-1',
						LeagueMatchAwayWinnerID = '-1',
						LeagueMatchAwayLoserID = '-1',
						LeagueMatchHomeTieID = '-1',
						LeagueMatchAwayTieID = '-1',
                        LeagueMatchCreated = ".time()."
						");
                }
            }
        }
        $i++;
    }
}
elseif($cricket_modifyall_submit)
{
	$year = $cricket_seasonname;
    $month = intval($_POST['month']);
    $day = intval($_POST['day']);
    $dateandtime = $year."-".$month."-".$day;
    $qty = intval($_POST['qty']);
    
    //Delete old data from selected date
    $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("cricket_leaguematches")."
		WHERE LeagueMatchDate = '$dateandtime'
		");
    
    //Check the submitted form
    $i = 0;
    $home = $_POST['home'];	//home team id
    $away = $_POST['away'];	//away team id
    $home_bpoints = $_POST['home_bpoints'];	//home team bonus points
    $away_bpoints = $_POST['away_bpoints'];	//away team bonus points
    $home_runs = $_POST['home_runs'];
    $home_wickets = $_POST['home_wickets'];
    $away_runs = $_POST['away_runs'];
    $away_wickets = $_POST['away_wickets'];
    while($i < $qty)
    {
        $home[$i] = intval($home[$i]);
        $away[$i] = intval($away[$i]);
        $home_bpoints[$i] = $home_bpoints[$i] != null ? intval($home_bpoints[$i]) : null;
        $away_bpoints[$i] = $away_bpoints[$i] != null ? intval($away_bpoints[$i]) : null;
        $home_runs[$i] = $home_runs[$i] != null ? intval($home_runs[$i]) : null;
        $home_wickets[$i] = $home_wickets[$i] != null ? intval($home_wickets[$i]) : null;
        $away_runs[$i] = $away_runs[$i] != null ? intval($away_runs[$i]) : null;
        $away_wickets[$i] = $away_wickets[$i] != null ? intval($away_wickets[$i]) : null;

        //Set default
        $home_bonus = -1;
		$away_bonus = -1;
        $home_winner = -1;
        $home_loser = -1;
        $home_tie = -1;
        $away_winner = -1;
        $away_loser = -1;
        $away_tie = -1;
        
        //Home wins
        if($home_runs[$i] > $away_runs[$i])
        {
            $home_winner = $home[$i];
            $away_loser = $away[$i];
			$home_bonus = $home_bpoints[$i];
			$away_bonus = $away_bpoints[$i];
        }

        //Away wins
        elseif($home_runs[$i] < $away_runs[$i])
        {
            $away_winner = $away[$i];
            $home_loser = $home[$i];
			$home_bonus = $home_bpoints[$i];
			$away_bonus = $away_bpoints[$i];
        }

        //Draw
        elseif($home_runs[$i] == $away_runs[$i])
        {
            $home_tie = $home[$i];
            $away_tie = $away[$i];
			$home_bonus = $home_bpoints[$i];
			$away_bonus = $away_bpoints[$i];
        }

        if (($home_runs[$i] !== null) && ($away_runs[$i] !== null)) {
            $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("cricket_leaguematches")." SET
				LeagueMatchSeasonID = '$cricket_seasonid',
				LeagueMatchLeagueID = '$cricket_leagueid',
				LeagueMatchDate = '$dateandtime',
				LeagueMatchHomeID = '$home[$i]',
				LeagueMatchAwayID = '$away[$i]',
				LeagueMatchHomeBonus = '$home_bonus',
				LeagueMatchAwayBonus = '$away_bonus',
				LeagueMatchHomeBpoints = '$home_bpoints[$i]',
				LeagueMatchAwayBpoints = '$away_bpoints[$i]',
				LeagueMatchHomeWinnerID = '$home_winner',
				LeagueMatchHomeLoserID = '$home_loser',
				LeagueMatchAwayWinnerID = '$away_winner',
				LeagueMatchAwayLoserID = '$away_loser',
				LeagueMatchHomeTieID = '$home_tie',
				LeagueMatchAwayTieID = '$away_tie',
				LeagueMatchHomeRuns = '$home_runs[$i]',
				LeagueMatchHomeWickets = '$home_wickets[$i]',
				LeagueMatchAwayRuns = '$away_runs[$i]',
				LeagueMatchAwayWickets = '$away_wickets[$i]',
                LeagueMatchCreated = ".time()."
				");
        }
        else {
            $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("cricket_leaguematches")." SET
						LeagueMatchSeasonID = '$cricket_seasonid',
						LeagueMatchLeagueID = '$cricket_leagueid',
						LeagueMatchDate = '$dateandtime',
						LeagueMatchHomeID = '$home[$i]',
						LeagueMatchAwayID = '$away[$i]',
						LeagueMatchHomeBonus = '-1',
						LeagueMatchAwayBonus = '-1',
						LeagueMatchHomeWinnerID = '-1',
						LeagueMatchHomeLoserID = '-1',
						LeagueMatchAwayWinnerID = '-1',
						LeagueMatchAwayLoserID = '-1',
						LeagueMatchHomeTieID = '-1',
						LeagueMatchAwayTieID = '-1',
                        LeagueMatchCreated = ".time()."
						");
        }            
        $i++;
    }
}
elseif($cricket_modify_submit)
{
    $mid = intval($_POST['mid']);
    $homeid = intval($_POST['homeid']);
    $awayid = intval($_POST['awayid']);
    $year = $cricket_seasonname;
    $month = intval($_POST['month']);
    $day = intval($_POST['day']);
    $dateandtime = $year."-".$month."-".$day;
    
    $home = intval($_POST['home']);	//kotijoukkueen id
    $away = intval($_POST['away']);	//vierasjoukkueen id
    $home_bpoints = $_POST['home_bpoints'] != null ? intval($_POST['home_bpoints']) : null;
    $away_bpoints = $_POST['away_bpoints'] != null ? intval($_POST['away_bpoints']) : null;
    $home_runs = $_POST['home_runs'] != null ? intval($_POST['home_runs']) : null;
    $home_wickets = $_POST['home_wickets'] != null ? intval($_POST['home_wickets']) : null;
    $away_runs = $_POST['home_runs'] != null ? intval($_POST['away_runs']) : null;
    $away_wickets = $_POST['home_wickets'] != null ? intval($_POST['away_wickets']) : null;
    
    //Set default
    $home_bonus = -1;
    $home_bonus = -1;
    $home_winner = -1;
    $home_loser = -1;
    $home_tie = -1;
    $away_winner = -1;
    $away_loser = -1;
    $away_tie = -1;
    
    //Check that home and away are not the same
    if($home != $away)
    {
        //Home wins
        if($home_runs > $away_runs)
        {
            $home_winner = $home;
            $away_loser = $away;
            $home_bonus = $home_bpoints;
            $away_bonus = $away_bpoints;
        }
        
        //Away wins
        elseif($home_runs < $away_runs)
        {
            $away_winner = $away;
            $home_loser = $home;
            $home_bonus = $home_bpoints;
            $away_bonus = $away_bpoints;
        }
        
        //Draw
        elseif($home_runs == $away_runs)
        {
            $home_tie = $home;
            $away_tie = $away;
            $home_bonus = $home_bpoints;
            $away_bonus = $away_bpoints;
        }
        
        //query to check if home or away team already exists in the current day
        $query = $xoopsDB->query("SELECT LM.LeagueMatchID FROM
			".$xoopsDB->prefix("cricket_leaguematches")." LM
			WHERE
			(LM.LeagueMatchHomeID = '$home' OR
			LM.LeagueMatchAwayID = '$home' OR
			LM.LeagueMatchHomeID = '$homeid' OR
			LM.LeagueMatchAwayID = '$homeid' OR
			LM.LeagueMatchHomeID = '$away' OR
			LM.LeagueMatchAwayID = '$away' OR
			LM.LeagueMatchHomeID = '$awayid' OR
			LM.LeagueMatchAwayID = '$awayid') AND
			LM.LeagueMatchDate = '$dateandtime'
			");
        
        if($xoopsDB->getRowsNum($query) < 2)
        {
            if (($home_runs !== null) && ($away_runs !== null)) {
                $xoopsDB->query("UPDATE ".$xoopsDB->prefix("cricket_leaguematches")." SET
					LeagueMatchDate = '$dateandtime',
					LeagueMatchHomeID = '$home',
					LeagueMatchAwayID = '$away',
					LeagueMatchHomeBonus = '$home_bonus',
					LeagueMatchAwayBonus = '$away_bonus',
					LeagueMatchHomeBpoints = '$home_bpoints',
					LeagueMatchAwayBpoints = '$away_bpoints',
					LeagueMatchHomeWinnerID = '$home_winner',
					LeagueMatchHomeLoserID = '$home_loser',
					LeagueMatchAwayWinnerID = '$away_winner',
					LeagueMatchAwayLoserID = '$away_loser',
					LeagueMatchHomeTieID = '$home_tie',
					LeagueMatchAwayTieID = '$away_tie',
					LeagueMatchHomeRuns = '$home_runs',
					LeagueMatchHomeWickets = '$home_wickets',
					LeagueMatchAwayRuns = '$away_runs',
					LeagueMatchAwayWickets = '$away_wickets',
                    LeagueMatchCreated = ".time()."
					WHERE LeagueMatchID = '$mid'
					LIMIT 1
					");
            }
            else {
                $xoopsDB->query("UPDATE ".$xoopsDB->prefix("cricket_leaguematches")." SET
					LeagueMatchDate = '$dateandtime',
					LeagueMatchHomeID = '$home',
					LeagueMatchAwayID = '$away',
					LeagueMatchHomeBonus = '-1',
					LeagueMatchAwayBonus = '-1',
					LeagueMatchHomeBpoints = NULL,
					LeagueMatchAwayBpoints = NULL,
					LeagueMatchHomeWinnerID = '-1',
					LeagueMatchHomeLoserID = '-1',
					LeagueMatchAwayWinnerID = '-1',
					LeagueMatchAwayLoserID = '-1',
					LeagueMatchHomeTieID = '-1',
					LeagueMatchAwayTieID = '-1',
					LeagueMatchHomeRuns = NULL,
					LeagueMatchHomeWickets = NULL,
					LeagueMatchAwayRuns = NULL,
					LeagueMatchAwayWickets = NULL,
                    LeagueMatchCreated = ".time()."
					WHERE LeagueMatchID = '$mid'
					LIMIT 1
					");
            }                
        }        
    }
}
elseif($cricket_delete_submit)
{
    $mid = intval($_POST['mid']);
    $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("cricket_leaguematches")." WHERE LeagueMatchID = '$mid' LIMIT 1");
}

	?>

        <?php
	include('head.php');
	include('leaguehead.php');
	?>

	<table align="center" width="700">
		<tr>
		<td align="left" valign="top">
		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<?php
		if(!isset($cricket_action))
		{
		?>
		<h3><?php echo _AM_CRICK_ADDMATCH;?></h3>
		<?php echo _AM_CRICK_ADDMATCHNOTE;?><br><br>

		<?php echo _AM_CRICK_DATE;?>
		<select name="day">
		<?php
		//print the days
		for($i = 1 ; $i < 32 ; $i++)
		{
		    if($i<10)
		    {
		        $i = "0".$i;
		    }
		    if($i == "01")
		    echo "<option value=\"$i\" SELECTED>$i</option>\n";
		    else
		    echo "<option value=\"$i\">$i</option>\n";
		}
		?>
		</select>&nbsp;/&nbsp;

		<select name="month">
		<?php
		//print the months
		for($i = 1 ; $i < 13 ; $i++)
		{
		    if($i<10)
		    {
		        $i = "0".$i;
		    }
		    if($i == "01")
		    echo "<option value=\"$i\" SELECTED>$i</option>\n";
		    else
		    echo "<option value=\"$i\">$i</option>\n";
		}
		?>
		</select>&nbsp;/&nbsp;

		<select name="year">
		<?php
		//print the years
		for($i = $cricket_seasonname ; $i < $cricket_seasonname+1 ; $i++)
		{
		    if($i<10)
		    {
		        $i = "0".$cricket_seasonname;
		    } 
		    if($i == $cricket_seasonname)
		    echo "<option value=\"$i\" SELECTED>$i</option>\n";
		    else
		    echo "<option value=\"$i\">$i</option>\n";
		}
		?>
		</select><br><br>
		<?php echo _AM_CRICK_ADDMATCHNOTE2;?><br><br>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
		<td align="left" valign="middle"><b><?php echo _AM_CRICK_HOMETEAM;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_BONUSPOINTS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_RUNS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_WICKETS;?></b></td>
		<td align="left" valign="middle"><b><?php echo _AM_CRICK_AWAYTEAM;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_BONUSPOINTS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_RUNS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_WICKETS;?></b></td>
		</tr>

		<?php
		//query to get all the teams
		$cricket_get_opponents = $xoopsDB->query("SELECT OpponentID AS id,
		OpponentName AS name
		FROM ".$xoopsDB->prefix("cricket_opponents")." WHERE OpponentLeagueID = $cricket_leagueid AND OpponentSeasonID = $cricket_seasonid 
		ORDER BY OpponentName");
		
		//Prints 15 forms
		$i=0;
		
		while($i < 15)
		{
	    //query back to row 0 if not the first time in the loop
		    if($i>0)
		    mysql_data_seek($cricket_get_opponents, 0);
		    
		    echo'
			<tr>
			<td align="left" valign="middle">
			';
		    
		    echo"<select name=\"home[$i]\">";
		    
		    while($cricket_data = $xoopsDB->fetchArray($cricket_get_opponents))
		    {
		        echo"<option value=\"$cricket_data[id]\">$cricket_data[name]</option>\n";
		    }
		    
		    echo'
			</select>
			</td>
			';
			
			echo"
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"home_bpoints[$i]\" size=\"2\"></td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"home_runs[$i]\" size=\"2\"></td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"home_wickets[$i]\" size=\"2\"></td>
			";
			
			echo'
			<td align="left" valign="middle">
			';
		    
		    //Back to line 0 in the query
		    mysql_data_seek($cricket_get_opponents, 0);
		    
		    echo"<select name=\"away[$i]\">";
		    
		    while($cricket_data = $xoopsDB->fetchArray($cricket_get_opponents))
		    {
		        echo"<option value=\"$cricket_data[id]\">$cricket_data[name]</option>\n";
		    }
		    
		    echo"
			</select>
			</td>
			";
			
			echo"
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"away_bpoints[$i]\" size=\"2\"></td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"away_runs[$i]\" size=\"2\"></td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"away_wickets[$i]\" size=\"2\"></td>
			</tr>
			";
		    
		    $i++;
		}
		?>

		</table><br><br>
        <input type="hidden" name="seasonid" value="<?php echo $cricket_seasonid; ?>">
        <input type="hidden" name="seasonname" value="<?php echo $cricket_seasonname; ?>">
        <input type="hidden" name="leagueid" value="<?php echo $cricket_leagueid; ?>">
        <input type="hidden" name="leaguename" value="<?php echo $cricket_leaguename; ?>">
		<input type="submit" name="add_submit" value="<?php echo _AM_CRICK_ADDMATCHES;?>">
		</form>
		<?php
		}
		elseif($cricket_action == 'modifyall')
		{
		    $date = $_REQUEST['date'];
		    
		$cricket_get_matches = $xoopsDB->query("SELECT DAYOFMONTH(LM.LeagueMatchDate) AS dayofmonth,
		MONTH(LM.LeagueMatchDate) AS month,
		YEAR(LM.LeagueMatchDate) AS year,
		LM.LeagueMatchHomeID AS homeid,
		LM.LeagueMatchAwayID AS awayid,
		LM.LeagueMatchHomeBpoints AS homebpoints,
		LM.LeagueMatchAwayBpoints AS awaybpoints,
		LM.LeagueMatchHomeRuns AS homeruns,
		LM.LeagueMatchHomeWickets AS homewickets,
		LM.LeagueMatchAwayRuns AS awayruns,
		LM.LeagueMatchAwayWickets AS awaywickets
		FROM ".$xoopsDB->prefix("cricket_leaguematches")." LM
		WHERE LM.LeaguematchDate = '$date'
		");
		    
		//query to get date
		$cricket_get_match = $xoopsDB->query("SELECT DAYOFMONTH(LM.LeagueMatchDate) AS dayofmonth,
		MONTH(LM.LeagueMatchDate) AS month,
		YEAR(LM.LeagueMatchDate) AS year
		FROM ".$xoopsDB->prefix("cricket_leaguematches")." LM
		WHERE LM.LeaguematchDate = '$date'
		LIMIT 1
		");
		    
		    $datedata = $xoopsDB->fetchArray($cricket_get_match);
		    
		$cricket_get_opponents = $xoopsDB->query("SELECT OpponentID AS id,
		OpponentName AS name
		FROM ".$xoopsDB->prefix("cricket_opponents")." WHERE OpponentLeagueID = $cricket_leagueid AND OpponentSeasonID = $cricket_seasonid
		ORDER BY OpponentName
		");
		?>

		<form method="post" action="<?php echo "$PHP_SELF" ?>">
		<h3><?php echo _AM_CRICK_MODMATCHES;?></h3>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">

			<tr>
				<td align="left" valign="top">
				<?php echo _AM_CRICK_DATETIME;?>
				</td>
				<td align="left" valign="top">

				<select name="day">
				<?php
				//Print the days
				for($i = 1 ; $i < 32 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($datedata['dayofmonth'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
				</select>&nbsp;/&nbsp;

				<select name="month">
				<?php
				//Print the months
				for($i = 1 ; $i < 13 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($datedata['month'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
				</select>&nbsp;/&nbsp;

				<select name="year">
				<?php
				//Print the years
				for($i = $cricket_seasonname ; $i < $cricket_seasonname+1 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$cricket_seasonname;
				    } 
				    if($datedata['year'] == $cricket_seasonname)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
			</select>
			</td>
		</tr>
		</table>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
		<td align="left" valign="middle"><b><?php echo _AM_CRICK_HOMETEAM;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_BONUSPOINTS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_RUNS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_WICKETSHOME;?></b></td>
		<td align="left" valign="middle"><b><?php echo _AM_CRICK_AWAYTEAM;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_BONUSPOINTS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_RUNS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_WICKETS;?></b></td>
		</tr>

		<?php
		//Lets get all the matches from selected date to the form
		$i = 0;
		while($matchdata = $xoopsDB->fetchArray($cricket_get_matches))
		{
		    //Back to line 0 in the query if not the first loop
		    if($i>0)
		    mysql_data_seek($cricket_get_opponents, 0);
		    
		    echo'
			<tr>
			<td align="left" valign="middle">
			';
		    
		    echo"<select name=\"home[$i]\">";
		    
		    while($cricket_data = $xoopsDB->fetchArray($cricket_get_opponents))
		    {
		        if($matchdata['homeid'] == $cricket_data['id'])
		        echo"<option value=\"$cricket_data[id]\" SELECTED>$cricket_data[name]</option>\n";
		    }
		    
		    echo'
			</select>
			</td>
			';
			
			echo'
			<td align="center" valign="middle"><input type="text" name="home_bpoints[$i]" size="2" value="$matchdata[homebpoints]"></td>
			<td align="center" valign="middle"><input type="text" name="home_runs[$i]" size="2" value="$matchdata[homeruns]"></td>
			<td align="center" valign="middle"><input type="text" name="home_wickets[$i]" size="2" value="$matchdata[homewickets]"></td>
			';
			
			echo'
			<td align="left" valign="middle">
			';
		    
		    //Back to line 0 in the query
		    mysql_data_seek($cricket_get_opponents, 0);
		    
		    echo"<select name=\"away[$i]\">";
		    
		    while($cricket_data = $xoopsDB->fetchArray($cricket_get_opponents))
		    {
		        if($matchdata['awayid'] == $cricket_data['id'])
		        echo"<option value=\"$cricket_data[id]\" SELECTED>$cricket_data[name]</option>\n";
		    }
		    
		    echo"
			</select>
			</td>
			";
			
			echo"
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"away_bpoints[$i]\" size=\"2\" value=\"$matchdata[awaybpoints]\"></td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"away_runs[$i]\" size=\"2\" value=\"$matchdata[awayruns]\"></td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"away_wickets[$i]\" size=\"2\" value=\"$matchdata[awaywickets]\"></td>
			</tr>
			";
		    
		    $i++;
		}
		?>
		</table>

		<font color="red"><?php echo _AM_CRICK_MODNOTICE1;?></font><br><br>
		<input type="hidden" name="qty" value="<?= $i ?>">
		<input type="hidden" name="seasonname" value="<?php echo $cricket_seasonname; ?>">
		<input type="hidden" name="leaguename" value="<?php echo $cricket_leaguename; ?>">
		<br><input type="submit" name="modifyall_submit" value="<?php echo _AM_CRICK_MODINPUT;?>">
		</form>

		<?php
		}
		elseif($cricket_action == 'modify')
		{
		    $id = intval($_REQUEST['id']);
		    
		$cricket_get_match = $xoopsDB->query("SELECT DAYOFMONTH(LM.LeagueMatchDate) AS dayofmonth,
		MONTH(LM.LeagueMatchDate) AS month,
		YEAR(LM.LeagueMatchDate) AS year,
		LM.LeagueMatchHomeID AS homeid,
		LM.LeagueMatchAwayID AS awayid,
		LM.LeagueMatchHomeBpoints AS homebpoints,
		LM.LeagueMatchAwayBpoints AS awaybpoints,
		LM.LeagueMatchHomeRuns AS homeruns,
		LM.LeagueMatchHomeWickets AS homewickets,
		LM.LeagueMatchAwayRuns AS awayruns,
		LM.LeagueMatchAwayWickets AS awaywickets
		FROM ".$xoopsDB->prefix("cricket_leaguematches")." LM
		WHERE LM.LeaguematchID = '$id'
		LIMIT 1
		");
		    
		    $cricket_get_opponents = $xoopsDB->query("SELECT OpponentID AS id,
		OpponentName AS name
		FROM ".$xoopsDB->prefix("cricket_opponents")." WHERE OpponentLeagueID = $cricket_leagueid AND OpponentSeasonID = $cricket_seasonid 
		ORDER BY OpponentName
		");
		    
		    $matchdata = $xoopsDB->fetchArray($cricket_get_match);
		?>
		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_CRICK_MODMATCH;?></h3>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">

			<tr>
				<td align="left" valign="top">
				<?php echo _AM_CRICK_DATETIME;?>
				</td>
				<td align="left" valign="top">

				<select name="day">
				<?php
				//Print the days
				for($i = 1 ; $i < 32 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($matchdata['dayofmonth'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
				</select>&nbsp;/&nbsp;

				<select name="month">
				<?php
				//Print the months
				for($i = 1 ; $i < 13 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($matchdata['month'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
				</select>&nbsp;/&nbsp;

				<select name="year">
				<?php
				//Print the years
				for($i = $cricket_seasonname ; $i < $cricket_seasonname+1 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$cricket_seasonname;
				    } 
				    if($matchdata['year'] == $cricket_seasonname)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
			</select>
			</td>
		</tr>
		</table>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
		<td align="left" valign="middle"><b><?php echo _AM_CRICK_HOMETEAM;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_BONUSPOINTS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_RUNS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_WICKETS;?></b></td>
		<td align="left" valign="middle"><b><?php echo _AM_CRICK_AWAYTEAM;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_BONUSPOINTS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_RUNS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_CRICK_WICKETS;?></b></td>
		</tr>

		<tr>
		<td align="left" valign="middle">

		<select name="home">
		<?php
		
		while($cricket_data = $xoopsDB->fetchArray($cricket_get_opponents))
		{
		    if($matchdata['homeid'] == $cricket_data['id'])
		    echo"<option value=\"$cricket_data[id]\" SELECTED>$cricket_data[name]</option>\n";
		    else
		    echo"<option value=\"$cricket_data[id]\">$cricket_data[name]</option>\n";
		}
		
		?>
		</select>
		</td>
		<td align="center" valign="middle"><input type="text" name="home_bpoints" size="2" value="<?= $matchdata['homebpoints'] ?>"></td>
		<td align="center" valign="middle"><input type="text" name="home_runs" size="2" value="<?= $matchdata['homeruns'] ?>"></td>
		<td align="center" valign="middle"><input type="text" name="home_wickets" size="2" value="<?= $matchdata['homewickets'] ?>"></td>

		<td align="left" valign="middle">

		<select name="away">
		<?php
		
		mysql_data_seek($cricket_get_opponents, 0);
		
		while($cricket_data = $xoopsDB->fetchArray($cricket_get_opponents))
		{
		    if($matchdata['awayid'] == $cricket_data['id'])
		    echo"<option value=\"$cricket_data[id]\" SELECTED>$cricket_data[name]</option>\n";
		    else
		    echo"<option value=\"$cricket_data[id]\">$cricket_data[name]</option>\n";
		}
		
		?>
		</select>
		</td>
		<td align="center" valign="middle"><input type="text" name="away_bpoints" size="2" value="<?= $matchdata['awaybpoints'] ?>"></td>
		<td align="center" valign="middle"><input type="text" name="away_runs" size="2" value="<?= $matchdata['awayruns'] ?>"></td>
		<td align="center" valign="middle"><input type="text" name="away_wickets" size="2" value="<?= $matchdata['awaywickets'] ?>"></td>
		</tr>
		</table>

		<input type="hidden" name="mid" value="<?= $id ?>">
		<input type="hidden" name="homeid" value="<?= $matchdata['awayid'] ?>">
		<input type="hidden" name="awayid" value="<?= $matchdata['homeid'] ?>">
		<br><input type="submit" name="modify_submit" value="<?php echo _AM_CRICK_MODINPUT2;?>">
		<input type="hidden" name="seasonid" value="<?php echo $cricket_seasonid; ?>">
		<input type="hidden" name="seasonname" value="<?php echo $cricket_seasonname; ?>">
		<input type="hidden" name="leagueid" value="<?php echo $cricket_leagueid; ?>">
		<input type="hidden" name="leaguename" value="<?php echo $cricket_leaguename; ?>">
		<br><br><br><br><br>
		<input type="submit" name="delete_submit" value="<?php echo _AM_CRICK_DELINPUT;?>">
		</form>

		<?php
		}
		?>
		</td></tr>
<table width="100%" align="center" cellspacing="3" cellpadding="3" border="3">
<tr>
		<td align="left" valign="top">

		<table width="100%">
		<?php
		$cricket_get_matches = $xoopsDB->query("SELECT O.OpponentName AS hometeam,
		OP.OpponentName AS awayteam,
		LM.LeagueMatchHomeBpoints AS bpoints_home,
		LM.LeagueMatchAwayBpoints AS bpoints_away,
		LM.LeagueMatchHomeRuns AS runs_home,
		LM.LeagueMatchHomeWickets AS wickets_home,
		LM.LeagueMatchAwayRuns AS runs_away,
		LM.LeagueMatchAwayWickets AS wickets_away,
		LM.LeagueMatchID AS id,
		LM.LeagueMatchDate AS defaultdate,
		DATE_FORMAT(LM.LeagueMatchDate, '%b %D %Y') AS date
		FROM ".$xoopsDB->prefix("cricket_leaguematches")." LM, ".$xoopsDB->prefix("cricket_opponents")." O, ".$xoopsDB->prefix("cricket_opponents")." OP
		WHERE O.OpponentID = LM.LeagueMatchHomeID AND
		OP.OpponentID = LM.LeagueMatchAwayID AND
		LeagueMatchSeasonID = '$cricket_seasonid' AND
		LeagueMatchLeagueID = '$cricket_leagueid'
		ORDER BY LM.LeagueMatchDate DESC");
		
		if($xoopsDB->getRowsNum($cricket_get_matches) < 1)
		{
		    echo "<b>  "._AM_CRICK_NOMATCHESYET.": <u>$cricket_seasonname</u> </b><br /><br />";
			echo "<b>  "._AM_CRICK_NOLEAGUEMATCHESYET.": <u>$cricket_leaguename</u> </b><br /><br />";
		}
		else
		{
		    echo "<b> "._AM_CRICK_MATCHESYET.": <u>$cricket_seasonname</u></b><br /><br />";
			echo "<b> "._AM_CRICK_LEAGUEMATCHESYET.": <u>$cricket_leaguename</u></b><br /><br /><br />";
		    
		    $i = 0;
		    $temp = '';

			echo "<table width=\"90%\" align=\"center\" cellspacing=\"3\" cellpadding=\"3\" border=\"1\">";		    
		    while($cricket_data = $xoopsDB->fetchArray($cricket_get_matches))
		    {
		        if($i == 0)
		        {
					echo"
					<tr>
					<td align=\"left\" colspan=\"2\">
					<b><a href=\"$PHP_SELF?action=modifyall&amp;date=$cricket_data[defaultdate]\">$cricket_data[date]</a></b>
					</td>
					</tr>
					";
				}
		        
		        if($cricket_data['date'] != "$temp" && $i > 0)
		        {
					echo"
					<tr>
					<td align=\"left\" colspan=\"2\">
					<br><br>
					<b><a href=\"$PHP_SELF?action=modifyall&amp;date=$cricket_data[defaultdate]\">$cricket_data[date]</a></b>
					</td>
					</tr>
					";
		        }
		        
				echo "
				<tr>
				<td align=\"left\" valign=\"top\" width=\"500\">
				<a href=\"$PHP_SELF?action=modify&amp;id=$cricket_data[id]\">$cricket_data[hometeam] Vs $cricket_data[awayteam]</a>
				</td>
				<td align=\"center\" valign=\"top\" width=\"250\">";
		        
		        if(!is_null($cricket_data['runs_home']) || ($cricket_data['wickets_home']))
		        echo"$cricket_data[runs_home] for $cricket_data[wickets_home] - $cricket_data[runs_away] for $cricket_data[wickets_away]";
		        else
		        echo'&nbsp;';
		        
		        echo"
				</td>
				</tr>";
				
		        $temp = "$cricket_data[date]";
		        
		        $i++;
		    }
	      echo "</table>";
		}
		?>
		</table>
		</td>
		</tr>
	</table>
<?php 
xoops_cp_footer();
?>