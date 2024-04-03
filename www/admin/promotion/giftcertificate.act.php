<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

if($gift_type=="E") {
	if(!$gift_type_num) {
		$eque="SELECT MAX(gift_type_num) as maxnum FROM shop_gift_certificate";
		$db->query($sql);
		$db->fetch();
		$erow=$db->dt[maxnum];
		if($erow[0]) {
			$gift_type_num=$erow[0]+1;
		}else{
			$gift_type_num=1;
		}
	}else{
		$gift_type_num=$gift_type_num;
	}
}else{
	if(!$gift_type){
		$gift_type = "G";
	}
	$gift_type_num="";
}

if($act == "update"){
	$sql = "update shop_gift_certificate set
	 gift_certificate_name='".$gift_certificate_name."',
	 gift_amount='".$gift_amount."',
	 gift_start_date='".$gift_start_date."',
	 gift_end_date='".$gift_end_date."',
	 memo='".$memo."'
	 where gc_ix='".$gc_ix."' ";

	$db->query($sql);

	if(is_array($appoint_publish_ix)){
		$sql = "update shop_gift_certificate_cupon set insert_yn = 'N' where gc_ix = '".$gc_ix."' ";
		$db->query($sql);

		foreach($appoint_publish_ix as $cupon_ix){
			if($cupon_ix !=''){
				$sql = "select * from shop_gift_certificate_cupon where gc_ix = '".$gc_ix."' and gcc_ix = '".$cupon_ix."' ";
				$db->query($sql);
					
				if($db->total){
					$sql = "update shop_gift_certificate_cupon set insert_yn = 'Y' where gcc_ix = '".$gcc_ix."' and gcc_ix = '".$cupon_ix."'  ";
					$db->query($sql);
				}else{
					$sql = "insert into shop_gift_certificate_cupon (gcc_ix,gc_ix,gift_cupon_ix,insert_yn, regdate) values('','".$gc_ix."','".$cupon_ix."','Y',NOW())";
					$db->sequences = "SHOP_GIFT_CERTIFICATE_CUPON_SEQ";
					$db->query($sql);
				}
			}
		}

		$sql = "delete from shop_gift_certificate_cupon where insert_yn = 'N' and gc_ix = '".$gc_ix."' ";
		$db->query($sql);
	}

	echo "<script>alert('상품권 정보가 정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
}

