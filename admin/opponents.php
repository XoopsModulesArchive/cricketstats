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
include '../../../include/cp_header.php'; //Include file, which checks for permissions and sets navigation

if (isset($_POST['season_select'])) {
    $cricket_season = explode("____",$_POST['season_select']);
}
elseif (isset($_POST['seasonid'])) {
    $cricket_season = array (intval($_POST['seasonid']), $_POST['seasonname']);
}
elseif (!isset($_SESSION['season_id'])) {
    $sql = "SELECT SeasonID, SeasonName FROM ".$xoopsDB->prefix("cricket_seasonnames")." WHERE SeasonDefault=1";
    $cricket_seasonname = $xoopsDB->query($sql);
    $cricket_seasonname = $xoopsDB->fetchArray($cricket_seasonname);
    $cricket_season = array($cricket_seasonname['SeasonID'], $cricket_seasonname['SeasonName']);
}
else {
    $cricket_season = array(intval($_SESSION['season_id']), $_SESSION['season_name']);
}

if (isset($_POST['league_select'])) {
    $cricket_league = explode("____",$_POST['league_select']);
}
elseif (isset($_POST['leagueid'])) {
    $cricket_league = array (intval($_POST['leagueid']), $_POST['leaguename']);
}
elseif (!isset($_SESSION['league_id'])) {
    $sql2 = "SELECT LeagueID, LeagueName FROM ".$xoopsDB->prefix("cricket_leaguenames")." WHERE LeagueDefault=1";
    $cricket_leaguename = $xoopsDB->query($sql2);
    $cricket_leaguename = $xoopsDB->fetchArray($cricket_leaguename);
    $cricket_league = array($cricket_leaguename['LeagueID'], $cricket_leaguename['LeagueName']);
}
else {
    $cricket_league = array(intval($_SESSION['league_id']), $_SESSION['league_name']);
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

$cricket_d_points_add = isset($_POST['d_points_add']) ? $_POST['d_points_add'] : null;
$cricket_d_points_modify = isset($_POST['d_points_modify']) ? $_POST['d_points_modify'] : null;

xoops_cp_header();
$indexAdmin = new ModuleAdmin();
echo $indexAdmin->addNavigation('opponents.php');

//Add
if($cricket_add_submit)
{
    $opponent = trim($_POST['opponent']);
    $opponent = $xoopsDB->quoteString($opponent);
    $opponentleagueid = trim($_POST['leagueid']);
    $opponentleagueid = $xoopsDB->quoteString($cricket_leagueid);
    $opponentseasonid = trim($_POST['seasonid']);
    $opponentseasonid = $xoopsDB->quoteString($cricket_seasonid);

//query to check if there are already a team with submitted name
    $query = $xoopsDB->query("SELECT OpponentName FROM ".$xoopsDB->prefix("cricket_opponents")." WHERE OpponentName = $opponent AND OpponentLeagueID = $cricket_leagueid AND OpponentSeasonID = $cricket_seasonid");
    
    if($xoopsDB->getRowsNum($query) > 0)
    {
        echo "<font color='red'><b>". _AM_CRICK_TEAMDUPLICATE."</b></font><br><br>";
        exit();
    }
    
    if($opponent != '')
    {
        $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("cricket_opponents")." SET OpponentName = $opponent, OpponentLeagueID = $cricket_leagueid, OpponentSeasonID = $cricket_seasonid");
        
        header("Location: $PHP_SELF");
    }
}

