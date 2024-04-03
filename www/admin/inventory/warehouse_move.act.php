<?
include("../../class/database.class");
include("./inventory.lib.php");

$db = new Database;
if($act == "get_goodsinfo"){

		$sql = "select g.cid,g.gname, g.gid, g.gcode, g.admin, g.ci_ix, g.pi_ix, pi.place_name, pi.company_id,  ps.section_name, ifnull(sum(ips.stock),0) as stock, gu.* 
		from inventory_goods g 
		right join inventory_goods_unit gu  on g.gid =gu.gid
		right join  inventory_product_stockinfo ips on gu.gid = ips.gid and gu.unit = ips.unit
		left join  inventory_place_info pi on g.pi_ix = pi.pi_ix
		left join  inventory_place_section ps on ips.ps_ix = ps.ps_ix
		where gid = '".$gid."' and gu.unit = '".$unit."'
		 ";
//echo nl2br($sql);
//exit;
$db->query($sql);
$data = json_encode($db->dt);
echo $data;

//$goods_infos = $db->fetchall();

//	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('창고 등록이 완료 되었습니다.');parent.document.location.href='place_list.php'</script>";
}

if($act == "check_stock"){
	$now_pi_ix = $now_pi_ix;
	$now_ps_ix = $now_ps_ix;

	if($now_pi_ix && $now_ps_ix){
		$sql = "select
					stock
				from
					inventory_product_stockinfo
				where
					pi_ix = '".$now_pi_ix."'
					and ps_ix = '".$now_ps_ix."'
					and gid = '".$gid."'";
		$db->query($sql);
		$db->fetch();

		if($db->dt[stock] > 0 ){
			echo "Y";
		}else{
			echo "N";
		}
	}

}

if($act == "wm_apply_cancel"){
	
	$sql = "update inventory_warehouse_move set status='AC' where wm_ix = '".$wm_ix."'";
	$db->query($sql);
	$db->fetch();

	echo "<script type='text/javascript'>
		<!--
			alert('성공적으로 처리 되었습니다.');
			parent.location.reload();
		//-->
		</script>";
}

if($act == "wm_move_cancel"){
	
	$sql = "update inventory_warehouse_move set status='MR' where wm_ix = '".$wm_ix."'";
	$db->query($sql);
	$db->fetch();
	
	$sql = "select * from inventory_warehouse_move where wm_ix = '".$wm_ix."'";
	$db->query($sql);
	$db->fetch();
	

	//판품입고
	$item_info[pi_ix] = $db->dt["now_pi_ix"]; // 입출고 내역은 어디로 이동해 갔는지가 남기 때문에 move_pi_ix 기록만 남긴다.
	$item_info[ps_ix] = $db->dt["now_ps_ix"]; // 이동출고장소
	$item_info[company_id] = $db->dt["now_company_id"]; // 이동사업장
	$item_info[h_div] = "1";  // 입출고유형 1 :  입고
	$item_info[vdate] = date("Ymd");
	//$item_info[ci_ix] = $_POST["ci_ix"]; // 거래처
	//$item_info[oid] = $_POST["oid"];
	$item_info[msg] = "창고이동취소로 인한 반품입고";
	$item_info[h_type] = "04";
	$item_info[charger_name] = $_SESSION[admininfo]["charger"];
	$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
	
	$sql = "select gid,unit,standard,expiry_date,company_id,pi_ix,ps_ix,delivery_cnt as amount from inventory_warehouse_move_detail where wm_ix = '".$wm_ix."'";
	$db->query($sql);
	$warehouse_moveinfo = $db->fetchall();

	$item_info[detail] = $warehouse_moveinfo;
	//print_r($item_info);
	//exit;
	UpdateGoodsItemStockInfo($item_info, $db);

	echo "<script type='text/javascript'>
		<!--
			alert('성공적으로 처리 되었습니다.');
			parent.location.reload();
		//-->
		</script>";
}

