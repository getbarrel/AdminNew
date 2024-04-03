<?
include("../../class/database.class");
include("../inventory/inventory.lib.php");

$db = new Database;
//$db->debug = true;

//$act == "single_goods_reg" 대량엑셀처리에서 넘어오는 값입니다. 삭제하시면 안됩니다. 2014-10-13 이학봉
if($act == "insert" || $act == "single_goods_reg"){
	
	//20131128 Hong inventory_history_detail 에 ptprice 입력하기 위한 주문쪽데이터와 비교하기 위한 구분값 
	$regist_infos["act_from"] = "inventory";

	if($h_div == '1'){//입고일경우 POS 네임	2013-07-03 이학봉

		$regist_infos[pi_ix] = $regist_pi_ix;
		$regist_infos[company_id] = $regist_company_id;
		//$regist_infos[ps_ix] = $_POST["regist_ps_ix"];
		
		$sql="select ps_ix from inventory_place_section where pi_ix='".$regist_infos[pi_ix]."' and section_type = 'S' ";
		$db->query($sql);
		$db->fetch();
		$regist_infos[ps_ix] = $db->dt[ps_ix];

	}else if($h_div == '2'){//출고일경우 POS 네임
		$regist_infos[pi_ix] = $regist_pi_ix;
		$regist_infos[ps_ix] = $regist_ps_ix;
		$regist_infos[company_id] = $regist_company_id;
	}

	$regist_infos[h_div] = $h_div;
	//$regist_infos[vdate] = date("Ymd");
	$regist_infos[vdate] = str_replace('-','',$vdate);

	if($regist_infos[vdate]==""){
		$regist_infos[vdate] = date("Ymd");
	}

	$regist_infos[ci_ix] = $ci_ix;

	if($oid){
		$regist_infos[oid] = $oid;
	}

	if($regist_infos[h_div] == "1"){
		$regist_infos[ioid] = "1".substr(date("YmdHis"),1)."-".rand(10000, 99999);
	}else if($regist_infos[h_div] == '2'){
		if(!$regist_infos[oid]){
			$regist_infos[oid] = date("YmdHis")."-".rand(10000, 99999);
		}
	}

	$regist_infos[msg] = $etc;
	$regist_infos[h_type] = $h_type;
	$regist_infos[charger_name] = $_SESSION[admininfo]["charger"];
	$regist_infos[charger_ix] = $_SESSION[admininfo]["charger_ix"];
	$regist_infos[detail] = $item_infos;

	//echo $regist_infos[oid];
	//print_r($regist_infos);
	//exit;

	UpdateGoodsItemStockInfo($regist_infos, $db);
//	exit;
/*
	if($_POST[regist_pi_ix]){
		$sql = "select place_name, ccd.com_name
					from inventory_place_info pi 
					left join common_company_detail ccd on pi.company_id = ccd.company_id
					where pi.pi_ix = '".$_POST[regist_pi_ix]."' ";
		$db->query($sql);
		$db->fetch();
		$company_id = $db->dt[company_id];
		$com_name = $db->dt[com_name];
		$place_name = $db->dt[place_name];
	}

	if($_POST[regist_ps_ix]){
		$sql = "select * from inventory_place_section where ps_ix = '".$_POST[regist_ps_ix]."' ";
		$db->query($sql);
		$db->fetch();
		$section_name = $db->dt[section_name];
	}

	if($_POST[regist_company_id]){
		$sql = "select * from common_company_detail where company_id = '".$_POST[ci_ix]."' ";
		$db->query($sql);
		$db->fetch();
		$customer_name = $db->dt[com_name];
	}

	//storage_fee  는 별도의 테이블로 불리하는거 검토
	$sql = "insert into inventory_history 
				(h_ix,h_div,vdate,customer_name,com_name, place_name,section_name,ci_ix,company_id, pi_ix,ps_ix,oid,msg,h_type,charger_name,charger_ix,regdate) 
				values ('','".$_POST[h_div]."','".$_POST[vdate]."','".$customer_name."','".$com_name."','".$place_name."','".$section_name."','".$_POST[ci_ix]."','".$company_id."','".$_POST[regist_pi_ix]."','".$_POST[regist_ps_ix]."','".$_POST[oid]."','".$_POST[msg]."','".$_POST[h_type]."','".$_POST[charger_name]."','".$_POST[charger_ix]."',NOW()) ";
	//echo nl2br($sql);
	//exit;
	//if(false){
	$db->sequences = "INVENTORY_HISTORY_SEQ";
	$db->query($sql);
	//}

	if($db->dbms_type == "oracle"){
		$h_ix = $db->last_insert_id;
		//echo $INSERT_PRODUCT_ID;
		//exit;
	}else{
		$db->query("SELECT h_ix FROM inventory_history WHERE h_ix=LAST_INSERT_ID()");
		$db->fetch();
		$h_ix = $db->dt[0];
	}

	if(count($item_infos) > 0){
		//히스토리에 옵션별로 입력
		for($i=0;$i<count($item_infos);$i++){
				if($item_infos[$i][gid]){
					$sql = "select g.gname, g.standard, gu.gu_ix from inventory_goods g left join inventory_goods_unit gu on g.gid = gu.gid where g.gid = '".$item_infos[$i][gid]."' and gu.unit = '".$item_infos[$i][unit]."'  ";
					$db->query($sql);
					$db->fetch();
					$gname = $db->dt[gname];
					$standard = $db->dt[standard];
				} 
				$sql = "insert into inventory_history_detail 
							(hd_ix,h_ix,gid,unit,gname,standard,amount,price,regdate) 
							values 
							('','".$h_ix."','".$item_infos[$i][gid]."','".$item_infos[$i][unit]."','".$gname."','".$standard."','".$item_infos[$i][amount]."','".$item_infos[$i][price]."',NOW()) ";


				//echo nl2br($sql);
				//exit;
				$db->sequences = "INVENTORY_HISTORY_DT_SEQ";
				$db->query($sql);

				// 재고 업데이트 공통 함수 필요

				$item_info = $item_infos[$i];
				$item_info[pi_ix] = $_POST[regist_pi_ix];
				$item_info[ps_ix] = $_POST[regist_ps_ix];
				$item_info[company_id] = $_POST[company_id];
				
				UpdateProductStockInfo($item_info);
			
		}// for end
	}
*/
// exit;

	if($act != "single_goods_reg"){
		if(!$is_continue){
			$reload_str = "parent.document.location.reload();";
		}
		if($h_div == 1){
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입고 되었습니다.');".$reload_str."</script>";
		}else if($h_div == 2){
			echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 출고 되었습니다.');".$reload_str."</script>";//parent.self.close();
		}
	}
}


?>