if($act == "insert"){
	if($gift_way == 1){
		$length = 12;
	}else{
		$length = 16;

		if($gift_type=="U"){
			$gift_prefix_code = '';
			$pattern = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			for($i=0;$i<$length;$i++){
				$gift_prefix_code .= $pattern{rand(0,35)};
			}
		}
	}
	
	if($gift_type=="U"){
		$sql = "Select * from shop_gift_certificate where gift_type='U' and gift_prefix_code ='".trim($gift_prefix_code)."' ";
		$db->query($sql);

		if($db->total){
			echo "<script>parent.$.unblockUI();alert('기존 Code ".trim($gift_prefix_code)."를 사용하셨습니다. 다른 Code를 이용해 주시기 바랍니다.');</script>";
			exit;
		}
	}

	if($FromYY.$FromMM.$FromDD > $ToYY.$ToMM.$ToDD){
		echo "<script>parent.$.unblockUI();alert('쿠폰 등록일자가 사용일자보다 이전일수 없습니다.');</script>";
		exit;
	}

	$sql = "insert into shop_gift_certificate(gc_ix,gift_certificate_name,gift_prefix_code,gift_amount,create_cnt,gift_start_date,gift_end_date,gift_type,reg_cnt,reg_mem_ix,regdate,memo, gift_way) values('','$gift_certificate_name','$gift_prefix_code','$gift_amount','$create_cnt','$gift_start_date','$gift_end_date','$gift_type','$reg_cnt','$reg_mem_ix',NOW(),'$memo','$gift_way')";
	$db->query($sql);
	

	$db->query("SELECT gc_ix FROM shop_gift_certificate WHERE gc_ix = LAST_INSERT_ID()");
	$db->fetch();
	$gc_ix = $db->dt[gc_ix];

	if(is_array($appoint_publish_ix)){
		$sql = "update shop_gift_certificate_cupon set insert_yn = 'N' where gc_ix = '".$gc_ix."' ";
		$db->query($sql);

		foreach($appoint_publish_ix as $cupon_ix){
			if($cupon_ix !=''){
				$sql = "select * from shop_gift_certificate_cupon where gc_ix = '".$gc_ix."' and gcc_ix = '".$cupon_ix."' ";
				$db->query($sql);
					
				if($db->total){
					$sql = "update shop_gift_certificate_cupon set insert_yn = 'Y' where gcc_ix = '".$gcc_ix."' and gcc_ix = '".$cupon_ix."'  ";
					$db->query($sql);
				}else{
					$sql = "insert into shop_gift_certificate_cupon (gcc_ix,gc_ix,gift_cupon_ix,insert_yn, regdate) values('','".$gc_ix."','".$cupon_ix."','Y',NOW())";
					$db->sequences = "SHOP_GIFT_CERTIFICATE_CUPON_SEQ";
					$db->query($sql);
				}
			}
		}
		$sql = "delete from shop_gift_certificate_cupon where insert_yn = 'N' and gc_ix = '".$gc_ix."' ";
		$db->query($sql);
	}

	if($gift_type!="U"){
		$pattern = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		for($Loop=0;$Loop<$create_cnt;$Loop++) {
			for($i=0;$i<$length;$i++){
				$key .= $pattern{rand(0,35)};
			}
			$sql = "Select * from shop_gift_certificate_detail where gift_code ='".trim($gift_prefix_code.$key)."' ";
			$db->query($sql);

			if(!$db->total){
				if(trim($key) != ""){
					$sql = "insert into shop_gift_certificate_detail(gcd_ix,gc_ix,gift_code,gift_change_state) values('','".$gc_ix."','".$gift_prefix_code.$key."','0')";
					$db->sequences = "SHOP_GIFT_CERTIFICATE_DT_SEQ";
					if($check_mode != "test"){
						$db->query($sql);
					}
				$new_cnt++;
				}
			}else{
				$dupe_cnt++;
			}

			$key = '';
		}

		//exit;
		if($next_mode == "goon"){
			if($check_mode == "test"){
				echo "<script>alert('상품권 정보가 정상적으로 확인되었습니다. ');</script>";//등록 : $new_cnt , 중복 : $dupe_cnt '
			}else{
				echo "<script>alert('상품권 정보가 정상적으로 등록되었습니다. ');</script>";
			}
		}else{
			if($check_mode == "test"){
				echo "<script>alert('상품권 정보가 정상적으로 확인되었습니다. ');</script>";
			}else{
				echo "<script>alert('상품권 정보가 정상적으로 등록되었습니다. ');parent.document.location.href='../promotion/giftcertificate.php';</script>";
			}
		}
	}else{
		echo "<script>alert('상품권 정보가 정상적으로 등록되었습니다.');parent.document.location.href='../promotion/giftcertificate.php';</script>";
	}
	exit;
}


if($act == "delete"){

	$sql = "delete from shop_gift_certificate where gc_ix='$gc_ix' ";
	$db->query($sql);

	$sql = "delete from shop_gift_certificate_detail where gc_ix='$gc_ix' ";
	$db->query($sql);

	$sql = "delete from shop_gift_certificate_cupon where gc_ix='$gc_ix' ";
	$db->query($sql);

	echo "<script>alert(\"상품권 정보가 정상적으로 삭제 되었습니다. \");parent.document.location.reload();</script>";
}

if($act == "delete_selected"){
	if(! empty($search_searialize_value) && $update_type == 1){
		$unserialize_search_value = unserialize(urldecode($search_searialize_value));
		extract($unserialize_search_value);
	}

	if($update_type == 1){
		$where = " where gc.gc_ix <> '0' ";

		if($gift_change_state != ""){
			$where .= " and gc.gift_change_state = $gift_change_state ";
		}

		if($gift_type){
			$where .= " AND gc.gift_type = '".$gift_type."' ";
		}

		if($search_text != ""){
			if($search_type != ""){
				$where .= " and $search_type LIKE '%".trim($search_text)."%' ";
			}else{
				$where .= " and (gift_certificate_name LIKE '%".trim($search_text)."%' or memo LIKE '%".trim($search_text)."%') ";
			}
		}

		if($reg_sdate != "" && $reg_edate != ""){
			$where .= " and gc.regdate between '$reg_sdate 00:00:00' and '$reg_edate 23:59:59' ";
		}

		if($gift_start_date != "" && $gift_end_date != ""){
			$where .= " and  (gc.gift_start_date between  '$gift_start_date' and '$gift_end_date' or gc.gift_start_date between  '$gift_start_date' and '$gift_end_date' )";
		}

		$sql = "select gc.gc_ix
					from shop_gift_certificate gc
					left join common_member_detail m on gc.reg_mem_ix = m.code
					$where";
		$db->query($sql);
		$lists = $db->fetchall("object");
	}else{
		$sql = "select gc.gc_ix
					from shop_gift_certificate gc where gc_ix in ('".implode("','",$ix)."')";
		$db->query($sql);
		$lists = $db->fetchall("object");
	}
	
	if(is_array($lists)){
		foreach($lists as $k => $v){
			$sql = "delete from shop_gift_certificate where gc_ix='".$v[gc_ix]."' ";
			$db->query($sql);
			$sql = "delete from shop_gift_certificate_detail where gc_ix='".$v[gc_ix]."' ";
			$db->query($sql);
			$sql = "delete from shop_gift_certificate_cupon where gc_ix='".$v[gc_ix]."' ";
			$db->query($sql);
		}
	}

	echo "<script>alert(\"상품권 정보가 정상적으로 삭제 되었습니다. \");top.document.location.href='./giftcertificate.php';</script>";
	exit;
}

