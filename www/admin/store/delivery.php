<?
include("../class/layout.class");
//include("../webedit/webedit.lib.php");
include("../econtract/contract.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
//include($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");

$db = new Database;
$db2 = new Database;
$cdb = new Database;
$mdb = new Database;

if($info_type == ''){
	$info_type = 'shipping_info';
}

$company_id = $admininfo[company_id];		//셀러용

$sql = "select * from common_seller_delivery where company_id = '".$company_id."'";
$db->query($sql);
$db->fetch();
$delivery_deadline_yn = $db->dt[delivery_deadline_yn];
$delivery_deadline_hour = $db->dt[delivery_deadline_hour];
$delivery_deadline_minute = $db->dt[delivery_deadline_minute];

if($info_type == 'shipping_info' || $info_type == '' ){
	$sql = "select * from common_seller_delivery where company_id = '".$company_id."'";
	$db->query($sql);
	$db->fetch();
	$delivery_company = $db->dt[delivery_company];
	$delivery_product_policy = $db->dt[delivery_product_policy];
    $goodsflow_return_yn = $db->dt[goodsflow_return_yn];

}else if($info_type == "seller_setup"){

	$sql = "select
				csd.*,
				et.contract_group
			from
				common_seller_delivery as csd
				left join econtract_tmp as et on (csd.et_ix = et.et_ix)
			where
				company_id = '".$admininfo[company_id]."'";
	$db->query($sql);
	$db->fetch();

	$et_ix = $db->dt[et_ix];
	$contract_group = $db->dt[contract_group];
	$econtract_commission = $db->dt[econtract_commission];
	$is_contract = $db->dt[is_contract];
	$account_info = $db->dt[account_info];
	$ac_delivery_type = $db->dt[ac_delivery_type];
	$ac_expect_date = $db->dt[ac_expect_date];
	$ac_term_div = $db->dt[ac_term_div];
	$ac_term_date1 = $db->dt[ac_term_date1];
	$ac_term_date2 = $db->dt[ac_term_date2];

	$account_type = $db->dt[account_type];
	$account_method = $db->dt[account_method];			//=
	$wholesale_commission = $db->dt[wholesale_commission];
	$commission = $db->dt[commission];

	$seller_grant_use = $db->dt[seller_grant_use];
	$grant_setup_price = $db->dt[grant_setup_price];
	$ac_grant_price = $db->dt[ac_grant_price];
	$account_div = $db->dt[account_div];

	$act = "update";

}else{

	if($info_type == "factory_info"){
		$type = "F";
	}else if($info_type == "exchange_info"){
		$type = "E";
	}else if($info_type == "visit_info"){
		$type = "V";
	}

	if($_GET[addr_ix]){
		$where = "  and addr_ix = '".$_GET[addr_ix]."' "; 
	}

	$sql = "
		select
			*
		from
			shop_delivery_address
		where
			delivery_type = '".$type."'
			and mall_ix = '".$admininfo[mall_ix]."'
			and company_id = '".$admininfo[company_id]."'
	";
	$db->query($sql);
	$delivery_array = $db->fetchall();
	$act = "insert";
}

if($_GET[info_type] and $_GET[addr_ix]){
	if($info_type == "factory_info"){
	$type = "F";
	}else if($info_type == "exchange_info"){
	$type = "E";
	}else if($info_type == "visit_info"){
	$type = "V";
	}
	if($_GET[addr_ix]){
		$where = "  and addr_ix = '".$_GET[addr_ix]."' "; 
	}

	$sql = "
		select
			*
		from
			shop_delivery_address
		where
			delivery_type = '".$type."'
			and mall_ix = '".$admininfo[mall_ix]."'
			and company_id = '".$admininfo[company_id]."'
			$where";

	$db->query($sql);
	$db->fetch();

	$addr_phone = explode("-",$db->dt[addr_phone]);
	$addr_mobile = explode("-",$db->dt[addr_mobile]);
	$act = "update";
}

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
		<td align='left' colspan=2 > ".GetTitleNavigation("배송/택배정책", "상점관리 > 배송/택배정책 <a onClick=\"PoPWindow('/admin/_manual/manual.php?config=".urlencode("몰스토리동영상메뉴얼_배송택배정책(090322)_config.xml")."',800,517,'manual_view')\"  title='배송/택배정책 관리 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a>")."</td>
	</tr>
	<tr>
	    <td align='left' colspan=2 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<tr>
				<td class='tab'>
					<table id='tab_01' ".(($info_type == "shipping_info" || $info_type == "") ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?info_type=shipping_info&company_id=".$company_id."'>배송/택배정책</a></td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_06' ".($info_type == "delivery_group" ? "class='on' ":"")." style='$display_yn'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02'  ><a href='?info_type=delivery_group&company_id=".$company_id."'>묶음배송 그룹 정책</a></td>
						<th class='box_03'></th>
					</tr>
					</table>

					<table id='tab_03'".($info_type == "exchange_info" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						$Contents .= "<a href='?info_type=exchange_info&company_id=".$company_id."'>교환/반품지 관리</a>";
					$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>

					<table id='tab_04' ".($info_type == "visit_info" ? "class='on' ":"")." style='$display_yn'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						$Contents .= "<a href='?info_type=visit_info&company_id=".$company_id."'>방문수령지 관리</a>";
					$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>

					<table id='tab_05' ".($info_type == "seller_setup" ? "class='on' ":"")." style='$display_yn'>
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";
						$Contents .= "<a href='?info_type=seller_setup&company_id=".$company_id."'>수수료설정</a>";
					$Contents .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</div>
	    </td>
	</tr>
	</table>";

if($info_type == "shipping_info" || $info_type == ""){

if($max == ""){
	$max = 15; //페이지당 갯수
}else{
	$max = $max;
}

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if($info_type == ""){
	$info_type = "basic";
}

$sql = "select 
        count(*) as total
    from
        shop_delivery_template as dt
        inner join common_company_detail as ccd on (dt.company_id = ccd.company_id)
    where
        dt.company_id = '".$company_id."'";

$db->query($sql);
$db->fetch();
$total = $db->dt['total'];

$sql = "select 
    *
from
    shop_delivery_template as dt
    inner join common_company_detail as ccd on (dt.company_id = ccd.company_id)
where
    dt.company_id = '".$company_id."'
    order by dt.regdate DESC
    limit ".$start.",".$max."";
$db->query($sql);
$template_dt_array = $db->fetchall("object");

if($search_text != ""){
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&list_type=$list_type","view");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype&list_type=$list_type","view");
}

