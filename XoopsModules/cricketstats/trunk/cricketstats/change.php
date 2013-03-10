<?php

include ('../../mainfile.php');

$HTTP_REFERER = $_SERVER['HTTP_REFERER'];

$submit = $_POST['submit'];
$submit1 = $_POST['submit1'];
$submit2 = $_POST['submit2'];
$submit3 = $_POST['submit3'];
$submit4 = $_POST['submit4'];
$submit5 = $_POST['submit5'];
$submit6 = $_POST['submit6'];

if($submit)
{
	$cricket_season = intval($_POST['season']);

	//New value for session variable
	$_SESSION['defaultseasonid'] = $cricket_season;

	header("Location: $HTTP_REFERER");
}
elseif($submit1)
{
	$cricket_league = intval($_POST['league']);

	//New value for session variable
	$_SESSION['defaultleagueid'] = $cricket_league;

	header("Location: $HTTP_REFERER");
}
elseif($submit2)
{
	$cricket_change = intval($_POST['change_show']);

	//New value for session variable
	$_SESSION['defaultshow'] = $cricket_change;

	header("Location: index.php?sort=pts");
}
elseif($submit3)
{
	$cricket_change = intval($_POST['change_table']);

	//New value for session variable
	$_SESSION['defaulttable'] = $cricket_change;

	header("Location: $HTTP_REFERER");
}
elseif($submit4)
{
	$cricket_change = intval($_POST['home_id']);

	//New value for session variable
	$_SESSION['defaulthomeid'] = $cricket_change;

	header("Location: $HTTP_REFERER");
}
elseif($submit5)
{
	$cricket_change = intval($_POST['away_id']);

	//New value for session variable
	$_SESSION['defaultawayid'] = $cricket_change;

	header("Location: $HTTP_REFERER");
}
elseif($submit6)
{
	$cricket_moveto = $_POST['moveto'];

	header("Location: $cricket_moveto");
}
else
{
header("Location: index.php?sort=pts");
}
exit();
?>