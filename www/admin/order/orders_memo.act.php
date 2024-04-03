<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");

$db = new Database;



if ($act == "memo_insert"){

	if($sub_bbs_div)	$bbs_div = $sub_bbs_div;

	if($memo_state=="4"){ //처리완료
		$complete_date="NOW()";
	}else{
		$complete_date="''";
	}

	$memo_state_change_date="NOW()";

	$db->sequences = "SHOP_ORDER_MEMO_SEQ";
	$db->query("insert into shop_order_memo(om_ix,ucode,oid,order_date,order_from,memo,counselor,counselor_ix,charger,charger_ix,urgency_yn,call_type,call_action_yn,call_action_date,call_action_time,call_action_state,user_mood_state,memo_div,memo_state,memo_state_change_date,complete_date,regdate) values('','$ucode','$oid','$order_date','$order_from','".str_replace("'",'&#39;',$memo)."','".$_SESSION["admininfo"]["charger"]."','".$_SESSION["admininfo"]["charger_ix"]."','$md_name','$md_code','$urgency_yn','$call_type','$call_action_yn','$call_action_date','$call_action_time','$call_action_state','$user_mood_state','$bbs_div','$memo_state',$memo_state_change_date,$complete_date,NOW())");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메모가 정상적으로 입력되었습니다.');parent.document.location.reload();location.href='about:blank';</script>");
}

if ($act == "memo_update"){

	if($sub_bbs_div)	$bbs_div = $sub_bbs_div;
	
	if($memo_state=="4"){ //처리완료
		$complete_date="NOW()";
	}else{
		$complete_date="''";
	}

	if($b_memo_state != $memo_state){
		$memo_state_change_date="NOW()";
	}else{
		$memo_state_change_date="''";
	}

	$db->query("update shop_order_memo set 
							order_date ='".$order_date."',
							order_from ='".$order_from."',
							memo='".str_replace("'",'&#39;',$memo)."',
							memo_div ='".$bbs_div."',
							memo_state ='".$memo_state."',
							charger='".$_SESSION["admininfo"]["charger"]."',
							charger_ix='".$_SESSION["admininfo"]["charger_ix"]."',
							urgency_yn='".$urgency_yn."',
							call_type='".$call_type."',
							call_action_yn='".$call_action_yn."',
							call_action_date='".$call_action_date."',
							call_action_time='".$call_action_time."',
							call_action_state='".$call_action_state."',
							user_mood_state='".$user_mood_state."',
							memo_state_change_date=".$memo_state_change_date.",
							complete_date=".$complete_date."
						where om_ix= '".$om_ix."'
					");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메모가 정상적으로 수정되었습니다.');parent.document.location.reload();location.href='about:blank';</script>");
}


if ($act == "memo_delete"){
	$db->query("DELETE FROM shop_order_memo WHERE oid='$oid' and om_ix ='$om_ix'");

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('메모가 정상적으로 삭제되었습니다.');parent.document.location.reload();location.href='about:blank';</script>");
}
?>
