<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/



/********************************************************
Defaults:
1 - Yes/On/True
0 - No/Off/False

*/
define("yes",1);
define("no",0);
/*
********************************************************
_ Please attention _:
The temporary files will be stored on this folder
For security reasons, do not use web-shared folders

** The Web Server needs write-permission on this folder

* Unix/Linux users use.
/tmp/uebimiau
* Win32 users
c:/winnt/temp/uebimiau

********************************************************/
$temporary_directory = "temporary_files/";

/********************************************************
Your local SMTP Server (alias or IP) such as "smtp.yourdomain.com"
eg. "server1;server2;server3"   -> specify main and backup server
********************************************************/
$smtp_server = "localhost";  //YOU NEED CHANGE IT !!

/********************************************************
The TIME ZONE of server, format (+|-)HHMM (H=hours, M=minutes), eg. +0100
********************************************************/

$server_time_zone = "-0000";


/********************************************************
The maximum size for stored files
In order to keep you system fast, use values better than 5MB
If you need disable it, set the value to 0 or leave it blank
********************************************************/
$quota_limit = 4096;  //  in KB, eg. 2048 Kb = 2MB


/********************************************************
Use SMTP password (AUTH LOGIN type)
********************************************************/
$use_password_for_smtp = no;

/********************************************************
For qmail and others POP3/SMTP servers that uses user@domain
as username (full email address).
$use_email_as_user_pop3 is used only for POP3 authentications
$use_email_as_user_smtp is used only for SMTP authentications
********************************************************/
$use_email_as_user_pop3 = no;

$use_email_as_user_smtp = no;


/********************************************************
Redirect new users to the preferences page in the first login
********************************************************/
$check_first_login                = yes;



/********************************************************
Your local POP3 Servers, ____ OPTIONAL ___
********************************************************/

// 1st
//$pop3_servers[0]["domain"] = "forbiz.co.kr";
//$pop3_servers[0]["server"] = "mail.forbiz.co.kr";

$pop3_servers[0]["domain"] = "redsun.co.kr";
$pop3_servers[0]["server"] = "mail.redsun.co.kr";

// 2nd

//$pop3_servers[1]["domain"] = "YOURDOMAIN2.COM";
//$pop3_servers[1]["server"] = "mail.YOURDOMAIN2.COM";

// Nnd
//$pop3_servers[2]["domain"] = "YOURDOMAIN3.COM";
//$pop3_servers[2]["server"] = "mail.YOURDOMAIN3.COM";


// Nnd
//$pop3_servers[3]["domain"] = "YOURDOMAIN4.COM";
//$pop3_servers[3]["server"] = "mail.YOURDOMAIN4.COM";

/********************************************************
Language settings
********************************************************/
$default_language                 = 1;
$allow_user_change                = yes; //allow users select language or theme

$themes[0]["name"]					= "Default (Portugu (Brasil))";
$themes[0]["path"]					= "themes/uebimiau-default"; // without "/"
$themes[0]["language"]				= "langs/pt-BR";

$themes[1]["name"]					= "Default (English)";
$themes[1]["path"]					= "themes/uebimiau-default"; // without "/"
$themes[1]["language"]				= "langs/en";

$themes[2]["name"]					= "CastilloCentral (English)";
$themes[2]["path"]					= "themes/castillocentral"; // without "/"
$themes[2]["language"]				= "langs/en";

$themes[3]["name"]					= "Default (Franis)";
$themes[3]["path"]					= "themes/uebimiau-default"; // without "/"
$themes[3]["language"]				= "langs/fr";

$themes[4]["name"]					= "Default (German)";
$themes[4]["path"]					= "themes/uebimiau-default"; // without "/"
$themes[4]["language"]				= "langs/de";

$themes[5]["name"]					= "Default (Italian)";
$themes[5]["path"]					= "themes/uebimiau-default"; // without "/"
$themes[5]["language"]				= "langs/it";

/********************************************************
Support for SendMail (DEFAULT DISABLED (using SMTP))
Only for *nix Systems (NOT Win32)
********************************************************/
$use_sendmail     = no;
$path_to_sendmail = "/usr/sbin/sendmail";


/********************************************************
In some POP3 servers, if you send a "RETR" command, your
message will be automatically deleted :(
This option prevents this inconvenience
********************************************************/
$pop_use_top = yes;


/********************************************************
Enable visualization of HTML messages (recommended)

*This option afect only incoming messages, the  HTML editor
for new messages (compose page) is automatically activated 
when the client's browser support it
********************************************************/
$mime_show_html = yes;

/********************************************************
Name and Version, it's used in many places, like as
"X-Mailer" field,  footer
********************************************************/
$appversion = "2.5";
$appname = "UebiMiau";


/********************************************************
Add an "footer" to sent mails
********************************************************/

$footer = "

________________________________________________
This mail was sent by $appname $appversion
";

/********************************************************
Enable debug :)
********************************************************/
$enable_debug = no;

/********************************************************
Session timeout for inactivity
********************************************************/
$idle_timeout = 10; //minutes

/********************************************************
Order setting
********************************************************/
$default_sortby = "date";
$default_sortorder = "DESC";

/********************************************************
Default preferences...
********************************************************/
$send_to_trash_default			= yes;      //send deleted messages to trash
$st_only_ready_default			= yes;      //only read messages, otherwise, delete it
$save_to_sent_default			= yes;      //send sent messages to sent
$empty_trash_default			= yes;      //empty trash on logout
$sortby_default					= "date"; //alowed: "attach","subject","fromname","date","size"
$sortorder_default				= "DESC"; //alowed: "ASC","DESC"
$rpp_default					= 20;     // records per page (messages), alowed: 10,20,30,40,50
$add_signature_default			= no;      //add the signature by default
$signature_default				= "";      // a default signature for all users, use text only, with multiple lines if needed
$timezone_default				= "+0000"; // timezone, format (+|-)HHMM (H=hours, M=minutes)
?>