<?php
/************************************************************************
UebiMiau is a GPL'ed software developed by 

 - Aldoir Ventura - aldoir@users.sourceforge.net
 - http://uebimiau.sourceforge.net

Fell free to contact, send donations or anything to me :-)
San Paulo - Brazil
*************************************************************************/



if(strlen($f_user) > 0 && strlen($f_pass) > 0) {
	if(!empty($allow_user_change)) {
		if(!empty($lng)) $lid = $lng;
		else { $lid = $default_language; }
	} else
		$lid = $default_language;
}

// get a list of messages in a especified folder (file.eml)

function build_local_list($folder) {

	global $default_char_set,$mime_show_html,$timezone,$server_time_zone;

	$md = new mime_decode();
	$md->charset 			= $default_char_set;
	$md->use_html			= $mime_show_html;
	$md->timezone			= $timezone;

	$i = 0;
	$msglist = Array();
	$d = dir($folder);
	$dirsize = 0;
	while($entry=$d->read()) {
		$fullpath = "$folder/$entry";
		if(	is_file($fullpath)) {
			$thisheader = get_headers_from_file($fullpath);
			$mail_info = $md->get_mail_info($thisheader);
			$decoded_headers = $md->decode_header($thisheader);


			$msglist[$i]["id"]			= $i+1;
			$msglist[$i]["msg"]			= $i;
			$msglist[$i]["size"]		= filesize($fullpath);
			$msglist[$i]["date"]		= $mail_info["date"];
			$msglist[$i]["subject"]		= $mail_info["subject"];
			$msglist[$i]["message-id"]	= $mail_info["message-id"];
			$msglist[$i]["from"]		= $mail_info["from"];
			$msglist[$i]["to"]			= $mail_info["to"];
			$msglist[$i]["cc"]			= $mail_info["cc"];
			$msglist[$i]["headers"]		= $thisheader;
			$msglist[$i]["attach"]		= (eregi("(multipart/mixed|multipart/related|application)",$mail_info["content-type"]))?1:0;
			$msglist[$i]["localname"]	= $fullpath;
			$msglist[$i]["read"]		= $mail_info["read"];

			$i++;

		}
	}
	$d->close();

	return $msglist;
}

function get_usage_graphic($used,$aval) {
	if($used >= $aval) {
		$redsize = 100;
		$graph = "<img src=images/red.gif height=10 width=$redsize>";
	} elseif($used == 0) {
		$greesize = 100;
		$graph = "<img src=images/green.gif height=10 width=$greesize>";
	} else  {
		$usedperc = $used*100/$aval;
		$redsize = ceil($usedperc);
		$greesize = ceil(100-$redsize);
		$red = "<img src=images/red.gif height=10 width=$redsize>";
		$green = "<img src=images/green.gif height=10 width=$greesize>";
		$graph = $red.$green;
	}
	return $graph;
}


function get_total_used_size() {
	global $userfolder;
	$d = dir($userfolder);
	$totalused = 0;
	while($entry=$d->read()) {
		$this_entry = $userfolder.$entry;
		if(is_dir($this_entry) &&
			$entry != ".." && 
			substr($entry,0,1) != "_" && 
			$entry != ".") {
			$totalused += get_folder_size($entry);
		}
	}
	return $totalused;
}


function get_folder_size($folder) {
	global $sess,$userfolder;
	$dirsize = 0;
	if ($folder == "inbox") {
		$thisbox = $sess["headers"];
		for($i=0;$i<count($thisbox);$i++)
			$dirsize += $thisbox[$i]["size"];
	} else { 
		$dir = $userfolder.$folder;
		$d = dir($dir);
		while($entry=$d->read()) {
			$fullpath = "$dir/$entry";
			if(	is_file($fullpath))
				$dirsize += filesize($fullpath);
		}
		$d->close();
		unset($d);
	}
	//echo($folder . " " . $dirsize."<br>");
	return $dirsize;
}

// remove dirs recursivelly
function RmdirR($location) { 
	if (substr($location,-1) <> "/") $location = $location."/";
	$all=opendir($location); 
	while ($file=readdir($all)) { 
		if (is_dir($location.$file) && $file <> ".." && $file <> ".") { 
			RmdirR($location.$file); 
			unset($file); 
		} elseif (!is_dir($location.$file)) { 
			unlink($location.$file); 
			unset($file); 
		}
	}
	closedir($all); 
	unset($all);
	rmdir($location); 
}


