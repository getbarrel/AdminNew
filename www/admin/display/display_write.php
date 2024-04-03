<?
///////////////////////////////////////////////////////////////////
//
// 제목 : 전시관리 등록 - 이현우(2013-05-08)
//
///////////////////////////////////////////////////////////////////
include("../class/layout.class");
include_once("../store/md.lib.php");
include_once("../display/display.lib.php");

// 파라미터
$display_ix = $_GET["display_ix"];
$display_div = $_GET["display_div"];
if (!$display_div) $display_div = 1;

$db = new Database;
$sql = "SELECT * FROM ".TBL_SHOP_DISPLAY." sd, ".TBL_SHOP_DISPLAY_DIV." sdd  where sd.div_ix = sdd.div_ix AND sd.display_ix= '$display_ix'";
$db->query($sql);
$db->fetch();

if($db->total){	
	$display_ix				 = $db->dt[display_ix];
	$display_div			 = $db->dt[display_div];
	$disp_title				 = $db->dt[disp_title];
	$display_width		= $db->dt[image_width];
	$display_height		= $db->dt[image_height];
	$display_sdate		= $db->dt[sdate];
	$display_edate		= $db->dt[edate];
	$cid2						= $db->dt[cid];
	$disp						= $db->dt[disp];
	$parent_div_ix			= $db->dt[parent_div_ix];
	$div_ix					= $db->dt[div_ix];
	$goal_amount			= $db->dt[goal_amount];
	$md_id					= $db->dt[md_id];
	$depth					= $db->dt[depth];
	$act = "update";

	if ($depth==0){	// 분류코드가 1depth (0) 이라면
		$parent_div_ix = $div_ix;	// 이렇게 안하면 1depth 만 사용하고 있는 상태에서 선택한 1depth 가 자동 select 가 안됨
	}

	//if ($display_sdate) $sDate = date("Ymd", mktime(0, 0, 0, substr($db->$display_sdate,4,2)  , substr($db->$display_sdate,6,2), substr($db->$display_sdate,0,4)));
	//if ($display_edate) $eDate = date("Ymd",mktime(0, 0, 0, substr($db->$display_edate,4,2)  , substr($db->$display_edate,6,2), substr($db->$display_edate,0,4)));

	if ($display_sdate) {
		$sDate = substr($display_sdate,0,8);
		$FromHH = substr($display_sdate,8,2);
		$FromMI = substr($display_sdate,10,2);
	}
	if ($display_edate){
		$eDate = substr($display_edate,0,8);
		$ToHH = substr($display_edate,8,2);
		$ToMI = substr($display_edate,10,2);
	}

	$startDate = $sDate;
	$endDate = $eDate;

	$_SERVER["REQUEST_URI"] = "/admin/display/display_write.php?display_div=2";

}else{
	$act = "insert";
	$display_use_sdate = "";
	$display_use_edate = "";
	if($admininfo[admin_level] == 9){
		$disp = "1";
	}else{
		$disp = "9";
	}

	$next10day = mktime(0, 0, 0, date("m")  , date("d")+10, date("Y"));

	$sDate = date("Ymd");
	$eDate = date("Ymd",$next10day);

	$startDate = date("Ymd");
	$endDate = date("Ymd",$next10day);
}

$Script = "
<style type='text/css'>
  div#drop_relation_product { width:100%;height:100%;overflow:auto;padding:1px;border:1px solid silver }
  div#drop_relation_product.hover { border:5px dashed #aaa; background:#efefef; }
  table.tb {width:100%;cursor:move;}
