<?

/////////////////////////////////////////////////////////////

// 제목 : 상품 옵션관리

// 작성 : 이학봉(2013-04-24)

// 수정 : 정산예정내역 변경 작업에 관한 수정 : 이학봉(2013-04-24)
// 수정 내용 : 
//sum(if(od.account_type = '1' or od.account_type = '',od.ptprice,od.coprice)) as sell_total_ptprice,
//sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(100-od.commission)/100,od.coprice*(100-od.commission)/100)) as sell_total_coprice,
//sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, 
//sum(if(od.delivery_type = '2',od.delivery_price,'0')) as shipping_price,
//sum(if(o.method = 1,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as card_ptprice,
//sum(if(o.method = 5 or o.method = 8 or o.method = 0 or o.method = 4 ,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as bank_ptprice,
//	
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
*/
/////////////////////////////////////////////////////////////



include("../class/layout.class");
include ("./accounts.lib.php");
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


if ($vToYY == ""){
	$before10day = mktime(0, 0, 0, date("m")  , date("d")-20, date("Y"));
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());

//	$sDate = date("Y/m/d");
	$sDate = date("Y/m/d", time()-84600*(date("d")-1));
	$eDate = date("Y/m/d", time()+84600*(31-date("d")));

	$startDate = date("Ymd", time()-84600*(date("d")-1));
	$endDate = date("Ymd", time()+84600*(31-date("d")));

	//if($admininfo[admin_level] == 8){
		$eDate = date("Y/m/t",strtotime('-1 month'));
		$endDate = date("Ymt",strtotime('-1 month'));
	//}
}else{
	$vdate = date("Ymd", time());
	$today = date("Ymd", time());

	$sDate = $vFromYY."/".$vFromMM."/".$vFromDD;
	$eDate = $vToYY."/".$vToMM."/".$vToDD;
	$startDate = $vFromYY.$vFromMM.$vFromDD;
	$endDate = $vToYY.$vToMM.$vToDD;
}
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


$db = new Database;

/*
$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." WHERE mall_ix='".$admininfo[mall_ix]."' ");
$db->fetch();

$account_priod = $db->dt[account_priod];
*/
$where=" AND od.product_type NOT IN (".implode(',',$sns_product_type).") ";

/*
if ($vFromYY != ""){
	//$where .= "and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate ";
	$where .= "and date_format(od.dc_date,'%Y%m%d') <= $endDate ";
}else{
	//$where .= "and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate ";
	$where .= "and date_format(od.dc_date,'%Y%m%d') <= $endDate ";
}
*/
if($mode == "search"){

	if($sdate != "" && $edate != ""){
		$where = " and  date_format(od.dc_date,'%Y%m%d') between  $sdate and $edate ";
	}
}

