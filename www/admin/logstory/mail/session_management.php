<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/

@set_time_limit(0);
error_reporting (E_ALL ^E_NOTICE); 


include("./config.php");
include("./class_mime_decode.php");
include("./class_pop3_session.php");
include("./commom_functions.php");


if(empty($sid)) $sid = "{".uniqid("")."-".uniqid("")."-".time()."}";

//Header("Location: ./index.php?sessionerror"); exit;

$sess = load_session();
$start = (empty($sess["start"]))?time():$sess["start"];

$p3 = new pop3_session();

$p3->pop_port 			= 110;


if(strlen($f_user) > 0 && strlen($f_pass) > 0) {

	if(isset($six)) {
		$f_email = "$f_user@".$pop3_servers[$six]["domain"];
		$f_server = $pop3_servers[$six]["server"];
	}

	$sess["email"]  = stripslashes($f_email);
	$sess["user"]   = stripslashes($f_user);
	$sess["pass"]   = stripslashes($f_pass); 
	$sess["server"] = stripslashes($f_server); 
	$sess["start"] = time();


	$p3->pop_user   = $f_user;
	$p3->pop_pass   = $f_pass;
	$p3->pop_email  = $f_email;
	$p3->pop_server = $f_server;

	$refr = 1;

} elseif (
	(!empty($sess["user"]) && !empty($sess["pass"]) && intval((time()-$start)/60) < $idle_timeout)
	|| $ignoresession) {
	$p3->pop_user   = $f_user    = $sess["user"];
	$p3->pop_pass   = $f_pass    = $sess["pass"];
	$p3->pop_server = $f_server  = $sess["server"];
	$p3->pop_email  = $f_email   = $sess["email"];

} else { 
	Header("Location: logout.php?sid=$sid&lid=$lid\r\n"); 
	exit; 
}

$sess["start"] = time();
save_session($sess);

$userfolder = $temporary_directory.ereg_replace("[^A-Za-z0-9\._-]","_",$f_user)."_$f_server/";

$prefs = load_prefs();

$real_name              = $prefs["real-name"];
$reply_to               = $prefs["reply-to"];
$send_to_trash          = $prefs["save-to-trash"];
$st_only_read           = $prefs["st-only-read"];
$empty_trash            = $prefs["empty-trash"];
$save_to_sent           = $prefs["save-to-sent"];
$records_per_page       = $prefs["rpp"];
$signature              = $prefs["signature"];
$add_sig 				= $prefs["add-sig"];
$timezone 				= $prefs["timezone"];

$p3->timezone			= $timezone;
$p3->charset			= $default_char_set;

/*
Don't remove the fallowing lines, or you will be problems with browser's cache 
*/
Header("Expires: Wed, 11 Nov 1998 11:11:11 GMT\r\n".
"Cache-Control: no-cache\r\n".
"Cache-Control: must-revalidate\r\n".
"Pragma: no-cache");

$nocache = "
<META HTTP-EQUIV=\"Cache-Control\" CONTENT=\"no-cache\">
<META HTTP-EQUIV=\"Expires\" CONTENT=\"-1\">
<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">";

// Sort rules


if(!ereg("(attach|subject|fromname|date|size)",$sortby)) {
	$sortby = $prefs["sort-by"];
	if(!ereg("(attach|subject|fromname|date|size)",$sortby))
		$sortby = $default_sortby;
} else {
	$need_save = true;
	$prefs["sort-by"] = $sortby;
}

if(!ereg("ASC|DESC",$sortorder)) {
	$sortorder = $prefs["sort-order"];
	if(!ereg("ASC|DESC",$sortorder))
		$sortorder = $default_sortorder;
} else {
	$need_save = true;
	$prefs["sort-order"] = $sortorder;
}

if(!empty($need_save)) save_prefs($prefs);

if(empty($folder) || !(strpos($folder,".") === false)) 
	$folder = "inbox";
elseif(!file_exists($userfolder.$folder)) { Header("Location: ./logout.php?sid=$sid&lid=$lid"); exit; }

?>
