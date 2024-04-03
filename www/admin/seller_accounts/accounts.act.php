<?
include("../class/layout.class");
include("./accounts.lib.php");

$db = new Database;

if ($act == "account_ready"){//정산대기(정산확정)
	/*
	$sql = "select
				company_id, account_type, surtax_yorn, account_info, account_method,
				sum(case when refund_bool='Y' then -p_sell_price else p_sell_price end) as p_sell_price,
				sum(case when refund_bool='Y' then -p_expect_price else p_expect_price end) as p_expect_price,
				sum(case when refund_bool='Y' then -p_dc_allotment_price else p_dc_allotment_price end) as p_dc_allotment_price,
				sum(case when refund_bool='Y' then -p_fee_price else p_fee_price end) as p_fee_price,

				sum(case when refund_bool='Y' then -d_expect_price else d_expect_price end) as d_expect_price,
				sum(case when refund_bool='Y' then -d_dc_allotment_price else d_dc_allotment_price end) as d_dc_allotment_price
			from
			(
				select
					od.company_id, od.account_type, od.surtax_yorn, od.account_info, od.account_method,

					case when ".$AC_REFUND_QUERY." then 'Y' else 'N' end as refund_bool,

					pt_dcprice as p_sell_price,

					case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end as p_expect_price,
					
					(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) as p_dc_allotment_price,

					case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end as p_fee_price,

					(
						case when 
							".$AC_REFUND_QUERY."
						then
							case when 
								od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ori_company_id = od.ori_company_id and odr.delivery_type = od.delivery_type and odr.delivery_package = od.delivery_package and odr.delivery_method = od.delivery_method and odr.delivery_pay_method = od.delivery_pay_method and odr.delivery_addr_use = od.delivery_addr_use and odr.factory_info_addr_ix = od.factory_info_addr_ix and (case od.delivery_package when 'Y' then odr.pid=od.pid else 1=1 end) 
								and ".str_replace("od.","odr.",$AC_REFUND_QUERY)." and odr.od_ix in ('".implode("','",$od_ix)."'))
							then
								(
									odv.refund_delivery_price - odv.ac_refund_delivery_price
								) 
							else
								'0' 
							end
						else
							case when 
								od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ori_company_id = od.ori_company_id and odr.delivery_type = od.delivery_type and odr.delivery_package = od.delivery_package and odr.delivery_method = od.delivery_method and odr.delivery_pay_method = od.delivery_pay_method and odr.delivery_addr_use = od.delivery_addr_use and odr.factory_info_addr_ix = od.factory_info_addr_ix and (case od.delivery_package when 'Y' then odr.pid=od.pid else 1=1 end)
								and ".str_replace("od.","odr.",$AC_NORMARL_QUERY)." and odr.od_ix in ('".implode("','",$od_ix)."'))
							then
								(
									odv.delivery_price - odv.ac_delivery_price 
								) 
							else
								'0' 
							end
						end

					) as d_expect_price,
					
					(
						case when 
							".$AC_REFUND_QUERY."
						then
							'0'
						else
							case when 
								od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ori_company_id = od.ori_company_id and odr.delivery_type = od.delivery_type and odr.delivery_package = od.delivery_package and odr.delivery_method = od.delivery_method and odr.delivery_pay_method = od.delivery_pay_method and odr.delivery_addr_use = od.delivery_addr_use and odr.factory_info_addr_ix = od.factory_info_addr_ix and (case od.delivery_package when 'Y' then odr.pid=od.pid else 1=1 end)
								and ".str_replace("od.","odr.",$AC_NORMARL_QUERY)." and odr.od_ix in ('".implode("','",$od_ix)."'))
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
					".TBL_SHOP_ORDER." o on o.oid = od.oid
				left join shop_order_delivery odv on (
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
				where od.od_ix in ('".implode("','",$od_ix)."')
			) o
			group by o.company_id, o.account_type, o.surtax_yorn
		 " ;
	
	
	$db->query($sql);
	if($db->total){

		$ac_list = $db->fetchall("object");
		foreach($ac_list as $list){

			$ac_info=array();
			$ac_info[company_id]=$list[company_id];
			$ac_info[ac_date]=date('Y-m-d');
			$ac_info[ac_info]=$list[account_info];//정산 설정1 : 기간별 2:상품별
			$ac_info[ac_type]=$list[account_type];//정산방식 1:수수료 2:매입(공급가로 정산) 3:미정산
			$ac_info[account_method]=$list[account_method];//정산지급방식
			$ac_info[surtax_yorn]=$list[surtax_yorn];
			$ac_info[p_sell_price]=$list[p_sell_price];
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
				where
					od.od_ix in ('".implode("','",$od_ix)."')
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
				where
					od.od_ix in ('".implode("','",$od_ix)."')
				and
					od.company_id = '".$list[company_id]."'
				and
					od.account_type = '".$list[account_type]."'
				and
					od.surtax_yorn = '".$list[surtax_yorn]."'
				and
					".$AC_REFUND_QUERY." ";
			$db->query($sql);

			//배송비 업데이트
			$sql = "select
					case when od.refund_ac_ix = '".$ac_ix."' then 'Y' else 'N' end as refund_bool,
					(
						case when 
							od.refund_ac_ix = '".$ac_ix."'
						then
							case when 
								od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ori_company_id = od.ori_company_id and odr.delivery_type = od.delivery_type and odr.delivery_package = od.delivery_package and odr.delivery_method = od.delivery_method and odr.delivery_pay_method = od.delivery_pay_method and odr.delivery_addr_use = od.delivery_addr_use and odr.factory_info_addr_ix = od.factory_info_addr_ix and (case od.delivery_package when 'Y' then odr.pid=od.pid else 1=1 end) 
								and odr.refund_ac_ix = '".$ac_ix."' and odr.od_ix in ('".implode("','",$od_ix)."'))
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
								and odr.ac_ix = '".$ac_ix."' and odr.od_ix in ('".implode("','",$od_ix)."'))
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
								and odr.ac_ix = '".$ac_ix."' and odr.od_ix in ('".implode("','",$od_ix)."'))
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
				where 
					od.od_ix in ('".implode("','",$od_ix)."')
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
		}
	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 정산확정 되었습니다.');parent.document.location.reload();</script>";
	exit;
	*/

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('비정상적인 접근입니다.');parent.document.location.reload();</script>";
	exit;
}


