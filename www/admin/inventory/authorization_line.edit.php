<?
include("../class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."/class/bbs.lib.php");
//include_once($_SERVER["DOCUMENT_ROOT"]."/include/sns.config.php");

$db = new Database;

if($option_type == ""){
	$option_type = "basic";
}
if($_GET["al_ix"] != ""){
	$option_input_act = "tmp_update";
}else{
	$option_input_act = "tmp_insert";
}
$mstring ="<form name='authorization_line' method='POST' action='authorization_line.act.php' onsubmit=\"return ChekformValue(this)\" target='iframe_act'><!--target='iframe_act'-->
<input type=hidden name='act' value='".$option_input_act."'><input type=hidden name='al_ix' value='".$al_ix."'>

		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td align='left'> ".GetTitleNavigation("결제라인 등록/수정", "재고관리 > 기초정보관리 > 결제라인 등록/수정 ")."</td>
		</tr>
		<!--tr>
			<td align='left' colspan=8 style='padding-bottom:14px;'>
			<div class='tab'>
					<table class='s_org_tab'>
					<tr>
						<td class='tab'>
							<table id='tab_00'  ".($option_type == "basic" ? "class='on'":"").">
							<tr>
								<th class='box_01'></th>
								<td class='box_02' onclick=\"document.location.href='?option_type=basic&al_ix=".$al_ix."'\">기본옵션</td>
								<th class='box_03'></th>
							</tr>
							</table>



						</td>
						<td align='right' style='text-align:right;vertical-align:bottom;padding:0 0 6px 4px;'>";

	$mstring .= "
						</td>
					</tr>
					</table>
				</div>
			</td>
		</tr-->";

$mstring .="
	<tr>
		<td height=560 valign=top>
		";




$mstring .= "<div ".($option_type == "basic" ? "":"style='display:none;'")." id='basic_option_zone'>";

$mstring .="<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:5px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'> <b class=blk> 결제라인 정보 등록/수정  </b></div>")."</td></tr></table>";


$sql = "select * from common_authline_info where al_ix = '".$al_ix."'  order by regdate asc ";
//echo $sql;
$db->query($sql);

