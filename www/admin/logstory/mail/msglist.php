<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/

include("./session_management.php");

if($folder == "inbox" && ($decision == "delete" || 
	$decision == "move" || 
	isset($refr) || 
	isset($del) ||
	!is_array($sess["headers"]))) {

	if(!$p3->pop_connect()) Header("Location: error.php?msg=".urlencode($error_connect)."&sid=$sid&lid=$lid\r\n");
	if(!$p3->pop_auth()) { Header("Location: badlogin.php?sid=$sid&lid=$lid\r\n"); exit; }
	$deleted = 0;

	$headers = $sess["headers"];

	while(list ($key, $mid) = each($HTTP_POST_VARS)) {

		if(substr($key,0,3) == "msg") {

			$index = substr($key,4);
			$mail_info = $headers[$index];
			$mnum = $mail_info["id"];
			$mid = md5($mail_info["message-id"]);
			$msize = $mail_info["size"];

			$filename = basename($mail_info["localname"]);

			$LocalFilePath = $userfolder.$folder."/".$filename;
			$NewFilePath = $userfolder.$aval_folders."/".$filename;

			if ($decision == "delete") {
				$read = file_exists($LocalFilePath);
				$trash = 0;
				if(!empty($send_to_trash)) {
					$trash = 1;
					if($st_only_read && !$read) $trash = 0;
				}
				if($p3->pop_dele_msg($mnum,$mid,$msize,$trash,$LocalFilePath)) { $deleted++; unset($headers[$index]); }
				else Header("Location: msglist.php?pag=$pag&folder=".urlencode($folder)."&sid=$sid&lid=$lid&msg=".urlencode($error_deleting)."&refr=true\r\n");

			} elseif ($decision == "move") {
				$md = new mime_decode();

				if (!file_exists($LocalFilePath)) {

					$md->charset 			= $default_char_set;
					$md->use_html			= $mime_show_html;
					$md->timezone			= $timezone;

					$tempmail = $p3->pop_retr_msg($mnum,$mid,$msize,1,$LocalFilePath);
					$tempmail = $md->set_as($tempmail,0);

					$tmpfile = fopen($LocalFilePath,"wb+");
					fwrite($tmpfile,$tempmail);
					fclose($tmpfile);

				} else {

					$tempmail = read_file($LocalFilePath);
					$tempmail = $md->set_as($tempmail,1);

					$tmpfile = fopen($LocalFilePath,"wb+");
					fwrite($tmpfile,$tempmail);
					fclose($tmpfile);
					unset($tempmail,$tmpfile);
				}

				if(file_exists($userfolder.$aval_folders)) {
					copy($LocalFilePath,$NewFilePath);
					unlink($LocalFilePath);
				}

				if(!$p3->pop_dele_msg($mnum,$mid,$msize,0,$LocalFilePath))  { Header("Location: msglist.php?msg=".urlencode($error_deleting)."&folder=".urlencode($folder)."&pag=$pag&sid=$sid&lid=$lid&refr=true\r\n"); exit; } 
				else { unset($headers[$index]); }
			}
		}
	}

	$headers = $p3->pop_list_msgs(); 
	$sess["headers"] = $headers;
	save_session($sess);
	$p3->pop_disconnect();

	$pref_file = $userfolder."preferences_ok";

	if($check_first_login && !file_exists($pref_file)) {
		$t = fopen($pref_file,"wb+"); fclose($t); unset($t);
		Header("Location: preferences.php?sid=$sid&lid=$lid&folder=$folder&first=true");
		exit;
	}

} elseif ($decision == "move" || $decision == "delete" || (isset($mnum) && isset($ix))) {

	while(list ($key, $mid) = each ($HTTP_POST_VARS)) {
		if(substr($key,0,3) == "msg") {

			$index = substr($key,4);
			$mail_info = $sess["folderheaders"][$index];
			$localname = $mail_info["localname"];
			$filename = basename($localname);
			$newlocalname = $userfolder.$aval_folders."/$filename";

			$trash = 0;

			if(!empty($send_to_trash)) {
				$trash = 1;
				if($st_only_read && !$mail_info["read"]) $trash = 0;
			}

			if( $decision == "delete" && $trash && $folder != "trash")
				copy($localname,$userfolder."trash/$filename");
			elseif ($decision == "move" && file_exists($userfolder.$aval_folders))
				copy($localname,$newlocalname);

			@unlink($localname);
		}
	}
	$headers = build_local_list($userfolder.$folder);
	$sess["folderheaders"] = $headers;
	save_session($sess);


} elseif ($folder == "inbox")  {
		$headers = $sess["headers"];
} else {
	$headers = build_local_list($userfolder.$folder);
	$sess["folderheaders"] = $headers;
	save_session($sess);
}



