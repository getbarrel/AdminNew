<?
$script_time[start] = time();
include_once("../class/layout.class");
include("../class/calender.class");
include("./member_region.chart.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
include("../lib/report.lib.php");
$script_time[start] = time();
//print_r($admininfo);
$gnb_menus = topmenu('/admin/basic', "array");
if($_SESSION["admininfo"][charger_id] == "forbiz"){
	//echo "메인페이지";
	//exit;
}
if($gnb_menus[0]["basic_link"] != $_SERVER["PHP_SELF"]){
	//header("Location:".$gnb_menus[0]["basic_link"]);
	echo "<script language='javascript'>document.location.href='".$gnb_menus[0]["basic_link"]."'</script>";
	exit;
}

//$db = new Database;
//$mdb = new Database;
//$sms_design = new SMS;
//print_r($admininfo);
//print_r($admin_config); //
$Script = "
<style>
TABLE {
	border-collapse: collapse; FONT-SIZE: 9pt;
}

TD {border-collapse: collapse; }

DIV {
	FONT-SIZE: 9pt; FONT-FAMILY: TTche
}

INPUT {
	FONT-SIZE: 9pt; FONT-FAMILY: TTche
}

SELECT {
	FONT-SIZE: 9pt; FONT-FAMILY: TTche
}



a.calendar_year:link {font-family:돋움; font-size:11px;text-decoration:none; color:#ffffff}
a.calendar_year:visited {font-family:돋움; font-size:11px; text-decoration:none; color:#ffffff}
a.calendar_year:hover {font-family:돋움; font-size:11px; text-decoration:none; color:#ea4200;font-weight:bold;}
a.calendar_year:active {font-family:돋움; font-size:11px; text-decoration:none; color:#ffffff}

.calendar{
	border:solid 1px #C5C5C5;
	padding:0px;
	margin:0px;
	text-align:center;
	vertical-align:middle;
	/*WIDTH: 30px;*/
	CURSOR: pointer;
	/*COLOR: #2343a1; */
	color:gray;
	/*BACKGROUND-COLOR: #efefef; */
	BACKGROUND-COLOR: #efefef;
	TEXT-DECORATION: none;
	HEIGHT:23px
}

.calendar_on{
	border-top: 1px solid #C5C5C5;
	border-left: 1px solid #C5C5C5;
	Border-right:#ffffff 1px solid;
	Border-bottom:#ffffff 1px solid;
	padding:0px;
	CURSOR: hand;
	COLOR: #ffffff;
	text-align:center;
	vertical-align:middle;
	BACKGROUND-COLOR: #ea4200; /*#efefef*/
	TEXT-DECORATION: none;
	height:23px;
}
.calendar_on a{TEXT-DECORATION: none; COLOR: #ffffff; }

.calendar_main{
	border-top: #ffffff 1px solid;
	border-left: #ffffff 1px solid;
	Border-right:1px solid #C5C5C5;
	Border-bottom:1px solid #C5C5C5;
	padding:0px;
	CURSOR: hand;
	COLOR: #ffffff;
	BACKGROUND-COLOR: gray;
	TEXT-DECORATION: none;
	height:23px;
	width:200px;

}



.calendar_outbox{
	margin:0px;
	padding:0px;
	CURSOR: hand;
	COLOR: #ffffff;
	BACKGROUND-COLOR: gray;
	TEXT-DECORATION: none;
	height:23px;

}



.calendarWeekHeadOff{
	border-bottom:solid 1px #C5C5C5;
	border-left: #C5C5C5 1px solid;
	padding:0px;
	CURSOR: hand;
	COLOR: gray;

	BACKGROUND-COLOR: #efefef;
	TEXT-DECORATION: none;
	HEIGHT:23px
}

.calendarWeekHeadOn
{
	border-top: #C5C5C5 1px solid;
	border-left: #C5C5C5 1px solid;
	Border-bottom:1px solid #ffffff;

	CURSOR: hand;
	COLOR: #ffffff;
	BACKGROUND-COLOR: #ea4200;
	TEXT-DECORATION: none;
	height:23px;
}
.calendarWeekHeadOn a
{
	COLOR: #ffffff;
	text-decoration:none;
}

.calendarWeekMiddleOff{
	Border-bottom:1px solid #C5C5C5;
	padding:0px;
	CURSOR: hand;
	COLOR: gray;
	BACKGROUND-COLOR: #efefef;
	TEXT-DECORATION: none;
	height:23px;
}
.calendarWeekMiddleOn
{
	Border-bottom:1px solid #ffffff;
	padding:0px;
	CURSOR: hand;
	COLOR: #ffffff;
	BACKGROUND-COLOR: #ea4200;
	TEXT-DECORATION: none;
	height:23px;
}
.calendarWeekMiddleOn a
{
	COLOR: #ffffff;
	text-decoration:none;
}

.calendarWeekTailOff{
	border-top: #ffffff 1px solid;
	border-left: #C5C5C5 1px solid;
	Border-bottom:1px solid #C5C5C5;
	padding:0px;
	CURSOR: hand;
	COLOR: #2343a1;
	POSITION: relative; TOP: 0px;
	BACKGROUND-COLOR: #efefef;
	TEXT-DECORATION: none;
	height:23px;
}

.calendarWeekTailOn,
{
	border-top: #C5C5C5 1px solid;
	BORDER-Right: #ffffff 1px solid;
	Border-bottom:1px solid #ffffff;
	padding:0px;
	CURSOR: hand;
	COLOR: #ffffff;
	POSITION: relative; TOP: 0px;
	BACKGROUND-COLOR: #ea4200;
	TEXT-DECORATION: none;
	height:23px;
}

.calendarWeekTailOn a
{
	COLOR: #ffffff;
	text-decoration:none;
}



.calendarHeader{
	/*border-top: #ffffff 1px solid;*/
	border-left: #C5C5C5 1px solid;
	border-top: #C5C5C5 1px solid;
	border-right:1px solid #C5C5C5;
	border-bottom:1px solid #C5C5C5;
	vertical-align:middle;
	CURSOR: pointer;
	COLOR: #ffffff;
	BACKGROUND-COLOR: gray;
	TEXT-DECORATION: none;
	FONT-WEIGHT:BOLD;
	height:23px;


}

.calendar_outbox{border: #C5C5C5 1px solid;}
.calendarHeaderDisp a{color:#ffffff;}

.calendarButton{
	/*
	border-top: #C5C5C5 1px solid;
	border-left: #C5C5C5 1px solid;
	Border-right:1px solid #C5C5C5;
	Border-bottom:1px solid #C5C5C5;
	*/
	CURSOR: hand;
	COLOR: #ffffff;
	BACKGROUND-COLOR: gray;
	TEXT-DECORATION: none;
	/*FONT-WEIGHT:BOLD;*/
	height:23px;
	font-size:11px;


}

.calendarToday{
	border-top: #ffffff 1px solid;
	border-left: #ffffff 1px solid;
	Border-right:1px solid #C5C5C5;
	Border-bottom:1px solid #C5C5C5;
	vertical-align:middle;
	text-align:center;
	CURSOR: pointer;
	COLOR: #2343a1;
	BACKGROUND-COLOR: orange;
	TEXT-DECORATION: bold;
	height:23px;
}
</style>

<script language='javascript' src='shop_main_v3_calender.js'></script>

<Script language='javascript'>
function showTabContents(vid, tab_id){
	var area = new Array('recent_order','recent_contents','recent_use_after');
	var tab = new Array('tab_01','tab_02','tab_03');

	for(var i=0; i<area.length; ++i){
		if(area[i]==vid){
			document.getElementById(vid).style.display = 'block';
			document.getElementById(tab_id).className = 'on';
		}else{
			document.getElementById(area[i]).style.display = 'none';
			document.getElementById(tab[i]).className = '';
		}
	}
}

function useAfterDelete(uf_ix){
	if(confirm('사용후기를 정말로 삭제하시겠습니까? ')){
		document.frames['act'].location.href='../marketting/useafter.act.php?act=delete&uf_ix='+uf_ix
	}
}
function checkAllkrDomain(obj){
	var frm = document.domain_search;
	for(var i=0;i<frm.kdomain.length;i++){
		if(obj.checked){
			frm.kdomain[i].checked = true;
		}else{
			frm.kdomain[i].checked = false;
		}
	}
}

function checkAllcomDomain(obj){
	var frm = document.domain_search;
	for(var i=0;i<frm.edomain.length;i++){
		if(obj.checked){
			frm.edomain[i].checked = true;
		}else{
			frm.edomain[i].checked = false;
		}
	}
}

$(document).ready(function (){
	//PoPWindow('../qna_pop.php',600,400,'add_brand_category');
});
</Script>";
/*
if($_SESSION["admininfo"]["mall_type"] == "B" || $_SESSION["admininfo"]["mall_type"] == "F" || $_SESSION["admininfo"]["mall_type"] == "R"  || $_SESSION["admininfo"]["mall_type"] == "BW"  || $_SESSION["admininfo"]["mall_type"] == "S"){
	include($_SERVER["DOCUMENT_ROOT"]."/include/pear/SOAP/Client.php");
	$soapclient = new SOAP_Client("http://www.mallstory.com/admin/service/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
	//팝업 2012-10-23 홍진영
	$getPopupinfo = (array)$soapclient->call("getPopup",$params = array("popup_ix"=> ''),$options);

	if($getPopupinfo[0]!=""){
		$Script .=  MallstoryServicePopUp($getPopupinfo);
	}
}
*/

//$script_time[sms_start] = time();
//$sms_cnt = $sms_design->getSMSAbleCount($admininfo);
//$script_time[sms_end] = time();

include($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
$shmop = new Shared("shop_main_summary");

if(empty($_SESSION["layout_config"]["mall_data_root"])){
	$sql = "select mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
	$db->query($sql);
	$db->fetch();			

	$mall_data_root = $db->dt[mall_data_root];
}else{
	$mall_data_root = $_SESSION["layout_config"]["mall_data_root"];
}

$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$mall_data_root."/_shared/";
$shmop->SetFilePath();
$data = $shmop->getObjectForKey("shop_main_summary");
$main_data = unserialize(urldecode($data));

$Contents01 = "
<table cellpadding=0 cellspacing=0 border='0' align='left' style='margin-top:10px;width:100%;'>
	<tr>
		<td width=100% valign=top style='padding:3px 0px'>
		<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
		  <tr height=20><td style='padding:20px 0px 5px 0px;border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>최근 주문현황</b></td><td style='padding:20px 0px 5px 0px;' align='right'></td></tr>
		  <tr>
			<td style='padding:3px 0px' colspan='2'>";
$script_time[order_history_start] = time();
	$Contents01 .= "				".PrintOrderHistory($main_data['PrintOrderHistory'])."";
$script_time[order_history_end] = time();
	$Contents01 .= "
			</td>
		  </tr>
		  <tr height=20><td style='padding:20px 0px 5px 0px;border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>금일의매출액(종합)</b></td><td style='padding:20px 0px 5px 0px;' align='right'><a href='../logstory/commerce/salessummery.php'>상세내역보기</a></td></tr>
		  <tr>
			<td style='padding:3px 0px' colspan='2'>";
$script_time[today_report_start] = time();
	$Contents01 .= "				".salesByDateReportTable($vdate,'today',1,$main_data['salesByDateReportTable']['today'])."";
$script_time[today_report_end] = time();
	$Contents01 .= "
			</td>
		  </tr>
		  <tr> 
		    <td>'메인은 적립금을 포함한 실 매출액이 노출되고 있습니다. 적립금을 제외한 실매출액을 확인하시려면 이커머스 분석 > 매출요약에서 확인해주세요.'</td>
		  </tr>
		  <tr height=20><td style='padding:20px 0px 5px 0px;border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>이달의매출액(종합)</b></td><td style='padding:20px 0px 5px 0px;' align='right'><a href='../logstory/commerce/salessummery.php?SelectReport=3'>상세내역보기</a></td></tr>
		  <tr>
			<td style='padding:3px 0px' colspan='2'>";
$script_time[month_report_start] = time();
	$Contents01 .= "				".salesByDateReportTable($vdate,'month',1,$main_data['salesByDateReportTable']['month'])."";
$script_time[month_report_end] = time();
/*
	$Contents01 .= "
			</td>
		  </tr>
		 <tr height=20><td style='padding:20px 0px 5px 0px;border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>금일의매출액(판매처)</b></td><td style='padding:20px 0px 5px 0px;' align='right'><a href='../order/salesbydate_from.php'>상세내역보기</a></td></tr>
		  <tr>
			<td style='padding:3px 0px' colspan='2'>";
*/
$script_time[today_from_report_start] = time();
	//$Contents01 .= "				".salesByDateFromReportTable($vdate,'today',1,$main_data['salesByDateFromReportTable']['today'])."";
$script_time[today_from_report_end] = time();
/*
	$Contents01 .= "
			</td>
		  </tr>
		  <tr height=20><td style='padding:20px 0px 5px 0px;border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>이달의매출액(판매처)</b></td><td style='padding:20px 0px 5px 0px;' align='right'><a href='../order/salesbydate_from.php'>상세내역보기</a></td></tr>
		  <tr>
			<td style='padding:3px 0px' colspan='2'>";
*/
$script_time[month_from_report_start] = time();
	//$Contents01 .= "				".salesByDateFromReportTable($vdate,'month',1,$main_data['salesByDateFromReportTable']['month'])."";
$script_time[month_from_report_end] = time();
	$Contents01 .= "
			</td>
		  </tr>
		  <tr> 
		    <td>'메인은 적립금을 포함한 실 매출액이 노출되고 있습니다. 적립금을 제외한 실매출액을 확인하시려면 이커머스 분석 > 매출요약에서 확인해주세요.'</td>
		  </tr>
		  <tr height=20><td style='padding:20px 0px 5px 0px;border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>회원현황</b></td><td style='padding:20px 0px 5px 0px;' align='right'></td></tr>
		  <tr>
			<td style='padding:3px 0px' colspan='2'>";
	$Contents01 .= "".PrintMemberTable($main_data['PrintMemberTable'])."";
	$Contents01 .= "
			</td>
		  </tr>";
	/*
		$Contents01 .= "
		  <tr height=20><td style='padding:20px 0px 5px 0px;border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>이달의매출액(사이트)</b></td><td style='padding:20px 0px 5px 0px;' align='right'><a href='../order/salesbydate_memtype.php'>상세내역보기</a></td></tr>
		  <tr>
			<td style='padding:3px 0px' colspan='2'>";
	$Contents01 .= "
				".salesByDateMemypeReportTable($vdate,'month')."";

	$Contents01 .= "
			</td>
		  </tr>
		  <tr height=20><td style='padding:20px 0px 5px 0px;border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>이달의매출액(결제타입)</b></td><td style='padding:20px 0px 5px 0px;' align='right'><a href='../order/salesbydate_paymenttype.php'>상세내역보기</a></td></tr>
		  <tr>
			<td style='padding:3px 0px 43px 0px' colspan='2'>";
	$Contents01 .= "
				".salesByDatePaymenttypeReportTable($vdate,'month')."";
	$Contents01 .= "
			</td>
		  </tr>";
	*/
	$Contents01 .= "
		</table>
		</td>
	</tr>
</table>";

$Contents = $Contents01;

if($_SESSION["admininfo"][charger_id] == "forbiz"){
	//print_r($script_time);
}

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
$P->Navigation = "메인화면";
$P->TitleBool = false;
$P->ServiceInfoBool = false;
$P->ContentsWidth = "98%";
echo $P->PrintLayOut();

$script_time[end] = time();




function PrintOrderHistory($datas){
	
	global $admininfo,$sns_product_type;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Y-m-d", time()-84600*date("w"));
	$lastday = date("Y-m-d", time()+84600*(6-date("w")));
	
	/*
	$addWhere="";

	if($_SESSION["admininfo"][admin_level] == 9){
		if($_SESSION["admininfo"][mem_type] == "MD"){
			$addWhere = " and od.company_id in (".getMySellerList($_SESSION["admininfo"][charger_ix]).") ";
		}
	}else if($_SESSION["admininfo"][admin_level] == 8){
		$addWhere = " and od.company_id = '".$_SESSION["admininfo"][company_id]."'";
	}
	
	$addWhere .=" and status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."','".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') ";

	if($odb->dbms_type == "oracle"){
		//+ index(od IDX_OD_REGDATE)
		$sql = "Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				1 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate =  to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				union
				Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				2 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate = to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				union
				Select '".date("m/d")." 오늘 ',
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				3 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate =  to_date('".date("Y-m-d")."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				union
				Select  '최근1주',
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				4 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od
				where to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."','%Y-%m-%d')  <=  od.regdate
				and od.regdate <= to_date('".date("Y-m-d")."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				union
				Select  '금주',
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				5 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od where  to_date('".$firstday."','%Y-%m-%d') <= od.regdate  and od.regdate <=   to_date('".$lastday."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				union
				Select  '최근1개월',
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				6 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od
				where to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."','%Y-%m-%d') <= od.regdate
				and od.regdate <= to_date('".date("Y-m-d")."','%Y-%m-%d')
				AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				union
				Select  '전체',
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				7 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od
				WHERE od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				order by vieworder asc ";
	}else{
		$sql = "	Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				1 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate between '".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))." 00:00:00' and '".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))." 23:59:59' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				union
				Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				2 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate between '".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))." 00:00:00' and '".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))." 23:59:59'  AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				union
				Select '".date("m/d")." 오늘 ',
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				3 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate between '".date("Y-m-d")." 00:00:00' and '".date("Y-m-d")." 23:59:59' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				union
				Select '최근1주',
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				4 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od
				where od.regdate between '".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))." 00:00:00' and '".date("Y-m-d")." 23:59:59'
				AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				union
				Select '금주',
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				5 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od where 
				od.regdate between '".$firstday." 00:00:00' and '".$lastday." 23:59:59'
				AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				order by vieworder asc 
				";
	*/
				/*
				union
				Select '최근1개월',
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				6 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od
				where od.regdate between '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))." 00:00:00' and '".date("Y-m-d")." 23:59:59'
				AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				union
				Select '전체',
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then ptprice else 0 end),0) as total_price,
				IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
				IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
				IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
				7 as vieworder
				from ".TBL_SHOP_ORDER_DETAIL." od WHERE od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				*/
	/*
	}

	if($_SESSION["admininfo"][charger_id] == "forbiz"){
		//echo nl2br($sql);
		//exit;
	}
	$odb->query($sql);
	$datas = $odb->getrows();
	*/

	$datas_title[0] = "기간";
	$datas_title[1] = "매출액(원)";
	$datas_title[2] = "주문건수(건)";
	$datas_title[3] = "입금예정(건)";
	$datas_title[4] = "입금확인(건)";
	$datas_title[5] = "배송준비/배송중(건)";
	$datas_title[6] = "교환(건)";
	$datas_title[7] = "주문취소(건)";


	$mstring = "
	<table border=0 cellspacing='1' cellpadding='5' width='100%'>
      <tr>
        <td bgcolor='#F8F9FA'>
			<table cellpadding=0 cellspacing=1 width=100% bgcolor=#c0c0c0 class='list_table_box'>
				<col width='*' />
				<col width='15%' />
				<col width='15%' />
				<col width='15%' />
				<col width='15%' />
				<col width='15%' />
				<!--col width='12%' />
				<col width='12%' /-->
		";
	for($i=0;$i<count($datas)-2;$i++){
		//print_r($datas);
			if($i == 0){
				$mstring .= "<tr bgcolor=#ffffff align=center height=30>
				<td class='s_td'>".$datas_title[$i]."</td>
				<td class='s_td'>".$datas[$i][0]."</td>
				<td class='m_td'>".$datas[$i][1]."</td>
				<td class='m_td'>".$datas[$i][2]."</td>
				<td class='m_td'>".$datas[$i][3]."</td>
				<td class='m_td'>".$datas[$i][4]."</td>
				<!--td class='m_td'>".$datas[$i][5]."</td>
				<td class='m_td'>".$datas[$i][6]."</td>
				<td class='e_td'>".$datas[$i][7]."</td-->
				</tr>";
			}else{
				$mstring .= "<tr bgcolor=#ffffff height=25 align=right>
				<td class='s_td'>".$datas_title[$i]."</td>
				<td class='list_box_td list_bg_gray' style='padding:0px 0 0 10px;text-align:center;' ><b>".number_format($datas[$i][0])."</b></td>
				<td class='list_box_td'>".number_format($datas[$i][1])."</td>
				<td class='list_box_td list_bg_gray point'>".number_format($datas[$i][2])."</td>
				<td class='list_box_td '>".number_format($datas[$i][3])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][4])."</td>
				<!--td class='list_box_td'>".number_format($datas[$i][5])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][6])."</td>
				<td class='list_box_td'>".number_format($datas[$i][7])."</td-->
				</tr>";
			}
	}
	$mstring .= "</table>";
	$mstring .= "</td></tr></table>";

	return $mstring;


}


function PrintMemberTable($datas){
	/*
	global $admininfo,$sns_product_type;
	$odb = new Database;

	$today = date("Ymd", time());
	$Ymd1 = date("Ymd",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-1,substr($today,0,4)));
	$Ymd2 = date("Ymd",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-2,substr($today,0,4)));

	$today_ = date("Y-m-d", time());
	$Ymd1_ = date("Y-m-d",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-1,substr($today,0,4)));
	$Ymd2_ = date("Y-m-d",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-2,substr($today,0,4)));
	*/

	/*
	$sql = "select day_str,
					m_total, 
					concat(m_b2c_total , ' / ' , m_b2b_total , ' / ' , m_ect_total) as m,
					j_total,
					concat(j_b2c_total , ' / ' , j_b2b_total , ' / ' , j_ect_total) as j,
					(select IFNULL(sum(1),0) as d_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd2."' ) as d_total,
					concat((select IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as d_b2c_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd2."' ) , ' / ' , (select IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as d_b2b_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd2."' ) , ' / ' , (select IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as d_ect_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd2."' )) as d,
					1 as vieworder
				from 
				(
					Select '".date("m/d",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-2,substr($today,0,4)))."      ' as day_str ,
					IFNULL(sum(1),0) as m_total,
					IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as m_b2c_total,
					IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as m_b2b_total,
					IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as m_ect_total,

					IFNULL(sum(case when date_format(date,'%Y%m%d') = '".$Ymd2."' then 1 else 0 end),0) as j_total,
					IFNULL(sum(case when mem_type in ('M') and date_format(date,'%Y%m%d') = '".$Ymd2."' then 1 else 0 end),0) as j_b2c_total,
					IFNULL(sum(case when mem_type in ('C') and date_format(date,'%Y%m%d') = '".$Ymd2."' then 1 else 0 end),0) as j_b2b_total,
					IFNULL(sum(case when mem_type not in ('M','C') and date_format(date,'%Y%m%d') = '".$Ymd2."' then 1 else 0 end),0) as j_ect_total
					from ".TBL_COMMON_USER." where date_format(date,'%Y%m%d') <= '".$Ymd2."' 
				) data
				union
				select  day_str,
					m_total, 
					concat(m_b2c_total , ' / ' , m_b2b_total , ' / ' , m_ect_total) as m,
					j_total,
					concat(j_b2c_total , ' / ' , j_b2b_total , ' / ' , j_ect_total) as j,
					(select IFNULL(sum(1),0) as d_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd1."' ) as d_total,
					concat((select IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as d_b2c_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd1."' ) , ' / ' , (select IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as d_b2b_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd1."' ) , ' / ' , (select IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as d_ect_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd1."' )) as d,
					2 as vieworder
				from 
				(
					Select '".date("m/d",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-1,substr($today,0,4)))."      ' as day_str,
					IFNULL(sum(1),0) as m_total,
					IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as m_b2c_total,
					IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as m_b2b_total,
					IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as m_ect_total,

					IFNULL(sum(case when date_format(date,'%Y%m%d') = '".$Ymd1."' then 1 else 0 end),0) as j_total,
					IFNULL(sum(case when mem_type in ('M') and date_format(date,'%Y%m%d') = '".$Ymd1."' then 1 else 0 end),0) as j_b2c_total,
					IFNULL(sum(case when mem_type in ('C') and date_format(date,'%Y%m%d') = '".$Ymd1."' then 1 else 0 end),0) as j_b2b_total,
					IFNULL(sum(case when mem_type not in ('M','C') and date_format(date,'%Y%m%d') = '".$Ymd1."' then 1 else 0 end),0) as j_ect_total
					from ".TBL_COMMON_USER." where date_format(date,'%Y%m%d') <= '".$Ymd1."' 
				) data
				union
				select day_str,
					m_total, 
					concat(m_b2c_total , ' / ' , m_b2b_total , ' / ' , m_ect_total) as m,
					j_total,
					concat(j_b2c_total , ' / ' , j_b2b_total , ' / ' , j_ect_total) as j,
					(select IFNULL(sum(1),0) as d_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$today."' ) as d_total,
					concat((select IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as d_b2c_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$today."' ) , ' / ' , (select IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as d_b2b_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$today."' ) , ' / ' , (select IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as d_ect_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$today."' )) as d,
					3 as vieworder
				from 
				(
					Select '".date("m/d")." 오늘 ' as day_str,
					IFNULL(sum(1),0) as m_total,
					IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as m_b2c_total,
					IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as m_b2b_total,
					IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as m_ect_total,

					IFNULL(sum(case when date_format(date,'%Y%m%d') = '".$today."' then 1 else 0 end),0) as j_total,
					IFNULL(sum(case when mem_type in ('M') and date_format(date,'%Y%m%d') = '".$today."' then 1 else 0 end),0) as j_b2c_total,
					IFNULL(sum(case when mem_type in ('C') and date_format(date,'%Y%m%d') = '".$today."' then 1 else 0 end),0) as j_b2b_total,
					IFNULL(sum(case when mem_type not in ('M','C') and date_format(date,'%Y%m%d') = '".$today."' then 1 else 0 end),0) as j_ect_total
					from ".TBL_COMMON_USER." where date_format(date,'%Y%m%d') <= '".$today."' 
				) data
				order by vieworder asc ";
	*/
	
	/*
	$sql = "select day_str,
			(m_b2c_total + m_b2b_total + m_ect_total) as m_total, 
			concat(m_b2c_total , ' / ' , m_b2b_total , ' / ' , m_ect_total) as m,
			j_total,
			concat(j_b2c_total , ' / ' , j_b2b_total , ' / ' , j_ect_total) as j,
			(select IFNULL(sum(1),0) as d_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd2."' ) as d_total,
			concat((select IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as d_b2c_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd2."' ) , ' / ' , (select IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as d_b2b_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd2."' ) , ' / ' , (select IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as d_ect_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd2."' )) as d,
			1 as vieworder
		from 
		(
			select 
				data.*,
				IFNULL((select count(*) from common_user u where date <= '".$Ymd1_." 23:59:59' and mem_type in ('M') ),0) as m_b2c_total,
				IFNULL((select count(*) from common_user u where date <= '".$Ymd1_." 23:59:59' and mem_type in ('C') ),0) as m_b2b_total,
				IFNULL((select count(*) from common_user u where date <= '".$Ymd1_." 23:59:59' and mem_type not in ('M','C')) ,0) as m_ect_total
			from
			(
					Select '".date("m/d",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-2,substr($today,0,4)))."      ' as day_str ,
					IFNULL(sum(1),0) as m_total,
					
					IFNULL(sum(1),0) as j_total,
					IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as j_b2c_total,
					IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as j_b2b_total,
					IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as j_ect_total
					from ".TBL_COMMON_USER." where date between '".$Ymd1_." 00:00:00' and '".$Ymd1_." 23:59:59'
				) data
			) data2
		union
		select  day_str,
			(m_b2c_total + m_b2b_total + m_ect_total) as m_total, 
			concat(m_b2c_total , ' / ' , m_b2b_total , ' / ' , m_ect_total) as m,
			j_total,
			concat(j_b2c_total , ' / ' , j_b2b_total , ' / ' , j_ect_total) as j,
			(select IFNULL(sum(1),0) as d_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd1."' ) as d_total,
			concat((select IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as d_b2c_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd1."' ) , ' / ' , (select IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as d_b2b_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd1."' ) , ' / ' , (select IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as d_ect_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$Ymd1."' )) as d,
			2 as vieworder
		from 
		(
			Select '".date("m/d",mktime(0,0,0,substr($today,4,2),substr($today,6,2)-1,substr($today,0,4)))."      ' as day_str,
			IFNULL(sum(1),0) as m_total,
			IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as m_b2c_total,
			IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as m_b2b_total,
			IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as m_ect_total,

			IFNULL(sum(1),0) as j_total,
			IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as j_b2c_total,
			IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as j_b2b_total,
			IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as j_ect_total
			from ".TBL_COMMON_USER." where date between '".$Ymd2_." 00:00:00' and '".$Ymd2_." 23:59:59'
		) data
		union
		select day_str,
			(m_b2c_total + m_b2b_total + m_ect_total) as m_total, 
			concat(m_b2c_total , ' / ' , m_b2b_total , ' / ' , m_ect_total) as m,
			j_total,
			concat(j_b2c_total , ' / ' , j_b2b_total , ' / ' , j_ect_total) as j,
			(select IFNULL(sum(1),0) as d_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$today."' ) as d_total,
			concat((select IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as d_b2c_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$today."' ) , ' / ' , (select IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as d_b2b_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$today."' ) , ' / ' , (select IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as d_ect_total from common_dropmember where date_format(dropdate,'%Y%m%d') =  '".$today."' )) as d,
			3 as vieworder
		from 
		(
			Select '".date("m/d")." 오늘 ' as day_str,
			IFNULL(sum(1),0) as m_total,
			IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as m_b2c_total,
			IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as m_b2b_total,
			IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as m_ect_total,
			IFNULL(sum(1),0) as j_total,
			IFNULL(sum(case when mem_type in ('M') then 1 else 0 end),0) as j_b2c_total,
			IFNULL(sum(case when mem_type in ('C') then 1 else 0 end),0) as j_b2b_total,
			IFNULL(sum(case when mem_type not in ('M','C') then 1 else 0 end),0) as j_ect_total
			from ".TBL_COMMON_USER." where date between '".$today_." 00:00:00' and '".$today_." 23:59:59' 
		) data
		order by vieworder asc ";
	//echo ($sql);

	$odb->query($sql);
	
	$datas = $odb->getrows();
	*/

	$datas_title[0] = "구분";
	$datas_title[1] = "전체";
	$datas_title[3] = "가입자";
	$datas_title[5] = "탈퇴회원";

	$datas_title2[] = "합계";
	$datas_title2[] = "B2C / B2B / 기타";
	$datas_title2[] = "계";
	$datas_title2[] = "B2C / B2B / 기타";
	$datas_title2[] = "계";
	$datas_title2[] = "B2C / B2B / 기타";

	$mstring = "
	<table border=0 cellspacing='1' cellpadding='5' width='100%'>
      <tr>
        <td bgcolor='#F8F9FA'>
			<table cellpadding=0 cellspacing=1 width=100% bgcolor=#c0c0c0 class='list_table_box'>
				<col width='12%' />
				<col width='*' />
				<col width='20%' />
				<col width='20%' />
				<col width='20%' />
		";
	for($i=0;$i<count($datas)-2;$i++){
		
			if($i == 0){
				//print_r($datas);
				$mstring .= "<tr bgcolor=#ffffff align=center height=30>
				<td class='s_td' colspan='2'>".$datas_title[$i]."</td>
				<td class='m_td'>".($datas[$i][0])."</td>
				<td class='m_td'>".($datas[$i][1])."</td>
				<td class='m_td'>".($datas[$i][2])."</td>
				</tr>";
			}else{

				$mstring .= "<tr bgcolor=#ffffff height=25 align=right>";
				if($datas_title[$i]!=""){
					$mstring .= "
					<td class='s_td' rowspan='2'>".$datas_title[$i]."</td>";
				}
				
				$_tmp1 = explode("/",$datas[$i][0]);
				$_tmp2 = explode("/",$datas[$i][1]);
				$_tmp3 = explode("/",$datas[$i][2]);
				
				if($i==1 || $i==2){
					for($j=0;$j<count($_tmp1);$j++){
						if($j==0)	$_tmp_str1 = number_format(trim($_tmp1[$j]));
						else			$_tmp_str1 .=  " / ".number_format(trim($_tmp1[$j]));
					}

					for($j=0;$j<count($_tmp2);$j++){
						if($j==0)	$_tmp_str2 = number_format(trim($_tmp1[$j] + $_tmp2[$j]));
						else			$_tmp_str2 .= " / ".number_format(trim($_tmp1[$j] + $_tmp2[$j]));
					}

					for($j=0;$j<count($_tmp3);$j++){
						if($j==0)	$_tmp_str3 = number_format(trim($_tmp1[$j] + $_tmp2[$j] + $_tmp3[$j]));
						else			$_tmp_str3 .= " / ".number_format(trim($_tmp1[$j] + $_tmp2[$j] + $_tmp3[$j]));
					}
				}else{
					for($j=0;$j<count($_tmp1);$j++){
						if($j==0)	$_tmp_str1 = number_format(trim($_tmp1[$j]));
						else			$_tmp_str1 .=  " / ".number_format(trim($_tmp1[$j]));
					}

					for($j=0;$j<count($_tmp2);$j++){
						if($j==0)	$_tmp_str2 = number_format(trim($_tmp2[$j]));
						else			$_tmp_str2 .= " / ".number_format(trim($_tmp2[$j]));
					}

					for($j=0;$j<count($_tmp3);$j++){
						if($j==0)	$_tmp_str3 = number_format(trim($_tmp3[$j]));
						else			$_tmp_str3 .= " / ".number_format(trim($_tmp3[$j]));
					}
				}
				
				$mstring .= "
					<td class='m_td'>".$datas_title2[($i-1)]."</td>
					<td class='list_box_td list_bg_gray' style='padding:0px 0 0 10px;text-align:center;' ><b>".$_tmp_str1."</b></td>
					<td class='list_box_td'>".$_tmp_str2."</td>
					<td class='list_box_td list_bg_gray point'>".($_tmp_str3)."</td>
					</tr>";
			}
	}
	$mstring .= "</table>";
	$mstring .= "</td></tr></table>";

	return $mstring;


}



function PrintBoardRecentList(){
	global $slave_db,   $admininfo;

	$sql = "select COUNT(*) from ".TBL_SHOP_BBS_USEAFTER."  ";
	$slave_db->query($sql);
	$slave_db->fetch();
	$total = $slave_db->dt[0];

	$max = 10;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}


	$mString = "<table cellpadding=0 cellspacing=0 width='100%' bgcolor=silver class='list_table_box'>
		<tr align=center bgcolor=#efefef height=30 style='font-weight:bold'>
			<td width='30%' class='s_td'>제품</td>
			<td class='m_td'>내용</td>
			<td width='10%' class='m_td' nowrap>작성자</td>
			<td width='15%' class='m_td'>등록일</td>
			<td width='10%' class='e_td'>관리</td>
		</tr>";
	//$mString = $mString."<tr height=1><td colspan=5 class=dot-x></td></tr>";
	//$mString = $mString."<tr align=center bgcolor=#efefef height=25><td class=s_td width='25%'>제품</td><td class=m_td width='40%'>내용</td><td class=m_td width='10%' nowrap>작성자</td><td class=m_td width='15%'>등록일</td><td class=e_td width='10%'>관리</td></tr>";
	if ($total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=150><td colspan=5 align=center>사용후기 내역이 존재 하지 않습니다.</td></tr>";
	}else{

		$slave_db->query("select p.pname, u.* from ".TBL_SHOP_BBS_USEAFTER." u , ".TBL_SHOP_PRODUCT." p where u.pid = p.id  order by  regdate desc limit 6");

		for($i=0;$i < $slave_db->total;$i++){
			$slave_db->fetch($i);

			//$no = $no + 1;

			$mString .= "<tr height=27 bgcolor=#ffffff align=center>
			<td bgcolor='#efefef' align='left' style='padding:4px 20px;'>".$slave_db->dt[pname]."</td>
			<td align=left style='padding-left:20px;'>".cut_str(strip_tags($slave_db->dt[uf_contents]),30)."</td>
			<td bgcolor='#efefef'>".$slave_db->dt[uf_name]."</td>
			<td>".str_replace("-",".",substr($slave_db->dt[regdate],0,10))."</td>
			<td bgcolor='#efefef' align=center>
				<a href=JavaScript:useAfterDelete('".$slave_db->dt[uf_ix]."')><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0></a>
			</td>
			</tr>
			";
		}

	}

	//<tr height=50 bgcolor=#ffffff><td colspan=6 align=left>".page_bar($total, $page, $max,  "&max=$max")."</td></tr>
	$mString .= "</table>";

	return $mString;
}



function PrintRecentProductList($stock_status=""){
	global $slave_db,   $admin_config, $admininfo, $DOCUMENT_ROOT, $currency_display,$image_hosting_type;

	$where = array();
	if($stock_status == "soldout"){
		$where[] = "(option_stock_yn = 'Y' or stock = 0 ) and stock_use_yn = 'Y'  ";
	}else if($stock_status == "shortage"){
		$where[] = "(option_stock_yn = 'S' or (stock < safestock and stock != 0 )) and stock_use_yn = 'Y'   ";
	}
	if($slave_db->dbms_type == "oracle"){
		$where[] = " rownum <= 5 ";
	}
	$where = (count($where) > 0)	?	' WHERE '.implode(' AND ', $where):'';


	if($_SESSION["admininfo"][admin_level] == 9){
		if($slave_db->dbms_type == "oracle"){
			$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where order by mp1.regdate desc   ";
		}else{
			$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where order by mp1.regdate desc limit 5  ";
		}
	}else{
		if($slave_db->dbms_type == "oracle"){
			$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where and mp1.admin ='".$_SESSION["admininfo"][company_id]."'  order by mp1.regdate desc   ";
		}else{
			$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where and mp1.admin ='".$_SESSION["admininfo"][company_id]."'  order by mp1.regdate desc limit 5  ";
		}
	}
	//echo $sql;
	$slave_db->query($sql);


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width='100%' bgcolor=silver>";

	if ($slave_db->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=2 align=center>등록된 상품이 없습니다.</td></tr>";
	}else{

		for($i=0;$i < $slave_db->total;$i++){
			$slave_db->fetch($i);


			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($_SESSION["admininfo"][mall_data_root]."/images/product", $slave_db->dt[id], "s", $slave_db->dt)) || $image_hosting_type=='ftp'){
				$img_str = PrintImage($_SESSION["admininfo"][mall_data_root]."/images/product", $slave_db->dt[id], "s", $slave_db->dt);
			}else{
				$img_str = "../image/no_img.gif";
			}

			$mString = $mString."<tr height=45 bgcolor=#ffffff align=center>
			<td bgcolor='#ffffff' width=50><a href='../product/goods_input.php?id=".$slave_db->dt[id]."'><img src='".$img_str."' width=50 height=50 border=0 style='border:1px solid #c0c0c0'></a></td>
			<td align=left style='padding:4px 4px 4px 10px'>
				<table border=0 cellpadding=2 cellspacing=0 width=100%>
					<tr>
						<td>".((trim($slave_db->dt[brand_name]) != "" &&  $slave_db->dt[brand_name] != NULL) ? "[".$slave_db->dt[brand_name]."]":"")."</td>
					</tr>
					<tr>
						<td><a href='../product/goods_input.php?id=".$slave_db->dt[id]."'>".cut_str($slave_db->dt[pname],20)."</a></td>
					</tr>
					<tr>
						<td >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($slave_db->dt[sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
					</tr>
				</table>
			</td>
			</tr>
			<tr height=1><td colspan=6 class=dot-x></td></tr>
			";
		}

	}


	$mString = $mString."</table>";

	return $mString;
} 