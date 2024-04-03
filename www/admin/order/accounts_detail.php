<?
include("../class/layout.class");


$db1 = new Database;
$db3 = new Database;

$vdate = date("Ymd", time());
$today = date("Ymd", time());
$vyesterday = date("Ymd", time()-84600);
$voneweekago = date("Ymd", time()-84600*7);
$vtwoweekago = date("Ymd", time()-84600*14);
$vfourweekago = date("Ymd", time()-84600*28);
$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
$v15ago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*15);
$vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
$vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v2monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2)+1,substr($vdate,0,4)));
$v3monthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-3,substr($vdate,6,2)+1,substr($vdate,0,4)));

$Script = "<script language='javascript' src='../include/DateSelect.js'></script>\n
";
$Script .= "
<script language='javascript'>
$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력',
	onSelect: function(dateText, inst){
		//alert(dateText);
		if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
			$('#end_datepicker').val(dateText);
		}else{
			$('#end_datepicker').datepicker('setDate','+0d');
		}
	}

	});

	$(\"#end_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yymmdd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});

function setSelectDate(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

function ChangeRegistDate(frm){
	if(frm.regdate.checked){
		frm.sdate.disabled = false;
		frm.edate.disabled = false;
/*
		frm.FromYY.disabled = false;
		frm.FromMM.disabled = false;
		frm.FromDD.disabled = false;
		frm.ToYY.disabled = false;
		frm.ToMM.disabled = false;
		frm.ToDD.disabled = false;
*/
	}else{
		frm.sdate.disabled = true;
		frm.edate.disabled = true;
/*
		frm.FromYY.disabled = true;
		frm.FromMM.disabled = true;
		frm.FromDD.disabled = true;
		frm.ToYY.disabled = true;
		frm.ToMM.disabled = true;
		frm.ToDD.disabled = true;
*/
	}
}


function init(){
//alert(1);
	var frm = document.search_frm;
//	onLoad('$sDate','$eDate');";

if($regdate != "1"){
	$Script .= "

	frm.sdate.disabled = true;
	frm.edate.disabled = true;";

	/*
$Script .= "
	frm.FromYY.disabled = true;
	frm.FromMM.disabled = true;
	frm.FromDD.disabled = true;
	frm.ToYY.disabled = true;
	frm.ToMM.disabled = true;
	frm.ToDD.disabled = true;";
	*/
}

$Script .= "
}

</script>";

$Contents = "
<script language='javascript' src='"."tglib_orders.php?page=$page&ctgr=$ctgr&qstr=$qstr"."'></script>
<table width='100%'>
<tr>
    <td align='left' colspan=6 > ".GetTitleNavigation("정산 상세내역", "매출관리 > 정산 상세내역 ")."</td>
</tr>

</table>  ";

$Contents = $Contents."
<table width='100%' cellpadding=0 cellspacing=0>
<tr>
		<td align='left' colspan=4 style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:3px;'><img src='../image/title_head.gif' align=absmiddle><b> 정산정보검색</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	</table>
	<form name='search_frm' method='get' action=''>
	<input type=hidden name='acc_view_type' value='$acc_view_type'>
	<input type=hidden name='mode' value='search'>
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
	</tr>";
if($admininfo[admin_level] == 9){
$Contents .= "
	<!--<tr height=30>
		<td class='search_box_title'> 업체검색  </td>
		<td class='search_box_item' colspan=3>".CompanyList2($company_id,"")."업체명 <input type='text' name='com_name' value=''></td>
	</tr>-->";
}
$Contents .= "

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
	<tr height=30>
		<td class='search_box_title'> 구매 대상</td>
		<td class='search_box_item' colspan=3>
		<input type='checkbox' name='mem_type[]'  id='mem_type_1' value='M' ".ReturnStringAfterCompare($status, "M", " checked")."><label for='mem_type_1'> 개인회원</label>
		<input type='checkbox' name='mem_type[]'  id='mem_type_2' value='C' ".ReturnStringAfterCompare($status, "C", " checked")."><label for='mem_type_2'> 개인사업자</label>
		<input type='checkbox' name='mem_type[]'  id='mem_type_3' value='C' ".ReturnStringAfterCompare($status, "C", " checked")."><label for='mem_type_3'> 법인사업자</label>
		<input type='checkbox' name='mem_type[]'  id='mem_type_4' value='C' ".ReturnStringAfterCompare($status, "C", " checked")."><label for='mem_type_4'> 간의과세자</label>

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
	</form>
</table>";
$Contents = $Contents."
<table border='0' cellspacing='1' cellpadding='15' width='100%'>
                <tr>
                  <td bgcolor='#F8F9FA'>
					<table border='0' cellspacing='0' cellpadding='0' width='100%'>
						<tr>
							<td height='20'><img src='../images/dot_org.gif' width='11' height='11' valign='absmiddle'> <b>주문제품정보</b></td>
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
											<!--td width='10%' class='m_td'><b>상품코드</b></td-->
											<td width='*' colspan=2 class='m_td'><b>주문번호/제품명</b></td>
											<td width='8%' class='m_td'><b>옵션</b></td>

											<td width='6%' class='m_td'><b>입점<br>배송방식</b></td>
											<td width='6%' class='m_td'><b>입점<br>정산방식</b></td>
											<td width='5%' class='m_td'><b>과세구분</b></td>
											<td width='5%' class='m_td'><b>구매대상</b></td>

											<td width='4%' class='m_td'><b>수량</b></td>
											<td width='8%'  class='m_td'><b>정산기준단가</b></td>
											<td width='7%' class='m_td'><b>정산기준금액</b></td>
											<td width='7%' class='m_td'><b>수수료율</b></td>
											<td width='7%' class='m_td small' nowrap><b>수수료</b></td>
											<td width='7%' class='m_td small' nowrap><b>배송비정산</b></td>
											<td width='7%' class='e_td small' nowrap><b>정산금액</b></td>
										</tr>";

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

/*
	if(is_array($mem_type)){
		$where .= " and od.mem_type in (";
		foreach($mem_type as $key =>$value){
			$where .= "'$value'";
			if(($key == 0 and count($account_type) != 1) or $key != count($mem_type)-1){
				$where .= ",";
			}
		}
		$where .=")";
	}
*/

//echo "<pre>";
//print_r ($_REQUEST);
//echo "$where";
//EXIT;
}else{
	if($startDate != "" && $endDate != ""){
		$where .= " and  date_format(od.dc_date,'%Y%m%d') between  $startDate and $endDate ";
	}
}


//echo "$where";exit;
	if($admininfo[admin_level] == 9){
		if($ac_ix){
			$sql = "SELECT od.oid, od.pid, od.pname, od.reserve, 
							od.pcnt, 
							od.psprice,
							od.option_text, po.option_etc1, od.status ,
							od.commission,
							if(od.account_type = '1' or od.account_type = '' ,od.ptprice,od.coprice) as ptprice, 
							if(od.account_type = '1' or od.account_type = '' ,od.ptprice*(100-od.commission)/100 , od.coprice*(100-od.commission)/100) as coprice,
							if(od.delivery_type = '1' or od.delivery_type = '' ,'0',od.delivery_price) as delivery_price,
							if(od.account_type = 1 or od.account_type = '',od.ptprice*(od.commission)/100,od.coprice*(od.commission)/100) as commission_price,
							od.surtax_yorn,
							od.account_type,
							od.delivery_type
							FROM ".TBL_SHOP_ORDER_DETAIL." od
							left outer join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po on od.option1 = po.id
							left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid
							WHERE od.ac_ix = '".$ac_ix."' and od.account_type != '3' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $where ";
		//	echo "<pre>";
		//	echo $sql;
			}else{
			$sql = "SELECT od.oid, od.pid, od.pname, od.reserve,
							od.pcnt, 
							od.psprice, 
							if(od.account_type = '1' or od.account_type = '' ,od.ptprice,od.coprice) as ptprice, 
							od.option_text,
							po.option_etc1, 
							od.status ,
							od.commission,
							if(od.account_type = '1' or od.account_type = '' ,od.ptprice*(100-od.commission)/100 , od.coprice*(100-od.commission)/100) as coprice,
							if(od.delivery_type = '1' or od.delivery_type = '' ,'0',od.delivery_price) as delivery_price,
							if(od.account_type = 1 or od.account_type = '',od.ptprice*(od.commission)/100,od.coprice*(od.commission)/100) as commission_price,
							od.surtax_yorn,
							od.account_type,
							od.delivery_type
							FROM ".TBL_SHOP_ORDER_DETAIL." od left outer join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po on od.option1 = po.id left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid
							WHERE od.company_id = '".$company_id."' and od.account_type != '3'  and od.status ='".ORDER_STATUS_DELIVERY_COMPLETE."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") 
							$where ";
			}

	}else if($admininfo[admin_level] == 8){
		$sql = "SELECT od.oid, od.pid, od.pname, od.reserve, 
						od.pcnt,
						od.psprice,
						if(od.account_type = '1' or od.account_type = '' ,od.ptprice,od.coprice) as ptprice, 
						od.option_text,
						po.option_etc1, 
						od.status,
						od.commission,
						if(od.account_type = '1' or od.account_type = '',od.ptprice*(100-od.commission)/100 , od.coprice*(100-od.commission)/100) as coprice,
						if(od.delivery_type = '1' or od.delivery_type = '','0',od.delivery_price) as delivery_price,
						if(od.account_type = 1 or od.account_type = '',od.ptprice*(od.commission)/100,od.coprice*(od.commission)/100) as commission_price,
						od.surtax_yorn,
						od.account_type,
						od.delivery_type
						FROM ".TBL_SHOP_ORDER_DETAIL." od left outer join ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL." po  on od.option1 = po.id  left join ".TBL_SHOP_PRODUCT." p on p.id = od.pid
						WHERE od.ac_ix = '".$ac_ix."' and od.account_type != '3'  and od.company_id = '".$admininfo[company_id]."' and status IN ('DC') and date_format(od.dc_date,'%Y%m%d') <= $endDate AND od.product_type NOT IN (".implode(',',$sns_product_type).") ";
	}


	//echo "<pre>";
	//echo "$sql";
	$db3->query($sql);


	$num = 1;

	$sum = 0;

	for($j = 0; $j < $db3->total; $j++){
		$db3->fetch($j);

		$pname = $db3->dt[pname];
		$pcode = $db3->dt[pcode];
		$count = $db3->dt[pcnt];

		$surtax_yorn = $db3->dt[surtax_yorn];	//부가세 
		$account_type = $db3->dt[account_type];	//정산방식
		$delivery_type = $db3->dt[delivery_type];//배송방식
		$mem_type = $db3->dt[mem_type];//배송방식

		switch($surtax_yorn){
			case 'N':
				$surtax_yorn_txt = "과세";
			break;
			case 'Y':
				$surtax_yorn_txt = "면세";
			break;
			case 'P':
				$surtax_yorn_txt = "영세";
			break;
			case '':
				$surtax_yorn_txt = "-";
			break;
		}

		switch($account_type){
			case '1':
				$account_type_txt = "중개(수수료률)";
			break;
			case '2':
				$account_type_txt = "매입";
			break;
			case '':
				$account_type_txt = "-";
			break;
			
		}

		switch($delivery_type){
			case '1':
				$delivery_type_txt = "위탁(통합배송)";
			break;
			case '2':
				$delivery_type_txt = "면세(개별배송)";
			break;
			case '':
				$delivery_type_txt = "-";
			break;
		
		}

		switch($mem_type){
			case 'M':
				$mem_type_txt = "개인회원";
			break;
			case 'C':
				$mem_type_txt = "법인사업자";
			break;
			case '':
				$mem_type_txt = "-";
			break;
		
		}
		if($account_type == '1'){
			$pt_price = $db3->dt[ptprice];	//입점 정산 방식이 1 중개 일경우 최종판매가
		}else if($account_type == '2'){
			$pt_price = $db3->dt[coprice];	//입점 정산 방식이 2 매입 일경우 공급가
		}

		if($delivery_type == '1'){
			$shipping_price = 0;
		}else if($delivery_type == '2'){	
			$shipping_price = $db3->dt[delivery_price];
		}

		//$option_div = $db3->dt[option_text];
		//$option_etc1 = $db3->dt[option_etc1];
		//$price = $db3->dt[psprice];
		//$coprice = $db3->dt[coprice];
		//$psprice =  += $db3->dt[psprice];	//정산기준단가
		$coprice_sum += $db3->dt[coprice];	//정산 총액
		$sumptprice = $sumptprice + $db3->dt[ptprice];
		$ptprice_sum += $db3->dt[ptprice];	//정산기준금액
		$commission_sum += $db3->dt[commission_price];	//수수료
		$account_sum += $db3->dt[delivery_price] + $db3->dt[ptprice] - $db3->dt[commission_price];		//정산금액
		$count_sum += $db3->dt[pcnt];
		$delivery_price_sum += $db3->dt[delivery_price];	//배송비정산

		$reserve = $db3->dt[reserve];
		$ptotal = $price * $count;
		$sum += $ptotal;
		
		if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($admin_config[mall_data_root]."/images/product", $db3->dt[pid], "s"))){
			$img_str = PrintImage($admin_config[mall_data_root]."/images/product", $db3->dt[pid], "s");
		}else{
			$img_str = "../image/no_img.gif";
		}

