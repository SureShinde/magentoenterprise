CLS_Core 1.3.8 (7/14/2015)
=======================================
[REMOVED] CE-specific core hacks file (_isSecure method now implemented in CE 1.9.2.0)

CLS_Core 1.3.7 (6/19/2015)
=======================================
[CHANGED] Moved doc files to root directory
[REMOVED] js directory from robots.txt disallow list
[ADDED] CE-specific core hacks file (Inserted _isSecure method in block abstract to match latest EE codebase)

CLS_Core 1.3.6 (5/19/2015)
=======================================
[REMOVED] Core hack for index.php (due to error reporting settings being removed from core)

CLS_Core 1.3.5 (4/23/2015)
=======================================
[CHANGED] Nucleus rename
[REMOVED] Unused abstract classes

CLS_Core 1.3.4 (12/19/2014)
=======================================
[FIXED] Removed redundant gitignore rules

CLS_Core 1.3.3 (7/7/2014)
=======================================
[REMOVED] README.md

CLS_Core 1.3.2 (6/5/2014)
=======================================
[CHANGED] Moved allow_symlink to a config-defined default instead of being set with an upgrade script
[REMOVED] Removed "non-www" redirect from htaccess patch

CLS_Core 1.3.1 (5/13/2014)
=======================================
[CHANGED] Consolidated core hacks files

CLS_Core 1.3.0 (5/12/2014)
=======================================
[CHANGED] Base compatibility with CE 1.9/EE 1.14

CLS_Core 1.2.1 (5/7/2014)
=======================================
[ADDED] Data upgrade script to enable symlinks for templates

CLS_Core 1.2.0 (4/30/2014)
=======================================
[ADDED] Added license info to docblocks
[CHANGED] Minor documentation updates
[CHANGED] Moved events to admin-specific
[CHANGED] Moved error test controller that lived in CLS_Custom to CLS_Core instead
[REMOVED] CLS_Custom
[REMOVED] Removed unnecessary core hack of App model
[REMOVED] Removed CLS-specific conditions for DeveloperMode in index.php
[REMOVED] Removed section from system.xml (info inserted in CLS_NucleusCore instead)

CLS_Core 1.1.8 (4/4/2014)
=======================================
[CHANGED] Removed edit comments
[CHANGED] Changed CLS_Custom to version 1.0.0

CLS_Core 1.1.7 (3/29/2014)
=======================================
[CHANGED] Split core hacks into Enterprise and Community versions

CLS_Core 1.1.6 (3/9/2014)
=======================================
[ADDED] Added standard rewrite rules to htaccess in core hacks
[FIXED] Added translate file definition to config

CLS_Core 1.1.5 (3/5/2014)
=======================================
[CHANGED] Tweaks to gitignore to allow media and var files

CLS_Core 1.1.4 (3/3/2014)
=======================================
[CHANGED] Tweaks to gitignore

CLS_Core 1.1.3 (2/25/2014)
=======================================
[CHANGED] Moved install resource files and added Composer post-install scripts

CLS_Core 1.1.2 (2/20/2014)
=======================================
[CHANGED] Removed compatibility information

CLS_Core 1.1.1 (2/19/2014)
=======================================
[ADDED] Added resources/config, including .gitignore template

CLS_Core 1.1.0 (2/18/2014)
=======================================
[ADDED] Added composer.json file for automated building of Nucleus

CLS_Core 1.0.3 (2/18/2014)
=======================================
[FIXED] Moved core-hacks.diff to proper location

CLS_Core 1.0.2 (2/17/2014)
=======================================
[ADDED] Added CLS_Custom module and core hacks patch

CLS_Core 1.0.1 (2/5/2014)
=======================================
[CHANGED] Moved documentation files for compatibility with packager script

CLS_Core 1.0.0 (1/6/2014)
=======================================
Initial release
