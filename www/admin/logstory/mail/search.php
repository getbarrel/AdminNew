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


$tcontent = load_template($search_template,Array("menus","search"));

$jssource = "
<script language=\"JavaScript\">
function newmsg() { location = 'newmsg.php?pag=$pag&folder=".urlencode($folder)."&sid=$sid&lid=$lid'; }
function folderlist() { location = 'folders.php?folder=".urlencode($folder)."&sid=$sid&lid=$lid'}
function goend() { location = 'logout.php?sid=$sid&lid=$lid'; }
function goinbox() { location = 'msglist.php?folder=inbox&sid=$sid&lid=$lid'; }
function emptytrash() {	location = 'folders.php?empty=trash&folder=".urlencode($folder)."&goback=true&sid=$sid&lid=$lid';}
function addresses() { location = 'addressbook.php?sid=$sid&lid=$lid'; }
function prefs() { location = 'preferences.php?sid=$sid&lid=$lid'; }
</script>
";

$tcontent = eregi_replace("<!--%UM_SID%-->",$sid,$tcontent);
$tcontent = eregi_replace("<!--%UM_LID%-->",$lid,$tcontent);

$foundstart = strpos($tcontent,"<!--%UM_RESULTS_BEGIN%-->");
$foundend = strpos($tcontent,"<!--%UM_RESULTS_END%-->")+23;

$notfoundstart = strpos($tcontent,"<!--%UM_NOT_FOUND_BEGIN%-->");
$notfoundend = strpos($tcontent,"<!--%UM_NOT_FOUND_END%-->")+25;

