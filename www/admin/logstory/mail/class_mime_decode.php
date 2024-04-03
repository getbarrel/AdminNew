<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/



class mime_decode {

	var $content			= Array();
	var $use_html			= false;
	var $charset			= "iso-8859-1";
	var $timezone			= "+0000";


	// internal
	var $_msgbody			= "";


	function mime_encode_headers($string) {
		if(empty($string)) return;
        if(!eregi("^([[:print:]]*)$",$string))
    		$string = "=?".$this->charset."?Q?".str_replace("+","_",str_replace("%","=",urlencode($string)))."?=";
		return $string;
	}

	function add_body($strbody) {
		if(empty($this->_msgbody))
			$this->_msgbody = $strbody;
		else
			$this->_msgbody .= "\r\n<br>\r\n<br>\r\n<hr>\r\n<br>\r\n$strbody";
	}


	function decode_mime_string($subject) {
		$string = $subject;

		if(($pos = strpos($string,"=?")) === false) return $string;

		while(!($pos === false)) {

			$newresult .= substr($string,0,$pos);
			$string = substr($string,$pos+2,strlen($string));
			$intpos = strpos($string,"?");
			$charset = substr($string,0,$intpos);
			$enctype = strtolower(substr($string,$intpos+1,1));
			$string = substr($string,$intpos+3,strlen($string));
			$endpos = strpos($string,"?=");
			$mystring = substr($string,0,$endpos);
			$string = substr($string,$endpos+2,strlen($string));

			if($enctype == "q") $mystring = quoted_printable_decode(ereg_replace("_"," ",$mystring)); 
			else if ($enctype == "b") $mystring = base64_decode($mystring);

			$newresult .= $mystring;
			$pos = strpos($string,"=?");

		}

		$result = $newresult.$string;

		if(ereg("koi8", $subject)) $result = convert_cyr_string($result, "k", "w");
		return $result;

	}

	function decode_header($header) {
		$headers = explode("\r\n",$header);
		$decodedheaders = Array();
		for($i=0;$i<count($headers);$i++) {
			$thisheader = $headers[$i];
			if(!ereg("^[A-Za-z_-]+:",$thisheader))
				$decodedheaders[$lasthead] .= " $thisheader";
			else {
				$dbpoint = strpos($thisheader,":");
				$headname = strtolower(substr($thisheader,0,$dbpoint));
				$headvalue = trim(substr($thisheader,$dbpoint+1));
				if(!empty($decodedheaders[$headname])) $decodedheaders[$headname] .= "; $headvalue";
				else $decodedheaders[$headname] = $headvalue;
				$lasthead = $headname;
			}
		}
		return $decodedheaders;
	}


	function fetch_structure($email) {
		$ARemail = Array();
		$separador = "\r\n\r\n";
		$header = trim(substr($email,0,strpos($email,$separador)));
		$bodypos = strlen($header)+strlen($separador);
		$body = substr($email,$bodypos,strlen($email)-$bodypos);
		$ARemail["header"] = $header; $ARemail["body"] = $body;
		return $ARemail;
	}

	function get_names($strmail) {
		$ARfrom = Array();
		$strmail = stripslashes(ereg_replace("\t","",ereg_replace("\n","",ereg_replace("\r","",$strmail))));
		if(empty(trim($strmail))) return $ARfrom;

		$armail = Array();
		$counter = 0;  $inthechar = 0;
		$chartosplit = ",;"; $protectchar = "\""; $temp = "";
		$lt = "<"; $gt = ">";
		$closed = 1;

		for($i=0;$i<strlen($strmail);$i++) {
			$thischar = $strmail[$i];
			if($thischar == $lt && $closed) $closed = 0;
			if($thischar == $gt && !$closed) $closed = 1;
			if($thischar == $protectchar) $inthechar = ($inthechar)?0:1;
			if(!(strpos($chartosplit,$thischar) === false) && !$inthechar && $closed) {
				$armail[] = $temp; $temp = "";
			} else 
				$temp .= $thischar;
		}

		if(trim(!empty($temp)))
			$armail[] = trim($temp);

		for($i=0;$i<count($armail);$i++) {
			$thisPart = trim(eregi_replace("^\"(.*)\"$", "\\1", trim($armail[$i])));
			if(!empty($thisPart)) {
				if (eregi("(.*)<(.*)>", $thisPart, $regs)) {
					$email = trim($regs[2]);
					$name = trim($regs[1]);
				} else {
					if (eregi("([-a-z0-9_$+.]+@[-a-z0-9_.]+[-a-z0-9_]+)((.*))", $thisPart, $regs)) {
						$email = $regs[1];
						$name = $regs[2];
					} else
						$email = $thisPart;
				}
				$email = eregi_replace("^\<(.*)\>$", "\\1", $email);
				$name = eregi_replace("^\"(.*)\"$", "\\1", trim($name));
				$name = eregi_replace("^\((.*)\)$", "\\1", $name);
				if (empty($name)) $name = $email;
				if (empty($email)) $email = $name;
				$ARfrom[$i]["name"] = $this->decode_mime_string($name);
				$ARfrom[$i]["mail"] = $email;
				unset($name);unset($email);
			}
		}
		return $ARfrom;
	}

