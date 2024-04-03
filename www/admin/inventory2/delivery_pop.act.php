<?
include("../../class/database.class");
include("../inventory/inventory.lib.php");
$db = new Database;
$pdb = new Database;
$ldb = new Database;

if($act == "delivery"){
	//print_r($_POST);

	if($delivery_type == 1){//보관장소 이전 
	/*
		$sql = "insert into inventory_output_history
							(ioh_ix, output_msg,output_saler,output_type,output_totalsize,charger_ix,ouput_status, regdate) 
							values 
							('','".$output_msg."','".$output_saler."','".$output_type."','".$total_delivery_stock."','".$admininfo[charger_ix]."','DC', NOW())";
		//echo $sql;
		//exit;
		$db->query($sql);
		$sql = "select ioh_ix from inventory_output_history where ioh_ix = LAST_INSERT_ID()";
		$db->query($sql);
		$db->fetch();
		$ioh_ix = $db->dt[ioh_ix];
	*/
		/*
		$sql = "insert into inventory_input_history 
				(h_ix, oid, input_type,input_msg,input_company,pi_ix,input_owner,input_totalsize,regdate) 
				values 
				('','$oid','DI','$input_msg','$ci_ix','$pi_ix','$input_owner','$total_inputstock',NOW())";
	
		///echo $sql;
		//exit;
		$db->query($sql);
		$sql = "select h_ix from inventory_input_history where h_ix = LAST_INSERT_ID()";
		$db->query($sql);
		$db->fetch();
		$h_ix = $db->dt[h_ix];
		*/
		$db->query("begin");

		$sql = "select ci_ix from inventory_customer_info where customer_type = 'E' and basic_type = 'INVENTORY' ";
		$db->query($sql);
		$db->fetch();
		$e_ci_ix = $db->dt[ci_ix];

		$sql = "select ci_ix from inventory_customer_info where customer_type = 'D' and basic_type = 'INVENTORY' ";
		$db->query($sql);
		$db->fetch();
		$d_ci_ix = $db->dt[ci_ix];

		for($i=0; $i < count($options);$i++){
				if($options[$i][delivery_cnt] > 0){
					
					//$db->query($sql);
					// 현재 조건에 맞는 재고정보가 있는지 확인
					$sql = "select * from inventory_product_stockinfo where pi_ix = '".$pi_ix."' and pid = '".$pid."' and opn_ix = '".$options[$i][opn_ix]."' and opnd_ix = '".$options[$i][opnd_ix]."' ";					
					$db->query($sql);

					if($db->total){ // 재고정보가 있으면 재고수 만큼만  차감
							$db->fetch();
							$stock_info = $db->dt;
							$sql = "update inventory_product_stockinfo set 
							stock = stock - ".$options[$i][delivery_cnt]." 
							where psi_ix = '".$stock_info[psi_ix]."'  ";
							//echo $sql;
							//exit;
							$db->query($sql);
					}else{
						/*
							$sql = "insert into inventory_product_stockinfo
							select '' as psi_ix,io.ci_ix,pi_ix,pid,opn_ix,opnd_ix,'' as stock_code, ".$options[$i][delivery_cnt]." as stock,1 as exit_order, NOW() as regdate 
							from inventory_order_detail iod , inventory_order io where iod.ioid = io.ioid and iod_ix='".$iod_ix[$i]."' ";
						*/
							$sql = "insert into inventory_product_stockinfo
										(psi_ix,ci_ix,pi_ix,pid,opn_ix,opnd_ix,stock_pcode,stock,exit_order,regdate) 
										values
										('','$d_ci_ix','".$pi_ix."','$pid','".$options[$i][opn_ix]."','".$options[$i][opnd_ix]."','$stock_pcode','-".$options[$i][delivery_cnt]."','1',NOW())";

							//echo $sql;
							//exit;
							$db->query($sql);
					}

					$sql = "insert into inventory_output_history_detail
								(iohd_ix, pid,opn_ix,opnd_ix,oid,pname, option_div,ci_ix, pi_ix,delivery_cnt,delivery_price, delivery_msg, delivery_type, charger_ix,regdate)
								values
								('','$pid','".$options[$i][opn_ix]."','".$options[$i][opnd_ix]."','".$oid."','".$pname."','".$options[$i][option_div]."','".$d_ci_ix."','".$pi_ix."','".$options[$i][delivery_cnt]."','".$options[$i][delivery_price]."','".$delivery_msg."','".$delivery_type."','".$admininfo[charger_ix]."',NOW())";

					//echo nl2br($sql)."<br><br>";
					$db->query($sql);


					$sql = "insert into inventory_input_history_detail 
							(ihd_ix,pid,opn_ix, opnd_ix, oid, pname, option_name,ci_ix, pi_ix,input_cnt,input_price,input_msg, charger_ix,regdate) 
							values 
							('','$pid','".$options[$i][opn_ix]."','".$options[$i][opnd_ix]."','".$oid."','".$pname."','".$options[$i][option_div]."','".$e_ci_ix."','".$move_pi_ix."','".$options[$i][delivery_cnt]."','".$options[$i][delivery_price]."','창고이동','".$admininfo[charger_ix]."', NOW())";
				//echo $sql;

					$db->query($sql);


					
					// 현재 조건에 맞는 재고정보가 있는지 확인
					$sql = "select * from inventory_product_stockinfo where pi_ix = '".$move_pi_ix."' and pid = '".$pid."' and opn_ix = '".$options[$i][opn_ix]."' and opnd_ix = '".$options[$i][opnd_ix]."' ";
					
					$db->query($sql);

					if($db->total){ // 재고정보가 있으면 재고수 만큼만  차감
							$db->fetch();
							$sql = "update inventory_product_stockinfo set 
							stock = stock + ".$options[$i][delivery_cnt]." 
							where psi_ix = '".$db->dt[psi_ix]."'  ";
							//echo $sql;
							//exit;
							$db->query($sql);
					}else{
						/*
							$sql = "insert into inventory_product_stockinfo
							select '' as psi_ix,io.ci_ix,pi_ix,pid,opn_ix,opnd_ix,'' as stock_code, ".$options[$i][delivery_cnt]." as stock,1 as exit_order, NOW() as regdate 
							from inventory_order_detail iod , inventory_order io where iod.ioid = io.ioid and iod_ix='".$iod_ix[$i]."' ";
						*/
							$sql = "insert into inventory_product_stockinfo
										(psi_ix,ci_ix,pi_ix,pid,opn_ix,opnd_ix,stock_pcode,stock,exit_order,regdate) 
										values
										('','$ci_ix','".$move_pi_ix."','$pid','".$options[$i][opn_ix]."','".$options[$i][opnd_ix]."','$stock_pcode','".$options[$i][delivery_cnt]."','1',NOW())";

							//echo $sql;
							//exit;
							$db->query($sql);
					}
					
				}

		}
		
		$db->query("commit");

	}else{ // 일반 출고 및 ...
		//print_r($_POST);
		/*
		$sql = "insert into inventory_output_history
							(ioh_ix, output_msg,output_saler,output_type,output_totalsize,charger_ix,ouput_status, regdate) 
							values 
							('','".$delivery_msg."','".$output_saler."','".$output_type."','".$total_delivery_stock."','".$admininfo[charger_ix]."','DC', NOW())";
		//echo $sql;
		//exit;
		$db->query($sql);
		$sql = "select ioh_ix from inventory_output_history where ioh_ix = LAST_INSERT_ID()";
		$db->query($sql);
		$db->fetch();
		$ioh_ix = $db->dt[ioh_ix];
		*/
		//$db->debug = true;
		$db->query("begin");

		
		if(count($options) > 0){
				for($i=0; $i < count($options);$i++){
					// 히스토리 정보 기록
					if($options[$i][delivery_cnt] > 0){
							$sql = "insert into inventory_output_history_detail
											(iohd_ix,pid,opn_ix,opnd_ix,oid,pname, option_div,ci_ix, pi_ix,delivery_cnt,delivery_price,delivery_msg,delivery_type,charger_ix,regdate)
											values
											('','$pid','".$options[$i][opn_ix]."','".$options[$i][opnd_ix]."','".$oid."','".$pname."','".$options[$i][option_div]."','".$d_ci_ix."','".$pi_ix."','".$options[$i][delivery_cnt]."','".$options[$i][delivery_price]."','".$delivery_msg."','".$delivery_type."','".$admininfo[charger_ix]."',NOW())";
							//echo nl2br($sql)."<br><br>";
							$db->query($sql);

							// 현재 조건에 맞는 재고정보가 있는지 확인
							$sql = "select * from inventory_product_stockinfo where pi_ix = '".$pi_ix."' and pid = '".$pid."' and opn_ix = '".$options[$i][opn_ix]."' and opnd_ix = '".$options[$i][opnd_ix]."' ";
							
							$db->query($sql);

							if($db->total){ // 재고정보가 있으면 재고수 만큼만  차감
									$db->fetch();
									$order_info = $db->dt;
									$sql = "update inventory_product_stockinfo set 
									stock = stock - ".$options[$i][delivery_cnt]." 
									where psi_ix = '".$db->dt[psi_ix]."'  ";
									//echo $sql;
									//exit;
									$db->query($sql);
							}else{
								/*
									$sql = "insert into inventory_product_stockinfo
									select '' as psi_ix,io.ci_ix,pi_ix,pid,opn_ix,opnd_ix,'' as stock_code, ".$options[$i][delivery_cnt]." as stock,1 as exit_order, NOW() as regdate 
									from inventory_order_detail iod , inventory_order io where iod.ioid = io.ioid and iod_ix='".$iod_ix[$i]."' ";
								*/
									$order_info[pid] = $pid;
									$order_info[pid] = $pid;


									$sql = "insert into inventory_product_stockinfo
												(psi_ix,ci_ix,pi_ix,pid,surtax_yorn,opn_ix,opnd_ix,stock_pcode,stock,exit_order,regdate) 
												values
												('','$ci_ix','$pi_ix','$pid','$surtax_yorn','".$options[$i][opn_ix]."','".$options[$i][opnd_ix]."','$stock_pcode','-".$options[$i][delivery_cnt]."','1',NOW())";

									//echo $sql;
									//exit;
									$db->query($sql);
							}

							UpdateProductStockInfo($order_info);
					}

				}
		}else{
				$sql = "insert into inventory_output_history_detail
								(iohd_ix,pid,opn_ix,opnd_ix,oid,pname, option_div,ci_ix, pi_ix,delivery_cnt,delivery_price,delivery_msg,delivery_type, charger_ix,regdate)
								values
								('','$pid','','','".$oid."','".$pname."','','".$d_ci_ix."','".$pi_ix."','".$delivery_cnt."','".$delivery_price."','".$delivery_msg."','".$delivery_type."','".$admininfo[charger_ix]."',NOW())";
				//echo nl2br($sql)."<br><br>";
				$db->query($sql);

				$sql = "select * from inventory_product_stockinfo where pi_ix = '".$pi_ix."' and pid = '".$pid."'  ";
							
				$db->query($sql);

				if($db->total){ // 재고정보가 있으면 재고수 만큼만  차감
						$db->fetch();
						$order_info = $db->dt;
						$sql = "update inventory_product_stockinfo set 
						stock = stock - ".$delivery_cnt." 
						where psi_ix = '".$db->dt[psi_ix]."'  ";
						//echo $sql;
						//exit;
						$db->query($sql);
				}else{
				
						$order_info[pid] = $pid;


						$sql = "insert into inventory_product_stockinfo
									(psi_ix,ci_ix,pi_ix,pid,surtax_yorn,opn_ix,opnd_ix,stock_pcode,stock,exit_order,regdate) 
									values
									('','$ci_ix','$pi_ix','$pid','$surtax_yorn','','','$stock_pcode','-".$delivery_cnt."','1',NOW())";

						//echo $sql;
						//exit;
						$db->query($sql);
				}
		}

		

		$db->query("commit");
	}

}

		
		//exit;
		//}
		



	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 출고 되었습니다.');self.close();</script>";
	exit;



