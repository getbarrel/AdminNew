<?
echo 1;
exit;


include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/inventory/inventory.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/global_util.php");
include($_SERVER["DOCUMENT_ROOT"]."/include/lib.function.php");
include($_SERVER["DOCUMENT_ROOT"]."/admin/include/admin.util.php");

$db = new Database();
$mdb = new Database();
$master_db = new Database();

exit;
//발주입고 두번재고 입고되어 처리하는 프로세스
$ioid = "10150629144119-30833";

$sql="select * from inventory_order_detail where ioid='".$ioid."' and real_cnt > cnt";
$db->query($sql);
$iod_array=$db->fetchall("object");
foreach($iod_array as $val){

	$error_cnt = $val['real_cnt'] - $val['cnt'];
	$iod_ix = $val['iod_ix'];

	$sql = "select od.gid, od.unit, od.standard,
	od.buy_price as price ,
	o.company_id as company_id,
	o.pi_ix as pi_ix,
	ps.ps_ix as ps_ix,
	'".$error_cnt."' as amount
	from inventory_order_detail od
	left join inventory_order o on (o.ioid=od.ioid)
	left join inventory_place_info pi on (pi.pi_ix=o.pi_ix)
	left join inventory_place_section ps on (ps.pi_ix=pi.pi_ix and ps.section_type='S')
	where  od.iod_ix = '".$iod_ix."' ";
	$db->query($sql);
	$delivery_iteminfo = $db->fetchall("object");

	if($delivery_iteminfo[0][ps_ix]!=""){

		$item_info["act_from"] = "inventory";
		$item_info[h_div] = "2"; // 1:입고 2: 출고
		$item_info[vdate] = date("Ymd");
		$item_info[ioid] = $ioid;
		$item_info[msg] = "시스템 발주입고 조정";//$_POST["etc"];
		$item_info[h_type] = '18';//18; 기타(재고오차)
		$item_info[charger_name] = $_SESSION["admininfo"]["charger"];
		$item_info[charger_ix] = $_SESSION["admininfo"]["charger_ix"];
		$item_info[detail] = $delivery_iteminfo;

		UpdateGoodsItemStockInfo($item_info, $db);

		$sql="update inventory_order_detail set real_cnt= real_cnt-'".$error_cnt."' where ioid='".$ioid."' and iod_ix='".$iod_ix."' ";
		$db->query($sql);
	}
}

$sql="select 
			sum(iod.cancel_cnt) as cancel_goods_cnt,
			sum(iod.buy_price*iod.real_cnt) as real_goods_price,
			sum(iod.real_cnt) as real_goods_cnt,
			io.goods_cnt as total_goods_cnt,io.delivery_price
		from
			inventory_order_detail iod , inventory_order io
		where iod.ioid=io.ioid and iod.ioid='".$ioid."' ";
$db->query($sql);
$db->fetch();

$update_str="";

if($db->dt[total_goods_cnt]==($db->dt[real_goods_cnt]+$db->dt[cancel_goods_cnt])){
	$update_str=" status='WC', wc_date=NOW(), ";
}elseif($db->dt[real_goods_cnt]>0){
	$update_str=" status='WP', ";
}

$sql="update inventory_order set 
			$update_str
			cancel_goods_cnt='".$db->dt[cancel_goods_cnt]."',
			real_goods_price='".$db->dt[real_goods_price]."',
			real_goods_cnt='".$db->dt[real_goods_cnt]."',
			real_delivery_price='".$db->dt[delivery_price]."',
			real_total_price='".($db->dt[real_goods_price]+$db->dt[delivery_price])."'
		where ioid='".$ioid."' ";
$db->query($sql);

exit;

include("./basic/company.lib.php");
$sql="select ccd.company_id,ccd.com_name from common_company_detail ccd left join  common_company_relation ccr on (ccd.company_id=ccr.company_id) where ccr.relation_code is null ";
$db->query($sql);
$list = $db->fetchall("object");


foreach($list as $li){
	$new_code = check_relation('C0001');
	$seq	= check_seq($relation_code,$depth);

	$sql = "insert into 
		".TBL_COMMON_COMPANY_RELATION." set
	company_id = '".$li[company_id]."',
	relation_code = '".$new_code."',
	seq = '".$seq."',
	reg_date = NOW();";
	
	echo $sql;
	$db->query($sql);
}

exit;
$userData["abd8c785f888cbc85928f3416d754c14"]="10448";
$userData["7a9985eec4a7db36fe80f2948e35032b"]="11575";
$userData["ed50015222e435495c2c0d23de9efbe6"]="14101";
$userData["ad6b3a5ebba7330bcaa0656b3292a6f4"]="14932";
$userData["5708e198fb661a3f9172bc33b1184be6"]="17150";
$userData["fc732f700cf8d0ac64025423691a92ee"]="18375";
$userData["d03411f7127b83a0e3e95f35592f60e4"]="20077";

$etc="카페24 일괄 적립금 지급";
foreach($userData as $code => $reserve){
	InsertReserveInfo($code,"","","",$reserve,'1','1',$etc,'mileage',$_SESSION["admininfo"]);
}

exit;

$sql="select * from shop_order_detail where oid in ('20150225030502-87106','20150225033154-41924','20150225063606-73133','20150225082325-55888','20150225090257-46825','20150225090701-67351','20150225094611-41696','20150225094644-95888','20150225095729-25083','20150225095743-93174','20150225100016-61315','20150225100431-93685','20150225100814-96211','20150225103024-49548','20150225111204-17187','20150225112255-61214','20150225113006-26785','20150225114106-89244','20150225114526-01277','20150225115245-92071','20150225120402-96403','20150225120451-36077','20150225122012-28351','20150225122342-62726','20150225123000-22366','20150225131608-51532','20150225132338-92271','20150225134730-36429','20150225140544-64277','20150225142307-10499','20150225143428-92226','20150225143812-19479','20150225150147-97867','20150225150443-89032','20150225151625-48681','20150225154053-08101','20150225154204-63099','20150225154429-40916','20150225155805-16702','20150225160123-33557','20150225163255-64262','20150225164303-19334','20150225164747-43251','20150225165323-69981','20150225173549-64224','20150225173716-04192','20150225174106-05648') ";
$db->query($sql);
$id_array=$db->fetchall("object");
foreach($id_array as $key => $val){
	$sql="select option_name,option_div from shop_product_options o, shop_product_options_detail od where o.opn_ix=od.opn_ix and od.id='".$val[option_id]."' ";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		
		if(!(substr_count($val['option_text'],$db->dt['option_div']) > 0)){

			$option_text = $val['option_text'].$db->dt['option_name'].":".$db->dt['option_div']."<br/>";
			
			$sql="update shop_order_detail set option_text='".$option_text."' where od_ix='".$val[od_ix]."' ";
			$db->query($sql);
		}
	}
}

exit;
//재고 히스토리 기반으로 inventory_product_stockinfo 다시 정보 만들기!
$sql="select hd.gid,hd.unit,hd.amount,h.company_id,h.pi_ix,h.ps_ix from inventory_history h left join inventory_history_detail hd on ( h.h_ix=hd.h_ix) where hd.is_delete='1'";
$db->query($sql);
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){
	$sql="select psi_ix from inventory_product_stockinfo where company_id='".$val[company_id]."' and pi_ix='".$val[pi_ix]."' and ps_ix='".$val[ps_ix]."' and gid='".$val[gid]."' and unit='".$val[unit]."' ";
	$mdb->query($sql);
	if($mdb->total){
		$mdb->fetch();
		$sql="update inventory_product_stockinfo set stock = stock - '".$val[amount]."' where psi_ix ='".$mdb->dt[psi_ix]."' ";
		echo $sql."<br/>";
		//$mdb->query($sql);
	}else{
		echo "gid -'".$val[gid]."' inventory_product_stockinfo 데이터 없음<br/>";
	}
}
exit;
//shop_order_claim_delivery 마이그래이션 처리하기!!!
//2.환불 신청건들 처리하기!!
$sql="select oid from ".TBL_SHOP_ORDER_DETAIL." where refund_status in ('FA') and oid='20140805130917-49648' group by oid";
$db->query($sql);
$order_infos = $db->fetchall("object");

for($i=0;$i < count($order_infos);$i++){
	//2012-10-09 홍진영
	$mdb->query("select * from ".TBL_SHOP_ORDER." WHERE oid='".$order_infos[$i][oid]."'");
	$order="";
	$order = $mdb->fetch();

	$mdb->query("select *, pid as id from ".TBL_SHOP_ORDER_DETAIL." WHERE oid='".$order_infos[$i][oid]."' and refund_status in ('FA') $and_company_id");
	$order_details="";
	$order_details = $mdb->fetchall("object");

	$product_info=array();
	$product_com_info=array();
	foreach($order_details as $key => $detail){

		$product_com_info[$detail[company_id]] = $detail[delivery_type];

		$product_info[$key] = $detail;
		$product_info[$key][claim_type]="C";
		$product_info[$key][claim_group]="99";//클래임그룹임시로 99로 동일!
		$product_info[$key][claim_fault_type]=fetch_order_status_div('IR','CA',"type",$reason_code);//클래임책임자
		$product_info[$key][claim_apply_yn]="Y";//요청상품
		$product_info[$key][claim_apply_cnt]=$detail[pcnt];//요청상품수량
	}
	
	$resulte = clameChangePriceCalculate($product_info);
	
	foreach($product_com_info as $company_id => $delivery_type){

		$sql="select ( max(claim_group) +1 ) as claim_group from shop_order_detail where oid='".$order_infos[$i][oid]."' ";
		$db->query($sql);
		$db->fetch();
		$claim_group = $db->dt["claim_group"];

		$sql="update ".TBL_SHOP_ORDER_DETAIL." set claim_group = '".$claim_group."' where oid='".$order_infos[$i][oid]."' and refund_status in ('FA') and company_id='".$company_id."' $and_company_id ";
		echo $sql."<br/>";
		$db->query($sql);

		$sql="insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,regdate) values('','".$order_infos[$i][oid]."','$company_id','$delivery_type','$claim_group','".$resulte[delivery][$company_id][delivery_price]."','".$order_infos[$i][fa_date]."')";
		echo $sql."<br/>";
		$db->query($sql);
	}
}


$sql="select oid from ".TBL_SHOP_ORDER_DETAIL." where refund_status in ('FA') and oid='20140805130917-49648' group by oid";
$db->query($sql);


exit;
//shop_order_delivery 생성안된거 처리하기!
$sql="select * from shop_order_detail where od_ix in ('352618','354485','355985','356042','356456','357585','358838','359340','359341','359342','360790','360791','360912','360913','360914','360915','360916','360917','360918','360919','360920','360921','360922','360923','360924','360925','360926','360927','360928','360929','361290','361912','361913','361914','361915','361916','362661','362920','362921','362922','363226','363227','363736','364849','364850','365119','365120','365691','365750','365751','367339','367368','367594','368077','371194','371195','372514','372515','372516','373056','373058')";
$db->query($sql);
$order_infos = $db->fetchall("object");

for($i=0;$i < count($order_infos);$i++){
	
	$sql="SELECT 
			org_delivery_price 
		FROM
			shop_order 
		WHERE 
			oid='".$order_infos[$i][oid]."' ";
	$db->query($sql);
	$db->fetch();
	$delivery_dcprice = $db->dt[org_delivery_price];

	if($order_infos[$i][delivery_type]=="1"){//통합배송
		$sql="SELECT 
			ode_ix 
		FROM
			shop_order_delivery 
		WHERE 
			oid='".$order_infos[$i][oid]."' 
			and delivery_type = '".$order_infos[$i][delivery_type]."' 
			and delivery_package = '".$order_infos[$i][delivery_package]."'
			and delivery_method = '".$order_infos[$i][delivery_method]."'
			and delivery_pay_type = '".$order_infos[$i][delivery_pay_method]."'
			and delivery_addr_use = '".$order_infos[$i][delivery_addr_use]."'
			and factory_info_addr_ix = '".$order_infos[$i][factory_info_addr_ix]."'
			and ori_company_id = '".$order_infos[$i][ori_company_id]."'";
		$db->query($sql);
		$delivery="";
		if($db->total){
			$delivery = $db->fetch();
			$ode_ix = $db->dt[ode_ix];

			$sql="update shop_order_detail set ode_ix='".$ode_ix."' where od_ix='".$order_infos[$i][od_ix]."' ";
			//echo $sql."<br/>";
			$db->query($sql);
		}else{
			$sql = "insert into shop_order_delivery (ode_ix,oid,company_id,ori_company_id,delivery_type,delivery_package,delivery_policy,delivery_method,delivery_pay_type,delivery_addr_use,factory_info_addr_ix,pid,delivery_price,delivery_dcprice,regdate) values ('','".$order_infos[$i][oid]."','".$order_infos[$i][company_id]."','".$order_infos[$i][ori_company_id]."','".$order_infos[$i][delivery_type]."','".$order_infos[$i][delivery_package]."','".$order_infos[$i][delivery_policy]."','".$order_infos[$i][delivery_method]."','".$order_infos[$i][delivery_pay_method]."','".$order_infos[$i][delivery_addr_use]."','".$order_infos[$i][factory_info_addr_ix]."','".$order_infos[$i][pid]."','".$delivery_dcprice."','".$delivery_dcprice."',NOW())";
			$db->query($sql);
			$ode_ix = $db->insert_id();

			$sql="update shop_order_detail set ode_ix='".$ode_ix."' where od_ix='".$order_infos[$i][od_ix]."' ";
			//echo $sql."<br/>";
			$db->query($sql);
		}
	}else{
		$sql = "insert into shop_order_delivery (ode_ix,oid,company_id,ori_company_id,delivery_type,delivery_package,delivery_policy,delivery_method,delivery_pay_type,delivery_addr_use,factory_info_addr_ix,pid,delivery_price,delivery_dcprice,regdate) values ('','".$order_infos[$i][oid]."','".$order_infos[$i][company_id]."','".$order_infos[$i][ori_company_id]."','".$order_infos[$i][delivery_type]."','".$order_infos[$i][delivery_package]."','".$order_infos[$i][delivery_policy]."','".$order_infos[$i][delivery_method]."','".$order_infos[$i][delivery_pay_method]."','".$order_infos[$i][delivery_addr_use]."','".$order_infos[$i][factory_info_addr_ix]."','".$order_infos[$i][pid]."','".$delivery_dcprice."','".$delivery_dcprice."',NOW())";
		$db->query($sql);

		$ode_ix = $db->insert_id();
		$sql="update shop_order_detail set ode_ix='".$ode_ix."' where od_ix='".$order_infos[$i][od_ix]."' ";
		//echo $sql."<br/>";
		$db->query($sql);
	}
}

exit;
//ode_ix 매칭 안된거 처리하기!
$sql="select oid,od_ix,delivery_type,delivery_package,delivery_method,delivery_pay_method,delivery_addr_use,factory_info_addr_ix,ori_company_id from shop_order_detail where ode_ix=0 ";
$db->query($sql);
$order_infos = $db->fetchall("object");

for($i=0;$i < count($order_infos);$i++){

	$sql="SELECT 
		ode_ix 
	FROM
		shop_order_delivery 
	WHERE 
		oid='".$order_infos[$i][oid]."' 
		and delivery_type = '".$order_infos[$i][delivery_type]."' 
		and delivery_package = '".$order_infos[$i][delivery_package]."'
		and delivery_method = '".$order_infos[$i][delivery_method]."'
		and delivery_pay_type = '".$order_infos[$i][delivery_pay_method]."'
		and delivery_addr_use = '".$order_infos[$i][delivery_addr_use]."'
		and factory_info_addr_ix = '".$order_infos[$i][factory_info_addr_ix]."'
		and ori_company_id = '".$order_infos[$i][ori_company_id]."'";
	$db->query($sql);
	$delivery="";
	if($db->total){
		$delivery = $db->fetch();
		$ode_ix = $db->dt[ode_ix];

		$sql="update shop_order_detail set ode_ix='".$ode_ix."' where od_ix='".$order_infos[$i][od_ix]."' ";
		//echo $sql."<br/>";
		$db->query($sql);
	}
}

exit;
//재고 히스토리 기반으로 inventory_product_stockinfo 다시 정보 만들기!
$sql="select h.h_ix,hd.gid,hd.unit, (case when h_div='2' then -amount else amount end) as cnt from inventory_history h left join inventory_history_detail hd on ( h.h_ix=hd.h_ix) where h.ps_ix='0'";
$db->query($sql);
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){

	$sql="select bp.company_id, bp.pi_ix , bp.ps_ix , ccd.com_name, pi.place_name, ps.section_name
	from 
		inventory_goods_basic_place bp 
		left join common_company_detail ccd on (bp.company_id = ccd.company_id)
		left join inventory_place_info pi on (bp.pi_ix = pi.pi_ix)
		left join inventory_place_section ps on (bp.ps_ix = ps.ps_ix)

	where bp.gid='".$val[gid]."' and bp.unit='".$val[unit]."' and bp.pi_ix='1' ";
	$db->query($sql);
	if($db->total){
		$db->fetch();
		$sql="update inventory_history set com_name='".$db->dt[com_name]."',place_name='".$db->dt[place_name]."',section_name='".$db->dt[section_name]."',company_id='".$db->dt[company_id]."',pi_ix='".$db->dt[pi_ix]."',ps_ix='".$db->dt[ps_ix]."',msg='ps_ix 0 데이터 기본보관창고로 수정' where h_ix='".$val[h_ix]."' ";
	}else{
		$sql="update inventory_history set com_name='(주)한웰이쇼핑',place_name='남사물류센터',section_name='입고 보관장소',company_id='362ed8ee1cba4cc34f80aa5529d2fbcd',pi_ix='1',ps_ix='1',msg='ps_ix 0 데이터 기본보관창고로 수정' where h_ix='".$val[h_ix]."' ";
	}
	
	echo $sql."<br/>";
	$mdb->query($sql);

	$sql="select psi_ix from inventory_product_stockinfo where ps_ix='1' and gid='".$val[gid]."' and unit='".$val[unit]."' ";
	$mdb->query($sql);
	
	if($mdb->total){
		$mdb->fetch();
		
		if($val[cnt] > 0){
			$total_in_stock=$val[cnt];
			$total_out_stock=0;
		}else{
			$total_out_stock=$val[cnt]*-1;
			$total_in_stock=0;
		}

		$sql="update inventory_product_stockinfo set stock = stock + '".$val[cnt]."' , total_in_stock = total_in_stock + '".$total_in_stock."' , total_out_stock = total_out_stock + '".$total_out_stock."' where psi_ix ='".$mdb->dt[psi_ix]."' ";
		//echo $sql."<br/>";
		$mdb->query($sql);

	}else{
		
		if($val[cnt] > 0){
			$first_stock=$val[cnt];
			$total_in_stock=$val[cnt];
			$total_out_stock=0;
		}else{
			$first_stock=$val[cnt];
			$total_out_stock=$val[cnt]*-1;
			$total_in_stock=0;
		}

		$sql = "insert into inventory_product_stockinfo
		(psi_ix,vdate, expiry_date, company_id,pi_ix,ps_ix, gid,unit,stock_pcode,stock,exit_order,first_stock,total_in_stock,total_out_stock,regdate)
		values
		('','".date("Ymd")."','','362ed8ee1cba4cc34f80aa5529d2fbcd','1','1','".$val[gid]."','".$val[unit]."','','".$val[cnt]."','1','".$first_stock."','".$total_in_stock."','".$total_out_stock."',NOW()) ";

		//echo $sql."<br/>";
		$mdb->query($sql);

	}
}

exit;



$sql="delete from inventory_product_stockinfo where ps_ix='0' ";
$db->query($sql);
exit;


// 히스토리랑  inventory_product_stockinfo sum 값 틀린거 재고 마추기
$sql="select a.gid , stock , amount from 
(
	select gid , sum(stock) as stock  from inventory_product_stockinfo group by gid
) a
left join 

(
	select hd.gid,sum(case when h_div='2' then -amount  else  amount end) as amount from inventory_history h left join inventory_history_detail hd on ( h.h_ix=hd.h_ix) group by gid

) b on (a.gid=b.gid)

where stock!=amount";
$db->query($sql);
$id_array=$db->fetchall("object");


foreach($id_array as $key => $val){
	$sql = "select hd.gid,hd.unit,h.ps_ix,sum(case when h_div='2' then -amount  else  amount end) as stock from inventory_history h left join inventory_history_detail hd on ( h.h_ix=hd.h_ix) where hd.gid='".$val[gid]."' group by  hd.gid,hd.unit,h.ps_ix ";
	$db->query($sql);
	$stockinfo = $db->fetchall("object");
	
	foreach($stockinfo as $info){
		$sql="update inventory_product_stockinfo set stock = '".$info[stock]."' where gid ='".$info[gid]."' and unit ='".$info[unit]."' and ps_ix ='".$info[ps_ix]."' ";
		echo $sql."<br/>";
		$db->query($sql);
	}
}

exit;
//제휴사 주문이 재고 맵핑 안되거 처리하기!

$sql="select * from ".TBL_SHOP_ORDER_DETAIL." where oid in ('20140930200005-02874','20141001113004-95072','20141001120003-32648') and stock_use_yn='Y' and gu_ix='0' ";
$db->query($sql);
$order_infos = $db->fetchall("object");

for($i=0;$i < count($order_infos);$i++){
	
	$sql="select 
		p.*, csd.account_type,csd.account_info,csd.ac_delivery_type,csd.ac_expect_date,csd.account_method, csd.commission as com_commission,
		(select pr.cid from shop_product_relation pr where pr.pid=p.id order by basic desc limit 0,1) as cid, 
		(select com_name from common_company_detail where company_id=p.admin) as company_name,
		(select com_name from common_company_detail where company_id=p.trade_admin) as trade_company_name
	from 
		shop_product p, common_seller_delivery csd
	where 
		csd.company_id=p.admin and p.id='".$order_infos[$i][pid]."' ";
	$db->query($sql);
	$db->fetch();

	$pcode = $db->dt[pcode];
	$product_type = $db->dt[product_type];
	$stock_use_yn = $db->dt[stock_use_yn];
	$option_id = $order_infos[$i][option_id];

	if($stock_use_yn=="Y"){
		if($option_id!="0"){
			$sql="select gu.gid,gu.gu_ix from shop_product_options_detail pod , inventory_goods_unit gu where pod.option_code=gu.gu_ix and pod.id='".$option_id."' ";
			$db->query( $sql );
			$db->fetch();
			$gid = $db->dt["gid"];
			$gu_ix = $db->dt["gu_ix"];
			$pcode = $db->dt["gu_ix"];
		}else{
			$sql="select gu.gid,gu.gu_ix from inventory_goods_unit gu where gu.gu_ix='".$pcode."' ";
			$db->query( $sql );
			$db->fetch();
			$gid = $db->dt["gid"];
			$gu_ix = $db->dt["gu_ix"];
		}
	}else{
		$gid="";
		$gu_ix="";
	}
	
	
	$sql = "UPDATE ".TBL_SHOP_ORDER_DETAIL." SET
			gid = '".$gid."',
			gu_ix = '".$gu_ix."'
		where od_ix='".$order_infos[$i][od_ix]."' ";
	$db->query($sql);
}



exit;


