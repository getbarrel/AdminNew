<?
ini_set('include_path', ".:/usr/local/lib/php:$DOCUMENT_ROOT/include/pear");
include("../class/layout.class");
include("./co_goods.lib.php");
$install_path = "../../include/";
include("SOAP/Client.php");


if($list_type == ""){
		if($hostserver){
			$soapclient = new SOAP_Client("http://".$hostserver."/admin/cogoods/api/");
			// server.php 의 namespace 와 일치해야함
			$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

			## 한글 인자의 경우 에러가 나므로 인코딩함.



			$sellersinfo = $soapclient->call("getSellerInfo",$params = array("company_id"=> $admininfo[company_id]),	$options);
			$sellersinfo = (array)$sellersinfo;
			//print_r($sellersinfo);
			$total = $sellersinfo["total"];
			$apply_sellers = $sellersinfo["sellers"];
			//echo $sellersinfo["sql"];
			//print_r($ret);

		}
}else{
		//$soapclient = new SOAP_Client("http://b2b.mallstory.com/admin/cogoods/api/");//서버 판매공유 상품목록 페이지처럼 변경함 kbk 2011-04-04
		/*
		$db->query("SELECT * FROM ".TBL_SHOP_SHOPINFO." where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'  ");
		$db->fetch();
		$hostserver = $db->dt[hostserver];
		*/
		/*
		if($list_type == "apply"){
			$sql = "SELECT * FROM co_myserver_info   ";
			//echo $sql;
			$db->query($sql); //where mall_ix = '".$admininfo[mall_ix]."' and mall_div = '".$admininfo[mall_div]."'
			$_my_serverinfo = $db->fetchall("object");
			if($db->total){
				$db->fetch();

				for($i=0;$i < count($_my_serverinfo);$i++){
					$my_serverinfo[$_my_serverinfo[$i]["server_property"]] =  $_my_serverinfo[$i]["server_value"];
				}
				$hostserver = $my_serverinfo[server_url];
			}
		}
		*/

		$soapclient = new SOAP_Client("http://".$hostserver."/admin/cogoods/api/");

		// server.php 의 namespace 와 일치해야함
		$options = array('namespace' => 'urn:SOAP_FORBIZ_CoGoods_Server','trace' => 1);

		## 한글 인자의 경우 에러가 나므로 인코딩함.
		if(!$list_type){
			$list_type = "apply";
		}


		$sellersinfo = $soapclient->call("getSellerShopApplyList",$params = array("list_type" => $list_type,"company_id"=> $admininfo[company_id]),	$options);
		//print_r($sellersinfo);
		$sellersinfo = (array)$sellersinfo;
		$total = $sellersinfo["total"];
		$apply_sellers = $sellersinfo["sellers"];
		//echo $ret;


		//echo "<span style='background-color:red;'>{$apply_sellers}</span>";
		//print_r($apply_sellers);
		//echo count($apply_sellers);
		/*
		if($ret){
			$doc = new DOMDocument();
			$doc->loadXML(urldecode($ret));
			$doc->saveXML();
			$xpath = new DOMXpath($doc);
			$params = $xpath->query("*[@admin_level='8']");
		}
		*/
}

if($list_type == "byapply"){
	$act = "sellershop_cancel";
}else{
	$act = "sellershop_approval";
}

