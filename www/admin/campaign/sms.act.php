<?
include("../class/layout.class");


$db = new Database;
$idb = new Database;

if($act == "sms_info"){
	$db->query("SELECT * FROM shop_mail_box where disp = 1 and mail_ix = '".$mail_ix."' order by regdate desc limit 1 ");
	$db->fetch("object");
	$mail_info = $db->dt;
	//print_r($events);
	$datas = str_replace("\"true\"","true",json_encode($mail_info));
	$datas = str_replace("\"false\"","false",$datas);
	echo $datas;
}
if($act == "history_delete"){
	//echo ("delete FROM shop_mailling_history WHERE mh_ix = '$mh_ix' ");
	$db->query("delete FROM shop_mailling_history WHERE mh_ix = '$mh_ix' ");

	echo("<script>alert('메일 발송 목록이 정상적으로 삭제되었습니다 ');top.location.reload();</script>");
}

if($act == "history_select_delete"){
	for($i=0;$i < count($mh_ix);$i++){
		$db->query("delete FROM shop_mailling_history WHERE mh_ix = '".$mh_ix[$i]."' ");
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메일 발송 목록이 정상적으로 삭제되었습니다 ');top.location.reload();</script>");
}





if($act == "send"){
	include($_SERVER["DOCUMENT_ROOT"]."/include/mail_smtp.php");

//	print_r($_POST);
	$db->query("SELECT mail_text FROM shop_mail_box WHERE mail_ix = '$mail_ix'");
	$db->fetch();

	$body = $db->dt[mail_text];

	$body_type = "HTML";
	if(!$max){
		$max = 100;
	}
	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}
	$db->query("SELECT count(*) as total FROM shop_mailling_target_list WHERE mt_ix = '$mt_ix'   ");
	$db->fetch();
	$total = $db->dt[total];

	$db->query("SELECT * FROM shop_mailling_target_list WHERE mt_ix = '$mt_ix' order by mtl_ix asc limit $start,$max  ");
	//$db->fetch();

	$recipients = "";

	$from = "\"포비즈\"<tech@forbiz.co.kr>";
	$subject = $mail_title;


	for($i=0;$i < $db->total;$i++){
		$db->fetch($i);
		list($mail_id, $mail_host) = split('@',$db->dt[target_mail]);
		//echo $mail_host.":::".ValidateDNS($mail_host)."<br>";

		if(ValidateDNS($mail_host)){
			$check_key = md5(uniqid());
			$body = str_replace("{check_key}",$check_key,$body);
			$add_body = $body."<P style='DISPLAY: none'><IMG src='http://".$HTTP_HOST."/mailling/mail_open.php?check_key=$check_key'></P>";

			$to = "\"고객님\"<".$db->dt[target_mail].">";
			if(mail_smtp($db->dt[target_mail], $to, $from, $subject, $add_body, $body_type)){
				$sql = "insert into shop_mailling_history(mh_ix,mail_ix,ci_ix,sended_mail, check_key, regdate)
								values ('','$mail_ix','".$db->dt[ci_ix]."','".$db->dt[target_mail]."','$check_key', NOW())";
				//echo $sql;
				$idb->query($sql);
			}
		}


	}
	if($total > ($start+$max)){
		echo("<script>
		parent.document.getElementById('total_mail_cnt').innerHTML = '$total';
		parent.document.getElementById('sended_mail_cnt').innerHTML = '".($start+$max)."';
		parent.document.getElementById('remainder_mail_cnt').innerHTML = '".($total-($start+$max))."';
		document.location.href='?act=send&max=$max&mail_ix=$mail_ix&mt_ix=$mt_ix&page=".($page+1)."'
		</script>");
	}else{
		echo("<script>alert('".$total." ::: 메일이 정상적으로 발송되었습니다 ');top.location.reload();</script>");
	}
}

if ($act == "target_insert"){
	//print_r($_POST);
	$search_val = unserialize(urldecode($search_condition));

	if($_POST["target_type"] == 2){
		$target_cnt = count($target_info);
	}

	if($target_insert_kind == 1){
		$sql = "insert into shop_mailling_target(mt_ix,target_name,target_desc,target_cnt,regdate) values('','$target_name','$target_desc','$target_cnt',NOW())";
		$db->sequences = "SHOP_MAILLING_TARGET_SEQ";
		$db->query($sql);

		if($db->dbms_type == "oracle"){
			$mt_ix = $db->last_insert_id;
		}else{
			$db->query("SELECT mt_ix FROM shop_mailling_target WHERE mt_ix = LAST_INSERT_ID()");
			$db->fetch();
			$mt_ix = $db->dt[mt_ix];
		}
	}
	//echo $search_val["target_type"];
	if($_POST["target_type"] == 1){
		if($search_val["com_div"]){
			$search_str .= " and com_div = '".$search_val["com_div"]."' ";
		}

		if($search_val["div"]){
			$search_str .= " and div = '".$search_val["div"]."' ";
		}

		$sql = 	"insert into shop_mailling_target_list SELECT '' as mtl_ix, '$mt_ix' as mt_ix, ci_ix, email, NOW() as ragdate FROM shop_mailling_list where ci_ix is not null  $search_str order by regdate desc ";
		//echo $sql;
		$db->query($sql);

	}else{
		for($i=0;$i < count($target_info);$i++){
			list($ci_ix, $target_mail) = split('[|]',$target_info[$i],2);

			$db->sequences = "SHOP_MAILLING_TARGET_LIST_SEQ";
			$db->query("insert into shop_mailling_target_list(mtl_ix,mt_ix, ci_ix,target_mail,regdate) values('','$mt_ix','$ci_ix','$target_mail',NOW())");

		}
	}

	if($target_insert_kind == 2){
		$sql = "update shop_mailling_target set target_cnt = target_cnt + $target_cnt where mt_ix = '$mt_ix'";
		$db->query($sql);
	}

	echo("<script>top.location.reload();</script>");

	//print_r($search_val);
}

if($act == "target_delete"){
	$db->query("delete FROM shop_mailling_target WHERE mt_ix = '$mt_ix' ");
	$db->query("delete FROM shop_mailling_target_list WHERE mt_ix = '$mt_ix' ");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('타겟군 및 타겟목록이 정상적으로 삭제되었습니다 ');top.location.reload();</script>");
}


if ($act == "update")
{

	$mail_use_sdate = $FromYY.$FromMM.$FromDD;
	$mail_use_edate = $ToYY.$ToMM.$ToDD;

	$sql = "update shop_sms_box set
			sms_title='$sms_title',sms_group='$sms_group',sms_text='$sms_text',disp='$disp',sms_type='$sms_type',sms_code='$sms_code'
			where sms_ix='$sms_ix'";


	$db->query($sql);

	if($mmode =='pop'){
	echo("<script>alert('수정되었습니다');top.location.href = 'sms_write.php?sms_ix=$sms_ix&mmode=pop';</script>");
	}else{
	echo("<script>top.location.href = 'sms_write.php?sms_ix=$sms_ix';</script>");
	}
}

if ($act == "insert"){

	$db->sequences = "SHOP_SMS_BOX_SEQ";
	$db->query("insert into shop_sms_box (sms_ix,sms_group,sms_code,sms_type,sms_title,sms_text,disp,regdate) values('$sms_ix','$sms_group','$sms_code','$sms_type','$sms_title','$sms_text','$disp',NOW())");

	if($db->dbms_type == "oracle"){
		$LAST_ID = $db->last_insert_id;
	}else{
		$db->query("SELECT sms_ix FROM shop_sms_box WHERE sms_ix=LAST_INSERT_ID()");
		$db->fetch();
		$LAST_ID = $db->dt[sms_ix];
	}
	
	echo("<script>alert('등록되었습니다');self.close();</script>");
}

if ($act == "delete")
{
	if(is_dir($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/mail/$mail_ix/")){
		rmdirr($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/mail/$mail_ix/");
	}

	$db->query("DELETE FROM shop_sms_box WHERE mail_ix='$mail_ix'");
	//$db->query("DELETE FROM shop_mailling_history WHERE mail_ix='$mail_ix'");
	echo("<script>top.location.href = 'sms_box.php';</script>");
	exit;
}


/*
function rmdirr($target,$verbose=false)
// removes a directory and everything within it
{
$exceptions=array('.','..');
if (!$sourcedir=@opendir($target))
   {
   if ($verbose)
       echo '<strong>Couldn&#146;t open '.$target."</strong><br />\n";
   return false;
   }
while(false!==($sibling=readdir($sourcedir)))
   {
   if(!in_array($sibling,$exceptions))
       {
       $object=str_replace('//','/',$target.'/'.$sibling);
       if($verbose)
           echo 'Processing: <strong>'.$object."</strong><br />\n";
       if(is_dir($object))
           rmdirr($object);
       if(is_file($object))
           {
           $result=@unlink($object);
           if ($verbose&&$result)
               echo "File has been removed<br />\n";
           if ($verbose&&(!$result))
               echo "<strong>Couldn&#146;t remove file</strong>";
           }
       }
   }
closedir($sourcedir);
if($result=@rmdir($target))
   {
   if ($verbose)
       echo "Target directory has been removed<br />\n";
   return true;
   }
if ($verbose)
   echo "<strong>Couldn&#146;t remove target directory</strong>";
return false;
}



function GetDirContents($dir){
   ini_set("max_execution_time",10);
   if (!is_dir($dir)){die ("Fehler in Funktion GetDirContents: kein g?s Verzeichnis: $dir!");}
   if ($root=@opendir($dir)){
       while ($file=readdir($root)){
           if($file=="." || $file==".."){continue;}
           if(is_dir($dir."/".$file)){
               $files=array_merge($files,GetDirContents($dir."/".$file));
           }else{
           $files[]=$dir."/".$file;
           }
       }
   }
   return $files;
}


function ClearText($str){
	return str_replace(">","",$str);
}

function returnFileName($filestr){
	$strfile = split("/",$filestr);

	return str_replace("%20","",$strfile[count($strfile)-1]);
	//return count($strfile);

}

function returnImagePath($str){
	$IMG = split(" ",$str);

	for($i=0;$i<count($IMG);$i++){
		//echo substr_count($IMG[$i],"src");
			if(substr_count($IMG[$i],"src=") > 0){
				$mstring = str_replace("src=","",$IMG[$i]);
				return str_replace("\"","",$mstring);
			}
	}
}

function ValidateDNS($host){
	return (checkdnsrr($host, ANY))? true: false;
}

/*
CREATE TABLE `shop_mail` (
  `mail_ix` int(4) unsigned NOT NULL auto_increment,
  `mail_title` varchar(255) default NULL,
  `mail_text` mediumtext NOT NULL,
  `disp` char(1) NOT NULL default '1',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`mail_ix`)
) TYPE=MyISAM COMMENT='메일 목록' ;



CREATE TABLE `shop_mailling_history` (
  `mh_ix` int(8) unsigned NOT NULL auto_increment,
  `mail_ix` int(4) unsigned NOT NULL,
  `ci_ix` int(8) unsigned NOT NULL,
  `sended_mail` varchar(255) default NULL,
  `mail_open` enum('0','1') NOT NULL default '0',
  `mail_click` enum('0','1') NOT NULL default '0',
  `regdate` datetime default NULL,
  `open_date` datetime default NULL,
  `click_date` datetime default NULL,
  PRIMARY KEY  (`mh_ix`)
) TYPE=MyISAM COMMENT='메일 발송 목록' ;


CREATE TABLE `shop_mailling_target` (
  `mt_ix` int(8) unsigned NOT NULL auto_increment,
  `target_name` varchar(255) default NULL,
  `target_desc` varchar(255) default NULL,
  `target_cnt` int(8) unsigned NOT NULL,
  `regdate` datetime default NULL,
  PRIMARY KEY  (`mt_ix`)
) TYPE=MyISAM COMMENT='메일 발송 타겟군 정보' ;

CREATE TABLE `shop_mailling_target_list` (
  `mtl_ix` int(8) unsigned NOT NULL auto_increment,
  `mt_ix` int(8) unsigned NOT NULL,
  `ci_ix` int(8) unsigned NOT NULL,
  `target_mail` varchar(255) default NULL,
  `regdate` datetime default NULL,
  PRIMARY KEY  (`mtl_ix`)
) TYPE=MyISAM COMMENT='메일 발송 타겟군 리스트' ;
*/
?>