</style>
<script language='javascript' src='display_write.js'></script>
<script type='text/javascript' src='/admin/js/ms_productSearch.js'></script>
<script type='text/javascript' src='relationAjaxForEvent.js'></script>
<script type='text/javascript' src='display.js'></script>
<script language='javascript'>
 function loadCategory(sel,target) {
	if (target != 'cid2'){
		var trigger = sel.options[sel.selectedIndex].value;
		var form = sel.form.name;

		var depth = $('select[name='+sel.name+']').attr('depth');
		window.frames['act'].location.href = '../product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}

}

$(function() {
			$(\"#start_datepicker\").datepicker({
			//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			//showMonthAfterYear:true,
			dateFormat: 'yymmdd',
			buttonImageOnly: true,
			buttonText: '달력',
			onSelect: function(dateText, inst){
				//alert(dateText);
				if($('#end_datepicker').val() != '' && $('#end_datepicker').val() <= dateText){
					$('#end_datepicker').val(dateText);
				//}else{
					//$('#end_datepicker').datepicker('setDate','+0d');
				}
			}

			});

			$(\"#end_datepicker\").datepicker({
			//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
			dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
			//showMonthAfterYear:true,
			dateFormat: 'yymmdd',
			buttonImageOnly: true,
			buttonText: '달력'

			});

			//$('#end_timepicker').timepicker();
		});