$sql="select company_id,delivery_company from common_seller_delivery where company_id in ('f7fa4636b6f00a25388c0010bf36dffe','c3f7f0922eee3758b442b64e1603aa45','4bfcb1d25307f69e71cbb97dc5283835','0cbb4f09f1110f661d0d5e4b4ac98b2e','a691a17d143e1e3478d941333c638352','228c521e78e1e3fc372857b478dec32e','4b45a9f0794c3e6262f72671a07bcd30','7096b1b4e481f7abc96094050147b7ce','74979fa51597e6a86c2659c1f558dd76','1754e8b96e604d91867b203cf3c875c4','76b1d6b23e0ca80b0386c70169064e37','ad51cd76275b224b9dfb72fbba2f4510','63760d10e613032f3a859f60d7e1ed86','71ec4a6cd148ff0caa4be2e9d9c9b735','c124a92de09fd57f25f888c5135ab4ad','bd502a6c9be7462432871ba23fdf3673','04d5e5adba5c8368c0c75198a8d71540','2b4252cb3ef3c6ad0544e75ff765bef1','6c782c5a1867738722028417fdbe3444','456ab55e8f35e1553d0c0a551aa6a4b5','18b012c8319010d9e6c1949783f4b5af','836c7873767c9961cd130139c0daff37','a4b9a05b8c58f2ea7cdb19ec625329da','dd19d11e3eb7adc4e2a7f77418203826','10a44ef1b2075b0063850dece2c60e9d','c72e0d088cbfdd30452ca85472739747','c2968d7def3e67b0d596e3669a1f4eb5','881299461b25a84b240b943714237236','1add166848a73c2e54f59f1d4afdd29d','91cc39dbacf5e97b220ceb17eca2abcd','942462bb41ec9c35ecbc82336b5ae526','c26bf49fc35a226e9907962491109844','79beceebf4be4480e97f29827477dcfd','453e81b32dfc199713d69bd9cc9022a7','e5b868f99f467281722c72eef19dfb00','87417ef56aacde46e06ed654896daa16','6936b7d6e555a685bb1d7998ea431752','c3e1d59b02e2a96ff2da23742a16c68c','4eef9aabd424c99fc665d8c8ff225e69','e230bded63f8966132e5887d10a32af3','8c30527cacc502dced70fa400fdf2096','5fb54cd8b926839741511c68ff9551ce','e63306560f691428aa55e3b605b88861','92924ef0d417b95901dff27c15317be2','12ce24ddc188f23d4784b60569f522ae','6d22236c3cdf1ab550084875b9a8e318','438e8fa6a4d62dd8300817c5885a65fd','17dda62d389177fbabf0675f80cf6ad2','eacc47c18f1b0b490c1b6d29208dd1f9','6fb0b520895734711b987af92352cf26','a8757dbdb6ed30133d3d081fdd3136e4','60ade701ebae4776a7aad06e21bbebec','5611d1187a75ae621c7e3ce4c4c6a08f','e626b8575b75617f9bbacb65c6483188','303a398467a8fcefd3fa40da8fd6501d','a32412f4f5292a7f2a0326055e07c572','66def47c7f18ac053fe39c2063943dcc','6cd69e45928d92395fb0e19a4b818210','4f198dbccf5703474927e6ef9ed147df','effda05b12706e507b4074506bddf35d','c356d784f85c3771f3134cfef62e074f','1f6fa9b709731a5f84464a31540ff0f2','6abcf9b8160bded48abea1015d539c20','0c4b1791d5595286e73baab51309ad94','2e0e42a4e177b67b7c01019c863ffc2c','37863095d59256e4b069a88fec238f96','806ebdb678131b81fe2860acbdd636b7','48738f0f96f942fb3bb508dba766caf9','2150d99e258be5880876e46169c39360','9a0badbdc2c40409b2916156855ca6e9','02ebcd8d426dea00ed276802644011f1','aaaa5dba86dff5c269562185c7c19aad','2139da54a1d0f5a73606b7b51aa2395d','2960c835291d7ec6a31d109cfbaa34f5','08080fb57c5541b3f6010f89f3ea7bc5','41f9deb39c43268ecd4f9bc4a7ee857b','36fbf707b7547ae8ae81b8cb12c9907c','6cc0768a14f1b7fc64950ed74e40fe90','7489c3b850b56252be216dc96db57cd8','0b2c9f0fb454091ffb387b8be5a7c376','e28429947962168462c2f6120c53836f','ec9c3e260fac6c1b5fd7088428bb09d4','97e09dd0faddff5cc40534f19f73aad3','2bde883ddca332dab0ad6459e59dc082','a206c7efbf0668f465a5e7d5e2dc9c31','d4fd603d47d00429bd52cf038520ff1c','2c1810c1498a826324fb7334a8dfb95e','c6dba2ed54a6d508cf7e834d345c9695','e35246dae147e071871f14dba58d47af','0976a8f9884e7a08e276d6d875d22a59','b0ea1c87884693615bf90b75dff63854','55c7706a59876ee6e51c70c22bd584c4','4f3e780cfeed8bd4bdd16bbd41099b71','2170f72646ce41e566573b340f161dff','8b491a509d80345a5a1fb3042fe59ad0','585ecc49f72eace896c909f44d0e3898','3052a11f19bb2e3ce39e299c3fac6c54','2daf42b4597d2c81a2291b3053e9d017','3ae2c81b0cb951327cf42aa75005b818','b3098f96d4f79adf4c0a9cf15649ba30','0148cdda99a0f7446235e31d09598463','13464448e8308adca9cd54ad8f4ba42d','df97cd66fbc069dd1d98929c3ede64df','38301c3fef5927b5348bac17b5af8159','30a6e9a34030f4c878055985556f9a75','fa1c9be090560d627826def58bf36a1d','eb691bdf31c8d738be8146c0280ce47c','207a9a4c1f3acea792c6bdbe002727e1','9d3f9064155d12657cebfda6070efa26','c0427fa24ca51b581010ee7847f1dcca','d5e3e622b03e94d4ec944347267d6e83','28f97cd3223e70d5e2250bfdd38fcf15','69aacb62474be43d69b323e10c8f3509','df9c0fe7a92e9f2415538c280e617391','0aa6e023824a7af185fd4c1c41f59e26','10769f6aea985f675916a02817b623ef','b486d2193c3f085abe34c61dc4dd5586','8cd71d70426349b63a24d58d1a0958bf','d6fe101cd644ab6e866ef044fd2023e0','1ef2ffbe73cf1f398bedaa6e0f17276d','098510fc2e8f9c83faf94bcd28258852','32ecac1767ceb89a3392429bd32602f5','98f3daea1384f9c8280e76d1c4a78e92','0c351313478a84a2194a6a5fa9836fd4','f7366d4d7a6977451450bec4bf445feb','3d49729f86dc23aa9af6825b69da06db','3bf8ae384fecd378185a1a0470f05afd','3220fc07e3de58e387c094eec65bf5da','dd93e3ffe15e9df55c52a59975bee671','a26e530c53a826d8e8e5b0a3519ed27e','987008011c62875fccf1a1d3a002c767','ac7920d2a131bd56853c94e091943e6d','4def47a23925191903b1d4af57244ba0','5b89ed0f097e2a162b02fffbea16e959','c672b3b0941a0526afc8eadc4e32db9d','924c3f02b4ef32765fa92365d37611ff','b3b8f18733705c243a87738a83b13994','6e5c8c92bef9576c61fbb72b3887e562','9e7e8eb2f4d1fe1bfa680a25ec79603d','a2d21482a18ff5fa4b029227c460450b','0c54625a5741fb159c87f940b1f2eb9b','2ebe98022309291396381b5026d6c543','3b4788d58b66101c739918d2fb4dffb3','23abe3a7995e456e736a160488636f9a','691a831623da76699345d69b50ec2d1e','0e2aeda33a8cc2496d03fa67381c2fff','bfe818db7eb9dbb0583ee6853e60ad7c','5cc627a1a3c9a428b5f95b7975de9ac0','0138de4df364277e6ef145708b4d4a25','20f1031f87202594ac90fa7bd3c69d39','01bfd8a2f3fa0581f8cc43add004457d','f9cbe3d054894fc26e69be5544f3fc8e','3beb075151dfa49801bdcedbfe9cc48c','610f6ff25fd1b9289728ef5035320a60','e6f63be857d084117f07c448aba6951f','57bbe2cd1f5581352500f25c35e585ee','b93b3bf549db8113095e6c36c010f74c','8d6c958bef2ef5782034ff5cd3039527','9268d15823c2a927b44f4e7b7e054278','e31a8c92209aae3b447d98cf2e193c4b','b197a1b902a2cd42a4d93af6716268d9','b073f0eaf2240e23a8543ed1c9e198a1','51a72b2b4cb49da0429ce359887075d7','01021d03fae24e24bdaa24e245ce0d2a','c6db1f4f6ae79bb1adf4bf6f304930eb','1ac2e4e65c1e2cd461f6136306da7081','f7a26630c8bee4151f35ecb929526eee','5449ca7c030f3784a5081780445a2de4','8a23d817c5e0c55a4f630411be1b0e1d','95d96e99e8f92ec9b4fedecc81ad878e','5eb63a9204c52cc5ea078c280e496ef1','b495e54ec5ee3c0fe3f813563a030571','163936de655b4e58e69f5c80da18e83e','bfd564f6c6032e44e14c3a6f7f65bf15','1e9bbdfc8942009e208b29eca2d041a5','d7ea77b475742950f32a17a2e82c9aba','ef10cf3da01cbe7d52ebf5774dd49c51','664ba3c4ce8e8dc23e77bda9fe4137d7','473922dddee8a727d953fde1eced5cc4','9d7e32de082f20d00f51e946131809e1','3e0dd305954237309a2da365cfec941a','2b5c8d64bce911db01dcfe36d5ef96dd','ac89d3fcf7f13d1fadcf21c5117dbe7f','55468fe5ff6bed1a01d8e8b4b7553c15','00ee83d5df0faa9c952a2709c149b4bf','efa1186000bb98a44d84d64e789e48da','974272d32829e04302cc3745a748ed02','37a6efbaa0fafe28812c567cb8da71fb','5ea433e2bead19e4166c52d229d3b0df','ce7934ae5294ed27f500be567ec32266','711a3d150aaa13ea0eb016f7f45a0455','924ec77ee6baa7f25e3f65a564d549f6','611074336f8dc1a393e86d7ef3d55c2b','b01ff1d6ca27f6005203eedbef7a4be8','39161733328274a371218dcb92ebcecd','4136d5da52f9722ec0379eef951299ea','79fbdd26937b10d72ad908f277ecc934','5072ebaee03dad924370d076ad9b55a6','545e1befda7407f7a5226da2db3c1f7e','3b5980542dd240aec42c048d6f74c1b4','f485265b6a5391c5e889a6ce0b609922','9a4b59262cf6ac5e2d2b00ba3a6807e0','3566fb0383dc0c9beb410c81195df332','b8f0029cc7547f8c9d8fe8b86a6b1f84','ae8b86499e4f2fe2861a22850e09af8e','016cae59d51a08fe0fe6f002f970dca8','ff10a065331a0edd3e975e94ff962fc8','7cca292f2a68d7372f7f8f07266319cd','1d45a14c7e3f95c4ba623f82fa64226f','9bceff05be3d65e911b1ab3ea195a294','43f7ba0857526d53cc3879d9cdb011c0','e4981ede3c1b85a0113014de66a9bf51','b9ea54456970ec572d35fb14d1dce69e','faacea85a49a0e047fc05b7fa0353d3a','58c931276cc05e59d16c6219634c2361','e7c46445c93e11186c86202e19f510bd','ccfe9bf5cc984c2a18f4002cfccb1bf0','1381f9faf9c2d6cab5c4a911483940cd','1d643c19efeed5ba805844631bfc3c8f','1643efd51cc0ae02529a74a1ed6542e4','21f8945c02f8a10df02bd2036c1b2237','30c2ac98ba15c3c0db9647d2d7b29e2e','570cff84e058b4e475273031dbaa019e','78e5bc129bd9cc84d36a78c02ce33b19','95cd43dca26a7a6b4bbecfbef6a128d0','8b78c990473926007886062ef094fcc5','7e97be104c2b34c1b25fcd1d5abbff6b','90932281fc09f135d2dcc8f37b923957','0ce82bbb318e044d7093cccae98330ab','0ff75da7b8b012bc14f33d41baaa256c','7d82c28acf84161c14766a73752546c8','759f2c5d1be8255ac2b3de483eac0b4e','ff6349d9bfd4a49b69741d93b8ec45c0','fbfc5cb0fe740916c9e2660b9b23dd6c','8a409c2ce6bbf3e339f2afd55fb4b044','ae25f2f7a6dd0677bcf021541f7f10e5','6c11163d6aeb1f6c6a9d908581fabec2','df973b95e065f0dc7b59b89a9518a08e','0f08d2a905f288a12d5330b65c96ba40','62cf5205ab5225de6542bbd538820192','5d1ccbeeff0f23ecf821abeb5d91da27','885c2b579fe2102fb837b57b36deb38c','a61b0af6aeb86738d277b723509510dd','e852e262f9e51aea2c92b92150a93c19','1a5989f35bd066883cf51ca40997143a','ff7ccbaed082500ed56fe485564b06a0','418a72ddbce3d414bb48c0c12043786c','7cb9150a31c181131a201a53dbd65d69','94c6f9bf5615204cf5ea8eb7bc228849','43802b1a355a1ae56cfcc7cb1d69c27d','147753840b2c52fb174173631e156cc5','b2613a6b8770bb84e6e1b5b4aff44c78','0ef313415f7ddb9825cc041aee42612e','b07868b7c4e3503c3e47faaa5d435d98','4a70ba10c739c80b4749c4fa7a23e9fb','f1af83f91a569a105f70bd25e50f2ae0','26a2fd9059f1216cd326a41e6783ecc7','831a03a4e34abb5d5890603094b6d904','5ca4a9db00e88e9fb7306eac4f5634d3','e87980ecfe665ba40fc430dd6bd3a3be','c72b2af3a829738b44a62df62044d0ad','0f73786390e3324b51dda71920655cd7','d95614a1e4be556b64f5dd9562812ea9','f084490ad6d643f8e517a6d81002ce67','7d34f573030b3cfdc665100b19ddfd4b','8f7ee1d52aecf32b4a88309cb27176bf','cab5e37c6cc5ebaadf8d8ea9b7ffa2ee','4c40b675897fc3c458651c82f3667e3a','e8c6bc32d4346b12a13872664b14780a','d26763f993a55b88495973031099c7de','adda98b2efe8adf29927b686d376ca0d','a970af2184797867d67172986e525161','151f5539daa712a736ff85998b67027b','2a8219e8c90ea416da4606f812dabbda','d0078c44c0f906c6d5a950fae9360b4b','4e3868fa3d80839b90c00ba070240c3c','1d6c92e2a9c321218ececdfb15c2c06d','db1410ea46de9e5e3dbe4dd6b03b7d3b','389926530db8fc46abbf255045aa6c0f','91b56f7d426d93f97c9dc74a3cd4dacb','7c4b737b074ab939bb827b843426edca','8a820b56f7bc54b4933d3cad5a6f960c','71725e44b8c659364209acfd654674f4','7f2cb39e303408229e4442d702e6765f','5d55cc22731d652e3c59fbfe9c955955','03cf6c58f356d50e63f8e45f73390b0b','447bf3ef9e8a425ac4bb01d83f7f9fc7','cd934560a26cd33c802af460102b70d9','5e7e9128f30e6454c5b15ac37bc96e73','d67318999aed09a23b077ea6f3eeb48f','31596f63bc1cd3b649474b1b8da4cc4d','be58ecc18fb2fe8a09644d661182555d','c44fd110a5b4c2b3ae97d068c08ff43b','092cac82d4ceda73305cb3fb6e9f16cc','a0a8075da45b045c56a81e69fb323c48','f6474beba40ff8fdfbeb1104830cf476','07566543ec8cc540c50a768ab3a41257','e318f9670cdb815950d0d1ae9d1cacef','aaf210d635d28e300712376dfefc81e4','565ce753b1679eb3527265355969d9d5','d1b1651e5add6f76694edd16910e498f','cab250545ac0a7c9b119e5f233cc61b6','c4dec3baa804e50b13238020d20b2b55','9a2a65487156b1ac7a7ec4d92d4ca165','894c8b27fb4288cae4abd36fd732e166','ab966c15f2f2ac8d8942d23809c9d4a1','f18a18d468a22e2c2e61b77518bc6d94','3ba390841941ccb7dae41308ac1467e5','7c780d29177e03a755352fb81d7a7314','c738a12d6f53387b70cde184766d719d','4fc3990cd523e9c66cbf287d6cb669dc','59c29dd3577ede99319b99a49e5d972f','5e9a20c8d19d274093fa3ffa261dc620','a81e07ccaccfee5a53e6edb567b23416','9536074126022a83052d39636b34ff13','823818937bbf6d0f5403bfa02958a00b','be36856b38d3ad5f99a8bc889d3760cd','bc3595f0b5256f33fe481221946926c5','971b04b0837ec0d2b1d6767e49c1cddf','ac340a2538eeafe6e4bf9e1702406abb','721667b2085b877a36dca3345c90e1e5','16e6712ac4c8624c9a202d6e80d5ada5','5bd8cfafa45b441e8adf94145e879445','fd143c6872157266a5d76d618606e94c','b0df2b835dffef44bb7fdae49111ff53','26c80b3cd8b64354618edc2d18b1701d','3e811ca5f41e9e05c01a0a6bbb9d96a1','25f76fb3439802d37757334be67462a1','a93df644eaa997558a4ac19101f5e0c1','f5455c9ad13b8cb0fef4650f25e88200','a22b0ba2146daed443709ade7f2a2c66','4e54da873cfb807337c66e219deabf7c','f33f57af93b3023f233152a74435b55c','a72066cbb8ba2aac7aded433afe2f9d5','676c2960ec5098e7dd7e33030dff3315','4fa5349e08814792b19dbdcffa8d2408','b52e501c19d31ddd82089ddfa4e5c490','b8843ee104cd73f9b55731a47b649a0c','a973a0a23f5cdaa7948603e425a8893e','e4a53fde853ae06f6d53bb3d71099fdb','847b3eee4921121911e06e8760eb6b8d','a886276c72c3f34498e7d5e03b2701eb','a3817c1c8c619d56e5ef89a6c5efddf7','c96a8be2b9c528d1a741f861145496fc','8356f245e0e7ed5b701d74743e94eb1a','883f7156450bf2b495cd566291214d6e','46698ffd0c33d7cf545f3bb1e46213b3','9c679c9e746ffbbda4369aa878b453a8','c6560b8f62571090ad8f2bf35012e601','4ce6b705f129a3eb670084f6ccc8d5c9','3fc0418e8b1f8ef35d903a0be312ac62','561446774b59d537dc648bda4a971188','0728f4eda741c303b370069f39b13dca','560a382d398042ac9664fbcd63364bfe','b5a4d66bccf3169df28237ff78579683','f940a0f4f865bd371f028360a622cea9','ad6dff061a327d804701bf8d009870ef','8ca0824e2809a20e4afbea64e58f92cb','282af0278bf927cf1540e2750e00982b','b41fe6eef518c0a33bdcffa92ebb5c02','2883eb8d35a6d5c62304d57823eb72f4','91ec9fd0e7998841af18e86b8ad4ef1d','5f7d3cd5d8ed2a90a803202a886041f6','6469b2229d5f285300f8bea24d547d2d','faa6810359ac4d4a98add10ce94715fc','0fd3427a69e57b22516cb90307e3830b','d210e031258fc19adc9e29b4c1e959fd','79064f8019f2cba8cb5109fa00f75197','266df6843facb7c0543dfcc50218ac3b','d945fc06661e505ffaf5bbbc54919a8d','c87147a821be3d2a7ccef7ec3465da28','2ef1d496085bbd7fe0a332761e20b22e','daa31fa2cbbf60b5b0d789e330987404','6637d7e06cef8564dbfcb13a21ef3415','0e2fd56e378254258dad0b1ff386307b','1412d6d3408426804966b373c4c4177b','f0aa6761eaa215fb39d702bf7b16f5c0','bb328709351bb211786a3396d1a6afca','79e3f05190a6d6858678d06658c38969','bc5a6a03b31b41c93dac43176e0310b2','6ad0d47430679d287e302e8832b32fc3','76002aa36183f11cb5de2c2fb11cb9c2','57934459582677569ff5f3d86c5ed37d','5b1e9502b853af6cf627c8ae590edfbc','ee0ec5217550bdfd7225b75ef75ffad8','a7db796e730d551d8561b8de7e825e67','72ec561e37e3d8ba590a07f8455aec47','c73e018f9c0e243400466deb353b2c8a','63e1e23ddc80619cde33796f716ce737','98c091d94fe38780c443bceb6856dd62','e67551aa91cb08c55b47cff927224e54','cac1c314ef2341ca1b37b6af861b74d2','5efc7491dc595e4dc99aed7a043046c3','7226f85b62badbbca6c9958e38464b5c','1cfbffc0605ab361ea4c89d01e18bcc3','ec09f7a0d418f340f243be9a87acd876','01afbaff892d5b7e73a12d4d701f42ee','28840a38a862f1a825a28389f06029b0','6721606ffcaee9e4b11c6633608bbd60','59669f4be245c813ae1bbebca9b283d1','368e6a97637e9cf90d37e8cd77db8169','591f397135440d7c29f1ce6320ff9f0f','f32df69a4fd537aaaf74cc6163dab841','ad247c4bad255825c4122ff7e92ae701','16326ac69040de091c1c3c65c0a2f01c','83cbf30dd1fcc3dfdbf5f4b9e3523526','4b0afd1667a12ae9c451f7c6178ac123','b6e4a7c1b0c4463ae08e49b810bca4d0','fbbf81fa092cb7cf50ac66bed20aa954','0e7c138826874922c20e996d56604f92','073d55c591fee37d069268043950aa75','f4523c6f9eab1fafd7a0834c98cfe290','d0fd8bdd75ed0ae2cda2327e81605916','db16b0ce0f1ff1bcd2cd3e6b0dee7053','416bf276b444dcd486bc91927177133f','5f464ac67bcdcbdefdd3bc91b5b03ee8','08fab7ba97a40bdf5e95892ffcd11087','6d64d1ed3d45a037457f13e3f9716d5d','7347d0e4ca39807550bd95d8f7b73d8c','47181b036664486cd86f5a0bac63df7f','82177f18837f7f4919cbc08e1f3b7981','be8504488a9ed0ba69288f40ca14ab94','c94477d58c423c65232318b25940c84a','7da48f50f79381f4af7ecf3e33baf5ab','72ef6f4744144dd816b8bc6e66b83516','0a4c6df6d4d38367cce3e01df3449b54','8ba383974a0c672e46602927e1193ce9','9a94575ea1f43385e3c39e2ecc67b718','53f9015fc88f7bd76752713eb8b4b0fc','2161ccec6eb50cfe56fbaf19ff85bbc0','d37ccb44eb6dfd343fbf858dc4758231','b1abbd14e433bedaa4e9efbe9b507f98','9907a4ec816e9780aa6a886df0fba753','1e3db5f3170528e45c5207a47b0f96a3','21c058f4d03f5aa5103abd0ae36eeddb','35583f4ce8f27a533d777567f14ce571','aa4f6e93ea17816f19e41614995bc607','bb2275813f0a94e146076ef0a0d6fe74','d666239d94ad87af362bd5b9bd6e56df','7a9f1f31ab27362900f8a31ebee7e1de','7e426dec713ae44b37272cc12d2d0e0b','caba6678e40f9f75db707aaade8f65d8','59161eaf0375b33fcfd2811a1069d5a8','d054d29ca52bb8678aa96e280bb243d2','94f74eca7184e7bb03a92c6eebfdcf31','6d74b477406662e6483fe95a094b25dc','54575afc7e242dc7b958b7ca3005da98','5fe7a8c2a55b512da8e3fe4d8191e9cc','401314e19fe54597b49685b81b531d8f','1b1ace8296699032fbfead5261f37e64','4b4cbb6fd5e8a01acda901f04db3ac4a','cfd4a5cc2bd77e89f52e9dee2a184d12','b9d149f003bbe2528a39430a18bf75e9','7860bbc85710878459b9a1721a0cae50','c766205e27af1f1900172795e6b84f9c','6a947fa4eda3e14e95c6b36231472cc9','0950600bfeaaf6054a7e694a0d319632','151a81e8f569ff8c564551f08b49e26b','bbc3736ee5b5c637a7d661a481484369','5a2cd63576d88132605ffc198e035617','6843761714be33edf3035c59e1e90b9f','59752006af0c3a9cedd3adc0a616b8cd','d1c5799b411184ed27c4860040634834','6d7f68e29401c4988fd6b4e78a302f19','b2391f710e180d141081862ad8c547bd','df7a7d1db42877c6ccf14a92865470c4','a2f02c2c141d4ef32c35a07f48ded5f4','7f3b4631940797c7a58a0980490df50e','7cb2f0c6ee908d794e48f7d242708d54','724deda6188a538a5e2107ba55909500','52c769e591ebfedb1fbbe7350fe3d73f','254a6f84f7cb5ca9b83a672582eabb69','f8c993ba5153231a33916fbc40d7b354','daf7d89fa16c5b449e48c2e1f8401570','46dae95ed4a7aba68aac325956e11545','e3f3b3d9dee86959a05f5bb85a4eb5e0','e569e94e6283d2d81941e9977aed79a4','63d55f316846215d23d6883c0e7acc0d','6dd165bc60a17043454c6d65500780c8','f5e3b42dbbc3925560a77d178b6f9830','ca751b9f56ce4572b58283af0f400cdb','69e043a165b1f1399cdedb5e1c2f6f74','2268984f59868b4ea4f879413e7c17ad','13016491ec6ecc51452c2cab8ddb95c0','b925be9ec9e3f9e53a3f8b1fcab27500','6a71b7a558105fd8d8c851ed28f7f15b','b775457f23473662894534b443b2b147','77396d4ad98fdfde5e9ce18a04b2ada5','6263aaf15f0fab5cc6e8640a8b8d62ef','cc3567ec6ba379400006421046887aee','64cd4cb0fcc4e7dd81a3bc081a430c62','a3094101b8e0d661858dcd9bfb692e20','c83afa3103be045955b72db9a0079c9d','5d3dd148dc3a77827133e24b297e81ea','721fc85ac4e2392b75f5ebbc4e7085f8','95b951f31191841a5e31dd0abd7c59fc','a5dbae3935b10eedccc85aa0ce5d8ee1','8e6aa09a15668574f782ff78fed023ca','b5c7d288c7976113a891cc3ca4aac546','de1e846ebcf3b6c26981ae8be3fdd2cd','3db55378c6b0a512601657df5097b276','0fb5e5c16134588b6950fb1db2a9ef06','ec0464ec0a72b8930cb16b1a01f9f512','f28db1187e9e19a760a15bb1f9ffef01','d4d2e5f7d83c6e9b09d7cc858a7294c8','f64dbca479bf97b2f0b983cf4f5493b9','62a92a59cde946b90d26fd91e36bd433','a28d27094ccb2cfd35146997465e8d9b','0f8f7c8b434556a357fc07719516264a','13d19e7bf589e147f5aa67c7d2b8af74','686c3debeffb5188c20a140b2e049772','159b3c82978912a8c93d184e101d17b3','c9340323f9f182ac06cb5d6dc8755653','8c7a4fedbefd52347828e7a8efb63e8a','3abf60ebb171843c224b070bc2cffa20','7a5947f106f25379715dfc48a86f4b88','0af5ca4785e15b267bee7105748fc141','98e080dee23ae2b47a279d5fd9d7e56d','e609795d273c90d9fdb45ee354640742','36aa3eb8ac00d25ce0fdd0c4b69b3e1f','fc557810a272cc744da8d175acb7bdfe','1ed444690cc4e84b175674e46b820ac5','01247580255ed8faa94712bb927c9fd7','ca78ebd14c1466f654650e37c52cba82','4e53ce07ed29585aa0e70c9d46698404','b89653eec38a40045598a1a0f4493ea5','415f861ccf9e6851f60ac851482b5b41','aaf4f1c04ca88012e162877d29d32026','66ae0df7e980845b50495c61b342e5d3','030f7fc9c105eb1c92a3dea6c51f1353','d379de92bb195c414f0cb9e1fdc6c54b','521cb13f6878c2098142f2d97c31e158','d4680085ab488f25d910effc6b209f72','5181714a90260f0f12934f0a2c276daa','ecd82541f5a65314cd4bac8c5748424d','1f236e0d83a8dd9174c460710bc2a1a6','cefaffe31d8d78ade268a9c9b0c7b5e2','e7b4a461183acbbfbc38025930b465e0','d453ded8d0b7bbaeed2a93098f9601aa','2fce3f8caea34cb228cb2c3a88a6ba23','a680c36046068267f8d9f9d236031f65','2ba96498216ab044f08e0f6bb852864e','bc9ca6c744bf47ff5faef8d68572a25b','6011a7db3c709e594a8b02e8e88c9973','671f9120fac1e0d3f698ba3c15cd267d','d0433057eb062add1665e0d7a6fad7a2','b2ef8c6b1eec5319b11096d940d089e4','4287f0fbb71f448db8b7b3bc2e997af8','e8c146c9daa3ac5e7c00737f8de5d1c9','7370657f29c91c7c02938907b6b7e7a9','bcecc7b755b073c3b4b93462bf98e3ce','61ebe3de9fc75cc2a96825f982945b99','8f0cc2a79b7eeb3cf9e2b9c05c169591','abd5cc54d1e00566ad6d76eb85debc18','cc409af2a841d162003973e316428219','563dc6871f999f75ba1b10a13933433d','d5038052542fa58768d95a3880ff8992','ac60ddc9eec5cbfe39e7e3d1e5a02212','c5517c0fa9ec6822a12e457fb396c509','6b445599e7b437c24ca4d962cb75a29e','58c472f925ac0d1276a76f99e711dd98','cf64900179fa1944164eaab2c08102de','923da3424e6b4f11795d394712176671','0c1bbcccfab63219d2b2868c91ca12b5','bd74c566f9f3a5ef01858f40281b8fa6','e7ff41761abf62e4792e809c5df56c16','3467e4de993d49ce4eb81efaf81de9c7','30f35204619cc05b1a393a47634d5196','58baa7946523c6c580fec21b56c94627','00c9d1726a94ca8d9b354a392caabf7b','f220e71d82d466bdad81da511bf9be74','0b15289ebbde6c56b73be319b6ff840e','94ef91ec3416f6e902ea58e42737c9d2','25fb74fdd5cd6f91f37c182af7f5eeec','c40cb959eca3fd1329c13db6113a1441','b3963d512267e74ebb919ae9166ea355','b771719a22f483087cc3af0d8e296c1c','ebd4d002f6da446fecdd2684c9d00828','adeaa78777e1e5d0eb52d32ee797af32','40f231f8605891ec45decd3244aea2ae','662fe8eb2ff60935224876967e309051','45b16364ad25788b87d013f78def3214','35c0b108b04a017e967ab47c8d9c3b9d','60a24b9d5476e069c522cf807fdf59e2','a61d1b259b85c67c278b62fd4237f4a1','9df5370f3a9ad456c07539f50d96c5fa','bf497c7bddc3100d0607e31458132b3f','1e9b671945baea2ba38211c8361829c1','710a1d5aae865b2b3e11d9c231ac4b4e','bf71ac1d5aced64090d4f5b9f3b9493f','3e0510bc16d443a97ded5afe28ea105a','dd82b24e7b27de186a8c29b63b1a453b','80df9d66f5f4c231fed5800af00ce324')";

$db->query($sql);
$c_infos = $db->fetchall("object");

echo "company_id | 택배사 <br/>";

foreach($c_infos as $ci){

	$sql = "select  
			group_concat(code_name) as name
		from
			shop_code where code_gubun='02' and code_ix in ('".str_replace(",","','",$ci[delivery_company])."') ";
	$db->query($sql);
	$db->fetch();
	
	echo $ci[company_id]." | ". $db->dt[name]."<br/>";
}

exit;

