<link rel="stylesheet" href="../css/addservice2.css" type="text/css" />
<link rel="stylesheet" href="../css/common2.css" type="text/css" />
<?
ini_set('include_path', ".:/usr/local/lib/php:".$_SERVER["DOCUMENT_ROOT"]."/include/pear");
include("../class/layout.class");
$install_path = "../../include/";
include("SOAP/Client.php");

if($admininfo[admin_level] < 9){
	header("Location:../admin.php");
}

$db = new Database;
//phpinfo();
//print_r($db);
//print_r($admininfo);
$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'  ");
$db->fetch();

$phone = explode("-",$db->dt[phone]);
$fax = explode("-",$db->dt[fax]);

//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);

$soapclient = new SOAP_Client("http://www.mallstory.com/admin/service/api/");
// server.php 의 namespace 와 일치해야함
$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

$_service_status = $soapclient->call("getServiceStatus",$params = array("mall_domain"=> $_SERVER["HTTP_HOST"],"company_id"=> $admininfo[mall_domain_id], "mall_domain_key"=> $admininfo["mall_domain_key"]),	$options);
//print_r($_service_status);
$service_status = (array)$_service_status;
$service_infos["CMS"] = (array)$service_status["CMS"];
$service_infos["BASIC_ADD"] = (array)$service_status["BASIC_ADD"];
$service_infos["ADD"] = (array)$service_status["ADD"];
$service_infos["APP"] = (array)$service_status["APP"];
//print_r($service_infos["BASIC_ADD"]);

$Contents01 = "
<!--스피드재고관리1.0 [S]-->
<table cellpadding='5' cellspacing='0' border='0' width='100%' style='margin-bottom:3px;'>
	<tr>
		<td bgcolor='#efefef'>
			<div style='padding:5px;padding-left:10px;'><img src='../image/title_head.gif' align='absmiddle'> <b class='blk'>내 재고관리 사용 현황</b></div>
		</td>
	<tr>
</table>
<table cellpadding='0' cellspacing='0' border='0' width='100%' class='list_table_box'>
	<col width='20%' />
	<col width='30%' />
	<col width='20%' />
	<col width='*' />

	<tr>
		<td class='list_box_td list_bg_gray' style='padding:5px 0;'>서비스 기간</td>
		<td class='list_box_td ' >2012-05-14 ~ 2013-05-14</td>
		<td class='list_box_td list_bg_gray'>남은기간</td>
		<td class='list_box_td '>365</td>
	</tr>
