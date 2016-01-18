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
//if(!session_is_registered('league_name') || !session_is_registered('league_id'))
if ( !isset( $_SESSION['league_name'] ) || !isset( $_SESSION['league_id'] ) )
{
	echo "<form method=\"post\" action=\"leaguematches.php\">";
	echo '<b><?php echo _AM_CRICK_CHOLEAGUE;?></b>';
	echo '<select name="league_select">';
	$cricket_get_leagues = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_leaguenames")." ORDER BY LeagueName");

	while($cricket_sdata = $xoopsDB->fetchArray($cricket_get_leagues))
	{
		echo "<option value=\"$cricket_sdata[LeagueID]____$cricket_sdata[LeagueName]\">$cricket_sdata[LeagueName]</option>\n";
	}
	echo "</select> <input type=\"submit\" name=\"submit1\" value=" ._AM_CRICK_LEAGUEGO. "></form>";


	mysql_free_result($cricket_get_leagues);
}
else
{
	$cricket_league_name = $_SESSION['league_name'];
	echo "<form method=\"post\" action=\"leaguematches.php\">";
	echo "<b> "._AM_CRICK_LEAGUESELECT."  $cricket_league_name</b><br><br>";
	echo _AM_CRICK_LEAGUESELDROP;
	echo '<select name="league_select">';

	$cricket_get_leagues = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("cricket_leaguenames")." ORDER BY LeagueName");

	while($cricket_sdata = $xoopsDB->fetchArray($cricket_get_leagues))
	{
		if($cricket_sdata['LeagueID'] == $cricket_leagueid)
			echo "<option value=\"$cricket_sdata[LeagueID]____$cricket_sdata[LeagueName]\" SELECTED>$cricket_sdata[LeagueName]</option>\n";
		else
			echo "<option value=\"$cricket_sdata[LeagueID]____$cricket_sdata[LeagueName]\">$cricket_sdata[LeagueName]</option>\n";
	}
	echo "</select> <input type=\"submit\" name=\"submit1\" value=" ._AM_CRICK_LEAGUEGO. "></form>";

	mysql_free_result($cricket_get_leagues);
}
?>

<hr width="100%">

</center>