if($admininfo[admin_level] == 9){


	if($company_id != "") $where .= " and c.company_id='$company_id' ";

	if($admininfo[mem_type] == "MD"){
		$where .= " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
	}

	if($db->dbms_type == "oracle"){
		$sql = "SELECT od.company_id as company_id,c.com_name,bank_name,bank_number,bank_owner ,
				sum(od.pcnt) as sell_cnt,


				sum(if(od.account_type = '1' or od.account_type = '',od.ptprice,od.coprice)) as sell_total_ptprice,
				sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, 
				sum(if(od.delivery_type = '2',od.delivery_price,'0')) as shipping_price,
				sum(if(o.method = 1,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as card_ptprice,
				sum(if(o.method = 5 or o.method = 8 or o.method = 0 or o.method = 4 ,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as bank_ptprice,
				sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(od.commission)/100,od.coprice*(od.commission)/100)) as commission_price,
				od.account_type as account_type,
				od.delivery_type as delivery_type,
				od.surtax_yorn as surtax_yorn,
				od.regdate as order_com_date,
				avg(od.commission) as avg_commission,
				count(*) as order_cnt
				FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid
				left join ".TBL_COMMON_COMPANY_DETAIL." c on od.company_id = c.company_id
				left join ".TBL_COMMON_SELLER_DETAIL." csd on c.company_id = csd.company_id
				WHERE  od.status = 'DC' and od.account_type != '3' and od.company_id is not null  $where group by od.company_id,c.com_name,bank_name,bank_number,bank_owner,od.regdate  " ; // and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate
	}else{
		$sql = "SELECT c.com_name,od.company_id as company_id,bank_name,bank_number,bank_owner ,
				sum(od.pcnt) as sell_cnt, 

				sum(if(od.account_type = '1' or od.account_type = '',od.ptprice,od.coprice)) as sell_total_ptprice,
				sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, 
				sum(if(od.delivery_type = '2',od.delivery_price,'0')) as shipping_price,
				sum(if(o.method = 1,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as card_ptprice,
				sum(if(o.method = 5 or o.method = 8 or o.method = 0 or o.method = 4 ,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as bank_ptprice,
				sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(od.commission)/100,od.coprice*(od.commission)/100)) as commission_price,
				od.account_type as account_type,
				od.delivery_type as delivery_type,
				od.surtax_yorn as surtax_yorn,
				od.regdate as order_com_date,
				avg(od.commission) as avg_commission, 
				count(*) as order_cnt
				FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid
				left join ".TBL_COMMON_COMPANY_DETAIL." c on od.company_id = c.company_id
				left join ".TBL_COMMON_SELLER_DETAIL." csd on c.company_id = csd.company_id
				WHERE  od.status = 'DC' and od.account_type != '3' and od.company_id is not null  $where group by od.company_id " ; // and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate
	}
//echo nl2br($sql);
//and o.status = 'DC'
}else if($admininfo[admin_level] == 8){
	$where .= " and c.company_id = '".$admininfo[company_id]."'";

	if($db->dbms_type == "oracle"){
		$sql = "SELECT c.com_name,od.company_id as company_id,bank_name,bank_number,bank_owner ,sum(od.pcnt) as sell_cnt, 

			sum(if(od.account_type = '1' or od.account_type = '',od.ptprice,od.coprice)) as sell_total_ptprice,
			sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, 
			sum(if(od.delivery_type = '2',od.delivery_price,'0')) as shipping_price,
			sum(if(o.method = 1,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as card_ptprice,
			sum(if(o.method = 5 or o.method = 8 or o.method = 0 or o.method = 4 ,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as bank_ptprice,
			sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(od.commission)/100,od.coprice*(od.commission)/100)) as commission_price,
			od.account_type as account_type,
			od.delivery_type as delivery_type,
			od.surtax_yorn as surtax_yorn,
			od.regdate as order_com_date,
			avg(od.commission) as avg_commission, count(*) as order_cnt
			FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid
			left join ".TBL_COMMON_COMPANY_DETAIL." c on od.company_id = c.company_id
			left join ".TBL_COMMON_SELLER_DETAIL." csd on c.company_id = csd.company_id
			WHERE  od.status = 'DC' and od.account_type != '3' and od.company_id is not null  $where group by od.company_id,c.com_name,bank_name,bank_number,bank_owner,od.regdate " ; // and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate
			//and o.status = 'DC'
	}else{
		$sql = "SELECT c.com_name,od.company_id as company_id,bank_name,bank_number,bank_owner ,
			sum(od.pcnt) as sell_cnt, 

			sum(if(od.account_type = '1' or od.account_type = '',od.ptprice,od.coprice)) as sell_total_ptprice,
			sum(case when o.delivery_price > 0 then 1 else 0 end) as pre_shipping_cnt, 
			sum(if(od.delivery_type = '2',od.delivery_price,'0')) as shipping_price,
			sum(if(o.method = 1,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as card_ptprice,
			sum(if(o.method = 5 or o.method = 8 or o.method = 0 or o.method = 4 ,if(od.account_type = 1 or od.account_type = '', ptprice,coprice),'')) as bank_ptprice,
			sum(if(od.account_type = 1 or od.account_type = '',od.ptprice*(od.commission)/100,od.coprice*(od.commission)/100)) as commission_price,
			od.account_type as account_type,
			od.delivery_type as delivery_type,
			od.surtax_yorn as surtax_yorn,
			od.regdate as order_com_date,
			avg(od.commission) as avg_commission, 
			count(*) as order_cnt
			FROM ".TBL_SHOP_ORDER_DETAIL." od left join ".TBL_SHOP_ORDER." o on o.oid = od.oid
			left join ".TBL_COMMON_COMPANY_DETAIL." c on od.company_id = c.company_id
			left join ".TBL_COMMON_SELLER_DETAIL." csd on c.company_id = csd.company_id
			WHERE  od.status = 'DC' and od.account_type != '3' and od.company_id is not null  $where group by od.company_id " ; // and date_format(od.regdate,'%Y%m%d') between $startDate and $endDate
			//and o.status = 'DC'
	}
}

//echo $sql;
$db->query($sql);
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
}

$Contents = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' >
	<!--col width='25%' />
	<col width='25%' />
	<col width='25%' />
	<col width='25%' /-->
	<tr>
		<td align='left' colspan=4>".GetTitleNavigation("정산예정내역", "주문관리 > 정산예정내역")."</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding:10px 0px 4px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:3px;'><img src='../image/title_head.gif' align=absmiddle><b> 정산정책</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	</table>
	<form name='search_frm' method='get' action='accounts_plan_price.php'>
	<input type='hidden' name='act' value='account_info_update'>
	<input type='hidden' name='mode' value='search'>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='list_table_box'>
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
$Contents .= "<tr height=30>
		<td class='search_box_title'>업체검색  </td>
		<td class='search_box_item'><!--업체명 <input type='text' name='com_name' value=''-->".CompanyList2($company_id,"")."</td>
	</tr>";
}

$Contents .= "
	<!--tr bgcolor=#ffffff height=30>
		<td class='search_box_title'><img src='../image/ico_dot.gif' align=absmiddle>정산수행</td>
		<td class='search_box_item' >
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
	<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left' >
	<tr bgcolor=#ffffff>
		<td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0 style='cursor:pointer;border:0px;' ></td>
	</tr>
	</form>

	<tr>
		<td align='right' colspan=4><a href='accounts_plan_price_excel2003.php?mode=excel&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a></td>
	</tr>
	<tr>
		<td align='left' colspan=4 > ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=absmiddle><b> 정산예정내역</b><span class=small> &nbsp;&nbsp;&nbsp;</span></div>")."</td>
	</tr>
	<!--tr>
		<td align='left' colspan=4><input type='radio' name='view_type' value='SV' onclick=\"document.location.href='accounts_plan.php'\"> 금액간단히보기 <input type='radio' name='view_type' value='DV' onclick=\"document.location.href='accounts_plan_price.php'\" checked> 금액자세히보기</td>
	</tr-->
	<tr bgcolor=#ffffff >
		<td colspan=4 align=right>
			<form name=listform method=post action='accounts.act.php' onsubmit=\"alert(language_data['accounts_plan_price.php']['A'][language]);return false;\" style='display:inline;' target='act'><!--일괄정산 준비중입니다-->
			<input type='hidden' name='act' value='select_accounts_update'>
			<input type='hidden' name='page' value='$page'>
			<input type=hidden name='endDate' value='$endDate'>
			<input type=hidden name='com_name' value='$com_name'>
			<input type=hidden id='company_id' value=''>

			<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box'>
				<tr height=27>
					<td width='2%' align='center' class='s_td' rowspan='2'><input type=checkbox  name='all_fix' onclick='fixAll(document.listform)'></td>
					<td width='4%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>NO</b></font></td>
		
					<td width='8%' align='center' class='m_td' rowspan='2'><font color='#000000'><b>업체명</b></font></td>

					<td width='13%' align='center' class='m_td' colspan='2'><font color='#000000'><b>정산기준금액</b></font></td>
					<td width='5%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000'><b>배송비</b></font></td>
					<td width='5%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000'><b>정산기준금액<br>(카드+현금)</b></font></td>
					<td width='6%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000'><b>정산수수료</b></font></td>
					<td width='18%' align='center' class='m_td' rowspan='2' nowrap><font color='#000000'><b>정산금액</b><br>(배송비+정산기준금액-정산수수료)</font></td>
					";

if($admininfo[admin_level] == 9){
$Contents .= "
				<td width='8%' align='center' class='e_td' rowspan='2' nowrap><font color='#000000'><b>정산여부</b></font></td>";
}else if($admininfo[admin_level] == 8){
$Contents .= "
			<td width='8%' align='center' class='e_td' rowspan='2' nowrap><font color='#000000'><b>정산확인</b></font></td>";
}

$Contents .= "</tr>";
$Contents .= "<tr height=27>
				<td width='5%' align='center' class='m_td'  nowrap><font color='#000000'><b>현금</b></font></td>
				<td width='5%' align='center' class='m_td'  nowrap><font color='#000000'><b>카드</b></font></td>
			 </tr>";

	if($db->total){
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);

			//$Contents .= get_acctoun_plan($admininfo,$currency_display,$admin_config,$where,$db->dt[company_id]);// 각 거래타입,과세구분,구매대상,판매구분에 대한 분리 
				$Contents .= "
				<tr onMouseOver=\"this.style.backgroundColor='#E8ECF1'; \" onMouseOut=\"this.style.backgroundColor=''\" height=30>
					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center'><input type=checkbox name='company_id[]' id='company_id' value='".$db->dt[company_id]."'></td>
					<td class='list_box_td' align='center' nowrap>".($i+1)."</td>
					<td class='list_box_td list_bg_gray'  bgcolor='#EAEAEA' align='center'><a href='accounts_detail.php?company_id=".$db->dt[company_id]."'>".$db->dt[com_name]."123</a></td>

					<td class='list_box_td' align='left'><div style='padding-left:3px;'>".number_format($db->dt[bank_ptprice])." 원 </div></td>

					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center' nowrap>".number_format($db->dt[card_ptprice])." 원 </td>

					<td class='list_box_td' align='center' nowrap>".($db->dt[shipping_price]? $db->dt[shipping_price]:"0 원")."</td>

					<td class='list_box_td list_bg_gray' bgcolor='#EAEAEA' align='center'>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[sell_total_ptprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>

					<td class='list_box_td' align='center'  nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[commission_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td class='list_box_td point' bgcolor='#EAEAEA' align='center' >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[shipping_price] + $db->dt[sell_total_ptprice] - $db->dt[commission_price])." ".$currency_display[$admin_config["currency_unit"]]["back"]." </td>
					";

					if($admininfo[admin_level] == 9){
					$Contents .= "<td class='list_box_td list_bg_gray' align='center' bgcolor='#EAEAEA'>
									<!--<a href='#' onclick=\"PoPWindow('accounts_taxbill.php?company_id=".$db->dt[company_id]."&startDate=$startDate&endDate=$endDate',680,450,'account_taxbill')\"><img src='../images/".$admininfo["language"]."/btn_taxbill_view.gif' border=0 align=absmiddle></a>-->
									<a href=\"javascript:Account('".$db->dt[company_id]."','$endDate')\"><img src='../images/".$admininfo["language"]."/btn_account_confirm.gif' align=absmiddle ></a>
									</td>";

					}else if($admininfo[admin_level] == 8){
					$Contents .= "<td class='list_box_td list_bg_gray' align='center' bgcolor='#EAEAEA'><a href=\"javascript:Account('".$db->dt[company_id]."','$endDate')\"><img src='../images/".$admininfo["language"]."/btn_account_confirm.gif' align=absmiddle ></a></td>";
					}

			$Contents .= "</tr>";
			$sell_total_ptprice = $sell_total_ptprice + $db->dt[sell_total_ptprice];
			//$sell_total_coprice = $sell_total_coprice + $db->dt[sell_total_coprice];
			$sell_total_skpoint = $sell_total_skpoint + $db->dt[sk_point];
			$card_price = $card_price +  $db->dt[card_ptprice];
			$bank_price = $bank_price +  $db->dt[bank_ptprice];
			$shipping_price = $shipping_price +  $db->dt[shipping_price];
			$commission_price = $commission_price + $db->dt[commission_price];

		}
		$sell_total_coprice =$shipping_price + $sell_total_ptprice - $commission_price;
	$Contents .= "
				<tr bgcolor=#ffffff height=30>
					<td colspan=3 style='padding-right:20px;' class='point'>";
if($admininfo[admin_level] == 9){
	//$Contents .= "<input type=image  src='../image/btn_whole_accounts.gif' border=0 style='cursor:pointer;border:0px;' >";
}
	$Contents .= "합계 :
					</td>
					<td align='center' class=' str' nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($bank_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td align='center' class='list_bg_gray str' nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($card_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td align='center' class=' str' nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($shipping_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>


					<td align='center' class='list_bg_gray str' nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($sell_total_ptprice)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td align='center' class='str' nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($commission_price)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td align='center' class='point' nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($sell_total_coprice)." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					<td colspan=1></td>
				</tr>
				<!--tr bgcolor=#ffffff ><td colspan=10 align=right><input type='image' src='../images/".$admininfo["language"]."/btn_account.gif' border=0 style='cursor:pointer;border:0px;' > </td></tr-->
				</form>";

	}else{
		$Contents .= "
				<tr height=50><td colspan=10 align=center>정산 대기내역이 없습니다</td></tr>
				";
	}

	/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >배송완료후의 정산기준일자를 선택한 후 검색 합니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >정산정책에 따라  정산대기 내역이 입점업체 별로 리스트업 됩니다</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >업체명을 클릭하시면 정산대기 내역에 대한 상세 내역을 확인하실수 있습니다</td></tr>
	<!--tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >정산대기내역이 확인 되었으면 정산 버튼을 클릭합니다. 정산이 완료된 금액은 나의 통장으로 입금 되게 됩니다</td></tr-->
</table>
";
*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= "	</table>
	  	</td>
	  </tr>
	  <tr><td colspan=4>".HelpBox("정산정책관리", $help_text)."</td></tr>
	  </table><br>
	  <form name=account_frm method=post action='accounts.act.php' onsubmit=\"alert(language_data['accounts_plan_price.php']['A'][language]);return false;\" target='act'><!--target='act' 일괄정산 준비중입니다-->
			<input type='hidden' name='act' value='account'>
			<input type=hidden name='eDate' value='$endDate'>
			<input type=hidden name='company_id' value=''>
		</form>
	  ";

$Script .= "<script lanaguage='javascript'>
function Account(company_id, edate){
	var frm = document.account_frm;

	if(confirm(language_data['accounts_plan_price.php']['B'][language])){//해당 정산내용을 정산확인 처리 하시겠습니까?
		frm.company_id.value = company_id
		frm.submit();
	}
}

</script>";

$P = new LayOut();
$P->addScript = "<script language='javascript' src='account.js'></script>\n".$Script;
$P->OnloadFunction = "init();";
$P->strLeftMenu = order_menu();
$P->Navigation = "주문관리 > 정산 예정 내역";
$P->title = "정산 예정 내역";
$P->strContents = $Contents;
echo $P->PrintLayOut();



?>