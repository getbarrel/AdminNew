<?
include("../class/layout.class");

$Script = " 
<script>
function submitCmt(type, cmt_ix){
	if(type == 'insert'){
		if($('form[name=insert_cmt]').find('textarea').val() == ''){
			alert('내용을 입력해주시기 바랍니다');
			return;
		}

		$('form[name=insert_cmt]').submit();
	}else if(type == 'update'){
		if($('form[name=update_cmt_'+cmt_ix+']').find('textarea').val() == ''){
			alert('내용을 입력해주시기 바랍니다');
			return;
		}

		$('form[name=update_cmt_'+cmt_ix+']').submit();
	}else if(type == 'delete'){
		if(confirm('삭제하시겠습니까?')){
			$('form[name=update_cmt_'+cmt_ix+']').find('input[name=act]').val('delete_cmt');
			$('form[name=update_cmt_'+cmt_ix+']').submit();
		}
	}
}

function showUpdate(bbs_ix, type){
	if(type == 1){
		$('form[name=update_cmt_'+bbs_ix+']').find('.read_area').show();
		$('form[name=update_cmt_'+bbs_ix+']').find('.update_area').hide();
	}else{
		$('form[name=update_cmt_'+bbs_ix+']').find('.read_area').hide();
		$('form[name=update_cmt_'+bbs_ix+']').find('.update_area').show();
	}
}

