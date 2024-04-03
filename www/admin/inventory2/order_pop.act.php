<?
//print_r($_POST);
//exit;
include("../../class/database.class");
include("../inventory/inventory.lib.php");
$db = new Database;
$pdb = new Database;
$ldb = new Database;
if($mode == "insert"){
	//print_r($_POST);
	//exit;

	//print_r($_POST);
	//exit;
	
	//히스토리 에 기본정보 입력
	/*
	$sql = "insert into inventory_input_history (pid,pname,input_type,input_msg,input_company,input_inventory,input_owner,input_totalsize,regdate) 
				values 
				('$pid','$pname','DI','$input_msg','$input_company','$pi_ix','$input_owner','$total_inputstock',NOW())";
	*/
	///echo $sql;
	//exit;
	//$db->query($sql);
	//$sql = "select h_ix from inventory_input_history where h_ix = LAST_INSERT_ID()";
	//$ldb->query($sql);
	//$ldb->fetch();
	
	if(count($options) > 0 ){
		//히스토리에 옵션별로 입력
		for($i=0;$i < count($options);$i++){
			//echo $options[$i][order_coprice];
			if($options[$i][order_cnt] > 0 && $options[$i][order_coprice] != ""){
				/*
				$sql = "insert into inventory_input_history_detail (hix,pid,opn_ix, opn_d_ix, oid,option_name,input_inventory,input_size, regdate) 
							values 
							('".$ldb->dt[h_ix]."','$pid','".$opn_ix[$i]."','".$opn_d_ix[$i]."','".$iod[$i]."','".$option_name[$i]."','$pi_ix','".$input_size[$i]."', NOW())";
				*/
				$sql = "insert into inventory_order_detail_tmp
							(iodt_ix,ci_ix,company_id,charger_ix,pi_ix,pid,surtax_yorn,opn_ix,opnd_ix,stock_pcode,pname,option_name,order_cnt,order_coprice,regdate) 
							values
							('','".$ci_ix."','".$_SESSION["admininfo"]["company_id"]."','".$charger_ix."','".$pi_ix."','".$pid."','".$surtax_yorn."','".$options[$i][opn_ix]."',
							'".$options[$i][opnd_ix]."','".$options[$i][stock_pcode]."','".$pname."','".$options[$i][option_name]."','".$options[$i][order_cnt]."','".$options[$i][order_coprice]."',NOW())  ";
				//echo nl2br($sql)."<br>";

				$db->query($sql);
				//옵션테이블에 각옵션별 재고 업데이트
			}
		}
	}else{
		
			//$sql = "insert into inventory_input_history_detail (hix,pid,option_name,input_inventory,input_size, regdate) values ('".$ldb->dt[h_ix]."','$pid','".$option_name."','$pi_ix','".$total_inputstock."', NOW())";
			$sql = "insert into inventory_order_detail_tmp
							(iodt_ix,ci_ix,company_id,charger_ix,pi_ix,pid,opn_ix,opnd_ix,stock_pcode,pname,option_name,order_cnt,order_coprice,regdate) values
							('','".$ci_ix."','".$_SESSION["admininfo"]["company_id"]."','".$charger_ix."','".$pi_ix."','".$pid."','',
							'','".$stock_pcode."','".$pname."','','".$order_cnt."','".$order_coprice."',NOW())  ";
			//echo nl2br($sql)."<br>";
			//exit;
			$db->query($sql);
			//옵션테이블에 각옵션별 재고 업데이트
	}
	//해당상품 정보 테이블에 총재고 수 업데이트
	
	//$sql = "update shop_product set stock = stock + ".$total_inputstock." , safestock = ".$total_safestock.",inventory_info = '".$pi_ix."' where id = '".$pid."'";
	//$db->query($sql);

		
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 발주예정 상품으로 등록 되었습니다.');parent.self.close();</script>";
}

