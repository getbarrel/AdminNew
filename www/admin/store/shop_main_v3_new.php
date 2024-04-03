<?
$script_time[start] = time();
include("../class/layout.class");
include("../class/calender.class");
include("./member_region.chart.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
$script_time[start] = time();
//print_r($admininfo);

$db = new Database;
$mdb = new Database;
$sms_design = new SMS;
//print_r($admininfo);
//print_r($admin_config); //
$Script = "
<style>

a.calendar_year:link {font-family:돋움; font-size:11px;text-decoration:none; color:#ffffff}
a.calendar_year:visited {font-family:돋움; font-size:11px; text-decoration:none; color:#ffffff}
a.calendar_year:hover {font-family:돋움; font-size:11px; text-decoration:none; color:#ea4200;font-weight:bold;}
a.calendar_year:active {font-family:돋움; font-size:11px; text-decoration:none; color:#ffffff}

/*************************************************여기부터**********************************************************************/
.tab{float:left;width:100%;}
.tab dl dt{float:left;}
.tab dl dt span{background:url('../images/dot_org.gif') left center no-repeat; padding:0 4px 0 17px;font-famiily:'나눔고딕';font-size:13px;font-weight:bold;}
.tab dl dd{float:right;padding-right:38px;}
.tab dl dd ul{float:left;}
.tab dl dd ul li{float:left;}
.style_none div{padding:0; margin:0; }
.main_first{margin: 33px 0 0 0px;float:left;}
.main_first div{float:left;margin-right:12px;position:relative;}
.main_first div p{position:absolute;top:2px;right:0;}
.main_first dl{}
.main_first dl dt span{color:#383d41;padding-left:6px;}
.main_first dl dt{background:url('../images/dot_org.gif') left 20% no-repeat; padding:0 5px 6px 16px; border-bottom:2px solid #959595;}
.main_first div dl dd{border:1px solid #c5c5c5;border-top:0 none;  margin-left:0;}
.main_first01{padding:0; margin:0;}
.main_first02{padding:0; margin:0;}
.main_first03{width:320px; padding:0; margin:0;}
.main_first03 dl{margin-top:13px;}
.main_first03 dl dd{height:152px;padding:8px 14px 7px 17px;}
.main_first03 dl dd ul{float:left;width:100%;}
.main_first03 li .date{float:right;}
.main_first03 li{margin-bottom:9px;}
.second{float:left;width:100%;}
.second .show_main_title{background:url('../images/dot_org.gif') left 20% no-repeat; padding-left:17px;float:left;margin-bottom:5px;}
.second .show_main_title span{font-size:11px; color:#898989;margin-left:10px;}
.show_main_BG{background:url('../images/show_main_BG.gif') no-repeat;float:left; width:982px; height:280px; border-top:2px solid #959595 !important; border:1px solid #c5c5c5;position:relative;}
.show_main_BG p{text-align:center;margin:0;padding:0;font-weight:bold;}
.show_main_BG .show_main_text01{width:104px;position:absolute;left:25px;top:50px;color:#000;}
.show_main_BG .show_main_text02{width:104px;position:absolute;left:163px;top:50px;color:#000;}
.show_main_BG .show_main_text03{width:104px;position:absolute;left:284px;top:50px;color:#000;}
.show_main_BG .show_main_text04{width:40px;position:absolute;left:436px;top:53px;color:#000;}
.show_main_BG .show_main_text05{width:40px;position:absolute;left:524px;top:53px;color:#000;}
.show_main_BG .show_main_text06{width:104px;position:absolute;left:593px;top:50px;}
.show_main_BG .show_main_text07{width:104px;position:absolute;left:726px;top:50px;}
.show_main_BG .show_main_text08{width:104px;position:absolute;left:857px;top:50px;}
.show_main_BG .show_main_text09{width:86px;position:absolute;left:31px;top:153px;}
.show_main_BG .show_main_text10{width:86px;position:absolute;left:223px;top:169px;}
.show_main_BG .show_main_text11{width:86px;position:absolute;left:536px;top:123px;}
.show_main_BG .show_main_text12{width:86px;position:absolute;left:731px;top:174px;}
.show_main_BG .show_main_text13{width:86px;position:absolute;left:460px;top:241px;}
.show_main_BG .show_main_text14{width:86px;position:absolute;left:584px;top:241px;}
.show_main_BG .show_main_text15{width:86px;position:absolute;left:705px;top:241px;}

.show_main_text01, .show_main_text02, .show_main_text03{color:#000;}
.show_main_text06, .show_main_text07, .show_main_text08{color:#fff;}
.show_main_BG .show_main_text04 a, .show_main_BG .show_main_text05 a{font-size:12px;}
.show_main_BG p a{font-size:14px;}
.show_main_BG .show_main_text09 a, .show_main_BG .show_main_text15 a{color:#f00e0e;}
.show_main_BG .show_main_text06 a, .show_main_BG .show_main_text07 a, .show_main_BG .show_main_text08 a{color:#fff;}
.second_notice{float:left;}
.second .notice_mall{position:relative;width:320px;float:left;padding-left:12px;}
.second .notice_mall dl{margin-bottom:20px;}
.second .notice_mall dl dt{background:url('../images/dot_org.gif') left 20% no-repeat; padding:0 5px 8px 16px; border-bottom:2px solid #959595;}
.second .notice_mall dl dd{height:121px;padding:11px 0 0 0;margin:0;border:1px solid #c5c5c5;border-top:0 none;}
.second .notice_mall dl dd ul{float:left;width:100%;}
.second .notice_mall dl dd ul li{margin-bottom:10px;padding:0 14px 0 17px;}
.second .notice_mall dl dd ul li .date{float:right;}
.second .notice_mall p{position:absolute;top:13px;right:0;margin:0;}
#simple_stats_day{clear:both;}
#simple_stats_day dl{margin:0;}
#simple_stats_day dd{padding:0;height:102px;}
#simple_stats_day tr td{border-bottom:1px solid #c5c5c5;margin:0;height:25px;padding-left:17px;}
#simple_stats_day .last_tr td{border-bottom:0 none; background-size:width:199px;height:24px;} 
#simple_stats_day p{position:absolute;top:2px;right:0;margin:0;}
.align_right{text-align:right;padding-right:12px;}
.BG_third_table{background:#f7f7f7;}
.third{float:left;width:996px;}
.third div{width:320px;float:left;padding-right:12px;position:relative;} 
.third div p{position:absolute;right:12px;;top:2px;}
.third dl{}
.third dl dt{background:url('../images/dot_org.gif') left 20% no-repeat;padding-left:16px;padding-bottom:7px;border-bottom:2px solid #ea4200;}
.third dl dd{margin:0;border:1px solid #c5c5c5;border-top:0 none;line-height:100%;}
.third table tr td{border-bottom:1px solid #c5c5c5;padding:8px 12px 8px 15px;}
.third table .last_tr td{border-bottom:0 none;} 
.t_head td{padding:9px 12px 9px 16px;}
.fourth{float:left;width:996px;}
.fourth .fourth01{float:left;}
.fourth dd{margin:0;}
.fourth01{width:484px;border:1px solid #ececec;}
.fourth01 tr th{text-align:left;height:31px;background:#ececec;padding-left:18px;}
.fourth01 .fourth_td01{padding:14px 0 0 20px;}
.fourth_td01 dl{margin:0;}
.fourth_td01 dl dt{padding:0 0 9px 1px;}
.fourth_td01 dl dd{color:#636363;font-weight:bold;}
.fourth_td ul{margin-top:13px;}
.fourth_td02{padding:11px 0 6px 15px;border-left:1px solid #ececec;}
.fourth_td02 td{font-size:11px;padding:0 0 8px 0;}
.fourth_td02 td span{background:url('../images/list_dot_img.gif') left center no-repeat;padding-left:8px;}
.fourth_td02 .last_td td{padding:0;}
.fourth02{width:484px;border:1px solid #ececec;float:left;margin-left:12px;}
.fourth02 tr th{text-align:left;height:31px;background:#ececec;padding-left:18px;}
.fourth02 .img_turf{padding:41px 29px 42px 29px}
.fourth02 td dl{}
.fourth02 td dl dt{margin:22px 0 19px 0;}
.fourth02 .weather_text{margin-top:12px;line-height:1.5em;font-size:11px;padding-right:20px;}
.bottom_right{width:320px;float:left;}
.bottom_right01{position:relative;}
.bottom_right01 p{position:absolute;right:0;top:2px;margin:0;}
.bottom_right01 dl dt{background:url('../images/dot_org.gif') left 20% no-repeat;padding-left:16px;padding-bottom:7px;border-bottom:2px solid #959595;}
.bottom_right01 dl dd{margin:0;border:1px solid #c5c5c5;border-top:0 none;line-height:100%;}
.bottom_right01 table tr td{border-bottom:1px solid #c5c5c5;padding:8px 12px 8px 15px;}
.bottom_right01 table .last_tr td{border-bottom:0 none;} 
.bottom_right01 td span{font-weight:bold;color:#f00e0e;}
.bottom_right02{padding-top:8px;}
.bottom_right02 .bottom_right02_2{margin-top:22px;}
.bottom_right02 div{position:relative;}
.bottom_right02 ul li span{background:url('../images/dot_org.gif') left 20% no-repeat;padding-left:16px;padding-bottom:7px;border-bottom:2px solid #959595;display:block;}
.bottom_right02 .more_btn_img{position:absolute;right:0;top:2px;}
.bottom_right02 table{text-align:center;border-left:1px solid #cfceca;}
.bottom_right02 table td span{font-weight:bold;color:#636363;}
.bottom_right02 .red_font{color:red;}
.bottom_right02 .bottom_right02_1 tr th{height:31px;background:#f0f0f0;border-right:1px solid #cfceca;border-bottom:1px solid #cfceca;}
.bottom_right02 .bottom_right02_1 tr td{height:30px;border-right:1px solid #cfceca;border-bottom:1px solid #cfceca;}
.bottom_right02 .bottom_right02_2 tr th{height:31px;background:#f8f1de;border-right:1px solid #cfceca;border-bottom:1px solid #cfceca;}
.bottom_right02 .bottom_right02_2 tr td{height:30px;border-right:1px solid #cfceca;border-bottom:1px solid #cfceca;}

/*********************************************여기까지******************************************************************************/



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
</Script>";

if($_SESSION["admininfo"]["mall_type"] == "B" || $_SESSION["admininfo"]["mall_type"] == "F" || $_SESSION["admininfo"]["mall_type"] == "R"  || $_SESSION["admininfo"]["mall_type"] == "BW"  || $_SESSION["admininfo"]["mall_type"] == "S"){
	include("SOAP/Client.php");
	$soapclient = new SOAP_Client("http://www.mallstory.com/admin/service/api/");
	// server.php 의 namespace 와 일치해야함
	$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);
	//팝업 2012-10-23 홍진영
	$getPopupinfo = (array)$soapclient->call("getPopup",$params = array("popup_ix"=> ''),$options);

	if($getPopupinfo[0]!=""){
		$Script .=  MallstoryServicePopUp($getPopupinfo);
	}
}
//$script_time[sms_start] = time();
$sms_cnt = $sms_design->getSMSAbleCount($admininfo);
//$script_time[sms_end] = time();


$Contents01 = "

<!-----------------------------------------------------------------------------------------------------------여기부터------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>

<div class='style_none' style='float:left; width:100%;min-width:1330px;'>
	<!--div class='tab' style='margin: 0px 0px ;'>
		<dl>
			<dt><img src='../images/shopping_info.gif' alt='쇼핑몰 정보설정' align='absmiddle' />&nbsp;&nbsp;<input type='image' src='../images/book_markADD.gif' /></dt>
			<dd>
				<ul>
					<li><a href='#'>상정관리</a>&nbsp;>&nbsp;</li>
					<li><a href='#'>쇼핑몰 환경설정</a>&nbsp;>&nbsp;</li>
					<li>쇼핑몰정보설정&nbsp;<input type='image' src='../images/que.gif'></li>
				</ul>
			</dd>
		</dl>
	</div--><!-- 불필요한 부분이라서 주석 kbk 13/10/14 -->
	<div class='main_first'>
		<div class='main_first01'>
			<dl>
				<dt><img src='../images/week_guest.gif' alt='주별 방문자 분석' align='absmiddle' style='vertical-align:middle;' /><span style='vertical-align:middle;'>2013.11.11~11.17</span></dt>
				<dd><img src='../images/graph1.gif' alt='' align='absmiddle' /></dd>
			</dl>
			<p><input type='image' src='../images/btn_prev.gif' align='absmiddle' />&nbsp;<input type='image' src='../images/btn_next.gif' /></p>
		</div>
		<div class='main_first02'>
			<dl>
				<dt><img src='../images/week_sales.gif' alt='주별 매출액 분석' align='absmiddle' style='vertical-align:middle;' /><span style='vertical-align:middle;'>2013.11.11~11.17</span></dt>
				<dd><img src='../images/graph2.gif' alt='' align='absmiddle' /></dd>
			</dl>
			<p><input type='image' src='../images/btn_prev.gif' />&nbsp;<input type='image' src='../images/btn_next.gif' /></p>
		</div>
		<div class='main_first03'>
			<dl>
				<dt><img src='../images/notice_staff.gif' alt='본사 공지사항(직원용)' align='absmiddle' /></dt>
				<dd>
					<ul>";
						$bbs_company_board_fetch=fetch_bbs("bbs_company_board",7);
						$bbs_company_board_cnt=count($bbs_company_board_fetch);
						for($i=0;$i<$bbs_company_board_cnt;$i++) {
							$Contents01 .= "<li><a href='/admin/bbsmanage/bbs.php?mode=read&board=company_board&bbs_ix=".$bbs_company_board_fetch[$i]["bbs_ix"]."'>[".get_bbs_div($bbs_company_board_fetch[$i]["bbs_div"])."] ".cut_str($bbs_company_board_fetch[$i]["bbs_subject"],21)."</a><span class='date'>".str_replace("-",".",substr($bbs_company_board_fetch[$i]["regdate"],0,10))."</span></li>";
						}
						if($bbs_company_board_cnt<=0) {
							$Contents01 .= "<li style='padding:0px;margin-top:44px;text-align:center;'>등록된 글이 없습니다.</li>";
						}
					$Contents01 .= "</ul>
				</dd>
			</dl>
			<p><a href='/admin/bbsmanage/bbs.php?mode=list&mmode=&board=company_board'><img src='../images/btn_more.gif' alt='더보기' align='absmiddle' /></a></p>
		</div>
	</div>
</div>
<div class='second'>
	<div style='float:left; width:984px;'>
		<p class='show_main_title'><img src='../images/show_main.gif' alt='한눈에보는 쇼핑몰 현황' align='absmiddle' style='vertical-align:middle;' /><span style='vertical-align:middle;'>*최근 3개월 기준으로 표시됩니다.</span></p>
		<div class='show_main_BG'>";
			$fetch_pohc=PrintOrderHistoryByCnt();
			$Contents01 .= "<p class='show_main_text01'><a href='/admin/order/before_payment.php'>".number_format($fetch_pohc["incom_ready_cnt"])."</a>건</p><!-- 입금예정-->
			<p class='show_main_text02'><a href='/admin/order/incom_complete.php'>".number_format($fetch_pohc["incom_end_cnt"])."</a>건</p><!-- 입금확인-->
			<p class='show_main_text03'><a href='/admin/order/delivery_ready.php'>".number_format($fetch_pohc["delivery_ready_cnt"])."</a>건</p><!-- 배송준비중-->
			<p class='show_main_text04'><a href='/admin/order/warehouse_delivery_apply.php'>".number_format($fetch_pohc["warehouse_apply_cnt"])."</a></p><!-- 출고요청-->
			<p class='show_main_text05'><a href='/admin/order/warehouse_delivery_ready.php'>".number_format($fetch_pohc["warehouse_ready_cnt"])."</a></p><!--출고대기 -->
			<p class='show_main_text06'><a href='/admin/order/delivery_ing.php'>".number_format($fetch_pohc["delivery_ing_cnt"])."</a>건</p><!-- 배송중-->
			<p class='show_main_text07'><a href='/admin/order/delivery_complete.php'>".number_format($fetch_pohc["delivery_complete_cnt"])."</a>건</p><!--배송완료 -->
			<p class='show_main_text08'>".number_format($fetch_pohc["order_complete_cnt"])."건</p><!-- 거래완료-->
			<p class='show_main_text09'><a href='/admin/order/incom_before_cancel.php'>".number_format($fetch_pohc["incom_before_cnt"])."</a></p><!-- 입금전취소-->
			<p class='show_main_text10'><a href='/admin/order/cancel_apply.php'>".number_format($fetch_pohc["cancel_apply_cnt"])."</a></p><!-- 취소요청-->
			<p class='show_main_text11'><a href='/admin/order/exchange_apply.php'>".number_format($fetch_pohc["exchange_apply_cnt"])."</a></p><!-- 교환요청-->
			<p class='show_main_text12'><a href='/admin/order/return_apply.php'>".number_format($fetch_pohc["return_apply_cnt"])."</a></p><!-- 반품요청-->
			<p class='show_main_text13'><a href='/admin/order/incom_after_cancel.php'>".number_format($fetch_pohc["cancel_complete_cnt"])."</a></p><!--취소완료 -->
			<p class='show_main_text14'><a href='/admin/buyer_accounts/refund_apply.php'>".number_format($fetch_pohc["refund_apply_cnt"])."</a></p><!-- 환불요청-->
			<p class='show_main_text15'><a href='/admin/buyer_accounts/refund_complete.php'>".number_format($fetch_pohc["refund_complete_cnt"])."</a></p><!-- 환불완료-->
		</div>
	</div>
	<div class='second_notice'>
		<div class='notice_mall'>
			<dl>
				<dt><img src='../images/notice_mall.gif' alt='공지사항(쇼핑몰)' align='absmiddle' /></dt>
				<dd>
					<ul>";
						$bbs_notice_fetch=fetch_bbs("bbs_notice",5);
						$bbs_notice_cnt=count($bbs_notice_fetch);
						for($i=0;$i<$bbs_notice_cnt;$i++) {
							$Contents01 .= "<li><a href='/admin/bbsmanage/bbs.php?mode=read&board=notice&bbs_ix=".$bbs_notice_fetch[$i]["bbs_ix"]."'>[".get_bbs_div($bbs_notice_fetch[$i]["bbs_div"])."] ".cut_str($bbs_notice_fetch[$i]["bbs_subject"],21)."</a><span class='date'>".str_replace("-",".",substr($bbs_notice_fetch[$i]["regdate"],0,10))."</span></li>";
						}
						if($bbs_notice_cnt<=0) {
							$Contents01 .= "<li style='padding:0px;margin-top:44px;text-align:center;'>등록된 글이 없습니다.</li>";
						}
					$Contents01 .= "</ul>
				</dd>
			</dl>
			<p><a href='/admin/bbsmanage/bbs.php?mmode=&board=notice'><img src='../images/btn_more.gif' alt='더보기' align='absmiddle' /></a></p>	
		</div>
		<div class='notice_mall' id='simple_stats_day'>
			<dl>
				<dt><img src='../images/simple_stats_day.gif' alt='간편통계(일기준/전일)' align='absmiddle' /></dt>
				<dd>
					<table width='100%' cellpadding=0 cellspacing=0 border='0'>
					<col width='199'>
					<col width='*'>
						<tr>
							<td class='BG_third_table'><a href='#'>관심셀러등록 회원(신규/전체)</a></td>
							<td class='align_right'><strong>10</strong>/30</td>
						</tr>
						<tr>
							<td class='BG_third_table'><a href='#'>단골고객회원(신규/전체)</a></td>
							<td class='align_right'><strong>10</strong>/50</td>
						</tr>
						<tr>
							<td class='BG_third_table'><a href='#'>정산예정금액</a></td>
							<td class='align_right'><strong>300,000</strong>원</td>
						</tr>
						<tr class='last_tr'>
							<td class='BG_third_table'><a href='#'>정산예정금액(합계)</a></td>
							<td class='align_right'><strong>1,500,000</strong>원</td>
						</tr>
					</table>
				</dd>
			</dl>
			<!--p><a href='#'><img src='../images/btn_more.gif' alt='더보기' align='absmiddle' /></a></p-->	
		</div>
	</div>
	<div style='width:996px;float:left;'>
		<div class='third'>
			<div class='third01'>
				<dl>
					<dt><img src='../images/member_assay.gif' alt='회원분석' /></dt>
					<dd>
						<!--table width='100%' cellspacing='0' cellpadding='0' border='0'>
						<col width='159'>
						<col width='*'>
							<tr class='t_head'>
								<td class='BG_third_table'><strong>Total</strong></td>
								<td align='right'><strong>10,000,000</strong>명</td>
							</tr>
							<tr>
								<td class='BG_third_table'><a href='#'><strong>오늘 신규회원/전달</strong></a></td>
								<td align='right'><strong>10,000/10,000</strong></td>
							</tr>
							<tr>
								<td class='BG_third_table'><a href='#'>어제 신규회원/전달</a></td>
								<td align='right'><strong>10</strong>/10</td>
							</tr>
							<tr>
								<td class='BG_third_table'><a href='#'>금주 신규회원(합계)</a></td>
								<td align='right'><strong>10</strong>/10</td>
							</tr>
							<tr>
								<td class='BG_third_table'><a href='#'>한달 신규회원(합계)</a></td>
								<td align='right'><strong>10</strong>/10</td>
							</tr>
							<tr class='last_tr'>
								<td class='BG_third_table'><a href='#'>한달 평균 신규회원</a></td>
								<td align='right'></td>
							</tr>
						</table-->
						".PrintMemberHistory()."
					</dd>
				</dl>
				<p><a href='/admin/member/member.php'><img src='../images/btn_more.gif' alt='더보기' align='absmiddle' /></a></p>	
			</div>
			<div class='third02'>
				<dl>
					<dt><img src='../images/sales_assay.gif' alt='매출분석' /></dt>
					<dd>
						".PrintOrderHistory_new()."
					</dd>
				</dl>
				<p><a href='/admin/order/salesbydate.php'><img src='../images/btn_more.gif' alt='더보기' align='absmiddle' /></a></p>	
			</div>
			<div class='third03'>
				<dl>
					<dt><img src='../images/upload_assay.gif' alt='상품등록분석' /></dt>
					<dd>
						".PrintProductHistory()."
					</dd>
				</dl>
				<p><a href='/admin/product/product_list.php'><img src='../images/btn_more.gif' alt='더보기' align='absmiddle' /></a></p>	
			</div>
		</div>
		<div class='fourth'>
			<div class='fourth01'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
					<col width='135'>
					<col width='*'>
					<tr>
						<th colspan='3'><img src='../images/summary.gif' alt='담당자 요약' /></th>
					</tr>
					<tr>
						<td class='fourth_td01'>
							<dl>
								<dt><a href='#'><img src='../images/charger_picture.gif' alt='담당자프로필' /></a></dt>
								<dd><a href='#'>디자인실/이화진</a></dd>
							</dl>
						</td>
						<td class='fourth_td02'>
							<table width='100%' cellpadding=0 cellspacing=0 border='0'>
								<col width='134'>
								<col width='14'>
								<col width='*'>
								<tr>
									<td><span>어제진행상품수/전체</span></td>
									<td>:</td>
									<td><strong>103</strong>/5000</td>
								</tr>
								<tr>
									<td><span>어제매출/전주매출</span></td>
									<td>:</td>
									<td><strong style='color:red;'>10,000</strong>원/10,000원</td>
								</tr>
								<tr>
									<td><span>상품문의미처리수</span></td>
									<td>:</td>
									<td><strong>30</strong>/50</td>
								</tr>
								<tr>
									<td><span>진행 배너수</span></td>
									<td>:</td>
									<td><strong>0</strong>/10</td>
								</tr>
								<tr>
									<td><span>진행 기획전</span></td>
									<td>:</td>
									<td><strong>1</strong>/3</td>
								</tr>
								<tr class='last_td'>
									<td><span>진행 이벤트</span></td>
									<td>:</td>
									<td><strong>2</strong>/5</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div class='fourth02'>
				<table width='100%' cellpadding=0 cellspacing=0 border='0'>
					<col width='135'>
					<col width='*'>
					<tr>
						<th colspan='2'><img src='../images/today_weather.gif' alt='오늘의날씨' /></th>
					</tr>
					<tr>
						<td class='img_turf'><img src='../images/today_weatherBG.gif' alt='오늘의날씨' align='absmiddle' /></td>
						<td>
							<dl style='width:100%;'>
								<dt>서울,경기도 육상주간예보</dt>
								<dd><strong>흐림 26.8</strong></dd>
								<dd class='weather_text'>
									장만전선의 영향으로 23일과 24일은 비가 오겠고, 25일 이후에는 
									구름많은 날이 많겠습니다.기온은 평년(최저기온 : 21~23도, 최고
									기온 : 28~31도)과 비슷하겠습니다.강수량은 평년(강수량 : 9~16m...
								</dd>
							</dl>
						</td>	
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div class='bottom_right'>
		<div class='bottom_right01'>
			<dl>
				<dt><img src='../images/claim_today.gif' alt='구매자 미처리 클래임(오늘)/미처리' /></dt>
				<dd>
					<table width='100%'>
					<col width=199'>
					<col width='*'>
						<tr class='t_head'>
							<td class='BG_third_table'>입금전취소(자동)</td>
							<td align='right'><strong>10</strong></td>
						</tr>
						<tr>
							<td class='BG_third_table'><a href='#'>입금후취소(자동)</a></td>
							<td align='right'><strong>10</strong></td>
						</tr>
						<tr>
							<td class='BG_third_table'><a href='#'>취소요청(배송준비중)</a></td>
							<td align='right'><span>10</span>/0</td>
						</tr>
						<tr>
							<td class='BG_third_table'><a href='#'>교환요청(미완료)</a></td>
							<td align='right'><span>1</span>/10</td>
						</tr>
						<tr class='last_tr'>
							<td class='BG_third_table'><a href='#'>반품요청(미완료)</a></td>
							<td align='right'><span>10</span>/10</td>
						</tr>
					</table>
				</dd>
			</dl>
			<!--p><a href='#'><img src='../images/btn_more.gif' alt='더보기' align='absmiddle' /></a></p-->	
		</div>
		<div class='bottom_right02'>
			<div class='bottom_right02_1'>
				<ul>
					<li><span><img src='../images/qna_today.gif' alt='문의사항(오늘)/미처리' /></span></li>
					<!--li class='more_btn_img'><img src='../images/btn_more.gif' alt='문의사항(오늘)/미처리 더보기' /></li-->
				</ul>
				<table cellpadding=0 cellspacing=0 border='0' width='100%'>
					<tr>
						<th>상품문의</th>
						<th>1:1문의</th>
						<th>셀러 1:1문의</th>
					</tr>
					<tr>
						<td><span class='red_font'>10</span>/20</td>
						<td><span class='red_font'>10</span>/20</td>
						<td><span>10</span>/20</td>
					</tr>
				</table>
			</div>
			<div class='bottom_right02_2'>
				<ul>
					<li><span><img src='../images/stock_today.gif' alt='재고현황(오늘)/미처리' /></span></li>
					<li class='more_btn_img'><a href='/admin/inventory/stock_subul.php'><img src='../images/btn_more.gif' alt='재고현황(오늘)/미처리 더보기' /></a></li>
				</ul>
				<table cellpadding=0 cellspacing=0 border='0' width='100%'>
					<tr>
						<th>입고수량</th>
						<th>출고수량</th>
						<th>총 재고수량</th>
					</tr>
					<tr>
						<td><span>10</span>/20</td>
						<td><span>10</span>/20</td>
						<td><span>100</span></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>



<!-----------------------------------------------------------------------------------------------------------여기까지------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>


";



$Contents = $Contents01;




$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
$P->Navigation = "메인화면";
$P->TitleBool = false;
$P->ServiceInfoBool = true;
$P->ContentsWidth = "100%";

echo $P->PrintLayOut();

$script_time[end] = time();
if($_SESSION["admininfo"][charger_id] == "forbiz"){
	//print_r($script_time);
}



function PrintOrderHistory(){
	global $admininfo,$sns_product_type;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));


	if($_SESSION["admininfo"][admin_level] == 9){
		if($_SESSION["admininfo"][mem_type] == "MD"){
			$addWhere = " and od.company_id in (".getMySellerList($_SESSION["admininfo"][charger_ix]).") ";
		}
		/*
		if($odb->dbms_type == "oracle"){
			$sql = "select '기간','매출액(원) ', '주문건수(건)  ','입금예정(건)', '입금확인(건)','배송준비/배송중(건)', '교환(건) ','주문취소(건)' from dual
					union ";
		}else{
			$sql = "select '기간','매출액(원) ', '주문건수(건)  ','입금예정(건)', '입금확인(건)','배송준비/배송중(건)', '교환(건) ','주문취소(건)'
					union
					";
		}
		*/
		if($odb->dbms_type == "oracle"){

		$sql = "Select /*+ index(od IDX_OD_REGDATE)*/ '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					1 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate =  to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					2 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate = to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '".date("m/d")." 오늘 ',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					3 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate =  to_date('".date("Y-m-d")."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '최근1주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
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
					Select /*+ index(od IDX_OD_REGDATE)*/  '금주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					5 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where  to_date('".$firstday."','%Y-%m-%d') <= od.regdate  and od.regdate <=   to_date('".$lastday."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select /*+ index(od IDX_OD_REGDATE)*/  '최근1개월',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
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
					Select /*+ index(od IDX_OD_REGDATE)*/  '전체',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
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
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					1 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					2 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select '".date("m/d")." 오늘 ',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					3 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".date("Ymd")."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select '최근1주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					4 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."'  <=  date_format(od.regdate,'%Y%m%d')
					and date_format(od.regdate,'%Y%m%d') <= '".date("Ymd")."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select '금주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					5 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where  '".$firstday."' <= date_format(od.regdate,'%Y%m%d')  and date_format(od.regdate,'%Y%m%d') <=   '".$lastday."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select '최근1개월',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					6 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' <= date_format(od.regdate,'%Y%m%d')
					and date_format(od.regdate,'%Y%m%d') <= '".date("Ymd")."'
					AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select '전체',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					7 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od WHERE od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					order by vieworder asc ";
		}

		//echo $sql;
		//exit;
		/*
		$sql = "Select
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then total_price else 0 end) as today_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(regdate,'%Y%m%d') between '".$firstday."' and $lastday then total_price else 0 end) as thisweek_total_price,
					sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else 0 end) as thismonth_total_price,
					sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else 0 end) as thismonth_cancel_total_price,
					sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end) as ready_cnt,
					sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end) as order_end_cnt,
					sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end) as thismonth_return_total_cnt
				 	from ".TBL_SHOP_ORDER_DETAIL."  ";
				 	*/
	//echo $sql;
	}else if($_SESSION["admininfo"][admin_level] == 8){
		/*
		if($odb->dbms_type == "oracle"){
			$sql = "select '기간','매출액(원) ', '주문건수(건)  ','입금예정(건)', '입금확인(건)','배송준비/배송중(건)', '교환(건) ','주문취소(건)' from dual
					union ";
		}else{
			$sql = "select '기간','매출액(원) ', '주문건수(건)  ','입금예정(건)', '입금확인(건)','배송준비/배송중(건)', '교환(건) ','주문취소(건)'
					union
					";
		}
		*/
		$addWhere = " and od.company_id = '".$_SESSION["admininfo"][company_id]."'";
		if($odb->dbms_type == "oracle"){

			$sql = "Select /*+ index(od IDX_OD_REGDATE)*/ '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					1 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate =  to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					2 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate = to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '".date("m/d")." 오늘 ',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					3 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate =  to_date('".date("Y-m-d")."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '최근1주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
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
					Select /*+ index(od IDX_OD_REGDATE)*/  '금주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					5 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where  to_date('".$firstday."','%Y-%m-%d') <= od.regdate  and od.regdate <=   to_date('".$lastday."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select /*+ index(od IDX_OD_REGDATE)*/  '최근1개월',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
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
					Select /*+ index(od IDX_OD_REGDATE)*/  '전체',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
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
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					1 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)))."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select '".date("m/d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."      ' ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					2 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select '".date("m/d")." 오늘 ',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					3 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".date("Ymd")."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select '최근1주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					4 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-6,substr($vdate,0,4)))."'  <=  date_format(od.regdate,'%Y%m%d')
					and date_format(od.regdate,'%Y%m%d') <= '".date("Ymd")."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select '금주',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					5 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where  '".$firstday."' <= date_format(od.regdate,'%Y%m%d')  and date_format(od.regdate,'%Y%m%d') <=   '".$lastday."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select '최근1개월',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					6 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' <= date_format(od.regdate,'%Y%m%d')
					and date_format(od.regdate,'%Y%m%d') <= '".date("Ymd")."'
					AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select '전체',
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price,
					7 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od WHERE od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					order by vieworder asc ";
		}
		/*
		$sql = "Select
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then od.ptprice else 0 end) as today_total_price,
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(regdate,'%Y%m%d') between '".$firstday."' and $lastday then od.ptprice else 0 end) as thisweek_total_price,
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then od.ptprice else 0 end) as thismonth_total_price,
					sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else 0 end) as thismonth_cancel_total_price,
					sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end) as ready_cnt,
					sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end) as order_end_cnt,
					sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end) as thismonth_return_total_cnt
				 	FROM ".TBL_SHOP_ORDER_DETAIL." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$_SESSION["admininfo"][company_id]."' ";
		*/
		//echo $sql;
	}


	//echo nl2br($sql);

	$odb->query($sql);
	$datas = $odb->getrows();
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
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
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
				<td class='m_td'>".$datas[$i][5]."</td>
				<td class='m_td'>".$datas[$i][6]."</td>
				<!--td class='e_td'>".$datas[$i][7]."</td-->
				</tr>";
			}else{
				$mstring .= "<tr bgcolor=#ffffff height=25 align=right>
				<td class='s_td'>".$datas_title[$i]."</td>
				<td class='list_box_td list_bg_gray' style='padding:0px 0 0 10px;text-align:center;' ><b>".number_format($datas[$i][0])."</b></td>
				<td class='list_box_td'>".number_format($datas[$i][1])."</td>
				<td class='list_box_td list_bg_gray point'>".number_format($datas[$i][2])."</td>
				<td class='list_box_td '>".number_format($datas[$i][3])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][4])."</td>
				<td class='list_box_td'>".number_format($datas[$i][5])."</td>
				<td class='list_box_td list_bg_gray'>".number_format($datas[$i][6])."</td>
				<!--td class='list_box_td'>".number_format($datas[$i][7])."</td-->
				</tr>";
			}
	}
	$mstring .= "</table>";
	$mstring .= "</td></tr></table>";

	return $mstring;


}

function PrintOrderHistory_new(){//새로운 메인에 맞게 수정 kbk 13/10/14
	global $admininfo,$sns_product_type;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));
	$ago_month_firstday = date("Ym", strtotime("-1 month"))."01";
	$ago_month_lastday = date("Ymt", strtotime("-1 month"));


	if($_SESSION["admininfo"][admin_level] == 9){
		if($_SESSION["admininfo"][mem_type] == "MD"){
			$addWhere = " and od.company_id in (".getMySellerList($_SESSION["admininfo"][charger_ix]).") ";
		}
	} else if($_SESSION["admininfo"][admin_level] == 8) {
		$addWhere = " and od.company_id = '".$_SESSION["admininfo"][company_id]."'";
	}
	if($odb->dbms_type == "oracle"){

		$sql = "
				Select /*+ index(od IDX_OD_REGDATE)*/  'Total' AS l_title,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then od.pt_dcprice else 0 end),0) as total_price,
					1 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					WHERE od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '오늘 매출액' AS l_title,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then od.pt_dcprice else 0 end),0) as total_price,
					2 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate =  to_date('".date("Y-m-d")."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '어제 매출액' AS l_title ,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then od.pt_dcprice else 0 end),0) as total_price,
					3 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where od.regdate = to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select /*+ index(od IDX_OD_REGDATE)*/  '금주 매출액(합계)' AS l_title,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then od.pt_dcprice else 0 end),0) as total_price,
					4 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where  to_date('".$firstday."','%Y-%m-%d') <= od.regdate  and od.regdate <=   to_date('".$lastday."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select /*+ index(od IDX_OD_REGDATE)*/  '전달 매출액(합계)' AS l_title,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then od.pt_dcprice else 0 end),0) as total_price,
					5 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where to_date('".$ago_month_firstday."','%Y-%m-%d') <= od.regdate  and od.regdate <=   to_date('".$ago_month_lastday."','%Y-%m-%d') AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					order by vieworder asc ";
	}else{
		$sql = "Select 'Total' AS l_title,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then od.pt_dcprice else 0 end),0) as total_price,
					1 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od WHERE od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select '오늘 매출액' AS l_title,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then od.pt_dcprice else 0 end),0) as total_price,
					2 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".date("Ymd")."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select '어제 매출액' AS l_title ,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then od.pt_dcprice else 0 end),0) as total_price,
					3 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where date_format(od.regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					union
					Select '금주 매출액(합계)' AS l_title,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then od.pt_dcprice else 0 end),0) as total_price,
					4 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od where  '".$firstday."' <= date_format(od.regdate,'%Y%m%d')  and date_format(od.regdate,'%Y%m%d') <=   '".$lastday."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
				 	union
					Select '전달 매출액(합계)' AS l_title,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then od.pt_dcprice else 0 end),0) as total_price,
					5 as vieworder
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where '".$ago_month_firstday."' <= date_format(od.regdate,'%Y%m%d')  and date_format(od.regdate,'%Y%m%d') <=   '".$ago_month_lastday."' AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					order by vieworder asc ";
	}


	//echo nl2br($sql);

	$odb->query($sql);
	$datas = $odb->fetchall();
	$datas_cnt=count($datas);


	$mstring = "
	<table width='100%' cellspacing='0' cellpadding='0' border='0'>
		<col width='159'>
		<col width='*'>";
	for($i=0;$i<$datas_cnt;$i++) {
		switch($i) {
			case 0 : $class_text="class='t_head'";
			break;
			default : $class_text="";
			break;
		}
		switch($i) {
			case 0 : $view_text="<strong>".$datas[$i]["l_title"]."</strong>";$view_text2="<strong>".number_format($datas[$i]["total_price"])."</strong>원";
			break;
			case 1 : $view_text="<strong>".$datas[$i]["l_title"]."</strong>";$view_text2="<strong>".number_format($datas[$i]["total_price"])."</strong>";
			break;
			default : $view_text=$datas[$i]["l_title"];$view_text2="<strong>".number_format($datas[$i]["total_price"])."</strong>";
			break;
		}
		$mstring .= "
			<tr ".$class_text.">
				<td class='BG_third_table'>".$view_text."</td>
				<td align='right'>".$view_text2."</td>
			</tr>
		";
		if($i==4) {
			$mstring .= "
				<tr class='last_tr'>
					<td class='BG_third_table'>전달 평균 매출액</td>
					<td align='right'><strong>".number_format(round($datas[$i]["total_price"]/date("t",strtotime("-1 month"))))."</strong></td>
				</tr>
			";
		}
	}
	$mstring .= "</table>";

	return $mstring;


}

function PrintOrderHistoryByCnt(){//3개월치 구매건수 kbk 13/10/14
	global $admininfo,$sns_product_type;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));


	if($_SESSION["admininfo"][admin_level] == 9){
		if($_SESSION["admininfo"][mem_type] == "MD"){
			$addWhere = " and od.company_id in (".getMySellerList($_SESSION["admininfo"][charger_ix]).") ";
		}
		if($odb->dbms_type == "oracle"){

		$sql = "Select 
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_READY."' then 1 else 0 end),0) as delivery_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' then 1 else 0 end),0) as warehouse_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' then 1 else 0 end),0) as warehouse_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_ING."' then 1 else 0 end),0) as delivery_ing_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_COMPLETE."' then 1 else 0 end),0) as delivery_complete_cnt,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as order_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' then 1 else 0 end),0) as incom_before_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."' then 1 else 0 end),0) as cancel_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."' then 1 else 0 end),0) as exchange_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."' then 1 else 0 end),0) as return_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."' then 1 else 0 end),0) as cancel_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."' then 1 else 0 end),0) as refund_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."' then 1 else 0 end),0) as refund_complete_cnt
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-90,substr($vdate,0,4)))."','%Y-%m-%d') <= od.regdate
					and od.regdate <= to_date('".date("Y-m-d")."','%Y-%m-%d')
					AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere ";
		}else{
		$sql = "Select 
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_READY."' then 1 else 0 end),0) as delivery_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' then 1 else 0 end),0) as warehouse_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' then 1 else 0 end),0) as warehouse_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_ING."' then 1 else 0 end),0) as delivery_ing_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_COMPLETE."' then 1 else 0 end),0) as delivery_complete_cnt,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as order_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' then 1 else 0 end),0) as incom_before_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."' then 1 else 0 end),0) as cancel_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."' then 1 else 0 end),0) as exchange_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."' then 1 else 0 end),0) as return_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."' then 1 else 0 end),0) as cancel_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."' then 1 else 0 end),0) as refund_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."' then 1 else 0 end),0) as refund_complete_cnt
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-90,substr($vdate,0,4)))."' <= date_format(od.regdate,'%Y%m%d')
					and date_format(od.regdate,'%Y%m%d') <= '".date("Ymd")."'
					AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere ";
		}
	}else if($_SESSION["admininfo"][admin_level] == 8){
		$addWhere = " and od.company_id = '".$_SESSION["admininfo"][company_id]."'";
		if($odb->dbms_type == "oracle"){

			$sql = "Select 
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_READY."' then 1 else 0 end),0) as delivery_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' then 1 else 0 end),0) as warehouse_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' then 1 else 0 end),0) as warehouse_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_ING."' then 1 else 0 end),0) as delivery_ing_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_COMPLETE."' then 1 else 0 end),0) as delivery_complete_cnt,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as order_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' then 1 else 0 end),0) as incom_before_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."' then 1 else 0 end),0) as cancel_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."' then 1 else 0 end),0) as exchange_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."' then 1 else 0 end),0) as return_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."' then 1 else 0 end),0) as cancel_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."' then 1 else 0 end),0) as refund_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."' then 1 else 0 end),0) as refund_complete_cnt
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."','%Y-%m-%d') <= od.regdate
					and od.regdate <= to_date('".date("Y-m-d")."','%Y-%m-%d')
					AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere ";
		}else{
		$sql = "Select 
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_READY."' then 1 else 0 end),0) as delivery_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_APPLY."' then 1 else 0 end),0) as warehouse_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_WAREHOUSE_DELIVERY_READY."' then 1 else 0 end),0) as warehouse_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_ING."' then 1 else 0 end),0) as delivery_ing_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_DELIVERY_COMPLETE."' then 1 else 0 end),0) as delivery_complete_cnt,
					IFNULL(sum(case when status NOT IN ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."','".ORDER_STATUS_CANCEL_COMPLETE."','".ORDER_STATUS_SOLDOUT_CANCEL."','".ORDER_STATUS_REFUND_COMPLETE."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as order_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' then 1 else 0 end),0) as incom_before_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_APPLY."' then 1 else 0 end),0) as cancel_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_EXCHANGE_APPLY."' then 1 else 0 end),0) as exchange_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_RETURN_APPLY."' then 1 else 0 end),0) as return_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_CANCEL_COMPLETE."' then 1 else 0 end),0) as cancel_complete_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_APPLY."' then 1 else 0 end),0) as refund_apply_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_REFUND_COMPLETE."' then 1 else 0 end),0) as refund_complete_cnt
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."' <= date_format(od.regdate,'%Y%m%d')
					and date_format(od.regdate,'%Y%m%d') <= '".date("Ymd")."'
					AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere ";
		}
	}


	//echo nl2br($sql);

	$odb->query($sql);
	return $odb->fetch();
}

