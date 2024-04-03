<?
include("../../class/database.class");
include("./inventory.lib.php");

$db = new Database;

if ($act == "box_set_lift")
{

	//(-)수량 이동처리 X
	if($lift_cnt > 0){
		$sql = "select pi.company_id, ps.pi_ix, ps.ps_ix from inventory_place_info pi , inventory_place_section ps where pi.pi_ix = ps.pi_ix and ps.ps_ix = '".$ps_ix."' ";
		$db->query($sql);
		$db->fetch();
		$pi_ix = $db->dt[pi_ix];
		$now_company_id = $db->dt[company_id];
		$now_pi_ix = $db->dt[pi_ix];
		$now_ps_ix = $db->dt[ps_ix];

		$sql = "select pi.company_id, ps.pi_ix, ps.ps_ix from inventory_place_info pi , inventory_place_section ps where pi.pi_ix = ps.pi_ix and ps.ps_ix = '".$delivery_ps_ix."' ";
		$db->query($sql);
		$db->fetch();
		$move_company_id = $db->dt[company_id];
		$move_pi_ix = $db->dt[pi_ix];
		$move_ps_ix = $db->dt[ps_ix];

		$sql = "select g.gid, gu.unit, g.standard,  '".$lift_cnt."' as amount , '".$now_company_id."' as company_id,  '".$now_pi_ix."' as pi_ix,  '".$now_ps_ix."' as ps_ix  
					from inventory_goods g , inventory_goods_unit gu 
					where g.gid = gu.gid and gu.gu_ix = '".$gu_ix."'";
		// 출고가격을 어떻게 처리 할지? 
		// 한꺼번에 여러개를 묶음으로 처리하는데 출고가 ... 
		$db->query($sql);
		$warehouse_moveinfo = $db->fetchall();

		$item_info[pi_ix] = $pi_ix; // 입출고 내역은 어디로 이동해 갔는지가 남기 때문에 move_pi_ix 기록만 남긴다.
		$item_info[ps_ix] = $ps_ix; // 이동출고장소
		$item_info[company_id] = $now_company_id; // 이동사업장
		$item_info[h_div] = "2";  // 입출고유형 2 :  출고
		$item_info[vdate] = date("Ymd");
		//$item_info[ci_ix] = $_POST["ci_ix"]; // 거래처
		$item_info[oid] = $oid;
		$item_info[msg] = $msg."Box/Set 해제(출고)";//$_POST["etc"];
		$item_info[h_type] = 'IW';//$_POST["h_type"]; // 51: 내부창고 이동
		$item_info[charger_name] = $_SESSION[admininfo]["charger"];
		$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
		$item_info[detail] = $warehouse_moveinfo;
		//print_r($item_info);
		//exit;
		UpdateGoodsItemStockInfo($item_info, $db);


		//낱개로!!
		$sql = "select g.gid, gu.unit, g.standard,  '".$unit_lift_cnt."' as amount , '".$move_company_id."' as company_id,  '".$move_pi_ix."' as pi_ix,  '".$move_ps_ix."' as ps_ix  
					from inventory_goods g , inventory_goods_unit gu 
					where g.gid = gu.gid and gu.gid = '".$gid."' and gu.unit ='1' ";
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
		$item_info[msg] = $msg."Box/Set 해제(입고)";//$_POST["etc"];
		$item_info[h_type] = 'IW';//$_POST["h_type"]; 내부창고이동
		$item_info[charger_name] = $_SESSION[admininfo]["charger"];
		$item_info[charger_ix] = $_SESSION[admininfo]["charger_ix"];
		$item_info[detail] = $warehouse_moveinfo;

		UpdateGoodsItemStockInfo($item_info, $db);

	}

	if($mmode == "pop"){
		echo "<script type='text/javascript'>
		<!--
			alert('정상적으로 처리 되었습니다.');
			top.opener.location.reload();
			top.self.close();
		//-->
		</script>";
	}else{
		echo "정상적으로 처리 되었습니다.";
	}
}


?>