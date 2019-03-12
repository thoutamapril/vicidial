+------------------------------------------------------------------------------+
|    Asterisk GUI client - astguiclient - twenty-sixth public release 2.0.1    |
|           created by astGUIclient group <astguiclient@eflo.net>              |
|  project started 2003-10-06   http://sourceforge.net/projects/astguiclient/  |
+------------------------------------------------------------------------------+

This suite of programs is designed to work with the Asterisk Open-source PBX
(http://www.asterisk.org) as a cross-platform GUI client and the supporting 
server side applications necessary for the features of the GUI application to 
work with Asterisk. The client-side GUI apps are available now as web pages 
running AJAX scripted web pages(Firefox recommended)

 ***** Description, Notes and Changelog below *****

Included in this distribution of the Asterisk GUI client are:

 - README.txt - this file

 - SCRATCH_INSTALL.txt - detailed instructions of how to fully install Asterisk 
with astguiclient from scratch. Also you could use this doc to install from 
PHASE 6 of the document if you have an existing Asterisk installation you want 
to put astguiclient onto.

 - ACQS.txt - Readme file describing the Asterisk Central Queue System

 - VICIDIAL.txt - Readme file describing the VICIDIAL auto-dialing system

 - REQUIREMENTS.txt - Basic requirements for running astGUIclient/VICIDIAL

 - LOAD_BALANCING.txt - Instructions for multi-server installations

 - LICENSE.txt - The license for this release GPLv2

 - install.pl - perl script to put files in the right place on the 
server. Run as "perl install.pl"

 - start_asterisk_boot.pl - starts asterisk through a screen, good for system 
boot time startups of asterisk.

- MySQL_AST_CREATE_tables.sql - used to create tables for astguiclient and 
vicidial in your MySQL database

 - astLEADLOADER.pl - client perl/Tk GUI for loading leads into the vicidial_list
   + same application run specs as the AST_WINphoneAPP above
   ### SOON TO BE DEPRICATED AND UNSUPPORTED ### see the web lead loader

 - ADMIN_area_code_populate.pl - run once to update phone codes to database

 - AST_VICI_conf.pl - file where you define your variables for the client app
   + Must be present in one of the libs_path folders

 - AST_update.pl - command line DB updater
   + Ideally is run on the Asterisk server locally
   + Requires Net::Telnet, Net::MySQL and Time::HiRes perl Modules

 - AST_vm_update.pl - command line DB voicemail count updater
   + Ideally is run on the Asterisk server locally
   + Requires Net::Telnet, Net::MySQL and Time::HiRes perl Modules
   + place in the cron as "* * * * * /path/to/script"

- AST_conf_update.pl - command line DB conference room reg validator
   + Ideally is run on the Asterisk server locally
   + Requires Net::Telnet, Net::MySQL and Time::HiRes perl Modules
   + place in the cron as "* * * * * /path/to/script"

 - ADMIN_keepalive_AST_update.pl - checks to see that updater is running
   + Must put entry for this script in the cron as "* * * * * /path/to/script"

 - AST_CRON_mix_recordings.pl - command line recording mixer to be put in cron
   + Runs under UNIX CLI in the cron of the Asterisk Server
   + Requires perl (Net::FTP and Net::Ping optional if FTPing to archive server)

 - AST_manager_listen.pl - listener for the Asterisk Central Queue System (ACQS)
   + Must be present in the astguiclient directory
   + Requires Net::Telnet, Net::MySQL and Time::HiRes perl Modules

 - AST_manager_send.pl - send-spawn for the ACQS
   + Must be present in the astguiclient directory
   + Requires Net::Telnet, Net::MySQL and Time::HiRes perl Modules

 - AST_send_action_child.pl - blind-send for the ACQS
   + Must be present in the astguiclient directory
   + Requires Net::Telnet, Net::MySQL perl Modules

 - ADMIN_keepalive_AST_send_listen.pl - checks to see that ACQS is running
   + Must put entry for this script in the cron as "* * * * * /path/to/script"

 - AST_manager_kill_hung_congested.pl - kills CONGEST Local/ channels
   + To be used with VICIDIAL
   + Must put entry for this script in the cron as "* * * * * /path/to/script"

 - ADMIN_listener_restart.pl - automatically restart ACQS 
   + Assumes installation in astguiclient

 - ADMIN_restart_roll_logs.pl - rolls logs over datestamp upon restart
   + put this script in your machine's startup routine

 - AST_reset_mysql_vars.pl - resets MySQL tables 
   + To be used with VICIDIAL
   + put this script in your machine's startup routine

 - AST_flush_DBqueue.pl - streamlines DB on heavy volume VICIDIAL systems
   + To be used with VICIDIAL
   + put this script in the cron at a regular interval(once an hour or so)

 - AST_cleanup_agent_log.pl - cleans up agent log times in VICIDIAL systems
   + To be used with VICIDIAL
   + put this script in the cron at a regular interval(once an hour or so)

- The following AGI scripts:
   + Run on the Asterisk Server
   + Must be present in the /var/lib/asterisk/agi-bin/ directory
   + Require perl and Net::MySQL to run
 - call_log.agi - AGI/perl program for call logging
 - call_logCID.agi - AGI/perl program for call logging receiving CallerID
 - call_inbound.agi - AGI/perl program for CallerID popup info (OPTIONAL)
 - agi-dtmf.agi - AGI/perl program that plays DTMF tines for conferences
    + Requires DTMF sounds to be copied to /var/lib/asterisk/sounds/
 - agi-VDADtransfer.agi - AGI/perl program that transfers calls to VICIDIAL reps
 - agi-VDAD_LO_transfer.agi - transfers calls to VICIDIAL reps Load Balance Overflow
 - agi-VDAD_LB_transfer.agi - transfers calls to VICIDIAL reps Load Balanced
 - agi-VDADcloser.agi - AGI/perl program that takes calls from VICIDIAL reps
   + Allows calls from internal or over Zap crossover or IAX from another server
 - agi-VDAD_LO_closer.agi - calls from VICIDIAL front to CLOSERS Balance Overflow
 - agi-VDAD_LB_closer.agi - calls from VICIDIAL front to CLOSERS Load Balanced
 - agi-VDADcloser_inboundANI.agi - AGI/perl that takes calls from outside lines
   + like agi-VDADcloser.agi except grabs ANI from robbed-bit T1 channel inbound
 - agi-VDADcloser_inboundCID.agi - AGI/perl that takes calls from outside lines
   + like agi-VDADcloser.agi except grabs CID from T1-PRI channel inbound
 - agi-VDADcloser_inboundCIDlookup.agi - AGI/perl that takes calls from outside lines
   + like agi-VDADcloser.agi except looks up CID in DB to display info
 - agi-VDADcloser_inbound.agi - AGI/perl that takes calls from outside lines
   + like agi-VDADcloser.agi except grabs 4-digit entered code as fronter #
 - agi-VDAD_LO_closer_inbound.agi - Load-balance-overflow version
 - agi-VDAD_LB_closer_inbound.agi - Load-balance version
 - agi-VDADcloser_PHONE.agi - AGI/perl that takes calls from outside lines
   + like agi-VDADcloser.agi except attempts to lookup the info from PHONE provided
 - park_CID.agi - AGI/perl program to place the callID in the park entry
 - call_park_I.agi and call_park_L.agi - AGI/perl script for handling inbound
   + Used in conjunction with VICIDIAL closers

 - AST_VDauto_dial.pl - script to automatically call leads for VICIDIAL
   + To be used with VICIDIAL
   + kept running with the associated keepalive script in the cron

 - AST_VDremote_agents.pl - script to keep remote agents logged in
   + To be used with VICIDIAL
   + kept running with the associated keepalive script in the cron

 - AST_VDhopper.pl - script to keep leads in the VICIDIAL hopper
   + To be used with VICIDIAL
   + recommended to put into cron as "* * * * * /path/to/script"

 - AST_VDadapt.pl - controls predictive dialing call pacing
   + Must be present in the astguiclient directory

 - ADMIN_keepalive_AST_VDautodial.pl - checks to see that VDAD is running
   + Must put entry for this script in the cron as "* * * * * /path/to/script"

 - ADMIN_keepalive_AST_VDremote_agents.pl - keeps remote agents logged in
   + Must put entry for this script in the cron as "* * * * * /path/to/script"

 - VICIDIAL_IN_new_leads_file.pl - script to load lead files into VICIDIAL
   + To be used with VICIDIAL

 - AST_DB_optimize.pl - optimizes many heavily-used tables in the asterisk DB
   + recommended to put into cron as "2 1 * * * /path/to/script"

 - ADMIN_adjust_GMTnow_on_leads.pl - adjusts the current GMT offset for leads
   + recommended to put into cron as "3 1 * * * /path/to/script"
   + should be run immediately after loading in new leads
   + follows daylight saving rules for several countries including:
	USA, Canada, Mexico, UK, Ireland, Australia, New Zealand, etc...

 - phone_codes_GMT.txt - file that contains all GMT offsets
   + used by ADMIN_adjust_GMTnow_on_leads.pl script
   + has over 1,000 entries for various areacode/country/GMT combinations

 - ASTGUICLIENT Web admin pages - administration for ASTGUICLIENT
   + must be installed on machine with PHP and MySQL enabled
   + must be able to read/write/update to asterisk MySQL database
   + welcome.php - just a welcome page
   + admin.php - all admin functions for ASTGUICLIENT database
   + user_stats.php - look at live user stats
   + remote_inbound.php - custom script to grab parked inbound calls remotely
   + inbound_popup.php - from remote_inbound page

 - astGUIclient web-client pages - web-only client app in agc folder
   + must be installed on machine with PHP and MySQL enabled
   + must be able to read/write/update to asterisk MySQL database
   + astguiclient.php - the client app
   + vicidial.php - the VICIDIAL web-client app
   + dbconnect.php - must set to database
   + all other scripts are for use by the astguiclient.php script internally

 - VICIDIAL Web admin pages - administration for VICIDIAL campaigns
   + must be installed on machine with PHP and MySQL enabled
   + must be able to read/write/update to asterisk MySQL database
   + welcome.php - just a welcome page
   + admin.php - all admin functions for VICIDIAL
   + user_stats.php - look at live user stats
   + record_conf_1_hour.php - record a VICIDIAL conference for 1 hour
   + closer.php - where closers log in to grab calls sent from fronters
   + closer_popup.php - from closer page, sends call to closers phone
   + closer_dispo.php - from closer_popup, allows dispositioning of call


DESCRIPTION:
This program was designed as a GUI client for the Asterisk PBX with Zaptel 
trunks or IAX or SIP VOIP trunks and SIP/IAX/Zap hard or softphones or devices
as extensions, it could be adapted to other functions, but it was designed for 
Zap/IAX/SIP users. The client programs are web-based and will run on most 
modern web browsers


TO BE ADDED:
See the TODO page on the project Wiki for more info on what has been finished in
this version as well as what we're thinking of working on. The items marked HIGH
priority in the TODO file are the ones we expect to have finished for the next 
release. If you have suggestions please send them.
http://www.eflo.net/VICIDIALwiki/index.php/TODO:current


NOTES:
There are several features/processes that could have been done many different 
ways in this suite, one for example is call parking. After tinkering with 
Asterisk/AGI-only call parking where all of the call parking would be achieved 
with no need for a GUI client, I discovered that my implementation did not 
function well on all single line phones or the Grandstream phones. I settled on 
the current method of call-parking by creating a record in a table for a call 
parked and Redirecting the call to a constant music extension to take any phone 
button pressing out of the loop. We haven't had any problems since we switched 
to this method of call parking. Many of the other decisions of how to program 
features in this suite we arrived at through similar trial-and-error methods. If 
anyone has suggestions/praises/criticisms I would love to hear them.


KNOWN BUGS:
- the astGUIclient web client exposes a nasty memory leak in Internet Explorer,
  Firefox does not have the same problem so we recommend it instead.
- on astGUIclient you cannot record a native bridge VOIP-only conversation 
  unless you go into a conference, you also cannot monitor a native-bridge 
  VOIP-only conversation
- Depending on what version of perl you are running(for the Perl scripts only),
  you may have a conflicting version of the Time::HiRes perl module installed. 
  To fix this, jut go to http://search.cpan.org/~jhi/Time-HiRes-1.82/ and 
  download the version that your error message says it's looking for(HiRes.pm).


VERSION HISTORY:

0.7 - First public release - 2003-11-18
This is the first release of the Asterisk GUI Client code, it is entirely 
written in Perl with portability and rapid development/testing in mind. The perl 
code is NOT strict, it was written loose and fast and has been functioning 
rather well in a production environment of 60 clients for one month now.

0.8 - Second public release - 2003-12-09
- Several bug fixes
- New button for monitoring live extensions on Zap channels
- Changed the method that the live channels/phone were populated on the 
listboxes of the client app.
- Changed the Asterisk/Manager commands to work correctly with new Asterisk CVS 
versions requirements. 
- A new routine was enabled to allow for making sure that the updater is   
running and bringing up a popup alert window on the client if the updater has 
not updated in the last 6 seconds. (this also added a new MySQL table)
- Changed the updater to run every 450 milliseconds instead of every 333 
milliseconds. 
- Updater changed to allow for ringing channels to appear in the live_channels 
table. 

0.9 - Third public release - 2004-02-05
The majority of the work in this release it to make it more stable and fix some 
pretty bad bugs. We created the Asterisk Central Queue System to address the 
problem with buffer-overflows in the manager interface of Asterisk causing total 
system deadlocks. We also completed and touched-up many other features that we 
didn't finish in previous releases. Here is the list of changes:
- Several bug fixes
- Inclusion of listing for active SIP/Local channels and ability to hang them up
- Completely changed the method of conferencing to be more fluid
- Added HELP popup screen
- Added intrasystem calling funtionality
- Updater changed to allow for SIP/Local channels
- Recording for conferences is now able to record all audio in and out
- Added ability to send DTMF tones within a conference
- Changed alert window for updater being down timeout to 20 seconds
- Added an option for using the new Asterisk Central Queue System(ACQS) that 
reduces the risk of deadlocks that occur with buffer-overflows on remote manager 
interface connections
- Included new script to run at boot time and rotate the logs as well as a 
keepalive script for the new ACQS 
- Changed non-AGI server-side scripts to allow for a single config file
- Detailed activity logging to text file option added
- Activity logging added to all non-AGI server applications

0.9.2 - Fourth public release - 2004-03-08
- several bug fixes for the GUI client and ACQS applications
- addition of the new VICIDIAL auto-dialer application and admin web pages
- added new script to reset MySQL tables
- added new script to kill CONGEST Local/ channels

0.9.4 - Fifth public release - 2004-03-12
- a few bug fixes for the GUI and server apps
- addition of callerID popup for the GUI
- callerID buttons to launch web pages from callerID popup
- addition of voicemail count display to GUI
- addition of button to directly connect to voicemail
- addition of voicemail counter updater
- MySQL table "phones" modified and "inbound" table added(see CONF_MySQL.txt)
- new extensions.conf entries need to be added to use callerid and vmail GUI

1.0.0 - Sixth public release - 2004-03-26
- a few bug fixes for the GUI and server apps
- Major documentation changes with the addition of the SCRATCH_INSTALL.txt file 
that goes step-by-step through the entire install process of an asterisk server 
from blank hardware to astguiclient installation and configuration.
- added pretty buttons to the WINphoneAPP
- added blind transfer to voicemail, blind transfer to another extension and 
blind external transfer
- new install script created to put server files in default positions and set 
permissions to execute
- new sql file to create all tables by running a script execute command in MySQL

1.0.1 - Seventh public release - 2004-04-27
- a few bug fixes for the GUI and server apps
- minor changes to WINphoneAPP
- major changes to VICIDIAL client code:
   + rearranged VICIDIAL buttons and added some color to DIAL and HANGUP buttons
   + allowed for call parking with different park music per-campaign
   + allowed for webpage forms launched from GUI with call info
   + allowed for different web form web page per-campaign
   + allowed agents using VICIDIAL client to send calls with data to a closer
   + new call-closer functionality is web-based for flexibility
   + added new DTMF dialpad for sending tones quickly
- fixed bugs in VICIDIAL web admin pages
- fixed bugs in ASTGUICLIENT web admin pages
- added code from Paul Concepcion to allow admin pages to work with globals off
- added common database connection file for each set of PHP admin pages
- made small changes to the MySQL database Schema
  (NOTE: if upgrading from 1.0.0 run the upgrade_1.0.1.sql script in MySQL)

1.0.2 - Eighth public release - 2004-06-03
- a few bug fixes for the GUI apps
- corrected documentation errors in SCRATCH_INSTALL instructions
- updated SCRATCH_INSTALL instructions for some newer software
- corrected errors in SQL install queries
- corrected errors in server installation scripts
- WINphoneAPP config option to allow one persistant mysql DB connection
- WINphoneAPP config option to allow modification of info refresh interval
- VICIDIAL modified to allow for auto-dial of next number
  (NOTE: if upgrading from 1.0.1 you do not need to update any web pages or 
   server apps, this update is only a client GUI and docs update. Note that the 
   AST_VICI_conf.pl has been updated, so you may need to modify your client 
   configs to enable new features.)

1.0.3 - Ninth public release - 2004-07-21
- a few bug fixes for the GUI apps
- changed the recording to show recording ID when recording is started
- VICIDIAL overhauled:
  + added Time::HiRes module requirement to better control time increments
  + changed to dial from a small hopper of pre-ordered leads per campaign
  + added cron script to always keep leads in the hopper every minute
  + added a counter to see how many times a lead is called
  + added ability to dial campaign by how many times lead called
  + added some new stats to the admin web pages
  + created a limited predictive-dialer that will dial a certain amount of leads
  per logged in agent and direct the calls that are picked up to the next agent
  + ability to transfer the called line and the 3rd party call into a separate 
  meetme room and continue on dialing
 UPGRADE NOTES:
  * if upgrading from 1.0.2 you need to update the web pages and all 
   server apps. 
  * if upgrading from 1.0.2 run the upgrade_1.0.3.sql script in MySQL
  * if upgrading from 1.0.2 you may want to run AST_upgrade_1.0.3.pl to update
   your called counts of your leads in vicidial_list.
  * AST_SERVER_conf.pl has been updated, so you may need to modify your server 
   configs to enable new features.
  * client app names have been changed to astVICIDIAL and astGUIclient

1.0.4 - Tenth public release - 2004-09-21
- several minor bug fixes for GUI apps and server apps
- fixed recording bug in astGUIclient that would rarely stop recording
- added timezone dialing in VICIDIAL
- added new client GUI app for inserting leads into the VICIDIAL leads table
- adjusted timings in the VICIDIAL autodialer for better performance
- added streamlined manager interface logins to help scripts run more smoothly
 UPGRADE NOTES:
  * if upgrading from 1.0.3 you need to update the web pages and all 
   server apps. 
  * if upgrading from 1.0.3 run the upgrade_1.0.4.sql script in MySQL
  * if upgrading from 1.0.3 you may want to run ADMIN_adjust_GMTnow_on_leads.pl
   to update the timezone in the vicidial_list table
  * AST_SERVER_conf.pl on the server has been updated, so you may need to modify
   your server configs to enable new features(like timezone dialing).
  * AST_VICI_conf.pl for clients has been updated, so you may need to modify
   your client configs to enable new features(like timezone dialing).
  * run this script if you plan on doing timezone restrictions on VICIDIAL:
   ADMIN_area_code_populate.pl

1.0.5 - Eleventh public release - 2004-10-28
- many minor bug fixes in most of the server and client scripts
- fixed multiple login bug on VICIDIAL
- fixed the manual-dial/auto-dial switching bug on VICIDIAL
- fixed a few time zone calling bugs on VICIDIAL server apps
- all apps now allow for IAX trunks to be used in addition to the usual Zap
- added more options to install script
- changed CLI lead loader to use Net::MySQL instead of DBI
- modified several scripts that handle CallerID because of the early October
  change in the CVS of outputting CID info as two separate fields(CallerID and 
  CallerIDname) instead of everything being sent in CallerID.
UPGRADE NOTES:
 * if upgrading from 1.0.4 you need to update the web pages and all 
   server and client apps. 
 * if upgrading from 1.0.4 run the upgrade_1.0.5.sql script in MySQL
 * if using a CVS version 2004-10-07 or newer or release 1.0.3 or newer 
   change the line in extensions.conf
	exten => 8309,2,Monitor(wav,${CALLERIDNUM})
		change to
	exten => 8309,2,Monitor(wav,${CALLERIDNAME})

1.0.6 - Twelfth public release - 2004-12-23
- many minor bug fixes in most of the server and client scripts
- optimized the ACQS and restructured its keepalive scripts
- added hijack line feature to astGUIclient
- added inbound call handling for VICIDIAL
- added blended in-outbound calling within VICIDIAL
- added new closer stats report
- updated the SCRATCH_INSTALL instructions and corrected errors
- see the TODO.txt file for other items DONE in this release
UPGRADE NOTES:
 * if upgrading from 1.0.5 you need to update the web pages and all 
   server and client apps. 
 * if upgrading from 1.0.5 run the upgrade_1.0.6.sql script in MySQL
 * if upgrading from 1.0.5 add a campaign "CLOSER" to enable closers and
   inbound call taking through the VICIDIAL application
     (see the SCRATCH_INSTALL document SUBPHASE 6.2 for more information)

1.1.0 - Thirteenth public release - 2005-03-09
- several small bug fixes in most of the server and client scripts
- added IAX and Zap client/agent compatibility
- implemented some major database optimizations
- ability to specify MySQL port across all apps
- moved almost all client config variables from client files to the Database
- created a meetme room validator that will allow faster reassignment of conferences
- created a new remote_agent login system for VICIDIAL in/outbound calls
- restructuring of AST_update script to cope better with manager output hiccups
- added dial timeout field to VICIDIAL to specify the timeout per campaign
- added help popups to every page of the admin web pages to explain fields
- see the TODO.txt file for other items DONE in this release
UPGRADE NOTES:
 * if upgrading from 1.0.6 you need to update the web pages and all 
   server and client apps. 
 * if upgrading from 1.0.6 run the upgrade_1.1.0.sql script in MySQL
 * if upgrading from 1.0.6 you will need to configure your client parameters on
   the admin web page and remove them from the client computers (except for the 
   fields that are in the sample AST_VICI_conf.pl file)
 * older client apps are not compatible with this version, you must upgrade

1.1.1 - Fourteenth public release - 2005-06-03
- several small bug fixes in most of the server and client scripts
- added astGUIclient web-only client
- added campaign-custom disposition statuses to VICIDIAL
- added campaign-custom CallerID to VICIDIAL
- added fronter-display-disable option to VICIDIAL Inbound Groups
- added ability to separate vicidial users by groups
- added performance stats gathering ability to AST_update script
- fixed the send-to-vmail portion of inbound AGI scripts
- added simple web-based lead file loader for VICIDIAL
UPGRADE NOTES:
 * if upgrading from 1.1.0 you need to update the web pages and all 
   server apps. 
 * if upgrading from 1.1.0 run the upgrade_1.1.1.sql script in MySQL

1.1.2 - Fifteenth public release - 2005-06-06
- fixed fatal bug in AST_update.pl script
- added instructions for ploticus install to SCRATCH_INSTALL
UPGRADE NOTES:
 * if upgrading from 1.1.0 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
 * if upgrading from 1.1.0 run the upgrade_1.1.1.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.1.sql)

