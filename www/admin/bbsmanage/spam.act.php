<?
include("../../class/database.class");



$db = new Database;

if ($act == "insert"){

	$sql = "insert into bbs_spam_config (sc_ix,spam_word,block_ip,spam_usable,regdate) values('$sc_ix','$spam_word','$block_ip','$spam_usable',NOW())";
	$db->sequences = "BBS_SPAM_CONFIG_SEQ";
	$db->query($sql);

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('스팸 관리 정보가  정상적으로 등록되었습니다.');</script>");
	echo("<script>document.location.href='spam.php';</script>");
}


if ($act == "update"){

	$sql = "update bbs_spam_config set spam_word='$spam_word',block_ip='$block_ip',spam_usable='$spam_usable'
					where sc_ix='$sc_ix' ";

	$db->query($sql);



	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('스팸 관리 정보가 정상적으로 수정되었습니다.');</script>");
	echo("<script>location.href = 'spam.php';</script>");
}

?>