//loading quota info

$tcontent = load_template($message_list_template,Array("messagelist","menus"));

$tmp   = get_tags("<!--%UM_IF_QUOTA_ENABLED_BEGIN%-->","<!--%UM_IF_QUOTA_ENABLED_END%-->",$tcontent);
$clean = $tmp["re-content"];

if(!empty($quota_limit)) {
	$totalused = get_total_used_size();
	if(($totalused/1024) > $quota_limit && $folder == "inbox") $exceeded = 1;
	
	$tcontent = substr($tcontent,0,$tmp["ab-begin"]).$tmp["re-content"].substr($tcontent,$tmp["ab-end"]);
	$tcontent = eregi_replace("<!--%UM_TOTAL_USED%-->",ceil($totalused/1024) ." Kb",$tcontent);
	$tcontent = eregi_replace("<!--%UM_AVAL_SIZE%-->",$quota_limit ." Kb",$tcontent);
	$tcontent = eregi_replace("<!--%UM_USAGE_GRAPHIC%-->",get_usage_graphic(($totalused/1024),$quota_limit),$tcontent);
} else 
	$tcontent = substr($tcontent,0,$tmp["ab-begin"]).substr($tcontent,$tmp["ab-end"]);


// sorting arrays..


$arrow = ($sortorder == "ASC")?"images/arrow_up.gif":"images/arrow_down.gif";
$arrow = "&nbsp;<img src=$arrow width=8 height=7 border=0 alt=>";

$attach_arrow  	= "";
$subject_arrow 	= "";
$fromname_arrow = "";
$date_arrow 	= "";
$size_arrow 	= "";

switch($sortby) {
	case "attach":
		$attach_arrow  		= $arrow;
		break;
	case "subject":
		$subject_arrow  	= $arrow;
		break;
	case "fromname":
		$fromname_arrow  	= $arrow;
		break;
	case "date":
		$date_arrow  		= $arrow;
		break;
	case "size":
		$size_arrow   		= $arrow;
		break;
}

$tcontent = eregi_replace("<!--%UM_ATTACH_ARROW%-->",$attach_arrow,$tcontent);
$tcontent = eregi_replace("<!--%UM_SUBJECT_ARROW%-->",$subject_arrow,$tcontent);
$tcontent = eregi_replace("<!--%UM_FROM_ARROW%-->",$fromname_arrow,$tcontent);
$tcontent = eregi_replace("<!--%UM_DATE_ARROW%-->",$date_arrow,$tcontent);
$tcontent = eregi_replace("<!--%UM_SIZE_ARROW%-->",$size_arrow,$tcontent);

array_qsort2($headers,$sortby,$sortorder);
reset($headers);

if($folder == "inbox")
	$sess["headers"] = $headers;
else
	$sess["folderheaders"] = $headers;
save_session($sess);


$useremail = $sess["email"];
$nummsg = count($headers);

if(!isset($pag) || !is_numeric(trim($pag))) $pag = 1;

$reg_pp    = $records_per_page;
$start_pos = ($pag-1)*$reg_pp;
$end_pos   = (($start_pos+$reg_pp) > $nummsg)?$nummsg:$start_pos+$reg_pp;

if(($start_pos >= $end_pos) && ($pag != 1)) header("Location: msglist.php?folder=$folder&pag=".($pag-1)."&sid=$sid&lid=$lid\r\n");

echo($nocache);

$jsquota = ($exceeded)?"true":"false";
$jssource = "
<script language=\"JavaScript\">
no_quota  = $jsquota;
quota_msg = '".ereg_replace("'","\\'",$quota_exceeded)."';
function readmsg(mnum,pag,ix,read) {
	if(!read && no_quota)
		alert(quota_msg)
	else
		location = 'readmsg.php?folder=".urlencode($folder)."&mnum='+mnum+'&pag='+pag+'&ix='+ix+'&sid=$sid&lid=$lid'; 
}
function newmsg() { location = 'newmsg.php?pag=$pag&folder=".urlencode($folder)."&sid=$sid&lid=$lid'; }
function refreshlist() { location = 'msglist.php?refr=true&folder=".urlencode($folder)."&sid=$sid&lid=$lid' }
function folderlist() { location = 'folders.php?folder=".urlencode($folder)."&sid=$sid&lid=$lid'}
function delemsg() { document.form1.submit() }
function goend() { location = 'logout.php?sid=$sid&lid=$lid'; }
function goinbox() { location = 'msglist.php?folder=inbox&sid=$sid&lid=$lid'; }
function search() { location = 'search.php?sid=$sid&lid=$lid'; }
function emptytrash() {	location = 'folders.php?empty=trash&folder=".urlencode($folder)."&goback=true&sid=$sid&lid=$lid';}
function movemsg() { 
	if(no_quota) 
		alert(quota_msg);
	else {
		with(document.form1) { decision.value = 'move'; submit(); } 
	}
}
function addresses() { location = 'addressbook.php?sid=$sid&lid=$lid'; }
function prefs() { location = 'preferences.php?sid=$sid&lid=$lid'; }
function sel() {
	with(document.form1) {
		for(i=0;i<elements.length;i++) {
			thiselm = elements[i];
			if(thiselm.name.substring(0,3) == 'msg')
				thiselm.checked = !thiselm.checked
		}
	}
}
sort_colum = '$sortby';
sort_order = '$sortorder';