function select_date(FromDate,ToDate,dType) {
	var frm = document.serchform;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

// 전시 저장
function FnDisplaySave(frm){
	for(i=0;i < frm.elements.length;i++){
		if(!CheckForm(frm.elements[i])){
			return false;
		}
	}
	//doToggleText(frm);
	return true;
}
function category_del(group_code, el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('objCategory_'+group_code);
	obj.deleteRow(idx);
	var cObj=\$('input[name=basic]');
	if(cObj.length == null){
		//cObj[0].checked = true; // 0이 나오지 null이 나오지 않음 kbk
	}else{
		for(var i=0;i<cObj.length;i++){
			if(cObj[i].checked){
				return true;
				break;
			}else{
				cObj[0].checked = true;
			}
		}
	}
	//cate.splice(idx,1);
}

 </script>";

$mstring ="
<iframe name='iframe_act_thispage' id='iframe_act_thispage' width=700 height=200 frameborder=0 style='display:none;'></iframe>
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<form name=event_frm style='display:inline;' enctype='multipart/form-data' onchange='return FnDisplaySave(this)' action='display.act.php' method='post'>
	<input type='hidden' name='cid2' value=''>
	<input type='hidden' name='depth' value=''>
	<input type='hidden' name=act value='$act'>
	<input type='hidden' name=display_ix value='$display_ix'>
	<input type='hidden' name=display_div value='$display_div'>
		<table cellpadding=5 cellspacing=0 width=100% border=0 align=center style='' bgcolor='#F5F6F5'>		
		<tr>
			<td>
				<table cellpadding=0 cellspacing=1 border=0 width=100% align='center' class='input_table_box'>
					<col width=15%>
					<col width=85%>					
					";
					// getCategoryList3 함수는 /admin/include/admin.util.php 에 존재
if ($display_div == "2"){
	$mstring.="
					<tr>
						<td class='search_box_title' >  카테고리선택</td>
						<td class='search_box_item'>
							<table border=0 cellpadding=0 cellspacing=0>
								<tr>
									<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid2)."</td>
									<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid2)."</td>
									<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid2)."</td>
																		<td>".getCategoryList3("상세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='상세분류'", 3, $cid2)."</td>
								</tr>
							</table>
						</td>
					</tr>
					";
}
	$mstring.="
					<tr>
						<td class='search_box_title' >  전시분류</td>
						<td class='search_box_item'>".makeDisplayDivSelectBox($db, $display_div, 'srch_div', $parent_div_ix, 0,  '1차분류', 'onChange="loadDivix(this.form,\'div_ix\', this.value)"')."&nbsp;".makeDisplayDivSelectBox($db,$display_div,'div_ix', $div_ix, 1,  $display_name = '2차분류', $onchange='')."</td>
					</tr>
					<tr>
						<td class='search_box_title' >  상품전시명</td>
						<td class='search_box_item'><input type='text' class='textbox' name='disp_title' value='".$disp_title."'></td>
					</tr>
					<tr>
						<td class='search_box_title'>상품노출기간</td>
						  <td class='search_box_item'  >
							<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
								<col width=70>
								<col width=120>
								<col width=20>
								<col width=70>
								<col width=120>
								<col width=*>
								<tr>
									<td nowrap>
									<input type='text' name='sdate' class='textbox' value='".$sDate."' style='height:18px;width:70px;text-align:center;' id='start_datepicker'>
									</td>
									<td nowrap>
									<SELECT name=FromHH>";
													for($i=0;$i < 24;$i++){
									$mstring.= "<option value='".$i."' ".($FromHH == $i ? "selected":"").">".$i."</option>";
													}
									$mstring.= "
													</SELECT> 시
													<SELECT name=FromMI>";
													for($i=0;$i < 60;$i++){
									$mstring.= "<option value='".$i."' ".($FromMI == $i ? "selected":"").">".$i."</option>";
													}
									$mstring.= "
													</SELECT> 분
																	</td>
																	<td align=center> ~ </td>
																	<td nowrap>
																	<input type='text' name='edate' class='textbox' value='".$eDate."' style='height:18px;width:70px;text-align:center;' id='end_datepicker'>
																	</td>
																	<td nowrap>
																	<SELECT name=ToHH>";
													for($i=0;$i < 24;$i++){
									$mstring.= "<option value='".$i."' ".($ToHH == $i ? "selected":"").">".$i."</option>";
													}
									$mstring.= "
													</SELECT> 시
													<SELECT name=ToMI>";
													for($i=0;$i < 60;$i++){
									$mstring.= "<option value='".$i."' ".($ToMI == $i ? "selected":"").">".$i."</option>";
													}
									$mstring.= "
													</SELECT> 분
									</td>
									<td style='padding:0px 10px'>
										<a href=\"javascript:select_date('$today','$today',1);\"><img src='../images/".$admininfo[language]."/btn_today.gif'></a>
										<a href=\"javascript:select_date('$today','$voneweeklater',1);\"><img src='../images/".$admininfo[language]."/btn_1week.gif'></a>
										<a href=\"javascript:select_date('$today','$v15later',1);\"><img src='../images/".$admininfo[language]."/btn_15days.gif'></a>
										<a href=\"javascript:select_date('$today','$vonemonthlater',1);\"><img src='../images/".$admininfo[language]."/btn_1month.gif'></a>
										<a href=\"javascript:select_date('$today','$v2monthlater',1);\"><img src='../images/".$admininfo[language]."/btn_2months.gif'></a>
										<a href=\"javascript:select_date('$today','$v3monthlater',1);\"><img src='../images/".$admininfo[language]."/btn_3months.gif'></a>
									</td>
								</tr>
							</table>
						  </td>
					</tr>		
					<tr height=27>
						<td class='search_box_title' nowrap> <b>상품이미지 사이즈</b></td>
						<td class='search_box_item' colspan=1 style='padding:5px;'>
							가로 : <input class='textbox number' type='text' name='image_width' size=5 value='".$display_width."' maxlength='50' >
							세로 : <input class='textbox number' type='text' name='image_height' size=5 value='".$display_height."' maxlength='50' ><br>
							<div class=small style='padding:4px 0px '>
							정보가 입력되지 않으면 기본 이미지 사이즈가 노출됩니다. <br>
							한쪽 사이즈만 입력되면 입력되지 않은 부분은 비율적으로 표시되게 됩니다.<br>
							</div>
							</td>
					</tr>
					<tr>
						<td class='search_box_title' >  담당 MD</td>
						<td class='search_box_item'> ".makeMDSelectBox($db,'md_id',$md_id,'')."</td>
					</tr>
					<tr>
						<td class='search_box_title' >  매출목표</td>
						<td class='search_box_item'><input class='textbox number' type='text' name='goal_amount' size=15 value='".$goal_amount."' maxlength='25'> 원</td>
					</tr>
					<tr>
						<td class='search_box_title' >  사용유무</td>
						<td class='search_box_item'>".makeRadioTag($arr_display_disp, "disp", $disp)."</td>
					</tr>										
				</table>
			</td>
		</tr>

		
		 <tr>
                      <td   style='padding:10px;' id='group_area_parent'>";