if($act=="add_price_insert"){
	
	$sql="insert into shop_accounts_add_price (aap_ix,ac_ix,company_id,app_type,app_state,app_msg,app_price,regdate) 
	values ('','$ac_ix','$company_id','$app_type','$app_state','$app_msg','$app_price',NOW())";
	$db->query($sql);
	
	if($app_type=="D"){
		$where = " and app_type='D' ";
		$update_colum="d_add_price";
	}else{
		$where = " and app_type in ('C','P') ";
		$update_colum="p_add_price";
	}

	$sql="update  shop_accounts set 
			".$update_colum." = (select sum(case when app_state='2' then -app_price else app_price end) as sum_app_price from shop_accounts_add_price where ac_ix='".$ac_ix."' $where)
		where ac_ix='".$ac_ix."' ";
	$db->query($sql);

	$sql="update  shop_accounts set 
		p_ac_price = p_expect_price - p_dc_allotment_price - p_fee_price + p_add_price,
		d_ac_price = d_expect_price - d_dc_allotment_price + d_add_price,
		ac_price = (p_expect_price - p_dc_allotment_price - p_fee_price  + p_add_price) + (d_expect_price - d_dc_allotment_price + d_add_price)
	where ac_ix='".$ac_ix."' ";
	$db->query($sql);

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리 되었습니다.');parent.document.location.reload();parent.opener.document.location.reload();</script>";
	exit;
}


