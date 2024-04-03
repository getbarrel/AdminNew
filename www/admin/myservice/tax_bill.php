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
<div class='center_menu'>
	<div class='sub_centes'>
		<div class='serviceBox'>
			<h1><img src='../images/myservice/webtax_img01.jpg' title='' align='absmiddle'></h1>
			<!--전자세금계산서 [S]-->
			<!--전자세금계산서란 [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/webtax_title01.gif' title='전자세금계산서란?' align='absmiddle' /></h2>
				<ul>
					<li>
						인터넷으로 공인인증시스템을 거쳐 신원확인 되면 인터넷으로 계산서를 받을 수 있는 서비스입니다. 공인인증서를 통한 전자서명으로<br /> 종이세금계산서와 동일한 법적효력을 갖게 됩니다.
					</li>
				</ul>
				<div>
					<img src='../images/myservice/webtax_img02.gif' title='?' align='absmiddle' />
				</div>
				<ul>
					<li>
						<h4 class='orange01'><img src='../images/myservice/list_arrow_icon.jpg' title='' align='absmiddle' />  <span style='vertical-align:middle;'>2010년 1월 1일 부터 국세청에서 전자세금계산서 제도 시행</span></h4>
						<div class='orange_list'>
							<ul class='ul_listbox01'>
								<li>
									<div>
									법인사업자는 2010년에는 전자세금계산서 또는 종이세금계산서 발행이 가능하고, 2011년부터는 전자세금계산서 의무발행 대상자로 가산세가 부과됩니다.
									</div>
								<li>
							</ul>
						</div>
					</li>
					<li>
						<h4 class='orange01'><img src='../images/myservice/list_arrow_icon.jpg' title='' align='absmiddle' /> <span style='vertical-align:middle;'>2011년부터 법인사업자의 <span style='color:#ea4200;'>전자세금계산서 발행 의무화</span></span></h4>
						<div class='orange_list'>
							<ul class='ul_listbox01'>
								<li>
									<div>법인사업자는 전자적 방법으로 세금계산서(전자세금계산서)를 교부하여야 하며, 이 전자세금계산서 정보를 국세청으로 전송하여야 함</div>
								</li>
								<li>
									<div>전자세금계산서 미발행 시 누락된 매출의 가산세가 부과됨</div>
								</li>
								<li>
									<div>전자세금계산서 전송분에 대해서는 세금계산서합계표 명세 제출 및 세금계산서 보관의무 면제</div>
								</li>
								<li>
									<div>전자세금계산서 발행(교부) 건당 100원의 세액공제(연간한도 100만원) 혜택 부여</div>
								</li>
								<li>
									<div>개인사업자도 전자세금계산서 발행 시 동일한 혜택 부여 </div>
								</li>
							</ul>
						</div>
					</li>
				</ul>
			</div>
			<!--전자세금계산서란? [E]-->
			<!--왜필요할까요? [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2><img src='../images/myservice/online_service_title06.gif' title='왜필요할까요?' align='absmiddle' /></h2>
				<ul>
					<li>
						<h3 class='title_O1'><span>시간단축</span></h3>
						<div class='orange_list'>
							발행 즉시 전송되어 세금계산서 전달업무가 빠르게 처리됩니다.
						</div>
					</li>
					<li>
						<h3 class='title_O1'><span>비용절감</span></h3>
						<div class='orange_list'>
							세금계산서의 전산화로 처리비용이 줄어듭니다.
						</div>
					</li>
					<li>
						<h3 class='title_O1'><span>보관관리</span></h3>
						<div class='orange_list'>
							부가가치세법에서 정한 보존기간 5년 동안 안전하게 보관되므로 편리하게 검색하고 관리하실 수 있습니다.
						</div>
					</li>
					<li>
						<h3 class='title_O1'><span>상태확인</span></h3>
						<div class='orange_list'>
							발송한 세금계산서가 잘 도착했는지 많이 궁금하셨죠? 이젠, 세금계산서가 어느 단계에 있는지 쉽게 확인하실 수 있습니다.
						</div>
					</li>
					<li>
						<h3 class='title_O1'><span>대량처리</span></h3>
						<div class='orange_list'>
							여러 장의 세금계산서 자료를 엑셀로 작성하여 단 한번의 전자서명으로 빠르게 전달됩니다.
						</div>
					</li>
					<li>
						<h3 class='title_O1'><span>조회</span></h3>
						<div class='orange_list'>
							거래처, 기간, 문서상태 등 다양한 조건으로 검색할 수 있습니다.
						</div>
					</li>
					<li>
						<h3 class='title_O1'><span>출력</span></h3>
						<div class='orange_list'>
							별도의 전용지가 필요 없이, A4용지에 세금계산서를 동일하게 출력할 수 있습니다.
						</div>
					</li>
					<li>
						<h3 class='title_O1'><span>업로드</span></h3>
						<div class='orange_list'>
							다량의 문서나 거래처 등을 파일로 한번에 등록할 수 있어 편리합니다.
						</div>
					</li>
					<li>
						<h3 class='title_O1'><span>거래처관리</span></h3>
						<div class='orange_list'>
							거래처 홍보를 쉽게 할 수 있고 공급받는 거래처는 별도의 가입절차 없이 세금계산서를 메일로 받을 수 있습니다.
						</div>
					</li>
				</ul>
			</div>
			<!--왜필요할까요? [E]-->
			<!--전자세금계산서 [E]-->
			<div class='Mgap_H20' style='text-align:right;'>
				<a href='/customer/bbs.php?board=qna'><img src='../images/myservice/ask_btn_my.gif' title='문의하기' align='absmiddle' /></a>
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
$P->Navigation = "마이서비스 > 전자세금계산서(바로빌)";
$P->title = "전자세금계산서(바로빌)";
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