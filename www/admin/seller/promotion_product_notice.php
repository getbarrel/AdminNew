<?
include("../class/layout.class");

$db = new Database;
$cdb = new Database;

if($pn_ix){
	$sql = "select * from common_seller_promotion_notice where pn_ix = '".$pn_ix."' and company_id = '".$admininfo[company_id]."'";
	$db->query($sql);
	$db->fetch();
}

$helpbox_title = "상품상세페이지 공지";



$Contents .= "
	<form name='group_frm' action='seller.act.php' method='post' enctype='multipart/form-data' onsubmit='return CheckFormValue(this)' target=''>
	<input name='act' type='hidden' value='seller_promotion_notice_update'>
	<input name='pn_ix' type='hidden' value='".$pn_ix."' validation='true'>
	<input name='company_id' type='hidden' value='".$admininfo[company_id]."' validation='true'>
	<input name='com_name' type='hidden' value='".$admininfo[company_name]."' validation='true'>

	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left' class='search_table_box'>
	<col width='18%'>
	<col width='32%'>
	<col width='18%'>
	<col width='32%'>
	<tr bgcolor=#ffffff  height='30' >
		<td class='search_box_title'> <b>사용유무 <img src='".$required3_path."'></b></td>
		<td class='search_box_item' colspan='3'>
			<input type=radio name='is_use' id='is_use_yn_1' value='1' ".($db->dt[is_use] =='1'?'checked':'')."><label for='is_use_yn_1'>사용</label>
			<input type=radio name='is_use' id='is_use_yn_0' value='0' ".($db->dt[is_use] =='0'?'checked':'')."><label for='is_use_yn_0'>미사용</label>
		</td>
	</tr>
	<tr bgcolor=#ffffff height='30'>
		<td class='search_box_title'> <b>공지사항명  <img src='".$required3_path."'></b> </td>
		<td class='search_box_item' colspan='3'>
			<input type=text class='textbox point_color' name='notice_title' value='".$db->dt[notice_title]."' style='width:300px;' validation='true' title='공지사항명'>
		</td>
	</tr>
	<tr bgcolor=#ffffff height='40'>
		<td class='search_box_title'> 공지사항 이미지 <br><span class=small> 가로 : 1050 * 세로 : (200 ~ 600)</span></td>
		<td class='search_box_item' colspan=3>

			<input type=file class='textbox' name='seller_notice_img' title='상품상세공지사항 이미지'>";

			if(file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/sellergroup/seller_promotion_".$pn_ix.".jpg")){
				$Contents .= "seller_promotion_".$pn_ix.".jpg &nbsp;&nbsp;&nbsp;
				<a href='javascript:' onclick=\"del_img('".$pn_ix."','".$company_id."');\"><img src='../images/korea/btc_del.gif' border='0' align='absmiddle' style='cursor:pointer; padding-left:10px;'></a>";
			}
			

$Contents .= "
		</td>
	</tr>
	</table>
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'  height='30'>
	<tr bgcolor=#ffffff >
		<td></td>
	</tr>
	</table>";

if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"C")){

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >
		</td>
	</tr>
	</table>";
}else{
$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	<tr bgcolor=#ffffff >
		<td colspan=4 align=center>
			<a href=\"".$auth_write_msg."\"><img src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' ></a>
		</td>
	</tr>
	</table>";
}

$Contents .= "</form>";

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'  height='30'>
	<tr bgcolor=#ffffff >
		<td></td>
	</tr>
	</table>
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' class='list_table_box' style='margin-top:3px;'>
	<col width='4%'>
	<col width='15%'>
	<col width='*'>
	<col width='20%'>
	<col width='10%'>
	<col width='10%'>
	<col width='10%'>

	<tr bgcolor=#efefef style='font-weight:bold' align='center' height=30>
		<td class='s_td' >번호</td>
		<td class='m_td' >셀러명</td>
		<td class='m_td' >공지사항명</td>
		<td class='m_td' >공지사항이미지</td>
		<td class='m_td' >등록일<br>최종수정일</td>
		<td class='m_td' >사용유무</td>
		<td class='e_td'>관리</td>
	</tr>";

$sql = "SELECT
				*
			from
				common_seller_promotion_notice as spn
			where
				1
				and company_id = '".$admininfo[company_id]."'
				order by pn_ix ASC";

$db->query($sql);
$data_array = $db->fetchall();

