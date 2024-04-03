<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");
if($admininfo[admin_level] < 9){
	header("Location:/admin/store/company.add.php");
}

if($admininfo[admin_id] == "forbiz"){
	//print_r($admininfo);
//	echo $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
}


$shmop = new Shared("b2b_point_rule");
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$reserve_data = $shmop->getObjectForKey("b2b_point_rule");
$reserve_data = unserialize(urldecode($reserve_data));
//echo md5("wooho".$db->dt[mall_domain].$db->dt[mall_domain_id]);


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
	<col width='15%' />
	<col width='35%' />
	<col width='15%' />
	<col width='35%' />
	  <tr>
	    <td align='left' colspan=4> ".GetTitleNavigation("$menu_name", "본사관리 > $menu_name")."</td>
	  </tr>
	  <tr>
	    <td align='left' colspan=4 style='padding-bottom:20px;'>
	    <div class='tab'>
			<table class='s_org_tab'>
			<col width='550px'>
			<col width='*'>
			<tr>
				<td class='tab'>

					<table id='tab_02' ".($info_type == "b2c_point" || $info_type == "" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents01 .= "<a href='point_rule.php?info_type=b2c_point'>B2C 포인트 설정</a>";

						$Contents01 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>
					<table id='tab_04' ".($info_type == "b2b_point" ? "class='on' ":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' >";

							$Contents01 .= "<a href='b2b_point_rule.php?info_type=b2b_point'>B2B 포인트 설정</a>";

						$Contents01 .= "
						</td>
						<th class='box_03'></th>
					</tr>
					</table>

				</td>
				<td style='text-align:right;vertical-align:bottom;padding:0 0 10px 0'>

				</td>
			</tr>
			</table>
		</div>
	    </td>
	  </tr>
	</table>
";

////////////////////////////////////////////////////////////////////////////

$Contents01 .= "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;'>
<col width='27%' />
<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>포인트 지급 정책</b></div>")."</td>
	</tr>
</table>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<col width='20%' />
	<col width='*' />
	<!--
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>포인트 사용 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='point_use_yn' id='point_use_y' value='Y' ".($reserve_data[point_use_yn] == "Y" || $reserve_data[point_use_yn] == "" ? "checked":"")."> <label for='point_use_y'> 사용 </label>
		<input type='radio' name='point_use_yn' id='point_use_n' value='N' ".($reserve_data[point_use_yn] =="N" ? "checked":"")."> <label for='point_use_n'>미 사용</label>
		</td>
		<td class='input_box_title'> <b>구매기능 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='order_use_yn' id='order_use_y' value='Y' ".($reserve_data[order_use_yn] == "Y" || $reserve_data[order_use_yn] == "" ? "checked":"")."> <label for='order_use_y'> 사용 </label>
		<input type='radio' name='order_use_yn' id='order_use_n' value='N' ".($reserve_data[order_use_yn] =="N" ? "checked":"")."> <label for='order_use_n'>미 사용</label>
		</td>
	</tr>-->
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>포인트 적립 사용 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' colspan='3'>
		<input type='radio' name='point_use_yn' id='point_use_y' value='Y' ".($reserve_data[point_use_yn] == "Y" || $reserve_data[point_use_yn] == "" ? "checked":"")."> <label for='point_use_y'> 사용 </label>
		<input type='radio' name='point_use_yn' id='point_use_n' value='N' ".($reserve_data[point_use_yn] =="N" ? "checked":"")."> <label for='point_use_n'>미사용</label>
		&nbsp;&nbsp;&nbsp;- 미사용을 선택시 모든 적립금 지급이 <font color='red'><b>불가능</b></font>합니다. 
		</td>
	</tr>
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>회원 로그인시 적립 <img src='".$required3_path."'></b></td>
		<td class='input_box_item' colspan='3'>
		<input type='radio' name='point_login_yn' id='point_login_yn_y' value='Y' ".($reserve_data[point_auto_yn] == "Y" ? "checked":"")."><label for='point_login_yn_y'> 사용 </label>
		<input type='radio' name='point_login_yn' id='point_login_yn_n' value='N' ".($reserve_data[point_auto_yn] =="N" ? "checked":"")."> <label for='point_login_yn_n'> 미사용</label>
		&nbsp;&nbsp;&nbsp;
		- 회원 로그인시  <input type=text class='textbox' name='login_point_price' value='".$reserve_data[login_point_price]."' style='width:60px;' validation='false' title='Mileage In-Use제한 설정'> 포인트 지급
		</td>
	</tr>
	</table>";

