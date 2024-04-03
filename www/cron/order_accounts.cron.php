<?
/*
 * 2017-09-05 홍진영 ac_term_div 는 본래 송금 대기 일자에서 정산 확정일자로 변경
 * */
include("../class/layout.class");
include("../admin/seller_accounts/accounts.lib.php");

$db = new Database;

$sql="select company_id from ".TBL_COMMON_COMPANY_DETAIL." where com_type='A' ";
$db->query($sql);
$db->fetch();
$admin_com_id = $db->dt[company_id];

/*
if(date("d")=="1"){
	$ac_action=true;
	
	$sql="select
				company_id,ac_term_div
			from
				".TBL_COMMON_SELLER_DELIVERY."
			where
				company_id != '".$admin_com_id."'
			and 
				ac_term_div in ('1','2') ";
	$db->query($sql);
	
	

	if($db->total){
		$ac_company = $db->fetchall("object");
		foreach($ac_company as $key => $value){
			$company_id_array[$key]=$value[company_id];
			if($value[ac_term_div]=="2"){
				$ac_title[$value[company_id]]=date("m",strtotime('-1 month'))."월 2차";
			}else{
				$ac_title[$value[company_id]]=date("m",strtotime('-1 month'))."월 1차";
			}
		}
	}


}elseif(date("d")=="16"){
	$ac_action=true;
	
	$sql="select
				company_id,ac_term_div
			from
				".TBL_COMMON_SELLER_DELIVERY."
			where
				company_id != '".$admin_com_id."'
			and 
				ac_term_div in ('2') ";
	$db->query($sql);
	
	if($db->total){
		$ac_company = $db->fetchall("object");
		foreach($ac_company as $key => $value){
			$company_id_array[$key]=$value[company_id];
			$ac_title[$value[company_id]]=date("m")."월 1차";
		}
	}

}else{
	$ac_action=false;
}
*/

$sql = "select
    company_id, ac_term_div
from
    " . TBL_COMMON_SELLER_DELIVERY . "
where
    company_id != '" . $admin_com_id . "'
and
    (
        (
            ac_term_div ='1'
            and
            (
                ac_term_date1 ='" . date('j') . "'
                OR
                ( ac_term_date1 > '" . date('t') . "' and '" . date("d") . "' in ('28','29','30','31') )
            )
        )
        OR
        (
            ac_term_div ='2'
            and
            (
                ac_term_date1 ='" . date('j') . "'
                OR
                ( ac_term_date1 > '" . date('t') . "' and '" . date("d") . "' in ('28','29','30','31') )
            )
        )
        OR
        (
            ac_term_div ='2'
            and
            (
                ac_term_date2 ='" . date('j') . "'
                OR
                ( ac_term_date2 > '" . date('t') . "' and '" . date("d") . "' in ('28','29','30','31') )
            )
        )
        OR
        (
            ac_term_div ='3'
            and
            ac_term_date1 ='" . date('w') . "')
    )";
$db->query($sql);

if($db->total){
    $ac_action=true;
    $yesterday = date("Y년 m월 d일",strtotime('-1 day'));
    $ac_company = $db->fetchall("object");
    foreach($ac_company as $key => $value){
        $company_id_array[$key]=$value[company_id];

        //월 2회
        if($value[ac_term_div]=="2"){
            if($value[ac_term_date1] == date('j')){
                $ac_title[$value[company_id]]= $yesterday."까지 월 1차 정산";
            }else{
                $ac_title[$value[company_id]]= $yesterday."까지 월 2차 정산";
            }
        }
        //매주
        else if($value[ac_term_div]=="3"){
            $ac_title[$value[company_id]]= $yesterday."까지 주 ".ceil((date('w') + date('j') - 1)/7)."차 정산";
        }
        //월 1회
        else {
            $ac_title[$value[company_id]]= $yesterday."까지 월 정산";
        }
    }
}