$Contents .= "
	<form name='edit_form' action='delivery.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='act'>
	<input name='act' type='hidden' value='template_update'>
	<input name='info_type' type='hidden' value='$info_type'>
	<input name='company_id' type='hidden' value='".$company_id."'>

	<input type='hidden' name='delivery_policy' value='2'><!-- 셀러별 개별정책 설정은 마스터만 설정가능 셀러는 셀러업체 배송정책을 사용-->
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
	<col width='20%' />
	<col width='80' />

	<tr bgcolor=#ffffff>
		<td class='input_box_title'> 택배업체 설정 </td>
		<td class='input_box_item' style='padding:5px;'>
			<table cellpadding=0 cellspacing=0 border=0 width='640' height='150' style='table-layout:fixed;'>
			<col width='220' />
			<col width='*' />";

                if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") && $_SESSION["admininfo"]["charger_id"]=="forbiz"){
                    $Contents .= "
                    <tr height=25><td colspan='2'><a href='javascript:' onclick=\"window.open('delivery_modify.php?code_ix=','win_category','width=450,height=365');\"><img src='../images/".$admininfo["language"]."/btn_add_ship.gif'></a></td></tr>";
                }


				$Contents .= "
				<tr>
					<td><div id='searchDelieryCompanyArea' style='overflow:auto;height:105px;width:200px;border:1px solid silver;padding:10px;margin:10px 0px;'>".deliveryCompanyList($delivery_company,"seller_list","",$compnay_id)."</div></td>
				</tr>";
				

			$Contents .= "
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff height=50 style='$display_yn'>
		<td class='input_box_title'> <b> 배송마감 시간 </b></td>
		<td class='input_box_item'>
			<input type=radio name='delivery_deadline_yn' value='N' id='delivery_deadline_n'  ".CompareReturnValue("N",$delivery_deadline_yn,"checked")." onclick=\"$('#delivery_deadline_area').hide();\"><label for='delivery_deadline_n'>미사용</label>
			<input type=radio name='delivery_deadline_yn' value='Y' id='delivery_deadline_y'  ".CompareReturnValue("Y",$delivery_deadline_yn,"checked")." onclick=\"$('#delivery_deadline_area').show();\" ><label for='delivery_deadline_y'>사용</label>
			<span id='delivery_deadline_area' style='".($delivery_deadline_yn=='Y' ? "" : "display:none;" )."'>
			  <select name='delivery_deadline_hour'>";
					for ($i=0;$i < 24;$i++){
						$Contents .= "<option value='".$i."' ".($delivery_deadline_hour == $i ? "selected" : "").">".$i."</option>";
					}
				$Contents .= "
			  </select>시 
			  <select name='delivery_deadline_minute'>";
					for ($i=0;$i < 60;$i+=5){
						$Contents .= "<option value='".$i."' ".($delivery_deadline_minute == $i ? "selected" : "").">".$i."</option>";
					}
				$Contents .= "
			  </select>분
			</span>
		</td>
	</tr>";

    if(false) {

        $OAL = new OpenAPI('goodsflow');
        $goodsflowServiceInfo = $OAL->lib->getPartnerCodeServiceInfo($OAL->lib->getPartnerCode($company_id));

        $Contents .= "
       <tr bgcolor=#ffffff>
           <td class='input_box_title'>굿스플로 반품 서비스 사용유무</td>
           <td class='input_box_item' style='padding:5px;'>
                <input type=radio name='goodsflow_return_yn' value='N' id='goodsflow_return_n'  ".CompareReturnValue("N",$goodsflow_return_yn,"checked")." onclick=\"$('#goodsflow_return_service_area').hide();\"><label for='goodsflow_return_n'>미사용</label>
                <input type=radio name='goodsflow_return_yn' value='Y' id='goodsflow_return_y'  ".CompareReturnValue("Y",$goodsflow_return_yn,"checked")." onclick=\"$('#goodsflow_return_service_area').show();\"><label for='goodsflow_return_y'>사용</label>
                <input type='hidden' name='goodsflow_policy_type' value='1' />
           </td>
       </tr>
       <tr bgcolor=#ffffff id='goodsflow_return_service_area' ".($goodsflow_return_yn=='Y' ? "" : "style='display:none;'")." >
           <td class='input_box_title'>굿스플로 반품 서비스 관리</td>
           <td class='input_box_item' style='padding:5px;'>";
        $goodsflowServiceCnt = 0;
        if( is_array($goodsflowServiceInfo) && count($goodsflowServiceInfo) > 0){
            foreach ($goodsflowServiceInfo as $gsi){
                $verifiedResult = $OAL->lib->getVerifiedResult($gsi->requestKey);
                if($verifiedResult){
                    if($verifiedResult->verifiedResult == 'Y'){
                        $verifiedText = "<span class='blue'>승인</span>";
                        $OAL->lib->setDBServiceInfo($company_id,$gsi);
                    }else{
                        //승인 취소시 노출 안함
                        if($verifiedResult->verifiedResult == 'N' && $verifiedResult->verifiedResultCode=='CTN'){
                            continue;
                        }else{
                            $verifiedText = "<span class='red'>".$verifiedResult->verifiedMsg."</span>";
                        }
                    }
                }else{
                    $verifiedText = "<span class='red'>연동 실패</span>";
                }
                $goodsflowServiceCnt ++;
                $Contents .= $gsi->centerName. "(".$OAL->lib->getDeliverNameToDeliveryCode($gsi->deliverCode).") - ".$verifiedText." ";
                $Contents .= "<input type='button' value='취소' onclick=\"if(confirm('해당 서비스를 승인 취소하시겠습니까?')){window.frames['iframe_act'].location.href ='/admin/openapi/goodsflow/goodsflowServiceCancel.php?company_id=".$company_id."&requestKey=".$gsi->requestKey."';}\" />";
            }
        }
        if($goodsflowServiceCnt == 0){
            $Contents .= "<input type='button' value='서비스 등록' onclick='goodsflowServiceRegistPopUp();' />";
            $Contents .= "<script>
            var goodsflowServiceRegistPopObj;
            var goodsflowServiceSetInterval;
            function goodsflowServiceRegistPopUp(){
                goodsflowServiceRegistPopObj = window.open('/admin/openapi/goodsflow/goodsflowServiceRegist.php?company_id=".$company_id."','goodsflowServiceRegist','width=800,height=500');
                goodsflowServiceSetInterval = setInterval(function() {
                   checkGoodsflowServiceRegistPopUp();
                }, 1000);
            }
            
            function checkGoodsflowServiceRegistPopUp(){
                if(goodsflowServiceRegistPopObj.closed){
                    clearInterval(goodsflowServiceSetInterval);
                    document.location.reload();
                }
            }
           </script>";
        }
        $Contents .= "
           </td>
       </tr>";
    }
    $Contents .= "
	</table>
	
	<table width='100%' height='80' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
		</td>
	</tr>
	</table><br><br>
	</form>";

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
		<td colspan=8>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
				<tr>
					<td style='width:100%;padding:20px 0px 10px 0px' valign=top colspan=3>
						<img src='../images/dot_org.gif' align=absmiddle> <b>배송정책 템플릿 리스트</b> 전체 : ( ".$total." ) 개
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>

	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
		<col width='4%'>
		".($_SESSION["admin_config"]["front_multiview"] == "Y" ? "<col style='width:7%;'>":"")."
		<col width='12%'>
		<col width='12%'>
		<col width='6%'>
		<col width='9%'>
		<col width='6%'>
		<col width='6%'>
		<col width='*'>
		<col width='7%'>

		<tr bgcolor=#efefef align=center height=27>
			<td class='s_td'>번호</td>
			".($_SESSION["admin_config"][front_multiview] == "Y" ? "<td class='m_td'> 프론트전시</td>":"")."
			<td class='m_td'>배송정책명</td>
			<td class='m_td'>셀러명</td>
			<td class='m_td'>소매/도매</td>
			<td class='m_td'>배송비 결제수단</td>
			<td class='m_td'>배송 방식</td>
			<td class='m_td'>묶음배송 그룹여부</td>
			<td class='m_td'>배송비 조건</td>
			<td class='e_td'>관리</td>
		</tr>";

if($total > 0){

		for($j =0;$j<count($template_dt_array);$j++){
			$no = $total - ($page - 1) * $max - $j;
$Contents .="	<tr height=35 align=center>
					<td class='list_box_td list_bg_gray' >".$no."</td>";
if($_SESSION["admin_config"]["front_multiview"] == "Y"){
	$Contents .= "
		    <td class='list_box_td'>".GetDisplayDivision($template_dt_array[$j][mall_ix], "text")."</td>";
}
	$Contents .= "
					<td class='list_box_td point' >".$template_dt_array[$j][template_name]."</td>
					<td class='list_box_td point' >
						<a href='#'>".$template_dt_array[$j][com_name]."</a>";
					if($template_dt_array[$j][is_basic_template] == '1'){
						$Contents .="<span style='color:red;'><br>(기본배송정책)</span>";
					}
				$Contents .="
					</td>";
				
				
				switch($template_dt_array[$j][product_sell_type]){
					case 'R':
						$product_sell_type = '소매';
						break;
					case 'W':
						$product_sell_type = '도매';
						break;
				}
				//delivery_basic_policy
				
				switch($template_dt_array[$j][delivery_basic_policy]){
					case '1':
						$delivery_basic_policy = '선불';
						break;
					case '5':
						$delivery_basic_policy = '선불/착불 선택';
						break;
					case '2':
						$delivery_basic_policy = '착불';
						break;
				}

				//delivery_package
				switch($template_dt_array[$j][delivery_package]){
					case 'N':
						$delivery_package = '묶음배송';
						break;
					case 'Y':
						$delivery_package = '개별배송';
						break;
				}

				switch($template_dt_array[$j][delivery_policy]){
					case '1':
						$template_text = "조건 배송비 (".$delivery_package.") : 무료";
						break;
					case '2':
						$template_text = "조건 배송비 (".$delivery_package.") : 고정배송비 ".number_format($template_dt_array[$j][delivery_price])." 원";
						break;
					case '3':
						$sql = "select * from shop_delivery_terms where dt_ix = '".$template_dt_array[$j][dt_ix]."' and delivery_policy_type = '3' order by seq ASC limit 0,1";
						$db->query($sql);
						$db->fetch();
						$template_text = "조건 배송비 (".$delivery_package.") : 주문결제금액 할인 / 주문금액 ".number_format($db->dt[delivery_basic_terms])." 원 미만일경우 ".number_format($db->dt[delivery_price])." 원";
						break;
					case '4':
						$sql = "select * from shop_delivery_terms where dt_ix = '".$template_dt_array[$j][dt_ix]."'  and delivery_policy_type = '4' order by seq ASC limit 0,1";
						$db->query($sql);
						$db->fetch();
						$template_text = "조건 배송비 (".$delivery_package.") : 수량별 할인 / 기본배송비 ".number_format($template_dt_array[$j][delivery_cnt_price])." 원 ".number_format($db->dt[delivery_price])." 개 이상시 ".number_format($db->dt[delivery_basic_terms])." 원 배송비 적용";
						break;
					case '5':
						$sql = "select * from shop_delivery_address where addr_ix = '".$template_dt_array[$j][factory_info_addr_ix]."'";
						$db->query($sql);
						$db->fetch();

						$template_text = "조건 배송비 (".$delivery_package.") : 출고지별 배송비 ( ".$db->dt[addr_name]." )";
						break;
					case '6':
						$template_text = "조건 배송비 (".$delivery_package.") : 상품 1개단위 배송비 ".number_format($template_dt_array[$j][delivery_unit_price])." 원";
						break;
					case '7':
						$sql = "select * from shop_delivery_terms where dt_ix = '".$template_dt_array[$j][dt_ix]."'  and delivery_policy_type = '7' order by seq ASC limit 0,1";
						$db->query($sql);
						$db->fetch();

						$template_text = "조건 배송비 (".$delivery_package.") : 무게당 배송비 / 주문금액 ".number_format($template_dt_array[$j][free_shipping_term])." 원 이상 무료배송 / ".number_format($db->dt[delivery_basic_terms])." kg 이하 ".number_format($db->dt[delivery_price])." 원 배송비 적용";
						break;
				}
			
$Contents .="
					<td class='list_box_td'>".$product_sell_type."</td>
					<td class='list_box_td'>".$delivery_basic_policy."</td>
					<td class='list_box_td'>".$delivery_package."</td>
					<td class='list_box_td'>".checkGroupDt_ix($template_dt_array[$j][dt_ix])."</td>
					<td class='list_box_td'>".$template_text."</td>
					<td class='list_box_td'  align=center style='padding:0px 5px' nowrap>";

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			
				$Contents .="
					<a href=\"javascript:PoPWindow3('../product/product_delivery_template.php?mmode=pop&dt_ix=".$template_dt_array[$j][dt_ix]."&page_type=seller&company_id=".$company_id."',960,960,'company')\"'>
					<img src='../images/".$_SESSION["admininfo"]["language"]."/btc_modify.gif' align=absmiddle border=0>
					</a>";
			}else{
				$Contents .="
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>
					";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents .="
					<a href=\"JavaScript:DeleteTemplate('".$template_dt_array[$j][dt_ix]."','".$template_dt_array[$j][company_id]."')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle'></a> ";
			}else{
				$Contents .="
					<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle'></a> ";
			}

			$Contents .="
					</td>
				</tr>";
		}
}else{
	$Contents .= "<tr height=50><td colspan=9 align=center style='padding-top:10px;'>등록된 배송정책 템플릿이 없습니다.</td></tr>";
}