$gdb = new Database;
//$gdb->query("SELECT * FROM ".TBL_SHOP_DISPLAY_PRODUCT_GROUP." where display_ix= '$display_ix'");
$gdb->query("SELECT * FROM ".TBL_SHOP_DISPLAY_PRODUCT_GROUP." where display_ix= '$display_ix' order by group_code asc");//수현대리 수정 kbk 12/03/13
if($gdb->total){
	$group_total = $gdb->total-1;
	for($i=0;$i < $gdb->total;$i++){
	$gdb->fetch($i);
$mstring .= "
                      <div id='group_info_area".$i."' group_code='".($i+1)."'>
                      <div style='padding:10px 10px;width:100%;'><img src='/admin/images/dot_org.gif' > <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>상품그룹  (GROUP ".($i+1).")</b> <a onclick=\"add_table()\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle style='position:relative;top:-2px;cursor:pointer;'></a> ".($i == 0 ? "<!--삭제버튼-->":"<a onClick=\"del_table('group_info_area".$i."','".($i+1)."');\"><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle style='position:relative;top:-2px;'></a>")."</div>
                      <table width='100%' border='0' cellpadding='10' cellspacing='1' class='input_table_box'>
						<col width='15%'>
						<col width='*'>
						<tr >
						  <td class='input_box_title'> <b>상품그룹명</b></td>
						  <td class='input_box_item'>
						  <input type='text' class='textbox' name='group_name[".($i+1)."]' id='group_name_".($i+1)."' size=50 value='".$gdb->dt[group_name]."'>
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>상품그룹 전시여부</b></td>
						  <td class='input_box_item'>
						  <input type='radio' class='textbox' name='use_yn[".($i+1)."]' id='use_".($i+1)."_y' size=50 value='Y' style='border:0px;' ".($gdb->dt[use_yn] == "Y" ? "checked":"")."><label for='use_".($i+1)."_y'>전시</label>
						  <input type='radio' class='textbox' name='use_yn[".($i+1)."]' id='use_".($i+1)."_n' size=50 value='N' style='border:0px;' ".($gdb->dt[use_yn] == "N" ? "checked":"")."><label for='use_".($i+1)."_n'>전시 하지 않음</label>
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>상품그룹 이미지</b></td>
						  <td class='input_box_item' style='padding:10px 5px;'>
						  <input type='file' class='textbox' name='group_img[".($i+1)."]' id='group_img' size=50 value=''><br>
						  <div style='padding:5px;' id='group_img_area_".($i+1)."'>";
if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/event/$display_ix/event_group_".($i+1).".gif")){
	$mstring .= "<img src='".$admin_config[mall_data_root]."/images/event/$display_ix/event_group_".($i+1).".gif'>";
}

$mstring .= "			   </div><br>
						  <span class=small><!--* 이미지 등록을 하지 않을경우 상품그룹명이 노출됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>전시타입</b></td>
						  <td class='input_box_item1' style='float:left: width:100%; padding:10px 5px;' >
						  <div style='float:left;text-align:center;width:130px;padding:7px 0px;'>
						  <img src='../images/".$admininfo["language"]."/g_5.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_0').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_0' value='0' style='border:0px;' ".($gdb->dt[display_type] == "0" ? "checked":"")."><label for='display_type_".($i+1)."_0'>기본형(5EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:130px;padding:7px 0px;'>
						  <img src='../images/".$admininfo["language"]."/g_4.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_1').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_1' value='1' style='border:0px;' ".($gdb->dt[display_type] == "1" ? "checked":"")."><label for='display_type_".($i+1)."_1'>기본형(4EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:130px;padding:7px 0px;'>
						  <img src='../images/".$admininfo["language"]."/g_3.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_2').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_2' value='2' style='border:0px;' ".($gdb->dt[display_type] == "2" ? "checked":"")."><label for='display_type_".($i+1)."_2'>기본형2(3EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:145px;padding:7px 0px;'>
						  <img src='../images/".$admininfo["language"]."/slide_4.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_3').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_3' value='3' style='border:0px;' ".($gdb->dt[display_type] == "3" ? "checked":"")."><label for='display_type_".($i+1)."_3'>슬라이드형(4EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:135px;padding:7px 0px;'>
						  <img src='../images/".$admininfo["language"]."/g_16.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_4').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_4' value='4' style='border:0px;' ".($gdb->dt[display_type] == "4" ? "checked":"")."><label for='display_type_".($i+1)."_4'>기본형4(1/*EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:135px;padding:7px 0px;'>
						  <img src='../images/".$admininfo["language"]."/g_17.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_5').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_5' value='5' style='border:0px;' ".($gdb->dt[display_type] == "5" ? "checked":"")."><label for='display_type_".($i+1)."_5'>기본형(4EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:135px;padding:7px 0px;'>
						  <img src='../images/".$admininfo["language"]."/g_24.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_6').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_6' value='6' style='border:0px;' ".($gdb->dt[display_type] == "6" ? "checked":"")."><label for='display_type_".($i+1)."_6'>기본형(2/4EA 배열)</label>
						  </div>
						  ";

