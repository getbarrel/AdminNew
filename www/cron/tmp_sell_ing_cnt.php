<?

include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");

$db = new Database();

// select id,option_sell_ing_cnt from shop_product_options_detail where option_sell_ing_cnt > 0 order by option_sell_ing_cnt desc
// select id,sell_ing_cnt from shop_product where sell_ing_cnt > 0 order by sell_ing_cnt desc
// select gu_ix,sell_ing_cnt from inventory_goods_unit where sell_ing_cnt > 0 order by sell_ing_cnt desc

$db->query("select id,pid from shop_product_options_detail where option_sell_ing_cnt > 0 ");
$id_array=$db->fetchall("object");


foreach($id_array as $key => $val){
	
	$sql="select sum(pcnt) as sell_ing_cnt from shop_order_detail  where status in ('IR','IC','DR','DD') and option_id='".$val[id]."'";
	$db->query($sql);
	$db->fetch();
	$sell_ing_cnt = $db->dt[sell_ing_cnt];
	
	$sql="update shop_product_options_detail set option_sell_ing_cnt = '".$sell_ing_cnt."' where id ='".$val[id]."'";
	//echo $sql."<br/>";
	$db->query($sql);

	$sql="update ".TBL_SHOP_PRODUCT." p set p.sell_ing_cnt = (select sum(option_sell_ing_cnt) from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od where od.pid=p.id) where p.pcode ='".$val[pid]."' ";
	//echo $sql."<br/>";
	$db->query($sql);
}


$db->query("select gu_ix from inventory_goods_unit where sell_ing_cnt > 0 ");
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){
	
	$sql="select sum(pcnt) as sell_ing_cnt from shop_order_detail  where status in ('IR','IC','DR','DD') and gu_ix='".$val[gu_ix]."'";
	$db->query($sql);
	$db->fetch();
	$sell_ing_cnt = $db->dt[sell_ing_cnt];
	

	$sql = "update ".TBL_SHOP_PRODUCT." set sell_ing_cnt = '".$sell_ing_cnt."' where pcode ='".$val[gu_ix]."' and stock_use_yn='Y' ";
	//echo $sql."<br/>";
	$db->query($sql);

	$sql = "select od.id as opnd_ix ,pid from ".TBL_SHOP_PRODUCT." p left join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od on (p.id=od.pid) where p.stock_use_yn='Y' and option_code = '".$val[gu_ix]."' ";
	$db->query($sql);
	if($db->total){
		$option_dt_info = $db->fetchall();
		for($j=0;$j<count($option_dt_info);$j++){
			$sql="update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_sell_ing_cnt = '".$sell_ing_cnt."' where id = '".$option_dt_info[$j][opnd_ix]."' ";
			//echo $sql."<br/>";
			$db->query($sql);
		}
		
		$sql="update ".TBL_SHOP_PRODUCT." p set p.sell_ing_cnt = (select sum(option_sell_ing_cnt) from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od where od.pid=p.id) where p.pcode ='".$val[gu_ix]."' and p.stock_use_yn='Y' ";
		//echo $sql."<br/>";
		$db->query($sql);
	}
	$sql="update inventory_goods_unit set sell_ing_cnt = '".$sell_ing_cnt."' where gu_ix = '".$val[gu_ix]."' ";
	//echo $sql."<br/>";
	$db->query($sql);
}


$db->query("select id from shop_product where sell_ing_cnt > 0 and stock_use_yn in ('Q','Y') ");
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){
	$db->query("select sum(pcnt) as sell_ing_cnt from shop_order_detail  where status in ('IR','IC','DR','DD') and pid='".$val[id]."' ");
	$db->fetch();
	$sell_ing_cnt = $db->dt[sell_ing_cnt];

	$sql="update ".TBL_SHOP_PRODUCT." set sell_ing_cnt = '".$sell_ing_cnt."' where id ='".$val[id]."' ";
	//echo $sql."<br/>";
	$db->query($sql);
}


?>