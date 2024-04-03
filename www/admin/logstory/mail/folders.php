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

// check and create a new folder
$newfolder = trim($newfolder);
if(!empty($newfolder) && 
	ereg("[A-Za-z0-9\. -]",$newfolder) && 
	!file_exists($userfolder.$newfolder))
	mkdir($userfolder.$newfolder,0700);

// check and delete the especified folder: system folders can not be deleted
if(!empty($delfolder) && 
	$delfolder != "inbox" && 
	$delfolder != "sent" && 
	$delfolder != "trash" && 
	$delfolder != "_attachments" && 
	$delfolder != "_infos" && 
	ereg("[A-Za-z0-9\. -]",$delfolder) &&
	(strpos($delfolder,"..") === false))
	@RmDirR($userfolder.$delfolder);

$jssource = "
<script language=\"JavaScript\">
function newmsg() { location = 'newmsg.php?pag=$pag&folder=".urlencode($folder)."&sid=$sid&lid=$lid'; }
function refreshlist() { location = 'folders.php?folder=".urlencode($folder)."&sid=$sid&lid=$lid'}
function goend() { location = 'logout.php?sid=$sid&lid=$lid'; }
function search() { location = 'search.php?sid=$sid&lid=$lid'; }
function goinbox() { location = 'msglist.php?folder=inbox&sid=$sid&lid=$lid'; }
function emptytrash() {	location = 'folders.php?empty=trash&folder=".urlencode($folder)."&goback=true&sid=$sid&lid=$lid';}
function addresses() { location = 'addressbook.php?sid=$sid&lid=$lid'; }
function prefs() { location = 'preferences.php?sid=$sid&lid=$lid'; }
function create() {
	strPat = /[^A-Za-z0-9\. -]/;
	frm = document.forms[0];
	strName = frm.newfolder.value
	mathArray = strName.match(strPat)
	if(mathArray != null) {
		alert('".ereg_replace("'","\\'",$error_invalid_name)."')
		return false;
	}else
		frm.submit();
}
</script>
";



if(isset($empty)) {
	if($empty=="inbox" && count($sess["headers"]) > 0) {
		$headers = $sess["headers"];

		if(!$p3->pop_connect()) die("<script language=\"javascript\">location = 'error.php?msg=".urlencode($error_connect)."&sid=$sid&lid=$lid';</script>");
		if(!$p3->pop_auth())  die("<script language=\"javascript\">location = 'badlogin.php?sid=$sid&lid=$lid'</script>"); 

		for($i=0;$i<count($headers);$i++) {

			$mail_info = $headers[$i];
			$mnum = $mail_info["id"];
			$mid = md5($mail_info["message-id"]);
			$msize = $mail_info["size"];

			$read = file_exists($mail_info["localname"]);

			$trash = 0;
			if(!empty($send_to_trash)) {
				$trash = 1;
				if($st_only_read && !$read) $trash = 0;
			}
			if(!$p3->pop_dele_msg($mnum,$mid,$msize,$trash)) die("<script language=\"javascript\">location = 'error.php?sid=$sid&lid=$lid&msg=".urlencode($error_deleting)."';</script>");

		}

		$headers = $p3->pop_list_msgs(); 
		$p3->pop_disconnect();

		$sess["headers"] = $headers;
		save_session($sess);

	} else {


		$headers = build_local_list($userfolder.$empty);

		for($i=0;$i<count($headers);$i++) {
			$mail_info = $headers[$i];
			$localname = $mail_info["localname"];
			$mid = md5($mail_info["message-id"]);
			$msize = $mail_info["size"];
			$read = $mail_info["read"];
			$trash = 0;
			if(!empty($send_to_trash)) {
				$trash = 1;
				if($st_only_read && !$read) $trash = 0;
			}
			if($trash && $empty != "trash")
				copy($localname,$userfolder."trash/".basename($localname));
			@unlink($localname);
		}
		if(isset($goback)) Header("Location: msglist.php?folder=".urlencode($folder)."&sid=$sid&lid=$lid");
	}

}
echo($nocache);

$tcontent = load_template($folder_list_template,Array("menus","folders"));

$tcontent = eregi_replace("<!--%UM_LID%-->",$lid,$tcontent);
$tcontent = eregi_replace("<!--%UM_SID%-->",$sid,$tcontent);
$tcontent = eregi_replace("<!--%UM_USER_EMAIL%-->",$sess["email"],$tcontent);
$tcontent = eregi_replace("<!--%UM_JS%-->",$jssource,$tcontent);

$startpos = strpos($tcontent,"<!--%UM_BEGIN_FOLDERS_LOOP%-->");
$endpos = strpos($tcontent,"<!--%UM_END_FOLDERS_LOOP%-->")+28;


$loopline = substr($tcontent,$startpos+30,$endpos-$startpos-58);

$d = dir($userfolder);

