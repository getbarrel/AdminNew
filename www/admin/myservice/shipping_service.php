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
<link rel='stylesheet' href='{templet_src}/css/addservice.css' type='text/css' />
<div class='center_menu' style='padding-left:50px;'>
	<div class='sub_centes'>
		<div style='clear:both;'>
			<h2 class='Mgap_H10'><img src='../images/myservice/country_img01.gif' title='해외구매대행' align='absmiddle' /></h2>
		</div>

		<div class='Pgap_B50 serviceBox' style='width:734px;'>
			<h1><img src='../images/myservice/buy_s_title01.gif' title='몰스토리 해외구매대행 시스템소개' title='몰스토리 해외구매대행 시스템소개' align='absmiddle' /></h1>
			<div style='margin-bottom:18px;' >
				<img src='../images/myservice/buy_s_title01_1.gif' title='' alt=''align='absmiddle' />
			</div>
			<div style='line-height:150%;margin-bottom:18px;'>
				프로그램으로 분석된 사이트를 바탕으로 <strong>상품 이미지, 상세, SKU코드, 상품브랜드</strong> 등을 자동으로 분석, 수집 하고, 해당 상품의 품절 상태 등 <strong>상태관리를 자동</strong>으로 가능하도록 설계된 해외구매대행 전문 기능입니다.!!
			</div>
			<div style='margin-bottom:18px;' >
				<img src='../images/myservice/buy_s_img01.gif' title='' alt=''align='absmiddle' />
			</div>
			<div style='margin-bottom:18px;padding-top:40px;' >
				<img src='../images/myservice/buy_s_title01_2.gif' title='셀프구매대행' alt=''align='absmiddle' />
			</div>
			<div style='line-height:150%;margin-bottom:18px;margin-left:5px;'>
				방문고객이 직접 상품 URL을 검색하고 바로 상품을 구매할 수 있도록 구현한 쉽고 간단한 구매대행 솔루션 입니다.
			</div>
			<div style='margin-bottom:18px;' >
				<img src='../images/myservice/buy_s_img02.gif' title='' alt=''align='absmiddle' />
			</div>
		</div>

		<div class='Pgap_B50 serviceBox' style='width:734px;'>
			<h1><img src='../images/myservice/buy_s_title02.gif' title='몰스토리 구매대행 특징' title='몰스토리 구매대행 특징' align='absmiddle' /></h1>
			<div style='margin-bottom:18px;' >
				<img src='../images/myservice/buy_s_title02_01.gif' title='1) 몰스토리 구매대행 솔루션이 강력한 이유 3가지!!' alt='1) 몰스토리 구매대행 솔루션이 강력한 이유 3가지!!'align='absmiddle' />
			</div>
			<div style='margin-bottom:18px;' >
				<img src='../images/myservice/buy_s_img03.gif' title='' alt=''align='absmiddle' />
			</div>
			<div style='margin-bottom:30px;padding-top:40px;' >
				<img src='../images/myservice/buy_s_title02_02.gif' title='돈되는 정보!!  FTA시대 무료 물류 컨설팅을 지원합니다.' alt='돈되는 정보!!  FTA시대 무료 물류 컨설팅을 지원합니다.' align='absmiddle' />
			</div>
			<div style='margin-bottom:18px;' >
				<img src='../images/myservice/buy_s_title02_03.gif' title='몰스토리는 해외구매대행 비즈니스를 다방면에서 지원합니다.' alt='몰스토리는 해외구매대행 비즈니스를 다방면에서 지원합니다.' align='absmiddle' />
			</div>
			<div style='line-height:150%;margin-bottom:30px;margin-left:5px;'>
				몰스토리는 미국, 중국, 인도네시아 등지에서 물류 파트너쉽을 가지고 있어, 몰스토리 솔루션 구매고객님들께는 언제나 물류 관련 컨설팅을 ‘<strong style='color:#ea4200;'>무료</strong>’로 제공해 드리고 있습니다.
			</div>
			<div style='margin-bottom:18px;' >
				<img src='../images/myservice/buy_s_title02_04.gif' title='몰스토리 현지 파트너사 정보' alt='몰스토리 현지 파트너사 정보' align='absmiddle' />
			</div>
			<div style='margin-bottom:18px;' >
				<img src='../images/myservice/buy_s_img04.gif' title='' alt=''align='absmiddle' />
			</div>
			<div style='margin-bottom:18px;padding-top:40px;' >
				<img src='../images/myservice/buy_s_title02_05.gif' title='모든 시스템이 통합 자동화 되었습니다.' alt='모든 시스템이 통합 자동화 되었습니다.' align='absmiddle' />
			</div>
			<div style='margin-bottom:18px;' >
				<img src='../images/myservice/buy_s_img05.gif' title='' alt='' align='absmiddle' />
			</div>
		</div>

		<div class=' serviceBox' style='width:734px;  line-height:170%;'>
			<h1 style='padding:20px 0;'><img src='../images/myservice/buy_s_title03.gif' title='몰스토리 스크래핑 서비스 가격' title='몰스토리 스크래핑 서비스 가격' align='absmiddle' /></h1>
			<div style='margin-bottom:18px; >
				<img src='../images/myservice/buy_s_title03_01.gif' title='비즈니스형 사용' alt='비즈니스형 사용' align='absmiddle' />
			</div>
			<ul style='line-height:170%;'>
				<li>
					1. 구매대행 스크래핑 기능 세팅비 :  <strong><s>2,000,000원</s> -> 1,500,000원 (1회)</strong>
				</li>
				<li>
					2. 사이트 분석비용 : <strong><s>200,000원</s> -> 100,000원/1사이트 (1회)</strong>
				</li>
				<li>
					3. 월사용료 : <strong><s>400,000원</s> -> 200,000원</strong>
				</li>
			</ul>
			<!--div style='margin-bottom:18px;' >
				<img src='../images/myservice/buy_s_img06.gif' title='' alt=''align='absmiddle' />
			</div>
			<div style='line-height:150%;margin-bottom:18px;margin-left:5px;'>
				* 2012년 상반기 구매고객 이벤트 : 세팅시점부터 6개월간 50%할인 - 장기결제고객은 일괄 500,000원 할인
			</div-->
		</div>

		<div class=' serviceBox' style='width:734px; line-height:170%;'>
			<h1 style='padding:20px 0;'><img src='../images/myservice/buy_s_title04.gif' title='몰스토리 셀프구매대행 서비스 가격' title='몰스토리 셀프구매대행 서비스 가격' align='absmiddle' /></h1>
			<div style='margin-bottom:18px;' >
				<img src='../images/myservice/buy_s_title03_01.gif' title='비즈니스형 사용' alt='비즈니스형 사용' align='absmiddle' />
			</div>
			<ul>
				<li>
					1. 셀프구매대행 기능 세팅비 : <strong><s>1,000,000원</s> -> 800,000원 (1회)</strong>  <!--span style='color:#ea4200;'> ( 스크래핑 사용고객은 세팅비 무료)</span-->
				</li>
				<li>
					2. 월사용료 : <strong><s>200,000원</s> -> 100,000원</strong>
				</li>
			</ul>
			<!--div style='margin-bottom:18px;' >
				<img src='../images/myservice/buy_s_img07.gif' title='' alt=''align='absmiddle' />
			</div-->
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
$P->Navigation = "추가 소프트웨어 > 해외구매대행서비스";
$P->title = "해외구매대행서비스";
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