$sql="select * from common_company_detail where company_id in ('f7fa4636b6f00a25388c0010bf36dffe','c3f7f0922eee3758b442b64e1603aa45','4bfcb1d25307f69e71cbb97dc5283835','0cbb4f09f1110f661d0d5e4b4ac98b2e','a691a17d143e1e3478d941333c638352','228c521e78e1e3fc372857b478dec32e','4b45a9f0794c3e6262f72671a07bcd30','7096b1b4e481f7abc96094050147b7ce','74979fa51597e6a86c2659c1f558dd76','1754e8b96e604d91867b203cf3c875c4','76b1d6b23e0ca80b0386c70169064e37','ad51cd76275b224b9dfb72fbba2f4510','63760d10e613032f3a859f60d7e1ed86','71ec4a6cd148ff0caa4be2e9d9c9b735','c124a92de09fd57f25f888c5135ab4ad','bd502a6c9be7462432871ba23fdf3673','04d5e5adba5c8368c0c75198a8d71540','2b4252cb3ef3c6ad0544e75ff765bef1','6c782c5a1867738722028417fdbe3444','456ab55e8f35e1553d0c0a551aa6a4b5','18b012c8319010d9e6c1949783f4b5af','836c7873767c9961cd130139c0daff37','a4b9a05b8c58f2ea7cdb19ec625329da','dd19d11e3eb7adc4e2a7f77418203826','10a44ef1b2075b0063850dece2c60e9d','c72e0d088cbfdd30452ca85472739747','c2968d7def3e67b0d596e3669a1f4eb5','881299461b25a84b240b943714237236','1add166848a73c2e54f59f1d4afdd29d','91cc39dbacf5e97b220ceb17eca2abcd','942462bb41ec9c35ecbc82336b5ae526','c26bf49fc35a226e9907962491109844','79beceebf4be4480e97f29827477dcfd','453e81b32dfc199713d69bd9cc9022a7','e5b868f99f467281722c72eef19dfb00','87417ef56aacde46e06ed654896daa16','6936b7d6e555a685bb1d7998ea431752','c3e1d59b02e2a96ff2da23742a16c68c','4eef9aabd424c99fc665d8c8ff225e69','e230bded63f8966132e5887d10a32af3','8c30527cacc502dced70fa400fdf2096','5fb54cd8b926839741511c68ff9551ce','e63306560f691428aa55e3b605b88861','92924ef0d417b95901dff27c15317be2','12ce24ddc188f23d4784b60569f522ae','6d22236c3cdf1ab550084875b9a8e318','438e8fa6a4d62dd8300817c5885a65fd','17dda62d389177fbabf0675f80cf6ad2','eacc47c18f1b0b490c1b6d29208dd1f9','6fb0b520895734711b987af92352cf26','a8757dbdb6ed30133d3d081fdd3136e4','60ade701ebae4776a7aad06e21bbebec','5611d1187a75ae621c7e3ce4c4c6a08f','e626b8575b75617f9bbacb65c6483188','303a398467a8fcefd3fa40da8fd6501d','a32412f4f5292a7f2a0326055e07c572','66def47c7f18ac053fe39c2063943dcc','6cd69e45928d92395fb0e19a4b818210','4f198dbccf5703474927e6ef9ed147df','effda05b12706e507b4074506bddf35d','c356d784f85c3771f3134cfef62e074f','1f6fa9b709731a5f84464a31540ff0f2','6abcf9b8160bded48abea1015d539c20','0c4b1791d5595286e73baab51309ad94','2e0e42a4e177b67b7c01019c863ffc2c','37863095d59256e4b069a88fec238f96','806ebdb678131b81fe2860acbdd636b7','48738f0f96f942fb3bb508dba766caf9','2150d99e258be5880876e46169c39360','9a0badbdc2c40409b2916156855ca6e9','02ebcd8d426dea00ed276802644011f1','aaaa5dba86dff5c269562185c7c19aad','2139da54a1d0f5a73606b7b51aa2395d','2960c835291d7ec6a31d109cfbaa34f5','08080fb57c5541b3f6010f89f3ea7bc5','41f9deb39c43268ecd4f9bc4a7ee857b','36fbf707b7547ae8ae81b8cb12c9907c','6cc0768a14f1b7fc64950ed74e40fe90','7489c3b850b56252be216dc96db57cd8','0b2c9f0fb454091ffb387b8be5a7c376','e28429947962168462c2f6120c53836f','ec9c3e260fac6c1b5fd7088428bb09d4','97e09dd0faddff5cc40534f19f73aad3','2bde883ddca332dab0ad6459e59dc082','a206c7efbf0668f465a5e7d5e2dc9c31','d4fd603d47d00429bd52cf038520ff1c','2c1810c1498a826324fb7334a8dfb95e','c6dba2ed54a6d508cf7e834d345c9695','e35246dae147e071871f14dba58d47af','0976a8f9884e7a08e276d6d875d22a59','b0ea1c87884693615bf90b75dff63854','55c7706a59876ee6e51c70c22bd584c4','4f3e780cfeed8bd4bdd16bbd41099b71','2170f72646ce41e566573b340f161dff','8b491a509d80345a5a1fb3042fe59ad0','585ecc49f72eace896c909f44d0e3898','3052a11f19bb2e3ce39e299c3fac6c54','2daf42b4597d2c81a2291b3053e9d017','3ae2c81b0cb951327cf42aa75005b818','b3098f96d4f79adf4c0a9cf15649ba30','0148cdda99a0f7446235e31d09598463','13464448e8308adca9cd54ad8f4ba42d','df97cd66fbc069dd1d98929c3ede64df','38301c3fef5927b5348bac17b5af8159','30a6e9a34030f4c878055985556f9a75','fa1c9be090560d627826def58bf36a1d','eb691bdf31c8d738be8146c0280ce47c','207a9a4c1f3acea792c6bdbe002727e1','9d3f9064155d12657cebfda6070efa26','c0427fa24ca51b581010ee7847f1dcca','d5e3e622b03e94d4ec944347267d6e83','28f97cd3223e70d5e2250bfdd38fcf15','69aacb62474be43d69b323e10c8f3509','df9c0fe7a92e9f2415538c280e617391','0aa6e023824a7af185fd4c1c41f59e26','10769f6aea985f675916a02817b623ef','b486d2193c3f085abe34c61dc4dd5586','8cd71d70426349b63a24d58d1a0958bf','d6fe101cd644ab6e866ef044fd2023e0','1ef2ffbe73cf1f398bedaa6e0f17276d','098510fc2e8f9c83faf94bcd28258852','32ecac1767ceb89a3392429bd32602f5','98f3daea1384f9c8280e76d1c4a78e92','0c351313478a84a2194a6a5fa9836fd4','f7366d4d7a6977451450bec4bf445feb','3d49729f86dc23aa9af6825b69da06db','3bf8ae384fecd378185a1a0470f05afd','3220fc07e3de58e387c094eec65bf5da','dd93e3ffe15e9df55c52a59975bee671','a26e530c53a826d8e8e5b0a3519ed27e','987008011c62875fccf1a1d3a002c767','ac7920d2a131bd56853c94e091943e6d','4def47a23925191903b1d4af57244ba0','5b89ed0f097e2a162b02fffbea16e959','c672b3b0941a0526afc8eadc4e32db9d','924c3f02b4ef32765fa92365d37611ff','b3b8f18733705c243a87738a83b13994','6e5c8c92bef9576c61fbb72b3887e562','9e7e8eb2f4d1fe1bfa680a25ec79603d','a2d21482a18ff5fa4b029227c460450b','0c54625a5741fb159c87f940b1f2eb9b','2ebe98022309291396381b5026d6c543','3b4788d58b66101c739918d2fb4dffb3','23abe3a7995e456e736a160488636f9a','691a831623da76699345d69b50ec2d1e','0e2aeda33a8cc2496d03fa67381c2fff','bfe818db7eb9dbb0583ee6853e60ad7c','5cc627a1a3c9a428b5f95b7975de9ac0','0138de4df364277e6ef145708b4d4a25','20f1031f87202594ac90fa7bd3c69d39','01bfd8a2f3fa0581f8cc43add004457d','f9cbe3d054894fc26e69be5544f3fc8e','3beb075151dfa49801bdcedbfe9cc48c','610f6ff25fd1b9289728ef5035320a60','e6f63be857d084117f07c448aba6951f','57bbe2cd1f5581352500f25c35e585ee','b93b3bf549db8113095e6c36c010f74c','8d6c958bef2ef5782034ff5cd3039527','9268d15823c2a927b44f4e7b7e054278','e31a8c92209aae3b447d98cf2e193c4b','b197a1b902a2cd42a4d93af6716268d9','b073f0eaf2240e23a8543ed1c9e198a1','51a72b2b4cb49da0429ce359887075d7','01021d03fae24e24bdaa24e245ce0d2a','c6db1f4f6ae79bb1adf4bf6f304930eb','1ac2e4e65c1e2cd461f6136306da7081','f7a26630c8bee4151f35ecb929526eee','5449ca7c030f3784a5081780445a2de4','8a23d817c5e0c55a4f630411be1b0e1d','95d96e99e8f92ec9b4fedecc81ad878e','5eb63a9204c52cc5ea078c280e496ef1','b495e54ec5ee3c0fe3f813563a030571','163936de655b4e58e69f5c80da18e83e','bfd564f6c6032e44e14c3a6f7f65bf15','1e9bbdfc8942009e208b29eca2d041a5','d7ea77b475742950f32a17a2e82c9aba','ef10cf3da01cbe7d52ebf5774dd49c51','664ba3c4ce8e8dc23e77bda9fe4137d7','473922dddee8a727d953fde1eced5cc4','9d7e32de082f20d00f51e946131809e1','3e0dd305954237309a2da365cfec941a','2b5c8d64bce911db01dcfe36d5ef96dd','ac89d3fcf7f13d1fadcf21c5117dbe7f','55468fe5ff6bed1a01d8e8b4b7553c15','00ee83d5df0faa9c952a2709c149b4bf','efa1186000bb98a44d84d64e789e48da','974272d32829e04302cc3745a748ed02','37a6efbaa0fafe28812c567cb8da71fb','5ea433e2bead19e4166c52d229d3b0df','ce7934ae5294ed27f500be567ec32266','711a3d150aaa13ea0eb016f7f45a0455','924ec77ee6baa7f25e3f65a564d549f6','611074336f8dc1a393e86d7ef3d55c2b','b01ff1d6ca27f6005203eedbef7a4be8','39161733328274a371218dcb92ebcecd','4136d5da52f9722ec0379eef951299ea','79fbdd26937b10d72ad908f277ecc934','5072ebaee03dad924370d076ad9b55a6','545e1befda7407f7a5226da2db3c1f7e','3b5980542dd240aec42c048d6f74c1b4','f485265b6a5391c5e889a6ce0b609922','9a4b59262cf6ac5e2d2b00ba3a6807e0','3566fb0383dc0c9beb410c81195df332','b8f0029cc7547f8c9d8fe8b86a6b1f84','ae8b86499e4f2fe2861a22850e09af8e','016cae59d51a08fe0fe6f002f970dca8','ff10a065331a0edd3e975e94ff962fc8','7cca292f2a68d7372f7f8f07266319cd','1d45a14c7e3f95c4ba623f82fa64226f','9bceff05be3d65e911b1ab3ea195a294','43f7ba0857526d53cc3879d9cdb011c0','e4981ede3c1b85a0113014de66a9bf51','b9ea54456970ec572d35fb14d1dce69e','faacea85a49a0e047fc05b7fa0353d3a','58c931276cc05e59d16c6219634c2361','e7c46445c93e11186c86202e19f510bd','ccfe9bf5cc984c2a18f4002cfccb1bf0','1381f9faf9c2d6cab5c4a911483940cd','1d643c19efeed5ba805844631bfc3c8f','1643efd51cc0ae02529a74a1ed6542e4','21f8945c02f8a10df02bd2036c1b2237','30c2ac98ba15c3c0db9647d2d7b29e2e','570cff84e058b4e475273031dbaa019e','78e5bc129bd9cc84d36a78c02ce33b19','95cd43dca26a7a6b4bbecfbef6a128d0','8b78c990473926007886062ef094fcc5','7e97be104c2b34c1b25fcd1d5abbff6b','90932281fc09f135d2dcc8f37b923957','0ce82bbb318e044d7093cccae98330ab','0ff75da7b8b012bc14f33d41baaa256c','7d82c28acf84161c14766a73752546c8','759f2c5d1be8255ac2b3de483eac0b4e','ff6349d9bfd4a49b69741d93b8ec45c0','fbfc5cb0fe740916c9e2660b9b23dd6c','8a409c2ce6bbf3e339f2afd55fb4b044','ae25f2f7a6dd0677bcf021541f7f10e5','6c11163d6aeb1f6c6a9d908581fabec2','df973b95e065f0dc7b59b89a9518a08e','0f08d2a905f288a12d5330b65c96ba40','62cf5205ab5225de6542bbd538820192','5d1ccbeeff0f23ecf821abeb5d91da27','885c2b579fe2102fb837b57b36deb38c','a61b0af6aeb86738d277b723509510dd','e852e262f9e51aea2c92b92150a93c19','1a5989f35bd066883cf51ca40997143a','ff7ccbaed082500ed56fe485564b06a0','418a72ddbce3d414bb48c0c12043786c','7cb9150a31c181131a201a53dbd65d69','94c6f9bf5615204cf5ea8eb7bc228849','43802b1a355a1ae56cfcc7cb1d69c27d','147753840b2c52fb174173631e156cc5','b2613a6b8770bb84e6e1b5b4aff44c78','0ef313415f7ddb9825cc041aee42612e','b07868b7c4e3503c3e47faaa5d435d98','4a70ba10c739c80b4749c4fa7a23e9fb','f1af83f91a569a105f70bd25e50f2ae0','26a2fd9059f1216cd326a41e6783ecc7','831a03a4e34abb5d5890603094b6d904','5ca4a9db00e88e9fb7306eac4f5634d3','e87980ecfe665ba40fc430dd6bd3a3be','c72b2af3a829738b44a62df62044d0ad','0f73786390e3324b51dda71920655cd7','d95614a1e4be556b64f5dd9562812ea9','f084490ad6d643f8e517a6d81002ce67','7d34f573030b3cfdc665100b19ddfd4b','8f7ee1d52aecf32b4a88309cb27176bf','cab5e37c6cc5ebaadf8d8ea9b7ffa2ee','4c40b675897fc3c458651c82f3667e3a','e8c6bc32d4346b12a13872664b14780a','d26763f993a55b88495973031099c7de','adda98b2efe8adf29927b686d376ca0d','a970af2184797867d67172986e525161','151f5539daa712a736ff85998b67027b','2a8219e8c90ea416da4606f812dabbda','d0078c44c0f906c6d5a950fae9360b4b','4e3868fa3d80839b90c00ba070240c3c','1d6c92e2a9c321218ececdfb15c2c06d','db1410ea46de9e5e3dbe4dd6b03b7d3b','389926530db8fc46abbf255045aa6c0f','91b56f7d426d93f97c9dc74a3cd4dacb','7c4b737b074ab939bb827b843426edca','8a820b56f7bc54b4933d3cad5a6f960c','71725e44b8c659364209acfd654674f4','7f2cb39e303408229e4442d702e6765f','5d55cc22731d652e3c59fbfe9c955955','03cf6c58f356d50e63f8e45f73390b0b','447bf3ef9e8a425ac4bb01d83f7f9fc7','cd934560a26cd33c802af460102b70d9','5e7e9128f30e6454c5b15ac37bc96e73','d67318999aed09a23b077ea6f3eeb48f','31596f63bc1cd3b649474b1b8da4cc4d','be58ecc18fb2fe8a09644d661182555d','c44fd110a5b4c2b3ae97d068c08ff43b','092cac82d4ceda73305cb3fb6e9f16cc','a0a8075da45b045c56a81e69fb323c48','f6474beba40ff8fdfbeb1104830cf476','07566543ec8cc540c50a768ab3a41257','e318f9670cdb815950d0d1ae9d1cacef','aaf210d635d28e300712376dfefc81e4','565ce753b1679eb3527265355969d9d5','d1b1651e5add6f76694edd16910e498f','cab250545ac0a7c9b119e5f233cc61b6','c4dec3baa804e50b13238020d20b2b55','9a2a65487156b1ac7a7ec4d92d4ca165','894c8b27fb4288cae4abd36fd732e166','ab966c15f2f2ac8d8942d23809c9d4a1','f18a18d468a22e2c2e61b77518bc6d94','3ba390841941ccb7dae41308ac1467e5','7c780d29177e03a755352fb81d7a7314','c738a12d6f53387b70cde184766d719d','4fc3990cd523e9c66cbf287d6cb669dc','59c29dd3577ede99319b99a49e5d972f','5e9a20c8d19d274093fa3ffa261dc620','a81e07ccaccfee5a53e6edb567b23416','9536074126022a83052d39636b34ff13','823818937bbf6d0f5403bfa02958a00b','be36856b38d3ad5f99a8bc889d3760cd','bc3595f0b5256f33fe481221946926c5','971b04b0837ec0d2b1d6767e49c1cddf','ac340a2538eeafe6e4bf9e1702406abb','721667b2085b877a36dca3345c90e1e5','16e6712ac4c8624c9a202d6e80d5ada5','5bd8cfafa45b441e8adf94145e879445','fd143c6872157266a5d76d618606e94c','b0df2b835dffef44bb7fdae49111ff53','26c80b3cd8b64354618edc2d18b1701d','3e811ca5f41e9e05c01a0a6bbb9d96a1','25f76fb3439802d37757334be67462a1','a93df644eaa997558a4ac19101f5e0c1','f5455c9ad13b8cb0fef4650f25e88200','a22b0ba2146daed443709ade7f2a2c66','4e54da873cfb807337c66e219deabf7c','f33f57af93b3023f233152a74435b55c','a72066cbb8ba2aac7aded433afe2f9d5','676c2960ec5098e7dd7e33030dff3315','4fa5349e08814792b19dbdcffa8d2408','b52e501c19d31ddd82089ddfa4e5c490','b8843ee104cd73f9b55731a47b649a0c','a973a0a23f5cdaa7948603e425a8893e','e4a53fde853ae06f6d53bb3d71099fdb','847b3eee4921121911e06e8760eb6b8d','a886276c72c3f34498e7d5e03b2701eb','a3817c1c8c619d56e5ef89a6c5efddf7','c96a8be2b9c528d1a741f861145496fc','8356f245e0e7ed5b701d74743e94eb1a','883f7156450bf2b495cd566291214d6e','46698ffd0c33d7cf545f3bb1e46213b3','9c679c9e746ffbbda4369aa878b453a8','c6560b8f62571090ad8f2bf35012e601','4ce6b705f129a3eb670084f6ccc8d5c9','3fc0418e8b1f8ef35d903a0be312ac62','561446774b59d537dc648bda4a971188','0728f4eda741c303b370069f39b13dca','560a382d398042ac9664fbcd63364bfe','b5a4d66bccf3169df28237ff78579683','f940a0f4f865bd371f028360a622cea9','ad6dff061a327d804701bf8d009870ef','8ca0824e2809a20e4afbea64e58f92cb','282af0278bf927cf1540e2750e00982b','b41fe6eef518c0a33bdcffa92ebb5c02','2883eb8d35a6d5c62304d57823eb72f4','91ec9fd0e7998841af18e86b8ad4ef1d','5f7d3cd5d8ed2a90a803202a886041f6','6469b2229d5f285300f8bea24d547d2d','faa6810359ac4d4a98add10ce94715fc','0fd3427a69e57b22516cb90307e3830b','d210e031258fc19adc9e29b4c1e959fd','79064f8019f2cba8cb5109fa00f75197','266df6843facb7c0543dfcc50218ac3b','d945fc06661e505ffaf5bbbc54919a8d','c87147a821be3d2a7ccef7ec3465da28','2ef1d496085bbd7fe0a332761e20b22e','daa31fa2cbbf60b5b0d789e330987404','6637d7e06cef8564dbfcb13a21ef3415','0e2fd56e378254258dad0b1ff386307b','1412d6d3408426804966b373c4c4177b','f0aa6761eaa215fb39d702bf7b16f5c0','bb328709351bb211786a3396d1a6afca','79e3f05190a6d6858678d06658c38969','bc5a6a03b31b41c93dac43176e0310b2','6ad0d47430679d287e302e8832b32fc3','76002aa36183f11cb5de2c2fb11cb9c2','57934459582677569ff5f3d86c5ed37d','5b1e9502b853af6cf627c8ae590edfbc','ee0ec5217550bdfd7225b75ef75ffad8','a7db796e730d551d8561b8de7e825e67','72ec561e37e3d8ba590a07f8455aec47','c73e018f9c0e243400466deb353b2c8a','63e1e23ddc80619cde33796f716ce737','98c091d94fe38780c443bceb6856dd62','e67551aa91cb08c55b47cff927224e54','cac1c314ef2341ca1b37b6af861b74d2','5efc7491dc595e4dc99aed7a043046c3','7226f85b62badbbca6c9958e38464b5c','1cfbffc0605ab361ea4c89d01e18bcc3','ec09f7a0d418f340f243be9a87acd876','01afbaff892d5b7e73a12d4d701f42ee','28840a38a862f1a825a28389f06029b0','6721606ffcaee9e4b11c6633608bbd60','59669f4be245c813ae1bbebca9b283d1','368e6a97637e9cf90d37e8cd77db8169','591f397135440d7c29f1ce6320ff9f0f','f32df69a4fd537aaaf74cc6163dab841','ad247c4bad255825c4122ff7e92ae701','16326ac69040de091c1c3c65c0a2f01c','83cbf30dd1fcc3dfdbf5f4b9e3523526','4b0afd1667a12ae9c451f7c6178ac123','b6e4a7c1b0c4463ae08e49b810bca4d0','fbbf81fa092cb7cf50ac66bed20aa954','0e7c138826874922c20e996d56604f92','073d55c591fee37d069268043950aa75','f4523c6f9eab1fafd7a0834c98cfe290','d0fd8bdd75ed0ae2cda2327e81605916','db16b0ce0f1ff1bcd2cd3e6b0dee7053','416bf276b444dcd486bc91927177133f','5f464ac67bcdcbdefdd3bc91b5b03ee8','08fab7ba97a40bdf5e95892ffcd11087','6d64d1ed3d45a037457f13e3f9716d5d','7347d0e4ca39807550bd95d8f7b73d8c','47181b036664486cd86f5a0bac63df7f','82177f18837f7f4919cbc08e1f3b7981','be8504488a9ed0ba69288f40ca14ab94','c94477d58c423c65232318b25940c84a','7da48f50f79381f4af7ecf3e33baf5ab','72ef6f4744144dd816b8bc6e66b83516','0a4c6df6d4d38367cce3e01df3449b54','8ba383974a0c672e46602927e1193ce9','9a94575ea1f43385e3c39e2ecc67b718','53f9015fc88f7bd76752713eb8b4b0fc','2161ccec6eb50cfe56fbaf19ff85bbc0','d37ccb44eb6dfd343fbf858dc4758231','b1abbd14e433bedaa4e9efbe9b507f98','9907a4ec816e9780aa6a886df0fba753','1e3db5f3170528e45c5207a47b0f96a3','21c058f4d03f5aa5103abd0ae36eeddb','35583f4ce8f27a533d777567f14ce571','aa4f6e93ea17816f19e41614995bc607','bb2275813f0a94e146076ef0a0d6fe74','d666239d94ad87af362bd5b9bd6e56df','7a9f1f31ab27362900f8a31ebee7e1de','7e426dec713ae44b37272cc12d2d0e0b','caba6678e40f9f75db707aaade8f65d8','59161eaf0375b33fcfd2811a1069d5a8','d054d29ca52bb8678aa96e280bb243d2','94f74eca7184e7bb03a92c6eebfdcf31','6d74b477406662e6483fe95a094b25dc','54575afc7e242dc7b958b7ca3005da98','5fe7a8c2a55b512da8e3fe4d8191e9cc','401314e19fe54597b49685b81b531d8f','1b1ace8296699032fbfead5261f37e64','4b4cbb6fd5e8a01acda901f04db3ac4a','cfd4a5cc2bd77e89f52e9dee2a184d12','b9d149f003bbe2528a39430a18bf75e9','7860bbc85710878459b9a1721a0cae50','c766205e27af1f1900172795e6b84f9c','6a947fa4eda3e14e95c6b36231472cc9','0950600bfeaaf6054a7e694a0d319632','151a81e8f569ff8c564551f08b49e26b','bbc3736ee5b5c637a7d661a481484369','5a2cd63576d88132605ffc198e035617','6843761714be33edf3035c59e1e90b9f','59752006af0c3a9cedd3adc0a616b8cd','d1c5799b411184ed27c4860040634834','6d7f68e29401c4988fd6b4e78a302f19','b2391f710e180d141081862ad8c547bd','df7a7d1db42877c6ccf14a92865470c4','a2f02c2c141d4ef32c35a07f48ded5f4','7f3b4631940797c7a58a0980490df50e','7cb2f0c6ee908d794e48f7d242708d54','724deda6188a538a5e2107ba55909500','52c769e591ebfedb1fbbe7350fe3d73f','254a6f84f7cb5ca9b83a672582eabb69','f8c993ba5153231a33916fbc40d7b354','daf7d89fa16c5b449e48c2e1f8401570','46dae95ed4a7aba68aac325956e11545','e3f3b3d9dee86959a05f5bb85a4eb5e0','e569e94e6283d2d81941e9977aed79a4','63d55f316846215d23d6883c0e7acc0d','6dd165bc60a17043454c6d65500780c8','f5e3b42dbbc3925560a77d178b6f9830','ca751b9f56ce4572b58283af0f400cdb','69e043a165b1f1399cdedb5e1c2f6f74','2268984f59868b4ea4f879413e7c17ad','13016491ec6ecc51452c2cab8ddb95c0','b925be9ec9e3f9e53a3f8b1fcab27500','6a71b7a558105fd8d8c851ed28f7f15b','b775457f23473662894534b443b2b147','77396d4ad98fdfde5e9ce18a04b2ada5','6263aaf15f0fab5cc6e8640a8b8d62ef','cc3567ec6ba379400006421046887aee','64cd4cb0fcc4e7dd81a3bc081a430c62','a3094101b8e0d661858dcd9bfb692e20','c83afa3103be045955b72db9a0079c9d','5d3dd148dc3a77827133e24b297e81ea','721fc85ac4e2392b75f5ebbc4e7085f8','95b951f31191841a5e31dd0abd7c59fc','a5dbae3935b10eedccc85aa0ce5d8ee1','8e6aa09a15668574f782ff78fed023ca','b5c7d288c7976113a891cc3ca4aac546','de1e846ebcf3b6c26981ae8be3fdd2cd','3db55378c6b0a512601657df5097b276','0fb5e5c16134588b6950fb1db2a9ef06','ec0464ec0a72b8930cb16b1a01f9f512','f28db1187e9e19a760a15bb1f9ffef01','d4d2e5f7d83c6e9b09d7cc858a7294c8','f64dbca479bf97b2f0b983cf4f5493b9','62a92a59cde946b90d26fd91e36bd433','a28d27094ccb2cfd35146997465e8d9b','0f8f7c8b434556a357fc07719516264a','13d19e7bf589e147f5aa67c7d2b8af74','686c3debeffb5188c20a140b2e049772','159b3c82978912a8c93d184e101d17b3','c9340323f9f182ac06cb5d6dc8755653','8c7a4fedbefd52347828e7a8efb63e8a','3abf60ebb171843c224b070bc2cffa20','7a5947f106f25379715dfc48a86f4b88','0af5ca4785e15b267bee7105748fc141','98e080dee23ae2b47a279d5fd9d7e56d','e609795d273c90d9fdb45ee354640742','36aa3eb8ac00d25ce0fdd0c4b69b3e1f','fc557810a272cc744da8d175acb7bdfe','1ed444690cc4e84b175674e46b820ac5','01247580255ed8faa94712bb927c9fd7','ca78ebd14c1466f654650e37c52cba82','4e53ce07ed29585aa0e70c9d46698404','b89653eec38a40045598a1a0f4493ea5','415f861ccf9e6851f60ac851482b5b41','aaf4f1c04ca88012e162877d29d32026','66ae0df7e980845b50495c61b342e5d3','030f7fc9c105eb1c92a3dea6c51f1353','d379de92bb195c414f0cb9e1fdc6c54b','521cb13f6878c2098142f2d97c31e158','d4680085ab488f25d910effc6b209f72','5181714a90260f0f12934f0a2c276daa','ecd82541f5a65314cd4bac8c5748424d','1f236e0d83a8dd9174c460710bc2a1a6','cefaffe31d8d78ade268a9c9b0c7b5e2','e7b4a461183acbbfbc38025930b465e0','d453ded8d0b7bbaeed2a93098f9601aa','2fce3f8caea34cb228cb2c3a88a6ba23','a680c36046068267f8d9f9d236031f65','2ba96498216ab044f08e0f6bb852864e','bc9ca6c744bf47ff5faef8d68572a25b','6011a7db3c709e594a8b02e8e88c9973','671f9120fac1e0d3f698ba3c15cd267d','d0433057eb062add1665e0d7a6fad7a2','b2ef8c6b1eec5319b11096d940d089e4','4287f0fbb71f448db8b7b3bc2e997af8','e8c146c9daa3ac5e7c00737f8de5d1c9','7370657f29c91c7c02938907b6b7e7a9','bcecc7b755b073c3b4b93462bf98e3ce','61ebe3de9fc75cc2a96825f982945b99','8f0cc2a79b7eeb3cf9e2b9c05c169591','abd5cc54d1e00566ad6d76eb85debc18','cc409af2a841d162003973e316428219','563dc6871f999f75ba1b10a13933433d','d5038052542fa58768d95a3880ff8992','ac60ddc9eec5cbfe39e7e3d1e5a02212','c5517c0fa9ec6822a12e457fb396c509','6b445599e7b437c24ca4d962cb75a29e','58c472f925ac0d1276a76f99e711dd98','cf64900179fa1944164eaab2c08102de','923da3424e6b4f11795d394712176671','0c1bbcccfab63219d2b2868c91ca12b5','bd74c566f9f3a5ef01858f40281b8fa6','e7ff41761abf62e4792e809c5df56c16','3467e4de993d49ce4eb81efaf81de9c7','30f35204619cc05b1a393a47634d5196','58baa7946523c6c580fec21b56c94627','00c9d1726a94ca8d9b354a392caabf7b','f220e71d82d466bdad81da511bf9be74','0b15289ebbde6c56b73be319b6ff840e','94ef91ec3416f6e902ea58e42737c9d2','25fb74fdd5cd6f91f37c182af7f5eeec','c40cb959eca3fd1329c13db6113a1441','b3963d512267e74ebb919ae9166ea355','b771719a22f483087cc3af0d8e296c1c','ebd4d002f6da446fecdd2684c9d00828','adeaa78777e1e5d0eb52d32ee797af32','40f231f8605891ec45decd3244aea2ae','662fe8eb2ff60935224876967e309051','45b16364ad25788b87d013f78def3214','35c0b108b04a017e967ab47c8d9c3b9d','60a24b9d5476e069c522cf807fdf59e2','a61d1b259b85c67c278b62fd4237f4a1','9df5370f3a9ad456c07539f50d96c5fa','bf497c7bddc3100d0607e31458132b3f','1e9b671945baea2ba38211c8361829c1','710a1d5aae865b2b3e11d9c231ac4b4e','bf71ac1d5aced64090d4f5b9f3b9493f','3e0510bc16d443a97ded5afe28ea105a','dd82b24e7b27de186a8c29b63b1a453b','80df9d66f5f4c231fed5800af00ce324')";

$db->query($sql);
$c_infos = $db->fetchall("object");

echo "company_id | 배송정책 | 반품지 주소 | 반품지우편번호  | CS 연락처1 | CS 연락처2 <br/>";