function submit(type){
	$('form[name=submit_'+type+']').submit();
}
function bbs_response_templet(selectbox,id){
    $('#'+id).val(selectbox.val());
    $('#'+id).closest('form').find('input[name=focus_info]').val('Y');
}
</script>
<style>
.width_class {width:150px;}
input {border:1px solid #c6c6c6;padding:3px;}
.member_table td {text-align:left;}
</style>";

$db = new Database;
$mdb = new Database;
$cdb = new Database;
$rproduct_db = new Database;

$sql = "select * from ".TBL_SHOP_PRODUCT_QNA." where bbs_ix = '".$bbs_ix."' ";
$db->query($sql);
$db->fetch();

if($page_type == "modify"){
	$Contents = "
	<form name='frm' action='./product_qna.act.php' method='post'>
	<input name='act' type='hidden' value='update'>
	<input name='bbs_ix' type='hidden' value='".$bbs_ix."'>
	<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
		<TR>
			<td align=center colspan=2 valign=top>
			<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
				<tr >
					<td align='left' colspan=2> ".GetTitleNavigation("상품문의", "고객센타 > 상품문의", false)."</td>
				</tr>
				<tr>
					<td align=center> <!-- style='padding: 0 10px 0 10px;height:569px;vertical-align:top' -->
					<table border='0' cellspacing='1' cellpadding='5' width='100%'>
					<tr>
					  <td bgcolor='#F8F9FA'>
						<table border='0' width='100%' cellspacing='1' cellpadding='0'>
							<tr>
								<td >
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
										<col width=15%>
										<col width=35%>
										<col width=15%>
										<col width=35%>
									<tr>
										<td class='input_box_title' nowrap> 상품정보 </td>
										<td class='input_box_item' colspan='3' style='padding: 10px;'>
											<img src='".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "s")."' width=100 align=absmiddle style='border:1px solid #efefef'>
											".$db->dt[pname]."
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 작성자 </td>
										<td class='input_box_item'>".$db->dt[bbs_name]."</td>
										<td class='input_box_title' nowrap> 등록일 </td>
										<td class='input_box_item'>".$db->dt[regdate]."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 문의분류 </td>
										<td class='input_box_item' colspan='3'>
											".getQnaDiv2($db->dt[bbs_div])."
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 공개설정 </td>
										<td class='input_box_item' colspan='3'>
											<input type='radio' name='bbs_hidden' value='1' id='bbs_hidden1' ".($db->dt[bbs_hidden] == '1'?'checked':'')."><label for='bbs_hidden1'>비공개</label>
											<input type='radio' name='bbs_hidden' value='0' id='bbs_hidden0' ".($db->dt[bbs_hidden] == '0'?'checked':'')."><label for='bbs_hidden0'>공개</label>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 제목 </td>
										<td class='input_box_item' colspan='3'>".$db->dt[bbs_subject]."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 내용 </td>
										<td class='input_box_item' colspan='3'>
											<table>
											<tr><td>".nl2br($db->dt[bbs_contents])."</td></tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						</table>
					  </td>
					</tr>
					</tr>
					</table>
					</td>
				</tr>
			</table>
			</td>
		</tr>
	</TABLE>
	<table style='margin-left: 4px;'>
		<tr>
			<td><input type='submit' value='저장'></td>
		</tr>
	</table>
	</form>";
}else{
	$Contents = "
	<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
		<TR>
			<td align=center colspan=2 valign=top>
			<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
				<tr >
					<td align='left' colspan=2> ".GetTitleNavigation("상품문의", "고객센타 > 상품문의", false)."</td>
				</tr>
				<tr>
					<td align=center> <!-- style='padding: 0 10px 0 10px;height:569px;vertical-align:top' -->
					<table border='0' cellspacing='1' cellpadding='5' width='100%'>
					<tr>
					  <td bgcolor='#F8F9FA'>
						<table border='0' width='100%' cellspacing='1' cellpadding='0'>
							<tr>
								<td >
								<table border='0' width='100%' cellspacing='0' cellpadding='0' class='member_table input_table_box' style='text-align:left;'>
										<col width=15%>
										<col width=35%>
										<col width=15%>
										<col width=35%>
									<tr>
										<td class='input_box_title' nowrap> 상품정보 </td>
										<td class='input_box_item' colspan='3' style='padding: 10px;'>
											<img src='".PrintImage($admin_config[mall_data_root]."/images/product", $db->dt[pid], "s")."' width=100 align=absmiddle style='border:1px solid #efefef'>
											".$db->dt[pname]."
											(".$db->dt[pid].") 
										</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 작성자 </td>
										<td class='input_box_item'>".$db->dt[bbs_name]." (".$db->dt[bbs_id].")</td>
										<td class='input_box_title' nowrap> 등록일 </td>
										<td class='input_box_item'>".$db->dt[regdate]."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 문의분류 </td>
										<td class='input_box_item' colspan='3'>".getQnaDivName($db->dt[bbs_div])."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 공개설정 </td>
										<td class='input_box_item' colspan='3'>".($db->dt[bbs_hidden] == 1 ? "비공개" : "공개")."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 제목 </td>
										<td class='input_box_item' colspan='3'>".$db->dt[bbs_subject]."</td>
									</tr>
									<tr>
										<td class='input_box_title' nowrap> 내용 </td>
										<td class='input_box_item' colspan='3'>
											<table>
											<tr><td>".nl2br($db->dt[bbs_contents])."</td></tr>
											</table>
										</td>
									</tr>
									<tr>
										<td class='input_box_title' rowspan='2'nowrap> 관리자 댓글</td>
										<td class='input_box_item' colspan='3'>
											<form name='insert_cmt' action='./product_qna.act.php' method='post'>
												<input name='act' type='hidden' value='insert_cmt'>
												<input name='bbs_ix' type='hidden' value='".$db->dt[bbs_ix]."'>
												<input name='cmt_name' type='hidden' value='".$admininfo[charger]."'>
												<input name='mem_ix' type='hidden' value='".$admininfo[charger_ix]."'>
												<table border='0' width='100%' cellspacing='0' cellpadding='10px'>
													<tr>
														<td>
															<b>".$admininfo[charger]."(".$admininfo[charger_id].")</b>
															답변 탬플릿 선택 ".bbs_response_templet_selectbox('cmt_contents','P_Q&A')."
														</td>
														<td><input type='button' value='등록' onclick=\"submitCmt('insert');\"></td>
													</tr>
													<tr>
														<td>
															<textarea name='cmt_contents' id='cmt_contents' style='height:150px;'></textarea>
														</td>
													</tr>
												</table>
											</form>
										</td>
									</tr>
									".getCmt($db->dt[bbs_ix])."
								</table>
							</td>
						</tr>
						</table>
					  </td>
					</tr>
					</tr>
					</table>
					</td>
				</tr>
			</table>
			</td>
		</tr>
	</TABLE>
	<table style='margin-left: 4px;'>
		<tr>
			<td><input type='button' value='수정' onclick=\"top.location.href='?page_type=modify&bbs_ix=".$bbs_ix."'\"></td>
			<td><input type='button' value='삭제' onclick=\"submit('delete');\"></td>
		</tr>
	</table>
	
	<form name='submit_delete' action='./product_qna.act.php' method='post'>
		<input name='act' type='hidden' value='delete'>
		<input name='mmode' type='hidden' value='pop'>
		<input name='bbs_ix' type='hidden' value='".$bbs_ix."'>
	</form>
	";
}

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "고객센타 > 상품문의";
$P->NaviTitle = "상품문의";
$P->title = "상품문의";
$P->strContents = $Contents;
echo $P->PrintLayOut();

function getUrl($bbs_ix, $file){
	global $_SERVER, $admin_config;
	$bbs_ix = (int)$bbs_ix;
	
	if(! empty($file)){
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$admin_config[mall_data_root]."/product_after/".$bbs_ix."/".$file)){
			$url = $admin_config[mall_data_root]."/product_after/".$bbs_ix."/".$file;
		}else{
			if($file != ""){
				$url = "../image/no_img.gif";
			}
		}
	}

	return $url;
}

