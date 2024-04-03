<?
//include("../../class/database.class");
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include("../class/layout.class");
include("./co_goods.lib.php");
$install_path = "../../include/";
include("SOAP/Client.php");


if($admininfo[company_id] == ""){
	echo "<script language='javascript'>alert('관리자 로그인후 사용하실수 있습니다.');location.href='/admin/admin.php'</script>";
	exit;	
}

$db = new Database;
$db2 = new Database;

//co_goods.lib.php 파일에 공통으로 호스트 서버 지정 하는 부분이 추가 
//$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'  ");

//$db->fetch();
//$hostserver = $db->dt[hostserver];
//echo $hostserver;

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
			if($co_goods == 2){
				//echo $hostserver;
				$soapclient = new SOAP_Client("http://".$hostserver."/admin/cogoods/api/");
				// server.php 의 namespace 와 일치해야함
				$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
				//print_r($pid);

				
				for($i=0;$i < count($pid);$i++){
					$sql = "select id,product_type,pname,pcode,brand,brand_name,company,paper_pname,buying_company,shotinfo,buyingservice_coprice,listprice,sellprice,coprice,reserve_yn,reserve,reserve_rate,sns_btn_yn,sns_btn,basicinfo,icons,state,disp,movie,vieworder,admin,stock,safestock,view_cnt,order_cnt,recommend_cnt,search_keyword,reg_category,option_stock_yn,inventory_info,surtax_yorn,delivery_company,one_commission,commission,stock_use_yn,delivery_policy,delivery_product_policy,delivery_package,delivery_price,free_delivery_yn,free_delivery_count,etc1,etc2,etc3,etc4,etc5,etc6,etc7,etc8,etc9,etc10,hotcon_event_id,hotcon_pcode,co_goods,bs_goods_url,bs_site,
					'http://".$admin_config[mall_domain]."/".PrintImage($admin_config[mall_data_root]."/images/product", $pid[$i], "b")."' as bimg,
					'http://".$admin_config[mall_domain]."/".PrintImage($admin_config[mall_data_root]."/images/product", $pid[$i], "m")."' as mimg,
					'http://".$admin_config[mall_domain]."/".PrintImage($admin_config[mall_data_root]."/images/product", $pid[$i], "s")."' as simg
					from ".TBL_SHOP_PRODUCT."  Where id = '".$pid[$i]."' ";
					//echo $sql."<br>";
					$db->query ($sql);
					//$db->fetch(0,"array", MYSQL_ASSOC);
					$db->fetch(0,"object");
					if($db->total){
						$goodsinfos = $db->dt;
						//$goodsinfos = (array)$goodsinfos;						
						//echo PrintImage($admin_config[mall_data_root]."/images/product", $pid[$i], "b");
						//exit;
						//$goodsinfos->basic_img= file_get_contents("../../".PrintImage($admin_config[mall_data_root]."/images/product", $pid[$i], "b"));
						//print_r($goodsinfos);
						$server_pid = $soapclient->call("SellerShopGoodsReg",$params = array("company_id"=> $admininfo[company_id], "goodsinfos"=> $goodsinfos),	$options);
						//$ret;
						//unset($goodsinfos);
						//print_r($server_pid);
						//exit;
						
						$sql = "select '' as dp_ix,pid,dp_title,dp_desc,insert_yn,dp_use from shop_product_displayinfo where pid = '".$pid[$i]."' ";
						$db->query ($sql);
						//echo $sql;
						//exit;
						if($db->total){
							$display_options = $db->fetchall("object");						
							$display_options = $display_options;
							//print_r($display_options);
							$ret = $soapclient->call("SellerShopGoodDisplayOptionReg",$params = array("company_id"=> $admininfo[company_id],"server_pid"=> $server_pid, "display_options"=> $display_options),	$options);
							//$ret;
							//unset($goodsinfos);
							//print_r($ret);
							//exit;
						}
						
						$sql = "select '' as opn_ix,pid,option_name,option_kind,option_type,option_use,insert_yn,regdate,old_uid from shop_product_options where pid = '".$pid[$i]."' ";
						//echo $sql;
						$db->query ($sql);
						if($db->total){
							$options = $db->fetchall("object");
							$option_infos[options] = $options;

							$sql = "select id,pid,opn_ix,option_div,option_price,option_coprice,option_m_price,option_d_price,option_a_price,option_stock,option_safestock,option_etc1,option_useprice,insert_yn from shop_product_options_detail where pid = '".$pid[$i]."' ";
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
								$ret = $soapclient->call("SellerShopGoodOptionReg",$params = array("company_id"=> $admininfo[company_id],"server_pid"=> $server_pid, "option_infos"=> $option_infos),	$options);
								print_r($ret);
							}
						}
					}
					
					$ret = $soapclient->call("autoGoodsReg",$params = array("company_id"=> $admininfo[company_id],"co_pid"=> $pid[$i], "option_infos"=> $option_infos),	$options);
					//print_r($ret);
					//exit;
	

				}// for loop
				/*
				if($ret){
					echo("<script>alert('서버에 정상적으로 등록 되었습니다.');parent.document.location.reload();</script>");
				}else{
					//echo("<script>alert('서버에 이미 공유된 상품 입니다.');</script>");
				}
				*/
				echo("<script language='javascript' src='../js/message.js.php'></script><script>show_alert('서버에 정상적으로 등록 되었습니다.');parent.document.location.reload();</script>");
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
			//echo $sql;
			if($co_goods == 2){
				echo $hostserver;
				$soapclient = new SOAP_Client("http://".$hostserver."/admin/cogoods/api/");
				// server.php 의 namespace 와 일치해야함
				$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
				
				for ($i = 0; $i < $db->total; $i++){
					$db->fetch($i);
					$sql = "select * from ".TBL_SHOP_PRODUCT."  Where id = '".$db->dt[id]."' ";
					
					$db2->query ($sql);
					$db2->fetch(0,"object");
					$_goodsinfos = $db2->dt;
					//echo count($goodsinfos);
					foreach($_goodsinfos as $key => $value){
						$goodsinfos[$key]= urlencode($value);
					}
					
					$goodsinfos[basic_img]= file_get_contents("../../".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[id], "b"));
					//print_r($goodsinfos);
					$ret = $soapclient->call("SellerShopGoodsReg",$params = array("company_id"=> $admininfo[company_id], "goodsinfos"=> $goodsinfos),	$options);
					//echo $ret;
					print_r($ret);
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