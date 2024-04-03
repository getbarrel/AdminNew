<?
include($_SERVER["DOCUMENT_ROOT"]."/admin/class/layout.class");


//session_start();
if(!$admininfo){
	echo("<script language='javascript' src='../_language/language.php'></script><script>alert(language_data['common']['C'][language]);</script>");
	//'로그인 하신후에 사용하실수  있습니다.'
	exit;
}

$db = new Database;


if ($act == "insert"){

	$db->sequences = "SHOP_BBS_USEAFTER_SEQ";
	$db->query("insert into ".TBL_SHOP_BBS_USEAFTER." (uf_ix,pid,pname,company_id,ucode, uf_name,uf_contents,uf_hit,uf_valuation,regdate,uf_subject) values('','$pid','".$pname."','".$admin."','".$admin."','".$uf_name."','$uf_contents','0','$uf_valuation',NOW(),'$uf_subject')");
	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품평등록이 완료 되었습니다.');self.close();</script>");
}


?>