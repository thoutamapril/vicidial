<?
# vdc_db_query.php
# 
# Copyright (C) 2006  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#
# This script is designed purely to send whether the meetme conference has live channels connected and which they are
# This script depends on the server_ip being sent and also needs to have a valid user/pass from the vicidial_users table
# 
# required variables:
#  - $server_ip
#  - $session_name
#  - $user
#  - $pass
# optional variables:
#  - $format - ('text','debug')
#  - $ACTION - ('regCLOSER','manDiaLnextCALL','manDiaLskip','manDiaLonly','manDiaLlookCALL','manDiaLlogCALL','userLOGout','updateDISPO','VDADpause','VDADready','VDADcheckINCOMING','UpdatEFavoritEs','CalLBacKLisT','CalLBacKCounT')
#  - $stage - ('start','finish','lookup','new')
#  - $closer_choice - ('CL_TESTCAMP_L CL_OUT123_L -')
#  - $conf_exten - ('8600011',...)
#  - $exten - ('123test',...)
#  - $ext_context - ('default','demo',...)
#  - $ext_priority - ('1','2',...)
#  - $campaign - ('testcamp',...)
#  - $dial_timeout - ('60','26',...)
#  - $dial_prefix - ('9','8',...)
#  - $campaign_cid - ('3125551212','0000000000',...)
#  - $MDnextCID - ('M06301413000000002',...)
#  - $uniqueid - ('1120232758.2406800',...)
#  - $lead_id - ('36524',...)
#  - $list_id - ('101','123456',...)
#  - $length_in_sec - ('12',...)
#  - $phone_code - ('1',...)
#  - $phone_number - ('3125551212',...)
#  - $channel - ('Zap/12-1',...)
#  - $start_epoch - ('1120236911',...)
#  - $vendor_lead_code - ('1234test',...)
#  - $title - ('Mr.',...)
#  - $first_name - ('Bob',...)
#  - $middle_initial - ('L',...)
#  - $last_name - ('Wilson',...)
#  - $address1 - ('1324 Main St.',...)
#  - $address2 - ('Apt. 12',...)
#  - $address3 - ('co Robert Wilson',...)
#  - $city - ('Chicago',...)
#  - $state - ('IL',...)
#  - $province - ('NA',...)
#  - $postal_code - ('60054',...)
#  - $country_code - ('USA',...)
#  - $gender - ('M',...)
#  - $date_of_birth - ('1970-01-01',...)
#  - $alt_phone - ('3125551213',...)
#  - $email - ('bob@bob.com',...)
#  - $security_phrase - ('Hello',...)
#  - $comments - ('Good Customer',...)
#  - $auto_dial_level - ('0','1','1.2',...)
#  - $VDstop_rec_after_each_call - ('0','1')
#  - $conf_silent_prefix - ('7','8','',...)
#  - $extension - ('123','user123','25-1',...)
#  - $protocol - ('Zap','SIP','IAX2',...)
#  - $user_abb - ('1234','6666',...)
#  - $preview - ('YES','NO',...)
#  - $called_count - ('0','1','2',...)
#  - $agent_log_id - ('123456',...)
#  - $agent_log - ('NO',...)
#  - $favorites_list - (",'cc160','cc100'",...)
#  - $CallBackDatETimE - ('2006-04-21 14:30:00',...)
#  - $recipient - ('ANYONE,'USERONLY')
#  - $callback_id - ('12345','12346',...)
#  - $use_internal_dnc - ('Y','N')
#  - $omit_phone_code - ('Y','N')
#  - $no_delete_sessions - ('0','1')

# changes
# 50629-1044 - First build of script
# 50630-1422 - Added manual dial action and MD channel lookup
# 50701-1451 - Added dial log for start and end of vicidial calls
# 50705-1239 - Added call disposition update
# 50804-1627 - Fixed updateDispo to update vicidial_log entry
# 50816-1605 - Added VDADpause/ready for auto dialing
# 50816-1811 - Added basic autodial call pickup functions
# 50817-1005 - Altered logging functions to accomodate auto_dialing
# 50818-1305 - Added stop-all-recordings-after-each-vicidial-call option
# 50818-1411 - Added hangup of agent phone after Logout
# 50901-1315 - Fixed CLOSER IN-GROUP Web Form bug
# 50902-1507 - Fixed CLOSER log length_in_sec bug
# 50902-1730 - Added functions for manual preview dialing and revert
# 50913-1214 - Added agent random update to leadupdate
# 51020-1421 - Added agent_log_id framework for detailed agent activity logging
# 51021-1717 - Allows for multi-line comments (changes \n to !N in database)
# 51111-1046 - Added vicidial_agent_log lead_id earlier for manual dial
# 51121-1445 - Altered echo statements for several small PHP speed optimizations
# 51122-1328 - Fixed UserLogout issue not removing conference reservation
# 51129-1012 - Added ability to accept calls from other VICIDIAL servers
# 51129-1729 - Changed manual dial to use the '/n' flag for calls
# 51221-1154 - Added SCRIPT id lookup and sending to vicidial.php for display
# 60105-1059 - Added Updating of astguiclient favorites in the DB
# 60208-1617 - Added dtmf buttons output per call
# 60213-1521 - Added closer_campaigns update to vicidial_users
# 60215-1036 - Added Callback date-time entry into vicidial_callbacks table
# 60413-1541 - Added USERONLY Callback listings output - CalLBacKLisT
#            - Added USERONLY Callback count output - CalLBacKCounT
# 60414-1140 - Added Callback lead lookup for manual dialing
# 60419-1517 - After CALLBK is sent to agent, update callback record to INACTIVE
# 60421-1419 - check GET/POST vars lines with isset to not trigger PHP NOTICES
# 60427-1236 - Fixed closer_choice error for CLOSER campaigns
# 60609-1148 - Added ability to check for manual dial numbers in DNC
# 60619-1117 - Added variable filters to close security holes for login form
# 60623-1414 - Fixed variable filter for phone_code and fixed manual dial logic
# 60821-1600 - Added ability to omit the phone code on vicidial lead dialing
# 60821-1647 - Added ability to not delete sessions at logout

$version = '2.0.36';
$build = '60821-1647';

require("dbconnect.php");

### If you have globals turned off uncomment these lines
if (isset($_GET["user"]))						{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))				{$user=$_POST["user"];}
if (isset($_GET["pass"]))						{$pass=$_GET["pass"];}
	elseif (isset($_POST["pass"]))				{$pass=$_POST["pass"];}
if (isset($_GET["server_ip"]))					{$server_ip=$_GET["server_ip"];}
	elseif (isset($_POST["server_ip"]))			{$server_ip=$_POST["server_ip"];}
if (isset($_GET["session_name"]))				{$session_name=$_GET["session_name"];}
	elseif (isset($_POST["session_name"]))		{$session_name=$_POST["session_name"];}
if (isset($_GET["format"]))						{$format=$_GET["format"];}
	elseif (isset($_POST["format"]))			{$format=$_POST["format"];}
if (isset($_GET["ACTION"]))						{$ACTION=$_GET["ACTION"];}
	elseif (isset($_POST["ACTION"]))			{$ACTION=$_POST["ACTION"];}
if (isset($_GET["stage"]))						{$stage=$_GET["stage"];}
	elseif (isset($_POST["stage"]))				{$stage=$_POST["stage"];}
if (isset($_GET["closer_choice"]))				{$closer_choice=$_GET["closer_choice"];}
	elseif (isset($_POST["closer_choice"]))		{$closer_choice=$_POST["closer_choice"];}
if (isset($_GET["conf_exten"]))					{$conf_exten=$_GET["conf_exten"];}
	elseif (isset($_POST["conf_exten"]))		{$conf_exten=$_POST["conf_exten"];}
if (isset($_GET["exten"]))						{$exten=$_GET["exten"];}
	elseif (isset($_POST["exten"]))				{$exten=$_POST["exten"];}
if (isset($_GET["ext_context"]))				{$ext_context=$_GET["ext_context"];}
	elseif (isset($_POST["ext_context"]))		{$ext_context=$_POST["ext_context"];}
if (isset($_GET["ext_priority"]))				{$ext_priority=$_GET["ext_priority"];}
	elseif (isset($_POST["ext_priority"]))		{$ext_priority=$_POST["ext_priority"];}
if (isset($_GET["campaign"]))					{$campaign=$_GET["campaign"];}
	elseif (isset($_POST["campaign"]))			{$campaign=$_POST["campaign"];}
