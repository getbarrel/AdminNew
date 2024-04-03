<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/


include("./session_management.php");
if(!isset($folder) || !isset($ix)) die("Expected parameters");

$mail_info = ($folder == "inbox")?$sess["headers"][$ix]:$sess["folderheaders"][$ix];

$tcontent = load_template($headers_window_template,Array("headers"));
$tcontent = eregi_replace("<!--%UM_TITLE%-->",htmlspecialchars($mail_info["subject"]),$tcontent);

$start = strpos($tcontent,"<!--%UM_LOOP_BEGIN%-->");
$end = strpos($tcontent,"<!--%UM_LOOP_END%-->")+20;

$line = substr($tcontent,$start+22,$end-$start-42);

$md = new mime_decode();
$md->charset 			= $default_char_set;
$md->use_html			= $mime_show_html;
$md->timezone			= $timezone;

$headers = $md->decode_header($mail_info["headers"]);

while(list($key,$val) = each($headers)) {
	$thisline = $line;
	$thisline = eregi_replace("<!--%UM_KEY%-->",UCFirst($key),$thisline);
	$thisline = eregi_replace("<!--%UM_VALUE%-->",htmlspecialchars(stripslashes($val)),$thisline);
	$result .= $thisline;
}

$tcontent = substr($tcontent,0,$start).$result.substr($tcontent,$end);

echo($tcontent);

?>
