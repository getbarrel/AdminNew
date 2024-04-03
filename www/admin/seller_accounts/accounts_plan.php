<?

/////////////////////////////////////////////////////////////
/*

입점배송방식  : 위탁 (통합) - 배송비 정산하지 않는다		delivery_type = 1
                입점 (개별발송) - 배송비 정산			delivery_type = 2

입점정산방식 : 중개 (수수료률) 정산기준금액 (최종판매가)	account_type= 1	ptprice
				    매입  정산기준금액 (공급가)			account_type = 2	coprice
					  선매입(미정산) : 정산에 반영하지 않는다 		account_type = 3

과세구분 : 과세	surtax_yorn = N
			  면세 surtax_yorn = Y
			  영세.(정산에는 영향이 미치지 않느다. )	surtax_yorn = P


정산 수수료 : 상품별 개별수수료 잇을경우 우선 순위 사용
              상품별 개별수수료 없을경우 셀러관리 수수료률 사용   현재 프로세스가 적용되어 있음


//and odv.delivery_type != '1' 통합배송은 정산에 반영이 안된다!

*/
/////////////////////////////////////////////////////////////


include("../class/layout.class");
include ("./accounts.lib.php");

$Script = "
<script language='javascript'>

</script>";

if($_COOKIE[accounts_plan_limit]){
	$max = $_COOKIE[accounts_plan_limit]; //페이지당 갯수
}else{
	$max = 15;
}

//$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}


$db = new Database;

$sql="select company_id from ".TBL_COMMON_COMPANY_DETAIL." where com_type='A' ";
$db->query($sql);
$db->fetch();
$admin_com_id = $db->dt[company_id];


$where="
where od.product_type NOT IN (".implode(',',$sns_product_type).") 
and od.company_id != '".$admin_com_id."'
";

$sub_where="
and odr.product_type NOT IN (".implode(',',$sns_product_type).") 
and odr.company_id != '".$admin_com_id."' ";


if($admininfo[admin_level] == 9){
	if($company_id != "") $where .= " and od.company_id='$company_id' ";

	if($admininfo[mem_type] == "MD"){
		$where .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
	}

}else if($admininfo[admin_level] == 8){
	$where .= " and od.company_id = '".$admininfo[company_id]."' ";
}

if($mode == "search"){
	if($check_search_date){
		if($date_type =="accounts_expect_date"){
			$where .= " and  DATE_FORMAT(DATE_ADD(case od.ac_delivery_type when '".ORDER_STATUS_INCOM_COMPLETE."' then od.ic_date when '".ORDER_STATUS_DELIVERY_READY."' then od.dr_date when '".ORDER_STATUS_DELIVERY_ING."' then od.di_date when '".ORDER_STATUS_DELIVERY_COMPLETE."' then od.dc_date when '".ORDER_STATUS_BUY_FINALIZED."' then od.bf_date else od.dc_date end,INTERVAL od.ac_expect_date DAY),'%Y-%m-%d') between '".$startDate."' and '".$endDate."' ";
		}else{
			$where .= " and  date_format( ".$date_type." ,'%Y-%m-%d') between  '".$startDate."' and '".$endDate."' ";
		}
	}
}

if($search_type && $search_text){
	//$where .= "and $search_type = '".trim($search_text)."' ";
}

if(is_array($ac_type)){
	for($i=0;$i < count($ac_type);$i++){
		if($ac_type[$i] != ""){
			if($ac_type_str == ""){
				$ac_type_str .= "'".$ac_type[$i]."'";
			}else{
				$ac_type_str .= ",'".$ac_type[$i]."' ";
			}
		}
	}

	if($ac_type_str != ""){
		$where .= "and od.account_type in ($ac_type_str) ";
	}
}else{
	if($ac_type){
		$where .= "and od.account_type = '$ac_type' ";
	}
}

if(is_array($surtax_yorn)){
	for($i=0;$i < count($surtax_yorn);$i++){
		if($surtax_yorn[$i] != ""){
			if($surtax_yorn_str == ""){
				$surtax_yorn_str .= "'".$surtax_yorn[$i]."'";
			}else{
				$surtax_yorn_str .= ",'".$surtax_yorn[$i]."' ";
			}
		}
	}

	if($surtax_yorn_str != ""){
		$where .= "and od.surtax_yorn in ($surtax_yorn_str) ";
	}
}else{
	if($surtax_yorn){
		$where .= "and od.surtax_yorn = '$surtax_yorn' ";
	}
}

if(is_array($account_method)){
	for($i=0;$i < count($account_method);$i++){
		if($account_method[$i] != ""){
			if($account_method_str == ""){
				$account_method_str .= "'".$account_method[$i]."'";
			}else{
				$account_method_str .= ",'".$account_method[$i]."' ";
			}
		}
	}

	if($account_method_str != ""){
		$where .= "and od.account_method in ($account_method_str) ";
	}

}else{
	if($account_method){
		$where .= "and od.account_method = '$account_method' ";
	}
}

