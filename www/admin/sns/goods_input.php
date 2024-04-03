<?
include("../class/layout.class");
include_once($_SERVER['DOCUMENT_ROOT'].'/include/sns.config.php');
$goods_input_type = "social";

$db = new Database;

$db->query("select spei.*, sp.product_type from ".TBL_SNS_PRODUCT." sp INNER JOIN  ".TBL_SNS_PRODUCT_ETCINFO." spei where spei.pid = '".$id."' and sp.id = spei.pid ");
if($db->total != 0)
{
	$db->fetch(0);
	extract($db->dt);
}else{
	$disp = "1";
	$min_local_type = 3;
	$act = "insert";

	$surtax_yorn = "N";

	// sns 관련 값
	$spei_couponSDate = mktime(0,0,0,date('m'),date('d'),date('Y'));
	$spei_couponEDate = mktime(23,59,59,date('m'),date('d')+1,date('Y'));
	$spei_sDate = mktime(0,0,0,date('m'),date('d'),date('Y'));
	$spei_eDate = mktime(23,59,59,date('m'),date('d')+1,date('Y'));
	$spei_dispDate = 'Y';
	$spei_dispStock = 'Y';
	$spei_dispathPoint = 'O';
	$spei_targetNumber = 0;
	$spei_addSaleCount = 0;
	$spei_addSaleMaxCount = 0;
	$spei_buyLimitMin = 1;
	$spei_buyLimitMax = 0;
	$spei_discountRate = 0;
	$spei_dispDiscountRate = 'Y';
}
// SNS 추가정보
$SocailAddContents .= "
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;top:-1px;'><b> SNS 추가정보</b></div>")."</td></tr></table>
			<table cellpadding=0 cellspacing=0 bgcolor=silver border=0 width='100%' style='table-layout:fixed;' class='input_table_box'>
				<col width=15%>
				<col width=35%>
				<col width=15%>
				<col width=35%>";
