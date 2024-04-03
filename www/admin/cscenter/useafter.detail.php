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

if($act == "down"){
	$ie = isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false); 
	 
	if($ie) {
	   header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	   header('Pragma: public');
	}else{
	   // IE가 아닌 경우 일반 헤더 적용
	   header("Cache-Control: no-cache, must-revalidate"); 
	   header('Pragma: no-cache');
	}

	$db = new Database();
	$db->sqlFilter($file);
	$file = urldecode($file);
	$file = str_replace("../","",$file);

	function mb_basename($path) { return end(explode('/',$path)); } 
	function utf2euc($str) { return iconv("UTF-8","cp949//IGNORE", $str); }
	function is_ie() {
		if(!isset($_SERVER['HTTP_USER_AGENT']))return false;
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
		return false;
	}

	$filepath = $_SERVER["DOCUMENT_ROOT"]."".getUrl($bbs_ix, $bbs_file);
	$filesize = filesize($filepath);
	$filename = iconv('UTF-8','EUC-KR',$bbs_file);
	if( is_ie() ) $filename = utf2euc($filename);

	header("Pragma: public");
	header("Expires: 0");
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"$filename\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: $filesize");

	readfile($filepath);

	exit;
}

$sql = "select * from shop_product_after where bbs_ix='".$bbs_ix."'";
$db->query($sql);
$db->fetch();

$file1 = getUrl($db->dt[bbs_ix], $db->dt[bbs_file_1]);
$file2 = getUrl($db->dt[bbs_ix], $db->dt[bbs_file_2]);
$file3 = getUrl($db->dt[bbs_ix], $db->dt[bbs_file_3]);
$file4 = getUrl($db->dt[bbs_ix], $db->dt[bbs_file_4]);
$file5 = getUrl($db->dt[bbs_ix], $db->dt[bbs_file_5]);

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2 valign=top>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			<tr >
				<td align='left' colspan=2> ".GetTitleNavigation("상품후기", "고객센타 > 상품후기", false)."</td>
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
									<td class='input_box_title' nowrap> 제목 </td>
									<td class='input_box_item' colspan='3'>".$db->dt[bbs_subject]."</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 상품명 </td>
									<td class='input_box_item' colspan='3'>".$db->dt[pname]."</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 작성자 </td>
									<td class='input_box_item'>".$db->dt[bbs_name]."</td>
									<td class='input_box_title' nowrap> 공개설정 </td>
									<td class='input_box_item'>".($db->dt[bbs_hidden] == 1 ? "비공개" : "공개")."</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 등록일 </td>
									<td class='input_box_item'>".$db->dt[regdate]."</td>
									<td class='input_box_title' nowrap> 조회수 </td>
									<td class='input_box_item'>".$db->dt[bbs_hit]."</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 베스트 후기 지정 </td>
									<td class='input_box_item' colspan='3'>".($db->dt[is_best] == "1" ? "지정":"미지정")."</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 첨부파일 </td>
									<td class='input_box_item' colspan='3'>
									".($file1 == "" ? "" : "<a href='?act=down&bbs_ix=".$db->dt[bbs_ix]."&bbs_file=".$db->dt[bbs_file_1]."'>".$db->dt[bbs_file_1]."</a></br>")."
									".($file2 == "" ? "" : "<a href='?act=down&bbs_ix=".$db->dt[bbs_ix]."&bbs_file=".$db->dt[bbs_file_2]."'>".$db->dt[bbs_file_2]."</a></br>")."
									".($file3 == "" ? "" : "<a href='?act=down&bbs_ix=".$db->dt[bbs_ix]."&bbs_file=".$db->dt[bbs_file_3]."'>".$db->dt[bbs_file_3]."</a></br>")."
									".($file4 == "" ? "" : "<a href='?act=down&bbs_ix=".$db->dt[bbs_ix]."&bbs_file=".$db->dt[bbs_file_4]."'>".$db->dt[bbs_file_4]."</a></br>")."
									".($file5 == "" ? "" : "<a href='?act=down&bbs_ix=".$db->dt[bbs_ix]."&bbs_file=".$db->dt[bbs_file_5]."'>".$db->dt[bbs_file_5]."</a>")."
									</td>
								</tr>
								<tr>
									<td class='input_box_title' nowrap> 내용 </td>
									<td class='input_box_item' colspan='3'>
										<table>
										".($file1 == "" ? "" : "<tr><td><img src='".$file1."' style='border:1px solid silver' width=100 align=left></td></tr>")."
										".($file2 == "" ? "" : "<tr><td><img src='".$file2."' style='border:1px solid silver' width=100 align=left></td></tr>")."
										".($file3 == "" ? "" : "<tr><td><img src='".$file3."' style='border:1px solid silver' width=100 align=left></td></tr>")."
										".($file4 == "" ? "" : "<tr><td><img src='".$file4."' style='border:1px solid silver' width=100 align=left></td></tr>")."
										".($file5 == "" ? "" : "<tr><td><img src='".$file5."' style='border:1px solid silver' width=100 align=left></td></tr>")."

										<tr><td>".nl2br($db->dt[bbs_contents])."</td></tr>
										</table>
									</td>
								</tr>
								<tr>
									<td class='input_box_title' rowspan='2'nowrap> 관리자 댓글</td>
									<td class='input_box_item' colspan='3'>
										<form name='insert_cmt' action='./useafter.act.php' method='post'>
											<input name='act' type='hidden' value='insert_cmt'>
											<input name='bbs_ix' type='hidden' value='".$db->dt[bbs_ix]."'>
											<input name='cmt_name' type='hidden' value='".$admininfo[charger]."'>
											<input name='mem_ix' type='hidden' value='".$admininfo[charger_ix]."'>
											<table border='0' width='100%' cellspacing='0' cellpadding='10px'>
												<tr>
													<td><b>".$admininfo[charger]."(".$admininfo[charger_id].")</b></td>
													<td><input type='button' value='등록' onclick=\"submitCmt('insert');\"></td>
												</tr>
												<tr>
													<td>
														<textarea name='cmt_contents'></textarea>
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
		<td><input type='button' value='수정' onclick=\"top.location.href='./useafter.write.php?mmode=pop&bbs_ix=".$bbs_ix."'\"></td>
		<td><input type='button' value='삭제' onclick=\"submit('delete');\"></td>
	</tr>
</table>

<form name='submit_delete' action='./useafter.act.php' method='post'>
	<input name='act' type='hidden' value='delete'>
	<input name='mmode' type='hidden' value='pop'>
	<input name='bbs_ix' type='hidden' value='".$bbs_ix."'>
</form>
";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "고객센타 > 상품후기";
$P->NaviTitle = "상품후기";
$P->title = "상품후기";
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

	$sql = "select * from shop_product_after_comment where bbs_ix='".$bbs_ix."'";
	$mdb->query($sql);
	$lists = $mdb->fetchall("object");
	
	if(is_array($lists) && ! empty($lists)){
		$contents = "<tr>
						<td class='input_box_item' colspan='3'>";
		foreach($lists as $k => $v){
			$contents .= "
					<form name='update_cmt_".$v[cmt_ix]."' action='./useafter.act.php' method='post'>
						<input name='act' type='hidden' value='update_cmt'>
						<input name='bbs_ix' type='hidden' value='".$bbs_ix."'>
						<input name='cmt_ix' type='hidden' value='".$v[cmt_ix]."'>
						<table border='0' width='100%' cellspacing='0' cellpadding='10px'>
							<tr>
								<td><b>".$admininfo[charger]."(".$admininfo[charger_id].")</b> ".$v[regdate]."</td>
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
?>
