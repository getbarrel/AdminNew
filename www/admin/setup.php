<?
include("./class/layout.class");

//print_r($admin_config);


//if(($_SERVER["HTTP_HOST"] == "deploy.mallstory.com" || $_SERVER["HTTP_HOST"] == "openmarket.mallstory.com") && $admin_config[mall_data_root] != "" && ($admin_config[mall_data_root] == "/data/deploy" || $admin_config[mall_data_root] == "/data/openmarket")){
if(($_SERVER["HTTP_HOST"] == "100mc.forbiz.co.kr") && $admin_config[mall_data_root] != "" && ($admin_config[mall_data_root] == "/data/100mc_data/")){
//echo "디플로이 합니다.";
//exit;
		$debug = false;

		$db = new Database();
		$change = new Database();

		// 테이블 중에 유지해야 하는 테이블을 제외 하고는 초기화 한다.
		$sql = "show tables;";
		$db->query($sql);

		echo "<table >";
		echo "<tr><td>";
		$manage_tables[] = "admin_auth_templet";
		$manage_tables[] = "admin_auth_templet_detail";
		$manage_tables[] = "admin_dic";
		$manage_tables[] = "admin_language";
		$manage_tables[] = "admin_menus";
		$manage_tables[] = "admin_menu_div";

		//게시판 관련
		$manage_tables[] = "bbs_manage_config";
		$manage_tables[] = "bbs_manage_div";
		$manage_tables[] = "bbs_manage_status";
		$manage_tables[] = "bbs_spam_config";
		$manage_tables[] = "bbs_group";

		//로그분석 관련 관리 테이블
		$manage_tables[] = "logstory_time";
		$manage_tables[] = "logstory_referer_categoryinfo";
		$manage_tables[] = "logstory_pageinfo";

		//서비스 관련 테이블
		$manage_tables[] = "service_ing";
		$manage_tables[] = "service_mall_ing";
		$manage_tables[] = "service_division";
		
		$manage_tables[] = "service_mall_order";
		$manage_tables[] = "service_mall_order_detail";
		$manage_tables[] = "service_mall_order_memo";
		$manage_tables[] = "service_mall_order_status";

		$manage_tables[] = "service_product";
		$manage_tables[] = "service_product_options";
		$manage_tables[] = "service_product_options_detail";
		$manage_tables[] = "service_product_relation";


		$manage_tables[] = "shop_payment_config";

		$manage_tables[] = "shop_bannerinfo";
		$manage_tables[] = "shop_banner_div";
		$manage_tables[] = "shop_category_info"; // 1depth 라도 기본세팅해준다. 또는 쇼핑몰 운영 카테고리에 따라서 기본 세팅을 달리해준다

		// 분류별 프로모션 관리 관련 테이블 ..
		$manage_tables[] = "shop_category_main_category_relation";



		$manage_tables[] = "shop_code"; // 각종 코드 관련 정보들
		$manage_tables[] = "shop_design";

		//$manage_tables[] = "shop_groupinfo";  // 기본세팅정보 그대로 살리기  --> 삭제 하고 다시 넣어주는걸로 처리함
		$manage_tables[] = "shop_html_library"; // 내용을 교체 필요
		$manage_tables[] = "shop_icon"; // 기본 아이콘 세팅 필요
		$manage_tables[] = "shop_image_resizeinfo";
		$manage_tables[] = "shop_join_info";  // 회원가입 설정 정보
		$manage_tables[] = "shop_layout_info";

		// 구매대행 관련 테이블
		$manage_tables[] = "shop_buyingservice_currencytype_info"; // 환율타입정보
		$manage_tables[] = "shop_buyingservice_url_info"; // 즐겨찾기 테이블
		$manage_tables[] = "shop_buyingservice_site"; // 구매대행 사이트 정보
		$manage_tables[] = "shop_buyingservice_info"; // 구매대행 사이트 정보

		// 메인 상품 관리 관련 테이블
		$manage_tables[] = "shop_main_category_relation";
		$manage_tables[] = "shop_main_product_group";
		$manage_tables[] = "shop_main_product_relation";
		$manage_tables[] = "shop_manage_flash";
		$manage_tables[] = "shop_manage_flash_detail";

		//상품데이 삭제시는 주석처리
		
		$manage_tables[] = "shop_product";
		$manage_tables[] = "shop_product_relation";
		$manage_tables[] = "shop_product_options";
		$manage_tables[] = "shop_product_options_detail";
		$manage_tables[] = "shop_relation_product";
		

		// 프로모션 상품관리 관련 테이블
		$manage_tables[] = "shop_display_templetinfo";
		$manage_tables[] = "shop_promotion_div";
		$manage_tables[] = "shop_promotion_goods";
		$manage_tables[] = "shop_promotion_goods_relation";
		


		$manage_tables[] = "shop_zip";
		$manage_tables[] = "shop_zipnew";
		$manage_tables[] = "shop_shopinfo";

		$manage_tables[] = "sns_category_info"; 
		$manage_tables[] = "sns_image_resizeinfo";



		$manage_tables[] = "shop_mailsend_config";



		$manage_tables[] = "inventory_delivery_type";
		$manage_tables[] = "inventory_customer_info";
		//$manage_tables[] = "inventory_place_info";

		//재고관련 테이블 삭제시 주석 처리;
		$manage_tables[] = "inventory_category_info";	
		$manage_tables[] = "inventory_company_detail";	
		$manage_tables[] = "inventory_company_info";	
		$manage_tables[] = "inventory_config";	
		$manage_tables[] = "inventory_customer_info";	
		$manage_tables[] = "inventory_goods";	
		$manage_tables[] = "inventory_goods_basic_place";	
		$manage_tables[] = "inventory_goods_item";	
		$manage_tables[] = "inventory_goods_multi_price";	
		$manage_tables[] = "inventory_goods_safestock";
		$manage_tables[] = "inventory_goods_unit";	
		$manage_tables[] = "inventory_history";	
		$manage_tables[] = "inventory_history_detail";	
		$manage_tables[] = "inventory_info_productorder";	
		$manage_tables[] = "inventory_order";	
		$manage_tables[] = "inventory_order_detail";	
		$manage_tables[] = "inventory_order_detail_tmp";	
		$manage_tables[] = "inventory_place_info";	
		$manage_tables[] = "inventory_place_section";	
		$manage_tables[] = "inventory_product_stockinfo";	
		$manage_tables[] = "inventory_product_stockinfo_bydate";	
		$manage_tables[] = "inventory_type";	
		$manage_tables[] = "inventory_warehouse_move";	
		$manage_tables[] = "inventory_warehouse_move_detail";


		$manage_tables[] = "shop_level";

		$manage_tables[] = "shop_mandatory_detail";
		$manage_tables[] = "shop_mandatory_info";

		//$manage_tables[] = "common_member_detail";
		//$manage_tables[] = "common_company_detail";
		//$manage_tables[] = "common_seller_detail";
		//$manage_tables[] = "common_seller_delivery";
		$manage_tables[] = "bbs_manage_div";
		$manage_tables[] = "shop_companyinfo"; // 쇼핑몰 운영업체 정보, 배송 정책 정보 --> 다른테이블들이랑 혼재되어 사용되고 있음 정리필요 *******************
		$manage_tables[] = "diquest";	



		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);	
				
				if(!in_array($db->dt[0],$manage_tables)){
					//substr($db->dt[0],0,10) == "shop_" ){
					
					//|| substr($db->dt[0],0,4) == "for_" || $db->dt[0] == "referer_categoryinfo"
					//$change_tablename = str_replace("forbiz_","logstory_",$db->dt[0]);
					//$change_tablename = str_replace("for_","logstory_",$change_tablename);
					//$change_tablename = str_replace("referer_categoryinfo","".TBL_LOGSTORY_REFERER_CATEGORYINFO."",$change_tablename);
					//$change_tablename = str_replace("shop_","shop_",$db->dt[0]);
					if($db->dt[0] != "view_goods_saleprice"){
						$sql = "TRUNCATE TABLE ".$db->dt[0]." ";
						if($debug){
							echo $sql."<br>";
						}else{
							echo $sql."<br>";
							$change->query($sql);
						}
					}
					//
					//echo "<a href='table_desc.php?table=".$db->dt[0]."'>".$db->dt[0]."</a><br>";	
				}
			

		}

					// 그룹정보를 넣는 부분
					//$sql = "INSERT INTO `shop_groupinfo` (`gp_ix`, `gp_name`, `organization_img`, `sale_rate`, `gp_level`, `disp`, `regdate`) VALUES(1, '일반회원', '', '0', 9, '1', NOW()) ";
					//insert into shop_groupinfo(gp_ix,gp_name,organization_img,sale_rate,gp_level,disp,basic,regdate) values('$gp_ix','$gp_name','$organization_img','$sale_rate','$gp_level','$disp','$basic','$regdate')
					$sql = "INSERT INTO shop_groupinfo (gp_ix, gp_name, organization_img, sale_rate, gp_level, disp, basic, regdate) 
							VALUES
							(1, '일반회원', '', '0', 9, '0', 'Y', '2008-01-09 17:07:10'),
							(2, '회원등급8', '', '0', 8, '0', 'Y', '2011-04-18 09:52:36'),
							(3, '회원등급7', '', '0', 7, '0', 'Y', '2012-01-05 11:46:58'),
							(4, '회원등급6', '', '0', 6, '0', 'Y', '2011-04-07 11:43:55'),
							(5, '회원등급5', '', '0', 5, '0', 'Y', '2009-07-07 18:06:54'),
							(6, '회원등급4', '', '0', 4, '0', 'Y', '2012-01-05 11:47:35'),
							(7, '회원등급3', '', '0', 3, '0', 'Y', '2010-02-22 13:31:32'),
							(8, '회원등급2', '', '0', 2, '0', 'Y', '2009-12-28 12:44:14'),
							(9, '회원등급1', '', '0', 1, '0', 'Y', '2010-02-23 13:28:18') ";


					if($debug){
						echo $sql;
					}else{
						$db->query($sql);
					}
					

					// 업체 키 
					$company_id  = md5(uniqid(rand()));

					// 관리자 회원정보 입력
					$code  = md5(uniqid(rand()));					
					$language = "korea";
					
					//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					/////																																																									/////
					/////																																																									/////
					/////																			시스템 사용자 생성 forbiz 계정 생성						    																			/////
					/////																																																									/////
					/////																																																									/////
					//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

					//auth : 3 --> 마스터 사용자
					//auth : 1 --> 시스템 관리자
					$sql = "INSERT INTO ".TBL_COMMON_USER."
							(code, id, pw, mem_type, language, company_id, date, visit, last, ip, authorized , auth)
							VALUES
							('$code','forbiz','".hash("sha256", 'shin0606')."','A','".$language."','".$company_id."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."','Y','1')";

					if($debug){
						echo $sql;
					}else{
						$db->query($sql);
					}

					$name = "관리자";
					$com_name = "기본업체";
					$shop_name = "기본상점";
					$gp_ix = 1;

					$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
									(code, name, mail, tel, pcs, date, recom_id,   gp_ix)
									VALUES
									('$code',HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),NOW(),'".$admininfo[charger_id]."','$gp_ix')";

					if($debug){
						echo $sql;
					}else{
						$db->query($sql);
					}

					//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					/////																																																									/////
					/////																																																									/////
					/////																			기본 사용자 생성 admin 계정 생성						    																			/////
					/////																																																									/////
					/////																																																									/////
					//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


					// 관리자 회원정보 입력
					$code  = md5(uniqid(rand()));					
					$language = "korea";

					$sql = "INSERT INTO ".TBL_COMMON_USER."
							(code, id, pw, mem_type, language, company_id, date, visit, last, ip, authorized , auth)
							VALUES
							('$code','admin','".hash("sha256", '1234')."','A','".$language."','".$company_id."',NOW(),'0',NOW(),'".$_SERVER["REMOTE_ADDR"]."','Y','3')";

					if($debug){
						echo $sql;
					}else{
						$db->query($sql);
					}


					$name = "관리자";
					$com_name = "기본업체";
					$shop_name = "기본상점";
					$gp_ix = 1;

					$sql = "INSERT INTO ".TBL_COMMON_MEMBER_DETAIL."
									(code, name, mail, tel, pcs, date, recom_id,   gp_ix)
									VALUES
									('$code',HEX(AES_ENCRYPT('$name','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$mail','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$tel','".$db->ase_encrypt_key."')),HEX(AES_ENCRYPT('$pcs','".$db->ase_encrypt_key."')),NOW(),'".$admininfo[charger_id]."','$gp_ix')";

					if($debug){
						echo $sql;
					}else{
						$db->query($sql);
					}

					$sql = "insert into  
							common_company_detail SET
							com_name='$com_name', 
							com_div='P', 
							com_type='A', 
							company_id='$company_id', 
							seller_auth ='Y'
						
						"; // 이름에 대한 수정을 없앰 kbk
					
					if($debug){
						echo $sql;
					}else{
						$db->query($sql);
					}

					$sql = "insert into common_seller_detail set 
							company_id='$company_id',
							shop_name='$shop_name',
							shop_desc='$shop_desc',
							homepage='$homepage'";

					if($debug){
						echo $sql;
					}else{
						$db->query($sql);
					}
					$delivery_company = 1;
					$delivery_policy = 1;
					$delivery_basic_policy = "2";
					$delivery_price = 3000;
					$delivery_freeprice = 30000;
					$delivery_free_policy = 1;
					$delivery_product_policy = 1;

					$sql = "insert into common_seller_delivery set
						company_id='$company_id',
						commission='$commission',
						delivery_company='$delivery_company',
						delivery_policy='$delivery_policy',
						delivery_basic_policy='$delivery_basic_policy',
						/*delivery_price='$delivery_price',*/
						/*delivery_freeprice='$delivery_freeprice',*/
						/*delivery_free_policy='$delivery_free_policy',*/
						delivery_product_policy='$delivery_product_policy'
						 ";

				

					if($debug){
						echo $sql;
					}else{
						$db->query($sql);
					}


					$sql = "update shop_product set 
							admin='$company_id' ";

					if($debug){
						echo $sql;
					}else{
						$db->query($sql);
					}

					// shopinfo 테이블에 사이트 정보 
					/*
					$sql = "delete from inventory_delivery_type where is_basic !='1' ";

					if($debug){
						echo $sql;
					}else{
						$db->query($sql);
					}
					*/

					$sql = "delete from inventory_customer_info where is_basic !='Y' ";

					if($debug){
						echo $sql;
					}else{
						$db->query($sql);
					}


					$sql = "INSERT INTO `common_company_relation` (`relation_ix`, `company_id`, `relation_code`, `seq`, `depth`, `edit_date`, `reg_date`) VALUES
							('', '".$company_id."', 'C0001', 1, 0, '0000-00-00 00:00:00', NOW())";
					if($debug){
						echo $sql;
					}else{
						$db->query($sql);
					}






		$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/upfile/";
		//echo $path;
		//exit;
		if(!is_dir($path)){
			mkdir($path, 0777);
		}

		//$make_dirs[] = "_shared";
		$make_dirs[] = array("dir"=>"BatchUploadImages");		
		$make_dirs[] = array("dir"=>"_cache");// 캐쉬데이타 
		$make_dirs[] = array("dir"=>"_logs"); 
		$make_dirs[] = array("dir"=>"bbs_data"); // 게시판 업로드 데이타
		//$make_dirs[] = array("dir"=>"bbs_templet");
		$make_dirs[] = array("dir"=>"blogbbs_data"); 
		$make_dirs[] = array("dir"=>"cafebbs_data");
		$make_dirs[] = array("dir"=>"category"); //
		$make_dirs[] = array("dir"=>"cms");
		//$make_dirs[] = "compile_");		
		//$make_dirs[] = "minishop_templet");
		//$make_dirs[] = "mobile_templet");
		$make_dirs[] = array("dir"=>"compile_");
		$make_dirs[] = array("dir"=>"dbbackup");
		$make_dirs[] = array("dir"=>"deepzoom");
		$make_dirs[] = array("dir"=>"images", "sub_dir"=> array("product","product_detail","brand","cafe","company","cooperation","cupon","dbbackup","deepzoom","department","event","main","member_group","memberimg","popup","position","shopimg","stamps","upfile","useafter","watermark"));
		$make_dirs[] = array("dir"=>"work");
		//"banner","category","addimg","flash_data","icon","mail",

		for($i = 0; $i < count($make_dirs);$i++){
			
			if(is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/")){
				if(!$debug){
					//echo count($make_dirs[$i]["sub_dir"]);
					if(count($make_dirs[$i]["sub_dir"]) == 0){
						rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/");
						echo "디렉터리 삭제 : ".$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/ 서브디렉터리 갯수 : ".count($make_dirs[$i]["sub_dir"])."<br>";
					}
				}else{
					echo ($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/<br><br>");
				}
			}
			$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/";
			if(!is_dir($path)){
				if(!$debug){
					mkdir($path, 0777);
					chmod($path, 0777);
					echo "디렉터리 생성 : ".$path."/<br>";
				}else{
					echo "디렉터리 생성 : ".$path."<br>";
				}
			}

			for($j = 0; $j < count($make_dirs[$i]["sub_dir"]);$j++){
				if(is_dir($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/".$make_dirs[$i]["sub_dir"][$j]."/")){
					if(!$debug){
						rmdirr($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/".$make_dirs[$i]["sub_dir"][$j]."/");
						echo "***디렉터리 삭제 : ".$_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/".$make_dirs[$i]["sub_dir"][$j]."/<br>";
					}else{
						echo ($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/".$make_dirs[$i]["sub_dir"][$j]."/<br><br>");
					}
				}
				$path = $_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/".$make_dirs[$i]["dir"]."/".$make_dirs[$i]["sub_dir"][$j]."/";
				if(!is_dir($path)){
					if(!$debug){
						mkdir($path, 0777);
						chmod($path, 0777);
						echo "서브 디렉터리 생성  : ".$path."<br>";
					}else{
						echo "서브 디렉터리 생성 : ".$path."<br>";
					}
				}
			}
			
		}

}else{
	echo "초기화 하실 수 없습니다. 초기화는 deploy 사이트만 가능합니다.";
}
exit;
exit;

$product_sql[] = "delete from shop_priceinfo "; // 상품 가격 히스토리 테이블
$product_sql[] = "delete from shop_product"; //상품 메인테이블
$product_sql[] = "delete from shop_product_auction"; // 상품 경매 상세 테이블
$product_sql[] = "delete from shop_product_buyingservice_priceinfo "; // 구매대행 상품 가격상세테이블 
$product_sql[] = "delete from shop_product_car"; // 자동차 상품 상세 테이블
$product_sql[] = "delete from shop_product_displayinfo"; // 상품 디스플레이 옵션 테이블
$product_sql[] = "delete from shop_product_options "; //상품 옵션 정보 테이블
$product_sql[] = "delete from shop_product_options_detail "; // 상품 옵션 정보 상세 테이블 
$product_sql[] = "delete from shop_product_photo ";  // 상품 포토 게시판????    *******************  
$product_sql[] = "delete from shop_product_qna ";  // 상품문의 테이블
$product_sql[] = "delete from shop_product_qna2 ";  // 상품문의 백업 테이블 /// 삭제 필요할듯
$product_sql[] = "delete from shop_product_relation ";  // 상품 카테고리 매핑 테이블
$product_sql[] = "delete from shop_category_info ";
$product_sql[] = "delete from shop_category_addfield ";
$product_sql[] = "delete from shop_company ";  // 제조사 테이블
$product_sql[] = "delete from shop_brand ";  // 브랜드 정보 테이블
$product_sql[] = "delete from shop_addimage ";  // 브랜드 정보 테이블 --> shop_product_addimage  로 변경
$product_sql[] = "delete from shop_aution_list ";  // 브랜드 정보 테이블  --> shop_product_auction_list  테이블로 변경





$order_sql[] = "delete from shop_cart ";   // 장바구니 테이블
$order_sql[] = "delete from shop_cart_options ";   // 장바구니 옵션 테이블

$order_sql[] = "delete from shop_order ";   // 상품 주문 메인 테이블
$order_sql[] = "delete from shop_order_detail ";  // 주문 상세 테이블
$order_sql[] = "delete from shop_order_delivery "; // 주문 업체별 배송비 테이블
$order_sql[] = "delete from shop_order_detail "; // 주문상세 테이블
$order_sql[] = "delete from shop_order_gift ";  // 사은품 테이블 ? 사제 필요할듯 사용하는데 있는지 확인해서  *******************
$order_sql[] = "delete from shop_order_memo ";  // 주문 관리자 메모 내역
$order_sql[] = "delete from shop_order_status ";  // 주문 상태 변경 상세 테이블
$order_sql[] = "delete from shop_cash_info ";   // 캐쉬정보 관리 페이블
$order_sql[] = "delete from shop_accounts ";   // 정산테이블


$member_sql[] = "delete from common_user where mem_type != 'A' "; // 회원 테이블 
$member_sql[] = "delete from common_user u, common_member_detail md where u.code = md.code and mem_type != 'A'  "; // 회원 상세테이블
$member_sql[] = "delete from common_seller_delivery "; // 셀러 배송정책 테이블
$member_sql[] = "delete from common_seller_detail ";  // 셀러 상세 테이블
$member_sql[] = "delete from common_company_detail where com_type != 'A'  "; // 기업회원 상세 테이블
$member_sql[] = "delete from co_common_user "; // 공유 회원 테이블
$member_sql[] = "delete from co_common_member_detail "; // 공유 회원 상세테이블
$member_sql[] = "delete from co_common_seller_delivery "; // 공유 셀러 배송정책 테이블
$member_sql[] = "delete from co_common_seller_detail "; // 공유 셀러 상세 정보테이블
$member_sql[] = "delete from co_common_company_detail  "; // 공유 기업회원 상세 테이블
$member_sql[] = "delete from shop_reserve_info  ";  //적립금 정보 
$member_sql[] = "delete from shop_companyinfo "; // 쇼핑몰 운영업체 정보, 배송 정책 정보 --> 다른테이블들이랑 혼재되어 사용되고 있음 정리필요 *******************

$member_sql[] = "delete from shop_member "; // 쓰는데 있는지 확인해서 삭제 필요
$member_sql[] = "delete from shop_member_mail "; // 쓰는데 있는지 확인해서 삭제 필요
$member_sql[] = "delete from shop_member_talk_history "; // 회원 상담내역 --> common_member_talk_history 로 변경 필요  *******************
$member_sql[] = "delete from shop_memobox  ";  //쪽지  
$member_sql[] = "delete from shop_memobox_send   ";  //쪽지 발송 테이블  <-- 필요한지 확인해서 삭제 필요  *******************
$member_sql[] = "delete from shop_memobox  ";  //쪽지 


$display_sql[] = "delete from shop_cupon  ";  //쿠폰 테이블
$display_sql[] = "delete from shop_cupon_publish  ";  //쿠폰 발행 
$display_sql[] = "delete from shop_cupon_regist  ";  //쿠폰 등록
$display_sql[] = "delete from shop_cupon_relation_brand  ";  //쿠폰 발행 브랜드 정보
$display_sql[] = "delete from shop_cupon_relation_category  ";  //쿠폰 발행 카테고리 정보 
$display_sql[] = "delete from shop_cupon_relation_product  ";  //쿠폰 발행 상품정보
$display_sql[] = "delete from shop_cupon_relation_category  ";  //쿠폰 

print_r($product_sql);
exit;


/*



exit;
$db = new Database();
$udb = new Database();
$sql = "select * from admin_menus where menu_div = 'logstory' ";
$db->query($sql);

for($i=0;$i<$db->total;$i++){
	$db->fetch($i);
	
	$paths = explode("/",$db->dt["menu_link"]);
	
	if(count($paths) == 5){
		$menu_div = $paths[2]."/".$paths[3];
	}else{
		$menu_div = $paths[2];
	}

	$sql = "update admin_menus set menu_div = '".$menu_div."' where menu_div = 'logstory' and menu_code = '".$db->dt[menu_code]."'";
	echo $sql."<br><br>";
	//$udb->query($sql);

	$sql = "update admin_dic set menu_div = '".$menu_div."' where menu_div = 'logstory' and menu_code = '".$db->dt[menu_code]."'";
	echo $sql."<br><br>";
	//$udb->query($sql);

}








include "./class/Snoopy.class.php";
$Tag = curl_init();

$bs_url = "http://www1.bloomingdales.com/catalog/product/index.ognc?ID=293508&CategoryID=18229&PageID=18228*1*24*-1*-1*1";
$bs_url = "http://www.sunshome.com/shop/shopbrand.html?xcode=139&type=X";
//$bs_url = "http://www.eurohomme.co.kr/front/php/product.php?product_no=12626&main_cate_no=1&display_group=5";

if(true){
curl_setopt( $Tag , CURLOPT_URL , "$bs_url" ); 

ob_start();
curl_exec( $Tag );
curl_close( $Tag );
$results = ob_get_contents();
ob_clean();

echo $results;
$datas = split("\n",$results);
}else{

$snoopy = new Snoopy;
$snoopy->fetch($bs_url);
print $snoopy->results;
//exit;
$datas = split("\n",$snoopy->results);
}
echo "<img src='http://www.sunshome.com/shopimages/sunshomecom/1390010000053.jpg?1291355381'>";
echo "<img src='http://www.eurohomme.co.kr/web/images/erbrandstory.jpg'>";
*/

if($act == "user_name_update" || false){
	$db = new Database;

	$db->query("select * from common_member_detail ");
	$users = $db->fetchall();

	for($i=0;$i < count($users);$i++){
		$sql = "select name from shop_member where code ='".$users[$i][code]."'  ";
		//echo $sql."<br>";
		$db->query($sql);
		if($db->total){
			$db->fetch();		
			$sql = "update common_member_detail set name ='".$db->dt[name]."' where code = '".$users[$i][code]."'  ";
			echo $sql."<br>";
			$db->query($sql);
		}
	}



	$db->query("select cmd.name , cmd.code,  cu.charger_ix from common_member_detail cmd, common_user cu where cmd.code = cu.code  ");
	$users = $db->fetchall();

	for($i=0;$i < count($users);$i++){
		$sql = "select charger as name from  __shop_company_userinfo  where charger_ix ='".$users[$i][charger_ix]."'  ";
		//echo $sql."<br>";
		$db->query($sql);
		if($db->total){
			$db->fetch();		
			$sql = "update common_member_detail set name ='".$db->dt[name]."' where code = '".$users[$i][code]."'  ";
			echo $sql."<br>";
			$db->query($sql);
		}
	}
}

if($mode == "company_id_update"){
	$db = new Database;
	//$db->debug = true;
	//'3444fde7c7d641abc19d5a26f35a12cc' 
	//$change_company_id = "723ca0c86cf676addb1f7726505e455b";//md5(uniqid(rand())); // 디마켓 
	//echo md5(uniqid(rand()));
	//exit;
	$change_company_id = "2aa0bfd33c7a517104b6351660a4232d";// // biz.mallstory.com
	//echo $change_company_id;
	//exit;
	$this_company_id = '3444fde7c7d641abc19d5a26f35a12cc';

	$db->query("update common_user set company_id ='".$change_company_id."' where company_id = '$this_company_id'  ");
	$db->query("update common_company_detail set company_id ='".$change_company_id."' where company_id = '$this_company_id'  ");
	$db->query("update common_seller_detail set company_id ='".$change_company_id."' where company_id = '$this_company_id'  ");
	$db->query("update common_seller_delivery set company_id ='".$change_company_id."' where company_id = '$this_company_id'  ");
	$db->query("update shop_product set admin ='".$change_company_id."' where admin = '$this_company_id'  ");

	
	//$db->query("update shop_product set admin ='".$change_company_id."' where admin = '$this_company_id'  ");
	//$db->query("update shop_product set admin ='".$change_company_id."' where admin = '$this_company_id'  ");
}
/*
if($mode == 'initialize'){	
$db = new Database;

	$db->query("DELETE FROM product ");
	$db->query("DELETE FROM priceinfo ");
	$db->query("DELETE FROM relation ");
	$db->query("DELETE FROM addimage ");
	//$db->query("DELETE FROM category_info ");
	$db->query("DELETE FROM priceinfo ");
	$db->query("DELETE FROM member ");
	$db->query("DELETE FROM member_add ");
	$db->query("DELETE FROM myshopping ");
	$db->query("DELETE FROM orders ");
	$db->query("DELETE FROM poll_field ");
	$db->query("DELETE FROM product_option ");
	$db->query("DELETE FROM search_relation ");

	
}


if($mode == 'forbizinit'){	
$db = new Database;

	$db->query("DELETE FROM ".TBL_COMMERCE_SALESTACK." ");
	$db->query("DELETE FROM ".TBL_COMMERCE_VIEWINGVIEW." ");
	$db->query("DELETE FROM for_byetcreferer ");
	$db->query("DELETE FROM for_bykeyword ");
	$db->query("DELETE FROM for_bypage ");
	$db->query("DELETE FROM for_byreferer ");
	$db->query("DELETE FROM for_etchost ");
	$db->query("DELETE FROM for_etcrefererinfo ");
	$db->query("DELETE FROM for_keywordinfo ");
	$db->query("DELETE FROM for_pageinfo ");
	$db->query("DELETE FROM for_pageviewtime ");
	$db->query("DELETE FROM for_refererurl");
	$db->query("DELETE FROM for_visitor");
	$db->query("DELETE FROM for_visittime");
	$db->query("DELETE FROM forbiz_time");
	$db->query("DELETE FROM referer_categoryinfo");

	
}
*/
?>