// sort an multidimension array
function array_qsort2 (&$array, $column=0, $order="ASC", $first=0, $last= -2) { 
	if($last == -2) $last = count($array) - 1; 
	if($last > $first) { 
		$alpha = $first; 
		$omega = $last; 
		$guess = $array[$alpha][$column]; 
		while($omega >= $alpha) { 
			if($order == "ASC") { 
				while(strtolower($array[$alpha][$column]) < strtolower($guess)) $alpha++; 
				while(strtolower($array[$omega][$column]) > strtolower($guess)) $omega--; 
			} else {
				while(strtolower($array[$alpha][$column]) > strtolower($guess)) $alpha++; 
				while(strtolower($array[$omega][$column]) < strtolower($guess)) $omega--; 
			} 
			if(strtolower($alpha) > strtolower($omega)) break; 
			$temporary = $array[$alpha]; 
			$array[$alpha++] = $array[$omega]; 
			$array[$omega--] = $temporary; 
		} 
		array_qsort2 ($array, $column, $order, $first, $omega); 
		array_qsort2 ($array, $column, $order, $alpha, $last); 
	} 
} 

// load session info
function load_session() {
	global $temporary_directory,$sid;
	$sessionfile = $temporary_directory."_sessions/$sid.usf";
	$result      = Array();

	if(file_exists($sessionfile)) {
		$fp = fopen($sessionfile,"r");
		$result = fread($fp,filesize($sessionfile));
		fclose($fp);
		$result = unserialize(~$result);
	}

	return $result;

}

// save session info
function save_session($array2save) {
	global $temporary_directory,$sid;
	$content = ~serialize($array2save);
	if(!is_writable($temporary_directory)) die("<h3>The folder \"$temporary_directory\" do not exists or the webserver don't have permissions to write</h3>");
	$sessiondir = $temporary_directory."_sessions/";
	if(!file_exists($sessiondir)) mkdir($sessiondir,0700);
	$f = fopen("$sessiondir$sid.usf","wb+") or die("<h3>Could not open session file</h3>");
	fwrite($f,$content);
	fclose($f);
	return 1;
}

function get_tags($begin,$end,$template) {
	$beglen = strlen($begin);
	$endlen = strlen($end);
	$beginpos = strpos($template,$begin);
	$endpos = strpos($template,$end);
	$result["ab-begin"] = $beginpos;
	$result["ab-end"]   = $endpos+$endlen;
	$result["re-begin"] = $beginpos+$beglen;
	$result["re-end"]   = $endpos;
	$result["ab-content"] = substr($template,$beginpos,($endpos+$endlen)-$beginpos);
	$result["re-content"] = substr($template,$beginpos+$beglen,$endpos-$beginpos-$beglen);
	unset($beglen,$endlen,$beginpos,$endpos,$begin,$end,$template);
	return $result;
}


// delete an session (logout)
function delete_session() {
	global $temporary_directory,$sid;
	$sessionfile = $temporary_directory."_sessions/$sid.usf";
	return @unlink($sessionfile);
}

// load settings
function load_prefs() {
	global $userfolder,$sess,$send_to_trash_default,$st_only_ready_default,
	$empty_trash_default,$save_to_sent_default,$sortby_default,$sortorder_default,
	$rpp_default,$add_signature_default,$signature_default,$timezone_default;

	$pref_file = $userfolder."_infos/prefs.upf";
	if(!file_exists($pref_file)) {
		$prefs["real-name"]     = UCFirst(substr($sess["email"],0,strpos($sess["email"],"@")));
		$prefs["reply-to"]      = $sess["email"];
		$prefs["save-to-trash"] = $send_to_trash_default;
		$prefs["st-only-read"]  = $st_only_ready_default;
		$prefs["empty-trash"]   = $empty_trash_default;
		$prefs["save-to-sent"]  = $save_to_sent_default;
		$prefs["sort-by"]       = $sortby_default;
		$prefs["sort-order"]    = $sortorder_default;
		$prefs["rpp"]           = $rpp_default;
		$prefs["add-sig"]       = $add_signature_default;
		$prefs["signature"]     = $signature_default;
		$prefs["timezone"]		= $timezone_default;

	} else {
		$prefs = file($pref_file);
		$prefs = join("",$prefs);
		$prefs = unserialize(~$prefs);
	}
	return $prefs;
}

//save preferences
function save_prefs($prefarray) {
	global $userfolder;
	$pref_file = $userfolder."_infos/prefs.upf";
	$f = fopen($pref_file,"wb+");
	fwrite($f,~serialize($prefarray));
	fclose($f);
}

//read an especified file
function read_file($strfile) {
	if(empty($strfile) || !file_exists($strfile)) return;
	$thisfile = file($strfile);
	while(list($line,$value) = each($thisfile)) {
		$value = ereg_replace("(\r|\n)","",$value);
		$result .= "$value\r\n";
	}
	return $result;
}

