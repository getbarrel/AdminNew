<?
include("../../class/database.class");
include("./inventory.lib.php");


$db = new Database;
if ($act == "warehouse_move")
{
	//for($

	//print_r($warehouse_delivery);
	//exit;
	//$db->debug = true;
	foreach($warehouse_delivery as $ps_ix => $delivery_cnt){
		if($delivery_cnt > 0){
				//$sql .= $ps_ix.":::".$delivery_cnt."<br>";
				$sql = "select pi.company_id, ps.pi_ix, ps.ps_ix from inventory_place_info pi , inventory_place_section ps where pi.pi_ix = ps.pi_ix and ps.ps_ix = '".$ps_ix."' ";
				$db->query($sql);
				$db->fetch();
				$pi_ix = $db->dt[pi_ix];
				$now_company_id = $db->dt[company_id];
				$now_pi_ix = $db->dt[pi_ix];
				$now_ps_ix = $db->dt[ps_ix];

				$sql = "select pi.company_id, ps.pi_ix, ps.ps_ix from inventory_place_info pi , inventory_place_section ps where pi.pi_ix = ps.pi_ix and ps.pi_ix = '".$pi_ix."' and ps.section_type ='D'  ";
				$db->query($sql);
				$db->fetch();
				$move_company_id = $db->dt[company_id];
				$move_pi_ix = $db->dt[pi_ix];
				$move_ps_ix = $db->dt[ps_ix];

				$sql = "select g.gid, gu.unit, g.standard,  '".$delivery_cnt."' as amount , '".$now_company_id."' as company_id,  '".$now_pi_ix."' as pi_ix,  '".$now_ps_ix."' as ps_ix  
							from inventory_goods g , inventory_goods_unit gu 
							where g.gid = gu.gid and gu.gu_ix = '".$gu_ix."'";
				// 출고가격을 어떻게 처리 할지? 
				// 한꺼번에 여러개를 묶음으로 처리하는데 출고가 ... 
				$db->query($sql);
				$warehouse_moveinfo = $db->fetchall();

				/*
							$sql = "insert into inventory_history_detail
										(hd_ix,h_ix,gid,unit,gname,standard,amount,price,expiry_date,regdate) 
										values
										('','".$h_ix."','".$stocked_detail_info[$i][gid]."','".$stocked_detail_info[$i][unit]."','".$gname."','".$stocked_detail_info[$i][standard]."','".$stocked_detail_info[$i][amount]."','".$stocked_detail_info[$i][price]."','".$stocked_detail_info[$i][expiry_date]."',NOW()) ";
				*/

				$item_info[pi_ix] = $pi_ix; // 입출고 내역은 어디로 이동해 갔는지가 남기 때문에 move_pi_ix 기록만 남긴다.
				$item_info[ps_ix] = $ps_ix; // 이동출고장소
				$item_info[company_id] = $now_company_id; // 이동사업장
				$item_info[h_div] = "2";  // 입출고유형 2 :  출고
				$item_info[vdate] = date("Ymd");
				//$item_info[ci_ix] = $_POST["ci_ix"]; // 거래처
				$item_info[oid] = $oid;
				$item_info[msg] = "상품판매 출고를 위한 내부창고이동(출고)";//$_POST["etc"];
				$item_info[h_type] = 'IW';//$_POST["h_type"]; // 51: 내부창고 이동
				$item_info[charger_name] = $_SESSION[admininfo]["charger"];
				$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
				$item_info[detail] = $warehouse_moveinfo;
				//print_r($item_info);
				//exit;
				UpdateGoodsItemStockInfo($item_info, $db);



				$sql = "select g.gid, gu.unit, g.standard,  '".$delivery_cnt."' as amount , '".$move_company_id."' as company_id,  '".$move_pi_ix."' as pi_ix,  '".$move_ps_ix."' as ps_ix  
							from inventory_goods g , inventory_goods_unit gu 
							where g.gid = gu.gid and gu.gu_ix = '".$gu_ix."'";
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
				$item_info[msg] = "상품판매 출고를 위한 내부창고이동(입고)";//$_POST["etc"];
				$item_info[h_type] = 'IW';//$_POST["h_type"]; 내부창고이동
				$item_info[charger_name] = $_SESSION[admininfo]["charger"];
				$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
				$item_info[detail] = $warehouse_moveinfo;

				UpdateGoodsItemStockInfo($item_info, $db);
		}
	}
	//echo $sql;
	echo "정상적으로 출고창고 이동 처리 되었습니다.";
}


if ($act == "delivery_status_update")
{
		$sql = "update shop_order_detail set delivery_status='$delivery_status' where od_ix='".$od_ix."' ";
		//echo $sql;
		//exit;
		$db->query($sql);

		echo "출고생태가 정상적으로 업데이트 되었습니다.";
		exit;
}



if ($act == "delivery_status_update_oid"){
		$sql = "update shop_order_detail set delivery_status='$delivery_status' where oid='".$oid."' ";
		//echo $sql;
		//exit;
		$db->query($sql);

		echo "출고생태가 정상적으로 업데이트 되었습니다.";
		exit;
}





if ($act == "insert")
{

	$sql = "select * from  inventory_delivery_type where delivery_type_code = '$delivery_type_code' ";
	$db->query($sql);
	if($db->total){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('출고형태코드 정보가 이미 등록되어 있습니다. 확인후 다른 코드로 입력해주세요');</script>");
	}else{
		$sql = "insert into inventory_delivery_type (delivery_type,delivery_type_code,disp,regdate) values('$delivery_type','$delivery_type_code','$disp',NOW())";
		$db->query($sql);
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('출고형태 정보가 정상적으로 등록되었습니다.');</script>");
		echo("<script>parent.document.location.href='delivery_type.php?mmode=$mmode';</script>");
	}
}


if ($act == "update"){
	
	$sql = "select * from  inventory_delivery_type where delivery_type_code = '$delivery_type_code' and dt_ix NOT IN ('$dt_ix') ";
	$db->query($sql);
	if($db->total){
		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('출고형태코드 정보가 이미 등록되어 있습니다. 확인후 다른 코드로 입력해주세요');</script>");
	}else{

		$sql = "update inventory_delivery_type set delivery_type='$delivery_type',delivery_type_code='$delivery_type_code',disp='$disp' where dt_ix='$dt_ix' ";
		
		$db->query($sql);

		

		echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('출고형태 정보가 정상적으로 수정되었습니다.');</script>");
		echo("<script>parent.document.location.href = 'delivery_type.php?mmode=$mmode';</script>");
	}
}

if ($act == "delete"){
	
	$sql = "delete from inventory_delivery_type where dt_ix='$dt_ix'";
	$db->query($sql);


	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('출고형태 정보가 정상적으로 삭제되었습니다.');</script>");
	echo("<script>parent.document.location.href='delivery_type.php?mmode=$mmode';</script>");
}

?>
