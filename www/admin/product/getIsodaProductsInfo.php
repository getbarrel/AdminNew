<?php
define_syslog_variables();
openlog("phplog", LOG_PID , LOG_LOCAL0);

	include_once("../../class/database.class");
syslog(LOG_INFO, $_SERVER['PHP_SELF']."START");

	//////// 쉘에서 구동시 세션이 없어서 처리해줌 ///////////
	$db = new Database;
	
	$sql = "select company_id from common_company_detail  where com_type = 'A'  ";
	$db->query($sql);
	$db->fetch();
	$admininfo[company_id] = $db->dt[company_id];
	
	$sql = "select mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
	$db->query($sql);
	$db->fetch();
	
	$admininfo[mall_data_root] = $db->dt[mall_data_root];
	$admininfo[admin_level] = 9;
	$admininfo[language] = 'korea';
	$admininfo[mall_type] = $db->dt[mall_type];
	$admin_config[mall_data_root] = $db->dt[mall_data_root];

	//////////////////////////////////////////////////////////
	
	$minPid = $_REQUEST["pid"];
	//$minPid = '081291';
	$minPid = '000000'; // 전부
	$pid = $minPid; 


	$c="";
	$c = shell_exec("lynx --dump 'http://www.isoda.co.kr/c_price/share.php?branch=count'");
	$product_count = unserialize(base64_decode($c));
	
	//print_r($product_count);
	//echo $product_count[0][total];
	//exit;
	
	for($limit=0; $limit <= $product_count[0][total]; $limit += 100){

		$limit_str = "limit ".$limit.", 100";

		$x = shell_exec("lynx --dump 'http://www.isoda.co.kr/c_price/share.php?pid=$minPid&branch=product&limit=$limit_str'");
		$productInfos = unserialize(base64_decode($x));
		$admininfo[admin_level] = 9;

		$admin = '337c27b1fff52964ac53dd9ce00782d9';

		//print_r($productInfos);
		//echo count($productInfos);
		//exit;

		$goods_desc_copy = 1;
		$iii = 0;
		foreach ($productInfos as $productInfo) {
			$db = new Database;
			$sql = "SELECT id, editdate,etc2 from shop_product where co_pid = '" . $productInfo["id"] . "' ";
			$db->query($sql);
			$result = $db->fetchall();
			 
			$act = "";
			
			if(count($result) == 0){
				$act = 'insert';
			}else {
				if ($result[0]["editdate"] != $productInfo["editdate"]) 
				{
					$act = 'update';
				}
			}
			
			if($act == ""){
				//$act = "skip";
				$act = "update";
			// 상품정보 전체 업데이트를 위해
			}
			if(($act != "skip")){
			$co_pid = $productInfo["id"];	
			if($act == "update"){
				$id = $result[0]["id"];

				if($result[0]["etc2"] == $productInfo["editdate"]){ //isoda 상품이 수정되지 않았으면 

					$sql = "update shop_product set editdate=NOW() where  id='".$id."' ";
					$db->query($sql);
					continue;
				}

			}	
				$x = shell_exec("lynx --dump 'http://www.isoda.co.kr/c_price/share.php?pid=$co_pid&branch=category'");
				
				///////////////////////// 디폴트값 ///////////////////////////
				$delivery_company = "MI";	// 배송업체 : 기본값 셋팅
				$stock = "999999";		 // 재고 : 기본값 셋팅
				$safestock = "10";		// 안전재고 : 기본값 셋팅
				$stock_use_yn = "N";		// 재고사용여부 : 기본값 셋팅
				$surtax_yorn = "N";		// 면세여부 : 기본값 셋팅
				$product_type = "0";
				$state = '1';
				$disp = '1';
				//////////////////////////////////////////////////////////////
				syslog(LOG_INFO, $productInfo["id"] . "    " . $act . "   " . $productInfo["pname"]);
				
				$pname = $productInfo["pname"];
				$pname = str_replace("\t"," ", $pname);
				$pname = str_replace("'","\'", $pname);
				
				$pcode = $productInfo["pcode"];

				$etc2 = $productInfo["editdate"];
				$etc10 = $productInfo["regdate"];

				$regdate = "";
				$editdate = "";

				$vieworder = $productInfo["vieworder"];
				$download_img = $productInfo["download_img"];
				$download_desc = $productInfo["download_desc"];
				
				$cids = unserialize(base64_decode($x));
				$c = count($cids);
		
				for($i = 0; $i < $c; $i++){
				syslog(LOG_INFO, sprintf("before categorycode = %s", $cids[$i]["cid"]));
					$cids[$i]["cid"] = categoryCodeReplace($cids[$i]["cid"]); // goodss 카테고리정보로 카테고리코드변경
		
					if ($cids[$i]["basic"] == "1") {
						$basic = $cids[$i]["cid"];
					syslog(LOG_INFO, sprintf("after categorycode = %s", $cids[$i]["cid"]));
					$category = array();
						$category[0] = $basic; 
					syslog(LOG_INFO, sprintf(print_r($category, true)));
					}
					
				}
			
				$bimg_text = sprintf("http://www.isoda.co.kr/data/basic/images/product/b_%06s.gif", $productInfo["id"]);
				$img_url_copy = 1;
				
				$basicinfo = $productInfo["basicinfo"];
				$basicinfo = str_replace("<img", "<IMG", $basicinfo);
				$basicinfo = str_replace("/data/basic/images/product_detail/", "http://www.isoda.co.kr/data/basic/images/product_detail/", $basicinfo);
				
				$coprice = 0;
				$coprice = $productInfo["listprice"];//아이소다 공급가(VAT포함) 가격을 굿스 공급가로
				//$listprice = $productInfo["listprice"];
				$sellprice = $coprice * 1.5;
				$listprice = $sellprice;
				
				$xx = shell_exec("lynx --dump 'http://www.isoda.co.kr/c_price/share.php?pid=$co_pid&branch=options'");
				
				$ioptionsList = unserialize(base64_decode($xx));
				
				
				$options = array();
				if($ioptionsList != ""){
					$i = 0;
					foreach ($ioptionsList as $ioptions) {
						$option_ix = $ioptions["opn_ix"];
						$x = shell_exec("lynx --dump 'http://www.isoda.co.kr/c_price/share.php?pid=$co_pid&branch=option&option_ix=$option_ix'");
						$ioptionDetailList = unserialize(base64_decode($x));
						
						$options[$i][option_type] = "9";
						$options[$i][option_kind] = "s";
						$options[$i][option_use] = "1";
						$options[$i][option_name] = $ioptions["option_name"];
						$j = 0;
						
						foreach ($ioptionDetailList as $optionDetail) {
							$options[$i][details][$j][option_div] = $optionDetail["option_div"];
							$options[$i][details][$j][price] = $optionDetail["option_price"];
							$options[$i][details][$j][etc1] = $optionDetail["option_etc1"];
							$j++;				
						}
						$i ++;
					}
					
				}
			$db->debug = true;
			include "goods_input.act.php";
			}
		//if($iii > 10){
		//	exit;
		//}
		$iii++;
		}

	}