$Contents .="</table><br>";

$Contents .="<table width='100%' cellpadding=0 cellspacing=0 border='0' >";
	if( $admininfo[mall_use_multishop] && $admininfo[admin_level] == 9){
$Contents .= "<tr hegiht=30><td colspan=7 align=center style='padding:10px 0px;'>".$str_page_bar."</td></tr>";
		
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$Contents .= "
			<tr hegiht=30>
				<td colspan=7 align=right style='padding-top:10px;'>
					<a href=\"javascript:PoPWindow3('../product/product_delivery_template.php?mmode=pop&page_type=seller&company_id=".$company_id."',960,960,'company')\"'>
						<button>배송정책 템플릿 추가하기</button>
					</a>
				</td>
			</tr>";
		}
	}else{
$Contents .= "
			<tr hegiht=30>
				<td colspan=7 align=right style='padding-top:10px 0px;'>".$str_page_bar."</td>
			</tr>";
	}
$Contents .="</table><br>";

$Contents .= "
<script language='javascript'>

function DeleteTemplate(tmp_ix,company_id){

	if(confirm('배송정책 템플릿을 삭제하시겠습니까?')){
		document.location.href='../product/product_delivery.act.php?act=delete_template&dt_ix='+tmp_ix+'&company_id='+company_id;
	}
}

</script>";
$Contents = $Contents;

}

if($info_type == "factory_info"){		//출고지
$Contents .= "
<table width='100%' cellpadding=3 cellspacing=0 border='0'  class=''>
	<tr>
		<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=middle><b> 출고지 관리</b><span class=small> &nbsp;&nbsp;&nbsp;<font color=#5B5B5B>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</font> </span></div>")."</td>
	</tr>
</table>";

$Contents .= "
<form name='edit_form' action='delivery.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='act'><!--target='iframe_act' -->
<input name='act' type='hidden' value='$act'>
<input name='info_type' type='hidden' value='$info_type'>
<input name='company_id' type='hidden' value='".$admininfo[company_id]."'>
<input name='delivery_type' type='hidden' value='F'>
<input name='mall_ix' type='hidden' value='".$admininfo[mall_ix]."'>
<input name='addr_ix' type='hidden' value='$addr_ix'>
<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
	</colgroup>
	<tr>
		<td class='input_box_title'> <b>출고지명 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='addr_name' value='".$db->dt[addr_name]."'  style='width:200px' validation='true' title='출고지명'>
		</td>
		<td class='input_box_title'> <b>담당자명 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='person_name' value='".$db->dt[person_name]."'  title='담당자명'  style='width:200px'>
		</td>
	</tr>

	<tr>
		<td class='input_box_title'> <b>일반 전화번호 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='addr_phone_1' id='addr_phone_1' value='".$addr_phone[0]."' maxlength=3 style='width:20px;' class='textbox ' com_numeric=true validation='true' title='전화'> -
			<input type=text name='addr_phone_2' id='addr_phone_2' value='".$addr_phone[1]."' maxlength=4 style='width:30px;' class='textbox ' com_numeric=true validation='true' title='전화'> -
			<input type=text name='addr_phone_3' id='addr_phone_3' value='".$addr_phone[2]."' maxlength=4 style='width:30px;' class='textbox ' com_numeric=true validation='true' title='전화'>
		</td>
		<td class='input_box_title'> <b>핸드폰번호 <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=text name='addr_mobile_1' id='addr_mobile_1' value='".$addr_mobile[0]."' maxlength=3 style='width:20px;' class='textbox' com_numeric=true validation='true' title='핸드폰'> -
			<input type=text name='addr_mobile_2' id='addr_mobile_2' value='".$addr_mobile[1]."' maxlength=4 style='width:30px;' class='textbox' com_numeric=true validation='true' title='핸드폰'> -
			<input type=text name='addr_mobile_3' id='addr_mobile_3' value='".$addr_mobile[2]."' maxlength=4 style='width:30px;' class='textbox' com_numeric=true validation='true' title='핸드폰'>
		</td>
	</tr>
	<tr>
	<td class='input_box_title'> <b>출고지 주소 <img src='".$required3_path."'> </b>    </td>
	<td class='input_box_item' colspan=3>
		<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
		<div id='input_address_area' ><!--style='display:none;'-->
		<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
			<col width='70px'>
			<col width='*'>
			<tr>
				<td height=26>
					<input type='text' class='textbox' name='com_zip' id='zip_b_1' style='width:60px;' maxlength='15' value='".$db->dt[zip_code]."' readonly>
				</td>
				<td style='padding:1px 0 0 5px;'>
					<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
				</td>
			</tr>
			<tr>
				<td colspan=2 height=26>
					<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[address_1]."' size=50 class='textbox'  style='width:300px;'>
				</td>
			</tr>
			<tr>
				<td colspan=2 height=26>
					<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[address_2]."' size=70 class='textbox'  style='width:300px'> (상세주소)
				</td>
			</tr>
			</table>
		</div>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>기본 출고지 사용  <img src='".$required3_path."'> </b></td>
		<td class='input_box_item'>
			<input type=radio class='radio' name='basic_addr_use' value='Y' id='basic_addr_use_1'  validation='false' title='기본 출고지 사용' ".CompareReturnValue("Y",$db->dt[basic_addr_use],"checked")."> <label for='basic_addr_use_1'>사용</lable>
			<input type=radio class='radio' name='basic_addr_use' value='N' id='basic_addr_use_2'  validation='false' title='기본 출고지 사용' ".CompareReturnValue("N",$db->dt[basic_addr_use],"checked")."> <label for='basic_addr_use_2'>미사용</lable>
		</td>
		<td class='input_box_title'> <b>코드</b></td>
		<td class='input_box_item'>
			<input type=text class='textbox' name='code' value='".$db->dt[code]."'  validation='false' title='코드'  style='width:200px'>
		</td>
	</tr>
	<tr>
			<td class='input_box_title'> <b>출고지 배송정책 노출여부  <img src='".$required3_path."'> </b></td>
			<td class='input_box_item' colspan='3'>
				<input type=radio class='radio' name='is_delivery_use' value='Y' id='is_delivery_use_y'  validation='true' title='출고지 배송비 사용' ".CompareReturnValue("Y",$db->dt[is_delivery_use],"checked")."> <label for='is_delivery_use_y'>사용</lable>
				<input type=radio class='radio' name='is_delivery_use' value='N' id='is_delivery_use_n'  validation='true' title='출고지 배송비 미사용' ".CompareReturnValue("N",$db->dt[is_delivery_use],"checked")."> <label for='is_delivery_use_n'>미사용</lable>
			</td>
		</tr>
</table><br>";

$Contents .= "
<table width='100%' height='80' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table>
</form>";

$Contents .= "
<table width='100%' cellpadding=3 cellspacing=0 border='0'  class=''>
<tr>
	<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=middle><b> 출고지 리스트</b><span class=small> &nbsp;&nbsp;&nbsp;<font color=#5B5B5B>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</font> </span></div>")."</td>
</tr>
</table>";
$Contents .= "
<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
<col width='5%'>
<col width='6%'>
<col width='7%'>
<col width='7%'>
<col width=8%>
<col width=8%>
<col width=8%>
<col width=*>
<col width=10%>
<col width=10%>
<tr bgcolor=#efefef align=center height=27>
	<td class='s_td'>번호</td>
	<td class='m_td'>코드</td>
	<td class='m_td'>주소명</td>
	<td class='m_td'>담당자명</td>
	<td class='m_td'>전화번호</td>
	<td class='m_td'>핸드폰번호</td>
	<td class='m_td'>우편번호</td>
	<td class='m_td'>상세주소</td>
	<td class='m_td'>기본주소여부</td>
	<td class='e_td'>관리</td>
</tr>";

if(count($delivery_array) > 0){
	for($i=0;$i<count($delivery_array); $i++){

		$Contents .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td '>".$delivery_array[$i][code]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_name]."</td>
					<td class='list_box_td point' style='padding-left:10px; text-align:left;'>".$delivery_array[$i][person_name]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_phone]."</td>
					<td class='list_box_td'>".$delivery_array[$i][addr_mobile]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][zip_code]."</td>
					<td class='list_box_td '>".$delivery_array[$i][address_1]."<br>".$delivery_array[$i][address_2]."</td>
					<td class='list_box_td list_bg_gray'>".($delivery_array[$i][basic_addr_use])."</td>
					<td class='list_box_td ' nowrap>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .="<a href='delivery.php?addr_ix=".$delivery_array[$i][addr_ix]."&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$Contents .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents .="<a href='delivery.act.php?addr_ix=".$delivery_array[$i][addr_ix]."&act=delete&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents .="<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$Contents .="
					</td>
				</tr>";
	}
	$Contents .=	"</table>";
	$Contents .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$Contents .= "
				<tr height=50><td colspan=10 align=center style='padding-top:10px;'>등록된 촐고지 정보가 없습니다.</td></tr>";
}
$Contents .= "</table>";
$Contents .= "<table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$Contents .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}else{
	$Contents .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}
