<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/

//defines
include("./session_management.php");

if(!isset($ix) || !isset($pag)) Header("Location: error.php?msg=".urlencode($error_other)."&sid=$sid&lid=$lid");

$md = new mime_decode();

$md->use_html			= $mime_show_html;
$md->charset 			= $default_char_set;
$md->timezone			= $timezone;


$tcontent = load_template($read_message_template,Array("readmsg","menus"));

if ($folder == "inbox" && !isset($search)) {

	$mysess = $sess["headers"];
	$mail_info = $mysess[$ix];
	$mnum = $mail_info["id"];
	$mid = md5($mail_info["message-id"]);
	$msize = $mail_info["size"];
	$localname = $mail_info["localname"];

	
	if(!file_exists($localname)) {

	
		if(!$p3->pop_connect()) { Header("Location: error.php?msg=".urlencode($error_connect)."&sid=$sid&lid=$lid\r\n"); exit; }
		if(!$p3->pop_auth()) { Header("Location: badlogin.php?sid=$sid&lid=$lid\r\n"); exit; }
		if(!($result = $p3->pop_retr_msg($mnum,$mid,$msize,1,$localname))) { Header("Location: msglist.php?msg=".urlencode($error_retrieving)."&folder=".urlencode($folder)."&pag=$pag&sid=$sid&lid=$lid&refr=true\r\n"); exit; }
		$p3->pop_disconnect(); 
		$sess["headers"][$ix]["read"] = 1;
		save_session($sess);

	} else {


		$result = read_file($localname);

		/*
		$result = $md->set_as($result,1);
		$sess["headers"][$ix]["read"] = 1;
		save_file($localname,$result);
		*/
	}

} else {

	$mysess = $sess["folderheaders"];
	$mail_info = $sess["folderheaders"][$ix];
	$filename = $mail_info["localname"];

	if(!file_exists($filename)) { Header("Location: msglist.php?msg=".urlencode($error_retrieving)."&folder=".urlencode($folder)."&pag=$pag&sid=$sid&lid=$lid&refr=true\r\n"); exit; }
	$result = read_file($filename);
	$result = $md->set_as($result,1);
	save_file($filename,$result);
}

echo($nocache);

//echo("<pre>".htmlspecialchars($result)."</pre>");

$md->initialize($result);

$tmp   = get_tags("<!--%IF_HAVE_PREV_BEGIN%-->","<!--%IF_HAVE_PREV_END%-->",$tcontent);

if($ix > 0) {
	// Display "Previous" text
	$clean = $tmp["re-content"];
	$nix = ($ix-1);
	$title = htmlspecialchars($mysess[$nix]["subject"]);
	$clean = eregi_replace("<!--%UM_PREV_LINK%-->","readmsg.php?pag=$pag&folder=".urlencode($folder)."&sid=$sid&lid=$lid&ix=$nix",$clean);
	$clean = eregi_replace("<!--%UM_PREV_TITLE%-->",$title,$clean);
	$tcontent = substr($tcontent,0,$tmp["ab-begin"]).$clean.substr($tcontent,$tmp["ab-end"]);

} else $tcontent = substr($tcontent,0,$tmp["ab-begin"]).substr($tcontent,$tmp["ab-end"]);

$tmp   = get_tags("<!--%IF_HAVE_NEXT_BEGIN%-->","<!--%IF_HAVE_NEXT_END%-->",$tcontent);

if($ix < (count($mysess)-1)) {
	// Display "Next" text
	$clean = $tmp["re-content"];
	$nix = ($ix+1);
	$title = htmlspecialchars($mysess[$nix]["subject"]);
	$clean = eregi_replace("<!--%UM_NEXT_LINK%-->","readmsg.php?pag=$pag&folder=".urlencode($folder)."&sid=$sid&lid=$lid&ix=$nix",$clean);
	$clean = eregi_replace("<!--%UM_NEXT_TITLE%-->",$title,$clean);
	$tcontent = substr($tcontent,0,$tmp["ab-begin"]).$clean.substr($tcontent,$tmp["ab-end"]);
} else $tcontent = substr($tcontent,0,$tmp["ab-begin"]).substr($tcontent,$tmp["ab-end"]);


