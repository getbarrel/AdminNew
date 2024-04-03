<?
include("../class/database.class");

session_start();

$db = new Database;

if($act == "add"){
	$db->query("select * from shop_admin_favorite where page_id = '".$page_id."' and company_id = '".$admininfo["company_id"]."' and charger_id = '".$admininfo["admin_id"]."'  ");
	if($db->total){
		echo "<script language='javascript'>alert('이미 추가된 즐겨찾기 메뉴입니다.');</script>";
	}else{
		$db->query("select max(view_order) as view_order from shop_admin_favorite where company_id = '".$admininfo["company_id"]."' and charger_id = '".$admininfo["admin_id"]."'  ");
		if($db->total){
			$db->fetch();
			$view_order = $db->dt[view_order]+1;
		}else{
			$view_order = 1;
		}

		$sql = "insert into shop_admin_favorite(favorite_ix,company_id,charger_id,page_id,favorite_name,favorite_link,view_order,regdate) values('','".$admininfo["company_id"]."','".$admininfo["admin_id"]."','$page_id','$favorite_name','$favorite_link','$view_order',NOW())";
		$db->sequences = "SHOP_ADMIN_FAVORITE_SEQ";
		$db->query($sql);

		echo "<script language='javascript'>alert('즐겨찾기가 정상적으로 추가되었습니다.');parent.document.location.reload();</script>";
	}
}

if($act == "delete"){
	$db->query("delete from shop_admin_favorite where company_id = '".$admininfo["company_id"]."' and charger_id = '".$admininfo["admin_id"]."' and page_id = '".$page_id."' ");

	echo "<script language='javascript'>alert('즐겨찾기가 정상적으로 삭제되었습니다.');parent.document.location.reload();</script>";

}
?>