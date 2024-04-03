<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../include/ReportReferTree.php");
include("../include/commerce.lib.php");
include("../../lib/report.lib.php");

$fordb = new forbizDatabase();

if($report_type == ''){
    $report_type='1';
}

$type_text_array['1']=array('title'=>'일간','to'=>'오늘','be'=>'어제');
$type_text_array['2']=array('title'=>'주간','to'=>'금주','be'=>'전주');
$type_text_array['3']=array('title'=>'월간','to'=>'금월','be'=>'전달');


$ReTypeTxt=$type_text_array[$report_type];


$vdate = date("Ymd");
$vdate2 = date("Y-m-d");
$vyesterday = date("Ymd", strtotime("-1 day"));
$vyesterday2 = date("Y-m-d", strtotime("-1 day"));

$vyear=substr($vdate,0,4);
$vweek=date("W");

$vYyear=substr($vyesterday,0,4);
$vYweek = date("W", strtotime("-1 week"));

$vweekstart = date( "Ymd", strtotime($vyear.'-W'.$vweek.'0')); // First day of week
$vweekstart2 = date( "Y-m-d", strtotime($vyear.'-W'.$vweek.'0')); // First day of week
$vweekend = date( "Ymd", strtotime($vyear.'-W'.$vweek.'6'));
$vweekend2 = date( "Y-m-d", strtotime($vyear.'-W'.$vweek.'6'));

$vBweekstart = date( "Ymd", strtotime($vYyear.'-W'.$vYweek.'0'));
$vBweekstart2 = date( "Y-m-d", strtotime($vYyear.'-W'.$vYweek.'0'));
$vBweekend = date( "Ymd", strtotime($vYyear.'-W'.$vYweek.'6'));
$vBweekend2 = date( "Y-m-d", strtotime($vYyear.'-W'.$vYweek.'6'));

$vmonthstart = date( "Ym01" );
$vmonthstart2 = date( "Y-m-01" );
$vmonthend = date( "Ym31" );
$vmonthend2 = date( "Y-m-31" );

$vBmonthstart = date( "Ym01" , strtotime("-1 month"));
$vBmonthstart2 = date( "Y-m-01" , strtotime("-1 month"));
$vBmonthend = date( "Ym31" , strtotime("-1 month"));
$vBmonthend2 = date( "Y-m-31" , strtotime("-1 month"));

$sql_where_type['1']['to']=" [COLUMN] = '".$vdate."' ";
$sql_where_type['1']['be']=" [COLUMN] = '".$vyesterday."' ";

$sql_where_type['2']['to']=" [COLUMN] between '".$vweekstart."' and '".$vweekend."' ";
$sql_where_type['2']['be']=" [COLUMN] between '".$vBweekstart."' and '".$vBweekend."' ";

$sql_where_type['3']['to']=" [COLUMN] between '".$vmonthstart."' and '".$vmonthend."' ";
$sql_where_type['3']['be']=" [COLUMN] between '".$vBmonthstart."' and '".$vBmonthend."' ";

$sql_where_type2['1']['to']=" [COLUMN] between '".$vdate2." 00:00:00' and '".$vdate2." 23:59:59' ";
$sql_where_type2['1']['be']=" [COLUMN] between '".$vyesterday2." 00:00:00' and '".$vyesterday2." 23:59:59' ";

$sql_where_type2['2']['to']=" [COLUMN] between '".$vweekstart2." 00:00:00' and '".$vweekend2." 23:59:59' ";
$sql_where_type2['2']['be']=" [COLUMN] between '".$vBweekstart2." 00:00:00' and '".$vBweekend2." 23:59:59' ";

$sql_where_type2['3']['to']=" [COLUMN] between '".$vmonthstart2." 00:00:00' and '".$vmonthend2." 23:59:59' ";
$sql_where_type2['3']['be']=" [COLUMN] between '".$vBmonthstart2." 00:00:00' and '".$vBmonthend2." 23:59:59' ";

