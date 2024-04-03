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
<div class='center_menu'>
	<div class='sub_centes'>
		<div class='serviceBox'>
			<h1 style='border:0;'><img src='../images/myservice/web_h1_img01.gif' title='' align='absmiddle'></h1>
			<!--웹하드 [S]-->
			<!--웹메일/웹하드란? [S]-->
			<div class='Pgap_B20 '>
				<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/web_title01.gif' title='웹메일/웹하드 란?' align='absmiddle' /></h2>
				<ul class='bottom10_list'>
					<li>
						쉽게 생각하세요. 웹메일은 네이버나 네이트 메일과 같은 서비스이구요.<br />
						웹하드는 일정한 용량의 저장공간에 파일을 관리할 수 있는 장소입니다. <br />
						몰스토리에서는 <strong style='color:#ea4200;'>쇼핑몰 도메인을 메일 주소로 사용할 수 있는 웹메일에 웹하드까지 함께 플러스해서 서비스</strong>하고 있습니다.
					</li>
				</ul>
			</div>
			<!--웹메일/웹하드란? [E]-->
			<!--왜필요할까요? [S]-->
			<div class='Pgap_B20 '>
				<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/web_title02.gif' title='왜필요할까요?' align='absmiddle' /></h2>
				<ul>
					<li>
						일반메일을 사용하시는 것 보다, 내 쇼핑몰 도메인을 메일주소를 사용하는 것이 쇼핑몰의 신뢰도와 이미지를 향상 시킨다는 점!!<br />
						또한 <strong style='color:#ea4200;'>자사 웹하드는 각종 파일 저장과 공유가 가능</strong>해서 정말 편리해요~
					</li>
				</ul>
			</div>
			<!--웹메일/웹하드란? [E]-->
			<!--서비스특징? [S]-->
			<div class='Pgap_B20 '>
				<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/web_title03.gif' title='서비스특징' align='absmiddle' /></h2>
				<ul class='bottom10_list'>
					<li>
						<img src='../images/myservice/web_title03_list01.gif' title='회사도메인 주소 사용 가능' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/web_title03_list02.gif' title='직원 수에 상관없이 무제한 계정 생성 가능 (용량 범위 내)' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/web_title03_list03.gif' title='웹메일과 웹하드를 동시에 이용 가능' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/web_title03_list04.gif' title='POP3를 이용한 Outlook 연동 지원' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/web_title03_list05.gif' title='메일그룹(폴더)관리 기능' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/web_title03_list06.gif' title='메일주소록 관리 기능' align='absmiddle' />
					</li>
					<li>
						<img src='../images/myservice/web_title03_list07.gif' title='스팸메일 자동 분류 기능' align='absmiddle' />
					</li>
				</ul>
			</div>
			<!--서비스특징? [E]-->
			<!--웹메일/웹하드 신청 및 이용안내 [S]-->
			<div class='Pgap_B20 '>
				<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/web_title04.gif' title='웹메일/웹하드 신청 및 이용안내' align='absmiddle' /></h2>
				<ul class='bottom10_list'>
					<li>
						일반메일을 사용하시는 것 보다, 내 쇼핑몰 도메인을 메일주소를 사용하는 것이 쇼핑몰의<strong style='color:#ea4200;'> 신뢰도와 이미지를 향상</strong> 시킨다는 점!!<br />
						또한 <strong style='color:#ea4200;'>자사 웹하드는 각종 파일 저장과 공유가 가능</strong>해서 정말 편리해요~
					</li>
					<li>
						<img src='../images/myservice/web_title03_list02.gif' title='직원 수에 상관없이 무제한 계정 생성 가능 (용량 범위 내)' align='absmiddle' />
					</li>
					<li>
						* 웹메일신청시에는 <strong>정식도메인을 소유</strong>하고 계셔야 합니다. <br />
						* 쇼핑몰 관리자 페이지의 <strong>마이서비스 메뉴에서 신청</strong> 하실 수 있습니다.<br />
						* <strong>비즈니스형 솔루션</strong> 사용 고객님께는 <strong>3GB의 웹메일/웹하드 서비스를 무료로 제공</strong>해 드리고 있습니다. (신청시)<br />
						* 데모사이트 로그인 계정 <strong>아이디는 guest</strong> 이며 <strong>비밀번호는 guest</strong> 입니다. <br />
					</li>
				</ul>
			</div>
			<!--웹메일/웹하드 신청 및 이용안내 [E]-->
			<!--이용요금 [S]-->
			<div class='Pgap_B20'>
				<h2 class='under_line01 Mgap_B20'><img src='../images/myservice/web_title05.gif' title='이용요금' align='absmiddle' /></h2>
				<!--table cellpadding='0' cellspacing='0' border='0' width='734' class='Table_box Sbox_table01'>
					<col width='*' />
					<col width='123' />
					<col width='123' />
					<col width='123' />
					<col width='123' />
					<tr>
						<th class='bg_td' style='border-left:0;' colspan='2'></th>
						<th class='bg_td Pgap_H10'>
							기본형
						</th>
						<th class='bg_td'>
							경제형
						</th>
						<th class='bg_td'>
							실속형
						</th>
						<th class='bg_td' style='border-right:0;'>
							고급형
						</th>
					</tr>
					<tr>
						<td class='bg_td Pgap_H10' style='border-left:0;' colspan='2'>
							총용량 (메일 + 웹하드 + 대용량메일)
						</td>
						<td rowspan='4' style='padding:0px;'>
							<ul class='ul_box'>
								{@ etc_fetch('045389')}
								<li class='li_box1'>{.etc1}</li>
								<li class='li_box1'>{.etc2}</li>
								<li class='li_box1'>{.etc3}</li>
								<li class='li_box2'>{.etc4}</li>
								{/}
							</ul>
						</td>
						<td rowspan='4' style='padding:0px;'>
							<ul class='ul_box'>
								{@ etc_fetch('045390')}
								<li class='li_box1'>{.etc1}</li>
								<li class='li_box1'>{.etc2}</li>
								<li class='li_box1'>{.etc3}</li>
								<li class='li_box2'>{.etc4}</li>
								{/}
							</ul>
						</td>
						<td rowspan='4' style='padding:0px;'>
							<ul class='ul_box'>
								{@ etc_fetch('045391')}
								<li class='li_box1'>{.etc1}</li>
								<li class='li_box1'>{.etc2}</li>
								<li class='li_box1'>{.etc3}</li>
								<li class='li_box2'>{.etc4}</li>
								{/}
							</ul>
						</td>
						<td rowspan='4' style='padding:0px;border-right:0px;'>
							<ul class='ul_box'>
								{@ etc_fetch('045392')}
								<li class='li_box1'>{.etc1}</li>
								<li class='li_box1'>{.etc2}</li>
								<li class='li_box1'>{.etc3}</li>
								<li class='li_box2'>{.etc4}</li>
								{/}
							</ul>
						</td>
					</tr>
					<tr>
						<td class='bg_td' style='border-left:0;' colspan='2'>
							메일 용량
						</td>
					</tr>
					<tr>
						<td class='bg_td' style='border-left:0;' colspan='2'>
							웹하드 용량
						</td>
					</tr>
					<tr>
						<td class='bg_td' style='border-left:0;' colspan='2'>
							대용량첨부 용량
						</td>
					</tr>
					<!--tr>
						<td class='bg_td' style='border-left:0;' colspan='2'>
							게시판(자료실) 용량
						</td>
						<td>
							200M
						</td>
						<td>
							500M
						</td>
						<td>
							500M
						</td>
						<td style='border-right:0;'>
							1G
						</td>
					</tr-->
					<!--tr>
						<td class='bg_td' style='border-left:0;' colspan='2'>
							메일계정
						</td>
						<td colspan='4' style='border-right:0;'>
							<span style='color:red;'>용량내 무제한 생성 가능</span>
						</td>
					</tr>
					<tr>
						<td class='bg_td' style='border-left:0;' colspan='2'>
							설치비
						</td>
						<td colspan='4' style='border-right:0;'>
							<span style='color:red;'>20,000</span>원<!--  * 단, 몰스토리 솔루션 구매 고객은 무료-->
						<!--/td>
					</tr>
					<!--tr>
						<td class='bg_td' style='border-left:0;' colspan='2'>
							무료지원
						</td>
						<td colspan='4' style='text-align:left;border-right:0;'>
							<ul class='Mgap_L15'>
								<li>
									- 소호형 신규 신청시 기본형으로 무료 셋팅
								</li>
								<li>
									- 비즈형으로 변경 또는 비즈형 신규 신청시 경제형으로 무료 셋팅
								</li>
								<li>
									- 단, 모든 무료지원은 Mallstory 에서 신청한 정식 도메인을 소유하고 계셔야 합니다.
								</li>
							</ul>
						</td>
					</tr-->
					<!--tr >
						<td class='bg_td' style='border-left:0;' colspan='2'>
							<strong>30일 이용료</strong>
						</td>
						<td rowspan='3' style='padding:0px;'>
							<ul class='ul_box'>
								{@ etc_price_fetch('045389')}
								<li class='{? .index_==2}li_box2{:}li_box1{/}'><font color='red'>{=number_format(.option_price)}</font>원</li>
								{/}
							</ul>
						</td>
						<td rowspan='3' style='padding:0px;'>
							<ul class='ul_box'>
								{@ etc_price_fetch('045390')}
								<li class='{? .index_==2}li_box2{:}li_box1{/}'><font color='red'>{=number_format(.option_price)}</font>원</li>
								{/}
							</ul>
						</td>
						<td rowspan='3' style='padding:0px;'>
							<ul class='ul_box'>
								{@ etc_price_fetch('045391')}
								<li class='{? .index_==2}li_box2{:}li_box1{/}'><font color='red'>{=number_format(.option_price)}</font>원</li>
								{/}
							</ul>
						</td>
						<td rowspan='3' style='padding:0px;border-right:0px;'>
							<ul class='ul_box'>
								{@ etc_price_fetch('045392')}
								<li class='{? .index_==2}li_box2{:}li_box1{/}'><font color='red'>{=number_format(.option_price)}</font>원</li>
								{/}
							</ul>
						</td>
					</tr>
					<tr >
						<td class='bg_td' style='border-left:0;' colspan='2'>
							<strong>180일 이용료</strong>
						</td>
					</tr>
					<tr >
						<td class='bg_td' style='border-left:0;' colspan='2'>
							<strong>365일 이용료</strong>
						</td>
					</tr>
					<tr>
						<th class='bg_td' style='border-left:0;' colspan='2'>
							서비스 신청
						</th>
						<td>
							<a href='/addservice/webmail_subscribe.php?pid=045389'><img src='{templet_src}/images/common/application_btn.gif' title='신청하기' alt='신청하기' align='absmiddle' /></a>

						</td>
						<td>
							<a href='/addservice/webmail_subscribe.php?pid=045390'><img src='{templet_src}/images/common/application_btn.gif' title='신청하기' alt='신청하기' align='absmiddle' /></a>
						</td>
						<td>
							<a href='/addservice/webmail_subscribe.php?pid=045391'><img src='{templet_src}/images/common/application_btn.gif' title='신청하기' alt='신청하기' align='absmiddle' /></a>
						</td>
						<td style='border-right:0;'>
							<a href='/addservice/webmail_subscribe.php?pid=045392'><img src='{templet_src}/images/common/application_btn.gif' title='신청하기' alt='신청하기' align='absmiddle' /></a>
						</td>
					</tr>
				</table-->
				<ul style='margin-bottom:10px;'>
					<li>
						아래 이용요금은 1GB 용량 기준입니다. (VAT 포함)
					</li>
				</ul>
				<table cellpadding='0' cellspacing='0' border='0' width='100%' class='paybox'>
					<col width='33%' />
					<col width='*' />
					<col width='33%' />
					<tr>
						<td class='titlebox'>
							사용이간
						</td>
						<td class='titlebox line_rl'>
							정상가
						</td>
						<td class='titlebox'>
							할인가
						</td>
					</tr>
					<tr>
						<td>
							3개월
						</td>
						<td class='line_rl'>
							33,000원
						</td>
						<td>
							<strong>33,000원</strong>
						</td>
					</tr>
					<tr>
						<td>
							6개월
						</td>
						<td class='line_rl'>
							66,000원
						</td>
						<td>
							<strong>62,700원</strong>
						</td>
					</tr>
					<tr>
						<td>
							12개월
						</td>
						<td class='line_rl'>
							142,000원
						</td>
						<td>
							<strong>127,000원</strong>
						</td>
					</tr>
				</table>
			</div>
			<div style='text-align:center; margin-top:20px;' >
				<a href='http://mail.mallstory.com' target='_blank'><img src='../images/myservice/web_hard_demo.gif' title='' align='absmiddle' /></a>
			</div>
			<!--웹메일상품종류 [E]-->
			<!--웹하드 [E]-->
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
$P->Navigation = "운영 지원 서비스 > 웹메일 / 웹하드";
$P->title = "웹메일 / 웹하드";
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