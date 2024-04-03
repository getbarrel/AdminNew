<?
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include("../class/layout.class");
include("./goodss.lib.php");
$install_path = "../../include/";
include("SOAP/Client.php");


//$db = new Database;
//print_r($admininfo);

//$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'  ");


//echo $hostserver;

if($hostserver){
			$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
			// server.php 의 namespace 와 일치해야함
			$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

			## 한글 인자의 경우 에러가 나므로 인코딩함.



			$results = $soapclient->call("getServiceGoodsList",$params = array("company_id"=> $admininfo[company_id]),	$options);
			
			$results = (array)$results;
			
			$total = $results["total"];
			$b2b_companyinfos = $results["b2b_companyinfos"];
			//print_r($b2b_companyinfos);
			//exit;
			/*
			
			*/
			
			/*
			if($ret){
				$doc = new DOMDocument();
				$doc->loadXML(urldecode($ret));
				$doc->saveXML();
				$xpath = new DOMXpath($doc);
				//echo($doc);
				//$params = $xpath->query("*[@admin_level='*']");
				$params = $xpath->query("*");
			}
			*/
		}
//$db = new Database;

$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
	    <td align='left' colspan=9> ".GetTitleNavigation("공유서버 회원가입", "상점관리 > 공유서버 회원가입")."</td>
	</tr>
	<tr>
	    <td align='center' colspan=9><img src='./images/service_intro.jpg' border=0></td>
	</tr>
</table>";


	$mstring .= "<table width='100%' cellpadding=0 cellspacing=0 >
				<tr height=40>
					<td colspan=9 align=center style='padding-top:10px;'>
					<a href='/admin/goodss/b2b_company.php'><img type=image src='../images/".$admininfo["language"]."/btn_apply.gif' border=0></a>
					<a href='http://www.mallstory.com/customer/bbs.php?mode=list&board=qna' target=_blank><img type=image src='../images/".$admininfo["language"]."/btn_qna.gif' border=0></a>
					</td>
				</tr>
			</table>";

$Contents = $mstring;


/*
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');
$help_text .= "
<table cellpadding=1 cellspacing=0 class='small' width=100%>
	<col width=8>
	<col width=*>
	<tr>
		<td valign=top colspan=2 align=center style='padding:20px 0 20px 0;vertical-align:middle;'>
			<table>
				<tr>
					<td><b style='font-size:17px;'>".$server_name."</b> 에 </td>
					<td><a href='hostserver.act.php?chs_ix=".$chs_ix."&act=sellershop_insert' target='iframe_act'><!--target='iframe_act'--><img src='../images/".$admininfo["language"]."/co_sellershop_reg.gif' border=0></a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
";
*/

//$help_text = HelpBox("공유서버 회원가입", $help_text);
//$Contents .= $help_text;

$Script = "<script language='javascript'>
function DeleteSellerShop(company_id){
	if(confirm('등록된 판매사이트 정보를 삭제하시겠습니까? 삭제하시게 되면 관련된 정보가 모두 삭제 됩니다.')){
	//
		window.frames['iframe_act'].location.href='hostserver.act.php?act=sellershop_delete&chs_ix=".$chs_ix."&company_id='+company_id;
		//window.location.href='hostserver.act.php?act=sellershop_delete&chs_ix=".$chs_ix."&company_id='+company_id;
	}
}

function SellerRegAuth(frm){
	var check_bool = false;

	for(i=0;i < frm.co_company_id.length;i++){
			if(frm.co_company_id[i].checked){
				check_bool = true;
			}
	}

	if(!check_bool){
		alert('승인을 원하는 사이트를 한개이상 선택하셔야 합니다.');
		return false;
	}else{
		return true;
	}

}
function clearAll(frm){
		for(i=0;i < frm.co_company_id.length;i++){
			if(!frm.co_company_id[i].disabled){
				frm.co_company_id[i].checked = false;
			}
		}
}

function checkAll(frm){
    for(i=0;i < frm.co_company_id.length;i++){
    	if(!frm.co_company_id[i].disabled){
				frm.co_company_id[i].checked = true;
			}
		}
}

function fixAll(frm){
	if (!frm.company_id_all.checked){
		clearAll(frm);
		frm.company_id_all.checked = false;

	}else{
		checkAll(frm);
		frm.company_id_all.checked = true;
	}
}

</script>";
/*
$P = new AdminPage();
$P->addScript = $Script;
$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
echo $P->AdminFrame();
*/
$P = new LayOut;
$P->addScript = "$Script";
$P->strLeftMenu = goodss_menu();
$P->Navigation = "도매아이템 > 서비스소개";
$P->title = "서비스소개";
$P->strContents = $Contents;
$P->PrintLayOut();


?>