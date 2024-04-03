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
<div>
	<div style='clear:both;'>
		<h2 class='Mgap_H10'><img src='../images/myservice/sosial_biz_img02.jpg' title='입접형(몰인몰)기반' align='absmiddle' /></h2>
	</div>
	<!--입접형(몰인몰)기반? [E]-->
	<!--몰인몰센터소개 [S]-->
	<div class='Pgap_B50' style='width:734px;'>
		<h1><img src='../images/myservice/biz_title_01.gif' title='몰인몰센터소개' title='몰인몰센터소개' align='absmiddle' /></h1>
		<div style='' >
			<img src='../images/myservice/biz_img_01.gif' title='' alt=''align='absmiddle' />
		</div>
		<div style='margin-top:42px;' >
			<img src='../images/myservice/biz_img_02.gif' title='몰인몰(입점형)이란? 입점형(몰인몰)은 오프라인의 샵인 샵과 같은 원리입니다. 한 매장에서 장소 대여하여 판매가의 수수료를 제공하고 입점하여 상품을 판매하는 방식입니다.
			오프라인과 같이 온라인 쇼핑몰에는 여러업체가 각자의 관리자로 로그인하여 권한에 따라 상품 등록, 판매, 배송관리 등을 관리할 수 있습니다.
			몰인몰 솔루션은 중개사이트로 보급화되어 있으며, 중,대형쇼핑몰, 백화점 전문몰, 오픈마켓 등이 유형이 바로 입점형식의 기반으로 만들어진 쇼핑몰입니다.

			몰스토리는 입점형 기술을 기반으로 하는 솔루션입니다. 다년간의 경험과 국내 최강의 입점형 기술력이 있었기에 임대형에 몰인몰 방식을 과감히 도입할 수 있었습니다.' alt='몰인몰(입점형)이란? 입점형(몰인몰)은 오프라인의 샵인 샵과 같은 원리입니다. 한 매장에서 장소 대여하여 판매가의 수수료를 제공하고 입점하여 상품을 판매하는 방식입니다.
			오프라인과 같이 온라인 쇼핑몰에는 여러업체가 각자의 관리자로 로그인하여 권한에 따라 상품 등록, 판매, 배송관리 등을 관리할 수 있습니다.
			몰인몰 솔루션은 중개사이트로 보급화되어 있으며, 중,대형쇼핑몰, 백화점 전문몰, 오픈마켓 등이 유형이 바로 입점형식의 기반으로 만들어진 쇼핑몰입니다.

			몰스토리는 입점형 기술을 기반으로 하는 솔루션입니다. 다년간의 경험과 국내 최강의 입점형 기술력이 있었기에 임대형에 몰인몰 방식을 과감히 도입할 수 있었습니다.'align='absmiddle' />
		</div>
	</div>
	<!--몰인몰센터소개 [E]-->
	<!--몰인몰센터의 필요성 [S]-->
	<div class='Pgap_B50' style='width:734px;'>
		<h1><img src='../images/myservice/biz_title_02.gif' title='몰인몰센터의 필요성' title='몰인몰센터의 필요성' align='absmiddle' /></h1>
		<ul class='biz_list01'>
			<li>
				<img src='../images/myservice/biz_title_02_list01.gif' title='대부분의 창업자들이 처음 쇼핑몰을 운영하게 될 때 투자비용을 줄이고, 쇼핑몰에 최적화된 솔루션을 사용하기 위해 임대형을 많이 사용합니다. ' alt='대부분의 창업자들이 처음 쇼핑몰을 운영하게 될 때 투자비용을 줄이고, 쇼핑몰에 최적화된 솔루션을 사용하기 위해 임대형을 많이 사용합니다. ' align='absmiddle' />
			</li>
			<li>
				<img src='../images/myservice/biz_title_02_list02.gif' title='시간이 지나고 어느정도 매출이 오르면 상품의 다양화를 기획하고 독단적으로 아이템을 구축하는 경우도 있지만, 타 업체와의 제휴를 통해 확장히기도 합니다. 이때 일반 쇼핑몰에서 몰인몰 기반의 쇼핑몰로 옮기거나 기능 개발이 별도로 필요합니다. 이때 생각보다 많은 시간과 비용이 발생하게 됩니다.' alt='시간이 지나고 어느정도 매출이 오르면 상품의 다양화를 기획하고 독단적으로 아이템을 구축하는 경우도 있지만, 타 업체와의 제휴를 통해 확장히기도 합니다. 이때 일반 쇼핑몰에서 몰인몰 기반의 쇼핑몰로 옮기거나 기능 개발이 별도로 필요합니다. 이때 생각보다 많은 시간과 비용이 발생하게 됩니다. ' align='absmiddle' />
			</li>
			<li>
				<img src='../images/myservice/biz_title_02_list03.gif' title='하지만 몰스토리는 “소호형”에서 “비즈형”으로 비용 부담 없이 바로 확장이 가능하고, 국내 유일한  몰인몰 기반 구성으로 고객님의 비즈니스 확장에 큰 힘이 될 수 있습니다.' alt='하지만 몰스토리는 “소호형”에서 “비즈형”으로 비용 부담 없이 바로 확장이 가능하고, 국내 유일한  몰인몰 기반 구성으로 고객님의 비즈니스 확장에 큰 힘이 될 수 있습니다.' align='absmiddle' />
			</li>
			<li>
				<img src='../images/myservice/biz_title_02_list04.gif' title='국내 임대형 서비스 중 유일하게 기반 서비스로 제공되는 솔루션은 오직! 몰스토리 ‘비즈니스형 ‘임대형 솔루션 뿐입니다. ' alt='국내 임대형 서비스 중 유일하게 기반 서비스로 제공되는 솔루션은 오직! 몰스토리 ‘비즈니스형 ‘임대형 솔루션 뿐입니다.' align='absmiddle' />
			</li>
		</ul>
	</div>
	<!--몰인몰센터의 필요성 [E]-->
	<!--몰인몰센터의 장점 [S]-->
	<div class='Pgap_B50 under_line01' style='width:734px;'>
		<h1><img src='../images/myservice/biz_title_03.gif' title='몰인몰센터의 장점' title='몰인몰센터의 장점' align='absmiddle' /></h1>
		<ul class='biz_list02'>
			<li>
				<img src='../images/myservice/biz_title_03_list01.gif' title='상점 입점 기능을 통해 재고관리 및 상품등록을 쉽고 빠르게' alt='상점 입점 기능을 통해 재고관리 및 상품등록을 쉽고 빠르게'  />
			</li>
			<li>
				<img src='../images/myservice/biz_title_03_list02.gif' title='비즈형 신청만으로 몰인몰 기능사용가능  * 5셀러 까지 무료 (셀러 추가시 문의 필요)' alt='비즈형 신청만으로 몰인몰 기능사용가능  * 5셀러 까지 무료 (셀러 추가시 문의 필요)'  />
			</li>
			<li>
				<img src='../images/myservice/biz_title_03_list03.gif' title='셀러별 별도 전용 관리자 제공' alt='셀러별 별도 전용 관리자 제공'  />
			</li>
			<li>
				<img src='../images/myservice/biz_title_03_list04.gif' title='원클릭으로 상세 셀러 매출, 정산, 판매 현황 레포팅 가능' alt='원클릭으로 상세 셀러 매출, 정산, 판매 현황 레포팅 가능'  />
			</li>
			<li>
				<img src='../images/myservice/biz_title_03_list05.gif' title='중대형 쇼핑몰 운영 가능' alt='중대형 쇼핑몰 운영 가능'  />
			</li>
			<li>
				<img src='../images/myservice/biz_title_03_list06.gif' title='매출 규모와 상관없이 운영리소스 분산(셀러 직배송 시스템 활용시)' alt='매출 규모와 상관없이 운영리소스 분산(셀러 직배송 시스템 활용시)'  />
			</li>
			<li>
				<img src='../images/myservice/biz_title_03_list07.gif' title='고객의 다양한 욕구를 충족할 상품 컨텐츠 확보 및 판매 가능' alt='고객의 다양한 욕구를 충족할 상품 컨텐츠 확보 및 판매 가능'  />
			</li>
		</ul>
	</div>
	<!--몰인몰센터의 장점 [E]-->
	<!--몰스토리만의 소셜커머스 기능 [E]-->
</div>
<!--입접형(몰인몰)기반 [E]-->
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
$P->Navigation = "추가 소프트웨어 > 셀러관리";
$P->title = "셀러관리";
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