if($db->total){
	for($i=0;$i < count($data_array);$i++){

	switch($data_array[$i][is_use]){
		case '1':
			$is_use = '사용';
		break;
		case '0':
			$is_use = '미사용';
		break;
	}

	$Contents .= "
		<tr bgcolor=#ffffff align='center' height=30>
			<td class='list_box_td'>".($i+1)."</td>
			<td class='list_box_td'>".$data_array[$i][com_name]." </td>
			<td class='list_box_td point'>".$data_array[$i][notice_title]."</td>
			<td class='list_box_td' align=center style='padding:10px;'>";
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."".$admin_config[mall_data_root]."/images/basic/sellergroup/seller_promotion_".$data_array[$i][pn_ix].".jpg")){
				$Contents .= "<img src='".$admin_config[mall_data_root]."/images/basic/sellergroup/seller_promotion_".$data_array[$i][pn_ix].".jpg' width='100' height='40'>";
			}
	$Contents .= "
			</td>
			
			<td class='list_box_td'>".$data_array[$i][regdate]."<br>".$data_array[$i][edit_date]." </td>
			<td class='list_box_td'>".$is_use."</td>
			<td class='list_box_td'>";
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
					$Contents.="
					<a href=\"?pn_ix=".$data_array[$i][pn_ix]."&info_type=".$info_type."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a>";
				}else{
					 $Contents.="
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/bts_modify.gif' border=0></a>";
				}
				if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"D")){
					$Contents.="
					<a href='javascript:' onclick=\"delete_pn_ix('".$data_array[$i][pn_ix]."','".$data_array[$i][company_id]."');\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}else{
					 $Contents.="
					<a href=\"".$auth_update_msg."\"><img src='../images/".$admininfo["language"]."/btc_del.gif' border=0></a>";
				}
	$Contents .= "
			</td>
		</tr>";
	}
}else{
	$Contents .= "
		<tr bgcolor=#ffffff height=50>
			<td class='list_box_td' align=center colspan=7>등록된 그룹이 없습니다. </td>
		</tr>";
	}
	$Contents .= "
	</table>";

	$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'C');



$Contents .= HelpBox($helpbox_title, $help_text,'100');

$Script = "
<script language='javascript'>

function updateGroupInfo(gp_ix,gp_name,gp_level,mem_type,font_color,wholesale_dc,retail_dc,shipping_dc_price,organization_name, organization_id,organization_img,mem_cnt,disp,shipping_dc_yn,shipping_free_yn){

	var frm = document.group_frm;

	frm.act.value = 'update';
	frm.gp_ix.value = gp_ix;
	frm.gp_name.value = gp_name;
	frm.gp_level.value = gp_level;
	frm.mem_type.value = mem_type;
	frm.font_color.value = font_color;
	frm.wholesale_dc.value = wholesale_dc;
	frm.retail_dc.value = retail_dc;
	frm.shipping_dc_price.value = shipping_dc_price;
	
	if(mem_type == 'M'){
		$('#mem_type_M').attr('checked','checked');
	}else if(mem_type == 'C'){
		$('#mem_type_C').attr('checked','checked');
	}

	if(shipping_dc_yn == '1'){
		$('#shipping_dc_y').attr('checked','checked');
	}else if(shipping_dc_yn == '0'){
		$('#shipping_dc_n').attr('checked','checked');
	}else if(shipping_dc_yn == '2'){
		$('#shipping_dc_f').attr('checked','checked');
	}

	if(shipping_free_yn == '1'){
		$('#shipping_free_yn').attr('checked','checked');
	}

	if(mem_cnt > 0){
		frm.disp[0].disabled = true;
		frm.disp[1].disabled = true;

		if(disp == '1'){
			frm.disp[0].checked = true;
		}else{
			frm.disp[1].checked = true;
		}
	}else{
		frm.disp[0].disabled = false;
		frm.disp[1].disabled = false;

		if(disp == '1'){
			frm.disp[0].checked = true;
		}else{
			frm.disp[1].checked = true;
		}
	}
}

function deleteGroupInfo(act, gp_ix){
	if(confirm(language_data['group.php']['A'][language])){
		//'해당그룹 정보를 정말로 삭제하시겠습니까?'
		var frm = document.group_frm;
		frm.act.value = act;
		frm.gp_ix.value = gp_ix;
		frm.submit();
	}
}

$(document).ready(function(){

	$('#shipping_free_yn').click(function(){
		$('#shipping_free_yn').toggle(function(){
			$('#shipping_free_yn').attr('checked','true');
		});
	});

	$('#setup_date').datepicker({
		dateFormat: 'yy-mm-dd',
		buttonImageOnly: true,
		buttonText: 'Kalender',
	});
});

function delete_pn_ix(pn_ix,company_id){

	var select = confirm('삭제하시겠습니까?');

	if(select){
		$.ajax({
				url: 'seller.act.php',
				type: 'get',
				dataType: 'html',
				data: {pn_ix : pn_ix, company_id : company_id, act : 'seller_promotion_pn_ix'},
				success: function(result){
					document.location.reload();
				}
		});
	}
	else{
		return false;
	}
}

function del_img(pn_ix,company_id){

	var select = confirm('삭제하시겠습니까?');

	if(select){
		$.ajax({
				url: 'seller.act.php',
				type: 'get',
				dataType: 'html',
				data: {pn_ix : pn_ix, company_id : company_id, act : 'seller_promotion_image_del'},
				success: function(result){
					document.location.reload();
				}
		});
	}
	else{
		return false;
	}
}

</script>
";

$P = new LayOut();
$P->strLeftMenu = seller_menu();
$P->addScript = $Script;
$P->strContents = $Contents;
$P->Navigation = "셀러관리 > 상품상세페이지 공지";
$P->title = "상품상세페이지 공지";
echo $P->PrintLayOut();

function penalty_point_select($select_name,$select_id,$value=''){
	
$data = "
	<select name='".$select_name."' id='".$select_id."' style='width:50px;'>";
	for($i=0;$i<=10;$i++){
		$data .= "<option value='".$i."' ".($value == $i?"selected":"").">".$i."</option>";
	}
$data .= "
	</select>";

return $data;

}
?>