if (isset($_GET["dial_timeout"]))				{$dial_timeout=$_GET["dial_timeout"];}
	elseif (isset($_POST["dial_timeout"]))		{$dial_timeout=$_POST["dial_timeout"];}
if (isset($_GET["dial_prefix"]))				{$dial_prefix=$_GET["dial_prefix"];}
	elseif (isset($_POST["dial_prefix"]))		{$dial_prefix=$_POST["dial_prefix"];}
if (isset($_GET["campaign_cid"]))				{$campaign_cid=$_GET["campaign_cid"];}
	elseif (isset($_POST["campaign_cid"]))		{$campaign_cid=$_POST["campaign_cid"];}
if (isset($_GET["MDnextCID"]))					{$MDnextCID=$_GET["MDnextCID"];}
	elseif (isset($_POST["MDnextCID"]))			{$MDnextCID=$_POST["MDnextCID"];}
if (isset($_GET["uniqueid"]))					{$uniqueid=$_GET["uniqueid"];}
	elseif (isset($_POST["uniqueid"]))			{$uniqueid=$_POST["uniqueid"];}
if (isset($_GET["lead_id"]))					{$lead_id=$_GET["lead_id"];}
	elseif (isset($_POST["lead_id"]))			{$lead_id=$_POST["lead_id"];}
if (isset($_GET["list_id"]))					{$list_id=$_GET["list_id"];}
	elseif (isset($_POST["list_id"]))			{$list_id=$_POST["list_id"];}
if (isset($_GET["length_in_sec"]))				{$length_in_sec=$_GET["length_in_sec"];}
	elseif (isset($_POST["length_in_sec"]))		{$length_in_sec=$_POST["length_in_sec"];}
if (isset($_GET["phone_code"]))					{$phone_code=$_GET["phone_code"];}
	elseif (isset($_POST["phone_code"]))		{$phone_code=$_POST["phone_code"];}
if (isset($_GET["phone_number"]))				{$phone_number=$_GET["phone_number"];}
	elseif (isset($_POST["phone_number"]))		{$phone_number=$_POST["phone_number"];}
if (isset($_GET["channel"]))					{$channel=$_GET["channel"];}
	elseif (isset($_POST["channel"]))			{$channel=$_POST["channel"];}
if (isset($_GET["start_epoch"]))				{$start_epoch=$_GET["start_epoch"];}
	elseif (isset($_POST["start_epoch"]))		{$start_epoch=$_POST["start_epoch"];}
if (isset($_GET["dispo_choice"]))				{$dispo_choice=$_GET["dispo_choice"];}
	elseif (isset($_POST["dispo_choice"]))		{$dispo_choice=$_POST["dispo_choice"];}
if (isset($_GET["vendor_lead_code"]))			{$vendor_lead_code=$_GET["vendor_lead_code"];}
	elseif (isset($_POST["vendor_lead_code"]))	{$vendor_lead_code=$_POST["vendor_lead_code"];}
if (isset($_GET["title"]))						{$title=$_GET["title"];}
	elseif (isset($_POST["title"]))				{$title=$_POST["title"];}
if (isset($_GET["first_name"]))					{$first_name=$_GET["first_name"];}
	elseif (isset($_POST["first_name"]))		{$first_name=$_POST["first_name"];}
if (isset($_GET["middle_initial"]))				{$middle_initial=$_GET["middle_initial"];}
	elseif (isset($_POST["middle_initial"]))	{$middle_initial=$_POST["middle_initial"];}
if (isset($_GET["last_name"]))					{$last_name=$_GET["last_name"];}
	elseif (isset($_POST["last_name"]))			{$last_name=$_POST["last_name"];}
if (isset($_GET["address1"]))					{$address1=$_GET["address1"];}
	elseif (isset($_POST["address1"]))			{$address1=$_POST["address1"];}
if (isset($_GET["address2"]))					{$address2=$_GET["address2"];}
	elseif (isset($_POST["address2"]))			{$address2=$_POST["address2"];}
if (isset($_GET["address3"]))					{$address3=$_GET["address3"];}
	elseif (isset($_POST["address3"]))			{$address3=$_POST["address3"];}
if (isset($_GET["city"]))						{$city=$_GET["city"];}
	elseif (isset($_POST["city"]))				{$city=$_POST["city"];}
if (isset($_GET["state"]))						{$state=$_GET["state"];}
	elseif (isset($_POST["state"]))				{$state=$_POST["state"];}
if (isset($_GET["province"]))					{$province=$_GET["province"];}
	elseif (isset($_POST["province"]))			{$province=$_POST["province"];}
if (isset($_GET["postal_code"]))				{$postal_code=$_GET["postal_code"];}
	elseif (isset($_POST["postal_code"]))		{$postal_code=$_POST["postal_code"];}
if (isset($_GET["country_code"]))				{$country_code=$_GET["country_code"];}
	elseif (isset($_POST["country_code"]))		{$country_code=$_POST["country_code"];}
if (isset($_GET["gender"]))						{$gender=$_GET["gender"];}
	elseif (isset($_POST["gender"]))			{$gender=$_POST["gender"];}
if (isset($_GET["date_of_birth"]))				{$date_of_birth=$_GET["date_of_birth"];}
	elseif (isset($_POST["date_of_birth"]))		{$date_of_birth=$_POST["date_of_birth"];}
if (isset($_GET["alt_phone"]))					{$alt_phone=$_GET["alt_phone"];}
	elseif (isset($_POST["alt_phone"]))			{$alt_phone=$_POST["alt_phone"];}
if (isset($_GET["email"]))						{$email=$_GET["email"];}
	elseif (isset($_POST["email"]))				{$email=$_POST["email"];}
if (isset($_GET["security_phrase"]))			{$security_phrase=$_GET["security_phrase"];}
	elseif (isset($_POST["security_phrase"]))	{$security_phrase=$_POST["security_phrase"];}
if (isset($_GET["comments"]))					{$comments=$_GET["comments"];}
	elseif (isset($_POST["comments"]))			{$comments=$_POST["comments"];}
if (isset($_GET["auto_dial_level"]))			{$auto_dial_level=$_GET["auto_dial_level"];}
	elseif (isset($_POST["auto_dial_level"]))	{$auto_dial_level=$_POST["auto_dial_level"];}
if (isset($_GET["VDstop_rec_after_each_call"]))				{$VDstop_rec_after_each_call=$_GET["VDstop_rec_after_each_call"];}
	elseif (isset($_POST["VDstop_rec_after_each_call"]))		{$VDstop_rec_after_each_call=$_POST["VDstop_rec_after_each_call"];}
if (isset($_GET["conf_silent_prefix"]))				{$conf_silent_prefix=$_GET["conf_silent_prefix"];}
	elseif (isset($_POST["conf_silent_prefix"]))	{$conf_silent_prefix=$_POST["conf_silent_prefix"];}
if (isset($_GET["extension"]))					{$extension=$_GET["extension"];}
	elseif (isset($_POST["extension"]))			{$extension=$_POST["extension"];}
if (isset($_GET["protocol"]))					{$protocol=$_GET["protocol"];}
	elseif (isset($_POST["protocol"]))			{$protocol=$_POST["protocol"];}
if (isset($_GET["user_abb"]))					{$user_abb=$_GET["user_abb"];}
	elseif (isset($_POST["user_abb"]))			{$user_abb=$_POST["user_abb"];}
if (isset($_GET["preview"]))					{$preview=$_GET["preview"];}
	elseif (isset($_POST["preview"]))			{$preview=$_POST["preview"];}
if (isset($_GET["called_count"]))				{$called_count=$_GET["called_count"];}
	elseif (isset($_POST["called_count"]))		{$called_count=$_POST["called_count"];}
if (isset($_GET["agent_log_id"]))				{$agent_log_id=$_GET["agent_log_id"];}
	elseif (isset($_POST["agent_log_id"]))		{$agent_log_id=$_POST["agent_log_id"];}
if (isset($_GET["agent_log"]))					{$agent_log=$_GET["agent_log"];}
	elseif (isset($_POST["agent_log"]))			{$agent_log=$_POST["agent_log"];}
if (isset($_GET["favorites_list"]))				{$favorites_list=$_GET["favorites_list"];}
	elseif (isset($_POST["favorites_list"]))	{$favorites_list=$_POST["favorites_list"];}
