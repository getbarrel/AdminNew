<?
$db = new Database;
$db2 = new Database;

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

		if($page_type != 'update_download'){			//대량상품수정 다운로드에서는 미분류 설정안함 
			if($page_type == "update_nocategory" ){
				$where .=" and p.reg_category = 'N'";
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

		/*상품 리스트 기본 조건 끝 2014-04-11 이학봉*/


		// serarch 검색 시작 
		//2014-05-13 다른곳에서 인클루드 해서 쓸수 있으므로 절대경로도 변경
		include ($_SERVER["DOCUMENT_ROOT"]."/admin/product/product_query_search.php");
		// serarch 검색 끝 


		/*상품총 수량 쿼리 시작 2014-04-11 이학봉*/
		if(false){
			if($db->dbms_type == "oracle"){
				$sql = "SELECT 
								/*+ index(SHOP_GOODS PRIMARY_202)*/ count(id) as total
							FROM
								".TBL_SHOP_PRODUCT." p
								left join ".TBL_SHOP_PRODUCT_RELATION." r on (p.id = r.pid and r.basic = 1)
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
							left join ".TBL_SHOP_PRODUCT_RELATION." r on (p.id = r.pid and r.basic = 1)
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
			$db2->query($sql);
			$result = $db2->fetch();
			$total = $result['total'];
			$ptotal = $total;
		}

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

		if($orderby != "" && $ordertype != ""){	//정렬조건
			if($db->dbms_type == "oracle"){
				$IndexHint = " /*+ index_desc(IDX_MP_REGDATE) */";
				$orderbyStringForIndex = "  and  p.regdate <  to_date('9999/12/31','YYYY/MM/DD')";
			}else{
				if($orderby == "regdate" && $ordertype == "asc"){
					$orderbyString = " ORDER BY id DESC ";
				}else{
					$orderbyString = " order by $orderby $ordertype ";
				}
			}
		}else{
			if($db->dbms_type == "oracle"){
				$orderbyStringForIndex = " and  p.regdate < to_date('9999/12/31','YYYY/MM/DD') ";
				$IndexHint = " /*+ index_desc(IDX_MP_REGDATE) */";
			}else{
				$orderbyString = " ORDER BY regdate_desc  ";
			}
		}
		/*페이징 및 정렬순서 관련 조건 */
		
		if($mode != 'excel'){	//엑셀다운로드시 limit 값을 설정 안함 2014-07-15 이학봉
			$limit = " LIMIT $start, $max ";
		}


		/*상품리스트 출력 쿼리 2014-04-11*/
		if($db->dbms_type == "oracle"){
				$sql = "select * from (
							select a.*, ROWNUM rnum, func_get_company_name(a.admin) as com_name, case when a.one_commission = 'Y' then a.commission else func_get_commission(a.admin) end as commission_result
							from (
							SELECT distinct
								HIGH_PRIORITY p.id , p.*,r.cid,csd.charge_code,
								case when vieworder = 0 then 100000 else vieworder end as vieworder2
							".$product_image_column_str."
							FROM 
								".TBL_SHOP_PRODUCT." p USE INDEX (regdate_desc)
								right join ".TBL_SHOP_PRODUCT_RELATION."  r on (p.id = r.pid and r.basic = '1')
								right join ".TBL_COMMON_COMPANY_DETAIL."  ccd on (p.admin = ccd.company_id)
								left join ".TBL_COMMON_SELLER_DETAIL." csd on (ccd.company_id = csd.company_id)
								
							where  
								".$orderbyStringForIndex." 
								".$where."
								and p.is_delete = '0'
								".$orderbyString."
						) a where ROWNUM <= ".($start+$max)."
						) where rnum >= ".$start." ";
						//left join shop_product_addinfo as pa on (p.id = pa.pid)
		}else{
			if($static_div == "category"){
				$group_by_string = " group by cid ";
				$select_name = ", p.cid as static_div ";
			}else if($static_div == "md"){
				$group_by_string = " group by md_code ";
				$select_name = ", IFNULL(AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."'),'기타') as static_div ";
			}else if($static_div == "seller"){
				$group_by_string = " group by admin ";
				$select_name = ", IFNULL(ccd.com_name,'기타') as static_div ";
			}else if($static_div == "brand"){
				$group_by_string = " group by brand ";
				$select_name = ", IF(brand_name='','미지정',IFNULL(brand_name,'미지정')) as static_div ";
			}else if($static_div == "date"){
				$group_by_string = " group by static_date  ";
				$select_name = ", date_format(p.regdate, '%Y-%m-%d') as static_div ";
				$orderbyString = " order by static_date asc ";
			}else{
				$group_by_string = " group by admin ";
				$select_name = ", IFNULL(AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."'),'기타') as static_div_name ";
			}
			 
			$sql = "SELECT
						HIGH_PRIORITY p.id, p.*  ".$select_name."
						
						".$product_image_column_str."
					from
					(
						select p.*,r.cid,ccd.com_name, ccd.company_id, 
						sum(case when state = 1 then 1 else 0 end ) as state_1_cnt,
						sum(case when state = 0 then 1 else 0 end ) as state_0_cnt,
						sum(case when state = 2 then 1 else 0 end ) as state_2_cnt,
						sum(case when state = 7 then 1 else 0 end ) as state_7_cnt,
						sum(case when state = 6 then 1 else 0 end ) as state_6_cnt,
						sum(case when state = 8 then 1 else 0 end ) as state_8_cnt,
						sum(case when state = 9 then 1 else 0 end ) as state_9_cnt, 
						date_format(p.regdate, '%Y%m%d') as static_date
						from 
							".TBL_SHOP_PRODUCT." p USE INDEX (regdate_desc)
							inner join ".TBL_COMMON_COMPANY_DETAIL."  ccd on (p.admin = ccd.company_id)
							join ".TBL_SHOP_PRODUCT_RELATION."  r on (p.id = r.pid and r.basic = '1')
						where 
							1
							".$where." 
							and p.is_delete = '0'
							$group_by_string 
							$orderbyString
							$limit
					) p 
					 
					";
					//left join shop_product_addinfo as pa on (p.id = pa.pid)

					if($static_div == "category" && false){
						$sql .= "left join shop_category_info ci on p.cid = ci.cid ";
					}else if($static_div == "seller"){
						$sql .= "left join common_company_detail ccd on p.admin = ccd.company_id ";
					}else if($static_div == "md"){
						$sql .= "left join common_member_detail cmd on p.md_code = cmd.code ";
					}
		}
		
		//echo nl2br($sql);
		//exit;
		$db->query($sql);
}
?>