$email = $md->content;

$body = $email["body"];

//echo("<pre>".htmlspecialchars($body)."</pre>");

$uagent = $HTTP_SERVER_VARS["HTTP_USER_AGENT"];

$ns4    = (ereg("Mozilla/4",$uagent) && !ereg("MSIE",$uagent) && !ereg("Gecko",$uagent));
$ns6moz = ereg("Gecko",$uagent);
$ie4up  = ereg("MSIE (4|5|6)",$uagent);

$other = (!$ns4 && !$ns6moz && !$ie4up);

$body = eregi_replace("<base","<uebimiau_base_not_alowed",eregi_replace("<link","<uebimiau_link_not_alowed",$body));

if ($other) {
	//cleanning bad tags to show in other browsers
	if(eregi("<[ ]*body.*background[ ]*=[ ]*[\"']?([A-Za-z0-9._&?=:/-]+)[\"']?.*>",$body,$regs))
		$backimg = 	" background=\"".$regs[1]."\"";
	$tcontent = eregi_replace("<!--%UM_BACK_IMG%-->",$backimg,$tcontent);
	
	if(eregi("<[ ]*body[A-Z0-9._&?=:/\"' -]*bgcolor=[\"']?([A-Z0-9#]+)[\"']?[A-Z0-9._&?=:/\"' -]*>",$body,$regs))
		$backcolor = " bgcolor=\"".$regs[1]."\"";
	$tcontent = eregi_replace("<!--%UM_BACK_COLOR%-->",$backcolor,$tcontent);

	$body = eregi_replace("<body","<uebimiau_body_not_alowed",$body);
	$body = eregi_replace("a:(link|visited|hover)",".".uniqid(""),$body);
	$body = eregi_replace("(body)[ ]?{",".".uniqid(""),$body);

} elseif($ie4up || $ns6moz) {
	$sess["currentbody"] = $body;;
	save_session($sess);
	$tcontent = eregi_replace("<!--%UM_BACK_IMG%-->","",$tcontent);
	$tcontent = eregi_replace("<!--%UM_BACK_COLOR%-->","",$tcontent);
	$body = "<iframe src=\"show_body.php?sid=$sid&lid=$lid&folder=".htmlspecialchars($folder)."&ix=$ix\" width=\"100%\" height=\"400\"></iframe>";

} elseif(!empty($ns4)) {
	$sess["currentbody"] = $body;;
	save_session($sess);
	$tcontent = eregi_replace("<!--%UM_BACK_IMG%-->","",$tcontent);
	$tcontent = eregi_replace("<!--%UM_BACK_COLOR%-->","",$tcontent);
	$body = "<ilayer width=\"100%\" left=\"0\" top=\"0\">$body</ilayer>";
}

//$body = eregi_replace("mailto:","newmsg.php?sid=$sid&lid=$lid&folder=".htmlspecialchars($folder)."&to=",$body);



$ARFrom = $email["from"];
$useremail = $sess["email"];

// from
$name = $ARFrom[0]["name"];
$thismail = $ARFrom[0]["mail"];

$tcontent = eregi_replace("<!--%UM_FROM_LINK%-->","newmsg.php?nameto=".urlencode($name)."&mailto=$thismail&sid=$sid&lid=$lid",$tcontent);
$tcontent = eregi_replace("<!--%UM_FROM_TITLE%-->",htmlspecialchars("$name <$thismail>"),$tcontent);
$tcontent = eregi_replace("<!--%UM_FROM_NAME%-->",htmlspecialchars($name),$tcontent);

// To
$ARTo = $email["to"];

$tobegin = strpos($tcontent,"<!--%UM_TO_LOOP_BEGIN%-->");
$toend = strpos($tcontent,"<!--%UM_TO_LOOP_END%-->")+23;
$tostring = substr($tcontent,$tobegin,$toend-$tobegin);
$toline = substr($tcontent,$tobegin+25,$toend-48-$tobegin);

