<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database;

if($event_gift=="E") {
	if(!$event_gift_num) {
		$eque="SELECT MAX(event_gift_num) as maxnum FROM shop_gift_certificate";
		$db->query($sql);
		$db->fetch();
		$erow=$db->dt[maxnum];
		if($erow[0]) {
			$event_gift_num=$erow[0]+1;
		}else{
			$event_gift_num=1;
		}
	}else{
		$event_gift_num=$event_gift_num;
	}
}else{
	$event_gift = "G";
	$event_gift_num="";
}

if($act == "update"){
	$gift_start_date = $FromYY."-".$FromMM."-".$FromDD;
	$gift_end_date = $ToYY."-".$ToMM."-".$ToDD;
	
	$sql = "update shop_gift_certificate set 
					gift_code='$gift_code',gift_amount='$gift_amount',gift_start_date='$gift_start_date',gift_end_date='$gift_end_date',gift_change_state='$gift_change_state',
					event_gift='$event_gift',event_gift_num='$event_gift_num',reg_member_id='$reg_member_id',reg_ip='$reg_ip',reg_date='$reg_date',member_id='$member_id',
					member_ip='$member_ip',chagne_request_date='$chagne_request_date',memo='$memo' 
					where uid='$uid' ";

	$db->query($sql);

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품권 정보가 정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
}

if($act == "insert"){
	$gift_start_date = $FromYY."-".$FromMM."-".$FromDD;
	$gift_end_date = $ToYY."-".$ToMM."-".$ToDD;
	
	$pattern = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
	for($Loop=0;$Loop<$endLoop;$Loop++) {
		for($i=0;$i<$length;$i++){
			$key .= $pattern{rand(0,35)};
		}	
		$sql = "Select * from shop_gift_certificate where gift_code ='".trim($key)."' ";
		$db->query($sql);
		if(!$db->total){
			if(trim($key) != ""){
				$sql = "insert into shop_gift_certificate 
							(uid,gift_code,gift_amount,gift_start_date,gift_end_date,gift_change_state,event_gift,event_gift_num,reg_member_id,reg_ip,reg_date,memo) 
							values
							('$uid','$key','$gift_amount','$gift_start_date','$gift_end_date','0','$event_gift','$event_gift_num','".$admininfo["company_id"]."','".$_SERVER["REMOTE_ADDR"]."',NOW(),'$memo')";
				echo $i.":".$sql."<br>\n";
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

	if($next_mode == "goon"){
		if($check_mode == "test"){
			echo "<script>alert('상품권 정보가 정상적으로 확인되었습니다. 등록 : $new_cnt , 중복 : $dupe_cnt ');</script>";
		}else{
			echo "<script>alert('상품권 정보가 정상적으로 등록되었습니다. 등록 : $new_cnt , 중복 : $dupe_cnt ');</script>";
		}
	}else{
		if($check_mode == "test"){
			echo "<script>alert('상품권 정보가 정상적으로 확인되었습니다. 등록 : $new_cnt , 중복 : $dupe_cnt ');parent.document.location.reload();</script>";
		}else{
			echo "<script>alert('상품권 정보가 정상적으로 등록되었습니다. 등록 : $new_cnt , 중복 : $dupe_cnt ');parent.document.location.reload();</script>";
		}
	}
}


if($act == "delete"){
	
	$sql = "delete from shop_gift_certificate where uid='$uid' ";
					


	$db->query($sql);
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert(\"상품권 정보가 정상적으로 삭제 되었습니다. \");parent.document.location.reload();</script>";
}
?>