<?
/*
프로모션프로모션 분류 추가로 인해 이미지명이 중복되어 저장되므로 분류 코드를 이미지명 앞에 붙여서 중복 저장을 막음(기존에 쓰던 방식은 그대로 유지) kbk 13/05/09
*/
include("../class/layout.class");
include("../display/promotion_display.lib.php");
if(!$agent_type){
	$agent_type = "W";
}

if($admininfo[mall_type] == "H"){
	header("Location:../display/popup.list.php");
}


$sql = "SELECT pg_title, pg.pg_ix, pg.mall_ix, pg.div_ix, goods_max, image_width, image_height, pg_use_sdate , pg_use_edate, pg.md_mem_ix, pg.goal_amount, pg.disp, cmd.div_code
			FROM shop_promotion_goods pg left join shop_promotion_div cmd on pg.div_ix = cmd.div_ix 
			where pg.pg_ix ='".$pg_ix."' and pg.agent_type = '".$agent_type."'
			order by pg_use_edate desc limit 0,1";

$slave_db->query($sql); //AND cid='$cid'
if($slave_db->total){
	$slave_db->fetch();
	$mall_ix = $slave_db->dt[mall_ix];
	$div_ix = $slave_db->dt[div_ix];
	$pg_ix = $slave_db->dt[pg_ix];
 	$pg_title = $slave_db->dt[pg_title];
	$goods_max = $slave_db->dt[goods_max];
	$image_width = $slave_db->dt[image_width];
	$image_height = $slave_db->dt[image_height];

	//echo $slave_db->dt[pg_use_sdate];
	$pg_use_sdate = date("Y-m-d",$slave_db->dt[pg_use_sdate]);
	$pg_use_stime = date("H",$slave_db->dt[pg_use_sdate]);
	$pg_use_smin = date("i",$slave_db->dt[pg_use_sdate]);
//	echo $pg_use_sdate;

	$pg_use_edate = date("Y-m-d",$slave_db->dt[pg_use_edate]);
	$pg_use_etime = date("H",$slave_db->dt[pg_use_edate]);
	$pg_use_emin = date("i",$slave_db->dt[pg_use_edate]);
	
	$md_mem_ix = $slave_db->dt[md_mem_ix];
	$goal_amount = $slave_db->dt[goal_amount];
	$div_code = $slave_db->dt[div_code];
	$disp = $slave_db->dt[disp];
	if($mode == "copy"){
		$act = "insert";
		$pg_ix = "";
	}else{
		$act = "update";
	}
}else{
	$disp = "1";
	$pg_use_sdate = date("Y-m-d");
	$pg_use_edate = date("Y-m-d");
	$act = "insert";
}
 
//echo $div_code;

$Script = "
<style type='text/css'>
  div#drop_relation_product { width:100%;height:100%;overflow:auto;padding:1px;border:1px solid silver }
  div#drop_relation_product.hover { border:5px dashed #aaa; background:#efefef; }
  table.tb {width:100%;cursor:move;}
</style>
<script type='text/javascript' src='/admin/js/jquery.multisortable.js'></script>
<script type='text/javascript' src='/admin/js/jquery.multisortable.js'></script>
<script type='text/javascript' src='/admin/display/main_goods.js'></script>
<script type='text/javascript' src='../js/ms_productSearch.js'></script>
<Script Language='JavaScript'>



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

function SubmitX(frm){
	//alert(iView.document.body.innerHTML);
	//frm.content.value = iView.document.body.innerHTML;
	return true;
}




function init(){
	var frm = document.promotion_frm;
	Content_Input();
	Init(frm);
	onLoadDate('$sDate','$eDate');
}

function onDropAction(mode, promotion_ix,pid)
{
	//outTip(img3);
	//alert(1);
	parent.document.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&promotion_ix='+promotion_ix+'&pid='+pid;

}

$(document).ready(function () {
	var type_btn = $('input[id^=display_type]');
	//alert(type_btn);
	if(type_btn.checked == true)
	{
		$(this).hide();
	}

	//click event
	$('.promotion_type_box').click(function(){
		promotion_type_check_reset($(this));
		var img_tag = $(this).find('img');
		img_tag.attr('src',img_tag.attr('src').replace('.png','_on.png'));
		
		$(this).find('input').attr('checked','checked');
	});

});

function promotion_type_check_reset(jquery_obj){
	//img reset
	//$('.promotion_types').find('img').each(function( i, element ){
	jquery_obj.closest('td.promotion_types').find('img').each(function( i, element ){
		//alert(i);
		$(element).attr('src', $(element).attr('src').replace('_on.png', '.png') );
		$(element).parent().find('input').attr('checked','');
	})
	//checkbox reset
	//$('.promotion_types').find('input').attr('checked','');
}

</Script>";



$Contents = "
<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
 <tr>
	<td align='left' colspan=6 > ".GetTitleNavigation("프로모션상품 관리", "전시관리 > 프로모션상품 관리 ")."</td>
