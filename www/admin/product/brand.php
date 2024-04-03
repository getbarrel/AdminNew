<?
include("../class/layout.class");
include("../webedit/webedit.lib.php");
include_once("brand.lib.php");

$language_list = getTranslationType("","","array");
$globalInfo = getGlobalInfo();

$db = new Database;
$db2 = new Database;
$db->query("SELECT * FROM shop_brand WHERE b_ix='$b_ix'");

if($db->total){
    $db->fetch();
    $act = "update";
    $brand_info = $db->dt;
    $check_brand_code = 1;

    /*$db->query("SELECT parent_bd_ix FROM shop_brand_div WHERE bd_ix='".$brand_info[bd_ix]."'");
    $db->fetch();

    $parent_bd_ix = $db->dt[parent_bd_ix];*/
    $db->query("SELECT * FROM shop_brand_div WHERE bd_ix = '".$brand_info[bd_ix]."'");
    $db->fetch();
    if($db->total){
        $db->fetch();
        if($db->dt["depth"] == 1) {
            $parent_bd_ix = $db->dt[bd_ix];
            $div_name = $db->dt[div_name];
            $brand_category = "<tr style='height:26px;' id='department_row_".$parent_bd_ix."'><td><input type=hidden name=bd_ix id='department_".$parent_bd_ix."' value='".$parent_bd_ix."'>".$div_name."</td><td><a href='javascript:void(0)' onClick='department_del(\'".$parent_bd_ix."\');'><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0></a></td></tr>";
        } else {
            $parent_bd_ix = $db->dt[parent_bd_ix];
            $db2->query("SELECT * FROM shop_brand_div WHERE parent_bd_ix='".$db->dt[parent_bd_ix]."'");
            $db2->fetch();
            $div_name_1 = $db2->dt[div_name];
            $div_name_2 = $db->dt[div_name];
            $brand_category = "<tr style='height:26px;' id='department_row_".$parent_bd_ix."'><td><input type=hidden name=bd_ix id='department_".$parent_bd_ix."' value='".$parent_bd_ix."'>".$div_name_1." > ".$div_name_2."</td><td><a href='javascript:void(0)' onClick=\"department_del('".$parent_bd_ix."')\"><img src='../images/".$_SESSION["admininfo"]["language"]."/btc_del.gif' border=0></a></td></tr>";
        }
    }
}else{
    $act = "insert";
    $check_brand_code = 0;
}

$Contents = "
	<table cellpadding=0 cellspacing=0 border=0 width=100%>
	<tr>
		<td >
			".GetTitleNavigation("브랜드설정", "상품관리 > 브랜드설정")."
		</td>
	</tr>
	<tr>
		<td align='left' colspan=4 style='padding-bottom:15px;'>
		".brand_tab("reg")."
		</td>
	</tr>
	<tr height=10>
		<td rowspan=6 valign=top>
		<form name='brandform' action='./brand.act.php' method='post'  onsubmit=\"return BrandInput(this,'".$act."');\" enctype='multipart/form-data'  target='iframe_act'>
		<input type=hidden name=mode value='".$act."'>
		<input type=hidden name=b_ix value='".$b_ix."'>
		<input type='hidden' name='brand_name_division' value=''>
		<input type='hidden' name='mmode' value='$mmode'>";

$Contents .= "	<table width='100%' cellpadding=0 cellspacing=0>
	                <tr height=30>
	                    <td style='padding-bottom:10px;'>".colorCirCleBox("#efefef","100%","<table cellpadding=0 width=100%><tr><td style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'><b class=blk> 브랜드 등록/수정 하기 </b><span class=small> 브랜드 등록은 최하위 카테고리에만 등록해야합니다. 또한 다중등록 가능합니다.
		                                                 ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'D')." </span>
		                                                 </td><td align=right style='padding-right:20px;' class=small><!--a href='JavaScript:SampleProductInsert()'>샘플데이타 넣기</a--></td></tr></table>")."
	                    </td>
	                </tr>
	            </table>";

