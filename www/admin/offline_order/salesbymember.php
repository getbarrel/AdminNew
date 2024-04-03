<?
include("../class/layout.class");
//include("./pie.graph.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
//include("../lib/report.lib.php");
include("../logstory/include/commerce.lib.php");
include("../logstory/include/util.php");
include("../basic/company.lib.php");

$db = new mySQL();


if($groupbytype==""){
	$groupbytype="member";
}

if($groupbytype=="member"){
$Script ="
	<script type='text/javascript'>
	<!--
		$(document).ready(function(){
			$('#mem_code').combobox();
		})
		
	//-->
	</script>
";
}

if($department!=""){
	$dp_ix=$department;
}

$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
		    <td align='left' colspan=6 > ".GetTitleNavigation("영업매출계획", "영업관리 > 영업매출계획 > 담당자별 매출계획")."</td>
	  </tr>";


$Contents01 .= "
		  <tr height=40>
			<td >
				<form name='search' >
					<table border='0' cellpadding='0' cellspacing='0' width='100%'>
						<tr>
						<td style='width:100%;' valign=top colspan=3>
							<table width=100%  cellpadding='0' cellspacing='0'  border=0>
								<!--tr height=25><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>적립금 검색하기</b></td></tr-->
								<tr>
									<td align='left' colspan=2  width='100%' valign=top style='padding-top:0px;'>
										<table class='box_shadow' cellpadding=0 cellspacing=0 style='width:100%;' align=left>
											<tr>
												<th class='box_01'></th>
												<td class='box_02'></td>
												<th class='box_03'></th>
											</tr>
											<tr>
												<th class='box_04'></th>
												<td class='box_05' valign=top style='padding:0px;'>
													<TABLE height=20 cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
													<TR>
														<TD bgColor=#ffffff style='padding:0 0 0 0;'>
														<table cellpadding=3 cellspacing=1 width='100%' class='search_table_box'>
															<col width='20%'>
															<col width='*'>
															<tr height=27>";
															if($groupbytype=="member"){
																$Contents01 .= "
																  <td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>담당자</b></label></td>
																  <td class='search_box_item' align=left style='padding-left:5px;'>
																		<select name='code' id='mem_code'>
																				<option value=''>담당자 선택</option>";
																	$db->query("select cu.code, AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."') as name from common_user cu , common_member_detail cmd where cu.code=cmd.code and cu.mem_type ='A' and is_id_auth ='Y' and auth !='1' ");

																	for($i=0;$i<$db->total;$i++){
																		$db->fetch($i);
																		$Contents01 .= "<option value='".$db->dt[code]."' ".($db->dt[code] == $code ? "selected" :"").">".$db->dt[name]."</option>";
																	}

																$Contents01 .= "
																		</select>
																  </td>";
															}elseif($groupbytype=="department"){
																$Contents01 .= "
																  <td class='search_box_title' bgcolor='#efefef' align=center><label for='regdate'><b>담당자</b></label></td>
																  <td class='search_box_item' align=left style='padding-left:5px;'>
																		".getdepartment($dp_ix,'','true')."
																  </td>";
															}
														$Contents01 .= "
															</tr>
														</table>
														</TD>
													</TR>
													</TABLE>
												</td>
												<th class='box_06'></th>
											</tr>
											<tr>
												<th class='box_07'></th>
												<td class='box_08'></td>
												<th class='box_09'></th>
											</tr>
											</table>

									</td>
								</tr>
								<tr >
									<td colspan=3 align=center style='padding:10px 0 20px 0'>
										<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					</table>
				</form>
			</td>
		</tr>";

$Contents01 .= "
	  <tr>
	  	<td style='padding:5px 0px 0px 0px'>
	  		".salesByDateReportTable($vdate,$groupbytype,$SelectReport)."
	  	</td>
	  </tr>

	  <tr height=50><td colspan=5 class=small>
	   ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</td></tr>


	  <tr height=50><td colspan=5></td></tr>
	</table>";



$Contents = $Contents01;


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = offline_order_menu();
$P->strContents = $Contents;

if($groupbytype=="member"){
	$P->Navigation = "영업매출계획 > 담당자별 매출계획";
	$P->title = "담당자별 매출계획";
}elseif($groupbytype=="department"){
	$P->Navigation = "영업매출계획 > 부서별 매출계획";
	$P->title = "부서별 매출계획";
}

echo $P->PrintLayOut();


function salesByDateReportTable($vdate,$groupbytype="member",$SelectReport=3){
	global $depth,$referer_id, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status,$code,$db,$dp_ix;

	$nview_cnt = 0;
	$cid = $referer_id;
	if($SelectReport == ""){
		$SelectReport = 3;
		$vdate = date("Ym", time())."01";
	}

	$fordb = new Database();

	if($depth == ""){
		$depth = 0;
	}else{
		$depth = $depth+1;
	}


	if($vdate == ""){
		$vdate = date("Ymd", time());
		$vyesterday = date("Ymd", time()-84600);
		$voneweekago = date("Ymd", time()-84600*7);
	}else{
		if($SelectReport ==3){
			$vdate = $vdate."01";
		}
		$vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
		$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
		$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
	}

	if($groupbytype=="member"){
		if(empty($code)){
			$code = $_SESSION["admininfo"]["charger_ix"];
		}
	}elseif($groupbytype=="department"){
		if(empty($dp_ix)){
			$db->query("select department from common_member_detail where code ='".$_SESSION["admininfo"]["charger_ix"]."' ");
			$db->fetch();
			$dp_ix = $db->dt[department];
		}
	}

	$where =" and o.oid=od.oid and od.order_from in ('offline')  ";

	if($groupbytype=="member"){
		$select=" date_format(od.regdate,'%Y%m%d') as vdate ,o.charger_ix, ";
		$where .=" and date_format(od.regdate,'%Y%m%d') LIKE '".substr($vdate,0,6)."%' and o.charger_ix ='".$code."'";
		$group_by = "group by date_format(od.regdate,'%Y%m%d')";
	}elseif($groupbytype=="department"){
		$select=" date_format(od.regdate,'%Y%m%d') as vdate , ";
		$where .=" and date_format(od.regdate,'%Y%m%d') LIKE '".substr($vdate,0,6)."%' and o.charger_dp_ix ='".$dp_ix."' and o.charger_dp_ix !='0' ";
		$group_by = "group by date_format(od.regdate,'%Y%m%d')";
	}

	$sql = "select data.vdate, sum(data.order_sale_cnt) as order_sale_cnt, sum(order_sale_sum) as order_sale_sum, sum(order_coprice_sum) as order_coprice_sum, 
				sum(sale_all_cnt) as sale_all_cnt, sum(sale_all_sum) as sale_all_sum, sum(coprice_all_sum) as coprice_all_sum, 
				sum(cancel_sale_cnt) as cancel_sale_cnt, sum(cancel_sale_sum) as cancel_sale_sum, sum(cancel_coprice_sum) as cancel_coprice_sum, 
				sum(return_sale_cnt) as return_sale_cnt, sum(return_sale_sum) as return_sale_sum, sum(return_coprice_sum) as return_coprice_sum, 
				sum(whole_delivery_cnt) as whole_delivery_cnt, sum(whole_delivery_sum) as whole_delivery_sum, sum(return_delivery_cnt) as return_delivery_cnt, sum(return_delivery_sum) as return_delivery_sum


				from (
				Select $select
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt,
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then (od.pt_dcprice)  else 0 end) as order_sale_sum,
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.coprice*od.pcnt else 0 end) as order_coprice_sum,

				sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt,
				sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else 0 end) as sale_all_sum,
				sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.coprice*od.pcnt else 0 end) as coprice_all_sum,

				sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
				sum(case when od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else 0 end) as cancel_sale_sum,
				sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.coprice*od.pcnt else 0 end) as cancel_coprice_sum,

				sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
				sum(case when od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else 0 end) as return_sale_sum,
				sum(case when od.status IN ('".implode("','",$return_status)."')  then od.coprice*od.pcnt else 0 end) as return_coprice_sum, 
				0 as whole_delivery_cnt,
				0 as whole_delivery_sum,
				0 as return_delivery_cnt,
				0 as return_delivery_sum
				from  shop_order_detail od , shop_order o

				where od.status NOT IN ('".implode("','",$non_sale_status)."') 
				$where
				$group_by
				";

			$sql .= "
				union 
				select $select
				0 as order_sale_cnt,
				0 as order_sale_sum,
				0 as order_coprice_sum,

				0 as sale_all_cnt,
				0 as sale_all_sum,
				0 as coprice_all_sum,

				0 as cancel_sale_cnt,
				0 as cancel_sale_sum,
				0 as cancel_coprice_sum,

				0 as return_sale_cnt,
				0 as return_sale_sum,
				0 as return_coprice_sum, 
				sum(case when oph.price_div = 'D' and payment_status = 'G'  then 1 else 0 end) as whole_delivery_cnt,
				sum(case when oph.price_div = 'D' and payment_status = 'G'  then oph.expect_price else 0 end) as whole_delivery_sum,
				sum(case when oph.price_div = 'D' and payment_status = 'F'  then 1 else 0 end) as return_delivery_cnt,
				sum(case when oph.price_div = 'D' and payment_status = 'F'  then oph.expect_price else 0 end) as return_delivery_sum
				from 
				shop_order o, shop_order_detail od , shop_order_price_history oph

				where  od.oid = oph.oid
				$where
				$group_by

				";

				$sql .= ") data
				";


				//							left join ".TBL_COMMERCE_VIEWINGVIEW." b on od.pid = b.pid
				//AND od.status NOT IN ('".implode("','",$non_sale_status)."')
				//and substr(c.cid,1,".(($depth+1)*3).") = substr(b.cid,1,3)


	$dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");

	//echo nl2br($sql);

	$fordb->query($sql);
	$data = $fordb->fetchall();
	
	


	$mstring = "<table width='100%' border=0>
					<tr><td>".TitleBar("상품군별 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString)."</td><td align=right>(단위:원) </td></tr>
				</table>";

	if($groupbytype=="member"){
		$mstring .= "<form name='salesbymember' action='./salesby.act.php' method='post' target='act' >";
		$mstring .= "<input type='hidden' name='act' value='memsales_update'>";
		$mstring .= "<input type='hidden' name='code' value='".$code."'>";
	}

	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>";

		$mstring .= "
						<col width='25%'>
						<col width='15%'>
						<col width='15%'>
						<col width='15%'>
						<col width='15%'>
						<col width='15%'>";

	$mstring .= "
		<tr height=30>
			<td class=m_td rowspan=2>날짜</td>
			<td class=m_td colspan=2>매출목표(원)</td>
			<td class=m_td colspan=2>실매출액(원)</td>
			<td class=m_td rowspan=2>성과율(%)</td>
		</tr>
		<tr height=30 align=center>
			<td class=m_td nowrap>VAT별도</td>
			<td class=m_td >VAT포함</td>
			<td class=m_td nowrap>VAT별도</td>
			<td class=m_td >VAT포함</td>
			</tr>\n";


	if($groupbytype=="member"){
		$db->query("select * from shop_member_sales_plan where code ='".$code."' and plan_date LIKE '".substr($vdate,0,6)."%' ");
		$sales_plan = $db->fetchall();
	}elseif($groupbytype=="department"){
		$db->query("select plan_date,sum(plan_price) as plan_price from shop_member_sales_plan where dp_ix ='".$dp_ix."' and plan_date LIKE '".substr($vdate,0,6)."%' group by plan_date ");
		$sales_plan = $db->fetchall();
	}

	$nLoop = date("t", mktime(0, 0, 0, substr($vdate,4,2), substr($vdate,6,2), substr($vdate,0,4)));

	for($i=0;$i<$nLoop;$i++){
		
		$_date = getNameOfDate($i,$vdate);

		$_data = "";
		for($j=0;$j<count($data);$j++){
			if($data[$j][vdate]==$_date){
				$_data = $data[$j];
				//unset($data[$j]);
				break;
			}
		}

		$real_sale_sum = $_data[sale_all_sum]-$_data[cancel_sale_sum]-$_data[return_sale_sum];
		$real_sale_sum_with_deliveryprice = $real_sale_sum +  $_data[whole_delivery_sum]-$_data[return_delivery_sum];

		$mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td class='list_box_td list_bg_gray str' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".getNameOfWeekday($i,$vdate)."</td>";

		$sales_plan_price = 0;
		for($j=0;$j<count($sales_plan);$j++){
			if($sales_plan[$j][plan_date]==$_date){
				//echo count($sales_plan)."<br/>";
				$sales_plan_price = $sales_plan[$j][plan_price];
				//unset($sales_plan[$j]);
				break;
			}
		}

		if($code==$_SESSION["admininfo"]["charger_ix"] && $groupbytype=="member"){
			$mstring .= "
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">
				<input type='text' class='number' name='sale[".$_date."]' value='".$sales_plan_price."' />
			</td>";
		}else{
			$mstring .= "
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($sales_plan_price,0)."&nbsp;</td>";
		}

		$mstring .= "
		<td class='list_box_td number point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($sales_plan_price*1.1,0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_sum_with_deliveryprice/1.1,0)."&nbsp;</td>

		<td class='list_box_td number point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_sum_with_deliveryprice,0)."</td>";
		
		if($sales_plan_price!=0){
			$mstring .= "
			<td class='list_box_td number blue_point str' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($real_sale_sum_with_deliveryprice/1.1)/$sales_plan_price*100,0)."</td>";
		}else{
			$mstring .= "
			<td class='list_box_td number blue_point str' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(0,0)."</td>";
		}

		$mstring .= "
		</tr>\n";

		$sum_sales_plan_price += returnZeroValue($sales_plan_price);
		$sum_sales_plan_price_vat += returnZeroValue($sales_plan_price*1.1);
		$sum_sales_price += returnZeroValue($real_sale_sum_with_deliveryprice/1.1);
		$sum_sales_price_vat += returnZeroValue($real_sale_sum_with_deliveryprice);
	}


	if($sum_sales_plan_price > 0){
		$sum_rate = $sum_sales_price/$sum_sales_plan_price*100;
	}else{
		$sum_rate = 0;
	}


	$mstring .= "<tr height=25 align=right>
	<td class=s_td align=center>합계</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($sum_sales_plan_price,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($sum_sales_plan_price_vat,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($sum_sales_price,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($sum_sales_price_vat,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($sum_rate,0)."</td>
	</tr>\n";

	$mstring .= "</table>\n";

	if($groupbytype=="member"){
		if($code==$_SESSION["admininfo"]["charger_ix"]){
			$mstring .= "
			<table class='box_shadow' cellpadding=0 cellspacing=0 style='width:100%;' align=left>
				<tr>
					<td align=center style='padding:10px 0 20px 0'>
						<input type='image' src='../images/".$_SESSION["admininfo"]["language"]."/b_save.gif' border=0>
					</td>
				</tr>
			</table>";
		}

		$mstring .= "
		</form>";
	}

	/*
	if($groupbytype=="day"){
		$mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>VAT 포함</td></tr></table>";

		
		$help_text = "
		<table>
			<tr>
				<td style='line-height:150%'>
				- 카테고리별 상품조회 회수를 바탕으로 귀사 사이트의 인기카테고리와 비인기 카테고리를 정확히 파악하여 그에 맞는 운영및 마케팅 정책을 수립 수행할수 있습니다<br>
				- 좌측 카테고리를 클릭하면 하부 카테고리에 대한 상세 정보가 표시 됩니다<br><br>
				</td>
			</tr>
		</table>
		";

		$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B' );


		$mstring .= HelpBox("일별매출(종합)", $help_text);
	}
	*/
	return $mstring;
}


function getNameOfDate($WeekNum, $vdate){
	return date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$WeekNum)."";
}

/*
CREATE TABLE IF NOT EXISTS `shop_member_sales_plan` (
  `msp_ix` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '인덱스',
  `code` varchar(32) NOT NULL COMMENT '코드',
  `name` varchar(100) DEFAULT NULL COMMENT '이름',
  `dp_ix` int(8) unsigned NOT NULL COMMENT '부서인덱스',
  `dp_name` varchar(100) DEFAULT NULL COMMENT '부서이름',
  `plan_price` int(8) unsigned DEFAULT '0' COMMENT '목표액',
  `plan_date` int(8) unsigned NOT NULL COMMENT '목표일자',
  `regdate` datetime DEFAULT NULL COMMENT '등록일',
  PRIMARY KEY (`msp_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='영업매출계획' AUTO_INCREMENT=1 ;

ALTER TABLE  `shop_order` ADD  `charger_ix` VARCHAR( 32 ) NULL COMMENT  '수동주문담당자' AFTER  `user_agent` ,
ADD  `charger_dp_ix` INT( 4 ) NULL COMMENT  '수동주문담당자부서' AFTER  `charger_ix`

*/

?>