</tr>";
if(false){
$Contents .= "
<tr>
	<td align='left' colspan=6 style='padding-bottom:10px;'>
		<div class='tab'>
			<table class='s_org_tab' style='width:100%' border=0>
			<tr>
				<td class='tab'>
					<!--table id='tab_01' ".($div_code == "" ? "class='on'":"")."  >
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='?div_code='\">전체보기</td>
						<th class='box_03'></th>
					</tr>
					</table-->";


$sql = 	"SELECT * FROM shop_promotion_div where disp=1 and agent_type = '".$agent_type."' ";

$slave_db->query($sql);

for($i=0;$i < $slave_db->total;$i++){
	$slave_db->fetch($i);
	//echo $div_code." ". $slave_db->dt[div_code];
	//exit;
	if($div_code == $slave_db->dt[div_code] && $div_code){
		$div_code_str = "class='on'";
	}else if($div_code == ""){
		if($i == 0){
			$div_code_str = " class='on'";
			//$div_code = $slave_db->dt[div_code];
		}else{
			$div_code_str = "";
		}
	}else{
		$div_code_str = "";
	}
$Contents .= "<table id='tab_".($i+2)."'  ".$div_code_str.">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='?div_code=".$slave_db->dt[div_code]."'\">".$slave_db->dt[div_name]."</td>
						<th class='box_03'></th>
					</tr>
					</table>";
}
$Contents .= "
				</td>
				<td class='btn' align=right>
					<a href='promotion_goods_category.php'><!--img src='../images/".$admininfo["language"]."/btn_promotion_type.gif' align=absmiddle-->프로모션 전시 분류관리</a>
				</td>
			</tr>
			</table>
			</div>
	</td>
</tr>";
}
$Contents .= "
  <tr>
    <td>

        <form name='promotion_frm' method='post' onSubmit=\"return SubmitX(this)\" action='../display/promotion_goods.act.php' style='display:inline;' enctype='multipart/form-data' target=''>
		<input type='hidden' name=act value='".$act."'>
		<input type='hidden' name=mmode value='".$mmode."'>
		<input type='hidden' name=div_code value='".$div_code."'>
		<input type='hidden' name=pg_ix value='".$pg_ix."'>
		<input type='hidden' name=agent_type value='".$agent_type."'>

			<table class='box_shadow' style='width:100%;' cellpadding='0' cellspacing='0' border='0'>
				<tr>
					<th class='box_01'></th>
					<td class='box_02'></td>
					<th class='box_03'></th>
				</tr>
				<tr>
					<th class='box_04'></th>
					<td class='box_05'  valign=top style='padding:0px'>
					<table border='0' cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
					  <col width='15%'>
					  <col width='35%'>
					  <col width='15%'>
					  <col width='35%'>";
					if($_SESSION["admin_config"][front_multiview] == "Y"){
					$Contents .= "
					<tr height=28>
						<td class='search_box_title' > 프론트 전시 구분</td>
						<td class='search_box_item' colspan=3>".GetDisplayDivision($mall_ix, "select")." <span class=small>전시 구분을 선택하실경우 해당 사이트에 노출됩니다.</span></td>
					</tr>";
					}
					$Contents .= "
					  <tr height=28>
						<td class='search_box_title' nowrap> <b>프로모션전시 제목</b></td>
						<td class='search_box_item' colspan=3><input class='textbox' type='text' name='pg_title' value='".$slave_db->dt[pg_title]."' validation=true title='프로모션 제목' maxlength='50' style='width:400px'></td>
					  </tr>
					  <tr height=28>
						<td class='search_box_title' nowrap> <b>프로모션전시 분류 링크</b></td>
						<td class='search_box_item' colspan=3>
						<input class='textbox' type='text' name='pg_link' value='".$slave_db->dt[pg_link]."' validation=false title='메인전시 분류 링크' maxlength='50' style='width:400px'>
						</td>
					  </tr>
					 <tr height=27>
						  <td class='search_box_title' > <b>노출일자</b></td>
						  <td class='search_box_item'  colspan=3>
						  ".search_date('pg_use_sdate','pg_use_edate',$pg_use_sdate,$pg_use_edate,'Y',"")."";
