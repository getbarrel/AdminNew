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

//if($hostserver){
			$soapclient = new SOAP_Client("http://www.goodss.co.kr/admin/goodss/api/");
			// server.php 의 namespace 와 일치해야함
			$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

			## 한글 인자의 경우 에러가 나므로 인코딩함.



			$results = $soapclient->call("getServiceGoodsList",$params = array("mall_ix"=> $admininfo[mall_ix]),	$options);
			//print_r($results);
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
//		}
//$db = new Database;

$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
	    <td align='left' colspan=9> ".GetTitleNavigation("공유서버 회원가입", "상점관리 > 공유서버 회원가입")."</td>
	</tr>
</table>
<form name='co_shoplist_frm' action='hostserver.act.php' method=post onsubmit='return SellerRegAuth(this);' target='iframe_act'>
	<input type=hidden name=act value=seller_reg_auth>
	<input type=hidden name=chs_ix value='".$chs_ix."'>
<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
	<col width=3%>
	<col width=*>
	<col width=10%>
	<col width=20%>
	<col width=20%>
	

	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'><input type=checkbox name='company_id_all' id='co_company_id' value='' onclick='fixAll(this.form)'></td>
		<td class='m_td'>업체소개</td>
		<td class='m_td'>기본가격</td>
		<td class='m_td'>가입상태</td>
		<td class='e_td'>관리</td>
		</tr>";
if($b2b_companyinfos) {
	if(count($b2b_companyinfos) > 0){

		for($i=0;$i < count($b2b_companyinfos);$i++){
			$b2b_companyinfo = (array)$b2b_companyinfos[$i];
			//print_r($b2b_companyinfo);
			//$b2b_companyinfo[apply_status] == "AU"
			if($b2b_companyinfo[seller_auth] == "N"){
				$seller_auth = "승인대기";
			}else if($b2b_companyinfo[seller_auth] == "Y"){
				$seller_auth = "승인";
			}else if($b2b_companyinfo[seller_auth] == "X"){
				$seller_auth = "승인취소";
			}
			$mstring .="<tr height=152 align=center>
						<td><input type=checkbox name='co_company_id[]' id='co_company_id' value='".$b2b_companyinfo[company_id]."' title='".$b2b_companyinfo[company_id]."'  ".($b2b_companyinfo[company_id] == $admininfo[company_id] ? "disabled":"")."></td>
						<td>
						<table width='90%'>
							<col width='280'>
							<col width='*'>
							<tr>
								<td><img src='http://www.goodss.co.kr/".PrintImageForGoodss("/data/goodss/images/service_product", $b2b_companyinfo[id], "s")."' style='margin:10px;'> </td>
								<td style='text-align:left;padding:10px;line-height:130%;'>
									<!--a href='company.add.php?company_id=".$b2b_companyinfo[company_id]."'-->".nl2br($b2b_companyinfo[shotinfo])."<!--/a-->
								</td>
							</tr>
						</table>
						
						
						</td>
						<td>".number_format($b2b_companyinfo[sellprice])." 원</td>
						<td style='line-height:130%;'>";
						if($b2b_companyinfo[si_status] == "SR"){
							$mstring .= "서비스 신청중";
						}else if($b2b_companyinfo[si_status] == "SI"){
							$mstring .= "<b>서비스 중</b><br>".$b2b_companyinfo[sm_sdate]." ~ ".$b2b_companyinfo[sm_edate];
						}else if($b2b_companyinfo[si_status] == "SC"){
							$mstring .= "<b>서비스취소</b>";
						}else{
							$mstring .= "<b>미신청중</b>";
						}
						$mstring .= "</td>
						<td align=center nowrap>";

						

				if($b2b_companyinfo[b2b_url] != ""){
				$mstring .="
					<a href='".$b2b_companyinfo[b2b_url]."' target=_blank><img src='../images/".$admininfo["language"]."/go_b2b.gif' border=0 style='margin-bottom:3px;'></a><br>  ";
				}
				$mstring .="
					<!--a href='/admin/store/basicinfo.php?company_id=".$b2b_companyinfo[company_id]."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a>
					<a href=\"JavaScript:DeleteSellerShop('".$b2b_companyinfo[company_id]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a--> <!--img src='../images/".$admininfo["language"]."/btn_detail_view.gif' border=0-->  ";

				if($b2b_companyinfo[si_status] == "SR"){
					$mstring .= "<a href='#' onClick=\"alert('서비스 신청중입니다. \\n서비스 처리가 늦어질 경우 www.gooodss.co.kr (1600-2028)고객센타로 문의 주시기 바랍니다.')\" >".($service_info[si_ix] == "" ? "<img src='../images/korea/btn_service.gif' align=absmiddle>":"<img src='../images/korea/btn_addext.gif' align=absmiddle>")."</a>";	
				}elseif($b2b_companyinfo[si_status] == "SI"){
					$mstring .= "<a href='#' onClick=\"PopSWindow('service_apply.php?si_ix=".$b2b_companyinfo[si_ix]."&pid=".$b2b_companyinfo[pid]."&parent_service_code=".$b2b_companyinfo[parent_service_code]."&service_code=".$b2b_companyinfo[service_code]."',700,400,'service_apply')\" >".($service_info[si_ix] == "" ? "<img src='../images/korea/btn_service_extension.gif' align=absmiddle>":"<img src='../images/korea/btn_addext.gif' align=absmiddle>")."</a>";	
				}else{
					$mstring .= "<a href='#' onClick=\"PopSWindow('service_apply.php?si_ix=".$b2b_companyinfo[si_ix]."&pid=".$b2b_companyinfo[pid]."&parent_service_code=".$b2b_companyinfo[parent_service_code]."&service_code=".$b2b_companyinfo[service_code]."',700,400,'service_apply')\" >".($service_info[si_ix] == "" ? "<img src='../images/korea/btn_service.gif' align=absmiddle>":"<img src='../images/korea/btn_addext.gif' align=absmiddle>")."</a>";	
				}
				//}

			$mstring .="
						</td>
					</tr>";
		}
	}
}else{
	$mstring .= "<tr height=100><td colspan=9 align=center style='padding-top:10px;'>등록된 판매사이트가 없습니다.</td></tr>
							";
}



$mstring .="</table><br>";
if($hostserver == $_SERVER["HTTP_HOST"]){
	$mstring .= "<table width='100%' cellpadding=0 cellspacing=0 ><tr height=40><td colspan=9 align=center style='padding-top:10px;'><input type=image src='../images/".$admininfo["language"]."/co_sellershop_apply.gif' border=0><-- 회원가입 승인 버튼으로 변경</td></tr></table>";
}
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
$P->Navigation = "도매아이템 > 도매업체리스트";
$P->title = "도매업체리스트";
$P->strContents = $Contents;
$P->PrintLayOut();


?>