if($product_type == '5'){
$SocailAddContents .= "
				<tr height=40>
					<td class='input_box_title'   width=13% nowrap> <b>쿠폰관리번호 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' colspan=3>
						<table>
						<tr>
							<td>
							<input type='text' id='spei_couponInfo' name='spei_couponInfo' value='{$spei_couponInfo}' maxlength='4' class='textbox' style='width:60px;' validation=true title='쿠폰관리번호' /> <span class='small'>
							</td>
							<td style='padding-left:10px;'>
								<b><!--쿠폰생성시 상품별 구분자로 들어갈 쿠폰번호 4자리를 입력해주세요-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'L')."</b></span>
								<div class='small'><!--* 일괄 발송/배송 일 경우에만 사용가능-->  ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')."</div>
							</td>
						</tr>
						</table>
					</td>
				</tr>";
}
$SocailAddContents .= "
				<tr>
					<td class='input_box_title' width=13% nowrap> <b>진행기간 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' style='padding:5px;' colspan=3>
						<table>
						<tr>
							<td>
								<input type='text' name='spei_sDateYMD' class='textbox2' value='".date('Ymd',$spei_sDate)."' style='width:60px;text-align:center; vertical-align:middle;' id='spei_sDate_datepicker' validation=true title='진행시작일' /> 일
								<select name='spei_sDateH' class='textbox' style='vertical-align:middle;'>";
								$hh = date('H', $spei_sDate);
								$mm = date('i', $spei_sDate);
								for($i = 0; $i < 24; $i++)	{
									$sel = ($i == $hh)	?	' selected':'';
									$SocailAddContents .= "<option value='".str_pad($i, 2, '0', STR_PAD_LEFT)."'{$sel}>".str_pad($i, 2, '0', STR_PAD_LEFT)."</option>";
								}
		$SocailAddContents .= "
								</select> 시
								<select name='spei_sDateM' class='textbox' style='vertical-align:middle;'>";
								for($i = 0; $i < 60; $i++)	{
									$sel = ($i == $mm)	?	' selected':'';
									$SocailAddContents .= "<option value='".str_pad($i, 2, '0', STR_PAD_LEFT)."'{$sel}>".str_pad($i, 2, '0', STR_PAD_LEFT)."</option>";
								}
		$SocailAddContents .= "
								</select> 분
								~
								<input type='text' name='spei_eDateYMD' class='textbox2' value='".date('Ymd',$spei_eDate)."' style='width:60px;text-align:center vertical-align:middle;;' id='spei_eDate_datepicker' validation=true title='진행종료일' /> 일
								<select name='spei_eDateH' class='textbox' style='vertical-align:middle;'>";
								$hh = date('H', $spei_eDate);
								$mm = date('i', $spei_eDate);
								for($i = 0; $i < 24; $i++)	{
									$sel = ($i == $hh)	?	' selected':'';
									$SocailAddContents .= "<option value='".str_pad($i, 2, '0', STR_PAD_LEFT)."'{$sel}>".str_pad($i, 2, '0', STR_PAD_LEFT)."</option>";
								}
		$SocailAddContents .= "
								</select> 시
								<select name='spei_eDateM' class='textbox' style='vertical-align:middle;'>";
								for($i = 0; $i < 60; $i++)	{
									$sel = ($i == $mm)	?	' selected':'';
									$SocailAddContents .= "<option value='".str_pad($i, 2, '0', STR_PAD_LEFT)."'{$sel}>".str_pad($i, 2, '0', STR_PAD_LEFT)."</option>";
								}
		$SocailAddContents .= "
								</select> 분
							</td>
							<td style='line-height:130%;padding-left:20px;' >".getTransDiscription(md5($_SERVER["PHP_SELF"]),'G')."</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' width=13% nowrap> <b>남은시간노출 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' colspan=3>
						<input type='radio' id='spei_dispDateY' name='spei_dispDate' value='Y'".(($spei_dispDate == 'Y')	?	' checked':'')." /> <label for='spei_dispDateY'>사용</label>
						<input type='radio' id='spei_dispDateN' name='spei_dispDate' value='N'".(($spei_dispDate == 'N')	?	' checked':'')." /> <label for='spei_dispDateN'>미사용</label>
						<span class='small'><!--(미사용일때는 화면에 노출하지 않습니다.)-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'M')."</span>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' width=13% nowrap> <b>재고량노출 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' >
						<input type='radio' id='spei_dispStockY' name='spei_dispStock' value='Y'".(($spei_dispStock == 'Y')	?	' checked':'')." /> <label for='spei_dispStockY'>노출</label>
						<input type='radio' id='spei_dispStockN' name='spei_dispStock' value='N'".(($spei_dispStock == 'N')	?	' checked':'')." /> <label for='spei_dispStockN'>숨김</label>
						<span class='small'><!--(메인에 재고 수량 노출 여부)-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'N')."</span>
					</td>
					<td class='input_box_title' width=13% nowrap> <b>최대판매수량 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item'  >
						<input type='text' id='spei_addSaleMaxCount' name='spei_addSaleMaxCount' value='{$spei_addSaleMaxCount}' class='textbox' style='width:60px;' />
						<span class='small'> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'Q')."</span>
					</td>
				</tr>
				<tr id='targetNumber_area' ".($product_type=="21"?"style='display:none;'":"").">
					<td class='input_box_title' width=13% nowrap> <b>구매달성인원 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' style='padding:5px;'  >
					<table>
						<tr>
							<td>
								<input type='text' id='spei_targetNumber' name='spei_targetNumber' class='textbox' style='width:60px;' value='".($spei_targetNumber==""?"0":$spei_targetNumber)."' />
							</td>
							<td style='padding-left:10px;'>
								<span  ><!--명 이상 구매 시 할인가 적용 <b>(0명 일 경우 구매달성 제한 없음)</b>-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'O')."</span>
								
							</td>
						</tr>
						<tr>
							<td></td>
							<td style='padding-left:10px;'><div class='small'><!--* 일괄 발송/배송 일 경우에만 사용가능-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'P')."</div></td>
						</tr>
					</table>
					</td>
					<td class='input_box_title' width=13% nowrap> <b>발송시점 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' >
						<input type='radio' id='spei_dispathPointO' name='spei_dispathPoint' value='O'".(($spei_dispathPoint == 'O')	?	' checked':'')." /> <label for='spei_dispathPointO'>즉시 발송/배송</label>
						<input type='radio' id='spei_dispathPointA' name='spei_dispathPoint' value='A'".(($spei_dispathPoint == 'A')	?	' checked':'')." /> <label for='spei_dispathPointA'>일괄 발송/배송</label>
					</td>
				</tr>
				<tr>
					<td class='input_box_title' width=13% nowrap> <b>판매수량노출설정 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' >
						<input type='text' id='spei_addSaleCount' name='spei_addSaleCount' value='{$spei_addSaleCount}' class='textbox' style='width:60px;' />
						<span class='small'>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'R')."</span>
					</td>
					<td class='input_box_title' width=13% nowrap> <b>구매수량설정 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' style='padding:5px;' >
						최소구매수량 : <input type='text' id='spei_buyLimitMin' name='spei_buyLimitMin' value='{$spei_buyLimitMin}' class='textbox' style='width:60px;' />
						최대구매수량 : <input type='text' id='spei_buyLimitMax' name='spei_buyLimitMax' value='{$spei_buyLimitMax}' class='textbox' style='width:60px;' />
						<span style='padding-top:3px; margin:0px;'><!--(0 일경우 제한 없습니다.)-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'S')."</span>
					</td>
				</tr>";
