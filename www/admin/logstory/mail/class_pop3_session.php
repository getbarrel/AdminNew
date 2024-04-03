<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/



class pop3_session {

	//public
	var $pop_connection		= 0;
	var $pop_server			= "localhost";
	var	$pop_port			= 110;
	var	$pop_error_msg		= "";
	var	$pop_user			= "unknown";
	var	$pop_pass			= "";
	var	$pop_email			= "unknown@localhost";
	var	$charset			= "iso-8859-1";
	var	$timezone			= "+0000";

	//private
	var	$md				= 0;

	function pop3_session() {
		$this->md = new mime_decode();
		$this->md->charset 			= $this->charset;

	}
		
	function pop_get_line() {
		global $enable_debug;
		$buffer = fgets($this->pop_connection,10240);
		$buffer = eregi_replace("(\r|\n)","",$buffer);
		if(!empty($enable_debug)) {
			$output = (eregi("^(\+OK)|^(\-ERR)",$buffer))?"<- <b>".htmlspecialchars($buffer)."</b>":htmlspecialchars($buffer);
			echo("<font style=\"font-size:12px; font-family: Courier New; background-color: white; color: black;\"> $output</font><br>\r\n");flush();
		}
		return $buffer;
	}

	function pop_send_command($cmd) {
		global $enable_debug;
		if(!empty($this->pop_connection)) {
			if(!empty($enable_debug)) {
				$output = (eregi("^(PASS)",$cmd))?"PASS ****":$cmd;
				echo("<font style=\"font-size:12px; font-family: Courier New; background-color: white; color: black;\">-&gt; <em><b>".htmlspecialchars($output)."</b></em></font><br>\r\n");flush();
			}
			fwrite($this->pop_connection,"$cmd\r\n");
			return 1;
		}
		return 0;
	}

	function pop_connect() {
		$this->pop_connection = fsockopen($this->pop_server, $this->pop_port, $errno, $errstr, 60);
		if(!empty($this->pop_connection)) {
			$buffer = $this->pop_get_line();
			if(ereg("^(\+OK)",$buffer)) return 1;
			else return 0;
		}
		return 0;
	}

	function pop_auth() {
		global $userfolder,$error_permiss,$temporary_directory,$idle_timeout,$use_email_as_user_pop3;
		if(!empty($this->pop_connection)) {

			$myuser = ($use_email_as_user_pop3)?$this->pop_email:$this->pop_user;

			$this->pop_send_command("USER $myuser");
			$buffer = $this->pop_get_line();
			if(ereg("^(\+OK)",$buffer)) {
				$this->pop_send_command("PASS ".$this->pop_pass);
				$buffer = $this->pop_get_line();

				if(ereg("^(\+OK)",$buffer)) { 
					if(!file_exists($userfolder))
						if(!@mkdir($userfolder,0700)) die("<h1><br><br><br><center>$error_permiss</center></h1>");
					if(!file_exists($userfolder."inbox"))
						mkdir($userfolder."inbox",0700);
					if(!file_exists($userfolder."trash"))
						mkdir($userfolder."trash",0700);
					if(!file_exists($userfolder."sent"))
						mkdir($userfolder."sent",0700);
					if(!file_exists($userfolder."sent"))
						mkdir($userfolder."sent",0700);
					if(!file_exists($userfolder."_attachments"))
						mkdir($userfolder."_attachments",0700);
					if(!file_exists($userfolder."_infos"))
						mkdir($userfolder."_infos",0700);
					$sessiondir = $temporary_directory."_sessions/";

					// Clean old sessions
					$all=opendir($sessiondir); 
					while ($file=readdir($all)) { 
						$thisfile = $sessiondir.$file;
						if (is_file($thisfile)) {
							$idle = intval((time()-@filemtime($thisfile))/60);
							if(($idle_timeout+10) < $idle)
								@unlink($thisfile);
						}
					}
					closedir($all); 
					unset($all);

					return 1;
				}
				else { $this->pop_error_msg = $buffer; return 0; }
			} else return 0;
		}
		return 0;
	}

	function pop_retr_msg($mnum,$mid,$msize=10000,$check=1,$filename) {
		global $pop_use_top,$appname,$appversion,$error_retrieving;
		if(!empty($check)) {
			$this->pop_send_command("TOP $mnum 0");
			$buffer = $this->pop_get_line();
			if(ereg("^(\+OK)",$buffer)) {
				unset($header);
				while (!feof($this->pop_connection)) {
					$buffer = $this->pop_get_line();
					if(trim($buffer) == ".") break;
					$header .= "$buffer\r\n";
				}
				$mail_info = $this->md->get_mail_info($header);
				$mailmid = $mail_info["message-id"];

/*				print_r($mid); echo("<br>"); */

				if($mid != md5(trim($mailmid))) {
					$this->pop_error_msg = $error_retrieving;
					return 0;
				}
			} else return 0;
		}

		$command = ($pop_use_top)?"TOP $mnum $msize":"RETR $mnum";
		$this->pop_send_command($command);
		$buffer = $this->pop_get_line();

		if(!ereg("^(\+OK)",$buffer)) { $this->pop_error_msg = $buffer; return 0; }
		while (!feof($this->pop_connection)) {
			$buffer = ereg_replace("(\n|\r)","",$this->pop_get_line());
			if(trim($buffer) == ".") break;
			$msg .= "$buffer\r\n";
		}

		$msg = "X-Decoded-By: $appname $appversion\r\n".$msg;
		$parts = $this->md->fetch_structure($msg);
		$header = $parts["header"];
		$mail_info = $this->md->get_mail_info($header);

		$tmpfile = fopen($filename,"wb+");
		fwrite($tmpfile,$msg);
		fclose($tmpfile);
		return $msg;
	}