if (isset($_GET["CallBackDatETimE"]))			{$CallBackDatETimE=$_GET["CallBackDatETimE"];}
	elseif (isset($_POST["CallBackDatETimE"]))	{$CallBackDatETimE=$_POST["CallBackDatETimE"];}
if (isset($_GET["recipient"]))					{$recipient=$_GET["recipient"];}
	elseif (isset($_POST["recipient"]))			{$recipient=$_POST["recipient"];}
if (isset($_GET["callback_id"]))				{$callback_id=$_GET["callback_id"];}
	elseif (isset($_POST["callback_id"]))		{$callback_id=$_POST["callback_id"];}
if (isset($_GET["use_internal_dnc"]))			{$use_internal_dnc=$_GET["use_internal_dnc"];}
	elseif (isset($_POST["use_internal_dnc"]))	{$use_internal_dnc=$_POST["use_internal_dnc"];}
if (isset($_GET["omit_phone_code"]))			{$omit_phone_code=$_GET["omit_phone_code"];}
	elseif (isset($_POST["omit_phone_code"]))	{$omit_phone_code=$_POST["omit_phone_code"];}


$user=ereg_replace("[^0-9a-zA-Z]","",$user);
$pass=ereg_replace("[^0-9a-zA-Z]","",$pass);
$length_in_sec = ereg_replace("[^0-9]","",$length_in_sec);
$phone_code = ereg_replace("[^0-9]","",$phone_code);
$phone_number = ereg_replace("[^0-9]","",$phone_number);


# default optional vars if not set
if (!isset($format))   {$format="text";}
	if ($format == 'debug')	{$DB=1;}
if (!isset($ACTION))   {$ACTION="refresh";}

$StarTtime = date("U");
$NOW_DATE = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");
$CIDdate = date("mdHis");
$ENTRYdate = date("YmdHis");
if (!isset($query_date)) {$query_date = $NOW_DATE;}
$MT[0]='';