if(false){
$SocailAddContents .= "
				<tr>
					<td class='input_box_title' width=13% nowrap> <b>SMS 문구 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' style='padding:5px;' colspan=3>
						<div style='float:left;width:140px;'><textarea id='spei_smsMessage' name='spei_smsMessage' style='width:120px;height:100px;'>".$spei_smsMessage."</textarea></div>
						<div style='float:left;width:400px;margin-left:20px;line-height:18px;' class='small'>
							<!--SMS  전송 서비스는 회원 전용 서비스 입니다.<br />
							입력한 SMS <u>내용이 80byte 를 초과 할 경우 여러 개의 문자로 발송</u> 되며<br />
							SMS  충전수량이  남아있지 않은 경우 발송 되지 않습니다.<br />
							현재 잔여 수량 <b>".number_format($sms_cnt)."건</b> 입니다.-->
							".getTransDiscription(md5($_SERVER["PHP_SELF"]),'T', $sms_cnt,"sms_cnt")."
						</div>
					</td>
				</tr>";
}
$SocailAddContents .= "
			</table><br />";

$SocailAddContents .= "<div id='subsciption_input_area' ".(($product_type == '21') ? " style='display:block;' ":"style='display:none;'")." >
			<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;top:-1px;'><b> 서브스크립션 커머스</b></div>")."</td></tr></table>";
/*
$SocailAddContents .= "
			<table cellpadding=0 cellspacing=0 bgcolor=silver border=0 width='100%' style='table-layout:fixed;' class='input_table_box'>
				<col width=15%>
				<col width=35%>
				<col width=15%>
				<col width=35%>
				<tr height=40>
					<td class='input_box_title'  width=13% nowrap> <b>발송회수 설정 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' >
						<input type='text' id='spei_count' name='spei_count' value='{$spei_count}' maxlength='4' class='textbox' style='width:60px;' validation=true title='발송회수' /> 회
					</td>
					<td class='input_box_title' width=13% nowrap> <b>할인율 <img src='".$required3_path."'></b> </td>
					<td class='input_box_item' >
						도매할인율 : <input type='text' id='spei_count' name='spei_count' value='{$spei_count}' maxlength='4' class='textbox' style='width:60px;' validation=true title='도매할인율' /> %
						소매할인율 : <input type='text' id='spei_count' name='spei_count' value='{$spei_count}' maxlength='4' class='textbox' style='width:60px;' validation=true title='소매할인율' /> %
					</td>
				</tr>

			</table><br>
			";
*/
$SocailAddContents .= "
							<!--a onclick=\"CopySubscription('subsciption_input')\" style='cursor:pointer;'><img src='../images/".$_SESSION["admininfo"]["language"]."/btn_display_option_detail_add.gif' border=0 align=absmiddle style='margin:0 0 3px 0;'></a> <br-->
							<table width='50%' cellpadding=5 cellspacing=1 bgcolor=silver id='subsciption_input' class='subsciption_input' opt_idx=0 style='margin-bottom:10px'>
								<col width='30%'>
								<col width='*'>
								<!--tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small>발송회차</td>
									<td bgcolor=\"#efefef\" class=small> 발송예정일 *</td>
									<td bgcolor=\"#efefef\" class=small> 추가할인율 *</td>
									<td bgcolor=\"#efefef\" class=small> 기타설명</td>
								</tr-->";



