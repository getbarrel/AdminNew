<?
include("../class/layout.class");

$db = new database();


$Script = "
<script language='javascript'>


</script>";

//<script language='javascript' src='../include/DateSelect.js'></script>\n
//<script language='javascript' src='"."tglib_orders.php?page=$page&ctgr=$ctgr&qstr=$qstr"."'></script>
$Contents = "
<table width='100%'>
<tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("정산 상세내역", "매출관리 > 정산 상세내역 ")."</td>
</tr>

</table>  ";
/*
$Contents = $Contents."
<table width='100%' cellpadding=0 cellspacing=0>
<tr>
		<td align='left' colspan=4 style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:3px;'><img src='../image/title_head.gif' align=absmiddle><b> 정산정보검색</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	</table>
	<form name='search_frm' method='get' action=''>
	<input type=hidden name='mode' value='search'>
	<input type=hidden name='ac_ix' value='$ac_ix'>
	<table width='100%' class='search_table_box'>
	<col width=15%>
	<col width=35%>
	<col width=15%>
	<col width=35%>
	<tr height=30>
	  <td class='search_box_title'><label for='regdate'>등록일자</label><input type='checkbox' name='regdate' id='regdate' value='1' onclick='ChangeRegistDate(document.search_frm);' ".CompareReturnValue("1",$regdate,"checked")."></td>
	  <td class='search_box_item' colspan=3 >
		<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
			<col width=70>
			<col width=20>
			<col width=70>
			<col width=*>
			<tr>
				<TD nowrap>
				<input type='text' name='sdate' class='textbox' value='".$sdate."' style='height:20px;width:70px;text-align:center;' id='start_datepicker'>
				<!--SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromYY ></SELECT> 년
				<SELECT onchange=javascript:onChangeDate(this.form.FromYY,this.form.FromMM,this.form.FromDD) name=FromMM></SELECT> 월
				<SELECT name=FromDD></SELECT> 일 -->
				</TD>
				<TD align=center> ~ </TD>
				<TD nowrap>
				<input type='text' name='edate' class='textbox' value='".$edate."' style='height:20px;width:70px;text-align:center;' id='end_datepicker'>
				<!--SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToYY></SELECT> 년
				<SELECT onchange=javascript:onChangeDate(this.form.ToYY,this.form.ToMM,this.form.ToDD) name=ToMM></SELECT> 월
				<SELECT name=ToDD></SELECT> 일 -->
				</TD>
				<TD style='padding:0px 10px'>
					<a href=\"javascript:setSelectDate('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
					<a href=\"javascript:setSelectDate('$vyesterday','$vyesterday',1);\"><img src='../images/".$admininfo[language]."/btn_yesterday.gif'></a>
					<a href=\"javascript:setSelectDate('$voneweekago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
					<a href=\"javascript:setSelectDate('$v15ago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
					<a href=\"javascript:setSelectDate('$vonemonthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
					<a href=\"javascript:setSelectDate('$v2monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
					<a href=\"javascript:setSelectDate('$v3monthago','$today',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
				</TD>
			</tr>
		</table>
	  </td>
	</tr>
	<tr height=30>
		<td class='search_box_title'> 입점 정산 방식</td>
		<td class='search_box_item' colspan=3>
		<input type='checkbox' name='account_type[]'  id='account_type_1' value='1' ".ReturnStringAfterCompare($status, "1", " checked")."><label for='account_type_1'> 중개(수수료률)</label>
		<input type='checkbox' name='account_type[]'  id='account_type_2' value='2' ".ReturnStringAfterCompare($status, "2", " checked")."><label for='account_type_2'> 매입</label>
		<!--<input type='checkbox' name='account_type[]'  id='account_type_3' value='3' ".ReturnStringAfterCompare($status, "3", " checked")."><label for='account_type_3'> 선매입(미정산)</label>-->
		</td>
	</tr>
	<tr height=30>
		<td class='search_box_title'> 입점 배송 방식</td>
		<td class='search_box_item' colspan=3>
		<input type='checkbox' name='delivery_type[]'  id='delivery_type_1' value='1' ".ReturnStringAfterCompare($status, "1", " checked")."><label for='delivery_type_1'> 위탁(통합배송)</label>
		<input type='checkbox' name='delivery_type[]'  id='delivery_type_2' value='2' ".ReturnStringAfterCompare($status, "2", " checked")."><label for='delivery_type_2'> 입점(개별배송)</label>
		</td>
	</tr>
	<tr height=30>
		<td class='search_box_title'> 과세구분</td>
		<td class='search_box_item' colspan=3>
		<input type='checkbox' name='surtax_yorn[]'  id='surtax_yorn_1' value='N' ".ReturnStringAfterCompare($status, "N", " checked")."><label for='surtax_yorn_1'> 과세</label>
		<input type='checkbox' name='surtax_yorn[]'  id='surtax_yorn_2' value='Y' ".ReturnStringAfterCompare($status, "Y", " checked")."><label for='surtax_yorn_2'> 면세</label>
		<input type='checkbox' name='surtax_yorn[]'  id='surtax_yorn_3' value='P' ".ReturnStringAfterCompare($status, "P", " checked")."><label for='surtax_yorn_3'> 영세</label>
		</td>
	</tr>
	<!--tr bgcolor=#ffffff height=30>
		<td><img src='../image/ico_dot.gif' align=absmiddle>정산수행</td>
		<td colspan=3>
			<table cellpadding=3 cellspacing=0>
				<tr>
					<td><input type=radio name='account_auto' value='1' id='account_auto_1'  ".CompareReturnValue("1",$db->dt[account_auto],"checked")."><label for='account_auto_1'>자동수행</label>
	    			<input type=radio name='account_auto' value='0' id='account_auto_2'  ".CompareReturnValue("0",$db->dt[account_auto],"checked")."><label for='account_auto_2'>수동수행</label></td>
				</tr>
				<tr>
					<td><span class=small><b>외부 호스팅</b>을 받으시는 고객님의 경우 <b>자동수행</b>으로 정산을 원하시는 경우  URL(<u>http://".$HTTP_HOST."/clone/account.php</u>) 을 <b>clone</b> 에 등록해주시기 바랍니다</span></td>
	    		</tr>
			</table>
		</td>
	</tr>
	<tr height=1><td colspan=4 class='dot-x'></td></tr-->
</table>
<table width='100%' >
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center style='padding:10px 0px'><input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0 style='cursor:hand;border:0px;' ></td>
	</tr>
	
</table>
</form>";
*/

