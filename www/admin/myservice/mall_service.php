<link href="../css/mypage.css" type="text/css" rel="stylesheet">
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


$Contents01 .= "
<link rel='stylesheet' href='../css/common2.css' type='text/css' />
<div class='center_menu'>
	<div class='sub_centes'>
		<h1><img src='../images/myservice/sub_title_sample_img01.jpg' title='' align='absmiddle'></h1>
		<ul class='texts left_listarrow'>
			<li>
			 	 <span>도메인/솔루션 변경(비즈니스형 전환)을 원하시는 경우 [변경신청버튼]을 클릭해 신청하세요.</span>
			</li>
			<li>
				 <span>솔루션 변경은 소호형을 비즈니스형으로 변경하는 경우만 가능 합니다.</span>
			</li>
		</ul>
		<table cellpadding='0' cellspacing='0' border='0' width='734' style='border-top:solid 1px #dfdfdf;border-bottom:solid 1px #dfdfdf;'>
			<!--{@ shop_row}-->
			<tr>
				<td style='padding:10px 0;'>
					<table cellpadding='0' cellspacing='0' border='0' width='734' >
						<col width='148' />
						<col width='*' />
						<tr>
							<td align='center'>
								<ul class='btns_01'>
									<li class='btns_02'>
										<img src='../images/myservice/mallstroy_img01.gif' title='' align='absmiddle' />
									</li>
									<li>
										<a href='#'><img src='../images/myservice/go_shoppiongmall_btn.gif' title='쇼핑몰가기' align='absmiddle' /></a>
									</li>
									<li>
										<a href='#'><img src='../images/myservice/go_manager_btn.gif' title='관리자가기' align='absmiddle' /></a>
									</li>
								</ul>
							</td>
							<td>
								<table cellpadding='0' cellspacing='0' border='0' width='586' class='my_shoppingMall'>
									<col width='102' />
									<col width='*' />
									<tr>
										<td>
											<span>쇼핑몰 계정</span>
										</td>
										<td>
											<strong>TEST</strong>
										</td>
									</tr>
									<tr class='gap_border'>
										<td>
											<span>도메인</span>
										</td>
										<td style='padding:10px 10px 10px 0px;'>
											<strong class='lineUp' style='vertical-align:middle;'>test.s1.mallstory.com</strong>
											<a href='#'><img src='../images/myservice/www_change_btn.gif' title='도메인변경' align='absmiddle' style='vertical-align:middle;' /></a>
										</td>
									</tr>
									<tr class='gap_border'>
										<td>
											<span>솔루션 종류</span>
										</td>
										<td>
											<strong class='lineUp' style='vertical-align:middle;'>test</strong>	<a href='#'><img src='../images/myservice/solution_change_btn.gif' title='솔루션변경' align='absmiddle' style='vertical-align:middle;' /></a>
										</td>
									</tr>
									<tr class='gap_border'>
										<td>
											<span>시작일</span>
										</td>
										<td>
											<strong>2012-05-11</strong>
										</td>
									</tr>
									<tr class='gap_border'>
										<td>
											<span>종료일</span>
										</td>
										<td>
											<strong style='vertical-align:middle;'>
											무제한</strong>
										</td>
									</tr>
									<!--tr class='gap_border'>
										<td>
											<span>변경신청</span>
										</td>
										<td>
											<span><a href='#'><img src='../images/myservice/change_ftp_pw_btn.gif' title='FTP비밀번호변경' align='absmiddle' /></a></span>
										</td>
									</tr-->
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td class='gapLine'></td></tr>

		</table>
		<div class='one_btn'>
			<a href='#'><img src='../images/myservice/plus_shoppingmall_btn.gif' title='쇼핑몰 추가신청' align='absmiddle'></a>
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
$P->Navigation = "마이서비스 > 솔루션 서비스 관리 ";
$P->title = "솔루션 서비스 관리";
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