$sql = "select * from shop_product_subs_senddetail where pid = '".$id."' order by pss_ix asc ";

$db->query($sql);
$subsciptions = $db->fetchall();

//print_r($options);
if($db->total && $id != "" || true){//디스플레이 옵션 수정 kbk 12/06/19
	for($i=0; ($i < count($subsciptions) || $i < 1) ;$i++){

$SocailAddContents .= "
											<tr align='center' bgcolor='#ffffff' depth=1 item=1>
												<td class='input_box_title' id='inning_text' height='30'>												
												".($i+1)."회 발송
												</td>
												<td>
													<ul>
														<li style='float:left;padding:0px 10px;'>
														<input type='hidden' class='pss_ix' name='subsciptions[".$i."][pss_ix]' id='pss_ix'  value='".$subsciptions[$i][pss_ix]."' />
														발송예정일 : <input type=text class='textbox' name='subsciptions[".$i."][due_date]' id='due_date' inputid='due_date' style='width:70px;vertical-align:middle' value='".$subsciptions[$i][due_date]."'>
														</li>
														<!--li style='float:left;padding:0px 10px;'>
														추가 할인율 : <input type=text class='textbox' name='subsciptions[".$i."][add_sale_rate]' id='add_sale_rate' inputid='add_sale_rate' style='width:60px;vertical-align:middle' value='".$subsciptions[$i][add_sale_rate]."'> % 
														</li-->
														<li style='float:left;padding:4px 10px;'>
														<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;display:none;' id='close_btn'  ondblclick=\"if($('#subsciption_input tbody').find('tr[item^=1]').length > 1){RemoveSubscription('subsciption_input',$(this).parent().parent().parent().parent());}else{DisplayDeleteOption($(this).parent().parent());}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'> <a onclick=\"CopySubscription('subsciption_input')\" style='cursor:pointer;'><img src='../images/i_add.gif' border=0 style='margin:0px 0 0px 0;' align=absmiddle></a>
														</li>
													</ul>
												</td>
											</tr>";
	}
}else{ 

}
$SocailAddContents .= "

							</table><br>
							</div>";



$SocailAddContents .= "
							<div id='local_delivery_area' ".(($product_type == '31') ? " style='display:block;' ":"style='display:none;'").">
							<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;top:-1px;'><b> 로컬딜리버리 커머스</b></div>")."</td></tr></table>
							
						
			<table cellpadding=0 cellspacing=0 bgcolor=silver border=0 width='100%' style='table-layout:fixed;' class='input_table_box'>
				<col width=15%>
				<col width=*>
				<tr height=40>
					<td class='input_box_title'  width=13% nowrap> <b>최소 지역단위 <img src='".$required3_path."'></b></td> 
					<td class='input_box_item' >
						<input type='radio' id='min_local_type_1' name='min_local_type' value='1' validation=true title='최소지역단위' onclick=\"$('.sigugun').attr('disabled',true);$('.dong').attr('disabled',true);\" ".($min_local_type == 1 ? "checked ":"")." /><label for='min_local_type_1'>시/도</label>
						<input type='radio' id='min_local_type_2' name='min_local_type' value='2' validation=true title='최소지역단위' onclick=\"$('.sigugun').attr('disabled',false);$('.dong').attr('disabled',true);\" ".($min_local_type == 2 ? "checked ":"")." /><label for='min_local_type_2'>시/구/군</label>
						<input type='radio' id='min_local_type_3' name='min_local_type' value='3' validation=true title='최소지역단위' onclick=\"$('.sigugun').attr('disabled',false);$('.dong').attr('disabled',false);\" ".($min_local_type == 3 ? "checked ":"")." /><label for='min_local_type_3'>동/읍/면</label>
					</td>
				</tr>

			</table><br>
			";