$mstring = "
<table width='100%' cellpadding=0 cellspacing=0 border='0'>
	<tr>
	    <td align='left' colspan=8> ".GetTitleNavigation("입점신청관리", "상점관리 > 입점신청관리")."</td>
	</tr>
	<tr>
	    <td align='left' colspan=8 style='padding-bottom:10px;'> ".getHostServer($chs_ix)."</td>
	</tr>
	<tr>
	    <td align='left' colspan=8 style='padding-bottom:14px;'>
	    <div class='tab'>
				<table class='s_org_tab'>
				<tr>
					<td class='tab'>
						<table id='tab_00'  ".($list_type == "" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='co_sellershop_apply.php?list_type=&chs_ix=".$chs_ix."'\">공유서버 판매사이트</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_01'  ".($list_type == "byapply" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='co_sellershop_apply.php?list_type=byapply&chs_ix=".$chs_ix."'\">내가 신청한 입점신청 목록</td>
							<th class='box_03'></th>
						</tr>
						</table>
						<table id='tab_02' ".($list_type == "apply" ? "class='on'":"").">
						<tr>
							<th class='box_01'></th>
							<td class='box_02' onclick=\"document.location.href='co_sellershop_apply.php?list_type=apply&chs_ix=".$chs_ix."'\">내가 요청받은 입점요청 목록</td>
							<th class='box_03'></th>
						</tr>
						</table>

					</td>
					<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>";
					if($list_type == "byapply"){
						$mstring .= "<!--귀사가 입점신청한 업체 목록입니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." ";
					}else{
						$mstring .= " <!--귀사의 쇼핑몰을 입점업체로 등록한 업체 목록입니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'C')." ";
					}
$mstring .= "
					</td>
				</tr>
				</table>
			</div>
	    </td>
	</tr>
	<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
		<col width=3%>
		<col width=10%>
		<col width=20%>
		<col width=7%>
		<col width=20%>
		<col width=10%>
		<col width=20%>
		<col width=10%>

	<form name='co_shoplist_frm' action='hostserver.act.php' method=post onsubmit='return CheckSeopList(this);' target='iframe_act'><!--target='iframe_act'-->
	<input type=hidden name=act value='".$act."'>
	<input type=hidden name=chs_ix value='".$chs_ix."'>
	<tr bgcolor=#efefef align=center height=27>
		<td class='s_td'><input type=checkbox name='company_id_all' id='co_company_id' value='' onclick='fixAll(this.form)'></td>
		<td class='m_td'>상점명</td>
		<td class='m_td'>도메인</td>
		<td class='m_td'>대표자명</td>
		<td class='m_td'>대표전화</td>
		<td class='m_td'>팩스</td>
		<td class='m_td'>이메일</td>
		<td class='e_td'>신청상태</td>
		</tr>";
if($apply_sellers) {
	if(count($apply_sellers) > 0){

		for($i=0;$i < count($apply_sellers);$i++){
			$apply_sellers[$i] = (array)$apply_sellers[$i];

			if($list_type == "" || $list_type == "apply"){
				$this_company_id = $apply_sellers[$i][company_id];
			}else{
				$this_company_id = $apply_sellers[$i][co_company_id];
			}

			if($apply_sellers[$i]["apply_status"] == "AP"){
				$apply_status_str = "신청중";
			}else if($apply_sellers[$i][apply_status] == "AU"){
				$apply_status_str = "승인완료";
				if($admininfo[company_id] == $this_company_id){
					$add_button_str = " <a href='setting.php?chs_ix=".$chs_ix."&company_id=".$apply_sellers[$i][company_id]."' rel='facebox'>환경설정 </a> ";
				}
			}else if($apply_sellers[$i][apply_status] == "CA"){
				$apply_status_str = "승인취소";
			}else{
				$apply_status_str = "-";
			}


			$mstring .="<tr height=32 align=center>
						<td title='".$this_company_id."'><input type=checkbox name='co_company_id[]' id='co_company_id' value='".$this_company_id."'  ".($this_company_id == $admininfo[company_id] ? "disabled":"")."></td>
						<td><a href='company.add.php?company_id=".$this_company_id."'>".$apply_sellers[$i][shop_name]."</a></td>
						<td>".$apply_sellers[$i][shop_url]."</td>
						<td>".$apply_sellers[$i][com_ceo]."</td>
						<td>".$apply_sellers[$i][com_phone]."</td>
						<td>".$apply_sellers[$i][com_fax]."</td>
						<td>".$apply_sellers[$i][com_email]."</td>
						<td align=center nowrap>
						$apply_status_str
						$add_button_str
						</td>
					</tr>";
		}
	}
}else{

	$mstring .= "<tr height=100>
						<td colspan=8 align=center style='padding-top:10px;'>";

			if($list_type == "byapply"){
				$mstring .= "		입점신청 목록이 없습니다.";
			}else if($list_type == "apply"){
				$mstring .= "		입점요청 목록이 없습니다.";
			}else{
				$mstring .= "
										등록된 판매사이트가 없습니다.";
			}
	$mstring .= "
						</td>
					  </tr>	";
}
$mstring .= "</table>";
$mstring .= "<table width='100%' cellpadding=0 cellspacing=0 >";
if($list_type == "byapply"){
	$mstring .= "<tr height=40><td colspan=8 align=center style='padding-top:20px;'><input type=image src='../images/".$admininfo["language"]."/co_sellershop_cancel.gif' border=0></td></tr>";
}else if($list_type == "apply"){
	$mstring .= "<tr height=40>
							<td colspan=8 align=center style='padding-top:20px;'>
							<table>
								<tr>
									<td><input type=image src='../images/".$admininfo["language"]."/co_sellershop_approval.gif' border=0> </td>
									<td><img  src='../images/".$admininfo["language"]."/co_sellershop_cancel.gif' border=0 style='cursor:hand;' onclick=\"ApprovalCancelSellerShop(document.co_shoplist_frm)\"></td>
								</tr>
							</table>
							</td>
					  </tr>";
}else{
	$mstring .= "<tr height=40><td colspan=8 align=center style='padding-top:10px;'><img  src='../images/".$admininfo["language"]."/co_sellershop_apply.gif' border=0 style='cursor:hand;' onclick=\"ApprovalSellerShop(document.co_shoplist_frm)\"></td></tr>";

}

$mstring .="</form>
						</table><br>";
$Contents = $mstring;
/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>입점신청목록</b> 은 귀사의 쇼핑몰을 입점업체로 요청한 목록을 말합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>입점요청목록</b> 은 귀사가 입점요청을 한 업체목록을 말합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >입점신청 후 판매사이트에서 승인후 해당 사이트의 상품을 공유해서 판매 하실 수 있습니다.</td></tr>
</table>
";*/

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$help_text = HelpBox("입점신청관리", $help_text);
$Contents .= $help_text;


$Script = "<script language='javascript'>
function ApprovalCancelSellerShop(frm){
	var check_bool = false;

	for(i=0;i < frm.co_company_id.length;i++){
			if(frm.co_company_id[i].checked){
				check_bool = true;
			}
	}

	if(!check_bool){
		alert('입점업체를 한개이상 선택하셔야 합니다.');
		return false;
	}

	if(confirm('승인된 입점업체를 승인 취소하시겠습니까? 승인 취소하게 되시면 판매중인 상품이 모두 판매 중지 처리 되게 됩니다.')){
	//frames['iframe_act'].
		frm.act.value = 'sellershop_sellercancel';
		frm.submit();
		//document.location.href='hostserver.act.php?act=sellershop_cancel&company_id='+company_id;
	}
}

function ApprovalSellerShop(frm){
	var check_bool = false;

	for(i=0;i < frm.co_company_id.length;i++){
			if(frm.co_company_id[i].checked){
				check_bool = true;
			}
	}

	if(!check_bool){
		alert('판매사이트를 한개이상 선택하셔야 합니다.');
		//return false;
	}else{
		frm.act.value = 'sellershop_apply';
		frm.submit();
	}

}

function CheckSeopList(frm){
	var check_bool = false;

	for(i=0;i < frm.co_company_id.length;i++){
			if(frm.co_company_id[i].checked){
				check_bool = true;
			}
	}

	if(!check_bool){
		alert('판매사이트를 한개이상 선택하셔야 합니다.');
		return false;
	}else{
		if(confirm('입점업체 승인을 하시면 해당업체가 자동으로 셀러 등록이 되게 됩니다. 입점업체 승인을 하시겠습니까?')){
			return true;
		}else{
			return false;
		}

	}

}
function clearAll(frm){
		for(i=0;i < frm.co_company_id.length;i++){
				frm.co_company_id[i].checked = false;
		}
}

function checkAll(frm){
       for(i=0;i < frm.co_company_id.length;i++){
				frm.co_company_id[i].checked = true;
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
$P->Navigation = "공유상품관리 > 입점신청관리";
$P->title = "입점신청관리";
$P->strContents = $Contents;
$P->PrintLayOut();


?>