$Contents .="</table><br>";
$Contents = $Contents;

}

if($info_type == "delivery_group"){		//묶음배송 그룹정책

if(! empty($g_ix)){
	$sql = "select * from shop_delivery_group where g_ix='".$g_ix."'";
	$db->query($sql);
	$db->fetch();
	$name = $db->dt[name];
	$state = $db->dt[state];
}

$temp_list = loadDeliveryTemplate();
$contents_list = loadDeliveryGroup();

$Contents .= "
<form name='edit_form' action='delivery.act.php' method='post' onsubmit='return CheckFormCustom(this)' enctype='multipart/form-data' style='display:inline;'>
<input name='act' type='hidden' value='".(! empty($g_ix) ? "group_update" : "group_insert")."'>
<input name='info_type' type='hidden' value='$info_type'>
<input name='g_ix' type='hidden' value='$g_ix'>
<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
	<colgroup>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
		<col width='15%' />
		<col width='35%' style='padding:0px 0px 0px 10px'/>
	</colgroup>
	<tr>
		<td class='input_box_title'> <b>묶음배송 그룹명  </b></td>
		<td class='input_box_item' colspan='3'>
			<input type=text class='textbox' name='name' value='".$name."'  style='width:200px' validation='true' title='묶음배송 그룹명'>
		</td>
	</tr>

	<tr>
		<td class='input_box_title'> <b>배송정책 템플릿 선택</br>(2개 이상)</b></td>
		<td class='input_box_item' colspan='3'>
			<div id='category_div'>
				<div id='category_area' style=''>
				<table cellpadding=0 cellspacing=0  border='0' width='40%'  id='select_category' style='display:;'>
					<col width=90%>
					<tr>
						<td class=''>
							<table width=100% border=0 cellpadding=0 cellspacing=0>
								<tr>
									<td style='padding:5px 0px 5px 0px;'>
										<select id='selectTemplate' multiple style='1px solid silver;height:155px;width:50%;' onchange='addTemplate(this);'>
											";
											if(count($temp_list) > 0){
												foreach($temp_list as $k => $v){
													$Contents .= "<option value='".$v[dt_ix]."' name='".$v[template_name]."'>".$v[template_name]."</option>";
												}
											}
$Contents .= "
										</select>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				</div>

				<table border=0 cellpadding=0 cellspacing=0 width='50%' style='padding:5px 10px 5px 10px;border:1px solid silver' class='addTemplateDiv'>
					<col width=100%>";
				
				$sql = "select t.dt_ix, t.template_name, r.rep from shop_delivery_relation r left join shop_delivery_template t on r.dt_ix=t.dt_ix where r.g_ix='".$g_ix."'";
				$db->query($sql);
				$saved_list = $db->fetchall("object");

				if(count($saved_list) > 0){
					foreach($saved_list as $sdk => $sdv){
						$Contents .= "
						<tr height='30'>
							<td width='5'><input type='radio' id='".$sdv[dt_ix]."' name='rep' value='".$sdv[dt_ix]."' title='대표 템플릿' ".($sdv[rep] == "Y" ? "checked" : "")."><input type='hidden' name='dt_ix[]' value='".$sdv[dt_ix]."'><label for='".$sdv[dt_ix]."'>".$sdv[template_name]."</label></td>
							<td></td><td></td>
							<td width='100' onclick=\"$(this).parent().remove();addTemplate2(".$sdv[dt_ix].", '".$sdv[template_name]."');\"><img src='../images/korea/btc_del.gif' style='cursor:pointer'></td>
						</tr>";
					}
				}

$Contents .= "
				</table>
				<div style='clear:both;height:70px;'></div>
			</div>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>사용 여부</b></td>
		<td class='input_box_item' colspan='3'>
			<input type=radio class='radio' name='state' value='1' id='state_1'  validation='true' title='사용 여부' ".($state == '1' || $state == '' ? "checked" : "")."> <label for='state_1'>사용</lable>
			<input type=radio class='radio' name='state' value='0' id='state_0'  validation='true' title='사용 여부' ".($state == '0' ? "checked" : "")."> <label for='state_0'>미사용</lable>
		</td>
	</tr>
</table><br>";

$Contents .= "
<table width='100%' height='80' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table>
</form>";

$Contents .= "
<table width='100%' cellpadding=3 cellspacing=0 border='0'  class=''>
<tr>
	<td align='left' colspan=2 style='padding:3px 0px;'> 묶음 배송 그룹 리스트 | 전체 : ".count($contents_list)."개</td>
</tr>
</table>";
$Contents .= "
<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
<col width='5%'>
<col width=*>
<col width='7%'>
<col width=*>
<col width=10%>
<tr bgcolor=#efefef align=center height=27>
	<td class='s_td'>번호</td>
	<td class='m_td'>묶음배송 그룹명</td>
	<td class='m_td'>사용여부</td>
	<td class='m_td'>배송정책 템플릿</td>
	<td class='e_td'>관리</td>
</tr>";

if(count($contents_list) > 0){
	for($i=0;$i<count($contents_list); $i++){
		$sub_sql = "select t.dt_ix, t.template_name from shop_delivery_relation r left join shop_delivery_template t on r.dt_ix=t.dt_ix where r.g_ix='".$contents_list[$i][g_ix]."'";
		$db->query($sub_sql);
		$sub_list = $db->fetchall("object");

		$Contents .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td '>".$contents_list[$i][name]."</td>
					<td class='list_box_td '>".($contents_list[$i][state] == 1 ? "사용" : "미사용")."</td>
					<td class='list_box_td '>";
					if(count($sub_list) > 0){
						foreach($sub_list as $sk => $sv){
							$Contents .= $sv[template_name]."</br>";
						}
					}
		$Contents .=
					"</td>
					<td class='list_box_td ' nowrap>
						<a href='delivery.php?g_ix=".$contents_list[$i][g_ix]."&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> 
						<a onclick='group_delete(".$contents_list[$i][g_ix].");' style='cursor:pointer;'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>
					</td>
				</tr>";
	}
	$Contents .=	"</table>";
	$Contents .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$Contents .= "
				<tr height=50><td colspan=10 align=center style='padding-top:10px;'>등록된 묶음배송 그룹 정책이 없습니다.</td></tr>";
}
$Contents .= "</table>";
$Contents .= "<table>";
$Contents .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
$Contents .="</table><br>";
$Contents = $Contents;

}

