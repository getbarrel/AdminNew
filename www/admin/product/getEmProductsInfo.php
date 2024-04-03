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
	$c = shell_exec("lynx --dump 'http://www.emever.com/cron/share.php?branch=count'");
	$product_count = unserialize(base64_decode($c));
	
	//print_r($product_count);
	//echo $product_count[0][total];
	//exit;
	
	for($limit=0; $limit <= $product_count[0][total]; $limit += 100){

		$limit_str = "limit ".$limit.", 100";


		$x = shell_exec("lynx --dump 'http://www.emever.com/cron/share.php?pid=$minPid&branch=product&limit=$limit_str' ");
		$productInfos = unserialize(base64_decode($x));
		$admininfo[admin_level] = 9;
		$admin = '6c12b1415134b1d0995508af371303c8';
		$goods_desc_copy = 1;
		$iii = 0;
		$jjj=0;

		//echo count($productInfos);
		//print_r($productInfos);
		//exit;

		foreach ($productInfos as $productInfo) {
			echo "update-".$iii."<br>insert-".$jjj;

			$db = new Database;
			$sql = "SELECT id, editdate ,etc2 from shop_product where admin='6c12b1415134b1d0995508af371303c8' and co_pid = '" . $productInfo["id"] . "' ";
			$db->query($sql);
			$result = $db->fetchall();
	  
			//print_r($productInfo);
			//exit;
			
			$act = "";
			
			if(count($result) == 0){
				$act = 'insert';
				$jjj++;
			}else {
				if ($result[0]["editdate"] != $editdate) 
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
				$state = $productInfo["state"];
				$disp = $productInfo["disp"];

				if($act == "update"){
					$id = $result[0]["id"];
					$iii++;

					if($result[0]["etc2"] == $productInfo["editdate"]){ //isoda 상품이 수정되지 않았으면 
						$sql = "update shop_product set editdate=NOW() , state='".$state."', disp='".$disp."'  where  id='".$id."' ";
						$db->query($sql);
						continue;
					}
				}
				
				$x = shell_exec("lynx --dump 'http://www.emever.com/cron/share.php?pid=$co_pid&branch=category'");
				
				///////////////////////// 디폴트값 ///////////////////////////
				$delivery_company = "MI";	// 배송업체 : 기본값 셋팅
				$stock = "999999";		 // 재고 : 기본값 셋팅
				$safestock = "10";		// 안전재고 : 기본값 셋팅
				$stock_use_yn = "N";		// 재고사용여부 : 기본값 셋팅
				$surtax_yorn = "N";		// 면세여부 : 기본값 셋팅
				$product_type = "0";
				//////////////////////////////////////////////////////////////
				syslog(LOG_INFO, $productInfo["id"] . "    " . $act . "   " . $productInfo["pname"]);

				$pname =  iconv("euc-kr","utf-8", $productInfo["pname"]);
				$pname = str_replace("\t"," ", $pname);
				$pname = str_replace("'","\'", $pname);
				
				$pcode =  iconv("euc-kr","utf-8", $productInfo["pcode"]);
				$pcode = str_replace("\t"," ", $pcode);
				$pcode = str_replace("'","\'", $pcode);

				$etc2 = $productInfo["editdate"];
				$etc10 = $productInfo["regdate"];
				
				$regdate = $productInfo["regdate"];
				$editdate = "";

				$vieworder = $productInfo["vieworder"];

				//$download_img = $productInfo["download_img"];
				//$download_desc = $productInfo["download_desc"];

					$cids = unserialize(base64_decode($x));
					$c = count($cids);
					
					$basic="";
					$category = array();
					for($i = 0; $i < $c; $i++){
						syslog(LOG_INFO, sprintf("before categorycode = %s", $cids[$i]["cid"]));
						$cids[$i]["cid"] = categoryCodeReplace($cids[$i]["cid"]); // goodss 카테고리정보로 카테고리코드변경
						$basic = $cids[$i]["cid"];
						syslog(LOG_INFO, sprintf("after categorycode = %s", $cids[$i]["cid"]));
						$category[$i] = $basic; 
						syslog(LOG_INFO, sprintf(print_r($category, true)));
					}

					$bimg_text = sprintf("http://emever.com/data/fashion2/images/product/m_%06s.gif", $productInfo["id"]);
					$img_url_copy = 1;
					
					$basicinfo =  iconv("euc-kr","utf-8", $productInfo["basicinfo"]);
					$basicinfo = str_replace("<img", "<IMG", $basicinfo);
					$basicinfo = str_replace("/data/fashion2/images/product_detail/", "http://emever.com/data/fashion2/images/product_detail/", $basicinfo);
					$basicinfo = str_replace("\t"," ", $basicinfo);
					$basicinfo = str_replace("'","\'", $basicinfo);

					$xxx = shell_exec("lynx --dump 'http://www.emever.com/cron/share.php?pid=$co_pid&branch=priceinfo'");
					$priceinfo = unserialize(base64_decode($xxx));

					$coprice = 0;
					$sellprice = 0;
					$listprice = 0;
					$coprice   = $priceinfo[0]["coprice"];
					$sellprice = $priceinfo[0]["listprice"];
					$listprice = $priceinfo[0]["listprice"];

					$xx = shell_exec("lynx --dump 'http://www.emever.com/cron/share.php?pid=$co_pid&branch=options'");
					$ioptionsList = unserialize(base64_decode($xx));
					
					$options = array();
					if($ioptionsList != ""){
						$i = 0;
						foreach ($ioptionsList as $ioptions) {
							$option_ix = $ioptions["opn_ix"];
							$x = shell_exec("lynx --dump 'http://www.emever.com/cron/share.php?pid=$co_pid&branch=option&option_ix=$option_ix'");
							$ioptionDetailList = unserialize(base64_decode($x));
							
							$options[$i][option_type] = "9";
							$options[$i][option_kind] = "s";
							$options[$i][option_use] = "1";
							$options[$i][option_name] =  iconv("euc-kr","utf-8", $ioptions["option_name"]);
							$j = 0;
							
							foreach ($ioptionDetailList as $optionDetail) {
								$options[$i][details][$j][option_div] =  iconv("euc-kr","utf-8",$optionDetail["option_div"]);
								$options[$i][details][$j][price] = $optionDetail["option_price"];
								$options[$i][details][$j][etc1] =  iconv("euc-kr","utf-8",$optionDetail["option_etc1"]);
								$j++;				
							}
							$i ++;
						}
						
					}

				$db->debug = true;
				$bs_act ='_act';
				$mmode = "shell";
				include "goods_input.act.php";
			}
		//if($iii > 10){
		//	exit;
		//}
		//$iii++;
		}
	}

