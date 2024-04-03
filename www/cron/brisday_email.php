<?
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/include/email.send.php");

// 생일이 오늘인 사람만 추출한다.
$sql="select  cu.id, cmd.birthday,  AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name, 
			AES_DECRYPT(UNHEX(cmd.mail),'".$db->ase_encrypt_key."') as mail,
			AES_DECRYPT(UNHEX(cmd.pcs),'".$db->ase_encrypt_key."') as pcs,
			cu.code, cmd.gp_ix
			from ".TBL_COMMON_USER." cu , ".TBL_COMMON_MEMBER_DETAIL." cmd where cu.code=cmd.code and right(birthday,6) = date_format(now(),'-%m-%d') ";

$db->query($sql);

$members = $db->fetchall("object");

foreach ($members as $member) {

    preferredConditionGiveCoupon('3', $member["code"], $member['gp_ix']);

//	$mail_info[mem_name] = $member[name];
//	$mail_info[mem_mail] = $member[mail];
//	$mail_info[mem_id] = $member[id];
//	$mail_info[mem_mobile] = $member[pcs];
//
//	sendMessageByStep('member_reg', $mail_info);

}

?>
