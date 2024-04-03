<?
//print_r($_POST);
//exit;
include("../../class/database.class");
include("../inventory/inventory.lib.php");

$db = new Database;

if($mode == "insert"){
	//print_r($_POST);
	//exit;
	if(count($options) > 0){
		//히스토리에 옵션별로 입력
		for($i=0;$i<count($options);$i++){
			if($options[$i][input_cnt] > 0 && $options[$i][input_cnt] != ""){

				$sql = "insert into inventory_input_history_detail
							(ihd_ix,gid,gi_ix, oid, gname, item_name,ci_ix,pi_ix,ps_ix,input_cnt,input_price, input_msg, input_type,charger_ix, regdate)
							values
							('','$gid','".$options[$i][gi_ix]."','','$gname','".$options[$i][item_name]."','$ci_ix','$pi_ix','$ps_ix','".$options[$i][input_cnt]."','".$options[$i][input_price]."', '".$input_msg."','".$dt_ix."','".$admininfo[charger_ix]."', NOW())";
				//echo $sql;
				$db->sequences = "INVENTORY_IN_HISTORY_DT_SEQ";
				$db->query($sql);


				$sql = "select * from inventory_product_stockinfo where vdate = '".date("Ymd")."' and pi_ix = '".$pi_ix."' and ps_ix = '".$ps_ix."' and gid = '".$gid."' and gi_ix = '".$options[$i][gi_ix]."' ";
				$db->query($sql);

				if($db->total){
						$db->fetch();
						$sql = "update inventory_product_stockinfo set
						stock = stock + ".$options[$i][input_cnt]."
						where psi_ix = '".$db->dt[psi_ix]."'  ";
						//echo $sql;
						//exit;
						$db->query($sql);
				}else{

						$sql = "insert into inventory_product_stockinfo
									(psi_ix,vdate, ci_ix,pi_ix,ps_ix, gid,gi_ix,stock_pcode,stock,exit_order,regdate)
									values
									('','".date("Ymd")."','".$ci_ix."','".$pi_ix."','".$ps_ix."','".$gid."','".$options[$i][gi_ix]."','".$options[$i][item_code]."','".$options[$i][input_cnt]."','1',NOW()) ";
						//echo $sql;
						//exit;
						$db->sequences = "INVENTORY_GOODS_INFO_SEQ";
						$db->query($sql);
				}

				$item_info[gi_ix] = $options[$i][gi_ix];
				UpdateProductStockInfo($item_info);
			}
		}// for end
	}
 
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입고 되었습니다.');parent.self.close();</script>";
}


?>