/*
$Contents .= "
							<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
								<col width=35>
								<col width=10>
								<col width=35>
								<col width=20>
								<col width=35>
								<col width=10>
								<col width=35>
								<col width=*>
								<tr>
									<td nowrap>
									<input type='text' name='pg_use_sdate' class='textbox' value='".$pg_use_sdate."' style='height:18px;width:70px;text-align:center;' id='start_datepicker'>
									</td>
									<td align=center>일</td>
									<td nowrap>
									<SELECT name=pg_use_stime>";
					for($i=0;$i < 24;$i++){
	$Contents .= "<option value='".$i."' ".($pg_use_stime == $i ? "selected":"").">".$i."</option>";
					}
	$Contents .= "
					</SELECT> 시
					<SELECT name=pg_use_smin>";
					for($i=0;$i < 60;$i++){
	$Contents .= "<option value='".$i."' ".($pg_use_smin == $i ? "selected":"").">".$i."</option>";
					}
	$Contents .= "
					</SELECT> 분
									</td>
									<td align=center> ~ </td>
									<td nowrap>
									<input type='text' name='pg_use_edate' class='textbox' value='".$pg_use_edate."' style='height:18px;width:70px;text-align:center;' id='end_datepicker'>
									</td>
									<td align=center>일</td>
									<td nowrap>
									<SELECT name=pg_use_etime>";
					for($i=0;$i < 24;$i++){
	$Contents .= "<option value='".$i."' ".($pg_use_etime == $i ? "selected":"").">".$i."</option>";
					}
	$Contents .= "
					</SELECT> 시
					<SELECT name=pg_use_emin>";
					for($i=0;$i < 60;$i++){
	$Contents .= "<option value='".$i."' ".($pg_use_emin == $i ? "selected":"").">".$i."</option>";
					}
	$Contents .= "
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
							</table>";
*/
$Contents .= "
						  </td>
						</tr>
						<!--tr height=27>
							<td class='search_box_title' nowrap> <b>상품목록갯수</b></td>
							<td class='search_box_item' colspan=3>
							한페이지에 <input class='textbox number' type='text' name='goods_max' size=5 value='".$slave_db->dt[goods_max]."' maxlength='50' > 개의 상품을 노출합니다
							<div class=small style='display:inline;'>입력되지 않으면 기본 상품 노출갯수 15개로 노출됩니다.</div>
							</td>
						</tr>
						<tr height=27>
							<td class='search_box_title' nowrap> <b>상품이미지 사이즈</b></td>
							<td class='search_box_item' colspan=3 style='padding:5px;'>
								가로 : <input class='textbox number' type='text' name='image_width' size=5 value='".$image_width."' maxlength='50' >
								세로 : <input class='textbox number' type='text' name='image_height' size=5 value='".$image_height."' maxlength='50' ><br>
								<div class=small style='padding:4px 0px '>
								정보가 입력되지 않으면 기본 이미지 사이즈가 노출됩니다. <br>
								한쪽 사이즈만 입력되면 입력되지 않은 부분은 비율적으로 표시되게 됩니다.<br>


								</div>
								</td>
						</tr-->
					</table>
					</td>
					<th class='box_06'></th>
				</tr>
				<tr>
					<th class='box_07'></th>
					<td class='box_08'></td>
					<th class='box_09'></th>
				</tr>
			</table>


        <table border='0' width='100%' cellspacing='1' cellpadding='0'>
          <tr>
            <td bgcolor='#6783A8'>
              <table border='0' cellspacing='0' cellpadding='0' width='100%'>
                <tr>
                  <td bgcolor='#ffffff'>
                    <table border='0' cellpadding=3 cellspacing=0 width='100%'>


                    <tr>
                      <td  colspan='4' style='padding:0px;'>";