$stmt="SELECT count(*) from vicidial_users where user='$user' and pass='$pass' and user_level > 0;";
if ($DB) {echo "|$stmt|\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if( (strlen($user)<2) or (strlen($pass)<2) or ($auth==0))
{
echo "Invalid Username/Password: |$user|$pass|\n";
exit;
}
else
{

if( (strlen($server_ip)<6) or (!isset($server_ip)) or ( (strlen($session_name)<12) or (!isset($session_name)) ) )
	{
	echo "Invalid server_ip: |$server_ip|  or  Invalid session_name: |$session_name|\n";
	exit;
	}
else
	{
	$stmt="SELECT count(*) from web_client_sessions where session_name='$session_name' and server_ip='$server_ip';";
	if ($DB) {echo "|$stmt|\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$SNauth=$row[0];
	  if($SNauth==0)
		{
		echo "Invalid session_name: |$session_name|$server_ip|\n";
		exit;
		}
	  else
		{
		# do nothing for now
		}
	}
}

if ($format=='debug')
{
echo "<html>\n";
echo "<head>\n";
echo "<!-- VERSION: $version     BUILD: $build    USER: $user   server_ip: $server_ip-->\n";
echo "<title>VICIDiaL Database Query Script";
echo "</title>\n";
echo "</head>\n";
echo "<BODY BGCOLOR=white marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>\n";
}


################################################################################
### regCLOSER - update the vicidial_live_agents table to reflect the closer
###             inbound choices made upon login
################################################################################
if ($ACTION == 'regCLOSER')
{
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($closer_choice)<1) || (strlen($user)<1) )
	{
	$channel_live=0;
	echo "Group Choice $closer_choice is not valid\n";
	exit;
	}
	else
	{
		if ($closer_choice == "MGRLOCK-")
		{
		$stmt="SELECT closer_campaigns FROM vicidial_users where user='$user' LIMIT 1;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
			$row=mysql_fetch_row($rslt);
			$closer_choice =$row[0];

		$stmt="UPDATE vicidial_live_agents set closer_campaigns='$closer_choice' where user='$user' and server_ip='$server_ip';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		}
		else
		{
		$stmt="UPDATE vicidial_live_agents set closer_campaigns='$closer_choice' where user='$user' and server_ip='$server_ip';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);

		$stmt="UPDATE vicidial_users set closer_campaigns='$closer_choice' where user='$user';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		}
	}
	echo "Closer In Group Choice $closer_choice has been registered to user $user\n";
}


################################################################################
### manDiaLnextCALL - for manual VICIDiaL dialing this will grab the next lead
###                   in the campaign, reserve it, send data back to client and
###                   place the call by inserting into vicidial_manager
################################################################################
if ($ACTION == 'manDiaLnextCaLL')
{
	$MT[0]='';
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($conf_exten)<1) || (strlen($campaign)<1)  || (strlen($ext_context)<1) )
	{
	$channel_live=0;
	echo "HOPPER EMPTY\n";
	echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context is not valid\n";
	exit;
	}
	else
	{
	### check if this is a callback, if it is, skip the grabbing of a new lead and mark the callback as INACTIVE
	if ( (strlen($callback_id)>0) && (strlen($lead_id)>0) )
		{
		$affected_rows=1;
		$CBleadIDset=1;

		$stmt = "UPDATE vicidial_callbacks set status='INACTIVE' where callback_id='$callback_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		}
	else
		{
		if (strlen($phone_number)>3)
			{
			if ($use_internal_dnc=='Y')
				{
				$stmt="SELECT count(*) FROM vicidial_dnc where phone_number='$phone_number';";
				$rslt=mysql_query($stmt, $link);
				if ($DB) {echo "$stmt\n";}
				$row=mysql_fetch_row($rslt);
				
				if ($row[0] > 0)
					{
					echo "DNC NUMBER\n";
					exit;
					}
				}
			if ($stage=='lookup')
				{
				$stmt="SELECT lead_id FROM vicidial_list where phone_number='$phone_number' order by modify_date desc LIMIT 1;";
				$rslt=mysql_query($stmt, $link);
				if ($DB) {echo "$stmt\n";}
				$man_leadID_ct = mysql_num_rows($rslt);
				if ($man_leadID_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$affected_rows=1;
					$lead_id =$row[0];
					$CBleadIDset=1;
					}
				else
					{
					### insert a new lead in the system with this phone number
					$stmt = "INSERT INTO vicidial_list SET phone_code='$phone_code',phone_number='$phone_number',list_id='$list_id',status='QUEUE',user='$user',called_since_last_reset='Y',entry_date='$ENTRYdate';";
					if ($DB) {echo "$stmt\n";}
					$rslt=mysql_query($stmt, $link);
					$affected_rows = mysql_affected_rows($link);
					$lead_id = mysql_insert_id();
					$CBleadIDset=1;
					}
				}
			else
				{
				### insert a new lead in the system with this phone number
				$stmt = "INSERT INTO vicidial_list SET phone_code='$phone_code',phone_number='$phone_number',list_id='$list_id',status='QUEUE',user='$user',called_since_last_reset='Y',entry_date='$ENTRYdate';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$affected_rows = mysql_affected_rows($link);
				$lead_id = mysql_insert_id();
				$CBleadIDset=1;
				}
			}
		else
			{
			### grab the next lead in the hopper for this campaign and reserve it for the user
			$stmt = "UPDATE vicidial_hopper set status='QUEUE', user='$user' where campaign_id='$campaign' and status='READY' order by hopper_id LIMIT 1";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$affected_rows = mysql_affected_rows($link);
			}
		}

	if ($affected_rows > 0)
		{
		if (!$CBleadIDset)
			{
			##### grab the lead_id of the reserved user in vicidial_hopper
			$stmt="SELECT lead_id FROM vicidial_hopper where campaign_id='$campaign' and status='QUEUE' and user='$user' LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$hopper_leadID_ct = mysql_num_rows($rslt);
			if ($hopper_leadID_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$lead_id =$row[0];
				}
			}

			##### grab the data from vicidial_list for the lead_id
			$stmt="SELECT * FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$list_lead_ct = mysql_num_rows($rslt);
			if ($list_lead_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
			#	$lead_id		= trim("$row[0]");
				$dispo			= trim("$row[3]");
				$tsr			= trim("$row[4]");
				$vendor_id		= trim("$row[5]");
				$list_id		= trim("$row[7]");
				$gmt_offset_now	= trim("$row[8]");
				$phone_code		= trim("$row[10]");
				$phone_number	= trim("$row[11]");
				$title			= trim("$row[12]");
				$first_name		= trim("$row[13]");
				$middle_initial	= trim("$row[14]");
				$last_name		= trim("$row[15]");
				$address1		= trim("$row[16]");
				$address2		= trim("$row[17]");
				$address3		= trim("$row[18]");
				$city			= trim("$row[19]");
				$state			= trim("$row[20]");
				$province		= trim("$row[21]");
				$postal_code	= trim("$row[22]");
				$country_code	= trim("$row[23]");
				$gender			= trim("$row[24]");
				$date_of_birth	= trim("$row[25]");
				$alt_phone		= trim("$row[26]");
				$email			= trim("$row[27]");
				$security		= trim("$row[28]");
				$comments		= trim("$row[29]");
				$called_count	= trim("$row[30]");
				}

			$called_count++;

			### flag the lead as called and change it's status to INCALL
			$stmt = "UPDATE vicidial_list set status='INCALL', called_since_last_reset='Y', called_count='$called_count',user='$user' where lead_id='$lead_id';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);

			if (!$CBleadIDset)
				{
				### delete the lead from the hopper
				$stmt = "DELETE FROM vicidial_hopper where lead_id='$lead_id';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				}

			$stmt="UPDATE vicidial_agent_log set lead_id='$lead_id' where agent_log_id='$agent_log_id';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
		
			### if preview dialing, do not send the call	
			if ( (strlen($preview)<1) || ($preview == 'NO') )
				{
				### prepare variables to place manual call from VICIDiaL
				$CCID_on=0;   $CCID='';
				$local_DEF = 'Local/';
				$local_AMP = '@';
				$Local_out_prefix = '9';
				$Local_dial_timeout = '60';
				$Local_persist = '/n';
				if ($dial_timeout > 4) {$Local_dial_timeout = $dial_timeout;}
				$Local_dial_timeout = ($Local_dial_timeout * 1000);
				if (strlen($dial_prefix) > 0) {$Local_out_prefix = "$dial_prefix";}
				if (strlen($campaign_cid) > 6) {$CCID = "$campaign_cid";   $CCID_on++;}
				if (eregi("x",$dial_prefix)) {$Local_out_prefix = '';}

				$PADlead_id = sprintf("%09s", $lead_id);
					while (strlen($PADlead_id) > 9) {$PADlead_id = substr("$PADlead_id", 0, -1);}

				# Create unique calleridname to track the call: MmmddhhmmssLLLLLLLLL
					$MqueryCID = "M$CIDdate$PADlead_id";
				if ($CCID_on) {$CIDstring = "\"$MqueryCID\" <$CCID>";}
				else {$CIDstring = "$MqueryCID";}

				### whether to omit phone_code or not
				if (eregi('Y',$omit_phone_code)) 
					{$Ndialstring = "$Local_out_prefix$phone_number";}
				else
					{$Ndialstring = "$Local_out_prefix$phone_code$phone_number";}
				### insert the call action into the vicidial_manager table to initiate the call
				#	$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $conf_exten','Context: $ext_context','Channel: $local_DEF$Local_out_prefix$phone_code$phone_number$local_AMP$ext_context','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','');";
				$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $Ndialstring','Context: $ext_context','Channel: $local_DEF$conf_exten$local_AMP$ext_context$Local_persist','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','');";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				}

			$comments = eregi_replace("\r",'',$comments);
			$comments = eregi_replace("\n",'!N',$comments);

			$LeaD_InfO =	$MqueryCID . "\n";
			$LeaD_InfO .=	$lead_id . "\n";
			$LeaD_InfO .=	$dispo . "\n";
			$LeaD_InfO .=	$tsr . "\n";
			$LeaD_InfO .=	$vendor_id . "\n";
			$LeaD_InfO .=	$list_id . "\n";
			$LeaD_InfO .=	$gmt_offset_now . "\n";
			$LeaD_InfO .=	$phone_code . "\n";
			$LeaD_InfO .=	$phone_number . "\n";
			$LeaD_InfO .=	$title . "\n";
			$LeaD_InfO .=	$first_name . "\n";
			$LeaD_InfO .=	$middle_initial . "\n";
			$LeaD_InfO .=	$last_name . "\n";
			$LeaD_InfO .=	$address1 . "\n";
			$LeaD_InfO .=	$address2 . "\n";
			$LeaD_InfO .=	$address3 . "\n";
			$LeaD_InfO .=	$city . "\n";
			$LeaD_InfO .=	$state . "\n";
			$LeaD_InfO .=	$province . "\n";
			$LeaD_InfO .=	$postal_code . "\n";
			$LeaD_InfO .=	$country_code . "\n";
			$LeaD_InfO .=	$gender . "\n";
			$LeaD_InfO .=	$date_of_birth . "\n";
			$LeaD_InfO .=	$alt_phone . "\n";
			$LeaD_InfO .=	$email . "\n";
			$LeaD_InfO .=	$security . "\n";
			$LeaD_InfO .=	$comments . "\n";
			$LeaD_InfO .=	$called_count . "\n";

			echo $LeaD_InfO;

		}
		else
		{
		echo "HOPPER EMPTY\n";
		}
	}
}


################################################################################
### manDiaLskip - for manual VICIDiaL dialing this skips the lead that was
###               previewed in the step above and puts it back in orig status
################################################################################
if ($ACTION == 'manDiaLskip')
{
	$MT[0]='';
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($stage)<1) || (strlen($called_count)<1) || (strlen($lead_id)<1) )
	{
		$channel_live=0;
		echo "LEAD NOT REVERTED\n";
		echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context is not valid\n";
		exit;
	}
	else
	{
		$called_count = ($called_count - 1);
		### flag the lead as called and change it's status to INCALL
		$stmt = "UPDATE vicidial_list set status='$stage', called_count='$called_count',user='$user' where lead_id='$lead_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);


		echo "LEAD REVERTED\n";
	}
}


################################################################################
### manDiaLonly - for manual VICIDiaL dialing this sends the call that was
###               previewed in the step above
################################################################################
if ($ACTION == 'manDiaLonly')
{
	$MT[0]='';
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($conf_exten)<1) || (strlen($campaign)<1) || (strlen($ext_context)<1) || (strlen($phone_number)<1) || (strlen($lead_id)<1) )
	{
		$channel_live=0;
		echo " CALL NOT PLACED\n";
		echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context is not valid\n";
		exit;
	}
	else
	{
		### prepare variables to place manual call from VICIDiaL
		$CCID_on=0;   $CCID='';
		$local_DEF = 'Local/';
		$local_AMP = '@';
		$Local_out_prefix = '9';
		$Local_dial_timeout = '60';
		$Local_persist = '/n';
		if ($dial_timeout > 4) {$Local_dial_timeout = $dial_timeout;}
		$Local_dial_timeout = ($Local_dial_timeout * 1000);
		if (strlen($dial_prefix) > 0) {$Local_out_prefix = "$dial_prefix";}
		if (strlen($campaign_cid) > 6) {$CCID = "$campaign_cid";   $CCID_on++;}
		if (eregi("x",$dial_prefix)) {$Local_out_prefix = '';}

		$PADlead_id = sprintf("%09s", $lead_id);
			while (strlen($PADlead_id) > 9) {$PADlead_id = substr("$PADlead_id", 0, -1);}

		# Create unique calleridname to track the call: MmmddhhmmssLLLLLLLLL
			$MqueryCID = "M$CIDdate$PADlead_id";
		if ($CCID_on) {$CIDstring = "\"$MqueryCID\" <$CCID>";}
		else {$CIDstring = "$MqueryCID";}

		### whether to omit phone_code or not
		if (eregi('Y',$omit_phone_code)) 
			{$Ndialstring = "$Local_out_prefix$phone_number";}
		else
			{$Ndialstring = "$Local_out_prefix$phone_code$phone_number";}
		### insert the call action into the vicidial_manager table to initiate the call
		#	$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $conf_exten','Context: $ext_context','Channel: $local_DEF$Local_out_prefix$phone_code$phone_number$local_AMP$ext_context','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','');";
		$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $Ndialstring','Context: $ext_context','Channel: $local_DEF$conf_exten$local_AMP$ext_context$Local_persist','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','');";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);

		echo "$MqueryCID\n";
	}
}


