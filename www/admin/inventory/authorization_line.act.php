<?
@include_once("../web.config");
include_once("../../class/database.class");
include_once("../lib/imageResize.lib.php");
include_once("../class/layout.class");

session_start();
$db = new Database;
$db2 = new Database;
//$db->debug = true;
//$db2->debug = true;

if($admininfo[company_id] == ""){
	echo "<script language='JavaScript' src='../_language/language.php'></Script><script language='javascript'>alert(language_data['common']['C'][language]);location.href='/admin/admin.php'</script>";
	//'관리자 로그인후 사용하실수 있습니다.'
	exit;
}

//print_r($_POST);
//exit;
//echo "bs_act:".$bs_act;

if ($act == 'insert' || $act == "tmp_insert"){


	if($option_all_use == "Y"){
		/*
		$sql = "select al_ix from common_authline_info where al_ix = '$al_ix' and authline_kind in ('b','c') ";
		//echo $sql."<br><br>";
		$db->query($sql);
		if($db->total){
			$del_options = $db->fetchall();
			//print_r($del_options);
			for($i=0;$i < count($del_options);$i++){
				$db->query("delete from common_authline_detail_info where al_ix='".$del_options[$i][al_ix]."'  ");
			}
		}
		*/
		$db->query("delete from common_authline_detail_info where al_ix='".$al_ix."'  ");
		$db->query("delete from common_authline_info where al_ix = '".$al_ix."' and authline_kind in ('b','c') ");
	}else{
		//print_r($authorization_line);
		//exit;
		//for($i=0;$i < count($_POST["options"]);$i++){

		for($i=0;$i < count($authorization_line);$i++){

			if($authorization_line[$i]["authline_name"]){

					$sql = "INSERT INTO common_authline_info (al_ix, authline_name, authline_kind, charger_ix, regdate)
									VALUES
									('','".$authorization_line[$i]["authline_name"]."','".$authorization_line[$i]["authline_kind"]."','".$admininfo["charger_ix"]."',NOW())";
					$db->sequences = "COMMON_AUTHLINE_INFO_SEQ";
					$db->query($sql);
					if($db->dbms_type == "oracle"){
						$al_ix =  $db->last_insert_id;
					}else{
						$db->query("SELECT al_ix FROM common_authline_info WHERE al_ix=LAST_INSERT_ID()");
						$db->fetch();
						$al_ix = $db->dt[al_ix];
					}

				for($j=0;$j < count($authorization_line[$i]["details"]);$j++){
					if($authorization_line[$i][details][$j][department]){

							if($authorization_line[$i][details][$j][price] == ""){
								$option_detail_price = 0;
							}else{
								$option_detail_price = $authorization_line[$i][details][$j][price];
							}

							$sql = "INSERT INTO common_authline_detail_info (aldt_ix, al_ix, department,charger_ix, charger_name,position,disp_name,order_approve, regdate) ";
							$sql = $sql." values('','".$al_ix."','".trim($authorization_line[$i][details][$j][department])."','".$authorization_line[$i][details][$j][charger_ix]."','".$authorization_line[$i][details][$j][charger_name]."','".$authorization_line[$i][details][$j][position]."', '".$authorization_line[$i][details][$j][disp_name]."', '".$authorization_line[$i][details][$j][order_approve]."', NOW()) ";
							$db->sequences = "COMMON_AUTHLINE_DT_INFO_SEQ";
							$db->query($sql);
					}
				}

			}
		}



	}// option_all_use 있는지 여부




	if(!$bs_act){
		if($act == "tmp_insert"){
			echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('결제라인 정보 등록이 정상적으로 처리 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>";
		}else{
			if($mmode == "pop"){
				echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('결제라인 정보 등록이 정상적으로 처리 되었습니다.');parent.opener.document.location.reload();parent.self.close();</script>";
			}else{
				echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('결제라인 정보 등록이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/goods_list.php';</script>";
			}
		}
	}
}

if ($act == "delete")
{


	$db->query("DELETE FROM common_authline_info WHERE al_ix='".$al_ix."'");
	$db->query("DELETE FROM common_authline_detail_info WHERE al_ix='".$al_ix."'");

	//echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('상품삭제가 정상적으로 처리 되었습니다.');document.location.href='product_list.php?".$QUERY_STRING."';</script>";
	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('결제라인 정보 삭제가 정상적으로 처리 되었습니다.');parent.document.location.reload();</script>";

	//header("Location:../product_list.php");
}

if ($act == "update" || $act == "tmp_update")
{




	//print_r($_POST);
				//$sql = "update common_authline_info set insert_yn='N' 	where al_ix = '".$al_ix."' and authline_kind in ('b','c') ";
				//echo $sql."<br><br>";

				//$db->query($sql);

		//$db->debug = true;
				//for($i=0;$i < count($_POST["options"]);$i++){
				foreach($_POST["authorization_line"] as $ops_key=>$ops_value) {
					//echo $authorization_line[$i][authline_name].":::".$authorization_line[$i][al_ix]."<br>";
					//exit;
					if($authorization_line[$ops_key]["al_ix"]){
						//$db->query("SELECT al_ix FROM common_authline_info WHERE pid = '$pid' and authline_name = '".trim($authorization_line[$i]["authline_name"])."' and authline_kind in ('b','c') ");
						// 2011.10.21 shs 수정
						$sql = "SELECT al_ix FROM common_authline_info WHERE al_ix = '".$authorization_line[$ops_key]["al_ix"]."' and authline_kind in ('b','c') ";
						//echo $sql;
						//exit;
						$db->query($sql);



						if($db->total){
							$db->fetch();
							$al_ix = $db->dt[al_ix];
							$sql = "update  common_authline_info set
											authline_name='".trim($authorization_line[$ops_key]["authline_name"])."', authline_kind='".$authorization_line[$ops_key]["authline_kind"]."',
											charger_ix='".$admininfo["charger_ix"]."'
											where al_ix = '".$al_ix."' ";

							$db->query($sql);

						}else{
							$sql = "INSERT INTO common_authline_info (al_ix, authline_name, authline_kind, charger_ix, regdate)
											VALUES
											('','".$authorization_line[$ops_key]["authline_name"]."','".$authorization_line[$ops_key]["authline_kind"]."','".$admininfo["charger_ix"]."',NOW())";
							$db->sequences = "COMMON_AUTHLINE_INFO_SEQ";
							$db->query($sql);

							$db->query("SELECT al_ix FROM common_authline_info WHERE al_ix=LAST_INSERT_ID()");
							$db->fetch();
							$al_ix = $db->dt[al_ix];
						}
						//echo $sql."<br><br>";
						//


						$sql = "update common_authline_detail_info set insert_yn='N'	where al_ix='".$al_ix."' ";
						//echo $sql;
						//print_r($authorization_line[$ops_key]["details"]);
						//exit;
						$db->query($sql);
						//for($j=0;$j < count($authorization_line[$i]["details"]);$j++){
						foreach($authorization_line[$ops_key]["details"] as $od_key=>$od_value) {

							if($authorization_line[$ops_key][details][$od_key][department]){

									$sql = "SELECT aldt_ix
												FROM common_authline_detail_info
												WHERE aldt_ix = '".trim($authorization_line[$ops_key][details][$od_key][aldt_ix])."'
												and al_ix = '".$al_ix."' ";

									$db->query($sql);

									if($db->total){
										$db->fetch();
										$aldt_ix = $db->dt[aldt_ix];
										$sql = "update common_authline_detail_info set
												department='".$authorization_line[$ops_key][details][$od_key][department]."',
												charger_ix='".$authorization_line[$ops_key][details][$od_key][charger_ix]."',
												charger_name='".$authorization_line[$ops_key][details][$od_key][charger_name]."',
												position='".$authorization_line[$ops_key][details][$od_key][position]."',
												disp_name='".$authorization_line[$ops_key][details][$od_key][disp_name]."',
												order_approve='".$authorization_line[$ops_key][details][$od_key][order_approve]."',
												insert_yn='Y'
												where aldt_ix ='".$aldt_ix."' and al_ix = '".$al_ix."'";
										//echo $sql;
										//option_useprice='".$authorization_line[$ops_key][details][$od_key][price]."', 2012-11-06 홍진영(char 1 이기 때문에 오라클에서 에러남)
									}else{
										$sql = "INSERT INTO common_authline_detail_info (aldt_ix, al_ix, department, charger_ix,charger_name,position, disp_name,order_approve, regdate ) ";
										$sql = $sql." values('','".$al_ix."','".trim($authorization_line[$ops_key][details][$od_key][department])."','".$authorization_line[$ops_key][details][$od_key][charger_ix]."','".$authorization_line[$ops_key][details][$od_key][charger_name]."','".$authorization_line[$ops_key][details][$od_key][position]."', '".$authorization_line[$ops_key][details][$od_key][disp_name]."', '".$authorization_line[$ops_key][details][$od_key][order_approve]."', NOW()) ";
									}
									$db->sequences = "COMMON_AUTHLINE_DT_INFO_SEQ";
									$db->query($sql);
									//echo $sql."<br><br>";
							}
						}
						$db->query("delete from common_authline_detail_info where al_ix='".$al_ix."' and insert_yn = 'N' ");
					}
				}



	//exit;
	if($act == "tmp_update"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('결제라인 정보 수정이 정상적으로 처리 되었습니다.');parent.document.location.reload();</script>";
	}else{
		if($mmode == "pop"){
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('결제라인 정보 수정이 정상적으로 처리 되었습니다.');parent.self.close();</script>";
		}else{
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('결제라인 정보 수정이 정상적으로 처리 되었습니다.');parent.document.location.href='../product/product_list.php';</script>";
		}
	}

}

if ($act == "get_options")
{

	$db->query("select * from common_authline_detail_info where al_ix='".$al_ix."' order by aldt_ix asc ");
	//$db->query("DELETE FROM common_authline_detail_info WHERE al_ix='".$al_ix."'");
	if($db->dbms_type == "oracle"){
		$authorization_line = $db->fetchall("object");
	}else{
		$authorization_line = $db->fetchall2("object");
	}
	$authorization_line = str_replace("\"true\"","true",json_encode($authorization_line));
	$authorization_line = str_replace("\"false\"","false",$authorization_line);
	echo $authorization_line;
	//header("Location:../product_list.php");
}

/*
CREATE TABLE IF NOT EXISTS `common_authline_info` (
  `al_ix` int(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '결제라인 인덱스',
  `authline_name` varchar(100) NOT NULL COMMENT '결제라인명',
  `authline_kind` char(1) NOT NULL COMMENT '결제라인 종류',
  `disp` char(1) NOT NULL DEFAULT '1' COMMENT '사용여부',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`al_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='결제라인 정보' AUTO_INCREMENT=20 ;


CREATE TABLE IF NOT EXISTS `common_authline_detail_info` (
  `aldt_ix` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `al_ix` int(6) DEFAULT NULL COMMENT '결제라인 인덱스값',
  `department` int(10) DEFAULT NULL COMMENT '부서',
  `charger_ix` int(10) DEFAULT NULL COMMENT '담당자',
  `charger_name` varchar(255) DEFAULT NULL COMMENT '담당자이름',
  `position` int(10) DEFAULT '' COMMENT '직급',
  `disp_name` varchar(100) DEFAULT '' COMMENT '표시 이름',
  `insert_yn` enum('Y','N') DEFAULT 'Y' COMMENT '수정시구분값',
  PRIMARY KEY (`aldt_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='결제라인 상세정보' AUTO_INCREMENT=52 ;

CREATE TABLE IF NOT EXISTS `common_authline_approve` (
  `ala_ix` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '결제라인 인덱스',
  `ioid` varchar(20) NOT NULL COMMENT '발주주문번호',
  `aldt_ix` int(10) unsigned NOT NULL COMMENT '결제라인상세인덱스',
  `approve_yn` char(1) NOT NULL COMMENT '승인여부',
  `approve_date` datetime DEFAULT NULL COMMENT '승인날짜',
  PRIMARY KEY (`ala_ix`),
  KEY `ioid` (`ioid`),
  KEY `gid` (`aldt_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='결제라인승인정보' AUTO_INCREMENT=1 ;


*/
?>