	function build_alternative_body($ctype,$body) {

		$boundary = $this->get_boundary($ctype);
		$part = $this->split_parts($boundary,$body);
		$thispart = ($this->use_html)?$part[1]:$part[0];
		$email = $this->fetch_structure($thispart);
		$header = $email["header"];
		$body = $email["body"];
		$headers = $this->decode_header($header);
		$body = $this->compile_body($body,$headers["content-transfer-encoding"],$headers["content-type"]);
		$this->add_body($body);

	}

	function build_complex_body($ctype,$body) {
		global $sid,$lid,$ix,$folder;

		$Rtype = trim(substr($ctype,strpos($ctype,"type=")+5,strlen($ctype)));

		if(strpos($Rtype,";") != 0)
			$Rtype = substr($Rtype,0,strpos($Rtype,";"));
		if(substr($Rtype,0,1) == "\"" && substr($Rtype,-1) == "\"")
			$Rtype = substr($Rtype,1,strlen($Rtype)-2);


		$boundary = $this->get_boundary($ctype);
		$part = $this->split_parts($boundary,$body);

		for($i=0;$i<count($part);$i++) {
			$email = $this->fetch_structure($part[$i]);

			$header = $email["header"];
			$body = $email["body"];
			$headers = $this->decode_header($header);

			$ctype = $headers["content-type"];
			$cid = $headers["content-id"];
			$Actype = split(";",$headers["content-type"]);
			$types = split("/",$Actype[0]); $rctype = strtolower($Actype[0]);

			$is_download = (ereg("name=",$headers["content-disposition"].$headers["content-type"]) || !empty($headers["content-id"]));

			if($rctype == "multipart/alternative")

				$this->add_body($this->build_alternative_body($ctype,$body));

			elseif($rctype == "text/plain" && !$is_download) {

				$body = $this->compile_body($body,$headers["content-transfer-encoding"],$headers["content-type"]);
				$this->add_body($this->build_text_body($body));

			} elseif($rctype == "text/html" &&  !$is_download) {

				$body = $this->compile_body($body,$headers["content-transfer-encoding"],$headers["content-type"]);
				if(empty($this->use_html)) $body = $this->build_text_body(strip_tags($body));

				$this->add_body($body);

			} elseif(!empty($is_download)) {

				$thisattach = $this->build_attach($header,$body,$boundary,$i);

				if(!empty($cid)) {
					if(substr($cid,0,1) == "<" && substr($cid,-1) == ">")
						$cid = substr($cid,1,strlen($cid)-2);
					$cid = "cid:$cid";
					$thisfile = "download.php?sid=$sid&lid=$lid&folder=".urlencode($folder)."&ix=".$ix."&bound=".base64_encode($thisattach["boundary"])."&part=".$thisattach["part"]."&filename=".urlencode($thisattach["name"]);
					$this->_msgbody = str_replace($cid,$thisfile,$this->_msgbody);
				}
			} else
				$this->process_message($header,$body);

		}
	}

	function build_text_body($body) {
		$body = ereg_replace("\n","",$body);
		$body = ereg_replace("\r","<br>\r",$this->make_link_clickable(htmlspecialchars($body)));
		return "<font face=\"Courier New\" size=2>$body</font>";
	}

	function decode_qp($text) {
		$text = ereg_replace("\r\n","\r",$text);
		$text = ereg_replace("=\r","",$text);
		$text = ereg_replace("\r","\r\n",$text);
		$text = quoted_printable_decode($text);
		return $text;
	}

