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
	//$gift_start_date = $FromYY."-".$FromMM."-".$FromDD;
	//$gift_end_date = $ToYY."-".$ToMM."-".$ToDD;
	/*
	$sql = "update shop_gift_certificate set
					gift_code='$gift_code',gift_amount='$gift_amount',gift_start_date='$gift_start_date',gift_end_date='$gift_end_date',
					gift_change_state='$gift_change_state',	gift_type='$gift_type',mem_type='$mem_type',use_priod='$use_priod',
					gift_type_num='$gift_type_num',reg_member_id='$reg_member_id',reg_ip='$reg_ip',reg_date='$reg_date',member_id='$member_id',
					member_ip='$member_ip',chagne_request_date='$chagne_request_date',memo='$memo'
					where uid='$uid' ";
	*/
	
	// gift_type='".$gift_type."', gift_cupon_ix='".$appoint_publish_ix."', 20131118 Hong disabled 때문에
	if($db->dbms_type == "oracle"){
		$gift_start_date = "to_date('$gift_start_date','yyyy-mm-dd')";
		$gift_end_date = "to_date('$gift_end_date','yyyy-mm-dd')";

		$sql = "update shop_gift_certificate set
		 mall_ix= '".$mall_ix."' , 
		 gift_certificate_name='".$gift_certificate_name."',
		 gift_prefix_code='".$gift_prefix_code."',
		 gift_amount='".$gift_amount."',
		 create_cnt='".$create_cnt."',
		 gift_start_date=".$gift_start_date.",
		 gift_end_date=".$gift_end_date.",
		 reg_cnt='".$reg_cnt."',
		 reg_mem_ix='".$reg_mem_ix."',
		 memo='".$memo."'
		 where gc_ix='".$gc_ix."' ";

	}else{
		$sql = "update shop_gift_certificate set
		 mall_ix= '".$mall_ix."' , 
		 gift_certificate_name='".$gift_certificate_name."',
		 gift_prefix_code='".$gift_prefix_code."',
		 gift_amount='".$gift_amount."',
		 create_cnt='".$create_cnt."',
		 gift_start_date='".$gift_start_date."',
		 gift_end_date='".$gift_end_date."',
		 reg_cnt='".$reg_cnt."',
		 reg_mem_ix='".$reg_mem_ix."',
		 memo='".$memo."'
		 where gc_ix='".$gc_ix."' ";
	}

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
//	print_r($_POST);
//	exit;

	if($gift_type=="U"){
		$sql = "Select * from shop_gift_certificate where gift_type='U' and gift_prefix_code ='".trim($gift_prefix_code)."' ";
		$db->query($sql);

		if($db->total){
			echo "<script>parent.$.unblockUI();alert('기존에 Gift Code ".trim($gift_prefix_code)."를 사용하셨습니다. 다른 Gift Code를 이용해 주시기 바랍니다.');</script>";
			exit;
		}
	}

	if($FromYY.$FromMM.$FromDD > $ToYY.$ToMM.$ToDD){
		echo "<script>parent.$.unblockUI();alert('쿠폰 등록일자가 사용일자보다 이전일수 없습니다.');</script>";
		exit;
	}

	//$gift_start_date = $FromYY."-".$FromMM."-".$FromDD;
	//$gift_end_date = $ToYY."-".$ToMM."-".$ToDD;


	/*
	$sql = "insert into shop_gift_certificate set
	 gc_ix = SHOP_GIFT_CERTIFICATE_SEQ.nextval,
	 gift_certificate_name='".$gift_certificate_name."',
	 gift_prefix_code='".$gift_prefix_code."',
	 gift_amount='".$gift_amount."',
	 create_cnt='".$create_cnt."',
	 gift_start_date='".$gift_start_date."',
	 gift_end_date='".$gift_end_date."',
	 gift_type='".$gift_type."',
	 reg_cnt='".$reg_cnt."',
	 reg_mem_ix='".$reg_mem_ix."',
	 regdate=NOW(),
	 memo='".$memo."' ";
	*/


	if($db->dbms_type == "oracle"){

		$gift_start_date = "to_date('$gift_start_date','yyyy-mm-dd')";
		$gift_end_date = "to_date('$gift_end_date','yyyy-mm-dd')";

		//$sql = "insert into shop_gift_certificate(gc_ix,gift_certificate_name,gift_prefix_code,gift_amount,gift_cupon_ix,create_cnt,gift_start_date,gift_end_date,gift_type,reg_cnt,reg_mem_ix,regdate,memo) values('','$gift_certificate_name','$gift_prefix_code','$gift_amount','$appoint_publish_ix','$create_cnt',$gift_start_date,$gift_end_date,'$gift_type','$reg_cnt','$reg_mem_ix',NOW(),'$memo')";

		$sql = "insert into shop_gift_certificate(gc_ix,gift_certificate_name,gift_prefix_code,gift_amount,create_cnt,gift_start_date,gift_end_date,gift_type,reg_cnt,reg_mem_ix,regdate,memo) values('','$gift_certificate_name','$gift_prefix_code','$gift_amount','$create_cnt',$gift_start_date,$gift_end_date,'$gift_type','$reg_cnt','$reg_mem_ix',NOW(),'$memo')";
		$db->query($sql);

		$gc_ix = $db->last_insert_id;
	}else{

		//$sql = "insert into shop_gift_certificate(gc_ix,gift_certificate_name,gift_prefix_code,gift_amount,gift_cupon_ix,create_cnt,gift_start_date,gift_end_date,gift_type,reg_cnt,reg_mem_ix,regdate,memo) values('','$gift_certificate_name','$gift_prefix_code','$gift_amount','$appoint_publish_ix','$create_cnt','$gift_start_date','$gift_end_date','$gift_type','$reg_cnt','$reg_mem_ix',NOW(),'$memo')";

		$sql = "insert into shop_gift_certificate(gc_ix,gift_certificate_name,gift_prefix_code,gift_amount,create_cnt,gift_start_date,gift_end_date,gift_type,reg_cnt,reg_mem_ix,regdate,memo) values('','$gift_certificate_name','$gift_prefix_code','$gift_amount','$create_cnt','$gift_start_date','$gift_end_date','$gift_type','$reg_cnt','$reg_mem_ix',NOW(),'$memo')";
		$db->query($sql);
		

		$db->query("SELECT gc_ix FROM shop_gift_certificate WHERE gc_ix = LAST_INSERT_ID()");
		$db->fetch();
		$gc_ix = $db->dt[gc_ix];
	}


		
	
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
					/*
					$sql = "insert into shop_gift_certificate_detail set
							 gcd_ix='".$gcd_ix."',
							 gc_ix='".$gc_ix."',
							 gift_code='".$gift_prefix_code.$key."',
							 gift_change_state='0'  ";
					*/
					$sql = "insert into shop_gift_certificate_detail(gcd_ix,gc_ix,gift_code,gift_change_state) values('','".$gc_ix."','".$gift_prefix_code.$key."','0')";
					$db->sequences = "SHOP_GIFT_CERTIFICATE_DT_SEQ";
					/*
					$sql = "insert into shop_gift_certificate
								(uid,gift_code,gift_amount,gift_start_date,gift_end_date,gift_change_state,gift_type,event_gift_num, mem_type, use_priod,reg_member_id,reg_ip,reg_date,memo)
								values
								('$uid','".$gift_prefix_code.$key."','$gift_amount','$gift_start_date','$gift_end_date','0','$gift_type','$event_gift_num','$mem_type','$use_priod','".$admininfo["company_id"]."','".$_SERVER["REMOTE_ADDR"]."',NOW(),'$memo')";
					*/
					//echo $i.":".$sql."<br>\n";

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

if($act == "detail_delete"){

	$sql = "delete from shop_gift_certificate_detail where gcd_ix='$gcd_ix' ";
	$db->query($sql);

	echo "<script>alert(\"상품권 정보가 정상적으로 삭제 되었습니다. \");parent.document.location.reload();</script>";
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