$SocailAddContents .= "
							<table width='100%' cellpadding=5 cellspacing=1 bgcolor=silver id='local_delivery' class='local_delivery' opt_idx=0 style='margin-bottom:10px'>
								<col width='15%'>
								<col width='*'>";
$sql = "select distinct sido from shop_zip ";

$db->query($sql);
$sidos = $db->fetchall();

$sql = "select * from shop_product_localdelivery_detail where pid = '".$id."' order by pld_ix asc ";
//echo $sql;
$db->query($sql);
$local_deliverys = $db->fetchall();

//print_r($options);
if(count($local_deliverys) && $id != "" || true){
	//echo count($local_deliverys);
	for($i=0; ($i < count($local_deliverys) || $i < 1) ;$i++){
	//echo $i."<br>";
$SocailAddContents .= "
											<tr align='center' bgcolor='#ffffff' depth=1 item=1>
												<td class='input_box_title' id='inning_text' height='30'>												
												선택지역 <input type='hidden' class='pld_ix' name='local_deliverys[".$i."][pld_ix]' id='pld_ix'  value='".$local_deliverys[$i][pld_ix]."' /> 
												</td>
												<td>
													<ul>
														<li style='float:left;padding:0px 10px;width:100px;'>
														<select name='local_deliverys[".$i."][sido]' style='width:100px;' class='sido' id='sido_".$i."' onChange=\"loadLocalInfo(this,'local_deliverys[".$i."][sigugun]')\">
															<option value=''>시/도</option>";
											for($j=0;$j < count($sidos);$j++){
						$SocailAddContents .= "	<option value='".$sidos[$j][sido]."' ".($sidos[$j][sido] == $local_deliverys[$i][sido] ? "selected":"").">".$sidos[$j][sido]."</option>";
											}
$SocailAddContents .= "
														</select>
														</li>
														<li style='float:left;padding:0px 10px;width:100px;'>";
if($local_deliverys[$i][sido]){
	$sql = "select distinct sigugun from shop_zip where sido = '".$local_deliverys[$i][sido]."' ";

	$db->query($sql);
	$siguguns = $db->fetchall();
}
$SocailAddContents .= "
														<select name='local_deliverys[".$i."][sigugun]' style='width:100px;'  class='sigugun' id='sigugun_".$i."' onChange=\"loadLocalInfo(this,'local_deliverys[".$i."][dong]')\">
															<option value=''>시/구/군</option>";
											for($j=0;$j < count($siguguns);$j++){
$SocailAddContents .= "<option value='".$siguguns[$j][sigugun]."' ".($siguguns[$j][sigugun] == $local_deliverys[$i][sigugun] ? "selected":"").">".$siguguns[$j][sigugun]."</option>";
											}

$SocailAddContents .= "
														</select>
														</li>
														<li style='float:left;padding:0px 10px;width:100px;'>";
if($local_deliverys[$i][sigugun]){
	$sql = "select distinct dong from shop_zip where sigugun = '".$local_deliverys[$i][sigugun]."' ";

	$db->query($sql);
	$dongs = $db->fetchall();
}
$SocailAddContents .= "
														<select name='local_deliverys[".$i."][dong]' style='width:100px;'  class='dong' id='dong_".$i."' ".($min_local_type != 3 ? "disabled ":"").">
															<option value=''>동/읍/면</option>";
											for($j=0;$j < count($dongs);$j++){
$SocailAddContents .= "<option value='".$dongs[$j][dong]."' ".($dongs[$j][dong] == $local_deliverys[$i][dong] ? "selected":"").">".$dongs[$j][dong]."</option>";
											}

$SocailAddContents .= "
														</select>
														</li>
														<li style='float:left;padding:0px 10px;width:200px;'>
														발송예정일 : <input type=text class='textbox' name='local_deliverys[".$i."][due_date]' id='delivery_due_date".($i>0?"_".$i:"")."'  style='width:70px;vertical-align:middle' value='".$local_deliverys[$i][due_date]."'>";
													if($i>0) {
$SocailAddContents .= "									<script language='javascript'>
														$(function() {
															$(\"#delivery_due_date_".$i."\").datepicker({
																dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
																dateFormat: 'yy-mm-dd',
																buttonImageOnly: true,
																buttonText: '달력',
																onSelect: function(dateText, inst){

																}
															});
														});
														</script>";
													}
$SocailAddContents .= "									</li>
														<li>
														<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;display:none;' id='close_btn'  ondblclick=\"if($('#local_delivery tbody').find('tr[item^=1]').length > 1){RemoveSubscription('local_delivery',$(this).parent().parent().parent().parent());}else{DisplayDeleteOption($(this).parent().parent());}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'> <a onclick=\"CopyLocalDelivery('local_delivery')\" style='cursor:pointer;'><img src='../images/i_add.gif' border=0 style='margin:0px 0 0px 0;' align=absmiddle></a>
														</li>
													</ul>
												</td>
											</tr>";
	}
}
$SocailAddContents .= "

							</table><br>
							</div>";