	function make_link_clickable($str){

		$str = eregi_replace("([[:space:]])((f|ht)tps?:\/\/[a-z0-9~#%@\&:=?+\/\.,_-]+[a-z0-9~#%@\&=?+\/_-]+)", "\\1<a class=autolink href=\"\\2\" target=\"_blank\">\\2</a>", $str); //http 
		$str = eregi_replace("([[:space:]])(www\.[a-z0-9~#%@\&:=?+\/\.,_-]+[a-z0-9~#%@\&=?+\/_-]+)", "\\1<a class=autolink href=\"http://\\2\" target=\"_blank\">\\2</a>", $str); // www. 
		$str = eregi_replace("([[:space:]])([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})","\\1<a class=autolink href=\"mailto:\\2\">\\2</a>", $str); // mail 
		
		$str = eregi_replace("^((f|ht)tp:\/\/[a-z0-9~#%@\&:=?+\/\.,_-]+[a-z0-9~#%@\&=?+\/_-]+)", "<a href=\"\\1\" target=\"_blank\">\\1</a>", $str); //http 
		$str = eregi_replace("^(www\.[a-z0-9~#%@\&:=?+\/\.,_-]+[a-z0-9~#%@\&=?+\/_-]+)", "<a class=autolink href=\"http://\\1\" target=\"_blank\">\\1</a>", $str); // www. 
		$str = eregi_replace("^([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})","<a class=autolink href=\"mailto:\\1\">\\1</a>", $str); // mail 

		return $str;
	}

	function process_message($header,$body) {
		$mail_info = $this->get_mail_info($header);

		$ctype = $mail_info["content-type"];
		$ctenc = $mail_info["content-transfer-encoding"];

		if(empty($ctype)) $ctype = "text/plain";

		$type = $ctype;

		$ctype = split(";",$ctype);
		$types = split("/",$ctype[0]);

		$maintype = trim(strtolower($types[0]));
		$subtype = trim(strtolower($types[1]));
		switch($maintype) {
		case "text":
			$body = $this->compile_body($body,$ctenc,$mail_info["content-type"]);
			switch($subtype) {
			case "html":
				if(empty($this->use_html)) {
					$body = eregi_replace("(\r|\n)","",$body);
					$body = eregi_replace("<br[ ]?[/]?>","\r\n",$body);
					$body = eregi_replace("</p>","\r\n\r\n",$body);
					$body = $this->build_text_body(strip_tags($body));
				}
				$msgbody = $body;
				break;
			default:
				$msgbody = $this->build_text_body($body);
				break;
			}
			$this->add_body($msgbody);
			break;
		case "multipart":
			if(ereg($subtype,"signed,mixed,related"))
				$subtype = "complex";

			switch($subtype) {
			case "alternative":
				$msgbody = $this->build_alternative_body($ctype[1],$body);
				break;
			case "complex":
				$msgbody = $this->build_complex_body($type,$body);
				break;
			default:
				$thisattach = $this->build_attach($header,$body,"",0);
			}
			break;
		default:
			$thisattach = $this->build_attach($header,$body,"",0);
		}

	}

	function build_attach($header,$body,$boundary,$part) {

		global $mail,$temporary_directory,$userfolder;

		$headers = $this->decode_header($header);
		$cdisp = $headers["content-disposition"];
		$ctype = $headers["content-type"]; $ctype2 = explode(";",$ctype); $ctype2 = $ctype2[0];
		
		$Atype = split("/",$ctype);
		$Acdisp = split(";",$cdisp);

		$tenc = $headers["content-transfer-encoding"];

		if($Atype[0] == "message") {
			$divpos = strpos($body,"\n\r");
			$attachheader = substr($body,0,$divpos);
			$attachheaders = $this->decode_header($attachheader);
			$filename = $this->decode_mime_string($attachheaders["subject"]);
			if(empty($filename))
				$filename = uniqid("");
			$filename = substr(ereg_replace("[^A-Za-z0-9]","_",$filename),0,20).".eml";
		} else {
			$fname = $Acdisp[1];
			$filename = substr($fname,strpos($fname,"filename=")+9,strlen($fname));
			if(empty($filename)) 
				$filename = substr($ctype,strpos($ctype,"name=")+5,strlen($ctype));
			if(substr($filename,0,1) == "\"" && substr($filename,-1) == "\"")
				$filename = substr($filename,1,strlen($filename)-2);
			$filename = $this->decode_mime_string($filename);
		}
		$is_embebed = (empty($headers["content-id"]));

		if($Atype[0] != "message" && !$is_embebed)
			$body = $this->compile_body($body,$tenc,$ctype);

		$temp_array["name"] = $filename;
		$temp_array["size"] = strlen($body);
		$temp_array["temp"] = $temp;
		$temp_array["content-type"] = $ctype2;
		$temp_array["content-disposition"] = $Acdisp[0];
		$temp_array["boundary"] = $boundary;
		$temp_array["part"] = $part;

		$indice = count($this->content["attachments"]);

		if(!empty($is_embebed))
			$this->content["attachments"][$indice] = $temp_array;

		return $temp_array;

	}