function getCmt($bbs_ix){
	global $mdb, $admininfo;

	$sql = "select *, (select id from common_user where code = mem_ix) as id from shop_product_qna_comment where bbs_ix='".$bbs_ix."'";
	$mdb->query($sql);
	$lists = $mdb->fetchall("object");
	
	if(is_array($lists) && ! empty($lists)){
		$contents = "<tr>
						<td class='input_box_item' colspan='3'>";
		foreach($lists as $k => $v){
			$contents .= "
					<form name='update_cmt_".$v[cmt_ix]."' action='./product_qna.act.php' method='post'>
						<input name='act' type='hidden' value='update_cmt'>
						<input name='bbs_ix' type='hidden' value='".$bbs_ix."'>
						<input name='cmt_ix' type='hidden' value='".$v[cmt_ix]."'>
						<table border='0' width='100%' cellspacing='0' cellpadding='10px'>
							<tr>
								<td><b>".$v[cmt_name]."(".$v[id].")</b> ".$v[regdate]."</td>
								<td >
									<input class='read_area' type='button' style='margin-right: 4px;' value='수정' onclick=\"showUpdate('".$v[cmt_ix]."');\">
									<input class='read_area' type='button' value='삭제' onclick=\"submitCmt('delete', '".$v[cmt_ix]."');\">
									<input class='update_area' type='button' style='margin-right: 4px;display:none;' value='저장' onclick=\"submitCmt('update', '".$v[cmt_ix]."');\">
									<input class='update_area' type='button' style='display:none;' value='취소' onclick=\"showUpdate('".$v[cmt_ix]."', 1);\">
								</td>
							</tr>
							<tr>
								<td>
									<div class='read_area'>".$v[cmt_contents]."</div>
									<div class='update_area' style='display:none;'><textarea name='cmt_contents'>".$v[cmt_contents]."</textarea></div>
								</td>
							</tr>
						</table>
					</form>";
		}
		$contents .= "</td>
					</tr>";
	}

	return $contents;
}

function getQnaDiv2($bbs_div){
	global $mdb;

	$sql = "select * from shop_product_qna_div where disp='1'";
	$mdb->query($sql);
	$datas = $mdb->fetchall("object");
	$return = "";

	if(! empty($datas)){
		foreach($datas as $k => $v){
			$return .= "<input type='radio' name='bbs_div' value='".$v[ix]."' id='bbs_div_".$v[ix]."' ".($v[ix] == $bbs_div ?'checked':'')."><label for='bbs_div_".$v[ix]."'>".$v[div_name]."</label>&nbsp;&nbsp;";
		}
	}

	return $return;
}

?>