################################################################################
### manDiaLlookCALL - for manual VICIDiaL dialing this will attempt to look up
###                   the trunk channel that the call was placed on
################################################################################
if ($ACTION == 'manDiaLlookCaLL')
{
	$MT[0]='';
	$row='';   $rowx='';
if (strlen($MDnextCID)<18)
	{
	echo "NO\n";
	echo "MDnextCID $MDnextCID is not valid\n";
	exit;
	}
else
	{
	##### look for the channel in the UPDATED vicidial_manager record of the call initiation
	$stmt="SELECT uniqueid,channel FROM vicidial_manager where callerid='$MDnextCID' and server_ip='$server_ip' and status='UPDATED' LIMIT 1;";
	$rslt=mysql_query($stmt, $link);
	if ($DB) {echo "$stmt\n";}
	$VM_mancall_ct = mysql_num_rows($rslt);
	if ($VM_mancall_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$uniqueid =$row[0];
		$channel =$row[1];
		echo "$uniqueid\n$channel";

		$wait_sec=0;
		$stmt = "select wait_epoch,wait_sec from vicidial_agent_log where agent_log_id='$agent_log_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$VDpr_ct = mysql_num_rows($rslt);
		if ($VDpr_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$wait_sec = (($StarTtime - $row[0]) + $row[1]);
			}
		$stmt="UPDATE vicidial_agent_log set wait_sec='$wait_sec',wait_epoch='$StarTtime',talk_epoch='$StarTtime',lead_id='$lead_id' where agent_log_id='$agent_log_id';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		}
	else
		{
		echo "NO\n";
		}
	}
}



################################################################################
### manDiaLlogCALL - for manual VICIDiaL logging of calls places record in
###                  vicidial_log and then sends process to call_log entry
################################################################################
if ($ACTION == 'manDiaLlogCaLL')
{
	$MT[0]='';
	$row='';   $rowx='';

if ($stage == "start")
	{
	if ( (strlen($uniqueid)<1) || (strlen($lead_id)<1) || (strlen($list_id)<1) || (strlen($phone_number)<1) || (strlen($campaign)<1) )
		{
		echo "LOG NOT ENTERED\n";
		echo "uniqueid $uniqueid or lead_id: $lead_id or list_id: $list_id or phone_number: $phone_number or campaign: $campaign is not valid\n";
		exit;
		}
	else
		{
		##### insert log into vicidial_log for manual VICIDiaL call
		$stmt="INSERT INTO vicidial_log (uniqueid,lead_id,list_id,campaign_id,call_date,start_epoch,status,phone_code,phone_number,user,comments,processed) values('$uniqueid','$lead_id','$list_id','$campaign','$NOW_TIME','$StarTtime','INCALL','$phone_code','$phone_number','$user','MANUAL','N');";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$affected_rows = mysql_affected_rows($link);

		if ($affected_rows > 0)
			{
			echo "VICIDiaL_LOG Inserted: $uniqueid|$channel|$NOW_TIME\n";
			echo "$StarTtime\n";
			}
		else
			{
			echo "LOG NOT ENTERED\n";
			}

	#	##### insert log into call_log for manual VICIDiaL call
	#	$stmt = "INSERT INTO call_log (uniqueid,channel,server_ip,extension,number_dialed,caller_code,start_time,start_epoch) values('$uniqueid','$channel','$server_ip','$exten','$phone_code$phone_number','MD $user $lead_id','$NOW_TIME','$StarTtime')";
	#	if ($DB) {echo "$stmt\n";}
	#	$rslt=mysql_query($stmt, $link);
	#	$affected_rows = mysql_affected_rows($link);

	#	if ($affected_rows > 0)
	#		{
	#		echo "CALL_LOG Inserted: $uniqueid|$channel|$NOW_TIME";
	#		}
	#	else
	#		{
	#		echo "LOG NOT ENTERED\n";
	#		}
		}
	}

if ($stage == "end")
	{
	if ( (strlen($uniqueid)<1) || (strlen($lead_id)<1) )
		{
		echo "LOG NOT ENTERED\n";
		echo "uniqueid $uniqueid or lead_id: $lead_id is not valid\n";
		exit;
		}
	else
		{
		if ($start_epoch < 1000)
			{
			if (eregi("CLOSER",$campaign))
				{
				##### look for the channel in the UPDATED vicidial_manager record of the call initiation
				$stmt="SELECT start_epoch FROM vicidial_closer_log where phone_number='$phone_number' and lead_id='$lead_id' and user='$user';";
				}
			else
				{
				##### look for the channel in the UPDATED vicidial_manager record of the call initiation
				$stmt="SELECT start_epoch FROM vicidial_log where uniqueid='$uniqueid' and lead_id='$lead_id';";
				}
			$rslt=mysql_query($stmt, $link);
			if ($DB) {echo "$stmt\n";}
			$VM_mancall_ct = mysql_num_rows($rslt);
			if ($VM_mancall_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$start_epoch =$row[0];
				$length_in_sec = ($StarTtime - $start_epoch);
				}
			else
				{
				$length_in_sec = 0;
				}
			}
		else {$length_in_sec = ($StarTtime - $start_epoch);}
		
		if (eregi("CLOSER",$campaign))
			{
			$stmt = "UPDATE vicidial_closer_log set end_epoch='$StarTtime', length_in_sec='$length_in_sec',status='DONE' where lead_id='$lead_id' order by start_epoch desc limit 1;";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			}
		if ($auto_dial_level > 0)
			{
			### delete call record from  vicidial_auto_calls
			$stmt = "DELETE from vicidial_auto_calls where uniqueid='$uniqueid';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);

			$stmt = "UPDATE vicidial_live_agents set status='PAUSED',lead_id='',uniqueid=0,callerid='',channel='',call_server_ip='',last_call_finish='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			}

		##### look for the channel in the UPDATED vicidial_manager record of the call initiation
		$stmt="UPDATE vicidial_log set end_epoch='$StarTtime', length_in_sec='$length_in_sec' where uniqueid='$uniqueid' and lead_id='$lead_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$affected_rows = mysql_affected_rows($link);

		if ($affected_rows > 0)
			{
			echo "$uniqueid\n$channel\n";
			}
		else
			{
			echo "LOG NOT ENTERED\n\n";
			}
		}

	echo $VDstop_rec_after_each_call . '|' . $extension . '|' . $conf_silent_prefix . '|' . $conf_exten . '|' . $user_abb . "|\n";

	##### if VICIDiaL call and hangup_after_each_call activated, find all recording 
	##### channels and hang them up while entering info into recording_log and 
	##### returning filename/recordingID
	if ($VDstop_rec_after_each_call == 1)
		{
		$local_DEF = 'Local/';
		$local_AMP = '@';
		$total_rec=0;
		$loop_count=0;
		$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and extension = '$conf_exten' order by channel desc;";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		if ($rslt) {$rec_list = mysql_num_rows($rslt);}
			while ($rec_list>$loop_count)
			{
			$row=mysql_fetch_row($rslt);
			if (preg_match("/Local\/$conf_silent_prefix$conf_exten\@/i",$row[0]))
				{
				$rec_channels[$total_rec] = "$row[0]";
				$total_rec++;
				}
			if ($format=='debug') {echo "\n<!-- $row[0] -->";}
			$loop_count++; 
			}

		$total_recFN=0;
		$loop_count=0;
		$filename=$MT;		# not necessary : and cmd_line_f LIKE \"%_$user_abb\"
		$stmt="SELECT cmd_line_f FROM vicidial_manager where server_ip='$server_ip' and action='Originate' and cmd_line_b = 'Channel: $local_DEF$conf_silent_prefix$conf_exten$local_AMP$ext_context' order by entry_date desc limit $total_rec;";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		if ($rslt) {$recFN_list = mysql_num_rows($rslt);}
			while ($recFN_list>$loop_count)
			{
			$row=mysql_fetch_row($rslt);
			$filename[$total_recFN] = preg_replace("/Callerid: /i","",$row[0]);
			if ($format=='debug') {echo "\n<!-- $row[0] -->";}
			$total_recFN++;
			$loop_count++; 
			}

		$loop_count=0;
		while($loop_count < $total_rec)
			{
			if (strlen($rec_channels[$loop_count])>5)
				{
				$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','RH12345$StarTtime$loop_count','Channel: $rec_channels[$loop_count]','','','','','','','','','');";
					if ($format=='debug') {echo "\n<!-- $stmt -->";}
				$rslt=mysql_query($stmt, $link);

				echo "REC_STOP|$rec_channels[$loop_count]|$filename[$loop_count]|";
				if (strlen($filename)>2)
					{
					$stmt="SELECT recording_id,start_epoch FROM recording_log where filename='$filename[$loop_count]'";
						if ($format=='debug') {echo "\n<!-- $stmt -->";}
					$rslt=mysql_query($stmt, $link);
					if ($rslt) {$fn_count = mysql_num_rows($rslt);}
					if ($fn_count)
						{
						$row=mysql_fetch_row($rslt);
						$recording_id = $row[0];
						$start_time = $row[1];

						$length_in_sec = ($StarTtime - $start_time);
						$length_in_min = ($length_in_sec / 60);
						$length_in_min = sprintf("%8.2f", $length_in_min);

						$stmt="UPDATE recording_log set end_time='$NOW_TIME',end_epoch='$StarTtime',length_in_sec=$length_in_sec,length_in_min='$length_in_min' where filename='$filename[$loop_count]' and end_epoch is NULL;";
							if ($format=='debug') {echo "\n<!-- $stmt -->";}
						$rslt=mysql_query($stmt, $link);

						echo "$recording_id|$length_in_min|";
						}
					else {echo "||";}
					}
				else {echo "||";}
				echo "\n";
				}
			$loop_count++;
			}
		}


	$talk_sec=0;
	$StarTtime = date("U");
	$stmt = "select talk_epoch,talk_sec from vicidial_agent_log where agent_log_id='$agent_log_id';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$VDpr_ct = mysql_num_rows($rslt);
	if ($VDpr_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$talk_sec = (($StarTtime - $row[0]) + $row[1]);
		}
	$stmt="UPDATE vicidial_agent_log set talk_sec='$talk_sec',talk_epoch='$StarTtime',dispo_epoch='$StarTtime' where agent_log_id='$agent_log_id';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);

	}
}


################################################################################
### VDADREcheckINCOMING - for auto-dial VICIDiaL dialing this will recheck for
###                       calls to see if the channel has updated
################################################################################
if ($ACTION == 'VDADREcheckINCOMING')
{
	$MT[0]='';
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($campaign)<1) || (strlen($server_ip)<1) || (strlen($lead_id)<1) )
	{
	$channel_live=0;
	echo "0\n";
	echo "Campaign $campaign is not valid\n";
	echo "lead_id $lead_id is not valid\n";
	exit;
	}
	else
	{
	### grab the call and lead info from the vicidial_live_agents table
	$stmt = "SELECT lead_id,uniqueid,callerid,channel,call_server_ip FROM vicidial_live_agents where server_ip = '$server_ip' and user='$user' and campaign_id='$campaign' and lead_id='$lead_id';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$queue_leadID_ct = mysql_num_rows($rslt);

	if ($queue_leadID_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$lead_id	=$row[0];
		$uniqueid	=$row[1];
		$callerid	=$row[2];
		$channel	=$row[3];
		$call_server_ip	=$row[4];
			if (strlen($call_server_ip)<7) {$call_server_ip = $server_ip;}
		echo "1\n" . $lead_id . '|' . $uniqueid . '|' . $callerid . '|' . $channel . '|' . $call_server_ip . "|\n";
		}
	}
}


