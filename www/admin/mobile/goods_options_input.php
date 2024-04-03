<?
include("../class/mobilelayout.class");
include("../../class/database.class");
include("../include/admin.util.php");

$db = new Database;

$Script = "<script Language='JavaScript' src='../product/goods_input.js'></script><script Language='JavaScript' src='../product/goods_option_input.js'></script>
<script language='javascript' >

 function DeleteOptionTmpInfo(opnt_ix){
 	if(confirm('해당 임시옵션 정보를 정말로 삭제 하시겠습니까?')){
		window.frames['act'].location.href='./goods_options_input.act.php?act=delete&opnt_ix='+opnt_ix;
 	}
}
</script>
<style type='text/css'>
	.goods_input_header{padding:0 0 0 8px ;background:#ebebeb;border-bottom:2px solid #d3d3d3;}
	.goods_input_header:after{content:'';clear:both;display:block;} 
	.goods_input_header span{padding-left:9px;background:url('./images/li_bg.gif') 0 center no-repeat;background-size:3px 3px;font-size:15px;font-weight:bold;}
	.goods_input_header tr td {height:46px;}
</style>";


if($option_type == ""){
	$option_type = "basic";
}

if($_GET["opnt_ix"] != ""){
	$option_input_act = "tmp_update";
}else{
	$option_input_act = "tmp_insert";
}

$mstring ="<form name='goods_options_input' method='POST' action='../product/goods_options_input.act.php' target='act'>
<input type=hidden name='act' value='".$option_input_act."'>
<input type=hidden name='opnt_ix' value='".$opnt_ix."'>
<input type=hidden name='mmode' value='mobile'>

<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
	<tr>
		<td valign=top>
		";



$mstring .= "<div id='basic_option_zone'>";

$mstring .="
<div class='goods_input_header'>
	<table border='0' cellpadding='0' cellspacing='0' width=100%'>
	<col width='50%' />
	<col width='50%' />
		<tr>
			<td><span>자주쓰는옵션 등록/수정</span></td>
			<td align='right'>
			</td>
		</tr>
	</table>
</div>
<div style='clear:both;height:10px;'></div>
";


$sql = "select * from ".TBL_SHOP_PRODUCT_OPTIONS_TMP." where opnt_ix = '".$opnt_ix."' and option_kind != 'b' order by regdate asc ";
//echo $sql;
$db->query($sql);

$options = $db->fetchall();
//print_r($options);
if($db->total){
	for($i=0;$i < count($options);$i++){
		$mstring .= "
							<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver id='options_input' class='options_input' idx=".$i." style='margin-bottom:10px' >
								<col width='33%'>
								<col width='33%'>
								<col width='*'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small nowrap>
									사용
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 옵션명
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 옵션종류
									</td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td valign=top style='padding-top:6px;' align=left>
										<input type=hidden name='options[".$i."][opnt_ix]' id='option_opnt_ix' value='".$options[$i][opnt_ix]."'>
										<input type=hidden name='options[".$i."][option_type]' id='options_option_type' value='".($options[$i][option_type] ? $options[$i][option_type]:"9")."'>
										<input type=checkbox name='options[".$i."][option_use]' id='options_option_use' value='1' ".(($options[$i][option_use] == 1 || $options[$i][option_use] == '') ? "checked":"")." align=absmiddle> <label for='options_option_use'>사용여부</label>";
									if($admininfo[admin_level]==9){
										$mstring .= "
										<div>
											<input type=checkbox name='options[".$i."][basic]' id='options_option_basic' value='Y' ".($options[$i][basic] == 'Y' ? "checked":"")." align=absmiddle><label for='options_option_basic'> 기본제공 여부</label>
										</div>";
									}

									$mstring .= "
									</td>
									<td valign=middle style='padding-top:4px;' >
										";

									//$mstring .= "			<input type=text class='textbox' name='options[".$i."][option_name]' id='option_name' size=28 style='width:115;vertical-align:middle' value='".$options[$i][option_name]."'> <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if(document.all.options_input.length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>";
									$mstring .= "			<span id='".$i."'><input type=text class='textbox' name='options[".$i."][option_name]' id='option_name' inputid='option_name' style='width:80%;vertical-align:middle' value='".$options[$i][option_name]."'> </span>";

$mstring .= "
										".($i == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.options_input').length > 1){ \$('.options_input[idx=".$i."]').remove();/*this.parentNode.parentNode.parentNode.removeNode(true);*/showMessage('options_input_status_area_".$i."','해당 옵션 구분정보가 삭제 되었습니다.');}else{alert(language_data['goods_input.php']['G'][language]);}\" title='더블클릭시 해당 테이블이 삭제 됩니다.'>")." ";

$mstring .= "
									</td>
									<td valign=middle style='padding-top:4px;'>
										<select name='options[".$i."][option_kind]' id='option_kind_0' style='font-size:12px;'>
											<option value=s ".($options[$i][option_kind] == "s" ? "selected":"").">선택옵션</option>
											<option value=p ".($options[$i][option_kind] == "p" ? "selected":"").">가격추가옵션</option>

										</select>
										<img src='../images/".$admininfo["language"]."/btn_option_detail_add.gif' border=0 align=absmiddle style='cursor:pointer;margin:3px 0px;' onclick=\"copyOptions('options_item_input_".$i."');showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 추가 되었습니다.');\" />
									</td>
								</tr>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small> 옵션구분 *</td>
									<td bgcolor=\"#efefef\" class=small> 추가가격 *</td>
									<td bgcolor=\"#efefef\" class=small> 기타  </td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td colspan=3 id='options_basic_item_input_table_".$i."'>
									<input type=hidden id='options_item_option_div_".$i."' inputid='options_item_option_div_".$i."' value=''>
									<input type=hidden id='options_item_option_code_".$i."' value=''>
									";

			$sql = "select * from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." where opnt_ix = '".$options[$i][opnt_ix]."' order by opndt_ix asc ";
			//echo $sql;
			$db->query($sql);

			if($db->total){
				for($j=0;$j < $db->total;$j++){
				$db->fetch($j);

		$mstring .= "<table width=100% id='options_item_input_".$i."' class='options_item_input_".$i."' idx=".$i." detail_idx=".$j." cellspacing=4 cellpadding=0 ><!--ondblclick=\"if(document.all.options_item_input_0.length > 1){this.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\"-->
											<col width='33%'>
											<col width='33%'>
											<col width='34%'>
											<tr>
												<td>
												<input type=hidden name='options[".$i."][details][".$j."][opd_ix]' value='".$db->dt[id]."'>
												<input type=text class='textbox' name='options[".$i."][details][".$j."][option_div]' id='options_item_option_div_".$i."' inputid='options_item_option_div_".$i."' style='width:90%;vertical-align:middle' value='".$db->dt[option_div]."'>
												</td>
												<td><input type=text class='textbox' name='options[".$i."][details][".$j."][price]' id='options_item_option_price_".$i."' style='width:90%' value='".$db->dt[option_price]."' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>";
			$mstring .= "			<td><input type=text class='textbox' name='options[".$i."][details][".$j."][code]' id='options_item_option_code_".$i."' style='width:70%' value='".$db->dt[option_code]."'> <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.options_item_input_".$i."').length > 1){document.getElementById('options_basic_item_input_table_".$i."').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.parentNode.removeNode(true);*/showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 삭제되었습니다.');}else{clearInputBox('options_item_input_".$i."');showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 삭제되었습니다.');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'></td>";
	//	$mstring .= "			<td><input type=text class='textbox' name='options[".$i."][details][".$j."][code]' size=28 style='width:85%' value='".$db->dt[option_code]."'> ".($j == 0 ? "<!-- 옵션 삭제 -->":"<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if(document.all.options_item_input_".$i.".length > 1){this.parentNode.parentNode.parentNode.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'>")."</td>";

		$mstring .= "
											</tr>
										</table>";
					}
			}else{
		$mstring .= "<table width=100% id='options_item_input_0' class='options_item_input_0' idx=0 detail_idx=0 cellspacing=4 cellpadding=0 ><!--ondblclick=\"if(document.all.options_item_input_0.length > 1){this.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\"-->
											<col width='33%'>
											<col width='33%'>
											<col width='34%'>
											<tr>
												<td>
													<input type=hidden id='options_item_option_details_ix_0'  name='options[0][details][0][opd_ix]' value=''>
													<input type=text class='textbox' name='options[0][details][0][option_div]' id='options_item_option_div_0' inputid='options_item_option_div_0' style='width:90%;vertical-align:middle' value=''>
												</td>
												<td><input type=text class='textbox' name='options[0][details][0][price]' id='options_item_option_price_0' style='width:90%' value='' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>
												<td><input type=text class='textbox' name='options[0][details][0][code]' id='options_item_option_code_0' style='width:70%' value=''><img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.options_item_input_0').length > 1){document.getElementById('options_basic_item_input_table_".$i."').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.removeNode(true);*/}else{clearInputBox('options_item_input_0');alert(language_data['goods_input.php']['G'][language]);}\" alt='더블클릭시 해당 라인이 삭제 됩니다.'></td>
											</tr>
										</table>";
			}
$mstring .= "
									</td>
								</tr>
								<tr><td colspan=3 style='background-color:#ffffff;'><div style='height:30px;text-align:right;color:gray;line-height:220%;' id='options_input_status_area_".$i."'></div></td></tr>
							</table>
							";
		}
}else{
	$mstring .= "
							<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver border='0' id='options_input' class='options_input' idx=0 style='margin-bottom:10px;' ><!--ondblclick=\"if(document.all.options_input.length > 1){this.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\"-->
								<col width='33%'>
								<col width='33%'>
								<col width='*'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small nowrap>
									사용
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 옵션명
									</td>
									<td bgcolor=\"#efefef\" class=small nowrap>
									 옵션종류
									</td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td valign='top' style='padding-top:6px;' align=left>
									<input type=hidden name='options[0][option_type]' value='9'>
									<input type=checkbox name='options[0][option_use]' id='options_option_use'  value='1' checked> <label for='options_option_use'>사용여부</label>
									";
									
									if($admininfo[admin_level]==9){
										$mstring .= "
										<div>
											<input type=checkbox name='options[".$i."][basic]' id='options_option_basic' value='Y' align=absmiddle><label for='options_option_basic'> 기본제공 여부</label>
										</div>";
									}
									$mstring .= "

									</td>
									<td valign='middle' align=center style='padding-top:4px;' >

										<span id=''><input type=text class='textbox' name='options[0][option_name]' id='option_name' inputid='option_name' style='width:80%;vertical-align:middle' value='$option_div'></span><!-- 옵션 삭제 -->

									</td>
									<td valign='top' align=center style='padding:4px 4px;'>
									<select name='options[0][option_kind]' id='option_kind_0' style='font-size:12px;'>
										<option value=s>선택옵션</option>
										<option value=p>가격추가옵션</option>
									</select>
									<img src='../images/".$admininfo["language"]."/btn_option_detail_add.gif' border=0 align=absmiddle style='cursor:pointer;margin:3px 0px;' onclick=\"copyOptions('options_item_input_0');showMessage('options_input_status_area_0','해당옵션 구분정보가 추가 되었습니다.');\" />
									</td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class=small> 옵션구분 *</td>
									<td bgcolor=\"#efefef\" class=small> 추가가격 *</td>
									<td bgcolor=\"#efefef\" class=small> 기타  </td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td colspan='3' id='options_basic_item_input_table_0'>
										<input type=hidden id='options_item_option_div_0' inputid='options_item_option_div_0' value=''><input type=hidden id='options_item_option_code_0' value=''>
										<table width=100% id='options_item_input_0' class='options_item_input_0' idx=0 detail_idx=0 cellspacing=4 cellpadding=0 ><!--ondblclick=\"if($('.options_item_input_0').length > 1){this.removeNode(true);}else{alert(language_data['goods_input.php']['G'][language]);}\"-->
											<tr align='center'>
												<td><input type=text class='textbox' name='options[0][details][0][option_div]' id='options_item_option_div_0' inputid='options_item_option_div_0' style='width:90%;vertical-align:middle' value='$option_div'></td>
												<td><input type=text class='textbox' name='options[0][details][0][price]' id='options_item_option_price_0' style='width:90%' value='$option_price' onkeydown='onlyEditableNumber(this)'  onkeyup='onlyEditableNumber(this)'></td>";

$mstring .= "					<td><input type=text class='textbox' name='options[0][details][0][code]' id='options_item_option_code_0' style='width:70%' value='$option_code'> <img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('.options_item_input_0').length > 1){document.getElementById('options_basic_item_input_table_0').removeChild(this.parentNode.parentNode.parentNode.parentNode);/*this.parentNode.parentNode.parentNode.parentNode.removeNode(true);*/showMessage('options_input_status_area_0','해당옵션 구분정보가 삭제되었습니다.');}else{clearInputBox('options_item_input_0');showMessage('options_input_status_area_0','해당옵션 구분정보가 삭제되었습니다.');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'></td>";

//$mstring .= "					<td><input type=text class='textbox' name='options[0][details][0][code]' size=28 style='width:85%' value='$option_code'> <!-- 옵션 삭제 --></td>";

$mstring .= "
											</tr>
										</table>
									</td>
								</tr>
								<tr><td colspan=3 style='background-color:#ffffff;'><div style='height:30px;text-align:right;color:gray;line-height:220%;' id='options_input_status_area_0'></div></td></tr>
							</table>";
}
$mstring .="
		</div>";


$mstring .= "
				<div align=center>
					<img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"OptionInput(document.goods_options_input,'".$option_input_act."')\">
					<img src='../images/".$admininfo["language"]."/b_cancel.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"document.location.href='goods_options_tmp_list.php'\">
				</div>
				
		</td>
	</tr>
</table>
</form>
<div style='clear:both;height:10px;'></div>";

$Contents = $mstring;



$P = new MobileLayOut();
$P->addScript = $Script;
//$P->strLeftMenu = store_menu();
$P->strContents = $Contents;
$P->Navigation = "";
$P->layout_display = false;
echo $P->PrintLayOut();


?>