if($ac_action){

	$tmp_where="
	where od.product_type NOT IN (".implode(',',$sns_product_type).") 
	and od.account_info = '1'
	and od.account_type !='3' 
	and DATE_FORMAT(DATE_ADD(case od.ac_delivery_type when '".ORDER_STATUS_INCOM_COMPLETE."' then od.ic_date when '".ORDER_STATUS_DELIVERY_READY."' then od.dr_date when '".ORDER_STATUS_DELIVERY_ING."' then od.di_date when '".ORDER_STATUS_DELIVERY_COMPLETE."' then od.dc_date when '".ORDER_STATUS_BUY_FINALIZED."' then od.bf_date else od.dc_date end,INTERVAL od.ac_expect_date DAY),'%Y%m%d') < '".date('Ymd')."' ";

	$where = $tmp_where."
	and od.company_id != '".$admin_com_id."'
	and od.company_id in ('".implode("','",$company_id_array)."') ";


	$sub_where="
	and odr.product_type NOT IN (".implode(',',$sns_product_type).") 
	and odr.company_id != '".$admin_com_id."'
	and odr.account_info = '1'
	and odr.account_type !='3'
	and  DATE_FORMAT(DATE_ADD(case odr.ac_delivery_type when '".ORDER_STATUS_INCOM_COMPLETE."' then odr.ic_date when '".ORDER_STATUS_DELIVERY_READY."' then odr.dr_date when '".ORDER_STATUS_DELIVERY_ING."' then odr.di_date when '".ORDER_STATUS_DELIVERY_COMPLETE."' then odr.dc_date when '".ORDER_STATUS_BUY_FINALIZED."' then odr.bf_date else odr.dc_date end,INTERVAL odr.ac_expect_date DAY),'%Y%m%d') < '".date('Ymd')."'";

	$sql = "select
			company_id, account_type, surtax_yorn, account_info, account_method,
			sum(case when refund_bool='Y' then ifnull(-p_sell_price,'0') else ifnull(p_sell_price,'0') end) as p_sell_price,
			sum(case when refund_bool='Y' then ifnull(-p_expect_price,'0') else ifnull(p_expect_price,'0') end) as p_expect_price,
			sum(case when refund_bool='Y' then ifnull(-p_dc_allotment_price,'0') else ifnull(p_dc_allotment_price,'0') end) as p_dc_allotment_price,
			sum(case when refund_bool='Y' then ifnull(-p_fee_price,'0') else ifnull(p_fee_price,'0') end) as p_fee_price,

			sum(case when refund_bool='Y' then ifnull(-d_expect_price,'0') else ifnull(d_expect_price,'0') end) as d_expect_price,
			sum(case when refund_bool='Y' then ifnull(-d_dc_allotment_price,'0') else ifnull(d_dc_allotment_price,'0') end) as d_dc_allotment_price
		from
		(
			select
				od.company_id, od.account_type, od.surtax_yorn, od.account_info, od.account_method,

				'N' as refund_bool,

				od.ptprice as p_sell_price,

				case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end as p_expect_price,
				
				(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) as p_dc_allotment_price,

				ROUND(case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end) as p_fee_price,

				(
					case when 
						od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix = od.ode_ix and ".str_replace("od.","odr.",$AC_NORMARL_QUERY)." ".$sub_where.")
					then
						(
							odv.delivery_price
						) 
					else
						'0' 
					end

				) as d_expect_price,
				
				(
					case when 
						od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix = od.ode_ix and ".str_replace("od.","odr.",$AC_NORMARL_QUERY)." ".$sub_where.")
					then
						(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.ode_ix=odv.ode_ix and dc.dc_type in ('DCP','DE')) 
					else
						'0' 
					end

				) as d_dc_allotment_price
		
			from
				".TBL_SHOP_ORDER_DETAIL." od
			left join
				".TBL_SHOP_ORDER." o on o.oid = od.oid
			left join shop_order_delivery odv on (
				odv.oid=od.oid
				and odv.ode_ix = od.ode_ix
				and odv.delivery_type != '1'
				and odv.delivery_pay_type = '1'
				and odv.ac_ix = '0'
			)
			".$where."
			and
			(
				
				".$AC_NORMARL_QUERY."
				
			)
			
			UNION ALL

			select
				od.company_id, od.account_type, od.surtax_yorn, od.account_info, od.account_method,

				'Y' as refund_bool,

				od.ptprice as p_sell_price,

				case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end as p_expect_price,
				
				(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) as p_dc_allotment_price,

				ROUND(case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end) as p_fee_price,

				(
					case when 
						od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.claim_group = od.claim_group and ".str_replace("od.","odr.",$AC_REFUND_QUERY)." ".$sub_where.")
					then
						(
							ocd.delivery_price
						) 
					else
						'0' 
					end

				) as d_expect_price,
				
				'0' as d_dc_allotment_price
		
			from
				".TBL_SHOP_ORDER_DETAIL." od
			left join
				".TBL_SHOP_ORDER." o on o.oid = od.oid
			left join
					shop_order_claim_delivery ocd on (
				ocd.oid=od.oid
				and ocd.company_id=od.company_id
				and ocd.claim_group=od.claim_group
				and ocd.ac_ix='0' 
				and ocd.ac_target_yn='Y' 
				and ocd.delivery_type != '1'
			)
			".$where."
			and
			(
				
				".$AC_REFUND_QUERY."
				
			)
			and
				DATE_FORMAT(od.fc_date,'%Y%m%d') < '".date('Ymd')."'

		) o
		group by o.company_id, o.account_type, o.surtax_yorn ";

	//echo $sql;
	//exit;

	$db->query($sql);
	if($db->total){

		$ac_list = $db->fetchall("object");
		foreach($ac_list as $list){

			$ac_info=array();
			$ac_info[company_id]=$list[company_id];
			$ac_info[ac_date]=date('Y-m-d');
			$ac_info[ac_title]=$ac_title[$list[company_id]];
			$ac_info[ac_info]=$list[account_info];//정산 설정1 : 기간별 2:상품별
			$ac_info[ac_type]=$list[account_type];//정산방식 1:수수료 2:매입(공급가로 정산) 3:미정산
			$ac_info[account_method]=$list[account_method];//정산지급방식
			$ac_info[surtax_yorn]=$list[surtax_yorn];
			//실매출액 = 상품 판매가 - 할인부담율
			$ac_info[p_sell_price]=($list[p_sell_price] - $list[p_dc_allotment_price]);
			$ac_info[p_expect_price]=$list[p_expect_price];
			$ac_info[p_fee_price]=$list[p_fee_price];
			$ac_info[p_dc_allotment_price]=$list[p_dc_allotment_price];
			$ac_info[p_ac_price]=($ac_info[p_expect_price] - $ac_info[p_dc_allotment_price]) - $ac_info[p_fee_price];
			$ac_info[d_sell_price]=$list[d_expect_price];
			$ac_info[d_expect_price]=$list[d_expect_price];
			$ac_info[d_dc_allotment_price]=$list[d_dc_allotment_price];
			$ac_info[d_ac_price]=$ac_info[d_expect_price] - $ac_info[d_dc_allotment_price];
			$ac_info[ac_price]=$ac_info[p_ac_price]+$ac_info[d_ac_price];
			$ac_info[status]=ORDER_STATUS_ACCOUNT_READY;

			$ac_ix = shop_accounts_insert($ac_info);
			
			//정상 주문 업데이트
			$sql="update ".TBL_SHOP_ORDER_DETAIL." od set ac_ix = '".$ac_ix."' , accounts_status = '".ORDER_STATUS_ACCOUNT_READY."',  ac_date = '".date('Ymd')."'
				".$tmp_where."
				and
					od.company_id = '".$list[company_id]."'
				and
					od.account_type = '".$list[account_type]."'
				and
					od.surtax_yorn = '".$list[surtax_yorn]."'
				and
					".$AC_NORMARL_QUERY." ";
			$db->query($sql);

			//반품 주문 업데이트
			$sql="update ".TBL_SHOP_ORDER_DETAIL." od set refund_ac_ix = '".$ac_ix."' , ac_date = '".date('Ymd')."'
				".$tmp_where."
				and
					od.company_id = '".$list[company_id]."'
				and
					od.account_type = '".$list[account_type]."'
				and
					od.surtax_yorn = '".$list[surtax_yorn]."'
				and
					".$AC_REFUND_QUERY."
				and
					DATE_FORMAT(od.fc_date,'%Y%m%d') < '".date('Ymd')."'
				";
			$db->query($sql);

			//배송비 업데이트
			$sql="UPDATE 
					shop_order_delivery odr 
				INNER JOIN
					".TBL_SHOP_ORDER_DETAIL." od
				ON 
					(odr.oid=od.oid and odr.ode_ix=od.ode_ix and odr.delivery_type != '1' and odr.delivery_pay_type = '1' and odr.ac_ix = '0')
				SET 
					odr.ac_ix = '".$ac_ix."'
				WHERE 
					od.ac_ix='".$ac_ix."'
				 ";

			$db->query($sql);

			//클래임배송비 업데이트
			$sql="UPDATE 
					shop_order_claim_delivery ocd 
				INNER JOIN
					".TBL_SHOP_ORDER_DETAIL." od
				ON 
					(ocd.oid=od.oid and ocd.company_id=od.company_id and ocd.claim_group=od.claim_group and ocd.ac_ix='0' and ocd.ac_target_yn='Y' and ocd.delivery_type != '1')
				SET 
					ocd.ac_ix = '".$ac_ix."'
				WHERE 
					od.refund_ac_ix='".$ac_ix."' 
				";

			$db->query($sql);

			/*
			//배송비 업데이트
			$sql = "select
					case when od.refund_ac_ix = '".$ac_ix."' then 'Y' else 'N' end as refund_bool,
					(
						case when 
							od.refund_ac_ix = '".$ac_ix."'
						then
							case when 
								od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ori_company_id = od.ori_company_id and odr.delivery_type = od.delivery_type and odr.delivery_package = od.delivery_package and odr.delivery_method = od.delivery_method and odr.delivery_pay_method = od.delivery_pay_method and odr.delivery_addr_use = od.delivery_addr_use and odr.factory_info_addr_ix = od.factory_info_addr_ix and (case od.delivery_package when 'Y' then odr.pid=od.pid else 1=1 end) 
								and odr.refund_ac_ix = '".$ac_ix."' ".$sub_where.")
							then
								(
									odv.ode_ix
								) 
							else
								'0' 
							end
						else
							case when 
								od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ori_company_id = od.ori_company_id and odr.delivery_type = od.delivery_type and odr.delivery_package = od.delivery_package and odr.delivery_method = od.delivery_method and odr.delivery_pay_method = od.delivery_pay_method and odr.delivery_addr_use = od.delivery_addr_use and odr.factory_info_addr_ix = od.factory_info_addr_ix and (case od.delivery_package when 'Y' then odr.pid=od.pid else 1=1 end)
								and odr.ac_ix = '".$ac_ix."' ".$sub_where.")
							then
								(
									odv.ode_ix
								) 
							else
								'0' 
							end
						end

					) as ode_ix,

					(
						case when 
							od.refund_ac_ix = '".$ac_ix."'
						then
							'0'
						else
							case when 
								od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ori_company_id = od.ori_company_id and odr.delivery_type = od.delivery_type and odr.delivery_package = od.delivery_package and odr.delivery_method = od.delivery_method and odr.delivery_pay_method = od.delivery_pay_method and odr.delivery_addr_use = od.delivery_addr_use and odr.factory_info_addr_ix = od.factory_info_addr_ix and (case od.delivery_package when 'Y' then odr.pid=od.pid else 1=1 end)
								and odr.ac_ix = '".$ac_ix."' ".$sub_where.")
							then
								(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.ode_ix=odv.ode_ix and dc.dc_type in ('DCP','DE')) 
							else
								'0' 
							end
						end
					) as d_dc_allotment_price
				from
					".TBL_SHOP_ORDER_DETAIL." od
				left join 
					shop_order_delivery odv on (
					odv.oid=od.oid
					and odv.ori_company_id = od.ori_company_id
					and odv.delivery_type = od.delivery_type
					and odv.delivery_package = od.delivery_package
					and odv.delivery_method = od.delivery_method
					and odv.delivery_pay_type = od.delivery_pay_method
					and odv.delivery_addr_use = od.delivery_addr_use
					and odv.factory_info_addr_ix = od.factory_info_addr_ix
					and (case od.delivery_package when 'Y' then odv.pid=od.pid else 1=1 end)
					and odv.delivery_type != '1'
				)
				".$where."
				and
					od.company_id = '".$list[company_id]."'
				and
					od.account_type = '".$list[account_type]."'
				and
					od.surtax_yorn = '".$list[surtax_yorn]."' ";
			
			$db->query($sql);
			$delivery_info=$db->fetchall("object");
			
			if(count($delivery_info)>0){
				foreach($delivery_info as $di){
					
					if($di[ode_ix] > 0){
						if($di[refund_bool]=="Y"){
							$update_str=" ac_refund_delivery_price = refund_delivery_price - ac_refund_delivery_price ";
						}else{
							$update_str=" ac_delivery_price = delivery_price - ac_delivery_price - '".($di[d_dc_allotment_price])."' ";
						}

						$sql="update shop_order_delivery set ".$update_str." where ode_ix='".$di[ode_ix]."' ";
						$db->query($sql);
					}
				}
			}
			*/
		}
	}
}