for($i=0;$i<count($ARTo);$i++) {

	$thisline = $toline;

	$name = $ARTo[$i]["name"]; $thismail = $ARTo[$i]["mail"];

	$thisline = eregi_replace("<!--%UM_TO_LINK%-->","newmsg.php?nameto=".urlencode($name)."&mailto=$thismail&sid=$sid&lid=$lid",$thisline);
	$thisline = eregi_replace("<!--%UM_TO_TITLE%-->",htmlspecialchars("$name <$thismail>"),$thisline);
	$thisline = eregi_replace("<!--%UM_TO_NAME%-->",htmlspecialchars($name),$thisline);

	if(isset($tos)) $tos .= ";$thisline";
	else $tos = "$thisline";

}

$tos = (!empty($tos))?$tos:" &nbsp;$no_recipient_text";

$tcontent = eregi_replace($tostring,$tos,$tcontent);

// CC
$ARCC = $email["cc"];

$ccbegin = strpos($tcontent,"<!--%UM_CC_BEGIN%-->");
$ccend = strpos($tcontent,"<!--%UM_CC_END%-->")+18;
$ccstring = substr($tcontent,$ccbegin,$ccend-$ccbegin);
$ccstring2 = substr($tcontent,$ccbegin+20,$ccend-38-$ccbegin);

if(count($ARCC) > 0) {
	$ccloopbegin = strpos($tcontent,"<!--%UM_CC_LOOP_BEGIN%-->");
	$ccloopend = strpos($tcontent,"<!--%UM_CC_LOOP_END%-->")+23;
	$ccloopstring = substr($tcontent,$ccloopbegin,$ccloopend-$ccloopbegin);
	$ccloopstring2 = substr($tcontent,$ccloopbegin+25,$ccloopend-48-$ccloopbegin);

	for($i=0;$i<count($ARCC);$i++) {
		$name = $ARCC[$i]["name"]; $thismail = $ARCC[$i]["mail"];

		$thisline = $ccloopstring2;
	
		$thisline = eregi_replace("<!--%UM_CC_LINK%-->","newmsg.php?nameto=".urlencode($name)."&mailto=$thismail&sid=$sid&lid=$lid",$thisline);
		$thisline = eregi_replace("<!--%UM_CC_TITLE%-->",htmlspecialchars("$name <$thismail>"),$thisline);
		$thisline = eregi_replace("<!--%UM_CC_NAME%-->",htmlspecialchars($name),$thisline);

		if(isset($ccs)) $ccs .= ";$thisline";
		else $ccs = "$thisline";

	}
	$tcontent = eregi_replace($ccstring,eregi_replace($ccloopstring,$ccs,$ccstring2),$tcontent);
} else
	$tcontent = eregi_replace($ccstring,"",$tcontent);



$tcontent = eregi_replace("<!--%UM_TITLE%-->",htmlspecialchars($email["subject"]),$tcontent);

$script = "
<script language=\"JavaScript\">
function deletemsg() { 
	if(confirm('".ereg_replace("'","\\'",$confirm_delete)."')) 
		with(document.move) { decision.value = 'delete'; submit(); } 
}
function reply() { document.msg.submit(); }
function movemsg() { document.move.submit(); }
function newmsg() {	location = 'newmsg.php?folder=$folder&pag=$pag&sid=$sid&lid=$lid'; }
function headers() { mywin = window.open('headers.php?folder=".urlencode($folder)."&ix=$ix&sid=$sid&lid=$lid','Headers','width=550, top=100, left=100, height=320,directories=no,toolbar=no,status=no,scrollbars=yes'); }
function catch_addresses() { mywin = window.open('catch.php?folder=".urlencode($folder)."&ix=$ix&sid=$sid&lid=$lid','Catch','width=550, top=100, left=100, height=320,directories=no,toolbar=no,status=no,scrollbars=yes'); }
function replyall() { with(document.msg) { rtype.value = 'replyall'; submit(); } }
function forward() { with(document.msg) { rtype.value = 'forward'; submit(); } }
function newmsg() { location = 'newmsg.php?pag=$pag&folder=".urlencode($folder)."&sid=$sid&lid=$lid'; }
function folderlist() { location = 'folders.php?folder=".urlencode($folder)."&sid=$sid&lid=$lid'}
function goend() { location = 'logout.php?sid=$sid&lid=$lid'; }
function goinbox() { location = 'msglist.php?folder=inbox&sid=$sid&lid=$lid'; }
function goback() { location = 'msglist.php?folder=".urlencode($folder)."&sid=$sid&lid=$lid&pag=$pag'; }
function search() { location = 'search.php?sid=$sid&lid=$lid'; }
function emptytrash() {	location = 'folders.php?empty=trash&folder=".urlencode($folder)."&goback=true&sid=$sid&lid=$lid';}
function addresses() { location = 'addressbook.php?sid=$sid&lid=$lid'; }
function prefs() { location = 'preferences.php?sid=$sid&lid=$lid'; }
</script>
";


