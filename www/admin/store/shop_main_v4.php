<?php
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

// 개발 전용 코드 시작
$_SERVER["PHP_SELF"] = $gnb_menus[0]["basic_link"];
// 개발 전용코드 끝


if($gnb_menus[0]["basic_link"] != $_SERVER["PHP_SELF"]){
    //header("Location:".$gnb_menus[0]["basic_link"]);
    echo "<script language='javascript'>document.location.href='".$gnb_menus[0]["basic_link"]."'</script>";
    exit;
}

/******************************
	데시보드 데이터 산출
******************************/

include($_SERVER["DOCUMENT_ROOT"]."/admin/logstory/class/sharedmemory.class");
$shmop = new Shared("shop_main_summary_v4");

if(empty($_SESSION["layout_config"]["mall_data_root"])){
    $sql = "select mall_data_root, mall_type from shop_shopinfo  where mall_div = 'B'  ";
    $db->query($sql);
    $db->fetch();

    $mall_data_root = $db->dt['mall_data_root'];
}else{
    $mall_data_root = $_SESSION["layout_config"]["mall_data_root"];
}

$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$mall_data_root."/_shared/";
$shmop->SetFilePath();
$data = $shmop->getObjectForKey("shop_main_summary_v4");
$main_data = unserialize(urldecode($data));


$orderState = isset($main_data['orderState']) ? $main_data['orderState'] : array();
$productState = isset($main_data['productState']) ? $main_data['productState'] : array();
$qnaStat = isset($main_data['qnaStat']) ? $main_data['qnaStat'] : array();
$moreStat = isset($main_data['moreStat']) ? $main_data['moreStat'] : array();
$dailySales = isset($main_data['dailySales']) ? $main_data['dailySales'] : array();


