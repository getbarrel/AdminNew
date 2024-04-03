<?
include("../../class/database.class");
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include("../class/layout.class");

$install_path = "../../include/";
include("SOAP/Client.php");


if($admininfo[company_id] == ""){
	echo "<script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='/admin/admin.php'</script>";
	exit;
}

$db = new Database;
$db2 = new Database;


$db->query("SELECT * FROM co_client_hostservers where chs_ix = '".$chs_ix."'  ");

if($db->total){
	$db->fetch();

	$hostserver = $db->dt[server_url];
	$server_name = $db->dt[server_name];
}else{
	echo "<script language='javascript'>alert('호스트 서버 선택후 판매사이트 설정이 가능합니다.');</script>";
	exit;
}

//print_r($_POST);

if ($act == "update"){
//echo count($pid);
	/*
	if($approval_type == 1){
		$approval_str = "state = 1 ";
	}else if($approval_type == 2){
		$approval_str = "state = 1, disp = 1 ";
	}else if($approval_type == 3){
		$approval_str = "disp = 0 ";
	}else if($approval_type == 4){
		$approval_str = "state = 0 ";
	}
	*/
	if($apply_data == 1){
		if($admininfo[admin_level] == 9){
			if($co_goods_server == 2){
				//echo $hostserver;
				$soapclient = new SOAP_Client("http://".$hostserver."/admin/cogoods/api/");
				// server.php 의 namespace 와 일치해야함
				$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
				for($i=0;$i < count($company_id_pid);$i++){
					$ids = split("-",$company_id_pid[$i]);
					$co_company_id = $ids[0];
					$pid = $ids[1];
					//echo $admininfo[company_id]."::::".$co_company_id."::::".$pid."<br>";

					//상품정보를 가져온다.  가져온 상품정보를 바탕으로 아래 상품등록을 한다.
					$goodsinfos = $soapclient->call("getCoGoodsInfoByServer",$params = array("company_id"=> $admininfo[company_id], "co_company_id"=> $co_company_id, "pid"=> $pid),	$options);
					//echo nl2br($goodsinfos);
					//exit;
					$goodsinfos = (array)$goodsinfos ;
					$goodsinfo = (array)$goodsinfos[0];
					//print_r($goodsinfos);
					//print_r($goodsinfo);
					//exit;
					$sql = "SELECT id FROM ".TBL_SHOP_PRODUCT." WHERE co_company_id = '".$goodsinfo[co_company_id]."' and co_pid = '".$goodsinfo[co_pid]."'";
					//echo $sql;
					$db->query($sql);

					if (!$db->total){


						//admin='".$goodsinfo[admin]."',
						//admin='".$goodsinfo[admin]."', --> 해당상품 업체가 자동으로 공유사이트에 등록된 업체키로 대치 되어야 한다.
						// 입점업체 승인시 귀사 사이트가 입점 업체로 등록되고 ...


						$sql = "insert into shop_product set
								id='',
								product_type='".$goodsinfo[product_type]."',
								pname='".$goodsinfo[pname]."',
								pcode='".$goodsinfo[pcode]."',
								brand='".$goodsinfo[brand]."',
								brand_name='".$goodsinfo[brand_name]."',
								company='".$goodsinfo[company]."',
								paper_pname='".$goodsinfo[paper_pname]."',
								buying_company='".$goodsinfo[buying_company]."',
								shotinfo='".$goodsinfo[shotinfo]."',
								buyingservice_coprice='".$goodsinfo[buyingservice_coprice]."',
								listprice='".$goodsinfo[listprice]."',
								sellprice='".$goodsinfo[sellprice]."',
								coprice='".$goodsinfo[coprice]."',
								reserve_yn='".$goodsinfo[reserve_yn]."',
								reserve='".$goodsinfo[reserve]."',
								reserve_rate='".$goodsinfo[reserve_rate]."',
								sns_btn_yn='".$goodsinfo[sns_btn_yn]."',
								sns_btn='".$goodsinfo[sns_btn]."',
								bimg='".$goodsinfo[bimg]."',
								mimg='".$goodsinfo[mimg]."',
								simg='".$goodsinfo[simg]."',
								basicinfo='".$goodsinfo[basicinfo]."',
								icons='".$goodsinfo[icons]."',
								state='".$goodsinfo[state]."',
								disp='".$goodsinfo[disp]."',
								movie='".$goodsinfo[movie]."',
								vieworder='".$goodsinfo[vieworder]."',
								admin='".$goodsinfo[admin]."',
								stock='".$goodsinfo[stock]."',
								safestock='".$goodsinfo[safestock]."',
								view_cnt='".$goodsinfo[view_cnt]."',
								order_cnt='".$goodsinfo[order_cnt]."',
								recommend_cnt='".$goodsinfo[recommend_cnt]."',
								search_keyword='".$goodsinfo[search_keyword]."',
								reg_category='".$goodsinfo[reg_category]."',
								option_stock_yn='".$goodsinfo[option_stock_yn]."',
								inventory_info='".$goodsinfo[inventory_info]."',
								surtax_yorn='".$goodsinfo[surtax_yorn]."',
								delivery_company='".$goodsinfo[delivery_company]."',
								one_commission='".$goodsinfo[one_commission]."',
								commission='".$goodsinfo[commission]."',
								stock_use_yn='".$goodsinfo[stock_use_yn]."',
								delivery_policy='".$goodsinfo[delivery_policy]."',
								delivery_product_policy='".$goodsinfo[delivery_product_policy]."',
								delivery_package='".$goodsinfo[delivery_package]."',
								delivery_price='".$goodsinfo[delivery_price]."',
								free_delivery_yn='".$goodsinfo[free_delivery_yn]."',
								free_delivery_count='".$goodsinfo[free_delivery_count]."',
								etc1='".$goodsinfo[etc1]."',
								etc2='".$goodsinfo[etc2]."',
								etc3='".$goodsinfo[etc3]."',
								etc4='".$goodsinfo[etc4]."',
								etc5='".$goodsinfo[etc5]."',
								etc6='".$goodsinfo[etc6]."',
								etc7='".$goodsinfo[etc7]."',
								etc8='".$goodsinfo[etc8]."',
								etc9='".$goodsinfo[etc9]."',
								etc10='".$goodsinfo[etc10]."',
								hotcon_event_id='".$goodsinfo[hotcon_event_id]."',
								hotcon_pcode='".$goodsinfo[hotcon_pcode]."',
								co_goods='2',
								co_pid='".$goodsinfo[co_pid]."',
								co_company_id='".$goodsinfo[co_company_id]."',
								bs_goods_url='".$goodsinfo[bs_goods_url]."',
								bs_site='".$goodsinfo[bs_site]."',
								editdate=NOW(),
								regdate=NOW()

									";
									//$goodsinfos->id  는 co_pid 로 등록된다.
							//echo $sql;
							//exit;
							$db->query($sql);
							$db->query("SELECT id FROM shop_product WHERE id=LAST_INSERT_ID()");
							$db->fetch();
							$pid = $db->dt[0];


							$goodsinfos = $soapclient->call("setGoodsCopyHistory",$params = array("company_id"=> $admininfo[company_id], "co_company_id"=> $co_company_id, "pid"=> $goodsinfo[co_pid], "copy_type"=> "M"),	$options);
							//echo nl2br($goodsinfos);
							//exit;
							//$goodsinfos = (array)$goodsinfos ;
							//$goodsinfo = (array)$goodsinfos[0];
					}else{
						$db->fetch();
						$pid = $db->dt[0]; // local Server 에 상품키값
						//echo  $pid."<br>";
					}

					//echo $pid;
						// 디스플레이 옵션을 가져올때는 공유된상품의 공유서버 키값을 가지고 관리되기 때문에 $goodsinfo[pid] 을 가지고 디스플레이 옵션을 가져와야 한다.
						$displayoptions_info = $soapclient->call("getCoGoodsDisplayOptionInfoByServer",$params = array("company_id"=> $admininfo[company_id], "co_company_id"=> $goodsinfo[co_company_id], "pid"=> $goodsinfo[pid]),	$options);
					//echo $ret;
						//print_r($displayoptions_info);
						//exit;
						$displayoptions_info = (array)$displayoptions_info ;

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
									insert_yn='".$display_option->insert_yn."',
									dp_use='".$display_option->dp_use."',
									regdate=NOW()";

							//echo $sql."<br>";
							$db->query($sql);
							}
						}

						//exit;
						//  옵션정보를 가져올때는 공유된상품의 공유서버 키값을 가지고 관리되기 때문에 $goodsinfo[pid] 을 가지고 옵션정보를 가져와야 한다.
						$options_info = $soapclient->call("getCoGoodsOptionInfoByServer",$params = array("company_id"=> $admininfo[company_id], "co_company_id"=> $goodsinfo[co_company_id], "pid"=> $goodsinfo[pid]),	$options);
					//echo $ret;
						//print_r($options_info);
						//exit;
						$options_info = (array)$options_info ;
						$options = $options_info[options];
						$options_detail = $options_info[options_detail];

						//print_r($option);
						for($x=0;$x < count($options);$x++){
							$option = $options[$x];
							$sql = "SELECT opn_ix FROM shop_product_options WHERE option_name = '".$option->option_name."' and pid = '".$pid."' ";
							//echo  $sql;
							//exit;
							$db->query($sql);

							if (!$db->total){
								//$goodsinfos->id 를 co_pid 로 입력하고 상품아이디(id)는 새로 발급받는다.

								$sql = "insert into shop_product_options set
										opn_ix='',
										pid='".$pid."',
										option_name='".$option->option_name."',
										option_kind='".$option->option_kind."',
										option_type='".$option->option_type."',
										option_use='".$option->option_use."',
										insert_yn='".$option->insert_yn."',
										regdate=NOW(),
										old_uid='".$option->old_uid."'
										";
										//$basic->id  는 co_pid 로 등록된다.
								//echo $sql;
								//exit;

								$db->query($sql);
								$db->query("SELECT opn_ix FROM shop_product_options WHERE opn_ix=LAST_INSERT_ID()");
								$db->fetch();
								$opn_ix = $db->dt[0];
							}else{
								$db->fetch();
								$opn_ix = $db->dt[0];
								//echo $opn_ix."<br>";
							}
							//exit;
								//exit;
								//return $options_detail;
								for($j=0; $j < count($options_detail);$j++){
									$option_detail = $options_detail[$j];

									$sql = "SELECT * FROM shop_product_options_detail WHERE option_div='".$option_detail->option_div."' and pid = '".$pid."' ";
									//cho $sql;
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
											insert_yn='".$option_detail->insert_yn."' ";
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
											insert_yn='".$option_detail->insert_yn."' ";
									//echo $sql."<br>";
									//exit;
									$db->query($sql);
									}
								}
								//exit;

						}

					//	exit;
					//exit;
					//print_r($ret);
				}
				/*
				if($ret){
					echo("<script>alert('서버에 정상적으로 등록 되었습니다.');parent.document.location.reload();</script>");
				}else{
					//echo("<script>alert('서버에 이미 공유된 상품 입니다.');</script>");
				}
				*/
				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('상품이 정상적으로 등록 되었습니다.');parent.document.location.reload();</script>");
				exit;
			}else{
				for($i=0;$i<count($pid);$i++){//disp='".$_POST["disp".$pid[$i]]."',
					$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET co_goods = '".$co_goods."' , editdate = NOW() Where id = '".$pid[$i]."' "; // ,state = ".$state." , disp = '".$disp."'
					//echo $sql."<br><br>";
					//exit;
					$db->query ($sql);
				}
			}
		}
	}else if($apply_data == 2){

	}else{
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
				$where .= "and p.".$search_type." LIKE '%".$search_text."%' ";
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
				$where .= " and p.disp = ".$disp;
			}

			if($co_type == "co_goods"){
				$where .= " and p.co_goods = '1'";
			}


			if($state2 != ""){
				$where .= " and state = ".$state2."";
			}


			if($cid2 != ""){
				$where .= " and r.cid LIKE '".substr($cid2,0,$cut_num)."%'";
			}else{
				$where .= "";
			}
			if($admininfo[admin_level] == 9){
				if($company_id != ""){
					$addWhere = "and admin ='".$company_id."'";
				}else{
					unset($addWhere);
				}
				$sql = "SELECT distinct p.id
				FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_SHOP_BRAND." b
				where c.company_id = p.admin and p.id = r.pid and p.brand = b.b_ix $addWhere $where $orderbyString ";
				//echo $sql;
				$db->query($sql);
			}else{
				$sql = "SELECT distinct p.id
				FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_PRODUCT_RELATION." r, ".TBL_COMMON_COMPANY_DETAIL." c, ".TBL_SHOP_BRAND." b
				where c.company_id = p.admin and p.id = r.pid  and p.brand = b.b_ix and admin ='".$admininfo[company_id]."' $where $orderbyString ";


				$db->query($sql);
			}

			if($co_goods == 2){
				$soapclient = new SOAP_Client("http://".$hostserver."/admin/cogoods/api/");
				// server.php 의 namespace 와 일치해야함
				$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

				for ($i = 0; $i < $db->total; $i++){
					$db->fetch($i);
					$sql = "select id,product_type,pname,pcode,brand,brand_name,company,paper_pname,buying_company,shotinfo,buyingservice_coprice,listprice,sellprice,coprice,reserve_yn,reserve,reserve_rate,sns_btn_yn,sns_btn,bimg,mimg,simg,basicinfo,icons,state,disp,movie,vieworder,admin,stock,safestock,view_cnt,order_cnt,recommend_cnt,search_keyword,reg_category,option_stock_yn,inventory_info,surtax_yorn,delivery_company,one_commission,commission,stock_use_yn,delivery_policy,delivery_product_policy,delivery_package,delivery_price,free_delivery_yn,free_delivery_count,etc1,etc2,etc3,etc4,etc5,etc6,etc7,etc8,etc9,etc10,hotcon_event_id,hotcon_pcode,co_goods,bs_goods_url,bs_site
					from ".TBL_SHOP_PRODUCT."  Where id = '".$db->dt[id]."' ";

					$db2->query ($sql);
					$db2->fetch(0,"object");
					$_goodsinfos = $db2->dt;
					//echo count($goodsinfos);
					foreach($_goodsinfos as $key => $value){
						$goodsinfos[$key]= urlencode($value);
					}
					//print_r($goodsinfos);
					$ret = $soapclient->call("SellerShopGoodsReg",$params = array("company_id"=> $admininfo[company_id], "goodsinfos"=> $goodsinfos),	$options);
					//echo $ret;
					//print_r($ret);
				}

				if($ret){
					//echo("<script>alert('서버에 정상적으로 등록 되었습니다.');parent.document.location.reload();</script>");
				}else{
					echo("<script>alert('서버 저장시 장애가 발생했습니다.');</script>");
				}
				exit;
			}else{
				for ($i = 0; $i < $db->total; $i++){
					$db->fetch($i);
					$sql = "UPDATE ".TBL_SHOP_PRODUCT." SET co_goods = '".$co_goods."' , editdate = NOW() Where id = '".$db->dt[id]."' ";
					//echo $sql;
					$db2->query ($sql);

				}
			}
	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script language='javascript'>show_alert('정상적으로 수정되었습니다.');parent.document.location.reload();</script>";

}

?>