</table>
<div class='servicebox' style='width:740px;'>
<div style='width:100%;{? tab!=3}display:none;{/}' id='biz_info_03'>
	<div style='clear:both;'>
		<h2 class='Mgap_H10'><img src='../images/myservice/sosial_biz_img03.jpg' title='스피드재고관리1.0' align='absmiddle' /></h2>
	</div>
	<!--스피드재고관리1.0 [E]-->
	<!--스피드재고관리란? [S]-->
	<div class='Pgap_B50' style='width:734px;'>
		<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/sosial_good_title03.gif' title='스피드재고관리란?' align='absmiddle' /></h2>
		<ul>
			<li>
				<img src='../images/myservice/sosial_good_title03_list01.gif' title='스피드재고관리란?' align='absmiddle' />
			</li>
			<li style='padding:25px 0;'>
				<strong style='color:#ea4200;'>쇼핑몰 CEO님들의 요구사항을 바탕으로 개발 되었습니다.<br /></strong>
				<strong style='color:#ea4200;'>소형 쇼핑몰과 중대형쇼핑몰에 각각 최적화 되도록 설계 하였습니다.<br /></strong>
				쇼핑몰에서 가장 필요한 재고파악, 품절관리, 안전재고, 입출고 계획등을 효율적으로 관리를 할 수 있도록 소호형에 최적화하도록<br />
				만들었으며, 중대형몰의 판매처, 구매처, 창고관리 등을 편리하고 활용도를 높여 어느업종에서도 사용할 수 있도록 설계되어있습니다. <br />
				<strong style='color:#ea4200;'>오프라인 매장에서도 사용가능 한 서비스를 제공합니다.<br /></strong>
				또한 오프라인 매장, 공장등에서도 활용할  수 있도록하여 대리점관리, 창고별관리 등을 할 수 있어 B2B 카달로그(홍보용쇼핑몰)와 SCM을<br />
				통합적으로 관리 할 수 있도록 서비스를 제공하고 있습니다.
			</li>
			<li>
				<img src='../images/myservice/sosial_good_title03_list02.gif' title='스피드재고관리란?' align='absmiddle' />
			</li>
		</ul>
	</div>
	<!--스피드재고관리란? [E]-->
	<!--서비스특징 [S]-->
	<div class='Pgap_B50 under_line01'>
		<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/sosial_good_title05.gif' title='몰스토리 SCM 특징' align='absmiddle' /></h2>
		<ul class='bottom10_list'>
			<li>
				<img src='../images/myservice/sosial_good_title04_list01.gif' title='재고상품등록 쇼핑몰에서 판매하고 있거나 재고관리를 원하는 상품을 간편하게 등록 관리 할 수 있도록 하였으며, 상품 이미지를 같이 등록할 수 있도록 하여 더욱 직관적으로 상품관리를 할 수 있도록하였습니다. 또한 소셜네트워크와 연결하여 SNS로 신상품등을 홍보 할 수 있어 쇼핑몰 운영외에도 업체 홍보용으로도 함께 사용할 수 있습니다.' align='absmiddle' />
			</li>
			<li>
				<img src='../images/myservice/sosial_good_title04_list02.gif' title='실시간재고 실시간재고현황에서는 구매처별 입고재고, 브랜드별 재고, 카테고리별 재고, 입점업체별재고, 보관장소별 재고 등을 검색하여 실시간으로 재고현황등을 파악할 수 있도록 하여, 현재 재고자산이 얼마인지, 어느 상품이 어디에 재고가 있는지를 손쉽게 파악할 수 있습니다.' align='absmiddle' />
			</li>
			<li>
				<img src='../images/myservice/sosial_good_title04_list03.gif' title='입출고관리 입출고관리란 보관창고에서 실제로 판매 혹은 구매가 이루어져 상품의 입고 혹은 출고, 출고예정 상품 등을 실시간으로 알 수 있습니다.  또한 입출고의 담당자를 지정하여 더욱 효과적으로 활용할 수 있도록 하였습니다.' align='absmiddle' />
			</li>
			<li>
				<img src='../images/myservice/sosial_good_title04_list04.gif' title='발주(사입)관리  쇼핑몰 고객 혹은 온.오프라인 판매처에서 주문이 발생되어 재고가 없을 때 구매업체에게 발주를 할 수 있는 기능으로, 일반업체와 동/남대문업체들로 나누어 특화된 발주양식을 제공하여, 패션(동/남대문)전문 쇼핑몰 운영자들에게 더욱 편리하게 설계되어 있습니다. 발주또한 전체 구매업체, 구매업체별, 담당자별로 나누어 관리의 편리성을 제공하고 있으며, 부분입고가 가능하게 끔 하여 더욱 효율적으로 사용할 수 있도록 하였습니다.' align='absmiddle' />
			</li>
			<li>
				<img src='../images/myservice/sosial_good_title04_list05.gif' title='기초정보 관리 공급망, 유통망, 창고등을 동시에 관리 할 수 있으며, 판매처관리, 구매처관리, 보관장소 관리, 입고타입, 출고타입 등을 편리하게 추가, 수정 할 수 있도록 하여, 온라인 뿐만 아니라 오프라인 도매업체, 공장, 대리점, 프랜차이즈등의 다양한 업종에서도 사용이 가능하도록 설계되어 있습니다.' align='absmiddle' />
			</li>
			<li>
				<img src='../images/myservice/sosial_good_title04_list06.gif' title='정산괸리 상품의 판매, 구매 발생시 결제된 실 자산을 관리할 수 있습니다. 결제시 현금, 무통장, 카드로 나누어 각 수수료율, 날짜, 담당자의 세부사항들을 체크할 수 있습니다. 또한 미수급처리된 결산전 내역들을 노출시켜 실시간으로 미수금 관리를 할 수 있도록 하였습니다. 경리기능으로 지출경비를 입력하여 전반적인 회사 운영의 내용등을 몰스토리 임대형에서 일체화하여 중소기업용 ERP에 최적화된 솔루션이라 할 수 있습니다.' align='absmiddle' />
			</li>
		</ul>
	</div>
</div>
</div>
";

$Contents = "<table width='100%' height='100%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='mallinfo.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>
		<input name='act' type='hidden' value='update'><input name='mall_ix' type='hidden' value='".$db->dt[mall_ix]."'>
		<input name='mall_div' type='hidden' value='".$db->dt[mall_div]."'>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";

$Contents = $Contents."<tr><td>".$Contents05."<br></td></tr>";
$Contents = $Contents."<tr ><td height=20></td></tr>";
//$Contents = $Contents."<tr><td>".$Contents05_1."<br></td></tr>";
//$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents03."<br></td></tr>";
$Contents = $Contents."<tr ><td height=20></td></tr>";
//$Contents = $Contents."<tr><td>".$Contents04."<br></td></tr>";
//$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents06."<br></td></tr>";
//$Contents = $Contents."<tr><td>".$Contents07."<br></td></tr>";
//$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents08."<br></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents."<tr ><td height=20></td></tr>";
$Contents = $Contents."<tr><td>".$Contents09."<br></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";

//$Contents = "<div style=height:1000px;'></div>";


$Script = "<script language='javascript' src='basicinfo.js'></script>
<script language='javascript'>
function update_zipcode(){
	form = document.edit_form;
	form.action = './zip_act.php';
	form.act.value = 'zipcode';
	form.submit();
}
</script>
";

if($admininfo[mall_type] == "H"){
	$Contents = str_replace("쇼핑몰","사이트",$Contents);
}

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = myservice_menu();
$P->Navigation = "추가 소프트웨어 > 재고관리시스템";
$P->title = "재고관리시스템";
$P->strContents = $Contents;
echo $P->PrintLayOut();




/*
create table admin_menus (
menu_code varchar(32) not null ,
menu_name varchar(255) null default null,
menu_path varchar(255) null default null,
auth_read enum('Y','N') null default 'Y',
auth_write enum('Y','N') null default 'Y',
shipping_company varchar(30) null default null,
primary key(menu_code));
*/
?>