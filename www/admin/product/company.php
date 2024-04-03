<?
include("../class/layout.class");
include_once("brand.lib.php");

$db = new Database;
$db2 = new Database;
$db->query("SELECT * FROM shop_company WHERE c_ix='$c_ix'");

if($db->total){
	$db->fetch();
	$act = "update";
	$brand_info = $db->dt;
	$check_company_code = 1;

	$db->query("SELECT * FROM shop_company_div WHERE cd_ix='".$brand_info[cd_ix]."'");
	$db->fetch();
	if($db->total) {
		$db->fetch();
		if($db->dt["depth"]==1) {
			
			$parent_cd_ix = $db->dt[cd_ix];
			$div_name = $db->dt[div_name];
			$brand_category = "<tr style='height:26px;' id='department_row_".$parent_cd_ix."'><td><input type=hidden name=cd_ix id='department_".$parent_cd_ix."' value='".$parent_cd_ix."'>".$div_name."</td><td><a href='javascript:void(0)' onClick='department_del(\'".$parent_cd_ix."\');'><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0></a></td></tr>";
		} else {
	
			$parent_cd_ix = $db->dt[parent_cd_ix];
			$db2->query("SELECT * FROM shop_company_div WHERE parent_cd_ix='".$db->dt[parent_cd_ix]."'");
			$db2->fetch();
			$div_name_1 = $db2->dt[div_name];
			$div_name_2 = $db->dt[div_name];
			$brand_category = "<tr style='height:26px;' id='department_row_".$parent_cd_ix."'><td><input type=hidden name=cd_ix id='department_".$parent_cd_ix."' value='".$parent_cd_ix."'>".$div_name_1." > ".$div_name_2."</td><td><a href='javascript:void(0)' onClick=\"department_del('".$parent_cd_ix."')\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0></a></td></tr>";
		}
	}
	$zipcode = explode("-",$brand_info[zipcode]);
	//echo $parent_cd_ix;

}else{
	$act = "insert";
	$check_company_code = 0;
}

$Contents = "
	<table cellpadding=0 cellspacing=0 border=0 width=100%>
	<tr>
		<td >
			".GetTitleNavigation("제조사등록", "상품관리 > 제조사등록")."
		</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:15px;'>
			<div class='tab'>
				<table class='s_org_tab'>
					<tr>
						<td class='tab'>

							<table id='tab_01'  ".($info_type == "list"? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02'  ><a href='company_list.php?mmode=".$mmode."&info_type=list'>제조사 리스트</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							<table id='tab_02' ".($info_type == "add"  || $info_type == "" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='company.php?mmode=".$mmode."&info_type=add'>제조사 등록</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
			
							<table id='tab_04' ".($info_type == "category" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='company_div.php?mmode=".$mmode."&info_type=category'>제조사 분류관리</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
							
							<table id='tab_05' ".($info_type == "batch" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' ><a href='company_batch.php?mmode=".$mmode."&info_type=batch'>제조사 일괄등록</a></td>
								<th class='box_03'></th>
							</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr height=10>
		<td rowspan=6 valign=top>
		<form name='brandform' action='./company.act.php' method='post'  onsubmit=\"return BrandInput(this,'".$act."');\" enctype='multipart/form-data'  target='act'>
		<input type=hidden name=mode value='".$act."'>
		<input type=hidden name=c_ix value='".$c_ix."'>
		<input type='hidden' name='company_name_division' value=''>
		<input type='hidden' name='mmode' value='$mmode'>";
		$Contents .= "	<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 제조사 등록/수정 하기 </b><span class=small> 제조사 등록은 최하위 카테고리에만 등록해야합니다. 또한 다중등록 가능합니다.
		".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')." </span>
		</td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."</td></tr></table>";

if($admininfo[mall_type] == "B" || $admininfo[mall_type] == "O"){

$Contents .= "<table cellpadding=0 cellspacing=0  border=0 width='100%' class='input_table_box'>
				<col width=15%>
				<col width=90%>
				<tr>
					<td class='input_box_title'  nowrap> <b>카테고리 *</b> </td>
					<td class='input_box_item'>
					<input type='hidden' name=selected_cid value='".$cid."'>
					<input type='hidden' name=selected_depth value=''>
					<input type='hidden' id='_category' value=''>
					<input type='hidden' id='_category' value=''>
					<input type='hidden' id='basic' value=''>
					<!--input type='hidden' name=cid_1 value=''>
					<input type='hidden' name=cid_2 value=''>
					<input type='hidden' name=cid_3 value=''-->
						<table width=100% border=0 cellpadding=0 cellspacing=0>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<col width='20%'>
							<tr>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--1차분류--", "cid0", "cid","onChange=\"loadCategory(this,'cid1',2)\" title='1차분류' ", 0, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--2차분류--", "cid1",  "cid","onChange=\"loadCategory(this,'cid2',2)\" title='2차분류'", 1, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--3차분류--", "cid2", "cid", "onChange=\"loadCategory(this,'cid3',2)\" title='3차분류'", 2, $cid)." </td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--4차분류--", "cid3", "cid", "onChange=\"loadCategory(this,'cid4',2)\" title='4차분류'", 3, $cid)."</td>
								<td style='padding:5px 0px 5px 2px;'>".getCategoryMultipleSelect("--5차분류--", "cid4", "cid", "onChange=\"loadCategory(this,'cid_1',2)\" title='5차분류'", 4, $cid)."</td>
								<td style='padding:5px 4px 5px 6px;'><img src='../images/".$_SESSION["admininfo"]["language"]."/category_add.gif' align=absmiddle border=0 onclick=\"categoryadd()\" style='cursor:pointer;'></td>
							</tr>
						</table>";

				$Contents .= "	</td>
				</tr>
			</table><br>

			<table border=0 cellpadding=0 cellspacing=0 width='100%' style='padding:5px 10px 5px 10px;border:1px solid silver' >
				<col width=100%>
				<tr>
					<td>";
						if($c_ix != ""){
							$Contents .= BrandCategoryRelation($c_ix);
						}else{
				$Contents .= "<table width=100% cellpadding=0 cellspacing=0 id=objCategory >
								<col width=5>
								<col width=50>
								<col width=*>
								<col width=100>
							</table>";
						}
				$Contents .= "	</td>
				</tr>
				<tr><td class='small' height='25' style='padding-left:15px;'> <span class='small'> <!--* 첫번째 선택된 카테고리가 기본카테고리로 지정되며 라디오 버튼 클릭으로 기본카테고리를 변경 하실 수 있습니다>--> ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'E')." </span></td></tr>
			</table><br>";
}

