<?
include("../class/layout.class");
include("../logstory/class/sharedmemory.class");

$file_name = $admininfo[company_id]."_goods_multi_price_setup";

$shmop = new Shared($file_name);
$shmop->filepath = $_SERVER["DOCUMENT_ROOT"].$admininfo["mall_data_root"]."/_shared/";
$shmop->SetFilePath();
$setup_info = $shmop->getObjectForKey($file_name);
$setup_info = unserialize(urldecode($setup_info));

$db = new Database;
$db2 = new Database;


if($setup_info[update_kind] == ""){
	$setup_info[update_kind] = "update_sellprice_rate";
}

$Contents .= "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' >
		<col width='25%' />
		<col width='30%' />
		<col width='20%' />
		<col width='30%' />
	<tr>
		<td align='left' colspan=4 > ".GetTitleNavigation("재고현황", "재고관리 > 재고현황")."</td>
	</tr>
	</table>";

$Contents .= "
	<form name='delivery_form' action='goods_mult_price_setup.act.php' method='post' onsubmit='return SubmitX(this)' target=''>
	<input name='act' type='hidden' value='config_update'>
	<input name='company_id' type='hidden' value = '".$admininfo[company_id]."'>
	<input name='mall_ix' type='hidden' value='".$mall_ix."'>

	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='18%'>
	<col width='*'>
	<tr>
		<td class='input_box_title'> <b>가격수정</b></td>
		<td  class='input_box_item'>
			<input type='radio' name='update_kind' id='update_state_rate' value='update_sellprice_rate' ".CompareReturnValue("update_sellprice_rate",$setup_info[update_kind],"checked")." onclick=\"ChangeUpdateForm('update_sellprice_rate');\"><label for='update_state_rate'> 판매가대비 가격수정 (할인율%)</label>
			<!--<input type='radio' name='update_kind' id='update_state_multi' value='update_sellprice_multi' ".CompareReturnValue("update_sellprice_multi",$setup_info[update_kind],"checked")." onclick=\"ChangeUpdateForm('update_sellprice_multi');\"><label for='update_state_multi'> 매입가대비 가격수정 (배*)</label>-->
		</td>
	</tr>
	</table>

	<div id='update_sellprice_rate' ".($setup_info[update_kind] == "update_sellprice_rate" || $setup_info[update_kind] == "" ? "style='display:block;padding-top:10px;'":"style='display:none;padding-top:10px;'")." >
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='18%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b>가격수정</b></td>
		<td class='input_box_item'>
			<select name='round_cnt[rate]' id='round_cnt'>
			<option value='-1' ".CompareReturnValue("-1",$setup_info[round_cnt][rate],"selected").">10</option>
			<option value='-2' ".CompareReturnValue("-2",$setup_info[round_cnt][rate],"selected").">100</option>
			<option value='-3' ".CompareReturnValue("-3",$setup_info[round_cnt][rate],"selected").">1000</option>
			</select>

			<select name='round_type[rate]' id='round_type'>
			<option value='round' ".CompareReturnValue("round",$setup_info[round_type][rate],"selected").">반올림</option>
			<option value='floor' ".CompareReturnValue("floor",$setup_info[round_type][rate],"selected").">반내림</option>
			</select>
		</td>
	</tr>
	<tr>
		<td class='input_box_title' rowspan='2'> <b>가격수정</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding='5' cellspacing='0' border='0' bgcolor=#ffffff  class='list_table_box' width=99%>
				<col width='10%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<tr align=center height=30>
					<td class=s_td >기본가 (소매가) -> </td>
					<td class=m_td >할인가 -> </td>
					<td class=m_td >".$setup_info[batch][rate][R][a_name]." -> </td>
					<td class=m_td >".$setup_info[batch][rate][R][b_name]." -> </td>
					<td class=m_td >".$setup_info[batch][rate][R][c_name]." -> </td>
					<td class=m_td >".$setup_info[batch][rate][R][d_name]." -> </td>
					<td class=e_td >".$setup_info[batch][rate][R][e_name]." -> </td>
				</tr>
				<tr align=center>
					<td >소매</td>
					<td><input type='text' class='textbox number' name='batch[rate][R][product_sellprice]' id='product_sellprice_r' value='".$setup_info[batch][rate][R][product_sellprice]."' style='width:60%;'> % </td>
					<td><input type='text' class='textbox number' name='batch[rate][R][a]' id='a_type_price_r' value='".$setup_info[batch][rate][R][a]."' style='width:60%;'> % </td>
					<td><input type='text' class='textbox number' name='batch[rate][R][b]' id='b_type_price_r' value='".$setup_info[batch][rate][R][b]."' style='width:60%;'> % </td>
					<td><input type='text' class='textbox number' name='batch[rate][R][c]' id='c_type_price_r' value='".$setup_info[batch][rate][R][c]."' style='width:60%;'> % </td>
					<td><input type='text' class='textbox number' name='batch[rate][R][d]' id='d_type_price_r' value='".$setup_info[batch][rate][R][d]."' style='width:60%;'> % </td>
					<td ><input type='text' class='textbox number' name='batch[rate][R][e]' id='e_type_price_r' value='".$setup_info[batch][rate][R][e]."' style='width:60%;'> % </td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding='5' cellspacing='0' border='0' bgcolor=#ffffff  class='list_table_box' width=99%>
				<col width='10%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<tr align=center height=30>
					<td class=s_td >기본가 (도매가) -> </td>
					<td class=m_td > 할인가 -> </td>
					<td class=m_td > ".$setup_info[batch][rate][W][a_name]." -> </td>
					<td class=m_td > ".$setup_info[batch][rate][W][b_name]." -> </td>
					<td class=m_td > ".$setup_info[batch][rate][W][c_name]." -> </td>
					<td class=m_td > ".$setup_info[batch][rate][W][d_name]." -> </td>
					<td class=e_td > ".$setup_info[batch][rate][W][e_name]." -> </td>
				</tr>
				<tr align=center>
					<td >도매</td>
					<td><input type='text' class='textbox number' name='batch[rate][W][product_sellprice]' id='product_sellprice_w' value='".$setup_info[batch][rate][W][product_sellprice]."' style='width:60%;'> % </td>
					<td><input type='text' class='textbox number' name='batch[rate][W][a]' id='a_type_price_w' value='".$setup_info[batch][rate][W][a]."' style='width:60%;'> % </td>
					<td><input type='text' class='textbox number' name='batch[rate][W][b]' id='b_type_price_w' value='".$setup_info[batch][rate][W][b]."' style='width:60%;'> % </td>
					<td><input type='text' class='textbox number' name='batch[rate][W][c]' id='c_type_price_w' value='".$setup_info[batch][rate][W][c]."' style='width:60%;'> % </td>
					<td><input type='text' class='textbox number' name='batch[rate][W][d]' id='d_type_price_w' value='".$setup_info[batch][rate][W][d]."' style='width:60%;'> % </td>
					<td ><input type='text' class='textbox number' name='batch[rate][W][e]' id='e_type_price_w' value='".$setup_info[batch][rate][W][e]."' style='width:60%;'> % </td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class='input_box_title'> <b>타입명 수정</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding='5' cellspacing='0' border='0' bgcolor=#ffffff  class='list_table_box' width=99%>
				<col width='10%'>
				<col width='18%'>
				<col width='18%'>
				<col width='18%'>
				<col width='18%'>
				<col width='18%'>
			
				<tr align=center height=30>
					<td class=s_td >구분</td>
					<td class=m_td >A타입</td>
					<td class=m_td >B타입</td>
					<td class=m_td >C타입</td>
					<td class=m_td >D타입</td>
					<td class=e_td >E타입</td>
				</tr>
				<tr align=center>
					
					<td>소매</td>
					<td><input type='text' class='textbox ' name='batch[rate][R][a_name]' id='a_type_price_r' value='".$setup_info[batch][rate][R][a_name]."' style='width:60%;'></td>
					<td><input type='text' class='textbox ' name='batch[rate][R][b_name]' id='b_type_price_r' value='".$setup_info[batch][rate][R][b_name]."' style='width:60%;'></td>
					<td><input type='text' class='textbox ' name='batch[rate][R][c_name]' id='c_type_price_r' value='".$setup_info[batch][rate][R][c_name]."' style='width:60%;'></td>
					<td><input type='text' class='textbox ' name='batch[rate][R][d_name]' id='d_type_price_r' value='".$setup_info[batch][rate][R][d_name]."' style='width:60%;'></td>
					<td><input type='text' class='textbox ' name='batch[rate][R][e_name]' id='e_type_price_r' value='".$setup_info[batch][rate][R][e_name]."' style='width:60%;'></td>
				</tr>
				<tr align=center>
					
					<td>도매</td>
					<td><input type='text' class='textbox ' name='batch[rate][W][a_name]' id='a_type_price_r' value='".$setup_info[batch][rate][W][a_name]."' style='width:60%;'></td>
					<td><input type='text' class='textbox ' name='batch[rate][W][b_name]' id='b_type_price_r' value='".$setup_info[batch][rate][W][b_name]."' style='width:60%;'></td>
					<td><input type='text' class='textbox ' name='batch[rate][W][c_name]' id='c_type_price_r' value='".$setup_info[batch][rate][W][c_name]."' style='width:60%;'></td>
					<td><input type='text' class='textbox ' name='batch[rate][W][d_name]' id='d_type_price_r' value='".$setup_info[batch][rate][W][d_name]."' style='width:60%;'></td>
					<td><input type='text' class='textbox ' name='batch[rate][W][e_name]' id='e_type_price_r' value='".$setup_info[batch][rate][W][e_name]."' style='width:60%;'></td>
				</tr>
			</table>


		</td>
	</tr>
	</table>
	</div>

	<div id='update_sellprice_multi' ".($setup_info[update_kind] == "update_sellprice_multi" ? "style='display:block;padding-top:10px;'":"style='display:none;padding-top:10px;'")." >
	<table width='100%' border=0 cellpadding=0 cellspacing=0 class='input_table_box'>
	<col width='18%'>
	<col width='*'>
	<tr height=30>
		<td class='input_box_title'> <b>가격수정</b></td>
		<td class='input_box_item'>
			<select name='round_cnt[multi]' id='round_cnt'>
			<option value='-1' ".CompareReturnValue("-1",$setup_info[round_cnt][multi],"selected").">10</option>
			<option value='-2' ".CompareReturnValue("-2",$setup_info[round_cnt][multi],"selected").">100</option>
			<option value='-3' ".CompareReturnValue("-3",$setup_info[round_cnt][multi],"selected").">1000</option>
			</select>

			<select name='round_type[multi]' id='round_type'>
			<option value='round' ".CompareReturnValue("round",$setup_info[round_type][multi],"selected").">반올림</option>
			<option value='floor'".CompareReturnValue("floor",$setup_info[round_type][multi],"selected").">반내림</option>
			</select>

			<span class='small blu'> 배(*)를 선택할 경우 기본가도 변경됩니다. (매입가는 소매/도매 동일합니다.)</span>
		</td>
	</tr>
	<tr>
		<td class='input_box_title' rowspan='2'> <b>가격수정</b></td>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding='5' cellspacing='0' border='0' bgcolor=#ffffff  class='list_table_box' width=99%>
				<col width='5%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<tr align=center height=30>
					<td class='s_td' rowspan='2'><input type= 'checkbox' name='batch[multi][R][check]' value='1' ".CompareReturnValue("1",$setup_info[batch][multi][R][check],"checked").">
					<td class=m_td >구분</td>
					<td class=m_td >E타입 -> </td>
					<td class=m_td >D타입 -> </td>	
					<td class=m_td >C타입 -> </td>
					<td class=m_td >B타입 -> </td>
					<td class=m_td >A타입 -> </td>
					<td class=m_td >할인가 -> </td>
					<td class=e_td >기본가격(소매가)</td>
				</tr>
				<tr align=center>
					<td >소매가(매입가)</td>
					<td ><input type='text' class='textbox number' name='batch[multi][R][e]' id='e_type_price_r' value='".$setup_info[batch][multi][R][e]."' style='width:60%;'> * 배</td>
					<td><input type='text' class='textbox number' name='batch[multi][R][d]' id='d_type_price_r' value='".$setup_info[batch][multi][R][d]."' style='width:60%;'> * 배</td>
					<td><input type='text' class='textbox number' name='batch[multi][R][c]' id='c_type_price_r' value='".$setup_info[batch][multi][R][c]."' style='width:60%;'> * 배</td>
					<td><input type='text' class='textbox number' name='batch[multi][R][b]' id='b_type_price_r' value='".$setup_info[batch][multi][R][b]."' style='width:60%;'> * 배</td>
					<td><input type='text' class='textbox number' name='batch[multi][R][a]' id='a_type_price_r' value='".$setup_info[batch][multi][R][a]."' style='width:60%;'> * 배</td>
					<td><input type='text' class='textbox number' name='batch[multi][R][product_sellprice]' id='product_sellprice_r' value='".$setup_info[batch][multi][R][product_sellprice]."' style='width:60%;'> * 배</td>
					<td><input type='text' class='textbox number' name='batch[multi][R][sellprice]' id='sellprice_r' value='".$setup_info[batch][multi][R][sellprice]."' style='width:60%;'> * 배</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class='input_box_item' style='padding:10px;'>
			<table cellpadding='5' cellspacing='0' border='0' bgcolor=#ffffff  class='list_table_box' width=99%>
				<col width='5%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<col width='12%'>
				<tr align=center height=30>
					<td class='s_td' rowspan='2'><input type= 'checkbox' name='batch[multi][W][check]' value='1' ".CompareReturnValue("1",$setup_info[batch][multi][W][check],"checked").">
					<td class=m_td >구분</td>
					<td class=m_td >E타입 -> </td>
					<td class=m_td >D타입 -> </td>	
					<td class=m_td >C타입 -> </td>
					<td class=m_td >B타입 -> </td>
					<td class=m_td >A타입 -> </td>
					<td class=m_td >할인가 -> </td>
					<td class=e_td >기본가격(소매가)<br></td>
				</tr>
				<tr align=center>
					<td >도매가(매입가)</td>
					<td ><input type='text' class='textbox number' name='batch[multi][W][e]' id='e_type_price_w' value='".$setup_info[batch][multi][W][e]."' style='width:60%;'> * 배</td>
					<td><input type='text' class='textbox number' name='batch[multi][W][d]' id='d_type_price_w' value='".$setup_info[batch][multi][W][d]."' style='width:60%;'> * 배</td>
					<td><input type='text' class='textbox number' name='batch[multi][W][c]' id='c_type_price_w' value='".$setup_info[batch][multi][W][c]."' style='width:60%;'> * 배</td>
					<td><input type='text' class='textbox number' name='batch[multi][W][b]' id='b_type_price_w' value='".$setup_info[batch][multi][W][b]."' style='width:60%;'> * 배</td>
					<td><input type='text' class='textbox number' name='batch[multi][W][a]' id='a_type_price_w' value='".$setup_info[batch][multi][W][a]."' style='width:60%;'> * 배</td>
					<td><input type='text' class='textbox number' name='batch[multi][W][product_sellprice]' id='product_sellprice_w' value='".$setup_info[batch][multi][W][product_sellprice]."' style='width:60%;'> * 배</td>
					<td><input type='text' class='textbox number' name='batch[multi][W][sellprice]' id='sellprice_w' value='".$setup_info[batch][multi][W][sellprice]."' style='width:60%;'> * 배</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	</div>