$scounter = 0;
$pcounter = 0;
$totalused = 0;
while($entry=$d->read()) {
	if(	is_dir($userfolder.$entry) && 
		$entry != ".." && 
		substr($entry,0,1) != "_" && 
		$entry != ".") {
		$unread = 0;

		if ($entry == "inbox") $thisbox = $sess["headers"];
		else $thisbox = build_local_list($userfolder.$entry);

		$boxsize = 0;
		for($i=0;$i<count($thisbox);$i++) {
			if(!$thisbox[$i]["read"]) $unread++;
			$boxsize += $thisbox[$i]["size"];
		}
		$delete = "&nbsp;";

		switch($entry) {
		case "inbox":
			$boxname = $inbox_extended; 
			break;
		case "sent":
			$boxname = $sent_extended; 
			break;
		case "trash":
			$boxname = $trash_extended; 
			break;
		default: $boxname = $entry;
			$delete = "<a href=\"folders.php?delfolder=$entry&folder=$folder&sid=$sid&lid=$lid\">OK</a>";
		}

		if($unread != 0) $unread = "<b>$unread</b>";

		$boxsize   = get_folder_size($entry);
		$totalused += $boxsize;
		if(ereg("inbox|sent|trash",$entry)) {
			$system[$scounter]["entry"]     = $entry;
			$system[$scounter]["name"]      = $boxname;
			$system[$scounter]["msgs"]      = count($thisbox)."/$unread";
			$system[$scounter]["del"]       = $delete;
			$system[$scounter]["boxsize"]   = $boxsize;
			$scounter++;
		} else {
			$personal[$pcounter]["entry"]   = $entry;
			$personal[$pcounter]["name"]    = $boxname;
			$personal[$pcounter]["msgs"]    = count($thisbox)."/$unread";
			$personal[$pcounter]["del"]     = $delete;
			$personal[$pcounter]["boxsize"] = $boxsize;
			$pcounter++;
		}
	}
}

array_qsort2 ($system,"name");
reset($system);

for($i=0;$i<count($system);$i++) {
		$entry = $system[$i]["entry"];
		$thisline = $loopline;
		$thisline = eregi_replace("<!--%UM_LINK%-->","msglist.php?folder=$entry&sid=$sid&lid=$lid",$thisline);
		$thisline = eregi_replace("<!--%UM_BOX_NAME%-->",htmlspecialchars($system[$i]["name"]),$thisline);
		$thisline = eregi_replace("<!--%UM_READ_UNREAD%-->",$system[$i]["msgs"],$thisline);
		$thisline = eregi_replace("<!--%UM_READ_UNREAD%-->",$system[$i]["msgs"],$thisline);
		$thisline = eregi_replace("<!--%UM_BOX_SIZE%-->",ceil($system[$i]["boxsize"]/1024)." Kb",$thisline);
		$thisline = eregi_replace("<!--%UM_EMPTY_LINK%-->","<a href=\"folders.php?empty=$entry&folder=$folder&sid=$sid&lid=$lid\">OK</a>",$thisline);
		$thisline = eregi_replace("<!--%UM_DEL_LINK%-->",$system[$i]["del"],$thisline);
		$folderlist .= $thisline;
}
for($i=0;$i<count($personal);$i++) {
		$entry = $personal[$i]["entry"];
		$thisline = $loopline;
		$thisline = eregi_replace("<!--%UM_LINK%-->","msglist.php?folder=$entry&sid=$sid&lid=$lid",$thisline);
		$thisline = eregi_replace("<!--%UM_BOX_NAME%-->",htmlspecialchars($personal[$i]["name"]),$thisline);
		$thisline = eregi_replace("<!--%UM_READ_UNREAD%-->",$personal[$i]["msgs"],$thisline);
		$thisline = eregi_replace("<!--%UM_BOX_SIZE%-->",ceil($personal[$i]["boxsize"]/1024)." Kb",$thisline);
		$thisline = eregi_replace("<!--%UM_EMPTY_LINK%-->","<a href=\"folders.php?empty=$entry&folder=$folder&sid=$sid&lid=$lid\">OK</a>",$thisline);
		$thisline = eregi_replace("<!--%UM_DEL_LINK%-->",$personal[$i]["del"],$thisline);
		$folderlist .= $thisline;
}

$tcontent = substr($tcontent,0,$startpos).$folderlist.substr($tcontent,$endpos);


$tmp   = get_tags("<!--%UM_IF_QUOTA_ENABLED_BEGIN%-->","<!--%UM_IF_QUOTA_ENABLED_END%-->",$tcontent);
$clean = $tmp["re-content"];

if(!empty($quota_limit)) {
	if(($totalused/1024) > $quota_limit && $folder == "inbox") $exceeded = 1;
	$tcontent = substr($tcontent,0,$tmp["ab-begin"]).$tmp["re-content"].substr($tcontent,$tmp["ab-end"]);
	$tcontent = eregi_replace("<!--%UM_TOTAL_USED%-->",ceil($totalused/1024) ." Kb",$tcontent);
	$tcontent = eregi_replace("<!--%UM_AVAL_SIZE%-->",$quota_limit ." Kb",$tcontent);
	$tcontent = eregi_replace("<!--%UM_USAGE_GRAPHIC%-->",get_usage_graphic(($totalused/1024),$quota_limit),$tcontent);
} else 
	$tcontent = substr($tcontent,0,$tmp["ab-begin"]).substr($tcontent,$tmp["ab-end"]);

$d->close();
echo($tcontent);
?>