////////////////////////////////////////////////////////////////////////////
/*
$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
	<col width='20%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>포인트 지급일정 관리</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>지급자동 사용 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='point_auto_yn' id='point_auto_y' value='Y' ".($reserve_data[point_auto_yn] == "Y" ? "checked":"")."><label for='point_auto_y'> 사용 </label>
		<input type='radio' name='point_auto_yn' id='point_auto_n' value='N' ".($reserve_data[point_auto_yn] =="N" ? "checked":"")."> <label for='point_auto_n'>  미사용 * 미사용시 관리자가 직접 수정 처리합니다. </label></td>
	</tr>
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title' rowspan='2'> <b>포인트 적립일 상세설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<table width='100%' cellpadding=4 cellspacing=0 border='0' align='left' style='table-layout:fixed; padding:5px;'>
			<tr>
				<td class='input_box_item'>
					<input type='radio' name='point_add_setup' id='mileage_add_setup' value='S'  ".($reserve_data[point_add_setup] == "S"   ? "checked":"")." > '적립대기일'로 부터 
						<select name='point_complate_time' style='width:80px;'>
							<option value='0'>즉시</option>";
						for($i=1; $i<=30; $i++){
							$Contents01 .= "	<option value='".$i."' ".CompareReturnValue($i,$reserve_data[point_complate_time],"selected").">".$i."일</option>";
						}
			$Contents01 .= "
						</select> (후) '적립완료' 처리 합니다.
				</td>
			</tr>
			</table>
		</td>
	</tr>
</table>";
*/
$Contents01 .= "<!--
<div class='point_use_setup' id='point_use_setup_id'>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;'>
	<col width='20%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>포인트 사용정책</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'> <b>포인트 사용제한 설정 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			<table width='100%' cellpadding=4 cellspacing=0 border='0' align='left' style='table-layout:fixed; padding:5px;'>
			<col width='100%' />
			<tr>
				<td > 
					- 일반 상품 구매 합계액이  <input type=text class='textbox' name='total_order_price' value='".$reserve_data[total_order_price]."' style='width:60px;' validation='false' title='Mileage In-Use제한 설정'> 원 이상 상품 구매시사용 가능(무제한이일 경우 0원입력)
				</td>
			</tr>
			<tr>
				<td>
					- 서비스 상품 구매 합계액이  <input type=text class='textbox' name='service_total_order_price' value='".$reserve_data[service_total_order_price]."' style='width:60px;' validation='false' title='Mileage In-Use제한 설정'> 원 이상 상품 구매시사용 가능(무제한이일 경우 0원 입력)
				</td>
			</tr>
			<tr>
				<td> 
					<span class=blue>* 신규 상품 등록시 기본으로 적용되며, 상품별로 개별 설정시 본 설정은 적용되지 않습니다.".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')." </span>
				<td>
			</tr>
			<tr>
				<td>
					- 보유 적림금이   <input type=text class='textbox' name='min_point_price' value='".$reserve_data[min_point_price]."' style='width:60px;' validation='false' title='Mileage In-Use제한 설정'> 원 이상일때 상품 구매시 사용 가능(제한이 없을경우 0입력)v
				</td>
			</tr>
			<tr>
				<td > 
					<input type=radio name='point_one_use_type'  id='point_one_use_type_1' value='1' ".($reserve_data[point_one_use_type] == "1" ? "checked":"")." > 1회 사용한도 최대  
					<input type=text class='textbox' name='use_point_max' value='".$reserve_data[use_point_max]."' style='width:60px;' id='once_point__one_use_type' ".($reserve_data[point_one_use_type] == "1" ? "validation='false'":"validation='false'")." title='Mileage 1회 In-Use 한도'> 원  까지만 사용 가능 * 0원일 경우 전액적립금 사용 가능 합니다.
				</td>
			</tr>
			<tr>
				<td>
					<input type=radio name='point_one_rate_type' id='point_one_use_type_2' value='2' ".($reserve_data[point_one_rate_type] == "2" ? "checked":"")." > 1회 사용한도 상품 구매 합계액의 <input type=text class='textbox' name='max_goods_sum_rate' value='".$reserve_data[max_goods_sum_rate]."' style='width:60px;' id='max_goods_sum_rate' ".($reserve_data[point_one_rate_type] == "2" ? "validation='false'":"validation='false'")." title='Mileage 1회 In-Use 한도'>%  까지만 사용 가능 * 100%시 전액 적립금 사용 가능합니다.
				</td>
			</tr>
			</table>
		</td>
	</tr>

	
</table>
</div>-->";

////////////////////////////////////////////////////////////////////////////