";

$Contents .= "
	<table width='100%' cellpadding=3 cellspacing=0 border='0'>
	<tr bgcolor=#ffffff >
			<td colspan=2 align=center style='padding:20px;'>";
			if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"U")){
				$Contents .= "<input type='image' src='../images/".$admininfo["language"]."/b_save.gif' border=0 style='cursor:pointer;border:0px;' >";
			}
	$Contents .= "
		</td></tr>
	</table>
</form>";


$Script = "
<script language='JavaScript' >

function SubmitX(frm){
	if(!CheckFormValue(frm)){
		return false;
	}
	frm.content.value = iView.document.body.innerHTML;
	return true;
}

function ChangeUpdateForm(selected_id){

	var area = new Array('update_sellprice_rate','update_sellprice_multi'); 

	for(var i=0; i<area.length; ++i){
		if(area[i]==selected_id){
			
			document.getElementById(selected_id).style.display = 'block';
			//$.cookie('goodsinfo_update_kind', selected_id.replace('batch_update_',''), {expires:1,domain:document.domain, path:'/', secure:0});
		}else{
			document.getElementById(area[i]).style.display = 'none';
		}
	}
}
</script>
";

$P = new LayOut();
$P->strLeftMenu = inventory_menu();
$P->addScript = $Script;
$P->Navigation = "품목가격관리 > 품목 다중할인 기본설정";
$P->title = "품목 다중할인 기본설정";
$P->strContents = $Contents;
$P->PrintLayOut();


?>