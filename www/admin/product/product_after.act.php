<?
include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
$db = new Database;
$sql = "select pname,admin from shop_product where id = '$pid' ";
$db->query($sql);
$db->fetch();
$pname = $db->dt[pname];
$company_id = $db->dt[admin];
$db->query("insert into shop_bbs_useafter (uf_subject,uf_ix,pid,pname,company_id,ucode, uf_name,uf_contents,uf_hit,uf_valuation,regdate) values('$uf_subject','','$pid','$pname','$company_id','','$uf_name','$uf_contents','0','$uf_valuation',NOW())");
echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품평 작성이 완료 되었습니다.');self.close();</script>");
?>