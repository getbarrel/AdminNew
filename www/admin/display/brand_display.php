<?php
	include("../class/layout.class");
	include("./brand_display.lib.php");

	$db = new MySQL;

	//print_r($admininfo);
	//echo "$DOCUMENT_ROOT".$admin_config[mall_data_root];



	$sql = "SELECT db_title, db.db_ix, db.display_type, db.div_ix,  db_use_sdate , db_use_edate, db.disp, db.cid
				FROM shop_display_brand db, shop_display_brand_div cmd 
				where db.div_ix = cmd.div_ix and db.db_ix ='$db_ix' 
				order by db_use_edate desc limit 1";
	$db->query($sql); //AND cid='$cid' 
	if($db->total){
		$db->fetch();
		$div_ix = $db->dt[div_ix];
		$display_type = $db->dt[display_type];
		$db_ix = $db->dt[db_ix];
		$db_title = $db->dt[db_title];
		$db_use_sdate = substr($db->dt[db_use_sdate],0,10);
		$db_use_stime = substr($db->dt[db_use_sdate],11,2);
		$db_use_smin = substr($db->dt[db_use_sdate],13,2);

		$db_use_edate = substr($db->dt[db_use_edate],0,10);
		$db_use_etime = substr($db->dt[db_use_edate],11,2);
		$db_use_emin = substr($db->dt[db_use_edate],13,2);
		$disp = $db->dt[disp];
		$cid = $db->dt[cid];
		$depth = $db->dt[depth];

	}


	if($display_type == ""){
		$display_type = "C";
	}


	if($db_ix=="" || !$db_ix) $db_ix="H";
	else $db_ix=$_GET["db_ix"];

	$img_pre_text=$db_ix."_";

	$Script = "
	<style type='text/css'>
	  div#drop_relation_product { width:100%;height:100%;overflow:auto;padding:1px;border:1px solid silver }
	  div#drop_relation_product.hover { border:5px dashed #aaa; background:#efefef; }
	  table.tb {width:100%;cursor:move;}
	</style>
	<script type='text/javascript' src='../js/ms_brandSearch.js'></script>

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
		var frm = document.main_frm;
		Content_Input();
		Init(frm);
		onLoadDate('$sDate','$eDate');
	}

	function onDropAction(mode, main_ix,pid)
	{
		//outTip(img3);
		//alert(1);
		parent.document.frames['act'].location.href='./relation.category.act.php?mode='+mode+'&main_ix='+main_ix+'&pid='+pid;

	}


	function loadCategory(sel,target) {
		var trigger = sel.options[sel.selectedIndex].value;	
		var form = sel.form.name;
		var depth = $('select[name='+sel.name+']').attr('depth');//sel.depth; 크롬도 되도록 (홍진영)
		window.frames['act'].location.href = '/admin/product/category.load2.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}

	</Script>";


	$Contents = "
	<table width='100%' border='0' cellpadding=0 cellspacing=0>
	 <tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("브랜드 전시관리", "전시관리 > 브랜드 전시관리 ")."</td>
	</tr>
	<tr>
		<td align='left' >								
			<div class='tab' style='width:100%;height:32px;margin:0px;'>
			<table width='100%' class='s_org_tab'>				
			<tr>							
				<td class='tab' >
					<table id='tab_1' ".(($display_type == "C" || $display_type == "") ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='?display_type=C'\">분류별 브랜드 전시</td>
						<th class='box_03'></th>							
					</tr>
					</table>
					<table id='tab_2' ".($display_type == "B" ? "class='on'":"").">
					<tr>
						<th class='box_01'></th>
						<td class='box_02' onclick=\"document.location.href='?display_type=B'\">브랜드관 관리</td>
						<th class='box_03'></th>				
					</tr>
					</table>
				</td>							
				<td align='right'>
					<!--a href='#;' onclick='FnDisplayWrite()'><img src='../images/".$admininfo["language"]."/btn_disp_write.gif' align=absmiddle ></a>&nbsp;
					<a href='#;' onclick='location.href=\"display_div.php?display_div=".$display_div."\"'><img src='../images/".$admininfo["language"]."/btn_disp_div.gif' align=absmiddle ></a-->
					<a href='main_goods_category.php'><img src='../images/".$admininfo["language"]."/btn_disp_div.gif' align=absmiddle></a>
				</td>
			</tr>
			</table>										
			</div>					
		</td>
	</tr>
	<tr>
		<td align='left' colspan=6 class='point_color' style='width:100%;padding:10px;text-align:center;border:1px silver solid'>";
		if($display_type == "C"){ 
			$Contents .= "<div  ><b style='font-size:15px;'>분류별 브랜드 전시</b> - 상품 분류별로 노출될 브랜드를 설정하실 수 있습니다.</div>";
		}else{
			$Contents .= "<div  ><b style='font-size:15px;'>브랜드관  전시</b> - 상품 분류별로 노출될 브랜드를 설정하실 수 있습니다.</div>";
		}

		$Contents .= "
		</td>
	</tr>
	  <tr>
		<td>

			<form name='main_frm' method='post' onsubmit='return CheckFormValue(this)'  action='brand_display.act.php' style='display:inline;' enctype='multipart/form-data' target='iframe_act'><!-- onSubmit=\"return SubmitX(this)\"-->
			<input type='hidden' name=act value='update'>
			<input type='hidden' name='db_ix' value='".$db_ix."'>
			<input type='hidden' name='display_type' value='".$display_type."'>

			<input type='hidden' name='cid2' value='".$cid."'>
			<table border='0' width='100%' cellspacing='1' cellpadding='0'>
			  <tr>
				<td>
				
						<table border='0' cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
						  <col width='15%'>
						  <col width='35%'>
						  <col width='15%'>
						  <col width='35%'>";
	$Contents .= "
						  <tr height=28>
							<td class='search_box_title'  nowrap> <b>브랜드 전시 분류</b></td>
							<td class='search_box_item'>".($div_ix == "" ? getBrandDisplayDiv($div_ix):getBrandDisplayDiv($div_ix,"text"))."</td>
							<td class='search_box_title' > <b>노출여부</b> </td>
							<td class='search_box_item'  >
							<input type='hidden' name='pop' value='1'> <input type='radio' name='disp' id='disp_1' value='1' ".(($disp == "1" || $disp == "") ? "checked":"")." validation=true title='노출여부'> <label for='disp_1' >노출</label> <input type='radio' name='disp' id='disp_0' value='0' ".CompareReturnValue("0",$disp,"checked")." validation=true title='노출여부'><label for='disp_0' >노출안함</label>
							</td>
						  </tr>";
	if($display_type == "C"){ 
	$Contents .= "
						  <tr height=28>
							<td class='search_box_title' nowrap> <b>노출 카테고리 선택</b></td>
							<td class='search_box_item' colspan=3>
								<table border=0 cellpadding=0 cellspacing=0>
									<tr>
										<td style='padding-right:5px;'>".getCategoryList3("대분류", "cid0_1", "onChange=\"loadCategory(this,'cid1_1',2)\" title='대분류' ", 0, $cid)."</td>
										<td style='padding-right:5px;'>".getCategoryList3("중분류", "cid1_1", "onChange=\"loadCategory(this,'cid2_1',2)\" title='중분류'", 1, $cid)."</td>
										<td style='padding-right:5px;'>".getCategoryList3("소분류", "cid2_1", "onChange=\"loadCategory(this,'cid3_1',2)\" title='소분류'", 2, $cid)."</td>
										<td>".getCategoryList3("세분류", "cid3_1", "onChange=\"loadCategory(this,'cid2',2)\" title='세분류'", 3, $cid)."</td>
									</tr>
								</table>
							</td>
						  </tr>";
	}

	$Contents .= "
						  <tr height=28>
							<td class='search_box_title' nowrap> <b>브랜드 전시 제목</b></td>
							<td class='search_box_item' colspan=3><input class='textbox' type='text' name='db_title' value='".$db->dt[db_title]."' validation=true title='전시 제목' maxlength='50' style='width:300px'></td>
						  </tr>
						 <tr height=27>
							  <td class='search_box_title' > <b>노출일자</b></td>
							  <td class='search_box_item'  colspan=3>
								<table cellpadding=0 cellspacing=2 border=0 bgcolor=#ffffff width=100%>
									<col width=35>
									<col width=13>
									<col width=35>
									<col width=20>
									<col width=35>
									<col width=13>
									<col width=35>
									<col width=*>
									<tr>
										<td nowrap>
										<input type='text' name='db_use_sdate' class='textbox' value='".$db_use_sdate."' style='height:18px;width:70px;text-align:center;' id='start_datepicker'>
										</td>
										<td>일</td>
										<td nowrap>
										<SELECT name=db_use_stime>";
						for($i=0;$i < 24;$i++){
		$Contents .= "<option value='".$i."' ".($db_use_stime == $i ? "selected":"").">".$i."</option>";
						}
		$Contents .= "
						</SELECT> 시
						<SELECT name=db_use_smin>";
						for($i=0;$i < 60;$i++){
		$Contents .= "<option value='".$i."' ".($db_use_smin == $i ? "selected":"").">".$i."</option>";
						}
		$Contents .= "
						</SELECT> 분
										</td>
										<td align=center> ~ </td>
										<td nowrap>
										<input type='text' name='db_use_edate' class='textbox' value='".$db_use_edate."' style='height:18px;width:70px;text-align:center;' id='end_datepicker'>
										</td>
										<td>일</td>
										<td nowrap>
										<SELECT name=db_use_etime>";
						for($i=0;$i < 24;$i++){
		$Contents .= "<option value='".$i."' ".($db_use_etime == $i ? "selected":"").">".$i."</option>";
						}
		$Contents .= "
						</SELECT> 시
						<SELECT name=db_use_emin>";
						for($i=0;$i < 60;$i++){
		$Contents .= "<option value='".$i."' ".($db_use_emin == $i ? "selected":"").">".$i."</option>";
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
								</table>
							  </td>
							</tr>
							<!--tr height=27>
								<td class='search_box_title' nowrap> <b>상품목록갯수</b></td>
								<td class='search_box_item' colspan=3>
								한페이지에 <input class='textbox number' type='text' name='brand_max' size=5 value='".$db->dt[brand_max]."' maxlength='50' > 개의 상품을 노출합니다
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
							<tr>
								<td class='search_box_title' >  담당 MD</td>
								<td class='search_box_item'>  ".MDSelect($md_mem_ix)." </td>
								<td class='search_box_title' >  매출목표</td>
								<td class='search_box_item'><input class='textbox number' type='text' name='sales_target' size=15 value='".$sales_target."' maxlength='25'> 원</td>
							</tr>
						</table>
						
				</td>
			  </tr>
			  <tr>
				<td bgcolor='#6783A8'>
				  <table border='0' cellspacing='0' cellpadding='0' width='100%'>
					<tr>
					  <td bgcolor='#ffffff'>
						<table border='0' cellpadding=3 cellspacing=0 width='100%'>
						<tr>
						  <td  colspan='4' style='padding:10px 0px;'  id='group_area_parent'>";
	$gdb = new MySQL;
	$gdb->query("SELECT * FROM shop_display_brand_group WHERE db_ix='".$db_ix."' order by group_code asc ");

		$group_total = $gdb->total-1;
		for($i=0;($i < $gdb->total || $i == 0);$i++){
		$gdb->fetch($i);
	$Contents .= "
						  <div id='group_info_area".$i."' group_code='".($i+1)."'>
						  <div style='padding:10px 10px'><img src='/admin/images/dot_org.gif' align=absmiddle> <b style='font-family:굴림;font-size:13px;font-weight:bold;letter-spacing:-1px;word-spacing:0px;'>브랜드그룹 < (GROUP ".($i+1).")</b> <a onclick=\"add_table()\"><img src='../images/".$admininfo["language"]."/btn_goods_group_add.gif' border=0 align=absmiddle></a> ".($i == 0 ? "":"<a onclick=\"del_table('group_info_area".$i."',".($i+1).");\"><img src='../images/".$admininfo["language"]."/btn_goods_group_del.gif' border=0 align=absmiddle></a>")."</div>
						  <table width='100%' border='0' cellpadding='5' cellspacing='1' bgcolor='#E9E9E9' class='search_table_box'>
						  <col width='15%'>
						  <col width='*'>
							<tr>
							  <td class='search_box_title'><b>브랜드 그룹명</b></td>
							  <td class='search_box_item'>
							  <input type='text' class='textbox' name='group_name[".($i+1)."]' id='group_name_".($i+1)."' size=50 value=\"".$gdb->dt[group_name]."\"> 상품그룹 이미지 등록을 하지 않은경우 노출됩니다.
							  </td>
							</tr>
							<tr>
							  <td class='search_box_title'><b>전시여부</b></td>
							  <td class='search_box_item'>
							  <input type='radio' class='textbox' name='use_yn[".($i+1)."]' id='use_".($i+1)."_y' size=50 value='Y' style='border:0px;' ".($gdb->dt[use_yn] == "Y" ? "checked":"")."><label for='use_".($i+1)."_y'> 전시</label>
							  <input type='radio' class='textbox' name='use_yn[".($i+1)."]' id='use_".($i+1)."_n' size=50 value='N' style='border:0px;' ".($gdb->dt[use_yn] == "N" ? "checked":"")."><label for='use_".($i+1)."_n'> 전시 하지 않음</label>
							  </td>
							</tr>
							<tr>
							  <td class='search_box_title'><b>브랜드 그룹명 이미지</b></td>
							  <td class='search_box_item' style='padding:10px'>
							  <input type='file' class='textbox' name='group_img[".($i+1)."]' id='group_img' size=50 value=''> <input type='checkbox' name='group_img_del[".($i+1)."]' id='group_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_img_del_".($i+1)."'>그룹이미지 삭제</label><br>
							  <div style='padding:5px 5px 5px 0px;height:90px;width:90%;overflow:auto' id='group_img_area_".($i+1)."'>";
	if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/display_banner/".$db_ix."/display_banner_group_".($i+1).".gif")){
		$Contents .= "<img src='".$admin_config[mall_data_root]."/images/display_banner/".$db_ix."/display_banner_group_".($i+1).".gif'>";
	}

	$Contents .= "						</div><br>
							  <span class=small>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span>
							  </td>
							</tr>
							<!--tr>
							  <td class='search_box_title'><b>브랜드 그룹명 이미지링크</b></td>
							  <td class='search_box_item'>
							  <input type='text' class='textbox' name='group_link[".($i+1)."]' id='group_link_".($i+1)."' size=50 value='".$gdb->dt[group_link]."'>
							  </td>
							</tr>
							<tr>
							  <td class='search_box_title'><b>상품그룹 배너 이미지</b></td>
							  <td class='search_box_item' style='padding:10px'>
							  <input type='file' class='textbox' name='group_banner_img[".($i+1)."]' id='group_banner_img' size=50 value=''> <input type='checkbox' name='group_banner_img_del[".($i+1)."]' id='group_banner_img_del_".($i+1)."' size=50 value='Y' style='vertical-align:middle;'><label for='group_banner_img_del_".($i+1)."'>그룹 배너이미지 삭제</label><br>
							  <div style='padding:5px 5px 5px 0px;height:90px;width:90%;overflow:auto' id='group_banner_img_area_".($i+1)."'>";
	if(file_exists("$DOCUMENT_ROOT".$admin_config[mall_data_root]."/images/display_banner/display_banner_group_".($i+1).".gif")){
		$Contents .= "<img src='".$admin_config[mall_data_root]."/images/display_banner/display_banner_group_".($i+1).".gif'>";
	}

	$Contents .= "						</div><br>
							  <span class=small>".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</span>
							  </td>
							</tr>
							<tr>
							  <td class='search_box_title'><b>전시타입</b></td>
							  <td class='search_box_item' style='padding:10px 5px;'>
							  <div style='float:left;text-align:center;width:130px;'>
								<img src='../images/".$admininfo["language"]."/g_5.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_0').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_0' value='0' style='border:0px;' ".($gdb->dt[display_type] == "0" ? "checked":"")."><label for='display_type_".($i+1)."_0'>기본형(5EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:130px;'>
								<img src='../images/".$admininfo["language"]."/g_4.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_1').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_1' value='1' style='border:0px;' ".($gdb->dt[display_type] == "1" ? "checked":"")."><label for='display_type_".($i+1)."_1'>기본형(4EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:130px;'>
								<img src='../images/".$admininfo["language"]."/g_3.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_2').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_2' value='2' style='border:0px;' ".($gdb->dt[display_type] == "2" ? "checked":"")."><label for='display_type_".($i+1)."_2'>기본형2(3EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:140px;'>
								<img src='../images/".$admininfo["language"]."/slide_4.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_3').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_3' value='3' style='border:0px;' ".($gdb->dt[display_type] == "3" ? "checked":"")."><label for='display_type_".($i+1)."_3' class='small'>슬라이드형(4EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:135px;display:none;'>
								<img src='../images/".$admininfo["language"]."/g_16.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_4').checked = true;\"><br>
								<input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_4' value='4' style='border:0px;' ".($gdb->dt[display_type] == "4" ? "checked":"")."><label for='display_type_".($i+1)."_4'>기본형4(1/*EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:135px;'>
								<img src='../images/".$admininfo["language"]."/g_17.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_5').checked = true;\"><br>
							  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_5' value='5' style='border:0px;' ".($gdb->dt[display_type] == "5" ? "checked":"")."><label for='display_type_".($i+1)."_5'>기본형(4EA 배열)</label>
							  </div>
							  <div style='float:left;text-align:center;width:135px;'>
							  <img src='../images/".$admininfo["language"]."/g_24.gif' align=center onclick=\"document.getElementById('display_type_".($i+1)."_6').checked = true;\"><br>
							  <input type='radio' class='textbox' name='display_type[".($i+1)."]' id='display_type_".($i+1)."_6' value='6' style='border:0px;' ".($gdb->dt[display_type] == "6" ? "checked":"")."><label for='display_type_".($i+1)."_6'>기본형(2/4EA 배열)</label>
							  </div>
							  ";

	//$Contents .= SelectFileList2($DOCUMENT_ROOT.$admin_config[mall_data_root]."/module/main_templet/")."

	$Contents .= "
							  </td>
							</tr-->
							<tr>
							  <td class='search_box_title'><b>브랜드 노출갯수</b></td>
							  <td class='search_box_item'>
							  <input type='text' class='textbox' name='product_cnt[".($i+1)."]' id='product_cnt_".($i+1)."' size=10 value='".$gdb->dt[product_cnt]."'>
							  </td>
							</tr>
							<tr>
							  <td class='search_box_title'><b>전시브랜드</b></td>
							  <td class='search_box_item' style='padding:10px 10px;'>
							   <!--div style='padding-bottom:10px;'>
								  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_m' size=50 value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_m'>수동등록</label>
								  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_a' size=50 value='A' style='border:0px;' ".($gdb->dt[goods_display_type] == "A" ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_a'>자동등록</label><br>
							  </div-->
							  <div id='goods_manual_area_".($i+1)."'>
								  <a href=\"javascript:\" onclick=\"ms_brandSearch.show_productSearchBox(event,".($i+1).",'brandList_".($i+1)."');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
								  <div style='width:100%;padding:5px;' id='group_product_area_".($i+1)."' >".relationEventGroupProductList(($i+1), "clipart",$db_ix)."</div>
								  <div style='width:100%;float:left;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'B')." </span>
								  </div>
							  </div>
							  <div style='padding:0px 0px;".($gdb->dt[goods_display_type] == "A" ? "display:block;":"display:none;")."' id='goods_auto_area_".($i+1)."'>
								<a href=\"javascript:PoPWindow3('category_select.php?mmode=pop&group_code=".($i+1)."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_select_category.gif' border=0 align=absmiddle></a><br>
								<table border=0 cellpadding=0 cellspacing=0 width='100%' style='margin-top:5px;padding:5px 10px 5px 10px;border:1px solid silver' >
									<col width=100%>
									<tr>
										<td style='padding-top:5px;'>";

											//	$Contents .= PrintCategoryRelation(($i+1),$db_ix);

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
								</select>
								으로 노출 합니다.
								</div>
								</div>
							  </td>
							</tr>
						  </table><br><br>
						  </div>";
		}
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

	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');

	//$help_text = HelpBox("이벤트/기획전  관리", $help_text);
	$help_text = "<div style='position:relative;z-index:100px;'>".HelpBox("<div style='position:relative;top:0px;'><table><tr><td valign=middle><b>브랜드전시 등록/수정</b></td><td></td></tr></table></div>", $help_text,110)."</div>";//<!--a onClick=\"PoPWindow('/admin/_manual/manual.php?config=몰스토리동영상메뉴얼_기획전등록(090322)_config.xml',800,517,'manual_view')\"  title='이벤트/기획전 동영상 메뉴얼입니다'><img src='../image/movie_manual.gif' align=absmiddle></a-->

	$Contents .= "
	  <tr>
		<td align='left' style='padding-bottom:100px;'>
		$help_text

		</td>
	  </tr>";

	$Contents .= "
		</table>


	<form name='lyrstat'><input type='hidden' name='opend' value=''></form>
	<Script Language='JavaScript'>
	//init();
	my_init('$group_total');
	</Script>";



	$Script = "<script language='JavaScript' src='../js/scriptaculous.js'></script>\n
	<script language='JavaScript' src='../js/dd.js'></script>\n
	<!--script language='JavaScript' src='../js/mozInnerHTML.js'></script-->\n
	<script language='javascript' src='../display/event.write.js'></script>\n
	<script language='JavaScript' src='../webedit/webedit.js'></script>\n
	$Script";
	$P = new LayOut();
	$P->addScript = $Script;
	switch($db_ix) {
		case ("H") : 
			$P->Navigation = "프로모션/전시 > 브랜드 전시관리 > 브랜드전시 등록/수정";
			$P->title = "브랜드전시 등록/수정";
		break;
		case ("R") : 
			$P->Navigation = "프로모션/전시 > 브랜드 전시관리 > 추천도매 브랜드관리";
			$P->title = "추천도매 브랜드관리";
		break;
		default : 
			$P->Navigation = "프로모션/전시 > 브랜드 전시관리 > 브랜드전시 등록/수정";
			$P->title = "브랜드전시 등록/수정";
		break;
	}

	$P->OnloadFunction = "";//showSubMenuLayer('storeleft');
	$P->strLeftMenu = display_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();


	function PrintCategoryRelation($group_code,$db_ix="M"){
		global $db ,$admininfo;

		$sql = "select c.cid,c.cname,c.depth, r.mcr_ix, r.regdate  from shop_display_brand_relation r, ".TBL_SHOP_CATEGORY_INFO." c where group_code = '".$group_code."' and c.cid = r.cid AND r.db_ix='".$db_ix."' ";

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
			$path = "$DOCUMENT_ROOT/data/sample/templet/basic";
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


	function relationEventGroupProductList($group_code, $disp_type="",$db_ix="M"){
		global $start,$page, $orderby, $admin_config, $erpid;

		$max = 105;

		if ($page == ''){
			$start = 0;
			$page  = 1;
		}else{
			$start = ($page - 1) * $max;
		}

		$db = new MySQL;

		//$db->debug = true;


		$sql = "SELECT count(*) as total 
						FROM shop_brand mb 					
						LEFT JOIN shop_display_brand_relation br ON mb.b_ix=br.b_ix 
						where mb.b_ix IS NOT NULL and br.db_ix='".$db_ix."' and group_code = '".$group_code."'
						 ";


		$db->query($sql);
		$db->fetch();
		$total = $db->dt[0];


		$sql = "SELECT mb.* 
						FROM shop_brand mb 					
						LEFT JOIN shop_display_brand_relation br ON mb.b_ix=br.b_ix 
						where mb.b_ix IS NOT NULL  and br.db_ix='".$db_ix."' and group_code = '".$group_code."'
						order by vieworder asc
						LIMIT $start, $max ";

		//echo nl2br($sql);
		$db->query($sql);

		if ($db->total == 0){
			if($disp_type == "clipart"){
				$mString = '<ul id="brandList_'.$group_code.'" name="brandList" class="brandList"></ul>';
			}
		}else{
			$i=0;
			if($disp_type == "clipart"){
				$mString = '<ul id="brandList_'.$group_code.'" name="brandList" class="brandList"></ul>'."\n";
				$mString .= '<script>'."\n";
				$mString .= 'ms_brandSearch.groupCode = '.$group_code.";\n";
				
				for($i=0;$i<$db->total;$i++){
					$db->fetch($i);

					//$imgPath = $admin_config['mall_data_root'].'/images/shopimg/shop_logo_'.$db->dt['company_id'].'.gif';
					$imgPath = $admin_config['mall_data_root'].'/images/brand/'.$db->dt[b_ix].'/brand_'.$db->dt[b_ix].'.gif' ;

					$mString .= 'ms_brandSearch._setBrand("brandList_'.$group_code.'", "M", "'.$db->dt['b_ix'].'", "'.$imgPath.'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.addslashes(addslashes(trim($db->dt['brand_name']))).'", "'.$db->dt['brand_name'].'");'."\n";
				}
				$mString .= '</script>'."\n";
			}
		}
		return $mString;
	}

	function relationProductList2(){

		global $start,$page, $orderby, $admin_config;

		$max = 105;

		if ($page == ''){
			$start = 0;
			$page  = 1;
		}else{
			$start = ($page - 1) * $max;
		}

		$db = new MySQL;

		$sql = "SELECT distinct p.id,p.pname, p.sellprice, p.reserve
						FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_MAIN_PRODUCT_RELATION." erp
						where p.id = erp.pid and p.disp = 1   ";
		$db->query($sql);
		$total = $db->total;

		$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, mpr_ix
						FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_MAIN_PRODUCT_RELATION." erp
						where p.id = erp.pid and p.disp = 1 order by erp.vieworder limit $start,$max";
		$db->query($sql);




		if ($db->total){

			$mString = "<div id='sortlist'>";

			$i=0;
			for($i=0;$i<$db->total;$i++){
				$db->fetch($i);
				$mString .= "<img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' id='image_".$db->dt[id]."' title='".cut_str($db->dt[pname],30)."' ondblclick=\"document.frames['act'].location.href='relation.category.act.php?mode=delete&pid=".$pid."&rp_ix=".$db->dt[rp_ix]."'\"width=50 height=50 style='border:1px solid silver' vpace=2 hspace=2>";
			}
		}
		$mString .= "</div>";

		return $mString;

	}

	function relationProductList($disp_type=""){

		global $start,$page, $orderby, $admin_config, $erpid;

		$max = 105;

		if ($page == ''){
			$start = 0;
			$page  = 1;
		}else{
			$start = ($page - 1) * $max;
		}

		$db = new MySQL;

		$sql = "SELECT distinct p.id,p.pname, p.sellprice,  p.reserve
						FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_MAIN_PRODUCT_RELATION." erp
						where p.id = erp.pid and p.disp = 1   ";
		$db->query($sql);
		$total = $db->total;

		$sql = "SELECT distinct p.id, p.pcode, p.shotinfo, p.pname, p.sellprice, p.reserve, mpr_ix, erp.vieworder, erp.group_code
						FROM ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_MAIN_PRODUCT_RELATION." erp
						where p.id = erp.pid and p.disp = 1 order by erp.vieworder asc limit $start,$max";
		$db->query($sql);



		if ($db->total == 0){
			$mString = "<table cellpadding=0 cellspacing=0 width=100% height=100%  bgcolor=silver style='font-size:10px;'>";
			$mString = $mString."<tr bgcolor=#ffffff height=100%><td colspan=5 align=center class='small'><!--등록된 이벤트/기획전 상품 정보가 없습니다 . <br> 좌측 상품을 이곳으로 드래그하시면 <br>이벤트/기획전 상품으로 등록됩니다.-->".getTransDiscription(md5($_SERVER["PHP_SELF"]),'F')." </td></tr>";
			$mString .= "</table>";
		}else{
	//		$mString = "<ul id='sortlist' >";

			$i=0;
			if($disp_type == "clipart"){

				for($i=0;$i<$db->total;$i++){
					$db->fetch($i);
					$mString .= "<div id='seleted_tb_".$db->dt[id]."' style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'>\n";
					$mString .= "<table id='seleted_tb_".$db->dt[id]."' cellpadding=0 cellspacing=0 border=0 style='display:inline;'>\n";
					$mString .= "<tr>\n";
					$mString .= "<td style='display:none;'></td>\n";
					$mString .= "<td><img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif' ></td>\n";
					$mString .= "<td style='display:none;'>".$db->dt[pname]."</td>\n";
					$mString .= "<td style='display:none;'><input type='hidden' name='rpid[".$db->dt[group_code]."][]' value='"+spid+"'></td>\n";
					$mString .= "</tr>\n";
					$mString .= "</table>\n";
					$mString .= "</div>\n";
				}
			}else{
			$mString .= "<!--li id='image_".$db->dt[id]."' -->
								<table width=100% cellpadding=0 cellspacing=0 id=tb_relation_product class=tb border=0 >
								<col width='60'>
								<col width='*'>
								<col width='60'>";

				for($i=0;$i<$db->total;$i++){
					$db->fetch($i);
					//ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);$('tb_relation_product').deleteRow(this.rowIndex);	\"
					$mString .= "<tr height=27 bgcolor=#ffffff onclick=\"spoit(this)\" ondblclick=\"$('tb_relation_product').deleteRow(this.rowIndex);\" style='background: url(../images/dot.gif) repeat-x left bottom; '>
								<td class=table_td_white align=center style='padding:5px;'>
									<div style='border:1px solid #efefef;margin:0 5 5 0;display:inline;width:50px;height:50px;text-align:center'><img src='".$admin_config[mall_data_root]."/images/product/c_".$db->dt[id].".gif'></div>
								</td>
								<td class=table_td_white>".cut_str($db->dt[pname],30)."<br>".number_format($db->dt[sellprice])."</td>
								<td><input type='hidden' name='rpid[]' value='".$db->dt[id]."'></td>
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

	CREATE TABLE `shop_main_product_group` (
	  `mpg_ix` int(8) unsigned zerofill NOT NULL auto_increment,
	  `group_name` varchar(100) NOT NULL default '',
	  `group_code` int(2) NOT NULL default '0',
	  `display_type` int(2) default '1',
	  `insert_yn` enum('Y','N') default 'Y',
	  `use_yn` enum('Y','N') default 'Y',
	  `regdate` datetime default NULL,
	  PRIMARY KEY  (`mpg_ix`)
	) TYPE=MyISAM COMMENT='메인상품전시관리_그룹'



	CREATE TABLE `shop_main_product_relation` (
	  `mpr_ix` int(8) unsigned zerofill NOT NULL auto_increment,
	  `pid` int(6) unsigned zerofill NOT NULL default '000000',
	  `group_code` int(2) NOT NULL default '1',
	  `vieworder` int(5) NOT NULL default '0',
	  `insert_yn` enum('Y','N') default 'Y',
	  `regdate` datetime default NULL,
	  PRIMARY KEY  (`mpr_ix`)
	) TYPE=MyISAM COMMENT='메인상품전시관리_상품'


	CREATE TABLE `shop_display_brand_relation` (
	  `mcr_ix` int(8) unsigned zerofill NOT NULL auto_increment,
	  `cid` int(6) unsigned zerofill NOT NULL default '000000',
	  `group_code` int(2) NOT NULL default '1',
	  `vieworder` int(5) NOT NULL default '0',
	  `insert_yn` enum('Y','N') default 'Y',
	  `regdate` datetime default NULL,
	  PRIMARY KEY  (`mcr_ix`)
	) TYPE=MyISAM COMMENT='메인상품전시관리_노출카테고리'

	*/