function PrintMemberHistory(){//회원분석 kbk 13/11/08
	global $admininfo;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$ydate_month_ago=date("Ymd",strtotime("-1 month"));
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));

	$ago_month_firstday = date("Ym", strtotime("-1 month"))."01";
	$ago_month_lastday = date("Ymt", strtotime("-1 month"));

	if($odb->dbms_type == "oracle"){

		$sql = "
				Select /*+ index(od IDX_OD_REGDATE)*/  'Total' AS l_title, 1 as vieworder,
					COUNT(cu.code) AS regi_count, '' AS pre_regi_count
				 	from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C')
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '오늘 매출액' AS l_title, 2 as vieworder,
					COUNT(cu.code) AS regi_count, 
					(Select COUNT(cu.code) from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C') AND cu.date =  to_date('".date("Y-m-d",strtotime("-1 month"))."','%Y-%m-%d')) AS pre_regi_count
				 	from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C') AND cu.date =  to_date('".date("Y-m-d")."','%Y-%m-%d')
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '어제 매출액' AS l_title , 3 as vieworder,
					COUNT(cu.code) AS regi_count, 
					(Select COUNT(cu.code) from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C') AND cu.date =  to_date('".date("Ymd",mktime(0,0,0,substr($ydate_month_ago,4,2),substr($ydate_month_ago,6,2)-1,substr($ydate_month_ago,0,4)))."','%Y-%m-%d')) AS pre_regi_count
				 	from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C') AND cu.date = to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."','%Y-%m-%d')
				 	union
					Select /*+ index(od IDX_OD_REGDATE)*/  '금주 매출액(합계)' AS l_title, 4 as vieworder,
					COUNT(cu.code) AS regi_count, '' AS pre_regi_count
				 	from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C') 
					where  to_date('".$firstday."','%Y-%m-%d') <= cu.date  and cu.date <=   to_date('".$lastday."','%Y-%m-%d')
				 	union
					Select /*+ index(od IDX_OD_REGDATE)*/  '한달 매출액(합계)' AS l_title, 5 as vieworder,
					COUNT(cu.code) AS regi_count, '' AS pre_regi_count
				 	from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C')
					where to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."','%Y-%m-%d') <= cu.date
					and cu.date <= to_date('".date("Y-m-d")."','%Y-%m-%d')
					order by vieworder asc ";
	}else{
		$sql = "Select 'Total' AS l_title, 1 as vieworder,
					COUNT(cu.code) AS regi_count, '' AS pre_regi_count
				 	from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C')
					union
					Select '오늘 신규회원/전달' AS l_title, 2 as vieworder,
					COUNT(cu.code) AS regi_count, 
					(Select COUNT(cu.code) from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C') AND date_format(cu.date,'%Y%m%d') =  '".date("Ymd",strtotime("-1 month"))."') AS pre_regi_count
				 	from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C') AND date_format(cu.date,'%Y%m%d') =  '".date("Ymd")."' 
					union
					Select '어제 신규회원/전달' AS l_title , 3 as vieworder,
					COUNT(cu.code) AS regi_count, 
					(Select COUNT(cu.code) from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C') AND date_format(cu.date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($ydate_month_ago,4,2),substr($ydate_month_ago,6,2)-1,substr($ydate_month_ago,0,4)))."') AS pre_regi_count
				 	from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C') AND date_format(cu.date,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
					union
					Select '금주 신규회원(합계)' AS l_title , 4 as vieworder,
					COUNT(cu.code) AS regi_count, '' AS pre_regi_count
				 	from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C') AND '".$firstday."' <= date_format(cu.date,'%Y%m%d')  and date_format(cu.date,'%Y%m%d') <=   '".$lastday."'
				 	union
					Select '전달 신규회원(합계)' AS l_title, 5 as vieworder,
					COUNT(cu.code) AS regi_count, '' AS pre_regi_count
				 	from ".TBL_COMMON_USER." cu LEFT JOIN ".TBL_COMMON_MEMBER_DETAIL." cmd ON cu.code=cmd.code LEFT JOIN ".TBL_SHOP_GROUPINFO." g ON cmd.gp_ix=g.gp_ix LEFT JOIN ".TBL_COMMON_COMPANY_DETAIL." ccd ON (ccd.company_id = cu.company_id) WHERE cu.code != '' AND cu.mem_type in ('M','C') AND '".$ago_month_firstday."' <= date_format(cu.date,'%Y%m%d')  and date_format(cu.date,'%Y%m%d') <=   '".$ago_month_lastday."'
					order by vieworder asc ";
	}


	//echo nl2br($sql);

	$odb->query($sql);
	$datas = $odb->fetchall();
	$datas_cnt=count($datas);


	$mstring = "
	<table width='100%' cellspacing='0' cellpadding='0' border='0'>
		<col width='159'>
		<col width='*'>";
	for($i=0;$i<$datas_cnt;$i++) {
		switch($i) {
			case 0 : $class_text="class='t_head'";
			break;
			default : $class_text="";
			break;
		}
		switch($i) {
			case 1 : 
			case 2 : $pre_regi_count="/".number_format($datas[$i]["pre_regi_count"]);
			break;
			default : $pre_regi_count="";
			break;
		}
		switch($i) {
			case 0 : $view_text="<strong>".$datas[$i]["l_title"]."</strong>";$view_text2="<strong>".number_format($datas[$i]["regi_count"])."</strong>명";
			break;
			case 1 : $view_text="<strong>".$datas[$i]["l_title"]."</strong>";$view_text2="<strong>".number_format($datas[$i]["regi_count"])."</strong>".$pre_regi_count;
			break;
			default : $view_text=$datas[$i]["l_title"];$view_text2="<strong>".number_format($datas[$i]["regi_count"])."</strong>".$pre_regi_count;
			break;
		}
		$mstring .= "
			<tr ".$class_text.">
				<td class='BG_third_table'>".$view_text."</td>
				<td align='right'>".$view_text2."</td>
			</tr>
		";
		if($i==4) {
			$mstring .= "
				<tr class='last_tr'>
					<td class='BG_third_table'>전달 평균 신규회원</td>
					<td align='right'><strong>".number_format(round($datas[$i]["regi_count"]/date("t",strtotime("-1 month"))))."</strong></td>
				</tr>
			";
		}
	}
	$mstring .= "</table>";

	return $mstring;
}

function PrintProductHistory(){//상품등록분석 kbk 13/11/08
	global $admininfo;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$ydate_month_ago=date("Ymd",strtotime("-1 month"));
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));

	$ago_month_firstday = date("Ym", strtotime("-1 month"))."01";
	$ago_month_lastday = date("Ymt", strtotime("-1 month"));

	if($odb->dbms_type == "oracle"){

		$sql = "
				Select /*+ index(od IDX_OD_REGDATE)*/  'Total' AS l_title, 1 as vieworder,
					COUNT(cu.code) AS regi_count
				 	from ".TBL_SHOP_PRODUCT."
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '오늘 등록 상품' AS l_title, 2 as vieworder,
					COUNT(cu.code) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." WHERE regdate =  to_date('".date("Y-m-d")."','%Y-%m-%d')
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '어제 등록 상품' AS l_title , 3 as vieworder,
					COUNT(cu.code) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." WHERE regdate = to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."','%Y-%m-%d')
				 	union
					Select /*+ index(od IDX_OD_REGDATE)*/  '금주 등록 상품(합계)' AS l_title, 4 as vieworder,
					COUNT(cu.code) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." where to_date('".$firstday."','%Y-%m-%d') <= regdate  and regdate <=   to_date('".$lastday."','%Y-%m-%d')
				 	union
					Select /*+ index(od IDX_OD_REGDATE)*/  '한달 등록 상품(합계)' AS l_title, 5 as vieworder,
					COUNT(cu.code) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." where to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."','%Y-%m-%d') <= regdate
					and regdate <= to_date('".date("Y-m-d")."','%Y-%m-%d')
					order by vieworder asc ";
	}else{
		$sql = "Select 'Total' AS l_title, 1 as vieworder,
					COUNT(id) AS regi_count
				 	from ".TBL_SHOP_PRODUCT."
					union
					Select '오늘 등록 상품' AS l_title, 2 as vieworder,
					COUNT(id) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." WHERE date_format(regdate,'%Y%m%d') =  '".date("Ymd")."' 
					union
					Select '어제 등록 상품' AS l_title , 3 as vieworder,
					COUNT(id) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." WHERE date_format(regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."'
					union
					Select '금주 등록 상품(합계)' AS l_title , 4 as vieworder,
					COUNT(id) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." WHERE '".$firstday."' <= date_format(regdate,'%Y%m%d')  and date_format(regdate,'%Y%m%d') <=   '".$lastday."'
				 	union
					Select '전달 등록 상품(합계)' AS l_title, 5 as vieworder,
					COUNT(id) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." WHERE '".$ago_month_firstday."' <= date_format(regdate,'%Y%m%d')  and date_format(regdate,'%Y%m%d') <=   '".$ago_month_lastday."'
					order by vieworder asc ";
	}


	//echo nl2br($sql);

	$odb->query($sql);
	$datas = $odb->fetchall();
	$datas_cnt=count($datas);


	$mstring = "
	<table width='100%' cellspacing='0' cellpadding='0' border='0'>
		<col width='159'>
		<col width='*'>";
	for($i=0;$i<$datas_cnt;$i++) {
		switch($i) {
			case 0 : $class_text="class='t_head'";
			break;
			default : $class_text="";
			break;
		}
		switch($i) {
			case 0 : $view_text="<strong>".$datas[$i]["l_title"]."</strong>";$view_text2="<strong>".number_format($datas[$i]["regi_count"])."</strong>개";
			break;
			case 1 : $view_text="<strong>".$datas[$i]["l_title"]."</strong>";$view_text2="<strong>".number_format($datas[$i]["regi_count"])."</strong>";
			break;
			default : $view_text=$datas[$i]["l_title"];$view_text2="<strong>".number_format($datas[$i]["regi_count"])."</strong>";
			break;
		}
		$mstring .= "
			<tr ".$class_text.">
				<td class='BG_third_table'>".$view_text."</td>
				<td align='right'>".$view_text2."</td>
			</tr>
		";
		if($i==4) {
			$mstring .= "
				<tr class='last_tr'>
					<td class='BG_third_table'>전달 평균 등록 상품</td>
					<td align='right'><strong>".number_format(round($datas[$i]["regi_count"]/date("t",strtotime("-1 month"))))."</strong></td>
				</tr>
			";
		}
	}
	$mstring .= "</table>";

	return $mstring;
}