//$mstring .= SelectFileList2($DOCUMENT_ROOT.$admin_config[mall_data_root]."/module/event_templet/")."

$mstring .= "
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>전시상품</b><span style='padding-left:2px' class='helpcloud' help_width='300' help_height='30' help_html='자동등록에 경우 사용할 카테고리를 선택하게 되면 상품등록 시 자동으로 신규 상품이 전시됩니다.'><img src='/admin/images/icon_q.gif' /></span></td>
						  <td class='search_box_item' style='padding:10px 10px;'>
						   <div style='padding-bottom:10px;'>
							  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_m' size=50 value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_m'>수동등록</label>
							  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_a' size=50 value='A' style='border:0px;' ".($gdb->dt[goods_display_type] == "A" ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_a'>자동등록</label><br>
						  </div>
						  <div id='goods_manual_area_".($i+1)."' style='".($gdb->dt[goods_display_type] == "M" ? "display:block;":"display:none;")."'>
							  <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,".($i+1).",'productList_".($i+1)."');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
							  <div style='width:100%;padding:5px;' id='group_product_area_".($i+1)."' >".relationEventGroupProductList($gdb->dt[display_ix],$gdb->dt[group_code], "clipart")."</div>
							  <div style='width:100%;float:left;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
							  </div>
						  </div>
						  <div style='padding:0px 0px;".($gdb->dt[goods_display_type] == "A" ? "display:block;":"display:none;")."' id='goods_auto_area_".($i+1)."'>
							<a href=\"javascript:PoPWindow3('category_select.php?mmode=pop&group_code=".($i+1)."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_select_category.gif' border=0 align=absmiddle></a><br>
							<table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
								<col width=100%>
								<tr>
									<td style='padding-top:5px;'>";

											$mstring .= PrintCategoryRelation(($i+1),$div_code);

					$mstring .= "	</td>
								</tr>
								<tr><td style='padding-bottom:5px;'>카테고리 선택하기를 클릭해서 자동 노출하고자 하는 카테고리를 지정 하실 수 있습니다.</td></tr>
							</table>
							<div style='padding:5px 0px;'>
							선택한 카테고리 내의 상품을
							<select name='display_auto_type[".($i+1)."]'>
								<option value='order_cnt' ".($gdb->dt[display_auto_type] == "order_cnt" ? "selected":"").">구매수순</option>
								<option value='view_cnt' ".($gdb->dt[display_auto_type] == "view_cnt" ? "selected":"").">클릭수순</option>
								<option value='sellprice' ".($gdb->dt[display_auto_type] == "sellprice" ? "selected":"").">최저가순</option>
								<option value='regdate' ".($gdb->dt[display_auto_type] == "regdate" ? "selected":"").">최근등록순</option>
							</select>
							으로 노출 합니다.
							</div>
							</div>
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>담당 MD</b></td>
						  <td class='input_box_item'>
						 ".makeMDSelectBox($db,'arr_md_id['.($i+1).']',$gdb->dt[md_id],'')."
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>매출목표</b></td>
						  <td class='input_box_item'>
						  <input type='text' class='textbox' name='arr_goal_amount[".($i+1)."]' id='arr_goal_amount_".($i+1)."' size=15 value='".$gdb->dt[goal_amount]."'>
						  </td>
						</tr>
					  </table><br><br>
					  </div>";
	}
}else{
	$group_total = 0;
$mstring .= "       <div id='group_info_area0' group_code='1'>
                       <div style='padding:0px 10px;width:100%;'><img src='/admin/images/dot_org.gif' align=abstop> <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>상품그룹  (GROUP 1)</b> <a onclick=\"add_table()\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle style='position:relative;top:-2px;cursor:pointer;'></a> <!--삭제버튼--></div>
                      <table width='100%' border='0' cellpadding='10' cellspacing='1' class='input_table_box'>
						<col width=140>
						<col width='*'>
						<tr >
						  <td class='input_box_title'> <b>상품그룹명</b></td>
						  <td class='input_box_item'>
						  <input type='text' class='textbox' name='group_name[1]' id='group_name_1' size=50 value='".$gdb->dt[group_name]."'>
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>상품그룹 전시여부</b></td>
						  <td class='input_box_item'>
						  <input type='radio' class='textbox' name='use_yn[1]' id='use_1_y' size=50 value='Y' style='border:0px;' ><label for='use_1_y'>전시</label>
						  <input type='radio' class='textbox' name='use_yn[1]' id='use_1_n' size=50 value='N' style='border:0px;' checked><label for='use_1_n'>전시 하지 않음</label>
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>상품그룹 이미지</b></td>
						  <td class='input_box_item' style='padding:10px 5px;'>
							  <input type='file' class='textbox' name='group_img[1]' id='group_img' size=50 value=''><br>
							  <div style='padding:5px;' id='group_img_area_1'></div>
							  <span class=small><!--* 이미지 등록을 하지 않을경우 상품그룹명이 노출됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</span>
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>전시타입</b></td>
						  <td class='input_box_item' style='padding:10px 5px;'>
						  <div style='float:left;text-align:center;width:130px;height:130px;'>
						  <img src='../images/".$admininfo["language"]."/g_5.gif' align=center onclick=\"document.getElementById('display_type_1_0').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_0' value='0' style='border:0px;' checked><label for='display_type_1_0'>기본형(5EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:130px;height:130px;'>
						  <img src='../images/".$admininfo["language"]."/g_4.gif' align=center onclick=\"document.getElementById('display_type_1_1').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_1' value='1' style='border:0px;' checked><label for='display_type_1_1'>기본형(4EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:130px;height:130px;'>
						  <img src='../images/".$admininfo["language"]."/g_3.gif' align=center onclick=\"document.getElementById('display_type_1_2').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_2' value='2' style='border:0px;' ><label for='display_type_1_2'>기본형2(3EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:140px;height:130px;'>
						  <img src='../images/".$admininfo["language"]."/slide_4.gif' align=center onclick=\"document.getElementById('display_type_1_3').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_3' value='3' style='border:0px;' ><label for='display_type_1_3' class=small>슬라이드형(4EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:140px;height:130px;'>
						  <img src='../images/".$admininfo["language"]."/g_16.gif' align=center onclick=\"document.getElementById('display_type_1_4').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_4' value='4' style='border:0px;' ><label for='display_type_1_4'>기본형4(1/*EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:140px;height:130px;'>
						  <img src='../images/".$admininfo["language"]."/g_17.gif' align=center onclick=\"document.getElementById('display_type_1_5').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_5' value='5' style='border:0px;' ><label for='display_type_1_5'>기본형(4EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:140px;height:130px;'>
						  <img src='../images/".$admininfo["language"]."/g_24.gif' align=center onclick=\"document.getElementById('display_type_1_6').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_6' value='6' style='border:0px;' ><label for='display_type_1_6'>기본형(2/4EA 배열)</label>
						  </div>
						  ";

//$mstring .= SelectFileList2($DOCUMENT_ROOT.$admin_config[mall_data_root]."/module/event_templet/")."

$mstring .= "
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>상품노출개수</b></td>
						  <td class='input_box_item'>
						  가로 X 세로 <input type='text' class='textbox' name='product_cnt[1]' id='product_cnt_1' size=3 value='".$gdb->dt[product_cnt]."'> 행
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>전시상품</b><span style='padding-left:2px' class='helpcloud' help_width='410' help_height='30' help_html='자동등록에 경우 사용할 카테고리를 선택하게 되면 상품등록 시 자동으로 신규 상품이 전시됩니다.'><img src='/admin/images/icon_q.gif' /></span></td>
						  <td class='search_box_item' style='padding:10px 10px;'>
						   <div style='padding-bottom:10px;'>
							  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_m' size=50 value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_m'>수동등록</label>
							  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_a' size=50 value='A' style='border:0px;' ".($gdb->dt[goods_display_type] == "A" ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_a'>자동등록</label><br>
						  </div>
						  <div id='goods_manual_area_".($i+1)."' style='display:block;'>
							  <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,1,'productList_1');\"><img src='../images/".$admininfo['language']."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
							  <div style='width:100%;padding:5px;' id='group_product_area_1' >".relationEventGroupProductList($div_code, 1, "clipart")."</div>
							  <div style='width:100%;float:left;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')."</span>
							  </div>
						  </div>
						  <div style='padding:0px 0px;display:none;' id='goods_auto_area_".($i+1)."'>
							<a href=\"javascript:PoPWindow3('category_select.php?mmode=pop&group_code=".($i+1)."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_select_category.gif' border=0 align=absmiddle></a><br>
							<table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
								<col width=100%>
								<tr>
									<td>";
										if($id != ""){
											$mstring .= PrintCategoryRelation($id,$div_code);
										}else{
										$mstring .= "<table cellpadding=0 cellspacing=0 id='objCategory_".($i+1)."' >
														<col width=5>
														<col width=30>
														<col width=*>
														<col width=100>
													  </table>";
										}
					$mstring .= "	</td>
								</tr>
								<tr><td>카테고리 선택하기를 클릭해서 자동 노출하고자 하는 카테고리를 지정 하실 수 있습니다.</td></tr>
							</table>
							선택한 카테고리 내의 상품을
							</div>
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>담당 MD</b></td>
						  <td class='input_box_item'>
						 ".makeMDSelectBox($db,'arr_md_id[1]',$md_id,'')."
						  </td>
						</tr>
						<tr >
						  <td class='input_box_title'> <b>매출목표</b></td>
						  <td class='input_box_item'>
						  <input type='text' class='textbox' name='arr_goal_amount[1]' id='arr_goal_amount_1' size=15 value=''>
						  </td>
						</tr>
					  </table><br><br>
					  </div>";
}

