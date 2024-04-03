<?
include($_SERVER["DOCUMENT_ROOT"]."/class/layout.class");

$db = new Database();
$db2 = new Database();

$interpark_not_in = "('2fc868d274f8b45f35441045c2673b46','eb9c5b29062583fdbcdc3fd9903bb261','5d3adb6c933183ddac15426532948a42','fbbcb5efa262ff9b0b5bcacfbdad4e46','8beac8aab830d9ec68fe5cba69653b0b','daa9c141868d73fd5bf5060a2eeceff1','d8b888f97dd12da4971526e9bbde135f')";


$not_in_11st = "('2fc868d274f8b45f35441045c2673b46','eb9c5b29062583fdbcdc3fd9903bb261','daa9c141868d73fd5bf5060a2eeceff1','d8b888f97dd12da4971526e9bbde135f')";


$gmarket_not_in = "('2fc868d274f8b45f35441045c2673b46','5d3adb6c933183ddac15426532948a42','fbbcb5efa262ff9b0b5bcacfbdad4e46','8beac8aab830d9ec68fe5cba69653b0b','daa9c141868d73fd5bf5060a2eeceff1','d8b888f97dd12da4971526e9bbde135f')";


$auction_not_in = "('2fc868d274f8b45f35441045c2673b46','eb9c5b29062583fdbcdc3fd9903bb261','5d3adb6c933183ddac15426532948a42','fbbcb5efa262ff9b0b5bcacfbdad4e46','8beac8aab830d9ec68fe5cba69653b0b','daa9c141868d73fd5bf5060a2eeceff1','d8b888f97dd12da4971526e9bbde135f')";

/*
fashionplus

왕십리_Tommy Hilfiger		2fc868d274f8b45f35441045c2673b46
왕십리_GUESS				eb9c5b29062583fdbcdc3fd9903bb261
강변테크노_GUESS				5d3adb6c933183ddac15426532948a42
강변테크노_Samsonite RED		fbbcb5efa262ff9b0b5bcacfbdad4e46
동탄_Samsonite RED		8beac8aab830d9ec68fe5cba69653b0b
왕십리_NORTHFACE			daa9c141868d73fd5bf5060a2eeceff1
동탄_new balance kids		d8b888f97dd12da4971526e9bbde135f

패션플러스 판매불가 브랜드입니다.
*/

$f_not_in = "('2fc868d274f8b45f35441045c2673b46','eb9c5b29062583fdbcdc3fd9903bb261','5d3adb6c933183ddac15426532948a42','fbbcb5efa262ff9b0b5bcacfbdad4e46','8beac8aab830d9ec68fe5cba69653b0b','daa9c141868d73fd5bf5060a2eeceff1','d8b888f97dd12da4971526e9bbde135f')";

$sql = "select id from ".TBL_SHOP_PRODUCT." where state='1' and disp='1' and admin not in $f_not_in ";
$db->query($sql);
if($db->total){
	for($i=0; $i < $db->total; $i++){
		$db->fetch($i);
		$sql = "select pid from sellertool_get_product where pid = '".$db->dt[id]."' and site_code = 'fashionplus' ";
		$db2->query($sql);

		if(!$db2->total){
			$sql = "insert into sellertool_get_product (pid,site_code,state) values ('".$db->dt[id]."','fashionplus','1')";
			$db2->query($sql);
		}else{
			$sql = "update sellertool_get_product set state = '1' where site_code = 'fashionplus' and state='0' and pid = '".$db->dt[id]."' ";
			$db2->query($sql);
		}
	}
}


/*
$sql = "select id from ".TBL_SHOP_PRODUCT." where admin = '9230953a96895ab22296fd26d1bf6d3f' and disp ='1' and state ='1' ";
$db->query($sql);
if($db->total){
	for($i=0; $i < $db->total; $i++){
		$db->fetch($i);
		$sql = "select pid from sellertool_get_product where pid = '".$db->dt[id]."' and site_code = 'auction' ";
		$db2->query($sql);

		if(!$db2->total){
			$sql = "insert into sellertool_get_product (pid,site_code,state) values ('".$db->dt[id]."','auction','1')";
			$db2->query($sql);
		}
	}
}
*/
?>