if($act == "detail_delete"){

	$sql = "delete from shop_gift_certificate_detail where gcd_ix='$gcd_ix' ";
	$db->query($sql);

	echo "<script>alert(\"상품권 정보가 정상적으로 삭제 되었습니다. \");parent.document.location.reload();</script>";
}

if($act == "delete_detail_selected"){
	if(! empty($search_searialize_value) && $update_type == 1){
		$unserialize_search_value = unserialize(urldecode($search_searialize_value));
		extract($unserialize_search_value);
	}

	if($update_type == 1){
		$where = " where gcd.gc_ix <> '0' and gcd.gc_ix = '".$gc_ix."' and gc.gc_ix = gcd.gc_ix  ";

		if($gift_change_state != ""){
			$where .= " and gcd.gift_change_state = $gift_change_state ";
		}

		if($search_text != ""){
			if($search_type != ""){
				if($search_type == "gcd.gift_code"){
					 $search_text = str_replace("-","",$search_text);
					 $where .= " and $search_type LIKE '%".trim($search_text)."%' ";

				}else if($search_type == "m.name"){
					$where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
				}else{
					$where .= " and $search_type LIKE '%".trim($search_text)."%' ";
				}
			}else{
				$where .= " and (gcd.gift_code LIKE '%".str_replace("-","",trim($search_text))."%' or AES_DECRYPT(UNHEX(m.name),'".$db->ase_encrypt_key."') LIKE '%$search_text%' or cu.id LIKE '%$search_text%') ";
			}
		}

		$startDate = $FromYY.$FromMM.$FromDD;
		$endDate = $ToYY.$ToMM.$ToDD;

		if($startDate != "" && $endDate != ""){
			$where .= " and  gcd.use_date between $startDate and $endDate ";
		}

		$sql = "select gcd.gcd_ix from 
					shop_gift_certificate_detail gcd 
					left join common_user as cu on (gcd.member_id = cu.id)
					left join common_member_detail m on cu.code = m.code
					,shop_gift_certificate gc
				$where";
		$db->query($sql);
		$lists = $db->fetchall("object");
	}else{
		$sql = "select gcd.gcd_ix 
					from shop_gift_certificate_detail gcd where gcd_ix in ('".implode("','",$ix)."')";
		$db->query($sql);
		$lists = $db->fetchall("object");
	}
	
	if(is_array($lists)){
		foreach($lists as $k => $v){
			$sql = "delete from shop_gift_certificate_detail where gcd_ix='".$v[gcd_ix]."' ";
			$db->query($sql);
		}
	}

	echo "<script>alert(\"상품권 정보가 정상적으로 삭제 되었습니다. \");top.document.location.href='./giftcertificate_detail.php?gc_ix=".$gc_ix."';</script>";
	exit;
}