if($info_type == "exchange_info"){		//교환/반품지
$Contents .= "
<table width='100%' cellpadding=3 cellspacing=0 border='0'  class=''>
<tr>
	<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=middle><b> 교환/반품지 관리</b><span class=small> &nbsp;&nbsp;&nbsp;<font color=#5B5B5B>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</font> </span></div>")."</td>
</tr>
</table>";

$Contents .= "
<form name='edit_form' action='delivery.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='iframe_act'><!--target='iframe_act' -->
<input name='act' type='hidden' value='$act'>
<input name='info_type' type='hidden' value='$info_type'>
<input name='company_id' type='hidden' value='".$admininfo[company_id]."'>
<input name='delivery_type' type='hidden' value='E'>
<input name='mall_ix' type='hidden' value='".$admininfo[mall_ix]."'>
<input name='addr_ix' type='hidden' value='$addr_ix'>
<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
<colgroup>
	<col width='15%' />
	<col width='35%' style='padding:0px 0px 0px 10px'/>
	<col width='15%' />
	<col width='35%' style='padding:0px 0px 0px 10px'/>
</colgroup>
<tr>
	<td class='input_box_title'> <b>교환/반품지명  <img src='".$required3_path."'> </b></td>
	<td class='input_box_item'>
		<input type=text class='textbox' name='addr_name' value='".$db->dt[addr_name]."'  style='width:200px' validation='true' title='교환/반품지명'>
	</td>
	<td class='input_box_title'> <b>담당자명  </b></td>
	<td class='input_box_item'>
		<input type=text class='textbox' name='person_name' value='".$db->dt[person_name]."'  title='담당자명'  style='width:200px'>
	</td>
</tr>
<tr>
	<td class='input_box_title'> <b>일반 전화번호  <img src='".$required3_path."'> </b></td>
	<td class='input_box_item'>
		<input type=text name='addr_phone_1' id='addr_phone_1' value='".$addr_phone[0]."' maxlength=3 style='width:20px;'  class='textbox' com_numeric=true validation='true' title='전화'> -
		<input type=text name='addr_phone_2' id='addr_phone_2' value='".$addr_phone[1]."' maxlength=4 style='width:30px;' class='textbox' com_numeric=true validation='true' title='전화'> -
		<input type=text name='addr_phone_3' id='addr_phone_3' value='".$addr_phone[2]."' maxlength=4 style='width:30px;' class='textbox' com_numeric=true validation='true' title='전화'>
	</td>
	<td class='input_box_title'> <b>핸드폰번호  <img src='".$required3_path."'> </b></td>
	<td class='input_box_item'>
		<input type=text name='addr_mobile_1' id='addr_mobile_1' value='".$addr_mobile[0]."' maxlength=3 style='width:20px;'  class='textbox' com_numeric=true validation='true' title='핸드폰'> -
		<input type=text name='addr_mobile_2' id='addr_mobile_2' value='".$addr_mobile[1]."' maxlength=4 style='width:30px;' class='textbox' com_numeric=true validation='true' title='핸드폰'> -
		<input type=text name='addr_mobile_3' id='addr_mobile_3' value='".$addr_mobile[2]."' maxlength=4 style='width:30px;' class='textbox' com_numeric=true validation='true' title='핸드폰'>
	</td>
</tr>
<tr>
<td class='input_box_title'> <b> 교환/반품 주소 <img src='".$required3_path."'> </b></td>
<td class='input_box_item' colspan=3>
	<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
	<div id='input_address_area' ><!--style='display:none;'-->
	<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
		<col width='70px'>
		<col width='*'>
		<tr>
			<td height=26>
				<input type='text' class='textbox' name='com_zip' id='zip_b_1' style='width:60px;' maxlength='15' value='".$db->dt[zip_code]."' readonly>
			</td>
			<td style='padding:1px 0 0 5px;'>
				<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
			</td>
		</tr>
		<tr>
			<td colspan=2 height=26>
				<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[address_1]."' size=50 class='textbox'  style='width:300px'>
			</td>
		</tr>
		<tr>
			<td colspan=2 height=26>
				<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[address_2]."' size=70 class='textbox'  style='width:300px'> (상세주소)
			</td>
		</tr>
		</table>
	</div>
	</td>
</tr>
<tr>
	<td class='input_box_title'> <b>기본 교환/반품지 사용  <img src='".$required3_path."'> </b></td>
	<td class='input_box_item'>
		<input type=radio class='radio' name='basic_addr_use' value='Y' id='basic_addr_use_1'  validation='false' title='기본 교환/반품지 사용' ".CompareReturnValue("Y",$db->dt[basic_addr_use],"checked")."> <label for='basic_addr_use_1'>사용</lable>
		<input type=radio class='radio' name='basic_addr_use' value='N' id='basic_addr_use_2'  validation='false' title='기본 교환/반품지 사용' ".CompareReturnValue("N",$db->dt[basic_addr_use],"checked")."> <label for='basic_addr_use_2'>미사용</lable>
	</td>
	<td class='input_box_title'> <b>코드</b></td>
	<td class='input_box_item'>
		<input type=text class='textbox' name='code' value='".$db->dt[code]."'  validation='false' title='코드'  style='width:200px'>
	</td>
</tr>
</table><br>";

$Contents .= "
<table width='100%' height='80'  cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table><br><br>
</form>";

$Contents .= "
<table width='100%'cellpadding=3 cellspacing=0 border='0'  class=''>
<tr>
	<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=middle><b> 교환 / 반품지 리스트</b><span class=small> &nbsp;&nbsp;&nbsp;<font color=#5B5B5B>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</font> </span></div>")."</td>
</tr>
</table>";

$Contents .= "
<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
<col width='5%'>
<col width='6%'>
<col width='13%'>
<col width='12%'>
<col width=8%>
<col width=10%>
<col width=6%>
<col width=*>
<col width=9%>
<col width=9%>
<tr bgcolor=#efefef align=center height=27>
	<td class='s_td'>번호</td>
	<td class='m_td'>코드</td>
	<td class='m_td'>주소명</td>
	<td class='m_td'>담당자명</td>
	<td class='m_td'>전화번호</td>
	<td class='m_td'>핸드폰번호</td>
	<td class='m_td'>우편번호</td>
	<td class='m_td'>상세주소</td>
	<td class='m_td'>기본주소여부</td>
	<td class='e_td'>관리</td>
</tr>";

if(count($delivery_array) > 0){
	for($i=0;$i<count($delivery_array); $i++){

		$Contents .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td '>".$delivery_array[$i][code]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_name]."</td>
					<td class='list_box_td point' style='padding-left:10px; text-align:left;'>".$delivery_array[$i][person_name]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_phone]."</td>
					<td class='list_box_td'>".$delivery_array[$i][addr_mobile]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][zip_code]."</td>
					<td class='list_box_td '>".$delivery_array[$i][address_1]."<br>".$delivery_array[$i][address_2]."</td>
					<td class='list_box_td list_bg_gray'>".($delivery_array[$i][basic_addr_use])."</td>
					<td class='list_box_td ' nowrap>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .="<a href='delivery.php?addr_ix=".$delivery_array[$i][addr_ix]."&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$Contents .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents .="<a href='delivery.act.php?addr_ix=".$delivery_array[$i][addr_ix]."&act=delete&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents .="<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$Contents .="
					</td>
				</tr>";
	}
	$Contents .=	"</table>";
	$Contents .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$Contents .= "
				<tr height=50><td colspan=10 align=center style='padding-top:10px;'>등록된 촐고지 정보가 없습니다.</td></tr>";
}
$Contents .= "</table>";
$Contents .= "<table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$Contents .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}else{
	$Contents .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}
$Contents .="</table><br>";
$Contents = $Contents;

}

