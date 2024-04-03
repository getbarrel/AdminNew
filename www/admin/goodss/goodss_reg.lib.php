<?

if(!function_exists("GoodssProductCopy")){ // 도매상품리스트에서 상품가져올때, 크론등록시 사용되는 함수
	function GoodssProductCopy($goodss_pid){
		global $admininfo, $admin_config, $db, $soapclient, $options , $company_id,$category_setting_info;

	//print_r($_POST);
	//echo $goodss_pid;
					$install_path = "../../include/";
					//include("SOAP/Client.php");
					/*
					$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
					// server.php 의 namespace 와 일치해야함
					$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
					*/
					//goodss_pid 는 상품공유 서버에서 유니크 하기때문에 co_company_id 가 없어두 된다.
					//상품정보를 가져온다.  가져온 상품정보를 바탕으로 아래 상품등록을 한다.
					$goodsinfos = $soapclient->call("getCoGoodsInfoByServer",$params = array("goodss_pid"=> $goodss_pid),	$options);
					//print_r($goodsinfos);
					//exit;
					$goodsinfos = (array)$goodsinfos ;
					$goodsinfo = (array)$goodsinfos[0];
					//print_r($goodsinfos);
					//print_r($goodsinfo);
					//exit;
					$sql = "SELECT id, pname FROM ".TBL_SHOP_PRODUCT." WHERE co_pid = '".$goodsinfo[id]."' and co_goods = '2' ";
					//echo $sql;
					//exit;
					//$db->debug = true;
					$db->query($sql);

					if (!$db->total){

						///////////////////////// 디폴트값 ///////////////////////////
						$delivery_company = "MI";	// 배송업체 : 기본값 셋팅
						$stock = "999999";		 // 재고 : 기본값 셋팅
						$safestock = "10";		// 안전재고 : 기본값 셋팅
						$stock_use_yn = "N";		// 재고사용여부 : 기본값 셋팅
						$surtax_yorn = "N";		// 면세여부 : 기본값 셋팅
						$product_type = "0";
						//////////////////////////////////////////////////////////////
						//syslog(LOG_INFO, $goodsinfo["id"]);

						$pname = $goodsinfo[pname];
						$pname = str_replace("\t"," ", $pname);
						$pname = str_replace("'","\'", $pname);
						if($company_id != ""){
							$admin = $company_id;
						}
						//$etc10 = $goodsinfo["id"];
						$pcode = $goodsinfo["pcode"];
						$regdate = $goodsinfo["regdate"];
						$editdate = $goodsinfo["editdate"];
						$co_company_id = $goodsinfo["admin"];
						$co_goods = 2; // 공유해온 상품
						$co_pid = $goodsinfo["id"];
						$etc2 = $goodsinfo["etc2"]; // 도매사이트 수정날짜
						$etc10 = $goodsinfo["etc10"]; // 도매사이트 등록날짜

						if($category_setting_info[gcs_ix] !=''){ //쉘에서 구동시
							$disp = $category_setting_info[gcs_disp];
							$state = $category_setting_info[gcs_state];
						}else{
							$disp = $_POST[sc_disp];
							$state = $_POST[sc_state];
						}

						/* 불필요 하여 제거 pcode 중복(ull 값)되어 업데이트로 넘어가는 현상 발생하여 주석 2012-09-20 홍진영
						$sql = "SELECT editdate from shop_product where pcode = '$pcode' ";
						$db->query($sql);
						$result = $db->fetchall();
						*/

						$act = "";

						if(count($result) == 0){
							$act = 'insert';
						} else {
							if ($result[0]["editdate"] != $editdate) {
								$act = 'update';
							}
						}

						$category[0] = $_POST[c_cid];
						$basic = $_POST[c_cid];

						if(!$category[0]){
							$category[0] = $category_setting_info[cid];
							$basic = $category_setting_info[cid];
						}

						$bimg_text = $goodsinfo["bimg"]; //sprintf("http://www.isoda.co.kr/data/basic/images/product/b_%06s.gif", $productInfo["id"]);
						$img_url_copy = 1;

						if($goodsinfo["download_desc"] == ""){
							$basicinfo = $goodsinfo["basicinfo"];
						}else{
							$basicinfo = $goodsinfo["b2b_basic_info"];
						}
						$basicinfo = str_replace("<img", "<IMG", $basicinfo);
						$basicinfo = str_replace("/data/goodss/images/product_detail/", "http://www.goodss.co.kr/data/goodss/images/product_detail/", $basicinfo);
						$basicinfo = str_replace("/data/goodss/images/", "http://www.goodss.co.kr/data/goodss/images/", $basicinfo);
						$goods_desc_copy = '1';
						$coprice = $goodsinfo["coprice"];

						if($category_setting_info[gcs_ix] !=''){ //쉘에서 구동시

							if($category_setting_info[margin_caculation_type] == 1){
								$listprice = $goodsinfo["listprice"] ;
								$sellprice = $goodsinfo["coprice"] + ($goodsinfo["coprice"] * $category_setting_info[margin]/100);
							}else if($category_setting_info[margin_caculation_type] == 2){
								$listprice = $goodsinfo["listprice"];
								$sellprice = $goodsinfo["coprice"] * $category_setting_info[margin];
							}else if($category_setting_info[margin_caculation_type] == 9 || $category_setting_info[margin_caculation_type] == "" ){
								$listprice = $goodsinfo["listprice"];
								$sellprice = $goodsinfo["sellprice"];
							}

							if($listprice < $sellprice){
								$listprice = $sellprice;
							}

							 if($category_setting_info[usable_round]=='Y'){
								if($category_setting_info[round_type] == "round"){
									$listprice = roundBetterUp($listprice,-1*$category_setting_info[round_precision]);
									$sellprice = roundBetterUp($sellprice,-1*$category_setting_info[round_precision]);
								}else if($category_setting_info[round_type] == "floor"){
									$listprice = roundBetterDown($listprice,-1*$category_setting_info[round_precision]);
									$sellprice = roundBetterDown($sellprice,-1*$category_setting_info[round_precision]);
								}
							}else{
								$listprice = round($listprice,-1);
								$sellprice = round($sellprice,-1);
							}

						}else{

							if($_POST[price_setting] == 1){
								$listprice = $goodsinfo["listprice"] ;
								$sellprice = $goodsinfo["coprice"] + ($goodsinfo["coprice"] * $_POST[margin_percent]/100);
							}else if($_POST[price_setting] == 2){
								$listprice = $goodsinfo["listprice"];
								$sellprice = $goodsinfo["coprice"] * $_POST[margin_cross];
							}else if($_POST[price_setting] == 9 || $_POST[price_setting] == "" ){
								$listprice = $goodsinfo["listprice"];
								$sellprice = $goodsinfo["sellprice"];
							}

							if($listprice < $sellprice){
								$listprice = $sellprice;
							}

							 if($_POST[round_type] && $_POST[round_precision]){
								if($_POST[round_type] == "round"){
									$listprice = roundBetterUp($listprice,-1*$_POST[round_precision]);
									$sellprice = roundBetterUp($sellprice,-1*$_POST[round_precision]);
								}else if($_POST[round_type] == "floor"){
									$listprice = roundBetterDown($listprice,-1*$_POST[round_precision]);
									$sellprice = roundBetterDown($sellprice,-1*$_POST[round_precision]);
								}

							}else{
								$listprice = round($listprice,-1);
								$sellprice = round($sellprice,-1);
							}

						}

						//print_r($options);
						//exit;

						//echo $sellprice;
						//exit;
						echo $pname." 상품정보 등록중...";
						//$db->debug = true;

						$bs_act = "insert"; // 스크립트 완료 구문 안찍히게 처리하기위해서 삽입 2012.04.27
						include $_SERVER["DOCUMENT_ROOT"]."/admin/product/goods_input.act.php";

						$pid = $INSERT_PRODUCT_ID;
						unset($regdate);
						unset($editdate);


							//$goodsinfos = $soapclient->call("setGoodsCopyHistory",$params = array("company_id"=> $admininfo[company_id], "co_company_id"=> $co_company_id, "pid"=> $goodsinfo[co_pid], "copy_type"=> "M"),	$options);

							//echo nl2br($goodsinfos);
							//exit;
							//$goodsinfos = (array)$goodsinfos ;
							//$goodsinfo = (array)$goodsinfos[0];
					}else{
						$db->fetch();
						$pid = $db->dt[0]; // local Server 에 상품키값
						//$pid = $db->dt[pname]; // local Server 에 상품키값
						$co_company_id = $goodsinfo["admin"];
						$state = $goodsinfo["state"];
						$disp = $goodsinfo["disp"];
						$co_pid = $goodsinfo["id"];

						$sql = "update ".TBL_SHOP_PRODUCT." set
									state = '".$state."',
									disp = '".$disp."',
									co_company_id = '".$co_company_id."'
									WHERE co_pid = '".$co_pid."' 
									and co_goods = '2' ";
						//echo $sql;
						//exit;
						//$db->debug = true;
						$db->query($sql);

						$category[0] = $_POST[c_cid];

						if($category[0]){
							$sql = "select cid from ".TBL_SHOP_PRODUCT_RELATION." WHERE cid = '".$category[0]."' and pid='".$pid."'";
							$db->query($sql);
							if(!$db->total){
								$sql = "update ".TBL_SHOP_PRODUCT_RELATION." set cid = '".$category[0]."'
								WHERE pid='".$pid."' and basic='1'";
								$db->query($sql);
							}
						}


						echo $db->dt[pname]." 상품정보 업데이트중...";
						//echo  $pid."<br>";
					}

					//echo $pid;
						// 디스플레이 옵션을 가져올때는 공유된상품의 공유서버 키값을 가지고 관리되기 때문에 $goodsinfo[id] 을 가지고 디스플레이 옵션을 가져와야 한다.
						$displayoptions_info = $soapclient->call("getCoGoodsDisplayOptionInfoByServer",$params = array("pid"=> $goodsinfo[id]),	$options);
					//echo $ret;
						//print_r($displayoptions_info);
						//exit;
						$displayoptions_info = (array)$displayoptions_info ;
						if(count($displayoptions_info) > 0){
								$sql = "update shop_product_displayinfo set insert_yn = 'N' WHERE  pid = '".$pid."'  ";
								$db->query($sql);

								for($j=0;$j < count($displayoptions_info);$j++){
									$display_option = $displayoptions_info[$j];

									$sql = "SELECT * FROM shop_product_displayinfo WHERE dp_title='".$display_option->dp_title."' and pid = '".$pid."'  ";
									//echo $sql;
									$db->query($sql);

									if (!$db->total){
											$sql = "insert into shop_product_displayinfo set
													dp_ix='".$display_option->dp_ix."',
													pid='".$pid."',
													dp_title='".$display_option->dp_title."',
													dp_desc='".$display_option->dp_desc."',
													insert_yn='Y',
													dp_use='".$display_option->dp_use."',
													regdate=NOW()";

											//echo $sql."<br>";
											$db->query($sql);
									}else{
											$sql = "update into shop_product_displayinfo set
													dp_title='".$display_option->dp_title."',
													dp_desc='".$display_option->dp_desc."',
													insert_yn='Y',
													dp_use='".$display_option->dp_use."',
													where dp_ix='".$db->dt[dp_ix]."'and  pid='".$pid."' ";

											//echo $sql."<br>";
											$db->query($sql);
									}
									unset($display_option);
								}
								unset($displayoptions_info);
						}


						//exit;
						//  옵션정보를 가져올때는 공유된상품의 공유서버 키값을 가지고 관리되기 때문에 $goodsinfo[id] 을 가지고 옵션정보를 가져와야 한다.
						$goodss_options_info = $soapclient->call("getCoGoodsOptionInfoByServer",$params = array("pid"=> $goodsinfo[id]),	$options);
					//echo $ret;
						//print_r($goodss_options_info);
						//exit;
						$goodss_options_info = (array)$goodss_options_info ;
						$goodss_options = $goodss_options_info[options];
						$goodss_options_detail = $goodss_options_info[options_detail];

						//print_r($goodss_options);
						//exit;
						if(count($goodss_options) > 0){
								$sql = "update shop_product_options set insert_yn = 'N' WHERE  pid = '".$pid."'  ";
								$db->query($sql);

								for($x=0;$x < count($goodss_options);$x++){
									$option = $goodss_options[$x];
									$sql = "SELECT opn_ix FROM shop_product_options WHERE option_name = '".$option->option_name."' and pid = '".$pid."' ";
									//echo  $sql;
									//exit;
									$db->query($sql);
									//echo $db->total;
									//exit;
									if (!$db->total){
										//$goodsinfos->id 를 co_pid 로 입력하고 상품아이디(id)는 새로 발급받는다.

										$sql = "insert into shop_product_options set
												opn_ix='',
												pid='".$pid."',
												option_name='".$option->option_name."',
												option_kind='".$option->option_kind."',
												option_type='".$option->option_type."',
												option_use='".$option->option_use."',
												insert_yn='Y',
												regdate=NOW(),
												old_uid='".$option->old_uid."'
												";
												//$basic->id  는 co_pid 로 등록된다.
										//echo nl2br($sql);
										//exit;

										$db->query($sql);
										$db->query("SELECT opn_ix FROM shop_product_options WHERE opn_ix=LAST_INSERT_ID()");
										$db->fetch();
										$opn_ix = $db->dt[0];
									}else{
										$db->fetch();
										$opn_ix = $db->dt[0];

										$sql = "update shop_product_options set
												option_name='".$option->option_name."',
												option_kind='".$option->option_kind."',
												option_type='".$option->option_type."',
												option_use='".$option->option_use."',
												insert_yn='Y'
												where opn_ix = '".$opn_ix."' and pid='".$pid."' ";
												//$basic->id  는 co_pid 로 등록된다.
										//echo nl2br($sql);
										//exit;

										$db->query($sql);

										//echo $opn_ix."<br>";
									}
									//exit;
										//exit;
										//print_r($goodss_options_detail);
										//exit;
										if(count($goodss_options_detail) > 0){
												$sql = "update shop_product_options_detail set insert_yn = 'N' WHERE  pid = '".$pid."' and opn_ix = '".$opn_ix."' ";
												$db->query($sql);

												for($j=0; $j < count($goodss_options_detail);$j++){
													$option_detail = $goodss_options_detail[$j];
													//echo $option->opn_ix ."==". $option_detail->opn_ix."<br>";
													if($option->opn_ix == $option_detail->opn_ix){
															$sql = "SELECT * FROM shop_product_options_detail WHERE option_div='".$option_detail->option_div."' and opn_ix = '".$opn_ix."' ";
															//echo $sql;
															//exit;
															$db->query($sql);
															if (!$db->total){
																/*
																$sql = "insert into shop_product_options_detail set
																		id='',
																		pid='".$pid."',
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
																		insert_yn='Y' ";
																*/
																$sql = "insert into shop_product_options_detail set
																		id='',
																		pid='".$pid."',
																		opn_ix='".$opn_ix."',
																		option_div='".$option_detail->option_div."',
																		option_price='".$option_detail->option_price."',
																		option_coprice='".$option_detail->option_coprice."',
																		option_stock='".$option_detail->option_stock."',
																		option_safestock='".$option_detail->option_safestock."',
																		option_etc1='".$option_detail->option_etc1."',
																		insert_yn='Y' ";
																//echo nl2br($sql)."<br>";
																//exit;
																$db->query($sql);
															}else{
																$db->fetch();
																/*
																$sql = "update shop_product_options_detail set
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
																		insert_yn='Y'
																		where id = '".$db->dt[id]."'";
																*/
																$sql = "update shop_product_options_detail set
																		option_div='".$option_detail->option_div."',
																		option_price='".$option_detail->option_price."',
																		option_coprice='".$option_detail->option_coprice."',
																		option_stock='".$option_detail->option_stock."',
																		option_safestock='".$option_detail->option_safestock."',
																		option_etc1='".$option_detail->option_etc1."',
																		insert_yn='Y'
																		where id = '".$db->dt[id]."'";
																//echo nl2br($sql)."<br>";
																//exit;
																$db->query($sql);
															}
													}
													unset($option_detail);
												}
												unset($option);
												$sql = "delete from shop_product_options_detail  where insert_yn = 'N' and  pid = '".$pid."' and opn_ix = '".$opn_ix."' ";
												$db->query($sql);
										}

										//exit;

								}

								$sql = "delete from shop_product_options where insert_yn = 'N' and  pid = '".$pid."' and opn_ix = '".$opn_ix."'  ";
								$db->query($sql);

						}

						unset($goodss_options_info) ;
						unset($goodss_options);
						unset($goodss_options_detail);
						unset($goodsinfos);
						unset($goodsinfo);


		}
}