if($act == "copy"){
	$db->query("select gift_way, gift_type, gift_prefix_code, create_cnt from shop_gift_certificate where gc_ix='$gc_ix'");
	$db->fetch();
	$gift_way = $db->dt[gift_way];
	$gift_type = $db->dt[gift_type];
	$gift_prefix_code = $db->dt[gift_prefix_code];
	$create_cnt = $db->dt[create_cnt];
	$b_gc_ix = $gc_ix;

	if($gift_way == 1){
		$length = 12;
	}else{
		$length = 16;

		if($gift_type=="U"){
			$gift_prefix_code = '';
			$pattern = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			for($i=0;$i<$length;$i++){
				$gift_prefix_code .= $pattern{rand(0,35)};
			}
		}
	}

	$sql = "insert into shop_gift_certificate(gift_certificate_name,gift_prefix_code,gift_amount,create_cnt,gift_start_date,gift_end_date,gift_type,reg_cnt,reg_mem_ix,regdate,memo, gift_way) 
	select gift_certificate_name,'".$gift_prefix_code."',gift_amount,create_cnt,gift_start_date,gift_end_date,gift_type,reg_cnt,reg_mem_ix,NOW(),memo, gift_way
	from shop_gift_certificate where gc_ix='".$b_gc_ix."'";
	$db->query($sql);

	$db->query("SELECT gc_ix FROM shop_gift_certificate WHERE gc_ix=LAST_INSERT_ID()");
	$db->fetch();
	$gc_ix = $db->dt[gc_ix];

	if($gift_type=="C" || $gift_type=="U"){
		$sql = "select gcc_ix from shop_gift_certificate_cupon where gc_ix = '".$b_gc_ix."'";
		$db->query($sql);
		$c_list = $db->fetchall("object");

		for($i=0;$i < count($c_list);$i++){
			$sql = "insert into shop_gift_certificate_cupon (gc_ix,gift_cupon_ix,insert_yn, regdate)
							select '".$gc_ix."',gift_cupon_ix, 'Y', NOW() from shop_gift_certificate_cupon where gcc_ix = '".$c_list[$i][gcc_ix]."'";
			$db->query($sql);
		}
	}
	
	if($gift_type != "U"){
		$pattern = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		for($Loop=0;$Loop<$create_cnt;$Loop++) {
			for($i=0;$i<$length;$i++){
				$key .= $pattern{rand(0,35)};
			}
			$sql = "Select * from shop_gift_certificate_detail where gift_code ='".trim($gift_prefix_code.$key)."' ";
			$db->query($sql);

			if(!$db->total){
				if(trim($key) != ""){
					$sql = "insert into shop_gift_certificate_detail(gcd_ix,gc_ix,gift_code,gift_change_state) values('','".$gc_ix."','".$gift_prefix_code.$key."','0')";
					$db->sequences = "SHOP_GIFT_CERTIFICATE_DT_SEQ";
					if($check_mode != "test"){
						$db->query($sql);
					}
				$new_cnt++;
				}
			}else{
				$dupe_cnt++;
			}

			$key = '';
		}
	}

	echo "<script>alert('상품권 정보가 정상적으로 복사되었습니다.');parent.document.location.href='../promotion/giftcertificate.php';</script>";
	exit;
}

/*
CREATE TABLE IF NOT EXISTS `mallstory_gift_certificate` (
  `gc_ix` int(11) unsigned NOT NULL auto_increment,
  `gift_certificate_name` varchar(100) NOT NULL default '',
  `gift_code` varchar(32) NOT NULL default '',
  `gift_amount` int(11) unsigned NOT NULL default '0',
  `gift_start_date` date default '0000-00-00',
  `gift_end_date` date default '0000-00-00',
  `gift_change_state` char(1) NOT NULL default '',
  `gift_type` enum('E','G','M') default 'E',
  `mem_type` enum('S','G') default 'S',
  `use_priod` int(3) default '0',
  `event_gift_num` int(11) unsigned NOT NULL default '0',
  `reg_member_id` varchar(255) NOT NULL default '',
  `reg_ip` varchar(15) NOT NULL default '',
  `reg_date` datetime default '0000-00-00 00:00:00',
  `member_id` varchar(255) NOT NULL default '',
  `member_ip` varchar(15) NOT NULL default '',
  `chagne_request_date` datetime default '0000-00-00 00:00:00',
  `memo` varchar(255) default NULL,
  PRIMARY KEY  (`gc_ix`),
  UNIQUE KEY `gift_code` (`gift_code`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8





CREATE TABLE IF NOT EXISTS `shop_gift_certificate` (
  `gc_ix` int(11) unsigned NOT NULL auto_increment,
  `gift_certificate_name` varchar(100) NOT NULL default '',
  `gift_prefix_code` varchar(32) NOT NULL default '',
  `gift_amount` int(11) unsigned NOT NULL default '0',
  `create_cnt` int(11) unsigned NOT NULL default '0',
  `gift_start_date` date default '0000-00-00',
  `gift_end_date` date default '0000-00-00',
  `gift_type` enum('E','G','M','R') default 'E',
  `reg_cnt` int(11) unsigned NOT NULL default '0',
  `reg_mem_ix` varchar(255) NOT NULL default '',
  `regdate` datetime default '0000-00-00 00:00:00',
  `memo` varchar(255) default NULL,
  PRIMARY KEY  (`gc_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8


CREATE TABLE IF NOT EXISTS `shop_gift_certificate_detail` (
  `gcd_ix` int(11) unsigned NOT NULL auto_increment,
  `gift_code` varchar(32) NOT NULL default '',
  `gift_change_state` char(1) NOT NULL default '',
  `member_id` varchar(255) NOT NULL default '',
  `member_ip` varchar(15) NOT NULL default '',
  `use_date` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`gcd_ix`),
  UNIQUE KEY `gift_code` (`gift_code`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8

*/
?>