	function pop_dele_msg($mnum,$mid,$msize,$send_to_trash = 1,$filename) {
		global $userfolder,$error_retrieving;
		$this->pop_send_command("TOP $mnum 0");
		$buffer = $this->pop_get_line();
		if(ereg("^(\+OK)",$buffer)) {
			unset($header);
			while (!feof($this->pop_connection)) {
				$buffer = $this->pop_get_line();
				if(trim($buffer) == ".") break;
				if(strlen($buffer) > 3) 
					$header .= "$buffer\r\n";
			}
			$mail_info = $this->md->get_mail_info($header);

			if($mid != md5($mail_info["message-id"])) {
				$this->pop_error_msg = $error_retrieving;
				return 0;
			}
			$filename = basename($filename);

			$flocalname = $userfolder."inbox/$filename";
			$flocaltrashname = $userfolder."trash/$filename";

			if ($send_to_trash && !file_exists($flocalname)) {
				$tempmail = $this->pop_retr_msg($mnum,$mid,$msize,0,$flocalname);
				$tempmail = $this->md->set_as($tempmail,0);
				$tmpfile = fopen($flocalname,"wb+");
				fwrite($tmpfile,$tempmail);
				fclose($tmpfile);
			}
			if(!empty($send_to_trash))
				@copy($flocalname,$flocaltrashname);
			@unlink($flocalname);
			$this->pop_send_command("DELE $mnum");
			$buffer = $this->pop_get_line();
			if(!ereg("^(\+OK)",$buffer)) {
				$this->pop_error_msg = $buffer;
				return 0;
			} else return 1;
		}
		$this->pop_error_msg = $buffer;
		return 0;
	}

	function pop_list_msgs() {

		$this->md->timezone = $this->timezone;

		global $userfolder,$use_progress_bar;
		$msglist = Array();
		$this->pop_send_command("LIST");
		unset($buffer);
		$buffer = $this->pop_get_line();
		if(ereg("^(\+OK)",$buffer)) {
			$counter = 0;
			while (!feof($this->pop_connection)) {
				$buffer = $this->pop_get_line();
				if(trim($buffer) == ".") break;
				$msgs = split(" ",$buffer);
				if(is_numeric($msgs[0])) {
					$msglist[$counter]["id"] = $counter+1; //$msgs[0];
					$msglist[$counter]["msg"] = $msgs[0];
					$msglist[$counter]["size"] = $msgs[1];
					$counter++;
				}
			}
			if(count($msglist) == 0) return $msglist;

			for($i=0;$i<count($msglist);$i++) {
				$this->pop_send_command("TOP ".$msglist[$i]["msg"]." 0");
				$buffer = $this->pop_get_line();
				if(ereg("^(\+OK)",$buffer)) {
					while (!feof($this->pop_connection)) {
						$buffer = $this->pop_get_line();
						if(trim($buffer) == ".") break;
						if(strlen($buffer) > 3) 
							$header .= "$buffer\r\n";
					}
					$mail_info = $this->md->get_mail_info($header);
					$msglist[$i]["date"] = $mail_info["date"];
					$msglist[$i]["subject"] = $mail_info["subject"];
					$msglist[$i]["message-id"] = $mail_info["message-id"];
					$msglist[$i]["from"] = $mail_info["from"];
					$msglist[$i]["fromname"] = $mail_info["from"][0]["name"];
					$msglist[$i]["to"] = $mail_info["to"];
					$msglist[$i]["cc"] = $mail_info["cc"];
					$msglist[$i]["headers"] = $header;
					$msglist[$i]["attach"] = (eregi("(multipart/mixed|multipart/related|application)",$mail_info["content-type"]))?1:0;

					$flocalname = trim($userfolder."inbox/".md5(trim($mail_info["subject"].$mail_info["date"].$mail_info["message-id"])).".eml");

					$msglist[$i]["localname"] = $flocalname;
					$msglist[$i]["read"] = file_exists($flocalname)?1:0;
				}
				$header = "";
			}
		}
		
		return $msglist;
	}
	function pop_disconnect() {
		$this->pop_send_command("QUIT");
		$tmp = $this->pop_get_line();
        fclose($this->pop_connection);
		return 1;
	}
	function pop_reset() {
		$this->pop_send_command("RSET");
		$buffer = $this->pop_get_line();
		if(!ereg("^(\+OK)",$buffer)) {
			$this->pop_error_msg = $buffer;
			return 0;
		} else return 1;
	}
}
?>
