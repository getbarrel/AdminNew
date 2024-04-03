<?
include("../class/layout.class");
//include($_SERVER["DOCUMENT_ROOT"]."/class/bbs.lib.php");
//include_once($_SERVER["DOCUMENT_ROOT"]."/include/sns.config.php");

$db = new Database;

if($_GET["opnt_ix"] != ""){
	$option_input_act = "tmp_update";
}else{
	$option_input_act = "tmp_insert";
}

$mstring ="<form name='goods_options_input' method='POST' action='../product/goods_options_input.act.php' enctype='multipart/form-data'  target='act' >
		<input type=hidden name='act' value='".$option_input_act."'>
		<input type=hidden name='opnt_ix' value='".$opnt_ix."'>
		<table cellpadding=0 cellspacing=0 width=100% border=0 align=center>
		<tr>
			<td height=560 valign=top>
			";

$mstring .= "<div id='basic_option_zone'>";
$mstring .="<table width='100%' cellpadding=0 cellspacing=0><tr height=30><td style='padding-bottom:5px;'>".colorCirCleBox("#efefef","100%","<div style='padding:5px;padding-left:10px;'><img src='../images/dot_org.gif' align=absmiddle style='position:relative;'> <b class=blk> 옵션정보 </b></div>")."</td></tr></table>";


$sql = "select * from ".TBL_SHOP_PRODUCT_OPTIONS_TMP." where opnt_ix = '".$opnt_ix."' order by regdate asc ";
$db->query($sql);

