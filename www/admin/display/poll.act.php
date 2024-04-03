<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

if ($act == "group_update"){
	$sdate = $FromYY.$FromMM.$FromDD;
	$edate = $ToYY.$ToMM.$ToDD;
	if(!$use_date){
		$use_date = "0";
	}
	$sql = "update shop_poll_group set 
					g_title='$g_title',g_desc='$g_desc',pname='$pname',use_date='$use_date',sdate='$sdate',edate='$edate',disp='$disp'
					where pg_ix='$pg_ix' ";
//echo $sql;
	$db->query($sql);
	/*
	if ($file_size > 0)
	{
		$db->query("SELECT * FROM banner WHERE no='$no'");
		$db->fetch();

		unlink($_SERVER["DOCUMENT_ROOT"]."/image/banner/".$db->dt[image].".gif");
		unlink($_SERVER["DOCUMENT_ROOT"]."/image/banner/".$db->dt[image].".swf");

		if ($flash == "0")
		{
			copy("$file", $_SERVER["DOCUMENT_ROOT"]."/image/banner/".$db->dt[image].".gif");
		}

		if ($flash == "1")
		{
			copy("$file", $_SERVER["DOCUMENT_ROOT"]."/image/banner/".$db->dt[image].".swf");
			$link = "";
		}
	}

	$db->query("UPDATE banner SET subj='$subj', link='$link', type='$type', disp='$disp', flash='$flash', w_size='$w_size', h_size='$h_size' WHERE no='$no'");

	echo("<script>location.href = 'banner.php';</script>");
	*/

	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('설문그룹이 정상적으로 수정되었습니다.');location.href = 'poll_list.php';</script>");
}

if ($act == "group_insert"){
	$sdate = $FromYY.$FromMM.$FromDD;
	$edate = $ToYY.$ToMM.$ToDD;
	if(!$use_date){
		$use_date = "0";
	}
	$sql = "insert into shop_poll_group 
					(pg_ix,g_title,g_desc,pname,use_date, sdate, edate, disp, regdate) 
					values
					('$pg_ix','$g_title','$g_desc','$pname','$use_date','$sdate','$edate','$disp',NOW()) ";
	
	$db->query($sql);
	
	/*
	if($id == ""){
		$db->query("INSERT INTO shop_poll_group VALUES('','$g_title',NOW())");
		//if($title != ""){
			//$db->query("INSERT INTO ".TBL_SHOP_POLL_TITLE." VALUES('','$title','0','$fieldnum',LAST_INSERT_ID())");
		//}
		
	}else{
		$db->query("Update shop_poll_group set g_title = '$g_title' where id ='".$id."' ");
		if($title != ""){
			$db->query("INSERT INTO ".TBL_SHOP_POLL_TITLE." VALUES('','$title','0','$fieldnum',".$id.")");
		}
		echo("<script>location.href = 'poll.php?id=$id';</script>");
	}
*/
	

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('설문그룹이 정상적으로 입력되었습니다.');location.href = 'poll_list.php';</script>");

	
}

if ($act == "fieldinsert")
{
	$db->query("Update ".TBL_SHOP_POLL_TITLE." set disp = '$disp' where id ='".$pollnumber."' ");
	
	$aryfield[0] = $fielddesc0;
	$aryfield[1] = $fielddesc1;
	$aryfield[2] = $fielddesc2;
	$aryfield[3] = $fielddesc3;
	$aryfield[4] = $fielddesc4;
	$aryfield[5] = $fielddesc5;
	$aryfield[6] = $fielddesc6;
	$aryfield[7] = $fielddesc7;
	
	for($i=0;$i<$fieldsize;$i++){
		$db->query("INSERT INTO ".TBL_SHOP_POLL_FIELD." (pt_ix, number, fieldnumber, result, fielddesc) VALUES ('','".$pollnumber."','".($i+1)."','','".$aryfield[$i]."')");
	}

	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입력되었습니다.');</script>");
	echo("<script>location.href = 'poll.php?pt_ix=$pt_ix';</script>");
}