$Contents = "
<table width='100%' border=0>
	<tr height=50>
		<td >
			<div class='tab'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'> 
						<table id='tab_01'  ".(($report_type == '1') ? "class=on":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='?report_type=1'\">일간 요약현황</td>
							<th class='box_03'></th>
						</tr>
						</table> 
						<table id='tab_02'  ".(($report_type == '2') ? "class=on":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='?report_type=2'\">주간 요약현황</td>
							<th class='box_03'></th>
						</tr>
						</table> 
						<table id='tab_03'  ".(($report_type == '3') ? "class=on":"")." >
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='?report_type=3'\">월간 요약현황</td>
							<th class='box_03'></th>
						</tr>
						</table> 
					</td>
				</tr>
				</table>
			</div>
		</td>
	  </tr>
</table>";

$script_times['member_start'] = time();
$sql=returnSql("member","to");
$sql.=" UNION ALL ";
$sql.=returnSql("member","be");
$fordb->query($sql);
$script_times['member_end'] = time();
//echo nl2br($sql);
//exit;
$script_times['visit_start'] = time();
$infos=array();
$infos["title"]="유입현황";
$infos["menu_title"]=array("기간","UV","PV","회원가입(명)","회원탈퇴(명)");
$infos["menu_data"] = $fordb->fetchall("object");

//$Contents .= table_templet($infos);
$script_times['visit_end'] = time();

///////////////////////////////////////
$script_times['order_start'] = time();
$sql=returnSql("order","to");
$sql.=" UNION ALL ";
$sql.=returnSql("order","be");
$fordb->query($sql);
//echo nl2br($sql);
//exit;
$infos=array();
$infos["title"]="주문현황 (입금확인기준)";
$infos["menu_title"]=array("기간","주문건수(개)","매출액(원)","예상이익액(원)","예상수수료율(%)");
$infos["menu_data"] = $fordb->fetchall("object");

$Contents .= table_templet($infos);
$script_times['order_end'] = time();


///////////////////////////////////////
$script_times['product_start'] = time();
$sql=returnSql("product","to");
$sql.=" UNION ALL ";
$sql.=returnSql("product","be");
$fordb->query($sql);
//echo nl2br($sql);
//exit;
$infos=array();
$infos["title"]="상품현황";
$infos["menu_title"]=array("기간","전체(개)","판매중(개)","미판매(개)","신규등록(개)");
$infos["menu_data"] = $fordb->fetchall("object");

$Contents .= table_templet($infos);
$script_times['product_end'] = time();

$script_times['sales_start'] = time();
$Contents .= "
<table width='100%' cellpadding=3 cellspacing=0 border='0' align='left'>
<tr height=20><td style='padding:20px 0px 5px 0px;border-bottom:2px solid #efefef'><img src='../../images/dot_org.gif' align=absmiddle> <b class='middle_title'>".$type_text_array[$report_type]['title']."의매출액(종합)</b></td><td style='padding:20px 0px 5px 0px;' align='right'><a href='../order/salesbydate.php'>상세내역보기</a></td></tr>
		  <tr>
			<td style='padding:3px 0px' colspan='2'>";
if($report_type == "1" || $report_type == ""){
    $Contents .= "".salesByDateReportTable($vdate,'dashboard_today')."";
}else if($report_type == "2"){
    $Contents .= "".salesByDateReportTable($vdate,'dashboard_week')."";
}else if($report_type == "3"){
    $Contents .= "".salesByDateReportTable($vdate,'dashboard_month')."";
}
$Contents .= "
			</td>
		  </tr>
</table>		 
";
$script_times['sales_end'] = time();
/*
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
$Contents .= HelpBox("종합 통계 현황", $help_text);
*/

$script = "<script language='javascript'>

</script>
";


$p = new forbizReportPage();
$p->forbizLeftMenu = commerce_munu('synthesizecommerce.php',""," ");
$p->forbizContents = $Contents;
$p->addScript = "$script ";
$p->Navigation = "이커머스분석 > 종합 통계 현황";
$p->title = "종합 통계 현황";
$p->ContentsWidth = "98%";
//print_r($script_times);
$p->PrintReportPage();