foreach($c_infos as $ci){

	$sql = "select 
			*
		from
			shop_delivery_template as dt 
			inner join common_company_detail as ccd on (dt.company_id = ccd.company_id)
		where
			dt.company_id = '".$ci[company_id]."' and is_basic_template='1' and product_sell_type='R'
			order by dt.regdate DESC";
	$db->query($sql);
	$db->fetch();

	switch($db->dt[delivery_basic_policy]){
		case '1':
			$delivery_basic_policy = '선불';
			break;
		case '5':
			$delivery_basic_policy = '선불/착불 선택';
			break;
		case '2':
			$delivery_basic_policy = '착불';
			break;
	}

	switch($db->dt[delivery_package]){
		case 'N':
			$delivery_package = '묶음배송';
			break;
		case 'Y':
			$delivery_package = '개별배송';
			break;
	}
	

	switch($db->dt[delivery_policy]){
		case '1':
			$template_text = "조건 배송비 (".$delivery_package.") : 무료";
			break;
		case '2':
			$template_text = "조건 배송비 (".$delivery_package.") : 고정배송비 ".number_format($db->dt[delivery_price])." 원";
			break;
		case '3':
			$sql = "select * from shop_delivery_terms where dt_ix = '".$db->dt[dt_ix]."' and delivery_policy_type = '3' order by seq ASC limit 0,1";
			$mdb->query($sql);
			$mdb->fetch();
			$template_text = "조건 배송비 (".$delivery_package.") : 주문결제금액 할인 / 주문금액 ".number_format($mdb->dt[delivery_basic_terms])." 원 미만일경우 ".number_format($mdb->dt[delivery_price])." 원";
			break;
		case '4':
			$sql = "select * from shop_delivery_terms where dt_ix = '".$db->dt[dt_ix]."'  and delivery_policy_type = '4' order by seq ASC limit 0,1";
			$mdb->query($sql);
			$mdb->fetch();
			$template_text = "조건 배송비 (".$delivery_package.") : 수량별 할인 / 기본배송비 ".number_format($db->dt[delivery_cnt_price])." 원 ".number_format($mdb->dt[delivery_price])." 개 이상시 ".number_format($mdb->dt[delivery_basic_terms])." 원 배송비 적용";
			break;
		case '5':
			$sql = "select * from shop_delivery_address where addr_ix = '".$db->dt[factory_info_addr_ix]."'";
			$mdb->query($sql);
			$mdb->fetch();

			$template_text = "조건 배송비 (".$delivery_package.") : 출고지별 배송비 ( ".$mdb->dt[addr_name]." )";
			break;
		case '6':
			$template_text = "조건 배송비 (".$delivery_package.") : 상품 1개단위 배송비 ".number_format($db->dt[delivery_unit_price])." 원";
			break;
	}
	
	$sql = "select
					*
				from
					shop_delivery_address
				where
					delivery_type = 'E'
					and company_id = '".$ci[company_id]."'";
	$mdb->query($sql);
	$mdb->fetch();
	

	
	echo $ci[company_id]." | ". $delivery_basic_policy." ". $template_text ." | ".$mdb->dt[address_1]." ".$mdb->dt[address_2]." | ".$mdb->dt[zip_code]." | ".$mdb->dt[addr_phone]." | ".$mdb->dt[addr_mobile]."<br/>";
}


exit;

//-- 정산 할인 이슈로 인해서 shop_order_detail_discount 할인금액(본사) -> 할인금액(셀러) 
/*


SELECT * 
FROM  `shop_order_detail_discount` 
WHERE  
	`dc_type` =  'GP' 
and
	dc_ix in (select pg.dpg_ix from shop_discount d , shop_discount_product_group pg where d.dc_ix=pg.dc_ix and d.discount_type='SP')



UPDATE  
	`shop_order_detail_discount` 
SET
	dc_type='SP',
	dc_rate_seller = dc_rate_admin,
	dc_price_seller = dc_price_admin,
	dc_rate_admin = 0,
	dc_price_admin = 0
WHERE  
	`dc_type` =  'GP' 
and
	dc_ix in (select pg.dpg_ix from shop_discount d , shop_discount_product_group pg where d.dc_ix=pg.dc_ix and d.discount_type='SP')

*/
exit;
//real_lack_stock 초기 정보 update
$sql="select gu_ix from shop_order_detail  where status in ('IR','IC','DR','DD') and stock_use_yn='Y' and gu_ix!='0' group by gu_ix ";
$db->query($sql);
$order_infos = $db->fetchall("object");
for($i=0;$i < count($order_infos);$i++){

	$sql = "select sum(ps.stock) as stock
				from inventory_goods_unit gu  left join inventory_product_stockinfo ps on ( ps.unit = gu.unit and ps.gid=gu.gid)
				where gu.gu_ix = '".$order_infos[$i][gu_ix]."' ";
	$mdb->query($sql);
	$mdb->fetch();

	$item_stock_sum = $mdb->dt[stock];

	$sql="select od_ix, pcnt from shop_order_detail  where gu_ix = '".$order_infos[$i][gu_ix]."' and status in ('IR','IC','DR','DD') order by regdate asc";
		$mdb->query($sql);
		if($mdb->total){
			$od_info = $mdb->fetchall("object");

			$real_lack_stock = $item_stock_sum;

			for($j=0;$j<count($od_info);$j++){
				$real_lack_stock -= $od_info[$j][pcnt];
				$sql="update shop_order_detail set real_lack_stock='".$real_lack_stock."' where od_ix='".$od_info[$j][od_ix]."' ";
				echo $sql."<br/>";
				$mdb->query($sql);
			}
		}
}


exit;


//상품 delivery_price 수정하기!
$db->query("select id , delivery_price from shop_product where state in ('0','1') and disp='1' and is_delete='0' order by id desc  ");
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){

	$product_basic_delivery_price = PorudctBasicDeliveryPrice($val[id]);
	
	if($val[delivery_price] != $product_basic_delivery_price){
		$sql = "update ".TBL_SHOP_PRODUCT." set delivery_price = '".$product_basic_delivery_price."' , editdate = NOW()  where id ='".$val[id]."' ";
		$db->query($sql);
	}
	
}

exit;





//1.이미 환불 완료된건들!!
$db->query("select oid from shop_order_detail where refund_status in ('FC')  group by oid ");
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){
	
	$db->query("select sum(refund_delivery_price) as refund_delivery_price from shop_order_delivery where oid='".$val[oid]."' ");
	$db->fetch();

	$refund_delivery_price = $db->dt[refund_delivery_price];

	
	$db->query("select oid,company_id,delivery_type,claim_group,ode_ix,fa_date from shop_order_detail where refund_status in ('FC') and oid='".$val[oid]."' group by ode_ix ");
	$od_array=$db->fetchall("object");

	foreach($od_array as $od){
		
		$db->query("select delivery_price from shop_order_delivery where ode_ix='".$od[ode_ix]."' ");
		$db->fetch();

		$delivery_price = $db->dt[delivery_price];
		
		$refund_delivery_price -= $delivery_price;

		$r_price = $refund_delivery_price;

		if($r_price < 0){
			echo $od[oid] . "~~~~" . $r_price."<br/>";
		}else{
			$r_delivery_price = $delivery_price;

			$sql="insert into shop_order_claim_delivery(ocde_ix,oid,company_id,delivery_type,claim_group,delivery_price,ac_target_yn,regdate) values('','".$od[oid]."','".$od[company_id]."','".$od[delivery_type]."','".$od[claim_group]."','".$r_delivery_price."','Y','".$od[fa_date]."')";
			echo $sql."<br/>";
			$db->query($sql);
		}
	}
}

echo  "<br/><br/>~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/><br/>";
exit;




//재고 매칭되어있는 상품들 수량과 selling_cnt 처리하기!
$db->query("select gu.gu_ix, gu.sell_ing_cnt, ifnull(sum(ips.stock),0) as stock ,ips.gid from inventory_goods_unit gu left join inventory_product_stockinfo ips on (gu.gid=ips.gid and gu.unit=ips.unit) group by gu.gu_ix order by gu_ix limit 30000,5000");
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){

	$sql = "select od.id as opnd_ix ,pid from ".TBL_SHOP_PRODUCT." p inner join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od on (p.id=od.pid) where p.stock_use_yn='Y' and option_code = '".$val[gu_ix]."' ";
	$db->query($sql);
	if($db->total){
		$option_dt_info = $db->fetchall();
		for($j=0;$j<count($option_dt_info);$j++){
			$sql="update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_sell_ing_cnt = '".$val[sell_ing_cnt]."' , option_stock = '".$val[stock]."' where id = '".$option_dt_info[$j][opnd_ix]."' ";
			echo $sql."<br/>";
			$db->query($sql);
		}
		
		$sql="update ".TBL_SHOP_PRODUCT." p set
			p.sell_ing_cnt = (select sum(option_sell_ing_cnt) from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od where od.pid=p.id),
			p.stock = (select sum(option_stock) from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od where od.pid=p.id)
		where p.pcode ='".$val[gu_ix]."' and p.stock_use_yn='Y' ";
		echo $sql."<br/>";
		$db->query($sql);
	}

	$sql = "update ".TBL_SHOP_PRODUCT." set sell_ing_cnt = '".$val[sell_ing_cnt]."' , stock = '".$val[stock]."'  where pcode ='".$val[gu_ix]."' and stock_use_yn='Y' ";
	echo $sql."<br/>";
	$db->query($sql);

	echo "gu_ix : ". $val[gu_ix]."<br/>";
}


exit;

exit;

//재고 히스토리 보관창고 지정 안되서 마이그레이션 진행하기!!

$sql="select h.h_ix,hd.gid,hd.unit, (case when h_div='2' then -amount else amount end) as cnt from inventory_history h left join inventory_history_detail hd on ( h.h_ix=hd.h_ix) where h.ps_ix='0'";
$db->query($sql);
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){
	/*
	$sql="select bp.company_id, bp.pi_ix , bp.ps_ix , ccd.com_name, pi.place_name, ps.section_name
		from 
			inventory_goods_basic_place bp 
			left join common_company_detail ccd on (bp.company_id = ccd.company_id)
			left join inventory_place_info pi on (bp.pi_ix = pi.pi_ix)
			left join inventory_place_section ps on (bp.ps_ix = ps.ps_ix)

		where bp.gid='".$val[gid]."' and bp.unit='".$val[unit]."' and bp.pi_ix='1' ";
	$db->query($sql);
	*/
	if($db->total || true){
		//$db->fetch();

		//$sql="update inventory_history set com_name='".$db->dt[com_name]."',place_name='".$db->dt[place_name]."',section_name='".$db->dt[section_name]."',company_id='".$db->dt[company_id]."',pi_ix='".$db->dt[pi_ix]."',ps_ix='".$db->dt[ps_ix]."',msg='ps_ix 0 데이터 기본보관창고로 수정' where h_ix='".$val[h_ix]."' ";

		$sql="update inventory_history set com_name='(주)한웰이쇼핑',place_name='남사물류센터',section_name='입고 보관장소',company_id='362ed8ee1cba4cc34f80aa5529d2fbcd',pi_ix='1',ps_ix='1',msg='ps_ix 0 데이터 기본보관창고로 수정' where h_ix='".$val[h_ix]."' ";
		
		echo $sql."<br/>";
		$mdb->query($sql);

		$sql="select psi_ix from inventory_product_stockinfo where ps_ix='1' and gid='".$val[gid]."' and unit='".$val[unit]."' ";
		$mdb->query($sql);
		
		if($mdb->total){
			$mdb->fetch();
			
			if($val[cnt] > 0){
				$total_in_stock=$val[cnt];
				$total_out_stock=0;
			}else{
				$total_out_stock=$val[cnt]*-1;
				$total_in_stock=0;
			}

			$sql="update inventory_product_stockinfo set stock = stock + '".$val[cnt]."' , total_in_stock = total_in_stock + '".$total_in_stock."' , total_out_stock = total_out_stock + '".$total_out_stock."' where psi_ix ='".$mdb->dt[psi_ix]."' ";
			echo $sql."<br/>";
			$mdb->query($sql);

		}else{
			
			if($val[cnt] > 0){
				$first_stock=$val[cnt];
				$total_in_stock=$val[cnt];
				$total_out_stock=0;
			}else{
				$first_stock=$val[cnt];
				$total_out_stock=$val[cnt]*-1;
				$total_in_stock=0;
			}

			$sql = "insert into inventory_product_stockinfo
			(psi_ix,vdate, expiry_date, company_id,pi_ix,ps_ix, gid,unit,stock_pcode,stock,exit_order,first_stock,total_in_stock,total_out_stock,regdate)
			values
			('','".date("Ymd")."','','362ed8ee1cba4cc34f80aa5529d2fbcd','1','1','".$val[gid]."','".$val[unit]."','','".$val[cnt]."','1','".$first_stock."','".$total_in_stock."','".$total_out_stock."',NOW()) ";

			echo $sql."<br/>";
			$mdb->query($sql);

		}
	}else{
		echo "기본 보관창고 없음 " . $val[gid] . "~~" . $val[unit]."<br/>";
	}

}

exit;


//재고 inventory_product_stockinfo  보관창고 지정 안되서 마이그레이션 진행하기!!

$sql="select * from inventory_product_stockinfo where ps_ix='0' ";
$db->query($sql);
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){
	
	$sql="select psi_ix from inventory_product_stockinfo where ps_ix='1' and gid='".$val[gid]."' and unit='".$val[unit]."' ";
	$mdb->query($sql);
	
	if($mdb->total){
		$mdb->fetch();
		
		if($val[stock] > 0){
			$total_in_stock=$val[stock];
			$total_out_stock=0;
		}else{
			$total_out_stock=$val[stock]*-1;
			$total_in_stock=0;
		}

		$sql="update inventory_product_stockinfo set stock = stock + '".$val[stock]."' , total_in_stock = total_in_stock + '".$total_in_stock."' , total_out_stock = total_out_stock + '".$total_out_stock."' where psi_ix ='".$mdb->dt[psi_ix]."' ";
		echo $sql."<br/>";
		$mdb->query($sql);

	}else{
		
		if($val[stock] > 0){
			$first_stock=$val[stock];
			$total_in_stock=$val[stock];
			$total_out_stock=0;
		}else{
			$first_stock=$val[stock];
			$total_out_stock=$val[stock]*-1;
			$total_in_stock=0;
		}

		$sql = "insert into inventory_product_stockinfo
			(psi_ix,vdate, expiry_date, company_id,pi_ix,ps_ix, gid,unit,stock_pcode,stock,exit_order,first_stock,total_in_stock,total_out_stock,regdate)
			values
			('','".date("Ymd")."','','362ed8ee1cba4cc34f80aa5529d2fbcd','1','1','".$val[gid]."','".$val[unit]."','','".$val[stock]."','1','".$first_stock."','".$total_in_stock."','".$total_out_stock."',NOW()) ";

		echo $sql."<br/>";
		$mdb->query($sql);

	}

	$sql = "delete from  inventory_product_stockinfo where psi_ix='".$val[psi_ix]."' ";
	echo $sql."<br/>";
	$mdb->query($sql);

}

exit;

//재고 차감 안된 내용들 처리하기!!!
echo "<pre>";
$sql="select * , date_format(di_date,'%Y%m%d') as vdate from shop_order_detail where stock_use_yn='Y' and di_date is not null and delivery_basic_ps_ix='2' and date_format(di_date,'%Y%m%d') in ('20140806','20140807') ";
$db->query($sql);
$order_details = $db->fetchall("object");

for($i=0;$i < count($order_details);$i++){

	$sql = "select * from inventory_history h, inventory_history_detail hd , inventory_goods_unit gu where h.h_ix=hd.h_ix and hd.gid=gu.gid and hd.unit=gu.unit  and h.h_div='2' and h.h_type='01' and h.oid='".$order_details[$i][oid]."' and gu.gu_ix='".$order_details[$i][gu_ix]."' ";
	$db->query($sql);

	if(!$db->total){

		$sql = "select pi.pi_ix, pi.company_id, pi.place_name, ps.ps_ix, ps.section_name
				from	inventory_place_section ps
				left join inventory_place_info pi on pi.pi_ix = ps.pi_ix
				where ps.ps_ix = '".$order_details[$i]["delivery_basic_ps_ix"]."'";

		$db->query($sql);
		$db->fetch();
		$order_item_info = $db->dt;

		$sql = "select g.gid, gu.unit, g.standard,
		'".$order_details[$i][pcnt]."' as amount ,
		'".$order_details[$i][psprice]."' as price ,
		'".$order_details[$i][pt_dcprice]."' as pt_dcprice ,
		'".$order_item_info[company_id]."' as company_id,
		'".$order_item_info[pi_ix]."' as pi_ix,
		'".$order_item_info[ps_ix]."' as ps_ix
		from inventory_goods g , inventory_goods_unit gu
		where g.gid = gu.gid and gu.gu_ix = '".$order_details[$i][gu_ix]."'";
		// 출고가격을 어떻게 처리 할지?
		// 한꺼번에 여러개를 묶음으로 처리하는데 출고가 ...
		$db->query($sql);
		$delivery_iteminfo = $db->fetchall("object");

		if($db->total){
			$item_info[pi_ix] = $order_item_info[pi_ix];
			$item_info[ps_ix] = $order_item_info[ps_ix];
			$item_info[company_id] = $order_item_info[company_id];
			$item_info[h_div] = "2"; // 2: 출고
			//$item_info[vdate] = date("Ymd");
			$item_info[vdate] = $order_details[$i][vdate];
			//$item_info[ci_ix] = $_POST["ci_ix"];
			$item_info[oid] = $order_details[$i][oid];
			$item_info[msg] = "상품판매 - 출고";//$_POST["etc"];
			$item_info[h_type] = '01';//01; 상품매출
			$item_info[charger_name] = $_SESSION[admininfo]["charger"];
			$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
			$item_info[detail] = $delivery_iteminfo;

			print_r($item_info)."<br/><br/>";
			UpdateGoodsItemStockInfo($item_info, $db);

			UpdateProductCnt_complete($order_details[$i]);
		}else{
			echo "gu_ix 존재하지 않음 " . $order_details[$i][oid] . "~~" . $order_details[$i][gu_ix]."<br/>";
		}

	}else{
		echo "출고됨 " . $order_details[$i][oid] . "~~" . $order_details[$i][gu_ix]."<br/>";
	}
}

exit;
//order_cnt 보정

$db->query("update `shop_product` set order_cnt ='0' where order_cnt > 0 ");

$db->query("select sum(pcnt) as order_cnt, pid from shop_order_detail  where pid !='' and status not in ('SR') group by pid ");
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){

	$db->query("update ".TBL_SHOP_PRODUCT." set order_cnt = '".($val[order_cnt])."' where id ='".$val[pid]."' ");

}
exit;

//shop_order_detail_discount 데이터 마이그레이션
$sql="select * from shop_order_detail_discount where (dc_price_seller + dc_price_admin) != dc_price ";
$db->query($sql);
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){
	
	if($val[dc_price_seller] > 0 && $val[dc_price_admin]==0){
		$sql="update shop_order_detail_discount set dc_price_seller='".$val[dc_price]."' where oid='".$val[oid]."' and od_ix='".$val[od_ix]."' ";
		echo $sql."<br/>";
		$db->query($sql);

	}elseif($val[dc_price_seller]== 0 && $val[dc_price_admin] > 0){
		$sql="update shop_order_detail_discount set dc_price_admin='".$val[dc_price]."' where oid='".$val[oid]."' and od_ix='".$val[od_ix]."' ";
		echo $sql."<br/>";
		$db->query($sql);
	}

}

exit;

//product 재고 수량 마추기!
$sql="select id,pcode from shop_product where stock_use_yn ='Y' ";
$db->query($sql);
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){
	
	if($val[pcode]!=""){
		$sql = "select gu.gu_ix, ifnull(sum(ps.stock),'0') as stock
			from inventory_goods_unit gu  left join inventory_product_stockinfo ps on ps.unit = gu.unit and ps.gid= gu.gid
			where gu.gu_ix = '".$val[pcode]."' ";
		$mdb->query($sql);
		$mdb->fetch();
		
		$sql="update shop_product set stock='".($mdb->dt[stock])."' where id='".$val[id]."' ";
		//echo $sql."<br/>";
		$db->query($sql);

	}else{
		$sql="select id,option_code from shop_product_options_detail where pid ='".$val[id]."' ";
		$db->query($sql);
		$options=$db->fetchall("object");
		if(count($options) > 0){
			foreach($options as $o_key => $o_val){
				if($o_val[option_code]!=""){
					$sql = "select gu.gu_ix, ifnull(sum(ps.stock),'0') as stock
						from inventory_goods_unit gu  left join inventory_product_stockinfo ps on ps.unit = gu.unit and ps.gid= gu.gid
						where gu.gu_ix = '".$o_val[option_code]."' ";
					$mdb->query($sql);
					$mdb->fetch();
					
					$sql="update shop_product_options_detail set option_stock='".($mdb->dt[stock])."' where id='".$o_val[id]."' ";
					//echo $sql."<br/>";
					$db->query($sql);
				}
			}
			$sql="update shop_product set stock=(select ifnull(sum(option_stock),'0') as stock from shop_product_options_detail where pid='".$val[id]."') where id='".$val[id]."' ";
			//echo $sql."<br/>";
			$db->query($sql);
		}
	}

}




exit;

//WMS 재고관련 상품 이슈 있는데이터 원복!
$sql="select gid,gu_ix,pid,option_id from shop_order_detail od where stock_use_yn ='' and gu_ix='' and company_id in ('362ed8ee1cba4cc34f80aa5529d2fbcd','c72e0d088cbfdd30452ca85472739747') and date_format(regdate,'%Y%m%d') > '20140726' group by pid,option_id ";
$db->query($sql);
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){
	
	$gu_ix="";

	$sql="select pcode from shop_product where id='".$val[pid]."' and stock_use_yn='Y' ";
	$db->query($sql);

	if($db->total){
		$db->fetch();

		$gu_ix = $db->dt[pcode];


		if($val[option_id]){

			$sql="select option_code from shop_product_options_detail where id='".$val[option_id]."' ";
			$db->query($sql);
			if($db->total){
				$db->fetch();
				$gu_ix = $db->dt[option_code];
			}
		}
		
		if($gu_ix){
			$sql="select gid,gu_ix from inventory_goods_unit where gu_ix='".$gu_ix."' ";
			$db->query($sql);
			if($db->total){
				$db->fetch();

				$sql="update shop_order_detail set gid='".$db->dt[gid]."' , gu_ix='".$db->dt[gu_ix]."' , stock_use_yn='Y'  where pid='".$val[pid]."' and option_id='".$val[option_id]."' and stock_use_yn ='' and gu_ix='' and company_id in ('362ed8ee1cba4cc34f80aa5529d2fbcd','c72e0d088cbfdd30452ca85472739747') and date_format(regdate,'%Y%m%d') > '20140726' ";
				$db->query($sql);
			}
		}
	}
}


$sql="select pid from shop_order_detail od where stock_use_yn ='' and date_format(regdate,'%Y%m%d') > '20140726' group by pid ";
$db->query($sql);
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){

	$sql="select stock_use_yn from shop_product where id='".$val[pid]."' ";
	$db->query($sql);

	if($db->total){
		$db->fetch();
		
		$sql="update shop_order_detail set stock_use_yn='".$db->dt[stock_use_yn]."'  where pid='".$val[pid]."' and stock_use_yn ='' and date_format(regdate,'%Y%m%d') > '20140726' ";
		//echo $sql."<br/>";
		$db->query($sql);
	}
}

exit;



// pqyment ic_date update
//select * from shop_order_payment where pay_type='G' and pay_status='IC' and ic_date='0000-00-00 00:00:00'

$db->query("select opay_ix , (select od.ic_date from shop_order_detail od where od.oid=op.oid limit 0,1) as ic_date from shop_order_payment op where pay_type='G' and pay_status='IC' and ic_date='0000-00-00 00:00:00' ");
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){
	
	//echo "update shop_order_payment set ic_date = '".$val[ic_date]."' where opay_ix ='".$val[opay_ix]."' <br/>";
	$db->query("update shop_order_payment set ic_date = '".$val[ic_date]."' where opay_ix ='".$val[opay_ix]."' ");

}
exit;







$info[] = array ("gid"=>"99098993000","1_c"=>"1500","1_w"=>"1500","1_s"=>"2000","5_c"=>"18000","5_w"=>"18000","5_s"=>"24000");


foreach($info as $key => $val){

	$sql="select * from inventory_goods_unit where gid='".$val[gid]."' and unit ='1' and (buying_price !='".$val["1_c"]."' OR wholesale_price !='".$val["1_w"]."' OR sellprice !='".$val["1_s"]."' )";
	$db->query($sql);
	if($db->total){
		$gu_dt = $db->fetch();
		echo " GID : ".$val[gid].", 단위 : EA , 공급가(DB : ".$gu_dt[buying_price]." , EX : ".$val["1_c"]."), 도매가(DB : ".$gu_dt[wholesale_price]." , EX : ".$val["1_w"].") , 소매가(DB : ".$gu_dt[sellprice]." , EX : ".$val["1_s"].") <br/>  " ;
	}

	$sql="select * from inventory_goods_unit where gid='".$val[gid]."' and unit ='5' and (buying_price !='".$val["5_c"]."' OR wholesale_price !='".$val["5_w"]."' OR sellprice !='".$val["5_s"]."' )";
	$db->query($sql);
	if($db->total){
		$gu_dt = $db->fetch();
		echo " GID : ".$val[gid].", 단위 : BOX , 공급가(DB : ".$gu_dt[buying_price]." , EX : ".$val["5_c"]."), 도매가(DB : ".$gu_dt[wholesale_price]." , EX : ".$val["5_w"].") , 소매가(DB : ".$gu_dt[sellprice]." , EX : ".$val["5_s"].") <br/>  " ;
	}

}


exit;







$info[] = array ("gid"=>"4799000","stock"=>"1");


foreach($info as $key => $val){
	$sql="select * from inventory_product_stockinfo where gid='".$val[gid]."' and unit='1' and company_id='362ed8ee1cba4cc34f80aa5529d2fbcd' and pi_ix='1' and ps_ix='1' ";
	$db->query($sql);
	if(!$db->total){

		$regist_infos["act_from"] = "inventory";
		$regist_infos[pi_ix] = "1";
		$regist_infos[company_id] = "362ed8ee1cba4cc34f80aa5529d2fbcd";

		$sql="select * from inventory_goods_basic_place where gid='".$val[gid]."' and unit='1' and company_id='362ed8ee1cba4cc34f80aa5529d2fbcd' and pi_ix='1' ";
		$db->query($sql);
		if($db->total){
			$db->fetch();
			$ps_ix = $db->dt[ps_ix];
			$regist_infos[ps_ix] = $ps_ix;
		}else{
			$regist_infos[ps_ix] = "1";
		}

		$regist_infos[h_div] = "1";
		$regist_infos[vdate] = date("Ymd");
		$regist_infos[ioid] = "1".substr(date("YmdHis"),1)."-".rand(10000, 99999);
		$regist_infos[msg] = "마이그레이션";
		$regist_infos[h_type] = "FC";
		$regist_infos[charger_name] = "forbiz";
		$regist_infos[charger_ix] = "";

		$sql="select g.gid,g.standard,g.item_account,gu.unit,g.surtax_div,gu.buying_price from inventory_goods g , inventory_goods_unit gu where g.gid=gu.gid and g.gid='".$val[gid]."' and gu.unit='1' ";
		$db->query($sql);
		//echo $sql."<br/>";
		if($db->total){
			$db->fetch();

			$item_infos[0][gid]=$db->dt[gid];
			$item_infos[0][standard]=$db->dt[standard];
			$item_infos[0][item_account]=$db->dt[item_account];
			$item_infos[0][unit]=$db->dt[unit];
			$item_infos[0][surtax_div]=$db->dt[surtax_div];
			$item_infos[0][amount]=$val[stock];
			$item_infos[0][price]=$db->dt[buying_price];

			$regist_infos[detail] = $item_infos;
			
			UpdateGoodsItemStockInfo($regist_infos, $db);
			echo "gid : ".$val[gid].", stock : ".$val[stock]." 성공<br/>";
		}else{
			echo "gid : ".$val[gid].", stock : ".$val[stock]." 실패<br/>";
		}
	}else{
		echo "gid : ".$val[gid].", stock : ".$val[stock]." 기등록<br/>";
	}
}

exit;


$info[] = array ("gid"=>"M940120600","section"=>"RF0021");



foreach($info as $key => $val){
	if($val["section"]!=""){
		$db->query("SELECT * FROM inventory_goods WHERE gid='".$val[gid]."' or gcode='".$val[gid]."' ");
		if($db->total){
			$goods=$db->fetch();

			$db->query("SELECT p.company_id,s.pi_ix,s.ps_ix FROM inventory_place_info p, inventory_place_section s WHERE p.pi_ix = s.pi_ix and s.section_name='".$val[section]."' ");
			if($db->total){
				$section=$db->fetch();

				$db->query("SELECT * FROM inventory_goods_unit WHERE gid='".$goods[gid]."' and unit='1' ");
				if($db->total){
					$unit=$db->fetchall("object");

					for($i=0;$i<count($unit);$i++){

						$db->query("select * from inventory_goods_basic_place where gid='".$goods[gid]."' and unit='".$unit[$i][unit]."' and pi_ix='".$section[pi_ix]."' ");

						if($db->total){
							$db->fetch();
							$gbp_ix = $db->dt[gbp_ix];
							$db->query("update inventory_goods_basic_place set ps_ix='".$section[ps_ix]."' where gbp_ix ='".$gbp_ix."' ");
						}else{
							$db->query("insert into inventory_goods_basic_place(gbp_ix,gid,unit,gu_ix,company_id,pi_ix,ps_ix,editdate,regdate) values('','".$goods[gid]."','".$unit[$i][unit]."','".$unit[$i][gu_ix]."','".$section[company_id]."','".$section[pi_ix]."','".$section[ps_ix]."',NOW(),NOW())");
						}

						echo $val[gid]."|".$val[section]."|성공|<br/>";
					}
				}else{
					echo $val[gid]."|".$val[section]."|실패|단위정보가 존재 X <br/>";
				}
			}else{
				echo $val[gid]."|".$val[section]."|실패|로케이션정보가 존재 X.<br/>";
			}
		}else{
			echo $val[gid]."|".$val[section]."|실패|품목코드 존재 X<br/>";
		}
	}else{
		echo $val[gid]."|".$val[section]."|실패|로케이션명 없음<br/>";
	}
}


exit;





//-------------------------------------------------------------------------------------------------------------------------


