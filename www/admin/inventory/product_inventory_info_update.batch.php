<?
include("../../class/database.class");
include("../inventory/inventory.lib.php");

$db = new Database;
$sdb = new Database;

$sql="select 
	u.gu_ix,sum(s.stock) as stock,
	(
		select sum(case when status in ('IR','IC','DP','DR','CA','DD') then pcnt else '0' end) as sell_ing_cnt from shop_order_detail where pcode = u.gu_ix
	) as sell_ing_cnt
from 
	inventory_goods_unit u 
left join 
	inventory_product_stockinfo s 
on 
	(u.gid=s.gid and u.unit=s.unit)
group by 
	u.gu_ix";

$db->query($sql);
$inventory_goods_unit=$db->fetchall();

//print_r($inventory_goods_unit);
//exit;
/*
foreach($inventory_goods_unit as $unit){
	
	if(!$unit[sell_ing_cnt]){
		$unit[sell_ing_cnt]="0";
	}

	$db->query("update ".TBL_SHOP_PRODUCT." set stock = '".$unit[stock]."', sell_ing_cnt = '".$unit[sell_ing_cnt]."' where pcode ='".$unit[gu_ix]."' and stock_use_yn='Y' ");
	$db->query("update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_stock = '".$unit[stock]."', option_sell_ing_cnt = '".$unit[sell_ing_cnt]."' where option_code = '".$unit[gu_ix]."' ");

	$db->query("select * from  ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." where option_code = '".$unit[gu_ix]."' ");
	if($db->total){
		$product_options ="";
		$product_options=$db->fetchall();
		
		foreach($product_options as $options){
			$sql = "SELECT o.pid, sum(option_stock) as option_stock 
						FROM ".TBL_SHOP_PRODUCT_OPTIONS." o , ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od
						WHERE od.pid='".$options[pid]."' and o.opn_ix = od.opn_ix
						and option_kind in ('b','x','x2','s2') group by o.pid ";
			$sdb->query($sql);
			$sdb->fetch();
			$goods_stock = $sdb->dt[option_stock];

			$sdb->query("update ".TBL_SHOP_PRODUCT." set stock = '".$goods_stock."' where id ='".$options[pid]."'");
		}
	}
}
*/

echo "완료";
?>