//다중검색으로 추가
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
				$search_array = explode("\n",$search_text);
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

//echo "$where";

//account_info 정산 설정1 : 기간별 2:상품별
if($pre_type=="product"){
	$title = "상품별 예정리스트";
	$where .= " 
	and od.account_info = '2'
	and od.account_type !='3' ";

	$sub_where .= " 
	and odr.account_info = '2'
	and odr.account_type !='3' ";

}else{
	$title = "기간별 예정리스트";

	$where .= " 
	and od.account_info = '1'
	and od.account_type !='3' ";

	$sub_where .= " 
	and odr.account_info = '1'
	and odr.account_type !='3' ";
}

/*
$sql = "select
			o.oid
		from
			".TBL_SHOP_ORDER_DETAIL." od
		left join
			".TBL_SHOP_ORDER." o on o.oid = od.oid
		$where";
*/
$sql = "
	select count(*) as total from (
		select
			od.oid,od.od_ix,'N' as refund_bool
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
		".$search_where."
		and
		(
			".$AC_NORMARL_QUERY."
			
		)

		union all

		select
			od.oid,od.od_ix,'Y' as refund_bool
		from
			".TBL_SHOP_ORDER_DETAIL." od
		left join
			".TBL_SHOP_ORDER." o on o.oid = od.oid
		left join shop_order_claim_delivery ocd on (
			ocd.oid=od.oid
			and ocd.company_id=od.company_id
			and ocd.claim_group=od.claim_group
			and ocd.ac_ix='0' 
			and ocd.ac_target_yn='Y' 
			and ocd.delivery_type != '1'
		)

		".$where."
		".$search_where."
		and
		(
			".$AC_REFUND_QUERY."
		)
	) a
";

$db->query($sql);
$db->fetch();
$total = $db->dt[total];


//echo $total;
//exit;
/*
$sql = "select
				o.*,c.com_name
			from
			(
				select
					o.bname,od.oid,od.od_ix,od.company_id,

					'N' as refund_bool,

					pt_dcprice as p_sell_price,

					case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end as p_expect_price,
					
					(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) as p_dc_allotment_price,

					case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end as p_fee_price,

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

					) as d_dc_allotment_price,

					DATE_FORMAT(DATE_ADD(case od.ac_delivery_type when '".ORDER_STATUS_INCOM_COMPLETE."' then od.ic_date when '".ORDER_STATUS_DELIVERY_READY."' then od.dr_date when '".ORDER_STATUS_DELIVERY_ING."' then od.di_date when '".ORDER_STATUS_DELIVERY_COMPLETE."' then od.dc_date when '".ORDER_STATUS_BUY_FINALIZED."' then od.bf_date else od.dc_date end,INTERVAL od.ac_expect_date DAY),'%Y-%m-%d') as accounts_expect_date
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

				union all

				select
					o.bname,od.oid,od.od_ix,od.company_id,

					'Y' as refund_bool,

					pt_dcprice as p_sell_price,

					case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end as p_expect_price,
					
					(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) as p_dc_allotment_price,

					case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end as p_fee_price,

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
					'0' as d_dc_allotment_price,

					DATE_FORMAT(DATE_ADD(case od.ac_delivery_type when '".ORDER_STATUS_INCOM_COMPLETE."' then od.ic_date when '".ORDER_STATUS_DELIVERY_READY."' then od.dr_date when '".ORDER_STATUS_DELIVERY_ING."' then od.di_date when '".ORDER_STATUS_DELIVERY_COMPLETE."' then od.dc_date when '".ORDER_STATUS_BUY_FINALIZED."' then od.bf_date else od.dc_date end,INTERVAL od.ac_expect_date DAY),'%Y-%m-%d') as accounts_expect_date
				from
					".TBL_SHOP_ORDER_DETAIL." od
				left join
					".TBL_SHOP_ORDER." o on o.oid = od.oid
				left join shop_order_claim_delivery ocd on (
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

				limit $start,$max
			) o
			left join ".TBL_COMMON_COMPANY_DETAIL." c on o.company_id = c.company_id" ;
*/

if($mode != "excel"){
	//$limit = " limit $start,$max "; 
}

