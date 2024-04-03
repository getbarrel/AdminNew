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
<table cellpadding='0' cellspacing='0' border='0' width='100%' class='list_table_box'>
<col width='20%' />
<col width='40%' />
<col width='*' />
<tr>
	<td rowspan='3' class='s_td' style='padding:5px 0;'>서비스 신청일</td>
	<td class='list_box_td' style='padding:5px 0;'>2012-04-15</td>
	<td class='list_box_td'>2건</td>
</tr>
<tr>
	<td class='list_box_td' style='padding:5px 0;'>2012-04-15</td>
	<td class='list_box_td'>1건</td>
</tr>
<tr>
	<td class='list_box_td' style='padding:5px 0;'>2012-04-15</td>
	<td class='list_box_td'>1건</td>
</tr>
</table>
<div class='center_menu'>
	<div class='sub_centes'>
		<div class='serviceBox'>
			<h1><img src='../images/myservice/html_img01.jpg' title='' align='absmiddle'></h1>
			<!--html코딩 [S]-->
			<!--HTML 표준 코딩서비스 란? [S]-->
			<div class='Pgap_B50'>
				<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/html_title01.gif' title='HTML 표준 코딩서비스 란?' align='absmiddle' /></h2>
				<ul>
					<li>
						코딩은 포토샵, 일러스트, 플래시 같은 프로그램으로 만든 디자인파일을 컴퓨터가 알 수있는 언어로 다시 재구성한다 즉 ,HTML 언어로<br /> 코딩을 한다는 것을 말합니다.
					</li>
					<li style='padding:25px 0;'>
						표준코딩은 다양한 인터넷 브라우저에서 <strong style='color:#ea4200;'>기능과 디자인이 모두 이상없이 작동할 수 있도록 마크업하는 것</strong>을 의미하며, 주로 <strong style='color:#ea4200;'>개방성,<br /> 확장성, 호환성, 접근성</strong>을 고려한다는 것입니다.
					</li>
					<li>
						요컨데 표준코딩이란 어떠한 브라우저 환경에서도 모두 동일한 모습으로 홈페이지가 보이거나 동일하게 동작하게 하게끔 하는 것을<br /> 말합니다.
					</li>
				</ul>
			</div>
			<!--HTML 표준 코딩서비스 란? [E]-->
			<!--몰스토리 코딩 장점 [S]-->
			<div class='Pgap_B50'>
				<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/html_title02.gif' title='몰스토리 코딩 장점' align='absmiddle' /></h2>
				<ul>
					<li>
						<img src='../images/myservice/html_title02_list01.gif' title='쇼핑몰 웹 사이트들은 일반 웹사이트보다 프로그램이 좀더 복잡하고 고도의 안정성을 요구하는 분야입니다.' align='absmiddle' />
					</li>
					<li style='padding:10px 0;'>
						<img src='../images/myservice/html_title02_list02.gif' title='당연히 경험과 노하우를 가진 쇼핑몰 전문 퍼블리셔가 직접 작업을 진행해야겠죠?' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/html_title02_list03.gif' title='몰스토리는 창업 초기부터 전문 퍼블리셔로 양성하여, 국내 최고의 기술진으로 구성되어 있어서 믿을 수 있는 작업을 제공합니다.' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/html_title02_list04.gif' title='내가 디자인한 100% 원본그대로!경험있는 몰스토리 전문가의 손길로 고객님의 컨텐츠를 쇼핑몰에 그대로 옮겨드립니다.' align='absmiddle' />
					</li>
				</ul>
			</div>
			<!--몰스토리 코딩 장점 [E]-->
			<!--몰스토리 코딩이 꼭 필요하신 분 [S]-->
			<div class='Pgap_B50 '>
				<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/html_title03.gif' title='몰스토리 코딩이 꼭 필요하신 분' align='absmiddle' /></h2>
				<ul class='bottom10_list'>
					<li>
						<img src='../images/myservice/html_title03_list01.gif' title='디자인은 직접 할 수있는데 HTML 하드코딩이 어려운 분' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/html_title03_list02.gif' title='타쇼핑몰에서 운영중인 디자인을 몰스토리로 이전하는 분' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/html_title03_list03.gif' title='HTML코딩은 가능하지만 Web 2.0 표준코딩이 어려운 분' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/html_title03_list04.gif' title='솔루션 프로그램에 대한 이해가 어려운 분' align='absmiddle' />
					</li>
				</ul>
			</div>
			<!--몰스토리 코딩이 꼭 필요하신 분 [E]-->
			<!--작업절차및문의 [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/html_title04.gif' title='작업절차및문의' align='absmiddle' /></h2>
				<h3 class=''><img src='../images/myservice/html_title03_list05.gif' title='작업절차및문의' align='absmiddle' /></h3>
				<ul style='padding-bottom:20px;'  class='left_listarrow'>
					<li>
						<span> 고객님께서 준비하신 디자인파일(psd, fla)을 몰스토리에서 HTML코딩으로 원하는 쇼핑몰에 입혀드리는 서비스 입니다.</span>
					</li>
				</ul>
				<div>
					<img src='../images/myservice/html_img02.gif' title='전체코딩' align='absmiddle' />
				</div>
				<div class='Mgap_H20' style='text-align:right;margin:20px 0;'>
					<a href='/customer/bbs.php?board=qna_estimate'><img src='../images/myservice/online_ask02.gif' title='문의하기' align='absmiddle' /></a>
				</div>
			</div>
			<!--작업절차및문의 [E]-->
			<!--오픈마켓코딩 [S]-->
			<div class='Pgap_B50' >
				<h2><img src='../images/myservice/html_title03_list06.gif' title='오픈마켓코딩' align='absmiddle' /></h2>
				<ul style='padding-bottom:20px;' class='left_listarrow'>
					<li>
						<span> 오픈마켓의 이미지호스팅관련 팝업, 이미지 롤오버, 아이프레임, 스크립트 삽입 등 각종 코딩 부분을 옥션, 11번가, G마켓,
						인터파크등    다양한 오픈마켓을 전문 코더가 손수 작업해 드립니다.</span>
					</li>
				</ul>
				<div>
					<img src='../images/myservice/html_img03.gif' title='오픈마켓코딩' align='absmiddle' />
				</div>
				<div class='Mgap_H20' style='text-align:right; margin:20px 0;'>
					<a href='/customer/bbs.php?board=qna_estimate'><img src='../images/myservice/online_ask02.gif' title='문의하기' align='absmiddle' /></a>
				</div>
			</div>
			<!--오픈마켓코딩 [E]-->
			<!--html코딩 [E]-->
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
$P->Navigation = "운영 지원 서비스 > HTML코딩서비스";
$P->title = "HTML코딩서비스";
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