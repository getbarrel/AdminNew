<?
//상품검색 시작

if($mode == "search" || $mode == 'excel'){

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
	}
	//카테고리 검색
	$where .= " and p.id is NOT NULL  AND p.product_type NOT IN ('".implode("','",$sns_product_type)."') $product_bsgoods_where ";

	if($mult_search_use == '1'){	//다중검색 체크시 (검색어 다중검색)
		//다중검색 시작 2014-04-10 이학봉
		if($search_text != ""){
		    if($search_in_out == 'O'){
		        $query_search_type = " NOT IN ";
            }else{
                $query_search_type = " IN ";
            }
			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));

                $where .= "and $search_type $query_search_type ( ";
                $count_where .= "and $search_type $query_search_type ( ";

                for($i=0;$i<count($search_array);$i++){

                    $search_array[$i] = trim($search_array[$i]);

                    if($search_array[$i]){
                        if($i == count($search_array) - 1){
                            $where .= "'".trim($search_array[$i])."'";
                            $count_where .= "'".trim($search_array[$i])."'";
                        }else{
                            $where .= "'".trim($search_array[$i])."' , ";
                            $count_where .= "'".trim($search_array[$i])."' , ";
                        }
                    }
                }
                $where .= ")";
                $count_where .= ")";

			}else if(strpos($search_text,"\n") !== false){//\n
				$search_array = explode("\n",$search_text);
				$search_array = array_filter($search_array, create_function('$a','return preg_match("#\S#", $a);'));

                $where .= "and $search_type $query_search_type ( ";
                $count_where .= "and $search_type $query_search_type ( ";

                for($i=0;$i<count($search_array);$i++){
                    if($search_type == 'o.bmobile' || $search_type == 'odd.rmobile') {
                        $search_array[$i] = format_phone(trim($search_array[$i]));
                    }else {
                        $search_array[$i] = trim($search_array[$i]);
                    }
                    if($search_array[$i]){
                        if($i == count($search_array) - 1){
                            $where .= "'".trim($search_array[$i])."'";
                            $count_where .= "'".trim($search_array[$i])."'";
                        }else{
                            $where .= "'".trim($search_array[$i])."' , ";
                            $count_where .= "'".trim($search_array[$i])."' , ";
                        }
                    }
                }
                $where .= ")";
                $count_where .= ")";

			}else{
                $where .= " and ".$search_type." $query_search_type ('".trim($search_text)."')";
                $count_where .= " and ".$search_type." $query_search_type ('".trim($search_text)."')";
			}
		}

	}else{	//검색어 단일검색
		if($search_text != ""){
			if(substr_count($search_text,",") && $search_type != "p.pname" ){
				$where .= " and ".$search_type." in ('".str_replace(",","','",str_replace(" ","",$search_text))."') ";
			}else{
				$where .= " and ".$search_type." LIKE '%".trim($search_text)."%' ";
			}
		}
	}

	if($sprice && $eprice){	//가격대별 검색
		$where .= " and sellprice between $sprice and $eprice ";
	}

	if($wholesale_yn == "A"){	//소매/도매 검색
		$where .= " and p.wholesale_yn in ('A','Y','N')";
	}else if( $wholesale_yn == "Y"){
		$where .= " and p.wholesale_yn in ('A','Y')";
	}else if( $wholesale_yn == "N"){
		$where .= " and p.wholesale_yn in ('A','N')";
	}

	if($offline_yn != ""){	//포스사용여부
		$where .= " and p.offline_yn = '".$offline_yn."'";
	}

	if($soho != ""){
		$where .= " and p.soho = '".$soho."'";
	}

	if($designer != ""){
		$where .= " and p.designer = '".$designer."'";
	}

	if($mirrorpick != ""){
		$where .= " and p.mirrorpick = '".$mirrorpick."'";
	}


	//상품구분 검색관련
	if(is_array($product_type)) {		//상품구분 (일반,세트,사은품,기획) 검색
		if(count($product_type)>0){
			$where.=" AND p.product_type IN ('".implode("','",$product_type)."')";
		}
	}else{
		if($product_type != "" ){
			$where .= " and p.product_type = '".$product_type."'";
		}else{
			//$product_type=array();
			$product_type='';
		}
	}

	//모바일사용유무
	if($is_mobile_use != "" && $is_mobile_use != 'A'){
		$where .= " and p.is_mobile_use = '".$is_mobile_use."'";
	}

	//판매상태 검색관련
	if(is_array($state)){		//판매상태 (판매중, 일시품절, 본사대기 ... )
		if(count($state)>0){
			$where.=" AND p.state IN ('".implode("','",$state)."')";
		}
	}else{
		if($state != ""){
			$where .= " and p.state = '".$state."'";
		}else{
			//$state=array();
			$state='';
		}
	}

	//배송타입 검색관련
	if(is_array($delivery_type)){		//판매상태 (판매중, 일시품절, 본사대기 ... )
		if(count($delivery_type)>0){
			$where.=" AND p.delivery_type IN ('".implode("','",$delivery_type)."')";
		}
	}else{
		if($delivery_type != ""){
			$where .= " and p.delivery_type = '".$delivery_type."'";
		}else{
			$delivery_type='';
		}
	}
	
	//재고품절상품
	/*
	if($stock_zero != ""){
		$where .= " and p.stock_use_yn = 'Y' and (p.stock = '0' or (select sum(option_stock) as total from shop_product_options as po1 inner join shop_product_options_detail as pod1 on (po1.opn_ix = pod1.opn_ix) where pod1.pid = p.id and po1.option_kind in ('a','b','x','s2','x2','c')) or p.available_stock = '0')";
	}
	*/

	 
	//	$where .= " and p.stock_use_yn in ('Y','Q') ";
	if($stock_soldout != ""){
		$where .= " and p.stock = '0' ";
	}
	if($option_stock_soldout != ""){
		$where .= " and (select sum(option_stock) as total from shop_product_options as po1 inner join shop_product_options_detail as pod1 on (po1.opn_ix = pod1.opn_ix) where pod1.pid = p.id and po1.option_kind in ('a','b','x','s2','x2','c')) ";
	}

	if($available_stock_soldout != ""){
		$where .= " and p.available_stock = '0'";
	}

 


	//재고관리 검색관련
	if(is_array($stock_use_yn)){		//재고관리 (사용안함,빠른재고,WMS재고 ... )
		if(count($stock_use_yn)>0){
			$where.=" AND p.stock_use_yn IN ('".implode("','",$stock_use_yn)."')";
		}
	}else{
		if($stock_use_yn != ""){
			$where .= " and p.stock_use_yn = '".$stock_use_yn."'";
		}else{
			//$stock_use_yn=array();
			$stock_use_yn='';
		}
	}

	//노출여부 검색관련
	if(is_array($disp)){		//노출여부 
		if(count($disp)>0){
			$where.=" AND p.disp IN ('".implode("','",$disp)."')";
		}
	}else{
		if($disp != ""){
			$where .= " and p.disp = '".$disp."'";
		}else{
			//$disp=array();
			$disp='';
		}
	}

	//면세 , 과세 여부 
	if(is_array($surtax_yorn)){
		if(count($surtax_yorn)>0){
			$where.=" AND p.surtax_yorn IN ('".implode("','",$surtax_yorn)."')";
		}
	}else{
		if($surtax_yorn != ""){
			$where .= " and p.surtax_yorn = '".$surtax_yorn."'";
		}else{
			//$disp=array();
			$surtax_yorn='';
		}
	}

	//개별배송정책 사용유무
	if(is_array($delivery_policy)){
		if(count($delivery_policy)>0){
			$where.=" AND p.delivery_policy IN ('".implode("','",$delivery_policy)."')";
		}
	}else{
		if($delivery_policy != ""){
			$where .= " and p.delivery_policy = '".$delivery_policy."'";
		}else{
			//$disp=array();
			$delivery_policy='';
		}
	}

	//개별수수료 사용여부
	if(is_array($one_commission)){
		if(count($one_commission)>0){
			$where.=" AND p.one_commission IN ('".implode("','",$one_commission)."')";
		}
	}else{
		if($one_commission != ""){
			$where .= " and p.one_commission = '".$one_commission."'";
		}else{
			//$disp=array();
			$one_commission='';
		}
	}

	//상품관리 검색관련(본사,셀러)
	if(is_array($com_type)){		//노출여부 
		if(count($com_type)>0){
			$where.=" AND ccd.com_type IN ('".implode("','",$com_type)."')";
		}
	}else{
		if($com_type != ""){
			$where .= " and ccd.com_type = '".$com_type."'";
		}else{
			//$com_type=array();
			$com_type='';
		}
	}
	if(is_array($etc9)){		//노출여부 
		if(count($etc9)>0){
			$where.=" AND p.etc9 IN ('".implode("','",$etc9)."')";
		}
	}else{
		if($etc9 != ""){
			$where .= " and p.etc9 = '".$etc9."'";
		}else{
			//$etc9=array();
			$etc9='';
		}
	}

	if($company_id != ""){	//셀러별 검색
		$where .= " and p.admin = '".$company_id."'";
	}

	if($trade_admin != ""){	//매입처 검색
		$where .= " and p.trade_admin = '".$trade_admin."'";
	}

	if($b_ix != ""){	//브랜드검색
		$where .= " and p.brand = '".$b_ix."'";
	}


	if($regdate == '1'){
		if($search_date_type == '1'){	//상품등록일
			if($sdate != "" && $edate != ""){	//등록일자 검색
				$where .= " and  date_format(p.regdate,'%Y-%m-%d') between '".$sdate."' and '".$edate."' ";
			}
		}else{	//상품수정일
			if($sdate != "" && $edate != ""){	//등록일자 검색
				$where .= " and  date_format(p.editdate,'%Y-%m-%d') between '".$sdate."' and '".$edate."' ";
			}
		}
	}

	if($cid2 != ""){	//카테고리검색
		$where .= " and r.cid LIKE '".SetLikeCategory($cid2)."%'";
		$cidWhere .= " and cid LIKE '".SetLikeCategory($cid2)."%'";
	}else{
		$where .= "";
        $cidWhere .= "";
	}