1.1.3 - Sixteenth public release - 2005-06-10
- fixed config bug in AST_VDauto_dial.pl script
- added null query result checks to scripts in the agc web-only client
- added documentation for the inbound and transfer AGI script variables
- added documentation and tested install with MySQL 4.1 tree (4.1.12)
UPGRADE NOTES:
 * if upgrading from 1.1.0 - 1.1.2 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
 * if upgrading from 1.1.0 run the upgrade_1.1.1.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.1.sql)

1.1.4 - Seventeenth public release - 2005-06-24
- fixed new Local channel bugs caused by changes in Asterisk 1.0.8 and CVS
- added beta support for SIP trunks
- added ability to send VICIDIAL outbound calls to custom AGI script per 
   campaign and included new survey sample AGI script
- added better Asterisk shutdown detection to AST_manager_listen script
UPGRADE NOTES:
 * if upgrading from 1.1.3 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
  - if using VICIDIAL download the new client script to all client stations
  - make sure you populate the new $AST_ver variable in AST_SERVER_conf.pl
 * if upgrading from 1.1.3 run the upgrade_1.1.4.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.4.sql)

1.1.5 - Eighteenth public release - 2005-08-12
- fixed new Local channel bugs caused by changes in Asterisk 1.0.8 and 1.0.9
- moved many server variables to the database, editable through admin.php
- added ability to limit number of outbound trunks used by VICIDIAL auto dialer
- many small changes and bug fixes to the astguiclient web client
- added alpha vicidial web-client (click-to-dial only)
UPGRADE NOTES:
 * if upgrading from 1.1.4 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
  - VICIDIAL perl/TK client is unchanged from 1.1.4
  - make sure you check the astguiclient admin.php server parameters
 * if upgrading from 1.1.4 run the upgrade_1.1.5.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.5.sql)