$db->query("SELECT od.oid, od.od_ix, od.ode_ix, od2.ode_ix as ode_ix2
FROM shop_order_detail od left join shop_order_delivery od2 on (

od.oid =od2.oid
and od.delivery_type = od2.delivery_type
and od.delivery_package = od2.delivery_package
and od.delivery_policy = od2.delivery_policy
and od.delivery_method = od2.delivery_method
and od.delivery_pay_method = od2.delivery_pay_type
and od.delivery_addr_use = od2.delivery_addr_use
and od.factory_info_addr_ix = od2.factory_info_addr_ix
and od.ori_company_id = od2.ori_company_id

)
WHERE 
 od.ode_ix != od2.ode_ix
 ");
$id_array=$db->fetchall("object");


foreach($id_array as $key => $val){
	
	$sql="update shop_order_detail set 
			ode_ix = '".$val[ode_ix2]."'
		where
			od_ix='".$val[od_ix]."' ";
	$db->query($sql);
	//echo $sql."<br/><br/>";
	
}



exit;

$db->query("select * from shop_order_detail where ode_ix = 0");
$id_array=$db->fetchall("object");


foreach($id_array as $key => $val){

	$sql="SELECT * from shop_order_delivery where 
		oid='".$val[oid]."'
        and delivery_type='".$val[delivery_type]."'
        and delivery_package='".$val[delivery_package]."'
        and delivery_policy='".$val[delivery_policy]."'
        and delivery_method='".$val[delivery_method]."'
        and delivery_pay_type='".$val[delivery_pay_method]."'
        and delivery_addr_use='".$val[delivery_addr_use]."'
        and factory_info_addr_ix='".$val[factory_info_addr_ix]."'
        and ori_company_id='".$val[ori_company_id]."' ";
	$db->query($sql);
	if($db->total){
		$db->fetch();

		$sql="update shop_order_detail set 
				ode_ix = '".$db->dt[ode_ix]."'
			where
				oid='".$db->dt[oid]."'
				and delivery_type = '".$db->dt[delivery_type]."' 
				and delivery_package = '".$db->dt[delivery_package]."'
				and delivery_policy = '".$db->dt[delivery_policy]."'
				and delivery_method = '".$db->dt[delivery_method]."'
				and delivery_pay_method = '".$db->dt[delivery_pay_type]."'
				and delivery_addr_use = '".$db->dt[delivery_addr_use]."'
				and factory_info_addr_ix = '".$db->dt[factory_info_addr_ix]."'
				and ori_company_id = '".$db->dt[ori_company_id]."'";
		$db->query($sql);
		echo $sql."<br/><br/>";
	}
}

exit;

//sell_ing_cnt

$db->query("update `shop_product` set sell_ing_cnt ='0' where sell_ing_cnt > 0");
$db->query("update `shop_product_options_detail` set option_sell_ing_cnt ='0' where option_sell_ing_cnt > 0");
$db->query("update inventory_goods_unit set sell_ing_cnt ='0' where sell_ing_cnt > 0");

$db->query("select gu_ix, sum(pcnt) as sell_ing_cnt from shop_order_detail  where status in ('IR','IC','DR','DD') and gu_ix!=0 group by gu_ix ");
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){

	$sql = "select od.id as opnd_ix ,pid from ".TBL_SHOP_PRODUCT." p inner join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." od on (p.id=od.pid) where p.stock_use_yn='Y' and option_code = '".$val[gu_ix]."' ";
	$db->query($sql);
	if($db->total){
		$option_dt_info = $db->fetchall();
		for($j=0;$j<count($option_dt_info);$j++){
			$db->query("update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_sell_ing_cnt = '".$val[sell_ing_cnt]."' where id = '".$option_dt_info[$j][opnd_ix]."' ");
			echo "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set option_sell_ing_cnt = '".$val[sell_ing_cnt]."' where id = '".$option_dt_info[$j][opnd_ix]."' <br/>";
		}
	}
	
	$db->query("update ".TBL_SHOP_PRODUCT." set sell_ing_cnt = '".$val[sell_ing_cnt]."' where pcode ='".$val[gu_ix]."' and stock_use_yn='Y' ");
	echo "update ".TBL_SHOP_PRODUCT." set sell_ing_cnt = '".$val[sell_ing_cnt]."' where pcode ='".$val[gu_ix]."' and stock_use_yn='Y' <br/>";

	echo "update inventory_goods_unit set sell_ing_cnt = '".$val[sell_ing_cnt]."' where gu_ix = '".$val[gu_ix]."'  <br/>";
	$db->query("update inventory_goods_unit set sell_ing_cnt = '".$val[sell_ing_cnt]."' where gu_ix = '".$val[gu_ix]."' ");
}

exit;

$sql="SELECT gu_ix , pi_ix
FROM `inventory_goods_basic_place` 
GROUP BY gu_ix, pi_ix
HAVING count( * ) >1";
$db->query($sql);
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){

	$sql="SELECT gbp_ix from inventory_goods_basic_place where gu_ix ='".$val[gu_ix]."' and pi_ix='".$val[pi_ix]."' order by regdate asc";
	$db->query($sql);
	$db->fetch();
	$gbp_ix = $db->dt[gbp_ix];

	$sql="delete from  inventory_goods_basic_place where gbp_ix='".$gbp_ix."' ";
	$db->query($sql);
	//echo $sql."~~".$key."<br/>";
}


exit;

$sql="SELECT opd.pid FROM shop_product_options po, shop_product_options_detail opd 
WHERE po.opn_ix = opd.opn_ix 
and po.option_kind = 'b'
and option_price = 0 group by opd.pid";
$db->query($sql);
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){

	$sql="update shop_product set disp='0', editdate=NOW() where id='".$val[pid]."' ";
	$db->query($sql);
	echo $sql."~~".$key."<br/>";
}

exit;

$sql="SELECT od.id
FROM shop_product p , shop_product_options_detail od, inventory_goods_unit gu
WHERE p.id = od.pid and od.option_code = gu.gu_ix
AND p.admin != '362ed8ee1cba4cc34f80aa5529d2fbcd'";
$db->query($sql);
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){

	$sql="select * from shop_product_options_detail_20140726 where id='".$val[id]."' ";
	$db->query($sql);
	$db->fetch();

	$option_wholesale_listprice = $db->dt[option_wholesale_listprice];
	$option_listprice = $db->dt[option_listprice];
	$option_price = $db->dt[option_price];
	$option_wholesale_price = $db->dt[option_wholesale_price];

	$sql="update shop_product_options_detail set 
			option_wholesale_listprice = '".$option_wholesale_listprice."',
			option_listprice = '".$option_listprice."',
			option_price = '".$option_price."',
			option_wholesale_price = '".$option_wholesale_price."'
		where id='".$val[id]."' ";
	//$db->query($sql);
	echo $sql."<br/>";

	//if($key==10) exit;
}

exit;


$sql="SELECT p.id
FROM shop_product p, inventory_goods_unit gu
WHERE p.pcode = gu.gu_ix
AND p.admin != '362ed8ee1cba4cc34f80aa5529d2fbcd'
and gu.sellprice=p.listprice ";
$db->query($sql);
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){

	$sql="select * from shop_product_history where id='".$val[id]."' order by history_date asc";
	$db->query($sql);
	$db->fetch();
	$coprice = $db->dt[coprice];
	$wholesale_price = $db->dt[wholesale_price];
	$wholesale_sellprice = $db->dt[wholesale_sellprice];
	$listprice = $db->dt[listprice];
	$sellprice = $db->dt[sellprice];

	$sql="update shop_product set coprice='".$coprice."', wholesale_price='".$wholesale_price."', wholesale_sellprice='".$wholesale_sellprice."', listprice='".$listprice."',sellprice='".$sellprice."', editdate=NOW() where id='".$val[id]."' ";
	//$db->query($sql);
	echo $sql."<br/>";

	//if($key==10) exit;
}

exit;


$info[] = array ("gid"=>"M120800800","stock"=>"1");
$info[] = array ("gid"=>"M120800300","stock"=>"44");
$info[] = array ("gid"=>"M120800200","stock"=>"3");
$info[] = array ("gid"=>"M120800400","stock"=>"24");
$info[] = array ("gid"=>"M201301500","stock"=>"10");
$info[] = array ("gid"=>"M120800500","stock"=>"61");
$info[] = array ("gid"=>"M201301600","stock"=>"21");
$info[] = array ("gid"=>"M201300900","stock"=>"31");
$info[] = array ("gid"=>"M120112600","stock"=>"545");
$info[] = array ("gid"=>"M120112800","stock"=>"817");
$info[] = array ("gid"=>"M201302700","stock"=>"1");
$info[] = array ("gid"=>"M201301700","stock"=>"9");
$info[] = array ("gid"=>"M201301000","stock"=>"24");
$info[] = array ("gid"=>"M201300800","stock"=>"19");
$info[] = array ("gid"=>"M201300700","stock"=>"23");
$info[] = array ("gid"=>"M201301300","stock"=>"21");
$info[] = array ("gid"=>"M201302100","stock"=>"21");
$info[] = array ("gid"=>"M201302300","stock"=>"54");


foreach($info as $key => $val){
	
	$sql="select * from inventory_product_stockinfo where gid='".$val[gid]."' and unit='1' and company_id='362ed8ee1cba4cc34f80aa5529d2fbcd' and pi_ix='1' and ps_ix='1' ";
	$db->query($sql);
	if(!$db->total){
		$regist_infos["act_from"] = "inventory";
		$regist_infos[pi_ix] = "1";
		$regist_infos[company_id] = "362ed8ee1cba4cc34f80aa5529d2fbcd";
		$regist_infos[ps_ix] = "1";
		$regist_infos[h_div] = "1";
		$regist_infos[vdate] = date("Ymd");
		$regist_infos[ioid] = "1".substr(date("YmdHis"),1)."-".rand(10000, 99999);
		$regist_infos[msg] = "마이그레이션";
		$regist_infos[h_type] = "FC";
		$regist_infos[charger_name] = "forbiz";
		$regist_infos[charger_ix] = "";

		$sql="select g.gid,g.standard,g.item_account,gu.unit,g.surtax_div,gu.buying_price from inventory_goods g , inventory_goods_unit gu where g.gid=gu.gid and g.gid='".$val[gid]."' and gu.unit='1' ";
		$db->query($sql);
		echo $sql."<br/>";
		if($db->total){
			$db->fetch();

			$item_infos[0][gid]=$db->dt[gid];
			$item_infos[0][standard]=$db->dt[standard];
			$item_infos[0][item_account]=$db->dt[item_account];
			$item_infos[0][unit]=$db->dt[unit];
			$item_infos[0][surtax_div]=$db->dt[surtax_div];
			$item_infos[0][amount]=$val[stock];
			$item_infos[0][price]=$db->dt[buying_price];

			$regist_infos[detail] = $item_infos;
			
			UpdateGoodsItemStockInfo($regist_infos, $db);
			echo "gid : ".$val[gid].", stock : ".$val[stock]." 성공<br/>";
		}else{
			echo "gid : ".$val[gid].", stock : ".$val[stock]." 실패<br/>";
		}
	}else{
		echo "gid : ".$val[gid].", stock : ".$val[stock]." 기등록<br/>";
	}
}

exit;

$info[] = array ("gid"=>"M11001500","section"=>"C0203");

foreach($info as $key => $val){
	if($val["section"]!=""){
		$db->query("SELECT * FROM inventory_goods WHERE gid='".$val[gid]."' or gcode='".$val[gid]."' ");
		if($db->total){
			$goods=$db->fetch();

			$db->query("SELECT p.company_id,s.pi_ix,s.ps_ix FROM inventory_place_info p, inventory_place_section s WHERE p.pi_ix = s.pi_ix and s.section_name='".$val[section]."' ");
			if($db->total){
				$section=$db->fetch();

				$db->query("SELECT * FROM inventory_goods_unit WHERE gid='".$goods[gid]."' ");
				if($db->total){
					$unit=$db->fetchall("object");

					for($i=0;$i<count($unit);$i++){

						$db->query("select * from inventory_goods_basic_place where gid='".$goods[gid]."' and unit='".$unit[$i][unit]."' and ps_ix='".$section[ps_ix]."' ");
						if(!$db->total){
							//echo "insert into inventory_goods_basic_place(gbp_ix,gid,unit,gu_ix,company_id,pi_ix,ps_ix,editdate,regdate) values('','".$goods[gid]."','".$unit[$i][unit]."','".$unit[$i][gu_ix]."','".$section[company_id]."','".$section[pi_ix]."','".$section[ps_ix]."',NOW(),NOW())<br/><br/>";
							$db->query("insert into inventory_goods_basic_place(gbp_ix,gid,unit,gu_ix,company_id,pi_ix,ps_ix,editdate,regdate) values('','".$goods[gid]."','".$unit[$i][unit]."','".$unit[$i][gu_ix]."','".$section[company_id]."','".$section[pi_ix]."','".$section[ps_ix]."',NOW(),NOW())");
						}
						echo $val[gid]."|".$val[section]."|성공|<br/>";
					}
				}else{
					echo $val[gid]."|".$val[section]."|실패|단위정보가 존재 X <br/>";
				}
			}else{
				echo $val[gid]."|".$val[section]."|실패|로케이션정보가 존재 X.<br/>";
			}
		}else{
			echo $val[gid]."|".$val[section]."|실패|품목코드 존재 X<br/>";
		}
	}else{
		echo $val[gid]."|".$val[section]."|실패|로케이션명 없음<br/>";
	}
}

exit;


$db->query("select * from shop_order_detail where ode_ix = 0");
$id_array=$db->fetchall("object");

foreach($id_array as $key => $val){

	$sql="SELECT * from shop_order_delivery where 
		oid='".$val[oid]."'
        and delivery_type='".$val[delivery_type]."'
        and delivery_package='".$val[delivery_package]."'
        and delivery_method='".$val[delivery_method]."'
        and delivery_pay_type='".$val[delivery_pay_method]."'
        and delivery_addr_use='".$val[delivery_addr_use]."'
        and factory_info_addr_ix='".$val[factory_info_addr_ix]."'
        and ori_company_id='".$val[ori_company_id]."' ";
	$db->query($sql);

	if($db->total){
		$db->fetch();
		
		$sql="update shop_order_delivery set 
				delivery_policy = '".$val[delivery_policy]."'
			where
				ode_ix='".$db->dt[ode_ix]."'
		";

		$db->query($sql);
		//echo $sql."<br/>";

		$sql="update shop_order_detail set 
				ode_ix = '".$db->dt[ode_ix]."'
			where
				od_ix='".$val[od_ix]."'
		";
		
		$db->query($sql);
		//echo $sql."<br/><br/>";
	}
}

exit;

$mdb->query("select id from shop_product where id not in (select pid from shop_product_addinfo) ");

for($i=0;$i<$mdb->total;$i++){
	$mdb->fetch($i);
	$pid = $mdb->dt[id];

	//기본 정책이 무료인정책 뽑아내기
	$wholesale_free_delivery_yn=0;
	$free_delivery_yn=0;
	$sql="select
			pd.is_wholesale
		from
			shop_product_delivery as pd
			inner join shop_delivery_template as dt on (pd.dt_ix = dt.dt_ix)
		where
			pd.pid='".$pid."' and dt.delivery_policy = '1'
		group by pd.is_wholesale ";
	$db->query($sql);

	if($db->total){
		$db->fetch(0);
		if($db->dt[is_wholesale]=="W"){
			$wholesale_free_delivery_yn=1;
		}
		$db->fetch(1);
		if($db->dt[is_wholesale]=="R"){
			$free_delivery_yn=1;
		}
	}

	//추가 정보 입력하기!!
	$sql = "insert into shop_product_addinfo
		(
			select
				po.pid as pid, GROUP_CONCAT(pod.option_div SEPARATOR '|') as option_div_text , '".$wholesale_free_delivery_yn."' as wholesale_free_delivery_yn , '".$free_delivery_yn."' as free_delivery_yn
			from
				shop_product_options po
				left join shop_product_options_detail pod on (po.pid = pod.pid and po.opn_ix = pod.opn_ix)
			where
				po.pid='".$pid."' and po.option_use = '1'
			group by po.pid
		)";
	$db->query($sql);
}

exit;



$db->query("select * from shop_order_detail group by option1 ");



/*

	20130826 Hong shop_order_detail.option_kind update

*/

$db->query("select * from shop_order_detail group by option1 ");

for($i=0;$i<$db->total;$i++){
	$db->fetch($i);

	if($db->dt[option1]!="0"){
		$mdb->query("select o.option_kind from shop_product_options o , shop_product_options_detail od where o.opn_ix = od.opn_ix and od.id='".$db->dt[option1]."' ");
		$mdb->fetch();
		if($mdb->total){
			$option_kind=$mdb->dt[option_kind];
			$sql= "update shop_order_detail set option_kind='".$option_kind."' where option1='".$db->dt[option1]."' ";
			//echo $sql."<br/>";
			$mdb->query("update shop_order_detail set option_kind='".$option_kind."' where option1='".$db->dt[option1]."' ");
		}
	}
}



exit;
/*

	2013-07-21 홍진영 품목 정보를 상품정보에 업데이트 시키기!

*/

$db->query("select * from inventory_goods ");

for($i=0;$i<$db->total;$i++){
	$db->fetch($i);

	if(is_numeric($db->dt[orgin])){
		$mdb->query("select * from  common_origin where og_ix='".$db->dt[orgin]."' ");
		$mdb->fetch();
		if($mdb->total){
			$origin_name=$mdb->dt[origin_name];
			$mdb->query("update inventory_goods set orgin='".$origin_name."' where gid='".$db->dt[gid]."' ");
		}

		$mdb->query("select * from  common_origin where origin_code='".$db->dt[orgin]."' ");
		$mdb->fetch();
		if($mdb->total){
			$origin_name=$mdb->dt[origin_name];
			$mdb->query("update inventory_goods set orgin='".$origin_name."' where gid='".$db->dt[gid]."' ");
		}
	}
}

exit;

/*

	2013-07-17 홍진영 데이터 비우기 및 파일삭제 (상품, 주문 , 재고 입고리스트, 출고리스트, 수량)

*/
exit;

//상품
$db->query("DELETE FROM ".TBL_SHOP_PRODUCT." ");
$db->query("DELETE FROM ".TBL_SHOP_PRICEINFO." ");
$db->query("DELETE FROM shop_product_buyingservice_priceinfo ");
$db->query("DELETE FROM shop_product_car ");
$db->query("DELETE FROM shop_product_displayinfo ");
$db->query("DELETE FROM shop_product_hotel ");
$db->query("DELETE FROM shop_product_mandatory_info ");
$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS." ");
$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." ");
$db->query("DELETE FROM shop_product_photo");
$db->query("DELETE FROM shop_product_property");
$db->query("DELETE FROM shop_product_set_relation");
$db->query("DELETE FROM shop_product_sightseeing");
$db->query("DELETE FROM shop_product_viralinfo");
$db->query("DELETE FROM ".TBL_SHOP_PRODUCT_RELATION." ");
$db->query("DELETE FROM ".TBL_SHOP_RELATION_PRODUCT." ");
$db->query("DELETE FROM ".TBL_SHOP_PRODUCT."_auction ");
$db->query("DELETE FROM ".TBL_SHOP_CART." ");

//주문
$db->query("DELETE FROM shop_order ");
$db->query("DELETE FROM shop_order_delivery ");
$db->query("DELETE FROM shop_order_detail ");
$db->query("DELETE FROM shop_order_detail_deliveryinfo ");
$db->query("DELETE FROM shop_order_gift ");
$db->query("DELETE FROM shop_order_memo ");
$db->query("DELETE FROM shop_order_price ");
$db->query("DELETE FROM shop_order_price_history ");
$db->query("DELETE FROM shop_order_siteinfo ");
$db->query("DELETE FROM shop_order_status ");

//재고
$db->query("DELETE FROM inventory_history ");
$db->query("DELETE FROM inventory_history_detail ");
$db->query("DELETE FROM inventory_product_stockinfo ");
$db->query("DELETE FROM inventory_warehouse_move ");
$db->query("DELETE FROM inventory_warehouse_move_detail ");

//rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/product/");

exit;




/*

2013-05-28 홍진영 오라클용 스키마 작업

*/

$sql = "lkwtest.SHOW TABLE STATUS";
$db->query($sql);

			echo $db->total;
			exit;
for($i=0;$i < $db->total;$i++){
	$db->fetch($i);

		$table = $db->dt[Name];
		$sql = "SHOW FULL FIELDS FROM $table ";
		$mdb->query($sql);

		for($j=0;$j < $mdb->total;$j++){
			$mdb->fetch($j);

			echo "오케이";
			exit;
		}

}
echo "</table>";
exit;