//print_r($_POST);
if($act == "order_change"){

	
	
	
	if(count($iod_ix) > 0 ){
		//히스토리에 옵션별로 입력
		$db->query("begin");
		for($i=0;$i < count($iod_ix);$i++){
			if($change_status != ""){
				$status_str = ",detail_status='".$change_status."' ";
			}

			if($change_status == "WP"){
				//$db->debug = true;

				if($order_infos[$iod_ix[$i]][incom_cnt] > 0){
				
						
						
						$sql = "insert into inventory_order_detail 
								select '' as iod_ix, ioid, ci_ix, pi_ix, pid, surtax_yorn, opn_ix, opnd_ix, pname, option_name, '".$order_infos[$iod_ix[$i]][incom_cnt]."' as order_cnt, order_coprice, '".$order_infos[$iod_ix[$i]][incom_cnt]."' as incom_cnt, 
								 sellprice, coprice, '".$change_status."' as detail_status, ,order_charger_ix, '".$admininfo[charger]."' as in_charger_name, '".$admininfo[charger_ix]."' as in_charger_ix, NOW() as regdate 
								from inventory_order_detail where iod_ix='".$iod_ix[$i]."' ";
						//echo $sql;
						//exit;
						$db->query($sql);

						$sql = "update inventory_order_detail set 	
									order_cnt = order_cnt - ".$order_infos[$iod_ix[$i]][incom_cnt].", in_charger_name = '".$admininfo[charger]."', in_charger_ix = '".$admininfo[charger_ix]."', incom_cnt = 0
									where iod_ix='".$iod_ix[$i]."'
									";
						//echo nl2br($sql)."<br><Br>";
						$db->query($sql);

						
						$sql = "select * from inventory_order_detail where iod_ix='".$iod_ix[$i]."' ";
						$db->query($sql);

						if($db->total){
							$db->fetch();
							$order_info = $db->dt;

							$sql = "insert into inventory_output_history_detail
								(iohd_ix,pid,opn_ix,opnd_ix,oid,pname, option_div,ci_ix, pi_ix,delivery_cnt,delivery_price, delivery_msg, delivery_type, charger_ix,regdate)
								values
								('','".$order_info[pid]."','".$order_info[opn_ix]."','".$order_info[opnd_ix]."','".$oid."','".$order_info[pname]."','".$order_info[option_name]."','".$order_info[ci_ix]."','".$order_info[pi_ix]."','".$order_info[incom_cnt]."','".$order_info[order_coprice]."','','','".$admininfo[charger_ix]."',NOW())";

							//echo nl2br($sql)."<br><br>";
							$db->query($sql);

							$sql = "insert into inventory_input_history_detail 
										(ihd_ix, pid,opn_ix, opnd_ix, oid, pname, option_name,ci_ix, pi_ix,input_cnt, input_price, input_msg, charger_ix,  regdate ) 
										values 
										('','".$order_info[pid]."','".$order_info[opn_ix]."','".$order_info[opnd_ix]."','','".$order_info[pname]."','".$order_info[option_name]."','".$order_info[ci_ix]."','".$order_info[pi_ix]."','".$order_info[incom_cnt]."','".$order_info[order_coprice]."','','".$admininfo[charger_ix]."',NOW())";
							
							$db->query($sql);

							$sql = "select * from inventory_product_stockinfo 
										where pi_ix = '".$order_info[pi_ix]."' and pid = '".$order_info[pid]."' and opn_ix = '".$order_info[opn_ix]."' and opnd_ix = '".$order_info[opnd_ix]."' ";
							$db->query($sql);

							if($db->total){
									$db->fetch();
									$sql = "update inventory_product_stockinfo set 
									stock = stock + ".$order_infos[$iod_ix[$i]][incom_cnt]." 
									where psi_ix = '".$db->dt[psi_ix]."'  ";
									//echo $sql;
									//exit;
									$db->query($sql);
							}else{
									$sql = "insert into inventory_product_stockinfo
									select '' as psi_ix,io.ci_ix,pi_ix,pid,opn_ix,opnd_ix,'' as stock_code, ".$order_infos[$iod_ix[$i]][incom_cnt]." as stock,1 as exit_order, NOW() as regdate 
									from inventory_order_detail iod , inventory_order io where iod.ioid = io.ioid and iod_ix='".$iod_ix[$i]."' ";
									//echo $sql;
									//exit;
									$db->query($sql);
							}
						}

						
						//exit;
				}
			}else if($change_status == "DC"){
				if($order_infos[$iod_ix[$i]][incom_cnt] > 0){
					$sql = "update inventory_order_detail set 	
								incom_cnt='".$order_infos[$iod_ix[$i]][incom_cnt]."', in_charger_name = '".$admininfo[charger]."', in_charger_ix = '".$admininfo[charger_ix]."' ".$status_str."
								where iod_ix='".$iod_ix[$i]."'";

					$db->query($sql);
				}
			}else if($change_status == "WC"){
				/*
				$sql = "insert into inventory_order_detail 
							select '' as iod_ix, ioid, pid, opn_ix, opnd_ix, pname, option_name, '".$order_infos[$iod_ix[$i]][incom_cnt]."' as order_cnt, order_coprice, '".$order_infos[$iod_ix[$i]][incom_cnt]."' as incom_cnt, 
							sellprice, coprice, '".$change_status."' as detail_status, NOW() as regdate 
							from inventory_order_detail where iod_ix='".$iod_ix[$i]."' ";
						
				$db->query($sql);
				*/
				


				$sql = "update inventory_order_detail set 	
							incom_cnt='".$order_infos[$iod_ix[$i]][incom_cnt]."', 
							in_charger_name = '".$admininfo[charger]."', 
							in_charger_ix = '".$admininfo[charger_ix]."' ".$status_str."
							where iod_ix='".$iod_ix[$i]."'";
				$db->query($sql);

				$sql = "select * from inventory_order_detail where iod_ix='".$iod_ix[$i]."' ";
				$db->query($sql);
				

				if($db->total){	
					$db->fetch();
					$order_info = $db->dt;

					$sql = "insert into inventory_input_history_detail 
							(ihd_ix, pid,opn_ix, opnd_ix, oid, pname, option_name,ci_ix, pi_ix,input_cnt, input_price, input_msg, charger_ix, regdate ) 
							values 
							('','".$order_info[pid]."','".$order_info[opn_ix]."','".$order_info[opnd_ix]."','','".$order_info[pname]."','".$order_info[option_name]."','".$order_info[ci_ix]."','".$order_info[pi_ix]."','".$order_info[incom_cnt]."','".$order_info[order_coprice]."','발주에 의한 입고','".$admininfo[charger_ix]."',NOW())";
				
					$db->query($sql);

					$sql = "select * from inventory_product_stockinfo where pi_ix = '".$order_info[pi_ix]."' and pid = '".$order_info[pid]."' and opn_ix = '".$order_info[opn_ix]."' and opnd_ix = '".$order_info[opnd_ix]."' ";
					$db->query($sql);

					if($db->total){
							$db->fetch();
							$sql = "update inventory_product_stockinfo set 
							stock = stock + ".$order_infos[$iod_ix[$i]][incom_cnt]." 
							where psi_ix = '".$db->dt[psi_ix]."'  ";
							//echo $sql;
							//exit;
							$db->query($sql);
					}else{
							$sql = "insert into inventory_product_stockinfo
							select '' as psi_ix,io.ci_ix,pi_ix,pid,opn_ix,opnd_ix,'' as stock_code, ".$order_infos[$iod_ix[$i]][incom_cnt]." as stock,1 as exit_order, NOW() as regdate 
							from inventory_order_detail iod , inventory_order io where iod.ioid = io.ioid and iod_ix='".$iod_ix[$i]."' ";
							//echo $sql;
							//exit;
							$db->query($sql);
					}
				}
			}else{
				$sql = "update inventory_order_detail set 	
							incom_cnt='".$order_infos[$iod_ix[$i]][incom_cnt]."' ".$status_str."
							where iod_ix='".$iod_ix[$i]."'
							";
				//echo nl2br($sql)."<br><Br>";
				$db->query($sql);
			}

			$sql = "select * from inventory_order_detail where iod_ix='".$iod_ix[$i]."' ";
			$db->query($sql);
			$db->fetch();
			$order_info = $db->dt;

			UpdateProductStockInfo($order_info);
			
			

			//옵션테이블에 각옵션별 재고 업데이트
			
			//$sql = "update shop_product_options_detail set option_stock = option_stock + ".$input_size[$i].", option_safestock = ".$safestock[$i]." where id = '".$opnd_ix[$i]."'";
			//$db->query($sql);

			


		}
		$db->query("commit");


		if($change_status != ""){
			$status_str = ",status='".$change_status."' ";
		}

		$sql = "update inventory_order set 				
					limit_priod='".$limit_priod."',	etc='".$etc."' ".$status_str."
					where ioid='".$ioid."' ";

					//order_charger='".$order_charger."',
					//incom_company_ix='".$incom_company_ix."',
					//total_price='".$total_price."',
					//total_add_price='".$total_add_price."',
					//incom_company_charger='".$incom_company_charger."',
		//echo nl2br($sql)."<br><Br>";
		$db->query($sql);

		

	


	}else{// 옵션이 없을때

	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('발주정보가 정상적으로 수정 되었습니다.');parent.document.location.reload();</script>";
}

/*


CREATE TABLE IF NOT EXISTS `inventory_order_detail_tmp` (
  iodt_ix int(10) default NULL auto_increment COMMENT '인덱스',
  ci_ix int(6) unsigned NOT NULL COMMENT '입고처키',
  company_id varchar(32) NOT NULL COMMENT '회사키',
  charger_ix varchar(32) NOT NULL COMMENT '회원키',
  pi_ix int(6) unsigned NOT NULL COMMENT '예정보관장소',
  pid int(10) unsigned zerofill default NULL COMMENT '상품아이디',
  opn_ix int(6) unsigned default NULL COMMENT '상품물류 코드',
  opnd_ix int(10) unsigned default NULL COMMENT '상품물류 옵션코드',
  stock_pcode varchar(30) default NULL COMMENT '상품물류 코드',
  pname varchar(100) default NULL COMMENT '상품명',
  option_name varchar(100) default NULL COMMENT '옵션명(규격)',
  order_cnt int(8) default NULL COMMENT '발주수량',
  order_coprice int(10) default NULL COMMENT '발주 견적가(공급가)',
  regdate datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY  (iodt_ix)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='임시발주내역 상세정보';

CREATE TABLE IF NOT EXISTS `inventory_product_stockinfo` (
  psi_ix int(10) default NULL auto_increment COMMENT '인덱스',
  ci_ix int(6) unsigned NOT NULL COMMENT '입고처키',
  pi_ix int(6) unsigned NOT NULL COMMENT '보관장소',
  pid int(10) unsigned zerofill default NULL COMMENT '상품아이디',
  opn_ix int(6) unsigned default NULL COMMENT '상품물류 코드',
  opnd_ix int(10) unsigned default NULL COMMENT '상품물류 옵션코드',
  stock_pcode varchar(30) default NULL COMMENT '상품물류 코드',
  stock int(8) default NULL COMMENT '재고',
  exit_order int(4) default NULL COMMENT '출고우선순위',
  regdate datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY  (psi_ix)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='상품별 재고 상세정보';

*/
?>