$Contents .= "
			<table border='0' cellspacing='0' cellpadding='0' width='100%' >
			<tr>
				<td bgcolor='#F8F9FA'>
					<table cellpadding=3 cellspacing=0 border=0 width='100%' class='input_table_box'>
						<col width=15%>
						<col width=35%>
						<col width=15%>
						<col width=35%>
						<tr bgcolor=#ffffff height=30>
							<td class='input_box_title'> <b>제조사명 <img src='".$required3_path."'></b></td>
							<td class='input_box_item'>
							<input type=text class=textbox name='company_name' size=41 validation=true title='제조사명' style='height:18px;' value=\"".str_replace("\"","\\\"",$brand_info[company_name])."\">
							</td>
							<td class='input_box_title'> <b>제조사 코드 <img src='".$required3_path."'></b></td>
							<td class='input_box_item'>
							<input type='hidden' name='check_company_code' id='check_company_code' value='".$check_company_code."' >
							<input type='text' class='textbox' name='cp_code' validation='true' title='제조사 코드' style='height:18px; width:80px;' onkeyup=\"checkcompanyCode($(this),$('#check_desc'))\" value=\"".str_replace("\"","\\\"",$brand_info[cp_code])."\">
							<span id='check_desc' style='padding-left:10px;'>제조사 코드를 입력해주세요</span>

							</td>
						</tr>";

					if($admininfo[mall_type] == "B" || $admininfo[mall_type] == "O" || $admininfo[mall_type] == "E"){// 입점형
						$Contents .= "<tr bgcolor=#ffffff height=30>											
							<td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b></td>
							<td class='input_box_item'>";
						if($admininfo[admin_level] == 9){
						$Contents .= "<input type=radio name=disp class=nonborder value=0 id='disp_0' validation=false title='사용유무' ".($brand_info[disp] == "0" ? "checked":"")."><label for='disp_0'>사용하지않음</label>
									<input type=radio name=disp class=nonborder value=1 id='disp_1' validation=false title='사용유무' ".($brand_info[disp] == "1" || $brand_info[disp] == ""? "checked":"")."><label for='disp_1'>사용</label>
									<!--input type=radio name=disp class=nonborder value=2 id='disp_2' validation=false title='사용유무' ".($brand_info[disp] == "2" ? "checked":"")."><label for='disp_2'>신청</label-->";
						}else if($admininfo[admin_level] == 8){
							if($brand_info[disp] == "0"){
								$Contents .= "사용하지 않음";
							}else if($brand_info[disp] == "1"){
								$Contents .= "사용";
							}else if($brand_info[disp] == "2"){
								$Contents .= "신청";
							}
							$Contents .= "<input type=hidden name=disp value='".$brand_info[disp]."'>";
						}
		$Contents .= "		</td>
							<td class='input_box_title'> <b>신청상태 <img src='".$required3_path."'></b></td>
							<td class='input_box_item'>
								<input type=radio name=status class=nonborder value=0 id='apply_status_0' validation=false title='사용유무'  ".($brand_info[status] == "0" ? "checked":"")."><label for='apply_status_0'>승인거부</label>
								<input type=radio name=status class=nonborder value=1 id='apply_status_1' validation=false title='사용유무'  ".($brand_info[status] == "1" || $brand_info[status] == ""? "checked":"")."><label for='apply_status_1'>승인</label>
								<input type=radio name=status class=nonborder value=2 id='apply_status_2' validation=false title='사용유무'  ".($brand_info[status] == "2" ? "checked":"")."><label for='apply_status_2'>신청중</label>
								<input type=radio name=status class=nonborder value=3 id='apply_status_3' validation=false title='사용유무'  ".($brand_info[status] == "3" ? "checked":"")."><label for='apply_status_3'>신청보류</label>
							</td>									
						</tr>";
					}

			$Contents .= "<tr>
							<td class='input_box_title' style='font-size:12px;'>
							<b>제조사 분류 <img src='".$required3_path."'></b> <input type='button' name='search_company_category' id='search_company_category' value='검색' onclick=\"PoPWindow('./search_company_category.php?group_code=',600,600,'add_company_category')\" style='cursor:pointer;'>
							</td>
							<td class='input_box_item' colspan=3 nowrap>
								<div id='selected_category_5' style='padding:10px 0px 10px 0px;'>
								<table width='100%' cellpadding='0' cellspacing='0' id='objDepartment'>
									<colgroup>
									<col width='*'>
									<col width='150'>
									</colgroup>
									<tbody>
									".$brand_category."
									</tbody>
								</table>
								</div>
							</td>
						</tr>
						<tr>
							<td class='input_box_title' nowrap> <b>제조사 주소 </b></td>
							<td class='input_box_item' colspan=3>
								<div id='input_address_area' style='padding:5px 0px 5px 0px;' >
								<table border='0' cellpadding='0' cellspacing='0' style='table-layout:fixed;width:100%'>
									<col width='80px'>
									<col width='*'>
									<tr>
										<td height=26>
											<input type=text name='zipcode1' id='zipcode1' value='".$brand_info[zipcode]."'  maxlength='7' style='width:60px' class='textbox' validation='true' title='우편코드' readonly>
										</td>
										<td style='padding:1px 0 0 5px;'>
											<img src='../images/".$admininfo["language"]."/btn_search_address.gif' onclick=\"zipcode('1')\" style='cursor:pointer;'>
										</td>
									</tr>
									<tr>
										<td colspan=2 height=26>
											<input type=text name='addr1'  id='addr1' value='".$brand_info[addr1]."' size=50 class='textbox'  style='width:300px' readonly>
										</td>
									</tr>
									<tr>
										<td colspan=2 height=26>
											<input type=text name='addr2'  id='addr2'  value='".$brand_info[addr2]."' size=70 class='textbox'  style='width:300px' > (상세주소)
										</td>
									</tr>
									</table>
								</div>
							</td>
						</tr>
						<tr bgcolor=#ffffff height=60>
							<td class='input_box_title' nowrap> <b>제조사 간략설명 </b></td>
							<td class='input_box_item' colspan=3>
							<textarea type='text' name='cp_shotinfo' class=textbox style='width:90%;height:40px;padding:3px;'  maxlength='80'>".$brand_info[cp_shotinfo]."</textarea>
							</td>
						</tr>";
				$Contents .= "
							</td>
						</tr>
					</table>
					<table width='100%' cellpadding=0 cellspacing=0 border=0 bgcolor='#ffffff'>
						
						<tr >
							<td align=right nowrap style='padding:10px 0px'>
								<table cellpadding=0 cellspacing=0>
								<tr>
									<td style='padding-right:5px;'>";
									if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
							$Contents .= "<img src='../images/".$admininfo["language"]."/b_del.gif' border=0 id=delete style='cursor:pointer;display:none' onclick=\"brand_del(document.brandform);\">
										<script>
										function brand_del(frm){
											var select = confirm(frm.brand.value + '을(를) 삭제하시겠습니까?');
											if(select){
												CompanySubmit(frm,'delete');
											}else{
												return false;
											}
										}
										</script>
										";
									}else{
							$Contents .= "<a href=\"".$auth_delete_msg."\"><img src='../image/b_del.gif' border=0 id=delete style='cursor:pointer;display:none' ></a>";
									}
							$Contents .= "
									</td>
									<td>";
									if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
							$Contents .= "<img src='../images/".$admininfo["language"]."/bt_modify.gif' border=0 id=modify style='cursor:pointer;display:none' onclick=\"CompanySubmit(document.brandform,'update')\">";
									}else{
							$Contents .= "<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bt_modify.gif' border=0 id=modify style='cursor:pointer;display:none' ></a>";
									}
							$Contents .= "
									</td>
								</tr>
								</table>
							</td>
							<td align=right style='padding:10px 0px'>";
							if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){//b_save.gif
								$Contents .= " <img src='../images/".$admininfo["language"]."/b_save.gif' id=ok border=0 align=absmiddle style='cursor:pointer' onclick=\"CompanySubmit(document.brandform,'".$act."')\">";
							}else{
								$Contents .= "<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' id=ok border=0 align=absmiddle style='cursor:pointer' ></a>";
							}
			$Contents .= "</td>
						</tr>
						
					</table>
					</td>
				</tr>
			</table><br>
			</form>
			</td>
		</tr>
	</table>
