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
<div class='center_menu'>
	<div class='sub_centes'>
		<div class='serviceBox'>
			<h1 style='border:0;'><img src='../images/myservice/taxation_h1_img01.gif' title='' align='absmiddle'></h1>
			<!--세무지원서비스 [S]-->
			<!--세무지원서비스란 [S]-->
			<div class='Pgap_B20 '>
				<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/taxation_title01.gif' title='세무지원서비스란?' align='absmiddle' /></h2>
				<ul class='bottom10_list'>
					<li>
						법인이든 개인이든 사업자가 소득이 발생하면 국가에 정기적으로 거래사실을 증명하기 위해 부가가치세와 종합소득세, 법인세를 <br />신고해야 합니다.<strong> 이러한 세무/회계 관련 신고를 보다 편리하고 안전하게 운영하게끔 도와드리는 것이 세무대행 서비스 입니다.</strong>
					</li>
					<li style='padding-top:20px;'>
						<img src='../images/myservice/taxation_title01_list01.gif' title='' align='absmiddle' />
					</li>
				</ul>
			</div>
			<!--세무지원서비스란? [E]-->
			<!--세무회계 주요업무 [S]-->
			<div class='Pgap_B20 '>
				<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/taxation_title02.gif' title='세무회계 주요업무' align='absmiddle' /></h2>
				<ul class='bottom10_list'>
					<li style='font-size:14px;font-weight:600;letter-spacing:-1px;'>
						세무대리업무 및 세무조정 신고대행  /  법인설립 및 회계 입안 업무  /  4대보험 신고업무 대행  /  세무상담 및 교육  / <br />
						상속, 증여, 양도등 신고대행  /  이의신청 등 불복청구 및 구제관련
					</li>
				</ul>
				<h3>
					<img src='../images/myservice/taxation_title02_s_title01.gif' title='[ 주요세무신고 일정 안내 ]' align='absmiddle' />
				</h3>
				<h4 class='h4_gap'>
					<img src='../images/myservice/taxation_title02_s_list01.gif' title='1) 부가세 신고' align='absmiddle' />
				</h4>
				<div class='box690'>
					<ul class='bottom10_list'>
						<li>
							예정신고대상  - 모든 법인사업자와 일부 개인사업자
						</li>
						<li>
							확정신고대상  - 모든 사업자
						</li>
					</ul>
					<table cellpadding='0' cellspacing='0' border='0' width='100%' class='paybox border_left'>
						<col width='20%' />
						<col width='20%' />
						<col width='20%' />
						<col width='20%' />
						<col width='*' />
						<tr>
							<td class='titlebox'>
								구분
							</td>
							<td class='titlebox'>
								1기 예정
							</td>
							<td class='titlebox'>
								1기 확정
							</td>
							<td class='titlebox'>
								2기 예정
							</td>
							<td class='titlebox border0'>
								2기 확정
							</td>
						</tr>
						<tr>
							<td>
								과세기한
							</td>
							<td>
								1/1 ~ 3/31
							</td>
							<td>
								4/1~6/30
							</td>
							<td>
								7/1~9/30
							</td>
							<td class='border0'>
								10/1~12/31
							</td>
						</tr>
						<tr>
							<td>
								신고기한
							</td>
							<td>
								4/25일 까지
							</td>
							<td>
								7/25일까지
							</td>
							<td>
								10/25일 까지
							</td>
							<td class='border0'>
								다음해 1/25일까지
							</td>
						</tr>
					</table>
				</div>
				<h4 class='h4_gap'>
					<img src='../images/myservice/taxation_title02_s_title02.gif' title='2) 종합소득세 신고' align='absmiddle' />
				</h4>
				<div class='box690'>
					<ul class='bottom10_list'>
						<li>
							모든 개인사업자 대상
						</li>
					</ul>
					<table cellpadding='0' cellspacing='0' border='0' width='100%' class='paybox border_left'>
						<col width='*' />
						<col width='40%' />
						<col width='40%' />
						<tr>
							<td class='titlebox' rowspan='2'>
								과세표준
							</td>
							<td class='titlebox' colspan='2'>
								2010년 ~2011년 귀속
							</td>
						</tr>
						<tr>
							<td class='titlebox'>
								세율
							</td>
							<td class='titlebox'>
								누진공제
							</td>
						</tr>
						<tr>
							<td>
								1,200만원 이하
							</td>
							<td>
								6%
							</td>
							<td>
								-
							</td>
						</tr>
						<tr>
							<td>
								4,600만원 이하
							</td>
							<td>
								15%
							</td>
							<td>
								108만원
							</td>
						</tr>
						<tr>
							<td>
								8,800만원 이하
							</td>
							<td>
								24%
							</td>
							<td>
								522만원
							</td>
						</tr>
						<tr>
							<td>
								8,800만원 초과
							</td>
							<td>
								35%
							</td>
							<td>
								1,409만원
							</td>
						</tr>
					</table>
				</div>
				<h4 class='h4_gap'>
					<img src='../images/myservice/taxation_title02_s_title03.gif' title='3) 법인세 신고' align='absmiddle' />
				</h4>
				<div class='box690'>
					<ul class='bottom10_list'>
						<li>
							법인사업자 대상 (2010년도 기준)
						</li>
					</ul>
					<table cellpadding='0' cellspacing='0' border='0' width='100%' class='paybox border_left'>
						<col width='*' />
						<col width='40%' />
						<col width='40%' />
						<tr>
							<td class='titlebox'>
								구분
							</td>
							<td class='titlebox'>
								대상 기간
							</td>
							<td class='titlebox'>
								신고 신고
							</td>
						</tr>
						<tr>
							<td>
								법인세 중간예납
							</td>
							<td>
								1/1~6/30
							</td>
							<td>
								8/31일 까지
							</td>
						</tr>
						<tr>
							<td>
								법인세 확정신고
							</td>
							<td>
								1/1~12/31
							</td>
							<td>
								다음해 3/31 까지
							</td>
						</tr>
					</table>
					<h4 class='h4_gap'>
						<img src='../images/myservice/taxation_title02_s_title04.gif' title=' 4대보험 요율표' align='absmiddle' />
					</h4>
				<div class='box690'>
					<table cellpadding='0' cellspacing='0' border='0' width='100%' class='paybox border_left'>
						<col width='*' />
						<col width='18%' />
						<col width='20%' />
						<col width='15%' />
						<col width='15%' />
						<col width='10%' />
						<col width='10%' />
						<tr>
							<td class='titlebox' rowspan='2' colspan='2'>
								항목
							</td>
							<td class='titlebox' rowspan='2'>
								대상 기간
							</td>
							<td class='titlebox' colspan='2'>
								요울
							</td>
							<td class='titlebox' rowspan='2'>
								합계
							</td>
							<td class='titlebox' rowspan='2'>
								적용대상
							</td>
						</tr>
						<tr>
							<td class='titlebox'>
								근로자부담
							</td>
							<td class='titlebox'>
								사용자부담
							</td>
						</tr>
						<tr>
							<td colspan='2'>
								국민연금
							</td>
							<td>
								월급여액 (비과세제외)
							</td>
							<td>
								4.50%
							</td>
							<td>
								4.50%
							</td>
							<td>
								9.0%
							</td>
							<td rowspan='6'>
								1인 이상
							</td>
						</tr>
						<tr>
							<td colspan='2'>
								건강보험
							</td>
							<td>
								상동
							</td>
							<td>
								2.82%
							</td>
							<td>
								5.33%
							</td>
							<td>
								9.0%
							</td>
						</tr>
						<tr>
							<td rowspan='3'>
								고용보험
							</td>
							<td>
								실업급여
							</td>
							<td>
								상동
							</td>
							<td>
								0.55%
							</td>
							<td>
								0.55%
							</td>
							<td>
								0.90%
							</td>
						</tr>
						<tr>
							<td>
								고용안정사업
							</td>
							<td>
								상동
							</td>
							<td>
								-
							</td>
							<td>
								0.15%
							</td>
							<td>
								0.15%
							</td>
						</tr>
						<tr>
							<td>
								직업능력개발사업
							</td>
							<td>
								상동
							</td>
							<td>
								-
							</td>
							<td>
								0.10%
							</td>
							<td>
								0.10%
							</td>
						</tr>
						<tr>
							<td colspan='2'>
								산재보험
							</td>
							<td>
								상동
							</td>
							<td>
								-
							</td>
							<td>
								1%
							</td>
							<td>
								1%
							</td>
						</tr>
					</table>
				</div>
			</div>
			<!--세무회계 주요업무 [E]-->
			<!--세무지원서비스란 [S]-->
			<div class='Pgap_B20 '>
				<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/taxation_title03.gif' title=' 몰스토리 세무회계 서비스 할인 혜택' align='absmiddle' /></h2>
				<ul class='bottom10_list'>
					<li>
						<img src='../images/myservice/taxation_title03_list01.gif' title='서비스 신청 첫달 기장료 무료' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/taxation_title03_list02.gif' title='세무회계 각종 컨설팅 무료' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/taxation_title03_list03.gif' title='기본 기장료 안내' align='absmiddle' />
					</li>
				</ul>
				<div class='box690'>
					<table cellpadding='0' cellspacing='0' border='0' width='100%' class='paybox border_left'>
						<col width='20%' />
						<col width='25%' />
						<col width='25%' />
						<col width='*' />
						<tr>
							<td class='titlebox'>
								구분
							</td>
							<td class='titlebox'>
								연매출
							</td>
							<td class='titlebox'>
								기장료(VAT별도)
							</td>
							<td class='titlebox border0'>
								비고
							</td>
						</tr>
						<tr>
							<td rowspan='3'>
								개인
							</td>
							<td>
								1억이하
							</td>
							<td>
								50,000원
							</td>
							<td class='border0'>
								-
							</td>
						</tr>
						<tr>
							<td>
								1억~3억이하
							</td>
							<td>
								80,000원
							</td>
							<td class='border0'>
								-
							</td>
						</tr>
						<tr>
							<td>
								3억초과
							</td>
							<td>
								100,000원 ~
							</td>
							<td class='border0'>
								별도 협의
							</td>
						</tr>
						<tr>
							<td>
								법인
							</td>
							<td>
								-
							</td>
							<td>
								150,000원 ~
							</td>
							<td class='border0'>
								별도 협의
							</td>
						</tr>
					</table>
				</div>
				<ul style='margin-top:10px;'>
					<li>
						<img src='../images/myservice/taxation_title03_list04.gif' title='종합소득세 신고대행 수수료 (업종, 매출액 등 기타사항에 따라 변동될 수 있음)' align='absmiddle' />
					</li>
					<li style='margin:10px 0 0 20px;'>
						<div>
							- 추계신고 대상자 : 50,000원(단순경비율 대상자) / 10,000원~200,000원 (기준경비율 대상자)
						</div>
						<div style='margin-top:5px;'>
							- 간편장부 대상자 : 300,000 ~ 600,000원
						</div>
						<div style='margin-top:10px;line-height:150%;'>
							* 참조 : 간이과세자는 1과세기간 (상.하반기 각각 6개월)동안의 공급대가 1,200만원 미만이면 부가세를 면합니다.<br />&nbsp;&nbsp;
								다만 ,부가세 신고는 간이과사제라도 신고의 의무는 없으며, 매출액에 따라 신고여부를 판단합니다.
						</div>
					</li>
				</ul>
			</div>
			<!--세무지원서비스란? [E]-->
			<!--세무서비스주요업무 [S]-->
			<div class='Pgap_B20 '>
				<h2 class='Mgap_B20'><img src='../images/myservice/taxation_title04.gif' title='세무회계 서비스 신청 및 상담' align='absmiddle' /></h2>
				<div>
					<img src='../images/myservice/taxsupport_img02.gif' title='서비스 이용안내 ' align='absmiddle' />
				</div>
				<ul style='margin-top:10px;'>
					<li>
						<img src='../images/myservice/taxation_title04_list01.gif' title='세무법인 “강남제일 세무법인” 상담' align='absmiddle' />
					</li>
					<li style='margin:10px 0 0 20px;'>
						<div>
							000 세무사 / 000 차장
						</div>
						<div style='margin-top:5px;'>
							전화 : 02-529-0279  /  팩스 : 0000  /  메일 : 0000
						</div>
						<div style='margin-top:5px;line-height:150%;color:#ea4200;'>
							*서비스 상담시 반드시 ‘몰스토리’고객임을 말씀해 주세요!
						</div>
					</li>
				</ul>
			</div>
			<!--세무서비스주요업무 [E]-->
			<!--대량메일 [E]-->
			<div class='Mgap_H20' style='text-align:center;'>
				<a href='/customer/bbs.php?mode=list&board=qna'><img src='../images/myservice/ask_btn_my.gif' title='문의하기' align='absmiddle' /></a>
			</div>
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
$P->Navigation = "부가제휴서비스 > 세무회계";
$P->title = "세무회계";
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