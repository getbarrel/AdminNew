<?
include("../class/layout.class");
include_once("../store/md.lib.php");
include("../webedit/webedit.lib.php");
include ("../basic/company.lib.php");
include("../buyingservice/buying.lib.php");
include("../openapi/demandship/demandship.config.php");
include($_SERVER["DOCUMENT_ROOT"]."/admin/sellertool/sellertool.lib.php");
include($_SERVER["DOCUMENT_ROOT"]."/admin/openapi/openapi.lib.php");

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
$company_id = $admininfo[company_id];		//셀러용

$db = new Database;
$db2 = new Database;
$cdb = new Database;
$mdb = new Database;

$sql = "select 
		*
		from
			common_seller_delivery
		where
			company_id = '".$company_id."'
		";
		//echo nl2br($sql)."<br><br>";
$db->query($sql);
$db->fetch();
$delivery_free_policy = $db->dt[delivery_free_policy];
$delivery_company = $db->dt[delivery_company];
$delivery_product_policy = $db->dt[delivery_product_policy];
$goodsflow_return_yn = $db->dt[goodsflow_return_yn];
$goodsflow_policy_type = $db->dt[goodsflow_policy_type];

$sql = "select 
			*
		from
			shop_delivery_template as dt 
			inner join common_company_detail as ccd on (dt.company_id = ccd.company_id)
		where
			dt.company_id = '".$company_id."'
			order by dt.regdate DESC";

$db->query($sql);
$template_dt_array = $db->fetchall();
$total = count($template_dt_array);

$act = "seller_update";

if($search_text != ""){
	//$str_page_bar = page_bar_search($total, $page, $max);
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&search_type=$search_type&search_text=$search_text&orderby=$orderby&ordertype=$ordertype&list_type=$list_type","view");
}else{
	$str_page_bar = page_bar($total, $page,$max, "&view=innerview&max=$max&orderby=$orderby&ordertype=$ordertype&list_type=$list_type","view");
	//echo $total.":::".$page."::::".$max."<br>";
}

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 >
	<tr>
		<td colspan='4' height='25' style='padding:5px 0px;' bgcolor=#ffffff>
			<img src='../images/dot_org.gif' align=absmiddle> <b class='middle_title'>배송정책 설정</b>
		</td>
	</tr>
	<tr>
	    <td align='left' colspan=4 style='padding-bottom:15px;'>
	    	<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>

							<table id='tab_01' ".($region_type=='d​omestic' || $region_type==''  ? "class='on'" : "")." onclick=\"document.location.href='?region_type=d​omestic'\">
							<tr>
								<th class='box_01'></th>
								<td class='box_02'>국내 배송정책</td>
								<th class='box_03'></th>
							</tr>
							</table>
						
							<table id='tab_02' ".($region_type=='overseas' ? "class='on'" : "")." onclick=\"document.location.href='?region_type=overseas'\">
							<tr>
								<th class='box_01'></th>
								<td class='box_02'>해외 배송정책</td>
								<th class='box_03'></th>
							</tr>
							</table>
							
						</td>
						<td class='btn'>

						</td>
					</tr>
					</table>
				</div>
	    </td>
	</tr>
	</table>";