1.1.6 - Nineteenth public release - 2005-08-29
- streamlined many of the server apps and added command-line debug capability
- finished the vicidial web-client and tested in prduction
- modified server-side scripts to function with Asterisk CVS_HEAD(soon to be 1.2)
- modified server performance logging and graphing, added user/system proc %
UPGRADE NOTES:
 * if upgrading from 1.1.5 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - perl/TK clients are unchanged from 1.1.5
  - make sure you check the astguiclient admin.php server parameters
  - make the agi-VDADtransfer.agi changes in extensions.conf (mentioned in SCRATCH_INSTALL doc)
 * if upgrading from 1.1.5 run the upgrade_1.1.6.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.6.sql)

1.1.7 - Twentieth public release - 2005-10-05
- Added HotKeys key binding for VICIDIAL web-client fast dispositioning
- Internationalization(multi-language) of web-clients and admin pages
- Added ability in manual VICIDIAL to call alternate lead phone numbers
- Added option of pull-down of active campaigns to VICIDIAL web login
- Fixed some issues with scripts interacting with Asterisk 1.2
- other small changes listed in the TODO.txt file
UPGRADE NOTES:
 * if upgrading from 1.1.6 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - perl/TK clients have been slightly updated(will be unsupported soon)
  - multi-language versions of web-clients and admin pages are available in the 
    LANG_agc.zip and LANG_admin.zip files and can be unzipped into your webroot
    directory. Make sure you check the dbconnect.php file in each directory.
 * if upgrading from 1.1.6 run the upgrade_1.1.7.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.7.sql)

