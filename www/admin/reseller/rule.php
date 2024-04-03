<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");

//[S] 리셀러 데이터 로드
@include ($_SERVER["DOCUMENT_ROOT"]."/admin/reseller/reseller.lib.php");
$reseller_data = resellerShared("select");
//[E] 리셀러 데이터 로드

if($admininfo[admin_level] == ""){
	header("Location:/admin/");
}
if($admininfo[admin_level] < 9){
	header("Location:/admin/seller/");
}

$Contents01 = "
<script language='javascript' src='/admin/reseller/reseller.js'></script>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='27%' />
<col width='*' />
	<tr>
		<td align='left' colspan='2''> ".GetTitleNavigation("리셀러 설정", "리셀러관리 > 환경설정 > 리셀러 설정 ")."</td>
	</tr>
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>리셀러 설정</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='25%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>리셀러 사용여부<img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='rsl_use' id='rsl_use_y'  value='y' ".($reseller_data[rsl_use] == "y" ? "checked":"").">
			<label for='rsl_use_y'>사용</label> 
			<input type='radio' name='rsl_use' id='rsl_use_n' value='n' ".($reseller_data[rsl_use] =="n" || !$reseller_data[rsl_use] ? "checked":"").">
			<label for='rsl_use_n'>사용안함</label>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>지급방식<img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type=radio name='incentive_way' id='incentive_way_1' value='1' ".($reseller_data[incentive_way] == "1" ? "checked":"").">
		<label for='incentive_way_1'>적립금</label>
		<input type=radio name='incentive_way' id='incentive_way_2' value='2' ".($reseller_data[incentive_way] == "2" ? "checked":"").">
		<label for='incentive_way_2'>예치금</label>
		<input type=radio name='incentive_way' id='incentive_way_3' value='3' ".($reseller_data[incentive_way] == "3" || !$reseller_data[incentive_way] ? "checked":"").">
		<label for='incentive_way_3'>현금</label>
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>정산금액<img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<input type='radio' name='pay_method' id='pay_method_1' value='1' ".($reseller_data[pay_method] =="1" ? "checked":"").">
			<label for='pay_method_1'>매출액</label>
			<input type='radio' name='pay_method' id='pay_method_2'  value='2' ".($reseller_data[pay_method] == "2" || !$reseller_data[pay_method] ? "checked":"").">
			<label for='pay_method_2'>실 결제금액(쿠폰 및 적립금차감)</label> 
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>수수료 설정<img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<span>리셀러 지급 수수료</span>
			<input type=text class='textbox' title='리셀러 지급 수수료' onkeyup='incentiveTotal($(this).val(), 100);incentiveCreate();' name='incentive_rate' value='".$reseller_data[incentive_rate]."' style='width:35px;' id='incentive_rate' validation='true'> %
			<span style='margin:0 10px;display:inline-block;'>=</span>
			<span>매니저</span>
			<input type=text class='textbox' title='매니저 수수료' onkeyup='incentiveCreate()' name='incentive_rate_manager' value='".$reseller_data[incentive_rate_manager]."' style='width:35px;' id='incentive_rate_manager' validation='true'> %
			<span style='margin:0 10px;display:inline-block;'>+</span>
			<span>리셀러</span>
			<input type=text class='textbox' title='리셀러 수수료' readonly name='incentive_rate_reseller' value='".$reseller_data[incentive_rate_reseller]."' style='width:35px;background:#eee;' id='incentive_rate_reseller' validation='true'> %
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>정산기준일 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			매월
			<select name='incentive_day' validation='true''>";

				for($i=1; $i<=31; $i++){
					
					$Contents01 .= "
						<option value='".$i."' ".($reseller_data[incentive_day] == $i ? "selected" : "").">".str_pad($i,"2","0",STR_PAD_LEFT)."</option>
					";
				}
			
$Contents01 .= "
			</select>
			일
		</td>
	</tr>
	</table>
	<table width='100%' border='0'>
		<tr>
			<td align='left' style='line-height:120%;'>
				※ <span class='small'> 전체 인센티브 설정은 <b>회원이 리셀러 신청할때 일괄적</b>으로 적용됩니다.</span><br>
				※ <span class='small'> 인센티브 설정은 리셀러에게 인센티브를 어떤 정책으로 줄지 설정해주는 곳입니다.</span><br>
				※ <span class='small'> 신규 가입자 인센티브 & 매출액인센티브 사용 안할시 리셀러 화면에도 자동으로 노출하지 안습니다. </span>
			</td>
			
		</tr>
	</table>
	";

$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff height=70><td colspan=4 align=center><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";


$Contents = "<table width='100%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='rule.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data'><!-- target='act'-->";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr height=10><td></td></tr>";

$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";


$help_text = "
<table cellpadding=2 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td ><img src='/admin/image/icon_list.gif' ></td><td class='small' >신규가입자 인센티브 사용함 : 금액 입력시 즉시 반영 ( 한번 입력된 주민번호/ 1개월간 탈퇴후 가입불가 )</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >매출액의 VAT를 빼고 적용</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >인센티브 현금 지급은 익월의 지급일에 적용 ( 다음달에 지급 )</td></tr>
	<tr><td valign=top></td><td class='small' style='line-height:120%' >적립금 & 예치금은 지급일에 자동 지급 </td></tr>
</table>
";
//$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'D');

$Contents .=  HelpBox("리셀러 설정", $help_text);

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = reseller_menu();
$P->Navigation = "리셀러관리 > 환경설정 >리셀러 설정";
$P->title = "리셀러 설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>