/******************************
	PHP 헤더
******************************/
ob_start();
?>
	<script src="https://www.gstatic.com/charts/loader.js"></script>
	<script>
		var dailySales = <?=json_encode($dailySales)?>;

		$(function(){
			google.charts.load('current', {packages: ['corechart', 'line']});
			google.charts.setOnLoadCallback(function () {
				var data = new google.visualization.DataTable();

				data.addColumn('string', '일');
				data.addColumn('number', '매출액');
				data.addRows(dailySales);

				var options = {
					hAxis: {
						title: '',
						logScale: false
					},
					vAxis: {
						title: '',
						logScale: false
					},
//					curveType: 'function',
					height: 300,
				};

				var chart = new google.visualization.LineChart(document.getElementById('devChart'));
				chart.draw(data, options);
			});
		});
	</script>

    <!-- 191001 퍼블리싱 -->
    <style>
        .mainCont {padding: 10px;}
        .row {width:100%; display:table; table-layout:fixed; margin-bottom: 20px;}
        .row .title {margin:0; padding: 5px 0; font-size: 12px; font-weight:bold;}
        .row .box .cont {margin-top: 10px;}
        .row .box .cont li {width:100%; padding: 5px 0; font-size:0;}
        .row .box .cont li .txt-lft { display:inline-block; width:60%;}
        .row .box .cont li .txt-rgt { display:inline-block; width:40%; text-align:right; font-weight:bold; }

        .row1 .box {display:table-cell; box-sizing: border-box; vertical-align: top; }
        .row1 .box1, .row1 .box2, .row1 .box3 {width:24%;}
        .row1 .box4 {width:28%;}
        .row1 .box + .box {padding-left: 15px;}
        .row1 .box .boxInner{min-height: 116px; padding: 10px; border:1px solid #bcbcbc; }

        .row2 .box.graph{position: relative; display:table-cell; width:72%;}
        .row2 .box.site{display:table-cell; width:28%; padding-left: 15px;}
        .row2 .box .boxInner{ padding: 10px; border:1px solid #bcbcbc; }
        .row2 .box.graph .boxInner {height: 365px;}

        .row2 .box.graph .checkbox {display: none; margin-bottom:10px;}
        .row2 .box.graph .checkbox label {display: inline-block; width:90px; height: 24px; line-height: 24px; margin-bottom:5px; text-align:center; color: #454a60; border:1px solid #bcbcbc; }
        .row2 .box.graph .checkbox input[type="radio"] {position: absolute; width:1px; height:1px; opacity: 0; }
        .row2 .box.graph .checkbox input[type="radio"]:checked + label {font-weight: bold; border:1px solid #000; }
        .row2 .box.graph .desc {position:absolute; bottom: 5px;}
    </style>

    <div class="mainCont">
        <div class="row row1">
            <!-- 주문관리 -->
            <div class="box box1">
                <div class="boxInner">
                    <p class="title">주문관리</p>
                    <ul class="cont">
                        <a href="/admin/order/incom_complete.php">
                            <li><span class="txt-lft">입금확인</span> <span class="txt-rgt"><?=number_format($orderState['IC'])?>건</span></li>
                        </a>
                        <a href="/admin/order/delivery_ready.php">
                            <li><span class="txt-lft">배송준비중</span> <span class="txt-rgt"><?=number_format($orderState['DR'])?>건</span></li>
                        </a>
                        <a href="/admin/order/delivery_complete.php" >
                        <li><span class="txt-lft">배송완료</span> <span class="txt-rgt"><?=number_format($orderState['DC'])?>건</span></li>
                        </a>
                    </ul>
                </div>
            </div>

            <!-- 클레임관리 -->
            <div class="box box2">
                <div class="boxInner">
                    <p class="title">클레임관리</p>
                    <ul class="cont">
                        <a href="/admin/order/incom_before_cancel.php">
                            <li><span class="txt-lft">취소요청</span> <span class="txt-rgt"><?=number_format($orderState['CA'])?>건</span></li>
                        </a>
                        <a href="/admin/order/return_apply.php" >
                            <li><span class="txt-lft">반품요청</span> <span class="txt-rgt"><?=number_format($orderState['RA'])?>건</span></li>
                        </a>
                        <a href="/admin/order/exchange_apply.php" >
                            <li><span class="txt-lft">교환요청</span> <span class="txt-rgt"><?=number_format($orderState['EA'])?>건</span></li>
                        </a>
                    </ul>
                </div>
            </div>

            <!-- 상품현황 -->
            <div class="box box3">
                <div class="boxInner">
                    <p class="title">상품현황</p>
                    <ul class="cont">
                        <a href="/admin/product/product_list.php?mode=search&state[]=1" >
                            <li><span class="txt-lft">판매중</span> <span class="txt-rgt"><?=number_format($productState[1])?>개</span></li>
                        </a>
                        <a href="/admin/product/product_list.php?mode=search&state[]=0" >
                            <li><span class="txt-lft">일시품절</span> <span class="txt-rgt"><?=number_format($productState[0])?>개</span></li>
                        </a>
                        <a href="/admin/product/product_list.php?mode=search&state[]=5" >
                            <li><span class="txt-lft">판매종료</span> <span class="txt-rgt"><?=number_format($productState[5])?>개</span></li>
                        </a>
                    </ul>
                </div>
            </div>

            <!-- 문의현황 -->
            <div class="box box4">
                <div class="boxInner">
                    <p class="title">문의현황</p>
                    <ul class="cont">
                        <a href="/admin/cscenter/product_qna.php">
                            <li><span class="txt-lft">상품문의</span> <span class="txt-rgt"><?=$qnaStat['pQna']?>건</span></li>
                        </a>
                        <a href="/admin/cscenter/bbs.php?mode=list&board=qna" >
                            <li><span class="txt-lft">1:1문의</span> <span class="txt-rgt"><?=$qnaStat['sQna']?>건</span></li>
                        </a>
                        <a href="/admin/cscenter/useafter.list.php">
                            <li><!--span class="txt-lft">상품리뷰</span> <span class="txt-rgt">300건</span--></li>
                        </a>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row row2">
            <!-- 매출 그래프 -->
            <div class="box graph">
                <div class="boxInner">
                    <p class="title">매출 그래프</p>
                    <div class="cont">
                        <!-- <p>일간  : 2019-06-03</p> -->
                        <div class="checkbox">
                            <!-- <input type="radio" id="mth01" name="mth">
                            <label for="mth01">2019년 01월</label>

                            <input type="radio" id="mth02" name="mth">
                            <label for="mth02">2019년 02월</label>

                            <input type="radio" id="mth03" name="mth">
                            <label for="mth03">2019년 03월</label>

                            <input type="radio" id="mth04" name="mth">
                            <label for="mth04">2019년 04월</label>

                            <input type="radio" id="mth05" name="mth">
                            <label for="mth05">2019년 05월</label>

                            <input type="radio" id="mth06" name="mth">
                            <label for="mth06">2019년 06월</label>

                            <input type="radio" id="mth07" name="mth">
                            <label for="mth07">2019년 07월</label>

                            <input type="radio" id="mth08" name="mth">
                            <label for="mth08"">2019년 08월</label>

                            <input type="radio" id="mt09" name="mth">
                            <label for="mt09">2019년 09월</label>

                            <input type="radio" id="mth10" name="mth">
                            <label for="mth10">2019년 10월</label>

                            <input type="radio" id="mth11" name="mth">
                            <label for="mth11">2019년 11월</label>

                            <input type="radio" id="mth12" name="mth">
                            <label for="mth12">2019년 12월</label> -->
                        </div>
                        <div id="devChart">
                        </div>
                        <p class="desc">* 일별 입금확인 매출이 집계되어 노출됩니다. (취소/교환/반품 금액은 반영되지 않습니다.)</p>
                    </div>
                </div>
            </div>

            <!-- 사이트 현황 -->
            <div class="box site">
                <div class="boxInner">
                    <p class="title">사이트 현황</p>
                    <ul class="cont">
                        <li><span class="txt-lft">전일 회원가입</span> <span class="txt-rgt"><?=number_format($moreStat['regCnt'])?>건</span></li>
                        <li><span class="txt-lft">전일 회원탈퇴</span> <span class="txt-rgt"><?=number_format($moreStat['sleepCnt'])?>건</span></li>
                        <li><span class="txt-lft">전일 주문건수(P/M)</span> <span class="txt-rgt"><?=number_format($moreStat['orderCnt']['W'])?>/<?=number_format($moreStat['orderCnt']['M'])?>건</span></li>
                        <li><span class="txt-lft">전일 환불요청건수(P/M)</span> <span class="txt-rgt"><?=number_format($moreStat['refundCnt']['W'])?>/<?=number_format($moreStat['refundCnt']['M'])?>건</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- // 191001 퍼블리싱 -->


<?php
$Contents = ob_get_contents();
ob_end_clean();


/******************************
PHP 푸터
 ******************************/
$P = new LayOut();
$P->addScript = "
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
</Script>";

$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
$P->Navigation = "메인화면";
$P->TitleBool = false;
$P->ServiceInfoBool = false;
$P->ContentsWidth = "98%";
echo $P->PrintLayOut();

$script_time[end] = time();