<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");
if($admininfo[admin_level] == ""){
	header("Location:/admin/");
}
if($admininfo[admin_level] < 9){
	header("Location:/admin/seller/");
}

$shmop = new Shared("delay_order_process_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$rule_data = $shmop->getObjectForKey("delay_order_process_rule");
$rule_data = unserialize(urldecode($rule_data));

$Contents01 = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='27%' />
<col width='*' />
	<tr>
		<td align='left' colspan='2''> ".GetTitleNavigation("미처리 알림표시 설정", "주문관리 > 미처리 알림표시 설정 ")."</td>
	</tr>
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>주문 처리 지연 리스트 알림 설정</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:5px;' class='input_table_box'>
	<col width='25%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>입금처리상태</b></td>
		<td class='input_box_item'>
			<table>
				<tr>
					<col width='30px;' />
					<col width='260px;' />
					<col width='*' />
					<td><img src='../images/icon/alarm_danger.gif'></td>
					<td>".rule_checkbox_create("ir_ic_yn",$rule_data["ir_ic_yn"],"입금예정 -> 입금확인")." &nbsp;&nbsp;&nbsp;</td>
					<td>지연일 설정 ".rule_select_create("ir_ic_day",$rule_data["ir_ic_day"])." 일</td>
				</tr>
				<tr>
					<td><img src='../images/icon/alarm_danger.gif'></td>
					<td>".rule_checkbox_create("ca_cc_yn",$rule_data["ca_cc_yn"],"입금취소요청 -> 입금취소확인")." &nbsp;&nbsp;&nbsp;</td>
					<td>지연일 설정 ".rule_select_create("ca_cc_day",$rule_data["ca_cc_day"])." 일</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>배송처리상태</b></td>
		<td class='input_box_item'>
			<table>
				<col width='30px;' />
				<col width='260px;' />
				<col width='*' />
				<tr>
					<td><img src='../images/icon/alarm_danger.gif'></td>
					<td>".rule_checkbox_create("ic_dr_yn",$rule_data["ic_dr_yn"],"입금확인 -> 배송준비중")." &nbsp;&nbsp;&nbsp;</td>
					<td>지연일 설정 ".rule_select_create("ic_dr_day",$rule_data["ic_dr_day"])." 일</td>
				</tr>
				<tr>
					<td><img src='../images/icon/alarm_danger.gif'></td>
					<td>".rule_checkbox_create("dr_di_yn",$rule_data["dr_di_yn"],"배송준비중 -> 배송중")." &nbsp;&nbsp;&nbsp;</td>
					<td>지연일 설정 ".rule_select_create("dr_di_day",$rule_data["dr_di_day"])." 일</td>
				</tr>
				<tr>
					<td><img src='../images/icon/alarm_danger.gif'></td>
					<td>".rule_checkbox_create("di_dc_yn",$rule_data["di_dc_yn"],"배송중 -> 배송완료")." &nbsp;&nbsp;&nbsp;</td>
					<td>지연일 설정 ".rule_select_create("di_dc_day",$rule_data["di_dc_day"])." 일</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>출고처리상태</b></td>
		<td class='input_box_item'>
			<table>
				<col width='30px;' />
				<col width='260px;' />
				<col width='*' />
				<tr>
					<td><img src='../images/icon/alarm_warning.gif'></td>
					<td>".rule_checkbox_create("wda_wdr1_yn",$rule_data["wda_wdr1_yn"],"출고요청 -> 포장대기")." &nbsp;&nbsp;&nbsp;</td>
					<td>지연일 설정 ".rule_select_create("wda_wdr1_day",$rule_data["wda_wdr1_day"])." 일</td>
				</tr>
				<tr>
					<td><img src='../images/icon/alarm_warning.gif'></td>
					<td>".rule_checkbox_create("wdr1_wdr2_yn",$rule_data["wdr1_wdr2_yn"],"포장대기 -> 출고대기")." &nbsp;&nbsp;&nbsp;</td>
					<td>지연일 설정 ".rule_select_create("wdr1_wdr2_day",$rule_data["wdr1_wdr2_day"])." 일</td>
				</tr>
				<tr>
					<td><img src='../images/icon/alarm_warning.gif'></td>
					<td>".rule_checkbox_create("wdr_wdc_yn",$rule_data["wdr_wdc_yn"],"출고대기 -> 출고완료")." &nbsp;&nbsp;&nbsp;</td>
					<td>지연일 설정 ".rule_select_create("wdr_wdc_day",$rule_data["wdr_wdc_day"])." 일</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>교환/반품 클래임 처리상태</b></td>
		<td class='input_box_item'>
			<table>
				<col width='30px;' />
				<col width='260px;' />
				<col width='*' />
				<tr>
					<td><img src='../images/icon/alarm_danger.gif'></td>
					<td>".rule_checkbox_create("era_eri_yn",$rule_data["era_eri_yn"],"교환/반품요청 -> 교환/반품승인")." &nbsp;&nbsp;&nbsp;</td>
					<td>지연일 설정 ".rule_select_create("era_eri_day",$rule_data["era_eri_day"])." 일</td>
				</tr>
				<tr>
					<td><img src='../images/icon/alarm_danger.gif'></td>
					<td>".rule_checkbox_create("eri_erc_yn",$rule_data["eri_erc_yn"],"교환/반품승인 -> 교환/반품 확정/취소")." &nbsp;&nbsp;&nbsp;</td>
					<td>지연일 설정 ".rule_select_create("eri_erc_day",$rule_data["eri_erc_day"])." 일</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;'>
<col width='27%' />
<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>주문상담 처리 지연 리스트 알림 설정</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:5px;' class='input_table_box'>
	<col width='25%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>주문상담내역</b></td>
		<td class='input_box_item'>
			<table>
				<col width='30px;' />
				<col width='260px;' />
				<col width='*' />
				<tr>
					<td><img src='../images/icon/alarm_warning.gif'></td>
					<td>".rule_checkbox_create("omr_omc_yn",$rule_data["omr_omc_yn"],"접수완료 -> 처리완료")." &nbsp;&nbsp;&nbsp;</td>
					<td>지연일 설정 ".rule_select_create("omr_omc_day",$rule_data["omr_omc_day"])." 일</td>
				</tr>
			</table>
		</td>
	</tr>
</table>";

$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff height=70><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";

$Contents = "<form name='edit_form' action='delay_order_process_rule.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>";
$Contents = $Contents."<table width='100%'  border=0>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</table >";
$Contents = $Contents."</form>";

$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td class='small' >알림설정을 사용시 <input type='checkbox' checked onclick=\"$(this).attr('checked',true)\" /> 에 체크를 해주셔야 합니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >시간 계산은 1일은 24시간으로 상태변경시간 기준으로 체크합니다.</td></tr>
</table>
";

$Contents .=  HelpBox("미처리 알림표시 설정", $help_text);

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = order_menu();
$P->Navigation = "주문관리 > 미처리 알림표시 설정";
$P->title = "미처리 알림표시 설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function rule_checkbox_create($name,$value,$text){
	return "<input type='checkbox' name='".$name."' value='Y' id='".$name."' ".($value=="Y" ? "checked" :"")." /><label for='".$name."'> ".$text." </label>";
}

function rule_select_create($name,$value){
	$return = "<select name='".$name."'>";
		for($i=1;$i<6;$i++){
			$return .="<option value='".$i."' ".($i==$value ? "selected" : "" ).">".$i."</option>";
		}
	$return .= "</select>";
	return $return;
}


?>