$tcontent = eregi_replace("<!--%UM_JS%-->",$script,$tcontent);
$tcontent = eregi_replace("<!--%UM_LID%-->",$lid,$tcontent);
$tcontent = eregi_replace("<!--%UM_SID%-->",$sid,$tcontent);

$tcontent = eregi_replace("<!--%UM_HEADERS%-->",htmlspecialchars($email["headers"]),$tcontent);

$tcontent = eregi_replace("<!--%UM_SUBJECT%-->",htmlspecialchars($email["subject"]),$tcontent);
$tcontent = eregi_replace("<!--%UM_DATE%-->",@date($date_format,$email["date"]),$tcontent);

$tcontent = eregi_replace("<!--%UM_BODY%-->",$body,$tcontent);

$tcontent = eregi_replace("<!--%UM_TO%-->",htmlspecialchars($toreply),$tcontent);
$tcontent = eregi_replace("<!--%UM_DATE%-->",htmlspecialchars($fromreply),$tcontent);
$tcontent = eregi_replace("<!--%UM_FOLDER%-->",htmlspecialchars($folder),$tcontent);
$tcontent = eregi_replace("<!--%UM_IX%-->",$ix,$tcontent);

$anexos = $email["attachments"];


$tmp      = get_tags("<!--%UM_ATTACH_BEGIN%-->","<!--%UM_ATTACH_END%-->",$tcontent);
$clean    = $tmp["re-content"];
$attbegin = $tmp["ab-begin"];
$attend   = $tmp["ab-end"];

if(count($anexos) > 0) {
	$tmp			= get_tags("<!--%UM_ATTACH_LOOP_BEGIN%-->","<!--%UM_ATTACH_LOOP_END%-->",$clean);

	$loopbody		= $tmp["re-content"];

	$attloopbegin	= $tmp["ab-begin"];
	$attloopend 	= $tmp["ab-end"];

	for($i=0;$i<count($anexos);$i++) {
		$thisline = $loopbody;

		$fname = $anexos[$i]["name"];
		$ctype = $anexos[$i]["content-type"];

		$link1 = "download.php?folder=$folder&ix=$ix&mnum=$mnum&bound=".base64_encode($anexos[$i]["boundary"])."&part=".$anexos[$i]["part"]."&filename=".urlencode($fname)."&sid=$sid&lid=$lid";
		$link2 = "$link1&down=1";

		$thisline = eregi_replace("<!--%UM_NORMAL_LINK%-->",$link1,$thisline);
		$thisline = eregi_replace("<!--%UM_NAME%-->",htmlspecialchars($fname),$thisline);
		$thisline = eregi_replace("<!--%UM_FORCED_LINK%-->",$link2,$thisline);
		$thisline = eregi_replace("<!--%UM_SIZE%-->",ceil($anexos[$i]["size"]/1024)."Kb",$thisline);
		$thisline = eregi_replace("<!--%UM_TYPE%-->",substr($ctype,0,strpos($ctype,"/")),$thisline);
		
		$downloads .= $thisline;
	}
	$clean = substr($clean,0,$attloopbegin).$downloads.substr($clean,$attloopend);
	$tcontent = substr($tcontent,0,$attbegin).$clean.substr($tcontent,$attend);

} else
	$tcontent = substr($tcontent,0,$attbegin).substr($tcontent,$attend);

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

$tcontent = eregi_replace("<!--%UM_AVAL_FOLDERS%-->",$myselect,$tcontent);

echo($tcontent);

?>