function sortby(col) {
	if(col == sort_colum) ord = (sort_order == 'ASC')?'DESC':'ASC';
	else ord = 'ASC';
	location = 'msglist.php?folder=$folder&pag=$pag&sortby='+col+'&sortorder='+ord+'&sid=$sid&lid=$lid';
}

</script>
";


$tmp   = get_tags("<!--%UM_ERROR_BEGIN%-->","<!--%UM_ERROR_END%-->",$tcontent);
unset($clean);
if(isset($msg)) {
 	$clean = $tmp["re-content"];
	$clean = eregi_replace("<!--%UM_ERROR_MSG%-->",$msg,$clean);
}
$tcontent = substr($tcontent,0,$tmp["ab-begin"]).$clean.substr($tcontent,$tmp["ab-end"]);


$tcontent = eregi_replace("<!--%UM_USER_EMAIL%-->",$useremail,eregi_replace("<!--%UM_JS%-->",$jssource,$tcontent));
$tcontent = eregi_replace("<!--%UM_ACTIVE_FOLDER%-->",$folder,$tcontent);
$tcontent = eregi_replace("<!--%UM_LID%-->",$lid,$tcontent);
$tcontent = eregi_replace("<!--%UM_SID%-->",$sid,$tcontent);
$tcontent = eregi_replace("<!--%UM_ACTIVE_PAGE%-->",$pag,$tcontent);

$startlist = strpos($tcontent,"<!--%UM_BEGIN_MESSAGE_LIST%-->");
$endlist = strpos($tcontent,"<!--%UM_END_MESSAGE_LIST%-->")+28;

$listline = substr($tcontent,$startlist+30,$endlist-$startlist-58);
$listlinetoreplace = substr($tcontent,$startlist,$endlist-$startlist);

$startlistloop = strpos($tcontent,"<!--%UM_ML_LOOPBEGIN%-->");
$endlistloop = strpos($tcontent,"<!--%UM_ML_LOOPEND%-->")+22;

$listloop = substr($tcontent,$startlistloop+24,$endlistloop-$startlistloop-46);

$startnolist = strpos($tcontent,"<!--%UM_BEGIN_NO_MESSAGES%-->");
$endnolist = strpos($tcontent,"<!--%UM_END_NO_MESSAGES%-->")+27;

$nomessagesline = substr($tcontent,$startnolist+29,$endnolist-$startnolist-56);
$nomessageslinetoreplace = substr($tcontent,$startnolist,$endnolist-$startnolist);


switch($folder) {
case "inbox":
	$boxname = $inbox_extended;
	break;
case "sent":
	$boxname = $sent_extended;
	break;
case "trash":
	$boxname = $trash_extended;
	break;
default:
	$boxname = $folder;
}