	function compile_body($body,$enctype,$ctype) {

		$enctype = explode(" ",$enctype); $enctype = $enctype[0];
		if(strtolower($enctype) == "base64")
			$body = base64_decode($body);
		elseif(strtolower($enctype) == "quoted-printable")
			$body = $this->decode_qp($body);

		if(ereg("koi8", $ctype)) $body = convert_cyr_string($body, "k", "w");

		return $body;

	}

	function download_attach($header,$body,$down=1) {
		$headers = $this->decode_header($header);

		$cdisp = $headers["content-disposition"];
		$ctype = $headers["content-type"];

		$type = split(";",$ctype); $type = $type[0];
		$Atype = split("/",$ctype);
		$Acdisp = split(";",$cdisp);
		$tenc = strtolower($headers["content-transfer-encoding"]);

		if($Atype[0] == "message") {
			$divpos = strpos($body,"\n\r");
			$attachheader = substr($body,0,$divpos);
			$attachheaders = $this->decode_header($attachheader);
			$filename = $this->decode_mime_string($attachheaders["subject"]);
			if(empty($filename))
				$filename = uniqid("");
			$filename = substr(ereg_replace("[^A-Za-z0-9]","_",$filename),0,20);
			$filename .= ".eml";
		} else {
			$fname = $Acdisp[1];
			$filename = substr($fname,strpos(strtolower($fname),"filename=")+9,strlen($fname));
			if(empty($filename)) 
				$filename = substr($ctype,strpos(strtolower($ctype),"name=")+5,strlen($ctype));
			if(substr($filename,0,1) == "\"" && substr($filename,-1) == "\"")
				$filename = substr($filename,1,strlen($filename)-2);
			$filename = $this->decode_mime_string($filename);
		}

		if($Atype[0] != "message")
			$body = $this->compile_body($body,$tenc,$ctype);

		$content_type = ($down)?"application/octet-stream":strtolower($type);
		$filesize = strlen($body);

		header("Content-Type: $content_type; name=\"$filename\"\r\n"
		."Content-Length: $filesize\r\n");
		$cdisp = ($down)?"attachment":"inline";
		header("Content-Disposition: $cdisp; filename=\"$filename\"\r\n");
		echo($body);
	}

	function get_mail_info($header) {
		$myarray = Array();
		$headers = $this->decode_header($header);

		$message_id = $headers["message-id"];

		if(substr($message_id,0,1) == "<" && substr($message_id,-1) == ">")
			$message_id = substr($message_id,1,strlen($message_id)-2);

		$myarray["content-type"] = $headers["content-type"];
		$myarray["content-transfer-encoding"] = str_replace("GM","-",$headers["content-transfer-encoding"]);
		$myarray["message-id"] = trim($message_id);

		$received	= ereg_replace("  "," ",$headers["received"]);
		$user_date	= ereg_replace("  "," ",$headers["date"]);

		if(eregi("([0-9]{1,2}[ ]+[A-Z]{3}[ ]+[0-9]{4}[ ]+[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})[ ]?((\+|-)[0-9]{4})?",$received,$regs)) {
			//eg. Tue, 4 Sep 2001 16:22:31 -0000
			$mydate = $regs[1];
			$mytimezone = $regs[2];
			if(empty($mytimezone))
				if(eregi("((\\+|-)[0-9]{4})",$user_date,$regs)) $mytimezone = $regs[1];
				else $mytimezone = $this->timezone;
		} elseif(eregi("(([A-Z]{3})[ ]+([0-9]{1,2})[ ]+([0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})[ ]+([0-9]{4}))",$received,$regs)) {
			//eg. Tue Sep 4 16:26:17 2001 (Cubic Circle's style)
			$mydate = $regs[3]." ".$regs[2]." ".$regs[5]." ".$regs[4];
			if(eregi("((\\+|-)[0-9]{4})",$user_date,$regs)) $mytimezone = $regs[1];
			else $mytimezone = $this->timezone;

		} elseif(eregi("([0-9]{1,2}[ ]+[A-Z]{3}[ ]+[0-9]{4}[ ]+[0-9]{1,2}:[0-9]{1,2}:[0-9]{1,2})[ ]?((\+|-)[0-9]{4})?",$user_date,$regs)) {
			//eg. Tue, 4 Sep 2001 16:22:31 -0000 (from Date header)
			$mydate = $regs[1];
			$mytimezone = $regs[2];
			if(empty($mytimezone))
				if(eregi("((\\+|-)[0-9]{4})",$user_date,$regs)) $mytimezone = $regs[1];
				else $mytimezone = $this->timezone;
		} else {
			$mydate		= date("d M Y H:i");
			$mytimezone	= $this->timezone;
		}

		$myarray["date"] = $this->build_mime_date($mydate,$mytimezone);

		$myarray["subject"] = $this->decode_mime_string($headers["subject"]);

		$myarray["from"] = $this->get_names($headers["from"]);
		$myarray["to"] = $this->get_names($headers["to"]);
		$myarray["cc"] = $this->get_names($headers["cc"]);
		$myarray["status"] = $headers["status"];
		$myarray["read"] = $headers["x-um-status"];

		return $myarray;

	}

