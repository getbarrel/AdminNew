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

$Contents01 ="
<table cellpadding='0' cellspacing='0' border='0' width='100%' class='list_table_box'>
<col width='20%' />
<col width='20%' />
<col width='20%' />
<col width='20%' />
<col width='*' />
<tr>
	<td class='s_td' style='padding:5px 0;'>서비스구분</td>
	<td class='m_td'>신청일</td>
	<td class='m_td'>남은 건수</td>
	<td class='m_td'>서비스 내용</td>
	<td class='e_td'>관리</td>
</tr>
<tr>
	<td class='list_box_td list_bg_gray' style='padding:5px 0;'>대량이메일</td>
	<td class='list_box_td'>2012-04-15</td>
	<td class='list_box_td list_bg_gray' >100건/일</td>
	<td class='list_box_td'>서비스 내용</td>
	<td class='list_box_td list_bg_gray'>
		<table cellpadding='0' cellspacing='0' border='0' width='100%'>
			<tr>
				<td align='center'><button>충전하기</button></td>
			</tr>
			<tr>
				<td align='center'><button>비즈형 전환</button></td>
			</tr>
		</table>
	</td>
</tr>
</table>
<div class='center_menu'>
	<div class='sub_centes'>
		<div class='serviceBox'>
			<h1><img src='../images/myservice/big_mail_img01.jpg' title='' align='absmiddle'></h1>
			<!--대량메일서비스 [S]-->
			<!--대량메일서비스란? [S]-->
			<div class='Pgap_B50 under_line01' style='width:734px;'>
				<h2><img src='../images/myservice/big_mail_title.gif' title='대량메일서비스란?' align='absmiddle' /></h2>
				<div style='' class='text_list'>
					바이럴 마케팅의 시초라고 할 수 있는 이메일마케팅~ 이메일 마케팅은 오늘날 사업 성장에서 있어서 가장 효과적인 방법 중 하나입니다. 누구든 잠재 고객을 상대로 이메일 마케팅을 할 수 있으며, 최소의 비용으로 높은 전환율을 볼 수 있습니다. 이메일은 주력상품과 서비스에 대한 고객의 반응을 유도하기 위해 개별적으로 고객에게 접근하는 방식이며, 쇼핑몰의 인지도와 상품과 서비스의 대한 정보를 가장 빠르고 직접적으로 전달할 수 있다라는 장점을 가지고 있습니다. 현재 대부분의 쇼핑몰 솔루션은 개별로 이메일 발송하는 기능은 어느 쇼핑몰이든 가지고 있습니다. 하지만 쇼핑몰을 운영하다 보면, 전체메일, 그룹메일, 이벤트메일 등등 마케팅적으로 많이 사용하게되는데 이때 개별 회원 이메일 발송의 불편함과 시간이 많이 소요됨으로, 이와 같은 불편함을 해소하기 위해 쇼핑몰 자체내에서 회원에게 단체로 이메일을 보다 쉽고 빠르며 안정적으로 쇼핑몰 회원에게 메일을 전달할 수 있는 서비스라고 할 수 있습니다.
				</div>
			</div>
			<!--대량메일서비스란? [E]-->
			<!--왜필요할까요? [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/online_service_title06.gif' title='왜필요할까요?' align='absmiddle' /></h2>
				<div class='text_list'>
					어떤 쇼핑몰을 운영하더라도 이메일 발송을 어쩔 수 없이 해야 하는 기본 운영마케팅입니다. 회원가입, 상품구매, 배송완료 등등의 결과에 따라 이메일을 구매자에게 보내주는 것이 현재의 기본 틀이지만, 이는 몰스토리에서 자동이메일 설정을 통해 해결할 수 있습니다. 그럼 대량이메일은 왜 필요할까요? 가장 중요한 포인트는 마케팅입니다. 신상품, 세일전, 이벤트, 공지사항등 회원이 매일 들어와서 확인할 수 없기 때문에 판매자가 이런 내용들을 알리기 위해 이메일을 발송하게됩니다. 만약 회원이 10명~100명정도라면 괜찮겠지만, 수백에서 수십만이라면 이메일 발송하는 시간만 하더라도 어마어마하겠죠! 이를 위해 대량메일로 전체 혹은 타겟에 따라 이메일을 발송하여 관리적, 시간적 비용을 줄일 수 있는 것입니다. 또한 몰스토리는 대량메일 솔루션 기능을 자체적으로 보유하고 있어 보다 안정적이고 대량메일을 보낼 수 있으며, 발송후의 발송여부, 성공여부, 유입율 등의 리포트도 확인할 수 있습니다.
				</div>
			</div>
			<!--왜필요할까요? [E]-->
			<!--서비스특징 [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/web_hard_title03.gif' title='서비스특징?' align='absmiddle'></h2>
				<table cellpadding='0' cellspacing='0' border='0' width='734'>
					<col width='50' />
					<col width='*' />
					<tr>
						<td valign='top'>
							<img src='../images/myservice/img01.gif' title='' align='absmiddle' class='Mgap_H10'>
						</td>
						<td>
							<h4>관리자모드에서 직접 보낼 수가 있습니다.</h4>
							<div class='text_list'>
								발송리포트를 통하여, 발송한 이메일의 정확한 발송결과 확인 가능하며. 발송성공률/에러율/개봉률 등의 데이트를 통하여 보다 개선된 이메일 발송이 가능합니다.
							</div>
						</td>
					</tr>
					<tr>
						<td valign='top'>
							<img src='../images/myservice/img02.gif' title='' align='absmiddle' class='Mgap_H10'>
						</td>
						<td>
							<h4>회원별 타겟 발송 가능</h4>
							<div class='text_list'>
								쇼핑몰 회원검색 후, 원하는 회원을 대상으로 타겟 발송을 통하여 더욱 효과적인 이메일 마케팅이 가능합니다.
							</div>
						</td>
					</tr>
					<tr>
						<td valign='top'>
							<img src='../images/myservice/img03.gif' title='' align='absmiddle' class='Mgap_H10'>
						</td>
						<td>
							<h4>발송 시간의 제약이 없는 이메일</h4>
							<div class='text_list'>
								쇼핑몰에 기본적으로 제공되는 이메일과 관계없이 충전즉시 원할 때 이메일 발송이 가능합니다.
							</div>
						</td>
					</tr>
					<tr>
						<td valign='top'>
							<img src='../images/myservice/img04.gif' title='' align='absmiddle' class='Mgap_H10'>
						</td>
						<td>
							<h4>저렴한 발송 요금(1원/1통)</h4>
							<div class='text_list'>
								대량메일은 발송에 성공한 메일에 대하여 1통/1원의 요금이 부과됩니다.(부가세 별도) 충전은 1,000건 부터 언제든지 가능합니다.
							</div>
							<div>
								*소호형, 비즈형 PG사용자만 가능며, 대량메일신청은 쇼핑몰 관라자에서 신청가능합니다.
							</div>
						</td>
					</tr>
				</table>
			</div>
			<!--서비스특징 [E]-->
			<!--대량메일 사용하기 [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/big_mail_title_02.gif' title='모바일샵디자인변경/모바일어플리케이션신청안내' align='absmiddle'></h2>
				<div style='margin:30px 0 10px 0;'>
					<img src='../images/myservice/big_mail02.gif' title='' align='absmiddle'>
				</div>
			</div>
			<!--대량메일 사용하기 [E]-->
			<!--대량메일 [E]-->
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
$P->Navigation = "기본 운영서비스 > 대량이메일";
$P->title = "대량이메일";
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