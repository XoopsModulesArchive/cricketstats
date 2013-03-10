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

$cricket_seasonid = isset($_GET['season_id']) ? intval($_GET['season_id']) : 0;
$cricket_seasonname = isset($_GET['season_name']) ? $_GET['season_name'] : "";

$PHP_SELF = $_SERVER['PHP_SELF'];
$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
$cricket_action = isset($_GET['action']) ? $_GET['action'] : null;
$cricket_action = isset($_POST['action']) ? $_POST['action'] : $cricket_action;

$cricket_add_submit = isset($_POST['add_submit']) ? $_POST['add_submit'] : false;
$cricket_modify_submit = isset($_POST['modify_submit']) ? $_POST['modify_submit'] : false;
$cricket_delete_submit = isset($_POST['delete_submit']) ? $_POST['delete_submit'] : false;

xoops_cp_header();

$indexAdmin = new ModuleAdmin();
echo $indexAdmin->addNavigation('seasons.php');
if($cricket_add_submit)
{
    $name = $xoopsDB->quoteString(trim($_POST['name']));
//    $drawline = trim($_POST['drawline']);
    
    //Query to check if there are already a submitted season name in the database
    $query = $xoopsDB->query("SELECT SeasonName FROM ".$xoopsDB->prefix("cricket_seasonnames")." WHERE SeasonName = $name");
    
    if($xoopsDB->getRowsNum($query) > 0)
    {
        echo "<font color='red'><b>". _AM_CRICK_SEASONDUPLICATE."</b></font><br><br>";
        exit();
    }
    
    mysql_free_result($query);
    
    if($name != '')
    {
        $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("cricket_seasonnames")." SET SeasonName = $name, SeasonDefault = '$defseason'");
        
        header("Location: $PHP_SELF");
    }
}
elseif($cricket_modify_submit)
{
    $name = $xoopsDB->quoteString(trim($_POST['name']));
//    $drawline = trim($_POST['drawline']);
    $publish = $_POST['publish'];
    $cricket_seasonid = intval($_POST['seasonid']);
    $defseason = intval($_POST['defseason']);
    
    //
    //If published is checked
    //
    if(!isset($publish))
    {
        $publish = 0;
    }
    if(!isset($defseason))
    {
        $defseason = 0;
    }
    
    if($name != '')
    {
        //
        //If default season->delete the default status from the previous one
        //
        if($defseason == 1)
        {
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix("cricket_seasonnames")." SET
				SeasonDefault = '0'");
        }
        $xoopsDB->query("UPDATE ".$xoopsDB->prefix("cricket_seasonnames")." SET
			SeasonName = $name,
			SeasonPublish = '$publish',
            SeasonDefault = '$defseason'
			WHERE SeasonID = '$cricket_seasonid'");
    }
    
    header("Location: $HTTP_REFERER");
}
elseif($cricket_delete_submit)
{
    $cricket_seasonid = intval($_POST['seasonid']);
    
    //
    //Query to check if there are already matches in the season->can't delete
    //
    $query = $xoopsDB->query("SELECT M.LeagueMatchID
		FROM ".$xoopsDB->prefix("cricket_leaguematches")." M, ".$xoopsDB->prefix("cricket_seasonnames")." S
		WHERE M.LeagueMatchSeasonID = '$cricket_seasonid'");
    
    if($xoopsDB->getRowsNum($query) == 0)
    {
        $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("cricket_seasonnames")." WHERE SeasonID = '$cricket_seasonid'");
    }
    else
    {
        echo "<font color='red'><b>". _AM_CRICK_SEASONHASMATCHES."</b></font><br><br>";
        exit();
    }
    
    header("Location: $PHP_SELF");
}


?>
	
	<?php
	include('head.php');
	?>
	<table align="center" width="600">
		<tr>
		<td>
		<?php
		if(!isset($cricket_action))
		{
		?>
		<form method="post" action="<?php echo "$PHP_SELF" ?>">
		<h3><?php echo _AM_CRICK_ADDSEASON;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_CRICK_SEASONNAMEYEARS;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="name">
			</td>
		</tr>
		</table>
		<input type="submit" name="add_submit" value="<?php echo _AM_CRICK_SEASONADD;?>">
		</form>
		<?php
		}
		elseif($cricket_action == 'modify')
		{
		    $cricket_seasonid = intval($_REQUEST['season']);
		    $cricket_get_season = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_seasonnames")." WHERE SeasonID = '$cricket_seasonid' LIMIT 1");
		    $cricket_data = $xoopsDB->fetchArray($cricket_get_season);
		?>

		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_CRICK_SEASONMODIFYDELETE;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_CRICK_SEASONNAMEYEARS;?>
                        </td>
                        <td>
                        <input type="text" name="name" value="<?php echo $cricket_data['SeasonName'] ?>">
			<input type="hidden" name="seasonid" value="<?php echo $cricket_data['SeasonID'] ?>">
			</td>
	   </tr>
	   <tr>
		    <td align="left" valign="top">
			<?php echo _AM_CRICK_DEFAULTSEASON;?>
			</td>
			<td align="left" valign="top">
			<?php
			
			if($cricket_data['SeasonDefault'] == 1)
			echo"<input type=\"checkbox\" name=\"defseason\" value=\"1\" CHECKED>\n";
			else
			echo"<input type=\"checkbox\" name=\"defseason\" value=\"1\">\n";
			
			?>
			</td>
		</tr>

		<tr>
			<td align="left" valign="top">
			<?php echo _AM_CRICK_SEASONPUBLISHED;?>
			</td>
			<td align="left" valign="top">
			<?php
			//
			//If season is published
			//
			if($cricket_data['SeasonPublish'] == 1)
			echo'<input type="checkbox" name="publish" value="1" CHECKED>';
			else
			echo'<input type="checkbox" name="publish" value="1">';
			
			?>
			</td>
		</tr>

		</table>
		<input type="submit" name="modify_submit" value="<?php echo _AM_CRICK_SEASONMODIFY;?>"> <input type="submit" name="delete_submit" value="<?php echo _AM_CRICK_SEASONDELETE;?>">
		</form>

		<a href="<?php echo "$PHP_SELF"?>"><?php echo _AM_CRICK_ADDSEASON;?></a>

		<?php
		mysql_free_result($cricket_get_season );
		}
		?>
		</td>

		<td align="left" valign="top">
		<?php
		$cricket_get_seasons = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_seasonnames")." ORDER BY SeasonName");
		
		if($xoopsDB->getRowsNum($cricket_get_seasons) < 1)
		{
		    echo "<b>"._AM_CRICK_NOSEASONS."</b>";
		}
		else
		{
		    echo "<b>". _AM_CRICK_SEASONSAVAILABLE."</b><br><br>";
		    
		    while($cricket_data = $xoopsDB->fetchArray($cricket_get_seasons))
		    {
		        echo "<a href=\"$PHP_SELF?action=modify&amp;season=$cricket_data[SeasonID]\">$cricket_data[SeasonName]</a>";
		        
		        //
		        //Season published?
		        //
		        if($cricket_data['SeasonPublish'] == 0)
		        echo "&nbsp;" ._AM_CRICK_SEASONNP."<br>\n";
		        else
		        echo"<br>\n";
		    }
		}
		
		?>
		<br><br>
		<?php echo _AM_CRICK_SEASONNOTE;?>
		</td>
		</tr>
	</table>
	

<?php
xoops_cp_footer();
?>