$authorization_line = $db->fetchall();
//print_r($authorization_line);
if($db->total){
	for($i=0;$i < count($authorization_line);$i++){
		$mstring .= "
							<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver id='authorization_line_input' class='authorization_line_input' idx=".$i." style='margin-bottom:10px' >
								<col width='4%'>
								<col width='15%'>
								<col width='*'>
								<col width='23%'>
								<col width='11%'>
								<col width='14%'>
								<col width='10%'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small nowrap>
									사용
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 결제라인명
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 결제라인타입
									</td>
									<td bgcolor=\"#efefef\" class=small> 담당자명 *</td>
									<td bgcolor=\"#efefef\" class=small> 직위 *</td>
									<td bgcolor=\"#efefef\" class=small> 표시이름  </td>
									<td bgcolor=\"#efefef\" class=small> 승인순서  </td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td valign=top style='padding-top:6px;'>
										<input type=hidden name='authorization_line[0][al_ix]' id='option_al_ix' value='".$authorization_line[$i][al_ix]."'>

										<input type=checkbox name='authorization_line[0][al_use]' id='authorization_line_al_use' value='1' ".(($authorization_line[$i][al_use] == 1 || $authorization_line[$i][al_use] == '') ? "checked":"")." style='margin:0 0 0 0' align=absmiddle>
									</td>
									<td valign=top style='padding-top:4px;'>
										";

$mstring .= "			<span id='".$i."'><input type=text class='textbox' name='authorization_line[0][authline_name]' id='authline_name' style='width:115px;vertical-align:middle' value='".$authorization_line[$i][authline_name]."' validation=true title='결제라인명'> </span>

										".($i == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.authorization_line_input').length > 1){ \$('.authorization_line_input[idx=".$i."]').remove();/*this.parentNode.parentNode.parentNode.removeNode(true);*/showMessage('authorization_line_input_status_area','해당 옵션 구분정보가 삭제 되었습니다.');}else{alert(language_data['goods_input.php']['G'][language]);}\" title='더블클릭시 해당 테이블이 삭제 됩니다.'>")." ";

$mstring .= "
									</td>
									<td valign=top style='padding-top:4px;'>
										<select name='authorization_line[0][authline_kind]' id='authline_kind_0' style='font-size:12px;'>
											<option value=b ".($authorization_line[$i][authline_kind] == "b" ? "selected":"").">기본</option>
											<option value=c ".($authorization_line[$i][authline_kind] == "c" ? "selected":"").">사용자 정의</option>

										</select>
										<img src='../images/".$admininfo["language"]."/btn_line_add.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"copyOptions('authorization_line_item_input');showMessage('authorization_line_input_status_area','결제라인명정보가 추가 되었습니다.');\" />
									</td>
									<td colspan=4 id='authorization_line_input_table'><input type=hidden id='authorization_line_item_department' inputid='authorization_line_item_department' value=''><input type=hidden id='authorization_line_item_disp_name' value=''>
									";

			$sql = "select * from common_authline_detail_info where al_ix = '".$authorization_line[$i][al_ix]."' order by aldt_ix asc ";
			//echo $sql;
			$db->query($sql);
			$authorization_detial_infos = $db->fetchall();

			if($db->total){
				for($j=0;$j < count($authorization_detial_infos);$j++){
				//$db->fetch($j);

		$mstring .= "<table width=100% id='authorization_line_item_input' class='authorization_line_item_input' idx=".$i." aldt_ix=".$j." cellspacing=4 cellpadding=0 >
											<col width='34%'>
											<col width='19%'>
											<col width='24%'>
											<col width='*'>
											<tr>
												<td style='padding-left:5px;' nowrap>
												<input type=hidden name='authorization_line[0][details][".$j."][aldt_ix]' id='authorization_line_aldt_ix' value='".$authorization_detial_infos[$j][aldt_ix]."' style='width:20px;'>
												".makeDepartmentSelectBox($db,"authorization_line[0][details][".$j."][department]",$authorization_detial_infos[$j][department],"select","부서", " validation=true title='부서' class='combobox' id='authorization_line_department'  onchange=\"loadUser(this,'authorization_line[0][details][".$j."][charger_ix]')\"")."

												".makeCompanyUserList($admininfo["company_id"],"authorization_line[0][details][".$j."][charger_ix]", $authorization_detial_infos[$j][department], $authorization_detial_infos[$j][charger_ix],"validation=true title='담당자'  class='authorization_line_user combobox' id='authorization_line_user'")."
												</td>
												<td><input type=text class='textbox' name='authorization_line[0][details][".$j."][position]' id='authorization_line_position' style='width:80%' value='".$authorization_detial_infos[$j][position]."' >
												<input type=hidden class='textbox' name='authorization_line[0][details][".$j."][charger_name]' id='authorization_line_charger_name' style='width:80%' value='".$authorization_detial_infos[$j][charger_name]."' ></td>";
												$mstring .= "	<td><input type=text class='textbox' name='authorization_line[0][details][".$j."][disp_name]' id='authorization_line_disp_name' style='width:80%' value='".$authorization_detial_infos[$j][disp_name]."'></td>
												<td>
												<input type=text class='textbox' name='authorization_line[0][details][".$j."][order_approve]' id='authorization_line_order_approve' style='width:50%' value='".$authorization_detial_infos[$j][order_approve]."' validation='true' title='승인순서'> <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.authorization_line_item_input').length > 1){document.getElementById('authorization_line_input_table').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.parentNode.removeNode(true);*/showMessage('authorization_line_input_status_area','결제라인명정보가 삭제되었습니다.');}else{clearInputBox('authorization_line_item_input');showMessage('authorization_line_input_status_area','결제라인명정보가 삭제되었습니다.');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
												</td>";
	//	$mstring .= "			<td><input type=text class='textbox' name='authorization_line[0][details][".$j."][disp_name]' size=28 style='width:85%' value='".$authorization_detial_infos[$j][disp_name]."'> ".($j == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if(document.all.authorization_line_item_input.length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>")."</td>";

		$mstring .= "
											</tr>
										</table>";
					}
			}else{
		$mstring .= "<table width=100% id='authorization_line_item_input' class='authorization_line_item_input' idx=0 aldt_ix=0 cellspacing=4 cellpadding=0 ><!--ondblclick=\"if(document.all.authorization_line_item_input_0.length > 1){this.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\"-->
											<col width='34%'>
											<col width='19%'>
											<col width='24%'>
											<col width='*'>
											<tr>
												<td nowrap>
												".makeDepartmentSelectBox($db,"authorization_line[0][details][0][department]",$dp_ix,"select","부서", "class='combobox' id='authorization_line_department' onchange=\"loadUser(this,'authorization_line[0][details][0][charger_ix]')\"")."
												".makeCompanyUserList($admininfo["company_id"],"authorization_line[0][details][0][charger_ix]", $dp_ix, $charger_ix,"class='authorization_line_user combobox' id='authorization_line_user' ")."
												</td>
												<td>
												<input type=text class='textbox' name='authorization_line[0][details][0][position]' id='authorization_line_position' style='width:80%' value='직위'>
												<input type=hidden class='textbox' name='authorization_line[0][details][0][charger_name]' id='authorization_line_charger_name' style='width:80%' value='담당자이름'>
												</td>
												<td><input type=text class='textbox' name='authorization_line[0][details][0][disp_name]' id='authorization_line_disp_name' style='width:80%' value='표시이름'></td>
												<td>
													<input type=text class='textbox' name='authorization_line[0][details][0][order_approve]' id='authorization_line_order_approve' style='width:50%' value='' validation='true' title='승인순서'>
													<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.authorization_line_item_input').length > 1){document.getElementById('authorization_line_input_table').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.parentNode.removeNode(true);*/showMessage('authorization_line_input_status_area','결제라인명정보가 삭제되었습니다.');}else{clearInputBox('authorization_line_item_input');showMessage('authorization_line_input_status_area','결제라인명정보가 삭제되었습니다.');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
												</td>
											</tr>
										</table>";
			}
