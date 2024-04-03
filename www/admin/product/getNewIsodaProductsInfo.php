<?php
define_syslog_variables();
openlog("phplog", LOG_PID , LOG_LOCAL0);

	include_once("../../class/database.class");
//syslog(LOG_INFO, $_SERVER['PHP_SELF']."START");



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


	///////"buyingstory_admin_code"=>"goodss_admin_code"///////
	/*
	$compare_admins = array(

				"43026c9d1575e72ee41478fe748d7ebd"=>"cb1f12e8cfa9d23c0afeecb4b242287c",	//와투
				"55c88ebadac901e82b4df864999694cb"=>"a047590323b13ff88b35569a8044d71a",	//바잉스토리
				"447107172c0b0ee19bd348e747815d77"=>"cd17ca906f1e3582398cd6840ffa42b8",	//트라이앵글스타일
				"4458014f1d022f700751c4d291058be8"=>"0cd2b0c5a148761a519e54aa67dca675",	//그루빔
				"fb37d15d96c97f5164929965fad8a8d1"=>"4f709a0715f4d9658b9ca5e24649e1b5",	//리멤버클릭
				"f8d4c887d8b0e971e79377ecb87b3ce4"=>"e37972ebcd3e20a1f1cdcad4b5b8f5ee",	//캣보이
				"4234538b2455a4cdb9bf11809994a37e"=>"26862e04ed23bc053eda2cab8b1ceb66",	//홀리바나나
				"e7efb503262585743a23ea7c1897c054"=>"5fa0d9252b46d7c49d617dd7347fcfff",	//미스터콧수염
				"c024e104a18011ade374ce038ec8199c"=>"2d008f9d390a97f959af6fc7d5cd813f",	//샤이니비키
				"b57343dc19aff18fd8282098d5514860"=>"a74b369c75ed4ec3e5551a218f4e49eb",	//스타일드림
				"a40f923478f63b475f323118d0bdaa66"=>"163445fdc5888c5a83c32d1e25bd61db",	//스타일에스
				"94478c9e8812f02ed0371f61061c012f"=>"acd87a0ba2b3c79ef661633af65e21ed",	//르느와르
				"5b7c142b0e14ae8a4c8cab15cc3513e5"=>"4f10747a9c7c41a156b4fd47405f972e",	//레몬그라스
				"5982b2b05f66f524eba104effdf2638f"=>"20dcb697c6761839ae070f22cc0c31bf",	//아싸놀토다
				"515a86d225b13f3ef20e760f1dd998ea"=>"e2167239a0b2fd5e4319d3d9e9a6d56f",	//카리즈마
				"4c674577fe14d55c902c8b6e4af85c40"=>"59bd3cb2835569764800d21c55de0f8c"	//다크캣

				);
	*/
	/////////////////////////////////////////////////////////////////////


	//foreach($compare_admins as $buying_admin => $goodss_admin){

		$minPid = $_REQUEST["pid"];
		//$minPid = '081291';
		$minPid = '000000'; // 전부
		$pid = $minPid;

		$c="";
		//$c = shell_exec("lynx --dump 'http://dev.forbiz.co.kr/cron/share.php?admin=$buying_admin&branch=count'");
		$c = shell_exec("lynx --dump 'http://dev.forbiz.co.kr/cron/share.php?branch=count'");
		$product_count = unserialize(base64_decode($c));

		//print_r($product_count);
		//echo $product_count[0][total];
		//exit;

		for($limit=0; $limit <= $product_count[0][total]; $limit += 100){

			$limit_str = "limit ".$limit.", 100";

			$x="";
			//$x = shell_exec("lynx --dump 'http://dev.forbiz.co.kr/cron/share.php?pid=$minPid&branch=product&admin=$buying_admin&limit=$limit_str' ");
			$x = shell_exec("lynx --dump 'http://dev.forbiz.co.kr/cron/share.php?pid=$minPid&branch=product&limit=$limit_str' ");
			$productInfos = unserialize(base64_decode($x));
			$admininfo[admin_level] = 9;
			//$admin = $goodss_admin;
			$goods_desc_copy = 1;
			$iii = 0;
			$jjj=0;

			//echo count($productInfos);
			//print_r($productInfos);
			//exit;

			foreach ($productInfos as $productInfo) {
				//echo "update-".$iii."<br>insert-".$jjj;

				$db = new Database;
				$sql = "SELECT id, editdate,etc2 from shop_product where co_pid = '" . $productInfo["id"] . "' ";
				$db->query($sql);
				$result = $db->fetchall();

				//print_r($productInfo);
				//exit;

				$act = "";

				if(count($result) == 0){
					$act = 'insert';
					$jjj++;
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


					///////////////////////// 디폴트값 ///////////////////////////
					$delivery_company = "MI";	// 배송업체 : 기본값 셋팅
					$stock = "999999";		 // 재고 : 기본값 셋팅
					$safestock = "10";		// 안전재고 : 기본값 셋팅
					$stock_use_yn = "N";		// 재고사용여부 : 기본값 셋팅
					$surtax_yorn = "N";		// 면세여부 : 기본값 셋팅
					$product_type = "0";	//일반상품
					//////////////////////////////////////////////////////////////
					//syslog(LOG_INFO, $productInfo["id"] . "    " . $act . "   " . $productInfo["pname"]);

					$pname = $productInfo["pname"];
					$pname = str_replace("\t"," ", $pname);
					$pname = str_replace("'","\'", $pname);

					$pcode =  $productInfo["pcode"];
					$pcode = str_replace("\t"," ", $pcode);
					$pcode = str_replace("'","\'", $pcode);

					$search_keyword =  $productInfo["search_keyword"];
					$search_keyword = str_replace("\t"," ", $search_keyword);
					$search_keyword = str_replace("'","\'", $search_keyword);

					$paper_pname =  $productInfo["paper_pname"];
					$paper_pname = str_replace("\t"," ", $paper_pname);
					$paper_pname = str_replace("'","\'", $paper_pname);

					$etc2 = $productInfo["editdate"];
					$etc10 = $productInfo["regdate"];

					$regdate = "";
					$editdate = "";

					$vieworder = $productInfo["vieworder"];

					//$download_img = $productInfo["download_img"];
					//$download_desc = $productInfo["download_desc"];

					$x = shell_exec("lynx --dump 'http://dev.forbiz.co.kr/cron/share.php?pid=$co_pid&branch=category'");
					$cids = unserialize(base64_decode($x));
					$c = count($cids);

					$basic="";
					$admin = '';
					$category = array();
					for($i = 0; $i < $c; $i++){
						//syslog(LOG_INFO, sprintf("before categorycode = %s", $cids[$i]["cid"]));
						$cids[$i]["cid"] = categoryCodeReplace($cids[$i]["cid"]);
						$basic = $cids[$i]["cid"];
						$substr_basic = substr($basic,0,6);
						
						switch($substr_basic){
							case 007001 : //아이소다_여성의류
								$admin = 'b41fa67b40bea32c4dbe34d6c8c1bce7';
							break;
							case 007002 : //아이소다_남성의류
								$admin = '2a4a067ec837426a026bf01a35b26dfd';
							break;
							case 007003 : //아이소다_패션/뷰티
								$admin = '056affdd129b2a5b109111fc2fda30a3';
							break;
							case 007006 : //아이소다_잉글랜드
								$admin = 'a1fb5ec117367ea434a123374303a968';	
							break;
							case 007007 : //아이소다_럭스걸
								$admin = '4b9a87d6fa1aed4f74da986d43c24f6c';
							break;
							//case 007005 : //아이소다_패션디지털
							//	$admin = '47924276a495bfdba12afa6a90a4d541';
							//break;
							default:
								$admin = '';
						}

						if($basic=='999999999999999'){//품절 카테고리 있을때
							$state = '0';
							$category=="";
							break;
						}else{
							//syslog(LOG_INFO, sprintf("after categorycode = %s", $cids[$i]["cid"]));
							$category[$i] = $basic;
							//syslog(LOG_INFO, sprintf(print_r($category, true)));
						}
					}
					
					if($admin == ''){
						//syslog(LOG_INFO, 'PASS : id ->'.$productInfo["id"]);
						continue;
					}

					$id_1="";
					$id_2="";
					$id_3="";
					$id_4="";
					$id_5="";

					$id_1 = substr($productInfo["id"],0,2);
					$id_2 = substr($productInfo["id"],2,2);
					$id_3 = substr($productInfo["id"],4,2);
					$id_4 = substr($productInfo["id"],6,2);
					$id_5 = substr($productInfo["id"],8,2);

					$bimg_text = sprintf("http://dev.forbiz.co.kr/data/basic/images/product/".$id_1."/".$id_2."/".$id_3."/".$id_4."/".$id_5."/m_%06s.gif", $productInfo["id"]);
					$img_url_copy = 1;

					$basicinfo = $productInfo["basicinfo"];
					$basicinfo = str_replace("<img", "<IMG", $basicinfo);
					$basicinfo = str_replace("/data/basic/images/product_detail/", "http://dev.forbiz.co.kr/data/basic/images/product_detail/", $basicinfo);
					$basicinfo = str_replace("\t"," ", $basicinfo);
					$basicinfo = str_replace("'","\'", $basicinfo);


					//buyingstory 는 shop_priceinfo 테이블 안씀
					//$xxx = shell_exec("lynx --dump 'http://dev.forbiz.co.kr/cron/share.php?pid=$co_pid&branch=priceinfo'");
					//$priceinfo = unserialize(base64_decode($xxx));

					$coprice = 0;
					$sellprice = 0;
					$listprice = 0;
					$coprice   = $productInfo["wholesale_sellprice"] * 1.1;
					$listprice = $productInfo["sellprice"];
					$sellprice = $productInfo["sellprice"];

					$xx="";
					$xx = shell_exec("lynx --dump 'http://dev.forbiz.co.kr/cron/share.php?pid=$co_pid&branch=options'");
					$ioptionsList = unserialize(base64_decode($xx));


					$options = array();
					if($ioptionsList != ""){
						$i = 0;
						foreach ($ioptionsList as $ioptions) {
							$option_ix = $ioptions["opn_ix"];
							$x = shell_exec("lynx --dump 'http://dev.forbiz.co.kr/cron/share.php?pid=$co_pid&branch=option&option_ix=$option_ix'");
							$ioptionDetailList = unserialize(base64_decode($x));



							$options[$i][option_type] = $ioptions["option_type"];
							$options[$i][option_kind] = $ioptions["option_kind"];
							$options[$i][option_use] = $ioptions["option_use"];
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
					$bs_act ='_act';
					$mmode = "shell";
					include "goods_input.act.php";
				}
			//if($iii > 10){
			//	exit;
			//}
			//$iii++;
			}
			$productInfos="";
			$ioptionsList="";
			$ioptionDetailList="";

		}

		$sql = "update shop_product set state='0' where date_format(editdate, '%Y%m%d') <> '".date("Ymd")."' and date_format(regdate, '%Y%m%d') <> '".date("Ymd")."' and admin='".$admin."'  ";
		$db->query($sql);

	//}

//syslog(LOG_INFO, $_SERVER['PHP_SELF']."END");



closelog();

function categoryCodeReplace($cid)
{
	// 첫번째 나오는 값이 goodss 뒤의 값이 Isoda
	$result = "";
        $codeMap = array(

								//아이소다_여성의류 b41fa67b40bea32c4dbe34d6c8c1bce7
								array("007001000000000", "001000000000000"),	//WOMEN
								array("007001001000000", "001001000000000"),	//Top/T-shirts
								array("007001001001000", "001001001000000"),	//티셔츠
								array("007001001002000", "001001002000000"),	//니트
								array("007001001003000", "001001003000000"),	//민소매
								array("007001001004000", "001001004000000"),	//후드티
								array("007001002000000", "001002000000000"),	//Blouse/Shirts
								array("007001002001000", "001002001000000"),	//블라우스
								array("007001002002000", "001002002000000"),	//셔츠
								array("007001003000000", "001003000000000"),	//Outer
								array("007001003001000", "001003001000000"),	//자켓
								array("007001003002000", "001003002000000"),	//코트
								array("007001003003000", "001003003000000"),	//점퍼
								array("007001003004000", "001003004000000"),	//야상
								array("007001003005000", "001003005000000"),	//조끼
								array("007001004000000", "001004000000000"),	//Dress
								array("007001004001000", "001004001000000"),	//원피스
								array("007001005000000", "001005000000000"),	//Skirt
								array("007001005001000", "001005001000000"),	//롱스커트
								array("007001005002000", "001005002000000"),	//반/숏스커트
								array("007001006000000", "001006000000000"),	//Pants
								array("007001006001000", "001006001000000"),	//긴바지
								array("007001006002000", "001006002000000"),	//반바지
								array("007001006003000", "001006003000000"),	//청바지
								array("007001007000000", "001007000000000"),	//Cardigan
								array("007001008000000", "001008000000000"),	//Training wear
								array("007001009000000", "001009000000000"),	//Season wear
								//아이소다_남성의류 2a4a067ec837426a026bf01a35b26dfd
								array("007002000000000", "002000000000000"),	//MEN
								array("007002001000000", "002001000000000"),	//Top/T-shirts
								array("007002001001000", "002001001000000"),	//티셔츠
								array("007002001002000", "002001002000000"),	//니트
								array("007002001003000", "002001003000000"),	//민소매
								array("007002001004000", "002001004000000"),	//후드티
								array("007002002000000", "002002000000000"),	//Shirts
								array("007002002001000", "002002001000000"),	//셔츠
								array("007002002002000", "002002002000000"),	//난방
								array("007002003000000", "002003000000000"),	//Outer
								array("007002003001000", "002003001000000"),	//자켓
								array("007002003002000", "002003002000000"),	//코트
								array("007002003003000", "002003003000000"),	//점퍼
								array("007002003004000", "002003004000000"),	//야상
								array("007002003005000", "002003005000000"),	//조끼
								array("007002004000000", "002004000000000"),	//Pants
								array("007002004001000", "002004001000000"),	//긴바지
								array("007002004002000", "002004002000000"),	//반바지
								array("007002004003000", "002004003000000"),	//청바지
								array("007002005000000", "002005000000000"),	//Cardigan
								array("007002006000000", "002006000000000"),	//정장/슈트
								array("007002006001000", "002006001000000"),	//마이
								array("007002006002000", "002006002000000"),	//정장바지
								array("007002007000000", "002007000000000"),	//Training wear
								array("007002008000000", "002008000000000"),	//Season wear
								//아이소다_패션/뷰티 056affdd129b2a5b109111fc2fda30a3
								array("007003000000000", "003000000000000"),	//ACC/Beauty
								array("007003001000000", "003001000000000"),	//여성신발(WOMEN SHOES)
								array("007003001001000", "003001001000000"),	//하이힐(HEEL)
								array("007003001002000", "003001002000000"),	//단화(FLAT)
								array("007003001003000", "003001003000000"),	//운동화(RUNNING SHOES)
								array("007003001004000", "003001004000000"),	//웨지힐(WEDGE HEEL)
								array("007003001005000", "003001005000000"),	//부츠(BOOTS)
								array("007003001006000", "003001007000000"),	//실내화(SLIPPERS)
								array("007003002000000", "003002000000000"),	//여성가방/잡화
								array("007003002001000", "003002001000000"),	//가방
								array("007003002002000", "003002002000000"),	//벨트
								array("007003002003000", "003002003000000"),	//레깅스/스타킹/양말
								array("007003002004000", "003002004000000"),	//머플러/스카프
								array("007003002005000", "003002005000000"),	//모자
								array("007003002006000", "003002006000000"),	//헤어악세사리
								array("007003002007000", "003002007000000"),	//장갑
								array("007003002008000", "003002008000000"),	//기타
								array("007003003000000", "003003000000000"),	//남성신발/가방/잡화
								array("007003003001000", "003003001000000"),	//운동화/스니커즈
								array("007003003002000", "003003002000000"),	//구두
								array("007003003003000", "003003003000000"),	//가방
								array("007003003004000", "003003004000000"),	//모자
								array("007003003005000", "003003005000000"),	//벨트
								array("007003003006000", "003003006000000"),	//넥타이/스카프
								array("007003003007000", "003003007000000"),	//양말/깔창
								array("007003003008000", "003003008000000"),	//기타
								array("007003004000000", "003004000000000"),	//시계/악세사리
								array("007003004001000", "003004001000000"),	//귀걸이
								array("007003004002000", "003004002000000"),	//목걸이
								array("007003004003000", "003004003000000"),	//반지
								array("007003004004000", "003004004000000"),	//패션시계
								array("007003004005000", "003004005000000"),	//팔찌/발찌
								array("007003004006000", "003004006000000"),	//브로치
								array("007003005000000", "003005000000000"),	//향수/헤어/바디/미용
								array("007003006000000", "003006000000000"),	//스킨케어/메이크업
								//아이소다_잉글랜드 a1fb5ec117367ea434a123374303a968
								array("007006000000000", "004001000000000"),	//잉글랜드(여성)
								array("007006001000000", "004001001000000"),	//탑/블라우스
								array("007006002000000", "004001002000000"),	//아우터/가디건
								array("007006003000000", "004001003000000"),	//드레스/원피스
								array("007006004000000", "004001004000000"),	//스커트
								array("007006005000000", "004001005000000"),	//팬츠
								//아이소다_럭스걸 4b9a87d6fa1aed4f74da986d43c24f6c
								array("007007000000000", "004002000000000"),	//럭스걸(여성)
								array("007007001000000", "004002001000000"),	//DRESS
								array("007007002000000", "004002002000000"),	//OUTER
								array("007007003000000", "004002003000000"),	//KNIT&TOP
								array("007007004000000", "004002004000000"),	//BLOSE&SHIRTS
								array("007007005000000", "004002005000000"),	//SKIRT
								array("007007006000000", "004002006000000"),	//PANTS
								array("007007007000000", "004002007000000"),	//JEWERLY&ACC
								array("007007008000000", "004002008000000"),	//BAG
								array("007007009000000", "004002009000000")	//SHOES
								/*
								//아이소다_패션디지털 47924276a495bfdba12afa6a90a4d541
								array("007005000000000", "005000000000000"),	//패션디지털(DIGITAL ITEM)
								array("007005001000000", "005001000000000"),	//젤리케이스
								array("007005002000000", "005002000000000"),	//다이어리케이스
								array("007005003000000", "005003000000000"),	//플립커버케이스
								array("007005004000000", "005004000000000"),	//가죽케이스
								array("007005005000000", "005005000000000"),	//스패셜케이스
								array("007005006000000", "005006000000000"),	//보호필름
								array("007005010000000", "005007000000000"),	//충전기/젠더/케이블
								array("007005007000000", "005008000000000"),	//아이폰/플루투스
								array("007005008000000", "005009000000000"),	//브랜드존
								array("007005009000000", "005010000000000")	//기타악세사리
								*/
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