if(!function_exists("GoodssProductStockCheck")){ // 가져온 도매상품 정보/재고 업데이트시

		function GoodssProductStockCheck($goodss_pid){
			global $admininfo, $admin_config, $db, $soapclient, $options, $sc_disp, $sc_state, $img_update;

			//print_r($_POST);

							$install_path = "../../include/";
							//include("SOAP/Client.php");
							/*
							$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
							// server.php 의 namespace 와 일치해야함
							$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
							*/
							//상품정보를 가져온다.  가져온 상품정보를 바탕으로 아래 상품등록을 한다.
							$goodsinfos = $soapclient->call("getCoGoodsInfoByServer",$params = array("goodss_pid"=> $goodss_pid),	$options);
							//print_r($goodsinfos);
							//exit;
							$goodsinfos = (array)$goodsinfos ;
							$goodsinfo = (array)$goodsinfos[0];
							//print_r($goodsinfos);
							//print_r($goodsinfo);
							//exit;
				///////////************************[Start] 상품 업데이트 부분 `pcode` 로 검사 추가 kbk 13/04/22 *************************////////////
				if($goodsinfo[id]=="") {//굿스에 없는 상품이라면
					$sql="SELECT pname, pcode FROM ".TBL_SHOP_PRODUCT." WHERE co_pid='".$goodss_pid."' and co_goods = '2' ";
					$db->query($sql);
					
					if($db->total) {
						$db->fetch();
						$goodss_pname=$db->dt["pname"];
						$goodss_pcode=$db->dt["pcode"];

						
						if($goodss_pcode!="") {//임대형에 등록된 상품이 pcode 값을 가지고 있다면
							$goodsinfos = $soapclient->call("getCoGoodsInfoByServerPcode",$params = array("goodss_pcode"=> $goodss_pcode),	$options);//pcode로 굿스에 상품검색
							//print_r($goodsinfos);
							//exit;
							$goodsinfos = (array)$goodsinfos ;
							$goodsinfo = (array)$goodsinfos[0];

							if($goodsinfo[id]!="") {//굿스에서 pcode로 검색하여 상품이 있다면

								$sql="UPDATE ".TBL_SHOP_PRODUCT." SET co_pid='".$goodsinfo[id]."' WHERE pcode='".$goodss_pcode."' and co_goods = '2' ";
								$db->query($sql);
								$goodss_bool=true;
							} else {//굿스에서 pcode로 검색하여 상품이 없다면 해당 상품의 판매상태를 일시품절로, 진열을 미노출로 바꾼다.
								$sql="UPDATE ".TBL_SHOP_PRODUCT." SET state='0', disp='0' WHERE co_pid='".$goodss_pid."' and co_goods = '2' ";
								$db->query($sql);
								$goodss_bool=false;
								$message_text="상품 : ".$goodss_pname." 은 굿스에 삭제되었거나 등록되지않은 상품입니다.";
							}
						} else {//co_pid 로 굿스에서 검색하여 없는 상품이고 임대형에서 pcode 값도 없다면 해당 상품의 판매상태를 일시품절로, 진열을 미노출로 바꾼다.
							$sql="UPDATE ".TBL_SHOP_PRODUCT." SET state='0', disp='0' WHERE co_pid='".$goodss_pid."' and co_goods = '2' ";
							$db->query($sql);
							$goodss_bool=false;
							$message_text="상품 : ".$goodss_pname." 은 굿스에 없는 상품이며 상품코드도 없습니다.";
						}
					} else {//co_pid로 임대형 상품을 검색하지 못한다면
						$goodss_bool=false;
						$message_text="해당 상품에 대한 정보가 부족합니다.";
					}
				} else {
					$goodss_bool=true;
				}

				///////////************************[End] 상품 업데이트 부분 `pcode` 로 검사 추가 kbk 13/04/22 *************************////////////
				
				if($goodss_bool) {

							$sql = "SELECT id, pname, basicinfo, state, disp, co_company_id FROM ".TBL_SHOP_PRODUCT." WHERE co_pid = '".$goodsinfo[id]."' and co_goods = '2' ";
							//echo $sql;
							//exit;
							//$db->debug = true;
							$db->query($sql);

							if (!$db->total){

								///////////////////////// 디폴트값 ///////////////////////////
								$delivery_company = "MI";	// 배송업체 : 기본값 셋팅
								$stock = "999999";		 // 재고 : 기본값 셋팅
								$safestock = "10";		// 안전재고 : 기본값 셋팅
								$stock_use_yn = "N";		// 재고사용여부 : 기본값 셋팅
								$surtax_yorn = "N";		// 면세여부 : 기본값 셋팅
								$product_type = "0";
								//////////////////////////////////////////////////////////////
								//syslog(LOG_INFO, $goodsinfo["id"]);

								$pname = $goodsinfo[pname];
								$pname = str_replace("\t"," ", $pname);
								$pname = str_replace("'","\'", $pname);

								//$etc10 = $goodsinfo["id"];
								$pcode = $goodsinfo["pcode"];
								$regdate = $goodsinfo["regdate"];
								$editdate = $goodsinfo["editdate"];
								$co_goods = 2; // 공유해온 상품
								$co_pid = $goodsinfo["id"];

								/*$_POST[sc_state] 은 재고 업데이트에서 처리방법 값이므로 상품 값을 넣어주어야함 2012-08-28 홍진영
								$disp = $_POST[sc_disp];
								$state = $_POST[sc_state];
								*/

								$disp = $goodsinfo[disp];
								$state = $goodsinfo[state];

								/* 불필요 하여 제거 pcode 중복(ull 값)되어 업데이트로 넘어가는 현상 발생하여 주석 2012-09-20 홍진영
								$sql = "SELECT editdate from shop_product where pcode = '$pcode' ";
								$db->query($sql);
								$result = $db->fetchall();
								*/

								$act = "";

								if(count($result) == 0){
									$act = 'insert';
								} else {
									if ($result[0]["editdate"] != $editdate) {
										$act = 'update';
									}
								}

								$category[0] = $_POST[c_cid];
								$basic = $_POST[c_cid];

								$bimg_text = $goodsinfo["bimg"];//sprintf("http://www.isoda.co.kr/data/basic/images/product/b_%06s.gif", $productInfo["id"]);
								$img_url_copy = 1;

								if($goodsinfo["download_desc"] == ""){
									$basicinfo = $goodsinfo["basicinfo"];
								}else{
									$basicinfo = $goodsinfo["b2b_basic_info"];
								}
								$basicinfo = str_replace("<img", "<IMG", $basicinfo);
								$basicinfo = str_replace("\"/data/goodss/images/product", "\"http://www.goodss.co.kr/data/goodss/images/product", $basicinfo);

								$coprice = $goodsinfo["coprice"];
								if($_POST[price_setting] == 1){
									$listprice = $goodsinfo["coprice"] + $_POST[margin_plus];
									$sellprice = $goodsinfo["coprice"] + $_POST[margin_plus];
								}else if($_POST[price_setting] == 2){
									$listprice = $goodsinfo["listprice"] * $_POST[margin_cross];
									$sellprice = $goodsinfo["sellprice"] * $_POST[margin_cross];
								}else if($_POST[price_setting] == 9 || $_POST[price_setting] == "" ){
									$listprice = $goodsinfo["listprice"];
									$sellprice = $goodsinfo["sellprice"];
								}

								 if($_POST[round_type] && $_POST[round_precision]){
									if($_POST[round_type] == "round"){
										$listprice = roundBetterUp($listprice,-1*$_POST[round_precision]);
										$sellprice = roundBetterUp($sellprice,-1*$_POST[round_precision]);
									}else if($_POST[round_type] == "floor"){
										$listprice = roundBetterDown($listprice,-1*$_POST[round_precision]);
										$sellprice = roundBetterDown($sellprice,-1*$_POST[round_precision]);
									}

								}else{
									$listprice = round($listprice,-1);
									$sellprice = round($sellprice,-1);
								}

								//echo $sellprice;
								//exit;
								echo $pname." 상품정보 등록중...";
								//$db->debug = true;
								$bs_act = "insert"; // 스크립트 완료 구문 안찍히게 처리하기위해서 삽입 2012.04.27
								include "../product/goods_input.act.php";

								$pid = $INSERT_PRODUCT_ID;
								unset($regdate);
								unset($editdate);


									//$goodsinfos = $soapclient->call("setGoodsCopyHistory",$params = array("company_id"=> $admininfo[company_id], "co_company_id"=> $co_company_id, "pid"=> $goodsinfo[co_pid], "copy_type"=> "M"),	$options);

									//echo nl2br($goodsinfos);
									//exit;
									//$goodsinfos = (array)$goodsinfos ;
									//$goodsinfo = (array)$goodsinfos[0];
							}else{
								$db->fetch();
								$pid = $db->dt[id]; // local Server 에 상품키값
								$regdate = $goodsinfo["regdate"];

								if($db->dt[co_company_id] == ""){
									$co_company_id_str = ", co_company_id = '".$goodsinfo["admin"]."'";
								}
								//$pid = $db->dt[pname]; // local Server 에 상품키값


								if($goodsinfo["state"] != 1){
									if($sc_state==0){//재고 업데이트에 설정에 관한 처리 추가 2012-08-28 홍진영
										echo "<b>".$db->dt[pname]."</b> 상품이 <b style='color:red;'>품절</b> 되었습니다.";

										if($sc_state == ""){
											$sc_state = "0";
										}

										if($sc_disp == ""){
											$sc_disp = "0";
										}

										$sql = "update ".TBL_SHOP_PRODUCT." set
												state = '".$sc_state."',
												disp = '".$sc_disp."' $co_company_id_str
												WHERE co_pid = '".$goodsinfo[id]."'
												and co_goods = '2' ";
										//echo $sql."<br>";
										//exit;
										//$db->debug = true;
										$db->query($sql);
									}elseif($sc_state==9){//삭제 선택했을시
										echo "<b>".$db->dt[pname]."</b> 상품을 <b style='color:red;'>삭제</b> 합니다.";

										$act = "delete";
										$id = $pid;
										include "../product/goods_input.act.php";

									}
								}else{

									echo "<b>".$db->dt[pname]."</b> 상품의 재고가 정상적으로 확인 되었습니다.";
									if($img_update == "Y"){
										if($goodsinfo["download_desc"] == ""){
											$basicinfo = $goodsinfo["basicinfo"];
										}else{
											$basicinfo = $goodsinfo["b2b_basic_info"];
										}
										$basicinfo = str_replace("<img", "<IMG", $basicinfo);
										$basicinfo = str_replace("\"/data/goodss/images/product/", "\"http://www.goodss.co.kr/data/goodss/images/product/", $basicinfo);


										//$basicinfo = "<P align=center><IMG src=\"http://koreabuys.forbiz.co.kr/data/koreabuys/images/upfile/shoes_size_png.png\"></P>\n".$basicinfo;
										$sql = "update ".TBL_SHOP_PRODUCT." set
											basicinfo = '".$basicinfo."' ,
											regdate = '".$regdate."' ,
											editdate = NOW()
											$co_company_id_str
											WHERE co_pid = '".$goodsinfo[id]."'
											and co_goods = '2' ";
										//echo $sql;
										$db->query($sql);
										$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET regdate_desc = unix_timestamp(regdate)*-1 WHERE id='".$pid."'");
									}else{
										$sql = "update ".TBL_SHOP_PRODUCT." set
											regdate = '".$regdate."' ,
											editdate = NOW()
											$co_company_id_str
											WHERE co_pid = '".$goodsinfo[id]."' 
											and co_goods = '2' ";
										//echo $sql;
										$db->query($sql);
										$db->query("UPDATE ".TBL_SHOP_PRODUCT." SET regdate_desc = unix_timestamp(regdate)*-1 WHERE id='".$pid."'");
									}
								}

								return true;
								//echo  $pid."<br>";
							}

							//echo $pid;
								// 디스플레이 옵션을 가져올때는 공유된상품의 공유서버 키값을 가지고 관리되기 때문에 $goodsinfo[id] 을 가지고 디스플레이 옵션을 가져와야 한다.
								$displayoptions_info = $soapclient->call("getCoGoodsDisplayOptionInfoByServer",$params = array("pid"=> $goodsinfo[id]),	$options);
							//echo $ret;
								//print_r($displayoptions_info);
								//exit;
								$displayoptions_info = (array)$displayoptions_info ;
								if(count($displayoptions_info) > 0){
										$sql = "update shop_product_displayinfo set insert_yn = 'N' WHERE  pid = '".$pid."'  ";
										$db->query($sql);

										for($j=0;$j < count($displayoptions_info);$j++){
											$display_option = $displayoptions_info[$j];

											$sql = "SELECT * FROM shop_product_displayinfo WHERE dp_title='".$display_option->dp_title."' and pid = '".$pid."'  ";
											//echo $sql;
											$db->query($sql);

											if (!$db->total){
													$sql = "insert into shop_product_displayinfo set
															dp_ix='".$display_option->dp_ix."',
															pid='".$pid."',
															dp_title='".$display_option->dp_title."',
															dp_desc='".$display_option->dp_desc."',
															insert_yn='Y',
															dp_use='".$display_option->dp_use."',
															regdate=NOW()";

													//echo $sql."<br>";
													$db->query($sql);
											}else{
													$sql = "update into shop_product_displayinfo set
															dp_title='".$display_option->dp_title."',
															dp_desc='".$display_option->dp_desc."',
															insert_yn='Y',
															dp_use='".$display_option->dp_use."',
															where dp_ix='".$db->dt[dp_ix]."'and  pid='".$pid."' ";

													//echo $sql."<br>";
													$db->query($sql);
											}
											unset($display_option);
										}
										unset($displayoptions_info);
								}


								//exit;
								//  옵션정보를 가져올때는 공유된상품의 공유서버 키값을 가지고 관리되기 때문에 $goodsinfo[id] 을 가지고 옵션정보를 가져와야 한다.
								$goodss_options_info = $soapclient->call("getCoGoodsOptionInfoByServer",$params = array("pid"=> $goodsinfo[id]),	$options);
							//echo $ret;
								//print_r($goodss_options_info);
								//exit;
								$goodss_options_info = (array)$goodss_options_info ;
								$goodss_options = $goodss_options_info[options];
								$goodss_options_detail = $goodss_options_info[options_detail];

								//print_r($goodss_options);
								//exit;
								if(count($goodss_options) > 0){
										$sql = "update shop_product_options set insert_yn = 'N' WHERE  pid = '".$pid."'  ";
										$db->query($sql);

										for($x=0;$x < count($goodss_options);$x++){
											$option = $goodss_options[$x];
											$sql = "SELECT opn_ix FROM shop_product_options WHERE option_name = '".$option->option_name."' and pid = '".$pid."' ";
											//echo  $sql;
											//exit;
											$db->query($sql);
											//echo $db->total;
											//exit;
											if (!$db->total){
												//$goodsinfos->id 를 co_pid 로 입력하고 상품아이디(id)는 새로 발급받는다.

												$sql = "insert into shop_product_options set
														opn_ix='',
														pid='".$pid."',
														option_name='".$option->option_name."',
														option_kind='".$option->option_kind."',
														option_type='".$option->option_type."',
														option_use='".$option->option_use."',
														insert_yn='Y',
														regdate=NOW(),
														old_uid='".$option->old_uid."'
														";
														//$basic->id  는 co_pid 로 등록된다.
												//echo nl2br($sql);
												//exit;

												$db->query($sql);
												$db->query("SELECT opn_ix FROM shop_product_options WHERE opn_ix=LAST_INSERT_ID()");
												$db->fetch();
												$opn_ix = $db->dt[0];
											}else{
												$db->fetch();
												$opn_ix = $db->dt[0];

												$sql = "update shop_product_options set
														option_name='".$option->option_name."',
														option_kind='".$option->option_kind."',
														option_type='".$option->option_type."',
														option_use='".$option->option_use."',
														insert_yn='Y'
														where opn_ix = '".$opn_ix."' and pid='".$pid."' ";
														//$basic->id  는 co_pid 로 등록된다.
												//echo nl2br($sql);
												//exit;

												$db->query($sql);

												//echo $opn_ix."<br>";
											}
											//exit;
												//exit;
												//print_r($goodss_options_detail);
												//exit;
												if(count($goodss_options_detail) > 0){
														$sql = "update shop_product_options_detail set insert_yn = 'N' WHERE  pid = '".$pid."' and opn_ix = '".$opn_ix."' ";
														$db->query($sql);

														for($j=0; $j < count($goodss_options_detail);$j++){
															$option_detail = $goodss_options_detail[$j];
															//echo $option->opn_ix ."==". $option_detail->opn_ix."<br>";
															if($option->opn_ix == $option_detail->opn_ix){
																	$sql = "SELECT * FROM shop_product_options_detail WHERE option_div='".$option_detail->option_div."' and opn_ix = '".$opn_ix."' ";
																	//echo $sql;
																	//exit;
																	$db->query($sql);
																	if (!$db->total){
																		/*
																		$sql = "insert into shop_product_options_detail set
																				id='',
																				pid='".$pid."',
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
																				insert_yn='Y' ";
																		*/
																		$sql = "insert into shop_product_options_detail set
																				id='',
																				pid='".$pid."',
																				opn_ix='".$opn_ix."',
																				option_div='".$option_detail->option_div."',
																				option_price='".$option_detail->option_price."',
																				option_coprice='".$option_detail->option_coprice."',
																				option_stock='".$option_detail->option_stock."',
																				option_safestock='".$option_detail->option_safestock."',
																				option_etc1='".$option_detail->option_etc1."',
																				insert_yn='Y' ";
																		//echo nl2br($sql)."<br>";
																		//exit;
																		$db->query($sql);
																	}else{
																		$db->fetch();
																		/*
																		$sql = "update shop_product_options_detail set
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
																				insert_yn='Y'
																				where id = '".$db->dt[id]."'";
																		*/
																		$sql = "update shop_product_options_detail set
																				option_div='".$option_detail->option_div."',
																				option_price='".$option_detail->option_price."',
																				option_coprice='".$option_detail->option_coprice."',
																				option_stock='".$option_detail->option_stock."',
																				option_safestock='".$option_detail->option_safestock."',
																				option_etc1='".$option_detail->option_etc1."',
																				insert_yn='Y'
																				where id = '".$db->dt[id]."'";
																		//echo nl2br($sql)."<br>";
																		//exit;
																		$db->query($sql);
																	}
															}
															unset($option_detail);
														}
														unset($option);
														$sql = "delete from shop_product_options_detail  where insert_yn = 'N' and  pid = '".$pid."' and opn_ix = '".$opn_ix."' ";
														$db->query($sql);
												}

												//exit;

										}

										$sql = "delete from shop_product_options where insert_yn = 'N' and  pid = '".$pid."' and opn_ix = '".$opn_ix."'  ";
										$db->query($sql);

								}

								unset($goodss_options_info) ;
								unset($goodss_options);
								unset($goodss_options_detail);
								unset($goodsinfos);
								unset($goodsinfo);
				} else {
					echo $message_text;
					return true;
				}

		}

}





