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
<link rel='stylesheet' href='../css/common2.css' type='text/css' />
<link rel='stylesheet' href='../css/addservice2.css' type='text/css' />
	<div style='width:740px; padding-left:40px;'>
		<div>
			<h2 class='Mgap_H10'><img src='../images/myservice/share_biz_img01.gif' title='소셜커머스1.0' align='absmiddle' /></h2>
		</div>
		<!--소셜공유상품기능 [E]-->
		<!--공유상품관리 시스템 이란? [S]-->
		<div style='padding:50px 0;'>
			<h2 class='under_line01' style='padding:40px 0 20px 0;'><img src='../images/myservice/share_good_title01.gif' title='공유상품관리 시스템 이란?' align='absmiddle' /></h2>
			<ul style='padding-top:20px;'>
				<li>
					<img src='../images/myservice/share_good_title01_list01.gif' title='' align='absmiddle' />
				</li>
				<li style='padding:25px 0; line-height:170%;'>
					공유상품관리 시스템은 쇼핑몰간 ‘친구맺기(제휴)’를 통해 상품 판매를 새로운 관점에서 촉진하게끔 하고, 공급자 파트너쉽을 통해 새로운 시장을 형성 할 수 있는 기반을 마련하는 국내 최초 ‘<strong style='color:#ea4200;'>공급자 소셜커머스</strong>’ 입니다.
				</li>
				<li>
					<img src='../images/myservice/share_good_title01_list02.gif' title='' align='absmiddle' />
				</li>
			</ul>
		</div>
		<!--공유상품관리 시스템 이란? [E]-->
		<!--시스템 실제사례  (1:1사례) [S]-->
		<div class='Pgap_B50'>
			<h2 class='under_line01' style='padding:40px 0 20px 0;'><img src='../images/myservice/share_good_title02.gif' title='시스템 실제사례  (1:1사례)' align='absmiddle' /></h2>
			<ul style='padding-top:20px;'>
				<li>
					<img src='../images/myservice/share_good_title02_list01.gif' title='' align='absmiddle' />
				</li>
			</ul>
			<h3 style='margin:20px 0;'><img src='../images/myservice/share_good_title02_01.gif' title='기능 Process 예시.' align='absmiddle' /></h3>
			<ul style='line-height:170%;'>
				<li style='margin-bottom:15px;'>
					A 여성의류 쇼핑몰 사장님은 늘 검증된 액세서리 상품을 쇼핑몰에서 판매하고 싶어하였습니다. B 액세서리 전문 쇼핑몰 사장님은<br /> 여성의류와 매칭된 상품전시를 늘 고민해 왔습니다.
				</li>
				<li style='margin-bottom:15px;'>
					지금까지 시스템에서 두 쇼핑몰은 제휴는 오프라인 계약을 통해 수동(엑셀, 이메일송부 등)으로 서로 상품데이터를 주고받고 다시 각자<br /> 관리자에서 상품을 등록 하고, 수작업으로 상호 판매 정산을 해야 했습니다.
				</li>
				<li style='margin-bottom:15px;'>
					몰스토리 상품공유시스템은 <strong style='color:#ea4200;'>그래서 혁신 입니다.!</strong>
				</li>
				<li>
					이제 몰스토리 솔루션에서 오프라인 파트너쉽 만 맺으세요!<br />
					각자의 모든 상품을 쉽게 <strong style='color:#ea4200;'>공유서버를 통해 원 클릭으로 주고받고</strong> 쉽게 전시하고 판매하며, 정산에 재고관리까지 같이 <strong style='color:#ea4200;'>모든<br /> 프로세스가 자동</strong>으로 이루어 지게 됩니다.
				</li>
			</ul>
		</div>
		<!--시스템 실제사례  (1:1사례) [E]-->
		<!--공유상품관리 시스템의 특징 및 필요성 [S]-->
		<div class='Pgap_B50'>
			<h2 class='under_line01' style='padding:20px 0;'><img src='../images/myservice/share_good_title03.gif' title='공유상품관리 시스템의 특징 및 필요성' align='absmiddle' /></h2>
			<ul class='bottom10_list' style='padding-top:20px;'>
				<li>
					<img src='../images/myservice/share_good_title03_list01.gif' title='' align='absmiddle' />
				</li>
				<li>
					<img src='../images/myservice/share_good_title03_list02.gif' title='' align='absmiddle' />
				</li>
				<li>
					<img src='../images/myservice/share_good_title03_list03.gif' title='' align='absmiddle' />
				</li>
				<li>
					<img src='../images/myservice/share_good_title03_list04.gif' title='' align='absmiddle' />
				</li>
				<li>
					<img src='../images/myservice/share_good_title03_list05.gif' title='' align='absmiddle' />
				</li>
				<li>
					<img src='../images/myservice/share_good_title03_list06.gif' title='' align='absmiddle' />
				</li>
				<li>
					<img src='../images/myservice/share_good_title03_list07.gif' title='' align='absmiddle' />
				</li>
			</ul>
		</div>
		<!--공유상품관리 시스템의 특징 및 필요성 [E]-->
		<!--공유상품 시스템 서비스 가격 [S]-->
		<div class='Pgap_B50 under_line01'>
			<h2 class='under_line01' style='padding:20px 0;'><img src='../images/myservice/share_good_title04.gif' title='공유상품관리 시스템의 특징 및 필요성' align='absmiddle' /></h2>
			<ul class='bottom10_list' style='margin:20px 0'>
				<li>
					<img src='../images/myservice/share_good_title04_list01.gif' title='' align='absmiddle' />
				</li>
			</ul>
		</div>
		<!--공유상품 시스템 서비스 가격 [E]-->
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
$P->Navigation = "마이서비스 > 상품공유(소셜상품공유)";
$P->title = "상품공유(소셜상품공유)";
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