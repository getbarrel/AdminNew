<?
$slave_db = new Database;
//$slave_db = new Database;

if($admininfo[admin_level] == '9'){			//상품엑셀 다운시 샘플파일 (본사는 company 로 다운 셀러는 seller 로된 파일 양식으로 다운: 삭제하지 마세요 2014-07-25 이학봉 )
	$excel_updown_type = 'company';
}else{
	$excel_updown_type = 'seller';
}

$script_time[count_start] = time();
if($mode == "search" || $mode == "excel_search"  || $mode == "excel"){
		if(substr_count($_SERVER["REQUEST_URI"],"product_bsgoods")){	//사용용도가 무엇인지?
			$product_bsgoods_where = " and p.product_type = 1 ";
		}

		$reserve_data = getBasicSellerSetup('b2c_mileage_rule');		//소매적립금 정책 2014-04-11 이학봉
		$whole_reserve_data = getBasicSellerSetup('b2b_mileage_rule');	//도매적립금 정책 2014-04-11 이학봉


		/*상품 리스트 기본 조건 시작 2014-04-11 이학봉*/
		if($page_type == "error_product_list"){					//판매가오류
			//$where = " and (p.coprice > p.sellprice or p.coprice > p.listprice or p.coprice > p.wholesale_price or p.coprice > p.wholesale_sellprice) ";
			$where = " and (p.coprice > p.sellprice or p.coprice > p.listprice) ";
			$join_type = "inner";
		}else if($page_type == "gift_list"){					//사은품 리스트
			$where = " and p.product_type = '77' ";
			$join_type = "left";		//사은품은 카테고리와 매핑이 안되기에 left조인으로 처리
		}else if($page_type == "company_wating_product_list"){	//본사승인대기 상품
			$where = " and p.state = '7'";
		}else if($page_type == "shortage_results_product_list"){//실적부족상품
			$shortate_setup = getBasicSellerSetup($admininfo[company_id]."_shortage_results_setup");		//소매적립금 정책 2014-04-11 이학봉
			
			if($shortate_setup[disp] == '1'){	//설정 사용시에만 해당 조건 실행

				if($shortate_setup[shortage_state] == '1'){	//판매상태
					$where = " and p.state = '".$shortate_setup[shortage_state]."'";
				}

				if($shortate_setup[selling_use] == '1'){	//판매중일
					//판매중일 계산법 확인필요
				}

				if($shortate_setup[view_cnt_use] == '1'){	//클릭수
					$where .= " and p.view_cnt < '".$shortate_setup[shortage_view_cnt]."' ";
				}

				if($shortate_setup[order_cnt_use] == '1'){	//판매수량
					$where .= " and p.order_cnt < '".$shortate_setup[shortage_order_cnt]."' ";
				}

				if($shortate_setup[order_rate] == '1'){	//클릭대비 판매율
					$where .= " and round(p.order_cnt/p.view_cnt*100,2) < '".$shortate_setup[shortage_order_rate]."'";
				}
			}
		}else if($page_type =="update_state"){
			if($info == "state_waite" || $info == ""){
				$where .=" and p.state = '6'";
			}else if($info == 'state_cancel'){
				$where .=" and p.state = '8'";
			}
		}

		if($page_type != 'update_download' && $page_type != 'gift_list'){			//대량상품수정 다운로드에서는 미분류 설정안함 
			if($page_type == "update_nocategory" ){
				$where .=" and (p.reg_category = 'N' or r.cid is null or c.cid is null)";
			}else{
				$where .=" and p.reg_category = 'Y'";
			}
		}

	

		if($admininfo[admin_level] == 9){	//시스템 관리자일경우
			$where .= "";
		}else{								//셀러업체일경우
			$where .= "and admin ='".$admininfo[company_id]."' and p.product_type != 12 ";
		}

		$where .=$product_bsgoods_where;

		$where .= " and p.is_delete = 0 ";

		if($stock_status){
		    $where .= " and (p.sell_ing_cnt >= p.stock ) ";
        }

		/*상품 리스트 기본 조건 끝 2014-04-11 이학봉*/


		// serarch 검색 시작 
		//2014-05-13 다른곳에서 인클루드 해서 쓸수 있으므로 절대경로도 변경
		include ($_SERVER["DOCUMENT_ROOT"]."/admin/product/product_query_search.php");
		// serarch 검색 끝 
		//스핑크스 검색엔진 용 작업진행중 160615 JK 싱크 대비 하단에 조건 false 추가 
		if($_SESSION['admin_config']['search_engine_yn'] == 'Y' && $_SESSION['admin_config']['search_engine_type'] == 'S'){
			
			require $_SERVER["DOCUMENT_ROOT"].'/class/sphinxfb.class';
			$sfb = new sphinxfb(); // mysql 데이터베이스
			if($page_type){
				$_GET['page_type'] = $page_type;
			}
			$sphinx_where = $_GET;
			$search_result = $sfb->admin_search_goods($sphinx_where,$page,$max);	
			$total = $search_result[total];
			$ptotal = $total;
			$goods_datas = $search_result[products];
			//print_r($search_result);

		}else{

				/*상품총 수량 쿼리 시작 2014-04-11 이학봉*/
				if($slave_db->dbms_type == "oracle"){
					$sql = "SELECT 
									/*+ index(SHOP_GOODS PRIMARY_202)*/ count(id) as total
								FROM
									".TBL_SHOP_PRODUCT." p
									left join ".TBL_SHOP_PRODUCT_RELATION." r on (p.id = r.pid ".($notRelationBasic == true ? '' : 'and r.basic = 1').")
									left join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid
									inner join ".TBL_COMMON_COMPANY_DETAIL." ccd on (p.admin = ccd.company_id)
									left join ".TBL_COMMON_SELLER_DETAIL." csd on (ccd.company_id = csd.company_id)
									left join shop_product_addinfo as pa on (p.id = pa.pid)
								where
									p.id = r.pid
									and p.is_delete = '0'
									$where
									";
				}else{
					$sql = "SELECT 
								 count(p.id) as total
							FROM
								".TBL_SHOP_PRODUCT." p
								left join ".TBL_SHOP_PRODUCT_RELATION." r on (p.id = r.pid ".($notRelationBasic == true ? '' : 'and r.basic = 1').")
								left join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid
								inner join ".TBL_COMMON_COMPANY_DETAIL." ccd on (p.admin = ccd.company_id)
								left join ".TBL_COMMON_SELLER_DETAIL." csd on (ccd.company_id = csd.company_id)
								
							where
								1
								and p.is_delete = '0'
								$where
							";
							//left join shop_product_addinfo as pa on (p.id = pa.pid)
				}
				
	

				//echo nl2br($sql);exit;
				$slave_db->query($sql);
				$result = $slave_db->fetch();
				$total = $result['total'];

				$ptotal = $total;

				/*상품총 수량 쿼리 끝 2014-04-11 이학봉*/


				/*페이징 및 정렬순서 관련 조건 */
				if ($page == ''){ 
					if($_SESSION["pageging_info"][md5($_SERVER["PHP_SELF"])]["page"] != ""){
						$page  = $_SESSION["pageging_info"][md5($_SERVER["PHP_SELF"])]["page"];
						$start = ($page - 1) * $max;
					}else{
						$page  = 1;
						$start = 0;
					}
					if($_SESSION["pageging_info"][md5($_SERVER["PHP_SELF"])]["nset"] != ""){
						$nset  = $_SESSION["pageging_info"][md5($_SERVER["PHP_SELF"])]["nset"];
					}else{
						$nset  = 1;
					}
				}else{
					$start = ($page - 1) * $max;
					$_SESSION["pageging_info"][md5($_SERVER["PHP_SELF"])]["page"] = $page;
					$_SESSION["pageging_info"][md5($_SERVER["PHP_SELF"])]["nset"] = $nset;
				}

				if(ceil($total/$max)  < $_SESSION["pageging_info"][md5($_SERVER["PHP_SELF"])]["page"]){//수정 kbk 13/10/21
					unset($_SESSION["pageging_info"]);
					$page = 1;
				}

				$orderbyAll = "p.id desc";

				if($orderby != "" && $ordertype != ""){	//정렬조건
					if($slave_db->dbms_type == "oracle"){
						$IndexHint = " /*+ index_desc(IDX_MP_REGDATE) */";
						$orderbyStringForIndex = "  and  p.regdate <  to_date('9999/12/31','YYYY/MM/DD')";
					}else{
						if($orderby == "regdate" && $ordertype == "asc"){
							$orderbyString = " ORDER BY id ASC ";
						}else{
							$orderbyString = " order by $orderby $ordertype ";
						}
					}
					$orderbyAll = "p.".$sortDepth." asc";
				}else{
					if($slave_db->dbms_type == "oracle"){
						$orderbyStringForIndex = " and  p.regdate < to_date('9999/12/31','YYYY/MM/DD') ";
						$IndexHint = " /*+ index_desc(IDX_MP_REGDATE) */";
					}else{
						$orderbyString = " ORDER BY p.regdate DESC";
					}
				}
				/*페이징 및 정렬순서 관련 조건 */
				
				if($mode != 'excel'){	//엑셀다운로드시 limit 값을 설정 안함 2014-07-15 이학봉
					$limit = " LIMIT $start, $max ";
				}else{
				    if($notRelationBasic){
                        $limit = " LIMIT 0, $total ";
                    }
                }




				/*상품리스트 출력 쿼리 2014-04-11*/
				if($slave_db->dbms_type == "oracle"){
						$sql = "select * from (
									select a.*, ROWNUM rnum, func_get_company_name(a.admin) as com_name, case when a.one_commission = 'Y' then a.commission else func_get_commission(a.admin) end as commission_result
									from (
									SELECT distinct
										HIGH_PRIORITY p.id , p.*,r.cid,csd.charge_code,
										case when vieworder = 0 then 100000 else vieworder end as vieworder2
									".$product_image_column_str."
									FROM 
										".TBL_SHOP_PRODUCT." p 
										right join ".TBL_SHOP_PRODUCT_RELATION."  r on (p.id = r.pid ".($notRelationBasic == true ? '' : 'and r.basic = 1').")
		
										left join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid
										right join ".TBL_COMMON_COMPANY_DETAIL."  ccd on (p.admin = ccd.company_id)
										left join ".TBL_COMMON_SELLER_DETAIL." csd on (ccd.company_id = csd.company_id)
										left join shop_product_addinfo as pa on (p.id = pa.pid)
									where  
										".$orderbyStringForIndex." 
										".$where."
										and p.is_delete = '0'
										".$orderbyString."
								) a where ROWNUM <= ".($start+$max)."
								) where rnum >= ".$start." ";
				}else{

					$sql = "SELECT
								HIGH_PRIORITY p.id, p.* , csd.charge_code ,(select psh.state_msg from shop_product_state_history psh where psh.pid=p.id and psh.state = p.state order by psh.regdate desc limit 0,1) as state_msg";

					if($page_type == "update_movie"){
                        $sql .= ",(select count(*) from shop_product_viralinfo where pid = p.id and vi_use='1') as viral_url_total";
					}else if($page_type == "update_wish"){
                        $sql .= ",(select count(*) as total from shop_relation_product where pid = p.id) as relation_cnt";
                    }

                    $sql .=	$product_image_column_str."
							from
							(
								select p.*,r.basic,r.cid,r.rid,r.sortdepth0,r.sortdepth1,r.sortdepth2,r.sortdepth3,r.sortdepth4,ccd.com_name, ccd.company_id, case when vieworder = 0 then 100000 else vieworder end as vieworder2
								from 
									".TBL_SHOP_PRODUCT." p 
									inner join ".TBL_COMMON_COMPANY_DETAIL."  ccd on (p.admin = ccd.company_id)
									left join ".TBL_SHOP_PRODUCT_RELATION."  r on (p.id = r.pid ".($notRelationBasic == true ? '' : 'and r.basic = 1').")									
								left join ".TBL_SHOP_CATEGORY_INFO." c on r.cid = c.cid
									left join shop_product_addinfo as pa on (p.id = pa.pid)
								where 
									1
									".$where." 
									and p.is_delete = '0'
									$orderbyString
									$limit
							) p 
							left join ".TBL_COMMON_SELLER_DETAIL." csd on (p.company_id = csd.company_id) order by ".$orderbyAll."
							";
				}
				//p.id desc

				//echo nl2br($sql);
				// exit;
				$slave_db->query($sql);
				$goods_datas = $slave_db->fetchall();
		}
}
?>