$Contents01 .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;margin-top:30px;' >
	<col width='20%' />
	<col width='*' />
	<tr>
		<td align='left' colspan='2' style='padding:3px 0px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'> <img src='../image/title_head.gif' align=absmiddle> <b class='blk'>포인트 사용 및 소멸기간 설정</b></div>")."</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' style='table-layout:fixed;' class='input_table_box'>
	<col width='20%' />
	<col width='*' />
	<tr bgcolor='#ffffff'>
		<td class='input_box_title'> <b>포인트 사용순서 <img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
		<input type='radio' name='date_asc' id='date_asc_a' value='A' ".($reserve_data[date_asc] == "A" || $reserve_data[date_asc] == "" ? "checked":"")."><label for='date_asc_a'> 과거 적립일 순 </label>
		<!--input type='radio' name='date_asc' id='date_asc_d' value='D' ".($reserve_data[date_asc] =="D" ? "checked":"")."><label for='date_asc_d'> 최근 적립일 순</label></td-->
	</tr>

	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'> <b>포인트 자동 소멸 기간<img src='".$required3_path."'></b></td>
		<td class='input_box_item'>
			적립일로 부터 &nbsp;&nbsp;
			<select name='cancel_year' style='width:70px;'>
				<option value='0'>0</option>";
				for($i=1; $i<=10; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$reserve_data[cancel_year],"selected").">".$i."</option>";
				}
$Contents01 .= "
			</select> 년  &nbsp;&nbsp;
			<select name='cancel_month' style='width:70px;'>
				<option value='0'>0</option>";
				for($i=1; $i<=12; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$reserve_data[cancel_month],"selected").">".$i."</option>";
				}
$Contents01 .= "
			</select> 개월 지난 미사용 포인트 자동 소멸
		</td>
	</tr>
	
	<tr bgcolor='#ffffff' height='50' style='line-height:150%'>
		<td class='input_box_title'>
			<b>휴면 회원 자동 소멸 기간<img src='".$required3_path."'></b>
		</td>
		<td class='input_box_item'>
			최근 주문일로부터 &nbsp;&nbsp;
			<select name='order_year' style='width:70px;'>
				<option value='0'>0</option>";
				for($i=1; $i<=10; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$reserve_data[order_year],"selected").">".$i."</option>";
				}
$Contents01 .= "
			</select> 년  &nbsp;&nbsp;
			<select name='order_month' style='width:70px;'>
				<option value='0'>0</option>";
				for($i=1; $i<=12; $i++){
					$Contents01 .= "<option value='".$i."' ".CompareReturnValue($i,$reserve_data[order_month],"selected").">".$i."</option>";
				}
$Contents01 .= "
			</select> 개월 지난 회원으로 포인트가 &nbsp;&nbsp;
			<input type='text' name='order_member_point' value='".$reserve_data[order_member_point]."' style='width:70px;'>&nbsp;&nbsp; 미만인 회원은 휴면회원으로 간주하여 회사는 해당 회원의 적립된 포인트를 회수 합니다.
		</td>
	</tr>
</table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
$ButtonString = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
<tr bgcolor=#ffffff ><td colspan=4 align=center style='padding:10px 0px;'><input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:hand;border:0px;' ></td></tr>
</table>
";
}

$Contents = "<table width='100%'  border=0>";
$Contents = $Contents."<form name='edit_form' action='point_rule.act.php' method='post' onsubmit='return CheckFormValue(this)' enctype='multipart/form-data' target='act'>
<input type='hidden' name='act' value='b2b_point'>";
$Contents = $Contents."<tr><td>".$Contents01."<br></td></tr>";
$Contents = $Contents."<tr><td>".$ContentsDesc01."</td></tr>";
$Contents = $Contents."<tr><td>";
$Contents = $Contents.$ButtonString;
$Contents = $Contents."</td></tr>";
$Contents = $Contents."</form>";
$Contents = $Contents."</table >";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

$Contents .=  HelpBox("포인트/포인트 관리", $help_text, 100);
$script = "
<script language='javascript'>
	
	$(document).ready(function(){
		
		$('input[name=order_use_yn]').click(function(){
			var check_value = $(this).val();

			if(check_value == 'Y'){
				
				$('#point_use_setup_id').attr('disabled','false');
				$('#point_use_setup_id').css('display','');
			}else if(check_value == 'N'){
				
				$('#point_use_setup_id').css('disabled','true');
				$('#point_use_setup_id').css('display','none');
			}
		
		});

		var check_use = $('input[name=order_use_yn]:checked').val();

		if(check_use == 'Y'){
				
			$('#point_use_setup_id').attr('disabled','false');
			$('#point_use_setup_id').css('display','');
		}else if(check_use == 'N'){
			
			$('#point_use_setup_id').css('disabled','true');
			$('#point_use_setup_id').css('display','none');
		}
		
	});
</script>

";
$Script = "<script language='javascript' src='basicinfo.js'></script>".$script;
$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = member_menu();
$P->Navigation = "포인트/포인트 관리 > 포인트/포인트설정";
$P->title = "포인트/포인트설정";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>