$mstring .= "
									</td>
								</tr>
								<tr><td colspan=7 style='background-color:#ffffff;'><div style='height:30px;text-align:right;color:gray;line-height:220%;' id='authorization_line_input_status_area'></div></td></tr>
							</table>
							";
		}
}else{
	$mstring .= "
							<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver border='0' id='authorization_line_input' class='authorization_line_input' idx=0 style='margin-bottom:10px;' ><!--ondblclick=\"if(document.all.authorization_line_input.length > 1){this.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\"-->
								<col width='4%'>
								<col width='15%'>
								<col width='*'>
								<col width='23%'>
								<col width='11%'>
								<col width='14%'>
								<col width='10%'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small nowrap>
									사용
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 결제라인명
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 결제라인타입
									</td>
									<td bgcolor=\"#efefef\" class=small> 담당자명 *</td>
									<td bgcolor=\"#efefef\" class=small> 직위 *</td>
									<td bgcolor=\"#efefef\" class=small> 표시이름  </td>
									<td bgcolor=\"#efefef\" class=small> 승인순서  </td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td valign='top' style='padding-top:6px;' >
									<input type=checkbox name='authorization_line[0][al_use]' id='authorization_line_al_use'  value='1' checked>
									</td>
									<td valign='top' align=center style='padding-top:4px;' >

									<span id=''><input type=text class='textbox' name='authorization_line[0][authline_name]' id='authline_name' style='width:115px;vertical-align:middle' value='$authline_name' validation=true title='결제라인명'></span><!-- 옵션 삭제 --></td>
									<td valign='top' align=center style='padding:4px 4px;'>
									<select name='authorization_line[0][authline_kind]' id='authline_kind_0' style='font-size:12px;'>
										<option value=b>기본</option>
										<option value=c>사용자 정의 </option>
									</select>

									<img src='../images/".$admininfo["language"]."/btn_line_add.gif' border=0 align=absmiddle style='cursor:pointer;' onclick=\"copyOptions('authorization_line_item_input');showMessage('authorization_line_input_status_area_0','결제라인 정보가 추가 되었습니다.');\" />
									</td>
									<td colspan='4' id='authorization_line_input_table'>
										<input type=hidden id='authorization_line_department' inputid='authorization_line_item_department' value=''><input type=hidden id='authorization_line_disp_name' value=''>
										<table border=0 width=100% id='authorization_line_item_input' class='authorization_line_item_input' idx=0 aldt_ix=0 cellspacing=4 cellpadding=0 ><!--ondblclick=\"if($('.authorization_line_item_input_0').length > 1){this.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\"-->
											<col width='34%'>
											<col width='19%'>
											<col width='24%'>
											<col width='*'>
											<tr>
												<td style='padding-left:5px;' nowrap>
												".makeDepartmentSelectBox($db,"authorization_line[0][details][0][department]",$dp_ix,"select","부서", "class='combobox' id='authorization_line_department' onchange=\"loadUser(this,'authorization_line[0][details][0][charger_ix]')\"")."
												".makeCompanyUserList($admininfo["company_id"],"authorization_line[0][details][0][charger_ix]", $dp_ix, $charger_ix,"class='authorization_line_user combobox' id='authorization_line_user' ")."
												</td>
												<td>
												<input type=text class='textbox' name='authorization_line[0][details][0][position]' id='authorization_line_position' style='width:80%' value='' title='직위' validation=true>
												<input type=hidden class='textbox' name='authorization_line[0][details][0][charger_name]' id='authorization_line_charger_name' style='width:80%' value='' title='담당자이름' >
												</td>
												<td><input type=text class='textbox' name='authorization_line[0][details][0][disp_name]' id='authorization_line_disp_name' style='width:80%' value='담당자'></td>
												<td>
													<input type=text class='textbox' name='authorization_line[0][details][0][order_approve]' id='authorization_line_order_approve' style='width:50%' value='' validation='true' title='승인순서'>
													<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.authorization_line_item_input').length > 1){document.getElementById('authorization_line_input_table').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.parentNode.removeNode(true);*/showMessage('authorization_line_input_status_area','결제라인명정보가 삭제되었습니다.');}else{clearInputBox('authorization_line_item_input');showMessage('authorization_line_input_status_area','결제라인명정보가 삭제되었습니다.');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td colspan=7 style='background-color:#ffffff;'><div style='height:30px;text-align:right;color:gray;line-height:220%;' id='authorization_line_input_status_area'></div></td></tr>
							</table>

							";
}
$mstring .="				<div style='line-height:130%;padding:10px 0px 20px 0px'>

							</div>
		</div>";