$sql = "select
				o.*,c.com_name,od.*,

				pt_dcprice as p_sell_price,

				case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end as p_expect_price,
				
				(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) as p_dc_allotment_price,

				ROUND(case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end) as p_fee_price,

				(
					case when
						o.refund_bool='Y'
					then
						case when 
							od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.claim_group = od.claim_group and ".str_replace("od.","odr.",$AC_REFUND_QUERY)." ".$sub_where.")
						then
							(
								ocd.delivery_price
							) 
						else
							'0' 
						end
					else 
						case when 
							od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix = od.ode_ix and ".str_replace("od.","odr.",$AC_NORMARL_QUERY)." ".$sub_where.")
						then
							(
								odv.delivery_price
							) 
						else
							'0' 
						end
					end

				) as d_expect_price,
				(
					case when
						o.refund_bool='Y'
					then
						'0'
					else 
						case when 
							od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ode_ix = od.ode_ix and ".str_replace("od.","odr.",$AC_NORMARL_QUERY)." ".$sub_where.")
						then
							(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.ode_ix=odv.ode_ix and dc.dc_type in ('DCP','DE')) 
						else
							'0' 
						end
					end

				) as d_dc_allotment_price,

				DATE_FORMAT(DATE_ADD(case od.ac_delivery_type when '".ORDER_STATUS_INCOM_COMPLETE."' then od.ic_date when '".ORDER_STATUS_DELIVERY_READY."' then od.dr_date when '".ORDER_STATUS_DELIVERY_ING."' then od.di_date when '".ORDER_STATUS_DELIVERY_COMPLETE."' then od.dc_date when '".ORDER_STATUS_BUY_FINALIZED."' then od.bf_date else od.dc_date end,INTERVAL od.ac_expect_date DAY),'%Y-%m-%d') as accounts_expect_date

			from
			(
				select
					o.bname,od.oid,od.od_ix,'N' as refund_bool
				from
					".TBL_SHOP_ORDER_DETAIL." od
				left join
					".TBL_SHOP_ORDER." o on o.oid = od.oid
				".$where."
				and
				(
					
					".$AC_NORMARL_QUERY."
					
				)

				UNION ALL

				select
					o.bname,od.oid,od.od_ix,'Y' as refund_bool
				from
					".TBL_SHOP_ORDER_DETAIL." od
				left join
					".TBL_SHOP_ORDER." o on o.oid = od.oid
				".$where."
				and
				(
					".$AC_REFUND_QUERY."
				)

				order by oid desc
				limit $start,$max 
			) o
			left join
					".TBL_SHOP_ORDER_DETAIL." od on o.od_ix = od.od_ix
			left join 
					shop_order_delivery odv on (
				odv.oid=od.oid
				and odv.ode_ix = od.ode_ix
				and odv.delivery_type != '1'
				and odv.delivery_pay_type = '1'
				and odv.ac_ix = '0'
			)
			left join
					shop_order_claim_delivery ocd on (
				ocd.oid=od.oid
				and ocd.company_id=od.company_id
				and ocd.claim_group=od.claim_group
				and ocd.ac_ix='0' 
				and ocd.ac_target_yn='Y' 
				and ocd.delivery_type != '1'
			)
			
			left join ".TBL_COMMON_COMPANY_DETAIL." c on od.company_id = c.company_id
			
		where
			1
			$search_where" ;
$db->query($sql);

if($mode == "excel"){

	$goods_infos = $db->fetchall();
	$info_type = "accounts_plan";
	include("excel_out_columsinfo.php");
	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='account_list_".$info_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel = $db->dt[conf_val];

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='check_account_list_".$info_type."' ";

	$db->query($sql);
	$db->fetch();
	$stock_report_excel_checked = $db->dt[conf_val];

	$check_colums = unserialize(stripslashes($stock_report_excel_checked));

	$columsinfo = $colums;

	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$inventory_excel = new PHPExcel();

	// 속성 정의
	$inventory_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts plan price List")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");
	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
		$col++;
	}

	$before_pid = "";

	for ($i = 0; $i < count($goods_infos); $i++)
	{
		if($goods_infos[$i][refund_bool]=="Y"){
			$sign = -1;
		}else{
			$style='';
			$sign = 1;
		}

		$j="A";
		foreach($check_colums as $key => $value){
			if($key == "surtax_yorn"){
				switch($goods_infos[$i][surtax_yorn]){
					case "N":
						$value_str="과세";
						break;
					case "Y":
						$value_str="면세";
						break;
					case "P":
						$value_str="영세";
						break;
					default:
						$value_str="-";
						break;
				}

			}else if($key == "account_type"){

				switch($goods_infos[$i][account_type]){
					case "1":
						$value_str ="수수료";
						break;
					case "2":
						$value_str = "매입";
						break;
					case "":
						$value_str ="수수료";
						break;
					default:
						$value_str="-";
						break;
				}

			}else if($key == "pname"){
				if($goods_infos[$i][product_type]=='99'||$goods_infos[$i][product_type]=='21'||$goods_infos[$i][product_type]=='31'){
					$value_str = "<b class='".($goods_infos[$i][product_type]=='99' ? "red" : "blue")."' >".$goods_infos[$i][pname]."</b><br/><strong>".$goods_infos[$i][set_name]."<br /></strong>".$goods_infos[$i][sub_pname];
				}else{
					$value_str = $goods_infos[$i][pname];
				}
			}else if($key == "status"){
				$value_str = strip_tags(getOrderStatus($goods_infos[$i][status]));
			}else if($key == "p_expect_price"){
				$value_str = $goods_infos[$i][p_expect_price]*$sign;
			}else if($key == "p_fee_price"){
				$value_str = $goods_infos[$i][p_fee_price]*$sign;
			}else if($key == "p_ac_price"){
				$value_str = (($goods_infos[$i][p_expect_price] - $goods_infos[$i][p_dc_allotment_price]) - $goods_infos[$i][p_fee_price])*$sign;
			}else if($key == "d_expect_price"){
				$value_str = $goods_infos[$i][d_expect_price]*$sign;
			}else if($key == "d_dc_allotment_price"){
				$value_str = $goods_infos[$i][d_dc_allotment_price]*$sign;
			}else if($key == "d_ac_price"){
				$value_str = ($goods_infos[$i][d_expect_price] - $goods_infos[$i][d_dc_allotment_price])*$sign;
			}else if($key == "account_method"){
				$value_str = getMethodStatus($goods_infos[$i][account_method]);
			}else if($key == "accounts_status"){
				$value_str = '정산예정';
			}elseif($key=="option_text"){
                $value_str=strip_tags($goods_infos[$i][option_text]);
            }else{
				$value_str = $goods_infos[$i][$value];//$db1->dt[$value];
			}

			$inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
			$j++;

			unset($history_text);
		}
		$z++;
	}
	// 첫번째 시트 선택
	$inventory_excel->setActiveSheetIndex(0);

	// 너비조정
	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		$col++;
	}

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="account_list_'.$info_type.'.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter->save('php://output');

	exit;
}