if ($act == "poll_insert")
{
	$sql = "insert into ".TBL_SHOP_POLL_TITLE."
					(pt_ix,title,poll_type,disp,fieldsize,pg_ix,regdate) 
					values
					('','$title','$poll_type','$disp','$fieldsize','$pg_ix',NOW())";
	$db->query($sql);
	$db->query("SELECT pt_ix FROM ".TBL_SHOP_POLL_TITLE." WHERE pt_ix=LAST_INSERT_ID()");
	$db->fetch();
	$pt_ix = $db->dt[pt_ix];
	
	for($i=1;$i < count($_POST["pf_ix"]);$i++){
		if($_POST["pf_ix"][$i]){
			$db->query("Update ".TBL_SHOP_POLL_FIELD." set fieldnumber='".($i+1)."', fielddesc = '".$_POST["fielddesc"][$i]."', result = '".$_POST["result"][$i]."', insert_yn ='Y' where pt_ix ='".$pt_ix."' and pf_ix ='".$_POST["pf_ix"][$i]."'");
		}else{
			$db->query("insert into ".TBL_SHOP_POLL_FIELD." (pf_ix, pt_ix, fieldnumber, result, fielddesc, insert_yn, regdate) value ('','".$pt_ix."','".($i+1)."', '".$_POST["result"][$i]."', '".$_POST["fielddesc"][$i]."','Y', NOW()) ");
		}
	}
	
	
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 등록되었습니다.');</script>");
	echo("<script>location.href = 'poll_write.php?mmode=$mmode&pg_ix=$pg_ix&pt_ix=$pt_ix';</script>");
	
}
if ($act == "poll_update")
{
	$db->query("Update ".TBL_SHOP_POLL_TITLE." set title='$title', poll_type = '$poll_type', disp = '$disp' where pt_ix ='".$pt_ix."' ");
	
	//print_r($_POST);
	if($poll_type == 1){// 객관식일때
			$db->query("Update ".TBL_SHOP_POLL_FIELD." set insert_yn ='N'  where pt_ix ='".$pt_ix."' ");
			for($i=1;$i < count($_POST["pf_ix"]);$i++){
				if($_POST["pf_ix"][$i]){
					$db->query("Update ".TBL_SHOP_POLL_FIELD." set fieldnumber='".($i+1)."', fielddesc = '".$_POST["fielddesc"][$i]."', result = '".$_POST["result"][$i]."', insert_yn ='Y' where pt_ix ='".$pt_ix."' and pf_ix ='".$_POST["pf_ix"][$i]."'");
				}else{
					$db->query("insert into ".TBL_SHOP_POLL_FIELD." (pf_ix, pt_ix, fieldnumber, result, fielddesc, insert_yn, regdate) value ('','".$pt_ix."','".($i+1)."', '".$_POST["result"][$i]."', '".$_POST["fielddesc"][$i]."','Y', NOW()) ");
				}
			}
			$db->query("delete from ".TBL_SHOP_POLL_FIELD." where insert_yn ='N' and pt_ix ='".$pt_ix."' ");
	}else if($poll_type == 2){// 주관식일때
		$db->query("delete from ".TBL_SHOP_POLL_FIELD." where pt_ix ='".$pt_ix."' ");
	}
	
	
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'poll_write.php?mmode=$mmode&pg_ix=$pg_ix&pt_ix=$pt_ix';</script>");
}

