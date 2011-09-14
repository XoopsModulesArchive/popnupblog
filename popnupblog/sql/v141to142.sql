-- moblog email alias table added by hoshiyan@hoshiba-farm.com 2004.8.3
-- uid :
--    xoops uid
-- public:
--    0: closed it for other member 
--    1:
-- email:
--    another email address for moblog posting. Work under pop.php.
CREATE TABLE xoops_popnupblog_emailalias (
	uid int(5) unsigned NOT NULL default '0',
	public tinyint(1) NOT NULL default '1',
	email varchar(60) ,
	PRIMARY KEY (uid)
) ENGINE=MyISAM;