/*
if($mode == "excel"){

header( "Content-type: application/vnd.ms-excel" );
header( "Content-Disposition: attachment; filename=account_list_".date("Y-m-d").".xls" );
header( "Content-Description: Generated Data" );

	if($db->total){
		//echo "NO\t업체명\t은행명\t계좌번호\t입금자명\t판매건수\t판매수량\t배송비\t판매총액(할인가기준)\t수수료\t정산금액\n";
		$mstring = "NO\t업체명\t현금\t카드\t배송비\t정산기준금액\t정산수수료\t정산금액\n";
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);

			$mstring .= ($i+1)."\t".$db->dt[com_name]."\t".$db->dt[bank_ptprice]."\t ".$db->dt[card_ptprice]." \t".$db->dt[shipping_price]."\t".$db->dt[sell_total_ptprice]."\t".$db->dt[commission_price]."\t".($db->dt[shipping_price]+$db->dt[sell_total_ptprice]-$db->dt[commission_price])."\n";

		}
	}

	echo iconv("utf-8","CP949",$mstring);
	exit;
}*/

$Contents = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<!--col width='25%' />
	<col width='25%' />
	<col width='25%' />
	<col width='25%' /-->
	<tr>
		<td align='left' colspan=4>".GetTitleNavigation("$title", "셀러정산 > $title")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding:10px 0px 4px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:3px;'><img src='../image/title_head.gif' align=absmiddle><b> 정산예정검색</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	</table>
	<form name='search_frm' method='get' >
	<input type='hidden' name='pre_type' value='$pre_type'>
	<input type='hidden' name='mode' value='search'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
	<col width='18%' />
	<col width='32%' />
	<col width='18%' />
	<col width='32%' />
	<tr height=30>
		<td class='search_box_title'><!--label for='regdate'>주문등록일자</label-->
			<select name='date_type'>
				<option value='od.ic_date' ".CompareReturnValue('od.ic_date',$date_type,' selected').">입금확인일자</option>
				<option value='o.order_date' ".CompareReturnValue('o.order_date',$date_type,' selected').">주문일자</option>
				<option value='accounts_expect_date' ".CompareReturnValue('accounts_expect_date',$date_type,' selected').">정산예정일</option>
			</select>
			<input type='checkbox' name='check_search_date' id='check_search_date' value='1' onclick='ChangeOrderDate(document.search_frm);' ".CompareReturnValue("1",$check_search_date,"checked").">
		</td>
		<td class='search_box_item' colspan=3 >
			".search_date('startDate','endDate',$startDate,$endDate)."
		</td>
	</tr>
	<tr height=30>
		<td class='search_box_title'>정산방식  </td>
		<td class='search_box_item'>
			<input type='checkbox' name='ac_type[]' id='ac_type_1' value='1' ".CompareReturnValue("1",$ac_type,' checked')." ><label for='ac_type_1'>수수료</label>&nbsp;
			<!--<input type='checkbox' name='ac_type[]' id='ac_type_2' value='2' ".CompareReturnValue("2",$ac_type,' checked')." ><label for='ac_type_2'>매입(공급가정산)</label>-->
		</td>
		<td class='search_box_title'>과세여부 </td>
		<td class='search_box_item'>
			<input type='checkbox' name='surtax_yorn[]' id='surtax_yorn_n' value='N' ".CompareReturnValue("N",$surtax_yorn,' checked')." ><label for='surtax_yorn_n'>과세</label>&nbsp;
			<input type='checkbox' name='surtax_yorn[]' id='surtax_yorn_y' value='Y' ".CompareReturnValue("Y",$surtax_yorn,' checked')." ><label for='surtax_yorn_y'>면세</label>&nbsp;
			<!--input type='checkbox' name='surtax_yorn[]' id='surtax_yorn_p' value='P' ".CompareReturnValue("P",$surtax_yorn,' checked')." ><label for='surtax_yorn_p'>영세</label-->
		</td>
	</tr>";

