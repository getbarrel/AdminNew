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
			<div class='' style='padding:30px 0;'><img src='../images/myservice/business_img01.jpg' title='' /></div>
			<!-- 몰스토리업무관리소개 [S]-->
			<div class='Pgap_B50' style='width:734px;margin-top:20px;'>
				<h1><img src='../images/myservice/business_title01.gif' title='몰스토리업무관리소개' title='몰스토리업무관리소개' /></h1>
				<div style='padding-bottom:15px;' >
					<img src='../images/myservice/business_title01_01.gif' title='쇼핑몰도 기업입니다.' alt='쇼핑몰도 기업입니다.' />
				</div>
				<div style='line-height:160%;'>
					회사를 운영하다보면 직원도 생기고 관리해야할 일정이나 업무가 많아지게 됩니다.<br />
					몰스토리 ‘업무관리’는 쇼핑몰을 운영하시면서 편리하게 사용하실 수 있는 핵심 기능들을 제공하는 메뉴입니다.<br /><br />

					업무관리를 활용하시면 각종 일정을 스케쥴(캘린더)로 관리하시면서, 구글 캘린더와 연동되어 모바일로도 언제 어디서나 일정을<br /> 놓치지 않고 관리하실 수 있습니다.<br /><br />

					쇼핑몰 운영상 필요한 업무들을 등록하고 관리히실 수 있습니다.<br /><br />
				</div>
			</div>
			<!-- 몰스토리업무관리소개 [E]-->
			<!-- 몰스토리업무관리특징 [S]-->
			<div class='Pgap_B50' style='width:734px;margin-top:20px;'>
				<h1><img src='../images/myservice/business_title02.gif' title='몰스토리업무관리특징' title='몰스토리업무관리특징' /></h1>
				<div style='padding-bottom:15px;' >
					<img src='../images/myservice/business_title02_01.gif' title='쉽고 편한UI' alt='쉽고 편한UI' />
				</div>
				<p style='padding-bottom:20px;'>
					몰스토리 업무관리는 Task 관리 및 협업에 최적화 되어 있습니다.
				</p>
				<ul class='biz_list02'>
					<li>
						<img src='../images/myservice/business_title02_01_list01.gif' title='스케쥴 캘린더 관리 (구글과 연동되어 모바일과 연동)' alt='스케쥴 캘린더 관리 (구글과 연동되어 모바일과 연동)'  />
					</li>
					<li>
						<img src='../images/myservice/business_title02_01_list02.gif' title='업무 및 프로젝트 처리상태를 한눈에 파악할 수 있는 대쉬보드 및 간트 챠트보기' alt='업무 및 프로젝트 처리상태를 한눈에 파악할 수 있는 대쉬보드 및 간트 챠트보기'  />
					</li>
					<li>
						<img src='../images/myservice/business_title02_01_list03.gif' title='회사의 비전과 목표를 전 직원이 공유 할 수 있는 목표 공유 보드' alt='회사의 비전과 목표를 전 직원이 공유 할 수 있는 목표 공유 보드'  />
					</li>
					<li>
						<img src='../images/myservice/business_title02_01_list04.gif' title='메모를 Drag&Drop 한번으로 업무 및 스케줄로 간편하게 등록 가능한  “한줄메모”기능' alt='메모를 Drag&Drop 한번으로 업무 및 스케줄로 간편하게 등록 가능한  “한줄메모”기능'  />
					</li>
					<li>
						<img src='../images/myservice/business_title02_01_list05.gif' title='보고서 자동생성 출력' alt='보고서 자동생성 출력'  />
					</li>
				</ul>
				<p style='padding-bottom:20px;'>
					기본기능에 충실하면서도 보다 쉽고 간편하게 사용할 수 있도록 구성 하였습니다.
				</p>
				<div style='padding:20px 0 15px 0;' >
					<img src='../images/myservice/business_title02_02.gif' title='가치를 제공하는 애플리케이션' alt='가치를 제공하는 애플리케이션' />
				</div>
				<div style='line-height:160%;'>
					<ul class='biz_list02'>
						<li>
							<img src='../images/myservice/business_title02_02_list01.gif' title='조직에 부여하는 가치' alt='조직에 부여하는 가치'  />
						</li>
						<li>
							<img src='../images/myservice/business_title02_02_list02.gif' title='리더에 부여하는 가치' alt='리더에 부여하는 가치'  />
						</li>
						<li>
							<img src='../images/myservice/business_title02_02_list03.gif' title='구성원에 부여하는 가치' alt='구성원에 부여하는 가치'  />
						</li>
					</ul>
				</div>
			</div>
			<!-- 몰스토리업무관리특징 [E]-->
			<!-- 몰스토리업무관리주요기능 [S]-->
			<div class='' style='width:734px;margin-top:20px;'>
				<h1><img src='../images/myservice/business_title03.gif' title='몰스토리업무관리주요기능' title='몰스토리업무관리주요기능' /></h1>
				<div style='padding-bottom:15px;' >
					<img src='../images/myservice/business_title03_list.gif' title='' alt='' />
				</div>
			</div>
			<!-- 몰스토리업무관리주요기능 [S]-->
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
$P->Navigation = "추가 소프트웨어 > 업무관리";
$P->title = "업무관리";
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