	function build_mime_date($mydate,$timezone = "+0000") {
		if(!ereg("((\\+|-)[0-9]{4})",$timezone)) $timezone = "+0000";
		$parts = explode(" ",$mydate);
		if(count($parts) < 4) { return time(); }

		$day = $parts[0];

		switch(strtolower($parts[1])) {
			case "jan": $mon = 1; break;
			case "feb":	$mon = 2; break;
			case "mar":	$mon = 3; break;
			case "apr":	$mon = 4; break;
			case "may":	$mon = 5; break;
			case "jun": $mon = 6; break;
			case "jul": $mon = 7; break;
			case "aug": $mon = 8; break;
			case "sep": $mon = 9; break;
			case "oct": $mon = 10; break;
			case "nov": $mon = 11; break;
			case "dec": $mon = 12; break;
		}

		$year = $parts[2];
		$ahours = explode(":",$parts[3]);
		$hour = $ahours[0]; $min = $ahours[1]; $sec = $ahours[2];

		$timezone_oper	= $timezone[0];
		$timezone_hour	= intval("$timezone_oper".substr($timezone,1,2))*3600;
		$timezone_min	= intval("$timezone_oper".substr($timezone,3,2))*60;
		$timezone_diff	= $timezone_hour+$timezone_min;

		$user_timezone_oper	= $this->timezone[0];
		$user_timezone_hour	= intval("$user_timezone_oper".substr($this->timezone,1,2))*3600;
		$user_timezone_min	= intval("$user_timezone_oper".substr($this->timezone,3,2))*60;
		$user_timezone_diff	= $user_timezone_hour+$user_timezone_min;
		$diff 				= $timezone_diff-$user_timezone_diff;

		$mytimestamp	= mktime ($hour, $min, $sec, $mon, $day, $year)-$diff;

		return $mytimestamp;

	}

	function initialize($email) {
		$email = $this->fetch_structure($email);
		$body = $email["body"];
		$header = $email["header"];

		$mail_info = $this->get_mail_info($header);
		$this->process_message($header,$body);
		
		$this->content["headers"] = $header;
		$this->content["date"] = $mail_info["date"];
		$this->content["subject"] = $mail_info["subject"];
		$this->content["message-id"] = $mail_info["message-id"];
		$this->content["from"] = $mail_info["from"];
		$this->content["to"] = $mail_info["to"];
		$this->content["cc"] = $mail_info["cc"];
		$this->content["body"] = $this->_msgbody;
		$this->content["read"] = $mail_info["read"];
	}

	function split_parts($boundary,$body) {
		$startpos = strpos($body,"$boundary")+strlen("$boundary")+2;
		$lenbody = strpos($body,"\r\n$boundary--") - $startpos;
		$body = substr($body,$startpos,$lenbody);
		return split($boundary."\r\n",$body);
	}

	function get_boundary($ctype){
		$boundary = trim(substr($ctype,strpos(strtolower($ctype),"boundary=")+9,strlen($ctype)));
		$boundary = split(";",$boundary);$boundary = $boundary[0];

		if(substr($boundary,0,1) == "\"" && substr($boundary,-1) == "\"")
			$boundary = substr($boundary,1,strlen($boundary)-2);
		$boundary = "--".$boundary;
		return $boundary;
	}

	function set_as($email,$type) {
		$tempmail = $this->fetch_structure($email);
		$thisheader = $tempmail["header"];
		$mail_info = $this->get_mail_info($thisheader);
		$decoded_headers = $this->decode_header($thisheader);
		while(list($key,$val) = each($decoded_headers))
			if (eregi("x-um-status",$key)) {
				$newmail .= ucfirst($key).": $type\r\n"; $headerok = 1;
			} else $newmail .= ucfirst($key).": ".trim($val)."\r\n";
		if(empty($headerok)) $newmail .= "X-UM-Status: $type\r\n";

		$newmail = trim($newmail)."\r\n\r\n".trim($tempmail["body"]);
		return $newmail;
	}
}

?>