syslog(LOG_INFO, $_SERVER['PHP_SELF']."END");

	$sql = "update shop_product set state='0' where date_format(editdate, '%Y%m%d') <> '".date("Ymd")."' and date_format(regdate, '%Y%m%d') <> '".date("Ymd")."'  and admin='".$admin."'  ";
	$db->query($sql);

closelog();

function categoryCodeReplace($cid)
{
	// 첫번째 나오는 값이 goodss 뒤의 값이 emever
	$result = "";
        $codeMap = array(
                                array("006001001000000", "010005000000000"),
                                array("006001002000000", "010001000000000"),
                                array("006001003000000", "010006000000000"),
                                array("006001004000000", "010007000000000"),
                                array("006001005000000", "010008000000000"),
                                array("006002000000000", "006000000000000"),
                                array("006003000000000", "005000000000000"),
                                array("006004000000000", "004000000000000"),
                                array("006005000000000", "001000000000000"),
                                array("006006000000000", "003000000000000"),
                                array("006007000000000", "007000000000000"),
								array("006011000000000", "009000000000000"),
                                array("006008001000000", "008005000000000"),
                                array("006008002000000", "008004000000000"),
                                array("006008003000000", "008012000000000"),
                                array("006008004000000", "008013000000000"),
                                array("006009000000000", "011000000000000"),
                                array("006010001000000", "012001000000000"),
                                array("006010002000000", "012002000000000"),
                                array("006010003000000", "012003000000000"),
                                array("006010004000000", "012004000000000"),

								array("006000000000000", "000000000000000"),//추가
								array("006001000000000", "010000000000000"),
								array("006008000000000", "008000000000000"),
								array("006010000000000", "012000000000000")


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