function PrintOrderClaimHistory(){//상품등록분석 kbk 13/11/08
	global $admininfo;
	$odb = new Database;

	$vdate = date("Ymd", time());
	$ydate_month_ago=date("Ymd",strtotime("-1 month"));
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));

	$ago_month_firstday = date("Ym", strtotime("-1 month"))."01";
	$ago_month_lastday = date("Ymt", strtotime("-1 month"));

	if($odb->dbms_type == "oracle"){

		$sql = "
				Select /*+ index(od IDX_OD_REGDATE)*/  'Total' AS l_title, 1 as vieworder,
					COUNT(cu.code) AS regi_count
				 	from ".TBL_SHOP_PRODUCT."
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '오늘 등록 상품' AS l_title, 2 as vieworder,
					COUNT(cu.code) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." WHERE regdate =  to_date('".date("Y-m-d")."','%Y-%m-%d')
					union
					Select /*+ index(od IDX_OD_REGDATE)*/  '어제 등록 상품' AS l_title , 3 as vieworder,
					COUNT(cu.code) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." WHERE regdate = to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)))."','%Y-%m-%d')
				 	union
					Select /*+ index(od IDX_OD_REGDATE)*/  '금주 등록 상품(합계)' AS l_title, 4 as vieworder,
					COUNT(cu.code) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." where to_date('".$firstday."','%Y-%m-%d') <= regdate  and regdate <=   to_date('".$lastday."','%Y-%m-%d')
				 	union
					Select /*+ index(od IDX_OD_REGDATE)*/  '한달 등록 상품(합계)' AS l_title, 5 as vieworder,
					COUNT(cu.code) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." where to_date('".date("Y-m-d",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-30,substr($vdate,0,4)))."','%Y-%m-%d') <= regdate
					and regdate <= to_date('".date("Y-m-d")."','%Y-%m-%d')
					order by vieworder asc ";
	}else{
		$sql = "Select '입금전취소(자동)' AS l_title, 1 as vieworder,
					COUNT(id) AS regi_count, '' AS pre_regi_count
				 	from ".TBL_SHOP_ORDER_DETAIL." WHERE status='".ORDER_STATUS_INCOM_BEFORE_CANCEL_COMPLETE."' date_format(regdate,'%Y%m%d') =  '".date("Ymd")."'
					union
					Select '입금후취소(자동)' AS l_title, 2 as vieworder,
					COUNT(id) AS regi_count, '' AS pre_regi_count
				 	from ".TBL_SHOP_ORDER_DETAIL." WHERE status='".ORDER_STATUS_CANCEL_COMPLETE."' date_format(regdate,'%Y%m%d') =  '".date("Ymd")."'
					union
					Select '취소요청(배송준비중)' AS l_title , 3 as vieworder,
					COUNT(id) AS regi_count,
					(SELECT od_ix FROM ".TBL_SHOP_ORDER_DETAIL." WHERE status='".ORDER_STATUS_DELIVERY_ING."' date_format(regdate,'%Y%m%d') =  '".date("Ymd")."') AS pre_regi_count
				 	from ".TBL_SHOP_ORDER_DETAIL." WHERE status='".ORDER_STATUS_CANCEL_APPLY."' date_format(regdate,'%Y%m%d') =  '".date("Ymd")."'
					union
					Select '교환요청(미완료)' AS l_title , 4 as vieworder,
					COUNT(id) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." WHERE '".$firstday."' <= date_format(regdate,'%Y%m%d')  and date_format(regdate,'%Y%m%d') <=   '".$lastday."'
				 	union
					Select '반품요청(미완료)' AS l_title, 5 as vieworder,
					COUNT(id) AS regi_count
				 	from ".TBL_SHOP_PRODUCT." WHERE '".$ago_month_firstday."' <= date_format(regdate,'%Y%m%d')  and date_format(regdate,'%Y%m%d') <=   '".$ago_month_lastday."'
					order by vieworder asc ";
	}


	//echo nl2br($sql);

	$odb->query($sql);
	$datas = $odb->fetchall();
	$datas_cnt=count($datas);


	$mstring = "
	<table width='100%' cellspacing='0' cellpadding='0' border='0'>
		<col width='159'>
		<col width='*'>";
	for($i=0;$i<$datas_cnt;$i++) {
		switch($i) {
			case 0 : $class_text="class='t_head'";
			break;
			default : $class_text="";
			break;
		}
		switch($i) {
			case 0 : $view_text="<strong>".$datas[$i]["l_title"]."</strong>";$view_text2="<strong>".number_format($datas[$i]["regi_count"])."</strong>개";
			break;
			case 1 : $view_text="<strong>".$datas[$i]["l_title"]."</strong>";$view_text2="<strong>".number_format($datas[$i]["regi_count"])."</strong>";
			break;
			default : $view_text=$datas[$i]["l_title"];$view_text2="<strong>".number_format($datas[$i]["regi_count"])."</strong>";
			break;
		}
		$mstring .= "
			<tr ".$class_text.">
				<td class='BG_third_table'>".$view_text."</td>
				<td align='right'>".$view_text2."</td>
			</tr>
		";
		if($i==4) {
			$mstring .= "
				<tr class='last_tr'>
					<td class='BG_third_table'>전달 평균 등록 상품</td>
					<td align='right'><strong>".number_format(round($datas[$i]["regi_count"]/date("t",strtotime("-1 month"))))."</strong></td>
				</tr>
			";
		}
	}
	$mstring .= "</table>";

	return $mstring;
}