$AddScript = "
<script language='javascript'>

$(function() {
	$(\"#spei_couponSDate_datepicker\").datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){

		}
	});

	$(\"#spei_couponEDate_datepicker\").datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력'
	});

	$(\"#spei_sDate_datepicker\").datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){

		}
	});

	$(\"#spei_eDate_datepicker\").datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yymmdd',
		buttonImageOnly: true,
		buttonText: '달력'
	});

	$(\"#due_date\").datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){

		}
	});

	$(\"#delivery_due_date\").datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){

		}
	});
});

function RemoveSubscription(tbName, jquery_obj){
	var tbody = $('#' + tbName + ' tbody');  
	jquery_obj.remove();
	tbody.find('img[id^=close_btn]:last').show();
}

 function CopyLocalDelivery(tbName){

	var tbody = $('#' + tbName + ' tbody');  
	var total_rows = tbody.find('tr[depth^=1]').length;  
	var rows = tbody.find('tr[depth^=1]').length;  
  
   if($.browser.msie){
      var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
   }else{
	  var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
   }
	 
	//alert(total_rows); //
	newRow.find('input[id^=pld_ix]').attr('name','local_deliverys['+(total_rows)+'][pld_ix]');
	newRow.find('input[id^=pld_ix]').val('');
	newRow.find('select[id^=sido]').attr('name','local_deliverys['+(total_rows)+'][sido]');
	newRow.find('select[id^=sigugun]').attr('name','local_deliverys['+(total_rows)+'][sigugun]');	
	newRow.find('select[id^=dong]').attr('name','local_deliverys['+(total_rows)+'][dong]');
	newRow.find('select[id^=sido]').change(function(){
		loadLocalInfo(this,'local_deliverys['+total_rows+'][sigugun]');
	});
	newRow.find('select[id^=sido]').get(0).onclick='';
	newRow.find('select[id^=sido]').attr('onclick','');
	newRow.find('select[id^=sigugun]').change(function(){
		loadLocalInfo(this,'local_deliverys['+total_rows+'][dong]');
	});
	newRow.find('select[id^=sigugun]').get(0).onclick='';
	newRow.find('select[id^=sigugun]').attr('onclick','');

	newRow.find('input[id^=delivery_due_date]').attr('id','delivery_due_date_'+(total_rows)+'');
	newRow.find('input[id^=delivery_due_date]').attr('name','local_deliverys['+(total_rows)+'][due_date]');//추가 kbk 13/09/10
	newRow.find('input[id^=delivery_due_date_'+total_rows+']').datepicker('destroy').datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			 

		}
   });

   tbody.find('img[id^=close_btn]').hide();
   newRow.find('img[id^=close_btn]').show(); 
}