if($act == "insert"){
	//$db->debug = true;
	if(is_array($warehouse_moveinfo)){

		//for($i=0;$i<count($warehouse_moveinfo);$i++){
		//20131029 Hong now_pi_ix , now_ps_ix -> $wminfo[pi_ix] , $wminfo[ps_ix]
		foreach($warehouse_moveinfo as $wminfo){ //20131022 Hong
			$sql = "select
					ifnull(sum(stock),0) as stock
				from
					inventory_product_stockinfo
				where
					pi_ix = '".$wminfo[pi_ix]."' 
					and ps_ix = '".$wminfo[ps_ix]."'
					and gid = '".$wminfo[gid]."' 
					and unit = '".$wminfo[unit]."'";

			$db->query($sql);
			$db->fetch();
	
			if($db->dt[stock] <= 0 ){
				echo "<script> var pid = '".$wminfo[gid]."';  alert('품목번호가 '+pid+' 인 품목이 현재 창고에 재고가 없습니다.');</script>";
				exit;
			}
		}

	}
	//print_r($admininfo);
	//exit;
	//$db->debug = true;
	if($h_type == "IW"){
		$status = "MC";
	}else{
		$status = "MA";
	}
	
	foreach($warehouse_moveinfo as $wminfo){
		if($array_wm_ix[$wminfo[ps_ix]] ==""){
			$sql = "insert into inventory_warehouse_move
						(wm_ix,apply_charger_ix,apply_charger_name,now_company_id, now_pi_ix, now_ps_ix, move_company_id, move_pi_ix, move_ps_ix, wm_apply_date,status,charger_ix,charger_name,etc,h_type,al_ix,editdate, regdate) values('','$charger_ix','$charger_name','$now_company_id','".$wminfo[pi_ix]."','".$wminfo[ps_ix]."','$move_company_id','$move_pi_ix','$move_ps_ix','$wm_apply_date','$status','".$_SESSION[admininfo]["charger_ix"]."','".$_SESSION[admininfo]["charger"]."','$etc','$h_type','$al_ix',NOW(),NOW())";
			//echo nl2br($sql)."<br><br>";
			//exit;
			$db->sequences = "INVENTORY_WH_MOVE_SEQ";
			$db->query($sql);

			$db->query("SELECT wm_ix FROM inventory_warehouse_move WHERE wm_ix=LAST_INSERT_ID()");
			$db->fetch();

			$array_wm_ix[$wminfo[ps_ix]] = $db->dt[wm_ix];
		}
	}

	//for($i=0;$i<count($warehouse_moveinfo);$i++){
	foreach($warehouse_moveinfo as $wmkey => $wminfo){ //20131022 Hong
			$sql = "SELECT g.gname FROM inventory_goods g WHERE gid = '".$wminfo[gid]."' ";
			$db->query($sql);
			$db->fetch();
			$gname = $db->dt[gname];

			if($wminfo[pi_ix]){
				$sql = "select pi.company_id
							from inventory_place_info pi 
							where pi.pi_ix = '".$wminfo[pi_ix]."' ";

				$db->query($sql);
				$db->fetch();
				$company_id = $db->dt[company_id];
			}
		if($h_type == "IW"){
			$sql = "insert into inventory_warehouse_move_detail
					(wmd_ix,wm_ix,company_id, pi_ix,ps_ix,gid,gname,unit,standard,expiry_date,apply_cnt, delivery_cnt, entering_cnt,regdate) 
					values
					('','".$array_wm_ix[$wminfo[ps_ix]]."','".$company_id."','".$wminfo[pi_ix]."','".$wminfo[ps_ix]."','".$wminfo[gid]."','".$gname."','".$wminfo[unit]."','".$wminfo[standard]."','".$wminfo[expiry_date]."','".$wminfo[apply_cnt]."', '".$wminfo[apply_cnt]."','".$wminfo[apply_cnt]."',NOW()) ";

				
		}else{
			$sql = "insert into inventory_warehouse_move_detail
					(wmd_ix,wm_ix,company_id, pi_ix,ps_ix,gid,gname,unit,standard,expiry_date,apply_cnt,regdate) 
					values
					('','".$array_wm_ix[$wminfo[ps_ix]]."','".$company_id."','".$wminfo[pi_ix]."','".$wminfo[ps_ix]."','".$wminfo[gid]."','".$gname."','".$wminfo[unit]."','".$wminfo[standard]."','".$wminfo[expiry_date]."','".$wminfo[apply_cnt]."', NOW()) ";
		}
			
		//echo $sql;
		//exit;
		$db->query($sql);

					
		$warehouse_moveinfo[$wmkey][amount] = $warehouse_moveinfo[$wmkey][apply_cnt];
		//unset($warehouse_moveinfo[$wmkey][pi_ix]);
		//unset($warehouse_moveinfo[$wmkey][ps_ix]);
	}

	if($h_type == "IW"){
		//$item_info[pi_ix] = $_POST["now_pi_ix"]; // 입출고 내역은 어디로 이동해 갔는지가 남기 때문에 move_pi_ix 기록만 남긴다.
		//$item_info[ps_ix] = $_POST["now_ps_ix"]; // 이동출고장소
		//$item_info[company_id] = $_POST["now_company_id"]; // 이동사업장
		$item_info[h_div] = "2";  // 입출고유형 2 :  출고
		$item_info[vdate] = date("Ymd");
		$item_info[ci_ix] = $_POST["ci_ix"]; // 거래처
		$item_info[oid] = $_POST["oid"];
		$item_info[msg] = $_POST["etc"];
		$item_info[h_type] = $_POST["h_type"];
		$item_info[charger_name] = $_SESSION[admininfo]["charger"];
		$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
		$item_info[detail] = $warehouse_moveinfo;
		//print_r($item_info);
		//exit;
		UpdateGoodsItemStockInfo($item_info, $db);
		//$item_info[pi_ix] = $_POST["move_pi_ix"];
		//$item_info[ps_ix] = $_POST["move_ps_ix"];
		//$item_info[company_id] = $_POST["move_company_id"];

		foreach($warehouse_moveinfo as $wmkey => $wminfo){
			unset($warehouse_moveinfo[$wmkey][pi_ix]);
			unset($warehouse_moveinfo[$wmkey][ps_ix]);
		}

		$item_info[pi_ix] = $move_pi_ix;
		$item_info[ps_ix] = $move_ps_ix;
		$item_info[company_id] = $move_company_id;
		$item_info[h_div] = "1";
		$item_info[vdate] = date("Ymd");
		$item_info[ci_ix] = $_POST["ci_ix"];
		$item_info[oid] = $_POST["oid"];
		$item_info[msg] = $_POST["etc"];
		$item_info[h_type] = $_POST["h_type"];
		$item_info[charger_name] = $_SESSION[admininfo]["charger"];
		$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
		$item_info[detail] = $warehouse_moveinfo;
		
		

		UpdateGoodsItemStockInfo($item_info, $db);

		$db->query("update inventory_warehouse_move set  wm_delivery_date = '".date("Ymd")."', wm_entering_date = '".date("Ymd")."' WHERE wm_ix in ('".implode("','",$array_wm_ix)."') ");
	}
	//exit;
	if($mmode == "pop"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('창고이동요청이 정상적으로 완료 되었습니다.');parent.self.close()</script>";
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('창고이동요청이 정상적으로 완료 되었습니다.');parent.document.location.href='warehouse_move_list.php'</script>";
	}
}