function returnSql($div,$data_type){
    global $ReTypeTxt,$non_sale_status;

    switch ($div){
        case "member";
            $sql = "SELECT 
				'".$ReTypeTxt[$data_type]."' as data0, sum(v.ncnt) as data1, p.data2 , ms.data3, cd.data4
			FROM 
				".TBL_LOGSTORY_VISITOR." v 
			LEFT JOIN 
				(
					SELECT
						sum(ncnt) as data2 
					FROM 
						".TBL_LOGSTORY_PAGEVIEWTIME." p
					WHERE
						".whereStr("p.vdate",$data_type)."
				) p on (1=1)
			LEFT JOIN
				(
					SELECT
						count(*) as data3 
					FROM 
						".TBL_LOGSTORY_MEMBERREG_STACK." ms
					WHERE
						".whereStr("ms.vdate",$data_type)."
				) ms on (1=1)
			LEFT JOIN
				(
					SELECT
						count(*) as data4 
					FROM 
						common_dropmember cd
					WHERE 
						".whereStr("date_format(cd.dropdate,'%Y%m%d')",$data_type)."
				) cd on (1=1)
			WHERE 
				".whereStr("v.vdate",$data_type)."";
            break;

        case "order";
            $sql="SELECT
				'".$ReTypeTxt[$data_type]."' as data0, count(*) as data1, od.data2 , od.data3, round( (od.data3 / od.data2) * 100 ) as data4
			FROM
				shop_order_payment op
			LEFT JOIN 
				(
					SELECT
						sum(od.pt_dcprice) as data2,
						(case when od.account_type='3' or od.refund_status='FC' then '0' else (case when od.account_type in ('1','') then (od.pt_dcprice*(od.commission)/100) else (od.pt_dcprice-(od.coprice*od.pcnt)) end) end) as data3
					FROM 
						shop_order_detail od
					WHERE
						".whereStr("date_format(od.ic_date,'%Y%m%d')",$data_type)."
				) od on (1=1)
			WHERE
				".whereStr("date_format(op.ic_date,'%Y%m%d')",$data_type)."
			AND
				op.pay_status='IC'
			AND
				op.pay_type='G'";
            break;

        case "product";
            $sql="SELECT
				'".$ReTypeTxt[$data_type]."' as data0, 
				(select count(*) as cnt from shop_product p) as data1,
				(select count(*) as cnt from shop_product p where state='1') as data2,
				(select count(*) as cnt from shop_product p where state!='1') as data3,
				(select count(*) as cnt from shop_product p where ".whereStr("date_format(p.regdate,'%Y%m%d')",$data_type)." ) as data4
			";
            break;
    }
    //WHERE
    //			".whereStr("date_format(p.regdate,'%Y%m%d')",$data_type)."

    return $sql;
}

function whereStr($column,$type){
    global $sql_where_type,$sql_where_type2,$report_type;

    if(substr_count( $column , 'date_format(' ) > 0 ){
        $tmp_column = explode(',',str_replace('date_format(','',$column));
        $column = trim($tmp_column[0]);
        $return = str_replace("[COLUMN]",$column,$sql_where_type2[$report_type][$type]);
    }else{
        $return = str_replace("[COLUMN]",$column,$sql_where_type[$report_type][$type]);
    }
    return $return;
}

function table_templet($data){

    $return = "
	<table cellpadding=0 cellspacing=0 border='0' align='left' style='margin-top:10px;width:100%;margin-bottom:3px;'>
		<tr height=20>
			<td style='padding:20px 0px 5px 0px;border-bottom:2px solid #efefef'>
				<img src='../../images/dot_org.gif' align=absmiddle> <b class='middle_title'>".$data["title"]."</b> 
			</td>
		</tr>
	</table>
	<table cellpadding=3 cellspacing=0 width=100% class='list_table_box'>";

    foreach($data["menu_title"] as $menu_title){
        $return .= "
		<col width='".round(100/count($data["menu_title"]))."%'>";
    }

    $return .= "
	<tr height=30>";

    foreach($data["menu_title"] as $menu_title){
        $return .= "
		<td class=s_td>".$menu_title."</td>";
    }
    $return .= "
	</tr>";

    foreach($data["menu_data"] as $menu_data){
        $return .= "
		<tr height=30 bgcolor=#ffffff>
			<td class='list_box_td list_bg_gray'>".$menu_data["data0"]."</td>
			<td class='list_box_td'>".number_format($menu_data["data1"])."</td>
			<td class='list_box_td'>".number_format($menu_data["data2"])."</td>
			<td class='list_box_td'>".number_format($menu_data["data3"])."</td>
			<td class='list_box_td'>".number_format($menu_data["data4"])."</td>
		</tr>";
    }

    $return .= "
	</table>";

    return $return;
}