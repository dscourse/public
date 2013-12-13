Introduction
======

Dscourse is an online discussion tool developed at the Curry School of Education. The goal of this online discussion tool is to allow educators to create better discussion environments with tools that help students to have meaningful exchange of ideas. Dscourse features are described in depth in the landing page that can be found here: http://dscourse.org/info.php 
 
 
##File Structure

For developers who would like to contribute to dscourse here is some information that will help you find documents you are looking for:

###Folders


**admin**: Very basic administrative layer that allows dscourse site administrators to complete CRUD operations. 

**assets**: Folder for add ons and other scripts that may be put on the main dscourse fuctionality

**css**: Main css files, dscourse specific css is under *style.css*.

**fonts**: Dscourse relies on typicon and entypo web fonts which are stored here. 

**img**: Image files 

**js**: All javascript code including libraries and the main dscourse js code in dscourse.js file.   

**landing**: Files used in the info.php landing page. This theme is purchased for a single use license; DO NOT use these files unless you bought the theme (http://themeforest.net/item/appster-video-app-software-landing-page/4623611). 

**mail**: Files required for the mailing functionalities in the discussion. 

**php**: All relevant php scripts

**uploads**: Folder for uploads that include user and course images. 

###Main files

**/php/data.php**: Php scripts for ajax calls

**php/dscourse.class.php**: Main dscourse php functions in a class. 

**/js/dscourse.js**: The most important file in the system, includes majority of the dscourse javascript code. 


###Libraries and other Code
Dscourse uses the following:

**Jquery**: http://jquery.com/

**Jquery UI**: http://jqueryui.com/ 

**animate.cs**: https://daneden.me/animate/

###Security
Dscourse has its own login script. The scripts is generally safe but check the code for highly sensitive projects.  The login script escapes characters and uses md5 for hashing. Database connections are done with [PDO](http://php.net/pdo). 


###Database Structure
The SQL file that builds the database structure is in the file dscourse.sql. Run sql code in phpmyadmin to build the structure for a new project. 
Database configuration is done in the /php/config.php file. Use the config_sample.php to add your information and rename the file to config.php


##Contact

For more information, please contact:

Bill Ferster
bferster - at - virginia.edu

A Google Doc of the current working specification can be found here.

© 2012 The University of Virginia