<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/


// load session management
include("./session_management.php");
// check for all parameters


if(	empty($bound) || 
	empty($part) || 
	empty($ix)) die("<script language=\"javascript\">location = 'error.php?msg=".urlencode($error_other)."&sid=$sid&lid=$lid';</script>");

// choose correct session
$sessiontype = ($folder == "inbox")?"headers":"folderheaders";
$mail_info = $sess[$sessiontype][$ix];
$localname = $mail_info["localname"];
// check if the file exists, otherwise, do a error

if (!file_exists($localname)) die("<script language=\"javascript\">location = 'error.php?msg=".urlencode($error_other)."&sid=$sid&lid=$lid';</script>");

// read the email
$email = read_file($localname);
// start a new mime decode class

$md = new mime_decode();
$md->charset 			= $default_char_set;
$md->use_html			= $mime_show_html;
$md->timezone			= $timezone;

// split the mail, body and headers
$email = $md->fetch_structure($email);
$header = $email["header"];
$body = $email["body"];
// split the parsts of email if it have more than one parts
if(!empty($bound)) {
	$parts = $md->split_parts(base64_decode($bound),$body);
	// split the especified part of mail, body and headers
	$email = $md->fetch_structure($parts[$part]);
	$header = $email["header"];
	$body = $email["body"];
}
// check if file will be downloaded
$isdown = (isset($down))?1:0;
// get the attachment
$md->download_attach($header,$body,$isdown);
unset($md);
?>