//if($admininfo[mall_type] == "B" || $admininfo[mall_type] == "O"){

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
if($b_ix != ""){
    $Contents .= BrandCategoryRelation($b_ix);
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
//}

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
							<td class='input_box_title'> <b>브랜드명 <img src='".$required3_path."'></b></td>
							<td class='input_box_item' style='padding:5px;'>";
$Contents .= " 
											<table width='100%' border='0' cellspacing='0' cellpadding='3' >
											";

$Contents .= "<tr> <td style='border:0;'> <input type=text class=textbox name=brand size=41 style='width:80%' validation=true title='브랜드명' style='height:18px;' value=\"".str_replace("\"","\\\"",$brand_info[brand_name])."\"></td></tr>";


if($globalInfo['global_use']=='Y'){

    $global_binfo = json_decode(trim($brand_info[global_binfo]),true);

    if(count($global_binfo) > 0){
        foreach($global_binfo as $colum => $li){
            foreach($li as $ln => $val){
                $global_binfo[$colum][$ln] = urldecode($val);
            }
        }
    }

    if(is_array($language_list)){
        foreach($language_list as $key => $li){
            $Contents .= "<tr> <td style='border:0;'> <input type=text class='textbox' name=\"global_binfo[brand_name][".$li[language_code]."]\" id='global_brand_name_".$li[language_code]."' size=41 style='width:80%' value='".$global_binfo['brand_name'][$li['language_code']]."'  validation=false title='글로벌 브랜드명(".$li[language_name].")' placeholder='" . $li[language_name]."  '> </td> </tr>";
        }
    }


}
$Contents .= "</table> ";
$Contents .= "

							</td>
							<td class='input_box_title'> <b>브랜드 코드</b></td>
							<td class='input_box_item'>

							<input type='hidden' name='check_brand_code' id='check_brand_code' value='".$check_brand_code."' >
							<input type='text' class='textbox' name='brand_code' title='브랜드 코드' style='height:18px; width:80px;' onkeyup=\"checkBrandCode($(this),$('#check_desc'))\" value=\"".str_replace("\"","\\\"",$brand_info[brand_code])."\">
							<span id='check_desc' style='padding-left:10px;'>브랜드 코드를 입력해주세요</span>
							</td>
						</tr>
				";
if($admininfo[mall_type] == "B" || $admininfo[mall_type] == "O" || $admininfo[mall_type] == "E"  || $admininfo[mall_type] == "P"){// 입점형
    $Contents .= "<tr bgcolor=#ffffff height=30>											
							<td class='input_box_title'> <b>사용유무 <img src='".$required3_path."'></b></td>
							<td class='input_box_item' ";
    /**
     *  2017.07.24 sehyun : PVF 6. 브랜드는 셀러에게 신청받는게 아니라 본사에서 주도적으로 관리 하기 때문에 신청상태값은 비노출 처리 필요
     */
    if($_SESSION[admininfo][admin_id] != 'forbiz'){
        $Contents .= "colspan='3'";
    }
    //end
    $Contents .= ">";
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
    $Contents .= "		</td>";
    /**
     *  2017.07.24 sehyun : PVF 6. 브랜드는 셀러에게 신청받는게 아니라 본사에서 주도적으로 관리 하기 때문에 신청상태값은 비노출 처리 필요
     */
    if($_SESSION[admininfo][admin_id] == 'forbiz'){
        $Contents .= "
                        <td class='input_box_title'> <b>신청상태 <img src='".$required3_path."'></b></td>
                        <td class='input_box_item' >
                            <input type = radio name = apply_status class=nonborder value = 0 id = 'apply_status_0' validation = false title = '사용유무'  ".($brand_info[apply_status] == "0" ? "checked":"")." ><label for='apply_status_0' > 승인거부</label >
                            <input type = radio name = apply_status class=nonborder value = 1 id = 'apply_status_1' validation = false title = '사용유무'  ".($brand_info[apply_status] == "1" || $brand_info[apply_status] == ""? "checked":"")." ><label for='apply_status_1' > 승인</label >
                            <input type = radio name = apply_status class=nonborder value = 2 id = 'apply_status_2' validation = false title = '사용유무'  ".($brand_info[apply_status] == "2" ? "checked":"")." ><label for='apply_status_2' > 신청중</label >
                            <input type = radio name = apply_status class=nonborder value = 3 id = 'apply_status_3' validation = false title = '사용유무'  ".($brand_info[apply_status] == "3" ? "checked":"")." ><label for='apply_status_3' > 신청보류</label >
                        </td >";
    }
    //end
    $Contents .= "</tr>";
}
$Contents .= "<tr>
							<td class='input_box_title' style='font-size:12px;'>
							<b>브랜드 분류</b> <input type='button' name='search_brand_category' id='search_brand_category' value='검색' onclick=\"PoPWindow('./search_brand_category.php?group_code=',600,568,'add_brand_category')\" style='cursor:pointer;'>
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
						<tr bgcolor=#ffffff height=60>
							<td class='input_box_title' nowrap> <b>브랜드 간략설명 </b></td>
							<td class='input_box_item' colspan=3>
							<textarea type='text' name='shotinfo' class=textbox style='width:90%;height:40px;padding:3px;'  maxlength='80'>".$brand_info[shotinfo]."</textarea>
							</td>
						</tr>";
//브랜드 노출 순서를 위해서 작업 -  20151014
$Contents .= "<tr bgcolor=#ffffff height=30>
							<td class='input_box_title' nowrap> <b>브랜드 노출순서 </b></td>
							<td class='input_box_item' colspan=3>
								<input type='text' class='textbox' name='vieworder' size=3 title='브랜드 노출순서' value='".$brand_info[vieworder]."' >
							</td>
						</tr>";
$Contents .= "
							</td>
						</tr>
					</table>";
$Contents .= "
					<table width='100%' border=0 bgcolor='#ffffff'>
						<tr height=30><td width=100% style='padding:0px 0px 0px 10px'><img src='/admin/images/dot_org.gif' align=absmiddle> <b class=blk>브랜드로고 변경</b><br></td></tr>
					</table>
					<table cellpadding=3 cellspacing=0  class='line_color' border=0 width='100%' class='input_table_box'>
						<col width=15%>
						<col width=35%>
						<col width=15%>
						<col width=35%>
						<tr bgcolor=#ffffff height=30>
							<td class='input_box_title' rowspan=2 nowrap> <b>브랜드 로고등록</b> <span class=small>(134*50) </span></td>
							<td class='input_box_item' colspan=3>
							<input type=file class='textbox' title='브랜드 로고등록' name='brandimg' size=15 style='font-size:8pt;'>
							</td>
						</tr>
						<tr bgcolor=#ffffff height=100>
							<td id='brandimgarea' colspan=3 style='padding:5px;'>";
if($b_ix && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$b_ix.".gif")){
    $image_info = getimagesize ($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$b_ix.".gif");
    $width = $image_info[0];

    $Contents .= "<div style='padding:5px;'><a href=\"javascript:deleteImage('brandimg','".$b_ix."');\">[삭제]</a></div>";

    if($width > 110){
        $Contents .= "<img src='".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$brand_info[b_ix].".gif' width=110>";
    }else{
        $Contents .= "<img src='".$admin_config[mall_data_root]."/images/brand/$b_ix/brand_".$brand_info[b_ix].".gif'>";
    }
}