$Contents .= "
			<tr height='70' align='center'>
				<td >".$num."</td>
				<td ><div style='border:1px solid silver;padding:5px;width:60px;'><img src=\"".$img_str."\" width=50 height=50></div></td>
				<td ><div align='left' style='padding:5 0 5 10'><b class=blue>".$db3->dt[oid]."</b><br><!--a href=\"javascript:PoPWindow('/shop/goods_view.php?id=".$db3->dt[pid]."','1000','700','preview')\"--><a href='/shop/goods_view.php?id=".$db3->dt[pid]."' target='_blank'>".$db3->dt[pname]."</a></div></td>
				<td align=left style='padding-left:5px;'>".$db3->dt[option_text]."</td>

				<td align=center>".$delivery_type_txt."</td>
				<td align=center>".$account_type_txt."</td>
				<td align=center>".$surtax_yorn_txt."</td>
				<td align=center>".$mem_type_txt."</td>

				<td >".$db3->dt[pcnt]." 개</td>
				<td >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db3->dt[psprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db3->dt[ptprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td align=center>".number_format($db3->dt[commission])." %</td>
				<td >".$currency_display[$admin_config["currency_unit"]]["front"]." ".number_format($db3->dt[commission_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db3->dt[delivery_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
				<td align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db3->dt[delivery_price] + $db3->dt[ptprice] - $db3->dt[commission_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
			</tr>
			 <tr height=1><td colspan=15 class='dot-x'></td></tr>";

		$num++;
	}
$Contents = $Contents."
										<tr height='30' align='center'>
											<td colspan=7>합계</td>

											<td align=center></td>
											<td >".$count_sum." 개</td>
											<td ></td>
											<td align=center>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ptprice_sum)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td align=center></td>
											<td >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($commission_sum)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($delivery_price_sum)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
											<td>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($account_sum)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
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

<br>
<table width='100%' border='0' cellpadding='0' cellspacing='1'>
	<tr>
		<td>
		<img src='../image/title_head.gif' align=absmiddle>
		<font color='#000000'>정산 총액은 <b>".number_format($coprice_sum)."</b> 입니다.</font>".//getTransDiscription(md5($_SERVER["PHP_SELF"]),'A',$coprice_sum,"coprice_sum").
		"</td>";

unset($sumptprice);

$Contents .= "

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



$P = new LayOut();
$P->strLeftMenu = order_menu();
$P->OnloadFunction = "init();";
$P->addScript = $Script;
$P->Navigation = "정산관리 > 정산 상세내역";
$P->title = "정산 상세내역";
$P->strContents = $Contents;


echo $P->PrintLayOut();


function SelectQuickLink($QuickCode, $deliverycode){
	$divname = array ("#",
	"http://www.ilogen.com/customer/reserve_03-1_ok.asp?f_slipno=",
	"http://www.doortodoor.co.kr/jsp/cmn/Tracking.jsp?QueryType=3&pTdNo=",
	"http://samsunghth.com/homepage/searchTraceGoods/SearchTraceDtdShtno.jhtml?dtdShtno=",
	"#",
	"#",
	"http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=",
	"http://www.kgbls.co.kr/tracing.asp?number=",
	"http://www.yellowcap.co.kr/branch/chase/listbody.html?a_gb=branch&a_cd=5&a_item=0&f_slipno=",
	"#");


	return "<a href='".$divname[$QuickCode]."$deliverycode' target=_blank>$deliverycode</a>";

}

?>