if(false) {
/*
    //ac_term_div 1:월1회 2:월2회 3:매주
    $sql = "select
            ac.ac_ix
        from
            shop_accounts ac ,
            (
                select
                    company_id
                from
                    " . TBL_COMMON_SELLER_DELIVERY . "
                where
                    company_id != '" . $admin_com_id . "'
                and
                    (
                        (
                            ac_term_div ='1'
                            and
                            (
                                ac_term_date1 ='" . date('j') . "'
                                OR
                                ( ac_term_date1 > '" . date('t') . "' and '" . date("d") . "' in ('28','29','30','31') )
                            )
                        )
                        OR
                        (
                            ac_term_div ='2'
                            and
                            (
                                ac_term_date1 ='" . date('j') . "'
                                OR
                                ( ac_term_date1 > '" . date('t') . "' and '" . date("d") . "' in ('28','29','30','31') )
                            )
                        )
                        OR
                        (
                            ac_term_div ='2'
                            and
                            (
                                ac_term_date2 ='" . date('j') . "'
                                OR
                                ( ac_term_date2 > '" . date('t') . "' and '" . date("d") . "' in ('28','29','30','31') )
                            )
                        )
                        OR
                        (
                            ac_term_div ='3'
                            and
                            ac_term_date1 ='" . date('w') . "')
                    )
            ) c
        where ac.company_id = c.company_id and ac.status ='" . ORDER_STATUS_ACCOUNT_READY . "' ";

    $db->query($sql);

    $ac_ix = array();
    if ($db->total) {
        $ac_info = $db->fetchall("object");
        foreach ($ac_info as $key => $value) {
            $ac_ix[$key] = $value[ac_ix];
        }
    }

    $sql = "select
        a.*, c.account_method
    from
    (
        select
                ac.company_id, ac.surtax_yorn,
                sum(ac.p_sell_price) as p_sell_price,
                sum(ac.p_ac_price) as p_ac_price,
                sum(ac.d_sell_price) as d_sell_price,
                sum(ac.d_ac_price) as d_ac_price,
                sum(ac.ac_price) as ac_price
            from
                shop_accounts ac
            where ac_ix in ('" . implode("','", $ac_ix) . "')
            group by ac.company_id, ac.surtax_yorn
    ) a
    left join common_seller_delivery c on a.company_id = c.company_id ";

    $db->query($sql);

    if ($db->total) {
        $ac_info = $db->fetchall("object");

        foreach ($ac_info as $ai) {

            if ($ai["surtax_yorn"] == "N") {
                $p_tax_total_price = $ai["p_ac_price"];
                $p_tax_coprice = round($p_tax_total_price / 1.1);
                $p_tax_price = $p_tax_total_price - $p_tax_coprice;
                $p_tax_free_price = 0;
            } else {
                $p_tax_total_price = 0;
                $p_tax_coprice = 0;
                $p_tax_price = 0;
                $p_tax_free_price = $ai["p_ac_price"];
            }

            $d_tax_total_price = $ai["d_ac_price"];
            $d_tax_coprice = round($d_tax_total_price / 1.1);
            $d_tax_price = $d_tax_total_price - $d_tax_coprice;

            $d_tax_free_price = 0;
            $total_price = $ai["ac_price"];


            $sql = "select * from shop_accounts_remittance where company_id ='" . $ai["company_id"] . "' and status='" . ORDER_STATUS_ACCOUNT_COMPLETE . "' ";
            $db->query($sql);
            if ($db->total) {
                $db->fetch();
                $ar_ix = $db->dt[ar_ix];

                $sql = "update shop_accounts_remittance set
                    p_sell_price=p_sell_price+'" . $ai["p_sell_price"] . "',
                    p_tax_coprice=p_tax_coprice+'" . $p_tax_coprice . "',
                    p_tax_price=p_tax_price+'" . $p_tax_price . "',
                    p_tax_total_price=p_tax_total_price+'" . $p_tax_total_price . "',
                    d_sell_price=d_sell_price+'" . $ai["d_sell_price"] . "',
                    d_tax_coprice=d_tax_coprice+'" . $d_tax_coprice . "',
                    d_tax_price=d_tax_price+'" . $d_tax_price . "',
                    d_tax_total_price=d_tax_total_price+'" . $d_tax_total_price . "',
                    p_tax_free_price=p_tax_free_price+'" . $p_tax_free_price . "',
                    d_tax_free_price=d_tax_free_price+'" . $d_tax_free_price . "',
                    total_price=total_price+'" . $total_price . "'
                where
                    ar_ix='" . $ar_ix . "' ";
                $db->query($sql);

            } else {
                $sql = "insert into shop_accounts_remittance (ar_ix,company_id,p_sell_price,p_tax_coprice,p_tax_price,p_tax_total_price,d_sell_price,d_tax_coprice,d_tax_price,d_tax_total_price,p_tax_free_price,d_tax_free_price,total_price,status,account_method,regdate) values ('','" . $ai["company_id"] . "','" . $ai["p_sell_price"] . "','$p_tax_coprice','$p_tax_price','$p_tax_total_price','" . $ai["d_sell_price"] . "','$d_tax_coprice','$d_tax_price','$d_tax_total_price','$p_tax_free_price','$d_tax_free_price','$total_price','" . ORDER_STATUS_ACCOUNT_COMPLETE . "','" . $ai["account_method"] . "',NOW())";
                $db->query($sql);

                $sql = "select ar_ix from shop_accounts_remittance where ar_ix = LAST_INSERT_ID() ";
                $db->query($sql);
                $db->fetch();

                $ar_ix = $db->dt[ar_ix];
            }

            $sql = "update shop_accounts set ar_ix='" . $ar_ix . "', status='" . ORDER_STATUS_ACCOUNT_COMPLETE . "' where ac_ix in ('" . implode("','", $ac_ix) . "') and company_id='" . $ai["company_id"] . "' ";
            $db->query($sql);

        }

        $sql = "update shop_order_detail set accounts_status='" . ORDER_STATUS_ACCOUNT_COMPLETE . "' where ac_ix in ('" . implode("','", $ac_ix) . "')";
        $db->query($sql);
    }
*/
}
?>