if ($act == "static")
{
	//print_r($pollnumber)."<---";
	//exit;
	
	if($_COOKIE[poll][$group_id] == ""){
		setcookie("poll[$group_id]",$group_id,time()+60*60*24*30);
		//echo ("Update ".TBL_SHOP_POLL_FIELD." set result = result+1 where number ='".$pollnumber."' and fieldnumber ='".$field."'");
	
		for($i = 0;$i < count($pollnumber);$i++){
			//echo $field0."<---";
			//exit;
			$db->query("Update ".TBL_SHOP_POLL_FIELD." set result = result+1 where number ='".$pollnumber[$i]."' and fieldnumber ='".$field[$pollnumber[$i]]."'");
			//$db->query("Update ".TBL_SHOP_POLL_FIELD." set result = result+1 where number ='".$pollnumber[$i]."' and fieldnumber ='".$field[$pollnumber[$i]]."'");
		}
		//exit;
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 반영되었습니다.');parent.document.location.reload();</script>");
	}else{
		echo "<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['poll.act.php']['A'][language]);</script>";//'이미 설문에 참여 했습니다.'
	}
	
	if($popurl != ""){ 
		//echo("<Script Language='JavaScript' src='/js/basic.js'></Script>");
		//echo("<script>PoPWindow2('$popurl?pollid=$pollnumber', 650,375,'pollpop');</script>");
	}
}

if ($act == "delete")
{
	$db->query("DELETE FROM ".TBL_SHOP_POLL_TITLE." where pt_ix ='".$pt_ix."'");
	$db->query("DELETE FROM ".TBL_SHOP_POLL_FIELD." where pt_ix ='".$pt_ix."'");

	echo("<script>location.href = 'poll.php?pg_ix=$pg_ix'</script>");
}

if ($act == "g_delete")
{
	
	$db->query("DELETE FROM shop_poll_group where pg_ix ='".$pg_ix."'");

	$db->query("select pt_ix from ".TBL_SHOP_POLL_TITLE." where pg_ix = '".$pg_ix."'");
	$db->fetch();
	$pt_ix = $db->dt[pt_ix];
	
	$db->query("DELETE FROM ".TBL_SHOP_POLL_FIELD." where pt_ix ='".$pt_ix."'");
	$db->query("DELETE FROM ".TBL_SHOP_POLL_TITLE." where pg_ix ='".$pg_ix."'");

	echo("<script>location.href = 'poll_list.php'</script>");
}
/*
CREATE TABLE `shop_poll_result` (
	`pr_ix` int(11)  NOT NULL AUTO_INCREMENT,
  `pg_ix` int(8) NOT NULL DEFAULT '0',
  mem_ix varchar(32) NOT NULL,
  `pt_ix` int(11)  NOT NULL ,
  `poll_type` char(1) DEFAULT NULL,
  `result` varchar(255) NOT NULL DEFAULT '',  
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`pg_ix`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8


CREATE TABLE `shop_poll_group` (
  `pg_ix` int(8) NOT NULL DEFAULT '0',
  `g_title` varchar(255) NOT NULL DEFAULT '',
  `g_desc` mediumtext,
  `pname` varchar(255) DEFAULT NULL,
  `use_date` enum('1','0') DEFAULT NULL,
  `sdate` varchar(8) DEFAULT NULL,
  `edate` varchar(8) DEFAULT NULL,
  `disp` enum('1','0') DEFAULT '1',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`pg_ix`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8


CREATE TABLE `shop_poll_title` (
  `pt_ix` int(11)  NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `poll_type` char(1) DEFAULT NULL,
  `disp` char(1) DEFAULT '0',
  `fieldsize` int(10) unsigned NOT NULL DEFAULT '0',
  `pg_ix` int(11) unsigned DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`pt_ix`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8


CREATE TABLE `shop_poll_title` (
  `pt_ix` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `poll_type` char(1) DEFAULT NULL,
  `disp` char(1) DEFAULT '0',
  `fieldsize` int(10) unsigned NOT NULL DEFAULT '0',
  `pg_ix` int(11) unsigned DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`pt_ix`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8


CREATE TABLE `shop_poll_field` (
  `pf_ix` int(8) NOT NULL AUTO_INCREMENT,
  `pt_ix` int(8) DEFAULT NULL,
  `fieldnumber` int(10) unsigned NOT NULL DEFAULT '0',
  `result` int(8) unsigned DEFAULT '0',
  `fielddesc` varchar(255) NOT NULL DEFAULT '',
  `insert_yn` enum('Y','N') DEFAULT 'Y',
  `regdate` datetime DEFAULT NULL,
  PRIMARY KEY (`pf_ix`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8
*/
?>
