<?
//print_r($_POST);
//exit;
include("../../class/database.class");
$db = new Database;

if($mode == "insert"){
	//print_r($_POST);
	//exit;

	//print_r($_POST);
	//exit;
	

	//히스토리 에 기본정보 입력
	/*
	$sql = "insert into inventory_input_history 
				(input_type,input_msg,input_company,pi_ix,input_owner,input_totalsize,regdate) 
				values 
				('DI','$input_msg','$ci_ix','$pi_ix','$input_owner','$total_inputstock',NOW())";
	
	///echo $sql;
	//exit;
	$db->query($sql);
	$sql = "select h_ix from inventory_input_history where h_ix = LAST_INSERT_ID()";
	$ldb->query($sql);
	$ldb->fetch();
	*/
	//$db->debug = true;
	if(count($options) > 0){
		//히스토리에 옵션별로 입력
		
		$db->query("begin");

		for($i=0;$i<count($options);$i++){
			if($options[$i][input_cnt] > 0 && $options[$i][input_cnt] != ""){
				$sql = "insert into inventory_input_history_detail 
							(ihd_ix,pid,opn_ix, opnd_ix, oid, pname, option_name,ci_ix,pi_ix,input_cnt,input_price, input_msg, charger_ix, regdate) 
							values 
							('','$pid','".$options[$i][opn_ix]."','".$options[$i][opnd_ix]."','','$pname','".$options[$i][option_div]."','$ci_ix','$pi_ix','".$options[$i][input_cnt]."','".$options[$i][input_price]."', '".$input_msg."','".$admininfo[charger_ix]."', NOW())";
				//echo $sql;

				$db->query($sql);
				

				$sql = "select * from inventory_product_stockinfo where vdate = '".date("Ymd")."' and pi_ix = '".$pi_ix."' and pid = '".$pid."' and opn_ix = '".$options[$i][opn_ix]."' and opnd_ix = '".$options[$i][opnd_ix]."' ";
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
									(psi_ix,vdate, ci_ix,pi_ix,pid,opn_ix,opnd_ix,stock_pcode,stock,exit_order,regdate) 
									values
									('','".date("Ymd")."','".$ci_ix."','".$pi_ix."','".$pid."','".$options[$i][opn_ix]."','".$options[$i][opnd_ix]."','$stock_pcode','".$options[$i][input_cnt]."','1',NOW()) ";
						//echo $sql;
						//exit;
						$db->query($sql);
				}

				

				
			}// for end
			
			$sql = "select pid, opn_ix, opnd_ix, sum(stock) as stock 
						from inventory_product_stockinfo 
						where pid = '".$pid."' and opn_ix = '".$options[$i][opn_ix]."' and opnd_ix = '".$options[$i][opnd_ix]."' 
						group by pid, opn_ix, opnd_ix ";

				$db->query($sql);
				$db->fetch();
				$stock_sum = $db->dt[stock];
				if($stock_sum == ""){
					$stock_sum = 0;
				}
				

				//옵션테이블에 각옵션별 재고 업데이트
				
				//$sql = "update shop_product_options_detail set option_stock = option_stock + ".$options[$i][input_cnt].", option_safestock = ".$safestock[$i]." where id = '".$options[$i][opnd_ix]."'";
				//$db->query($sql);

				$sql = "update ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." set 
							option_stock = ".$stock_sum." 
							where pid = '".$pid."' and opn_ix = '".$options[$i][opn_ix]."' and id = '".$options[$i][opnd_ix]."'   ";
				//echo $sql;
				//exit;
				$db->query($sql);


			$sql = "SELECT po.pid, sum(pod.option_stock) as option_stock  
						FROM shop_product_options po , ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." pod 
						WHERE po.pid='".$pid."' 
						and po.opn_ix = pod.opn_ix 
						and po.option_kind = 'b' group by po.pid ";

			$db->query($sql);
			$db->fetch();
			$option_stock = $db->dt[option_stock];
			if($option_stock > 0){
				$option_stock_yn = "Y";
			}else{
				$option_stock_yn = "N";
			}
			$db->query("update ".TBL_SHOP_PRODUCT." set stock = ".$option_stock." , option_stock_yn = '$option_stock_yn'  where id ='$pid'");

		}
	
		$db->query("commit");

	}else{
			/*
			$sql = "insert into inventory_input_history_detail 
						(ihd_ix,pid,option_name,pi_ix,input_cnt, input_price, regdate) 
						values 
						('','$pid','".$option_name."','$pi_ix','".$total_inputstock."','".$total_input_price."', NOW())";

			$db->query($sql);
			*/
			//옵션테이블에 각옵션별 재고 업데이트
			$db->query("begin");

			$sql = "insert into inventory_input_history_detail 
							(ihd_ix,pid,opn_ix, opnd_ix, oid, pname, option_name,ci_ix,pi_ix,input_cnt,input_price, input_msg, charger_ix, regdate) 
							values 
							('','$pid','','','','$pname','','$ci_ix','$pi_ix','".$input_cnt."','".$input_price."', '".$input_msg."','".$admininfo[charger_ix]."', NOW())";
				
				$db->query($sql);
				


			$sql = "select * from inventory_product_stockinfo where vdate = '".date("Ymd")."' and pi_ix = '".$pi_ix."' and pid = '".$pid."'  ";
			$db->query($sql);

			if($db->total){
					$db->fetch();
					$sql = "update inventory_product_stockinfo set 
					stock = stock + ".$input_cnt." 
					where psi_ix = '".$db->dt[psi_ix]."'  ";
					//echo $sql;
					//exit;
					$db->query($sql);
			}else{

					$sql = "insert into inventory_product_stockinfo
								(psi_ix,vdate, ci_ix,pi_ix,pid,opn_ix,opnd_ix,stock_pcode,stock,exit_order,regdate) 
								values
								('','".date("Ymd")."','".$ci_ix."','".$pi_ix."','".$pid."','','','$stock_pcode','".$input_cnt."','1',NOW()) ";
					//echo $sql;
					//exit;
					$db->query($sql);
			}
			
			$sql = "select  sum(stock) as stock from inventory_product_stockinfo where pid = '".$pid."'   ";
			$db->query($sql);
			$db->fetch();
			$stock_sum = $db->dt[stock];
			if($stock_sum == ""){
				$stock_sum = 0;
			}
			if($stock_sum > 0){
				$option_stock_yn = "Y";
			}else{
				$option_stock_yn = "N";
			}

			$sql = "update shop_product set  stock =  ".$stock_sum.", option_stock_yn = '".$option_stock_yn."' where id = '".$pid."'  ";
			//echo $sql;
			//exit;
			$db->query($sql);
			$db->query("commit");
		
	}

	$sql = "select supply_company , inventory_info from shop_product  where id = '".$pid."'  ";		
	$db->query($sql);
	$db->fetch();
	$goods_info = $db->dt;

	if($goods_info[supply_company] == ""){
		$sql = "update shop_product set supply_company =  '".$ci_ix."' where id = '".$pid."'  ";		
		$db->query($sql);
	}

	if($goods_info[inventory_info] == ""){
		$sql = "update shop_product set inventory_info =  '".$pi_ix."' where id = '".$pid."'  ";		
		$db->query($sql);
	}
	
	//해당상품 정보 테이블에 총재고 수 업데이트
	
	//$sql = "update shop_product set stock = stock + ".$total_inputstock." , safestock = ".$total_safestock.",inventory_info = '".$pi_ix."' where id = '".$pid."'";
	//$db->query($sql);

		
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 입고 되었습니다.');parent.self.close();</script>";
}


?>