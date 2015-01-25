## Installation Instructions for FSEN

	1.	Make sure that you have installed [Concrete5](http://www.concrete5.org) V5.6.3.2 correctly. Make sure that the account accessing your MySQL database should have *CREATE VIEW* prevelidge. This is important because the default installation of Concreate5 does not have this set on.
	1.	Copy files in concrete/ directory to your Concrete5 installtion and replace the old ones. We have some minor fixes for the original Concrete5 release (See section below) and update the jQuery and other dependencies to be the latest version.
	1.	Run install.php in tools/ directory for your browser to intall FSEN blocks, themes, singl pages, and other things:

	http://<url-to-your-site>/index.php/tools/install.php

	1.	Create database triggers in your database instance. Because some limitations of non-super-user to drop/create triggers on a MySQL instance, we do not include the triggers creation in the installation script. You need to install the triggers mannly. The source code to create the triggers included in the db.sql file in the top directory of the source tree of this project.

## Some fixes and/or enhancements for Concrete5 V5.6.3.2

	1.	Upgrade jQuery, some plugins of jQuery, and jQuery UI to be the latest version.
	1.	Enhance to have full-page cache support when you have a theme for mobile device.
	1.	Use md5 hash value as the file name of the full page file.
	1.	Fix for non-root single pages can have template file in a specific theme.