<iframe name='extand' id='extand' src='' width=0 height=0></iframe>";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("제조사등록", $help_text);

$Script = "
<script language='javascript'>

function deleteImage(imagetype, b_ix){
	if(confirm('해당이미지를 정말로 삭제하시겠습니까?')){		
		window.frames['act'].location.href = './company.act.php?mode=image_delete&imagetype='+imagetype+'&c_ix='+c_ix;
	}
}

function loadCategory(sel,target) {
	//alert(target);
	var trigger = sel.options[sel.selectedIndex].value;
	var form = sel.form.name;
	//var depth = sel.depth; // 호환성 kbk
	var depth = sel.getAttribute('depth');
	//if(depth == 2){
	//document.write('category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target);
	//}
	//alert(trigger);
	//dynamic.src = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target; // 호환성 kbk

	if(sel.selectedIndex!=0) {
		// 크롬과 파폭에서 동작을 한번밖에 안하기에 iframe으로 처리 방식을 바꿈 2011-04-07 kbk
		//document.getElementById('act').src = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
		window.frames['act'].location.href = 'category.load.php?form=' + form + '&trigger=' + trigger + '&depth='+depth+'&target=' + target;
	}

}

function cd_del(dp_ix){
	
	$('#cd_row_'+dp_ix).remove();
}