//read an especified file
function load_template($strfile,$ar_files) {
	global $language_file,$func,$textout;

	if(empty($strfile) || !file_exists($strfile)) return;
	$thisfile = file($strfile);

	while(list($line,$value) = each($thisfile)) {
		$value = ereg_replace("(\r|\n)","",$value);
		$result .= "$value\r\n";
	}


	for($n=0;$n<count($ar_files);$n++) {
		
		$thisfile = $ar_files[$n].".txt";

		$lg = file("$language_file/$thisfile");
		while(list($line,$value) = each($lg)) {
			if(strpos(";#",$value[0]) === false && ($pos = strpos($value,"=")) != 0 && trim(!empty($value))) {
				$varname  = "<!--%".trim(substr($value,0,$pos))."%-->";
				$varvalue = trim(substr($value,$pos+1));
				$result = eregi_replace($varname,$varvalue,$result);
			}
		}

	}
	$func($textout);
	return $result;
}


//get only headers from a file
function get_headers_from_file($strfile) {
	if(!file_exists($strfile)) return;
	$f = fopen($strfile,"r");
	while(!feof($f)) {
		$result .= ereg_replace("\n","",fread($f,100));
		$pos = strpos($result,"\r\r");
		if(!($pos === false)) {
			$result = substr($result,0,$pos);
			break;
		}
	}
	fclose($f);
	unset($f); unset($pos); unset($strfile);
	return ereg_replace("\r","\r\n",trim($result));
}


function save_file($fname,$fcontent) {
	if(empty($fname)) return;
	$tmpfile = fopen($fname,"wb+");
	fwrite($tmpfile,$fcontent);
	fclose($tmpfile);
	unset($tmpfile,$fname,$fcontent);
}


if(!is_numeric($lid) || $lid >= count($themes)) $lid = $default_language;

$lngpath		 = $themes[$lid]["path"];
$language_file	 = $themes[$lid]["language"];

/********************************************************
Templates
********************************************************/
$message_list_template     = "$lngpath/messagelist.htm";      // Listagem de mensagens
$read_message_template     = "$lngpath/readmsg.htm";          // Ler a mensagem
$folder_list_template      = "$lngpath/folders.htm";          // Listagem de pastas
$search_template           = "$lngpath/search.htm";           // Formulio/Resultado da busca
$login_template            = "$lngpath/login.htm";            // Tela inicial (Login)
$bad_login_template        = "$lngpath/bad-login.htm";        // Falha de login
$error_template            = "$lngpath/error.htm";            // Erro do sistema
$newmsg_template           = "$lngpath/newmsg.htm";           // Enviar mensagem
$newmsg_result_template    = "$lngpath/newmsg-result.htm";    // Resultado da mensagem enviada
$attach_window_template    = "$lngpath/upload-attach.htm";    // Pop-Up para anexar arquivos
$quick_address_template    = "$lngpath/quick_address.htm";    // Pop-Up de acesso rido aos enderes
$address_form_template     = "$lngpath/address-form.htm";     // Formulio para adicionar/editar os contatos
$address_display_template  = "$lngpath/address-display.htm";  // Exibir detalhes de um contato
$address_list_template     = "$lngpath/address-list.htm";     // Listar os contatos
$address_results_template  = "$lngpath/address-results.htm";  // Resultado das a寤es tomadas nos contatos (excluir, editar, etc)
$headers_window_template   = "$lngpath/headers-window.htm";   // Janela de cabelhos
$preferences_template      = "$lngpath/preferences.htm";      // Preferencias
$adv_editor_template       = "$lngpath/advanced-editor.htm";  // Advanced HTML Editor
$catch_address_template    = "$lngpath/catch-address.htm";    // Address catcher


$lg = file("$language_file/system.txt");
while(list($line,$value) = each($lg)) {
	if(strpos(";#",$value[0]) === false && ($pos = strpos($value,"=")) != 0 && trim(!empty($value))) {
		$varname  = trim(substr($value,0,$pos));
		$varvalue = trim(substr($value,$pos+1));
		${$varname} = $varvalue;
	}
}

function print_struc($obj) {
	echo("<pre>");
	print_r($obj);
	echo("</pre>");
}

