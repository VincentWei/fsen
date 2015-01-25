## Installation Instructions for FSEN

1.	The the source of FSEN from GitHub or download the source code tarball and extract the tarball in a working directory.
1.	Make sure that you have installed [Concrete5](http://www.concrete5.org) V5.6.3.2 correctly. Note that the account accessing your MySQL database instance should have *CREATE VIEW* privilege. This is important because the default installation of Concreate5 does not have this set on.
1.	Copy files in concrete/ directory to your Concrete5 installtion and replace the old ones. We have some minor fixes for the original Concrete5 release (See section below) and update the jQuery and other dependencies to be the latest version.
1.	Copy files in blocks/, controllers/, single_pages/, helpers/, models, tools/, themes/, elements/, jobs/, js/, libraries/, and page_types/ to the corresponding directories of your Concrete5 installation.
1.	Run install.php in tools/ directory for your browser to intall FSEN blocks, themes, singl pages, and other things:

	http://<url-to-your-site>/index.php/tools/install.php

1.	Create database triggers in your database instance. Because some limitations of non-super-user to drop/create triggers on a MySQL instance, we do not include the triggers creation in the installation script. You need to install the triggers mannly. The source code to create the triggers included in the db.sql file in the top directory of the source tree of FSEN.

## Some fixes and/or enhancements for Concrete5 V5.6.3.2

1.	Upgrade jQuery, some plugins of jQuery, and jQuery UI to be the latest version.
1.	Enhance to have full-page cache support when you have a theme for mobile device.
1.	Use md5 hash value as the file name of the full page file.
1.	Fix for non-root single pages can have template file in a specific theme.

## Important Notes

1. Currently, we have not written FSEN as a package of Concrete5. We will do this in the future. Therefore, you need to install FSEN on a clean Concrete5 installation, in order to avoid some confilicts.
1. After you have installed FSEN, please remove install.php from your system or just add a line as below in the top of the file.

	exit (0);

