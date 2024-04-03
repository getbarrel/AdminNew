<?
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include("../class/layout.class");
include("./co_goods.lib.php");
$install_path = "../../include/";
include("SOAP/Client.php");


//$db = new Database;
//print_r($admininfo);

//$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'  ");


//echo $hostserver;

if($hostserver){
			$soapclient = new SOAP_Client("http://".$hostserver."/admin/cogoods/api/");
			// server.php 의 namespace 와 일치해야함
			$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

			## 한글 인자의 경우 에러가 나므로 인코딩함.



			$sellersinfo = $soapclient->call("getSellerInfo",$params = array("company_id"=> $admininfo[company_id]),	$options);
			$sellersinfo = (array)$sellersinfo;

			$sellersinfo = (array)$sellersinfo;
			$total = $sellersinfo["total"];
			$apply_sellers = $sellersinfo["sellers"];
			//print_r($sellersinfo);
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
	    <td align='left' colspan=9 style='padding-bottom:10px;'> ".getHostServer($chs_ix)."</td>
	</tr>

	<form name='co_shoplist_frm' action='hostserver.act.php' method=post onsubmit='return SellerRegAuth(this);' target='iframe_act'>
	<input type=hidden name=act value=seller_reg_auth>
	<input type=hidden name=chs_ix value='".$chs_ix."'>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
	<col width=3%>
	<col width=10%>
	<col width=15%>
	<col width=7%>
	<col width=15%>
	<col width=10%>
	<col width=20%>
	<col width=10%>
	<col width=10%>

	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'><input type=checkbox name='company_id_all' id='co_company_id' value='' onclick='fixAll(this.form)'></td>
		<td class='m_td'>상점명</td>
		<td class='m_td'>도메인</td>
		<td class='m_td'>대표자명</td>
		<td class='m_td'>대표전화</td>
		<td class='m_td'>팩스</td>
		<td class='m_td'>이메일</td>
		<td class='m_td'>가입상태</td>
		<td class='e_td'>관리</td>
		</tr>";
if($apply_sellers) {
	if(count($apply_sellers) > 0){

		for($i=0;$i < count($apply_sellers);$i++){
			$apply_sellers[$i] = (array)$apply_sellers[$i];
			//$apply_sellers[$i][apply_status] == "AU"
			if($apply_sellers[$i][seller_auth] == "N"){
				$seller_auth = "승인대기";
			}else if($apply_sellers[$i][seller_auth] == "Y"){
				$seller_auth = "승인";
			}else if($apply_sellers[$i][seller_auth] == "X"){
				$seller_auth = "승인취소";
			}
			$mstring .="<tr height=32 align=center>
						<td><input type=checkbox name='co_company_id[]' id='co_company_id' value='".$apply_sellers[$i][company_id]."' title='".$apply_sellers[$i][company_id]."'  ".($apply_sellers[$i][company_id] == $admininfo[company_id] ? "disabled":"")."></td>
						<td><a href='company.add.php?company_id=".$apply_sellers[$i][company_id]."'>".$apply_sellers[$i][shop_name]."</a></td>
						<td>".$apply_sellers[$i][shop_url]."</td>
						<td>".$apply_sellers[$i][com_ceo]."</td>
						<td>".$apply_sellers[$i][com_phone]."</td>
						<td>".$apply_sellers[$i][com_fax]."</td>
						<td>".$apply_sellers[$i][com_email]."</td>
						<td>".$seller_auth."</td>
						<td align=center nowrap>";
				if($apply_sellers[$i][company_id] == $admininfo[company_id]){
				$mstring .="
					<!--a href='/admin/store/basicinfo.php?company_id=".$apply_sellers[$i][company_id]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a-->
					<a href=\"JavaScript:DeleteSellerShop('".$apply_sellers[$i][company_id]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}

			$mstring .="
						</td>
					</tr>";
		}
	}
}else{
	$mstring .= "<tr height=100><td colspan=9 align=center style='padding-top:10px;'>등록된 판매사이트가 없습니다.</td></tr>
							";
}
/*
if(is_object($params)){
	foreach ($params as $param) {
	//for($i=0;$i<$db->total;$i++){
	//	$db->fetch($i);

		$company_id = $param->getElementsByTagName("company_id")->item(0)->nodeValue;
		$com_name = $param->getElementsByTagName("com_name")->item(0)->nodeValue;
		$shop_url = $param->getElementsByTagName("shop_url")->item(0)->nodeValue;
		$ceo = $param->getElementsByTagName("com_ceo")->item(0)->nodeValue;

		$phone = $param->getElementsByTagName("com_phone")->item(0)->nodeValue;
		$fax = $param->getElementsByTagName("com_fax")->item(0)->nodeValue;
		$com_email = $param->getElementsByTagName("com_email")->item(0)->nodeValue;

		$shop_name = $param->getElementsByTagName("shop_name")->item(0)->nodeValue;
		$shop_desc = $param->getElementsByTagName("shop_desc")->item(0)->nodeValue;


		$mstring .="<tr height=32 align=center>
					<td><input type=checkbox name='co_company_id[]' id='co_company_id' value='".$company_id."' title='".$company_id."' ".($company_id == $admininfo[company_id] ? "disabled":"")."></td>
					<td><a href='company.add.php?company_id=".$company_id."'>".$shop_name."</a></td>
					<td>".$shop_url."</td>
					<td>".$ceo."</td>
					<td>".$phone."</td>
					<td>".$fax."</td>
					<td>".$com_email."</td>

					<td align=right nowrap>";
				if($company_id == $admininfo[company_id]){
				$mstring .="
					<a href='/admin/store/basicinfo.php?company_id=".$company_id."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
					<a href=\"JavaScript:DeleteSellerShop('".$company_id."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}

			$mstring .="
					</td>
				</tr>
				<tr hegiht=1><td colspan=8 class='dot-x'></td></tr>";
	}
}else{
	$mstring .= "<tr height=100><td colspan=8 align=center style='padding-top:10px;'>등록된 판매사이트가 없습니다.</td></tr>
							<tr height=1><td colspan=8 class='dot-x'></td></tr>";
}
*/



$mstring .="</table><br>";
if($hostserver == $_SERVER["HTTP_HOST"]){
	$mstring .= "<table width='100%' cellpadding=0 cellspacing=0 ><tr height=40><td colspan=9 align=center style='padding-top:10px;'><input type=image src='../images/".$admininfo["language"]."/co_sellershop_apply.gif' border=0><-- 회원가입 승인 버튼으로 변경</td></tr></table>";
}
$Contents = $mstring;
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >상품공유 호스트 서버에 등록된 판매 사이트 입니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >판매사이트를 선택 후 입점신청을 하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >입점신청 후 판매사이트에서 승인후 해당 사이트의 상품을 공유해서 판매 하실 수 있습니다.</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$help_text = HelpBox("공유서버 회원가입 이란?", $help_text);
$Contents .= $help_text;

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


$help_text = HelpBox("공유서버 회원가입", $help_text);
$Contents .= $help_text;

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
$P->strLeftMenu = cogoods_menu();
$P->Navigation = "공유상품관리 > 공유서버 회원가입";
$P->title = "공유서버 회원가입";
$P->strContents = $Contents;
$P->PrintLayOut();


?>