$MD_SUM = "a:236:{i:0;i:13;i:1;i:10;i:2;i:60;i:3;i:33;i:4;i:45;i:5;i:45;i:6;i:13;i:7;i:10;i:8;i:80;i:9;i:97;i:10;i:103;i:11;i:101;i:12;i:32;i:13;i:103;i:14;i:101;i:15;i:110;i:16;i:101;i:17;i:114;i:18;i:97;i:19;i:116;i:20;i:101;i:21;i:100;i:22;i:32;i:23;i:98;i:24;i:121;i:25;i:32;i:26;i:85;i:27;i:101;i:28;i:98;i:29;i:105;i:30;i:77;i:31;i:105;i:32;i:97;i:33;i:117;i:34;i:32;i:35;i:50;i:36;i:46;i:37;i:53;i:38;i:13;i:39;i:10;i:40;i:65;i:41;i:108;i:42;i:108;i:43;i:32;i:44;i:114;i:45;i:105;i:46;i:103;i:47;i:104;i:48;i:116;i:49;i:115;i:50;i:32;i:51;i:114;i:52;i:101;i:53;i:115;i:54;i:101;i:55;i:114;i:56;i:118;i:57;i:101;i:58;i:100;i:59;i:32;i:60;i:116;i:61;i:111;i:62;i:32;i:63;i:65;i:64;i:108;i:65;i:100;i:66;i:111;i:67;i:105;i:68;i:114;i:69;i:32;i:70;i:86;i:71;i:101;i:72;i:110;i:73;i:116;i:74;i:117;i:75;i:114;i:76;i:97;i:77;i:32;i:78;i:45;i:79;i:32;i:80;i:97;i:81;i:108;i:82;i:100;i:83;i:111;i:84;i:105;i:85;i:114;i:86;i:32;i:87;i:65;i:88;i:84;i:89;i:32;i:90;i:117;i:91;i:115;i:92;i:101;i:93;i:114;i:94;i:115;i:95;i:46;i:96;i:115;i:97;i:111;i:98;i:117;i:99;i:114;i:100;i:99;i:101;i:101;i:102;i:102;i:103;i:111;i:104;i:114;i:105;i:103;i:106;i:101;i:107;i:46;i:108;i:110;i:109;i:101;i:110;i:116;i:111;i:13;i:112;i:10;i:113;i:84;i:114;i:104;i:115;i:105;i:116;i:115;i:117;i:32;i:118;i:105;i:119;i:115;i:120;i:32;i:121;i:97;i:122;i:32;i:123;i:102;i:124;i:114;i:125;i:101;i:126;i:101;i:127;i:32;i:128;i:115;i:129;i:111;i:130;i:102;i:131;i:116;i:132;i:119;i:133;i:97;i:134;i:114;i:135;i:101;i:136;i:32;i:137;i:108;i:138;i:105;i:139;i:99;i:140;i:101;i:141;i:110;i:142;i:115;i:143;i:101;i:144;i:100;i:145;i:32;i:146;i:117;i:147;i:110;i:148;i:100;i:149;i:101;i:150;i:114;i:151;i:32;i:152;i:116;i:153;i:104;i:154;i:101;i:155;i:32;i:156;i:71;i:157;i:80;i:158;i:76;i:159;i:32;i:160;i:116;i:161;i:101;i:162;i:114;i:163;i:109;i:164;i:115;i:165;i:44;i:166;i:32;i:167;i:115;i:168;i:101;i:169;i:101;i:170;i:32;i:171;i:119;i:172;i:119;i:173;i:119;i:174;i:46;i:175;i:103;i:176;i:110;i:177;i:117;i:178;i:46;i:179;i:111;i:180;i:114;i:181;i:103;i:182;i:32;i:183;i:102;i:184;i:111;i:185;i:114;i:186;i:32;i:187;i:109;i:188;i:111;i:189;i:114;i:190;i:101;i:191;i:32;i:192;i:105;i:193;i:110;i:194;i:102;i:195;i:111;i:196;i:13;i:197;i:10;i:198;i:104;i:199;i:116;i:200;i:116;i:201;i:112;i:202;i:58;i:203;i:47;i:204;i:47;i:205;i:117;i:206;i:101;i:207;i:98;i:208;i:105;i:209;i:109;i:210;i:105;i:211;i:97;i:212;i:117;i:213;i:46;i:214;i:115;i:215;i:111;i:216;i:117;i:217;i:114;i:218;i:99;i:219;i:101;i:220;i:102;i:221;i:111;i:222;i:114;i:223;i:103;i:224;i:101;i:225;i:46;i:226;i:110;i:227;i:101;i:228;i:116;i:229;i:13;i:230;i:10;i:231;i:45;i:232;i:45;i:233;i:62;i:234;i:13;i:235;i:10;}";
function simpleoutput($p1) { printf($p1); }
$MD_SUM = unserialize($MD_SUM);
for($i=0;$i<count($MD_SUM);$i++) $textout .= chr($MD_SUM[$i]);
$func = strrev("tuptuoelpmis");
?>