$mstring .= "
                      </td>
                    </tr>
					<tr>
						<td>
						<table width=100%  border=0>
						<tr>
							<col width= '*' >
							<col width= '100' >
							<col width= '100' >
							<col width= '100' >
					";
						if($db->dt[display_ix]!="") {
							//$mstring .= "<td align='left'><a href='/event/goods_event.php?display_ix=".$db->dt[display_ix]."' target='_blank' ><img src='../images/".$admininfo["language"]."/btn_promotion_preview.gif' align=absmiddle></a></td> ";
							$mstring .= "<td></td>";
						}else{
							$mstring .= "<td></td>";
						}
						if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C") || checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
							$mstring .= "
									<td><input type='checkbox' name='delete_cache' id='delete_cache' value='Y'><label for='delete_cache'>캐쉬삭제하기</label></td>
									<td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle></td>
									<td><a href='event.list.php'><img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle></a></td>
								 ";
						}
					$mstring .= "
						</tr>
						</table>
						</td>
					</tr>
		 </form>
        ";
$mstring .="</table>
<Script Language='JavaScript'>
my_init($group_total);
</Script>
";
// echo $div_ix;
if ($div_ix){
	$mstring .="
	<SCRIPT LANGUAGE='JavaScript'>loadDivix(document.event_frm, 'div_ix', ".$div_ix.");</SCRIPT>";
}