if($region_type=='overseas'){

	$sql = "select 
				demandship_service_key
			from
				common_seller_delivery
			where
				company_id = '". $company_id ."' limit 1";
	$db->query($sql);
	$template_dt_array = $db->fetchall();
	$total = count($template_dt_array);

	$Contents01 .= "
<form name='edit_form' action='company.act.php' method='post' onsubmit='return sendDelivery(this)' enctype='multipart/form-data' style='display:inline;' target='act'>
<input name='act' type='hidden' value='overseas_update'>
<input name='company_id' type='hidden' value='".$company_id."'>
<input type='hidden' name='delivery_policy' value='6'><!-- 셀러별 개별정책 설정은 마스터만 설정가능 셀러는 셀러업체 배송정책을 사용-->
<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
	<col width='20%' />
	<col width='80' />

	<tr bgcolor=#ffffff height=80>
		<td class='input_box_title'> <b>서비스 안내 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<table border=0 cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td> 
					한국 국내 배송사: 우체국 택배<br>
					말레이시아 국내 배송사: GDEX
					</td>
				</tr>
				<Tr>
					<td>
					DemadnShip은 국제 전자상거래를 위한 국제 물류 서비스를 제공하고 있습니다.
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr bgcolor=#ffffff height=50>
		<td class='input_box_title'> 배송비 요율표</td>
		<td class='input_box_item'  >
			Forbiz Korea 회원분들께는 Gold tier 를 부여해드립니다
		</td>
	</tr>
	<tr bgcolor=#ffffff height=50>
		<td class='input_box_title'> DemandShip 회원가입</td>
		<td class='input_box_item'  >
		DemandShip 서비스를 사용하기 위해서는 회원 가입을 하셔야 합니다.
		<u><a href='https://demandship.com/register' target=_blank>회원가입</a></u>
		</td>
	</tr>
	<!--
	<tr bgcolor=#ffffff height=80>
		<td class='input_box_title'> 아이디</td>
		<td class='input_box_item'   >
		디멘드쉽에서 가입한 아이디를 입력해주세요.<br>
		<input type='text' name='demandship_userid' id='demandship_userid' value='' size='' />
		</td>
	</tr>
	-->
	<tr bgcolor=#ffffff height=80>
		<td class='input_box_title'> Service Key <input type='button' value='키 가져오기' onclick=\"window.open('".constant("DEMANDSHIP_URL")."/oauth/authorize?client_id=".constant('CLIENT_ID')."&response_type=code&redirect_uri=".constant('CALLBACK_URL')."', '', '');\"></td>
		<td class='input_box_item'   >
		<!--아래 버튼을 클릭하여 Service Key를 발급받은 후 위 칸에 입력해주세요<br>-->
		<textarea class=textbox name='service_key' class=textbox style='width:900px;height:50px;margin-top:10px;margin-bottom:10px;' >".($total > 0 ? $template_dt_array[0][demandship_service_key] : '')."</textarea>
		</td>
	</tr>
	<!--
	<tr bgcolor=#ffffff height=50>
		<td class='input_box_title'> <b>묶음 배송정책 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<table border=0 cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td><input type=radio name='delivery_product_policy' value='1' id='delivery_product_policy_1'  ".CompareReturnValue("1",$delivery_product_policy,"checked")."><label for='delivery_product_policy_1'>묶음 배송중 가장 큰 배송비로 배송비 설정(무료 배송 포함시 무료로 설정)</label></td>
				</tr>
				<Tr>
					<td><input type=radio name='delivery_product_policy' value='2' id='delivery_product_policy_2'  ".CompareReturnValue("2",$delivery_product_policy,"checked")."><label for='delivery_product_policy_2'>묶음 배송중 가장 낮은 배송비로 배송비 설정(무료 배송 포함시 무료로 설정)</label></td>
				</tr>
			</table>
		</td>
	</tr>
	-->
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> 택배업체 설정 </td>
		<td class='input_box_item'  >
			<table cellpadding=0 cellspacing=0 border=0 width='640' height='150' style='table-layout:fixed;'>
			<col width='220' />
			<col width='*' />
				<!--tr height=25><td colspan='2'><span>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'V')." </span></td></tr-->
				<tr>
					<td><div id='searchDelieryCompanyArea' style='overflow:auto;height:105px;width:200px;border:1px solid silver;padding:10px;margin:10px 0px;'>".deliveryCompanyList($delivery_company,"overseas_seller_list","",$compnay_id)."</div></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor=#ffffff>
		<td class='input_box_title'> 무게/부피 단위설정 </td>
		<td class='input_box_item'  >
			<table cellpadding=0 cellspacing=0 border=0 width='640' height='150' style='table-layout:fixed;'>
			<col width='220' />
			<col width='*' />
				<!--tr height=25><td colspan='2'><span>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'V')." </span></td></tr-->";

				$Contents01 .= "
				<tr>
					<td>
						<div id='searchDelieryCompanyArea' style='overflow:auto;height:105px;width:200px;border:1px solid silver;padding:10px;margin:10px 0px;'>
							<table width='170' cellpadding=0 cellspacing=0 border='0' align='left'>
							<tr height=23 valign=middle>
								<td align=left><label for='weight_unit_kgcm' />kg/cm</label></td>
								<td align=right><input type='radio' name='unit_info' id='weight_unit_kgcm' value='k' class='delivery_ix' ".($db->dt[unit] == 'k' ? 'checked' : '')." /></td>
							</tr>
							<tr height=23 valign=middle>
								<td align=left><label for='weight_unit_lbin' />lb/in</label></td>
								<td align=right><input type='radio' name='unit_info' id='weight_unit_lbin' value='l' class='delivery_ix' ".($db->dt[unit] == 'l' ? 'checked' : '')." /></td>
							</tr>
							<tr hegiht=1><td colspan=6 background='/admin/image/dot.gif'></td></tr>
							</table>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	
	<table width='100%' height='80' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
		</td>
	</tr>
	</table> 
	</form>";
}else{

$Contents01 .= "
	<form name='edit_form' action='company.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' style='display:inline;' target='act'>
	<input name='act' type='hidden' value='template_update'>
	<input name='info_type' type='hidden' value='$info_type'>
	<input name='company_id' type='hidden' value='".$company_id."'>

	<input type='hidden' name='delivery_policy' value='2'><!-- 셀러별 개별정책 설정은 마스터만 설정가능 셀러는 셀러업체 배송정책을 사용-->
	<table width='100%' cellpadding=3 cellspacing=0 border='0'  class='input_table_box'>
	<col width='20%' />
	<col width='80' />

	<tr bgcolor=#ffffff height=50>
		<td class='input_box_title'> <b>묶음 배송정책 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<table border=0 cellpadding=0 cellspacing=0 width='100%'>
				<tr>
					<td><input type=radio name='delivery_product_policy' value='1' id='delivery_product_policy_1'  ".CompareReturnValue("1",$delivery_product_policy,"checked")."><label for='delivery_product_policy_1'>묶음 배송중 가장 큰 배송비로 배송비 설정(무료 배송 포함시 무료로 설정)</label></td>
				</tr>
				<Tr>
					<td><input type=radio name='delivery_product_policy' value='2' id='delivery_product_policy_2'  ".CompareReturnValue("2",$delivery_product_policy,"checked")."><label for='delivery_product_policy_2'>묶음 배송중 가장 낮은 배송비로 배송비 설정(무료 배송 포함시 무료로 설정)</label></td>
				</tr>
			</table>
		</td>
	</tr>

	<tr bgcolor=#ffffff>
		<td class='input_box_title'> 택배업체 설정 </td>
		<td class='input_box_item'  >
			<table cellpadding=0 cellspacing=0 border=0 width='640' height='150' style='table-layout:fixed;'>
			<col width='220' />
			<col width='*' />
				<!--tr height=25><td colspan='2'><span>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'V')." </span></td></tr-->
				<tr>
					<td><div id='searchDelieryCompanyArea' style='overflow:auto;height:105px;width:200px;border:1px solid silver;padding:10px;margin:10px 0px;' >".deliveryCompanyList($delivery_company,"seller_list","",$compnay_id)."</div></td>
				</tr>
			</table>
		</td>
	</tr>";
    if(isUseGoodsflow()) {

        $OAL = new OpenAPI('goodsflow');
        $goodsflowServiceInfo = $OAL->lib->getPartnerCodeServiceInfo($OAL->lib->getPartnerCode($company_id));

        $Contents01 .= "
       <tr bgcolor=#ffffff>
           <td class='input_box_title'>굿스플로 반품 서비스 사용유무</td>
           <td class='input_box_item' style='padding:5px;'>
                <input type=radio name='goodsflow_return_yn' value='N' id='goodsflow_return_n'  ".CompareReturnValue("N",$goodsflow_return_yn,"checked")." onclick='goodsflowAreaControl()'><label for='goodsflow_return_n'>미사용</label>
                <input type=radio name='goodsflow_return_yn' value='Y' id='goodsflow_return_y'  ".CompareReturnValue("Y",$goodsflow_return_yn,"checked")." onclick='goodsflowAreaControl()'><label for='goodsflow_return_y'>사용</label>
                <span id='goodsflow_policy_type_area' style='display:none;'>
					(
					<input type=radio name='goodsflow_policy_type' value='1' id='goodsflow_policy_type1'  ".CompareReturnValue("1",$goodsflow_policy_type,"checked")." onclick='goodsflowAreaControl()'><label for='goodsflow_policy_type1'>본사서비스사용</label>
					<input type=radio name='goodsflow_policy_type' value='2' id='goodsflow_policy_type2'  ".CompareReturnValue("2",$goodsflow_policy_type,"checked")." onclick='goodsflowAreaControl()'><label for='goodsflow_policy_type2'>개별서비스사용</label>
					)
					</span>
           </td>
       </tr>
       <tr bgcolor=#ffffff id='goodsflow_return_service_area' style='display:none;' >
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
                $Contents01 .= $gsi->centerName. "(".$OAL->lib->getDeliverNameToDeliveryCode($gsi->deliverCode).") - ".$verifiedText." ";
                $Contents01 .= "<input type='button' value='취소' onclick=\"if(confirm('해당 서비스를 승인 취소하시겠습니까?')){window.frames['iframe_act'].location.href ='/admin/openapi/goodsflow/goodsflowServiceCancel.php?company_id=".$company_id."&requestKey=".$gsi->requestKey."';}\" />";
            }
        }
        if($goodsflowServiceCnt == 0){
            $Contents01 .= "<input type='button' value='서비스 등록' onclick='goodsflowServiceRegistPopUp();' />";
            $Contents01 .= "<script>
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
        $Contents01 .= "
				<script>
					function goodsflowAreaControl(){
						if($('#goodsflow_return_y').is(':checked')){
							$('#goodsflow_policy_type_area').show();
						}else{
							$('#goodsflow_policy_type_area').hide();
						}
						if($('#goodsflow_return_y').is(':checked') && $('#goodsflow_policy_type2').is(':checked')){
							$('#goodsflow_return_service_area').show();
						}else{
							$('#goodsflow_return_service_area').hide();
						}
					}
					goodsflowAreaControl();
				</script>
           </td>
       </tr>";
    }
    $Contents01 .= "
	</table>
	
	<table width='100%' height='80' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
		</td>
	</tr>
	</table> 
	</form>";

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<tr>
		<td colspan=8>
			<table border='0' cellpadding='0' cellspacing='0' width='100%'>
				<tr>
					<td style='width:100%;padding:0px 0px 10px 0px' valign=top colspan=3>
						<img src='../images/dot_org.gif' align=absmiddle> <b>배송정책 템플릿 리스트</b> 전체 : ( ".$total." ) 개
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>

	<table width='100%' cellpadding=0 cellspacing=0 border='0' class='list_table_box'>
		<col width='4%'>
		<col width='12%'>
		<col width='12%'>
		<col width='6%'>
		<col width='9%'>
		<col width='6%'>
		<col width='*'>
		<col width='7%'>

		<tr bgcolor=#efefef align=center height=27>
			<td class='s_td'>번호</td>
			<td class='m_td'>배송정책명</td>
			<td class='m_td'>셀러명</td>
			<td class='m_td'>소매/도매</td>
			<td class='m_td'>배송비 결제수단</td>
			<td class='m_td'>배송 방식</td>
			<td class='m_td'>배송비 조건</td>
			<td class='e_td'>관리</td>
		</tr>";