if($act=="account_complete"){

	$sql="select
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
			where ac_ix in ('".implode("','",$ac_ix)."') 
			group by ac.company_id, ac.surtax_yorn
	) a
	left join common_seller_delivery c on a.company_id = c.company_id ";

	$db->query($sql);
	
	if($db->total){
		$ac_info = $db->fetchall("object");

		foreach($ac_info as $ai){

			if($ai["surtax_yorn"]=="N"){
				$p_tax_total_price=$ai["p_ac_price"];
				$p_tax_coprice=round($p_tax_total_price/1.1);
				$p_tax_price=$p_tax_total_price-$p_tax_coprice;
				$p_tax_free_price=0;
			}else{
				$p_tax_total_price=0;
				$p_tax_coprice=0;
				$p_tax_price=0;
				$p_tax_free_price=$ai["p_ac_price"];
			}

			$d_tax_total_price=$ai["d_ac_price"];
			$d_tax_coprice=round($d_tax_total_price/1.1);
			$d_tax_price=$d_tax_total_price-$d_tax_coprice;
			
			$d_tax_free_price=0;
			$total_price=$ai["ac_price"];
		

			$sql="select * from shop_accounts_remittance where company_id ='".$ai["company_id"]."' and status='".ORDER_STATUS_ACCOUNT_COMPLETE."' and tax_ix = '0' ";
			$db->query($sql);
			if($db->total){
				$db->fetch();
				$ar_ix = $db->dt[ar_ix];

				$sql="update shop_accounts_remittance set
					p_sell_price=p_sell_price+'".$ai["p_sell_price"]."',
					p_tax_coprice=p_tax_coprice+'".$p_tax_coprice."',
					p_tax_price=p_tax_price+'".$p_tax_price."',
					p_tax_total_price=p_tax_total_price+'".$p_tax_total_price."',
					d_sell_price=d_sell_price+'".$ai["d_sell_price"]."',
					d_tax_coprice=d_tax_coprice+'".$d_tax_coprice."',
					d_tax_price=d_tax_price+'".$d_tax_price."',
					d_tax_total_price=d_tax_total_price+'".$d_tax_total_price."',
					p_tax_free_price=p_tax_free_price+'".$p_tax_free_price."',
					d_tax_free_price=d_tax_free_price+'".$d_tax_free_price."',
					total_price=total_price+'".$total_price."'
				where
					ar_ix='".$ar_ix."' ";
				$db->query($sql);

			}else{
				$sql="insert into shop_accounts_remittance (ar_ix,company_id,p_sell_price,p_tax_coprice,p_tax_price,p_tax_total_price,d_sell_price,d_tax_coprice,d_tax_price,d_tax_total_price,p_tax_free_price,d_tax_free_price,total_price,status,account_method,regdate) values ('','".$ai["company_id"]."','".$ai["p_sell_price"]."','$p_tax_coprice','$p_tax_price','$p_tax_total_price','".$ai["d_sell_price"]."','$d_tax_coprice','$d_tax_price','$d_tax_total_price','$p_tax_free_price','$d_tax_free_price','$total_price','".ORDER_STATUS_ACCOUNT_COMPLETE."','".$ai["account_method"]."',NOW())";
				$db->query($sql);
				
				$sql="select ar_ix from shop_accounts_remittance where ar_ix = LAST_INSERT_ID() ";
				$db->query($sql);
				$db->fetch();

				$ar_ix = $db->dt[ar_ix];
			}

			$sql="update shop_accounts set ar_ix='".$ar_ix."', status='".ORDER_STATUS_ACCOUNT_COMPLETE."' where ac_ix in ('".implode("','",$ac_ix)."') and company_id='".$ai["company_id"]."' ";
			$db->query($sql);

		}

		$sql="update shop_order_detail set accounts_status='".ORDER_STATUS_ACCOUNT_COMPLETE."' where ac_ix in ('".implode("','",$ac_ix)."')";
		$db->query($sql);
	}

	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리 되었습니다.');parent.document.location.reload();</script>";
	exit;
}


if($act=="account_payment"){
	
	$sql="select * from shop_accounts_remittance where ar_ix in ('".implode("','",$ar_ix)."') ";
	$db->query($sql);
	if($db->total){
		$ar_info = $db->fetchall("object");
		

		foreach($ar_info as $ai){
			$sql="update shop_accounts_remittance set status='".ORDER_STATUS_ACCOUNT_PAYMENT."' , ap_date=NOW() where ar_ix='".$ai[ar_ix]."' ";
			$db->query($sql);
		}
	}
	echo "<script language='javascript' src='../js/message.js.php'></script><script>show_alert('정상적으로 처리 되었습니다.');parent.document.location.reload();</script>";
	exit;
}
?>