if($act == "delete"){
	$db->query("delete from inventory_place_info where pi_ix = '".$pi_ix."'");
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('창고가 정상적으로 삭제 되었습니다.');parent.document.location.href='place_list.php'</script>";
}

if($act == "update"){
	//print_r($_POST);
	//exit;
	//$db->debug = true;

	if($status == "MI" && $bstatus != $status){
		$wm_delivery_date_str = " wm_delivery_date = '".date("Ymd")."', ";
	}

	if($status == "MC" && $bstatus != $status){
		$wm_entering_date_str = " wm_entering_date = '".date("Ymd")."', ";
	}

	$sql = "update inventory_warehouse_move set
				apply_charger_ix='".$charger_ix."',
				apply_charger_name='".$charger_name."',
				wm_apply_date='".$wm_apply_date."',
				".$wm_delivery_date_str."
				".$wm_entering_date_str."
				now_company_id='".$now_company_id."',
				now_pi_ix='".$now_pi_ix."',
				now_ps_ix='".$now_ps_ix."',
				move_company_id='".$move_company_id."',
				move_pi_ix='".$move_pi_ix."',
				move_ps_ix='".$move_ps_ix."',
				status='".$status."',
				charger_ix='".$_SESSION[admininfo]["charger_ix"]."',
				charger_name='".$_SESSION[admininfo]["charger"]."',
				etc='".$etc."',
				h_type='".$h_type."',
				al_ix='".$al_ix."',
				editdate= NOW()
				where wm_ix='".$wm_ix."' ";
				
				//wm_delivery_date='".$wm_delivery_date."',
				//wm_entering_date='".$wm_entering_date."',

	//echo $sql;
	//exit;
	$db->query($sql);
	
	$sql = "update inventory_warehouse_move_detail set is_delete = '1' where wm_ix='".$wm_ix."' ";
	$db->query($sql);

	//for($i=0 ; $i < count($warehouse_moveinfo);$i++){
	foreach($warehouse_moveinfo as $wmkey => $wminfo){ //20131022 Hong
	//	print_r($warehouse_moveinfo[$i]);
		$sql = "SELECT wmd.wmd_ix, g.gname, wmd.company_id FROM inventory_warehouse_move_detail wmd 
					left join inventory_goods g on wmd.gid = g.gid 
					WHERE wmd_ix = '".$wminfo[wmd_ix]."' 
					and wmd.wm_ix = '".$wm_ix."'";

		//echo $sql."<br><br>";
		$db->query($sql);
		
		if($db->total){
			$db->fetch();
			$wmd_ix = $db->dt[wmd_ix];
			$gname = $db->dt[gname];
			$company_id = $db->dt[company_id];
			
			if($_POST["status"] == "MI"){
				$warehouse_moveinfo[$wmkey][pi_ix] = $_POST["now_pi_ix"];
				$warehouse_moveinfo[$wmkey][ps_ix] = $_POST["now_ps_ix"];
			}


			$sql = "update inventory_warehouse_move_detail set
						pi_ix='".$wminfo[pi_ix]."',
						ps_ix='".$wminfo[ps_ix]."',
						gid='".$wminfo[gid]."',
						gname='".$gname."',
						unit='".$wminfo[unit]."',
						standard='".$wminfo[standard]."',
						expiry_date='".$wminfo[expiry_date]."',						
						apply_cnt='".$wminfo[apply_cnt]."',";
				if($wminfo[delivery_cnt]){
					$sql .= "delivery_cnt='".$wminfo[delivery_cnt]."',";
				}else{
					$sql .= "delivery_cnt=NULL,";
				}
				if($wminfo[entering_cnt]){
					$sql .= "entering_cnt='".$wminfo[entering_cnt]."',";
				}else{
					$sql .= "entering_cnt=NULL,";
				}
				$sql .= "
						is_delete='0',
						editdate = NOW()
						where wmd_ix='".$wminfo[wmd_ix]."' and wm_ix = '".$wm_ix."' ";

		}else{
			$sql = "SELECT g.gname FROM inventory_goods g WHERE gid = '".$wminfo[gid]."' ";
			$db->query($sql);
			$db->fetch();
			$gname = $db->dt[gname];

			if($wminfo[pi_ix]){
				$sql = "select pi.company_id
							from inventory_place_info pi 
							where pi.pi_ix = '".$wminfo[pi_ix]."' ";

				$db->query($sql);
				$db->fetch();
				$company_id = $db->dt[company_id];
			}

			
			$sql = "insert into inventory_warehouse_move_detail
						(wmd_ix,wm_ix,company_id,pi_ix,ps_ix,gid,gname,unit,standard,expiry_date,apply_cnt,is_delete,regdate) 
						values
						('','$wm_ix','".$company_id."','".$wminfo[pi_ix]."','".$wminfo[ps_ix]."','".$wminfo[gid]."','".$gname."','".$wminfo[unit]."','".$wminfo[standard]."','".$wminfo[expiry_date]."','".$wminfo[apply_cnt]."','0',NOW()) ";
			
			//$db->query($sql);
		}
		//echo nl2br($sql)."<br><br>";
		//exit;
		$db->query($sql);

/*
$INVENTORY_STATUS["MA"] = "이동요청";
$INVENTORY_STATUS["MO"] = "이동출고";
$INVENTORY_STATUS["MI"] = "이동중";
$INVENTORY_STATUS["MC"] = "이동완료";
$INVENTORY_STATUS["MR"] = "이동취소";  
*/
		$warehouse_moveinfo[$wmkey][company_id] = $company_id;

		if($_POST["status"] == "MA"){
			$warehouse_moveinfo[$wmkey][amount] = $warehouse_moveinfo[$wmkey][apply_cnt];
		}else if($_POST["status"] == "MI"){
			$warehouse_moveinfo[$wmkey][amount] = $warehouse_moveinfo[$wmkey][delivery_cnt];
		}else if($_POST["status"] == "MC"){
			$warehouse_moveinfo[$wmkey][amount] = $warehouse_moveinfo[$wmkey][entering_cnt];
		}

		//if($_POST["status"] == "MC"){
		//	unset($warehouse_moveinfo[$i][pi_ix]);
		//	unset($warehouse_moveinfo[$i][ps_ix]);
		//}
	}
	$sql = "delete from inventory_warehouse_move_detail where is_delete = '1' and wm_ix='".$wm_ix."' ";
	$db->query($sql);
//exit;
	//print_r($warehouse_moveinfo);
	

	if($_POST["status"] == "MC" && $bstatus != $status){// 이동완료 일때 자동 입고처리
		$item_info[pi_ix] = $_POST["move_pi_ix"];
		$item_info[ps_ix] = $_POST["move_ps_ix"];
		$item_info[company_id] = $_POST["move_company_id"];
		$item_info[h_div] = "1";
		$item_info[vdate] = date("Ymd");
		$item_info[ci_ix] = $_POST["ci_ix"];
		$item_info[oid] = $_POST["oid"];
		$item_info[msg] = $_POST["etc"];
		$item_info[h_type] = $_POST["h_type"];
		$item_info[charger_name] = $_SESSION[admininfo]["charger"];
		$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
		$item_info[detail] = $warehouse_moveinfo;

		UpdateGoodsItemStockInfo($item_info, $db);
	}else if($_POST["status"] == "MI"  && $bstatus != $status){// 이동중 일때 자동 입고처리
		//외부창고이동 내부이동 출고
		//출고장소는 상품의 현재 창고가 출고 창고가된다.
		//$item_info[pi_ix] = $_POST["now_pi_ix"];
		//$item_info[ps_ix] = $_POST["now_ps_ix"];
		//$item_info[company_id] = $_POST["now_company_id"];
		$item_info[h_div] = "2";
		$item_info[h_type] = $_POST["h_type"];

		$item_info[vdate] = date("Ymd");
		$item_info[ci_ix] = $_POST["ci_ix"];
		$item_info[oid] = $_POST["oid"];
		$item_info[msg] = $_POST["etc"];		
		$item_info[charger_name] = $_SESSION[admininfo]["charger"];
		$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
		$item_info[detail] = $warehouse_moveinfo;

		UpdateGoodsItemStockInfo($item_info, $db);
		
		//내부이동 입고
		//입고장소는 현재 창고의 출고 보관장소가 입고 장소가 된다.
		$item_info[pi_ix] = $_POST["now_pi_ix"];
		$item_info[ps_ix] = $_POST["now_ps_ix"];
		$item_info[company_id] = $_POST["now_company_id"];
		$item_info[h_div] = "1";
		$item_info[h_type] = $_POST["h_type"];

		UpdateGoodsItemStockInfo($item_info, $db);

		//외부 출고는 
		$item_info[pi_ix] = $_POST["now_pi_ix"]; // 입출고 내역은 어디로 이동해 갔는지가 남기 때문에 move_pi_ix 기록만 남긴다.
		$item_info[ps_ix] = $_POST["now_ps_ix"]; // 이동출고장소
		$item_info[company_id] = $_POST["now_company_id"]; // 이동사업장
		$item_info[h_div] = "2";  // 입출고유형 O :  출고
		$item_info[vdate] = date("Ymd");
		$item_info[ci_ix] = $_POST["ci_ix"]; // 거래처
		$item_info[oid] = $_POST["oid"];
		$item_info[msg] = $_POST["etc"];
		$item_info[h_type] = $_POST["h_type"];
		$item_info[charger_name] = $_SESSION[admininfo]["charger"];
		$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
		$item_info[detail] = $warehouse_moveinfo;
		//print_r($item_info);
		//exit;
		UpdateGoodsItemStockInfo($item_info, $db);
	}

/*
	if($h_type == "OW"){
	
		$item_info[pi_ix] = $_POST["move_pi_ix"];
		$item_info[ps_ix] = $_POST["move_ps_ix"];
		$item_info[company_id] = $_POST["move_company_id"];
		$item_info[h_div] = "1";
		$item_info[vdate] = date("Ymd");
		$item_info[ci_ix] = $_POST["ci_ix"];
		$item_info[oid] = $_POST["oid"];
		$item_info[msg] = $_POST["etc"];
		$item_info[h_type] = $_POST["h_type"];
		$item_info[charger_name] = $_SESSION[admininfo]["charger"];
		$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
		$item_info[detail] = $warehouse_moveinfo;

		UpdateGoodsItemStockInfo($item_info, $db);
	
		$item_info[pi_ix] = $_POST["move_pi_ix"]; // 입출고 내역은 어디로 이동해 갔는지가 남기 때문에 move_pi_ix 기록만 남긴다.
		$item_info[ps_ix] = $_POST["move_ps_ix"]; // 이동출고장소
		$item_info[company_id] = $_POST["move_company_id"]; // 이동사업장
		$item_info[h_div] = "2";  // 입출고유형 2 :  출고
		$item_info[vdate] = date("Ymd");
		$item_info[ci_ix] = $_POST["ci_ix"]; // 거래처
		$item_info[oid] = $_POST["oid"];
		$item_info[msg] = $_POST["etc"];
		$item_info[h_type] = $_POST["h_type"];
		$item_info[charger_name] = $_SESSION[admininfo]["charger"];
		$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
		$item_info[detail] = $warehouse_moveinfo;
		//print_r($item_info);
		//exit;
		UpdateGoodsItemStockInfo($item_info, $db);
	}
*/
//exit;
	if($mmode == "pop"){
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('창고이동 요청대장 수정이 완료 되었습니다.');parent.self.close()</script>";
	}else{
		echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('창고이동 요청대장 수정이 완료 되었습니다.');parent.document.location.reload();</script>";
	}
}