$gdb = new Database;
$sql = "SELECT * FROM shop_promotion_product_group where pg_ix != '' and pg_ix ='".$pg_ix."'  order by group_code asc ";
//echo $sql;
$gdb->query($sql);//div_code = '".$div_code."'
if($gdb->total || true){
	//$group_total = $gdb->total-1;
	if($gdb->total)		$group_total = $gdb->total;
	else					$group_total =1;

	for($i=0;($i < $gdb->total || $i < 1);$i++){
	$gdb->fetch($i);
$Contents .= "
                      <div id='group_info_area".$i."' group_code='".($i+1)."'>
                      <div style='padding:10px 10px'><img src='/admin/images/dot_org.gif' align=absmiddle> <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>프로모션상품그룹  (GROUP ".($i+1).")</b> <a onclick=\"add_table()\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle style='cursor:pointer;'></a> ".($i == 0 ? "<!--삭제버튼-->":"<a onclick=\"del_table('group_info_area".$i."',".($i+1).");\"><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle style='cursor:pointer;'></a>")."</div>
                      <table width='100%' border='0' cellpadding='5' cellspacing='1' bgcolor='#E9E9E9' class='search_table_box'>
					  <col width='15%'>
					  <col width='35%'>
					  <col width='15%'>
					  <col width='35%'>
						<tr>
						  <td class='search_box_title'><b>프로모션 상품그룹명</b></td>
						  <td class='search_box_item' colspan=3>
						  <input type='text' class='textbox' name='group_name[".($i+1)."]' id='group_name_".($i+1)."' size=50 value='".$gdb->dt[group_name]."'> 상품그룹 이미지 등록을 하지 않은경우 노출됩니다.
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>상품그룹이미지링크</b></td>
						  <td class='search_box_item' colspan=3>
						  <input type='text' class='textbox' name='group_link[".($i+1)."]' id='group_link_".($i+1)."' size=50 value='".$gdb->dt[group_link]."'>
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>전시상품</b><span style='padding-left:2px' class='helpcloud' help_width='300' help_height='30' help_html='자동등록에 경우 사용할 카테고리를 선택하게 되면 상품등록 시 자동으로 신규 상품이 전시됩니다.'><img src='/admin/images/icon_q.gif' /></span></td>
						  <td class='search_box_item' style='padding:10px 10px;' colspan=3>
						   <div style='padding-bottom:10px;'>
							  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_m' size=50 value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_m'>수동등록</label>
							  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_a' size=50 value='A' style='border:0px;' ".($gdb->dt[goods_display_type] == "A" ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_a'>자동등록</label><br>
						  </div>
						  <div id='goods_manual_area_".($i+1)."' style='".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "display:block;":"display:none;")."'>
							  <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,".($i+1).",'productList_".($i+1)."');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
							  <div style='width:100%;padding:5px;' id='group_product_area_".($i+1)."' >".relationEventGroupProductList($gdb->dt[pg_ix],($gdb->dt[group_code] ? $gdb->dt[group_code]:($i+1)), "clipart")."</div>
							  <div style='width:100%;float:left;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> </span>
							  </div>
						  </div>
						  <div style='padding:0px 0px;".($gdb->dt[goods_display_type] == "A" ? "display:block;":"display:none;")."' id='goods_auto_area_".($i+1)."'>
							<a href=\"javascript:PoPWindow('category_select.php?mmode=pop&group_code=".($i+1)."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_select_category.gif' border=0 align=absmiddle></a><br>
							<table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
								<col width=100%>
								<tr>
									<td style='padding-top:5px;'>";

					$Contents .= PrintCategoryRelation(($i+1),$pg_ix);

					$Contents .= "	</td>
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
								<option value='wish_cnt' ".($gdb->dt[display_auto_type] == "wish_cnt" ? "selected":"").">찜한순</option>
								<option value='after_score' ".($gdb->dt[display_auto_type] == "after_score" ? "selected":"").">후기순위</option>
							</select>

							으로 노출 하며 <span class='red'>최근 
							
							<select name='display_auto_priod[".($i+1)."]'>
								<option value='1' ".($gdb->dt[display_auto_priod] == "1" ? "selected":"").">1일</option>
								<option value='7' ".($gdb->dt[display_auto_priod] == "7" ? "selected":"").">7일</option>
								<option value='10' ".($gdb->dt[display_auto_priod] == "10" ? "selected":"").">10일</option>
								<option value='15' ".($gdb->dt[display_auto_priod] == "15" ? "selected":"").">15일</option>
								<option value='30' ".($gdb->dt[display_auto_priod] == "30" ? "selected":"").">30일</option>
							</select>

							<!--input type='text' class='textbox' name='display_auto_priod[".($i+1)."]' id='display_auto_priod_".($i+1)."' size=10 value='".$gdb->dt[display_auto_priod]."'-->
							
							일 기준</span>으로 합니다.
							</div>
							</div>
						  </td>
						</tr>
					  </table><br><br>
					  </div>";
	}
}
/*
else{
	$group_total = 0;
$Contents .= "       <div id='group_info_area0' group_code='1'>
                      <div style='padding:10px 10px'><img src='/admin/images/dot_org.gif' align=absmiddle> <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>프로모션상품그룹  (GROUP 1)</b> <a onclick=\"add_table()\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle></a> <!--삭제버튼--></div>
                      <table width='100%' border='0' cellpadding='10' cellspacing='1' bgcolor='#E9E9E9' class='search_table_box'>
					  <col widht='15%'>
					  <col widht='*'>
						<tr>
						  <td class='search_box_title'><b>프로모션 상품그룹명</b></td>
						  <td class='search_box_item'>
						  <input type='text' class='textbox' name='group_name[1]' id='group_name_1' size=50 value='".$gdb->dt[group_name]."'>
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>전시여부</b></td>
						  <td class='search_box_item'>
						  <input type='radio' class='textbox' name='use_yn[1]' id='use_1_y' size=50 value='Y' style='border:0px;' ><label for='use_1_y'>전시</label>
						  <input type='radio' class='textbox' name='use_yn[1]' id='use_1_n' size=50 value='N' style='border:0px;' checked><label for='use_1_n'>전시 하지 않음</label>
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>상품그룹 이미지</b></td>
						  <td class='search_box_item'>
						  <input type='file' class='textbox' name='group_img[1]' id='group_img' size=50 value=''> <input type='checkbox' name='group_img_del[1]' id='group_img_del_1' size=50 value='Y'><label for='group_img_del_1'>그룹이미지 삭제</label><br>
						  <div style='padding:5px;' id='group_img_area_1'></div>
						  <span class=small><!--* 이미지 등록을 하지 않을경우 상품그룹명이 노출됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</span>
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>상품그룹 배너 이미지</b></td>
						  <td class='search_box_item'>
						  <input type='file' class='textbox' name='group_banner_img[1]' id='group_banner_img' size=50 value=''> <input type='checkbox' name='group_banner_img_del[1]' id='group_banner_img_del_1' size=50 value='Y'><label for='group_banner_img_del_1'>그룹이미지 삭제</label><br>
						  <div style='padding:5px;' id='group_banner_img_area_1'></div>
						  <span class=small><!--* 이미지 등록을 하지 않을경우 상품그룹명이 노출됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')."</span>
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>전시타입</b></td>
						  <td class='search_box_item' style='padding:10px 5px;'>
						  <div style='float:left;text-align:center;width:130px;'>
						  <img src='/admin/images/g_5.gif' align=center onclick=\"document.getElementById('display_type_1_0').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_0' value='0' style='border:0px;' checked><label for='display_type_1_0'>기본형(5EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:130px;'>
						  <img src='/admin/images/g_4.gif' align=center onclick=\"document.getElementById('display_type_1_1').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_1' value='1' style='border:0px;' checked><label for='display_type_1_1'>기본형(4EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:130px;'>
						  <img src='/admin/images/g_3.gif' align=center onclick=\"document.getElementById('display_type_1_2').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_2' value='2' style='border:0px;' ><label for='display_type_1_2'>기본형2(3EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:140px;'>
						  <img src='/admin/images/slide_4.gif' align=center onclick=\"document.getElementById('display_type_1_3').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_3' value='3' style='border:0px;' ><label for='display_type_1_3'>슬라이드형(4EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:135px;'>
						  <img src='/admin/images/g_16.gif' align=center onclick=\"document.getElementById('display_type_1_4').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_4' value='4' style='border:0px;' ><label for='display_type_1_4'>기본형4(1/*EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:135px;'>
						  <img src='/admin/images/g_17.gif' align=center onclick=\"document.getElementById('display_type_1_5').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_5' value='5' style='border:0px;' ><label for='display_type_1_5'>기본형(4EA 배열)</label>
						  </div>
						  <div style='float:left;text-align:center;width:135px;'>
						  <img src='/admin/images/g_24.gif' align=center onclick=\"document.getElementById('display_type_1_6').checked = true;\"><br>
						  <input type='radio' class='textbox' name='display_type[1]' id='display_type_1_6' value='6' style='border:0px;' ><label for='display_type_1_6'>기본형(2/4EA 배열)</label>
						  </div>
						  ";

//$Contents .= SelectFileList2($DOCUMENT_ROOT.$admin_config[mall_data_root]."/module/promotion_templet/")."

$Contents .= "
						  </td>
						</tr>
						<tr>
						  <td class='search_box_title'><b>전시갯수</b></td>
						  <td class='search_box_item'>
						  <input type='text' class='textbox numeric' name='group_disp_cnt[1]' id='group_disp_cnt_1' size=10 value='".$gdb->dt[group_disp_cnt]."'>
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
							  <div style='width:100%;padding:5px;' id='group_product_area_1' >".relationEventGroupProductList($pg_ix, 1, "clipart")."</div>
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
											$Contents .= PrintCategoryRelation($id,$pg_ix);
										}else{
										$Contents .= "<table cellpadding=0 cellspacing=0 id='objCategory_".($i+1)."' >
														<col width=5>
														<col width=30>
														<col width=*>
														<col width=100>
													  </table>";
										}
					$Contents .= "	</td>
								</tr>
								<tr><td>카테고리 선택하기를 클릭해서 자동 노출하고자 하는 카테고리를 지정 하실 수 있습니다.</td></tr>
							</table>
							선택한 카테고리 내의 상품을
							</div>
						  </td>
						</tr>
					  </table><br><br>
					  </div>";
}
*/
$Contents .= "
                      </td>
                    </tr>

                    <tr><td colspan=3 align=right style='padding:10px;'>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents .= "<table>
									<tr>
										<td><input type='checkbox' name='delete_cache' id='delete_cache' value='Y'><label for='delete_cache'>캐쉬삭제하기</label></td>
										<td><input type=image src='../images/".$admininfo["language"]."/b_save.gif' align=absmiddle border=0></td>
									</tr>
								</table>";
}
$Contents .= "
					<!--a href='main.list.php'><img src='../image/b_cancel.gif' align=absmiddle  border=0></a--></td></tr>
                  </table>

                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        </form>
    </td>
  </tr>

  ";
  /*
$help_text = "
<table cellpadding=1 cellspacing=0 >
	<col width=8>
	<col width=*>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >작업시에는 <u>전시하지 않음</u>으로 선택후 작업하시기 바랍니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >디자인관리에서 프로모션가 캐쉬 설정이 되어 있을경우 캐쉬삭제를 체크하신후 저장하시면  즉시 반영하실 수 있습니다. </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >프로모션상품그룹은 원하는 만큼 추가해서 관리 하실 수 있습니다.</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td >치환코드는 프로모션상품 전체를 가져올수도 있고 , 원하는 그룹만 </td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td ><b>전체 상품을 가져올경우</b> <br>
	{@getDisplayGoodsInfo()}<br>
		&nbsp;{.goods}<br>
		&nbsp;{..pname} <br>
		&nbsp;{? ..stock > 0}<br>
		&nbsp;&nbsp;	{? ..listprice == ..sellprice}<br>
		&nbsp;&nbsp;		&lt;b>{=number_format(..sellprice)}원&lt;/b><br>
		&nbsp;&nbsp;	{:}<br>
		&nbsp;&nbsp;		&lt;s>{=number_format(..sellprice)}원&lt;/s><br>
		&nbsp;&nbsp;		&lt;span style=\"color:#e73a00\">&lt;b>{=number_format(..listprice)}원&lt;/b>&lt;/span><br>
		&nbsp;&nbsp;	{/}<br>
		&nbsp;&nbsp;{:}<br>
		&nbsp;&nbsp;[일시품절]<br>
		&nbsp;&nbsp;{/}<br>
		&nbsp;{/}<br>
	{/}<br>
	</td></tr>
	<tr><td valign=top><img src='/admin/image/icon_list.gif' ></td><td ><b>해당 그룹상품만 가져올경우  상품을 가져올경우 예) 1, 3 번 그룹만 추출 할경우</b><br>
	{@getDisplayGoodsInfo(array(1,3))}<br>
		&nbsp;{.goods}<br>
		&nbsp;{..pname} <br>
		&nbsp;{? ..stock > 0}<br>
		&nbsp;&nbsp;	{? ..listprice == ..sellprice}<br>
		&nbsp;&nbsp;		&lt;b>{=number_format(..sellprice)}원&lt;/b><br>
		&nbsp;&nbsp;	{:}<br>
		&nbsp;&nbsp;		&lt;s>{=number_format(..sellprice)}원&lt;/s><br>
		&nbsp;&nbsp;		&lt;span style=\"color:#e73a00\">&lt;b>{=number_format(..listprice)}원&lt;/b>&lt;/span><br>
		&nbsp;&nbsp;	{/}<br>
		&nbsp;&nbsp;{:}<br>
		&nbsp;&nbsp;[일시품절]<br>
		&nbsp;&nbsp;{/}<br>
		&nbsp;{/}<br>
	{/}<br>
	</td></tr>
</table>
";*/
$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B');