1.1.8 - Twenty-First public release - 2005-11-10
- Added per-campaign call recording options to VICIDIAL
- Added detailed logging for VICIDIAL agent time
- Fixed some issues with scripts interacting with Asterisk 1.2
- other small changes listed in the TODO.txt file
UPGRADE NOTES:
 * if upgrading from 1.1.7 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - multi-language versions of web-clients and admin pages are available in the 
    LANG_agc.zip and LANG_admin.zip files and can be unzipped into your webroot
    directory. Make sure you check the dbconnect.php file in each directory.
 * if upgrading from 1.1.7 run the upgrade_1.1.8.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.8.sql)

1.1.9 - Twenty-Second public release - 2006-01-19
- Added Load Balancing of Inbound and Outbound calls across multiple Asterisk
   servers in the same setup
- Removed all PHP globals requirements, can now use register_globals=Off in PHP
- Add fully translated Greek admin and agc scripts(agc images to come)
- Added security elements to admin pages and added record deletion functions
- Added a script tab to VICIDIAL to have a variable-populated agent script
- Added new Super lead loader with field chooser and CVS/XLS-format capability
- Added favorites panel to astguiclient.php that shows off/on hook
- Many other small changes listed in the TODO.txt file
UPGRADE NOTES:
 * if upgrading from 1.1.8 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - multi-language versions of web-clients and admin pages are available in the 
    LANG_agc.zip and LANG_admin.zip files and can be unzipped into your webroot
    directory. Make sure you check the dbconnect.php file in each directory.
 * if upgrading from 1.1.8 run the upgrade_1.1.9.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.9.sql)

