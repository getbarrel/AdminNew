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
<div style='width:740px; padding-left:40px; padding-top:40px;'>
			<div class='under_line01' style='clear:both;position:relative; padding-bottom:30px;'>
				<h2 class='Mgap_H10'><img src='../images/myservice/deepzoom_img01.jpg' title='딥줌기능' align='absmiddle' /></h2>
				<p style='position:absolute; top:230px; left:70px;'><a href='http://dev.forbiz.co.kr/shop/goods_view.php?id=0000045318'><img src='../images/myservice/experience_btn01.gif' title='체험해보기' align='absmiddle' /></a></p>
			</div>
			<!--딥줌기능? [E]-->
			<!--딥줌소개 [s]-->
			<div class=' under_line01' style='width:734px;'>
				<h2 style='padding:20px 0;'><img src='../images/myservice/deepzoom_title01.gif' title='딥줌소개' /></h2>
				<div>
					<img src='../images/myservice/deepzoom_img02.jpg' title='딥줌소개' />
				</div>
				<div style='padding:40px 0 15px 0;'>
					<img src='../images/myservice/deepzoom_text02.gif' title='실올 하나 하나, 상품의 미세 입자까지도 보여드립니다.!! 사고 싶고, 가지고 싶게 만드는 이미지 보기의 혁명!!' />
				</div>
				<ul style='line-height:170%;'>
					<li>
						고객이 원하는 상품의 디테일 하나하나! 고객이 보고 싶어하는 상품의 자세하고 또렷한 이미지!
					</li>
					<li>
						까다로운 고객까지도 감탄한 단 하나의 비밀! 몰스토리 딥줌!  딥줌이 구매를 망설이는 고객을 확실하게 붙잡아 드립니다.

					</li>
				</ul>
				<div>
					<p style='padding:40px 0 5px 0;'>
						* 실제 적용 예
					</p>
					<div>
						<img src='../images/myservice/deepzoom_img03.jpg' title='딥줌소개' />
					</div>
				</div>
			</div>
			<!--딥줌소개 [e]-->
			<!--딥줌특징 [S]-->
			<div class='Pgap_B50 under_line01'>
				<h2 style='padding:20px 0;'><img src='../images/myservice/deepzoom_title02.gif' title='딥줌특징' /></h2>
				<table cellpadding='0' cellspacing='0' border='0' width='734'>
					<col width='50'>
					<col width='*'>
					<col width='115'>
					<tr>
						<td valign='top'>
							<img src='../images/myservice/img01.gif' title='' align='absmiddle' class='Mgap_H10'>
						</td>
						<td colspan='2'>
							<h4>딥줌이 강력한 이유 3가지!!</h4>
							<div class='text_list'>
								<div style='color:#ea4200;'>
									<strong>하나 , 몰스토리 자체기술로 개발된 장착형 서비스 입니다. </strong>
								</div>
								<div style='margin-left:40px;padding:12px 0 20px 0;'>
									설치형이 아닙니다. 자체 시스템으로 구축되어 있습니다. <br>
									쇼핑몰 솔루션 따로, 상세보기따로 구매하실 필요 없습니다.
								</div>
								<div style='color:#ea4200;'>
									<strong>둘 ,    사용하기 쉽고 편리합니다!</strong>
								</div>
								<div style='margin-left:40px;padding:12px 0 20px 0;'>
									 <span style='vertical-align:middle;'>- 상품등록시</span><img src='../images/myservice/deepzoom_img01.gif' title='' align='absmiddle'  style='vertical-align:middle;'/> <span style='vertical-align:middle;'>원클릭이면 끝</span><br>
									- 마우스 스크롤을 이용하여 편리한 확대 축소 가능 합니다!
								</div>
								<div style='color:#ea4200;'>
									<strong>셋 ,    국내 최저 가격 입니다!</strong>
								</div>
								<div style='margin-left:40px;padding:12px 0 20px 0;'>
									유사 프로그램의 가격대비 성능을 비교해 보세요! <br>
									비즈니스형은 기본으로 장착해 드립니다.
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td valign='top'>
							<img src='../images/myservice/img02.gif' title='' align='absmiddle' class='Mgap_H10'>
						</td>
						<td>
							<h4>적용기술</h4>
							<div style='color:#ea4200;line-height:150%;'>
								<strong>국내최초 Microsoft Silverlight를 응용한 tiling(타일링)기술로 구현된 딥줌으로 <br />오직 몰스토리에서만 만나보실 수 있습니다.</strong>
							</div>
							<div class='text_list' style='padding-top:13px;'>
								기존 고해상도 이미지 확대서비스와 달리 원본 크기에 상관없이 사용자가 사진을 보는 만큼만 <br />
								데이터를 전송하여 트래픽을 획기적으로 줄여주어 고해상도의 사진도 빠르고 부드럽게 <br />
								감상하실 수 있습니다.
							</div>
						</td>
						<td>
							<img src='../images/myservice/mic_title.gif' title='' style='margin-bottom:20px;'/>
							<img src='../images/myservice/silver_title.gif' title='' />
						</td>
					</tr>
				</table>
			</div>
			<!--딥줌특징 [E]-->
			<div class='under_line01' style=''>
				<h2 style='padding:20px 0;'><img src='../images/myservice/deepzoom_title03.gif' title='딥줌서비스가격' /></h2>
				<ul class='left_listarrow'>
					<li>
						<span><strong style='margin-right:40px;'>소호형</strong> &nbsp;: <strong style='color:#ea4200;'>유료</strong></span>
					</li>
					<li>
						<span><strong style='margin-right:14px;'>비지니스형</strong>  &nbsp;: <strong style='color:#ea4200;'>무료 300M 제공</strong> (추가비용 = 10,000원/100M)</span>
					</li>
					<li>
						<span><strong>일시불 결제고객 할인 정책</strong> (예. 300M 결제시)</span>
					</li>
				</ul>
				<p style='text-align:right;width:539px;padding-bottom:5px;'>(VAT 포함가)</p>
				<table cellpadding='0' cellspacing='0' border='0' width='524' class='deepzoom_pay' style='margin-bottom:40px;border-top:solid 1px #e0e0e0;margin-left:15px;' >
					<col width='33%' />
					<col width='*' />
					<col width='33%' />
					<tr>
						<td align='left' class='Pgap_H10'>
							<div class='Mgap_L10'>
								6개월 결제 (5%)
							</div>
						</td>
						<th align='center' style='border-left:solid 1px #e0e0e0;border-right:solid 1px #e0e0e0;'>
							<s>198,000원</s>
						</th>
						<th style='color:#ea4200;' align='center'>
							188,000원
						</th>
					</tr>
					<tr>
						<td align='left' class='Pgap_H10'>
							<div class='Mgap_L10'>
								12개월 결제 (10%)
							</div>
						</td>
						<th align='center' style='border-left:solid 1px #e0e0e0;border-right:solid 1px #e0e0e0;'>
							<s>396,000원</s>
						</th>
						<th style='color:#ea4200;' align='center'>
							356,000원
						</th>
					</tr>
				</table>
			</div>
			<div class='under_line01' style='text-align:center;padding:40px 0;'>
				<table cellpadding='0' cellspacing='0' border='0' width='100%'>
					<col width='*' />
					<col width='160' />
					<tr>
						<td>
							<img src='../images/myservice/deepzoom_text01.gif' title='‘구글맵스’와 같은 임팩트를 쇼핑몰로 가져다 써보세요!!'/>
						</td>
						<td>
							<a href='http://dev.forbiz.co.kr/shop/goods_view.php?id=0000045318'><img src='../images/myservice/experience_btn01.gif' title='체험해보기' /></a>
						</td>
					</tr>
				</table>
			</div>
		</div>
			<!--딥줌기능 [E]-->
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
$P->Navigation = "운영 지원 서비스 > 딥줌서비스";
$P->title = "딥줌서비스";
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