//제휴사 카테고리 맵핑
$cate["001002004000000"]="Q00301";
$cate["001002005000000"]="Q00301";
$cate["001002006000000"]="Q00301";
$cate["001002007000000"]="Q00301";
$cate["001002001000000"]="Q00303";
$cate["001002002000000"]="Q00305";
$cate["001002003000000"]="Q00307";
$cate["001012013000000"]="Q00616";
$cate["001012002000000"]="Q00617";
$cate["001012004000000"]="Q00617";
$cate["001012001000000"]="Q00617";
$cate["001012012000000"]="Q00618";
$cate["001012003000000"]="Q00619";
$cate["001012007000000"]="Q00619";
$cate["001012008000000"]="Q00619";
$cate["001012009000000"]="Q00619";
$cate["001012010000000"]="Q00619";
$cate["001012011000000"]="Q00619";
$cate["001012014000000"]="Q00619";
$cate["001012005000000"]="Q31102";
$cate["001012006000000"]="Q40701";
$cate["001003002000000"]="Q00106";
$cate["001003005000000"]="Q00106";
$cate["001003006000000"]="Q00602";
$cate["001003001000000"]="Q00901";
$cate["001003004000000"]="Q00903";
$cate["001003003000000"]="Q00904";
$cate["001001002000000"]="Q00201";
$cate["001001005000000"]="Q00204";
$cate["001001001000000"]="Q00205";
$cate["001001003000000"]="Q00205";
$cate["001001004000000"]="Q00207";
$cate["001001006000000"]="Q00208";
$cate["001013001000000"]="Q00604";
$cate["001013002000000"]="Q00604";
$cate["001013003000000"]="Q00604";
$cate["001013004000000"]="Q00604";
$cate["001013005000000"]="Q00604";
$cate["001013006000000"]="Q00604";
$cate["001013008000000"]="Q00604";
$cate["001013009000000"]="Q00604";
$cate["001013010000000"]="Q00604";
$cate["001013011000000"]="Q00802";
$cate["001013007000000"]="Q30416";
$cate["001007001000000"]="Q00401";
$cate["001006012000000"]="Q70210";
$cate["001006006000000"]="Q00503";
$cate["001006011000000"]="Q00503";
$cate["001006010000000"]="Q00504";
$cate["001006009000000"]="Q00506";
$cate["001006001000000"]="Q00507";
$cate["001006002000000"]="Q00507";
$cate["001006004000000"]="Q00507";
$cate["001006005000000"]="Q00507";
$cate["001006008000000"]="Q00509";
$cate["001006007000000"]="Q31108";
$cate["001009001000000"]="Q00601";
$cate["001009002000000"]="Q00601";
$cate["001009003000000"]="Q00601";
$cate["001009004000000"]="Q00601";
$cate["001009006000000"]="Q00601";
$cate["001009007000000"]="Q00601";
$cate["001009008000000"]="Q00601";
$cate["001009009000000"]="Q00601";
$cate["001010001000000"]="Q00603";
$cate["001010002000000"]="Q00603";
$cate["001010003000000"]="Q00603";
$cate["001010004000000"]="Q00603";
$cate["001010005000000"]="Q00603";
$cate["001010011000000"]="Q00603";
$cate["001010006000000"]="Q00624";
$cate["001010008000000"]="Q00624";
$cate["001010010000000"]="Q00624";
$cate["001010009000000"]="Q00624";
$cate["001010007000000"]="Q00624";
$cate["001008001000000"]="Q00612";
$cate["001008003000000"]="Q00612";
$cate["001008002000000"]="Q00613";
$cate["001008007000000"]="Q00614";
$cate["001008004000000"]="Q00615";
$cate["001008005000000"]="Q00615";
$cate["001008006000000"]="Q00615";
$cate["001008008000000"]="Q00621";
$cate["001008009000000"]="Q00621";
$cate["001011003000000"]="Q00623";
$cate["001011005000000"]="Q00623";
$cate["001011006000000"]="Q00623";
$cate["001011008000000"]="Q00623";
$cate["001011007000000"]="Q00623";
$cate["001011004000000"]="Q00623";
$cate["001011002000000"]="Q00623";
$cate["001011001000000"]="Q00623";
$cate["001005001000000"]="Q00401";
$cate["001005002000000"]="Q00401";
$cate["001005003000000"]="Q00401";
$cate["001005004000000"]="Q00401";
$cate["001005008000000"]="Q00402";
$cate["001005009000000"]="Q00402";
$cate["001005010000000"]="Q00402";
$cate["001005011000000"]="Q00402";
$cate["001005012000000"]="Q00402";
$cate["001005013000000"]="Q00402";
$cate["001005014000000"]="Q00402";
$cate["001005006000000"]="Q00403";
$cate["001005005000000"]="Q00404";
$cate["001005007000000"]="Q00406";
$cate["001004001000000"]="Q00102";
$cate["001004004000000"]="Q00107";
$cate["001004002000000"]="Q00108";
$cate["001004003000000"]="Q00108";
$cate["001004005000000"]="Q00602";
$cate["001014001000000"]="Q30408";
$cate["001014002000000"]="Q30408";
$cate["001014003000000"]="Q30408";
$cate["001014004000000"]="Q30408";
$cate["001014005000000"]="Q30408";
$cate["001014006000000"]="Q30408";
$cate["001015001000000"]="Q30416";
$cate["001015002000000"]="Q30416";
$cate["006004009000000"]="Q30413";
$cate["006004003000000"]="Q51301";
$cate["006004010000000"]="진행안함";
$cate["006004001000000"]="진행안함";
$cate["006004005000000"]="진행안함";
$cate["006004011000000"]="진행안함";
$cate["006004008000000"]="진행안함";
$cate["006004004000000"]="진행안함";
$cate["006004002000000"]="진행안함";
$cate["006004007000000"]="진행안함";
$cate["006004006000000"]="진행안함";
$cate["006003011000000"]="진행안함";
$cate["006003008000000"]="진행안함";
$cate["006003001000000"]="진행안함";
$cate["006003003000000"]="진행안함";
$cate["006003002000000"]="진행안함";
$cate["006003004000000"]="진행안함";
$cate["006003007000000"]="진행안함";
$cate["006003009000000"]="진행안함";
$cate["006003010000000"]="진행안함";
$cate["006003005000000"]="진행안함";
$cate["006003006000000"]="진행안함";
$cate["006013006000000"]="진행안함";
$cate["006013007000000"]="진행안함";
$cate["006013005000000"]="진행안함";
$cate["006013001000000"]="진행안함";
$cate["006013008000000"]="진행안함";
$cate["006013002000000"]="진행안함";
$cate["006013003000000"]="진행안함";
$cate["006013004000000"]="진행안함";
$cate["006002010000000"]="진행안함";
$cate["006002004000000"]="진행안함";
$cate["006002013000000"]="진행안함";
$cate["006002005000000"]="진행안함";
$cate["006002006000000"]="진행안함";
$cate["006002002000000"]="진행안함";
$cate["006002007000000"]="진행안함";
$cate["006002009000000"]="진행안함";
$cate["006002012000000"]="진행안함";
$cate["006002001000000"]="진행안함";
$cate["006002003000000"]="진행안함";
$cate["006002008000000"]="진행안함";
$cate["006002011000000"]="진행안함";
$cate["006005004000000"]="진행안함";
$cate["006005003000000"]="진행안함";
$cate["006005001000000"]="진행안함";
$cate["006005002000000"]="진행안함";
$cate["006009009000000"]="Q51102";
$cate["006009001000000"]="Q51103";
$cate["006009002000000"]="Q51103";
$cate["006009003000000"]="Q51103";
$cate["006009004000000"]="Q51105";
$cate["006009005000000"]="Q51105";
$cate["006009006000000"]="Q51105";
$cate["006009007000000"]="Q51105";
$cate["006009011000000"]="Q51106";
$cate["006009012000000"]="진행안함";
$cate["006009010000000"]="진행안함";
$cate["006009008000000"]="진행안함";
$cate["006012017000000"]="진행안함";
$cate["006012007000000"]="진행안함";
$cate["006012012000000"]="진행안함";
$cate["006012009000000"]="진행안함";
$cate["006012010000000"]="진행안함";
$cate["006012016000000"]="진행안함";
$cate["006012008000000"]="진행안함";
$cate["006012011000000"]="진행안함";
$cate["006012004000000"]="진행안함";
$cate["006012001000000"]="진행안함";
$cate["006012002000000"]="진행안함";
$cate["006012005000000"]="진행안함";
$cate["006012006000000"]="진행안함";
$cate["006012003000000"]="진행안함";
$cate["006012014000000"]="진행안함";
$cate["006012015000000"]="진행안함";
$cate["006012013000000"]="진행안함";
$cate["006008002000000"]="진행안함";
$cate["006008004000000"]="진행안함";
$cate["006008001000000"]="진행안함";
$cate["006008005000000"]="진행안함";
$cate["006008003000000"]="진행안함";
$cate["006008006000000"]="진행안함";
$cate["006010002000000"]="Q51202";
$cate["006010008000000"]="Q51206";
$cate["006010006000000"]="Q51207";
$cate["006010007000000"]="Q51209";
$cate["006010003000000"]="Q51210";
$cate["006010004000000"]="Q51210";
$cate["006010005000000"]="Q51210";
$cate["006010011000000"]="Q51211";
$cate["006010009000000"]="Q51212";
$cate["006010010000000"]="Q51212";
$cate["006010012000000"]="Q51212";
$cate["006010013000000"]="Q51212";
$cate["006010014000000"]="Q51212";
$cate["006010015000000"]="Q51212";
$cate["006014001000000"]="Q51401";
$cate["006014003000000"]="Q51402";
$cate["006014004000000"]="Q51402";
$cate["006014005000000"]="Q51404";
$cate["006014006000000"]="Q51405";
$cate["006014007000000"]="Q51405";
$cate["006014008000000"]="Q51405";
$cate["006014009000000"]="Q51405";
$cate["006014010000000"]="Q51405";
$cate["006014002000000"]="진행안함";
$cate["006001010000000"]="진행안함";
$cate["006001001000000"]="진행안함";
$cate["006001009000000"]="진행안함";
$cate["006001003000000"]="진행안함";
$cate["006001004000000"]="진행안함";
$cate["006001008000000"]="진행안함";
$cate["006001006000000"]="진행안함";
$cate["006001002000000"]="진행안함";
$cate["006001005000000"]="진행안함";
$cate["006001007000000"]="진행안함";
$cate["006011001000000"]="Q30420";
$cate["006011012000000"]="진행안함";
$cate["006011008000000"]="진행안함";
$cate["006011007000000"]="진행안함";
$cate["006011011000000"]="진행안함";
$cate["006011003000000"]="진행안함";
$cate["006011010000000"]="진행안함";
$cate["006011009000000"]="진행안함";
$cate["006011005000000"]="진행안함";
$cate["006011006000000"]="진행안함";
$cate["006011002000000"]="진행안함";
$cate["006011004000000"]="진행안함";
$cate["006007001000000"]="진행안함";
$cate["006007002000000"]="진행안함";
$cate["006006002000000"]="진행안함";
$cate["006006006000000"]="진행안함";
$cate["006006005000000"]="진행안함";
$cate["006006003000000"]="진행안함";
$cate["006006004000000"]="진행안함";
$cate["006006001000000"]="진행안함";
$cate["010006007000000"]="PO0501";
$cate["010006002000000"]="Q10316";
$cate["010006004000000"]="Q20212";
$cate["010006001000000"]="Q20702";
$cate["010006003000000"]="Q20704";
$cate["010006011000000"]="Q30205";
$cate["010006005000000"]="Q30405";
$cate["010006017000000"]="Q30413";
$cate["010006015000000"]="Q30413";
$cate["010006013000000"]="Q30419";
$cate["010006008000000"]="Q30423";
$cate["010006012000000"]="Q30703";
$cate["010006006000000"]="Q31411";
$cate["010006016000000"]="Q31803";
$cate["010006014000000"]="Q50804";
$cate["010006009000000"]="Q51104";
$cate["010006010000000"]="Q70104";
$cate["010015010000000"]="Q30202";
$cate["010015008000000"]="Q30202";
$cate["010015004000000"]="Q30203";
$cate["010015003000000"]="Q30203";
$cate["010015001000000"]="Q30205";
$cate["010015015000000"]="Q30205";
$cate["010015013000000"]="Q30205";
$cate["010015007000000"]="Q30205";
$cate["010015006000000"]="Q30205";
$cate["010015005000000"]="Q30205";
$cate["010015002000000"]="Q30205";
$cate["010015014000000"]="Q30205";
$cate["010015011000000"]="Q30206";
$cate["010015018000000"]="Q30433";
$cate["010015017000000"]="Q31413";
$cate["010001004000000"]="Q10604";
$cate["010001002000000"]="Q70101";
$cate["010001003000000"]="Q70101";
$cate["010001001000000"]="Q70101";
$cate["010001005000000"]="Q70104";
$cate["010001006000000"]="Q70104";
$cate["010001007000000"]="Q70104";
$cate["010002001000000"]="Q00906";
$cate["010002002000000"]="Q00906";
$cate["010002013000000"]="Q30431";
$cate["010002004000000"]="Q31106";
$cate["010002010000000"]="Q31106";
$cate["010002012000000"]="Q31108";
$cate["010002007000000"]="Q70104";
$cate["010002015000000"]="Q70201";
$cate["010002017000000"]="Q70201";
$cate["010002008000000"]="Q70202";
$cate["010002009000000"]="Q70204";
$cate["010002011000000"]="Q70205";
$cate["010002014000000"]="Q70205";
$cate["010002006000000"]="Q70206";
$cate["010002005000000"]="Q70206";
$cate["010002003000000"]="Q70206";
$cate["010002016000000"]="Q70209";
$cate["010014013000000"]="Q30203";
$cate["010014009000000"]="Q30205";
$cate["010014002000000"]="Q30205";
$cate["010014005000000"]="Q30205";
$cate["010014015000000"]="Q30205";
$cate["010014004000000"]="Q30205";
$cate["010014003000000"]="Q30205";
$cate["010014001000000"]="Q30205";
$cate["010014008000000"]="Q30205";
$cate["010014007000000"]="Q30205";
$cate["010014006000000"]="Q30205";
$cate["010014012000000"]="Q30206";
$cate["010014011000000"]="Q30206";
$cate["010014010000000"]="Q30206";
$cate["010017014000000"]="Q40101";
$cate["010017005000000"]="Q40405";
$cate["010017004000000"]="Q40701";
$cate["010017017000000"]="Q40710";
$cate["010017003000000"]="Q40713";
$cate["010017012000000"]="Q40713";
$cate["010017010000000"]="Q40713";
$cate["010017002000000"]="Q40713";
$cate["010017011000000"]="Q40713";
$cate["010017007000000"]="Q40713";
$cate["010017018000000"]="Q40713";
$cate["010017008000000"]="Q40713";
$cate["010017001000000"]="Q40713";
$cate["010017013000000"]="Q40714";
$cate["010017006000000"]="Q40802";
$cate["010017009000000"]="Q40904";
$cate["010017016000000"]="Q51102";
$cate["010010006000000"]="Q10603";
$cate["010010001000000"]="Q10604";
$cate["010010009000000"]="Q10605";
$cate["010010003000000"]="Q10606";
$cate["010010002000000"]="Q10607";
$cate["010010008000000"]="Q10607";
$cate["010010007000000"]="Q30401";
$cate["010010004000000"]="Q31406";
$cate["010008002000000"]="Q30408";
$cate["010008008000000"]="Q30416";
$cate["010008007000000"]="Q30434";
$cate["010008006000000"]="Q30435";
$cate["010008004000000"]="Q31410";
$cate["010008005000000"]="Q31410";
$cate["010008001000000"]="Q31410";
$cate["010008003000000"]="Q31410";
$cate["010003005000000"]="Q10210";
$cate["010003006000000"]="Q10211";
$cate["010003007000000"]="Q10212";
$cate["010003013000000"]="Q10216";
$cate["010003010000000"]="Q10217";
$cate["010003011000000"]="Q10217";
$cate["010003008000000"]="Q10234";
$cate["010003003000000"]="Q10502";
$cate["010003009000000"]="Q10503";
$cate["010003001000000"]="Q10504";
$cate["010003002000000"]="Q10504";
$cate["010003004000000"]="Q10504";
$cate["010003012000000"]="Q10507";
$cate["010007002000000"]="PH0302";
$cate["010007009000000"]="Q01204";
$cate["010007006000000"]="Q01204";
$cate["010007001000000"]="Q01204";
$cate["010007011000000"]="Q20708";
$cate["010007005000000"]="Q30413";
$cate["010007004000000"]="Q30413";
$cate["010007007000000"]="Q30426";
$cate["010007012000000"]="Q30426";
$cate["010007008000000"]="Q30426";
$cate["010007010000000"]="Q40904";
$cate["010018003000000"]="PF0208";
$cate["010018004000000"]="Q30434";
$cate["010018002000000"]="Q50711";
$cate["010018001000000"]="Q51601";
$cate["010011007000000"]="Q10302";
$cate["010011015000000"]="Q10302";
$cate["010011003000000"]="Q10302";
$cate["010011012000000"]="Q10302";
$cate["010011001000000"]="Q10302";
$cate["010011010000000"]="Q10302";
$cate["010011016000000"]="Q10302";
$cate["010011006000000"]="Q10305";
$cate["010011009000000"]="Q10308";
$cate["010011011000000"]="Q10312";
$cate["010011008000000"]="Q10316";
$cate["010011002000000"]="Q10317";
$cate["010011013000000"]="Q30432";
$cate["010011014000000"]="Q51104";
$cate["010013001000000"]="Q51902";
$cate["010013002000000"]="Q51902";
$cate["010013003000000"]="Q51903";
$cate["010013005000000"]="Q51903";
$cate["010013004000000"]="Q51903";
$cate["010013006000000"]="Q51903";
$cate["010013007000000"]="Q51903";
$cate["010016014000000"]="PF0111";
$cate["010016005000000"]="Q40312";
$cate["010016011000000"]="Q40313";
$cate["010016004000000"]="Q40313";
$cate["010016001000000"]="Q40401";
$cate["010016002000000"]="Q40403";
$cate["010016008000000"]="Q40713";
$cate["010016007000000"]="Q40713";
$cate["010016006000000"]="Q40713";
$cate["010016003000000"]="Q40713";
$cate["010016009000000"]="Q41501";
$cate["010016013000000"]="Q41501";
$cate["010016017000000"]="Q41501";
$cate["010016015000000"]="Q41501";
$cate["010016012000000"]="Q41501";
$cate["010016010000000"]="Q41501";
$cate["010012016000000"]="Q00601";
$cate["010012015000000"]="Q00603";
$cate["010012007000000"]="Q00604";
$cate["010012008000000"]="Q00604";
$cate["010012001000000"]="Q00606";
$cate["010012005000000"]="Q00619";
$cate["010012003000000"]="Q00619";
$cate["010012017000000"]="Q00619";
$cate["010012006000000"]="Q00619";
$cate["010012004000000"]="Q00623";
$cate["010012011000000"]="Q01202";
$cate["010012010000000"]="Q01203";
$cate["010012009000000"]="Q30426";
$cate["010012014000000"]="Q40504";
$cate["010012013000000"]="Q40901";
$cate["010012012000000"]="Q40903";
$cate["010012002000000"]="Q50602";
$cate["010005006000000"]="Q10236";
$cate["010005001000000"]="Q10236";
$cate["010005015000000"]="Q10236";
$cate["010005012000000"]="Q10240";
$cate["010005008000000"]="Q10305";
$cate["010005013000000"]="Q10403";
$cate["010005009000000"]="Q10501";
$cate["010005010000000"]="Q10501";
$cate["010005004000000"]="Q10501";
$cate["010005011000000"]="Q10503";
$cate["010005002000000"]="Q10512";
$cate["010005007000000"]="Q10514";
$cate["010005003000000"]="Q20708";
$cate["010005014000000"]="Q20709";
$cate["010005005000000"]="Q31407";
$cate["010004015000000"]="Q10201";
$cate["010004010000000"]="Q10211";
$cate["010004014000000"]="Q10224";
$cate["010004005000000"]="Q10236";
$cate["010004009000000"]="Q10401";
$cate["010004008000000"]="Q10401";
$cate["010004007000000"]="Q10402";
$cate["010004002000000"]="Q10403";
$cate["010004001000000"]="Q10403";
$cate["010004013000000"]="Q10404";
$cate["010004012000000"]="Q10404";
$cate["010004011000000"]="Q10406";
$cate["010004006000000"]="Q10407";
$cate["010004018000000"]="Q10504";
$cate["010004003000000"]="Q10505";
$cate["010004017000000"]="Q10508";
$cate["010004004000000"]="Q20709";
$cate["010004016000000"]="Q31605";
$cate["010009008000000"]="Q10701";
$cate["010009003000000"]="Q10702";
$cate["010009004000000"]="Q10702";
$cate["010009002000000"]="Q10705";
$cate["010009005000000"]="Q30203";
$cate["010009006000000"]="Q30401";
$cate["010009001000000"]="Q30429";
$cate["010009007000000"]="Q31407";
$cate["010019006000000"]="Q30427";
$cate["010019003000000"]="Q50406";
$cate["010019004000000"]="Q51402";
$cate["010019002000000"]="Q51405";
$cate["010019001000000"]="Q51405";
$cate["009009001000000"]="Q30703";
$cate["009009002000000"]="Q30703";
$cate["009009003000000"]="Q30703";
$cate["009002004000000"]="Q30203";
$cate["009002009000000"]="Q30203";
$cate["009002007000000"]="Q30203";
$cate["009002008000000"]="Q30203";
$cate["009002005000000"]="Q30203";
$cate["009002001000000"]="Q30203";
$cate["009002006000000"]="Q30203";
$cate["009002002000000"]="Q30203";
$cate["009002003000000"]="Q30203";
$cate["009002011000000"]="Q30204";
$cate["009002010000000"]="Q30204";
$cate["009010001000000"]="Q31701";
$cate["009010003000000"]="Q31702";
$cate["009010004000000"]="Q31702";
$cate["009010002000000"]="Q31703";
$cate["009004001000000"]="Q30202";
$cate["009004003000000"]="Q30202";
$cate["009004005000000"]="Q30202";
$cate["009004004000000"]="Q30202";
$cate["009004002000000"]="Q30202";
$cate["009005002000000"]="Q31801";
$cate["009005003000000"]="Q31802";
$cate["009005001000000"]="Q31802";
$cate["009005007000000"]="Q31803";
$cate["009005009000000"]="Q31803";
$cate["009005006000000"]="Q31803";
$cate["009005005000000"]="Q31803";
$cate["009005008000000"]="Q31803";
$cate["009005004000000"]="Q31804";
$cate["009007001000000"]="Q31804";
$cate["009007003000000"]="Q31804";
$cate["009007004000000"]="Q31804";
$cate["009007002000000"]="Q31804";
$cate["009001010000000"]="Q30203";
$cate["009001009000000"]="Q30205";
$cate["009001005000000"]="Q30205";
$cate["009001008000000"]="Q30205";
$cate["009001014000000"]="Q30205";
$cate["009001003000000"]="Q30205";
$cate["009001002000000"]="Q30205";
$cate["009001013000000"]="Q30205";
$cate["009001004000000"]="Q30205";
$cate["009001012000000"]="Q30205";
$cate["009001011000000"]="Q30205";
$cate["009001001000000"]="Q30205";
$cate["009001007000000"]="Q30206";
$cate["009001006000000"]="Q30206";
$cate["009008003000000"]="Q30206";
$cate["009008007000000"]="Q30703";
$cate["009008001000000"]="Q30703";
$cate["009008002000000"]="Q30703";
$cate["009008005000000"]="Q30703";
$cate["009008006000000"]="Q30703";
$cate["009008004000000"]="Q30703";
$cate["009006003000000"]="Q30206";
$cate["009006001000000"]="Q30206";
$cate["009006002000000"]="Q30206";
$cate["009003008000000"]="Q30201";
$cate["009003004000000"]="Q30206";
$cate["009003005000000"]="Q30206";
$cate["009003003000000"]="Q30206";
$cate["009003009000000"]="Q30206";
$cate["009003006000000"]="Q31402";
$cate["009003007000000"]="Q31402";
$cate["009003001000000"]="Q31404";
$cate["009003002000000"]="Q31404";
$cate["007011003000000"]="Q41505";
$cate["007011002000000"]="Q41505";
$cate["007011001000000"]="Q41505";
$cate["007007005000000"]="Q00619";
$cate["007007003000000"]="Q30409";
$cate["007007001000000"]="Q40104";
$cate["007007004000000"]="Q40104";
$cate["007007002000000"]="Q40202";
$cate["007007006000000"]="Q40504";
$cate["007007008000000"]="Q50109";
$cate["007007007000000"]="Q51102";
$cate["007003002000000"]="Q40703";
$cate["007003001000000"]="Q40708";
$cate["007003003000000"]="Q51305";
$cate["007001002000000"]="진행안함";
$cate["007001001000000"]="진행안함";
$cate["007001006000000"]="진행안함";
$cate["007001003000000"]="진행안함";
$cate["007001005000000"]="진행안함";
$cate["007001004000000"]="진행안함";
$cate["007001007000000"]="진행안함";
$cate["007008008000000"]="Q30417";
$cate["007008001000000"]="Q40706";
$cate["007008010000000"]="Q40715";
$cate["007008009000000"]="Q40715";
$cate["007008006000000"]="Q40715";
$cate["007008002000000"]="Q40715";
$cate["007008003000000"]="Q40715";
$cate["007008007000000"]="Q40715";
$cate["007008004000000"]="Q40715";
$cate["007008005000000"]="Q40715";
$cate["007009003000000"]="Q41501";
$cate["007009004000000"]="Q41501";
$cate["007009002000000"]="Q41503";
$cate["007009001000000"]="Q41503";
$cate["007004007000000"]="Q40701";
$cate["007004012000000"]="Q40713";
$cate["007004011000000"]="Q40713";
$cate["007004001000000"]="Q40713";
$cate["007004004000000"]="Q40713";
$cate["007004010000000"]="Q40713";
$cate["007004003000000"]="Q40713";
$cate["007004002000000"]="Q40713";
$cate["007004006000000"]="Q40713";
$cate["007004005000000"]="Q40713";
$cate["007004009000000"]="Q40714";
$cate["007004008000000"]="Q40714";
$cate["007012004000000"]="Q41501";
$cate["007012003000000"]="Q41501";
$cate["007012001000000"]="Q41501";
$cate["007012002000000"]="Q41501";
$cate["007005003000000"]="Q40301";
$cate["007005002000000"]="Q40302";
$cate["007005001000000"]="Q40312";
$cate["007005005000000"]="Q40312";
$cate["007005004000000"]="Q40504";
$cate["007006006000000"]="Q40402";
$cate["007006001000000"]="Q40403";
$cate["007006002000000"]="Q40403";
$cate["007006007000000"]="Q40404";
$cate["007006003000000"]="Q40405";
$cate["007006004000000"]="Q40405";
$cate["007006005000000"]="Q40405";
$cate["007010002000000"]="Q40405";
$cate["007010001000000"]="Q41501";
$cate["007010003000000"]="Q41501";
$cate["007010006000000"]="Q41503";
$cate["007010004000000"]="Q41504";
$cate["007010005000000"]="Q41504";
$cate["007002004000000"]="Q40802";
$cate["007002005000000"]="Q40802";
$cate["007002008000000"]="Q40802";
$cate["007002009000000"]="Q40802";
$cate["007002007000000"]="Q40802";
$cate["007002002000000"]="Q40802";
$cate["007002001000000"]="Q40802";
$cate["007002006000000"]="Q40802";
$cate["007002003000000"]="Q40802";
$cate["004006003000000"]="Q31409";
$cate["004006006000000"]="Q31410";
$cate["004006008000000"]="Q31410";
$cate["004006007000000"]="Q31410";
$cate["004006005000000"]="Q31410";
$cate["004006004000000"]="Q31410";
$cate["004006001000000"]="Q31412";
$cate["004006002000000"]="Q31412";
$cate["004009009000000"]="Q20210";
$cate["004009004000000"]="Q20702";
$cate["004009018000000"]="Q30413";
$cate["004009007000000"]="Q30413";
$cate["004009006000000"]="Q30413";
$cate["004009017000000"]="Q30413";
$cate["004009005000000"]="Q30419";
$cate["004009003000000"]="Q30419";
$cate["004009002000000"]="Q30419";
$cate["004009001000000"]="Q30419";
$cate["004009015000000"]="Q30421";
$cate["004009010000000"]="Q30434";
$cate["004009013000000"]="Q30434";
$cate["004009008000000"]="Q30434";
$cate["004009011000000"]="Q30434";
$cate["004009012000000"]="Q30434";
$cate["004009016000000"]="Q30435";
$cate["004009014000000"]="Q30435";
$cate["004002009000000"]="Q10603";
$cate["004002010000000"]="Q10603";
$cate["004002012000000"]="Q10604";
$cate["004002016000000"]="Q10605";
$cate["004002013000000"]="Q10607";
$cate["004002015000000"]="Q10607";
$cate["004002011000000"]="Q30401";
$cate["004002014000000"]="Q30401";
$cate["004002001000000"]="Q30402";
$cate["004002002000000"]="Q30402";
$cate["004002003000000"]="Q30402";
$cate["004002005000000"]="Q30402";
$cate["004002006000000"]="Q30402";
$cate["004002007000000"]="Q30402";
$cate["004002008000000"]="Q31407";
$cate["004002004000000"]="Q31408";
$cate["004004001000000"]="Q10302";
$cate["004004002000000"]="Q10302";
$cate["004004003000000"]="Q10302";
$cate["004004004000000"]="Q10302";
$cate["004004006000000"]="Q10302";
$cate["004004008000000"]="Q10302";
$cate["004004010000000"]="Q10302";
$cate["004004011000000"]="Q10302";
$cate["004004017000000"]="Q10302";
$cate["004004012000000"]="Q10305";
$cate["004004014000000"]="Q10309";
$cate["004004015000000"]="Q10312";
$cate["004004016000000"]="Q10313";
$cate["004004013000000"]="Q10316";
$cate["004004005000000"]="Q10317";
$cate["004004007000000"]="Q10318";
$cate["004004009000000"]="Q10318";
$cate["004008005000000"]="Q30430";
$cate["004008006000000"]="Q30431";
$cate["004008007000000"]="Q30431";
$cate["004008001000000"]="Q31501";
$cate["004008004000000"]="Q31503";
$cate["004008002000000"]="Q31504";
$cate["004008003000000"]="Q31505";
$cate["004005009000000"]="Q30205";
$cate["004005010000000"]="Q30413";
$cate["004005005000000"]="Q30432";
$cate["004005001000000"]="Q30432";
$cate["004005002000000"]="Q30432";
$cate["004005003000000"]="Q30432";
$cate["004005007000000"]="Q30433";
$cate["004005008000000"]="Q30433";
$cate["004005004000000"]="Q31401";
$cate["004005006000000"]="Q31403";
$cate["004007001000000"]="Q30403";
$cate["004007002000000"]="Q30404";
$cate["004007004000000"]="Q30404";
$cate["004007005000000"]="Q30404";
$cate["004007006000000"]="Q30404";
$cate["004007007000000"]="Q30404";
$cate["004007003000000"]="Q30417";
$cate["004001001000000"]="Q30402";
$cate["004001002000000"]="Q30402";
$cate["004001003000000"]="Q30402";
$cate["004001004000000"]="Q30402";
$cate["004001006000000"]="Q30402";
$cate["004001005000000"]="Q31411";
$cate["004001007000000"]="Q31413";
$cate["004001008000000"]="Q31413";
$cate["004003009000000"]="Q10702";
$cate["004003004000000"]="Q10703";
$cate["004003005000000"]="Q10703";
$cate["004003003000000"]="Q10704";
$cate["004003006000000"]="Q10705";
$cate["004003012000000"]="Q30401";
$cate["004003011000000"]="Q30401";
$cate["004003008000000"]="Q30401";
$cate["004003010000000"]="Q30401";
$cate["004003007000000"]="Q30401";
$cate["004003002000000"]="Q30429";
$cate["004003001000000"]="Q30429";
$cate["000006002000000"]="Q30205";
$cate["000006011000000"]="Q31106";
$cate["000006010000000"]="Q31107";
$cate["000006004000000"]="Q70204";
$cate["000006003000000"]="Q70204";
$cate["000006007000000"]="Q70205";
$cate["000006009000000"]="Q70205";
$cate["000006006000000"]="Q70205";
$cate["000006001000000"]="Q70205";
$cate["000006005000000"]="Q70206";
$cate["000006008000000"]="Q70207";
$cate["000002008000000"]="Q31104";
$cate["000002001000000"]="Q31105";
$cate["000002003000000"]="Q70201";
$cate["000002002000000"]="Q70201";
$cate["000002006000000"]="Q70202";
$cate["000002004000000"]="Q70202";
$cate["000002009000000"]="Q70203";
$cate["000002007000000"]="Q70203";
$cate["000001006000000"]="Q10604";
$cate["000001002000000"]="Q70101";
$cate["000001001000000"]="Q70101";
$cate["000001003000000"]="Q70102";
$cate["000001005000000"]="Q70102";
$cate["000001009000000"]="Q70103";
$cate["000001011000000"]="Q70104";
$cate["000001008000000"]="Q70104";
$cate["000001007000000"]="Q70104";
$cate["000001004000000"]="Q70104";
$cate["000005011000000"]="Q00106";
$cate["000005010000000"]="Q00207";
$cate["000005009000000"]="Q00207";
$cate["000005003000000"]="Q00207";
$cate["000005001000000"]="Q00207";
$cate["000005002000000"]="Q00207";
$cate["000005006000000"]="Q00306";
$cate["000005005000000"]="Q00401";
$cate["000005007000000"]="Q00403";
$cate["000005004000000"]="Q70210";
$cate["000004001000000"]="Q31107";
$cate["000004009000000"]="Q31108";
$cate["000004008000000"]="Q31109";
$cate["000004002000000"]="Q70209";
$cate["000004004000000"]="Q70209";
$cate["000004003000000"]="Q70209";
$cate["000004005000000"]="Q70209";
$cate["000004006000000"]="Q70209";
$cate["000009006000000"]="Q00702";
$cate["000009003000000"]="Q00703";
$cate["000009007000000"]="Q00703";
$cate["000009004000000"]="Q00703";
$cate["000009002000000"]="Q00703";
$cate["000009005000000"]="Q00703";
$cate["000009001000000"]="Q00906";
$cate["000008007000000"]="Q00205";
$cate["000008009000000"]="Q00707";
$cate["000008006000000"]="Q70207";
$cate["000008008000000"]="Q70207";
$cate["000008005000000"]="Q70207";
$cate["000008004000000"]="Q70207";
$cate["000008002000000"]="Q70208";
$cate["000008001000000"]="Q70208";
$cate["000008003000000"]="Q70208";
$cate["000003007000000"]="Q00901";
$cate["000003012000000"]="Q00903";
$cate["000003008000000"]="Q00905";
$cate["000003006000000"]="Q00906";
$cate["000003009000000"]="Q00907";
$cate["000003001000000"]="Q30415";
$cate["000003002000000"]="Q30415";
$cate["000003003000000"]="Q30415";
$cate["000003004000000"]="Q30415";
$cate["000003005000000"]="Q30415";
$cate["000003010000000"]="Q70206";
$cate["000007007000000"]="Q10704";
$cate["000007002000000"]="Q30431";
$cate["000007003000000"]="Q31108";
$cate["000007001000000"]="Q70205";
$cate["000007005000000"]="Q70205";
$cate["000007006000000"]="Q70205";
$cate["000007004000000"]="Q70208";
$cate["008011001000000"]="Q01201";
$cate["008004009000000"]="Q30427";
$cate["008004012000000"]="Q30427";
$cate["008004006000000"]="Q30427";
$cate["008004013000000"]="Q30427";
$cate["008004007000000"]="Q30427";
$cate["008004008000000"]="Q30427";
$cate["008004010000000"]="Q30427";
$cate["008004011000000"]="Q30427";
$cate["008004001000000"]="Q30427";
$cate["008004002000000"]="Q30427";
$cate["008004003000000"]="Q30427";
$cate["008004004000000"]="Q30427";
$cate["008004005000000"]="Q30427";
$cate["008010002000000"]="Q51001";
$cate["008010001000000"]="Q51002";
$cate["008010003000000"]="Q51003";
$cate["008001013000000"]="Q10234";
$cate["008001015000000"]="Q10403";
$cate["008001003000000"]="Q10504";
$cate["008001001000000"]="Q40901";
$cate["008001002000000"]="Q40901";
$cate["008001006000000"]="Q40901";
$cate["008001004000000"]="Q40902";
$cate["008001005000000"]="Q40902";
$cate["008001007000000"]="Q40902";
$cate["008001014000000"]="Q40902";
$cate["008001008000000"]="Q40903";
$cate["008001009000000"]="Q40903";
$cate["008001010000000"]="Q40903";
$cate["008001011000000"]="Q40904";
$cate["008001012000000"]="Q40905";
$cate["008005014000000"]="Q30427";
$cate["008005016000000"]="Q30427";
$cate["008005010000000"]="Q30427";
$cate["008005004000000"]="Q30427";
$cate["008005017000000"]="Q30427";
$cate["008005013000000"]="Q30427";
$cate["008005015000000"]="Q30427";
$cate["008005003000000"]="Q30427";
$cate["008005012000000"]="Q30427";
$cate["008005011000000"]="Q30427";
$cate["008005001000000"]="Q30427";
$cate["008005008000000"]="Q30427";
$cate["008005009000000"]="Q30427";
$cate["008005006000000"]="Q30427";
$cate["008005002000000"]="Q30427";
$cate["008005005000000"]="Q30427";
$cate["008005007000000"]="Q30427";
$cate["008006002000000"]="Q01201";
$cate["008006001000000"]="Q01201";
$cate["008006013000000"]="Q01202";
$cate["008006014000000"]="Q01202";
$cate["008006009000000"]="Q01202";
$cate["008006008000000"]="Q01202";
$cate["008006007000000"]="Q01202";
$cate["008006003000000"]="Q01202";
$cate["008006012000000"]="Q01203";
$cate["008006004000000"]="Q01203";
$cate["008006011000000"]="Q01203";
$cate["008006010000000"]="Q01203";
$cate["008006006000000"]="Q01205";
$cate["008006005000000"]="Q01205";
$cate["008003009000000"]="Q31109";
$cate["008003001000000"]="Q51901";
$cate["008003002000000"]="Q51901";
$cate["008003005000000"]="Q51902";
$cate["008003004000000"]="Q51902";
$cate["008003003000000"]="Q51902";
$cate["008003012000000"]="Q51903";
$cate["008003006000000"]="Q51903";
$cate["008003010000000"]="Q51903";
$cate["008003007000000"]="Q51903";
$cate["008003011000000"]="Q51903";
$cate["008003008000000"]="Q51903";
$cate["008007004000000"]="Q00604";
$cate["008007005000000"]="Q01202";
$cate["008007002000000"]="Q01202";
$cate["008007001000000"]="Q01203";
$cate["008007007000000"]="Q01203";
$cate["008007003000000"]="Q01203";
$cate["008007006000000"]="Q01204";
$cate["008007008000000"]="Q30426";
$cate["008007009000000"]="Q30426";
$cate["008009005000000"]="Q30408";
$cate["008009011000000"]="Q51601";
$cate["008009007000000"]="Q51601";
$cate["008009013000000"]="Q51601";
$cate["008009008000000"]="Q51601";
$cate["008009009000000"]="Q51601";
$cate["008009004000000"]="Q51601";
$cate["008009010000000"]="Q51602";
$cate["008009006000000"]="Q51606";
$cate["008009003000000"]="Q51606";
$cate["008009012000000"]="Q51607";
$cate["008009001000000"]="Q51608";
$cate["008009002000000"]="Q51608";
$cate["008002002000000"]="Q10404";
$cate["008002001000000"]="Q31602";
$cate["008002015000000"]="Q31603";
$cate["008002016000000"]="Q31603";
$cate["008002017000000"]="Q31603";
$cate["008002018000000"]="Q31603";
$cate["008002003000000"]="Q31605";
$cate["008002004000000"]="Q31605";
$cate["008002005000000"]="Q31605";
$cate["008002006000000"]="Q31605";
$cate["008002007000000"]="Q31605";
$cate["008002008000000"]="Q31605";
$cate["008002009000000"]="Q31605";
$cate["008002010000000"]="Q31605";
$cate["008002023000000"]="Q31606";
$cate["008002013000000"]="Q31606";
$cate["008002011000000"]="Q31606";
$cate["008002012000000"]="Q31606";
$cate["008002022000000"]="Q31606";
$cate["008002021000000"]="Q31606";
$cate["008002020000000"]="Q31606";
$cate["008002014000000"]="Q31606";
$cate["008002019000000"]="Q31606";
$cate["008008008000000"]="Q50803";
$cate["008008010000000"]="Q50804";
$cate["008008006000000"]="Q50804";
$cate["008008007000000"]="Q50804";
$cate["008008004000000"]="Q50804";
$cate["008008003000000"]="Q50804";
$cate["008008005000000"]="Q50804";
$cate["008008001000000"]="Q50804";
$cate["008008002000000"]="Q50804";
$cate["008008009000000"]="Q50805";
$cate["005012008000000"]="PF0111";
$cate["005012002000000"]="PF0111";
$cate["005012003000000"]="PF0111";
$cate["005012005000000"]="PF0111";
$cate["005012004000000"]="PF0111";
$cate["005012007000000"]="PF0111";
$cate["005012006000000"]="PF0111";
$cate["005012001000000"]="PF0208";
$cate["005001002000000"]="PO0501";
$cate["005001001000000"]="PO0501";
$cate["005002001000000"]="PH0301";
$cate["005002002000000"]="PH0301";
$cate["005002004000000"]="PH0302";
$cate["005002007000000"]="PH0302";
$cate["005002008000000"]="PH0302";
$cate["005002005000000"]="PH0302";
$cate["005002006000000"]="PH0302";
$cate["005002003000000"]="PH0302";
$cate["005005002000000"]="PL0137";
$cate["005005001000000"]="PL0137";
$cate["005005003000000"]="PL0137";
$cate["005005005000000"]="PL0137";
$cate["005005006000000"]="PL0240";
$cate["005005004000000"]="Q30502";
$cate["005003008000000"]="Q51601";
$cate["005003005000000"]="Q51601";
$cate["005003007000000"]="Q51601";
$cate["005003001000000"]="Q51606";
$cate["005003006000000"]="Q51609";
$cate["005003009000000"]="Q51609";
$cate["005003002000000"]="Q51609";
$cate["005003004000000"]="Q51609";
$cate["005003003000000"]="Q51609";
$cate["005006005000000"]="PB0401";
$cate["005006001000000"]="PE0501";
$cate["005006002000000"]="PE0501";
$cate["005006004000000"]="PE0501";
$cate["005006003000000"]="PE0501";
$cate["005013007000000"]="PJ0901";
$cate["005013016000000"]="PJ0901";
$cate["005013010000000"]="PJ0901";
$cate["005013002000000"]="PJ0901";
$cate["005013012000000"]="PJ0901";
$cate["005013008000000"]="PJ0901";
$cate["005013011000000"]="PJ0901";
$cate["005013006000000"]="PJ0901";
$cate["005013001000000"]="PJ0901";
$cate["005013015000000"]="PJ0901";
$cate["005013009000000"]="PJ0901";
$cate["005013004000000"]="PJ0901";
$cate["005013014000000"]="PJ0901";
$cate["005013005000000"]="PJ0901";
$cate["005013013000000"]="PJ0901";
$cate["005013003000000"]="PJ0901";
$cate["005007002000000"]="PC0401";
$cate["005007001000000"]="PC0402";
$cate["005011010000000"]="PF1101";
$cate["005011011000000"]="PF1101";
$cate["005011008000000"]="PF1101";
$cate["005011002000000"]="PF1101";
$cate["005011009000000"]="PF1101";
$cate["005011001000000"]="PF1101";
$cate["005011004000000"]="PF1101";
$cate["005011007000000"]="PF1101";
$cate["005011003000000"]="PF1101";
$cate["005011005000000"]="PF1101";
$cate["005011006000000"]="PF1101";
$cate["005008001000000"]="PB0401";
$cate["005008002000000"]="PB0401";
$cate["005009001000000"]="유아동패션";
$cate["005010001000000"]="유아동패션";
$cate["005004005000000"]="PB0111";
$cate["005004003000000"]="PB0111";
$cate["005004004000000"]="PB0111";
$cate["005004001000000"]="PB0111";
$cate["005004002000000"]="PB0111";
$cate["003013001000000"]="Q30501";
$cate["003013002000000"]="Q30502";
$cate["003013005000000"]="Q30502";
$cate["003013006000000"]="Q30502";
$cate["003013007000000"]="Q30502";
$cate["003013004000000"]="Q30503";
$cate["003013003000000"]="Q30504";
$cate["003012008000000"]="Q80101";
$cate["003012005000000"]="Q80101";
$cate["003012001000000"]="Q80101";
$cate["003012006000000"]="Q80101";
$cate["003012004000000"]="Q80101";
$cate["003012002000000"]="Q80101";
$cate["003012003000000"]="Q80101";
$cate["003012007000000"]="Q80101";
$cate["003008006000000"]="Q10404";
$cate["003008001000000"]="Q10507";
$cate["003008002000000"]="Q10507";
$cate["003008003000000"]="Q10507";
$cate["003008005000000"]="Q10507";
$cate["003008004000000"]="Q10510";
$cate["003008007000000"]="Q10515";
$cate["003008011000000"]="Q30437";
$cate["003008010000000"]="Q30437";
$cate["003008009000000"]="Q30437";
$cate["003008008000000"]="Q30437";
$cate["003010006000000"]="Q80201";
$cate["003010001000000"]="Q80201";
$cate["003010009000000"]="Q80201";
$cate["003010003000000"]="Q80201";
$cate["003010008000000"]="Q80201";
$cate["003010007000000"]="Q80201";
$cate["003010004000000"]="Q80201";
$cate["003010005000000"]="Q80201";
$cate["003010002000000"]="Q80201";
$cate["003001012000000"]="Q10216";
$cate["003001007000000"]="Q10502";
$cate["003001011000000"]="Q10503";
$cate["003001001000000"]="Q10504";
$cate["003001002000000"]="Q10504";
$cate["003001003000000"]="Q10504";
$cate["003001004000000"]="Q10504";
$cate["003001005000000"]="Q10504";
$cate["003001006000000"]="Q10504";
$cate["003001010000000"]="Q10504";
$cate["003001014000000"]="Q10504";
$cate["003001009000000"]="Q10506";
$cate["003001013000000"]="Q10509";
$cate["003001008000000"]="Q10511";
$cate["003002001000000"]="Q10210";
$cate["003002002000000"]="Q10210";
$cate["003002005000000"]="Q10211";
$cate["003002003000000"]="Q10212";
$cate["003002004000000"]="Q10212";
$cate["003002008000000"]="Q10214";
$cate["003002009000000"]="Q10215";
$cate["003002007000000"]="Q10234";
$cate["003002014000000"]="Q10234";
$cate["003002006000000"]="Q10234";
$cate["003002011000000"]="Q10403";
$cate["003002012000000"]="Q10403";
$cate["003002010000000"]="Q10403";
$cate["003002013000000"]="Q10403";
$cate["003007013000000"]="Q10208";
$cate["003007014000000"]="Q10208";
$cate["003007015000000"]="Q10208";
$cate["003007017000000"]="Q10208";
$cate["003007018000000"]="Q10208";
$cate["003007019000000"]="Q10208";
$cate["003007020000000"]="Q10208";
$cate["003007021000000"]="Q10208";
$cate["003007008000000"]="Q10239";
$cate["003007011000000"]="Q10239";
$cate["003007009000000"]="Q10239";
$cate["003007001000000"]="Q10239";
$cate["003007004000000"]="Q10239";
$cate["003007003000000"]="Q10239";
$cate["003007002000000"]="Q10239";
$cate["003007016000000"]="Q10239";
$cate["003007007000000"]="Q10239";
$cate["003007012000000"]="Q10239";
$cate["003007006000000"]="Q10239";
$cate["003007010000000"]="Q10239";
$cate["003007005000000"]="Q10239";
$cate["003004019000000"]="Q10201";
$cate["003004020000000"]="Q10201";
$cate["003004022000000"]="Q10201";
$cate["003004023000000"]="Q10201";
$cate["003004018000000"]="Q10221";
$cate["003004021000000"]="Q10223";
$cate["003004014000000"]="Q10224";
$cate["003004012000000"]="Q10225";
$cate["003004013000000"]="Q10225";
$cate["003004015000000"]="Q10225";
$cate["003004017000000"]="Q10228";
$cate["003004016000000"]="Q10229";
$cate["003004024000000"]="Q10239";
$cate["003004004000000"]="Q10401";
$cate["003004005000000"]="Q10402";
$cate["003004003000000"]="Q10404";
$cate["003004008000000"]="Q10404";
$cate["003004009000000"]="Q10404";
$cate["003004010000000"]="Q10404";
$cate["003004011000000"]="Q10404";
$cate["003004006000000"]="Q10405";
$cate["003004001000000"]="Q10406";
$cate["003004002000000"]="Q10407";
$cate["003004007000000"]="Q10407";
$cate["003005015000000"]="Q10236";
$cate["003005009000000"]="Q10236";
$cate["003005012000000"]="Q10236";
$cate["003005010000000"]="Q10236";
$cate["003005013000000"]="Q10236";
$cate["003005011000000"]="Q10236";
$cate["003005002000000"]="Q10236";
$cate["003005004000000"]="Q10236";
$cate["003005005000000"]="Q10236";
$cate["003005014000000"]="Q10408";
$cate["003005003000000"]="Q10504";
$cate["003005006000000"]="Q10505";
$cate["003005001000000"]="Q10508";
$cate["003005007000000"]="Q10512";
$cate["003005008000000"]="Q20708";
$cate["003006010000000"]="Q10238";
$cate["003006012000000"]="Q10238";
$cate["003006011000000"]="Q10238";
$cate["003006007000000"]="Q10238";
$cate["003006002000000"]="Q10238";
$cate["003006001000000"]="Q10238";
$cate["003006005000000"]="Q10238";
$cate["003006004000000"]="Q10238";
$cate["003006015000000"]="Q10240";
$cate["003006016000000"]="Q10240";
$cate["003006014000000"]="Q10240";
$cate["003006013000000"]="Q10240";
$cate["003006008000000"]="Q10514";
$cate["003006009000000"]="Q10514";
$cate["003006006000000"]="Q10708";
$cate["003006003000000"]="Q31407";
$cate["003009002000000"]="Q80301";
$cate["003009003000000"]="Q80301";
$cate["003009001000000"]="Q80301";
$cate["003009004000000"]="Q80301";
$cate["003003001000000"]="Q10217";
$cate["003003002000000"]="Q10217";
$cate["003003003000000"]="Q10217";
$cate["003003005000000"]="Q10217";
$cate["003003006000000"]="Q10217";
$cate["003003009000000"]="Q10217";
$cate["003003010000000"]="Q10217";
$cate["003003012000000"]="Q10217";
$cate["003003004000000"]="Q10218";
$cate["003003007000000"]="Q10218";
$cate["003003011000000"]="Q10219";
$cate["003003008000000"]="Q10220";
$cate["003014001000000"]="Q30510";
$cate["003014002000000"]="Q30510";
$cate["003014003000000"]="Q30510";
$cate["003014004000000"]="Q30510";
$cate["003014005000000"]="Q30510";
$cate["003014006000000"]="Q30510";
$cate["003011003000000"]="Q31606";
$cate["003011004000000"]="Q31606";
$cate["003011005000000"]="Q80401";
$cate["003011007000000"]="Q80401";
$cate["003011006000000"]="Q80401";
$cate["003011002000000"]="Q80401";
$cate["003011001000000"]="Q80401";
$cate["002006006000000"]="Q10316";
$cate["002006004000000"]="Q20301";
$cate["002006005000000"]="Q20301";
$cate["002006001000000"]="Q20303";
$cate["002006003000000"]="Q20303";
$cate["002006007000000"]="Q20304";
$cate["002006008000000"]="Q20304";
$cate["002008010000000"]="Q20505";
$cate["002008001000000"]="Q20703";
$cate["002008002000000"]="Q20704";
$cate["002008005000000"]="Q20705";
$cate["002008007000000"]="Q20710";
$cate["002008009000000"]="Q20710";
$cate["002008003000000"]="Q20710";
$cate["002008008000000"]="Q20710";
$cate["002008006000000"]="Q20710";
$cate["002005001000000"]="Q20117";
$cate["002005002000000"]="Q20117";
$cate["002005004000000"]="Q20117";
$cate["002005005000000"]="Q20117";
$cate["002005006000000"]="Q20117";
$cate["002005007000000"]="Q20117";
$cate["002005008000000"]="Q20501";
$cate["002005009000000"]="Q20503";
$cate["002005003000000"]="Q20506";
$cate["002002001000000"]="Q20202";
$cate["002002002000000"]="Q20202";
$cate["002002003000000"]="Q20202";
$cate["002002004000000"]="Q20202";
$cate["002002005000000"]="Q20202";
$cate["002009006000000"]="Q20119";
$cate["002009005000000"]="Q20401";
$cate["002009003000000"]="Q20404";
$cate["002009001000000"]="Q20405";
$cate["002009004000000"]="Q20405";
$cate["002009002000000"]="Q20406";
$cate["002003003000000"]="Q20102";
$cate["002003005000000"]="Q20110";
$cate["002003001000000"]="Q20111";
$cate["002003004000000"]="Q20112";
$cate["002003006000000"]="Q20118";
$cate["002003007000000"]="Q20118";
$cate["002003008000000"]="Q20119";
$cate["002003009000000"]="Q20119";
$cate["002003010000000"]="Q20504";
$cate["002003002000000"]="Q20604";
$cate["002004001000000"]="Q20101";
$cate["002004002000000"]="Q20101";
$cate["002004003000000"]="Q20101";
$cate["002004004000000"]="Q20101";
$cate["002004005000000"]="Q20101";
$cate["002004006000000"]="Q20101";
$cate["002004007000000"]="Q20101";
$cate["002004008000000"]="Q20101";
$cate["002001002000000"]="Q20201";
$cate["002001003000000"]="Q20201";
$cate["002001007000000"]="Q20201";
$cate["002001008000000"]="Q20201";
$cate["002001006000000"]="Q20203";
$cate["002001004000000"]="Q20204";
$cate["002001005000000"]="Q20205";
$cate["002001001000000"]="Q20207";
$cate["002001009000000"]="Q20208";
$cate["002001010000000"]="Q20212";
$cate["002001011000000"]="Q20212";
$cate["002007002000000"]="Q20701";
$cate["002007003000000"]="Q20701";
$cate["002007004000000"]="Q20701";
$cate["002007005000000"]="Q20701";
$cate["002007006000000"]="Q20701";
$cate["002007009000000"]="Q20701";
$cate["002007008000000"]="Q20702";
$cate["002007001000000"]="Q20706";
$cate["002007007000000"]="Q20709";