if ($act == "select_warehouse_move")
{

	foreach($wmlistinfo as $listinfo){
		//(-)수량 이동처리 X
		if($listinfo[gu_ix] !="" && $listinfo[delivery_cnt] > 0){

			$sql = "select pi.company_id, ps.pi_ix, ps.ps_ix from inventory_place_info pi , inventory_place_section ps where pi.pi_ix = ps.pi_ix and ps.ps_ix = '".$listinfo[ps_ix]."' ";
			$db->query($sql);
			$db->fetch();
			$pi_ix = $db->dt[pi_ix];
			$now_company_id = $db->dt[company_id];
			$now_pi_ix = $db->dt[pi_ix];
			$now_ps_ix = $db->dt[ps_ix];

			$sql = "select pi.company_id, ps.pi_ix, ps.ps_ix from inventory_place_info pi , inventory_place_section ps where pi.pi_ix = ps.pi_ix and ps.ps_ix = '".$listinfo[delivery_ps_ix]."' ";
			$db->query($sql);
			$db->fetch();
			$move_company_id = $db->dt[company_id];
			$move_pi_ix = $db->dt[pi_ix];
			$move_ps_ix = $db->dt[ps_ix];

			$sql = "select g.gid, gu.unit, g.standard,  '".$listinfo[delivery_cnt]."' as amount , '".$now_company_id."' as company_id,  '".$now_pi_ix."' as pi_ix,  '".$now_ps_ix."' as ps_ix  
						from inventory_goods g , inventory_goods_unit gu 
						where g.gid = gu.gid and gu.gu_ix = '".$listinfo[gu_ix]."'";
			// 출고가격을 어떻게 처리 할지? 
			// 한꺼번에 여러개를 묶음으로 처리하는데 출고가 ... 
			$db->query($sql);
			$warehouse_moveinfo = $db->fetchall();

			if($array_wm_ix[$listinfo[ps_ix]."-".$listinfo[delivery_ps_ix]]==""){
				$sql = "insert into inventory_warehouse_move
							(wm_ix,apply_charger_ix,apply_charger_name,now_company_id, now_pi_ix, now_ps_ix, move_company_id, move_pi_ix, move_ps_ix, wm_apply_date,status,charger_ix,charger_name,etc,h_type,al_ix,editdate, regdate) values('','".$_SESSION[admininfo]["charger_ix"]."','".$_SESSION[admininfo]["charger"]."','$now_company_id','$now_pi_ix','$now_ps_ix','$move_company_id','$move_pi_ix','$move_ps_ix',NOW(),'MC','".$_SESSION[admininfo]["charger_ix"]."','".$_SESSION[admininfo]["charger"]."','$etc','IW','$al_ix',NOW(),NOW())";
				//echo nl2br($sql)."<br><br>";
				//exit;
				$db->sequences = "INVENTORY_WH_MOVE_SEQ";
				$db->query($sql);

				$db->query("SELECT wm_ix FROM inventory_warehouse_move WHERE wm_ix=LAST_INSERT_ID()");
				$db->fetch();
				$array_wm_ix[$listinfo[ps_ix]."-".$listinfo[delivery_ps_ix]] = $db->dt[wm_ix];
			}


			for($i=0 ; $i < count($warehouse_moveinfo);$i++){

				$sql = "SELECT g.gname FROM inventory_goods g WHERE gid = '".$warehouse_moveinfo[$i][gid]."' ";
				$db->query($sql);
				$db->fetch();
				$gname = $db->dt[gname];

				if($warehouse_moveinfo[$i][pi_ix]){
					$sql = "select pi.company_id
								from inventory_place_info pi 
								where pi.pi_ix = '".$warehouse_moveinfo[$i][pi_ix]."' ";

					$db->query($sql);
					$db->fetch();
					$company_id = $db->dt[company_id];
				}

				$sql = "insert into inventory_warehouse_move_detail
						(wmd_ix,wm_ix,company_id, pi_ix,ps_ix,gid,gname,unit,standard,expiry_date,apply_cnt, delivery_cnt, entering_cnt,regdate) 
						values
						('','".$array_wm_ix[$listinfo[ps_ix]."-".$listinfo[delivery_ps_ix]]."','".$company_id."','".$warehouse_moveinfo[$i][pi_ix]."','".$warehouse_moveinfo[$i][ps_ix]."','".$warehouse_moveinfo[$i][gid]."','".$gname."','".$warehouse_moveinfo[$i][unit]."','".$warehouse_moveinfo[$i][standard]."','".$warehouse_moveinfo[$i][expiry_date]."','".$warehouse_moveinfo[$i][amount]."', '".$warehouse_moveinfo[$i][amount]."','".$warehouse_moveinfo[$i][amount]."',NOW()) ";
				//echo $sql;
				//exit;
				$db->query($sql);

				unset($warehouse_moveinfo[$i][pi_ix]);
				unset($warehouse_moveinfo[$i][ps_ix]);
			}

			/*
			$sql = "insert into inventory_history_detail
					(hd_ix,h_ix,gid,unit,gname,standard,amount,price,expiry_date,regdate) 
					values
					('','".$h_ix."','".$stocked_detail_info[$i][gid]."','".$stocked_detail_info[$i][unit]."','".$gname."','".$stocked_detail_info[$i][standard]."','".$stocked_detail_info[$i][amount]."','".$stocked_detail_info[$i][price]."','".$stocked_detail_info[$i][expiry_date]."',NOW()) ";
			*/

			$item_info[pi_ix] = $now_pi_ix; // 입출고 내역은 어디로 이동해 갔는지가 남기 때문에 move_pi_ix 기록만 남긴다.
			$item_info[ps_ix] = $now_ps_ix; // 이동출고장소
			$item_info[company_id] = $now_company_id; // 이동사업장
			$item_info[h_div] = "2";  // 입출고유형 2 :  출고
			$item_info[vdate] = date("Ymd");
			//$item_info[ci_ix] = $_POST["ci_ix"]; // 거래처
			$item_info[oid] = $oid;
			$item_info[msg] = $msg."내부창고이동(출고)";//$_POST["etc"];
			$item_info[h_type] = 'IW';//$_POST["h_type"]; // 51: 내부창고 이동
			$item_info[charger_name] = $_SESSION[admininfo]["charger"];
			$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
			$item_info[detail] = $warehouse_moveinfo;
			//print_r($item_info);
			//exit;
			UpdateGoodsItemStockInfo($item_info, $db);



			$sql = "select g.gid, gu.unit, g.standard,  '".$listinfo[delivery_cnt]."' as amount , '".$move_company_id."' as company_id,  '".$move_pi_ix."' as pi_ix,  '".$move_ps_ix."' as ps_ix  
						from inventory_goods g , inventory_goods_unit gu 
						where g.gid = gu.gid and gu.gu_ix = '".$listinfo[gu_ix]."'";
			// 출고가격을 어떻게 처리 할지? 
			// 한꺼번에 여러개를 묶음으로 처리하는데 출고가 ... 
			$db->query($sql);
			$warehouse_moveinfo = $db->fetchall();


			$item_info[pi_ix] = $move_pi_ix;
			$item_info[ps_ix] = $move_ps_ix;
			$item_info[company_id] = $move_company_id;
			$item_info[h_div] = "1";
			$item_info[vdate] = date("Ymd");
			//$item_info[ci_ix] = $_POST["ci_ix"];
			$item_info[oid] = $oid;
			$item_info[msg] = $msg."내부창고이동(입고)";//$_POST["etc"];
			$item_info[h_type] = 'IW';//$_POST["h_type"]; 내부창고이동
			$item_info[charger_name] = $_SESSION[admininfo]["charger"];
			$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
			$item_info[detail] = $warehouse_moveinfo;

			UpdateGoodsItemStockInfo($item_info, $db);

			$db->query("update inventory_warehouse_move set  wm_delivery_date = '".date("Ymd")."', wm_entering_date = '".date("Ymd")."' WHERE wm_ix= '".$array_wm_ix[$listinfo[ps_ix]."-".$listinfo[delivery_ps_ix]]."' ");

		}
	}

	if($mmode == "pop"){
		echo "<script type='text/javascript'>
		<!--
			alert('정상적으로 이동 처리 되었습니다.');
			top.opener.location.reload();
			top.self.close();
		//-->
		</script>";
		exit;
	}else{
		echo "<script type='text/javascript'>
		<!--
			alert('정상적으로 이동 처리 되었습니다.');
			parent.location.reload();
		//-->
		</script>";
		exit;
	}
}

if ($act == "warehouse_move")
{
	
	inventory_warehouse_move($gu_ix,$delivery_cnt,$ps_ix,$delivery_ps_ix,$charger_ix,$charger,$mmode);
}

?>