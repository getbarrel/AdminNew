<?php
		require_once 'SOAP/Value.php';
		require_once 'SOAP/Fault.php';
		require_once 'SOAP/Client.php';

		include('../../../include/xmlWriter.php');
		include("../../../class/database.class");

		class SOAP_FORBIZ_CoGoods_Server {
				var $__dispatch_map = array();
				var $hostserver_url ;

				function SOAP_FORBIZ_CoGoods_Server() {

						$this->__dispatch_map['setHostServer'] = array('in' => array('hostserver_url' => 'string'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['getB2BCompany'] = array('in' => array('company_id' => 'string','return_type' => 'string'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['getServiceGoodsList'] = array('in' => array('company_id' => 'string','return_type' => 'string'),'out' => array('outputString' => 'string'));


						$this->__dispatch_map['getServiceDetailInfo'] = array('in' => array('pid' => 'string','parent_service_code' => 'string','service_code' => 'string','mall_ix' => 'string'),'out' => array('outputArray' => 'array'));

						$this->__dispatch_map['ServiceApply'] = array('in' => array('mall_domain_key' => 'string','payment_info' => 'array','mall_domain' => 'string'),'out' => array('outputArray' => 'array'));

						$this->__dispatch_map['getCoGoodsByServer'] = array('in' => array('useable_service' => 'array','search_rules' => 'array','paginginfo' => 'array','select_type' => 'string'),'out' => array('outputString' => 'string'));

						$this->__dispatch_map['getMyServiceInfo'] = array('in' => array('mall_domain' => 'string','mall_domain_id' => 'string','mall_domain_key' => 'string'),'out' => array('outputArray' => 'array'));



						$this->__dispatch_map['getCategoryPathByAdmin'] = array('in' => array('cid' => 'string','depth' => 'string'),'out' => array('outputString' => 'string'));

						$this->__dispatch_map['getCoGoodsInfoByServer'] = array('in' => array('goodss_pid' => 'array'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['getCoGoodsDisplayOptionInfoByServer'] = array('in' => array('goodss_pid' => 'array'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['getCoGoodsOptionInfoByServer'] = array('in' => array('goodss_pid' => 'array'),'out' => array('outputString' => 'string'));

						$this->__dispatch_map['GoodsOrderReg'] = array('in' => array('goodss_order' => 'array'),'out' => array('outputString' => 'string'));



						$this->__dispatch_map['updateSellerInfo'] = array('in' => array('goods_copy' => 'string','my_company_id' => 'string','co_company_id' => 'string'),'out' => array('outputString' => 'string'));

						$this->__dispatch_map['SellerShopAdd'] = array('in' => array('sellerInfos' => 'array', 'auth_key' => 'string'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['SellerShopApply'] = array('in' => array('company_id' => 'string', 'co_company_id' => 'array'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['SellerRegAuth'] = array('in' => array('status' => 'string','co_company_id' => 'string'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['getSellerShopApplyList'] = array('in' => array('company_id' => 'string'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['SellerShopApproval'] = array('in' => array('company_id' => 'string'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['SellerShopCancel'] = array('in' => array('company_id' => 'string', 'co_company_id' => 'array'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['SellerShopSellerCancel'] = array('in' => array('company_id' => 'string', 'co_company_id' => 'array'),'out' => array('outputString' => 'string'));

						$this->__dispatch_map['SellerShopInsert'] = array('in' => array('sellerInfos' => 'array', 'auth_key' => 'string'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['SellerShopGoodsReg'] = array('in' => array('company_id' => 'string', 'goodsinfos' => 'array', 'copy_type' => 'string'),'out' => array('outputString' => 'string'));

						$this->__dispatch_map['setGoodsCopyHistory'] = array('in' => array('company_id' => 'string','co_company_id' => 'string', 'pid' => 'string', 'copy_type' => 'string'),'out' => array('outputString' => 'string'));


						$this->__dispatch_map['SellerShopGoodDisplayOptionReg'] = array('in' => array('company_id' => 'string', 'local_pid' => 'string', 'display_options' => 'array'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['SellerShopGoodOptionReg'] = array('in' => array('company_id' => 'string', 'local_pid' => 'string', 'option_infos' => 'array'),'out' => array('outputString' => 'string'));




						$this->hostserver_url = "";

				}

				function setHostServer($hostserver_url){
					$this->hostserver_url = $hostserver_url;
				}
				// 클라이언트에서 쓸 간단한 함수
				function getServiceGoodsList($mall_ix="", $return_type = "list") {
					$db = new Database;

					if($return_type == "list"){

							//$sql = "SELECT * FROM service_product where disp = '1' ";
							$sql = "SELECT sp.id, sp.id as pid, sp.pname, sp.sellprice , sp.shotinfo , sp.parent_service_code, sp.service_code , smi.si_ix, smi.si_status, smi.sm_sdate, smi.sm_edate
								FROM service_product sp left join service_mall_ing smi on sp.service_code = smi.service_code  and smi.mall_ix = '".$mall_ix."'
								where sp.disp = '1' ";


							//return $sql;
							$db->query($sql);
							$total = $db->total;
							$b2b_companyinfos = $db->fetchall("object");

							$sellers_info["total"] = $total;
							$sellers_info["b2b_companyinfos"] = $b2b_companyinfos;
							$sellers_info["query"] = $sql;
							$sellers_info["message"] = $myserver_company_id ."==".  $company_id;
							return $sellers_info;
					}else{
							$sql = "SELECT sp.id, sp.id as pid, sp.pname, sp.sellprice , sp.shotinfo , sp.parent_service_code, sp.service_code , smi.si_ix, smi.si_status, smi.sm_sdate, smi.sm_edate
								FROM service_product sp left join service_mall_ing smi on sp.service_code = smi.service_code  and smi.mall_ix = '".$mall_ix."'
								where sp.disp = '1' ";
							/*
							$sql = "SELECT ccd.company_id, com_div, com_name,  com_type, com_ceo,  com_phone, com_fax, com_email, shop_name, shop_url, apply_status, seller_auth, goods_copy
									FROM common_seller_detail csd, common_company_detail ccd
									WHERE  ccd.company_id = csd.company_id and ccd.company_id = '".$company_id."' and com_type = 'S'
									ORDER BY ccd.regdate DESC";
							*/
							$db->query($sql);

							$_sellers = $db->fetchall("object");
							$sellers_info["b2b_companyinfos"] = $_sellers[0];
							$sellers_info["query"] = $sql;

							return $sellers_info;
					}
				}



				function getServiceDetailInfo($pid ,$parent_service_code, $service_code, $mall_ix=""){


					$sql = "SELECT sp.id, sp.id as pid, sp.pname, sp.sellprice , sp.shotinfo , sp.parent_service_code, sp.service_code , smi.si_ix, smi.si_status, smi.sm_sdate, smi.sm_edate
								FROM service_product sp left join service_mall_ing smi on sp.service_code = smi.service_code 	and smi.mall_ix = '".$mall_ix."'
								and smi.parent_service_code = '".$parent_service_code."' and smi.service_code = '".$service_code."'
								where sp.disp = '1' and sp.service_code = '".$service_code."' and sp.id = '".$pid."' ";

					//return $sql;
					$db = new Database;
					$db->query($sql);
					$service_infos = $db->fetchall("object");
					$service_info = $service_infos[0];
					$return_values["service_info"] =  $service_info;


					$sql = "select pod.id as opnd_ix, option_div, option_price, option_coprice, option_etc1 from service_product_options po, service_product_options_detail pod
								where po.opn_ix = pod.opn_ix and po.option_kind = 'b' and po.pid = '".$service_info[pid]."'  order by pod.id asc ";
					$db->query($sql);
					$option_info = $db->fetchall2();


					$return_values["option_info"] = $option_info;




					return $return_values;
				}


				function ServiceApply($mall_domain_key, $sellerInfos, $payment_info, $mall_domain){
					$serller_insert_result = $this->SellerShopAdd($sellerInfos);
					//return $serller_insert_result;
					if(!$serller_insert_result[result]){
						//return $serller_insert_result;
						return false;
					}

					//exit;
					$db = new Database;
					//$db->debug = true;
					// mall_domain_key를 가지고 회원정보 가져오는 부분 추가
					// 가져온 회원정보를 바탕으로 세션정보 로 된부분을 변경 ~~

					//return $payment_info;
					//exit;
					$payment_info = (array)$payment_info;
					if($payment_info[gopaymethod] == "bank") {
						$method = 0;
						$status_message_txt="무통장입금결제";
						$payment_status = "IR";
					}else if($payment_info[gopaymethod] == "Card") {
						$method = 1;
						$status_message_txt="카드결제";
						$payment_status = "IC";
					}else if($payment_info[gopaymethod] == "iche") {
						$method = 2;
						$status_message_txt="계좌이체결제";
						$payment_status = "IC";
					}else if($payment_info[gopaymethod] == "virtual") {
						$method = 3;
						$status_message_txt="가상계좌결제";
						$payment_status = "IR";
					}

					$sql = "select  sp.product_type, pod.pid, pod.option_div,pod.option_coprice,  pod.option_price, pod.option_etc1 as service_value, sp.sellprice
								from service_product sp, service_product_options_detail pod
								where sp.id = pod.pid and pod.id = '".$payment_info[options]."'  ";

					// 서비스 service_unit_value 값을 가지고 정보가 같으면 service_ing_detail_etc 의 service_value 값을 업데이트 되지 않아도 된다.
					// 연장 프로세스 가 고려되어 처리 되어져야 한다.

					$result[sql2] = $sql;
					$db->query($sql);
					$db->fetch();
					if(!$db->total){
						$result[bool] = false;
						$result[message] = "선택된 서비스 항목 정보가 정확하지 않습니다. 문제가 계속될경우 [ 몰스토리 고객센타 : 1600-2028 ]로 문의 주시기 바랍니다.";
						return $result;
					}else{
						$service_info = $db->dt;
						$payment_price = $service_info[sellprice]*$payment_info[unit_cnt]*$service_info[service_value];
					}

					if($payment_info[si_ix] == "" || $apply_type == "C"){// 신규 신청일 경우
							$db->query("SELECT si_ix FROM service_mall_ing WHERE mall_ix= '".$payment_info[mall_ix]."' and service_code='".$payment_info[service_code]."' ");

							if(!$db->total){


								$apply_type = "N";
								$sql = "insert into service_mall_ing set
											si_ix='".$payment_info[si_ix]."',
											parent_service_code='".$payment_info[parent_service_code]."',
											service_code='".$payment_info[service_code]."',
											mall_ix='".$payment_info[mall_ix]."',
											sp_name='".$payment_info[pname]."',
											si_status='SR',
											set_status='SR',
											regdate=NOW() ";
							//return $sql;

								/*
								$sql = "insert into service_ing SET
											service_div = '".$payment_info[service_div]."',
											solution_div = '".$payment_info[solution_div]."',
											mem_ix = '".$mall_info[mem_ix]."',
											sp_name = '".$payment_info[sp_name]." - ".($service_info[unit_value]*$payment_info[unit_cnt])."".($service_info[unit] == "CNT" ? "개":$service_info[unit])."',
											service_unit_value = '".($service_info[unit_value]*$payment_info[unit_cnt])."',
											service_unit = '".$service_info[unit]."',
											regdate = NOW()";
								*/
								$result[sql3] = $sql;
								$db->query($sql);

								$db->query("SELECT si_ix FROM service_mall_ing WHERE si_ix=LAST_INSERT_ID()");
								$db->fetch();
								$si_ix = $db->dt[si_ix];
							}else{
								$db->fetch();
								$si_ix = $db->dt[si_ix];

							}

							// si_status : SR , set_status : SR
							// 신청서입력
							/*
							$sql = "insert into service_ing_detail_etc SET
									si_ix = '".$si_ix."',
									unit = '".$service_info[unit]."',
									service_value = '".$service_info[unit_value]*$payment_info[unit_cnt]."',
									regdate = now()";
							$result[sql4] = $sql;
							$db->query($sql);
							*/
							/*
							if($apply_type == "C"){
								SetServiceHistory($db, $si_ix, $status_message_txt, "서비스 신규 신청(변경)", $payment_price, $sdate, $edate, $payment_info[mall_term],$payment_info[oid]);
							}else{
								SetServiceHistory($db, $si_ix, $status_message_txt, "서비스 신규 신청", $payment_price, $sdate, $edate, $payment_info[mall_term],$payment_info[oid]);
							}
							*/

					}else{
						$apply_type = "E";
						$si_ix = $payment_info[si_ix];
					}
					//exit;
					// 해당 서비스에 대한 정보가 있는지 확인
					// 같은 oid 로 정보가 있으면


					// si_ix 값이 있다는건 연장 이라는 이야기임
					// 기본 무료서비스가 있는경우도 si_ix 가 있어야 함
					$user_ip=$_SERVER["REMOTE_ADDR"];
					$user_agent=$_SERVER["HTTP_USER_AGENT"];
					$member_detail = (array)$sellerInfos->member_detail;
					$company_detail = (array)$sellerInfos->company_detail;

//return $company_detail;

					$db->query("SELECT mall_ix FROM service_mall_order WHERE oid='".$payment_info[oid]."' ");
					//$db->fetch();
					if(!$db->total){
						$sql="INSERT INTO service_mall_order SET
								oid='".$payment_info[oid]."',
								mall_ix='".$payment_info[mall_ix]."',
								company_id='".$company_detail[company_id]."',
								mall_name='".$company_detail[com_name]."',
								msg='".$payment_info[msg]."',
								date=NOW(),
								method='".$method."',
								status='".$payment_status."',
								total_price='".$service_info[sellprice]."',
								payment_price='".$service_info[sellprice]."',
								taxsheet_yn='N',
								receipt_y='N',
								user_ip='".$_SERVER["REMOTE_ADDR"]."',
								user_agent='".$_SERVER["HTTP_USER_AGENT"]."'  ";
						$result[sql6] = $sql;


						$db->query($sql);
					}

					$sql = "SELECT od_ix FROM service_mall_order_detail WHERE oid='".$payment_info[oid]."' and service_code='".$payment_info[service_code]."' ";
					$db->query($sql);
					$result[sql7] = $sql;

					if(!$db->total){
						$sql="INSERT INTO service_mall_order_detail SET
								oid='".$payment_info[oid]."',
								pid = '".$service_info[pid]."',
								pname='".$payment_info[pname]."',
								si_ix='".$si_ix."',
								parent_service_code='".$payment_info[parent_service_code]."',
								service_code='".$payment_info[service_code]."',
								period = '".$service_info[service_value]."',
								apply_type='".$apply_type."',

								coprice='".$service_info[option_coprice]."',
								psprice='".$service_info[option_price]."',
								ptprice='".$service_info[option_price]."',
								status='".$payment_status."',
								regdate=NOW() ";
						//return nl2br($sql);
								// status : 결제 할때 정보가 넘어 오던지 아니면 결제 방법에 따라서 변경되게 처리한다.
						$result[sql8] = $sql;
						$db->query($sql);
					}



					if($payment_status == "IC"){
							$si_ix = $payment_info[si_ix];
							$mall_term_day = $service_info[service_value];

							if($apply_type=="N") {//신규
								$sdate=date('Y-m-d');
								$edate = date('Y-m-d',strtotime('+'.($mall_term_day-1).' day'));
								$sql="UPDATE service_mall_ing SET si_status='SI', sm_sdate='".$sdate."', sm_edate='".$edate."' WHERE si_ix='".$si_ix."' ";
								$db->query($sql);
								if($service_order_status==ORDER_STATUS_INCOM_READY) {
									SetServiceHistory($db, $si_ix, $input_sh_method." 완료 : 시스템 (Web Service)", "서비스 신규 신청", $sellprice, $sdate, $edate, $period, $oid);

								}
							} else if($apply_type=="E") {//연장
								$sql="SELECT sm_edate,si_status FROM service_mall_ing WHERE si_ix='".$si_ix."' ";
								$db->query($sql);
								$db->fetch();
								$sm_edate=$db->dt["sm_edate"];
								$si_status=$db->dt["si_status"];
								$cdate=date("Y-m-d");
								if($sm_edate>=$cdate) {
									$sdate=date('Y-m-d',strtotime('+1 day',strtotime($sm_edate)));
									$edate=date('Y-m-d',strtotime('+'.($mall_term_day-1).' day',strtotime($sdate)));
								} else {
									if($si_status=="SI" || $si_status=="SD") {//서비스 운영 상태가 운영중(SI), 유예기간(SD)일 때
										$sdate=date('Y-m-d',strtotime('+1 day',strtotime($sm_edate)));
										$edate=date('Y-m-d',strtotime('+'.($mall_term_day-1).' day',strtotime($sdate)));
									} else if($si_status=="SS") {//서비스 운영 상태가 정지
										$sdate=date('Y-m-d');
										$edate = date('Y-m-d',strtotime('+'.($mall_term_day-1).' day'));
									}
								}
								$delay_edate=date('Y-m-d',strtotime('+'.SERVICE_STATUS_DELAY_TERM.' day',strtotime($sdate)));
								if($sdate>=$cdate) {
									$insert_sh_si_status="SI";
								} else {
									if($delay_edate>=$cdate) {
										$insert_sh_si_status="SD";
									} else {
										$insert_sh_si_status="SS";
									}
								}
								$sql="UPDATE service_mall_ing SET si_status='".$insert_sh_si_status."', sm_edate='".$edate."' WHERE si_ix='".$si_ix."' ";
								$db->query($sql);
								if($service_order_status==ORDER_STATUS_INCOM_READY) {
									SetServiceHistory($db, $si_ix, $input_sh_method." 완료 :시스템 (Web Service) ", "서비스 연장 $mall_term_day 일 결제", $sellprice, $sdate, $edate, $period, $oid);
								}
							} else if($apply_type=="C") {//변경
								$sdate=date('Y-m-d');
								$edate = date('Y-m-d',strtotime('+'.($mall_term_day-1).' day'));
								$sql="UPDATE service_mall_ing SET si_status='SI', service_div='".$service_div."', solution_div='".$solution_div."', sp_name='".$pname."', sm_edate='".$edate."' WHERE si_ix='".$si_ix."' ";
								$db->query($sql);
								if($service_order_status==ORDER_STATUS_INCOM_READY) {
									SetServiceHistory($db, $si_ix, $input_sh_method." 완료 : 시스템 (Web Service) ", "서비스 변경 $mall_term_day 일 결제", $sellprice, $sdate, $edate, $period, $oid);
									if($payment_info[service_div] == "CMS"){
										//syslog(LOG_INFO, 'CHANGE MALL TYPE BEGIN\n');
										ChangeMallType($mall_id,"B");//셋팅된 사이트의 몰타입 변경 함수 /include/mallstory.function.php 이철성실장님 작업 12/02/16
										//syslog(LOG_INFO, 'CHANGE MALL TYPE END\n');
									}
								}
							}
					}

					$result[bool] = true;
					return $result;

				}


				function getMyServiceInfo($mall_ix, $mall_domain_id, $mall_domain_key){
					//if(makelicensekey($mall_domain, $mall_domain_id) != $mall_domain_key){
						//return "비정상적인 접근이거나 라이센스 발급중입니다. 계속 문제가 반복될 시 몰스토리 고객센타로 문의해 주시기 바랍니다.";
						//exit;
					//}
					$db = new Database;
					$sql = "SELECT sp.id, sp.id as pid, sp.pname, sp.sellprice , sp.shotinfo , sp.parent_service_code, sp.service_code , smi.si_ix, smi.si_status, smi.sm_sdate, smi.sm_edate
								FROM service_product sp left join service_mall_ing smi on sp.service_code = smi.service_code  and mall_ix = '".$mall_ix."'
								where sp.disp = '1' and ".date("Y-m-d")." between sm_sdate and sm_edate ";

					//return $sql;
					$db = new Database;
					$db->query($sql);
					$service_infos = $db->fetchall("object");
					//$service_info = $service_infos[0];
					//$return_values["service_info"] =  $service_info;

					return $service_infos;
				}


				function getUsableServiceInfo($mall_ix){
					//if(makelicensekey($mall_domain, $mall_domain_id) != $mall_domain_key){
						//return "비정상적인 접근이거나 라이센스 발급중입니다. 계속 문제가 반복될 시 몰스토리 고객센타로 문의해 주시기 바랍니다.";
						//exit;
					//}
					$db = new Database;
					$sql = "SELECT mi.service_code, ccd.com_name, ccd.company_id as goodss_company_id
								FROM service_mall_ing mi left join common_company_detail ccd on mi.service_code = ccd.company_id
								where mall_ix = '".$mall_ix."' and '".date("Y-m-d")."' between sm_sdate and sm_edate  ";

					//return $sql;
					$db = new Database;
					$db->query($sql);
					$db->fetchall("object");
					for($i=0;$i < $db->total;$i++){
						$db->fetch($i);

						$userable_service[$i] = $db->dt[service_code];
						$userable_service_infos[$i][service_code] = $db->dt[service_code];
						$userable_service_infos[$i][com_name] = $db->dt[com_name];
						$userable_service_infos[$i][goodss_company_id] = $db->dt[goodss_company_id];


					}
					//return $goodss_companyinfos;
					$service_infos["useable_service"] = $userable_service;
					$service_infos["userable_service_infos"] = $userable_service_infos;
					//$service_infos["goodss_companyinfos"] = $goodss_companyinfos;
					//$service_info = $service_infos[0];
					//$return_values["service_info"] =  $service_info;

					return $service_infos;
				}


				function SellerShopAdd($sellerInfos){

					$user = $sellerInfos->user;

					$member_detail = $sellerInfos->member_detail;
					$company_detail = $sellerInfos->company_detail;
					/*
					$seller_detail = $sellerInfos->seller_detail;
					$seller_delivery = $sellerInfos->seller_delivery;
					*/
					//return $sellerInfos->user;
					//print_r($member_detail);

					$db = new Database;
					/*
					$db->query("SELECT server_value  FROM co_myserver_info WHERE server_property = 'reg_auth' ");
					$db->fetch();
					$reg_auth  = $db->dt[server_value];

					if($reg_auth == "1"){
						$seller_auth = "Y";
					}else if($reg_auth == "0"){
						$seller_auth = "N";
					}else{
						$seller_auth = "X";
					}
					*/
					$sql = "SELECT * FROM common_company_detail WHERE company_id='".$company_detail->company_id."' ";

					$db->query($sql);

					if ($db->total)
					{

						//$db->query("delete FROM ".TBL_CO_SELLERSHOPINFO." WHERE company_id = '$sellerInfos->company_id'");
						//return false;
						$serller_insert_result["result"] = true;
						$serller_insert_result["message"] = "이미 등록된 사이트 입니다. ";
						$serller_insert_result["sql"] = $sql;
						return $serller_insert_result;;
						exit;
					}
					//return true;
					$sql = "SELECT * FROM common_user WHERE code='".$user->code."' ";

					$db->query($sql);
					if($db->total){
							$serller_insert_result["result"] = true;
							$serller_insert_result["message"] = "이미 등록된 사용자 입니다. ";
							$serller_insert_result["sql"] = $sql;
							return $serller_insert_result;;
							exit;
					}
					$sql = "insert into common_user set
							code='".$user->code."',
							id='".$user->id."',
							pw='".$user->pw."',
							date='".$user->date."',
							visit='".$user->visit."',
							last='".$user->last."',
							ip='".$user->ip."',
							file='".$user->file."',
							mem_type='".$user->mem_type."',
							language='".$user->language."',
							company_id='".$user->company_id."',
							authorized='".$user->authorized."',
							auth='".$user->auth."',
							charger_ix='".$user->charger_ix."' ";
					//return $sql;
					$return = $db->query($sql);
					if(!$return){
						$serller_insert_result["result"] = false;
						$serller_insert_result["message"] = "회원정보 입력시 오류 ";
						$serller_insert_result["sql"] = $sql;
						return $serller_insert_result;
					}



					$sql = "insert into common_member_detail set
							code='".$member_detail->code."',
							birthday='".$member_detail->birthday."',
							birthday_div='".$member_detail->birthday_div."',
							name= HEX(AES_ENCRYPT('".$member_detail->name."','".$db->ase_encrypt_key."')),
							mail= HEX(AES_ENCRYPT('".$member_detail->mail."','".$db->ase_encrypt_key."')),
							zip= HEX(AES_ENCRYPT('".$member_detail->zip."','".$db->ase_encrypt_key."')),
							addr1= HEX(AES_ENCRYPT('".$member_detail->addr1."','".$db->ase_encrypt_key."')),
							addr2= HEX(AES_ENCRYPT('".$member_detail->addr2."','".$db->ase_encrypt_key."')),
							tel= HEX(AES_ENCRYPT('".$member_detail->tel."','".$db->ase_encrypt_key."')),
							tel_div='".$member_detail->tel_div."',
							pcs= HEX(AES_ENCRYPT('".$member_detail->pcs."','".$db->ase_encrypt_key."')),
							info='".$member_detail->info."',
							sms='".$member_detail->sms."',
							nick_name='".$member_detail->nick_name."',
							job='".$member_detail->job."',
							date='".$member_detail->date."',
							file='".$member_detail->file."',
							recent_order_date='".$member_detail->recent_order_date."',
							recom_id='".$member_detail->recom_id."',
							gp_ix='".$member_detail->gp_ix."',
							sex_div='".$member_detail->sex_div."',
							mem_level='".$member_detail->mem_level."',
							branch='".$member_detail->branch."',
							team='".$member_detail->team."',
							department='".$member_detail->department."',
							position='".$member_detail->position."',
							add_etc1='".$member_detail->add_etc1."',
							add_etc2='".$member_detail->add_etc2."',
							add_etc3='".$member_detail->add_etc3."',
							add_etc4='".$member_detail->add_etc4."',
							add_etc5='".$member_detail->add_etc5."',
							add_etc6='".$member_detail->add_etc6."' ";
					$return = $db->query($sql);
					if(!$return){
						$serller_insert_result["result"] = false;
						$serller_insert_result["message"] = "회원 상세정보 입력시 오류 ";
						$serller_insert_result["sql"] = $sql;
						return $serller_insert_result;
					}

					$sql = "insert into common_company_detail set
							company_id='".$company_detail->company_id."',
							com_name='".$company_detail->com_name."',
							com_div='".$company_detail->com_div."',
							com_type='CS',
							com_ceo='".$company_detail->com_ceo."',
							com_business_status='".$company_detail->com_business_status."',
							com_business_category='".$company_detail->com_business_category."',
							com_number='".$company_detail->com_number."',
							online_business_number='".$company_detail->online_business_number."',
							com_phone='".$company_detail->com_phone."',
							com_fax='".$company_detail->com_fax."',
							com_email='".$company_detail->com_email."',
							com_zip='".$company_detail->com_zip."',
							com_addr1='".$company_detail->com_addr1."',
							com_addr2='".$company_detail->com_addr2."',
							seller_auth='".$seller_auth."',
							regdate=NOW() ";
							//".$company_detail->com_type." --> CS 로 고정 판매처 형태로 등록한다.
							//$company_detail->seller_auth

					$return = $db->query($sql);
					if(!$return){
						$serller_insert_result[result] = false;
						$serller_insert_result[sql] = $sql;
						return $serller_insert_result;
					}

					$serller_insert_result[result] = true;
					return $serller_insert_result;

				}


				function GoodsOrderReg($orders_info){
					//return $orders_info;
					$ordersinfo = $orders_info->order_info;

					$orders_detail_info = $orders_info->orders_detail_info;


					$db = new Database;

					$sql = "SELECT * FROM shop_order WHERE oid='".$ordersinfo->oid."'  ";

					$db->query($sql);

					if (!$db->total){
						// 회원정보를 불러서 주문하는 사람 정보를 변경해줘야함
						//
						//

					$sql = "insert into shop_order set
								oid='".$ordersinfo->oid."',
								uid='".$ordersinfo->uid."',
								com_name='".$ordersinfo->com_name."',
								buserid='".$ordersinfo->buserid."',
								bname='".$ordersinfo->bname."',
								mem_group='".$ordersinfo->mem_group."',
								btel='".$ordersinfo->btel."',
								bmobile='".$ordersinfo->bmobile."',
								bmail='".$ordersinfo->bmail."',
								bzip='".$ordersinfo->bzip."',
								baddr='".$ordersinfo->baddr."',
								rname='".$ordersinfo->rname."',
								rtel='".$ordersinfo->rtel."',
								rmobile='".$ordersinfo->rmobile."',
								rmail='".$ordersinfo->rmail."',
								zip='".$ordersinfo->zip."',
								addr='".$ordersinfo->addr."',
								msg='".$ordersinfo->msg."',
								date='".$ordersinfo->date."',
								static_date='".$ordersinfo->static_date."',
								method='".$ordersinfo->method."',
								vb_info='".$ordersinfo->vb_info."',
								bank='".$ordersinfo->bank."',
								bank_input_date='".$ordersinfo->bank_input_date."',
								bank_input_name='".$ordersinfo->bank_input_name."',
								settle_module='".$ordersinfo->settle_module."',
								tid='".$ordersinfo->tid."',
								authcode='".$ordersinfo->authcode."',
								status='".$ordersinfo->status."',
								os_ix='".$ordersinfo->os_ix."',
								return_message='".$ordersinfo->return_message."',
								return_date='".$ordersinfo->return_date."',
								delivery_price='".$ordersinfo->delivery_price."',
								delivery_method='".$ordersinfo->delivery_method."',
								quick='".$ordersinfo->quick."',
								deliverycode='".$ordersinfo->deliverycode."',
								recomm_uid='".$ordersinfo->recomm_uid."',
								recomm_name='".$ordersinfo->recomm_name."',
								recomm_reserve='".$ordersinfo->recomm_reserve."',
								use_cupon_code='".$ordersinfo->use_cupon_code."',
								use_cupon_price='".$ordersinfo->use_cupon_price."',
								use_reserve_price='".$ordersinfo->use_reserve_price."',
								use_member_sale_price='".$ordersinfo->use_member_sale_price."',
								total_price='".$ordersinfo->total_price."',
								payment_price='".$ordersinfo->payment_price."',
								order_cancel_price='".$ordersinfo->order_cancel_price."',
								order_return_price='".$ordersinfo->order_return_price."',
								taxsheet_yn='".$ordersinfo->taxsheet_yn."',
								receipt_y='".$ordersinfo->receipt_y."',
								escrow_yn='".$ordersinfo->escrow_yn."',
								es_sendno='".$ordersinfo->es_sendno."',
								gift='".$ordersinfo->gift."',
								user_ip='".$ordersinfo->user_ip."',
								user_agent='".$ordersinfo->user_agent."',
								payment_agent_type='".$ordersinfo->payment_agent_type."'
								";

								$return = $db->query($sql);
								if(!$return){
									$goodss_order["result"] = false;
									$goodss_order["message"] = "주문정보 입력시 오류 ";
									$goodss_order["sql"] = $sql;
									//return $goodss_order;
								}

					}else{
						$goodss_order["result"] = true;
						$goodss_order["message"] = "이미 등록된 주문정보 입니다. ";
						$goodss_order["sql"] = $sql;
						//return $goodss_order;;
						//exit;
					}

					//return $sql;

					$sql = "SELECT * FROM shop_order_detail WHERE oid='".$ordersinfo->oid."' ";

					$db->query($sql);
					if($db->total){
							$goodss_order["result"] = true;
							$goodss_order["message"] = "이미 등록된 주문정보 입니다. ";
							$goodss_order["sql"] = $sql;
							return $goodss_order;;
							exit;
					}else{
						$sql = "SELECT ccd.com_name, ccd.company_id, sp.commission,sp.one_commission, csd.commission as seller_commission FROM shop_product sp , common_company_detail ccd left join ".TBL_COMMON_SELLER_DELIVERY." csd on csd.company_id = ccd.company_id WHERE sp.id='".$orders_detail_info->co_pid."' and sp.admin = ccd.company_id ";

						$db->query($sql);
						$db->fetch();
						$company_id = $db->dt[company_id];
						$company_name = $db->dt[com_name];
						$one_commission=$db->dt[one_commission];

						if($db->dt[one_commission] == "N"){
							$commission = $db->dt[seller_commission];
						}else{
							$commission=$db->dt[commission];
						}

					}


					$sql = "insert into shop_order_detail set
								od_ix='".$orders_detail_info->od_ix."',
								oid='".$orders_detail_info->oid."',
								pid='".$orders_detail_info->co_pid."',
								pcode='".$orders_detail_info->pcode."',
								product_type='".$orders_detail_info->product_type."',
								pname='".$orders_detail_info->pname."',
								paper_pname='".$orders_detail_info->paper_pname."',
								bc_ix='".$orders_detail_info->bc_ix."',
								option1='".$orders_detail_info->option1."',
								option_text='".$orders_detail_info->option_text."',
								option_etc='".$orders_detail_info->option_etc."',
								option_price='".$orders_detail_info->option_price."',
								pcnt='".$orders_detail_info->pcnt."',
								coprice='".$orders_detail_info->coprice."',
								psprice='".$orders_detail_info->psprice."',
								ptprice='".$orders_detail_info->ptprice."',
								reserve='".$orders_detail_info->reserve."',
								use_coupon='',
								use_coupon_code='',
								msgbyproduct='".$orders_detail_info->msgbyproduct."',
								status='IR',
								coupon_sdate='',
								coupon_edate='',
								dispathpoint='',
								quick='',
								invoice_no='',
								delivery_price='',
								company_id='".$company_id."',
								company_name='".$company_name."',
								one_commission='".$one_commission."',
								commission='".$commission."',
								co_pid='',
								regdate= NOW()

								 ";
					//return $sql;
					$return = $db->query($sql);
					if(!$return){
						$goodss_order["result"] = false;
						$goodss_order["message"] = "주문 상세정보 입력시 오류 ";
						$goodss_order["sql"] = $sql;
						return $goodss_order;
					}



					$goodss_order[result] = true;
					return $goodss_order;

				}


				// 클라이언트에서 쓸 간단한 함수
				function getCoGoodsByServer($useable_service, $search_rules, $paginginfo, $select_type = "code") {
					//print_r($search_rules);
					$search_rules = (array) $search_rules;
					//return $search_rules;
					foreach($search_rules as $key => $value){
						$search_rules[$key]= urldecode($value);
						if($key != ""){
							eval("\$$key = \"$value\";");
						}
					}

					$where = " where ccd.company_id = sp.admin and sp.id = r.pid and r.basic = 1 ";
					if($mode == "search"){
						switch ($goodss_depth){
							case 0:
								$cut_num = 3;
								break;
							case 1:
								$cut_num = 6;
								break;
							case 2:
								$cut_num = 9;
								break;
							case 3:
								$cut_num = 12;
								break;
							case 4:
								$cut_num = 15;
								break;
						}

						if($goodss_cid != ""){
							$where .= " and r.cid LIKE '".substr($goodss_cid,0,$cut_num)."%'";
						}else{
							$where .= "";
						}

						if($company_id != ""){
							//session_register("company_id");
							$where = $where."and sp.admin = '".$company_id."' ";

						}

						if($search_text != ""){
							$where .= "and sp.".$search_type." LIKE '%".$search_text."%' ";
						}

						if($sprice && $eprice){
							$where .= "and sellprice between $sprice and $eprice ";
						}

						if($status_where){
							$where .= " and ($status_where) ";
						}
						if($brand2 != ""){
							$where .= " and brand = ".$brand2."";
						}

						if($brand_name != ""){
							$where .= " and brand_name LIKE '%".$brand_name."%' ";
						}

						if($disp != ""){
							$where .= " and sp.disp = ".$disp;
						}

						if($co_type_str){
							$where .= $co_type_str ;
						}

						if($state2 != ""){
							$where .= " and state = ".$state2."";
						}

					}
					if(is_array($useable_service)){
						$str_use_company_id = "and sp.admin IN ('".implode("','",$useable_service)."') ";
					}

					if($paginginfo->max != ""){
						$limit_str = " limit ".$paginginfo->start.", ".$paginginfo->max." ";
					}else{
						$limit_str = " limit 500 ";
					}

					//echo $act."<br>";
					//print_r($search_rules);

					//echo "<br>".$search_rules[act];
					//exit;
					$db = new Database;


					$sql = "SELECT count(*) as total FROM ".TBL_SHOP_PRODUCT." sp, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." ccd $where $str_use_company_id   ";
					$db->query($sql);
					$db->fetch();
					$total = $db->dt[total];


					if($select_type == "id"){
					$sql = "SELECT sp.id as pid
							FROM ".TBL_SHOP_PRODUCT." sp, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." ccd
							$where $str_use_company_id
							order by sp.regdate desc $limit_str ";
							//ssa.co_company_id
					}else{
					$sql = "SELECT sp.id as pid, sp.pname, sp.pcode, sp.coprice, sp.sellprice, sp.listprice, sp.state, sp.disp ,sp.bimg,sp.etc2,sp.etc10,ccd.com_name  , r.cid
							FROM ".TBL_SHOP_PRODUCT." sp, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." ccd
							$where $str_use_company_id
							order by sp.regdate desc  $limit_str ";
							//ssa.co_company_id
					}


					$db->query($sql);
					if($db->total){
						$co_goods = $db->fetchall("object");
						//print_r($co_goods);
						for($i=0;$i < count($co_goods);$i++){
							foreach($co_goods[$i] as $key => $value){
								$_co_goods[$i][$key]= $value;
								if($key == "cid"){
									$_co_goods[$i][category_text]= $this->getCategoryPathByAdmin($_co_goods[$i][cid], 4);
								}

							}
						}
						$co_goodsinfo[page] = (string)$paginginfo->page;
						$co_goodsinfo[total] = $total;
						$co_goodsinfo[max] = (string)$paginginfo->max;
						$co_goodsinfo[goods] = $_co_goods;
						$co_goodsinfo[sql] = $sql;
						return $co_goodsinfo;
					}else{
						$co_goodsinfo[page] = (string)$paginginfo->page;
						$co_goodsinfo[total] = $total;
						$co_goodsinfo[max] = (string)$paginginfo->max;
						$co_goodsinfo[sql] = $sql;
						//$co_goodsinfo[goods] = "";
						return $co_goodsinfo;
					}
					//print_r($_co_goods);
					//$co_goodsinfo[total] = $total;


				}


				function getGoodssCategoryList($cid="",$depth=0)
				{
					$mdb = new Database;

					if($depth == 0 || $cid != ""){
						$sql = "SELECT * FROM ".TBL_SHOP_CATEGORY_INFO." where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' and category_use  = '1' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
						$mdb->query($sql);
						$Categorys = $mdb->fetchall("object");
						return $Categorys;
					}
				}

				function getCategoryPathByAdmin($cid, $depth='-1'){
					global $user;
					//$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
					$tb = TBL_SHOP_CATEGORY_INFO;
					if($cid == ""){
						return "전체";
					}
					$mdb = new Database;

					if($depth == '0'){
						$sql = "select * from ".$tb." where depth = 0 and cid LIKE '".substr($cid,0,3)."%' order by depth asc";
					}else if($depth == '1'){
						$sql = "select * from ".$tb." where depth <= 1 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%'))  order by depth asc";
					}else if($depth == '2'){
						$sql = "select * from ".$tb." where depth <= 2 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%'))  order by depth asc";
					}else if($depth == '3'){
						$sql = "select * from ".$tb." where depth <= 3 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%'))  order by depth asc";
					}else if($depth == '4'){
						$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
					}else{
						$sql = "select * from ".$tb." where depth <= 4 and ((depth = 0 and cid LIKE '".substr($cid,0,3)."%') or (depth = 1 and cid LIKE '".substr($cid,0,6)."%') or (depth = 2 and cid LIKE '".substr($cid,0,9)."%') or (depth = 3 and cid LIKE '".substr($cid,0,12)."%') or (depth = 4 and cid LIKE '".substr($cid,0,15)."%'))  order by depth asc";
						return "전체";
					}

					//echo $sql."<br>";
					$mdb->query($sql);

					for($i=0;$i < $mdb->total;$i++){
						$mdb->fetch($i);

						if($i == 0){
							$mstring .= $mdb->dt[cname];
						}else{
							$mstring .= " > ".$mdb->dt[cname];
						}
					}
					return $mstring;
				}

				function getB2BCompany($company_id, $return_type="list") {
					$db = new Database;

					if($return_type == "list"){

							$sql = "SELECT ccd.company_id, com_div, com_type, com_ceo,  com_phone, com_fax, com_email, shop_name, shop_url,  shop_desc, seller_auth, homepage, com_business_category
									FROM common_seller_detail csd, common_company_detail ccd
									WHERE  ccd.company_id = csd.company_id and seller_auth = 'Y'  and com_type = 'S'
									ORDER BY ccd.regdate DESC";


							//return $sql;
							$db->query($sql);
							$total = $db->total;
							$_sellers = $db->fetchall("object");

							$sellers_info["total"] = $total;
							$sellers_info["sellers"] = $_sellers;
							$sellers_info["query"] = $sql;
							$sellers_info["message"] = $myserver_company_id ."==".  $company_id;
							return $sellers_info;
					}else{
							$sql = "SELECT ccd.company_id, com_div, com_name,  com_type, com_ceo,  com_phone, com_fax, com_email, shop_name, shop_url, apply_status, seller_auth, goods_copy
									FROM common_seller_detail csd, common_company_detail ccd
									WHERE  ccd.company_id = csd.company_id and ccd.company_id = '".$company_id."' and com_type = 'S'
									ORDER BY ccd.regdate DESC";
							$db->query($sql);

							$_sellers = $db->fetchall("object");
							$sellers_info["sellerinfo"] = $_sellers[0];
							$sellers_info["query"] = $sql;

							return $sellers_info;
					}
				}


				function updateSellerInfo($goods_copy, $my_company_id, $co_company_id){
					$db = new Database;
					$true_cnt = 0;
					$false_cnt = 0;

						$sql = "SELECT * FROM ".TBL_CO_SELLERSHOP_APPLY." ssa WHERE  company_id = '".$my_company_id."' and co_company_id = '".$co_company_id."'  ";
						$db->query($sql);

						if ($db->total)
						{
							$sql = "update ".TBL_CO_SELLERSHOP_APPLY." ssa set  goods_copy = '".$goods_copy."' where company_id = '".$my_company_id."' and co_company_id = '".$co_company_id."' ";
							$db->query($sql);

							$return_info["bool"] = true;
							$return_info["message"] = "상점정보가 정상적으로 수정되었습니다.";
							$return_info["sql"] = $sql;
						}else{
							$return_info["bool"] = false;
							$return_info["message"] = "상점정보가 존재하지 않습니다.";
							$return_info["sql"] = $sql;
						}



					return $return_info;
				}





				function SellerShopDelete($company_id){
					$db = new Database;

					$sql = "SELECT code FROM co_common_user WHERE company_id = '".trim($company_id)."'";
					//return $sql;
					$db->query($sql);
					$db->fetch();
					$code = $db->dt[code];
					if (!$db->total)
					{
						//$db->query("delete FROM ".TBL_CO_SELLERSHOPINFO." WHERE company_id = '$sellerInfos->company_id'");
						$_return["bool"] = false;
						$_return["message"] = "사이트가 존재 하지 않습니다.";
						$_return["sql"] = $sql;
						return $_return;
						exit;
					}else{
						$db->query("delete FROM co_common_user WHERE company_id = '".trim($company_id)."'");
						$db->query("delete FROM co_common_member_detail WHERE code = '".trim($code)."'");
						$db->query("delete FROM co_common_company_detail WHERE company_id = '".trim($company_id)."'");
						$db->query("delete FROM co_common_seller_detail WHERE company_id = '".trim($company_id)."'");
						$db->query("delete FROM co_common_seller_delivery WHERE company_id = '".trim($company_id)."'");
						$db->query("delete FROM co_sellershop_apply WHERE company_id = '".trim($company_id)."'");



						$_return["bool"] = true;
						$_return["message"] = "판매사이트가 정상적으로 삭제 되었습니다.";
						//$_return["sql"] = "";
						return $_return;
					}


				}

				function SellerRegAuth($status, $co_company_id){
					$db = new Database;
					$true_cnt = 0;
					$false_cnt = 0;
					for($i=0;$i < count($co_company_id);$i++){


						$sql = "SELECT * FROM co_common_company_detail WHERE  company_id = '".$co_company_id[$i]."' and seller_auth != '".$status."' ";
						$db->query($sql);

						if ($db->total)
						{
							$sql = "update co_common_company_detail set  seller_auth = '".$status."' where company_id = '".$co_company_id[$i]."' ";
							$db->query($sql);
							$true_cnt++;
							//echo $sql;
						}else{
							$false_cnt++;
						}
					}

					$return_info["bool"] = true;
					$return_info["message"] = "회원 승인이 정상적으로 처리되었습니다.";
					$return_info["bool"] = $sql;
					$return_info[true_cnt] = $true_cnt;
					$return_info[false_cnt] = $false_cnt;
					$return_info[total_cnt] = $i;

					return $return_info;
				}


				function SellerShopApply($company_id, $co_company_id){
					$db = new Database;
					$true_cnt = 0;
					$false_cnt = 0;
					for($i=0;$i < count($co_company_id);$i++){

						$db->query("SELECT * FROM ".TBL_CO_SELLERSHOP_APPLY." WHERE company_id = '$company_id' and co_company_id = '".$co_company_id[$i]."'");

						if (!$db->total)
						{
							$sql = "INSERT INTO ".TBL_CO_SELLERSHOP_APPLY." ";
							$sql = $sql."(company_id,co_company_id, regdate)";
							$sql = $sql." VALUES('$company_id','".$co_company_id[$i]."',NOW())";
							$db->query($sql);
							$true_cnt++;
							//echo $sql;
						}else{
							$false_cnt++;
						}
					}

					$return_info[true_cnt] = $true_cnt;
					$return_info[false_cnt] = $false_cnt;
					$return_info[total_cnt] = $i;

					return $return_info;
				}

				function SellerShopApproval($company_id, $company_ids){
					$db = new Database;
					//return count($co_company_id);

					for($i=0;$i < count($company_ids);$i++){

							$sql = "select shop_url from co_common_seller_detail where company_id = '".$company_id."' ";
							$db->query($sql);
							$db->fetch();
							$shop_url = $db->dt[shop_url];
							//exit;

							$sql = "SELECT shop_url, cu.code, ssa.company_id, ssa.apply_status
										FROM co_common_seller_detail csd, co_common_user cu, ".TBL_CO_SELLERSHOP_APPLY." ssa
										WHERE  cu.company_id = csd.company_id
										and csd.company_id = ssa.company_id
										and ssa.company_id = '".$company_ids[$i]."'
										";

							//return $sql;
							//exit;
							$db->query($sql);

							if($db->total){
									$db->fetch();

									if($db->dt[apply_status] == "AU"){
										$_return["bool"] = false;
										$_return["message"] = "이미  승인된 입점업체  입니다.";
										$_return["sql"] = $sql;
										return $_return;
									}

									$co_company_id = $db->dt[company_id];
									$code = $db->dt[code];
									//return $shop_url;
									//exit;
									$soapclient = new SOAP_Client("http://".$shop_url."/admin/cogoods/api/");
									// server.php 의 namespace 와 일치해야함
									$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);


									$sql = "select * from co_common_user cu where cu.company_id = '".$co_company_id."' and code = '".$code."' ";
									//return $sql;
									//exit;
									$db->query($sql);
									$db->fetch(0,"object");
									$sellerInfo[user] = (array)$db->dt;

									$sql = "select * from co_common_member_detail cmd where code = '".$code."' ";

									$db->query($sql);
									$db->fetch(0,"object");
									$sellerInfo[member_detail] = (array)$db->dt;

									$sql = "select * from co_common_company_detail ccd where ccd.company_id = '".$co_company_id."' ";
									//return $sql;
									//exit;
									$db->query($sql);
									$db->fetch(0,"object");
									$sellerInfo[company_detail] = (array)$db->dt;

									$sql = "select csd.* , '".$_SERVER["HTTP_HOST"]."' as hostserver, '1' as is_share from co_common_seller_detail csd where csd.company_id = '".$co_company_id."' ";

									$db->query($sql);
									$db->fetch(0,"object");
									$sellerInfo[seller_detail] = (array)$db->dt;


									$sql = "select * from co_common_seller_delivery csd where csd.company_id = '".$co_company_id."' ";
									//return $sql;
									//exit;
									$db->query($sql);
									$db->fetch(0,"object");
									$sellerInfo[seller_delivery] = (array)$db->dt;



									//print_r($sellerInfos);
									$ret = $soapclient->call("SellerShopInsert",$params = array("sellerInfo"=> $sellerInfo),	$options);
									$ret = (array)$ret;
									//print_r($ret);
									//return $ret;
									//exit;
									if($ret["bool"]){
											$sql = "update ".TBL_CO_SELLERSHOP_APPLY." set apply_status ='AU' where company_id = '".$company_ids[$i]."' and co_company_id = '".$co_company_id."' ";
											//return "sql : ".$sql;
											$return = $db->query($sql);
											if($return){
												$_return["bool"] = true;
												$_return["message"] = "업데이트가 정상적으로 처리 되었습니다.";
												$_return["sql"] = $sql;
												return $_return;
											}else{
												$_return["bool"] = false;
												$_return["message"] = "입점업체 승인시 에러가 발생했습니다.";
												$_return["sql"] = $sql;
												return $_return;
											}

									}else{
											$sql = "update ".TBL_CO_SELLERSHOP_APPLY." set apply_status ='AU' where company_id = '".$company_ids[$i]."' and co_company_id = '".$company_id."' ";
											//return $sql;
											$return = $db->query($sql);
											if($return){
												$_return["bool"] = true;
												$_return["message"] = "업데이트가 정상적으로 처리 되었습니다.";
												$_return["sql"] = $sql;
												return $_return;
											}else{
												$_return["bool"] = false;
												$_return["message"] = "입점업체 승인시 에러가 발생했습니다.";
												$_return["sql"] = $sql;
												return $_return;
											}
									}
							}
					}


				}

				function SellerShopCancel($company_id, $co_company_id){
					$db = new Database;
					//return count($co_company_id);

					for($i=0;$i < count($co_company_id);$i++){
							$sql = "select csd.homepage, ssa.co_company_id from  ".TBL_COMMON_SELLER_DETAIL." csd , ".TBL_CO_SELLERSHOP_APPLY." ssa
											where  csd.company_id = ssa.co_company_id and ssa.co_company_id = '".$co_company_id[$i]."'
											and ssa.company_id = '$company_id' ";//apply_status ='AU' and
							//return $sql;

							$db->query($sql);

							if($db->total){
								$db->fetch();
								/*
								$soapclient = new SOAP_Client("http://".$db->dt[homepage]."/admin/cogoods/api/");
								// server.php 의 namespace 와 일치해야함
								$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

								$sql = "select company_id, company_name, business_number, online_business_number , business_kind , ceo, business_item, company_zip, company_address,
												business_day, shop_name, shop_desc, phone, fax, bank_owner, homepage, charger_email, charger
												from  ".TBL_CO_SELLERSHOPINFO." ssi
												where ssi.company_id = '".$db->dt[co_company_id]."' ";//apply_status ='AU' and
							//return $sql;
								$db->query($sql);
								$db->fetch();
								$sellerInfo = $db->dt;

								//print_r($sellerInfos);

								$ret = $soapclient->call("SellerShopDelete",$params = array("sellerInfo"=> $sellerInfo),	$options);
								*/
								//return $ret;
								//if($ret){
									$sql = "update ".TBL_CO_SELLERSHOP_APPLY." set apply_status ='CA' where co_company_id = '".$co_company_id[$i]."' and company_id = '$company_id' ";
									//return $sql;
									$db->query($sql);
									//$sellershop_reginfo = array("company_id" => $co_company_id[$i]);
								//}

							}
					}

					$_return["bool"] = true;
					$_return["message"] = "승인 취소가 정상적으로 처리 되었습니다.";
					$_return["sql"] = $sql;
					return $_return;
					//return true;
				}

				function SellerShopSellerCancel($company_id, $co_company_id){
					$db = new Database;
					//return count($co_company_id);

					for($i=0;$i < count($co_company_id);$i++){
							$sql = "select csd.homepage, ssa.co_company_id from  ".TBL_COMMON_SELLER_DETAIL." csd , ".TBL_CO_SELLERSHOP_APPLY." ssa
									  where  csd.company_id = ssa.co_company_id and ssa.company_id = '".$co_company_id[$i]."'
									   and ssa.co_company_id = '$company_id' ";//apply_status ='AU' and
							//return $sql;

							$db->query($sql);

							if($db->total){
								$db->fetch();

								//if($ret){
									$sql = "update ".TBL_CO_SELLERSHOP_APPLY." set apply_status ='CA' where company_id = '".$co_company_id[$i]."' and co_company_id = '$company_id' ";
									//return $sql;
									$db->query($sql);
									//$sellershop_reginfo = array("company_id" => $co_company_id[$i]);
								//}

							}
					}

					$_return["bool"] = true;
					$_return["message"] = "승인 취소가 정상적으로 처리 되었습니다.";
					$_return["sql"] = $sql;
					return $_return;
					//return true;
				}

				function getSellerShopApplyList($list_type, $company_id) {
					$db = new Database;
					if($list_type == "apply"){
						$sql = "SELECT ssa.company_id, ssa.co_company_id, com_div, com_type, com_ceo,  com_phone, com_fax, com_email, shop_name, shop_url, ssa.apply_status
									FROM co_common_seller_detail csd, co_common_company_detail ccd, ".TBL_CO_SELLERSHOP_APPLY." ssa
									WHERE  ccd.company_id = csd.company_id
									and ccd.company_id = ssa.company_id
									and ssa.co_company_id = '$company_id'
									ORDER BY ccd.regdate DESC";

						//$sql = "SELECT ssi.*, ssa.apply_status FROM ".TBL_CO_SELLERSHOPINFO." ssi , ".TBL_CO_SELLERSHOP_APPLY." ssa where ssi.company_id = ssa.company_id and ssa.co_company_id = '$company_id' order by regdate desc";
					}else{
						$sql = "SELECT ssa.company_id, ssa.co_company_id, com_div, com_type, com_ceo,  com_phone, com_fax, com_email, shop_name, shop_url, ssa.apply_status
							FROM co_common_seller_detail csd, co_common_company_detail ccd, ".TBL_CO_SELLERSHOP_APPLY." ssa
							WHERE  ccd.company_id = csd.company_id and ccd.company_id = ssa.co_company_id and ssa.company_id = '$company_id'
							ORDER BY ccd.regdate DESC";

						//$sql = "SELECT ssi.*, ssa.apply_status FROM ".TBL_CO_SELLERSHOPINFO." ssi , ".TBL_CO_SELLERSHOP_APPLY." ssa where ssi.company_id = ssa.co_company_id and ssa.company_id = '$company_id' order by regdate desc";
					}
					//return $sql;
					//exit;
					$db->query($sql);
					$total = $db->total;
					$_sellers = $db->fetchall("object");
					/*
					for($i=0;$i < count($_sellers);$i++){
						foreach($_sellers[$i] as $key => $value){
							$sellers[$i][$key]= $value;
						}
					}
					*/
					$sellers_info["total"] = $total;
					$sellers_info["sellers"] = $_sellers;
					$sellers_info["query"] = $sql;
					return $sellers_info;
					/*
					if(count($sellers)){
						$xml = new XmlWriter_();
						$xml->push('sellers');

						foreach ($sellers as $seller) {
							//$xml->push('shop', array('species' => $animal[0]));
							$xml->push('seller', array('company_id' => $seller[company_id], 'admin_level' => $seller[admin_level]));
							$xml->element('company_id', $seller[company_id]);
							$xml->element('company_type', $seller[company_type]);
							$xml->element('homepage', $seller[homepage]);
							$xml->element('ceo', $seller[ceo]);
							$xml->element('charger', $seller[charger]);
							$xml->element('phone', $seller[phone]);
							$xml->element('fax', $seller[fax]);
							$xml->element('charger_email', $seller[charger_email]);
							$xml->element('company_name', $seller[company_name]);
							$xml->element('charger', $seller[charger]);
							$xml->element('shop_name', $seller[shop_name]);
							$xml->element('shop_desc', $seller[shop_desc]);
							$xml->element('admin_level', $seller[admin_level]);
							$xml->element('apply_status', $seller[apply_status]);
							$xml->pop();
						}
						$xml->pop();
						return urlencode($xml->getXml());
					}
					*/
				}


				function SellerShopInsert($sellerInfos){

					//$sellerInfos = (array)$sellerInfos;
					$db = new Database;

					$user = $sellerInfos->user;
					$member_detail = $sellerInfos->member_detail;
					$company_detail = $sellerInfos->company_detail;
					$seller_detail = $sellerInfos->seller_detail;
					$seller_delivery = $sellerInfos->seller_delivery;
					//return $member_detail;
					//exit;
					//print_r($member_detail);
					//exit;

					//$db = new Database;
					$sql = "SELECT * FROM ".TBL_COMMON_COMPANY_DETAIL." WHERE company_id='".$company_detail->company_id."' ";

					$db->query($sql);
					//return $db->total;
					//exit;
					if ($db->total){
							//$db->query("delete FROM ".TBL_CO_SELLERSHOPINFO." WHERE company_id = '$sellerInfos->company_id'");
							$_return["bool"] = 0;
							$_return["message"] = "이미 등록된 사이트 입니다.";
							$_return["sql"] = $sql;
							return $_return;
							exit;
					}else{

							// 입력값들 체크해보기 admin_level=8 로 들어가야하구 default 로 정해져야 하는 값 확인필요
							// mem_type 도 기본으로 셀러로 지정되어야 한다.
							$sql = "insert into common_user set
									code='".$user->code."',
									id='".$user->id."',
									pw='".$user->pw."',
									date='".$user->date."',
									visit='".$user->visit."',
									last='".$user->last."',
									ip='".$user->ip."',
									file='".$user->file."',
									mem_type='S',
									language='".$user->language."',
									company_id='".$user->company_id."',
									authorized='".$user->authorized."',
									auth='".$user->auth."' ";
							//echo $sql;
							//return $sql;
							//exit;
							$return = $db->query($sql);
							if(!$return){
								$_return["bool"] = false;
								$_return["message"] = "[사용자 정보] 등록중 에러가 발생했습니다. ";
								$_return["sql"] = $sql;
								return $_return;
							}
							$sql = "insert into common_member_detail set
									code='".$member_detail->code."',
									birthday='".$member_detail->birthday."',
									birthday_div='".$member_detail->birthday_div."',
									name=HEX(AES_ENCRYPT('".$member_detail->name."','".$db->ase_encrypt_key."')) ,
									mail=HEX(AES_ENCRYPT('".$member_detail->mail."','".$db->ase_encrypt_key."')) ,
									zip=HEX(AES_ENCRYPT('".$member_detail->zip."','".$db->ase_encrypt_key."')) ,
									addr1=HEX(AES_ENCRYPT('".$member_detail->addr1."','".$db->ase_encrypt_key."')) ,
									addr2=HEX(AES_ENCRYPT('".$member_detail->addr2."','".$db->ase_encrypt_key."')) ,
									tel=HEX(AES_ENCRYPT('".$member_detail->tel."','".$db->ase_encrypt_key."')) ,
									tel_div='".$member_detail->tel_div."',
									pcs=HEX(AES_ENCRYPT('".$member_detail->pcs."','".$db->ase_encrypt_key."')) ,
									info='".$member_detail->info."',
									sms='".$member_detail->sms."',
									nick_name='".$member_detail->nick_name."',
									job='".$member_detail->job."',
									date='".$member_detail->date."',
									file='".$member_detail->file."',
									recent_order_date='".$member_detail->recent_order_date."',
									recom_id='".$member_detail->recom_id."',
									gp_ix='".$member_detail->gp_ix."',
									sex_div='".$member_detail->sex_div."',
									mem_level='".$member_detail->mem_level."',
									branch='".$member_detail->branch."',
									team='".$member_detail->team."',
									department='".$member_detail->department."',
									position='".$member_detail->position."',
									add_etc1='".$member_detail->add_etc1."',
									add_etc2='".$member_detail->add_etc2."',
									add_etc3='".$member_detail->add_etc3."',
									add_etc4='".$member_detail->add_etc4."',
									add_etc5='".$member_detail->add_etc5."',
									add_etc6='".$member_detail->add_etc6."' ";
							$return = $db->query($sql);
							if(!$return){
								$_return["bool"] = false;
								$_return["message"] = "[사용자 상세 정보] 등록중 에러가 발생했습니다. ";
								$_return["sql"] = $sql;
								return $_return;
							}

							// 입점업체 등록을 할려면 무조건 com_type 이 셀러로 변경되어야 한다.
							// seller_auth 를 자동승인을 해줄지? 자동승인이 맞는것으로 판단. 그래서 default Y
							$sql = "insert into common_company_detail set
									company_id='".$company_detail->company_id."',
									com_name='".$company_detail->com_name."',
									com_div='".$company_detail->com_div."',
									com_type='S',
									com_ceo='".$company_detail->com_ceo."',
									com_business_status='".$company_detail->com_business_status."',
									com_business_category='".$company_detail->com_business_category."',
									com_number='".$company_detail->com_number."',
									online_business_number='".$company_detail->online_business_number."',
									com_phone='".$company_detail->com_phone."',
									com_fax='".$company_detail->com_fax."',
									com_email='".$company_detail->com_email."',
									com_zip='".$company_detail->com_zip."',
									com_addr1='".$company_detail->com_addr1."',
									com_addr2='".$company_detail->com_addr2."',
									seller_auth='Y',
									regdate=NOW() ";

							$return = $db->query($sql);
							if(!$return){
								$_return["bool"] = false;
								$_return["message"] = "[업체 상세 정보] 등록중 에러가 발생했습니다. ";
								$_return["sql"] = $sql;
								return $_return;
							}

							//seller_detail 에 hostserver URL 과 공유서버를 통한 등록인지 확인하는 필드 필요 is_share : 0 은 일반 1 은 공유를 통한 등록
							//공유서버를 통한 등록은 화면에서 수정할수 없게 처리한다. 부분적으로 관리자가 수정할수 있는것만 수정가능하게 한다.
							$sql = "insert into common_seller_detail set
									company_id='".$seller_detail->company_id."',
									shop_name='".$seller_detail->shop_name."',
									shop_desc='".$seller_detail->shop_desc."',
									shop_url='".$seller_detail->shop_url."',
									homepage='".$seller_detail->homepage."',
									bank_owner='".$seller_detail->bank_owner."',
									bank_name='".$seller_detail->bank_name."',
									bank_number='".$seller_detail->bank_number."',
									order_excel_info1='".$seller_detail->order_excel_info1."',
									order_excel_info2='".$seller_detail->order_excel_info2."',
									order_excel_checked='".$seller_detail->order_excel_checked."',
									authorized='".$seller_detail->authorized."',
									md_code='".$seller_detail->md_code."',
									team='".$seller_detail->team."',
									hostserver='".$seller_detail->hostserver."',
									is_share='".$seller_detail->is_share."',
									edit_date=NOW(),
									regdate=NOW() ";

							$db->query($sql);

							if(!$return){
								$_return["bool"] = false;
								$_return["message"] = "[셀러 상세 정보] 등록중 에러가 발생했습니다. ";
								$_return["sql"] = $sql;
								return $_return;
							}


							$sql = "insert into common_seller_delivery set
									company_id='".$seller_delivery->company_id."',
									commission='".$seller_delivery->commission."',
									delivery_policy='".$seller_delivery->delivery_policy."',
									delivery_basic_policy='".$seller_delivery->delivery_basic_policy."',
									delivery_price='".$seller_delivery->delivery_price."',
									delivery_freeprice='".$seller_delivery->delivery_freeprice."',
									delivery_free_policy='".$seller_delivery->delivery_free_policy."',
									delivery_product_policy='".$seller_delivery->delivery_product_policy."',
									regdate=NOW()
									";

							$return = $db->query($sql);
							if(!$return){
								$_return["bool"] = false;
								$_return["message"] = "[셀러 배송정책 정보] 등록중 에러가 발생했습니다. ";
								$_return["sql"] = $sql;
								return $_return;
							}

							$_return["bool"] = true;
							$_return["message"] = "사이트가 정상적으로 등록되었습니다.";
							$_return["sql"] = $sql;

							return $_return;
					}
				}

				function setGoodsCopyHistory($company_id, $co_company_id, $pid, $copy_type="M"){
					$db = new Database;

					$sql = "SELECT pid FROM co_product_copy_history WHERE company_id = '".$company_id."' and co_company_id = '".$co_company_id."' and pid = '".$pid."' ";
					//echo $sql;
					$db->query($sql);
					if (!$db->total){
							$sql = "insert into co_product_copy_history
									   (cpch_ix,company_id,co_company_id,pid,copy_type,regdate)
									   values
									   ('','".$company_id."','".$co_company_id."','".$pid."','".$copy_type."', NOW() ) ";

							$db->query($sql);
					}
				}

				function SellerShopGoodsReg($company_id, $goodsinfos, $copy_type="M"){
					//return $goodsinfos->id.":::".$goodsinfos->pname;

					$db = new Database;
					//$goodsinfos = $goodsinfos->basic;
					//$display_option = $goodsinfos->display_option;
					//$options = $goodsinfos->options;
					//$options_detail = $goodsinfos->options_detail;

					$sql = "SELECT pid FROM ".TBL_CO_PRODUCT." WHERE admin = '".$company_id."' and co_pid = '".$goodsinfos->id."'";
					//echo $sql;
					$db->query($sql);



					if (!$db->total){
						//$goodsinfos->id 를 co_pid 로 입력하고 상품아이디(id)는 새로 발급받는다.

						$sql = "insert into co_product set
								pid='',
								product_type='".$goodsinfos->product_type."',
								pname='".$goodsinfos->pname."',
								pcode='".$goodsinfos->pcode."',
								brand='".$goodsinfos->brand."',
								brand_name='".$goodsinfos->brand_name."',
								company='".$goodsinfos->company."',
								paper_pname='".$goodsinfos->paper_pname."',
								buying_company='".$goodsinfos->buying_company."',
								shotinfo='".$goodsinfos->shotinfo."',
								buyingservice_coprice='".$goodsinfos->buyingservice_coprice."',
								listprice='".$goodsinfos->listprice."',
								sellprice='".$goodsinfos->sellprice."',
								coprice='".$goodsinfos->coprice."',
								reserve_yn='".$goodsinfos->reserve_yn."',
								reserve='".$goodsinfos->reserve."',
								reserve_rate='".$goodsinfos->reserve_rate."',
								sns_btn_yn='".$goodsinfos->sns_btn_yn."',
								sns_btn='".$goodsinfos->sns_btn."',
								bimg='".$goodsinfos->bimg."',
								mimg='".$goodsinfos->mimg."',
								simg='".$goodsinfos->simg."',
								basicinfo='".$goodsinfos->basicinfo."',
								icons='',
								state='".$goodsinfos->state."',
								disp='".$goodsinfos->disp."',
								movie='".$goodsinfos->movie."',
								vieworder='".$goodsinfos->vieworder."',
								admin='".$goodsinfos->admin."',
								stock='".$goodsinfos->stock."',
								safestock='".$goodsinfos->safestock."',
								view_cnt='".$goodsinfos->view_cnt."',
								order_cnt='".$goodsinfos->order_cnt."',
								recommend_cnt='".$goodsinfos->recommend_cnt."',
								search_keyword='".$goodsinfos->search_keyword."',
								reg_category='".$goodsinfos->reg_category."',
								option_stock_yn='".$goodsinfos->option_stock_yn."',
								inventory_info='".$goodsinfos->inventory_info."',
								surtax_yorn='".$goodsinfos->surtax_yorn."',
								delivery_company='".$goodsinfos->delivery_company."',
								one_commission='".$goodsinfos->one_commission."',
								commission='".$goodsinfos->commission."',
								stock_use_yn='".$goodsinfos->stock_use_yn."',
								delivery_policy='".$goodsinfos->delivery_policy."',
								delivery_product_policy='".$goodsinfos->delivery_product_policy."',
								delivery_package='".$goodsinfos->delivery_package."',
								delivery_price='".$goodsinfos->delivery_price."',
								free_delivery_yn='".$goodsinfos->free_delivery_yn."',
								free_delivery_count='".$goodsinfos->free_delivery_count."',
								etc1='".$goodsinfos->etc1."',
								etc2='".$goodsinfos->etc2."',
								etc3='".$goodsinfos->etc3."',
								etc4='".$goodsinfos->etc4."',
								etc5='".$goodsinfos->etc5."',
								etc6='".$goodsinfos->etc6."',
								etc7='".$goodsinfos->etc7."',
								etc8='".$goodsinfos->etc8."',
								etc9='".$goodsinfos->etc9."',
								etc10='".$goodsinfos->etc10."',
								hotcon_event_id='".$goodsinfos->hotcon_event_id."',
								hotcon_pcode='".$goodsinfos->hotcon_pcode."',
								co_goods='".$goodsinfos->co_goods."',
								co_pid='".$goodsinfos->id."',
								co_company_id='".$goodsinfos->admin."',
								bs_goods_url='".$goodsinfos->bs_goods_url."',
								bs_site='".$goodsinfos->bs_site."',
								editdate=NOW(),
								regdate=NOW()
								";
								//$goodsinfos->id  는 co_pid 로 등록된다.
						//echo $sql;
						//return ($sql);
						//exit;
						//return "1111";
						//exit;
						$db->query($sql);
						$db->query("SELECT pid FROM co_product WHERE pid=LAST_INSERT_ID()");
						$db->fetch();
						$pid = $db->dt[0];

						if($goodsinfos->basic_img){
								$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"]."data/basic/images/co_product", $pid, 'Y');
								@file_put_contents($_SERVER["DOCUMENT_ROOT"]."/data/basic/images/co_product".$uploaddir."/basic_".$pid.".gif",$goodsinfos->basic_img);
						}

						return $pid;
					}else{
						$db->fetch();
						$pid = $db->dt[0];

						if($goodsinfos->basic_img){
								$uploaddir = UploadDirText($_SERVER["DOCUMENT_ROOT"]."data/basic/images/co_product", $pid, 'Y');
								@file_put_contents($_SERVER["DOCUMENT_ROOT"]."/data/basic/images/co_product".$uploaddir."/basic_".$pid.".gif",$goodsinfos->basic_img);
						}
						return $pid;
					}



				}


				function SellerShopGoodDisplayOptionReg($company_id, $server_pid, $display_options){
					//return $goodsinfos->id.":::".$goodsinfos->pname;

					$db = new Database;

					$display_options = $display_options;

					//return count($display_options);
					// 공유된 상품 디스플레이 옵션 정보가 유니크 하기 위해서는

					for($i=0;$i < count($display_options);$i++){
						$display_option = $display_options[$i];
						$sql = "SELECT * FROM co_product_displayinfo WHERE dp_title='".$display_option->dp_title."' and pid = '".$server_pid."' and co_company_id = '".$company_id."' ";
						//echo $sql;
						$db->query($sql);

						if (!$db->total){
							//$goodsinfos->id 를 co_pid 로 입력하고 상품아이디(id)는 새로 발급받는다.

							$sql = "insert into co_product_displayinfo set
									dp_ix='',
									pid='".$server_pid."',
									dp_title='".$display_option->dp_title."',
									dp_desc='".$display_option->dp_desc."',
									insert_yn='".$display_option->insert_yn."',
									dp_use='".$display_option->dp_use."',
									co_company_id='".$company_id."',
									regdate=NOW()

									";
									//$basic->id  는 co_pid 로 등록된다.
							//echo $sql;
							//return ($sql);
							//exit;
							$db->query($sql);
						}
					}

					return true;

				}


				function SellerShopGoodOptionReg($company_id, $server_pid, $option_infos){
					//return $goodsinfos->id.":::".$goodsinfos->pname;

					$db = new Database;

					$options = $option_infos->options;
					$options_detail = $option_infos->options_detail;

					//return $options_detail;
					//exit;
					// 공유된 상품 디스플레이 옵션 정보가 유니크 하기 위해서는

					for($i=0;$i < count($options);$i++){
						$option = $options[$i];
						$sql = "SELECT * FROM co_product_options WHERE option_name = '".$option->option_name."' and pid = '".$server_pid."' and co_company_id = '".$company_id."'";
						//return $sql;
						$db->query($sql);

						if (!$db->total){
							//$goodsinfos->id 를 co_pid 로 입력하고 상품아이디(id)는 새로 발급받는다.

							$sql = "insert into co_product_options set
									opn_ix='',
									pid='".$server_pid."',
									option_name='".$option->option_name."',
									option_kind='".$option->option_kind."',
									option_type='".$option->option_type."',
									option_use='".$option->option_use."',
									insert_yn='".$option->insert_yn."',
									regdate=NOW(),
									old_uid='".$option->old_uid."',
									co_company_id='".$company_id."'

									";
									//$basic->id  는 co_pid 로 등록된다.
							//echo $sql;
							//return ($sql."<br>");

							$db->query($sql);
							$db->query("SELECT opn_ix FROM co_product_options WHERE opn_ix=LAST_INSERT_ID()");
							$db->fetch();
							$opn_ix = $db->dt[0];

							//exit;
							//return $options_detail;
							for($j=0; $j < count($options_detail);$j++){
								$option_detail = $options_detail[$j];

								$sql = "SELECT * FROM co_product_options_detail WHERE option_div='".$option_detail->option_div."' and pid = '".$server_pid."' and co_company_id = '".$company_id."'";
								//echo $sql;
								$db->query($sql);
								if (!$db->total){
									/*
								$sql = "insert into co_product_options_detail set
										id='',
										pid='".$server_pid."',
										opn_ix='".$opn_ix."',
										option_div='".$option_detail->option_div."',
										option_price='".$option_detail->option_price."',
										option_coprice='".$option_detail->option_coprice."',
										option_m_price='".$option_detail->option_m_price."',
										option_d_price='".$option_detail->option_d_price."',
										option_a_price='".$option_detail->option_a_price."',
										option_stock='".$option_detail->option_stock."',
										option_safestock='".$option_detail->option_safestock."',
										option_etc1='".$option_detail->option_etc1."',
										option_useprice='".$option_detail->option_useprice."',
										insert_yn='".$option_detail->insert_yn."',
										co_company_id='".$company_id."' ";
									*/
									$sql = "insert into co_product_options_detail set
										id='',
										pid='".$server_pid."',
										opn_ix='".$opn_ix."',
										option_div='".$option_detail->option_div."',
										option_price='".$option_detail->option_price."',
										option_coprice='".$option_detail->option_coprice."',
										option_stock='".$option_detail->option_stock."',
										option_safestock='".$option_detail->option_safestock."',
										option_etc1='".$option_detail->option_etc1."',
										insert_yn='".$option_detail->insert_yn."',
										co_company_id='".$company_id."' ";
								//return $sql;
								//exit;
								$db->query($sql);
								}

							}
							//exit;
							//return $pid;

						}
					}

					return true;

				}



				// 클라이언트에서 쓸 간단한 함수
				function getCoGoodsInfoByServer($goodss_pid) {
					//print_r($search_rules);

						$db = new Database;
						//concat('<P align=center><IMG src=\"http://www.isoda.co.kr/data/basic/images/download/',md5(concat('FORBIZ',sp.id))

						$sql = "SELECT sp.* , concat('<P align=center><IMG src=\"http://www.isoda.co.kr/admin/product/download_desc.php?id=',sp.co_pid,'\"</P>') as b2b_basic_info ,  ccd.com_name
									FROM ".TBL_SHOP_PRODUCT." sp, common_company_detail ccd
									where  ccd.company_id = sp.admin
									and sp.id = '".$goodss_pid."'
									$where ";


						//return $sql;
						//exit;
						$db = new Database;
						$db->query($sql);

						if($db->total){
							$co_goods = $db->fetchall("object");
							return $co_goods;
							//print_r($co_goods);
							/*
							for($i=0;$i < count($co_goods);$i++){
								foreach($co_goods[$i] as $key => $value){
									$_co_goods[$i][$key]= $value;
								}
							}
							$co_goodsinfo[page] = (string)$paginginfo->page;
							$co_goodsinfo[total] = $total;
							$co_goodsinfo[max] = (string)$paginginfo->max;
							$co_goodsinfo[goods] = $_co_goods;
							return $co_goodsinfo;
							*/
						}else{
							$co_goodsinfo[page] = (string)$paginginfo->page;
							$co_goodsinfo[total] = $total;
							$co_goodsinfo[max] = (string)$paginginfo->max;
							//$co_goodsinfo[goods] = "";
							return $co_goodsinfo;
						}
					//print_r($_co_goods);
					//$co_goodsinfo[total] = $total;


				}

				function getCoGoodsDisplayOptionInfoByServer($goodss_pid){
						$sql = "SELECT * FROM shop_product_displayinfo pdi
							where pdi.pid = '".$goodss_pid."'  ";


						//return $sql;
						$db = new Database;
						$db->query($sql);

						if($db->total){
							$display_options = $db->fetchall("object");
							return $display_options;
						}
				}


				function getCoGoodsOptionInfoByServer($goodss_pid){
						$sql = "select  opn_ix,pid,option_name,option_kind,option_type,option_use,insert_yn,regdate,old_uid from shop_product_options where pid = '".$goodss_pid."' ";
						//return $sql;
						$db = new Database;
						$db->query ($sql);
						if($db->total){
							$options = $db->fetchall("object");
							$option_infos[options] = $options;

							//$sql = "select id,pid,opn_ix,option_div,option_price,option_coprice,option_m_price,option_d_price,option_a_price,option_stock,option_safestock,option_etc1,option_useprice,insert_yn from shop_product_options_detail where pid = '".$goodss_pid."'  ";
							$sql = "select id,pid,opn_ix,option_div,option_price,option_coprice,option_stock,option_safestock,option_etc1,insert_yn from shop_product_options_detail where pid = '".$goodss_pid."'  ";
							//return $sql;
							$db->query ($sql);
							if($db->total){
								$options_detail = $db->fetchall("object");
								$option_infos[options_detail] = $options_detail;
								//print_r($options_detail);
								//exit;
								return $option_infos;
							}

							//exit;
						}
				}


				function autoGoodsReg($co_company_id,  $pid, $hostserver){

					/*
					0. 현재 함수는 호스트 서버에서만 수행 된다.
					1. 상품등록하는 사이트에서 autoGoodsReg 함수를 호출해주면 등록된 정보를 바탕으로 자동 상품등록을 수행한다.
					2. 자동 상품등록을 수행하기 위해서는 co_sellershop_apply 테이블에 godds_copy 가 A 인 정보를 가져와서 company_id  값을 가지고 있는 업체로 상품을 자동등록한다.
					3. co_common_seller_detail shop_url 을 가져와서 해당 서버의 웹서비스를 호출한다.
					4.

					SELECT ccsd.shop_url , ssa.company_id FROM co_common_seller_detail ccsd, co_sellershop_apply ssa WHERE ccsd.company_id = ssa.company_id and ssa.company_id = '3444fde7c7d641abc19d5a26f35a12cc' and goods_copy = 'A'
					*/
					$sql = "SELECT ccsd.shop_url , ssa.co_company_id, ssa.company_id
								FROM co_common_seller_detail ccsd, ".TBL_CO_SELLERSHOP_APPLY." ssa
								WHERE ccsd.company_id = ssa.co_company_id
								and ssa.company_id = '$co_company_id' and goods_copy = 'A' ";
					//return $sql;
					$db = new Database;
					$db->query($sql);
					$target_companys = $db->fetchall('object');

						//$target_companyinfo[$target_companys[$i][co_company_id]][shop_url] = $target_companys[$i][shop_url];
					//}



					for($i=0;$i < count($target_companys);$i++){
						$soapclient = new SOAP_Client("http://".$target_companys[$i][shop_url]."/admin/cogoods/api/");
						// server.php 의 namespace 와 일치해야함
						$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

					//for($i=0;$i < count($pid);$i++){
						// 호스트 서버에서 수행되기때문에 로컬에 있는 공유상품 테이블에서 정보를 가져온다.
						$sql = "select	 pid ,product_type,pname,pcode,brand,brand_name,company,paper_pname,buying_company,shotinfo,buyingservice_coprice,listprice,sellprice,coprice,
									reserve_yn,reserve,reserve_rate,sns_btn_yn,sns_btn,basicinfo,icons,state,disp,movie,vieworder,admin,stock,safestock,view_cnt,order_cnt,recommend_cnt,
									search_keyword,reg_category,option_stock_yn,inventory_info,surtax_yorn,delivery_company,one_commission,commission,stock_use_yn,
									delivery_policy,delivery_product_policy,delivery_package,delivery_price,free_delivery_yn,free_delivery_count,
									etc1,etc2,etc3,etc4,etc5,etc6,etc7,etc8,etc9,etc10,hotcon_event_id,hotcon_pcode,co_goods,co_pid, bs_goods_url,bs_site, bimg,  mimg, simg
									from co_product
									Where co_pid = '".$pid."' and co_company_id = '".$co_company_id."' ";
						//return $sql;
						//echo $sql."<br>";
						$db->query ($sql);
						$db->fetch(0,"object");
						if($db->total){
							$goodsinfos = $db->dt;
							$goodsinfos = (array)$goodsinfos;
							//$goodsinfos[basic_img] = file_get_contents('http://dev.forbiz.co.kr//data/basic/images/product/00/00/04/53/87/b_0000045387.gif');
							// 가져온 정보는 자동등록이 설정되어 있는 서버의 웹서비스를 호출해서 상품등록을 수행한다.
							$local_pid = $soapclient->call("SellerShopGoodsRegLocal",$params = array("company_id"=> $target_companys[$i][company_id], "goodsinfos"=> $goodsinfos),	$options);
							//$ret;
							//unset($goodsinfos);
							//return($server_pid);
							//exit;

							$sql = "select '' as dp_ix,cpd.pid,dp_title,dp_desc,insert_yn,dp_use
										from co_product cp, co_product_displayinfo cpd
										where cp.pid = cpd.pid and cp.co_pid = '".$pid."' ";
							$db->query ($sql);

							//echo $sql;
							//exit;
							if($db->total){
								//return $sql;
								$display_options = $db->fetchall("object");
								$display_options = $display_options;
								//return ($display_options);
								$ret = $soapclient->call("SellerShopGoodDisplayOptionRegLocal",$params = array("company_id"=> $target_companys[$i][company_id],"local_pid"=> $local_pid, "display_options"=> $display_options),	$options);
								//return $ret;
								//unset($goodsinfos);
								//print_r($ret);
								//exit;
							}

							$sql = "select '' as opn_ix,cpo.pid,option_name,option_kind,option_type,option_use,insert_yn,cpo.regdate,old_uid
										from co_product cp, co_product_options cpo
										where cp.pid = cpo.pid and cp.co_pid = '".$pid."' ";
							//echo $sql;

							$db->query ($sql);
							if($db->total){
								$options = $db->fetchall("object");
								$option_infos[options] = $options;

								//$sql = "select id,cpod.pid,opn_ix,option_div,option_price,option_coprice,option_m_price,option_d_price,option_a_price,option_stock,option_safestock,option_etc1,option_useprice,insert_yn from co_product cp, co_product_options_detail cpod where cp.pid = cpod.pid and cp.co_pid  = '".$pid."' ";
								$sql = "select id,cpod.pid,opn_ix,option_div,option_price,option_coprice,option_stock,option_safestock,option_etc1,insert_yn from co_product cp, co_product_options_detail cpod where cp.pid = cpod.pid and cp.co_pid  = '".$pid."' ";
								//echo $sql;

								$db->query ($sql);
								if($db->total){
									$options_detail = $db->fetchall("object");
									/*
									for($j=0;$j < count($options_detail);$j++){
										$__options_detail[$options_detail[$j][pid]][$j] = $options_detail[$j];

									}
									*/
									$option_infos[options_detail] = $options_detail;
									//print_r($options_detail);
									//exit;
									$ret = $soapclient->call("SellerShopGoodOptionRegLocal",$params = array("company_id"=> $target_companys[$i][company_id],"local_pid"=> $local_pid, "option_infos"=> $option_infos),	$options);
									//print_r($ret);
									//return $ret;
									//exit;
								}
							}
						}// 상품정보가 있는지 판단
					}// for loop
				}


				function SellerShopGoodsRegLocal($company_id, $goodsinfos, $copy_type="M"){
					//return $goodsinfos->id.":::".$goodsinfos->pname;

					$db = new Database;
					//$goodsinfos = $goodsinfos->basic;
					//$display_option = $goodsinfos->display_option;
					//$options = $goodsinfos->options;
					//$options_detail = $goodsinfos->options_detail;

					$sql = "SELECT id FROM ".TBL_SHOP_PRODUCT." WHERE admin = '".$company_id."' and co_pid = '".$goodsinfos->co_pid."'";
					//return $sql;
					$db->query($sql);

					if (!$db->total){
						//$goodsinfos->id 를 co_pid 로 입력하고 상품아이디(id)는 새로 발급받는다.

						$sql = "insert into ".TBL_SHOP_PRODUCT." set
								id='',
								product_type='".$goodsinfos->product_type."',
								pname='".$goodsinfos->pname."',
								pcode='".$goodsinfos->pcode."',
								brand='".$goodsinfos->brand."',
								brand_name='".$goodsinfos->brand_name."',
								company='".$goodsinfos->company."',
								paper_pname='".$goodsinfos->paper_pname."',
								buying_company='".$goodsinfos->buying_company."',
								shotinfo='".$goodsinfos->shotinfo."',
								buyingservice_coprice='".$goodsinfos->buyingservice_coprice."',
								listprice='".$goodsinfos->listprice."',
								sellprice='".$goodsinfos->sellprice."',
								coprice='".$goodsinfos->coprice."',
								reserve_yn='".$goodsinfos->reserve_yn."',
								reserve='".$goodsinfos->reserve."',
								reserve_rate='".$goodsinfos->reserve_rate."',
								sns_btn_yn='".$goodsinfos->sns_btn_yn."',
								sns_btn='".$goodsinfos->sns_btn."',
								bimg='".$goodsinfos->bimg."',
								mimg='".$goodsinfos->mimg."',
								simg='".$goodsinfos->simg."',
								basicinfo='".$goodsinfos->basicinfo."',
								icons='',
								state='".$goodsinfos->state."',
								disp='".$goodsinfos->disp."',
								movie='".$goodsinfos->movie."',
								vieworder='".$goodsinfos->vieworder."',
								admin='".$goodsinfos->admin."',
								stock='".$goodsinfos->stock."',
								safestock='".$goodsinfos->safestock."',
								view_cnt='".$goodsinfos->view_cnt."',
								order_cnt='".$goodsinfos->order_cnt."',
								recommend_cnt='".$goodsinfos->recommend_cnt."',
								search_keyword='".$goodsinfos->search_keyword."',
								reg_category='".$goodsinfos->reg_category."',
								option_stock_yn='".$goodsinfos->option_stock_yn."',
								inventory_info='".$goodsinfos->inventory_info."',
								surtax_yorn='".$goodsinfos->surtax_yorn."',
								delivery_company='".$goodsinfos->delivery_company."',
								one_commission='".$goodsinfos->one_commission."',
								commission='".$goodsinfos->commission."',
								stock_use_yn='".$goodsinfos->stock_use_yn."',
								delivery_policy='".$goodsinfos->delivery_policy."',
								delivery_product_policy='".$goodsinfos->delivery_product_policy."',
								delivery_package='".$goodsinfos->delivery_package."',
								delivery_price='".$goodsinfos->delivery_price."',
								free_delivery_yn='".$goodsinfos->free_delivery_yn."',
								free_delivery_count='".$goodsinfos->free_delivery_count."',
								etc1='".$goodsinfos->etc1."',
								etc2='".$goodsinfos->etc2."',
								etc3='".$goodsinfos->etc3."',
								etc4='".$goodsinfos->etc4."',
								etc5='".$goodsinfos->etc5."',
								etc6='".$goodsinfos->etc6."',
								etc7='".$goodsinfos->etc7."',
								etc8='".$goodsinfos->etc8."',
								etc9='".$goodsinfos->etc9."',
								etc10='".$goodsinfos->etc10."',
								hotcon_event_id='".$goodsinfos->hotcon_event_id."',
								hotcon_pcode='".$goodsinfos->hotcon_pcode."',
								co_goods='2',
								co_pid='".$goodsinfos->co_pid."',
								co_company_id='".$goodsinfos->admin."',
								bs_goods_url='".$goodsinfos->bs_goods_url."',
								bs_site='".$goodsinfos->bs_site."',
								editdate=NOW(),
								regdate=NOW()
								";
								//$goodsinfos->id  는 co_pid 로 등록된다.
						//echo $sql;
						//return ($sql);
						//exit;
						//return "1111";
						//exit;
						$db->query($sql);
						$db->query("SELECT id FROM ".TBL_SHOP_PRODUCT." WHERE id=LAST_INSERT_ID()");
						$db->fetch();
						$pid = $db->dt[0];

						return $pid;
					}else{
						$db->fetch();
						$pid = $db->dt[id];
						return $pid;
					}

				}


				function SellerShopGoodDisplayOptionRegLocal($company_id, $local_pid, $display_options){
					//return $goodsinfos->id.":::".$goodsinfos->pname;

					$db = new Database;

					$display_options = $display_options;

					//return count($display_options);
					// 공유된 상품 디스플레이 옵션 정보가 유니크 하기 위해서는

					for($i=0;$i < count($display_options);$i++){
						$display_option = $display_options[$i];
						$sql = "SELECT * FROM shop_product_displayinfo WHERE dp_title='".$display_option->dp_title."' and pid = '".$local_pid."'  ";
						//return $sql;
						$db->query($sql);

						if (!$db->total){
							//$goodsinfos->id 를 co_pid 로 입력하고 상품아이디(id)는 새로 발급받는다.

							$sql = "insert into shop_product_displayinfo set
									dp_ix='',
									pid='".$local_pid."',
									dp_title='".$display_option->dp_title."',
									dp_desc='".$display_option->dp_desc."',
									insert_yn='".$display_option->insert_yn."',
									dp_use='".$display_option->dp_use."',
									regdate=NOW()

									";
									//$basic->id  는 co_pid 로 등록된다.
							//echo $sql;
							//return ($sql);
							//exit;
							$db->query($sql);
						}
					}

					return true;

				}


				function SellerShopGoodOptionRegLocal($company_id, $local_pid, $option_infos){
					//return $goodsinfos->id.":::".$goodsinfos->pname;

					$db = new Database;

					$options = $option_infos->options;
					$options_detail = $option_infos->options_detail;

					//return $options_detail;
					//exit;
					// 공유된 상품 디스플레이 옵션 정보가 유니크 하기 위해서는

					for($i=0;$i < count($options);$i++){
						$option = $options[$i];
						$sql = "SELECT opn_ix FROM shop_product_options WHERE option_name = '".$option->option_name."' and pid = '".$local_pid."'  ";
						//return $sql;
						$db->query($sql);

						if (!$db->total){
							//$goodsinfos->id 를 local_pid 로 입력하고 상품아이디(id)는 새로 발급받는다.

							$sql = "insert into shop_product_options set
									opn_ix='',
									pid='".$local_pid."',
									option_name='".$option->option_name."',
									option_kind='".$option->option_kind."',
									option_type='".$option->option_type."',
									option_use='".$option->option_use."',
									insert_yn='".$option->insert_yn."',
									regdate=NOW()

									";
									//$basic->id  는 co_pid 로 등록된다.
							//echo $sql;
							//return ($sql."<br>");

							$db->query($sql);
							$db->query("SELECT opn_ix FROM shop_product_options WHERE opn_ix=LAST_INSERT_ID()");
							$db->fetch();
							$opn_ix = $db->dt[0];

							//exit;
							//return $opn_ix;
							for($j=0; $j < count($options_detail);$j++){
								$option_detail = $options_detail[$j];

								$sql = "SELECT * FROM shop_product_options_detail WHERE option_div='".$option_detail->option_div."' and pid = '".$local_pid."'  ";
								//echo $sql;
								$db->query($sql);
								if (!$db->total){
									/*
								$sql = "insert into shop_product_options_detail set
										id='',
										pid='".$local_pid."',
										opn_ix='".$opn_ix."',
										option_div='".$option_detail->option_div."',
										option_price='".$option_detail->option_price."',
										option_coprice='".$option_detail->option_coprice."',
										option_m_price='".$option_detail->option_m_price."',
										option_d_price='".$option_detail->option_d_price."',
										option_a_price='".$option_detail->option_a_price."',
										option_stock='".$option_detail->option_stock."',
										option_safestock='".$option_detail->option_safestock."',
										option_etc1='".$option_detail->option_etc1."',
										option_useprice='".$option_detail->option_useprice."',
										insert_yn='".$option_detail->insert_yn."'  ";
									*/
								$sql = "insert into shop_product_options_detail set
										id='',
										pid='".$local_pid."',
										opn_ix='".$opn_ix."',
										option_div='".$option_detail->option_div."',
										option_price='".$option_detail->option_price."',
										option_coprice='".$option_detail->option_coprice."',
										option_stock='".$option_detail->option_stock."',
										option_safestock='".$option_detail->option_safestock."',
										option_etc1='".$option_detail->option_etc1."',
										insert_yn='".$option_detail->insert_yn."'  ";
								//return $sql;
								//exit;
								$db->query($sql);
								}

							}
							//exit;
							//return $pid;

						}else{
								$db->fetch();
								$opn_ix = $db->dt[0];

								for($j=0; $j < count($options_detail);$j++){
									$option_detail = $options_detail[$j];

									$sql = "SELECT * FROM shop_product_options_detail WHERE option_div='".$option_detail->option_div."' and pid = '".$local_pid."'  ";
									//echo $sql;
									$db->query($sql);
									if (!$db->total){
										/*
									$sql = "insert into shop_product_options_detail set
											id='',
											pid='".$local_pid."',
											opn_ix='".$opn_ix."',
											option_div='".$option_detail->option_div."',
											option_price='".$option_detail->option_price."',
											option_coprice='".$option_detail->option_coprice."',
											option_m_price='".$option_detail->option_m_price."',
											option_d_price='".$option_detail->option_d_price."',
											option_a_price='".$option_detail->option_a_price."',
											option_stock='".$option_detail->option_stock."',
											option_safestock='".$option_detail->option_safestock."',
											option_etc1='".$option_detail->option_etc1."',
											option_useprice='".$option_detail->option_useprice."',
											insert_yn='".$option_detail->insert_yn."'  ";
										*/
									$sql = "insert into shop_product_options_detail set
											id='',
											pid='".$local_pid."',
											opn_ix='".$opn_ix."',
											option_div='".$option_detail->option_div."',
											option_price='".$option_detail->option_price."',
											option_coprice='".$option_detail->option_coprice."',
											option_stock='".$option_detail->option_stock."',
											option_safestock='".$option_detail->option_safestock."',
											option_etc1='".$option_detail->option_etc1."',
											insert_yn='".$option_detail->insert_yn."'  ";
									//return $sql;
									//exit;
									$db->query($sql);
									}

								}
						}
					}

					return true;

				}

				/*
				function echoHelloTwo($hello_name,$hello_age) {
						$out = "안녕하세요. " . urldecode($hello_name) ." 님, 당신은 $hello_age 살 입니다";
						//return new SOAP_Value('outputString','string',urlencode($out));
						return urlencode($out); //한글같은 문자전송을 위해 인코딩
				}

				function echoHelloTwoAA($hello_name,$hello_age) {
						$out = "안녕하세요. " . urldecode($hello_name) ." 님, 당신은 $hello_age 살 입니다";
						//return new SOAP_Value('outputString','string',urlencode($out));
						return urlencode($out); //한글같은 문자전송을 위해 인코딩
				}
				*/
		}



/*
CREATE TABLE IF NOT EXISTS `co_common_user` (
  `code` varchar(32) NOT NULL default '',
  `id` varchar(20) default NULL,
  `pw` varchar(64) default NULL,
  `date` datetime default NULL,
  `visit` smallint(6) default '0',
  `last` datetime default NULL,
  `ip` varchar(100) default NULL,
  `file` varchar(255) default NULL,
  `mem_type` enum('M','C','F','S','A','MD') NOT NULL default 'M' COMMENT 'M : 일반회원, C:기업회원 , F : 외국인회원, S:셀러회원, A : 관리자, MD : MD회원',
  `language` enum('korea','english','indonesia','chinese','japan') default 'korea',
  `company_id` varchar(32) default NULL,
  `authorized` enum('Y','N','X') NOT NULL default 'Y' COMMENT '회원승인여부',
  `auth` int(8) NOT NULL,
  `charger_ix` int(8) NOT NULL,
  PRIMARY KEY  (`code`),
  KEY `id` (`id`),
  KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `co_common_member_detail` (
  `code` varchar(32) NOT NULL default '',
  `birthday` varchar(10) default NULL,
  `birthday_div` char(1) default '1',
  `name` varchar(50) default NULL,
  `mail` varchar(100) default NULL,
  `zip` varchar(50) default NULL,
  `addr1` varchar(255) default NULL,
  `addr2` varchar(255) default NULL,
  `tel` varchar(100) default NULL,
  `tel_div` enum('C','H') NOT NULL default 'C',
  `pcs` varchar(100) default NULL,
  `info` char(1) default '1',
  `sms` char(1) NOT NULL default '1',
  `nick_name` varchar(100) default NULL,
  `job` varchar(50) default NULL,
  `date` datetime default NULL,
  `file` varchar(255) default NULL,
  `recent_order_date` datetime default NULL,
  `recom_id` varchar(20) default '',
  `gp_ix` int(8) default NULL,
  `sex_div` enum('M','W') NOT NULL default 'M',
  `mem_level` int(2) default NULL COMMENT '회원레벨 11:지사장 , 12:총괄MD , 13 : MD 팀장, 14 : MD',
  `branch` int(4) default NULL,
  `team` int(4) NOT NULL,
  `department` int(4) unsigned NOT NULL,
  `position` int(4) unsigned NOT NULL,
  `add_etc1` varchar(255) default NULL,
  `add_etc2` varchar(255) default NULL,
  `add_etc3` varchar(255) default NULL,
  `add_etc4` varchar(255) default NULL,
  `add_etc5` varchar(255) default NULL,
  `add_etc6` varchar(255) default NULL,
  PRIMARY KEY  (`code`),
  KEY `name` (`name`),
  KEY `date` (`date`),
  KEY `zip` (`zip`),
  KEY `IDX_MGI_GP_IX` (`gp_ix`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `co_common_company_detail` (
  `company_id` varchar(32) NOT NULL default '',
  `com_name` varchar(50) default NULL,
  `com_div` enum('P','R') NOT NULL default 'P' COMMENT 'P:개인 , R :  법인',
  `com_type` enum('G','S','A') NOT NULL default 'G' COMMENT 'G : 일반기업, S : 셀러 , A : 쇼핑몰 운영업체',
  `com_ceo` varchar(20) default NULL,
  `com_business_status` varchar(255) default NULL,
  `com_business_category` varchar(255) default NULL,
  `com_number` varchar(20) default NULL,
  `online_business_number` varchar(30) NOT NULL,
  `com_phone` varchar(15) default NULL,
  `com_fax` varchar(15) default NULL,
  `com_email` varchar(100) NOT NULL,
  `com_zip` varchar(7) default NULL,
  `com_addr1` varchar(255) default NULL,
  `com_addr2` varchar(255) default NULL,
  `seller_auth` enum('N','Y','X') NOT NULL default 'N' COMMENT 'N:승인대기, Y:승인, X:승인거부',
  `regdate` datetime NOT NULL,
  PRIMARY KEY  (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `co_common_seller_detail` (
  `company_id` varchar(32) NOT NULL,
  `shop_name` varchar(50) default NULL,
  `shop_desc` mediumtext,
  `shop_url` varchar(150) default NULL,
  `homepage` varchar(255) default NULL,
  `bank_owner` varchar(20) default NULL,
  `bank_name` varchar(20) default NULL,
  `bank_number` varchar(30) default NULL,
  `order_excel_info1` mediumtext,
  `order_excel_info2` mediumtext,
  `order_excel_checked` mediumtext,
  `authorized` enum('Y','N','X') default 'N',
  `md_code` varchar(32) NOT NULL COMMENT '담당 MD',
  `team` int(4) default NULL COMMENT '담당팀',
  `edit_date` datetime NOT NULL COMMENT '수정일자',
  `regdate` datetime NOT NULL COMMENT '등록일자',
  PRIMARY KEY  (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `co_common_seller_delivery` (
  `company_id` varchar(32) NOT NULL default '',
  `commission` int(6) default '0',
  `delivery_policy` char(1) default NULL,
  `delivery_basic_policy` char(1) default NULL,
  `delivery_price` int(8) default NULL,
  `delivery_freeprice` int(8) default NULL,
  `delivery_free_policy` char(1) default NULL,
  `delivery_product_policy` char(1) default NULL,
  `regdate` datetime default NULL,
  PRIMARY KEY  (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `co_product_displayinfo` (
  `dp_ix` int(4) unsigned zerofill NOT NULL auto_increment,
  `pid` int(6) unsigned zerofill NOT NULL default '000000',
  `dp_title` varchar(255) default NULL,
  `dp_desc` varchar(255) default NULL,
  `insert_yn` enum('Y','N') default 'Y',
  `dp_use` char(1) default '1',
  `co_company_id` varchar(32) default NULL,
  `regdate` datetime default NULL,
  PRIMARY KEY  (`dp_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='공유 상품 추가정보';


CREATE TABLE IF NOT EXISTS `co_product_options` (
  `opn_ix` int(6) unsigned NOT NULL auto_increment,
  `pid` varchar(6) NOT NULL default '',
  `option_name` varchar(100) NOT NULL default '',
  `option_kind` char(1) NOT NULL default '',
  `option_type` char(1) default '9',
  `option_use` char(1) NOT NULL default '1',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `old_uid` int(11) default NULL,
  `co_company_id` varchar(32) default NULL,
  PRIMARY KEY  (`opn_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='공유 상품 옵션정보' ;

CREATE TABLE IF NOT EXISTS `co_product_options_detail` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` varchar(6) default NULL,
  `opn_ix` int(6) default NULL,
  `option_div` varchar(255) default NULL,
  `option_price` int(4) NOT NULL default '0',
  `option_coprice` int(4) unsigned default '0',
  `option_m_price` int(4) unsigned NOT NULL default '0',
  `option_d_price` int(4) unsigned NOT NULL default '0',
  `option_a_price` int(4) unsigned NOT NULL default '0',
  `option_stock` int(4) NOT NULL default '0',
  `option_safestock` int(4) NOT NULL default '0',
  `option_etc1` varchar(100) NOT NULL default '',
  `option_useprice` char(1) default '1',
  `insert_yn` enum('Y','N') default 'Y',
  `co_company_id` varchar(32) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='공유상품 옵션상세정보' ;

*/
?>