/*
CREATE TABLE IF NOT EXISTS `inventory_output_history_detail` (
  `iohd_ix` int(10) default NULL COMMENT '인덱스',
  `pid` int(10) unsigned zerofill default NULL COMMENT '상품키',
  `opn_ix` int(8) default NULL COMMENT '옵션키값',
  `opnd_ix` int(10) default NULL COMMENT '옵션상세키값',
  `oid` varchar(17) default NULL COMMENT '주문번호',
  `option_name` varchar(255) default NULL COMMENT '옵션명',
  `pi_ix` int(6) default NULL COMMENT '보관장소키',
  `delivery_cnt` int(10) default NULL COMMENT '출고수량',
  `delivery_price` int(10) default NULL COMMENT '출고가격',
  `regdate` datetime NOT NULL COMMENT '등록일자'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='출고내역 상세정보';

*/





	
/*
	$sql = "insert into inventory_output_history(pid,pname,output_msg,output_saler,output_type,option_id,option_text,inventory_info,output_totalsize,output_owner,saler_status, regdate) 
				values ('".$pid."','".$pname."','".$output_msg."','".$output_saler."','".$output_type."','".$opnd_ix."','".strip_tags($option_text)."',
				(select inventory_info from shop_product where id = '".$pid."'),'".$output_totalsize."',(select com_name as company_name from ".TBL_COMMON_COMPANY_DETAIL." where company_id = '".$admininfo[company_id]."'),'DC', NOW())";
	$db->query($sql);

	$sql = "update shop_product set stock = stock - '".$output_totalsize."' where id = '".$pid."'";
	$pdb->query($sql);
	//$sql = "update inventory_input set stock = stock -'".$output_totalsize."' where oid = '".$opnd_ix."' and inventory_info = '".$inventory_info3."'";
	//$pdb->query($sql);
	if($opnd_ix != ""){
		$sql = "update shop_product_options_detail set option_stock = option_stock - '".$output_totalsize."' where id = '".$opnd_ix."'";
		$pdb->query($sql);
	}
*/
	

