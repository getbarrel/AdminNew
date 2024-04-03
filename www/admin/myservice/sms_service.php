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

$Contents01="
<table cellpadding='0' cellspacing='0' border='0' width='100%' class='list_table_box'>
<tr>
	<td class='s_td' style='padding:5px 0;'>서비스구분</td>
	<td class='m_td'>신청일</td>
	<td class='m_td'>남은 건수</td>
	<td class='m_td'>서비스 내용</td>
	<td class='e_td'>관리</td>
</tr>
<tr>
	<td class='list_box_td list_bg_gray' style='padding:5px 0;'>SMS</td>
	<td class='list_box_td'>2012-04-15</td>
	<td class='list_box_td list_bg_gray' >300건</td>
	<td class='list_box_td'>서비스 내용</td>
	<td class='list_box_td list_bg_gray'>
		<table cellpadding='0' cellspacing='0' border='0' width='100%'>
			<tr>
				<td align='center'><button>충전하기</button></td>
			</tr>
			<tr>
				<td align='center'><button>발송하기</button></td>
			</tr>
		</table>
	</td>
</tr>
</table>
<div class='center_menu'>
	<div class='sub_centes'>
		<div class='serviceBox'>
			<h1><img src='../images/myservice/sms_img01.jpg' title='' align='absmiddle'></h1>
			<!--SMS서비스 [S]-->
			<!--SMS서비스란? [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/sms_title01.gif' title='SMS서비스란?' align='absmiddle' /></h2>
				<ul>
					<li>
						고객관리가 필요한 쇼핑몰 운영 시 기본이 되는 부가서비스로 쇼핑 단계별로 고객의 핸드폰으로 안내 메시지를 자동전송하며
						그 외 고객과의 커뮤니케이션 및 맞춤정보 전달이 가능한 <strong>고객만족 서비스</strong> 입니다.
					</li>
				</ul>
			</div>
			<!--SMS서비스란? [E]-->
			<!--왜필요할까요? [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/online_service_title06.gif' title='왜필요할까요?' align='absmiddle' /></h2>
				<ul class='left_listarrow'>
					<li>
						<span> 주문 접수, 입금확인, 배송안내 등 신속한 정보로 고객과의 신뢰감 형성!</span>
					</li>
					<li>
						<span> 문자 하나로 신상품 입고 안내, 이벤트 홍보까지 편리하게!</span>
					</li>
					<li>
						<span> 생일 축하 메세지, 쿠폰 발행 등 고객의 니즈를 충족 시키는 서비스 가능!</span>
					</li>
				</ul>
				<div class='Pgap_H20'>
					<img src='../images/myservice/sms_img02.gif' title='' align='absmiddle'>
				</div>
			</div>
			<!--왜필요할까요? [E]-->
			<!--SMS서비스사용예 [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/sms_title02.gif' title='SMS서비스사용예' align='absmiddle' /></h2>
				<div class='Pgap_H20'>
					<img src='../images/myservice/sms_img03.gif' title='SMS서비스사용예' align='absmiddle'>
				</div>
			</div>
			<!--SMS서비스사용예 [E]-->
			<!--서비스이용안내 [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/taxsupport_title03.gif' title='서비스이용안내' align='absmiddle' /></h2>
				<div>
					<img src='../images/myservice/sms_img04.gif' title='서비스이용안내' align='absmiddle' />
				</div>
				<ul class='Pgap_H20 left_listarrow'>
					<li>
						<span> SMS 서비스를 이용하기 위해서는 '핸드폰/신용카드/무통장입금/자동이체'를 통해 원하시는 금액만큼 충전하여 사용할 수 있습니다. </span>
					</li>
					<li>
						<span> 충전금액은 발송건수에 따라 최저 20원입니다. (충전금액은 부가세 별도) </span>
					</li>
					<li>
						<span> 별도의 신청절차 없이 충전만 하면 쇼핑몰에서 바로 문자서비스가 시작됩니다. </span>
					</li>
					<li>
						<span> 발송 성공한 건수에 대해서만 차감되며 실패한 건수에 대해서는 과금하지 않습니다. </span>
					</li>
				</ul>
			</div>
			<!--서비스이용안내 [E]-->
			<!--SMS서비스 [E]-->
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
$P->Navigation = "마이서비스 > SMS서비스";
$P->title = "SMS서비스";
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