function zipcode(type){
	var zip = window.open('../member/zipcode.php?zip_type='+type+'&obj_id=input_address_area','','width=440,height=300,scrollbars=yes,status=no');
}
</script>
";

if($mmode == "pop"){
	$Script = "<script language='JavaScript' src='brand.js'></script>\n".$Script;
	$P = new ManagePopLayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "Init(document.brandform);";
	$P->Navigation = "상품관리 > 상품분류관리 > 제조사등록";
	$P->NaviTitle = "제조사등록";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();
}else{
	$Script = "<script language='JavaScript' src='brand.js'></script>\n".$Script;
	$P = new LayOut();
	$P->addScript = $Script;
	$P->OnloadFunction = "";
	$P->Navigation = "상품관리 > 상품분류관리 > 제조사등록";
	$P->title = "제조사등록";
	$P->strLeftMenu = product_menu();
	$P->strContents = $Contents;
	echo $P->PrintLayOut();

}


function BrandCategoryRelation($c_ix){
	global $db ,$admininfo;

	$sql = "select c.cid,c.cname,c.depth,r.basic, r.crid, r.regdate  
				from shop_company_relation r, ".TBL_SHOP_CATEGORY_INFO." c 
				where c_ix = '$c_ix' and c.cid = r.cid ORDER BY r.regdate ASC ";
	$db->query($sql);

	$mString = "<table width=100% cellpadding=0 cellspacing=0 id=objCategory>";
	if ($db->total == 0){
		//$mString = $mString."<tr bgcolor=#ffffff height=45><td colspan=5 align=center>선택된 카테고리 정보가 없습니다.</td></tr>";
	}else{
		$i=0;
		for($i=0;$i<$db->total;$i++){
			$db->fetch($i);
			$parent_cname = GetParentCategory($db->dt[cid],$db->dt[depth]);
			$mString .= "<tr>
				<td class='table_td_white small ' width='5' height='25'><input type='text' name='category[]' id='_category' value='".$db->dt[cid]."' style='display:none'></td>
				<td class='table_td_white small' width='50'><input type='radio' name='basic' value='".$db->dt[cid]."' ".($db->dt[basic] == 1 ? "checked":"")."></td>
				<td class='table_td_white small ' width='*'>".($parent_cname != "" ? $parent_cname." > ":"").$db->dt[cname]."</td>
				<td class='table_td_white' width='100'><!--a href=\"JavaScript:void(0)\" onClick='category_del(this.parentNode.parentNode)'--><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align='absmiddle' onClick='category_del(this.parentNode.parentNode)' style='cursor:pointer;' /><!--/a--></td>
				</tr>";
		}
	}
	//$mString .= "<tr align=center bgcolor=silver height=1><td colspan=5></td></tr>";
	$mString = $mString."</table>";

	return $mString;
}


