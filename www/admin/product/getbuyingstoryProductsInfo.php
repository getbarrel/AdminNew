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
	$compare_admins = array(
				"43026c9d1575e72ee41478fe748d7ebd"=>"cb1f12e8cfa9d23c0afeecb4b242287c",	//와투
				"55c88ebadac901e82b4df864999694cb"=>"a047590323b13ff88b35569a8044d71a",	//바잉스토리
				//"447107172c0b0ee19bd348e747815d77"=>"cd17ca906f1e3582398cd6840ffa42b8",	//트라이앵글스타일
				"4458014f1d022f700751c4d291058be8"=>"0cd2b0c5a148761a519e54aa67dca675",	//그루빔
				//"fb37d15d96c97f5164929965fad8a8d1"=>"4f709a0715f4d9658b9ca5e24649e1b5",	//리멤버클릭
				"f8d4c887d8b0e971e79377ecb87b3ce4"=>"e37972ebcd3e20a1f1cdcad4b5b8f5ee",	//캣보이
				//"4234538b2455a4cdb9bf11809994a37e"=>"26862e04ed23bc053eda2cab8b1ceb66",	//홀리바나나
				//"e7efb503262585743a23ea7c1897c054"=>"5fa0d9252b46d7c49d617dd7347fcfff",	//미스터콧수염
				//"c024e104a18011ade374ce038ec8199c"=>"2d008f9d390a97f959af6fc7d5cd813f",	//샤이니비키
				//"b57343dc19aff18fd8282098d5514860"=>"a74b369c75ed4ec3e5551a218f4e49eb",	//스타일드림
				//"a40f923478f63b475f323118d0bdaa66"=>"163445fdc5888c5a83c32d1e25bd61db",	//스타일에스
				//"94478c9e8812f02ed0371f61061c012f"=>"acd87a0ba2b3c79ef661633af65e21ed",	//르느와르
				"5b7c142b0e14ae8a4c8cab15cc3513e5"=>"4f10747a9c7c41a156b4fd47405f972e",	//레몬그라스
				"5982b2b05f66f524eba104effdf2638f"=>"20dcb697c6761839ae070f22cc0c31bf",	//아싸놀토다
				"515a86d225b13f3ef20e760f1dd998ea"=>"e2167239a0b2fd5e4319d3d9e9a6d56f",	//카리즈마
				"4c674577fe14d55c902c8b6e4af85c40"=>"59bd3cb2835569764800d21c55de0f8c"	//다크캣
				);
	/////////////////////////////////////////////////////////////////////

		$iii = 0;
		$jjj=0;
	foreach($compare_admins as $buying_admin => $goodss_admin){

		$minPid = $_REQUEST["pid"];
		//$minPid = '081291';
		$minPid = '000000'; // 전부
		$pid = $minPid; 

		$c="";
		$c = shell_exec("lynx --dump 'http://buyingstory.com/cron/share.php?admin=$buying_admin&branch=count'");
		$product_count = unserialize(base64_decode($c));
		
		//print_r($product_count);
		//echo $product_count[0][total];
		//exit;
		
		for($limit=0; $limit <= $product_count[0][total]; $limit += 100){

			$limit_str = "limit ".$limit.", 100";
		
			$x="";
			$x = shell_exec("lynx --dump 'http://buyingstory.com/cron/share.php?pid=$minPid&branch=product&admin=$buying_admin&limit=$limit_str'");
			$productInfos = unserialize(base64_decode($x));
			$admininfo[admin_level] = 9;
			$admin = $goodss_admin;
			$goods_desc_copy = 1;


			//echo count($productInfos);
			//print_r($productInfos);
			//exit;

			foreach ($productInfos as $productInfo) {
				echo "update-".$iii."<br>insert-".$jjj;

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
					
					$x = shell_exec("lynx --dump 'http://buyingstory.com/cron/share.php?pid=$co_pid&branch=category'");
					
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

					//$editdate = date("Y-m-d H:i:s");
					
					$etc2 = $productInfo["editdate"]; // buyingstory 상품 수정 날짜와 비교 하여 같으면 패스하기 위해 추가
					$etc10 = $productInfo["regdate"];

					$regdate = "";
					$editdate = "";

					$vieworder = $productInfo["vieworder"];

					//$download_img = $productInfo["download_img"];
					//$download_desc = $productInfo["download_desc"];

						$cids = unserialize(base64_decode($x));
						$c = count($cids);

						$basic="";
						$category = array();
						for($i = 0; $i < $c; $i++){
							//syslog(LOG_INFO, sprintf("before categorycode = %s", $cids[$i]["cid"]));
							$cids[$i]["cid"] = categoryCodeReplace($cids[$i]["cid"]);
							$basic = $cids[$i]["cid"];

							if($basic=='999999999999999'){//품절 카테고리 있을때
								$state ='0';
								$category=="";
								break;
							}else{
								//syslog(LOG_INFO, sprintf("after categorycode = %s", $cids[$i]["cid"]));
								$category[$i] = $basic;
								//syslog(LOG_INFO, sprintf(print_r($category, true)));
							}
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

						$bimg_text = sprintf("http://buyingstory.com/data/dongdosi_data/images/product/".$id_1."/".$id_2."/".$id_3."/".$id_4."/".$id_5."/m_%06s.gif", $productInfo["id"]);
						$img_url_copy = 1;
				
						$basicinfo = $productInfo["basicinfo"];
						$basicinfo = str_replace("<img", "<IMG", $basicinfo);
						$basicinfo = str_replace("/data/dongdosi_data/images/product_detail/", "http://buyingstory.com/data/dongdosi_data/images/product_detail/", $basicinfo);
						$basicinfo = str_replace("\t"," ", $basicinfo);
						$basicinfo = str_replace("'","\'", $basicinfo);


						//최저 판매가
						$xxx = shell_exec("lynx --dump 'http://buyingstory.com/cron/share.php?pid=$co_pid&branch=priceinfo'");
						$displayinfo_price = unserialize(base64_decode($xxx));

						$lowest_price='';
						$str_replace_word = array('"',"=","'",',');
						$lowest_price = trim(str_replace($str_replace_word,"",$displayinfo_price[0][dp_desc]));

						$coprice = 0;
						$sellprice = 0;
						$listprice = 0;
						$coprice = $productInfo["sellprice"];

						if($lowest_price !=''){
							$sellprice = $lowest_price;
							$listprice = $lowest_price;
						}else{
							$sellprice = $coprice * 1.5;
							$listprice = $sellprice;
						}
					
						$xx="";
						$xx = shell_exec("lynx --dump 'http://buyingstory.com/cron/share.php?pid=$co_pid&branch=options'");
						$ioptionsList = unserialize(base64_decode($xx));
				

						$options = array();
						if($ioptionsList != ""){
							$i = 0;
							foreach ($ioptionsList as $ioptions) {
								$option_ix = $ioptions["opn_ix"];
								$x = shell_exec("lynx --dump 'http://buyingstory.com/cron/share.php?pid=$co_pid&branch=option&option_ix=$option_ix'");
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

	}
//syslog(LOG_INFO, $_SERVER['PHP_SELF']."END");
closelog();

function categoryCodeReplace($cid)
{
	// 첫번째 나오는 값이 goodss 뒤의 값이 buyingstory
	$result = "";
        $codeMap = array(

								//스페셜 M (WOMANS)
                                array("000001003000000", "001001001000000"),	//아웃터 - OUTWEAR
                                array("000001001001000", "001001002000000"),	//티셔츠 - TEE&SHIRT
                                array("000001004001000", "001001003000000"),	//원피스 - ONEPIECE
                                array("000001005000000", "001001004000000"),	//스커트/치마 - SKIRT
                                array("000001006000000", "001001005000000"),	//팬츠/바지 - PANTS
                                array("001004000000000", "001001006000000"),	//시계/보석/액세서리 - ACC
                                array("001001000000000", "001001007000000"),	//여성신발/스니커즈 - BAG&SHOES
                                array("000001011000000", "001001008000000"),	//비치웨어/수영복 - BEACH WEAR
								//스페셜 M (MANS)
                                array("000002002000000", "001002001000000"),	//아우터 - OUTWEAR
                                array("000002001001000", "001002002000000"),	//티셔츠 - TEE&SHIRT
                                array("000002007000000", "001002003000000"),	//정장수트 - SUIT
								array("000002003001000", "001002004000000"),	//Men>pants - PANTS
                                array("001003000000000", "001002005000000"),	//남성신발/가방/잡화 - ACC
                                array("001003000000000", "001002006000000"),	//남성신발/가방/잡화 - BAG&SHOES
                                array("000002010000000", "001002007000000"),	//비치웨어/수영복 - SWIMWEAR
								//스페셜 D (WOMANS)
                                array("000001003000000", "002001001000000"),	//아웃터 - OUTWEAR
                                array("000001003005000", "002001001001000"),	//야상 - 점퍼.야상
                                array("000001003004000", "002001001002000"),	//조끼 - 조끼.베스트
                                array("000001003001000", "002001001003000"),	//자켓 - 자켓.코트
                                array("000001008000000", "002001001004000"),	//가디건 - 가디건
                                array("000001009000000", "002001001005000"),	//트레이닝복 - 트레이닝
								array("000001001000000", "002001002000000"),	//티셔츠/탑 - TEE&SHIRT
								array("000001001002000", "002001002001000"),	//니트 - 니트.스웨터
								array("000001002000000", "002001002002000"),	//블라우스/셔츠/남방 - 블라우스.셔츠.남방
								array("000001001001000", "002001002003000"),	//티셔츠 - 반팔티셔츠
								array("000001001001000", "002001002004000"),	//티셔츠 - 긴팔티셔츠
								array("000001004001000", "002001003000000"),	//원피스 - ONEPIECE
								array("000001004001000", "002001003001000"),	//원피스 - 정장원피스
								array("000001004001000", "002001003002000"),	//원피스 - 캐쥬얼원피스
								array("000001005000000", "002001004000000"),	//스커트/치마 - SKIRT
								array("000001005000000", "002001004001000"),	//스커트/치마 - 미니스커트
								array("000001005000000", "002001004002000"),	//스커트/치마 - 롱스커트
								array("000001006000000", "002001005000000"),	//팬츠/바지 - PANTS
								array("000001007000000", "002001005001000"),	//청바지/진 - 청바지.진
								array("001002003000000", "002001005002000"),	//레깅스/스타킹/양말 - 레깅스.스타킹
								array("000001006000000", "002001005003000"),	//팬츠/바지 - 면바지
								array("000001006000000", "002001005004000"),	//팬츠/바지 - 카고바지
								array("000001009000000", "002001005005000"),	//트레이닝복 - 트레이닝
								array("001002000000000", "002001006000000"),	//여성가방/패션잡화 - ACC
								array("001002008000000", "002001006001000"),	//모자 - 모자
								array("001004000000000", "002001006002000"),	//시계/보석/액세서리 - 악세사리
								array("001002005000000", "002001006003000"),	//머플러/스카프/숄 - 스카프
								array("001004004000000", "002001006004000"),	//패션시계 - 시계
								array("001002002000000", "002001006005000"),	//벨트 - 벨트
								array("001002001000000", "002001007002000"),	//패션가방/지갑 - 지갑/기타
								array("001002004000000", "002001007009000"),	//캐주얼가방 - 숄더백
								array("001002001000000", "002001007010000"),	//패션가방/지갑 - 크로스백
								array("001002001000000", "002001007011000"),	//패션가방/지갑 - 백팩
								array("001002004000000", "002001007012000"),	//캐주얼가방 - 클러치/미니백
								array("001002006000000", "002001007013000"),	//여행용가방/소품 - 여행가방
								array("001002004000000", "002001007014000"),	//캐주얼가방 - 토트백
								array("000001011000000", "002001008000000"),	//비치웨어/수영복 - BIKINI
								array("001001000000000", "002001009000000"),	//여성신발/스니커즈 - SHOES
								array("001001001000000", "002001009001000"),	//하이힐/펌프스 - 힐/펌프스/웨지
								array("001001002000000", "002001009002000"),	//단화/플랫슈즈 - 플랫/로퍼
								array("001001009000000", "002001009003000"),	//샌들/조리/젤리슈즈 - 샌들/조리
								array("001001008000000", "002001009004000"),	//부츠/레인부츠 - 워커/부츠
								//스페셜 D (MANS)
								array("000002002000000", "002002001000000"),	//아우터 - OUTWEAR
								array("000002002003000", "002002001001000"),	//점퍼 - 점퍼.야상
								array("000002002004000", "002002001002000"),	//조끼 - 조끼.베스트
								array("000002005000000", "002002001004000"),	//가디건 - 가디건
								array("000002006000000", "002002001005000"),	//트레이닝복 - 트레이닝
								array("000002001000000", "002002002000000"),	//티셔츠/탑 - TEE&SHIRT
								array("000002001002000", "002002002001000"),	//니트 - 니트.스웨터
								array("000002009000000", "002002002002000"),	//셔츠/남방 - 셔츠.남방
								array("000002001001000", "002002002003000"),	//티셔츠 - 반팔티셔츠
								array("000002001001000", "002002002004000"),	//티셔츠 - 긴팔티셔츠
								array("000002007000000", "002002003001000"),	//정장수트 - 자켓
								array("000002007000000", "002002003002000"),	//정장수트 - 베스트
								array("000002008000000", "002002003003000"),	//정장바지/팬츠 - 팬츠
								array("000002003000000", "002002004000000"),	//팬츠/바지 - PANTS
								array("000002004000000", "002002004001000"),	//청바지/진 - 청바지.진
								array("000002003001000", "002002004002000"),	//Men>pants - 면바지
								array("000002003001000", "002002004003000"),	//Men>pants - 카고바지
								array("000002006000000", "002002004004000"),	//트레이닝복 - 트레이닝
								array("001003000000000", "002002005000000"),	//남성신발/가방/잡화 - ACC
								array("001003005000000", "002002005001000"),	//지갑/벨트 - 벨트
								array("001003008000000", "002002005002000"),	//넥타이/스카프/머플러 - 넥타이.보타이
								array("001004004000000", "002002005003000"),	//패션시계 - 시계
								array("001003000000000", "002002006000000"),	//남성신발/가방/잡화 - BAG
								array("001003003000000", "002002006001000"),	//가방 - 크로스백
								array("001003003000000", "002002006002000"),	//가방 - 백팩
								array("000002010000000", "002002007000000"),	//비치웨어/수영복 - SWIMWEAR
								array("001003000000000", "002002008000000"),	//남성신발/가방/잡화 - SHOES
								array("001003006000000", "002002008001000"),	//구두/정장화 - 구두
								array("001003002000000", "002002008002000"),	//캐주얼화 - 수제화
								array("001003001000000", "002002008003000"),	//운동화/스니커즈 - 스니커즈
								array("001003002000000", "002002008004000"),	//캐주얼화 - 로퍼
								array("001003002000000", "002002008005000"),	//캐주얼화 - 워커/부츠
								array("001003001000000", "002002008006000"),	//운동화/스니커즈 - 조리/샌들
								array("001003002000000", "002002008005000"),	//
								array("001004000000000", "002003002000000"),	//시계/보석/액세서리 - 훼드라
								array("001000000000000", "002003003000000"),	//잡화뷰티 - 슈앤걸	(임의)
								array("001000000000000", "002003004000000"),	//잡화뷰티 - 빌라슈즈	(임의)
								array("000000000000000", "002003005000000"),	//의류/패션 - 쿠츠샵	(임의)
								array("000000000000000", "002003006000000"),	//의류/패션 - 영앤비	(임의)
								array("000000000000000", "002003007000000"),	//의류/패션 - 비키도매	(임의)
								array("000000000000000", "002003008000000"),	//의류/패션 - 타브가	(임의)
								array("001000000000000", "002003009000000"),	//잡화뷰티 - 성신슈즈	(임의)
								array("999999999999999", "002004000000000")	//품절리스트
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
