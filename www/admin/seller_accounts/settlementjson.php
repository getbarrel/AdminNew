<?php
	
	// 검색어 검색 삭제
	// 프로그램 원본은 accounts_list.php 참조
	// 2016.11.13 이철성
	//
	// 2017. 2. 1일 이후 3개월후 본 페이지에 있는 comment 걸려 있는 코드들을 지우시오.
	// 2016.11.14 이철성


	// 샘플쿼리
	// select ac.* , c.com_name from shop_accounts ac left join common_company_detail as c on (ac.company_id = c.company_id) 
	//  WHERE status='AR' and ac_info='1' and date_format(ac.ac_date,'%Y-%m-%d') between '2014-09-01' and '2014-09-01'
	//  테스트 URL
	// http://dev.dcgworld.com/admin/seller_accounts/settlementjson.php?act=accounts_excel&pre_type=period&mode=search&startDate=2014-09-01&endDate=2014-09-01&company_id=&com_name=&search_type=c.com_name&search_text=&x=67&y=26

    if(strcmp($key, 'a4b2995f29b5fc1b458e309aa68be455e1728d8e')) exit;
 
	include("../class/layout.class");

	// 임시로 다이소로 설정
	// 데이터가 안나와서
	//$db = new Database;
	$db = new MySQL("183.111.154.23", "daiso", "daiso!@#$", "daiso_db", "3306");

	$data = $_GET;
	//조건설정
	$where = setCondition($data);

	// 아래 사용하지 않는 ac_ix_param
	// 뭔지 몰라서 함수로 빼고(보기 안좋으므로 comment 처리)
	// 2016.11.13 이철성
	// ac_ix_param = getAcIx();
	$sql = "select ac.* , c.com_name from
			".TBL_SHOP_ACCOUNTS." ac left join ".TBL_COMMON_COMPANY_DETAIL." as c on (ac.company_id = c.company_id)
			$where";


	$db->query($sql);

	date_default_timezone_set('Asia/Seoul');

	$data = array();

	for ($i = 0; $i < $db->total; $i++)
	{
		$db->fetch($i);
		$result = $db->dt;

		$ac_type = getAcType($result[ac_type]); // 수수료, 매입, -
		$surtax_yorn = getSurtaxYorn($result[surtax_yorn]); // 과세, 면세, 영세

		$sellerIds = getSellerIds($result[company_id]);
		$method = getPaymentMethod($result[method]);

		$acInfos = getACInfos($result[company_id]);

		array_push($data, array('id'					=> $result[ac_ix] // 정산번호
							  , 'ac_date'				=> $result[ac_date] // 정산일자
							  , 'com_name'				=> $result[com_name] // 셀러명
							  , 'ac_type'				=> $ac_type // 정산방식
							  , 'ac_term_div'			=> $acInfos[ac_term_div] // 1 = 월1회   2 = 월2회   3 = 매주1회
							  , 'ac_term_date1'			=> $acInfos[ac_term_date1] // 정산일 1
							  , 'ac_term_date2'			=> $acInfos[ac_term_date2] // 정산일 2
							  , 'econtract_commission'  => $acInfos[econtract_commission] // 수수료율
							  , 'surtax_yorn'			=> $surtax_yorn // 과세여부
							  , 'ac_price'				=> $result[ac_price] // 실정산금액
							  , 'order_status'			=> getOrderStatus($result[status]) // 정산상태
							  , 'method_status'			=> getMethodStatus($result[account_method]) // 정산지급방식
							  , 'sellerIds'				=> $sellerIds // 셀러 아이디, array, 한 회사에 여러명이 될 수 있음
							  , 'method'				=> $method // 결제방법
							  , 'module'				=> $result[settle_module]
							  , 'order '				=> array(  // 상품 주문 금액
																  'p_expect_price'		 => $result[p_expect_price] // 정산예정금액(+)
																, 'p_dc_allotment_price' => $result[p_dc_allotment_price] // 할인부담금액(-)
																, 'p_fee_price'			 => $result[p_fee_price] // 수수료(-) 
																, 'p_add_price'			 => $result[p_add_price] // 추가정산금액
																, 'p_ac_price'			 => $result[p_ac_price]  // 실정산금액
															)
							  , 'delivery '				=> array( // 배송비
																  'd_expect_price'		 => $result[d_expect_price] // 배송비(+)
																, 'd_dc_allotment_price' => $result[d_dc_allotment_price] // 할인부담금액(-)
																, 'd_add_price'			 => $result[d_add_price] //  추가정산금액
																, 'd_ac_price'			 => $result[d_ac_price] // 실정산금액
															)));
	}

	echo(json_encode($data));












	function getACInfos($companyId) {
		//$db = new MySQL("183.111.154.23", "daiso", "daiso!@#$", "daiso_db", "3306");
		$db = new Database;
		$sql = sprintf("select ac_term_div, ac_term_date1, ac_term_date2, econtract_commission
						  from common_seller_delivery as csd left join econtract_tmp as et on (csd.et_ix = et.et_ix)
						 where company_id = '%s'", $companyId);
		$db->query($sql);
		$results = $db->fetchall();
		return $results;
	}

	function getSellerIds($companyId) {
		//$db = new MySQL("183.111.154.23", "daiso", "daiso!@#$", "daiso_db", "3306");
		$db = new Database;
		$sql = sprintf("SELECT id FROM common_user WHERE company_id = '%s'", $companyId);
		$db->query($sql);
		$results = $db->fetchall();
		$result = $results[0];
		return $result;
	}

	function getPaymentMethod($method) {
		$result = "";

		switch($method){
			case '0':
				$result = "무통장";
				break;
			case '1':
				$result = "카드";
				break;
			case '2':
				$result = "휴대폰결제";
				break;
			case '3':
				$result = "";
				break;
			case '4':
				$result = "가상계좌";
				break;
			case '5':
				$result = "실시간계좌이체";
				break;
			case '6':
				$result = "모바일결제";
				break;
			case '8':
				$result = "무료결제";
				break;
			case '9':
				$result = "에스크로";
				break;
			case '10':
				$result = "현금";
				break;
			case '11':
				$result = "박스동봉 (반품경우에)";
				break;
			case '12':
				$result = "예치금";
				break;
			case '13':
				$result = "적립금";
				break;
			case '14':
				$result = "장바구니쿠폰";
				break;
			case '50':
				$result = "페이코";
				break;
			case '51':
				$result = "페이팔";
				break;
			default :
				break;
		}
		return $result;
	}

	function getAcType($acType) {
		$result = "";

		switch($acType){
			case 1:
				$result = "수수료";				
				break;
			case 2:
				$result = "매입";				
				break;
			default:
				$result = "-";				
				break;
		}
		return $result;
	}

	function getSurtaxYorn($surtaxYorn) {
		$result = '';
		switch($surtaxYorn){
			case 'N':
				$result = "과세";
				break;
			case 'Y':
				$result = "면세";
				break;
			case 'P':
				$result = "영세";
				break;
			default :
				$result = "-";
				break;
		}
		return $result;
	}





	function setCondition($data) {
		// 아래 테스트를 위해 막아놓음
		//$where .=" WHERE status='".ORDER_STATUS_ACCOUNT_READY."' ";
		$where .=" WHERE 1=1 ";

		$where .= " and ac_info='1' ";
		
		// 날짜는 무조건 있어야함.
		// 만일 없으면 오늘 날짜
		// 2016.11.13 이철성
		$startDate = setTodayIfBlank($data['startDate']);
		$endDate   = setTodayIfBlank($data['endDate']);

		$where .= sprintf(" and  date_format(ac.ac_date,'%%Y-%%m-%%d') between '%s' and '%s'", $startDate, $endDate);

		return $where;



		// ac_info 정산 설정1 : 기간별 2:상품별
		// 무조건 기간별로 한다.
		/*
		if($pre_type=="product"){
			$where .= " and ac_info='2' ";
		}else{
			$where .= " and ac_info='1' ";
		}
		*/

		// 웹에서 가져가는게 아니므로 아래 조건은 없는 것으로..
		// 혹시 특정 업체것만 달라할 수 있으므로 comment 처리
		// 2016.11.13 이철성
		/*
		if($admininfo[admin_level] == 9){
			if($company_id != "") $where .= " and ac.company_id='$company_id' ";

			if($admininfo[mem_type] == "MD"){
				$where .= " and ac.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
			}

		}else if($admininfo[admin_level] == 8){
			$where .= " and ac.company_id = '".$admininfo[company_id]."' ";
		}
		*/
		
		// 정산방식(수수료 체크되어 있는것과 아닌것, 정확히 무슨말인지 모르겠음)
		// 모두 나와야 하므로 삭제
		// 2016.11.13 이철성
		/*
		if(is_array($ac_type)){
			$where .= " and ac.ac_type in ('".implode("','",$ac_type)."') ";
		}else{
			if($ac_type!=""){
				$where .= " and ac.ac_type= '".$ac_type."' ";
			}
		}
		*/
		// 과세여부(과세, 면세)
		// 모두 나와야 하므로 삭제
		// 2016.11.13 이철성
		/*
		if(is_array($surtax_yorn)){
			$where .= " and ac.surtax_yorn in ('".implode("','",$surtax_yorn)."') ";
		}else{
			if($surtax_yorn!=""){
				$where .= " and ac.surtax_yorn= '".$surtax_yorn."' ";
			}
		}
		*/
		// 지급방식(현금, 현금 아닌것????)
		// 모두 나와야 하므로 삭제
		// 2016.11.13 이철성
		/*
		if(is_array($account_method)){
			$where .= " and ac.account_method in ('".implode("','",$account_method)."') ";
		}else{
			if($account_method!=""){
				$where .= " and ac.account_method= '".$account_method."' ";
			}
		}
		*/

		//검색어로 검색하는 것 삭제
		//다중검색으로 추가
		/*
		if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
			//다중검색 시작 2014-04-10 이학봉

			//조인상태땜에 어쩔수 없이 셀러명조인시 변수갑을 바꿧음 2014-08-19 이학봉

			if($search_type == 'c.com_name'){

				if($search_text != ""){
					if(strpos($search_text,",") !== false){
						$search_array = explode(",",$search_text);
						$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
						$search_where .= "and ( ";
						for($i=0;$i<count($search_array);$i++){
							$search_array[$i] = trim($search_array[$i]);
							if($search_array[$i]){
								if($i == count($search_array) - 1){
									$search_where .= $search_type." = '".trim($search_array[$i])."'";
								}else{
									$search_where .= $search_type." = '".trim($search_array[$i])."' or ";
								}
							}
						}
						$search_where .= ")";
					}else if(strpos($search_text,"\n") !== false){//\n
			
						$search_array = explode("\n",trim($search_text));
						$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
						$search_where .= "and ( ";

						for($i=0;$i<count($search_array);$i++){
							$search_array[$i] = trim($search_array[$i]);
							if($search_array[$i]){
								if($i == count($search_array) - 1){
									$search_where .= $search_type." = '".trim($search_array[$i])."'";
								}else{
									$search_where .= $search_type." = '".trim($search_array[$i])."' or ";
								}
							}
						}
						$search_where .= ")";
					}else{
						$search_where .= " and ".$search_type." = '".trim($search_text)."'";
					}
				}

			}else{

				if($search_text != ""){
					if(strpos($search_text,",") !== false){
						$search_array = explode(",",$search_text);
						$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
						$where .= "and ( ";
						for($i=0;$i<count($search_array);$i++){
							$search_array[$i] = trim($search_array[$i]);
							if($search_array[$i]){
								if($i == count($search_array) - 1){
									$where .= $search_type." = '".trim($search_array[$i])."'";
								}else{
									$where .= $search_type." = '".trim($search_array[$i])."' or ";
								}
							}
						}
						$where .= ")";
					}else if(strpos($search_text,"\n") !== false){//\n
						$search_array = explode("\n",$search_text);
						$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));
						$where .= "and ( ";

						for($i=0;$i<count($search_array);$i++){
							$search_array[$i] = trim($search_array[$i]);
							if($search_array[$i]){
								if($i == count($search_array) - 1){
									$where .= $search_type." = '".trim($search_array[$i])."'";
								}else{
									$where .= $search_type." = '".trim($search_array[$i])."' or ";
								}
							}
						}
						$where .= ")";
					}else{
						$where .= " and ".$search_type." = '".trim($search_text)."'";
					}
				}
			
			}

		}else{	//검색어 단일검색
			if($search_text != ""){
				if(substr_count($search_text,",")){
					if($search_type == 'c.com_name'){
						
						$search_where .= " and ".$search_type." in ('".str_replace(",","','",str_replace(" ","",$search_text))."') ";
					}else{
						$where .= " and ".$search_type." in ('".str_replace(",","','",str_replace(" ","",$search_text))."') ";
					}
				}else{
					if($search_type == 'c.com_name'){

						$search_where .= " and ".$search_type." LIKE '%".trim($search_text)."%' ";
					}else{
						$where .= " and ".$search_type." LIKE '%".trim($search_text)."%' ";
					}
				}
			}
		}
		*/
	}

	function setTodayIfBlank($param) {
		$result = '';

	    if($param == ''){
	        $result = date('Y-m-d');
		} else {
			$result = $param;
		}
		return $result;
	}

	function getAcIx() {
		$sql = "select	*
			  from ".TBL_SHOP_ACCOUNTS." ac left join ".TBL_COMMON_COMPANY_DETAIL." as c on (ac.company_id = c.company_id)
			$where";

		$db->query($sql);
		$total = $db->total;
		$accounts=$db->fetchall("object");

		for($i=0;$i < count($accounts);$i++){
			if($ac_ix_param == ""){
				$ac_ix_param = "ac_ix_text=".$accounts[$i][ac_ix];
			}else{
				$ac_ix_param .= "|".$accounts[$i][ac_ix];
			}
		}
		return $ac_ix_param;
	}