if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
	$mstring .= "<div align=center><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"OptionInput(document.authorization_line,'".$option_input_act."')\"></div>";
}else{
	$mstring .= "<div align=center><a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle /></a></div>";
}
$mstring .= "
		</td>
	</tr>
	<tr>
		<td>

		</td>
	</tr>
	</table>
	</form>";

$mstring = $mstring;

$Script = "<script Language='JavaScript' src='authorization_line.js'></script>
<script Language='JavaScript' >
	/*
	$(function() {
        $('.combobox').combobox({
			onchange:function(){
				alert($(this).attr('ps_name'));
			}
		});

    });
	*/
$(function() {
	//alert($('.authorization_line_user').length);
	$('.authorization_line_user').change(function(){
		//alert($(this).find('option:selected').attr('ps_name'));
		//alert($(this).parent().parent().parent().find('input[id^=authorization_line_position]').parent().html());
		$(this).parent().parent().parent().find('input[id^=authorization_line_position]').val($(this).find('option:selected').attr('ps_name'));
		$(this).parent().parent().parent().find('input[id^=authorization_line_charger_name]').val($(this).find('option:selected').text());
		//alert($(\"input['name^=\"+$(this).attr('name').replace('charger_ix','position')+\"']\").html());
		//$(\"input['name^=\"+$(this).attr('name').replace('charger_ix','position')+\"']\").val($(this).find('option:selected').attr('ps_name'));
		//alert($(this).find('option:selected').attr('ps_name'));
	});
 });

	</script>
<style type='text/css'>

</style>
	";
if($mmode == "pop"){
	$P = new ManagePopLayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "재고관리 > 기초정보관리 > 결제라인 등록/수정 ";
	$P->NaviTitle = "결제라인 등록/수정 ";
	$P->strContents = $mstring;
	$P->jquery_use = false;

	$P->PrintLayOut();
}else{
	$P = new LayOut();
	$P->addScript = "<script Language='JavaScript' src='authorization_line.js'></script>".$Script;
	$P->strLeftMenu = inventory_menu();
	$P->Navigation = "재고관리 > 기초정보관리 > 결제라인 등록/수정 ";
	$P->title = "결제라인 등록/수정 ";
	$P->strContents = $mstring;
	echo $P->PrintLayOut();
}

?>