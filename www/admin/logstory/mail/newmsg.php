<?php

/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/

include("./session_management.php");

echo($nocache);

if($tipo == "send") {
	echo $newmsg_result_template;
	$tcontent = load_template($newmsg_result_template,Array("menus","compose"));
	include("./class_smtp.php");

	$md = new mime_decode();
	$md->charset 			= $default_char_set;
	$md->use_html			= $mime_show_html;
	$md->timezone			= $timezone;

	$ARTo = $md->get_names(stripslashes($to));
	$ARCc = $md->get_names(stripslashes($cc));
	$ARBcc = $md->get_names(stripslashes($bcc));

	if((count($ARTo)+count($ARCc)+count($ARBcc)) > 0) {
		$mail = new phpmailer;
		// for password authenticated servers

		if(!empty($use_password_for_smtp)) {
			$user = ($use_email_as_user_smtp)?$sess["email"]:$sess["user"];
			$mail->UseAuthLogin($user,$sess["pass"]);
		}
		// if using the advanced editor

		if($is_html == "true")  {
			$mail->IsHTML(1);
			if(!empty($footer)) {
				$footer = ereg_replace("\n","",$footer); $footer = ereg_replace("\r","<br>\r\n",$footer);
				$body .= $footer;
			}
		} elseif (!empty($footer)) $body .= $footer;

		$mail->CharSet		= $default_char_set;
		$mail->IPAddress	= getenv("REMOTE_ADDR");
		$mail->timezone		= $server_time_zone;
		$mail->From 		= $sess["email"];
		$mail->FromName 	= $md->mime_encode_headers($real_name);
		$mail->AddReplyTo($reply_to, $md->mime_encode_headers($real_name));
		$mail->Host 		= $smtp_server;
		$mail->WordWrap 	= 76;

		if(count($ARTo) != 0) {
			for($i=0;$i<count($ARTo);$i++) {
				$name = $ARTo[$i]["name"];
				$email = $ARTo[$i]["mail"];
				if($name != $email)
					$mail->AddAddress($email,$md->mime_encode_headers($name));
				else
					$mail->AddAddress($email);
			}
		}

		if(count($ARCc) != 0) {
			for($i=0;$i<count($ARCc);$i++) {
				$name = $ARCc[$i]["name"];
				$email = $ARCc[$i]["mail"];
				if($name != $email)
					$mail->AddCC($email,$md->mime_encode_headers($name));
				else
					$mail->AddCC($email);
			}
		}

		if(count($ARBcc) != 0) {
			for($i=0;$i<count($ARBcc);$i++) {
				$name = $ARBcc[$i]["name"];
				$email = $ARBcc[$i]["mail"];
				if($name != $email)
					$mail->AddBCC($email,$md->mime_encode_headers($name));
				else
					$mail->AddBCC($email);
			}
		}

		if(is_array($attachs = $sess["attachments"])) {
			for($i=0;$i<count($attachs);$i++) {
				if(file_exists($attachs[$i]["localname"])) {
					$mail->AddAttachment($attachs[$i]["localname"], $attachs[$i]["name"], $attachs[$i]["type"]);
				}
			}
		}

		$mail->Subject = $md->mime_encode_headers(stripslashes($subject));
		$mail->Body = stripslashes($body);

		$sucstartpos = strpos($tcontent,"<!--%UM_SUCESS_BEGIN%-->");
		$sucendpos = strpos($tcontent,"<!--%UM_SUCESS_END%-->")+22;

		$failstartpos = strpos($tcontent,"<!--%UM_FAIL_BEGIN%-->");
		$failendpos = strpos($tcontent,"<!--%UM_FAIL_END%-->")+20;


		if(($resultmail = $mail->Send()) === false) {

			$err = $mail->ErrorAlerts[count($mail->ErrorAlerts)-1];
			$tcontent = substr($tcontent,0,$sucstartpos).substr($tcontent,$failstartpos+22,$failendpos-$failstartpos-42).substr($tcontent,$failendpos);
			$tcontent = eregi_replace("<!--%UM_ERR%-->",$err,$tcontent);

		} else {
			if(is_array($attachs = $sess["attachments"])) {
				for($i=0;$i<count($attachs);$i++) {
					if(file_exists($attachs[$i]["localname"])) {
						@unlink($attachs[$i]["localname"]);
					}
				}
				
				unset($sess["attachments"]);
				reset($sess);
				save_session($sess);
			}
			$tcontent = substr($tcontent,0,$sucstartpos).substr($tcontent,$sucstartpos+24,$sucendpos-$sucstartpos-46).substr($tcontent,$failendpos);

			if(!empty($save_to_sent)) {
				$struc = $md->fetch_structure($resultmail);
				$header = $struc["header"];
				$mail_info = $md->get_mail_info($header);
				$flocalname = $userfolder."sent/".md5(trim($mail_info["subject"].$mail_info["date"].$mail_info["message-id"])).".eml";
				$myfile = fopen($flocalname,"wb+");
				fwrite($myfile,$resultmail);
				fclose($myfile);
			}

		}

	} else die("<script language=\"javascript\">location = 'error.php?msg=".urlencode($error_no_recipients)."&sid=$sid&lid=$lid';</script>");

	$jssource = "
	<script language=\"javascript\">
	function newmsg() { location = 'newmsg.php?pag=$pag&folder=".urlencode($folder)."&sid=$sid&lid=$lid'; }
	function folderlist() { location = 'folders.php?folder=".urlencode($folder)."&sid=$sid&lid=$lid'}
	function goend() { location = 'logout.php?sid=$sid&lid=$lid'; }
	function goinbox() { location = 'msglist.php?folder=inbox&sid=$sid&lid=$lid'; }
	function emptytrash() {	location = 'folders.php?empty=trash&folder=".urlencode($folder)."&goback=true&sid=$sid&lid=$lid';}
	function search() {	location = 'search.php?folder=".urlencode($folder)."&sid=$sid&lid=$lid';}
	function addresses() { location = 'addressbook.php?sid=$sid&lid=$lid'; }
	function prefs() { location = 'preferences.php?sid=$sid&lid=$lid'; }
	</script>
	";

	$tcontent = eregi_replace("<!--%UM_SID%-->",$sid,$tcontent);
	$tcontent = eregi_replace("<!--%UM_LID%-->",$lid,$tcontent);
	$tcontent = eregi_replace("<!--%UM_JS%-->",$jssource,$tcontent);
	echo($tcontent);

}else {
	$uagent = $HTTP_SERVER_VARS["HTTP_USER_AGENT"];
	$isMac = ereg("Mac",$uagent);
	$uagent = explode("; ",$uagent);
	$uagent = explode(" ",$uagent[1]);
	$bname = strtoupper($uagent[0]);
	$bvers = $uagent[1];
	$show_advanced = (($bname == "MSIE") && (intval($bvers) >= 5) && (!$textmode) && (!$isMac) )?1:0;
	//$show_advanced = 0;
	$js_advanced = ($show_advanced)?"true":"false";

	if(!empty($show_advanced)) $signature = nl2br($signature);
	//echo $newmsg_template;
	$tcontent = load_template($newmsg_template,Array("compose","menus"));


	$tmp   = get_tags("<!--%UM_IF_ADD_SIG_BEGIN%-->","<!--%UM_IF_ADD_SIG_END%-->",$tcontent);
	$clean = $tmp["re-content"];

	if(empty($add_sig)) $tcontent = substr($tcontent,0,$tmp["ab-begin"]).$tmp["re-content"].substr($tcontent,$tmp["ab-end"]);
	else $tcontent = substr($tcontent,0,$tmp["ab-begin"]).substr($tcontent,$tmp["ab-end"]);

	$tcontent = eregi_replace("<!--%UM_IS_HTML%-->",$js_advanced,$tcontent);
	$tcontent = eregi_replace("<!--%UM_TEXTMODE%-->",$textmode,$tcontent);

	$jssource = "
	<script language=\"javascript\">
	bIs_html = $js_advanced;
	bsig_added = false;
	function addsig() {
		if(bsig_added) return;
		with(document.composeForm) {
			if(cksig.checked) {
				if(bIs_html) {
					cur = GetHtml()
					SetHtml(cur+'<br><br>--<br>'+sig.value);
				} else
					body.value += '\\r\\n\\r\\n--\\r\\n'+sig.value;
			}
			cksig.disabled = true;
			bsig_added = true;
		}
	}

	function upwin(rem) { 
		mywin = 'upload.php';
		if (rem != null) mywin += '?rem='+rem+'&sid=$sid';
		else mywin += '?sid=$sid&lid=$lid';
		window.open(mywin,'Upload','width=300,height=50,scrollbars=0,menubar=0,status=0'); 
	}

	function doupload() {
		if(bIs_html) document.composeForm.body.value = GetHtml();
		document.composeForm.tipo.value = 'edit';
		document.composeForm.submit();
	}
	function textmode() {
		with(document.composeForm) {
			if(bIs_html) body.value = GetText();
			textmode.value = 1;
			tipo.value = 'edit';
			submit();
		}
	}

	function enviar() {
		error_msg = new Array();
		frm = document.composeForm;
		check_mail(frm.to.value);
		check_mail(frm.cc.value);
		check_mail(frm.bcc.value);
		errors = error_msg.length;

		if(frm.to.value == '' && frm.cc.value == '' && frm.bcc.value == '')
			alert('".ereg_replace("'","\\'",$error_no_recipients)."');

		else if (errors > 0) {

			if (errors == 1) errmsg = '".ereg_replace("'","\\'",$error_compose_invalid_mail1_s)."\\r\\r';
			else  errmsg = '".ereg_replace("'","\\'",$error_compose_invalid_mail1_p)."\\r\\r';

			for(i=0;i<errors;i++)
				errmsg += error_msg[i]+'\\r';

			if (errors == 1) errmsg += '\\r".ereg_replace("'","\\'",$error_compose_invalid_mail2_s)."s';
			else  errmsg += '\\r".ereg_replace("'","\\'",$error_compose_invalid_mail2_p)."';

			alert(errmsg)
	
		} else {
			if(bIs_html) frm.body.value = GetHtml();
			frm.tipo.value = 'send';
			frm.submit();
		}
	}
	
	function newmsg() { location = 'newmsg.php?pag=$pag&folder=".urlencode($folder)."&sid=$sid&lid=$lid'; }
	function folderlist() { location = 'folders.php?folder=".urlencode($folder)."&sid=$sid&lid=$lid'}
	function goend() { location = 'logout.php?sid=$sid&lid=$lid'; }
	function goinbox() { location = 'msglist.php?folder=inbox&sid=$sid&lid=$lid'; }
	function emptytrash() {	location = 'folders.php?empty=trash&folder=".urlencode($folder)."&goback=true&sid=$sid&lid=$lid';}
	function search() {	location = 'search.php?folder=".urlencode($folder)."&sid=$sid&lid=$lid';}
	function addrpopup() {	mywin = window.open('quick_address.php?sid=$sid&lid=$lid','AddressBook','width=480,height=220,top=200,left=200'); }
	function addresses() { location = 'addressbook.php?sid=$sid&lid=$lid'; }
	function prefs() { location = 'preferences.php?sid=$sid&lid=$lid'; }
	function AddAddress(strType,strAddress) {
		obj = eval('document.composeForm.'+strType);
		if(obj.value == '') obj.value = strAddress
		else  obj.value = obj.value + ', ' + strAddress
	}
	
	function check_mail(strmail) {
		if(strmail == '') return;
		chartosplit = ',;';
		protectchar = '\"';
		temp = '';
		armail = new Array();
		inthechar = false; 
		lt = '<';
		gt = '>'; 
		isclosed = true;
	
		for(i=0;i<strmail.length;i++) {
			thischar = strmail.charAt(i);
			if(thischar == lt && isclosed) isclosed = false;
			if(thischar == gt && !isclosed) isclosed = true;
			if(thischar == protectchar) inthechar = (inthechar)?0:1;
			if(chartosplit.indexOf(thischar) != -1 && !inthechar && isclosed) {
				armail[armail.length] = temp; temp = '';
			} else temp += thischar;
		}
	
		armail[armail.length] = temp; 
	
		for(i=0;i<armail.length;i++) {
			thismail = armail[i]; strPat = /(.*)<(.*)>/;
			matchArray = thismail.match(strPat); 
			if (matchArray != null) strEmail = matchArray[2];
			else {
				strPat = /([-a-zA-Z0-9_$+.]+@[-a-zA-Z0-9_.]+[-a-zA-Z0-9_]+)((.*))/; matchArray = thismail.match(strPat); 
				if (matchArray != null) strEmail = matchArray[1];
				else strEmail = thismail;
			}
			if(strEmail.charAt(0) == '\"' && strEmail.charAt(strEmail.length-1) == '\"') strEmail = strEmail.substring(1,strEmail.length-1)
			if(strEmail.charAt(0) == '<' && strEmail.charAt(strEmail.length-1) == '>') strEmail = strEmail.substring(1,strEmail.length-1)
	
			strPat = /([-a-zA-Z0-9_$+.]+@[-a-zA-Z0-9_.]+[-a-zA-Z0-9_]+)((.*))/;
			matchArray = strEmail.match(strPat); 
			if(matchArray == null)
				error_msg[error_msg.length] = strEmail;
		}
	}
	
	
	</script>
	";
	
	$tcontent = eregi_replace("<!--%UM_JS%-->",$jssource,$tcontent);
	$tcontent = eregi_replace("<!--%UM_SID%-->",$sid,$tcontent);
	$tcontent = eregi_replace("<!--%UM_LID%-->",$lid,$tcontent);

	$body = stripslashes($body);


	if(isset($rtype)) {

		$foldertype = ($folder == "inbox")?"headers":"folderheaders";
		$mail_info = $sess[$foldertype][$ix];
		$filename = $mail_info["localname"];

		if(!file_exists($filename)) die("<script>location = 'msglist.php?msg=".urlencode($error_retrieving)."&folder=".urlencode($folder)."&pag=$pag&sid=$sid&lid=$lid&refr=true';</script>");

		$result = read_file($filename);


		$md = new mime_decode();
		$md->charset 			= $default_char_set;
		$md->use_html 			= $show_advanced;
		$md->timezone 			= $timezone;
		
		$md->initialize($result);

		$email = $md->content;

		$tmpbody = $email["body"];

		$subject = $mail_info["subject"];

		$ARFrom = $email["from"];
		$useremail = $sess["email"];

		// from
		$name = $ARFrom[0]["name"];
		$thismail = $ARFrom[0]["mail"];
		$fromreply = "\"$name\" <$thismail>";

		// To
		$ARTo = $email["to"];


		for($i=0;$i<count($ARTo);$i++) {
			$name = $ARTo[$i]["name"]; $thismail = $ARTo[$i]["mail"];
			if(isset($toreply)) $toreply .= ", \"$name\" <$thismail>";
			else $toreply = "\"$name\" <$thismail>";
		}

		// CC
		$ARCC = $email["cc"];
		for($i=0;$i<count($ARCC);$i++) {
			$name = $ARCC[$i]["name"]; $thismail = $ARCC[$i]["mail"];
			if(isset($ccreply)) $ccreply .= ", \"$name\" <$thismail>";
			else $ccreply = "\"$name\" <$thismail>";
		}

		function clear_names($strMail) {
			global $md;
			$strMail = $md->get_names($strMail);
			for($i=0;$i<count($strMail);$i++) {
				$thismail = $strMail[$i];
				$thisline = ($thismail["mail"] != $thismail["name"])?"\"".$thismail["name"]."\""." <".$thismail["mail"].">":$thismail["mail"];
				if(!empty($thismail["mail"]) && strpos($result,$thismail["mail"]) === false) {
					if(!empty($result)) $result .= ", ".$thisline;
					else $result = $thisline;
				}
			}
			return $result;
		}


		$allreply = clear_names($fromreply.", ".$toreply);
		$ccreply = clear_names($ccreply);
		$fromreply = clear_names($fromreply);

		$msgsubject = $email["subject"];

		$fromreply_quote 	= $fromreply;
		$toreply_quote		= $toreply;
		$ccreply_quote		= $ccreply;
		$msgsubject_quote	= $msgsubject;

		if(!empty($show_advanced)) {
			$fromreply_quote 	= htmlspecialchars(htmlspecialchars($fromreply_quote));
			$toreply_quote		= htmlspecialchars(htmlspecialchars($toreply_quote));
			$ccreply_quote		= htmlspecialchars(htmlspecialchars($ccreply_quote));
			$msgsubject_quote	= htmlspecialchars(htmlspecialchars($msgsubject_quote));
			$linebreak			= "<br>";

		} else {
			$tmpbody			= strip_tags($tmpbody);
			$quote_string = "> ";
			$tmpbody = $quote_string.ereg_replace("\n","\n$quote_string",$tmpbody);
		}

$body = "
$reply_delimiter$linebreak
$reply_from_hea ".ereg_replace("(\")","",$fromreply_quote)."$linebreak
$reply_to_hea ".ereg_replace("(\")","",$toreply_quote);

if(!empty($ccreply)) {
	$body .= "$linebreak
$reply_cc_hea ".ereg_replace("(\")","",$ccreply_quote);
}

$body .= "$linebreak
$reply_subject_hea ".$msgsubject_quote."$linebreak
$reply_date_hea ".@date($date_format,$email["date"])."$linebreak
$linebreak
$tmpbody";


		if(!empty($show_advanced)) {
			$body = "
<br>
<BLOCKQUOTE dir=ltr style=\"PADDING-RIGHT: 0px; PADDING-LEFT: 5px; MARGIN-LEFT: 5px; BORDER-LEFT: #000000 2px solid; MARGIN-RIGHT: 0px\">
  <DIV style=\"FONT: 10pt arial\">
  $body
  </DIV>
</BLOCKQUOTE>
";
		}

		switch($rtype) {
		case "reply":
			if(!eregi("^$reply_prefix",trim($subject))) $subject = "$reply_prefix $subject";
			$to = $fromreply;
			break;
		case "replyall":
			if(!eregi("^$reply_prefix",trim($subject))) $subject = "$reply_prefix $subject";
			$to = $allreply;
			$cc = $ccreply;
			break;
		case "forward":
			if(!eregi("^$forward_prefix",trim($subject))) $subject = "$forward_prefix $subject";
			$sessiontype = ($folder == "inbox")?"headers":"folderheaders";
			$mail_info = $sess[$sessiontype][$ix];
			$localname = $mail_info["localname"];
			if(file_exists($localname)) {

				if(!is_array($sess["attachments"])) $ind = 0;
				else $ind = count($sess["attachments"]);

				$filename = $userfolder."_attachments/".basename($localname);
			    copy($localname, $filename);

				$sess["attachments"][$ind]["localname"] = $filename;
				$sess["attachments"][$ind]["name"] = substr(ereg_replace("[^A-Za-z0-9]","_",$mail_info["subject"]),0,20).".eml";
				$sess["attachments"][$ind]["type"] = "message/rfc822";
				$sess["attachments"][$ind]["size"] = filesize($filename);
				save_session($sess);
	
			}
			break;
		}
		if($add_sig && !empty($signature)) 
			if(!empty($show_advanced)) $body = "<br><br>--<br>$signature<br><br>$body";
			else $body = "\r\n\r\n--\r\n$signature\r\n\r\n$body";
	} else

		if($add_sig && !empty($signature) && empty($body)) 
			if(!empty($show_advanced)) $body = "<br><br>--<br>$signature<br><br>$body";
			else $body = "\r\n\r\n--\r\n$signature\r\n\r\n$body";


	$strto = (isset($nameto) && eregi("([-a-z0-9_$+.]+@[-a-z0-9_.]+[-a-z0-9_])",$mailto))?
	"<input class=textbox style=\"width : 200px;\" type=text size=20 name=to value=\"&quot;".htmlspecialchars(stripslashes($nameto))."&quot; <".htmlspecialchars(stripslashes($mailto)).">\">
	":"<input class=textbox style=\"width : 200px;\" type=text size=20 name=to value=\"".htmlspecialchars(stripslashes($to))."\">";

	$strcc = "<input class=textbox style=\"width : 200px;\" type=text size=20 name=cc value=\"".htmlspecialchars(stripslashes($cc))."\">";
	$strbcc = "<input class=textbox style=\"width : 200px;\" type=text size=20 name=bcc value=\"".htmlspecialchars(stripslashes($bcc))."\">";
	$strsubject = "<input class=textbox style=\"width : 200px;\" type=text size=20 name=subject value=\"".htmlspecialchars(stripslashes($subject))."\">";

	$attbegin = strpos($tcontent,"<!--%UM_ATTACH_BEGIN%-->");
	$attend = strpos($tcontent,"<!--%UM_ATTACH_END%-->")+22;

	$noattbegin = strpos($tcontent,"<!--%UM_NO_ATTACH_BEGIN%-->");
	$noattend = strpos($tcontent,"<!--%UM_NO_ATTACH_END%-->")+25;
	
	
	if(is_array($attachs = $sess["attachments"]) && count($sess["attachments"]) != 0) {
		$cleantext = substr($tcontent,$attbegin+24,$attend-$attbegin-46);
	
		$loopbegin = strpos($cleantext,"<!--%UM_AT_LOOP_BEGIN%-->");
		$loopend = strpos($cleantext,"<!--%UM_AT_LOOP_END%-->")+23;
		
		$cleanline = substr($cleantext,$loopbegin+25,$loopend-$loopbegin-48);

		for($i=0;$i<count($attachs);$i++) {
			$thisline = $cleanline;
			$thisline = eregi_replace("<!--%UM_AT_NAME%-->",htmlspecialchars($attachs[$i]["name"]),$thisline);
			$thisline = eregi_replace("<!--%UM_AT_SIZE%-->",ceil($attachs[$i]["size"]/1024)."Kb",$thisline);
			$thisline = eregi_replace("<!--%UM_AT_TYPE%-->",$attachs[$i]["type"],$thisline);
			$thisline = eregi_replace("<!--%UM_AT_DEL%-->","javascript:upwin($i)",$thisline);
			$loopresult .= $thisline;
		}
		
		$result = substr($cleantext,0,$loopbegin).$loopresult.substr($cleantext,$loopend);
		$tcontent = substr($tcontent,0,$attbegin).$result.substr($tcontent,$noattend);
	} else { 
		$cleantext = substr($tcontent,$noattbegin+27,$noattend-$noattbegin-52);
		$tcontent = substr($tcontent,0,$attbegin).$cleantext.substr($tcontent,$noattend);
	}
	if(!empty($show_advanced)) {
		$editor = load_template($adv_editor_template,Array("advanced_editor"));
		$txtarea = $editor."\r<input type=hidden name=body>";

	echo("<div id=\"hiddenCompose\" style=\"position: absolute; left: 3; top: -100; visibility: visible; z-index: 3\">	      
		<form name=\"hiddencomposeForm\">
		<textarea name=\"hiddencomposeFormTextArea\">$body</textarea>
		</form>
		</div>");

	} else {
		$txtarea = "<textarea cols=50 rows=15 name=body>".htmlspecialchars(stripslashes($body))."</textarea>";
	}
	
	$tcontent = eregi_replace("<!--%UM_SIG%-->",$signature,$tcontent);
	$tcontent = eregi_replace("<!--%UM_TO%-->",$strto,$tcontent);
	$tcontent = eregi_replace("<!--%UM_CC%-->",$strcc,$tcontent);
	$tcontent = eregi_replace("<!--%UM_BCC%-->",$strbcc,$tcontent);
	$tcontent = eregi_replace("<!--%UM_SUBJECT%-->",$strsubject,$tcontent);
	$tcontent = eregi_replace("<!--%UM_TEXT_EDITOR%-->",$txtarea,$tcontent);
	$tcontent = eregi_replace("<!--%UM_CURRENT_FOLDER%-->",htmlspecialchars($folder),$tcontent);

	echo($tcontent);

}

?>