function CopySubscription(tbName){

	var tbody = $('#' + tbName + ' tbody');  
	var total_rows = tbody.find('tr[depth^=1]').length;  
	var rows = tbody.find('tr[depth^=1]').length;  
  
   if($.browser.msie){
      var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
   }else{
	  var newRow = tbody.find('tr[depth^=1]:last').clone(true).appendTo(tbody);  
   }
	 
	//alert(total_rows);
	newRow.find('td[id^=inning_text]').html((total_rows+1)+'회 발송');
	newRow.find('input[id^=pss_ix]').attr('name','subsciptions['+(total_rows)+'][pss_ix]');
	newRow.find('input[id^=pss_ix]').val('');
	newRow.find('input[id^=due_date]').attr('name','subsciptions['+(total_rows)+'][due_date]');
	
	newRow.find('input[id^=add_sale_rate]').attr('name','subsciptions['+(total_rows)+'][add_sale_rate]');
	newRow.find('input[id^=dp_desc]').attr('name','subsciptions['+(total_rows)+'][dp_desc]');
	newRow.find('input[id^=dp_etc_desc]').attr('name','subsciptions['+(total_rows)+'][dp_etc_desc]');

	newRow.find('input[id^=due_date]').attr('id','due_date_'+(total_rows)+'');
	newRow.find('input[id^=due_date_'+total_rows+']').datepicker('destroy').datepicker({
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: '달력',
		onSelect: function(dateText, inst){
			 

		}
   });
   tbody.find('img[id^=close_btn]').hide();
   newRow.find('img[id^=close_btn]').show(); 
}



function loadLocalInfo(sel,target) { 
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name; 
	var class_name = sel.getAttribute('class');
	 
	if(sel.selectedIndex != 0) {
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		//window.frames['act']
		window.frames['act'].location.href = '../product/local.load.php?form=' + form + '&trigger=' + encodeURI(trigger) + '&class_name='+ class_name +'&target=' + target;
	}

}


</script>
";

include("../product/goods_input.php");

/*
CREATE TABLE IF NOT EXISTS `shop_product_subs_senddetail` (
  `pss_ix` int(4) unsigned NOT NULL auto_increment COMMENT '인덱스',
  `pid` int(10) unsigned zerofill NOT NULL default '0000000000' COMMENT '상품아이디',
  `due_date` varchar(10) default NULL COMMENT '발송예정일',
  `add_sale_rate` varchar(10) default NULL COMMENT '추가할인율',
  `insert_yn` enum('Y','N') default 'Y' COMMENT '수정시구분값',
  `regdate` datetime default NULL COMMENT '등록일',
  PRIMARY KEY  (`pss_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='상품 추가정보'   ;

CREATE TABLE IF NOT EXISTS `shop_product_localdelivery_detail` (
  `pld_ix` int(4) unsigned NOT NULL auto_increment COMMENT '인덱스',
  `pid` int(10) unsigned zerofill NOT NULL default '0000000000' COMMENT '상품아이디',
  `sido` varchar(100) default NULL COMMENT '발송예정일',
  `sigugun` varchar(100) default NULL COMMENT '추가할인율',
  `dong` varchar(100) default NULL COMMENT '추가할인율',
  `due_date` VARCHAR(10) NOT NULL COMMENT '발송 예정일',
  `regdate` datetime default NULL COMMENT '등록일',
  PRIMARY KEY  (`pld_ix`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='상품 지역배송 상세정보'   ;



*/
?>