if(!function_exists("GoodssProductCopy")){


		function GoodssProductCopy($goodss_pid){
			global $admininfo, $admin_config, $db, $soapclient, $options ;

			//print_r($_POST);
			//echo $goodss_pid;
							$install_path = "../../include/";
							//include("SOAP/Client.php");
							/*
							$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
							// server.php 의 namespace 와 일치해야함
							$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
							*/
							//상품정보를 가져온다.  가져온 상품정보를 바탕으로 아래 상품등록을 한다.
							$goodsinfos = $soapclient->call("getCoGoodsInfoByServer",$params = array("goodss_pid"=> $goodss_pid),	$options);
							//print_r($goodsinfos);
							//exit;
							$goodsinfos = (array)$goodsinfos ;
							$goodsinfo = (array)$goodsinfos[0];
							//print_r($goodsinfos);
							//print_r($goodsinfo);
							//exit;
							$sql = "SELECT id, pname FROM ".TBL_SHOP_PRODUCT." WHERE co_pid = '".$goodsinfo[id]."'";
							//echo $sql;
							//exit;
							//$db->debug = true;
							$db->query($sql);

							if (!$db->total){

								///////////////////////// 디폴트값 ///////////////////////////
								$delivery_company = "MI";	// 배송업체 : 기본값 셋팅
								$stock = "999999";		 // 재고 : 기본값 셋팅
								$safestock = "10";		// 안전재고 : 기본값 셋팅
								$stock_use_yn = "N";		// 재고사용여부 : 기본값 셋팅
								$surtax_yorn = "N";		// 면세여부 : 기본값 셋팅
								$product_type = "0";
								//////////////////////////////////////////////////////////////
							//	syslog(LOG_INFO, $goodsinfo["id"]);

								$pname = $goodsinfo[pname];
								$pname = str_replace("\t"," ", $pname);
								$pname = str_replace("'","\'", $pname);

								//$etc10 = $goodsinfo["id"];
								$pcode = $goodsinfo["pcode"];
								$editdate = $goodsinfo["editdate"];
								$co_goods = 2; // 공유해온 상품
								$co_pid = $goodsinfo["id"];

								$disp = $_POST[sc_disp];
								$state = $_POST[sc_state];


								$sql = "SELECT editdate from shop_product where pcode = '$pcode' ";
								$db->query($sql);
								$result = $db->fetchall();

								$act = "";

								if(count($result) == 0){
									$act = 'insert';
								} else {
									if ($result[0]["editdate"] != $editdate) {
										$act = 'update';
									}
								}

								$category[0] = $_POST[c_cid];
								$basic = $_POST[c_cid];

								$bimg_text = $goodsinfo["bimg"];//sprintf("http://www.isoda.co.kr/data/basic/images/product/b_%06s.gif", $productInfo["id"]);
								$img_url_copy = 1;

								if($goodsinfo["download_desc"] == ""){
									$basicinfo = $goodsinfo["basicinfo"];
								}else{
									$basicinfo = $goodsinfo["b2b_basic_info"];
								}
								$basicinfo = str_replace("<img", "<IMG", $basicinfo);
								$basicinfo = str_replace("/data/goodss/images/product_detail/", "http://www.goodss.co.kr/data/goodss/images/product_detail/", $basicinfo);

								$coprice = $goodsinfo["coprice"];
								if($_POST[price_setting] == 1){
									$listprice = $goodsinfo["coprice"] + $_POST[margin_plus];
									$sellprice = $goodsinfo["coprice"] + $_POST[margin_plus];
								}else if($_POST[price_setting] == 2){
									$listprice = $goodsinfo["listprice"] * $_POST[margin_cross];
									$sellprice = $goodsinfo["sellprice"] * $_POST[margin_cross];
								}else if($_POST[price_setting] == 9 || $_POST[price_setting] == "" ){
									$listprice = $goodsinfo["listprice"];
									$sellprice = $goodsinfo["sellprice"];
								}

								 if($_POST[round_type] && $_POST[round_precision]){
									if($_POST[round_type] == "round"){
										$listprice = roundBetterUp($listprice,-1*$_POST[round_precision]);
										$sellprice = roundBetterUp($sellprice,-1*$_POST[round_precision]);
									}else if($_POST[round_type] == "floor"){
										$listprice = roundBetterDown($listprice,-1*$_POST[round_precision]);
										$sellprice = roundBetterDown($sellprice,-1*$_POST[round_precision]);
									}

								}else{
									$listprice = round($listprice,-1);
									$sellprice = round($sellprice,-1);
								}

								//echo $sellprice;
								//exit;
								echo $pname." 상품정보 등록중...";
								//$db->debug = true;
								$bs_act = "insert"; // 스크립트 완료 구문 안찍히게 처리하기위해서 삽입 2012.04.27
								include "../product/goods_input.act.php";

								$pid = $INSERT_PRODUCT_ID;
								unset($regdate);
								unset($editdate);


									//$goodsinfos = $soapclient->call("setGoodsCopyHistory",$params = array("company_id"=> $admininfo[company_id], "co_company_id"=> $co_company_id, "pid"=> $goodsinfo[co_pid], "copy_type"=> "M"),	$options);

									//echo nl2br($goodsinfos);
									//exit;
									//$goodsinfos = (array)$goodsinfos ;
									//$goodsinfo = (array)$goodsinfos[0];
							}else{
								$db->fetch();
								$pid = $db->dt[0]; // local Server 에 상품키값
								//$pid = $db->dt[pname]; // local Server 에 상품키값
								echo $db->dt[pname]." 상품정보 업데이트중...";
								//echo  $pid."<br>";
							}

							//echo $pid;
								// 디스플레이 옵션을 가져올때는 공유된상품의 공유서버 키값을 가지고 관리되기 때문에 $goodsinfo[id] 을 가지고 디스플레이 옵션을 가져와야 한다.
								$displayoptions_info = $soapclient->call("getCoGoodsDisplayOptionInfoByServer",$params = array("pid"=> $goodsinfo[id]),	$options);
							//echo $ret;
								//print_r($displayoptions_info);
								//exit;
								$displayoptions_info = (array)$displayoptions_info ;
								if(count($displayoptions_info) > 0){
										$sql = "update shop_product_displayinfo set insert_yn = 'N' WHERE  pid = '".$pid."'  ";
										$db->query($sql);

										for($j=0;$j < count($displayoptions_info);$j++){
											$display_option = $displayoptions_info[$j];

											$sql = "SELECT * FROM shop_product_displayinfo WHERE dp_title='".$display_option->dp_title."' and pid = '".$pid."'  ";
											//echo $sql;
											$db->query($sql);

											if (!$db->total){
													$sql = "insert into shop_product_displayinfo set
															dp_ix='".$display_option->dp_ix."',
															pid='".$pid."',
															dp_title='".$display_option->dp_title."',
															dp_desc='".$display_option->dp_desc."',
															insert_yn='Y',
															dp_use='".$display_option->dp_use."',
															regdate=NOW()";

													//echo $sql."<br>";
													$db->query($sql);
											}else{
													$sql = "update into shop_product_displayinfo set
															dp_title='".$display_option->dp_title."',
															dp_desc='".$display_option->dp_desc."',
															insert_yn='Y',
															dp_use='".$display_option->dp_use."',
															where dp_ix='".$db->dt[dp_ix]."'and  pid='".$pid."' ";

													//echo $sql."<br>";
													$db->query($sql);
											}
											unset($display_option);
										}
										unset($displayoptions_info);
								}


								//exit;
								//  옵션정보를 가져올때는 공유된상품의 공유서버 키값을 가지고 관리되기 때문에 $goodsinfo[id] 을 가지고 옵션정보를 가져와야 한다.
								$goodss_options_info = $soapclient->call("getCoGoodsOptionInfoByServer",$params = array("pid"=> $goodsinfo[id]),	$options);
							//echo $ret;
								//print_r($goodss_options_info);
								//exit;
								$goodss_options_info = (array)$goodss_options_info ;
								$goodss_options = $goodss_options_info[options];
								$goodss_options_detail = $goodss_options_info[options_detail];

								//print_r($goodss_options);
								//exit;
								if(count($goodss_options) > 0){
										$sql = "update shop_product_options set insert_yn = 'N' WHERE  pid = '".$pid."'  ";
										$db->query($sql);

										for($x=0;$x < count($goodss_options);$x++){
											$option = $goodss_options[$x];
											$sql = "SELECT opn_ix FROM shop_product_options WHERE option_name = '".$option->option_name."' and pid = '".$pid."' ";
											//echo  $sql;
											//exit;
											$db->query($sql);
											//echo $db->total;
											//exit;
											if (!$db->total){
												//$goodsinfos->id 를 co_pid 로 입력하고 상품아이디(id)는 새로 발급받는다.

												$sql = "insert into shop_product_options set
														opn_ix='',
														pid='".$pid."',
														option_name='".$option->option_name."',
														option_kind='".$option->option_kind."',
														option_type='".$option->option_type."',
														option_use='".$option->option_use."',
														insert_yn='Y',
														regdate=NOW(),
														old_uid='".$option->old_uid."'
														";
														//$basic->id  는 co_pid 로 등록된다.
												//echo nl2br($sql);
												//exit;

												$db->query($sql);
												$db->query("SELECT opn_ix FROM shop_product_options WHERE opn_ix=LAST_INSERT_ID()");
												$db->fetch();
												$opn_ix = $db->dt[0];
											}else{
												$db->fetch();
												$opn_ix = $db->dt[0];

												$sql = "update shop_product_options set
														option_name='".$option->option_name."',
														option_kind='".$option->option_kind."',
														option_type='".$option->option_type."',
														option_use='".$option->option_use."',
														insert_yn='Y'
														where opn_ix = '".$opn_ix."' and pid='".$pid."' ";
														//$basic->id  는 co_pid 로 등록된다.
												//echo nl2br($sql);
												//exit;

												$db->query($sql);

												//echo $opn_ix."<br>";
											}
											//exit;
												//exit;
												//print_r($goodss_options_detail);
												//exit;
												if(count($goodss_options_detail) > 0){
														$sql = "update shop_product_options_detail set insert_yn = 'N' WHERE  pid = '".$pid."' and opn_ix = '".$opn_ix."' ";
														$db->query($sql);

														for($j=0; $j < count($goodss_options_detail);$j++){
															$option_detail = $goodss_options_detail[$j];
															//echo $option->opn_ix ."==". $option_detail->opn_ix."<br>";
															if($option->opn_ix == $option_detail->opn_ix){
																	$sql = "SELECT * FROM shop_product_options_detail WHERE option_div='".$option_detail->option_div."' and opn_ix = '".$opn_ix."' ";
																	//echo $sql;
																	//exit;
																	$db->query($sql);
																	if (!$db->total){
																		/*
																		$sql = "insert into shop_product_options_detail set
																				id='',
																				pid='".$pid."',
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
																				insert_yn='Y' ";
																		*/
																		$sql = "insert into shop_product_options_detail set
																				id='',
																				pid='".$pid."',
																				opn_ix='".$opn_ix."',
																				option_div='".$option_detail->option_div."',
																				option_price='".$option_detail->option_price."',
																				option_coprice='".$option_detail->option_coprice."',
																				option_stock='".$option_detail->option_stock."',
																				option_safestock='".$option_detail->option_safestock."',
																				option_etc1='".$option_detail->option_etc1."',
																				insert_yn='Y' ";
																		//echo nl2br($sql)."<br>";
																		//exit;
																		$db->query($sql);
																	}else{
																		$db->fetch();
																		/*
																		$sql = "update shop_product_options_detail set
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
																				insert_yn='Y'
																				where id = '".$db->dt[id]."'";
																		*/
																		$sql = "update shop_product_options_detail set
																				option_div='".$option_detail->option_div."',
																				option_price='".$option_detail->option_price."',
																				option_coprice='".$option_detail->option_coprice."',
																				option_stock='".$option_detail->option_stock."',
																				option_safestock='".$option_detail->option_safestock."',
																				option_etc1='".$option_detail->option_etc1."',
																				insert_yn='Y'
																				where id = '".$db->dt[id]."'";
																		//echo nl2br($sql)."<br>";
																		//exit;
																		$db->query($sql);
																	}
															}
															unset($option_detail);
														}
														unset($option);
														$sql = "delete from shop_product_options_detail  where insert_yn = 'N' and  pid = '".$pid."' and opn_ix = '".$opn_ix."' ";
														$db->query($sql);
												}

												//exit;

										}

										$sql = "delete from shop_product_options where insert_yn = 'N' and  pid = '".$pid."' and opn_ix = '".$opn_ix."'  ";
										$db->query($sql);

								}

								unset($goodss_options_info) ;
								unset($goodss_options);
								unset($goodss_options_detail);
								unset($goodsinfos);
								unset($goodsinfo);


		}
}