if($info_type == "visit_info"){		//방문수령지 관리
$Contents .= "
<table width='100%' cellpadding=3 cellspacing=0 border='0'  class=''>
<tr>
	<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=middle><b> 방문수령지 관리</b><span class=small> &nbsp;&nbsp;&nbsp;<font color=#5B5B5B>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</font> </span></div>")."</td>
</tr>
</table>";

$Contents .= "
<form name='edit_form' action='delivery.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='iframe_act'><!--target='iframe_act' -->
<input name='act' type='hidden' value='$act'>
<input name='info_type' type='hidden' value='$info_type'>
<input name='company_id' type='hidden' value='".$admininfo[company_id]."'>
<input name='delivery_type' type='hidden' value='V'>
<input name='mall_ix' type='hidden' value='".$admininfo[mall_ix]."'>
<input name='addr_ix' type='hidden' value='$addr_ix'>
<table width='100%' cellpadding=0 cellspacing=0 class='input_table_box' >
<colgroup>
	<col width='15%' />
	<col width='35%' style='padding:0px 0px 0px 10px'/>
	<col width='15%' />
	<col width='35%' style='padding:0px 0px 0px 10px'/>
</colgroup>
<tr>
	<td class='input_box_title'> <b>방문수령지명  <img src='".$required3_path."'> </b></td>
	<td class='input_box_item'>
		<input type=text class='textbox' name='addr_name' value='".$db->dt[addr_name]."'  style='width:200px' validation='true' title='방문수령지명'>
	</td>
	<td class='input_box_title'> <b>담당자명  <img src='".$required3_path."'> </b></td>
	<td class='input_box_item'>
		<input type=text class='textbox' name='person_name' value='".$db->dt[person_name]."'  title='담당자명'  style='width:200px'>
	</td>
</tr>

<tr>
	<td class='input_box_title'> <b>일반 전화번호  <img src='".$required3_path."'> </b></td>
	<td class='input_box_item'>
		<input type=text name='addr_phone_1' id='addr_phone_1' value='".$addr_phone[0]."' maxlength=3 style='width:20px'  class='textbox' com_numeric=true validation='true' title='전화'> -
		<input type=text name='addr_phone_2' id='addr_phone_2' value='".$addr_phone[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='전화'> -
		<input type=text name='addr_phone_3' id='addr_phone_3' value='".$addr_phone[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='전화'>
	</td>
	<td class='input_box_title'> <b>핸드폰번호  <img src='".$required3_path."'> </b></td>
	<td class='input_box_item'>
		<input type=text name='addr_mobile_1' id='addr_mobile_1' value='".$addr_mobile[0]."' maxlength=3 style='width:20px'  class='textbox' com_numeric=true validation='true' title='핸드폰'> -
		<input type=text name='addr_mobile_2' id='addr_mobile_2' value='".$addr_mobile[1]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='핸드폰'> -
		<input type=text name='addr_mobile_3' id='addr_mobile_3' value='".$addr_mobile[2]."' maxlength=4 style='width:30px' class='textbox' com_numeric=true validation='true' title='핸드폰'>
	</td>
</tr>
<tr>
<td class='input_box_title'> <b>방문수령지 주소 <img src='".$required3_path."'> </b>    </td>
<td class='input_box_item' colspan=3>
	<!--".($db->dt[com_zip] == "" ? "":"[".$db->dt[com_zip]."]")." ".$db->dt[com_address]." <input type='checkbox' name='change_address' id='change_address' onclick='ChangeAddress(this)'><label for='change_address'>주소변경</label><br>-->
	<div id='input_address_area' ><!--style='display:none;'-->
	<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
		<col width='70px'>
		<col width='*'>
		<tr>
			<td height=26>
				<input type='text' class='textbox' name='com_zip' id='zip_b_1' style='width:60px' maxlength='15' value='".$db->dt[zip_code]."' readonly>
			</td>
			<td style='padding:1px 0 0 5px;'>
				<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('4');\" style='cursor:pointer;'>
			</td>
		</tr>
		<tr>
			<td colspan=2 height=26>
				<input type=text name='com_addr1'  id='addr_b_1' value='".$db->dt[address_1]."' size=50 class='textbox'  style='width:300px'>
			</td>
		</tr>
		<tr>
			<td colspan=2 height=26>
				<input type=text name='com_addr2'  id='addr_b_2'  value='".$db->dt[address_2]."' size=70 class='textbox'  style='width:300px'> (상세주소)
			</td>
		</tr>
		</table>
	</div>
	</td>
</tr>
<tr>
	<td class='input_box_title'> <b>기본 출고지 사용  <img src='".$required3_path."'> </b></td>
	<td class='input_box_item'>
		<input type=radio class='radio' name='basic_addr_use' value='Y' id='basic_addr_use_1'  validation='false' title='기본 출고지 사용' ".CompareReturnValue("Y",$db->dt[basic_addr_use],"checked")."> <label for='basic_addr_use_1'>사용</lable>
		<input type=radio class='radio' name='basic_addr_use' value='N' id='basic_addr_use_2'  validation='false' title='기본 출고지 사용' ".CompareReturnValue("N",$db->dt[basic_addr_use],"checked")."> <label for='basic_addr_use_2'>미사용</lable>
	</td>
	<td class='input_box_title'> <b>코드</b></td>
	<td class='input_box_item'>
		<input type=text class='textbox' name='code' value='".$db->dt[code]."'  validation='false' title='코드'  style='width:200px'>
	</td>
</tr>
</table><br>";

$Contents .= "
<table width='100%'  height='80' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></td></tr>
</table><br><br>
";

$Contents .= "
</form>";

$Contents .= "
<table width='100%' cellpadding=3 cellspacing=0 border='0'  class=''>
<tr>
	<td align='left' colspan=2 style='padding:3px 0px;'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=middle><b> 방문수령지 리스트</b><span class=small> &nbsp;&nbsp;&nbsp;<font color=#5B5B5B>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</font> </span></div>")."</td>
</tr>
</table>";

$Contents .= "
<table width='100%' cellpadding=0 cellspacing=0 class='list_table_box'>
<col width='5%'>
<col width='6%'>
<col width='12%'>
<col width='8%'>
<col width=10%>
<col width=10%>
<col width=8%>
<col width=*>
<col width=8%>
<col width=8%>
<tr bgcolor=#efefef align=center height=27>
	<td class='s_td'>번호</td>
	<td class='m_td'>코드</td>
	<td class='m_td'>주소명</td>
	<td class='m_td'>담당자명</td>
	<td class='m_td'>전화번호</td>
	<td class='m_td'>핸드폰번호</td>
	<td class='m_td'>우편번호</td>
	<td class='m_td'>상세주소</td>
	<td class='m_td'>기본주소여부</td>
	<td class='e_td'>관리</td>
</tr>";

if(count($delivery_array) > 0){
	for($i=0;$i<count($delivery_array); $i++){

		$Contents .="<tr height=32 align=center>
					<td class='list_box_td list_bg_gray'>".($i+1)."</td>
					<td class='list_box_td '>".$delivery_array[$i][code]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_name]."</td>
					<td class='list_box_td point' style='padding-left:10px; text-align:left;'>".$delivery_array[$i][person_name]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][addr_phone]."</td>
					<td class='list_box_td'>".$delivery_array[$i][addr_mobile]."</td>
					<td class='list_box_td list_bg_gray'>".$delivery_array[$i][zip_code]."</td>
					<td class='list_box_td '>".$delivery_array[$i][address_1]."<br>".$delivery_array[$i][address_2]."</td>
					<td class='list_box_td list_bg_gray'>".($delivery_array[$i][basic_addr_use])."</td>
					<td class='list_box_td ' nowrap>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .="<a href='delivery.php?addr_ix=".$delivery_array[$i][addr_ix]."&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}else{
				$Contents .="<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0></a> ";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents .="<a href='delivery.act.php?addr_ix=".$delivery_array[$i][addr_ix]."&act=delete&info_type=".$info_type."'><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}else{
				$Contents .="<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
			}
			$Contents .="
					</td>
				</tr>";
	}
	$Contents .=	"</table>";
	$Contents .=	"<table width='100%' cellpadding=0 cellspacing=0>";
}else{
	$Contents .= "
				<tr height=50><td colspan=10 align=center style='padding-top:10px;'>등록된 촐고지 정보가 없습니다.</td></tr>";
}
$Contents .= "</table>";
$Contents .= "<table>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$Contents .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}else{
	$Contents .= "<tr hegiht=40><td colspan=5>".$str_page_bar."</td><td colspan=1 align=right style='padding-top:10px;'></td></tr>";
}
$Contents .="</table><br>";
$Contents = $Contents;

}