//print_r($_POST);
	if(count($cid) > 0){	//카테고리 다중검색
		$where .=" and (";
		for($i=0;$i<count($cid);$i++){
			if($i == count($cid) - 1){
				$where .= " r.cid like '".SetLikeCategory($cid[$i])."%'";
                $cidWhere .= " cid like '".SetLikeCategory($cid[$i])."%'";
			}else{
				$where .= " r.cid like '".SetLikeCategory($cid[$i])."%' or ";
                $cidWhere .= " cid like '".SetLikeCategory($cid[$i])."%' or ";
			}
		}
		$where .= ")";
	}

	if($md_code != ""){
		$where .= " and p.md_code = '".$md_code."' ";
	}

	if($admininfo[mem_type] == "MD"){	//MD가 속한 업체상품 검색
		$where .= " and p.admin in (".getMySellerList($admininfo[charger_ix]).") ";
	}

	if($is_sell_date == "0"){
		$where .= " and p.is_sell_date = '0' ";
	}else if($is_sell_date == "1"){
		$sell_startdate = $search_sell_priod_sdate." ".$search_sell_priod_sdate_h.":".$search_sell_priod_sdate_i.":".$search_sell_priod_sdate_s;
		$sell_enddate = $search_sell_priod_edate." ".$search_sell_priod_edate_h.":".$search_sell_priod_edate_i.":".$search_sell_priod_edate_s;

		$where .= " and p.is_sell_date = '1' and p.sell_priod_sdate <= '".$sell_startdate."' and p.sell_priod_edate >= '".$sell_enddate."' ";
	}

	if($b_ix !=""){	//브랜드검색
		$where .= " and p.brand = '".$b_ix."' ";
	}

	if($search_ori_ix != ""){	//원산지
		$where .= " and p.origin = '".$search_origin."' ";
	}

	if($search_c_ix != ""){	//제조사
		$where .= " and p.c_ix = '".$search_c_ix."' ";
	}

	if($relation_product_check == '1'){
		$where .= " and (select count(*) as total from shop_relation_product where pid = p.id) ='0' ";
	}

	if($notRelationBasic === true) {
	    $where .= " and r.rid = (select rid from shop_product_relation where pid = p.id $cidWhere order by basic asc limit 1) ";
        //$where .= " and IF((select count(*) as total from shop_product_relation where pid = p.id $cidWhere) > 1, r.rid = (select rid from shop_product_relation where pid = p.id $cidWhere order by basic asc limit 1) ,r.rid = (select rid from shop_product_relation where pid = p.id $cidWhere)) ";
    }

	//할인상품
	if(count($sale_rate) > 0){
		if(in_array('1',$sale_rate)){	//즉시할인
			$where .= " and p.listprice > p.sellprice ";
		}
	}

	if($mall_ix !=""){	//프론트전시 구분
		$where .= " and p.mall_ix = '".$mall_ix."' ";
	}

}else if($mode == 'excel_search'){	//엑셀일괄 검색 2014-04-10 이학봉

	$search_data_array = getBasicSellerSetup("excel_search_".$admininfo[charger_ix]);

	if(is_array($search_data_array) > 0){

		$where .= "and ( ";
		$count_where .= "and ( ";
		for($i=0;$i<count($search_data_array);$i++){
			$search_data_array[$i] = trim($search_data_array[$i]);
			if($search_data_array[$i]){
				if($i == count($search_data_array) - 1){
					$where .= $search_type." = '".trim($search_data_array[$i])."'";
					$count_where .= $search_type." = '".trim($search_data_array[$i])."'";
				}else{
					$where .= $search_type." = '".trim($search_data_array[$i])."' or ";
					$count_where .= $search_type." = '".trim($search_data_array[$i])."' or ";
				}
			}
		}
		$where .= ") ";
		$count_where .= ") ";
	}

	if($regdate == '1'){
		if($sdate != "" && $edate != ""){	//등록일자 검색
            if($search_date_type == '' || $search_date_type =='1'){
                $where .= " and  date_format(p.regdate,'%Y-%m-%d') between '".$sdate."' and '".$edate."' ";
            }else if($search_date_type =='2'){
                $where .= " and  date_format(p.editdate,'%Y-%m-%d') between '".$sdate."' and '".$edate."' ";
            }

		}
	}

}
//상품검색 끝
?>