/*}else if($delivery_type == "DM"){
	//출고히스토리에 인서트
	$output_owner = $admininfo[company_id];
	$sql = "insert into inventory_output_history(pid,pname,output_type,output_msg,output_saler,output_type,oid,inventory_info,inventory_info2,output_totalsize,output_owner,date) values ('".$pid."','".$pname."','DM','".$output_msg."','".$output_saler."','".$output_type."','".$opnd_ix."','".$inventory_info."','".$inventory_info2."','".$output_totalsize."','".$output_owner."',NOW())";
	$db->query($sql);

	//입고히스토리 에 기본정보 입력
	$sql = "insert into inventory_input_history (pid,pname,input_type,input_msg,input_owner,input_totalsize,regdate) values ('".$pid."','".$pname."','DM','".$output_msg."','".$input_owner."','".$output_totalsize."',NOW())";
	$db->query($sql);
	$sql = "select h_ix from inventory_input_history where h_ix = LAST_INSERT_ID()";
	$ldb->query($sql);
	$ldb->fetch();

	//히스토리에 옵션별로 입력
	$sql = "insert into inventory_input_history_detail (hix,pid,option_name,inventory_info,input_size) values ('".$ldb->dt[h_ix]."','".$pid."','".$option_name."','".$inventory_info2."','".$output_totalsize."')";
	$db->query($sql);

	//메인창고 비교하여 출고 및 인서트
	$sql = "select main_inventory from shop_product where id = '".$pid."'";
	$db->query($sql);
	$db->fetch();

	if($db->dt[main_inventory] == $inventory_info2){
		$sql = "update shop_product set stock = stock + '".$output_totalsize."' where id = '".$pid."'";
		$pdb->query($sql);
		$sql = "update inventory_input set stock = stock -'".$output_totalsize."' where oid = '".$opnd_ix."' and inventory_info = '".$inventory_info."'";
		$pdb->query($sql);
		$sql = "update shop_product_options_detail set option_stock = option_stock + '".$output_totalsize."' where id = '".$opnd_ix."'";
		$pdb->query($sql);
		$sql = "update inventory_input set stock = stock +'".$output_totalsize."' where oid = '".$opnd_ix."' and inventory_info = '".$inventory_info2."'";
		$pdb->query($sql);
	}else{
		$sql = "update inventory_input set stock = stock -'".$output_totalsize."' where oid = '".$opnd_ix."' and inventory_info = '".$inventory_info."'";
		$pdb->query($sql);
		$sql = "update inventory_input set stock = stock +'".$output_totalsize."' where oid = '".$opnd_ix."' and inventory_info = '".$inventory_info2."'";
		$pdb->query($sql);
	}
	echo "<script>alert('정상적으로 재고이동 되었습니다.');self.close();</script>";
}*/
?>