################################################################################
### VDADcheckINCOMING - for auto-dial VICIDiaL dialing this will check for calls
###                     in the vicidial_live_agents table in QUEUE status, then
###                     lookup the lead info and pass it back to vicidial.php
################################################################################
if ($ACTION == 'VDADcheckINCOMING')
{
	$MT[0]='';
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($campaign)<1) || (strlen($server_ip)<1) )
	{
	$channel_live=0;
	echo "0\n";
	echo "Campaign $campaign is not valid\n";
	exit;
	}
	else
	{
	### grab the call and lead info from the vicidial_live_agents table
	$stmt = "SELECT lead_id,uniqueid,callerid,channel,call_server_ip FROM vicidial_live_agents where server_ip = '$server_ip' and user='$user' and campaign_id='$campaign' and status='QUEUE';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$queue_leadID_ct = mysql_num_rows($rslt);

	if ($queue_leadID_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$lead_id	=$row[0];
		$uniqueid	=$row[1];
		$callerid	=$row[2];
		$channel	=$row[3];
		$call_server_ip	=$row[4];
			if (strlen($call_server_ip)<7) {$call_server_ip = $server_ip;}
		echo "1\n" . $lead_id . '|' . $uniqueid . '|' . $callerid . '|' . $channel . '|' . $call_server_ip . "|\n";

		### update the agent status to INCALL in vicidial_live_agents
		$stmt = "UPDATE vicidial_live_agents set status='INCALL',last_call_time='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);

		##### grab the data from vicidial_list for the lead_id
		$stmt="SELECT * FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		$list_lead_ct = mysql_num_rows($rslt);
		if ($list_lead_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
		#	$lead_id		= trim("$row[0]");
			$dispo			= trim("$row[3]");
			$tsr			= trim("$row[4]");
			$vendor_id		= trim("$row[5]");
			$list_id		= trim("$row[7]");
			$gmt_offset_now	= trim("$row[8]");
			$phone_code		= trim("$row[10]");
			$phone_number	= trim("$row[11]");
			$title			= trim("$row[12]");
			$first_name		= trim("$row[13]");
			$middle_initial	= trim("$row[14]");
			$last_name		= trim("$row[15]");
			$address1		= trim("$row[16]");
			$address2		= trim("$row[17]");
			$address3		= trim("$row[18]");
			$city			= trim("$row[19]");
			$state			= trim("$row[20]");
			$province		= trim("$row[21]");
			$postal_code	= trim("$row[22]");
			$country_code	= trim("$row[23]");
			$gender			= trim("$row[24]");
			$date_of_birth	= trim("$row[25]");
			$alt_phone		= trim("$row[26]");
			$email			= trim("$row[27]");
			$security		= trim("$row[28]");
			$comments		= trim("$row[29]");
			$called_count	= trim("$row[30]");
			}

		### update the lead status to INCALL
		$stmt = "UPDATE vicidial_list set status='INCALL', user='$user' where lead_id='$lead_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);

		### update the log status to INCALL
		$stmt = "UPDATE vicidial_log set user='$user', comments='AUTO', list_id='$list_id', status='INCALL' where lead_id='$lead_id' and uniqueid='$uniqueid';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);

		if (eregi("CLOSER",$campaign))
			{
			### update the vicidial_closer_log user to INCALL
			$stmt = "UPDATE vicidial_closer_log set user='$user', comments='AUTO', list_id='$list_id', status='INCALL' where lead_id='$lead_id' order by closecallid desc limit 1;";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);

			$stmt = "select campaign_id from vicidial_auto_calls where callerid = '$callerid' order by call_time desc limit 1;";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$VDAC_cid_ct = mysql_num_rows($rslt);
			if ($list_lead_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$VDADchannel_group	=$row[0];
				}

			$stmt = "select count(*) from vicidial_log where lead_id='$lead_id' and uniqueid='$uniqueid';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$VDL_cid_ct = mysql_num_rows($rslt);
			if ($VDL_cid_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$VDCL_front_VDlog	=$row[0];
				}

			$stmt = "select * from vicidial_inbound_groups where group_id='$VDADchannel_group';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$VDIG_cid_ct = mysql_num_rows($rslt);
			if ($VDIG_cid_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$VDCL_group_name		= $row[1];
				$VDCL_group_color		= $row[2];
				$VDCL_group_web			= $row[4];
				$VDCL_fronter_display	= $row[7];
				$VDCL_ingroup_script	= $row[8];
				$VDCL_get_call_launch	= $row[9];
				$VDCL_xferconf_a_dtmf	= $row[10];
				$VDCL_xferconf_a_number	= $row[11];
				$VDCL_xferconf_b_dtmf	= $row[12];
				$VDCL_xferconf_b_number	= $row[13];
				}
			else
				{
				$stmt = "select campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number from vicidial_campaigns where campaign_id='$VDADchannel_group';";
				if ($DB) {echo "$stmt\n";}
				$rslt=mysql_query($stmt, $link);
				$VDIG_cid_ct = mysql_num_rows($rslt);
				if ($VDIG_cid_ct > 0)
					{
					$row=mysql_fetch_row($rslt);
					$VDCL_ingroup_script	= $row[0];
					$VDCL_get_call_launch	= $row[1];
					$VDCL_xferconf_a_dtmf	= $row[2];
					$VDCL_xferconf_a_number	= $row[3];
					$VDCL_xferconf_b_dtmf	= $row[4];
					$VDCL_xferconf_b_number	= $row[5];
					}
				}

			### if web form is set then send on to vicidial.php for override of WEB_FORM address
			if (strlen($VDCL_group_web)>5) {echo "$VDCL_group_web|$VDCL_group_name|$VDCL_group_color|$VDCL_fronter_display|$VDADchannel_group|$VDCL_ingroup_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|\n";}
			else {echo "|$VDCL_group_name|$VDCL_group_color|$VDCL_fronter_display|$VDADchannel_group|$VDCL_ingroup_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|\n";}

			$stmt = "SELECT full_name from vicidial_users where user='$tsr';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$VDU_cid_ct = mysql_num_rows($rslt);
			if ($VDU_cid_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$fronter_full_name		= $row[0];
				echo $fronter_full_name . '|' . $tsr . "\n";
				}
			else {echo '|' . $tsr . "\n";}
			}
		else 
			{
			$stmt = "select campaign_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number from vicidial_campaigns where campaign_id='$campaign';";
			if ($DB) {echo "$stmt\n";}
			$rslt=mysql_query($stmt, $link);
			$VDIG_cid_ct = mysql_num_rows($rslt);
			if ($VDIG_cid_ct > 0)
				{
				$row=mysql_fetch_row($rslt);
				$VDCL_campaign_script	= $row[0];
				$VDCL_get_call_launch	= $row[1];
				$VDCL_xferconf_a_dtmf	= $row[2];
				$VDCL_xferconf_a_number	= $row[3];
				$VDCL_xferconf_b_dtmf	= $row[4];
				$VDCL_xferconf_b_number	= $row[5];
				}
			echo "|||||$VDCL_campaign_script|$VDCL_get_call_launch|$VDCL_xferconf_a_dtmf|$VDCL_xferconf_a_number|$VDCL_xferconf_b_dtmf|$VDCL_xferconf_b_number|\n|\n";
			}

		$comments = eregi_replace("\r",'',$comments);
		$comments = eregi_replace("\n",'!N',$comments);

		$LeaD_InfO =	$callerid . "\n";
		$LeaD_InfO .=	$lead_id . "\n";
		$LeaD_InfO .=	$dispo . "\n";
		$LeaD_InfO .=	$tsr . "\n";
		$LeaD_InfO .=	$vendor_id . "\n";
		$LeaD_InfO .=	$list_id . "\n";
		$LeaD_InfO .=	$gmt_offset_now . "\n";
		$LeaD_InfO .=	$phone_code . "\n";
		$LeaD_InfO .=	$phone_number . "\n";
		$LeaD_InfO .=	$title . "\n";
		$LeaD_InfO .=	$first_name . "\n";
		$LeaD_InfO .=	$middle_initial . "\n";
		$LeaD_InfO .=	$last_name . "\n";
		$LeaD_InfO .=	$address1 . "\n";
		$LeaD_InfO .=	$address2 . "\n";
		$LeaD_InfO .=	$address3 . "\n";
		$LeaD_InfO .=	$city . "\n";
		$LeaD_InfO .=	$state . "\n";
		$LeaD_InfO .=	$province . "\n";
		$LeaD_InfO .=	$postal_code . "\n";
		$LeaD_InfO .=	$country_code . "\n";
		$LeaD_InfO .=	$gender . "\n";
		$LeaD_InfO .=	$date_of_birth . "\n";
		$LeaD_InfO .=	$alt_phone . "\n";
		$LeaD_InfO .=	$email . "\n";
		$LeaD_InfO .=	$security . "\n";
		$LeaD_InfO .=	$comments . "\n";
		$LeaD_InfO .=	$called_count . "\n";

		echo $LeaD_InfO;



		$wait_sec=0;
		$StarTtime = date("U");
		$stmt = "select wait_epoch,wait_sec from vicidial_agent_log where agent_log_id='$agent_log_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$VDpr_ct = mysql_num_rows($rslt);
		if ($VDpr_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$wait_sec = (($StarTtime - $row[0]) + $row[1]);
			}
		$stmt="UPDATE vicidial_agent_log set wait_sec='$wait_sec',talk_epoch='$StarTtime',lead_id='$lead_id' where agent_log_id='$agent_log_id';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);

		### If CALLBK, change vicidial_callback record to INACTIVE
		if (eregi("CALLBK|CBHOLD", $dispo))
			{
			$stmt="UPDATE vicidial_callbacks set status='INACTIVE' where lead_id='$lead_id' and status NOT IN('INACTIVE','DEAD','ARCHIVE');";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			}
		}
		else
		{
		echo "0\n";
	#	echo "No calls in QUEUE for $user on $server_ip\n";
		exit;
		}
	}
}