$code["Q00101"]="가구/인테리어>침실가구>침실가구세트";
$code["Q00102"]="가구/인테리어>침실가구>침대";
$code["Q00103"]="가구/인테리어>침실가구>쇼파";
$code["Q00104"]="가구/인테리어>침실가구>테이블";
$code["Q00105"]="가구/인테리어>침실가구>서랍장";
$code["Q00106"]="가구/인테리어>침실가구>옷장";
$code["Q00107"]="가구/인테리어>침실가구>화장대";
$code["Q00108"]="가구/인테리어>침실가구>매트리스";
$code["Q00201"]="가구/인테리어>주방가구>식탁";
$code["Q00202"]="가구/인테리어>주방가구>식탁의자";
$code["Q00203"]="가구/인테리어>주방가구>쌀통";
$code["Q00204"]="가구/인테리어>주방가구>교자상";
$code["Q00205"]="가구/인테리어>주방가구>렌지대";
$code["Q00206"]="가구/인테리어>주방가구>홈바/아일랜드식탁";
$code["Q00207"]="가구/인테리어>주방가구>수납장";
$code["Q00208"]="가구/인테리어>주방가구>테이블";
$code["Q00301"]="가구/인테리어>거실가구>쇼파";
$code["Q00302"]="가구/인테리어>거실가구>협탁";
$code["Q00303"]="가구/인테리어>거실가구>거실장";
$code["Q00304"]="가구/인테리어>거실가구>TV장";
$code["Q00305"]="가구/인테리어>거실가구>테이블";
$code["Q00306"]="가구/인테리어>거실가구>의자";
$code["Q00307"]="가구/인테리어>거실가구>장식장";
$code["Q00401"]="가구/인테리어>학생/서재가구>책상";
$code["Q00402"]="가구/인테리어>학생/서재가구>의자";
$code["Q00403"]="가구/인테리어>학생/서재가구>책장";
$code["Q00404"]="가구/인테리어>학생/서재가구>세트상품";
$code["Q00405"]="가구/인테리어>학생/서재가구>서랍장";
$code["Q00406"]="가구/인테리어>학생/서재가구>기타소품";
$code["Q00502"]="가구/인테리어>수납가구>수납박스/정리함";
$code["Q00503"]="가구/인테리어>수납가구>수납장";
$code["Q00504"]="가구/인테리어>수납가구>선반/코너장";
$code["Q00505"]="가구/인테리어>수납가구>수납소품";
$code["Q00506"]="가구/인테리어>수납가구>메탈랙/철재선반";
$code["Q00507"]="가구/인테리어>수납가구>이동형옷장/서랍장";
$code["Q00509"]="가구/인테리어>수납가구>CD/DVD장";
$code["Q00601"]="가구/인테리어>인테리어>시계";
$code["Q00602"]="가구/인테리어>인테리어>거울";
$code["Q00603"]="가구/인테리어>인테리어>액자";
$code["Q00604"]="가구/인테리어>인테리어>벽지/시트지";
$code["Q00605"]="가구/인테리어>인테리어>캐릭터 상품";
$code["Q00606"]="가구/인테리어>인테리어>DIY인테리어";
$code["Q00607"]="가구/인테리어>인테리어>와인냉장고";
$code["Q00610"]="가구/인테리어>인테리어>방석";
$code["Q00612"]="가구/인테리어>인테리어>단스탠드";
$code["Q00613"]="가구/인테리어>인테리어>장스탠드";
$code["Q00614"]="가구/인테리어>인테리어>이니셜스탠드";
$code["Q00615"]="가구/인테리어>인테리어>등조명";
$code["Q00616"]="가구/인테리어>인테리어>화병";
$code["Q00617"]="가구/인테리어>인테리어>벽장식";
$code["Q00618"]="가구/인테리어>인테리어>실내분수";
$code["Q00619"]="가구/인테리어>인테리어>장식소품";
$code["Q00621"]="가구/인테리어>인테리어>전기조명/램프";
$code["Q00701"]="가구/인테리어>유아/주니어가구>침대";
$code["Q00702"]="가구/인테리어>유아/주니어가구>책상";
$code["Q00703"]="가구/인테리어>유아/주니어가구>수납장";
$code["Q00704"]="가구/인테리어>유아/주니어가구>의자";
$code["Q00705"]="가구/인테리어>유아/주니어가구>세트상품";
$code["Q00706"]="가구/인테리어>유아/주니어가구>서랍장";
$code["Q00707"]="가구/인테리어>유아/주니어가구>기타소품";
$code["Q00801"]="가구/인테리어>벽지/시트지>포인트벽지";
$code["Q00802"]="가구/인테리어>벽지/시트지>베이스벽지";
$code["Q00803"]="가구/인테리어>벽지/시트지>주방벽지";
$code["Q00804"]="가구/인테리어>벽지/시트지>아이방벽지";
$code["Q00810"]="가구/인테리어>벽지/시트지>부자재";
$code["Q00811"]="가구/인테리어>벽지/시트지>입체스티커";
$code["Q00901"]="가구/인테리어>행거/드레스룸>드레스룸";
$code["Q00902"]="가구/인테리어>행거/드레스룸>선반형행거";
$code["Q00903"]="가구/인테리어>행거/드레스룸>이동형행거";
$code["Q00904"]="가구/인테리어>행거/드레스룸>일반형행거";
$code["Q00905"]="가구/인테리어>행거/드레스룸>커버형행거";
$code["Q00906"]="가구/인테리어>행거/드레스룸>폴행거/옷걸이";
$code["Q00907"]="가구/인테리어>행거/드레스룸>기타행거류";
$code["Q01001"]="가구/인테리어>엔틱/고가구>엔틱침실가구";
$code["Q01002"]="가구/인테리어>엔틱/고가구>엔틱거실가구";
$code["Q01003"]="가구/인테리어>엔틱/고가구>엔틱주방가구";
$code["Q01004"]="가구/인테리어>엔틱/고가구>엔틱소품가구";
$code["Q10101"]="주방/욕실>주방가전>오븐기";
$code["Q10102"]="주방/욕실>주방가전>전자레인지";
$code["Q10103"]="주방/욕실>주방가전>압력/전기밥솥";
$code["Q10104"]="주방/욕실>주방가전>전기포트/전열기기";
$code["Q10106"]="주방/욕실>주방가전>믹서기";
$code["Q10107"]="주방/욕실>주방가전>토스터";
$code["Q10108"]="주방/욕실>주방가전>제빵기";
$code["Q10109"]="주방/욕실>주방가전>커피메이커";
$code["Q10110"]="주방/욕실>주방가전>음식물처리기";
$code["Q10111"]="주방/욕실>주방가전>가스기기";
$code["Q10112"]="주방/욕실>주방가전>가스오븐레인지";
$code["Q10113"]="주방/욕실>주방가전>전기그릴";
$code["Q10114"]="주방/욕실>주방가전>전기냄비/찜기/팬/쿠커";
$code["Q10115"]="주방/욕실>주방가전>쥬서기/녹즙기/약탕기";
$code["Q10116"]="주방/욕실>주방가전>식기세척기";
$code["Q10117"]="주방/욕실>주방가전>살균/건조기";
$code["Q10119"]="주방/욕실>주방가전>요구르트/청국장제조기";
$code["Q10201"]="주방/욕실>주방용품>후라이팬";
$code["Q10208"]="주방/욕실>주방용품>커피/와인용품";
$code["Q10209"]="주방/욕실>주방용품>압력밥솥";
$code["Q10210"]="주방/욕실>주방용품>공기/대접";
$code["Q10211"]="주방/욕실>주방용품>면기/볼/채반";
$code["Q10212"]="주방/욕실>주방용품>접시/찬기";
$code["Q10213"]="주방/욕실>주방용품>반상기/디너세트";
$code["Q10214"]="주방/욕실>주방용품>홈세트";
$code["Q10215"]="주방/욕실>주방용품>유아동식기";
$code["Q10216"]="주방/욕실>주방용품>물병";
$code["Q10217"]="주방/욕실>주방용품>컵/잔";
$code["Q10218"]="주방/욕실>주방용품>커피잔/머그";
$code["Q10219"]="주방/욕실>주방용품>와인글라스";
$code["Q10220"]="주방/욕실>주방용품>다기/주기";
$code["Q10221"]="주방/욕실>주방용품>주전자/티포트";
$code["Q10222"]="주방/욕실>주방용품>주방세트상품";
$code["Q10223"]="주방/욕실>주방용품>궁중팬/튀김팬";
$code["Q10224"]="주방/욕실>주방용품>편수냄비";
$code["Q10225"]="주방/욕실>주방용품>양수냄비";
$code["Q10226"]="주방/욕실>주방용품>전골냄비";
$code["Q10227"]="주방/욕실>주방용품>법랑/내열냄비";
$code["Q10228"]="주방/욕실>주방용품>직화냄비/곰솥";
$code["Q10229"]="주방/욕실>주방용품>찜기/뚝배기";
$code["Q10230"]="주방/욕실>주방용품>돌솥/가마솥";
$code["Q10231"]="주방/욕실>주방용품>전기팬/전기그릴";
$code["Q10232"]="주방/욕실>주방용품>양면팬/구이팬/불판";
$code["Q10233"]="주방/욕실>주방용품>뚜껑/손잡이/패킹";
$code["Q10301"]="주방/욕실>욕실/위생용품>비데/연수기";
$code["Q10302"]="주방/욕실>욕실/위생용품>욕실잡화";
$code["Q10305"]="주방/욕실>욕실/위생용품>대야/바가지";
$code["Q10306"]="주방/욕실>욕실/위생용품>반신욕용품";
$code["Q10307"]="주방/욕실>욕실/위생용품>방향탈취제";
$code["Q10308"]="주방/욕실>욕실/위생용품>변기청소용품";
$code["Q10309"]="주방/욕실>욕실/위생용품>변기커버";
$code["Q10311"]="주방/욕실>욕실/위생용품>샤워가운";
$code["Q10312"]="주방/욕실>욕실/위생용품>샤워커튼/봉";
$code["Q10313"]="주방/욕실>욕실/위생용품>샤워호스";
$code["Q10314"]="주방/욕실>욕실/위생용품>세면/세제용품";
$code["Q10315"]="주방/욕실>욕실/위생용품>수건/타올";
$code["Q10316"]="주방/욕실>욕실/위생용품>욕실매트/발판";
$code["Q10317"]="주방/욕실>욕실/위생용품>욕실수납/선반장";
$code["Q10318"]="주방/욕실>욕실/위생용품>칫솔걸이/칫솔살균기";
$code["Q10401"]="주방/욕실>주방조리도구>국자/뒤집게/집게";
$code["Q10402"]="주방/욕실>주방조리도구>주걱/가위";
$code["Q10403"]="주방/욕실>주방조리도구>숟가락/젓가락/포크";
$code["Q10404"]="주방/욕실>주방조리도구>기타조리도구";
$code["Q10405"]="주방/욕실>주방조리도구>조리도구세트";
$code["Q10406"]="주방/욕실>주방조리도구>도마";
$code["Q10407"]="주방/욕실>주방조리도구>칼/채칼";
$code["Q10408"]="주방/욕실>주방조리도구>주방저울/쿠킹타이머";
$code["Q10409"]="주방/욕실>주방조리도구>DIY용품";
$code["Q10501"]="주방/욕실>주방보관수납>기타주방소품";
$code["Q10502"]="주방/욕실>주방보관수납>김치통";
$code["Q10503"]="주방/욕실>주방보관수납>도시락/찬합";
$code["Q10504"]="주방/욕실>주방보관수납>밀폐/보관용기";
$code["Q10505"]="주방/욕실>주방보관수납>반찬통/수저통";
$code["Q10506"]="주방/욕실>주방보관수납>보관수납기타";
$code["Q10507"]="주방/욕실>주방보관수납>보온/보냉병/도시락";
$code["Q10508"]="주방/욕실>주방보관수납>식기건조대/선반";
$code["Q10509"]="주방/욕실>주방보관수납>쌀통/항아리";
$code["Q10510"]="주방/욕실>주방보관수납>아이스박스/야외용품";
$code["Q10511"]="주방/욕실>주방보관수납>양념통";
$code["Q10512"]="주방/욕실>주방보관수납>쟁반";
$code["Q10513"]="주방/욕실>주방보관수납>진공포장기";
$code["Q10514"]="주방/욕실>주방보관수납>호일/비닐팩";
$code["Q10515"]="주방/욕실>주방보관수납>교자상";
$code["Q10601"]="주방/욕실>세탁용품>세탁용품";
$code["Q10602"]="주방/욕실>세탁용품>보풀제거기";
$code["Q10603"]="주방/욕실>세탁용품>빨래건조대";
$code["Q10604"]="주방/욕실>세탁용품>빨래바구기/수납";
$code["Q10605"]="주방/욕실>세탁용품>다림판/관련용품";
$code["Q10606"]="주방/욕실>세탁용품>빨래판/빨래솥";
$code["Q10607"]="주방/욕실>세탁용품>세탁망/세탁볼";
$code["Q10701"]="주방/욕실>청소용품>청소용품";
$code["Q10702"]="주방/욕실>청소용품>매직블럭/크리너";
$code["Q10703"]="주방/욕실>청소용품>밀대/패드";
$code["Q10704"]="주방/욕실>청소용품>분리수거함/도구함";
$code["Q10705"]="주방/욕실>청소용품>빗자루/쓰레받이";
$code["Q10706"]="주방/욕실>청소용품>스팀청소/핸디형청소기";
$code["Q10707"]="주방/욕실>청소용품>압축휴지통";
$code["Q10708"]="주방/욕실>청소용품>음식물쓰레기통";
$code["Q10709"]="주방/욕실>청소용품>음식물처리기";
$code["Q20101"]="침구/커튼/카페트>침구류>침구세트";
$code["Q20102"]="침구/커튼/카페트>침구류>침구단품";
$code["Q20108"]="침구/커튼/카페트>침구류>이불커버세트";
$code["Q20109"]="침구/커튼/카페트>침구류>매트커버세트";
$code["Q20110"]="침구/커튼/카페트>침구류>침대스커트/매트리스커버";
$code["Q20111"]="침구/커튼/카페트>침구류>차렵이불";
$code["Q20112"]="침구/커튼/카페트>침구류>패드";
$code["Q20114"]="침구/커튼/카페트>침구류>차렵패드세트";
$code["Q20115"]="침구/커튼/카페트>침구류>요차렵세트";
$code["Q20116"]="침구/커튼/카페트>침구류>침구+커튼세트";
$code["Q20117"]="침구/커튼/카페트>침구류>베개/베개커버";
$code["Q20118"]="침구/커튼/카페트>침구류>요커버/이불커버";
$code["Q20119"]="침구/커튼/카페트>침구류>담요/무릎담요";
$code["Q20201"]="침구/커튼/카페트>커튼류>커튼";
$code["Q20202"]="침구/커튼/카페트>커튼류>로만/블라인드";
$code["Q20203"]="침구/커튼/카페트>커튼류>발란스";
$code["Q20204"]="침구/커튼/카페트>커튼류>속커튼";
$code["Q20205"]="침구/커튼/카페트>커튼류>로만쉐이드";
$code["Q20206"]="침구/커튼/카페트>커튼류>버디컬/허니컴";
$code["Q20207"]="침구/커튼/카페트>커튼류>암막커튼";
$code["Q20208"]="침구/커튼/카페트>커튼류>비즈발";
$code["Q20209"]="침구/커튼/카페트>커튼류>오완식커튼";
$code["Q20210"]="침구/커튼/카페트>커튼류>캐노피/모기장";
$code["Q20211"]="침구/커튼/카페트>커튼류>커튼패키지세트";
$code["Q20212"]="침구/커튼/카페트>커튼류>커튼소품";
$code["Q20301"]="침구/커튼/카페트>카페트/옥매트>카페트";
$code["Q20302"]="침구/커튼/카페트>카페트/옥매트>옥매트";
$code["Q20303"]="침구/커튼/카페트>카페트/옥매트>러그";
$code["Q20304"]="침구/커튼/카페트>카페트/옥매트>대자리/우드";
$code["Q20401"]="침구/커튼/카페트>유아동침구>아동베개/쿠션";
$code["Q20402"]="침구/커튼/카페트>유아동침구>아동매트커버세트";
$code["Q20403"]="침구/커튼/카페트>유아동침구>아동침대커버세트";
$code["Q20404"]="침구/커튼/카페트>유아동침구>아동요/이불세트";
$code["Q20405"]="침구/커튼/카페트>유아동침구>아동침구패드";
$code["Q20406"]="침구/커튼/카페트>유아동침구>아동커튼";
$code["Q20407"]="침구/커튼/카페트>유아동침구>아동침구기타";
$code["Q20501"]="침구/커튼/카페트>기능성침구>메모리/기능성베개";
$code["Q20502"]="침구/커튼/카페트>기능성침구>기능성매트";
$code["Q20503"]="침구/커튼/카페트>기능성침구>베개솜/속통/기타";
$code["Q20504"]="침구/커튼/카페트>기능성침구>요솜/이불솜/매트솜";
$code["Q20505"]="침구/커튼/카페트>기능성침구>쿠션솜/방석솜";
$code["Q20506"]="침구/커튼/카페트>기능성침구>항균/방수커버";
$code["Q20601"]="침구/커튼/카페트>계절침구>거위털/오리털침구";
$code["Q20602"]="침구/커튼/카페트>계절침구>극세사침구세트";
$code["Q20604"]="침구/커튼/카페트>계절침구>리플/인견/모시침구";
$code["Q20605"]="침구/커튼/카페트>계절침구>양모침구";
$code["Q20606"]="침구/커튼/카페트>계절침구>극세사 패드/이불";
$code["Q20701"]="침구/커튼/카페트>쿠션/수예>기타커버류";
$code["Q20702"]="침구/커튼/카페트>쿠션/수예>슬리퍼";
$code["Q20703"]="침구/커튼/카페트>쿠션/수예>대방석/대쿠션";
$code["Q20704"]="침구/커튼/카페트>쿠션/수예>방석/미니쿠션";
$code["Q20705"]="침구/커튼/카페트>쿠션/수예>헤드쿠션";
$code["Q20706"]="침구/커튼/카페트>쿠션/수예>소파커버";
$code["Q20707"]="침구/커튼/카페트>쿠션/수예>수예소품";
$code["Q20708"]="침구/커튼/카페트>쿠션/수예>앞치마/주방장갑";
$code["Q20709"]="침구/커튼/카페트>쿠션/수예>식탁보";
$code["Q30101"]="이미용/생활>이미용가전>헤어롤/고데기";
$code["Q30103"]="이미용/생활>이미용가전>드라이기";
$code["Q30104"]="이미용/생활>이미용가전>미용관리기기";
$code["Q30105"]="이미용/생활>이미용가전>면도기";
$code["Q30106"]="이미용/생활>이미용가전>화장품냉장고";
$code["Q30107"]="이미용/생활>이미용가전>전동칫솔/살균건조기";
$code["Q30201"]="이미용/생활>이미용용품>샤워/바디용품";
$code["Q30202"]="이미용/생활>이미용용품>화장품";
$code["Q30301"]="이미용/생활>생활가전>청소기";
$code["Q30302"]="이미용/생활>생활가전>스팀청소기";
$code["Q30303"]="이미용/생활>생활가전>다리미";
$code["Q30306"]="이미용/생활>생활가전>공기청정기";
$code["Q30307"]="이미용/생활>생활가전>라디오";
$code["Q30308"]="이미용/생활>생활가전>MP3";
$code["Q30309"]="이미용/생활>생활가전>PMP";
$code["Q30310"]="이미용/생활>생활가전>디지털카메라";
$code["Q30311"]="이미용/생활>생활가전>가습기";
$code["Q30315"]="이미용/생활>생활가전>살균건조기";
$code["Q30318"]="이미용/생활>생활가전>미싱기";
$code["Q30319"]="이미용/생활>생활가전>보이스레코더";
$code["Q30320"]="이미용/생활>생활가전>스피커";
$code["Q30321"]="이미용/생활>생활가전>이어폰";
$code["Q30322"]="이미용/생활>생활가전>헤드폰";
$code["Q30323"]="이미용/생활>생활가전>도어락";
$code["Q30324"]="이미용/생활>생활가전>로봇청소기";
$code["Q30325"]="이미용/생활>생활가전>핸디형청소기";
$code["Q30326"]="이미용/생활>생활가전>전자사전";
$code["Q30327"]="이미용/생활>생활가전>노트북";
$code["Q30328"]="이미용/생활>생활가전>게임기";
$code["Q30332"]="이미용/생활>생활가전>기타가전";
$code["Q30333"]="이미용/생활>생활가전>재봉틀";
$code["Q30401"]="이미용/생활>생활용품>세탁/청소용품";
$code["Q30402"]="이미용/생활>생활용품>세제/화장지";
$code["Q30403"]="이미용/생활>생활용품>디지털도어락";
$code["Q30404"]="이미용/생활>생활용품>방범/보안용품";
$code["Q30405"]="이미용/생활>생활용품>아이디어용품";
$code["Q30406"]="이미용/생활>생활용품>생필품";
$code["Q30407"]="이미용/생활>생활용품>캐릭터가방";
$code["Q30408"]="이미용/생활>생활용품>아로마테라피";
$code["Q30409"]="이미용/생활>생활용품>앨범";
$code["Q30410"]="이미용/생활>생활용품>디자인용품";
$code["Q30411"]="이미용/생활>생활용품>데스크용품";
$code["Q30412"]="이미용/생활>생활용품>선물세트";
$code["Q30413"]="이미용/생활>생활용품>기타";
$code["Q30414"]="이미용/생활>생활용품>행거";
$code["Q30415"]="이미용/생활>생활용품>옷걸이";
$code["Q30416"]="이미용/생활>생활용품>크리스마스용품";
$code["Q30417"]="이미용/생활>생활용품>금고";
$code["Q30418"]="이미용/생활>생활용품>가공식품";
$code["Q30419"]="이미용/생활>생활용품>대자리/돗자리";
$code["Q30420"]="이미용/생활>생활용품>미니텐트";
$code["Q30421"]="이미용/생활>생활용품>문풍지";
$code["Q30422"]="이미용/생활>생활용품>해충퇴치";
$code["Q30423"]="이미용/생활>생활용품>우산";
$code["Q30424"]="이미용/생활>생활용품>유모차";
$code["Q30425"]="이미용/생활>생활용품>카시트";
$code["Q30426"]="이미용/생활>생활용품>공구용품";
$code["Q30427"]="이미용/생활>생활용품>애완용품";
$code["Q30501"]="이미용/생활>헬스용품>안마/맛사지기기";
$code["Q30502"]="이미용/생활>헬스용품>건강관리기";
$code["Q30503"]="이미용/생활>헬스용품>의료기";
$code["Q30504"]="이미용/생활>헬스용품>체중계";
$code["Q30505"]="이미용/생활>헬스용품>체온계";
$code["Q30506"]="이미용/생활>헬스용품>혈압계";
$code["Q30507"]="이미용/생활>헬스용품>만보계";
$code["Q30508"]="이미용/생활>헬스용품>족탕기/각탕기";
$code["Q30509"]="이미용/생활>헬스용품>좌훈/좌욕기";
$code["Q30510"]="이미용/생활>헬스용품>헬스용품";
$code["Q30701"]="이미용/생활>패션용품>캐릭터가방&지갑";
$code["Q31001"]="이미용/생활>계절가전>히터기";
$code["Q31002"]="이미용/생활>계절가전>난방기";
$code["Q31003"]="이미용/생활>계절가전>손난로";
$code["Q31004"]="이미용/생활>계절가전>핫팩";
$code["Q31005"]="이미용/생활>계절가전>선풍기";
$code["Q31006"]="이미용/생활>계절가전>온풍기";
$code["Q31007"]="이미용/생활>계절가전>냉풍기";
$code["Q31008"]="이미용/생활>계절가전>온수매트";
$code["Q31009"]="이미용/생활>계절가전>전기요매트";
$code["Q31010"]="이미용/생활>계절가전>전기방석";
$code["Q31101"]="이미용/생활>수납/정리>메탈랙/웨건";
$code["Q31102"]="이미용/생활>수납/정리>수납/정리용품";
$code["Q31103"]="이미용/생활>수납/정리>부직포정리함/언더베드";
$code["Q31104"]="이미용/생활>수납/정리>종이정리박스";
$code["Q31105"]="이미용/생활>수납/정리>플라스틱/리빙박스";
$code["Q31106"]="이미용/생활>수납/정리>옷커버/압축팩";
$code["Q31107"]="이미용/생활>수납/정리>선반";
$code["Q31108"]="이미용/생활>수납/정리>신발장";
$code["Q31109"]="이미용/생활>수납/정리>화분정리대";
$code["Q31201"]="이미용/생활>선물세트>과일선물세트";
$code["Q31202"]="이미용/생활>선물세트>건어물세트";
$code["Q31203"]="이미용/생활>선물세트>떡/한과/화과자세트";
$code["Q31204"]="이미용/생활>선물세트>생활선물세트";
$code["Q31205"]="이미용/생활>선물세트>식품선물세트";
$code["Q31206"]="이미용/생활>선물세트>기타선물세트";
$code["Q31301"]="이미용/생활>가공식품>슈퍼마켓";
$code["Q31302"]="이미용/생활>가공식품>DIY식품";
$code["Q31303"]="이미용/생활>가공식품>초콜릿";
$code["Q31304"]="이미용/생활>가공식품>사탕";
$code["Q31305"]="이미용/생활>가공식품>커피/코코아";
$code["Q31306"]="이미용/생활>가공식품>오일";
$code["Q31307"]="이미용/생활>가공식품>비타민";
$code["Q31401"]="이미용/생활>생필품>치실/가글";
$code["Q31402"]="이미용/생활>생필품>바디케어";
$code["Q31403"]="이미용/생활>생필품>비누/세정제";
$code["Q31404"]="이미용/생활>생필품>샴푸/린스";
$code["Q31405"]="이미용/생활>생필품>치약/칫솔";
$code["Q31406"]="이미용/생활>생필품>일반세탁세제";
$code["Q31407"]="이미용/생활>생필품>주방/욕실세제";
$code["Q31408"]="이미용/생활>생필품>섬유유연제";
$code["Q31409"]="이미용/생활>생필품>섬유탈취제";
$code["Q31410"]="이미용/생활>생필품>방향제/습기제거/탈취제";
$code["Q31411"]="이미용/생활>생필품>물티슈/화장지/세제";
$code["Q31412"]="이미용/생활>생필품>방향제";
$code["Q31413"]="이미용/생활>생필품>생리대";
$code["Q31501"]="이미용/생활>디자인우산/양산>2/3단우산";
$code["Q31502"]="이미용/생활>디자인우산/양산>4/5단우산";
$code["Q31503"]="이미용/생활>디자인우산/양산>핸드페이팅/캐릭터우산";
$code["Q31504"]="이미용/생활>디자인우산/양산>장우산";
$code["Q31505"]="이미용/생활>디자인우산/양산>양산";
$code["Q31506"]="이미용/생활>디자인우산/양산>우산소품";
$code["Q31601"]="이미용/생활>홈베이킹>아이스크림만들기";
$code["Q31602"]="이미용/생활>홈베이킹>초콜릿만들기";
$code["Q31603"]="이미용/생활>홈베이킹>토핑재료";
$code["Q31604"]="이미용/생활>홈베이킹>포장재료";
$code["Q31605"]="이미용/생활>홈베이킹>조리도구";
$code["Q40101"]="문구&팬시>다이어리>일러스트";
$code["Q40102"]="문구&팬시>다이어리>캐릭터";
$code["Q40103"]="문구&팬시>다이어리>포토";
$code["Q40104"]="문구&팬시>다이어리>심플";
$code["Q40105"]="문구&팬시>다이어리>커버/리필/악세서리";
$code["Q40201"]="문구&팬시>스케줄러/캘린더>스케줄러";
$code["Q40202"]="문구&팬시>스케줄러/캘린더>캘린더";
$code["Q40301"]="문구&팬시>노트류>유선노트";
$code["Q40302"]="문구&팬시>노트류>메모지";
$code["Q40303"]="문구&팬시>노트류>체크리스트";
$code["Q40304"]="문구&팬시>노트류>캐쉬북";
$code["Q40305"]="문구&팬시>노트류>스터디북";
$code["Q40306"]="문구&팬시>노트류>러브북/맞춤동화";
$code["Q40307"]="문구&팬시>노트류>트래블북";
$code["Q40308"]="문구&팬시>노트류>티켓북";
$code["Q40309"]="문구&팬시>노트류>테마북";
$code["Q40310"]="문구&팬시>노트류>스크랩북";
$code["Q40311"]="문구&팬시>노트류>전화번호부";
$code["Q40312"]="문구&팬시>노트류>포스트잇";
$code["Q40313"]="문구&팬시>노트류>무선노트";
$code["Q40401"]="문구&팬시>필기류>연필/샤프";
$code["Q40402"]="문구&팬시>필기류>색연필";
$code["Q40403"]="문구&팬시>필기류>볼펜";
$code["Q40404"]="문구&팬시>필기류>만년필";
$code["Q40405"]="문구&팬시>필기류>기타";
$code["Q40501"]="문구&팬시>카드/편지지>메세지카드";
$code["Q40502"]="문구&팬시>카드/편지지>시즌카드";
$code["Q40503"]="문구&팬시>카드/편지지>엽서";
$code["Q40504"]="문구&팬시>카드/편지지>편지지/봉투";
$code["Q40505"]="문구&팬시>카드/편지지>일반카드";
$code["Q40506"]="문구&팬시>카드/편지지>고백카드";
$code["Q40507"]="문구&팬시>카드/편지지>생일/축하 카드";
$code["Q40508"]="문구&팬시>카드/편지지>감사카드";
$code["Q40509"]="문구&팬시>카드/편지지>크리스마스카드";
$code["Q40510"]="문구&팬시>카드/편지지>연하장";
$code["Q40601"]="문구&팬시>꾸미기용품>스티커";
$code["Q40602"]="문구&팬시>꾸미기용품>스탬프/도장";
$code["Q40603"]="문구&팬시>꾸미기용품>디자인 테이프";
$code["Q40604"]="문구&팬시>꾸미기용품>펀치";
$code["Q40605"]="문구&팬시>꾸미기용품>다이모";
$code["Q40606"]="문구&팬시>꾸미기용품>기타";
$code["Q40607"]="문구&팬시>꾸미기용품>잉크패드";
$code["Q40609"]="문구&팬시>꾸미기용품>응원상품";
$code["Q40701"]="문구&팬시>데스크용품>보드/메모판";
$code["Q40702"]="문구&팬시>데스크용품>클립/집게/마그넷";
$code["Q40703"]="문구&팬시>데스크용품>메모홀더/데스크 액자";
$code["Q40704"]="문구&팬시>데스크용품>펜홀더";
$code["Q40705"]="문구&팬시>데스크용품>계산기";
$code["Q40706"]="문구&팬시>데스크용품>데스크 매트";
$code["Q40707"]="문구&팬시>데스크용품>마우스/키보드 쿠션";
$code["Q40708"]="문구&팬시>데스크용품>북커버/북마크/북앤드";
$code["Q40709"]="문구&팬시>데스크용품>기타";
$code["Q40710"]="문구&팬시>데스크용품>독서대";
$code["Q40801"]="문구&팬시>보관/정리용품>펜슬케이스";
$code["Q40802"]="문구&팬시>보관/정리용품>파일/바인더";
$code["Q40803"]="문구&팬시>보관/정리용품>데스크정리함";
$code["Q40804"]="문구&팬시>보관/정리용품>기타";
$code["Q40901"]="문구&팬시>포장용품>포장박스";
$code["Q40902"]="문구&팬시>포장용품>포장지/포장봉투";
$code["Q40903"]="문구&팬시>포장용품>리본/기타";
$code["Q41001"]="문구&팬시>앨범>자착식";
$code["Q41002"]="문구&팬시>앨범>포켓식";
$code["Q41003"]="문구&팬시>앨범>주문식";
$code["Q41004"]="문구&팬시>앨범>사진보관함";
$code["Q41101"]="문구&팬시>디자이너용품>디자이너 서적";
$code["Q41102"]="문구&팬시>디자이너용품>포트폴리오";
$code["Q41103"]="문구&팬시>디자이너용품>화방용품";
$code["Q41201"]="문구&팬시>문구세트>세트";
$code["Q50101"]="키덜트&취미>카메라>로모카메라";
$code["Q50102"]="키덜트&취미>카메라>폴라로이드";
$code["Q50103"]="키덜트&취미>카메라>토이카메라";
$code["Q50104"]="키덜트&취미>카메라>필름";
$code["Q50105"]="키덜트&취미>카메라>렌즈";
$code["Q50106"]="키덜트&취미>카메라>카메라스트랩";
$code["Q50107"]="키덜트&취미>카메라>카메라케이스";
$code["Q50108"]="키덜트&취미>카메라>카메라북";
$code["Q50109"]="키덜트&취미>카메라>기타액세서리";
$code["Q50110"]="키덜트&취미>카메라>Pivi mp-300";
$code["Q50111"]="키덜트&취미>카메라>자동카메라";
$code["Q50112"]="키덜트&취미>카메라>클래식카메라";
$code["Q50113"]="키덜트&취미>카메라>디지털카메라";
$code["Q50114"]="키덜트&취미>카메라>방수카메라/팩";
$code["Q50115"]="키덜트&취미>카메라>삼각대";
$code["Q50201"]="키덜트&취미>닌텐도>닌텐도DS Lite";
$code["Q50202"]="키덜트&취미>닌텐도>스킨";
$code["Q50203"]="키덜트&취미>닌텐도>케이스";
$code["Q50204"]="키덜트&취미>닌텐도>기타액세서리";
$code["Q50205"]="키덜트&취미>닌텐도>닌텐도Wii";
$code["Q50206"]="키덜트&취미>닌텐도>DS Lite S/W";
$code["Q50207"]="키덜트&취미>닌텐도>Wii S/W";
$code["Q50301"]="키덜트&취미>도서>팝업북";
$code["Q50302"]="키덜트&취미>도서>아트북";
$code["Q50303"]="키덜트&취미>도서>일본생활잡지";
$code["Q50304"]="키덜트&취미>도서>아티스트북";
$code["Q50305"]="키덜트&취미>도서>인테리어북";
$code["Q50306"]="키덜트&취미>도서>심리학";
$code["Q50307"]="키덜트&취미>도서>여행/취미";
$code["Q50308"]="키덜트&취미>도서>일러스트북";
$code["Q50309"]="키덜트&취미>도서>요리";
$code["Q50401"]="키덜트&취미>ipod>ipod";
$code["Q50402"]="키덜트&취미>ipod>skin";
$code["Q50403"]="키덜트&취미>ipod>case";
$code["Q50404"]="키덜트&취미>ipod>speaker";
$code["Q50405"]="키덜트&취미>ipod>이어폰/헤드폰";
$code["Q50406"]="키덜트&취미>ipod>기타액세서리";
$code["Q50407"]="키덜트&취미>ipod>pod";
$code["Q50505"]="키덜트&취미>피규어>캐릭터피규어";
$code["Q50508"]="키덜트&취미>피규어>플레이모빌";
$code["Q50512"]="키덜트&취미>피규어>리볼텍";
$code["Q50521"]="키덜트&취미>피규어>리락쿠마";
$code["Q50522"]="키덜트&취미>피규어>뿌까";
$code["Q50601"]="키덜트&취미>인형>봉제인형";
$code["Q50602"]="키덜트&취미>인형>장식인형";
$code["Q50603"]="키덜트&취미>인형>메세지인형";
$code["Q50604"]="키덜트&취미>인형>두돌스";
$code["Q50605"]="키덜트&취미>인형>바비인형";
$code["Q50606"]="키덜트&취미>인형>기타인형";
$code["Q50701"]="키덜트&취미>장난감>노호혼";
$code["Q50702"]="키덜트&취미>장난감>플립플랩";
$code["Q50703"]="키덜트&취미>장난감>모션버드";
$code["Q50704"]="키덜트&취미>장난감>마네키네코";
$code["Q50705"]="키덜트&취미>장난감>작동완구";
$code["Q50706"]="키덜트&취미>장난감>기능성완구";
$code["Q50707"]="키덜트&취미>장난감>프라모델/RC";
$code["Q50711"]="키덜트&취미>장난감>완구";
$code["Q50801"]="키덜트&취미>아이디어/DIY>아이디어";
$code["Q50802"]="키덜트&취미>아이디어/DIY>DIY";
$code["Q50901"]="키덜트&취미>디지털용품>스피커";
$code["Q50902"]="키덜트&취미>디지털용품>이어폰";
$code["Q50903"]="키덜트&취미>디지털용품>헤드폰";
$code["Q50904"]="키덜트&취미>디지털용품>기타액세서리";
$code["Q50905"]="키덜트&취미>디지털용품>mp3";
$code["Q50906"]="키덜트&취미>디지털용품>pmp";
$code["Q50907"]="키덜트&취미>디지털용품>전자사전";
$code["Q50908"]="키덜트&취미>디지털용품>skin";
$code["Q50909"]="키덜트&취미>디지털용품>case";
$code["Q50910"]="키덜트&취미>디지털용품>보이스레코드";
$code["Q50911"]="키덜트&취미>디지털용품>블루투스";
$code["Q50912"]="키덜트&취미>디지털용품>디빅스플레이어";
$code["Q50914"]="키덜트&취미>디지털용품>휴대용게임기";
$code["Q51001"]="키덜트&취미>게임/퍼즐>보드게임";
$code["Q51002"]="키덜트&취미>게임/퍼즐>퍼즐";
$code["Q51101"]="키덜트&취미>여행용품>네임택";
$code["Q51102"]="키덜트&취미>여행용품>여권케이스";
$code["Q51103"]="키덜트&취미>여행용품>여행가방";
$code["Q51104"]="키덜트&취미>여행용품>여행소품";
$code["Q51105"]="키덜트&취미>여행용품>여행정리용품백";
$code["Q51106"]="키덜트&취미>여행용품>안전용품";
$code["Q51201"]="키덜트&취미>차량용품>주차번호판";
$code["Q51202"]="키덜트&취미>차량용품>방향제";
$code["Q51203"]="키덜트&취미>차량용품>기타액세서리";
$code["Q51204"]="키덜트&취미>차량용품>네비게이션";
$code["Q51205"]="키덜트&취미>차량용품>하이패스";
$code["Q51206"]="키덜트&취미>차량용품>차량용스티커";
$code["Q51207"]="키덜트&취미>차량용품>카인테리어";
$code["Q51208"]="키덜트&취미>차량용품>전장용품";
$code["Q51209"]="키덜트&취미>차량용품>카시트/쿠션/매트";
$code["Q51210"]="키덜트&취미>차량용품>수납/정리용품";
$code["Q51211"]="키덜트&취미>차량용품>차량관리/세차용품";
$code["Q51212"]="키덜트&취미>차량용품>기타";
$code["Q51301"]="키덜트&취미>컴퓨터용품>마우스";
$code["Q51302"]="키덜트&취미>컴퓨터용품>키보드";
$code["Q51303"]="키덜트&취미>컴퓨터용품>USB제품";
$code["Q51304"]="키덜트&취미>컴퓨터용품>노트북가방";
$code["Q51305"]="키덜트&취미>컴퓨터용품>주변기기/액세서리";
$code["Q51306"]="키덜트&취미>컴퓨터용품>넷북";
$code["Q51307"]="키덜트&취미>컴퓨터용품>외장하드";
$code["Q51308"]="키덜트&취미>컴퓨터용품>데스크탑PC";
$code["Q51401"]="키덜트&취미>레저>자전거";
$code["Q51402"]="키덜트&취미>레저>자전거액세서리";
$code["Q51403"]="키덜트&취미>레저>자전거벨";
$code["Q51404"]="키덜트&취미>레저>자전거가방";
$code["Q51405"]="키덜트&취미>레저>아웃도어용품";
$code["Q51406"]="키덜트&취미>레저>계절상품";
$code["Q51501"]="키덜트&취미>플라워>꽃바구니";
$code["Q51502"]="키덜트&취미>플라워>꽃다발";
$code["Q51503"]="키덜트&취미>플라워>꽃상자";
$code["Q51504"]="키덜트&취미>플라워>꽃과 케이크";
$code["Q51505"]="키덜트&취미>플라워>100송이 장미";
$code["Q51506"]="키덜트&취미>플라워>카네이션";
$code["Q51507"]="키덜트&취미>플라워>화한(축하&근조)";
$code["Q51508"]="키덜트&취미>플라워>화분류";
$code["Q51509"]="키덜트&취미>플라워>시들지않는꽃";
$code["Q51510"]="키덜트&취미>플라워>토피어리";

