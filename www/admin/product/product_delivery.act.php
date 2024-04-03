<?
include("../../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");

$db = new Database;
$db2 = new Database;
$db3 = new Database;

if($act == "template_insert"){
	
	$company_id = trim($company_id);

	if($is_basic_template == ""){	//개별상품에서 넘어왓을경우 기본지정없음
		$sql = "select * from shop_delivery_template where company_id = '".$company_id."' and is_basic_template = '1' and product_sell_type = '".$product_sell_type."'";
		$db->query($sql);
		$db->fetch();

		if($db->total > 0){	//기본템플릿 지정이 되어 있을경우 0으로 입력 
			$add_basic_template = '0';
		}else{				//기본템플릿 지정이 안되어 있을경우 해당 템플렛을 기본템플릿으로 설정한다.
			$add_basic_template = '1';
		}
	}else{	//셀러배송정책에서 추가시 지정값 있음
		$add_basic_template = $is_basic_template;

		$sql = "select * from shop_delivery_template where company_id = '".$company_id."' and is_basic_template = '1' and product_sell_type = '".$product_sell_type."'";
		$db->query($sql);
		$db->fetch();

		if($db->total > 0){	//기본템플릿 지정이 되어 있을경우 0으로 입력 
			
			if($is_basic_template == '1'){	//기본정책이 존재하고 .. 기본정책으로 추가시 기존 정책 0 으로 하고 새로운정책을 1로 한다
				$sql = "update shop_delivery_template set
						is_basic_template = '0'
					where
						company_id = '".$company_id."'
						and product_sell_type = '".$product_sell_type."'";
				$db->query($sql);
				$add_basic_template = '1';
			}else{
				$add_basic_template = '0';
			}

		}else{				//기본템플릿 지정이 안되어 있을경우 해당 템플렛을 기본템플릿으로 설정한다.
			$add_basic_template = '1';
		}

	}

		$sql = "insert into shop_delivery_template set
					mall_ix = '".$mall_ix."',
					company_id = '".$company_id."',
					template_name = '".$template_name."',
					is_basic_template = '".$add_basic_template."',
					product_sell_type = '".$product_sell_type."',
					delivery_div = '".$delivery_div."',
					use_delivery_div_tekbae = '".$use_delivery_div_tekbae."',
					use_delivery_div_quick = '".$use_delivery_div_quick."',
					use_delivery_div_truck = '".$use_delivery_div_truck."',
					use_delivery_div_self = '".$use_delivery_div_self."',
					use_delivery_div_direct = '".$use_delivery_div_direct."',
					tekbae_ix = '".$tekbae_ix."',
					quick_company = '".$quick_company."',
					quick_phone = '".$quick_phone."',
					quick_service_addr = '".$quick_service_addr."',
					visit_info_addr_ix = '".$visit_info_addr_ix."',
					use_free_gift = '".$use_free_gift."',
					free_gift_name = '".$free_gift_name."',
					truck_company = '".$truck_company."',
					truck_phone = '".$truck_phone."',
					truck_person = '".$truck_person."',
					truck_person_phone = '".$truck_person_phone."',
					is_basic_direct = '".$is_basic_direct."',
					direct_ddc_ix = '".$direct_ddc_ix."',
					delivery_basic_policy = '".$delivery_basic_policy."',
					delivery_package = '".$delivery_package."',
					delivery_policy = '".$delivery_policy."',
					delivery_price = '".$delivery_price."',
					extra_charge = '".$extra_charge."',
					delivery_cnt_price = '".$delivery_cnt_price."',
					factory_info_addr_ix = '".$factory_info_addr_ix."',
					delivery_unit_price = '".$delivery_unit_price."',
					return_shipping_price = '".$return_shipping_price."',
					exchange_shipping_price = '".$exchange_shipping_price."',
					return_shipping_cnt = '".$return_shipping_cnt."',
					exchange_info_addr_ix = '".$exchange_info_addr_ix."',
					delivery_corprice = '".$delivery_corprice."',
					packing_corprice = '".$packing_corprice."',
					product_prohibition_text = '".$product_prohibition_text."',
					product_return_text = '".$product_return_text."',
					delivery_policy_text = '".$delivery_policy_text."',
					delivery_policy_text_m = '".$delivery_policy_text_m."',
					delivery_region_use='".$delivery_region_use."',
					delivery_jeju_price='".$delivery_jeju_price."',
					delivery_addr_use = '".$delivery_addr_use."',
					delivery_pay_metho_text = '".$delivery_pay_metho_text."',
					free_shipping_term = '".$free_shipping_term."',
					regdate = NOW()";

		$db->query($sql);
		$dt_ix = $db->insert_id();

		if($delivery_policy == "3"){
			if($dt_ix){
				$sql = "delete from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$product_sell_type."' and delivery_policy_type = '".$delivery_policy."'";
				$db->query($sql);

				foreach($delivery_price_terms as $seq => $val){
					if($val[delivery_price] != "" && $val[delivery_basic_terms] != ""){
						$sql = "insert into shop_delivery_terms set
									dt_ix = '".$dt_ix."',
									seq = '".$seq."',
									product_sell_type = '".$product_sell_type."',
									delivery_policy_type = '".$delivery_policy."',
									delivery_price = '".$val[delivery_price]."',
									delivery_basic_terms = '".$val[delivery_basic_terms]."',
									regdate = NOW()
									";
						$db->query($sql);
					}
				}
			}
		}else if($delivery_policy == "4"){
            if($dt_ix){
                $sql = "delete from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$product_sell_type."' and delivery_policy_type = '".$delivery_policy."'";
                $db->query($sql);

                foreach($delivery_cnt_terms as $seq => $val){
                    if($val[delivery_price] != "" && $val[delivery_basic_terms] != ""){
                        $sql = "insert into shop_delivery_terms set
									dt_ix = '".$dt_ix."',
									seq = '".$seq."',
									product_sell_type = '".$product_sell_type."',
									delivery_policy_type = '".$delivery_policy."',
									delivery_price = '".$val[delivery_price]."',
									delivery_basic_terms = '".$val[delivery_basic_terms]."',
									regdate = NOW()
									";
                        $db->query($sql);
                    }
                }
            }
        }else if($delivery_policy == "7"){
            if($dt_ix){
                $sql = "delete from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$product_sell_type."' and delivery_policy_type = '".$delivery_policy."'";
                $db->query($sql);

                foreach($delivery_weight_terms as $seq => $val){
                    if($val[delivery_price] != "" && $val[delivery_basic_terms] != ""){
                        $sql = "insert into shop_delivery_terms set
									dt_ix = '".$dt_ix."',
									seq = '".$seq."',
									product_sell_type = '".$product_sell_type."',
									delivery_policy_type = '".$delivery_policy."',
									delivery_price = '".$val[delivery_price]."',
									delivery_basic_terms = '".$val[delivery_basic_terms]."',
									regdate = NOW()
									";
                        $db->query($sql);
                    }
                }
            }
        }

		if($region_delivery_type == 1){	//shop_product_region_delivery
			$sql = "update shop_product_region_delivery set insert_yn = 'N' where dt_ix='".$dt_ix."' and  product_sell_type='".$product_sell_type."' ";
			$db->query($sql);

			for($j=0;$j<count($region_name_text);$j++){

				if($region_name_text[$j] !=''){

					$sql = "select * from shop_product_region_delivery where prd_ix='".$prd_ix[$j]."' and pid = '".$pid."' and  product_sell_type='".$product_sell_type."' ";
					$db2->query($sql);

					if($db2->total && $prd_ix[$j]){
						$sql = "update shop_product_region_delivery set
									region_name_text='".$region_name_text[$j]."',
									region_name_price='".$region_name_price[$j]."',
									insert_yn='Y',
									regdate=NOW() 
								where 
									prd_ix='".$prd_ix[$j]."'
									and dt_ix = '".$dt_ix."'";
						$db2->query($sql);
					}else{
						$sql = "insert into shop_product_region_delivery (prd_ix,dt_ix,pid,product_sell_type, region_delivery_type,region_name_text, region_name_price,insert_yn,regdate)
								values
								('','".$dt_ix."','".$pid."','".$product_sell_type."','$region_delivery_type','".$region_name_text[$j]."','".$region_name_price[$j]."','Y',NOW())";
						$db2->sequences = "SHOP_REGION_DELIVERY_SEQ";
						$db2->query($sql);
					}
				}
			}

			$sql = "delete from shop_product_region_delivery where insert_yn = 'N' and dt_ix='".$dt_ix."' ";
			$db->query($sql);
		}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('정상적으로 처리되었습니다.');</script>");

	if($page_type == "goods_input"){//상품등록페이지에서 추가시 템플릿 선택 select를 갱신해준다
//$product_sell_type='R',$delivery_div='1'
		echo ("<script language='JavaScript' src='../js/jquery-1.4.js'></Script>\n
			<script>	
				var company_id = '".$company_id."';
				var product_sell_type = '".$product_sell_type."';
				var delivery_div = '".$delivery_div."';

					$.ajax({
				????url : '../seller_search.act.php',
				????type : 'POST',
				????data : {company_id:company_id,
							product_sell_type:product_sell_type,
							delivery_div:delivery_div,
							act:'select_delivery_template'
							},
				????dataType: 'json',
				????error: function(data,error){// 실패시 실행함수 
				????????alert(error);},
				????success: function(args){
							if(args != null){
								$('#dt_ix_".$product_sell_type."_".$delivery_div."',parent.opener.document).empty();
								$('#dt_ix_".$product_sell_type."_".$delivery_div."',parent.opener.document).append('<option value=>배송정책 선택</option>');
								$.each(args, function(index, entry){
									//alert(index);
									$('#dt_ix_".$product_sell_type."_".$delivery_div."',parent.opener.document).append('<option value='+index+' selected>'+entry+'</option>');
								});
								parent.self.close();
							}
				????????}
				????});
				
				//self.close();
			</script>
		");
	}else{
		if($mmode == "pop"){
			echo("<script>parent.self.close();parent.opener.document.location.reload();</script>");
		}else{
			echo("<script>parent.document.location.href = 'product_delivery_template.php?info_type=".$info_type."&tmp_ix=".$tmp_ix."&mmode=pop&page_type=".$page_type."&company_id=".$company_id."';</script>");
		}
		exit;
	}

}


if($act == "template_update"){
	
	if($dt_ix){

		if($page_type == 'seller'){	//배송정책에서 추가햇을경우 2014-04-03 
			if($is_basic_template == "1"){//기존배송비로 지정할경우 
				$sql = "update shop_delivery_template set 
							is_basic_template = '0' 
						where 
							dt_ix not in ('".$dt_ix."')
							and company_id = '".$company_id."' 
							and product_sell_type = '".$product_sell_type."'";	//전체를 사용안함으로 한후 
				$db->query($sql);

				$sql = "update shop_delivery_template set 
							is_basic_template = '1'
						where
							dt_ix = '".$dt_ix."'
							and company_id = '".$company_id."' ";	//해당 배송정책만 사용함으로 변경
				$db->query($sql);
				
			}else if($is_basic_template == "0"){//기본 배송정책을 해제할경우 기본 배송정책이 존재하지 않으면 실행하지 말아야함. (기본배송정책은 무조건 하나가 존재해야함)

				$sql = "select dt_ix from shop_delivery_template where is_basic_template = '1' and company_id = '".$company_id."' and product_sell_type = '".$product_sell_type."'";
				$db->query($sql);

				if($db->total > 0){
					$db->fetch();
					$bsic_dt_ix = $db->dt[dt_ix];
					if($dt_ix == $bsic_dt_ix){
						echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('기본배송정책은 무조건 하나가 존재해야 합니다.');</script>");
						echo("<script>parent.document.location.href = 'product_delivery_template.php?info_type=".$info_type."&dt_ix=".$dt_ix."&mmode=pop&page_type=".$page_type."&company_id=".$company_id."';</script>");
						exit;
					}
				}else{
					$is_basic_template == "1";
				}

			}
		}else{	//개별상품 등록에서 추가햇을경우 2014-04-03 

			if($is_basic_template == ""){	//개별상품에서 넘어왓을경우 기본지정없음
				$sql = "select * from shop_delivery_template where company_id = '".$company_id."' and is_basic_template = '1' and dt_ix = '".$dt_ix."'";
				$db->query($sql);
				$db->fetch();

				if($db->total > 0){	//기본템플릿 지정이 되어 있을경우 0으로 입력 
					$is_basic_template = '0';
				}else{				//기본템플릿 지정이 안되어 있을경우 해당 템플렛을 기본템플릿으로 설정한다.
					$is_basic_template = '1';
				}
			}else{	//셀러배송정책에서 추가시 지정값 있음
				$is_basic_template = $is_basic_template;

				$sql = "update shop_delivery_template set
							is_basic_template = '0'
						where
							dt_ix = '".$dt_ix."'";
				$db->query($sql);
				
			}
		}

		$sql = "update shop_delivery_template set
					mall_ix = '".$mall_ix."',
					template_name = '".$template_name."',
					product_sell_type = '".$product_sell_type."',
					delivery_div = '".$delivery_div."',
					use_delivery_div_tekbae = '".$use_delivery_div_tekbae."',
					use_delivery_div_quick = '".$use_delivery_div_quick."',
					use_delivery_div_truck = '".$use_delivery_div_truck."',
					use_delivery_div_self = '".$use_delivery_div_self."',
					use_delivery_div_direct = '".$use_delivery_div_direct."',
					tekbae_ix = '".$tekbae_ix."',
					quick_company = '".$quick_company."',
					quick_phone = '".$quick_phone."',
					quick_service_addr = '".$quick_service_addr."',
					visit_info_addr_ix = '".$visit_info_addr_ix."',
					use_free_gift = '".$use_free_gift."',
					free_gift_name = '".$free_gift_name."',
					truck_company = '".$truck_company."',
					truck_phone = '".$truck_phone."',
					truck_person = '".$truck_person."',
					truck_person_phone = '".$truck_person_phone."',
					is_basic_direct = '".$is_basic_direct."',
					direct_ddc_ix = '".$direct_ddc_ix."',
					delivery_basic_policy = '".$delivery_basic_policy."',
					delivery_package = '".$delivery_package."',
					delivery_policy = '".$delivery_policy."',
					delivery_price = '".$delivery_price."',
					extra_charge = '".$extra_charge."',
					delivery_cnt_price = '".$delivery_cnt_price."',
					factory_info_addr_ix = '".$factory_info_addr_ix."',
					delivery_unit_price = '".$delivery_unit_price."',
					return_shipping_price = '".$return_shipping_price."',
					exchange_shipping_price = '".$exchange_shipping_price."',
					return_shipping_cnt = '".$return_shipping_cnt."',
					exchange_info_addr_ix = '".$exchange_info_addr_ix."',
					delivery_corprice = '".$delivery_corprice."',
					packing_corprice = '".$packing_corprice."',
					product_prohibition_text = '".$product_prohibition_text."',
					product_return_text = '".$product_return_text."',
					delivery_policy_text = '".$delivery_policy_text."',
					delivery_policy_text_m = '".$delivery_policy_text_m."',
					delivery_region_use='".$delivery_region_use."',
					delivery_jeju_price='".$delivery_jeju_price."',
					delivery_addr_use = '".$delivery_addr_use."',
					delivery_pay_metho_text = '".$delivery_pay_metho_text."',
					free_shipping_term = '".$free_shipping_term."',
					editdate = NOW()
				where 
					company_id = '".$company_id."'
					and dt_ix = '".$dt_ix."'";

		$db->query($sql);

		if($delivery_policy == "3"){
			if($dt_ix){
				$sql = "delete from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$product_sell_type."' and delivery_policy_type = '".$delivery_policy."'";
				$db->query($sql);

				foreach($delivery_price_terms as $seq => $val){
					if($val[delivery_price] != "" && $val[delivery_basic_terms] != ""){
						$sql = "insert into shop_delivery_terms set
									dt_ix = '".$dt_ix."',
									seq = '".$seq."',
									product_sell_type = '".$product_sell_type."',
									delivery_policy_type = '".$delivery_policy."',
									delivery_price = '".$val[delivery_price]."',
									delivery_basic_terms = '".$val[delivery_basic_terms]."',
									regdate = NOW()
									";
						$db->query($sql);
					}
				}
			}
		}else if($delivery_policy == "4"){
            if($dt_ix){
                $sql = "delete from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$product_sell_type."' and delivery_policy_type = '".$delivery_policy."'";
                $db->query($sql);

                foreach($delivery_cnt_terms as $seq => $val){
                    if($val[delivery_price] != "" && $val[delivery_basic_terms] != ""){
                        $sql = "insert into shop_delivery_terms set
									dt_ix = '".$dt_ix."',
									seq = '".$seq."',
									product_sell_type = '".$product_sell_type."',
									delivery_policy_type = '".$delivery_policy."',
									delivery_price = '".$val[delivery_price]."',
									delivery_basic_terms = '".$val[delivery_basic_terms]."',
									regdate = NOW()
									";
                        $db->query($sql);
                    }
                }
            }
        }else if($delivery_policy == "7"){
            if($dt_ix){
                $sql = "delete from shop_delivery_terms where dt_ix = '".$dt_ix."' and product_sell_type = '".$product_sell_type."' and delivery_policy_type = '".$delivery_policy."'";
                $db->query($sql);

                foreach($delivery_weight_terms as $seq => $val){
                    if($val[delivery_price] != "" && $val[delivery_basic_terms] != ""){
                        $sql = "insert into shop_delivery_terms set
									dt_ix = '".$dt_ix."',
									seq = '".$seq."',
									product_sell_type = '".$product_sell_type."',
									delivery_policy_type = '".$delivery_policy."',
									delivery_price = '".$val[delivery_price]."',
									delivery_basic_terms = '".$val[delivery_basic_terms]."',
									regdate = NOW()
									";
                        $db->query($sql);
                    }
                }
            }
        }
		
		if($region_delivery_type == 1){	//shop_product_region_delivery
			$sql = "update shop_product_region_delivery set insert_yn = 'N' where dt_ix='".$dt_ix."' and  product_sell_type='".$product_sell_type."' ";
			$db->query($sql);

			for($i=0;$i<count($region_name_text);$i++){

				if($region_name_text[$i] !=''){

					$sql = "select * from shop_product_region_delivery where prd_ix='".$prd_ix[$i]."' and pid = '".$pid."' and  product_sell_type='".$product_sell_type."' ";
					$db2->query($sql);

					if($db2->total && $prd_ix[$i]){
						$sql = "update shop_product_region_delivery set
									region_name_text='".$region_name_text[$i]."',
									region_name_price='".$region_name_price[$i]."',
									insert_yn='Y',
									regdate=NOW() 
								where 
									prd_ix='".$prd_ix[$i]."'
									and dt_ix = '".$dt_ix."'";
						$db2->query($sql);
					}else{
						$sql = "insert into shop_product_region_delivery (prd_ix,dt_ix,pid,product_sell_type, region_delivery_type,region_name_text, region_name_price,insert_yn,regdate)
								values
								('','".$dt_ix."','".$pid."','$product_sell_type','$region_delivery_type','".$region_name_text[$i]."','".$region_name_price[$i]."','Y',NOW())";
						$db2->sequences = "SHOP_REGION_DELIVERY_SEQ";
						$db2->query($sql);
					}
				}
			}

			$sql = "delete from shop_product_region_delivery where insert_yn = 'N' and dt_ix='".$dt_ix."' ";
			$db->query($sql);
		}

		if($mobile_delivery_policy_img_size > 0){

			if(!file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/delivery/")){
				mkdir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/delivery/");
				chmod($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/delivery/",0777);
			}

			$basic_img_src = $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/delivery/".$company_id.".gif";
			copy($_FILES['mobile_delivery_policy_img']['tmp_name'], $_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/images/delivery/".$company_id.".gif");
		}
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('정상적으로 처리되었습니다.');</script>");
	if($is_close){
		echo("<script language='javascript'>parent.opener.document.location.reload();parent.self.close();</script>");
	}else{
		echo("<script>parent.document.location.href = 'product_delivery_template.php?info_type=".$info_type."&dt_ix=".$dt_ix."&mmode=pop&page_type=".$page_type."&company_id=".$company_id."';</script>");
	}

}

if($act == "delete_template"){

	if($dt_ix){

		$sql = "select * from shop_product_delivery where dt_ix = '".$dt_ix."' and company_id = '".$company_id."'";
		$db->query($sql);
		$db->fetch();

		if($db->total > 0){
			echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('해당 배송정책을 사용하는 상품이 존재합니다.');</script>");
			if($page_type == 'seller'){
					echo("<script>parent.document.location.href = '../seller/seller_delivery_info.php';</script>");
				}else{
					echo("<script>parent.document.location.href = '../store/delivery.php';</script>");
				}
			exit;

		}else{

			$sql = "select
						is_basic_template
					from
						shop_delivery_template
					where
						dt_ix = '".$dt_ix."'
						and company_id = '".$company_id."'
						limit 0,1";
			$db->query($sql);
			$db->fetch();

			if($db->dt[is_basic_template] == '1'){
				echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('기본 배송정책은 삭제할수 없습니다.');</script>");
				if($page_type == 'seller'){
					echo("<script>parent.document.location.href = '../seller/seller_delivery_info.php';</script>");
				}else{
					echo("<script>parent.document.location.href = '../store/delivery.php';</script>");
				}
			}else{

				$delete = "delete from shop_delivery_template where dt_ix = '".$dt_ix."' and company_id = '".$company_id."'";
				$db->query($delete);

				echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('정상적으로 처리되었습니다.');</script>");
				if($page_type == 'seller'){
					echo("<script>parent.document.location.href = '../seller/seller_delivery_info.php';</script>");
				}else{
					echo("<script>parent.document.location.href = '../store/delivery.php';</script>");
				}

			}

		}

	}else{
		echo("<script language='javascript' src='../js/message.js.php'></script><script>alert('존재하지 않는 배송정책 입니다.');</script>");
		if($page_type == 'seller'){
			echo("<script>parent.document.location.href = '../seller/seller_delivery_info.php';</script>");
		}else{
			echo("<script>parent.document.location.href = '../store/delivery.php';</script>");
		}
	}

}


if($act == "insert"){
	
	//$product_delivery_type = 'R';	//소매로 일단 지정
	$sql = "select
				count(pid) as cnt
			from
				shop_product_delivery
			where
				pid = '".$pid."'
				and product_delivery_type = '".$product_delivery_type."'";	//W:도매 R:소매
	$db->query($sql);
	$db->fetch();
	$total_delivery = $db->dt[cnt];

	if($total_delivery > 0){
		$sql = "update shop_product_delivery set
					mall_send_tekbae_use = '$mall_send_tekbae_use',
					mall_send_quick_use = '$mall_send_quick_use',
					mall_send_truck_use = '$mall_send_truck_use',
					mall_send_self_use = '$mall_send_self_use',
					mall_send_direct_use = '$mall_send_direct_use',
					order_price = '$order_price',
					order_price_shipping = '$order_price_shipping',
					order_cnt_free_shipping_use = '$order_cnt_free_shipping_use',
					free_shipping_order_cnt = '$free_shipping_order_cnt',
					dump_shipping_use = '$dump_shipping_use',
					free_cost_price = '$free_cost_price',
					basic_send_cost_tekbae = '$basic_send_cost_tekbae', 
					delivery_basic_policy = '$delivery_basic_policy', 
					delivery_product_policy='$delivery_product_policy',
					delivery_region_policy = '$delivery_region_policy',
					return_shipping_price = '$return_shipping_price',
					return_shipping_cnt = '$return_shipping_cnt',
					exchange_shipping_price = '$exchange_shipping_price',
					delivery_region_use='$delivery_region_use',
					delivery_jeju_price='".$delivery_jeju_price."',
					editdate = NOW()
				where
					pid='".$pid."'
					and product_delivery_type = '".$product_delivery_type."'
				";
	}else{
		$sql = "insert into shop_product_delivery set
					pid='".$pid."',
					product_delivery_type = '$product_delivery_type',
					mall_send_tekbae_use = '$mall_send_tekbae_use',
					mall_send_quick_use = '$mall_send_quick_use',
					mall_send_truck_use = '$mall_send_truck_use',
					mall_send_self_use = '$mall_send_self_use',
					mall_send_direct_use = '$mall_send_direct_use',
					order_price = '$order_price',
					order_price_shipping = '$order_price_shipping',
					order_cnt_free_shipping_use = '$order_cnt_free_shipping_use',
					free_shipping_order_cnt = '$free_shipping_order_cnt',
					dump_shipping_use = '$dump_shipping_use',
					free_cost_price = '$free_cost_price',
					basic_send_cost_tekbae = '$basic_send_cost_tekbae', 
					delivery_basic_policy = '$delivery_basic_policy', 
					delivery_product_policy='$delivery_product_policy',
					delivery_region_policy = '$delivery_region_policy',
					return_shipping_price = '$return_shipping_price',
					return_shipping_cnt = '$return_shipping_cnt',
					exchange_shipping_price = '$exchange_shipping_price',
					delivery_region_use='$delivery_region_use',
					delivery_jeju_price='".$delivery_jeju_price."',
					regdate = NOW()
					";
	}

	$db->query($sql);
	//$pd_ix = $db->insert_id();

	if($region_delivery_type == 1){	//shop_product_region_delivery
		$sql = "update shop_product_region_delivery set insert_yn = 'N' where pid='".$pid."' and  product_delivery_type='".$product_delivery_type."' ";
		$db->query($sql);

		for($i=0;$i<count($region_name_text);$i++){

			if($region_name_text[$i] !=''){

				$sql = "select * from shop_product_region_delivery where prd_ix='".$prd_ix[$i]."' and pid = '".$pid."' and  product_delivery_type='".$product_delivery_type."' ";
				$db2->query($sql);

				if($db2->total && $prd_ix[$i]){
					$sql = "update shop_product_region_delivery set
								region_name_text='".$region_name_text[$i]."',
								region_name_price='".$region_name_price[$i]."',
								insert_yn='Y',
								regdate=NOW() 
							where 
								prd_ix='".$prd_ix[$i]."'
								and pid = '".$pid."'";
					$db2->query($sql);
				}else{
					$sql = "insert into shop_product_region_delivery (prd_ix,pid,product_delivery_type, region_delivery_type,region_name_text, region_name_price,insert_yn,regdate)
							values
							('','".$pid."','$product_delivery_type','$region_delivery_type','".$region_name_text[$i]."','".$region_name_price[$i]."','Y',NOW())";
					$db2->sequences = "SHOP_REGION_DELIVERY_SEQ";
					$db2->query($sql);
				}
			}
		}

		$sql = "delete from shop_product_region_delivery where insert_yn = 'N' and pid='".$pid."' ";
		$db->query($sql);
	}

	echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리되었습니다.');</script>");
	echo("<script>parent.document.location.href = 'product_delivery_info.php?info_type=".$info_type."&pid=".$pid."&mmode=pop';</script>");
}

?>