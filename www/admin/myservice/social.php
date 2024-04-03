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
<div style='width:730px; padding-left:50px;'>
			<!--무통장입금내역조회서비스란 [S]-->
				<div class='under_line01' style='clear:both;'>
					<h2 class='Mgap_H10'><img src='../images/myservice/soho_sosial_title01_img02.gif' title='소셜커머스1.0' align='absmiddle' /></h2>
				</div>
				<!--무통장입금내역조회서비스란? [E]-->
				<!--소셜커머스란? [S]-->
				<div class='under_line01' style='width:734px; padding:30px 0; line-height:170%;'>
					<h2 style='padding:20px 0;'><img src='../images/myservice/sosial_title01.gif' title='소셜커머스란?' align='absmiddle' /></h2>
					<div style='' class='text_list'>
						2010년을 온라인 사이트를 강타한 새로운 유통시장 소셜커머스.. 트위터나 페이스북 등 소셜네트워크 서비스 기반으로 오프라인 상품을
						온라인의 쿠폰화하여 판매를 하면서 빅히트를 친 쿠폰 쇼핑몰을 말합니다. 즉 50% 할인된 가격으로 목표 판매 수량을 도달하면 할인쿠폰을
						구매할 수 있도록한 방식으로 스마트폰의 등장으로 더욱더 빠른 성장을 하게된 하나의 쇼핑몰입니다.
					</div>
					<div class='text_list'>
						소셜커머스의 장점으로는 고객이 고객에제 전달한다는 입소문마케팅 즉 바이럴마케팅을 통해 더욱더 확대되어 갔으며, SNS의 기반으로
						전세계적으로 온라인 유통에 새로운 시장을 열었다고 해도 과언이 아닙니다.
					</div>
					<div class='text_list'>
						하지만 대기업 및 선두업체의 막대한 투자로 인해 소자본 창업자들은 자리가 자라지고 있는 실정이지만, <strong>몰스토리에서는 복잡한 쿠폰
						기능들은 현재 과감하게 삭제하고 쇼핑몰 운영을 하시면서 마케팅의 방법으로 소셜커머스를 간단하게 사용할 수 있도록 설계</strong>
						하였습니다. 마케팅 방법으로 한번 활용해보세요.
					</div>
				</div>
				<!--소셜커머스란? [E]-->
				<!--왜필요할까요? [S]-->
				<div class='under_line01' style='padding:30px 0; line-height:170%;'>
					<h2 style='padding:20px 0;'><img src='../images/myservice/online_service_title06.gif' title='왜필요할까요?' align='absmiddle' /></h2>
					<div class='text_list'>
						뭉치면 싸지는 원리로 서로에게 상품을 홍보하는 –소셜커머스-
					</div>
					<div class='text_list'>
						몰스토리의 소셜커머스는 쿠폰 쇼핑몰 기능보다는 배송방식의 소셜커머스 기능 위주로 만들어져 쇼핑몰 운영자들이게 <strong>하나의 마케팅
						수단</strong>으로 사용할 수 있도록 하였으며, 독립적으로 사용을 원하는 분들도 사용할 수 있도록 하였습니다.
						(현 1.0버전에서 2개월안 더욱 강력한 모델이 무료로 업그레이드 될 예정)
					</div>
					<div class='text_list'>
						쇼핑몰을 운영하다 보면 고객에 니즈에 맞는 프로모션을 한다는 것은 정말 어려운 일입니다. 쇼핑몰에서 가장 많이 사용하는 프로모션이
						뭘까요? 그건 <strong>기획전, 세일전 등 처럼 가격할인</strong>을 통한 매출의 극대화를 할 수 있는 과거에도 그랬고 현재에도 가장 보편화된 마케팅
						전략이기도 합니다. 이런 방식을 이제 새롭게 몰스토리 소셜커머스 기능을 통해 간편하게 상품을 등록하고 판매할 수 있게 설계되어
						소비자들에게 공동구매 방식의 최저가 할인 이벤트와 상품체험을 이벤트 등을 자유롭게 프모모션을 진행할 수 있을 것입니다.
					</div>
					<div class='text_list'>
						또한 쇼핑몰 운영중인 중견기업들 중에서도 새로운 수익사업 혹은 마케팅이 없을까 하지만 새로운 사업을 시작한다는 것이 쉬운 일은
						아니죠! 더욱이 또 하나의 사이트는 오픈한다는 것 또한 생각보다 쉽지는 않습니다.
						하지만 몰스토리 소호형(무료형), 비즈형에서는 자동으로 소셜커머스가 생성되어 온라인 + 오프라인이 결합된 새로운 서비스 사업도
						진행할 수 있을 것이며, <strong>쇼핑몰과 통합관리되기 때문에 효율적으로 관리</strong>할 수 있습니다.
					</div>
					<div>
						<p class='orang_point' style='padding:10px 0;'><strong>기본기능</strong></p>
						<ul class='G_point01'>
							<li>
								<strong>대규모트래픽 유입 안정된 시스템제공</strong>
							</li>
							<li>
								<strong>배송상품 공동구매 기능</strong>
							</li>
							<li>
								<strong>쿠폰상품 쿠폰발급 기능</strong>
							</li>
							<li>
								<strong>SNS 홍보 기능</strong>
							</li>
							<li>
								<strong>몰스토리 쇼핑몰 전체 기능</strong>
							</li>
						</ul>
					</div>
				</div>
				<!--왜필요할까요? [E]-->

				<!--서비스특징 [S]-->
				<div class='Pgap_B50 under_line01' style='padding:30px 0; line-height:170%;'>
					<h2 style='padding:20px 0;'><img src='../images/myservice/web_hard_title03.gif' title='서비스특징?' align='absmiddle'></h2>
					<table cellpadding='0' cellspacing='0' border='0' width='734'>
						<col width='50' />
						<col width='*' />
						<tr>
							<td valign='top'>
								<img src='../images/myservice/img01.gif' title='' align='absmiddle' class='Mgap_H10'>
							</td>
							<td>
								<h4>쇼핑몰 회원과 소셜커머스 회원의 공유</h4>
								<div class='text_list'>
									기존의 쇼핑몰 회원의 정보를 공유하여 설계되어 있고, 모든 배송,정산 등의 관리기능이 통합되어 있어 별도로 새롭게 구성하여 회원을 모집 관리할 불필요함을 효율적인 관리시스템으로 구축하였습니다. <br />
									새로운 URL이 필요 없음
								</div>
							</td>
						</tr>
						<tr>
							<td valign='top'>
								<img src='../images/myservice/img02.gif' title='' align='absmiddle' class='Mgap_H10'>
							</td>
							<td>
								<h4>간편한 URL 생성</h4>
								<div class='text_list'>
									10분이면 모두 뚝딱!! 편리한 기능<br />
									몰스토리 솔루션을 활용하여 쇼핑몰을 운영하고 있는 고객이라면 쇼핑몰의  URL에   + /SNS 추가만한다면 나만의 소셜커머스를 뚝딱~~ 만들수 있어, 소셜커머스를 위해 추가 도메인을 구입할 필요가 없습니다.
								</div>
							</td>
						</tr>
						<tr>
							<td valign='top'>
								<img src='../images/myservice/img03.gif' title='' align='absmiddle' class='Mgap_H10'>
							</td>
							<td>
								<h4>무료 스킨제공</h4>
								<div class='text_list'>
									소셜커머스 사용자를 위해 깔끔한 기본스킨을 제공하고 있으며, 관리방법 또한 기본 쇼핑몰관리와 동일하기 때문에 소셜커머스를 따로 교육받아 사용할 필요 없이 현재 사용하고 있는 관리시스템대로 관리하면 OK
								</div>
							</td>
						</tr>
						<tr>
							<td valign='top'>
								<img src='../images/myservice/img04.gif' title='' align='absmiddle' class='Mgap_H10'>
							</td>
							<td>
								<h4>무료 PG 등록</h4>
								<div class='text_list'>
									재 계약이 필요 없는 PG사 계약, 소셜커머스 한방에 해결<br />
									보기에는 소셜커머스 이지만 몰스토리는 배송상품을 위주의 구축되어 공동구매와 같기 때문에 몰스토리 사용하는 사용자께서는 쇼핑몰 PG 계약시 자동 소셜커머스 결제모듈과 연동이 되기때문에 따로 다시 계약을 하실 필요가 없습니다.<br />
									(단, 쿠폰발행만을 위한 소셜커머스를 독립적으로 운영하기 위해서는 상기 내용과 다를 수 있음)
								</div>
							</td>
						</tr>
					</table>
				</div>
				<!--서비스특징 [E]-->
				<!--몰스토리만의 소셜커머스 기능 [S]-->
				<div class='Pgap_B50 under_line01' style='padding:30px 0; line-height:170%;'>
					<h2 style='padding:20px 0;'><img src='../images/myservice/sosial_title02.gif' title='몰스토리만의 소셜커머스 기능' align='absmiddle'></h2>
					<table cellpadding='0' cellspacing='0' border='0' width='734'>
						<col width='50' />
						<col width='*' />
						<tr>
							<td valign='top'>
								<img src='../images/myservice/img01.gif' title='' align='absmiddle' class='Mgap_H10'>
							</td>
							<td>
								<h4>구매자 쿠폰장터 기능</h4>
								<div class='text_list'>
									구매자가 쿠폰을 구매 후 사용하지 못할 경우 자신의 쿠폰을 타인에게 판매할 수 있도록한 기능입니다. 이때 구매가가 쿠매한 쿠폰과 수량만을 거래할 수 있도록 설계하였습니다.
								</div>
							</td>
						</tr>
						<tr>
							<td valign='top'>
								<img src='../images/myservice/img02.gif' title='' align='absmiddle' class='Mgap_H10'>
							</td>
							<td>
								<h4>상품리뷰,체험리뷰을 위한 인증샷 기능</h4>
								<div class='text_list'>
									상품구매후기를 등록할 수 있는 기능으로 구매자가 구매한 상품의 평점을 주고 리뷰할 수 있도록 설계되었으며, 관리자가 마케팅에 활용할 수 있도록 메인화면에 전시할 수 있도록 설계되었으며 포인트등을 제공하여 활성화에 도움을 줄 수 있도록한 기능입니다.
								</div>
							</td>
						</tr>
						<tr>
							<td valign='top'>
								<img src='../images/myservice/img03.gif' title='' align='absmiddle' class='Mgap_H10'>
							</td>
							<td>
								<h4>쇼핑몰 내 원클릭 전시 기능(스페셜 쿠폰 관리 기능)</h4>
								<div class='text_list'>
									메인 전시 프로모션의 일종이며 일정상품을 메인화면의 우측에 원클릭 관리방식으로 자동 노출할 수 있도록 하여 관리자의 편리성과 상품의 매출액을 높이기 위한 마케팅적 기능입니다.
								</div>
							</td>
						</tr>
						<tr>
							<td valign='top'>
								<img src='../images/myservice/img04.gif' title='' align='absmiddle' class='Mgap_H10'>
							</td>
							<td>
								<h4>무료쿠폰 기능(일반 오프라인 전단지 쿠폰을 문자로 발급 가능한 기능)</h4>
								<div class='text_list'>
									일반업체, 제휴업체 등의 홍보를 할 수 있도록 새롭게 시도한 몰스토리만의 기능입니다. 소자본창업자들의 소셜커머스의 수익모델의 어려움이 많아 몰스토리 마케팅팀에서 소셜커머스의 영업을 바탕으로 새롭게 만들어진 기능이며, 홍보마케팅, 제휴마케팅, 광고 수익사업등으로 활용할 수 있는 기능입니다.
								</div>
							</td>
						</tr>
						<tr>
							<td valign='top'>
								<img src='../images/myservice/img05.gif' title='' align='absmiddle' class='Mgap_H10'>
							</td>
							<td>
								<h4>업데이트된 기능 무료 제공</h4>
								<div class='text_list'>
									현재 몰스토리의 소셜커머스 기능은 배송위주로 만들어졌으며, 지속적인 기능 업데이트로 현재 소셜커머스의 일반 기능들보다 월등한 기능들과 판매자들를 위한 다양한 지속적으로 무료 업로드 제공할 예정입니다.
								</div>
							</td>
						</tr>
					</table>
				</div>
				<!--몰스토리만의 소셜커머스 기능 [E]-->
			<!--서비스특징 [E]-->
		</div>
			<!--견적센터 [E]-->

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
$P->Navigation = "운영 지원 서비스 > 소셜 커머스";
$P->title = "소셜 커머스";
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