$options = $db->fetchall();
//print_r($options);
if($db->total){
	for($i=0;$i < count($options);$i++){

			$mstring .= "
							<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver id='options_input_".$i."' style='margin-bottom:10px' >
								<col width='12%'>
								<col width='12%'>
								<col width='15%'>
								<col width='15%'>
								<col width='15%'>
								<col width='25%'>
								<col width='*'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class='m_td' nowrap>
									옵션사용 <img src='".$required3_path."'>
									</td>
									<td bgcolor=\"#efefef\" class='m_td'  nowrap>
									 옵션자동<br/>생성코드
									</td>
									<td bgcolor=\"#efefef\" class='m_td' colspan='3' nowrap>
									 옵션명 <img src='".$required3_path."'>
									</td>
									<td bgcolor=\"#efefef\" class='m_td' colspan='2' nowrap>
									 옵션아이콘
									</td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td valign=top style='padding:4px;' align=center>
										<input type=hidden name='options[".$i."][opnt_ix]' id='option_opnt_ix' value='".$options[$i][opnt_ix]."'>
										<!--input type=hidden name='options[".$i."][option_type]' id='options_option_type' value='".($options[$i][option_type] ? $options[$i][option_type]:"9")."'-->
										<select name='options[".$i."][option_type]' style='font-size:12px;'>
											<option value=c ".($options[$i][option_type] == "c" ? "selected":"").">컬러</option>
											<option value=s ".($options[$i][option_type] == "s" ? "selected":"").">사이즈</option>
											<option value=9 ".($options[$i][option_type] == "9" ? "selected":"").">기본</option>
										</select>

										<!--select name='options[".$i."][option_kind]' id='option_kind_0' style='font-size:12px;'>
											<option value=s ".($options[$i][option_kind] == "s" ? "selected":"").">선택옵션</option>
											<option value=p ".($options[$i][option_kind] == "p" ? "selected":"").">가격추가옵션</option>
										</select-->
										<input type='hidden' name='options[".$i."][option_kind]' value='s' />
										<input type=checkbox name='options[".$i."][option_use]' id='options_option_use' value='1' ".(($options[$i][disp] == 1 || ($options[$i][disp] == '' && $option_input_act =="tmp_insert")) ? "checked":"")." align=absmiddle>
									</td>
									<td valign=middle style='padding:4px;' >
										<input type=text class='textbox' name='options[".$i."][opt_code]' id='opt_code' style='width:80%;vertical-align:middle' value='".$options[$i][opt_code]."'>
									</td>
									<td valign=middle style='padding:4px;' colspan='3'>
										<input type=text class='textbox' name='options[".$i."][option_name]' id='option_name' validation='true' title='옵션명' style='width:80%;vertical-align:middle' value='".$options[$i][option_name]."'>
									</td>
									<td valign=middle style='padding:4px;' colspan='2'>";
										
										$option_delete_btn="";
										if(file_exists($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$options[$i][opnt_ix]."/option_img.gif")){
											$mstring .= "<img src='".$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$options[$i][opnt_ix]."/option_img.gif' border=0 align=absmiddle style='width:20%;' />";
											$option_delete_btn="<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle id='delete_option_imgfile' style='cursor:pointer;margin:3px 0px;' onclick=\"deleteImg($(this),'".$options[$i][opnt_ix]."','');\" />";
										}

										$mstring .= "
										<input type='file' class='textbox' name='options[".$i."][option_imgfile]' id='option_imgfile' style='width:60%;vertical-align:middle'> 
										".$option_delete_btn."
									</td>
								</tr>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class='m_td' rowspan='2'> 옵션구분사용 <img src='".$required3_path."'> </td>
									<td bgcolor=\"#efefef\" class='m_td' rowspan='2'> 옵션구분<br/>자동생성코드 </td>
									<td bgcolor=\"#efefef\" class='m_td' colspan='3'> 옵션구분명 <img src='".$required3_path."'> </td>
									<td bgcolor=\"#efefef\" class='m_td' rowspan='2'> 옵션구분아이콘 </td>
									<td bgcolor=\"#efefef\" class='m_td' rowspan='2'> <img src='../images/".$admininfo["language"]."/btn_add2.gif' border=0 align=absmiddle style='cursor:pointer;margin:3px 0px;' onclick=\"copyOptions('options_input_".$i."');showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 추가 되었습니다.');\" /></td>
								</tr>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class='m_td' colspan='3'> KOREA  </td>
									<!--td bgcolor=\"#efefef\" class='m_td' > ENGLISH </td>
									<td bgcolor=\"#efefef\" class='m_td' > CHINA </td-->
								</tr>";

			$sql = "select * from ".TBL_SHOP_PRODUCT_OPTIONS_DETAIL_TMP." where opnt_ix = '".$options[$i][opnt_ix]."' order by opndt_ix asc ";
			$db->query($sql);

			if($db->total){

				for($j=0;$j < $db->total;$j++){
					
					$db->fetch($j);

								$mstring .= "
								<tr height=25 bgcolor='#ffffff' align=center class='option_details_tr' o_num='".$i."' od_num='".$j."' >
									<td>
										<input type=hidden class='textbox' name='options[".$i."][details][".$j."][opndt_ix]' value='".$db->dt[opndt_ix]."' id='options_item_option_id'>
										<input type=checkbox name='options[".$i."][details][".$j."][option_use]' id='options_item_option_use' value='1' ".($db->dt[disp] == 1 || ($db->dt[disp] == '' && $option_input_act =="tmp_insert")? "checked":"")." align=absmiddle>
									</td>
									<td>
										<input type=text class='textbox' name='options[".$i."][details][".$j."][opt_dt_code]' id='options_item_opt_dt_code' style='width:80%;vertical-align:middle' value='".$db->dt[opt_dt_code]."'>
									</td>
									<td colspan='3'>
										<input type=text class='textbox' name='options[".$i."][details][".$j."][option_div]' validation='true' title='옵션구분명' id='options_item_option_div' style='width:90%;vertical-align:middle' value='".$db->dt[option_div]."'>
									</td>
									<!--td>
										<input type=text class='textbox' name='options[".$i."][details][".$j."][option_div_engish]' id='options_item_option_div_engish' style='width:90%;vertical-align:middle' value='".$db->dt[option_div_engish]."'>
									</td>
									<td>
										<input type=text class='textbox' name='options[".$i."][details][".$j."][option_div_china]' id='options_item_option_div_china' style='width:90%;vertical-align:middle' value='".$db->dt[option_div_china]."'>
									</td-->
									<td>";
										$option_delete_btn="";
										if(file_exists($_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$options[$i][opnt_ix]."/option_detail_img_".$db->dt[opndt_ix].".gif")){
											$mstring .= "<img src='".$_SESSION["admin_config"]["mall_data_root"]."/images/option_tmp/".$options[$i][opnt_ix]."/option_detail_img_".$db->dt[opndt_ix].".gif' border=0 align=absmiddle id='options_item_option_img' style='width:25%;' />";
											$option_delete_btn="<img src='../images/".$admininfo["language"]."/btc_del.gif' border=0 align=absmiddle id='delete_option_imgfile' style='cursor:pointer;margin:3px 0px;' onclick=\"deleteImg($(this),'".$options[$i][opnt_ix]."','".$db->dt[opndt_ix]."');\" />";
										}

										$mstring .= "
										<input type='file' class='textbox' name='options[".$i."][details][".$j."][option_imgfile]' id='options_item_option_imgfile' style='vertical-align:middle;width:50%'>
										".$option_delete_btn."
									</td>
									<td>
										<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('#options_input_".$i."').find('tr.option_details_tr').length > 1){ $(this).parent().parent().remove();showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 삭제되었습니다.');}else{ $('#options_input_".$i."').find('tr.option_details_tr input.textbox').val('');$('#options_input_".$i."').find('#options_item_option_img').hide();showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 삭제되었습니다.');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
									</td>
								</tr>";
				}

			}else{
								$mstring .= "
								<tr height=25 bgcolor='#ffffff' align=center class='option_details_tr' o_num='".$i."' od_num='0' >
									<td>
										<input type=hidden class='textbox' name='options[".$i."][details][0][opndt_ix]' value='' id='options_item_option_id'>
										<input type=checkbox name='options[".$i."][details][0][option_use]' id='options_item_option_use' value='1' checked align=absmiddle>
									</td>
									<td>
										<input type=text class='textbox' name='options[".$i."][details][0][opt_dt_code]' id='options_item_opt_dt_code' style='width:80%;vertical-align:middle' value=''>
									</td>
									<td colspan='3'>
										<input type=text class='textbox' name='options[".$i."][details][0][option_div]' validation='true' title='옵션구분명' id='options_item_option_div' style='width:90%;vertical-align:middle' value=''>
									</td>
									<!--td>
										<input type=text class='textbox' name='options[".$i."][details][0][option_div_engish]' id='options_item_option_div_engish' style='width:90%;vertical-align:middle' value=''>
									</td>
									<td>
										<input type=text class='textbox' name='options[".$i."][details][0][option_div_china]' id='options_item_option_div_china' style='width:90%;vertical-align:middle' value=''>
									</td-->
									<td>
										<input type='file' class='textbox' name='options[".$i."][details][0][option_imgfile]' id='options_item_option_imgfile' style='vertical-align:middle;width:50%'>
									</td>
									<td>
										<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('#options_input_".$i."').find('tr.option_details_tr').length > 1){ $(this).parent().parent().remove();showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 삭제되었습니다.');}else{ $('#options_input_".$i."').find('tr.option_details_tr input.textbox').val('');$('#options_input_".$i."').find('#options_item_option_img').hide();showMessage('options_input_status_area_".$i."','해당옵션 구분정보가 삭제되었습니다.');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
									</td>
								</tr>";
			}
			
			$mstring .= "
							</table>
							<div style='height:30px;text-align:right;color:gray;line-height:220%;' id='options_input_status_area_".$i."'></div>
							";
		}
}else{
			$mstring .= "
							<table width='100%' cellpadding=0 cellspacing=1 bgcolor=silver id='options_input_0' style='margin-bottom:10px' >
								<col width='12%'>
								<col width='12%'>
								<col width='15%'>
								<col width='15%'>
								<col width='15%'>
								<col width='25%'>
								<col width='*'>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class='m_td' nowrap>
									옵션사용 <img src='".$required3_path."'>
									</td>
									<td bgcolor=\"#efefef\" class='m_td'  nowrap>
									 옵션자동<br/>생성코드
									</td>
									<td bgcolor=\"#efefef\" class='m_td' colspan='3' nowrap>
									 옵션명 <img src='".$required3_path."'>
									</td>
									<td bgcolor=\"#efefef\" class='m_td' colspan='2' nowrap>
									 옵션아이콘
									</td>
								</tr>
								<tr bgcolor='#ffffff' align=center>
									<td valign=top style='padding:4px;' align=center>
										<input type=hidden name='options[0][opnt_ix]' id='option_opnt_ix' value=''>
										<!--input type=hidden name='options[0][option_type]' id='options_option_type' value='".($options[$i][option_type] ? $options[$i][option_type]:"9")."'-->
										<select name='options[0][option_type]' style='font-size:12px;'>
											<option value=c>컬러</option>
											<option value=s>사이즈</option>
											<option value=9 selected>기본</option>
										</select>

										<!--select name='options[0][option_kind]' id='option_kind_0' style='font-size:12px;'>
											<option value=s ".($options[$i][option_kind] == "s" ? "selected":"").">선택옵션</option>
											<option value=p ".($options[$i][option_kind] == "p" ? "selected":"").">가격추가옵션</option>
										</select-->
										<input type='hidden' name='options[0][option_kind]' value='s' />
										<input type=checkbox name='options[0][option_use]' id='options_option_use' value='1' checked align=absmiddle>
									</td>
									<td valign=middle style='padding:4px;' >
										<input type=text class='textbox' name='options[0][opt_code]' id='opt_code' style='width:80%;vertical-align:middle' value=''>
									</td>
									<td valign=middle style='padding:4px;' colspan='3'>
										<input type=text class='textbox' name='options[0][option_name]' id='option_name' style='width:80%;vertical-align:middle' value=''>
									</td>
									<td valign=middle style='padding:4px;' colspan='2'>
										<input type='file' class='textbox' name='options[0][option_imgfile]' id='option_imgfile' style='width:60%;vertical-align:middle'> 
									</td>
								</tr>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class='m_td' rowspan='2'> 옵션구분사용 <img src='".$required3_path."'> </td>
									<td bgcolor=\"#efefef\" class='m_td' rowspan='2'> 옵션구분<br/>자동생성코드 </td>
									<td bgcolor=\"#efefef\" class='m_td' colspan='3'> 옵션구분명 <img src='".$required3_path."'> </td>
									<td bgcolor=\"#efefef\" class='m_td' rowspan='2'> 옵션구분아이콘 </td>
									<td bgcolor=\"#efefef\" class='m_td' rowspan='2'> <img src='../images/".$admininfo["language"]."/btn_add2.gif' border=0 align=absmiddle style='cursor:pointer;margin:3px 0px;' onclick=\"copyOptions('options_input_0');showMessage('options_input_status_area_0','해당옵션 구분정보가 추가 되었습니다.');\" /></td>
								</tr>
								<tr height=25 bgcolor='#ffffff' align=center>
									<td bgcolor=\"#efefef\" class='m_td' colspan='3'> KOREA  </td>
									<!--td bgcolor=\"#efefef\" class='m_td' > ENGLISH </td>
									<td bgcolor=\"#efefef\" class='m_td' > CHINA </td-->
								</tr>
								<tr height=25 bgcolor='#ffffff' align=center class='option_details_tr' o_num='0' od_num='0' >
									<td>
										<input type=hidden class='textbox' name='options[0][details][0][opndt_ix]' value='' id='options_item_option_id'>
										<input type=checkbox name='options[0][details][0][option_use]' id='options_item_option_use' value='1' checked align=absmiddle>
									</td>
									<td>
										<input type=text class='textbox' name='options[0][details][0][opt_dt_code]' id='options_item_opt_dt_code' style='width:80%;vertical-align:middle' value=''>
									</td>
									<td colspan='3'>
										<input type=text class='textbox' name='options[0][details][0][option_div]' validation='true' title='옵션구분명' id='options_item_option_div' style='width:90%;vertical-align:middle' value=''>
									</td>
									<!--td>
										<input type=text class='textbox' name='options[0][details][0][option_div_engish]' id='options_item_option_div_engish' style='width:90%;vertical-align:middle' value=''>
									</td>
									<td>
										<input type=text class='textbox' name='options[0][details][0][option_div_china]' id='options_item_option_div_china' style='width:90%;vertical-align:middle' value=''>
									</td-->
									<td>
										<input type='file' class='textbox' name='options[0][details][0][option_imgfile]' id='options_item_option_imgfile' style='vertical-align:middle;width:50%'>
									</td>
									<td>
										<img src='../images/i_close.gif' align=absmiddle style='cursor:pointer;' ondblclick=\"if($('#options_input_0').find('tr.option_details_tr').length > 1){ $(this).parent().parent().remove();showMessage('options_input_status_area_0','해당옵션 구분정보가 삭제되었습니다.');}else{ $('#options_input_0').find('tr.option_details_tr input.textbox').val('');$('#options_input_0').find('#options_item_option_img').hide();showMessage('options_input_status_area_0','해당옵션 구분정보가 삭제되었습니다.');}\" title='더블클릭시 해당 라인이 삭제 됩니다.'>
									</td>
								</tr>
							</table>
							<div style='height:30px;text-align:right;color:gray;line-height:220%;' id='options_input_status_area_0'></div>
							";

}

$mstring .="				<div style='line-height:130%;padding:10px 0px 20px 0px'>
							재고관리가 필요 없는 상품일 경우 옵션 추가를 이용하여 아래 예와 같이 옵션을 분리 적용 하실 수 있습니다.<br>
							예) 옵션1 – 옵션명 : 색상 / 옵션구분 : RED, BLUE<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;옵션2 – 옵션명 : 사이즈 / 옵션구분 : 95size, 100size, 105size<br>

							</div>
		</div>";


$mstring .= "
				<div align=center><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 align=absmiddle style='cursor:pointer' onclick=\"OptionInput(document.goods_options_input,'".$option_input_act."')\"></div>

		</td>
	</tr>
	<tr>
		<td>

		</td>
	</tr>
	</table>
	</form>";

$mstring = $mstring;

$Script = "<script Language='JavaScript' src='goods_option_input.js'></script>";

	$P = new ManagePopLayOut();
	$P->strLeftMenu = inventory_menu();
	$P->addScript = $Script;
	$P->Navigation = "상품관리 > 상품등록 > 임시옵션 생성하기 ";
	$P->NaviTitle = "임시옵션 생성하기 ";
	$P->strContents = $mstring;
	$P->jquery_use = false;

	$P->PrintLayOut();


?>