if($info_type == 'seller_setup'){
	$Contents .= "
	<form name='edit_form' action='delivery.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target=''>
	<input name='act' type='hidden' value='".$act."'>
	<input name='seller_minishop_use' type='hidden' value='2'>
	<input type = 'hidden' name='info_type' value='".$info_type."'>
	<input type='hidden' name='company_id' value='".$admininfo[company_id]."'>

	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	<tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>정산 상품 기간설정 <img src='".$required3_path."'></b></td>
	    <td class='input_box_item' colspan='3'>
	     	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
			<col width='20%' />
			<col width='*' />
			<tr bgcolor=#ffffff>
				<td style='height:30px;'>
					<input type='radio' name='account_info' value='1' id='account_info_1' ".($account_info == "1" || $account_info == "" ? 'checked':'')."> <label for='account_info_1'> 기본설정(기간별) 기간설정</label><span class='small blu' style='padding-left:15px;'><b> * 해당내역은 판매자정산관리 > 기간별정산내역 > 정산예정내역에서 확인하실수 있습니다.<b></span>
				</td>
			</tr>
			<tr bgcolor=#ffffff>
				<td class='input_box_item' style='padding-left:20px;'>
					배송 처리상태 <select name='ac_delivery_type' id='ac_delivery_type_1' style='width:80px;'>
					<option value='0'> 선택 </opion>
					<option value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>
					<option value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>
					<option value='".ORDER_STATUS_BUY_FINALIZED."' ".CompareReturnValue(ORDER_STATUS_BUY_FINALIZED,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</option>
					</select> 
					상태 변경후 <select name='ac_expect_date' id='ac_expect_date_1' style='width:50px;'>
					<option value='0'> 선택 </opion>
					";
					for($i=0; $i<=31; $i++){
						$Contents .= "<option value='".$i."' ".CompareReturnValue($i,$ac_expect_date,"selected").">".$i." </option>";
					}
					
			$Contents .= "
					</select> 일 후 정산신청 처리됩니다.
				</td>
			</tr>
			<!--
			<tr bgcolor=#ffffff>
				<td class='input_box_item'>
					<input type='radio' name='account_info' value='2' id='account_info_2' ".CompareReturnValue('2',$account_info,"checked")."> <label for='account_info_2'> 상품별(건별)정산</label>
				</td>
			</tr>
			<tr bgcolor=#ffffff>
				<td class='input_box_item' style='padding-left:20px;'>
					배송 처리상태 <select name='ac_delivery_type' id='ac_delivery_type_2' style='width:80px;'>
					<option value='".ORDER_STATUS_DELIVERY_ING."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_ING,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_ING)."</option>
					<option value='".ORDER_STATUS_DELIVERY_COMPLETE."' ".CompareReturnValue(ORDER_STATUS_DELIVERY_COMPLETE,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_DELIVERY_COMPLETE)."</option>
					<option value='".ORDER_STATUS_BUY_FINALIZED."' ".CompareReturnValue(ORDER_STATUS_BUY_FINALIZED,$ac_delivery_type,"selected").">".getOrderStatus(ORDER_STATUS_BUY_FINALIZED)."</option>
					</select> 
					상태 변경후 <select name='ac_expect_date' id='ac_expect_date_2' style='width:45px;'>
					<option value='0'> 선택 </opion>
					";
					for($i=0; $i<=31; $i++){
						$Contents .= "<option value='".$i."' ".CompareReturnValue($i,$ac_expect_date,"selected").">".$i." </option>";
					}
					
			$Contents .= "
					</select> 일 후 정산신청 처리됩니다.
				</td>
			</tr>-->
			</table>
		</td>
	</tr>

	<tr bgcolor=#ffffff>
	    <td class='input_box_title'> <b>정산 확정일 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item'  colspan='3' >
			<select name='ac_term_div' id='ac_term_div'  style='width:80px;'>
			<option value='0'> 선택 </opion>
			<option value='1' ".CompareReturnValue('1',$ac_term_div,"selected").">월 1 회</option>
			<option value='2' ".CompareReturnValue('2',$ac_term_div,"selected").">월 2 회</option>
			<option value='3' ".CompareReturnValue('3',$ac_term_div,"selected").">매주 1 회</option>
			</select> &nbsp;&nbsp;
			<select name='ac_term_date1' id='ac_term_date1' style='width:50px;'>
			<option value='0'> 선택 </opion>
			";
			for($i=1; $i<=31; $i++){
				$Contents .= "<option value='".$i."' ".CompareReturnValue($i,$ac_term_date1,"selected").">".$i." </option>";
			}
			
	$Contents .= "
			</select> &nbsp;&nbsp;
			<select name='ac_term_date2' id='ac_term_date2' style='width:50px;'>
			<option value='0'> 선택 </opion>
			";
			for($i=1; $i<=31; $i++){
				$Contents .= "<option value='".$i."' ".CompareReturnValue($i,$ac_term_date2,"selected").">".$i." </option>";
			}
			
	$Contents .= "
			</select>
			<select name='ac_term_date1' id='ac_term_date1_week' style='width:70px;display:none;'>
				<option value='0' ".CompareReturnValue('0',$ac_term_date1,"selected")."> 일요일 </opion>
				<option value='1' ".CompareReturnValue('1',$ac_term_date1,"selected")."> 월요일 </opion>
				<option value='2' ".CompareReturnValue('2',$ac_term_date1,"selected")."> 화요일 </opion>
				<option value='3' ".CompareReturnValue('3',$ac_term_date1,"selected")."> 수요일 </opion>
				<option value='4' ".CompareReturnValue('4',$ac_term_date1,"selected")."> 목요일 </opion>
				<option value='5' ".CompareReturnValue('5',$ac_term_date1,"selected")."> 금요일 </opion>
				<option value='6' ".CompareReturnValue('6',$ac_term_date1,"selected")."> 토요일 </opion>
			</select>
			<span class='small blu' style='padding-left:10px'><b> * 정산대상의 내역을 설정한 정산일에 정산하며, 내역은 판매자정산관리 > 기간별정산내역 > 정산대기리스트(셀러합산)에서 확인 하실수 있습니다.</b></span>
		</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>정산 방식 <img src='".$required3_path."'></b>   </td>
		<td class='input_box_item' colspan='3'>
			<table border=0 cellpadding=1 cellspacing=0>
				<tr>
					<td>
						<input type='radio' name='account_type' id='account_type_1' value='1' ".($account_type == "1" || $account_type == "" ? 'checked':'')."> <label for='account_type_1'>판매가 정산방식 ( 판매가에 수수료 적용 후 정산처리됩니다.)</label>
					</td>
				</tr>

				<tr>
					<td>
						<input type='radio' name='account_type' id='account_type_2' value='2' ".CompareReturnValue('2',$account_type,"checked")."> <label for='account_type_2'>매입가 정산방식 ( 공급가로 정산되며, 하단 수수료에 0 이 아닌 숫자를 입력시 그 숫자의 % 만큼 차감 후 정산 처리됩니다.)</label>
					</td>
				</tr>
				<!--
				<tr>
					<td>
						<input type='radio' name='account_type' id='account_type_3' value='3' ".CompareReturnValue('3',$account_type,"checked")."> <label for='account_type_3'>미정산 ( 선 매입으로 본사에 재고가 있으며, 상품등록을 셀러가 진행시에 사용되며, 정산에서 제외됩니다.)</label>
					</td>
				</tr>
				-->
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> <b>정산 지급방식</b> </td>
		<td class='input_box_item' colspan='3'>
		<input type='radio' id='account_method_cash' name='account_method' value='".ORDER_METHOD_CASH."' ".CompareReturnValue(ORDER_METHOD_CASH,$account_method,"checked")." checked><label for='account_method_cash'> 현금</label>
		<!--
		<input type='radio' id='account_method_service' name='account_method' value='".ORDER_METHOD_SAVEPRICE."' ".CompareReturnValue(ORDER_METHOD_SAVEPRICE,$account_method,"checked")."><label for='account_method_service'> 예치금</label>
		-->
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>정산 유형  <img src='".$required3_path."'></b> </td>
		<td class='input_box_item' colspan='3'>
			<div id='account_div' style='float:left;width:230px;position:relative;top:2px;'>
				<input type='radio' id='account_div_c' name='account_div' value='c' ".CompareReturnValue('c',$account_div,"checked")."><label for='account_div_c'> 카테고리별 설정</label>

				<input type='radio' id='account_div_s' name='account_div' value='s' ".CompareReturnValue('s',$account_div,"checked")."><label for='account_div_s'> 셀러별 설정</label>
			</div>

			<div id='account_div_table' style='float:left;'>
				소매 수수료 : 
				<input type='text' id='commission' name='commission' style='width:30px; text-align:center;' value='".$commission."' maxlength='2'><label for='commission'> %</label>&nbsp;&nbsp;&nbsp;
				도매 수수료 : 
				<input type='text' id='wholesale_commission' name='wholesale_commission' style='width:30px; text-align:center;' value='".$wholesale_commission."'  maxlength='2'><label for='wholesale_commission'> %</label>
			</div>
		</td>
	</tr>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>전자계약 <img src='".$required3_path."'></b> </td>
		<td class='input_box_item' colspan='3'>
		전자계약 선택 
		".getContractGroup($contract_group, "onchange=\"loadContract($(this), 'et_ix')\"")."
		".getContract($contract_group, $et_ix,"   ")."
		&nbsp;&nbsp;&nbsp;
		계약서내 수수료율 &nbsp;&nbsp;
		<input type='text' class='textbox numeric' name='electron_contract_commission' style='width:40px;' value='".$econtract_commission."'> %
		</td>
	</tr>
	</table>

	<table width='100%' cellpadding=0 cellspacing=0 border='0'  style='padding-top:20px'>
	<col width='15%' />
	<col width='85%' />
	<!--
	<tr>
		<td align='left' colspan=2 style='padding:3px 0px'> ".colorCirCleBox("#efefef","100%","<div style='padding:5px 5px 5px 15px;'><img src='../image/title_head.gif' align=middle><b> 배송비 부가 정책</b></div>")."</td>
	</tr>-->
	</table>";

$Contents011 .= "
	<table width='100%' cellpadding=0 cellspacing=0 >
		<tr>
			<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
				<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>셀러 판매장려금</b>
			</td>
		</tr>
	</table>";
$Contents011 .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
	<col width='15%'/>
	<col width='85'/>
	<tr bgcolor=#ffffff >
		<td class='input_box_title'> <b>추가 판매장려금 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td class='input_box_item'>
						<input type='radio' id='seller_grant_use_1' name='seller_grant_use' value='1' ".CompareReturnValue("1",$seller_grant_use,"checked")." checked><label for='seller_grant_use_1'> 사용</label>
						<input type='radio' id='seller_grant_use_0' name='seller_grant_use' value='0' ".CompareReturnValue("0",$seller_grant_use,"checked")."><label for='seller_grant_use_0'> 미사용</label>
					</td>
				</tr>
				<tr>
					<td class='input_box_item'>
					매출액 
					<input type='text' id='grant_setup_price' name='grant_setup_price' value='".$grant_setup_price."' style='width:60px' dir='rtl'> 원 이상일 경우 정산시 <input type='text' id='ac_grant_price' name='ac_grant_price' value='".$ac_grant_price."' style='width:50px' dir='rtl'> 원 추가 수수료 정산에서 합니다. (VAT 포함)<br>

					* 매출액 목표가 달성시 매 달성 금액 회수만큼 추가 정산 합니다.<br>
					* 정산 기준으로 매월 31일 까지의 매출을 통계하여 측정됩니다.
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>";

$Contents .= "
	<table width='100%'  height='80' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
		</td>
	</tr>
	</table><br>
</form>";

}

if($id != ""){
$Contents .= "<script type='text/javascript'>
	window.onload = function(){
		deliveryTypeView(".$delivery_policy.");
	}
</script>";
}


$Script = "
<script language='javascript' src='delivery.js'></script>
<script language='JavaScript' >
	//initbox(); kbk
/*
function Content_Input(){
	document.delivery_form.content.value = document.delivery_form.delivery_policy_text.value;
}
*/
function zipcode(type) {
	var zip = window.open('../member/zipcode.php?zip_type='+type,'','width=440,height=300,scrollbars=yes,status=no');
}
function SubmitX(frm){
	if(!CheckFormValue(frm)){
		return false;
	}
	frm.content.value = iView.document.body.innerHTML;
	return true;
}

