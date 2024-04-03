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
						$this->__dispatch_map['getSellerInfo'] = array('in' => array('company_id' => 'string','return_type' => 'string'),'out' => array('outputString' => 'string'));

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

						$this->__dispatch_map['getCoGoodsByServer'] = array('in' => array('co_type' => 'string', 'company_id' => 'string','search_rules' => 'array','paginginfo' => 'array'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['getCoGoodsInfoByServer'] = array('in' => array('company_id' => 'string','co_company_id' => 'string','pid' => 'array'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['getCoGoodsDisplayOptionInfoByServer'] = array('in' => array('company_id' => 'string','co_company_id' => 'string','pid' => 'array'),'out' => array('outputString' => 'string'));
						$this->__dispatch_map['getCoGoodsOptionInfoByServer'] = array('in' => array('company_id' => 'string','co_company_id' => 'string','pid' => 'array'),'out' => array('outputString' => 'string'));

						$this->hostserver_url = "";

				}

				function setHostServer($hostserver_url){
					$this->hostserver_url = $hostserver_url;
				}
				// 클라이언트에서 쓸 간단한 함수
				function getSellerInfo($company_id, $return_type="list") {
					$db = new Database;

					if($return_type == "list"){
							$db->query("SELECT server_value  FROM co_myserver_info WHERE server_property = 'company_id' ");
							$db->fetch();
							$myserver_company_id  = $db->dt[server_value];
							if($myserver_company_id == $company_id){
									$sql = "SELECT ccd.company_id, com_div, com_type, com_ceo,  com_phone, com_fax, com_email, shop_name, shop_url, apply_status, seller_auth, goods_copy
									FROM co_common_seller_detail csd, co_common_company_detail ccd
									left join ".TBL_CO_SELLERSHOP_APPLY." ssa on ccd.company_id = ssa.co_company_id
									WHERE  ccd.company_id = csd.company_id
									ORDER BY ccd.regdate DESC";
							}else{
							$sql = "SELECT ccd.company_id, com_div, com_type, com_ceo,  com_phone, com_fax, com_email, shop_name, shop_url, apply_status, seller_auth, goods_copy
									FROM co_common_seller_detail csd, co_common_company_detail ccd
									left join ".TBL_CO_SELLERSHOP_APPLY." ssa on ccd.company_id = ssa.company_id
									WHERE  ccd.company_id = csd.company_id and (ccd.company_id = '".$company_id."' or (ccd.company_id != '".$company_id."' and seller_auth = 'Y'))
									ORDER BY ccd.regdate DESC";
							}
							//return $sql;
							//exit;
							//$db->query($sql);
							//$sellers = $db->fetchall();

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
							$sellers_info["message"] = $myserver_company_id ."==".  $company_id;
							return $sellers_info;
					}else{
							$sql = "SELECT ccd.company_id, com_div, com_type, com_ceo,  com_phone, com_fax, com_email, shop_name, shop_url, apply_status, seller_auth, goods_copy
									FROM co_common_seller_detail csd, co_common_company_detail ccd
									left join ".TBL_CO_SELLERSHOP_APPLY." ssa on ccd.company_id = ssa.company_id
									WHERE  ccd.company_id = csd.company_id and ccd.company_id = '".$company_id."'
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

				function SellerShopAdd($sellerInfos){

					$user = $sellerInfos->user;
					$member_detail = $sellerInfos->member_detail;
					$company_detail = $sellerInfos->company_detail;
					$seller_detail = $sellerInfos->seller_detail;
					$seller_delivery = $sellerInfos->seller_delivery;
					//print_r($member_detail);

					$db = new Database;
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

					$db->query("SELECT * FROM co_common_company_detail WHERE company_id='".$company_detail->company_id."' ");

					if ($db->total)
					{
						//$db->query("delete FROM ".TBL_CO_SELLERSHOPINFO." WHERE company_id = '$sellerInfos->company_id'");
						return false;
						$_return["bool"] = false;
						$_return["message"] = "이미 등록된 사이트 입니다. ";
						$_return["sql"] = $sql;
						exit;
					}


					$sql = "insert into co_common_user set
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
					//echo $sql;
					$return = $db->query($sql);
					if(!$return){
						return false;
					}
					$sql = "insert into co_common_member_detail set
							code='".$member_detail->code."',
							jumin='".$member_detail->jumin."',
							birthday='".$member_detail->birthday."',
							birthday_div='".$member_detail->birthday_div."',
							name='".$member_detail->name."',
							mail='".$member_detail->mail."',
							zip='".$member_detail->zip."',
							addr1='".$member_detail->addr1."',
							addr2='".$member_detail->addr2."',
							tel='".$member_detail->tel."',
							tel_div='".$member_detail->tel_div."',
							pcs='".$member_detail->pcs."',
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
						return false;
					}

					$sql = "insert into co_common_company_detail set
							company_id='".$company_detail->company_id."',
							com_name='".$company_detail->com_name."',
							com_div='".$company_detail->com_div."',
							com_type='".$company_detail->com_type."',
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
							//$company_detail->seller_auth

					$return = $db->query($sql);
					if(!$return){
						return false;
					}

					$sql = "insert into co_common_seller_detail set
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
							edit_date=NOW(),
							regdate=NOW() ";

					$db->query($sql);

					$sql = "insert into co_common_seller_delivery set
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
						return false;
					}


					return true;

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
									jumin=HEX(AES_ENCRYPT('".$member_detail->jumin."','".$db->ase_encrypt_key."')) ,
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
				function getCoGoodsByServer($co_type, $my_company_id, $search_rules, $paginginfo) {
					//print_r($search_rules);
					$search_rules = (array) $search_rules;
					foreach($search_rules as $key => $value){
						$search_rules[$key]= urldecode($value);
						//eval("\$key = '$value'");
						eval("\$$key = \"$value\";");
					}

					if($mode == "search"){
						switch ($depth){
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
						$where = "";
						if($search_text != ""){
							$where .= "and cp.".$search_type." LIKE '%".$search_text."%' ";
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
							$where .= " and cp.disp = ".$disp;
						}

						if($co_type_str){
							$where .= $co_type_str ;
						}

						if($state2 != ""){
							$where .= " and state = ".$state2."";
						}

					}
					//echo $act."<br>";
					//print_r($search_rules);

					//echo "<br>".$search_rules[act];
					//exit;
					$db = new Database;
					if($my_company_id && false){//id, pname, pcode, state, disp, coprice, listprice, sellprice, '' as bimg
						$sql = "SELECT count(*) as total FROM ".TBL_CO_PRODUCT." cp, ".TBL_CO_SELLERSHOP_APPLY." ssa  where cp.admin = ssa.co_company_id and ssa.company_id ='$my_company_id' and ssa.apply_status = 'AU' $where ";
					}else{
						if($co_type == "co_goods_server_mylist"){
							$sql = "SELECT count(*) as total FROM ".TBL_CO_PRODUCT." cp  where cp.admin ='$my_company_id' $where ";
						}else if($co_type == "co_goods_server"){
							$sql = "SELECT count(*) as total FROM ".TBL_CO_PRODUCT." cp, ".TBL_CO_SELLERSHOP_APPLY." ssa  where cp.admin = ssa.co_company_id and ssa.company_id ='$my_company_id' and ssa.apply_status = 'AU' $where ";
						}
					}
					//return $sql;
					$db->query($sql);
					$db->fetch();
					$total = $db->dt[total];

					if($my_company_id && false){//id, pname, pcode, state, disp, coprice, listprice, sellprice, '' as bimg
						$sql = "SELECT cp.* , ccd.com_name FROM ".TBL_CO_PRODUCT." cp, ".TBL_CO_SELLERSHOP_APPLY." ssa , co_common_company_detail ccd
										where cp.admin = ssa.co_company_id
										and ccd.company_id = cp.admin
										and ssa.company_id ='".$my_company_id."'
										and ssa.apply_status = 'AU' $where
										order by cp.regdate desc limit ".$paginginfo->start.", ".$paginginfo->max."  "; //limit ".$paginginfo[start].", ".$paginginfo[max]."\
										//ssa.co_company_id
					}else{
						if($co_type == "co_goods_server_mylist"){
						$sql = "SELECT cp.* , ccd.com_name FROM ".TBL_CO_PRODUCT." cp ,co_common_company_detail ccd
								where ccd.company_id = cp.admin
								and cp.admin ='".$my_company_id."' $where
								order by cp.regdate desc limit ".$paginginfo->start.", ".$paginginfo->max."";
								//ssa.co_company_id
						}else{
						$sql = "SELECT cp.* , ccd.com_name FROM ".TBL_CO_PRODUCT." cp, ".TBL_CO_SELLERSHOP_APPLY." ssa ,co_common_company_detail ccd
								where cp.admin = ssa.company_id
								and ccd.company_id = cp.admin
								and ssa.co_company_id ='".$my_company_id."'
								and ssa.apply_status = 'AU' $where
								order by cp.regdate desc limit ".$paginginfo->start.", ".$paginginfo->max."";
								//ssa.co_company_id
						}
					}
					//return $sql;
					$db->query($sql);
					if($db->total){
						$co_goods = $db->fetchall("object");
						//print_r($co_goods);
						for($i=0;$i < count($co_goods);$i++){
							foreach($co_goods[$i] as $key => $value){
								$_co_goods[$i][$key]= $value;
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

				// 클라이언트에서 쓸 간단한 함수
				function getCoGoodsInfoByServer($company_id, $co_company_id, $pid) {
					//print_r($search_rules);

						$db = new Database;
						$sql = "SELECT cp.* , ccd.com_name
									FROM ".TBL_CO_PRODUCT." cp, ".TBL_CO_SELLERSHOP_APPLY." ssa ,co_common_company_detail ccd
									where cp.admin = ssa.company_id
									and ccd.company_id = cp.admin
									and ssa.co_company_id ='".$company_id."'
									and cp.pid = '".$pid."'
									and ssa.apply_status = 'AU' $where
										  "; //limit ".$paginginfo[start].", ".$paginginfo[max]."


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

				function getCoGoodsDisplayOptionInfoByServer($company_id, $co_company_id, $pid){
						$sql = "SELECT * FROM co_product_displayinfo pdi
							where pdi.co_company_id ='".$co_company_id."'
							and pdi.pid = '$pid'  ";


						//return $sql;
						$db = new Database;
						$db->query($sql);

						if($db->total){
							$display_options = $db->fetchall("object");
							return $display_options;
						}
				}


				function getCoGoodsOptionInfoByServer($company_id, $co_company_id, $pid){
						$sql = "select '' as opn_ix,pid,option_name,option_kind,option_type,option_use,insert_yn,regdate,old_uid from co_product_options where pid = '".$pid."' and co_company_id ='".$co_company_id."'";
						//echo $sql;
						$db = new Database;
						$db->query ($sql);
						if($db->total){
							$options = $db->fetchall("object");
							$option_infos[options] = $options;

							//$sql = "select id,pid,opn_ix,option_div,option_price,option_coprice,option_m_price,option_d_price,option_a_price,option_stock,option_safestock,option_etc1,option_useprice,insert_yn from co_product_options_detail where pid = '".$pid."' and co_company_id ='".$co_company_id."' ";
							$sql = "select id,pid,opn_ix,option_div,option_price,option_coprice,option_stock,option_safestock,option_etc1,insert_yn from co_product_options_detail where pid = '".$pid."' and co_company_id ='".$co_company_id."' ";
							//echo $sql;
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
  `jumin` varchar(100) default NULL,
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
  KEY `IDX_MM_JUMIN` (`jumin`)
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