if($total > 0){

		for($j =0;$j<count($template_dt_array);$j++){

			$no = $total - ($page - 1) * $max - $j;

$Contents01 .="
				<tr height=32 align=center>";

				$Contents01 .="
					<td class='list_box_td list_bg_gray'>".$no."</td>
					<td class='list_box_td point' >".$template_dt_array[$j][template_name]."</td>
					<td class='list_box_td point'>
						<a href='#'>".$template_dt_array[$j][com_name]."</a>";
					if($template_dt_array[$j][is_basic_template] == '1'){
						$Contents01 .="<span style='color:red;'><br>(기본배송정책)</span>";
					}
				$Contents01 .="
					</td>
					";
				
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
				}
			
$Contents01 .="
					<td class='list_box_td'>".$product_sell_type."</td>
					<td class='list_box_td'>".$delivery_basic_policy."</td>
					<td class='list_box_td'>".$delivery_package."</td>
					<td class='list_box_td'>".$template_text."</td>";

$Contents01 .="
					<td class='list_box_td' align=center style='padding:0px 5px' nowrap>";

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
			
				$Contents01 .="
					<a href=\"javascript:PoPWindow3('../product/product_delivery_template.php?mmode=pop&dt_ix=".$template_dt_array[$j][dt_ix]."&page_type=seller&company_id=".$company_id."',960,960,'company')\"'>
					<img src='../images/".$_SESSION["admininfo"]["language"]."/btc_modify.gif' align=absmiddle border=0>
					</a>";
			}else{
				$Contents01 .="
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_modify.gif' border=0 align='absmiddle'></a>
					";
			}

			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
				$Contents01 .="
					<a href=\"JavaScript:DeleteTemplate('".$template_dt_array[$j][dt_ix]."','".$template_dt_array[$j][company_id]."','seller')\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle'></a> ";
			}else{
				$Contents01 .="
					<a href=\"".$auth_delete_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle'></a> ";
			}

			$Contents01 .="
					</td>";
			