function loadContract(obj,target) {
	
	var contract_group = obj.find('option:selected').val();
	var form = obj.closest('form').attr('name'); 
	//alert(contract_group);
	$.ajax({ 
		type: 'GET', 
		data: {'act':'getContractList','return_type': 'json',  'contract_group':contract_group},
		url: '../econtract/contract.act.php',  
		dataType: 'json', 
		async: true, 
		beforeSend: function(){  
		},  
		error: function(request,status,error){ 
			alert('code:'+request.status+':: message:'+request.responseText+':: error:'+error);
		},  
		success: function(datas){
			$('select#'+target).find('option').not(':first').remove();
			if(datas != null){
				$.each(datas, function(i, data){ 
						$('select[name='+target+']').append(\"<option value='\"+data.et_ix+\"'>\"+data.contract_title+\"</option>\");
				});  
			}
		} 
	});  
}

$(document).ready(function (){

	$('input[name=account_type]').click(function (){
		var value = $(this).val();
		if(value == '2'){
			$('#wholesale_commission').val('0');
			$('#commission').val('0');
			$('#wholesale_commission').attr('readonly',true);
			$('#commission').attr('readonly',true);

		}else{
			$('#wholesale_commission').attr('readonly',false);
			$('#commission').attr('readonly',false);
		}
	});

	if($('input[name=account_type]:checked').val() == '2'){
		$('#wholesale_commission').val('0');
		$('#commission').val('0');
		$('#wholesale_commission').attr('readonly',true);
		$('#commission').attr('readonly',true);
	}else{
		$('#wholesale_commission').attr('readonly',false);
		$('#commission').attr('readonly',false);
	}

	if($('input[name=account_info]:checked').val() == '1'){

		$('#ac_delivery_type_2').attr('disabled',true);
		$('#ac_expect_date_2').attr('disabled',true);
		
		$('#ac_delivery_type_1').attr('disabled',false);
		$('#ac_expect_date_1').attr('disabled',false);

		//$('#ac_delivery_type_2').val('0');
		//$('#ac_expect_date_2').val('0');

	}else{

		$('#ac_delivery_type_1').attr('disabled',true);
		$('#ac_expect_date_1').attr('disabled',true);

		$('#ac_delivery_type_2').attr('disabled',false);
		$('#ac_expect_date_2').attr('disabled',false);

		//$('#ac_delivery_type_1').val('0');
		//$('#ac_expect_date_1').val('0');
	}

	$('input[name=account_info]').click(function (){
	
		var value = $(this).val();

		if(value == '1'){

			$('#ac_delivery_type_2').attr('disabled',true);
			$('#ac_expect_date_2').attr('disabled',true);
			
			$('#ac_delivery_type_1').attr('disabled',false);
			$('#ac_expect_date_1').attr('disabled',false);

			//$('#ac_term_div').attr('disabled',false);
			//$('#ac_term_date1_week').attr('disabled',false);
			//$('#ac_term_date1').attr('disabled',false);
			//$('#ac_term_date2').attr('disabled',false);

		}else if (value == '2'){

			$('#ac_delivery_type_1').attr('disabled',true);
			$('#ac_expect_date_1').attr('disabled',true);
			$('#ac_delivery_type_2').attr('disabled',false);
			$('#ac_expect_date_2').attr('disabled',false);

			//$('#ac_term_div').attr('disabled',true);
			//$('#ac_term_date1_week').attr('disabled',true);
			//$('#ac_term_date1').attr('disabled',true);
			//$('#ac_term_date2').attr('disabled',true);
		}
	
	});

	$('#ac_term_div').change(function(){
		
		var value = $(this).val();

		if(value == '1'){

			$('#ac_term_date2').css('display','none');
			$('#ac_term_date2').attr('disabled',true);
			
			$('#ac_term_date1_week').css('display','none');
			$('#ac_term_date1_week').attr('disabled',true);
			
			$('#ac_term_date1').css('display','');
			$('#ac_term_date1').attr('disabled',false);

		}else if(value == '2'){
			
			$('#ac_term_date1').css('display','');
			$('#ac_term_date1').attr('disabled',false);

			$('#ac_term_date2').css('display','');
			$('#ac_term_date2').attr('disabled',false);
			
			$('#ac_term_date1_week').css('display','none');
			$('#ac_term_date1_week').attr('disabled',true);
		
		}else if(value == '3'){

			$('#ac_term_date1_week').css('display','');
			$('#ac_term_date1_week').attr('disabled',false);

			$('#ac_term_date1').css('display','none');
			$('#ac_term_date1').attr('disabled',true);

			$('#ac_term_date2').css('display','none');
			$('#ac_term_date2').attr('disabled',true);
		}
	});

	change_term_div();

	$(\"#seller_date\").datepicker({
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	$('input[name=account_div]').click(function (){
		var value = $(this).val();
		if(value == 'c'){
			$('#account_div_table').css('display','none');
		}else{
			$('#account_div_table').css('display','');
		}
	});

	var accrount_div_value = $('input[name=account_div][checked]').val(); 
	if(accrount_div_value == 'c'){
		$('#account_div_table').css('display','none');
	}else{
		$('#account_div_table').css('display','');
	}

});

function change_term_div(){
	
	value = $('#ac_term_div').val();

	if(value == '1'){

		$('#ac_term_date2').css('display','none');
		$('#ac_term_date2').attr('disabled',true);
		
		$('#ac_term_date1_week').css('display','none');
		$('#ac_term_date1_week').attr('disabled',true);
		
		$('#ac_term_date1').css('display','');
		$('#ac_term_date1').attr('disabled',false);

	}else if(value == '2'){
		
		$('#ac_term_date1').css('display','');
		$('#ac_term_date1').attr('disabled',false);

		$('#ac_term_date2').css('display','');
		$('#ac_term_date2').attr('disabled',false);
		
		$('#ac_term_date1_week').css('display','none');
		$('#ac_term_date1_week').attr('disabled',true);
	
	}else if(value == '3'){

		$('#ac_term_date1_week').css('display','');
		$('#ac_term_date1_week').attr('disabled',false);

		$('#ac_term_date1').css('display','none');
		$('#ac_term_date1').attr('disabled',true);

		$('#ac_term_date2').css('display','none');
		$('#ac_term_date2').attr('disabled',true);
	}

}

function addTemplate(obj){
	var value = $(obj).val();
	var name = $(obj).find('option:selected').attr('name');
	var add_str = '';
	
	if($('.addTemplateDiv').find('input[value='+value+']').length == 0){
		$('.addTemplateDiv').append(\"<tr height=30><td width=5><input type=radio validation='true' id='\"+value+\"' name='rep' value='\"+value+\"' title='대표 템플릿'><input type=hidden name='dt_ix[]' value='\"+value+\"'><label for='\"+value+\"'>\"+name+\"</label></td><td></td><td></td><td width=100 onClick='$(this).parent().remove();'><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' style='cursor:pointer'></td></tr>\");
	}
}

function addTemplate2(value, name){
	var add_str = '';

	$('#selectTemplate').append('<option value=\"'+value+'\" name=\"'+name+'\">'+name+'</option>');
}

function CheckFormCustom(obj){
	if($('input[name=name]').val() == ''){
		alert('그룹명을 입력해주세요');
		return false;
	}

	if($('input[name^=dt_ix]').length < 2){
		alert('배송정책 템플릿은 2개이상 선택해주세요');
		return false;
	}

	if($('input[name=rep]:checked').length == 0){
		alert('대표 템플릿을 선택해주세요');
		return false;
	}
	return true;
}

function group_delete(g_ix){
	if(confirm('그룹을 삭제하시겠습니까?')){
		$.ajax({
			url : 'delivery.act.php?act=group_delete&g_ix='+g_ix,
			type : 'GET',
			dataType: 'text',
			success: function(result){
				if(result == 'Y'){
					alert('정상적으로 처리되었습니다');
					top.location.href='./delivery.php?info_type=delivery_group';
				}else{
					alert('삭제를 실패하였습니다');
				}
			},
			error: function(error){
				alert('그룹 삭제 실패 '+error);
			}
		});
	}else{
		return false;
	}
}
</script>
";
$P = new LayOut();
$P->addScript = $Script;
if($info_type == 'shipping_info'){	//웹에디터 때문에 분리
	$P->OnloadFunction = "";
}else{
	$P->OnloadFunction = "";
}
$P->strLeftMenu = store_menu();
$P->Navigation = "상점관리 > 쇼핑몰 환경설정 > 배송/택배정책";
$P->title = "배송/택배정책";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function loadDeliveryTemplate($g_ix=""){
	global $db, $admininfo;
	$sql = "select t.dt_ix, t.template_name from shop_delivery_template t left join shop_delivery_relation r on r.dt_ix=t.dt_ix where company_id='".$admininfo[company_id]."' and (r.dt_ix is null or r.dt_ix = '') and delivery_package='N'";
	$db->query($sql);
	$return = $db->fetchall("object");
	return $return;
}

function loadDeliveryGroup(){
	global $db, $admininfo;
	$sql = "select * from shop_delivery_group";
	$db->query($sql);
	$return = $db->fetchall("object");
	return $return;
}
function checkGroupDt_ix($dt_ix){
	global $db;
	$sql = "select * from shop_delivery_relation where dt_ix='".$dt_ix."'";
	$db->query($sql);
	if($db->total > 0){
		return "Y";
	}else{
		return "N";
	}
}
?>