if($admininfo[admin_level] == 9){
	$Contents .= "
	<tr height=30>
		<td class='search_box_title'>지급방식  </td>
		<td class='search_box_item'>
			<input type='checkbox' name='account_method[]' id='account_method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue("1",$account_method,' checked')." ><label for='account_method_".ORDER_METHOD_CASH."'>".getMethodStatus(ORDER_METHOD_CASH)."</label>&nbsp;
			<!--<input type='checkbox' name='account_method[]' id='account_method_".ORDER_METHOD_SAVEPRICE."' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue("2",$account_method,' checked')." ><label for='account_method_".ORDER_METHOD_SAVEPRICE."'>".getMethodStatus(ORDER_METHOD_SAVEPRICE)."</label>-->
		</td>
		<td class='search_box_title'>셀러명  </td>
		<td class='search_box_item'>".CompanyList($company_id,"","")."</td>
	</tr>

	
	<tr>
		<td class='search_box_title'>  주문번호
		<!--<span style='padding-left:2px' class='helpcloud' help_width='220' help_height='30' help_html='검색하시고자 하는 값을 ,(콤마) 구분으로 넣어서 검색 하실 수 있습니다'><img src='/admin/images/icon_q.gif' align=absmiddle/></span>
		
		<input type='checkbox' name='mult_search_use' id='mult_search_use' value='1' ".($mult_search_use == '1'?'checked':'')." title='다중검색 체크'> 
		<label for='mult_search_use'>(다중검색 체크)</label>-->
		</td>
		<td class='search_box_item' colspan='3'>
			<table cellpadding=0 cellspacing=0 border='0'>
			<tr>
				<td valign='top'>
					<div style='padding-top:5px;'>
					<!--<select name='search_type' id='search_type' style=\"font-size:12px;\">
						<option value='od.oid' ".CompareReturnValue("od.oid",$search_type).">주문번호</option>
						<option value='c.com_name' ".CompareReturnValue("c.com_name",$search_type).">셀러명</option>
					</select>-->
					</div>
				</td>
				<td style='padding:5px;'>
					<div id='search_text_input_div'>
						<input id=search_texts class='textbox1' value='".$search_text."' autocomplete='off' clickbool='false' style='WIDTH: 210px; height:18px; COLOR: #736357; BACKGROUND-COLOR: #ffffff' name=search_text validation=false  title='검색어'>
					</div>
					<div id='search_text_area_div' style='display:none;'>
						<textarea name='search_text' name='search_text_area' id='search_text_area' class='tline textbox' style='padding:0px;height:90px;width:150px;' >".$search_text."</textarea>
					</div>
				</td>
				<td>
					<div>
						<!--<span class='small blu' > * 다중 검색은 다중 아이디로 검색 지원이 가능합니다. 구분값은 ',' 혹은 'Enter'로 사용 가능합니다. </span>-->
					</div>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	";
}else{
	$Contents .= "
	<tr height=30>
		<td class='search_box_title'>검색항목 </td>
		<td class='search_box_item'>
			<table cellpadding='3' cellspacing='0' border='0' width='100%'>
			<col width='200px'>
			<tr>
				<td >
				<select name='search_type' style='font-size:12px;'>
					<option value='combi_name' ".CompareReturnValue('combi_name',$search_type,' selected').">주문자이름+입금자명+수취인명</option>
					<option value='bname' ".CompareReturnValue('bname',$search_type,' selected').">주문자이름</option>
					<option value='pname' ".CompareReturnValue('pname',$search_type,' selected').">상품이름</option>
					<option value='od.oid' ".CompareReturnValue('od.oid',$search_type,' selected').">주문번호</option>
					<option value='rname' ".CompareReturnValue('rname',$search_type,' selected').">수취인이름</option>
					<option value='bmobile' ".CompareReturnValue('bmobile',$search_type,' selected').">주문자핸드폰</option>
					<option value='rmobile' ".CompareReturnValue('rmobile',$search_type,' selected').">수취인핸드폰</option>
					<option value='deliverycode' ".CompareReturnValue('deliverycode',$search_type,' selected').">송장번호</option>
				</select>
				</td>
				<td width='*'><input type='text' class=textbox name='search_text' size='30' value='$search_text' style='' ></td>
			</tr>
			</table>
		</td>
		<td class='search_box_title'>지급방식  </td>
		<td class='search_box_item'>
			<input type='checkbox' name='account_method[]' id='account_method_".ORDER_METHOD_CASH."' value='".ORDER_METHOD_CASH."' ".CompareReturnValue("1",$account_method,' checked')." ><label for='account_method_".ORDER_METHOD_CASH."'>".getMethodStatus(ORDER_METHOD_CASH)."</label>&nbsp;
			<input type='checkbox' name='account_method[]' id='account_method_".ORDER_METHOD_SAVEPRICE."' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue("2",$account_method,' checked')." ><label for='account_method_".ORDER_METHOD_SAVEPRICE."'>".getMethodStatus(ORDER_METHOD_SAVEPRICE)."</label>
		</td>
	</tr>";
}

	$Contents .= "
	</table>
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' >
	<tr bgcolor=#ffffff height='100'>
		<td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0 style='cursor:pointer;border:0px;' ></td>
	</tr>
	<tr>
		<td align='right' colspan=4><!--a href='accounts_plan_price_excel2003.php?mode=excel&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a--></td>
	</tr>
	</table>
	</form>

	<table border='0' cellpadding='0' cellspacing='0' width='100%'>
	<col width=10%>
	<col width='*'>
	<col width=10%>

	<tr>
		<td >
			<img src='../images/dot_org.gif' align=absmiddle> <b>정산예정검색</b>
		</td>
		<td align=right>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	$Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
					<a href='excel_config.php?".$QUERY_STRING."&info_type=accounts_plan&excel_type=account_list' rel='facebox' >
					<img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
}else{
	$Contents .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
}
	$Contents .= "&nbsp;";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
	$Contents .= "<a href='accounts_plan.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
}else{
	$Contents .= "<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
}