if($nummsg > 0) {

	$tcontent = eregi_replace($nomessageslinetoreplace,"",$tcontent);
	
	$beforeloop = substr($listline,0,strpos($listline,"<!--%UM_ML_LOOPBEGIN%-->"));
	$afterloop = substr($listline,strpos($listline,"<!--%UM_ML_LOOPEND%-->")+22);

	$newmsgs = 0;
	for($i=0;$i<count($headers);$i++)
		if(!$headers[$i]["read"]) $newmsgs++;

	if($nummsg == 1) $counttext = $msg_count_s;
	else $counttext = sprintf($msg_count_p,$nummsg);
	if($newmsgs == 1) $counttext .= $msg_unread_s;
	elseif ($newmsgs > 1) $counttext .= sprintf($msg_unread_p,$newmsgs);
	else $counttext .= $msg_no_unread;
	$counttext .= sprintf($msg_boxname,$boxname);

	$msglist .= eregi_replace("<!--%UM_MESSAGECOUNT%-->",$counttext,$beforeloop);

	//print_r($headers);

	for($i=$start_pos;$i<$end_pos;$i++) {
		$thisline = "$listloop\r\n";
		$mnum = $headers[$i]["id"]; 

		$mid = md5($headers[$i]["message-id"]);
		//echo(htmlspecialchars($headers[$i]["message-id"])." - ".$mid."<br>");

		$read = ($headers[$i]["read"])?"true":"false";
		$link = "javascript:readmsg($mnum,$pag,$i,$read)";
		$dlink = "msglist.php?folder=$folder&pag=$pag&del=true&mnum=$mnum&mid=$mid&msize=".$headers[$i]["size"]."&sid=$sid&lid=$lid";
		$clink = "newmsg.php?folder=$folder&nameto=".htmlspecialchars($headers[$i]["from"][0]["name"])."&mailto=".htmlspecialchars($headers[$i]["from"][0]["mail"])."&sid=$sid&lid=$lid";

		$from = htmlspecialchars((!empty($headers[$i]["from"][0]["name"]))?$headers[$i]["from"][0]["name"]:$no_sender_text);
		$subject = htmlspecialchars((strlen($headers[$i]["subject"]) > 25)?substr($headers[$i]["subject"],0,25)."...":$headers[$i]["subject"]);
		if(empty($subject)) $subject = $no_subject_text;
		if(!$headers[$i]["read"])
			$subject = "<b>$subject</b>";

		$from = (strlen($from) > 25)?substr($from,0,25)."...":$from;

		$checkbox = "<input type=\"checkbox\" name=\"msg_$i\" value=\"$mid\">";
		$thisline = eregi_replace("<!--%UM_ML_CHECKBOX%-->",$checkbox,$thisline);
		$attachimg = ($headers[$i]["attach"])?"<img src=images/attach.gif border=0>":"&nbsp;";
		$thisline = eregi_replace("<!--%UM_ML_HAVE_ATTACH%-->",$attachimg,$thisline);
		$fromlink = "<a href=\"$clink\">$from</a>";
		$thisline = eregi_replace("<!--%UM_ML_FROM%-->",$fromlink,$thisline);
		$subjectlink = "<a href=\"$link\">$subject</a>";
		$thisline = eregi_replace("<!--%UM_ML_SUBJECT%-->",$subjectlink,$thisline);
		$date = $headers[$i]["date"];
		$thisline = eregi_replace("<!--%UM_ML_DATE%-->",@date($date_format,$date),$thisline);
		$size = ceil($headers[$i]["size"]/1024)."Kb";
		$thisline = eregi_replace("<!--%UM_ML_SIZE%-->",$size,$thisline);
		$msglist .= $thisline;
	}
	$msglist .= $afterloop;

	$tcontent = substr($tcontent,0,$startlist).$msglist.substr($tcontent,$endlist,strlen($tcontent));
	
} else {
	$tcontent = substr($tcontent,0,$startlist)."".substr($tcontent,$endlist,strlen($tcontent));
	$startnolist = strpos($tcontent,"<!--%UM_BEGIN_NO_MESSAGES%-->");
	$endnolist = strpos($tcontent,"<!--%UM_END_NO_MESSAGES%-->")+27;
	$tcontent = substr($tcontent,0,$startnolist).eregi_replace("<!--%UM_BOX_NAME%-->",$boxname,$nomessagesline).substr($tcontent,$endnolist,strlen($tcontent));
} 

if($nummsg > 0) {
	if($pag > 1) $navigation .= "<a href=\"msglist.php?folder=$folder&pag=".($pag-1)."&sid=$sid&lid=$lid\"  class=\"navigation\">$previous_text</a> ";
	for($i=1;$i<=ceil($nummsg / $reg_pp);$i++) 
		if($pag == $i) $navigation .= "$i ";
		else $navigation .= "<a href=\"msglist.php?folder=$folder&pag=$i&sid=$sid&lid=$lid\" class=\"navigation\">$i</a> ";
	if($end_pos < $nummsg) $navigation .= "<a href=\"msglist.php?folder=$folder&pag=".($pag+1)."&sid=$sid&lid=$lid\"  class=\"navigation\">$next_text</a> ";
	$navigation .= " ( $pag/".ceil($nummsg / $reg_pp).")";
}

$myselect = "<select name=\"aval_folders\">\r\n";
$d = dir($userfolder);
while($entry=$d->read()) {
	if(	is_dir($userfolder.$entry) && 
		$entry != ".." && 
		$entry != "." && 
		substr($entry,0,1) != "_" && 
		$entry != $folder &&
		$entry != "inbox") {
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
		default:
			$boxname = $entry;
		}
		$myselect .= "<option value=\"$entry\">$boxname\r\n";
	}
}
$myselect .= "</select>";
$d->close();

$tcontent = eregi_replace("<!--%UM_MESSAGE_NAVIGATION%-->",$navigation,$tcontent);
$tcontent = eregi_replace("<!--%UM_AVAL_FOLDERS%-->",$myselect,$tcontent);

echo($tcontent);

?>