$where = "";
if($_SESSION['admininfo']['admin_level'] < 9){
    $where .= " and od.company_id ='".$_SESSION['admininfo']['company_id']."' ";
}

/*
if($mode == "search"){

	if($sdate != "" && $edate != ""){
		$where .= " and  date_format(od.dc_date,'%Y%m%d') between  $sdate and $edate ";
	}

	if(is_array($account_type)){
		$where .= " and od.account_type in (";
		foreach($account_type as $key =>$value){
			$where .= "'$value'";
			if(($key == 0 and count($account_type) != 1) or $key != count($account_type)-1){
				$where .= ",";
			}
		}
		$where .=")";
	}

	if(is_array($delivery_type)){
		$where .= " and od.delivery_type in (";
		foreach($delivery_type as $key =>$value){
			$where .= "'$value'";
			if(($key == 0 and count($account_type) != 1) or $key != count($delivery_type)-1){
				$where .= ",";
			}
		}
		$where .=")";
	}

	if(is_array($surtax_yorn)){
		$where .= " and od.surtax_yorn in (";
		foreach($surtax_yorn as $key =>$value){
			$where .= "'$value'";
			if(($key == 0 and count($account_type) != 1) or $key != count($surtax_yorn)-1){
				$where .= ",";
			}
		}
		$where .=")";
	}

}else{
	if($startDate != "" && $endDate != ""){
		$where .= " and  date_format(od.dc_date,'%Y%m%d') between  $startDate and $endDate ";
	}
}
*/

