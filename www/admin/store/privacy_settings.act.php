<?
include("../../class/database.class");

session_start();

$db = new Database;

foreach ($_POST as $key => $val) {
	if($key != "act" && $key != "mall_ix" && $key != "x" && $key != "y"){
        if(is_array($val)){
            if($val[0] == '' && count($val) == 1){
                $val = '';
            }else{
                $val = json_encode($val);
            }

        }
		if($db->dbms_type=='oracle'){

			$sql = "delete shop_mall_privacy_setting where 
					mall_ix = '".$_POST[mall_ix]."' and
					config_name ='".$key."' and
					config_value ='".$val."'  ";
			$db->query($sql);
			$sql ="INSERT INTO shop_mall_privacy_setting (mall_ix,config_name,config_value) values ('".$_POST[mall_ix]."','".$key."','".$val."')";
			
		}else{
			$sql = "REPLACE INTO shop_mall_privacy_setting set 
					mall_ix = '".$_POST[mall_ix]."',
					config_name ='".$key."',
					config_value ='".$val."'  ";
		}
		$db->query($sql);
	}
}

/*개인정보 설정관련 필요한 DB 컬럼이 존재하지 않을 경우 추가 해주는 명령 진행 JK160223*/
$sql = "SHOW COLUMNS FROM ".TBL_COMMON_USER." LIKE 'fail_count'";
$db->query($sql);
if(empty($db->total)){
	$sql = "ALTER TABLE ".TBL_COMMON_USER." ADD COLUMN fail_count  int(1) NOT NULL DEFAULT 0 COMMENT '로그인실패 횟수' ";
	$db->query($sql);
}

$sql = "SHOW COLUMNS FROM ".TBL_COMMON_USER." LIKE 'pw_issued'";
$db->query($sql);
if(empty($db->total)){
	$sql = "ALTER TABLE ".TBL_COMMON_USER." ADD COLUMN pw_issued enum('Y','N') DEFAULT 'N' COMMENT '임시비밀번호 발급 유무 Y:발행 N:미발행' ";
	$db->query($sql);
}

$sql = "SHOW COLUMNS FROM ".TBL_COMMON_USER." LIKE 'pw_issued_date'";
$db->query($sql);
if(empty($db->total)){
	$sql = "ALTER TABLE ".TBL_COMMON_USER." ADD COLUMN pw_issued_date datetime DEFAULT NULL COMMENT '임시비밀번호 발급일' ";
	$db->query($sql);
}

$sql = "SHOW COLUMNS FROM ".TBL_COMMON_USER." LIKE 'change_pw_date'";
$db->query($sql);
if(empty($db->total)){
	$sql = "ALTER TABLE ".TBL_COMMON_USER." ADD COLUMN change_pw_date datetime DEFAULT NULL COMMENT '비밀번호변경일자' ";
	$db->query($sql);
}


/*휴면 설정관련 휴면 테이블 생성 JK*/
$sql = "SHOW TABLES LIKE 'common_user_sleep'";
$db->query($sql);

if(!is_array($db->fetchall())){
	$sql = "CREATE TABLE IF NOT EXISTS common_user_sleep LIKE ".TBL_COMMON_USER."";
	$db->query($sql);
}

$sql = "SHOW TABLES LIKE 'common_member_detail_sleep'";
$db->query($sql);

if(!is_array($db->fetchall())){
	$sql = "CREATE TABLE IF NOT EXISTS common_member_detail_sleep LIKE ".TBL_COMMON_MEMBER_DETAIL."";
	$db->query($sql);
}

/*탈퇴시 주문 개인정보 처리 테이블 생성*/
$sql = "SHOW TABLES LIKE 'separation_shop_order'";
$db->query($sql);
if(!is_array($db->fetchall())){
    $sql = "CREATE TABLE `separation_shop_order` (
          `oid` varchar(20) NOT NULL DEFAULT '' COMMENT '주문번호',
          `btel` varchar(13) DEFAULT NULL COMMENT '주문자전화번호',
          `bmobile` varchar(20) DEFAULT NULL COMMENT '주문자모바일번호',
          `bmail` varchar(100) DEFAULT NULL COMMENT '주문자메일',
          `bzip` varchar(8) DEFAULT NULL COMMENT '주문자우편번호',
          `baddr` varchar(160) DEFAULT NULL COMMENT '주문자주소',
          `regdate` datetime DEFAULT NULL COMMENT '등록일',
          PRIMARY KEY (`oid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='개인정보 분리-주문정보'";
    $db->query($sql);
}

$sql = "SHOW TABLES LIKE 'separation_shop_order_deliveryinfo'";
$db->query($sql);
if(!is_array($db->fetchall())){
    $sql = "CREATE TABLE `separation_shop_order_deliveryinfo` (
                          `odd_ix` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '배송지정보 인덱스',
                          `oid` varchar(20) DEFAULT NULL COMMENT '주문번호',
                          `od_ix` int(8) DEFAULT NULL COMMENT '주문상세인덱스값',
                          `rname` varchar(20) DEFAULT NULL COMMENT '수취인명',
                          `rtel` varchar(15) DEFAULT NULL COMMENT '수취인전화번호',
                          `rmobile` varchar(20) DEFAULT NULL COMMENT '수취인모바일번호',
                          `rmail` varchar(100) DEFAULT NULL COMMENT '수취인메일',
                          `zip` varchar(8) DEFAULT NULL COMMENT '수취인우편번호',
                          `addr1` varchar(160) DEFAULT NULL COMMENT '수취인주소',
                          `addr2` varchar(160) DEFAULT NULL COMMENT '수취인나머지주소',
                          `regdate` datetime DEFAULT NULL COMMENT '등록일',
                          PRIMARY KEY (`odd_ix`),
                          KEY `oid` (`oid`)
                        ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='개인정보 분리-주문 상품별 배송지 정보'";
    $db->query($sql);
}

echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>");
?>