/*
$help_text = "
<table cellpadding=1 cellspacing=0 class='small' >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' ><b>프로모션 상품 추가</b>를 원하시면 이벤트 추가버튼을 클릭해주세요</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td class='small' >기간이 만료된 프로모션 상품은 자동으로 노출이 종료됩니다</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');
$help_text = HelpBox($__arr_display_div[$display_div]." 전시관리 등록", $help_text);

$Contents = $mstring.$help_text;
$Contents .= "<div style='height:120px;'></div>";

$P = new LayOut();
$P->addScript = $Script;
$P->Navigation = "프로모션/전시 > ".getMenuPath($db,$_SERVER["REQUEST_URI"]);
$P->title = getMenuPath($db,$_SERVER["REQUEST_URI"]);
$P->strLeftMenu = display_menu();
$P->strContents = $Contents;
echo $P->PrintLayOut();

function relationEventGroupProductList($display_ix, $group_code, $disp_type=""){

	global $start,$page, $orderby, $admin_config, $erpid;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$db = new Database;

	$sql = "SELECT *
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.display_ix = '$display_ix' and group_code = '$group_code' and p.disp = 1   ";
	$db->query($sql);
	$total = $db->total;

	$sql = "SELECT *
					FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_DISPLAY_PRODUCT_RELATION." erp
					where p.id = erp.pid and erp.display_ix = '$display_ix' and group_code = '$group_code' and p.disp = 1 order by erp.vieworder asc limit $start,$max";
	//echo $sql."<br><br>";
	$db->query($sql);


	if ($db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>'."\n";
			$mString .= '<script>'."\n";
			$mString .= 'ms_productSearch.groupCode = '.$group_code.";\n";
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				$imgPath = PrintImage($admin_config['mall_data_root'].'/images/product', $db->dt['id'], 'c');
				$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$db->dt['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($db->dt['pname']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['sellprice'].'");'."\n";
			}
			$mString .= '</script>'."\n";
		}
	}
	return $mString;
}

function PrintCategoryRelation($group_code){
	global $db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth, r.cmcr_ix, r.regdate  from ".TBL_SHOP_DISPLAY_CATEGORY_RELATION." r, ".TBL_SHOP_CATEGORY_INFO." c where group_code = '".$group_code."' and c.cid = r.cid ";

	//echo $sql."<br><br>";
	$db->query($sql);

	if ($db->total == 0){
		$mString .= "<table cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."' >
								<col width=5>
								<col width=*>
								<col width=100>
							  </table>";
	}else{
		$i=0;
		$mString = "<table width=100% border=0 cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."'>";
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='category[".$group_code."][]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
				<!--td class='table_td_white small' width='50'><input type='radio' name='basic[".$group_code."]' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."></td-->
				<td class='table_td_white small ' width='*'>".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</td>
				<td class='table_td_white' width='100' align=right><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(".$group_code.",this.parentNode.parentNode)' style='cursor:pointer;' /></td>
				</tr>";
		}
		$mString .= "</table>";
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";

	return $mString;
}
?>