function PrintBoardRecentList(){
	global $db, $mdb, $admininfo;

	$sql = "select COUNT(*) from ".TBL_SHOP_BBS_USEAFTER."  ";
	$mdb->query($sql);
	$mdb->fetch();
	$total = $mdb->dt[0];

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

		$db->query("select p.pname, u.* from ".TBL_SHOP_BBS_USEAFTER." u , ".TBL_SHOP_PRODUCT." p where u.pid = p.id  order by  regdate desc limit 6");

		for($i=0;$i < $db->total;$i++){
			$db->fetch($i);

			//$no = $no + 1;

			$mString .= "<tr height=27 bgcolor=#ffffff align=center>
			<td bgcolor='#efefef' align='left' style='padding:4px 20px;'>".$db->dt[pname]."</td>
			<td align=left style='padding-left:20px;'>".cut_str(strip_tags($db->dt[uf_contents]),30)."</td>
			<td bgcolor='#efefef'>".$db->dt[uf_name]."</td>
			<td>".str_replace("-",".",substr($db->dt[regdate],0,10))."</td>
			<td bgcolor='#efefef' align=center>
				<a href=JavaScript:useAfterDelete('".$db->dt[uf_ix]."')><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0></a>
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
	global $db, $mdb, $admin_config, $admininfo, $DOCUMENT_ROOT, $currency_display,$image_hosting_type;

	$where = array();
	if($stock_status == "soldout"){
		$where[] = "(option_stock_yn = 'Y' or stock = 0 ) and stock_use_yn = 'Y'  ";
	}else if($stock_status == "shortage"){
		$where[] = "(option_stock_yn = 'S' or (stock < safestock and stock != 0 )) and stock_use_yn = 'Y'   ";
	}
	if($mdb->dbms_type == "oracle"){
		$where[] = " rownum <= 5 ";
	}
	$where = (count($where) > 0)	?	' WHERE '.implode(' AND ', $where):'';


	if($_SESSION["admininfo"][admin_level] == 9){
		if($mdb->dbms_type == "oracle"){
			$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where order by mp1.regdate desc   ";
		}else{
			$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where order by mp1.regdate desc limit 5  ";
		}
	}else{
		if($mdb->dbms_type == "oracle"){
			$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where and mp1.admin ='".$_SESSION["admininfo"][company_id]."'  order by mp1.regdate desc   ";
		}else{
			$sql = "select mp1.* from ".TBL_SHOP_PRODUCT." mp1 $where and mp1.admin ='".$_SESSION["admininfo"][company_id]."'  order by mp1.regdate desc limit 5  ";
		}
	}
	//echo $sql;
	$mdb->query($sql);


	$mString = "<table cellpadding=4 cellspacing=0 border=0 width='100%' bgcolor=silver>";

	if ($mdb->total == 0){
		$mString = $mString."<tr bgcolor=#ffffff height=50><td colspan=2 align=center>등록된 상품이 없습니다.</td></tr>";
	}else{

		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);


			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".PrintImage($_SESSION["admininfo"][mall_data_root]."/images/product", $mdb->dt[id], "s", $mdb->dt)) || $image_hosting_type=='ftp'){
				$img_str = PrintImage($_SESSION["admininfo"][mall_data_root]."/images/product", $mdb->dt[id], "s", $mdb->dt);
			}else{
				$img_str = "../image/no_img.gif";
			}

			$mString = $mString."<tr height=45 bgcolor=#ffffff align=center>
			<td bgcolor='#ffffff' width=50><a href='../product/goods_input.php?id=".$mdb->dt[id]."'><img src='".$img_str."' width=50 height=50 border=0 style='border:1px solid #c0c0c0'></a></td>
			<td align=left style='padding:4px 4px 4px 10px'>
				<table border=0 cellpadding=2 cellspacing=0 width=100%>
					<tr>
						<td>".((trim($mdb->dt[brand_name]) != "" &&  $mdb->dt[brand_name] != NULL) ? "[".$mdb->dt[brand_name]."]":"")."</td>
					</tr>
					<tr>
						<td><a href='../product/goods_input.php?id=".$mdb->dt[id]."'>".cut_str($mdb->dt[pname],20)."</a></td>
					</tr>
					<tr>
						<td >".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($mdb->dt[sellprice])." ".$currency_display[$admin_config["currency_unit"]]["back"]."</td>
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
?>
