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
<div class='center_menu' style='width:740px;'>
	<div class='sub_centes'>
		<div class='serviceBox'>
			<h1><img src='../images/myservice/no_bankbook_img01.jpg' title='' align='absmiddle'></h1>
			<!--무통장입금 [S]-->
			<!--무통장입금내역조회서비스란 [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/no_bankbook_title01.gif' title='무통장입금내역조회서비스란?' align='absmiddle' /></h2>
				<ul>
					<li>
						쇼핑몰 고객들이 여러개의 은행계좌로 입금한 내역을 쇼핑몰 주문 내역과 자동으로 비교하여 관리하실 수 있는 서비스 입니다. 여러 개의 은행계좌로 입금된 결제금액과 주문내역들…많이 번거로우셨죠? 이젠 자동으로 입금 확인하세요
					</li>
				</ul>
				<div class='Mgap_H20'>
					<img src='../images/myservice/no_bankbook_img02.gif' title='?' align='absmiddle' />
				</div>
				<ul>
					<li>
						<h4 class='orange01'><img src='../images/myservice/list_arrow_icon.jpg' title='' align='absmiddle' />  <span style='vertical-align:middle;'>고객 → 쇼핑몰 DB 서버</span></h4>
						<div class='orange_list'>
							주문 후 무통장입금 예약 (주문번호 생성)
						</div>
					</li>
					<li>
						<h4 class='orange01'><img src='../images/myservice/list_arrow_icon.jpg' title='' align='absmiddle' />  <span style='vertical-align:middle;'>고객 → 은행</span></h4>
						<div class='orange_list'>
							쇼핑몰로 주문금액 계좌이체/무통장 입금 (입금자명과 주문자명을 동일하게 입력)
						</div>
					</li>
					<li>
						<h4 class='orange01'><img src='../images/myservice/list_arrow_icon.jpg' title='' align='absmiddle' />  <span style='vertical-align:middle;'>은행 → 넷텔러웹</span></h4>
						<div class='orange_list'>
							스케줄러에 의해 자동으로 입금내역을 가져옴 (입금자명/입금액/입금일자 등)
						</div>
					</li>
					<li>
						<h4 class='orange01'><img src='../images/myservice/list_arrow_icon.jpg' title='' align='absmiddle' />  <span style='vertical-align:middle;'>쇼핑몰 db서버 → 넷텔러웹</span></h4>
						<div class='orange_list'>
							스케줄러에 의해 자동으로 입금내역을 가져옴 (주문자명/주문번호/주문금액 등)
						</div>
					</li>
					<li>
						<h4 class='orange01'><img src='../images/myservice/list_arrow_icon.jpg' title='' align='absmiddle' />  <span style='vertical-align:middle;'>넷텔러웹 → 쇼핑몰 db서버</span></h4>
						<div class='orange_list'>
							스케줄러에 의해 자동으로 주문자명과 입금자명, 주문금액과 입금액등을 대조 후 정상매치인 경우 입금확인 처리
						</div>
					</li>
				</ul>
			</div>
			<!--무통장입금내역조회서비스란? [E]-->
			<!--왜필요할까요? [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/online_service_title06.gif' title='왜필요할까요?' align='absmiddle' /></h2>
				<ul class='left_listarrow'>
					<li>
						<span> 입금자명과 주문내역 자동 비교로 편리한 입금내역 관리가 가능합니다.</span>
					</li>
					<li>
						<span> 고객의 입금확인요청 전에 처리되므로 고객의 입금여부 확인을 빠르게 알려드릴 수 있어 고객만족이 상승됩니다.</span>
					</li>
					<li>
						<span> 여러 개의 은행계좌를 통합관리 할 수 있어 편리합니다. </span>
					</li>
					<li>
						<span>시간 절약으로 운영자님의 귀한 시간을 금쪽같은 쇼핑몰에 투자하실 수 있습니다.</span>
					</li>
				</ul>
			</div>
			<!--왜필요할까요? [E]-->
			<!--넷텔러웹 전용 컴퓨터 [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/no_bankbook_title02.gif' title='넷텔러웹 전용 컴퓨터' align='absmiddle' /></h2>
				<ul class='left_listarrow'>
					<li>
						<span> 원격으로 넷텔러웹 설치 및 세팅이 가능합니다.<span>
					</li>
					<li>
						<span> ps/2타입의 키보드가 항상 연결되어 있어야 합니다. (UBS 타입 이용 불가)<span>
					</li>
					<li>
						<span> 비용면에서 IDC에 위치해 있을 필요가 없으며 귀사 사무실내에 위치해 있어도 충분합니다.<span>
					</li>
				</ul>
				<div class='domain_menu01 Pgap_H20'>
					<table cellpadding='0' cellspacing='0' border='0' width='734' class='Table_box'>
						<col width='177' />
						<col width='*' />
						<tr>
							<td class='bg_td'>
								<span class='Mgap_L15'>CPU</span>
							</td>
							<td>
								<span style='font-size:14px;' class='Mgap_L15'>셀러론 이상</span>
							</td>
						</tr>
						<tr>
							<td class='bg_td'>
								<span class='Mgap_L15'>메모리</span>
							</td>
							<td>
								<span style='font-size:14px;' class='Mgap_L15'>512M 이상</span>
							</td>
						</tr>
						<tr>
							<td class='bg_td'>
								<span class='Mgap_L15'>하드디스크</span>
							</td>
							<td>
								<span class='Mgap_L15'>40G 이상</span>
							</td>
						</tr>
						<tr>
							<td class='bg_td' style='border-bottom:solid 1px #d6d6d7;'>
								<span class='Mgap_L15'>O/S</span>
							</td>
							<td style='border-bottom:solid 1px #d6d6d7;'>
								<span class='Mgap_L15'>Windows 2000 professional, Windows XP</span>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<!--넷텔러웹 전용 컴퓨터 [E]-->
			<!--신청안내 [S]-->
			<div class='Pgap_B50'>
				<h2><img src='../images/myservice/no_bankbook_title03.gif' title='신청안내' align='absmiddle' /></h2>
				<div>
					<img src='../images/myservice/no_bankbook_img03.gif' title='신청안내' align='absmiddle' />
				</div>
				<div style='padding:30px 0px 10px 0;'>
					<ul class='left_listarrow'>
						<li>
							<span> 원격설치, 교육 및 기타지원 가능</span>
						</li>
					</ul>
				</div>
				<div class='domain_menu01 Pgap_H20'>
					<table cellpadding='0' cellspacing='0' border='0' width='734' class='Table_box'>
						<col width='177' />
						<col width='*' />
						<tr>
							<td class='bg_td'>
								<span class='Mgap_L15'>월 사용료</span>
							</td>
							<td>
								<span style='font-size:12px;color:red;' class='Mgap_L15'>별도문의</span>
							</td>
						</tr>
						<!--tr>
							<td class='bg_td'>
								<span class='Mgap_L15'>접수서류</span>
							</td>
							<td>
								<span style='font-size:12px;' class='Mgap_L15'>신청서( 다운로드 후 작성하셔서 우편 또는 팩스로 보내주세요.)</span>
							</td>
						</tr-->
						<tr>
							<td class='bg_td' style='border-bottom:solid 1px #d6d6d7;'>
								<span class='Mgap_L15'>문의</span>
							</td>
							<td style='border-bottom:solid 1px #d6d6d7;'>
								<div class='Mgap_L15' style='line-height:150%;'>
								<!--서울시 서초구 양재동 16-3 윤화빌딩 6층<br />
								몰스토리 <strong>문정길 대리</strong> 앞<br /-->
								- 문의전화 : 1600.2028<br />
								- FAX : 02.2058.2215<br />
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div class='Mgap_H20' style='text-align:right;'>
					<a href='/customer/bbs.php?mode=write&board=qna_estimate'><img src='../images/myservice/online_ask03.gif' title='문의하기' align='absmiddle' /></a>
				</div>
			</div>
		</div>
		<!--신청안내 [E]-->
		<!--대량메일 [E]-->
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
$P->Navigation = "부가제휴서비스 > 입금확인서비스";
$P->title = "입금확인서비스";
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