$Contents .= "
							</td>
						</tr>
					</table>";
$Contents .= "
					<table width='100%' border=0 bgcolor='#ffffff'>
						<tr height=30><td width=100% style='padding:0px 0px 0px 10px'><img src='/admin/images/dot_org.gif' align=absmiddle> <b class=blk>브랜드 백그라운드 이미지 변경</b><br></td></tr>
					</table>
					<table cellpadding=3 cellspacing=0  class='line_color' border=0 width='100%' class='input_table_box'>
						<col width=15%>
						<col width=35%>
						<col width=15%>
						<col width=35%>
						<tr bgcolor=#ffffff height=30>
							<td class='input_box_title' rowspan=2 nowrap> <b>브랜드 백그라운드 이미지 등록</b> <span class=small>(590*500) </span></td>
							<td class='input_box_item' colspan=3>
							<input type=file class='textbox' title='브랜드 백그라운드 이미지 등록' name='brandbgimg' size=15 style='font-size:8pt;'>
							</td>
						</tr>
						<tr bgcolor=#ffffff height=100>
							<td id='brandimgarea' colspan=3 style='padding:5px;'>";
if($b_ix && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brandbg_".$b_ix.".gif")){
    $image_info = getimagesize ($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/brandbg_".$b_ix.".gif");
    $width = $image_info[0];

    $Contents .= "<div style='padding:5px;'><a href=\"javascript:deleteImage('brandimg','".$b_ix."');\">[삭제]</a></div>";

    if($width > 110){
        $Contents .= "<img src='".$admin_config[mall_data_root]."/images/brand/$b_ix/brandbg_".$brand_info[b_ix].".gif' width=110>";
    }else{
        $Contents .= "<img src='".$admin_config[mall_data_root]."/images/brand/$b_ix/brandbg_".$brand_info[b_ix].".gif'>";
    }
}

$Contents .= "
							</td>
						</tr>






						<tr>
						  <td class='search_box_title'><b>전시상품</b><span style='padding-left:2px' class='helpcloud' help_width='300' help_height='30' help_html='자동등록에 경우 사용할 카테고리를 선택하게 되면 상품등록 시 자동으로 신규 상품이 전시됩니다.'><img src='/admin/images/icon_q.gif' /></span></td>
						  <td class='search_box_item' style='padding:10px 10px;' colspan=3>
						   <div style='padding-bottom:10px;'>
							  <input type='radio' class='textbox' name='goods_display_type[".($i+1)."]' id='use_".($i+1)."_m' size=50 value='M' style='border:0px;' ".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "checked":"")." onclick=\"$('#goods_manual_area_".($i+1)."').toggle();$('#goods_auto_area_".($i+1)."').toggle();\"><label for='use_".($i+1)."_m'>수동등록</label>
						  </div>
						  <div id='goods_manual_area_".($i+1)."' style='".(($gdb->dt[goods_display_type] == "M" || $gdb->dt[goods_display_type] == "") ? "display:block;":"display:none;")."'>
							  <a href=\"javascript:\" onclick=\"ms_productSearch.show_productSearchBox(event,".($i+1).",'productList_".($i+1)."');\"><img src='../images/".$admininfo["language"]."/btn_goods_search_add.gif' border=0 align=absmiddle></a><br>
							  <div style='width:100%;padding:5px;' id='group_product_area_".($i+1)."' >".relationEventGroupProductList($gdb->dt[pg_ix],($gdb->dt[group_code] ? $gdb->dt[group_code]:($i+1)), "clipart")."</div>
							  <div style='width:100%;float:left;'><span class=small><!--* 이미지 드레그앤드롭으로 노출 순서를 조정하실수 있습니다.<br />* 더블클릭 시 상품이 개별 삭제 됩니다.--> </span>
							  </div>
						  </div>
						  <div style='padding:0px 0px;".($gdb->dt[goods_display_type] == "A" ? "display:block;":"display:none;")."' id='goods_auto_area_".($i+1)."'>
							<a href=\"javascript:PoPWindow('../display/category_select.php?mmode=pop&group_code=".($i+1)."',660,300,'category_select')\"'><img src='../images/".$admininfo["language"]."/btn_select_category.gif' border=0 align=absmiddle></a><br>
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

					</table>";



$sql = sprintf("SELECT bpb_ix, b_ix, file_name, link, display_order 
						  FROM shop_brand_promotion_banner 
						 WHERE type='P' and b_ix = %d", $b_ix);
$db->query($sql);
$bannerPC = $db->fetchall();

$sql = sprintf("SELECT bpb_ix, b_ix, file_name, link, display_order 
						  FROM shop_brand_promotion_banner 
						 WHERE type='M' and b_ix = %d", $b_ix);
$db->query($sql);
$bannerM = $db->fetchall();

$Contents .= "
					<table width='100%' border=0 bgcolor='#ffffff'>
						<tr height=30><td width=100% style='padding:0px 0px 0px 10px'><img src='/admin/images/dot_org.gif' align=absmiddle> <b class=blk>상단 프로모션 이미지</b><br></td></tr>
					</table>";
$Contents .= "<table cellpadding=3 cellspacing=0  class='line_color' border=0 width='100%' class='input_table_box'>
						<col width=15%>
						<col width=35%>
						<col width=15%>
						<col width=35%>
						<tr bgcolor=#ffffff>
							<td class='input_box_title' nowrap> <b>배너이미지(PC)</b> <a href='javascript:void(0)' id='addPCBanner' onclick=\"addPCBanner('pc')\">추가</a></td>
							<td id='pcBannerView'>";
$i = 0;
if(is_array($bannerPC)){
    foreach($bannerPC as $item){
        $Contents .= "</br><ul name='pcBannerRow'>
								<li><input type='hidden' name='modiBannerPC[]' value='" . $item[bpb_ix] . "'>
								첨부파일<input type=file class='textbox' title='첨부파일' name='bannerPCImage[]' size=15 style='font-size:8pt;'></li>";
        $Contents .= sprintf("   <img src='%s' width=110px>", $admin_config[mall_data_root]."/images/brand/" . $item[b_ix] . "/brand_banner_" . $i . "_" .  $item[b_ix] . ".gif");

        $Contents .= sprintf("
								<li>링크<input type=text class='textbox' title='링크' name='linkPC[]' size=15 style='font-size:8pt;' value='%s'></li>
								<li>노출순서<input type=text class='textbox' title='노출순서' name='displayOrderPC[]' size=15 value='%d' style='font-size:8pt;'></li>", $item['link'], $item['display_order']);
        $Contents .=  "</ul>";
        $i++;
    }
}
$Contents .= "		
							</td>
						</tr>
						<tr bgcolor=#ffffff>
							<td class='input_box_title' nowrap> <b>배너이미지(Mobile)</b> <a href='javascript:void(0)' id='addPCBanner' onclick=\"addPCBanner('m')\">추가</a></td>
							<td id='mBannerView'>";
$i2 = 0;
if(is_array($bannerM)){
    foreach($bannerM as $item2){
        $Contents .= "</br><ul name='mBannerRow'>
								<li><input type='hidden' name='modiBannerM[]' value='" . $item2[bpb_ix] . "'>
								첨부파일<input type=file class='textbox' title='첨부파일' name='bannerMImage[]' size=15 style='font-size:8pt;'></li>";
        $Contents .= sprintf("   <img src='%s' width=110px>", $admin_config[mall_data_root]."/images/brand/" . $item2[b_ix] . "/m_brand_banner_" . $i2 . "_" .  $item2[b_ix] . ".gif");

        $Contents .= sprintf("
								<li>링크<input type=text class='textbox' title='링크' name='linkM[]' size=15 style='font-size:8pt;' value='%s'></li>
								<li>노출순서<input type=text class='textbox' title='노출순서' name='displayOrderM[]' size=15 value='%d' style='font-size:8pt;'></li>", $item2['link'], $item2['display_order']);
        $Contents .=  "</ul>";
        $i2++;
    }
}
$Contents .= "		
							</td>
						</tr>
					  </table>";


$Contents .= "
					<table width='100%' border=0 bgcolor='#ffffff'>
						<tr height=30><td width=100% style='padding:0px 0px 0px 10px'><img src='/admin/images/dot_org.gif' align=absmiddle> <b class=blk>브랜드페이지 상단 디자인</b><br></td></tr>
					</table>
					<table cellpadding=3 cellspacing=0  class='line_color' border=0 width='100%' class='input_table_box'>
					<col width=15%>
					<col width=35%>
					<col width=15%>
					<col width=35%>
					<tr bgcolor=#ffffff height=30>
						<td class='input_box_title' rowspan=2 nowrap> <b>상단이미지</b> <span class=small></span></td>
						<td class='input_box_item' colspan=2>
						<input type=file class='textbox' title='상단이미지' name='brand_banner_img' size=15 style='font-size:8pt;'>
						</td>
					</tr>
					<tr bgcolor=#ffffff height=100>
						<td id='brand_banner_imgarea' colspan=2>";
if($b_ix && file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/b_brand_".$b_ix.".gif")){
    $image_info = getimagesize ($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/brand/$b_ix/b_brand_".$b_ix.".gif");
    $width = $image_info[0];
    $Contents .= "<div style='padding:5px;'><a href=\"javascript:deleteImage('brand_banner_img','".$b_ix."');\">[삭제]</a></div>";
    if($width > 200){
        $Contents .= "<img src='".$admin_config[mall_data_root]."/images/brand/$b_ix/b_brand_".$brand_info[b_ix].".gif' width=100>";
    }else{
        $Contents .= "<img src='".$admin_config[mall_data_root]."/images/brand/$b_ix/b_brand_".$brand_info[b_ix].".gif'>";
    }
}
$Contents .= "
						</td>
					</tr>
					<tr>
						<td class='input_box_title' rowspan=2 nowrap> <b>HTML 적용</b> <span class=small></span></td>
						<td colspan='2'>
							<textarea name='top_design' id='top_design' style='display:none' >".$brand_info["top_design"]."</textarea>
						</td>
					</tr>
					</table>
					<table width='100%' cellpadding=0 cellspacing=0 border=0>
						
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
												BrandSubmit(frm,'delete');
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
    $Contents .= "<img src='../images/".$admininfo["language"]."/bt_modify.gif' border=0 id=modify style='cursor:pointer;display:none' onclick=\"BrandSubmit(document.brandform,'update')\">";
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
    $Contents .= " <img src='../images/".$admininfo["language"]."/b_save.gif' id=ok border=0 align=absmiddle style='cursor:pointer' onclick=\"BrandSubmit(document.brandform,'".$act."')\">";
}else{
    $Contents .= "<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' id=ok border=0 align=absmiddle style='cursor:pointer' ></a>";
}
$Contents .= "</td>
						</tr>
						
					</table>
					</form>
					</td>
				</tr>
			</table><br>
			</td>
		</tr>
	</table>
<iframe name='extand' id='extand' src='' width=0 height=0></iframe>";

$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A');

$Contents .= HelpBox("브랜드설정", $help_text);

$Script = "
<script type='text/javascript' src='/admin/js/jquery.multisortable.js'></script>
<script type='text/javascript' src='../js/ms_productSearch.js'></script>

<script language='javascript'>

$(document).ready(function() {

	// 최초 등록시
//	$('#addPCBanner').on('click', );
	
	CKEDITOR.replace('top_design',{
		startupFocus : false,height:200
	});
});

function deleteImage(imagetype, b_ix){
	if(confirm('해당이미지를 정말로 삭제하시겠습니까?')){		
		window.frames['act'].location.href = './brand.act.php?mode=image_delete&imagetype='+imagetype+'&b_ix='+b_ix;
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

function bd_del(dp_ix){
	$('#bd_row_'+dp_ix).remove();
}
</script>
";

if($mmode == "pop"){
    $Script = "<script language='JavaScript' src='brand.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>\n".$Script;
    $P = new ManagePopLayOut();
    $P->addScript = $Script;
    $P->OnloadFunction = "Init(document.brandform);";
    $P->Navigation = "상품관리 > 상품분류관리 > 브랜드설정";
    $P->NaviTitle = "브랜드설정";
    $P->strLeftMenu = product_menu();
    $P->strContents = $Contents;
    echo $P->PrintLayOut();
}else{
    $Script = "<script language='JavaScript' src='brand.js'></script>\n<script language='JavaScript' src='../ckeditor/ckeditor.js'></script>".$Script;
    $P = new LayOut();
    $P->addScript = $Script;
    $P->OnloadFunction = "";
    $P->Navigation = "상품관리 > 상품분류관리 > 브랜드설정";
    $P->title = "브랜드설정";
    $P->strLeftMenu = product_menu();
    $P->strContents = $Contents;
    echo $P->PrintLayOut();

}


function BrandCategoryRelation($b_ix){
    global $db ,$admininfo;

    $sql = "select c.cid,c.cname,c.depth,r.basic, r.brid, r.regdate  
				from shop_brand_relation r, ".TBL_SHOP_CATEGORY_INFO." c 
				where b_ix = '$b_ix' and c.cid = r.cid ORDER BY r.regdate ASC ";
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

    $sql = "SELECT count(*)
			  FROM shop_display_brand_product bp LEFT JOIN shop_product p on bp.pid = p.id;"; //and p.disp = 1
    $slave_db->query($sql);
    $slave_db->fetch();
    $total = $slave_db->dt[0];

//SELECT p.id, p.pcode, p.shotinfo, p.pname, p.sellprice,  p.reserve, ppr_ix, ppr.vieworder, ppr.group_code, p.brand_name
    /*
        $sql = "SELECT p.id, p.pcode, p.shotinfo, p.pname, p.listprice, p.sellprice, p.wholesale_sellprice, p.wholesale_price,   p.reserve, p.state, p.disp, ppr.vieworder, ppr.group_code, p.brand_name
                        FROM ".TBL_SHOP_PRODUCT." p, shop_promotion_product_relation ppr
                        where p.id = ppr.pid and pg_ix != '' and pg_ix = '$pg_ix' and group_code = '$group_code'
                        order by ppr.vieworder asc limit $start,$max";//and p.disp = 1
    */
    $sql = "SELECT p.id, p.pcode, p.shotinfo, p.pname, p.listprice, p.sellprice, p.wholesale_sellprice, p.wholesale_price,   p.reserve, p.state, p.disp, p.brand_name	
			  FROM shop_display_brand_product bp LEFT JOIN shop_product p on bp.pid = p.id;"; //and p.disp = 1

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