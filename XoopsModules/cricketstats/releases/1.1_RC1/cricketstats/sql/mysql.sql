CREATE TABLE cricket_deductedpoints (
	  id int(11) NOT NULL auto_increment,
	  seasonid int(10) unsigned NOT NULL default '0',
	  leagueid int(10) unsigned NOT NULL default '0',
	  teamid smallint(4) unsigned NOT NULL default '0',
	  points tinyint(3) NOT NULL default '0',
          PRIMARY KEY  (id),
	  KEY seasonid (seasonid),
	  KEY leagueid (leagueid),
	  KEY opponentid (teamid)
	) TYPE=MyISAM;
	
CREATE TABLE cricket_leaguematches (
	  LeagueMatchID int(10) unsigned NOT NULL auto_increment,
	  LeagueMatchSeasonID int(10) unsigned NOT NULL default '0',
	  LeagueMatchLeagueID int(10) unsigned NOT NULL default '0',
	  LeagueMatchDate date NOT NULL default '0000-00-00',
	  LeagueMatchHomeID smallint(4) unsigned NOT NULL default '0',
	  LeagueMatchAwayID smallint(4) unsigned NOT NULL default '0',
	  LeagueMatchHomeBonus smallint(3) NOT NULL default '0',
	  LeagueMatchAwayBonus smallint(3) NOT NULL default '0',
	  LeagueMatchHomeBpoints tinyint(2) default NULL,
	  LeagueMatchAwayBpoints tinyint(2) default NULL,
	  LeagueMatchHomeWinnerID smallint(4) NOT NULL default '0',
	  LeagueMatchHomeLoserID smallint(4) NOT NULL default '0',
	  LeagueMatchAwayWinnerID smallint(4) NOT NULL default '0',
	  LeagueMatchAwayLoserID smallint(4) NOT NULL default '0',
	  LeagueMatchHomeTieID smallint(4) NOT NULL default '0',
	  LeagueMatchAwayTieID smallint(4) NOT NULL default '0',
	  LeagueMatchHomeRuns tinyint(3) default NULL,
	  LeagueMatchHomeWickets tinyint(2) default NULL,
	  LeagueMatchAwayRuns tinyint(3) default NULL,
	  LeagueMatchAwayWickets tinyint(2) default NULL,
	  LeagueMatchCreated int(12) NOT NULL,
	  PRIMARY KEY  (LeagueMatchID),
	  KEY LeagueMatchSeasonID (LeagueMatchSeasonID),
	  KEY LeagueMatchLeagueID (LeagueMatchLeagueID),
	  KEY LeagueMatchHomeID (LeagueMatchHomeID),
	  KEY LeagueMatchAwayID (LeagueMatchAwayID),
	  KEY LeagueMatchHomeWinnerID (LeagueMatchHomeWinnerID),
	  KEY LeagueMatchHomeLoserID (LeagueMatchHomeLoserID),
	  KEY LeagueMatchAwayWinnerID (LeagueMatchAwayWinnerID),
	  KEY LeagueMatchAwayLoserID (LeagueMatchAwayLoserID),
	  KEY LeagueMatchHomeTieID (LeagueMatchHomeTieID),
	  KEY LeagueMatchAwayTieID (LeagueMatchAwayTieID)
	) TYPE=MyISAM;

CREATE TABLE cricket_opponents (
	  OpponentID smallint(4) unsigned NOT NULL auto_increment,
	  OpponentSeasonID int(10) unsigned NOT NULL default '0',
	  OpponentLeagueID int(10) unsigned NOT NULL default '0',
	  OpponentName varchar(128) NOT NULL default '',
	  OpponentOwn tinyint(1) unsigned NOT NULL default '0',
	  PRIMARY KEY  (OpponentID)
	) TYPE=MyISAM;
	
CREATE TABLE cricket_seasonnames (
	  SeasonID int(10) unsigned NOT NULL auto_increment,
	  SeasonName varchar(64) NOT NULL default '',
	  SeasonPublish tinyint(1) unsigned NOT NULL default '1',
	  SeasonLine varchar(32) NOT NULL default '1',
	  SeasonDefault tinyint(1) unsigned NOT NULL default '0',
	  PRIMARY KEY  (SeasonID)
	) TYPE=MyISAM;
	
INSERT INTO cricket_seasonnames (SeasonID, SeasonName, SeasonPublish, SeasonLine)
	VALUES ('1', '2005', '1', '1');

CREATE TABLE cricket_leaguenames (
	  LeagueID int(10) unsigned NOT NULL auto_increment,
	  LeagueName varchar(128) NOT NULL default '',
	  LeaguePublish tinyint(1) unsigned NOT NULL default '1',
	  LeagueLine varchar(32) NOT NULL default '1',
	  LeagueDefault tinyint(1) unsigned NOT NULL default '0',
	  PRIMARY KEY  (LeagueID)
	) TYPE=MyISAM;
	
INSERT INTO cricket_leaguenames (LeagueID, LeagueName, LeaguePublish, LeagueLine)
	VALUES ('1', 'Premier', '1', '1');