1.1.10 - Twenty-Third public release - 2006-03-17
- Added capability to work with app_amd(answering machine detection)
- Added transfer-conference number and DTMF presets per campaign
- Added sphinx ring-time analysis disposition package(beta)
- Added scheduled callbacks to VICIDIAL
- Added more user-permission options/restrictions
- Several small bug fixes for better Asterisk 1.2.4 compatibility
- Many other small changes listed in the TODO.txt file
UPGRADE NOTES:
 * if upgrading from 1.1.9 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - add "exten => h,2,DeadAGI(VD_hangup.agi,${EXTEN})" to extensions.conf
  - multi-language versions of web-clients and admin pages are available in the 
    LANG_agc.zip and LANG_admin.zip files and can be unzipped into your webroot
    directory. Make sure you check the dbconnect.php file in each directory.
 * if upgrading from 1.1.9 run the upgrade_1.1.10.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.10.sql)

1.1.11 - Twenty-Fourth public release - 2006-04-28
- Added Agent-only Scheduled Callbacks
- Added Lead Filters for campaigns to use custom SQL to narrow calling scope
- Added Agent manual-lead-insertion capability
- Fixed PHP variable checking in all PHP scripts to be more compliant
- Added more user-permission options/restrictions for both agents and admins
- Several small bug fixes for better Asterisk 1.2.6 and SVN compatibility
- Many other small changes listed in the TODO.txt file
UPGRADE NOTES:
 * if upgrading from 1.1.10 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - multi-language versions of web-clients and admin pages are available in the 
    LANG_agc.zip and LANG_admin.zip files and can be unzipped into your webroot
    directory. Make sure you check the dbconnect.php file in each directory.
 * if upgrading from 1.1.10 run the upgrade_1.1.11.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.11.sql)