//Modify
elseif($cricket_modify_submit)
{
    $opponent = $xoopsDB->quoteString(trim($_POST['opponent']));
    $opponentid = intval($_POST['opponentid']);
	$opponentleagueid = intval($_POST['leagueid']);
	$opponentseasonid = intval($_POST['seasonid']);
    $own = $_POST['own'];

    //Checked own
    if(!isset($own))
    {
        $own = 0;
    }
    
    if($opponent != '')
    {

        //If own team->delete the own status from the previous one
        if($own == 1)
        {
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix("cricket_opponents")." SET
				OpponentOwn = '0'
				WHERE OpponentOwn = '1' AND OpponentLeagueID = $cricket_leagueid AND OpponentSeasonID = $cricket_seasonid
				");
        }
        
        $xoopsDB->query("UPDATE ".$xoopsDB->prefix("cricket_opponents")." SET
			OpponentName = $opponent,
			OpponentOwn = '$own'
			WHERE OpponentID = $opponentid AND OpponentLeagueID = $cricket_leagueid AND OpponentSeasonID = $cricket_seasonid
			");
    }
    
    header("Location: $HTTP_REFERER");
}

//Delete
elseif($cricket_delete_submit)
{
    $opponentid = intval($_POST['opponentid']);
    $opponentleagueid = intval($_POST['opponentleagueid']);
    $opponentseasonid = intval($_POST['opponentseasonid']);

    //query to check, if team already exists in the leaguetables
    $query = $xoopsDB->query("SELECT LeagueMatchID
		FROM ".$xoopsDB->prefix("cricket_leaguematches")."
		WHERE LeagueMatchHomeID = $opponentid OR LeagueMatchAwayID = $opponentid AND OpponentLeagueID = $cricket_leagueid AND OpponentSeasonID = $cricket_seasonid");
    
    if($xoopsDB->getRowsNum($query) == 0)
    {
        $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("cricket_opponents")." WHERE OpponentID = $opponentid AND OpponentLeagueID = $cricket_leagueid AND OpponentSeasonID = $cricket_seasonid");
        
        header("Location: $PHP_SELF");
    }
    else
    {
        echo "<font color='red'><b>". _AM_CRICK_TEAMISINUSE."</b></font><br><br>";
        exit();
    }
}

//Deducted points
elseif($cricket_d_points_add)
{
    $cricket_d_points = intval($_POST['d_points']);
    $teamid = intval($_POST['teamid']);
    $cricket_seasonid = intval($_POST['seasonid']);
    $cricket_leagueid = intval($_POST['leagueid']);
    
    if(is_numeric($cricket_d_points) && $cricket_d_points != '')
    {
        //Adds
        $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("cricket_deductedpoints")." SET
			seasonid = $cricket_seasonid,
			leagueid = $cricket_leagueid,
			teamid = $teamid,
			points = $cricket_d_points");
    }
    
    header("Location: $HTTP_REFERER");
}

//Modify of deducted points
elseif($cricket_d_points_modify)
{
    $cricket_d_points = intval($_POST['d_points']);
    $id = intval($_POST['id']);
    
    if(is_numeric($cricket_d_points) && $cricket_d_points != '')
    {
        //Delete deducted points if zero is written
        if($cricket_d_points == 0)
        {
            $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("cricket_deductedpoints")."
				WHERE id = $id");
        }
        //Modify if some other number
        else
        {
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix("cricket_deductedpoints")." SET
				points = $cricket_d_points
				WHERE id = $id");
        }
    }
    
    header("Location: $HTTP_REFERER");
}
?>
	
	<?php
	include('head.php');
	include('leaguehead.php');
	?>
	<table align="center" width="100%" border="3">
		<tr>
		<td align="left" valign="top">
		<?php
		if(!isset($cricket_action))
		{
		?>
		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_CRICK_ADDNEWTEAM;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_CRICK_TEAMNAME;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="opponent">
			</td>
		</tr>
		</table>
		<input type="submit" name="add_submit" value="<?php echo _AM_CRICK_ADDTEAM;?>">
		</form>
		<?php
		}
		elseif($cricket_action == 'modify')
		{
		    $opponentid = intval($_REQUEST['opponent']);
		    $cricket_get_opponent = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_opponents")." WHERE OpponentID = $opponentid AND OpponentLeagueID = $cricket_leagueid AND OpponentSeasonID = $cricket_seasonid LIMIT 1");
		    $cricket_data = $xoopsDB->fetchArray($cricket_get_opponent);
		?>

		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_CRICK_TEAMMODIFYDELETE;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_CRICK_TEAMNAME;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="opponent" value="<?php echo $cricket_data['OpponentName'] ?>">
			<input type="hidden" name="opponentid" value="<?php echo $cricket_data['OpponentID'] ?>">
			<input type="hidden" name="opponentseasonid" value="<?php echo $cricket_data['OpponentSeasonID'] ?>">
			<input type="hidden" name="opponentleagueid" value="<?php echo $cricket_data['OpponentLeagueID'] ?>">
			</td>
		</tr>

		<tr>
			<td align="left" valign="top">
			<?php echo _AM_CRICK_TEAMISYOURS;?>
			</td>
			<td align="left" valign="top">
			<?php
			
			if($cricket_data['OpponentOwn'] == 1)
			echo"<input type=\"checkbox\" name=\"own\" value=\"1\" CHECKED>\n";
			else
			echo"<input type=\"checkbox\" name=\"own\" value=\"1\">\n";
			
			?>
			</td>
		</tr>
		</table>
		<input type="submit" name="modify_submit" value="<?php echo _AM_CRICK_TEAMMODIFY;?>"> <input type="submit" name="delete_submit" value="<?php echo _AM_CRICK_TEAMDELETE;?>">
		</form>

		<a href="<?php echo "$PHP_SELF" ?>"><?php echo _AM_CRICK_ADDNEWTEAM;?></a>

		<h3><?php echo _AM_CRICK_DEDPTS;?></h3>

		<?php
		
		//Check if there are deducted points
		
		echo"<b>$cricket_seasonname</b><br>";
		echo"<b>$cricket_leaguename</b><br><br>";
		
		$cricket_get_deduct = $xoopsDB->query("SELECT points, id
		FROM ".$xoopsDB->prefix("cricket_deductedpoints")."
		WHERE seasonid = $cricket_seasonid AND leagueid = $cricket_leagueid AND teamid = $opponentid
		LIMIT 1
		");
		
		if($xoopsDB->getRowsNum($cricket_get_deduct) == 0)
		{
		    echo"
			<form method=\"POST\" action=\"$PHP_SELF\">"
		    ._AM_CRICK_ADDDEDPTS.
		    "<input type=\"text\" size=\"2\" name=\"d_points\">
			<input type=\"hidden\" value=\"$opponentid\" name=\"teamid\">
		    <input type=\"hidden\" value=\"$cricket_seasonid\" name=\"seasonid\">
		    <input type=\"hidden\" value=\"$cricket_leagueid\" name=\"leagueid\">
			<input type=\"submit\" value="._AM_CRICK_ADEDPTS." name=\"d_points_add\">
			</form>
			";
		}
		else
		{
		    $cricket_data = $xoopsDB->fetchArray($cricket_get_deduct);
		    
		    echo"
			<form method=\"POST\" action=\"$PHP_SELF\">"
		    ._AM_CRICK_MODDEDPTS.
		    "<input type=\"text\" size=\"2\" name=\"d_points\" value=\"$cricket_data[points]\">
			<input type=\"hidden\" value=\"$cricket_data[id]\" name=\"id\">
			<input type=\"submit\" value="._AM_CRICK_MDEDPTS." name=\"d_points_modify\">
			</form>
			";
		}
		
		mysql_free_result($cricket_get_deduct);
		?>

		<?php
		mysql_free_result($cricket_get_opponent);
		}
		?>
		</td>

		<td align="left" valign="top">
		<?php
		$cricket_get_opponents = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_opponents")." WHERE OpponentLeagueID = $cricket_leagueid AND OpponentSeasonID = $cricket_seasonid ORDER BY OpponentName");
		
		if($xoopsDB->getRowsNum($cricket_get_opponents) < 1)
		{
		    echo "<b>". _AM_CRICK_NOTEAMSAVAILABLE."</b><br><br>";
		}
		else
		{
		    echo "<b>". _AM_CRICK_TEAMSAVAILABLE."</b><br><br>";
		    
		    while($cricket_data = $xoopsDB->fetchArray($cricket_get_opponents))
		    {
		        echo "<a href=\"$PHP_SELF?action=modify&amp;opponent=$cricket_data[OpponentID]\">$cricket_data[OpponentName]</a>";
		        
		        if($cricket_data['OpponentOwn'] == 1)
		        echo "&nbsp;"._AM_CRICK_YT. "<br>\n";
		        else
		        echo"<br>\n";
		    }
		}
		?>
		<br><br>

		<?php echo _AM_CRICK_YOURTEAM;?>
		</td>
		</tr>
	</table>
<?php
xoops_cp_footer();
?>