function getCategoryListForBrand($category_text ="기본카테고리 선택", $object_name="cid",$id="cid", $onchange_handler="", $depth=0, $cid="")
{
	$mdb = new Database;
	$tb = (defined('TBL_SNS_CATEGORY_INFO'))	?	TBL_SNS_CATEGORY_INFO:TBL_SHOP_CATEGORY_INFO;
	if($depth == 0 || $cid != ""){
		$sql = "SELECT * FROM $tb where depth ='$depth' and cid LIKE '".substr($cid,0,($depth)*3)."%' order by vlevel1, vlevel2, vlevel3, vlevel4, vlevel5 ";
		//echo $sql;
		$mdb->query($sql);
	}

	if ($mdb->total){
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' validation=false style='width:165px;font-size:12px;' title='카테고리'>\n";
		$mstring = $mstring."<option value=''>$category_text</option>\n";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			//if(substr_count($cid,substr($mdb->dt[cid],0,$mdb->dt[depth]+1))){
			if(substr($cid,0,($depth+1)*3) == substr($mdb->dt[cid],0,($depth+1)*3)){

				$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' selected>".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}else{
				$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".$mdb->dt[cname]."</option>\n";
				//$mstring = $mstring."<option value='".$mdb->dt[cid]."' >".substr($cid,0,($depth+1)*3)." == ".substr($mdb->dt[cid],0,($depth+1)*3)."</option>\n";
			}
		}
	}else{
		$mstring = "<Select name='$object_name' depth='$depth' $onchange_handler id='$id' validation=false  style='width:140px;'>\n";
		$mstring = $mstring."<option value=''> $category_text</option>\n";
	}

	$mstring = $mstring."</Select>\n";

	return $mstring;
}



/*
CREATE TABLE IF NOT EXISTS `shop_brand_relation` (
  `brid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '일련번호',
  `cid` varchar(15) NOT NULL COMMENT '카테고리 코드',
  `b_ix` int(10) unsigned zerofill DEFAULT NULL COMMENT '브랜드키',
  `disp` char(1) DEFAULT '1' COMMENT '노출 여부 (1:노출)',
  `basic` enum('1','0') DEFAULT NULL COMMENT '기본 카테고리 여부 (1:기본 카테고리)',
  `insert_yn` enum('Y','N') DEFAULT NULL COMMENT '입력여부 (관리자만 사용)',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '등록일',
  PRIMARY KEY (`brid`),
  KEY `IDX_MPR_CID` (`cid`,`b_ix`),
  KEY `pid` (`b_ix`),
  KEY `regdate` (`regdate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='카테고리 브랜드 등록정보' 
*/
?>