1.1.12 - Twenty-Fifth public release - 2006-06-22
- Security enhancements of PHP scripts to reduce SQL-injection and other threats
- Completely redesigned local call time system in VICIDIAL for more flexibility
- Added Drop-call and safe-harbor options to campaigns and inbound groups
- Added option for allowing wrapup time between calls in vicidial.php
- Multi-language translation rewritten for more flexibility and easy of use
- Added more user-permission options/restrictions for both agents and admins
- Added option of using app_conference instead of meetme engine
- Added internal DNC list for Do-Not-Call entries across the system
- Added automatic lead recycling to call back Busy calls at definable intervals
- Added listID override and timezone lookup to all lead importing scripts
- Added easy-prompt-recording AGI and playback: agi-record_prompts.agi
- Many other changes and bug fixes listed in the TODO.txt file
UPGRADE NOTES:
 * if upgrading from 1.1.11 you need to:
  - download the new version from the project website
  - unzip the zip file into the astguiclientastguiclient directory
  - and either:
    + copy the web pages and all server apps to their proper locations manually
    + or run the install_server_files.pl script to put all items in their
      default places
   (make sure AST_SERVER_conf.pl and dbconnect.php files are config'd properly)
  - multi-language versions of web-clients and admin pages are available in the 
    LANG_agc.zip and LANG_admin.zip files and can be unzipped into your webroot
    directory. Make sure you check the dbconnect.php file in each directory.
 * if upgrading from 1.1.11 run the upgrade_1.1.12.sql script in MySQL
   (\. astguiclientastguiclient/upgrade_1.1.12.sql)
 * if upgrading from older version you will need to run this query for each 
   campaign in VICIDIAL in MySQL:
   (INSERT INTO vicidial_campaign_stats (campaign_id) values('campaignname');)

2.0.1 - Twenty-Sixth public release - 2006-09-XX
- Changed all Perl script to use DBI instead of Net::MySQL
- Completely new interactive install script with new astguiclient.conf file
- Ability to turn off or on logging of server scripts
- Ability to redirect AGI output to file, STDERR, both or nowhere.
- Code and process optimization of many of the server-side scripts
- Added a Predictive outbound dialing algorithm with many user-defined settings
- Wrote new BASE_INSTALL.txt doc for simple install and added sample conf files
- Many other changes and bug fixes listed in the TODO Wiki webpage:
     http://www.eflo.net/VICIDIALwiki/index.php/TODO:current
UPGRADE NOTES:
 * if upgrading from 1.1.12 you need to follow instructions in the UPGRADE doc
