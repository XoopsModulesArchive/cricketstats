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

$cricket_leagueid = isset($_GET['league_id']) ? intval($_GET['league_id']) : 0;
$cricket_leaguename = isset($_GET['league_name']) ? $_GET['league_name'] : "";

$PHP_SELF = $_SERVER['PHP_SELF'];
$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
$cricket_action = isset($_GET['action']) ? $_GET['action'] : null;
$cricket_action = isset($_POST['action']) ? $_POST['action'] : $cricket_action;

$cricket_add_submit = isset($_POST['add_submit']) ? $_POST['add_submit'] : false;
$cricket_modify_submit = isset($_POST['modify_submit']) ? $_POST['modify_submit'] : false;
$cricket_delete_submit = isset($_POST['delete_submit']) ? $_POST['delete_submit'] : false;

xoops_cp_header();

$indexAdmin = new ModuleAdmin();
echo $indexAdmin->addNavigation('leagues.php');
if($cricket_add_submit)
{
    $cricket_name = $xoopsDB->quoteString(trim($_POST['name']));
    $cricket_drawline = trim($_POST['drawline']);
    
    //Query to check if there are already a submitted league name in the database
    $query = $xoopsDB->query("SELECT LeagueName FROM ".$xoopsDB->prefix("cricket_leaguenames")." WHERE LeagueName = $cricket_name");
    
    if($xoopsDB->getRowsNum($query) > 0)
    {
        echo "<font color='red'><b>". _AM_CRICK_LEAGUEDUPLICATE."</b></font><br><br>";
        exit();
    }
    
    mysql_free_result($query);
    
    if($cricket_name != '')
    {
        $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("cricket_leaguenames")." SET
			LeagueName = $cricket_name,
			LeagueLine = '$cricket_drawline',
            LeagueDefault = '$defleague'");
        
        header("Location: $PHP_SELF");
    }
}
elseif($cricket_modify_submit)
{
    $cricket_name = $xoopsDB->quoteString(trim($_POST['name']));
    $cricket_drawline = trim($_POST['drawline']);
    $publish = $_POST['publish'];
    $cricket_leagueid = intval($_POST['leagueid']);
    $defleague = intval($_POST['defleague']);
    
    //
    //If published is checked
    //
    if(!isset($publish))
    {
        $publish = 0;
    }
    if(!isset($defleague))
    {
        $defleague = 0;
    }
    
    if($cricket_name != '')
    {
        //
        //If default league->delete the default status from the previous one
        //
        if($defleague == 1)
        {
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix("cricket_leaguenames")." SET
				LeagueDefault = '0'");
        }
        $xoopsDB->query("UPDATE ".$xoopsDB->prefix("cricket_leaguenames")." SET
			LeagueName = $cricket_name,
			LeagueLine = '$cricket_drawline',
			LeaguePublish = '$publish',
            LeagueDefault = '$defleague'
			WHERE LeagueID = '$cricket_leagueid'");
    }
    
    header("Location: $HTTP_REFERER");
}
elseif($cricket_delete_submit)
{
    $cricket_leagueid = intval($_POST['leagueid']);
    
    //
    //Query to check if there are already matches in the league->can't delete
    //
    $query = $xoopsDB->query("SELECT M.LeagueMatchID
		FROM ".$xoopsDB->prefix("cricket_leaguematches")." M, ".$xoopsDB->prefix("cricket_leaguenames")." L
		WHERE M.LeagueMatchLeagueID = '$cricket_leagueid'");
    
    if($xoopsDB->getRowsNum($query) == 0)
    {
        $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("cricket_leaguenames")." WHERE LeagueID = '$cricket_leagueid'");
    }
    else
    {
        echo "<font color='red'><b>". _AM_CRICK_LEAGUEHASMATCHES."</b></font><br><br>";
        exit();
    }
    
    header("Location: $PHP_SELF");
}


?>
	
	<?php
	include('leaguehead.php');
	?>
	<table align="center" width="600">
		<tr>
		<td>
		<?php
		if(!isset($cricket_action))
		{
		?>
		<form method="post" action="<?php echo "$PHP_SELF" ?>">
		<h3><?php echo _AM_CRICK_ADDLEAGUE;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_CRICK_LEAGUENAMEYEARS;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="name">
			</td>
		</tr>

		<tr>
			<td align="left" valign="top">
			<?php echo _AM_CRICK_LEAGUEDRAWLINE;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="drawline" value="" size="10">
			</td>
		</tr>
		</table>
		<input type="submit" name="add_submit" value="<?php echo _AM_CRICK_LEAGUEADD;?>">
		</form>
		<?php
		}
		elseif($cricket_action == 'modify')
		{
		    $cricket_leagueid = intval($_REQUEST['league']);
		    $cricket_get_league = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_leaguenames")." WHERE LeagueID = '$cricket_leagueid' LIMIT 1");
		    $cricket_data = $xoopsDB->fetchArray($cricket_get_league);
		?>

		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_CRICK_LEAGUEMODIFYDELETE;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_CRICK_LEAGUENAMEYEARS;?>
                        </td>
                        <td>
                        <input type="text" name="name" value="<?php echo $cricket_data['LeagueName'] ?>">
			<input type="hidden" name="leagueid" value="<?php echo $cricket_data['LeagueID'] ?>">
			</td>
	   </tr>
	   <tr>
		    <td align="left" valign="top">
			<?php echo _AM_CRICK_DEFAULTLEAGUE;?>
			</td>
			<td align="left" valign="top">
			<?php
			
			if($cricket_data['LeagueDefault'] == 1)
			echo"<input type=\"checkbox\" name=\"defleague\" value=\"1\" CHECKED>\n";
			else
			echo"<input type=\"checkbox\" name=\"defleague\" value=\"1\">\n";
			
			?>
			</td>
		</tr>

		<tr>
			<td align="left" valign="top">
			<?php echo _AM_CRICK_LEAGUEDRAWLINE;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="drawline" value="<?= $cricket_data['LeagueLine'] ?>" size="10">
			</td>
		</tr>

		<tr>
			<td align="left" valign="top">
			<?php echo _AM_CRICK_LEAGUEPUBLISHED;?>
			</td>
			<td align="left" valign="top">
			<?php
			//
			//If league is published
			//
			if($cricket_data['LeaguePublish'] == 1)
			echo'<input type="checkbox" name="publish" value="1" CHECKED>';
			else
			echo'<input type="checkbox" name="publish" value="1">';
			
			?>
			</td>
		</tr>

		</table>
		<input type="submit" name="modify_submit" value="<?php echo _AM_CRICK_LEAGUEMODIFY;?>"> <input type="submit" name="delete_submit" value="<?php echo _AM_CRICK_LEAGUEDELETE;?>">
		</form>

		<a href="<?php echo "$PHP_SELF"?>"><?php echo _AM_CRICK_ADDLEAGUE;?></a>

		<?php
		mysql_free_result($cricket_get_league );
		}
		?>
		</td>

		<td align="left" valign="top">
		<?php
		$cricket_get_leagues = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_leaguenames")." ORDER BY LeagueName");
		
		if($xoopsDB->getRowsNum($cricket_get_leagues) < 1)
		{
		    echo "<b>"._AM_CRICK_NOLEAGUES."</b>";
		}
		else
		{
		    echo "<b>". _AM_CRICK_LEAGUESAVAILABLE."</b><br><br>";
		    
		    while($cricket_data = $xoopsDB->fetchArray($cricket_get_leagues))
		    {
		        echo "<a href=\"$PHP_SELF?action=modify&amp;league=$cricket_data[LeagueID]\">$cricket_data[LeagueName]</a>";
		        
		        //
		        //League published?
		        //
		        if($cricket_data['LeaguePublish'] == 0)
		        echo "&nbsp;" ._AM_CRICK_LEAGUENP."<br>\n";
		        else
		        echo"<br>\n";
		    }
		}
		
		?>
		<br><br>
		<?php echo _AM_CRICK_LEAGUENOTE;?>
		</td>
		</tr>
	</table>
	

<?php
xoops_cp_footer();
?>