syslog(LOG_INFO, $_SERVER['PHP_SELF']."END");

	$sql = "update shop_product set state='0' where date_format(editdate, '%Y%m%d') <> '".date("Ymd")."' and date_format(regdate, '%Y%m%d') <> '".date("Ymd")."' and admin='".$admin."'  ";
	$db->query($sql);

closelog();

function categoryCodeReplace($cid)
{
	// 첫번째 나오는 값이 goodss 뒤의 값이 isoda
	$result = "";
        $codeMap = array(
                                array("000001001001000", "001001001000000"),
                                array("000001001002000", "001001002000000"),
                                array("000001001003000", "001001003000000"),
                                array("000001002001000", "001011001000000"),
                                array("000001002002000", "001011002000000"),
                                array("000001003001000", "001002001000000"),
                                array("000001003002000", "001002005000000"),
                                array("000001003003000", "001002002000000"),
                                array("000001003004000", "001002006000000"),
                                array("000001004001000", "001003003000000"),
                                array("000001004002000", "001003001000000"),
                                array("000001005000000", "001004001000000"),
                                array("000001006000000", "001004002000000"),
                                array("000001007000000", "001003002000000"),
                                array("000001008000000", "001012000000000"),
                                array("000001009003000", "001003002000000"),
                                array("000002001000000", "009001000000000"),
                                array("000002002000000", "009002000000000"),
                                array("000002009000000", "009004000000000"),
                                array("001001001000000", "001006001000000"),
                                array("001001002000000", "001006005000000"),
                                array("001001005000000", "001006002000000"),
                                array("001002000000000", "001007000000000"),
                                array("001002001000000", "001009000000000"),
                                array("001002003000000", "001004004000000")
                        );
	
	$c = count($codeMap);
	$result = "";
	for($i = 0; $i < $c; $i++){
		if($cid == $codeMap[$i][1]){
			$result = $codeMap[$i][0];
		}
	}
	return $result;
}