if($ac_ix!=""){
	if(is_array($ac_ix)){
		foreach($ac_ix as $val){
			$array_ac_ix[]=$val;
		}
	}else{
		$array_ac_ix[]=$ac_ix;
	}
}elseif($ac_ix_text!=""){
	$array_ac_ix = explode("|",$ac_ix_text);
}elseif($ar_ix!=""){
	$sql="select * from shop_accounts where ar_ix=".$ar_ix." ";
	$db->query($sql);
	if($db->total){
		$ac_fetchall=$db->fetchall("object");
		foreach($ac_fetchall as $ac_fetch){
			$array_ac_ix[]=$ac_fetch[ac_ix];
		}
	}else{
		$array_ac_ix[]=array();
	}
}else{
	if(!is_array($array_ac_ix)){
		$array_ac_ix[]=array();
	}
}

$ac_ix_param="";
for($i=0;$i < count($array_ac_ix);$i++){
	if($ac_ix_param == ""){
		$ac_ix_param = "&ac_ix_text=".$array_ac_ix[$i];
	}else{
		$ac_ix_param .= "|".$array_ac_ix[$i];
	}
}

$Contents = $Contents."
<table border='0' cellspacing='1' cellpadding='15' width='100%'>
<tr>
  <td bgcolor='#F8F9FA'>
	<table border='0' cellspacing='0' cellpadding='0' width='100%'>
		<tr>
			<td height='20'><img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'> <b>주문제품정보</b></td>
			<td align='right'><a href='../seller_accounts/accounts_detail.php?act=accounts_excel".$ac_ix_param."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></td>
		</tr>
	</table>
	<table width='100%' border='0' cellpadding='0' cellspacing='1'>
		<tr>
			<td bgcolor='silver'>
				<table border='0' width='100%' cellspacing='1' cellpadding='2'>
					<tr>
						<td bgcolor='#ffffff'>
							<table border='0' width='100%'>
								<tr>
									<td>
										<table width='100%' border='0' cellpadding='0' cellspacing='0'>
											<tr height='25r' bgcolor='#efefef' align=center>
												<td width='5%' class='s_td'><b>번호</b></td>
												<td width='*' colspan=2 class='m_td'><b>주문번호/상세번호/상품명/옵션</b></td>
												<td width='6%' class='m_td'><b>정산방식</b></td>
												<td width='5%' class='m_td'><b>과세구분</b></td>
												<td width='4%' class='m_td'><b>수량</b></td>
												<td width='8%' class='m_td'><b>정산기준금액</b></td>
												<td width='8%'  class='m_td'><b>할인부담금액</b></td>
												<td width='7%' class='m_td'><b>수수료율</b></td>
												<td width='7%' class='m_td small' nowrap><b>수수료</b></td>
												<td width='6%' class='m_td'><b>배송방식</b></td>
												<td width='7%' class='m_td small' nowrap><b>배송비정산</b></td>
												<td width='7%' class='e_td small' nowrap><b>정산금액</b></td>
											</tr>";

	
	$sql = "select
			o.ac_ix,
			o.refund_bool,
			od.fc_date,
			od.ic_date,
			od.oid,
			od.od_ix,
			od.pid,
			od.pname,
			od.reserve, 
			od.pcnt, 
			od.option_text,
			od.commission,
			od.commission_msg,
			od.surtax_yorn,
			od.account_type,
			od.delivery_type,
			od.order_from,
			
			(select GROUP_CONCAT(DISTINCT method ORDER BY method DESC SEPARATOR '|') from shop_order_payment op where op.oid=od.oid) as method,

			case when od.account_type in ('1','') then od.ptprice else od.coprice*od.pcnt end as p_expect_price,

			(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) as p_dc_allotment_price,

			ROUND(case when od.account_type in ('1','') then ((od.ptprice - (select ifnull(sum(dc.dc_price_seller),'0') from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE'))) * od.commission / 100) else od.coprice*od.pcnt*od.commission/100 end) as p_fee_price,

			(
				case when
					o.refund_bool='Y'
				then
					case when 
						od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and ocd.claim_group = od.claim_group and odr.refund_ac_ix = od.refund_ac_ix)
					then
						(
							ocd.delivery_price
						) 
					else
						'0' 
					end
				else 
					case when 
						od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ac_ix = od.ac_ix)
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
						od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ac_ix = od.ac_ix)
					then
						(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.ode_ix=odv.ode_ix and dc.dc_type in ('DCP','DE')) 
					else
						'0' 
					end
				end

			) as d_dc_allotment_price

		from
		(
			select
				od.oid,od.od_ix,'N' as refund_bool, od.ac_ix as ac_ix
			from
				".TBL_SHOP_ORDER_DETAIL." od
			WHERE od.ac_ix in ('".implode("','",$array_ac_ix)."') and od.ac_ix not in ('0','')
          ".$where."

			UNION ALL

			select
				od.oid,od.od_ix,'Y' as refund_bool, od.refund_ac_ix as ac_ix
			from
				".TBL_SHOP_ORDER_DETAIL." od
			WHERE od.refund_ac_ix in ('".implode("','",$array_ac_ix)."') and od.refund_ac_ix not in ('0','')
          ".$where."
          
			order by oid desc
		) o
		left join
				".TBL_SHOP_ORDER_DETAIL." od on o.od_ix = od.od_ix
		left join 
				shop_order_delivery odv on (
			odv.oid=od.oid
			and odv.ode_ix = od.ode_ix
			and odv.ac_ix = od.ac_ix
		)
		left join
				shop_order_claim_delivery ocd on (
			ocd.oid=od.oid
			and ocd.claim_group = od.claim_group
			and ocd.ac_ix=od.refund_ac_ix
		) " ;

	$db->query($sql);

	if($act == "accounts_excel"){

		include '../include/phpexcel/Classes/PHPExcel.php';

		PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

		date_default_timezone_set('Asia/Seoul');

		$accounts_excel = new PHPExcel();

		// 속성 정의
		$accounts_excel->getProperties()->setCreator("포비즈 코리아")
									 ->setLastModifiedBy("Mallstory.com")
									 ->setTitle("accounts List")
									 ->setSubject("accounts List")
									 ->setDescription("generated by forbiz korea")
									 ->setKeywords("mallstory")
									 ->setCategory("accounts List");
		

		$accounts_excel->getActiveSheet(0)->setCellValue('A' . 1, "번호");
		$accounts_excel->getActiveSheet(0)->setCellValue('B' . 1, "정산번호");
		$accounts_excel->getActiveSheet(0)->setCellValue('C' . 1, "주문번호");
		$accounts_excel->getActiveSheet(0)->setCellValue('D' . 1, "주문상세번호");
		$accounts_excel->getActiveSheet(0)->setCellValue('E' . 1, "상품명");
		$accounts_excel->getActiveSheet(0)->setCellValue('F' . 1, "옵션");
		$accounts_excel->getActiveSheet(0)->setCellValue('G' . 1, "정산방식");
		$accounts_excel->getActiveSheet(0)->setCellValue('H' . 1, "과세구분");
		$accounts_excel->getActiveSheet(0)->setCellValue('I' . 1, "수량");
		$accounts_excel->getActiveSheet(0)->setCellValue('J' . 1, "정산기준금액");
		$accounts_excel->getActiveSheet(0)->setCellValue('K' . 1, "할인부담금액");
		$accounts_excel->getActiveSheet(0)->setCellValue('L' . 1, "수수료율");
		$accounts_excel->getActiveSheet(0)->setCellValue('M' . 1, "수수료");
		$accounts_excel->getActiveSheet(0)->setCellValue('N' . 1, "배송방식");
		$accounts_excel->getActiveSheet(0)->setCellValue('O' . 1, "배송비정산");
		$accounts_excel->getActiveSheet(0)->setCellValue('P' . 1, "정산금액");
		$accounts_excel->getActiveSheet(0)->setCellValue('Q' . 1, "입금일자");
		$accounts_excel->getActiveSheet(0)->setCellValue('R' . 1, "환불일자");

		if($_SESSION["admininfo"]["admin_level"] == 9){
			$accounts_excel->getActiveSheet(0)->setCellValue('S' . 1, "결제타입");
			$accounts_excel->getActiveSheet(0)->setCellValue('T' . 1, "판매처");
		}
		

		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);
			
			if($db->dt[refund_bool]=="Y"){
				$sign = -1;
			}else{
				$sign = 1;
			}

			switch($db->dt[surtax_yorn]){
				case 'N':
					$surtax_yorn = "과세";
				break;
				case 'Y':
					$surtax_yorn = "면세";
				break;
				case 'P':
					$surtax_yorn = "영세";
				break;
				case '':
					$surtax_yorn = "-";
				break;
			}

			switch($db->dt[account_type]){
				case '1':
					$account_type = "수수료";
				break;
				case '2':
					$account_type = "매입";
				break;
				case '':
					$account_type = "-";
				break;
				
			}

			switch($db->dt[delivery_type]){
				case '1':
					$delivery_type = "통합";
				break;
				case '2':
					$delivery_type = "개별";
				break;
				case '':
					$delivery_type = "-";
				break;
			}

			
			
			$p_ac_price = (($db->dt[p_expect_price] - $db->dt[p_dc_allotment_price]) - $db->dt[p_fee_price])*$sign;
			$d_ac_price = ($db->dt[d_expect_price] - $db->dt[d_dc_allotment_price])*$sign;
			
			$ac_price = $p_ac_price + $d_ac_price;

			$accounts_excel->getActiveSheet()->setCellValue('A' . ($i + 2), ($i+1));
			$accounts_excel->getActiveSheet()->setCellValue('B' . ($i + 2), $db->dt[ac_ix]);
			$accounts_excel->getActiveSheet()->setCellValue('C' . ($i + 2), $db->dt[oid]);
			$accounts_excel->getActiveSheet()->setCellValue('D' . ($i + 2), $db->dt[od_ix]);
			$accounts_excel->getActiveSheet()->setCellValue('E' . ($i + 2), strip_tags($db->dt[pname]));		
			$accounts_excel->getActiveSheet()->setCellValue('F' . ($i + 2), strip_tags($db->dt[option_text]));
			$accounts_excel->getActiveSheet()->setCellValue('G' . ($i + 2), $account_type);
			$accounts_excel->getActiveSheet()->setCellValue('H' . ($i + 2), $surtax_yorn);
			$accounts_excel->getActiveSheet()->setCellValue('I' . ($i + 2), $db->dt[pcnt]*$sign);
			$accounts_excel->getActiveSheet()->setCellValue('J' . ($i + 2), $db->dt[p_expect_price]*$sign);
			$accounts_excel->getActiveSheet()->setCellValue('K' . ($i + 2), $db->dt[p_dc_allotment_price]*$sign);
			$accounts_excel->getActiveSheet()->setCellValue('L' . ($i + 2), $db->dt[commission]." % (".$db->dt[commission_msg].")");
			$accounts_excel->getActiveSheet()->setCellValue('M' . ($i + 2), $db->dt[p_fee_price]*$sign);
			$accounts_excel->getActiveSheet()->setCellValue('N' . ($i + 2), $delivery_type);
			$accounts_excel->getActiveSheet()->setCellValue('O' . ($i + 2), $d_ac_price);
			$accounts_excel->getActiveSheet()->setCellValue('P' . ($i + 2), $ac_price);
			$accounts_excel->getActiveSheet()->setCellValue('Q' . ($i + 2), $db->dt[ic_date]);
			$accounts_excel->getActiveSheet()->setCellValue('R' . ($i + 2), $db->dt[fc_date]);
			
			if($_SESSION["admininfo"]["admin_level"] == 9){
				$method = getMethodStatus($db->dt[method],"text");
				$order_from = getOrderFromName($db->dt[order_from]);

				$accounts_excel->getActiveSheet()->setCellValue('S' . ($i + 2), $method);
				$accounts_excel->getActiveSheet()->setCellValue('T' . ($i + 2), $order_from);
			}
		}

		// 첫번째 시트 선택
		$accounts_excel->setActiveSheetIndex(0);

		$accounts_excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$accounts_excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$accounts_excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$accounts_excel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
		$accounts_excel->getActiveSheet()->getColumnDimension('R')->setWidth(10);

		if($_SESSION["admininfo"]["admin_level"] == 9){
			$accounts_excel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
			$accounts_excel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","정산상세정산현황.xls").'"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($accounts_excel, 'Excel5');
		$objWriter->save('php://output');

		exit;
	}

	if($db->total <= 1000){

		for($j = 0; $j < $db->total; $j++){
			$db->fetch($j);
			
			if($db->dt[refund_bool]=="Y"){
				$sign = -1;
			}else{
				$sign = 1;
			}

			switch($db->dt[surtax_yorn]){
				case 'N':
					$surtax_yorn = "과세";
				break;
				case 'Y':
					$surtax_yorn = "면세";
				break;
				case 'P':
					$surtax_yorn = "영세";
				break;
				case '':
					$surtax_yorn = "-";
				break;
			}

			switch($db->dt[account_type]){
				case '1':
					$account_type = "수수료";
				break;
				case '2':
					$account_type = "매입";
				break;
				case '':
					$account_type = "-";
				break;
				
			}

			switch($db->dt[delivery_type]){
				case '1':
					$delivery_type = "통합";
				break;
				case '2':
					$delivery_type = "개별";
				break;
				case '':
					$delivery_type = "-";
				break;
			}
			
			$p_ac_price = (($db->dt[p_expect_price] - $db->dt[p_dc_allotment_price]) - $db->dt[p_fee_price])*$sign;
			$d_ac_price = ($db->dt[d_expect_price] - $db->dt[d_dc_allotment_price])*$sign;
			
			$ac_price = $p_ac_price + $d_ac_price;

			$sum_standard_price += $db->dt[p_expect_price]*$sign;//정산기준금액
			$sum_dc_allotment_price += $db->dt[p_dc_allotment_price]*$sign;	//할인부담금액
			$sum_commission += $db->dt[p_fee_price]*$sign;	//수수료
			$sum_delivery_price += $d_ac_price;	//배송비정산
			$sum_account += $ac_price;		//정산금액
			$sum_count += $db->dt[pcnt]*$sign;
			
			//if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "s"))){
				$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "s");
			//}else{
			//	$img_str = "../image/no_img.gif";
			//}

			$Contents .= "
			<tr height='70' align='center'>
				<td >".($j+1)."</td>
				<td ><div style='border:1px solid silver;padding:5px;width:60px;'><img src=\"".$img_str."\" width=50 height=50></div></td>
				<td >
					<div align='left' style='padding:5 0 5 10'>
						<b class=blue>".$db->dt[oid]."</b> <b class='red'>".$db->dt[od_ix]."</b><br><a href='/shop/goods_view.php?id=".$db->dt[pid]."' target='_blank'>".$db->dt[pname]."</a>
						".(strip_tags($db->dt[option_text]) ? "<br/>".strip_tags($db->dt[option_text]) : "")."
					</div>
				</td>
				<td align=center>".$account_type."</td>
				<td align=center>".$surtax_yorn."</td>
				<td >".number_format($db->dt[pcnt]*$sign)." 개</td>
				<td align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_expect_price]*$sign)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[p_dc_allotment_price]*$sign)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td align=center>".number_format($db->dt[commission])." %<br/>(".$db->dt[commission_msg].")</td>
				<td >".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db->dt[p_fee_price]*$sign)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td align=center>".$delivery_type."</td>
				<td>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($d_ac_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ac_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
			</tr>
			<tr height=1><td colspan=13 class='dot-x'></td></tr>";
		}

		$Contents = $Contents."
											<tr height='30' align='center'>
												<td colspan=5>합계</td>
												<td >".$sum_count." 개</td>
												<td align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($sum_standard_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
												<td align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($sum_dc_allotment_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
												<td align=center></td>
												<td >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($sum_commission)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
												<td align=center></td>
												<td>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($sum_delivery_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
												<td>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($sum_account)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											</tr>";


	}else{
		$Contents = $Contents."
											<tr height='30' align='center'>
												<td colspan=13>1000건이상은 엑셀로 다운받아서 확인하실수 있습니다.</td>
											</tr>";
		
	}
	
	$Contents = $Contents."
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
		</td>
	</tr>
</table>
";


$Contents = $Contents."
<form name='lyrstat'>
	<input type='hidden' name='opend' value=''>
</form>";



$P = new ManagePopLayOut();
$P->OnloadFunction = "";
$P->addScript = $Script;
$P->Navigation = "정산관리 > 정산 상세내역";
$P->NaviTitle = "정산 상세내역";
$P->strContents = $Contents;


echo $P->PrintLayOut();

?>