//$help_text = HelpBox("이벤트/기획전  관리", $help_text);
$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:0px;'><table><tr><td valign=middle><b>프로모션전시관리</b></td><td></td></tr></table></div>", $help_text,110)."</div>";//<!--a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_기획전등록(090322)_config.xml',800,517,'manual_view')\"  title='이벤트/기획전 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a-->

$Contents .= "
  <tr>
    <td align='left' style='padding-bottom:100px;'>
    $help_text

    </td>
  </tr>";

$Contents .= "
	</table>


 
<Script Language='JavaScript'>

$(function() {
	$(\"#start_datepicker\").datepicker({
	//monthNames: ['년 1월','년 2월','년 3월','년 4월','년 5월','년 6월','년 7월','년 8월','년 9월','년 10월','년 11월','년 12월'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	//showMonthAfterYear:true,
	dateFormat: 'yy-mm-dd',
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
	dateFormat: 'yy-mm-dd',
	buttonImageOnly: true,
	buttonText: '달력'

	});

	//$('#end_timepicker').timepicker();
});



function select_date(FromDate,ToDate,dType) {
	var frm = document.searchmember;

	$(\"#start_datepicker\").val(FromDate);
	$(\"#end_datepicker\").val(ToDate);
}

my_init('$group_total');
</Script>";



$Script = "<script language='JavaScript' src='../js/scriptaculous.js'></script>\n
<script language='JavaScript' src='../js/dd.js'></script>\n
<!--script language='JavaScript' src='../js/mozInnerHTML.js'></script-->\n
<script language='javascript' src='../display/event.write.js'></script>\n
<script language='JavaScript' src='../webedit/webedit.js'></script>\n
$Script";

if($agent_type == "M"){
	if($mmode == "pop"){
		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->OnloadFunction = "";
		$P->strLeftMenu = mshop_menu();
		$P->Navigation = $navigation;
		$P->title = $title;
		$P->NaviTitle = $title;
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->Navigation = $navigation;
		$P->title = $title;
		$P->strLeftMenu = mshop_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}else{
		if($mmode == "pop"){

		$P = new ManagePopLayOut();
		$P->addScript = $Script;
		$P->OnloadFunction = "";
		$P->strLeftMenu = display_menu();
		$P->Navigation = "프로모션/전시 > 프로모션 전시관리 > 프로모션 상품관리";
		$P->title = "프로모션 상품관리";
		$P->NaviTitle = "프로모션 상품관리";
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}else{
		$P = new LayOut();
		$P->addScript = $Script;
		$P->Navigation = "프로모션/전시 > 프로모션 전시관리 > 프로모션 상품관리";
		$P->title = "프로모션 상품관리";
		$P->OnloadFunction = "";//showSubMenuLayer('storeleft');
		$P->strLeftMenu = display_menu();
		$P->strContents = $Contents;
		echo $P->PrintLayOut();
	}
}

function PrintCategoryRelation($group_code,$pg_ix){
	global $slave_db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth, r.pcr_ix, r.regdate  
				from shop_promotion_category_relation r, ".TBL_SHOP_CATEGORY_INFO." c 
				where group_code = '".$group_code."' 
				and c.cid = r.cid and pg_ix='".$pg_ix."'";

	//echo $sql."<br><br>";
	$slave_db->query($sql);




	if ($slave_db->total == 0){
		$mString .= "<table cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."' >
								<col width=5>
								<col width=*>
								<col width=100>
							  </table>";
	}else{
		$i=0;
		$mString = "<table width=100% border=0 cellpadding=0 cellspacing=0 id='objCategory_".($group_code)."'>";
		for($i=0;$i<$slave_db->total;$i++){
			$slave_db->fetch($i);
			$parent_cname = GetParentCategory($slave_db->dt[cid],$slave_db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='category[".$group_code."][]' id='_category' value='".$slave_db->dt[cid]."' style='display:none'></td>
				<!--td class='table_td_white small' width='50'><input type='radio' name='basic[".$group_code."]' value='".$slave_db->dt[cid]."' ".($slave_db->dt[basic] == 1 ? "checked":"")."></td-->
				<td class='table_td_white small ' width='*'>".($parent_cname != "" ? $parent_cname." > ":"").$slave_db->dt[cname]."</td>
				<td class='table_td_white' width='100' align=right><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(".$group_code.",this.parentNode.parentNode)' style='cursor:pointer;' /></td>
				</tr>";
		}
		$mString .= "</table>";
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";


	return $mString;
}


function FileList2 ( $path , $maxdepth = -1 , $mode = "FULL" , $d = 0 ){
global $page_name;
   if ( substr ( $path , strlen ( $path ) - 1 ) != '/' ) { $path .= '/' ; }
   $dirlist = array () ;
   //if ( $mode != "FILES" ) { $dirlist[] = $path ; }
   if(!is_dir($path)){return false;};
   if ( $handle = opendir ( $path ) )
   {

       while ( false !== ( $file = readdir ( $handle ) ) )
       {
           if ( $file != '.' && $file != '..' )
           {
               $only_file = $file;
               $file = $path . $file ;
               if ( ! is_dir ( $file ) || $mode == "FULL"){
               		if(is_dir ( $file )){
               			//$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
               		}else{
               			if($page_name == $only_file){
               				$mstring .=  "<option value='".$only_file ."' selected>".$only_file ."</option>";
               			}else{
               				$mstring .=  "<option value='".$only_file ."'>".$only_file ."</option>";
               			}
               		}

               }elseif ($d >=0 && ($d < $maxdepth || $maxdepth < 0) ){
                   $mstring .= FileList2 ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;
                  $mstring .=  "<option value='".Icon($only_file,"path",filetype($file))."'>".$only_file ."</option>";
               }
           }
       }
       closedir ( $handle ) ;
   }
   if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
   return ( $mstring ) ;
}

function SelectFileList2($path){
	global $DOCUMENT_ROOT, $mod, $SubID, $mmode;
	if($path == ""){
		$path = $_SERVER["DOCUMENT_ROOT"]."/data/sample/templet/basic";
	}

	$mstring =  "<select name='page_name' onchange=\"document.location.href='design.mod.php?SubID=$SubID&mod=$mod&page_name='+this.value+'&mmode=$mmode'\">";
	if(FileList2($path, 0, "FULL")){
		$mstring .= FileList2($path, 0, "FULL");
	}else{
		$mstring .= "<option>파일이 존재하지않습니다.</option>";
	}
	$mstring .=  "</select>";

	return $mstring;
}


function relationEventGroupProductList($pg_ix, $group_code, $disp_type=""){
	global $start,$page, $orderby, $admin_config, $pprid;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$slave_db = new Database;

	$sql = "SELECT COUNT(*) FROM ".TBL_SHOP_PRODUCT." p, shop_promotion_product_relation ppr 
				where p.id = ppr.pid and pg_ix = '$pg_ix' and group_code = '$group_code' "; //and p.disp = 1
	$slave_db->query($sql);
	$slave_db->fetch();
	$total = $slave_db->dt[0];

//SELECT p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, ppr_ix, ppr.vieworder, ppr.group_code, p.brand_name
	$sql = "SELECT p.id, p.pcode, p.shotinfo, p.pname, p.listprice, p.sellprice, p.wholesale_sellprice, p.wholesale_price,   p.reserve, p.state, p.disp, ppr.vieworder, ppr.group_code, p.brand_name	
					FROM ".TBL_SHOP_PRODUCT." p, shop_promotion_product_relation ppr
					where p.id = ppr.pid and pg_ix != '' and pg_ix = '$pg_ix' and group_code = '$group_code' 
					order by ppr.vieworder asc limit $start,$max";//and p.disp = 1
	$slave_db->query($sql);

	if ($slave_db->total == 0){
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>';
		}
	}else{
		$i=0;
		if($disp_type == "clipart"){
			$mString = '<ul id="productList_'.$group_code.'" name="productList" class="productList"></ul>'."\n";
			$mString .= '<script>'."\n";
			$mString .= 'ms_productSearch.groupCode = '.$group_code.";\n";
			for($i=0;$i<$slave_db->total;$i++){
				$slave_db->fetch($i);
				$imgPath = PrintImage($admin_config['mall_data_root'].'/images/product', $slave_db->dt['id'], 'c');
				//$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$slave_db->dt['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($slave_db->dt['pname']))).'", "'.addslashes(addslashes(trim($slave_db->dt['brand_name']))).'", "'.$slave_db->dt['sellprice'].'");'."\n";
				$mString .= 'ms_productSearch._setProduct("productList_'.$group_code.'", "M", "'.$slave_db->dt['id'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($slave_db->dt['pname']))).'", "'.addslashes(addslashes(trim($slave_db->dt['brand_name']))).'", "'.$slave_db->dt['sellprice'].'", "'.$slave_db->dt['listprice'].'", "'.$slave_db->dt['reserve'].'", "'.$slave_db->dt['coprice'].'", "'.$slave_db->dt['wholesale_price'].'", "'.$slave_db->dt['wholesale_sellprice'].'", "'.$slave_db->dt['disp'].'", "'.$slave_db->dt['state'].'");'."\n";
			}
			$mString .= '</script>'."\n";
		}
	}
	return $mString;
}

function relationProductList2(){

	global $start,$page, $orderby, $admin_config,$slave_db;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT distinct p.id,p.pname, p.sellprice, p.reserve
					FROM ".TBL_SHOP_PRODUCT." p, shop_promotion_product_relation ppr
					where p.id = ppr.pid and p.disp = 1   ";
	$slave_db->query($sql);
	$total = $slave_db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, ppr_ix
					FROM ".TBL_SHOP_PRODUCT." p, shop_promotion_product_relation ppr
					where p.id = ppr.pid and p.disp = 1 order by ppr.vieworder limit $start,$max";
	$slave_db->query($sql);




	if ($slave_db->total){

		$mString = "<div id='sortlist'>";

		$i=0;
		for($i=0;$i<$slave_db->total;$i++){
			$slave_db->fetch($i);
			$mString .= "<img src='".$admin_config[mall_data_root]."/images/product/c_".$slave_db->dt[id].".gif' id='image_".$slave_db->dt[id]."' title='".cut_str($slave_db->dt[pname],30)."' ondblclick=\"document.frames['act'].location.href='relation.category.act.php?mode=delete&pid=".$pid."&rp_ix=".$slave_db->dt[rp_ix]."'\"width=50 height=50 style='border:1px solid silver' vpace=2 hspace=2>";
		}
	}
	$mString .= "</div>";

	return $mString;

}

function relationProductList($disp_type=""){

	global $start,$page, $orderby, $admin_config, $pprid,$slave_db;

	$max = 105;

	if ($page == ''){
		$start = 0;
		$page  = 1;
	}else{
		$start = ($page - 1) * $max;
	}

	$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve
					FROM ".TBL_SHOP_PRODUCT." p, shop_promotion_product_relation ppr
					where p.id = ppr.pid and p.disp = 1   ";
	$slave_db->query($sql);
	$total = $slave_db->total;

	$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, ppr_ix, ppr.vieworder, ppr.group_code
					FROM ".TBL_SHOP_PRODUCT." p, shop_promotion_product_relation ppr
					where p.id = ppr.pid and p.disp = 1 order by ppr.vieworder asc limit $start,$max";
	$slave_db->query($sql);



	if ($slave_db->total == 0){
		$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
		$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'><!--등록된 이벤트/기획전 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 <br>이벤트/기획전 상품으로 등록됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')." </td></tr>";
		$mString .= "</table>";
	}else{
//		$mString = "<ul id='sortlist' >";

		$i=0;
		if($disp_type == "clipart"){

			for($i=0;$i<$slave_db->total;$i++){
				$slave_db->fetch($i);
				$mString .= "<div id='seleted_tb_".$slave_db->dt[id]."' style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'>\n";
				$mString .= "<table id='seleted_tb_".$slave_db->dt[id]."' cellpadding=0 cellspacing=0 border=0 style='display:inline;'>\n";
				$mString .= "<tr>\n";
				$mString .= "<td style='display:none;'></td>\n";
				$mString .= "<td><img src='".$admin_config[mall_data_root]."/images/product/c_".$slave_db->dt[id].".gif' ></td>\n";
				$mString .= "<td style='display:none;'>".$slave_db->dt[pname]."</td>\n";
				$mString .= "<td style='display:none;'><input type='hidden' name='rpid[".$slave_db->dt[group_code]."][]' value='"+spid+"'></td>\n";
				$mString .= "</tr>\n";
				$mString .= "</table>\n";
				$mString .= "</div>\n";
			}
		}else{
	  	$mString .= "<!--li id='image_".$slave_db->dt[id]."' -->
							<table width=100% cellpadding=0 cellspacing=0 id=tb_relation_product class=tb border=0 >
							<col width='60'>
							<col width='*'>
							<col width='60'>";

			for($i=0;$i<$slave_db->total;$i++){
				$slave_db->fetch($i);
				//ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);$('tb_relation_product').deleteRow(this.rowIndex);	\"
				$mString .= "<tr height=27 bgcolor=#ffffff onclick=\"spoit(this)\" ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);\" style='background: url(../images/dot.gif) repeat-x left bottom; '>
							<td class=table_td_white align=center style='padding:5px;'>
								<div style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'><img src='".$admin_config[mall_data_root]."/images/product/c_".$slave_db->dt[id].".gif'></div>
							</td>
							<td class=table_td_white>".cut_str($slave_db->dt[pname],30)."<br>".number_format($slave_db->dt[sellprice])."</td>
							<td><input type='hidden' name='rpid[]' value='".$slave_db->dt[id]."'></td>
							</tr>
							";
				//$mString .= "</li>";
			}
			$mString .= "</table>";

		}
	}

	//$mString = $mString."</ul>";

	return $mString;

}


/*

CREATE TABLE `shop_promotion_product_group` (
  `mpg_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `group_name` varchar(100) NOT NULL default '',
  `group_code` int(2) NOT NULL default '0',
  `display_type` int(2) default '1',
  `insert_yn` enum('Y','N') default 'Y',
  `use_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`mpg_ix`)
) TYPE=MyISAM COMMENT='프로모션상품전시관리_그룹'



CREATE TABLE `shop_promotion_product_relation` (
  `ppr_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `pid` int(6) unsigned zerofill NOT NULL default '000000',
  `group_code` int(2) NOT NULL default '1',
  `vieworder` int(5) NOT NULL default '0',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`ppr_ix`)
) TYPE=MyISAM COMMENT='프로모션상품전시관리_상품'


CREATE TABLE `shop_promotion_category_relation` (
  `pcr_ix` int(8) unsigned zerofill NOT NULL auto_increment,
  `cid` int(6) unsigned zerofill NOT NULL default '000000',
  `group_code` int(2) NOT NULL default '1',
  `vieworder` int(5) NOT NULL default '0',
  `insert_yn` enum('Y','N') default 'Y',
  `regdate` datetime default NULL,
  PRIMARY KEY  (`pcr_ix`)
) TYPE=MyISAM COMMENT='프로모션상품전시관리_노출카테고리'

*/
?>