$Contents .= "
	</td>
	<td align=right>
	목록수 : <select name='max' id='max'>
				<option value='5' ".($_COOKIE[accounts_plan_limit] == '5'?'selected':'').">5</option>
				<option value='10' ".($_COOKIE[accounts_plan_limit] == '10'?'selected':'').">10</option>
				<option value='20' ".($_COOKIE[accounts_plan_limit] == '20'?'selected':'').">20</option>
				<option value='30' ".($_COOKIE[accounts_plan_limit] == '30'?'selected':'').">30</option>
				<option value='50' ".($_COOKIE[accounts_plan_limit] == '50'?'selected':'').">50</option>
				<option value='100' ".($_COOKIE[accounts_plan_limit] == '100'?'selected':'').">100</option>
				<option value='500' ".($_COOKIE[accounts_plan_limit] == '500'?'selected':'').">500</option>
				<option value='1000' ".($_COOKIE[accounts_plan_limit] == '1000'?'selected':'').">1000</option>
				<option value='1500' ".($_COOKIE[accounts_plan_limit] == '1500'?'selected':'').">1500</option>
				<option value='2000' ".($_COOKIE[accounts_plan_limit] == '2000'?'selected':'').">2000</option>
			</select>
	</td>
	</tr>
	</table>

	<form name=listform method=post action='accounts.act.php' onsubmit=\"return account(this)\" target='act'>
	<input type=hidden id='od_ix' value=''>

	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' >
	<tr bgcolor=#ffffff >
		<td colspan=4 align=right>
			<div style='width:100%;height:350px;overflow-y:scroll;overflow-x:scroll;position:relative;' id='scroll_div'>
			<table width='200%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' style='position:absolute;top:0px;margin-top:0px;' id='scroll_title'>
				<col width='30px'>
				<col width='7%'>
				<col width='3%'>
				<col width='6%'>
				<col width='3%'>
				<col width='3%'>
				<col width='*'>
				<col width='6%'>
				<col width='3%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='4%'>
				<col width='4%'>
				<col width='4%'>
				<tr height='25'>
					<td class='s_td' align='center'  rowspan='2'><input type=checkbox  name='all_fix2' onclick='fixAll(document.listform)'></td>
					<td align='center' class='m_td' rowspan='2'><b>주문일자/주문번호</b></td>
					<td align='center' class='m_td' rowspan='2'><b>주문자명</b></td>
					<td align='center' class='m_td' rowspan='2'><b>셀러명</b></td>
					<td align='center' class='m_td' rowspan='2'><b>정산방식</b></td>
					<td align='center' class='m_td' rowspan='2'><b>과세여부</b></td>
					<td align='center' class='m_td' rowspan='2' nowrap><b>상품명</b></td>
					<td align='center' class='m_td' rowspan='2' ><b>옵션</b></td>
					<td align='center' class='m_td' rowspan='2'><b>수량</b></td>
					<td align='center' class='m_td' rowspan='2'><b>배송처리상태</b></td>
					<td align='center' class='m_td' colspan='4'><b>상품주문금액</b></td>
					<td align='center' class='m_td' colspan='3'><b>배송비</b></td>
					<td align='center' class='m_td' rowspan='2'><b>실정산합계</b></td>
					<td align='center' class='m_td' rowspan='2'><b>정산예정일</b></td>
					<td align='center' class='m_td' rowspan='2'><b>정산상태</b></td>
					<td align='center' class='m_td' rowspan='2'><b>정산지급방식</b></td>
				</tr>
				<tr height='25' >
					<td align='center' class='m_td' ><b>정산예정금액(+)</b></td>
					<td align='center' class='m_td' ><b>할인부담금액(-)</b></td>
					<td align='center' class='m_td' ><b>수수료(-)</b></td>
					<td align='center' class='m_td' ><b>실정산금액</b></td>
					<td align='center' class='m_td' ><b>배송비(+)</b></td>
					<td align='center' class='m_td' ><b>할인부담금액(-)</b></td>
					<td align='center' class='m_td' ><b>실정산금액</b></td>
				</tr>
			</table>
			<table width='200%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' id='scroll_list'>
				<col width='30px'>
				<col width='7%'>
				<col width='3%'>
				<col width='6%'>
				<col width='3%'>
				<col width='3%'>
				<col width='*'>
				<col width='6%'>
				<col width='3%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='5%'>
				<col width='4%'>
				<col width='4%'>
				<col width='4%'>";

	if($db->total){
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);

				if($db->dt[refund_bool]=="Y"){
					$style='style="background-color:#FFF5F5"';
					$sign = -1;
				}else{
					$style='';
					$sign = 1;
				}

				$Contents .= "
				<tr onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\" height=30>
					<td class='list_box_td'  align='center' ".$style."><input type=checkbox name='od_ix[]' id='od_ix' value='".$db->dt[od_ix]."'></td>
					<td class='list_box_td list_bg_gray' align='center' ".$style.">
						".$db->dt[regdate]."<br><a href=\"../order/orders.read.php?oid=".$db->dt[oid]."\" style='color:#007DB7;font-weight:bold;' class='small' target='_blank'>".$db->dt[oid]."</a>
					</td>
					<td class='list_box_td ' align='center' ".$style.">".$db->dt[bname]."</td>
					<td class='list_box_td list_bg_gray'  align='center' ".$style.">".$db->dt[com_name]."</td>";

					if($db->dt[account_type]==1||$db->dt[account_type]==''){
						$account_type="수수료";
					}elseif($db->dt[account_type]==2){
						$account_type="매입";
					}else{
						$account_type="-";
					}

					$Contents .= "
					<td class='list_box_td '  align='center' ".$style.">".$account_type."</td>";

					if($db->dt[surtax_yorn]=='N'){
						$surtax_yorn="과세";
					}elseif($db->dt[surtax_yorn]=='Y'){
						$surtax_yorn="면세";
					}elseif($db->dt[surtax_yorn]=='P'){
						$surtax_yorn="영세";
					}else{
						$surtax_yorn="-";
					}

					$Contents .= "
					<td class='list_box_td list_bg_gray'  align='center' ".$style.">".$surtax_yorn."</td>
					<td class='list_box_td' ".$style.">
						<div style='text-align:left;padding-left:3px;'>";
						
						if($db->dt[product_type]=='99'||$db->dt[product_type]=='21'||$db->dt[product_type]=='31'){
							$Contents .= "<b class='".($db->dt[product_type]=='99' ? "red" : "blue")."' >".$db->dt[pname]."</b><br/><strong>".$db->dt[set_name]."<br /></strong>".$db->dt[sub_pname];
						}else{
							$Contents .= $db->dt[pname];
						}
			
					$Contents .= "
						</div>
					</td>
					<td class='list_box_td list_bg_gray' ".$style."><div style='text-align:left;padding-left:3px;'>".strip_tags($db->dt[option_text])."</div></td>
					<td class='list_box_td '  align='center' ".$style.">".$db->dt[pcnt]."</td>
					<td class='list_box_td list_bg_gray'  align='center' ".$style.">".getOrderStatus($db->dt[status])."</td>


					<td class='list_box_td' align='center' ".$style.">".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_expect_price]*$sign)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td list_bg_gray' align='center'  ".$style.">".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_dc_allotment_price]*$sign)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td'  align='center' ".$style.">".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_fee_price]*$sign)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";

					$p_ac_price = (($db->dt[p_expect_price] - $db->dt[p_dc_allotment_price]) - $db->dt[p_fee_price])*$sign;
					
					//배송비는 차감해야할 금액도 모두 한꺼번에 계산하므로 -표시 필요 없음!
					$Contents .= "
					<td class='list_box_td list_bg_gray'  align='center' ".$style.">".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($p_ac_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>
					<td class='list_box_td' align='center' ".$style.">".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[d_expect_price]*$sign)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td list_bg_gray'  align='center' ".$style.">".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[d_dc_allotment_price]*$sign)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";


					$d_ac_price = ($db->dt[d_expect_price] - $db->dt[d_dc_allotment_price])*$sign;

					$Contents .= "
					<td class='list_box_td' align='center' ".$style.">".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($d_ac_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";

					$ac_price = $p_ac_price + $d_ac_price;

					$Contents .= "
					<td class='list_box_td point'  align='center' ".$style.">".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ac_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>
					<td class='list_box_td' align='center' ".$style.">".$db->dt[accounts_expect_date]."</td>
					<td class='list_box_td list_bg_gray'  align='center' ".$style.">정산예정</td>
					<td class='list_box_td '  align='center' ".$style.">".getMethodStatus($db->dt[account_method])."</td>
				</tr>";
		}
	}else{
		$Contents .= "<tr height=50><td colspan='21' align=center>정산 예정내역이 없습니다</td></tr>";
	}