################################################################################
### userLOGout - Logs the user out of VICIDiaL client, deleting db records and 
###              inserting into vicidial_user_log
################################################################################
if ($ACTION == 'userLOGout')
{
	$MT[0]='';
	$row='';   $rowx='';
if ( (strlen($campaign)<1) || (strlen($conf_exten)<1) )
	{
	echo "NO\n";
	echo "campaign $campaign or conf_exten $conf_exten is not valid\n";
	exit;
	}
else
	{
	##### Insert a LOGOUT record into the user log
	$stmt="INSERT INTO vicidial_user_log values('','$user','LOGOUT','$campaign','$NOW_TIME','$StarTtime');";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$vul_insert = mysql_affected_rows($link);

	if ($no_delete_sessions < 1)
		{
		##### Remove the reservation on the vicidial_conferences meetme room
		$stmt="UPDATE vicidial_conferences set extension='' where server_ip='$server_ip' and conf_exten='$conf_exten';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$vc_remove = mysql_affected_rows($link);
		}

	##### Delete the vicidial_live_agents record for this session
	$stmt="DELETE from vicidial_live_agents where server_ip='$server_ip' and user ='$user';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$vla_delete = mysql_affected_rows($link);

	##### Delete the web_client_sessions
	$stmt="DELETE from web_client_sessions where server_ip='$server_ip' and session_name ='$session_name';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$wcs_delete = mysql_affected_rows($link);

	##### Hangup the client phone
	$stmt="SELECT channel FROM live_sip_channels where server_ip = '$server_ip' and channel LIKE \"$protocol/$extension%\" order by channel desc;";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);
	if ($rslt) 
		{
		$row=mysql_fetch_row($rslt);
		$agent_channel = "$row[0]";
		if ($format=='debug') {echo "\n<!-- $row[0] -->";}
		$stmt="INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Hangup','RH123459$StarTtime','Channel: $agent_channel','','','','','','','','','');";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		}

	echo "$vul_insert|$vc_remove|$vla_delete|$wcs_delete|$agent_channel\n";
	}
}


################################################################################
### updateDISPO - update the vicidial_list table to reflect the agent choice of
###               call disposition for that leand
################################################################################
if ($ACTION == 'updateDISPO')
{
	$MT[0]='';
	$row='';   $rowx='';
	if ( (strlen($dispo_choice)<1) || (strlen($lead_id)<1) )
	{
	echo "Dispo Choice $dispo or lead_id $lead_id is not valid\n";
	exit;
	}
	else
	{
	$stmt="UPDATE vicidial_list set status='$dispo_choice', user='$user' where lead_id='$lead_id';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);

	$stmt="UPDATE vicidial_log set status='$dispo_choice' where lead_id='$lead_id' and user='$user' order by uniqueid desc limit 1;";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);

		if ( ($use_internal_dnc=='Y') and ($dispo_choice=='DNC') )
		{
		$stmt = "select phone_number from vicidial_list where lead_id='$lead_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
		$stmt="INSERT INTO vicidial_dnc (phone_number) values('$row[0]');";
		$rslt=mysql_query($stmt, $link);
		if ($DB) {echo "$stmt\n";}
		}
	}

	$dispo_sec=0;
	$StarTtime = date("U");
	$stmt = "select dispo_epoch,dispo_sec from vicidial_agent_log where agent_log_id='$agent_log_id';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$VDpr_ct = mysql_num_rows($rslt);
	if ($VDpr_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$dispo_sec = (($StarTtime - $row[0]) + $row[1]);
		}
	$stmt="UPDATE vicidial_agent_log set dispo_sec='$dispo_sec',dispo_epoch='$StarTtime',status='$dispo_choice' where agent_log_id='$agent_log_id';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);

	$stmt="INSERT INTO vicidial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch) values('$user','$server_ip','$NOW_TIME','$campaign','$StarTtime','0','$StarTtime');";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$affected_rows = mysql_affected_rows($link);
	$agent_log_id = mysql_insert_id();

	### CALLBACK ENTRY
	if ( ($dispo_choice == 'CBHOLD') and (strlen($CallBackDatETimE)>10) )
		{
		$stmt="INSERT INTO vicidial_callbacks (lead_id,list_id,campaign_id,status,entry_time,callback_time,user,recipient,comments) values('$lead_id','$list_id','$campaign','ACTIVE','$NOW_TIME','$CallBackDatETimE','$user','$recipient','$comments');";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		}

	echo 'Lead ' . $lead_id . ' has been changed to ' . $dispo_choice . " Status\nNext agent_log_id:\n" . $agent_log_id . "\n";
}

