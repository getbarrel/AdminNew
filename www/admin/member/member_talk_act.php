<?
include("$DOCUMENT_ROOT/class/mysql.class");

$db = new MySQL;
$db2 = new MySQL;

if($tel_1){
	$user_tel	=	$tel_1."-".$tel_2."-".$tel_3;
}

if($phone_1){
	$user_phone	=	$phone_1."-".$phone_2."-".$phone_3;
}

if($act == "memo"){
	
	$sql = "INSERT INTO 
				shop_member_talk_memo
			SET
				ca_ix = '".$admininfo['charger_ix']."',
				tm_memo = '$tm_memo'";
	$db->query($sql);
	
	echo '메모가 저장되었습니다';

}

if($act == "memo_delete"){
	
	$sql = "DELETE FROM
				shop_member_talk_memo
			WHERE 
				tm_ix = '$tm_ix'
			";
	$db->query($sql);
	
	echo '메모가 삭제되었습니다';

}


//셀러 긴급사항
if($act == 'seller_call'){
	
	$sql = "SELECT 
					AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name , AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs ,
					AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail ,
					AES_DECRYPT(UNHEX(cmd.tel),'".$db->ase_encrypt_key."') as tel ,cmd.gp_ix, cmd.level_ix, cmd.sex_div,cu.id , cu.code , cu.mem_type ,
					cu.mileage,cu.point,cu.deposit
				FROM 
					common_member_detail cmd
				LEFT JOIN
					common_user cu
				ON (cmd.code = cu.code)
				WHERE cmd.code = '".$admininfo['charger_ix']."'
				";
	$db->query($sql);
	$result = $db->fetch();

	for($i=0;$i<count($md_code);$i++){

	$sql = "insert into common_seller_support SET
				type = '".$type[$i]."',
				title = '".$title[$i]."',
				name='".$result['name']."',
				id='".$result['id']."',
				code = '".$code."',
				company_id = '".$company_id[$i]."',
				md_code = '".$md_code[$i]."',
				md_name = '".$md_name[$i]."',
				status = 'W',
				tel = '".$result['tel']."',
				mobile = '".$result['pcs']."',
				mail = '".$result['mail']."',
				contents = '".$ta_memo[$i]."',
				regdate = NOW()";
	$db->query($sql);

	}

	echo "<script>alert('문의요청 하였습니다');history.go(-1);</script>";
	exit;

}

if($act == "pop_insert"){
	
	for($i=0;$i<count($ta_memo);$i++){
		
		$aw_type = '';

		if($ans_sms[$i]){
			$aw_type = $ans_sms[$i];
		}

		if($ans_email[$i]){
			$aw_type .= ",".$ans_email[$i];
		}

		if($ans_call[$i]){
			$aw_type .= ",".$ans_email[$i];
			$aw_time  = $aw_time[$i]." ".$aw_hour[$i].":".$aw_minute[$i].":00";
		}

		/*if(substr($aw_type,0,1) == ','){
			$aw_type;
		}*/

		$sql = "INSERT INTO 
					shop_member_talk_history
				SET
					ta_type			= '".$ta_type."' ,
					ta_charger		= '".$ta_charger[$i]."' ,
					ta_charger_ix	= '".$ta_charger_ix[$i]."' ,
					ta_code			= '".$ta_code."' ,
					ta_counselor	= '".$ta_counselor."' ,
					qa_state		= '".$qa_state[$i]."' ,
					aw_type			= '".$aw_type."' ,
					aw_time			= '".$aw_time."' ,
					ucode			= '".$ucode."' ,
					user_id			= '".$user_id."' ,
					user_name		= '".$user_name."' ,
					user_group		= '".$user_group."' ,
					user_qa_group	= '".$user_qa_group[$i]."' ,
					oid				= '".$oid[$i]."' ,
					ta_memo			= '".$ta_memo[$i]."' ,
					regdate			= now()
			";

		$db->query($sql);
	}
	echo "<script>alert('등록되었습니다.');parent.location.reload();</script>";
	
}

if($act == "insert"){
	
	$user_tel = $tel_1."-".$tel_2."-".$tel_3;
	$user_phone = $phone_1."-".$phone_2."-".$phone_3;
	$ta_code = date("Ymd")."-".rand(10000, 99999);
	$sql = "INSERT INTO 
				shop_member_talk_history
			(ta_code,ucode,user_type,user_name,user_group,user_qa_group,user_sub_group,user_id,user_level,user_tel,user_phone,emergency_type,customer_state,oid,pid,add_file,ta_memo,ta_counselor,ta_charger,tc_ix,aw_type,aw_memo,qa_state,aw_state,regdate)
			VALUES
			('$ta_code','$ucode','$user_type','$user_name','$user_group','$bbs_div','$sub_bbs_div','$user_id','$level_ix','$user_tel','$user_phone','$emergency_type','$customer_state','$oid','$pid','$add_file','$ta_memo','$ta_counselor','$ta_charger','$tc_ix','$aw_type','$aw_memo','$qa_state','$aw_state',now())
			";

	if($db->query($sql)){
		echo "<script>alert('등록되었습니다.');top.opener.document.location.reload();top.window.close();</script>";
	}else{
		echo "<script>alert('등록실패하였습니다.');history.go(-1);</script>";
	}

}

if($act == "update"){
	
	$sql = "UPDATE 
				shop_member_talk_history
			SET
				user_type		=	'$user_type' ,
				user_name		=	'$user_name' ,
				user_group		=	'$user_group' , 
				user_qa_group	=	'$bbs_div' , 
				user_sub_group	=	'$sub_bbs_div' , 
				user_id			=	'$user_id' , 
				user_level		=	'$level_ix' , 
				user_tel		=	'$user_tel' , 
				user_phone		=	'$user_phone' , 
				emergency_type	=	'$emergency_type' , 
				customer_state	=	'$customer_state' , 
				oid				=	'$oid' , 
				pid				=	'$pid' , 
				add_file		=	'$add_file', 
				ta_memo			=	'$ta_memo' , 
				ta_counselor	=	'$ta_counselor' , 
				ta_charger		=	'$ta_charger' , 
				tc_ix			=	'$tc_ix' , 
				aw_type			=	'$aw_type' , 
				aw_memo			=	'$aw_memo' , 
				qa_state		=	'$qa_state' , 
				aw_state		=	'$aw_state' , 
				moddate			= now()
			WHERE ta_ix = '$ta_ix'
			";

	if($db->query($sql)){
		echo "<script>alert('수정되었습니다.');top.opener.document.location.reload();top.window.close();</script>";
	}else{
		echo "<script>alert('수정실패하였습니다.');history.go(-1);</script>";
	}

}

if($act == "delete") {
	
	if(! empty($_REQUEST['ta_ix'])){
	foreach($_REQUEST['ta_ix'] as $val):
		$ta_ix_str	.= ",'".$val."'";
	endforeach;
	}

	$ta_ix_str	=	mb_substr($ta_ix_str,1);

	$sql = "delete from shop_member_talk_history where ta_ix IN($ta_ix_str)";
	
	if($db->query($sql)){
		echo "<script>alert('삭제되었습니다.');history.go(-1);</script>";
	}else{
		echo "<script>alert('삭제실패하였습니다.');history.go(-1);</script>";
	}

}

?>