$Contents01 .="
				</tr>";

		}
	
}else{
	$Contents01 .= "<tr height=50><td colspan=8 align=center style='padding-top:10px;'>등록된 배송정책 템플릿이 없습니다.</td></tr>";
}

$Contents01 .="</table> ";

$Contents01 .="	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
				<tr hegiht=30>
					<td colspan=7 align=left style='padding:10px 0px;' >".$str_page_bar."</td>
					<td align=right>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){
$Contents01 .= "
						<a href=\"javascript:PoPWindow3('../product/product_delivery_template.php?mmode=pop&page_type=seller&company_id=".$company_id."',960,960,'company')\"'>
						<img src='../images/".$admininfo["language"]."/btn_delivery_add.gif' border=0>
						</a>";
}
$Contents01 .= "
					</td>
				</tr>";

 
$Contents01 .="	</table><br>";

}

$Contents01 .= "
<script language='javascript'>

function DeleteTemplate(dt_ix,company_id,page_type){

	if(confirm('배송정책 템플릿을 삭제하시겠습니까?')){
		document.location.href='../product/product_delivery.act.php?act=delete_template&dt_ix='+dt_ix+'&company_id='+company_id+'&page_type='+page_type;
	}
}

</script>
";

if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = seller_menu();
	$P->strContents = $Contents01;
	$P->Navigation = "셀러관리 > 배송정책 관리";
	$P->NaviTitle = "셀러관리";
	echo $P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = $Script;
	$P->strLeftMenu = seller_menu();
	$P->strContents = $Contents01;
	$P->Navigation = "셀러관리 > 배송정책 관리";
	$P->title = "셀러관리";
	echo $P->PrintLayOut();
}
?>