################################################################################
### updateLEAD - update the vicidial_list table to reflect the values that are
###              in the agents screen at time of call hangup
################################################################################
if ($ACTION == 'updateLEAD')
{
	$MT[0]='';
	$row='';   $rowx='';
	if ( (strlen($phone_number)<1) || (strlen($lead_id)<1) )
	{
	echo "phone_number $phone_number or lead_id $lead_id is not valid\n";
	exit;
	}
	else
	{
	$comments = eregi_replace("\r",'',$comments);
	$comments = eregi_replace("\n",'!N',$comments);

	$stmt="UPDATE vicidial_list set vendor_lead_code='" . mysql_real_escape_string($vendor_lead_code) . "', title='" . mysql_real_escape_string($title) . "', first_name='" . mysql_real_escape_string($first_name) . "', middle_initial='" . mysql_real_escape_string($middle_initial) . "', last_name='" . mysql_real_escape_string($last_name) . "', address1='" . mysql_real_escape_string($address1) . "', address2='" . mysql_real_escape_string($address2) . "', address3='" . mysql_real_escape_string($address3) . "', city='" . mysql_real_escape_string($city) . "', state='" . mysql_real_escape_string($state) . "', province='" . mysql_real_escape_string($province) . "', postal_code='" . mysql_real_escape_string($postal_code) . "', country_code='" . mysql_real_escape_string($country_code) . "', gender='" . mysql_real_escape_string($gender) . "', date_of_birth='" . mysql_real_escape_string($date_of_birth) . "', alt_phone='" . mysql_real_escape_string($alt_phone) . "', email='" . mysql_real_escape_string($email) . "', security_phrase='" . mysql_real_escape_string($security_phrase) . "', comments='" . mysql_real_escape_string($comments) . "' where lead_id='$lead_id';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);

	$random = (rand(1000000, 9999999) + 10000000);
	$stmt="UPDATE vicidial_live_agents set random_id='$random' where user='$user' and server_ip='$server_ip';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);

	}
	echo "Lead $lead_id information has been updated\n";
}


################################################################################
### VDADpause - update the vicidial_live_agents table to show that the agent is
###  or ready   now active and ready to take calls
################################################################################
if ( ($ACTION == 'VDADpause') || ($ACTION == 'VDADready') )
{
	$MT[0]='';
	$row='';   $rowx='';
	if ( (strlen($stage)<2) || (strlen($server_ip)<1) )
	{
	echo "stage $stage is not valid\n";
	exit;
	}
	else
	{
	$random = (rand(1000000, 9999999) + 10000000);
	$stmt="UPDATE vicidial_live_agents set status='$stage',lead_id='',uniqueid=0,callerid='',channel='', random_id='$random' where user='$user' and server_ip='$server_ip';";
		if ($format=='debug') {echo "\n<!-- $stmt -->";}
	$rslt=mysql_query($stmt, $link);

	if ($agent_log == 'NO')
		{$donothing=1;}
	else
		{
		$pause_sec=0;
		$stmt = "select pause_epoch,pause_sec from vicidial_agent_log where agent_log_id='$agent_log_id';";
		if ($DB) {echo "$stmt\n";}
		$rslt=mysql_query($stmt, $link);
		$VDpr_ct = mysql_num_rows($rslt);
		if ($VDpr_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$pause_sec = (($StarTtime - $row[0]) + $row[1]);
			}
		if ($ACTION == 'VDADready')
			{
			$stmt="UPDATE vicidial_agent_log set pause_sec='$pause_sec',wait_epoch='$StarTtime' where agent_log_id='$agent_log_id';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			}
		if ($ACTION == 'VDADpause')
			{
			$stmt="UPDATE vicidial_agent_log set pause_sec='$pause_sec',pause_epoch='$StarTtime' where agent_log_id='$agent_log_id';";
				if ($format=='debug') {echo "\n<!-- $stmt -->";}
			$rslt=mysql_query($stmt, $link);
			}
		}
	}
	echo 'Agent ' . $user . ' is now in status ' . $stage . "\n";
}


################################################################################
### UpdatEFavoritEs - update the astguiclient favorites list for this extension
################################################################################
if ($ACTION == 'UpdatEFavoritEs')
{
	$row='';   $rowx='';
	$channel_live=1;
	if ( (strlen($favorites_list)<1) || (strlen($user)<1) || (strlen($exten)<1) )
	{
	echo "favorites list $favorites_list is not valid\n";
	exit;
	}
	else
	{
	$stmt = "select count(*) from phone_favorites where extension='$exten' and server_ip='$server_ip';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);

	if ($row[0] > 0)
		{
		$stmt="UPDATE phone_favorites set extensions_list=\"$favorites_list\" where extension='$exten' and server_ip='$server_ip';";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		}
	else
		{
		$stmt="INSERT INTO phone_favorites values('$exten','$server_ip',\"$favorites_list\");";
			if ($format=='debug') {echo "\n<!-- $stmt -->";}
		$rslt=mysql_query($stmt, $link);
		}
	}
	echo "Favorites list has been updated to $favorites_list for $exten\n";
}


################################################################################
### CalLBacKLisT - List the USERONLY callbacks for an agent
################################################################################
if ($ACTION == 'CalLBacKLisT')
{
$stmt = "select callback_id,lead_id,campaign_id,status,entry_time,callback_time,comments from vicidial_callbacks where recipient='USERONLY' and user='$user' and status NOT IN('INACTIVE','DEAD') order by callback_time;";
if ($DB) {echo "$stmt\n";}
$rslt=mysql_query($stmt, $link);
if ($rslt) {$callbacks_count = mysql_num_rows($rslt);}
echo "$callbacks_count\n";
$loop_count=0;
	while ($callbacks_count>$loop_count)
	{
	$row=mysql_fetch_row($rslt);
	$callback_id[$loop_count]	= $row[0];
	$lead_id[$loop_count]		= $row[1];
	$campaign_id[$loop_count]	= $row[2];
	$status[$loop_count]		= $row[3];
	$entry_time[$loop_count]	= $row[4];
	$callback_time[$loop_count]	= $row[5];
	$comments[$loop_count]		= $row[6];
	$loop_count++;
	}
$loop_count=0;
	while ($callbacks_count>$loop_count)
	{
	$stmt = "select first_name,last_name,phone_number from vicidial_list where lead_id='$lead_id[$loop_count]';";
	if ($DB) {echo "$stmt\n";}
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);

	echo "$row[0] ~$row[1] ~$row[2] ~$callback_id[$loop_count] ~$lead_id[$loop_count] ~$campaign_id[$loop_count] ~$status[$loop_count] ~$entry_time[$loop_count] ~$callback_time[$loop_count] ~$comments[$loop_count]\n";
	$loop_count++;
	}

}


################################################################################
### CalLBacKCounT - send the count of the USERONLY callbacks for an agent
################################################################################
if ($ACTION == 'CalLBacKCounT')
{
$stmt = "select count(*) from vicidial_callbacks where recipient='USERONLY' and user='$user' and status NOT IN('INACTIVE','DEAD');";
if ($DB) {echo "$stmt\n";}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$cbcount=$row[0];

echo "$cbcount";
}




if ($format=='debug') 
{
$ENDtime = date("U");
$RUNtime = ($ENDtime - $StarTtime);
echo "\n<!-- script runtime: $RUNtime seconds -->";
echo "\n</body>\n</html>\n";
}
	
exit; 

?>