if(!empty($s)) {

	$file_list = Array();
	function build_list($location) {
		global $file_list;
		$all=opendir($location); 
		while ($file=readdir($all)) { 
			if (is_dir($location.$file) && $file <> ".." && $file <> ".") { 
				build_list($location.$file); 
				unset($file); 
			} elseif (substr($file,-4) == ".eml") { 
				$file_list[] = "$location/$file";
				unset($file); 
			}
		}
		closedir($all); 
	}
	build_list($userfolder);

	$search_in_body = 1;
	$search_in_subject = 1;
	
	
	function cleanstr($text){
		$text = strtolower($text);

		$tmp = explode(" ",$text);
		for($i=0;$i<count($tmp);$i++)
			if(trim(!empty($tmp[$i])))
				$newtext .= " ".$tmp[$i];

		return trim($newtext);
	}

	$search_text = $s;
	$search_text = cleanstr($search_text);
	$search_text = explode(" ",$search_text);
	
	$results = 0;
	
	$tempresult = Array();

	$md = new mime_decode();

	$md->use_html			= $mime_show_html;
	$md->charset 			= $default_char_set;
	$md->timezone			= $timezone;



	for($i=0;$i<count($file_list);$i++) {

		$thisfile = $file_list[$i];
		$tmp = fopen($thisfile,"r");
		$thismail = fread($tmp,filesize($thisfile));
		fclose($tmp);


		$md->initialize($thismail);
		$email = $md->content;


		$subject = $email["subject"];
		$data = $email["date"];
		$msize = strlen($thismail);
		$localname = $email["localname"];
		$from = $email["from"];
		$read = $email["read"];
		$body = strip_tags($email["body"]);

		$attach = (count($email["attachments"]) != 0)?1:0;
		$result = " ".cleanstr($subject)." ".cleanstr($body)." ";

		for($n=0;$n<count($search_text);$n++) {
			if(strpos($result," ".$search_text[$n]." ") === false) {
				$found = 0; break;
			} else
				$found = 1;
		}

		if(!empty($found)) { 
			$tempresult[$results]["localname"] = $thisfile;
			$tempresult[$results]["size"] = $msize;
			$tempresult[$results]["from"] = $from;
			$tempresult[$results]["date"] = $data;
			$tempresult[$results]["subject"] = $subject;
			$tempresult[$results]["haveattach"] = $attach;
			$tempresult[$results]["read"] = $read;

			$results ++;
		}
		unset($email,$thismail,$tmp);

	}
	if($results == 0) {
		$cleantext = substr($tcontent,$notfoundstart+27,$notfoundend-$notfoundstart-52);
		$tcontent = substr($tcontent,0,$foundstart).$cleantext.substr($tcontent,$notfoundend);
		$sess["folderheaders"] = Array();
	} else {
		$cleantext = substr($tcontent,$foundstart+25,$foundend-$foundstart-48);

		$startloop = strpos($cleantext,"<!--%UM_ML_LOOPBEGIN%-->");
		$endloop = strpos($cleantext,"<!--%UM_ML_LOOPEND%-->")+22;

		$looptext = substr($cleantext,$startloop+24,$endloop-$startloop-46);
		$sess["folderheaders"] = $tempresult;

		for($i=0;$i<$results;$i++) {
			$e = $tempresult[$i];
			$fromname = htmlspecialchars($e["from"][0]["name"]);
			$fromname = (strlen($fromname) > 25)?substr($fromname,0,25)."...":$fromname;

			$from = "<a href=\"newmsg.php?nameto=".urlencode($e["from"][0]["name"])."&mailto=".urlencode($e["from"][0]["mail"])."&sid=$sid&lid=$lid\">$fromname</a>";
			$location = eregi_replace($userfolder,"",$e["localname"]);
			$box = substr($location,0,strpos($location,"/"));

			switch($box) {
			case "inbox":
				$boxname = $inbox_extended; break;
			case "sent":
				$boxname = $sent_extended; break;
			case "trash":
				$boxname = $trash_extended; break;
			default: $boxname = $box;
			}
			$subject = htmlspecialchars(trim($e["subject"]));
			if(empty($subject)) $subject = $no_subject_text;
			$subject = (strlen($subject) > 25)?substr($subject,0,25)."...":$subject;
			if(empty($e["read"])) $subject = "<b>$subject</b>";

			$subject = "<a href=\"readmsg.php?pag=1&mnum=0&ix=$i&msize=$msize&folder=$box&search=1&sid=$sid&lid=$lid\">$subject</a>";
			$thisline = $looptext;
			$attach = ($e["haveattach"])?"<img src=\"images/attach.gif\" border=0 width=6 height=14>":"&nbsp;";
			$thisline = eregi_replace("<!--%UM_ML_HAVE_ATTACH%-->",$attach,$thisline);
			$thisline = eregi_replace("<!--%UM_ML_SUBJECT%-->",$subject,$thisline);
			$thisline = eregi_replace("<!--%UM_ML_FROM%-->",$from,$thisline);
			$thisline = eregi_replace("<!--%UM_ML_DATE%-->",@date("d/m/y h:m",$e["date"]),$thisline);
			$thisline = eregi_replace("<!--%UM_BOX%-->","<a href=\"msglist.php?folder=".urlencode($box)."&sid=$sid&lid=$lid\">$boxname</a>",$thisline);
			$loopresult .= $thisline;

		}

		save_session($sess);

		$cleantext = substr($tcontent,$foundstart+25,$foundend-$foundstart-48);

		$startloop = strpos($cleantext,"<!--%UM_ML_LOOPBEGIN%-->");
		$endloop = strpos($cleantext,"<!--%UM_ML_LOOPEND%-->")+22;
		
		$cleantext = substr($cleantext,0,$startloop).$loopresult.substr($cleantext,$endloop);
		$tcontent = substr($tcontent,0,$foundstart).$cleantext.substr($tcontent,$notfoundend);
	}
} else {
	$tcontent = substr($tcontent,0,$foundstart).substr($tcontent,$notfoundend);
}
$tcontent = eregi_replace("<!--%UM_INPUT_TEXT%-->",htmlspecialchars($s),$tcontent);
$tcontent = eregi_replace("<!--%UM_JS%-->",$jssource,$tcontent);


echo($tcontent);

?>