if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents .= "</table>
			</div>
			<table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
			<tr height=40>
				<td align='right'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
			</tr>
			</table>
			</td>
	</tr>";


if($admininfo[admin_level] == 9){
/*
	$help_title = "
		<nobr>
			<select name='update_type'>
				<!--option value='1'>검색한주문 전체에게</option-->
				<option value='2'>선택한주문 전체에게</option>
			</select>
			<input type='radio' name='update_kind' id='update_kind' value='' onclick=\"\" checked><label for='update_kind'>정산 처리</label>
		</nobr>";

		$help_text = "
		<script type='text/javascript'>
		<!--

		//-->
		</script>
		<div id='' style='margin-top:15px;'>
		<table cellpadding=3 cellspacing=0 width=100% class='input_table_box' >
			<col width=170>
			<col width=*>
			<tr id='ht_level0_status'>
				<td class='input_box_title'> <b>처리상태</b></td>
				<td class='input_box_item'>
					<input type='radio' name='act' id='account_ready' value='account_ready' onclick=\"\" checked><label for='account_ready'>정산확정</label>
				</td>
			</tr>
		</table>
		</div>
		<table cellpadding=3 cellspacing=0 width=100% style='border:0px solid silver;'>
			<tr height=50>
				<td colspan=4 align=center>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$help_text .= "
					<input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
				}else{
					$help_text .= "
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>";
				}
				$help_text .= "
				</td>
			</tr>
		</table>";

		$Contents .= "<tr><td colspan=4> ".HelpBox($help_title, $help_text,250)."</td></tr>";
*/

$Script .="<script type='text/javascript'>
		<!--
		$(document).ready(function (){

		//다중검색어 시작 2014-04-10 이학봉

			$('input[name=mult_search_use]').click(function (){
				var value = $(this).attr('checked');

				if(value == 'checked'){
					$('#search_text_input_div').css('display','none');
					$('#search_text_area_div').css('display','');
					
					$('#search_text_area').attr('disabled',false);
					$('#search_texts').attr('disabled',true);
				}else{
					$('#search_text_input_div').css('display','');
					$('#search_text_area_div').css('display','none');

					$('#search_text_area').attr('disabled',true);
					$('#search_texts').attr('disabled',false);
				}
			});

			var mult_search_use = $('input[name=mult_search_use]:checked').val();
				
			if(mult_search_use == '1'){
				$('#search_text_input_div').css('display','none');
				$('#search_text_area_div').css('display','');

				$('#search_text_area').attr('disabled',false);
				$('#search_texts').attr('disabled',true);
			}else{
				$('#search_text_input_div').css('display','');
				$('#search_text_area_div').css('display','none');

				$('#search_text_area').attr('disabled',true);
				$('#search_texts').attr('disabled',false);
			}

		//다중검색어 끝 2014-04-10 이학봉
		
		$('#max').change(function(){
			var value= $(this).val();
			$.cookie('accounts_plan_limit', value, {expires:1,domain:document.domain, path:'/', secure:0});
			document.location.reload();
			
		});

		});

		//-->
		</script>";
}

$Contents .= "
	  </table>
</form>";


$P = new LayOut();
$P->addScript = "<script language='javascript' src='accounts.js'></script>\n".$Script;
$P->OnloadFunction = "ChangeOrderDate(document.search_frm);";
$P->strLeftMenu = seller_accounts_menu();
$P->Navigation = "판매자정산관리 > $title";
$P->title = $title;
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>