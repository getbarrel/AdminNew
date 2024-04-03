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
<div class='serviceBox' style='width:740px; padding-left:40px;'>
<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/online_service_title01.gif' title='실명인증서비스란?' align='absmiddle' /></h2>
				<ul>
					<li>
						고객의 주민등록번호와 성명이 일치하는지 확인하는 서비스입니다.
					</li>
					<li>
						인터넷사업자가 가장 간편한 본인확인 수단으로 널리 활용하고 있는 서비스 입니다.
					</li>
				</ul>
				<div>
					<img src='../images/myservice/online_service_img02.gif' title='' align='absmiddle' />
				</div>
				<div>
					<h3 class='title_O1'><span>서비스 내용</span></h3>
					<ul class='ul_listbox01'>
						<li>
							<div>개인의 주민등록번호와 성명의 일치 여부를 <strong>실시간으로 확인하는 서비스</strong>입니다. </div>
						</li>
						<li>
							<div> <strong>한국신용정보에서 직접 확인</strong>한 실명정보와 국내 금융기관(은행, 카드, 캐피탈, 신협, 저축은행, 보험 등)은 물론, 백화점, 의류, 통신 등 비 금융기관에서 확인한 실명정보를 토대로 서비스를 제공합니다. </div>
						</li>
						<li>
							<div>약 4천5백만명 이상 개인 실명 DB를 토대로 한 <strong>높은 인증률</strong> </div>
						</li>
					</ul>
				</div>
				<div>
					<h3 class='title_O1'><span>이용대상</span></h3>
					<ul class='ul_listbox01'>
						<li>
							<div>회원가입을 받는 인터넷 사업자 </div>
						</li>
						<li>
							<div>게시판을 운영하는 인터넷사업자 및 개인 </div>
						</li>
						<li>
							<div>쇼핑몰, 홈쇼핑, 경매 등 거래사이트 </div>
						</li>
						<li>
							<div>물품을 직접 판매하는 사이트 </div>
						</li>
						<li>
							<div>미성년자의 이용을 제안해야 하는 성인사이트 </div>
						</li>
						<li>
							<div>기타 회원의 실명확인이 필요한 인터넷 사이트 및 사업자 </div>
						</li>
					</ul>
				</div>
			</div>
			<!--실명인증서비스 [E]-->
			<!--서비스의 필요성 및 효과 [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/online_service_title02.gif' title='서비스의 필요성 및 효과' align='absmiddle' /></h2>
				<div>
					<img src='../images/myservice/online_service_img03.gif' title='' align='absmiddle' />
				</div>
				<ul class='ul_listbox01 Pgap_H20'>
					<li>
						<div>몰스토리 실명확인서비스는 빠른 응답속도를 유지하며, 서비스를 24시간 안정적으로 제공하고 있습니다.</div>
					</li>
					<li>
						<div>서비스 테스트 및 오픈은 하루에 가능합니다. 또한, 데이터의 정확성이 높아 민원발생 빈도가 낮습니다. 민원이 발생할 경우, 전문상담직원이 최상의 민원서비스를 제공하고 있습니다.</div>
					</li>
				</ul>
				<table cellpadding='0' cellspacing='0' border='0' width='734' class='Table_box Sbox_table'>
					<col width='108' />
					<col width='*' />
					<tr>
						<td class='bg_td'>
							<strong>신뢰성</strong>
						</td>
						<td>
							<div>
								국내 금융기관 및 NICE신용평가정보에서 확인된 정확한 정보만을 제공하고 있습니다. <strong>국내에서 가장 많은 4천7백만명의 실명정보를 보유</strong>하고 있으며 매월 20만명 이상의 정보를 신규로 등록하고 있습니다.
							</div>
						</td>
					</tr>
					<tr>
						<td class='bg_td'>
							<strong>안정성</strong>
						</td>
						<td>
							<div>
								<strong>빠른 응답속도</strong>를 유지함과 동시에 서비스를 <strong>24시간 안정적으로 운영</strong>하고 있으며, 관리 요원이 계속 모니터링 및 관리하고 있습니다.
							</div>
						</td>
					</tr>
					<tr>
						<td class='bg_td'>
							<strong>정확성</strong>
						</td>
						<td>
							<div>
								데이터의 <strong>정확성이 매우 높아 민원발생 빈도가 낮습니다.</strong> 만일 민원이 발생할 경우 전문상담원이 최상의 민원 서비스를 제공하고 있습니다.
							</div>
						</td>
					</tr>
					<tr>
						<td class='bg_td'>
							<strong>확장성</strong>
						</td>
						<td>
							<div>
								ASP, JSP, PHP 등 <strong>다양한 환경</strong>에서의 클라이언트를 무상으로 제공하여, 웹 방식 외에도 전용선을 이용 방식도 제공하고 있습니다.
							</div>
						</td>
					</tr>
					<tr>
						<td class='bg_td'>
							<strong>보안성</strong>
						</td>
						<td>
							<div>
								전문 암호화들에 의해 데이터 전송을 보호하고 있으며, 매우 강력한 보안체계를 제공하고 있습니다.
							</div>
						</td>
					</tr>
					<tr>
						<td class='bg_td'>
							<strong>경제성</strong>
						</td>
						<td>
							<div>
								회원사 부담을 최소화할 수 있는 <strong>합리적인 가격 체계</strong>를 적용하고 있으며, 다양한 요금제의 상품이 준비되어 있습니다.
							</div>
						</td>
					</tr>
					<tr>
						<td class='bg_td'>
							<strong>대중성</strong>
						</td>
						<td>
							<div>
								국내 1만 여 개 이상의 사이트에 이미 제공되고 있으며, 인터넷전문기업에서부터 언론/미디어사, 금융기관, 통신업체, 관공서 및 지자체에 이르기까지 <strong>다양한 회원사</strong>들이 이용하고 있습니다.
							</div>
						</td>
					</tr>
					<tr>
						<td class='bg_td' style='border-bottom:solid 1px #d6d6d7;'>
							<strong>신속성</strong>
						</td>
						<td style='border-bottom:solid 1px #d6d6d7;'>
							<div>
								일괄 배치 조회 서비스의 경우 회원사의 빠른 업무 처리를 위해 처리 요청일로부터 <strong>24시간이내처리</strong> 하는것을 원칙으로 하고 있습니다.
							</div>
						</td>
					</tr>

				</table>
			</div>
			<!--서비스의 필요성 및 효과 [E]-->
			<!--서비스내용 [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/online_service_title03.gif' title='서비스내용' align='absmiddle' /></h2>
				<ul style='margin-bottom:20px;'>
					<li>
						몰스토리를 통한 실명인증 서비스 계약 시 할인 요금에 관해서는 <strong>전화(<span style='color:red;'>1600-2028</span>)로 문의해 주시기 바랍니다.</strong>
					</li>
					<li>
						<strong>몰스토리 고개님께 국내 최저가로 서비스해 드립니다. </strong>
					</li>
				</ul>
				<table cellpadding='0' cellspacing='0' border='0' width='734' class='Table_box Sbox_table Sbox_table01'>
					<col width='*' />
					<col width='200' />
					<col width='200' />
					<col width='200' />
					<tr>
						<th class='bg_td'></th>
						<th class='bg_td Pgap_H10'>월간제</th>
						<th class='bg_td'>선납제(6개월)</th>
						<th class='bg_td'>선납제(12개월)</th>
					</tr>
					<tr>
						<td class='bg_td'>
							제공
						</td>
						<td colspan='3'>
							<span style='color:red;'>6000건</span>/월
						</td>
					</tr>
					<tr>
						<td class='bg_td'>
							요금
						</td>
						<td>
							<span style='color:red;'>55,000</span>원/1개월(부가세포함)
						</td>
						<td>
							<span style='color:red;'>297,000</span>원/6개월(부가세포함)
						</td>
						<td>
							<span style='color:red;'>550,000</span>원/12개월(부가세포함)
						</td>
					</tr>
					<tr>
						<td class='bg_td'>
							초과수수료
						</td>
						<td colspan='3'>
							20원/건
						</td>
					</tr>
					<tr>
						<td class='bg_td'>
							기타
						</td>
						<td colspan='3'>
							관리자 모드에서 실명인증 설정값 입력시 즉시 사용가능
						</td>
					</tr>
				</table>
			</div>
			<!--서비스내용 [E]-->
			<!--신청안내 [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/online_service_title04.gif' title='신청안내' align='absmiddle' /></h2>
				<div style='padding:10px 0 30px 0;'>
					<img src='../images/myservice/online_service_img04.gif' title='' align='absmiddle' />
				</div>
				<table cellpadding='0' cellspacing='0' border='0' width='734' class='Table_box Sbox_table'>
					<col width='134' />
					<col width='*' />
					<col width='151' />
					<tr>
						<td class='bg_td' >
							보증금
						</td>
						<td style='border-right:0px;'>
							<ul class='Mgap_L15'>
								<li>
									- 신청서
								</li>
								<li>
									- 사업자등록증 사본, 인감증명서, 보증금 무통장입금증
								</li>
							</ul>
						</td>
						<td style='border-left:0px;'>
							<a href='idcheck.doc'><img src='../images/myservice/down_btn.gif' title='' align='absmiddle' /></a>
						</td>
					<tr>
						<td class='bg_td' style='border-bottom:solid 1px #d6d6d7;'>
							접수서류
						</td>
						<td colspan='2' style='border-bottom:solid 1px #d6d6d7;'>
							<ul class='Mgap_L15'>
								<li>
									서울시 영등포구 여의도동 14-33 한국신용정보 (우 150-871)
								</li>
								<li>
									CB사업본부 CB사업부문 E-biz사업실 김경선 과장 앞
								</li>
								<li>
									- FAX : 02-2122-4579
								</li>
								<li>
									- 문의 : 02-2122-4577~8
								</li>
								<li>
									- 우편 또는 팩스로 발송
								</li>
							</ul>
						</td>
					</tr>
				</table>
				<div class='Pgap_H10' style='color:red;'>
					※ 반드시 몰스토리 고객용 신청서를 다운 받으셔서 접수를 하셔야만 서비스를 받으실 수 있습니다.
				</div>
				<div>
					<ul class='left_listarrow'>
						<li>
							<span> 신청서를 작성하여 보내주시면 한국신용평가정보(주)에서는 신청하신 도메인에 대한 상점 ID/PW를 발급하여 이메일로 상세내역을  통보 해드립니다. </span>
						</li>
					</ul>
				</div>
				<ul class='ul_listbox01 Mgap_T20'>
					<li>
						<div>실명인증 서비스 담당자 : 몰스토리 강태웅</div>
					</li>
					<li>
						<div>연락처 : 1600-2028</div>
					</li>
					<li>
						<div>e-mail : ktw9@forbiz.co.kr </div>
					</li>
				</ul>
			</div>
			<!--신청안내 [E]-->
			<!--실명인증 [E]-->
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
$P->Navigation = "마이서비스 > 온라인식별(실명인증)";
$P->title = "온라인식별(실명인증)";
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