foreach($cate as $origin_cid=>$val){
	
	$sql="select * from shop_category_info where cid='".$origin_cid."' ";
	$db->query($sql);
	
	if($db->total){
		$db->fetch();
		$origin_name = $db->dt[cname];
		$origin_depth = $db->dt[depth];
		
		if($code[$val]){
			$target_name = $code[$val];
			$sql = "insert into sellertool_category_linked_relation (cla_ix,site_code,origin_cid,origin_name,origin_depth,target_cid,target_name,target_depth,rel_date)
				values('','halfclub','".$origin_cid."','$origin_name','$origin_depth','".substr($val,0,2).substr($val,0,4).$val."','".$target_name."','2',NOW())";
			$db->query($sql);
			//echo $sql."<br/>";
		}else{
			
			$sql="select concat((select disp_name from sellertool_received_category a where a.disp_no='".substr($val,0,2)."'),'>',(select disp_name from sellertool_received_category a where a.disp_no='".substr($val,0,2).substr($val,0,4)."'),'>',disp_name) as target_name from sellertool_received_category where disp_no='".substr($val,0,2).substr($val,0,4).$val."' ";
			$db->query($sql);

			if($db->total){
				$db->fetch();

				$target_name = $db->dt[target_name];

				$sql = "insert into sellertool_category_linked_relation (cla_ix,site_code,origin_cid,origin_name,origin_depth,target_cid,target_name,target_depth,rel_date)
				values('','halfclub','".$origin_cid."','$origin_name','$origin_depth','".substr($val,0,2).substr($val,0,4).$val."','".$target_name."','2',NOW())";
				$db->query($sql);
				//echo $sql."<br/>";

			}else{
				echo $origin_cid." ".$val." 실패 <br/>";
			}
		}

	}else{
		echo $origin_cid."실패 <br/>";
	}

}
exit;

?>